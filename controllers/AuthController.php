<?php
// ============================================================
//  controllers/AuthController.php
// ============================================================
class AuthController {
    private UserModel $um;

    public function __construct() { $this->um = new UserModel(); }

    public function login(): void {
        if (!csrfVerify()) { flash('error','Requête invalide.'); redirect('views/public/login.php'); }
        $email = trim($_POST['email'] ?? '');
        $pwd   = $_POST['password'] ?? '';
        if (!$email || !$pwd) { flash('error','Remplissez tous les champs.'); redirect('views/public/login.php'); }

        $user = $this->um->login($email, $pwd);
        if (!$user) { flash('error','Email ou mot de passe incorrect.'); redirect('views/public/login.php'); }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role']    = $user['role'];
        $_SESSION['user']    = $user;
        session_regenerate_id(true);

        switch ($user['role']) {
            case ROLE_ADMIN:    redirect('views/admin/dashboard.php');   break;
            case ROLE_CAISSIER: redirect('views/cashier/scan.php');      break;
            default:            redirect('views/student/dashboard.php'); break;
        }
    }

    public function register(): void {
        if (!csrfVerify()) { flash('error','Requête invalide.'); redirect('views/public/register.php'); }
        $nom  = trim($_POST['nom'] ?? '');
        $email= trim($_POST['email'] ?? '');
        $pwd  = $_POST['password'] ?? '';
        $conf = $_POST['confirm_password'] ?? '';
        $mat  = trim($_POST['matricule'] ?? '');

        if (!$nom||!$email||!$pwd||!$mat) { flash('error','Tous les champs sont obligatoires.'); redirect('views/public/register.php'); }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { flash('error','Email invalide.'); redirect('views/public/register.php'); }
        if ($pwd !== $conf) { flash('error','Les mots de passe ne correspondent pas.'); redirect('views/public/register.php'); }
        if (strlen($pwd) < 6) { flash('error','6 caractères minimum.'); redirect('views/public/register.php'); }
        if ($this->um->emailExists($email)) { flash('error','Cet email est déjà utilisé.'); redirect('views/public/register.php'); }
        if ($this->um->matriculeExists($mat)) { flash('error','Ce matricule est déjà enregistré.'); redirect('views/public/register.php'); }

        if ($this->um->registerEtudiant($nom, $email, $pwd, $mat)) {
            flash('success','Compte créé ! Connectez-vous.');
            redirect('views/public/login.php');
        }
        flash('error','Erreur lors de la création du compte.');
        redirect('views/public/register.php');
    }

    public function logout(): void {
        session_destroy();
        redirect('views/public/home.php');
    }
}
