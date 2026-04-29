<?php

class UserController {
    private UserModel $um;

    public function __construct() {
        $this->um = new UserModel();
    }

    public function updateProfile(string $role): void {
        requireAuth($role);
        if (!csrfVerify()) {
            flash('error','Requête invalide.');
            $this->redirectProfile($role);
        }
        $user  = currentUser();
        $nom   = trim($_POST['nom']   ?? '');
        $email = trim($_POST['email'] ?? '');

        if (!$nom || !$email) {
            flash('error', 'Nom et email obligatoires.');
        } elseif ($this->um->updateProfile((int)$user['id'], $nom, $email)) {
            $_SESSION['user']['nom']   = $nom;
            $_SESSION['user']['email'] = $email;
            flash('success', 'Profil mis à jour avec succès.');
        } else {
            flash('error', 'Erreur lors de la mise à jour.');
        }
        $this->redirectProfile($role);
    }

    public function updatePassword(string $role): void {
        requireAuth($role);
        if (!csrfVerify()) {
            flash('error','Requête invalide.');
            $this->redirectProfile($role);
        }
        $user = currentUser();
        $old  = $_POST['old_password']     ?? '';
        $new  = $_POST['new_password']     ?? '';
        $conf = $_POST['confirm_password'] ?? '';

        if (!$this->um->login($user['email'], $old)) {
            flash('error', 'Ancien mot de passe incorrect.');
        } elseif ($new !== $conf) {
            flash('error', 'Les mots de passe ne correspondent pas.');
        } elseif (strlen($new) < 6) {
            flash('error', 'Le mot de passe doit contenir au moins 6 caractères.');
        } else {
            $this->um->updatePassword((int)$user['id'], $new);
            flash('success', 'Mot de passe modifié avec succès.');
        }
        $this->redirectProfile($role);
    }

    private function redirectProfile(string $role): void {
        $map = [
            ROLE_ADMIN    => 'views/admin/profile.php',
            ROLE_ETUDIANT => 'views/student/profile.php',
            ROLE_CAISSIER => 'views/cashier/history.php',
        ];
        redirect($map[$role] ?? 'views/public/home.php');
    }

    public function deleteUser(): void {
        requireAuth(ROLE_ADMIN);
        if (!csrfVerify()) {
            flash('error','Requête invalide.');
            redirect('views/admin/users.php');
        }
        $id = (int)($_POST['id'] ?? 0);
        if ($id === (int)$_SESSION['user_id']) {
            flash('error', 'Impossible de supprimer votre propre compte.');
        } elseif ($this->um->deleteUser($id)) {
            flash('success', 'Utilisateur supprimé avec succès.');
        } else {
            flash('error', 'Erreur lors de la suppression.');
        }
        redirect('views/admin/users.php');
    }

    public function editUser(): void {
        requireAuth(ROLE_ADMIN);
        if (!csrfVerify()) {
            flash('error','Requête invalide.');
            redirect('views/admin/users.php');
        }
        $id    = (int)($_POST['id']    ?? 0);
        $nom   = trim($_POST['nom']   ?? '');
        $email = trim($_POST['email'] ?? '');
        if ($this->um->updateUser($id, $nom, $email)) {
            flash('success', 'Utilisateur modifié.');
        } else {
            flash('error', 'Erreur de modification.');
        }
        redirect('views/admin/users.php');
    }
}
