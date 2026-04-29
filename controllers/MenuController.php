<?php
// ============================================================
//  controllers/MenuController.php
// ============================================================
class MenuController {
    private MenuModel $mm;

    public function __construct() { $this->mm = new MenuModel(); }

    public function creerMenu(): void {
        requireAuth(ROLE_ADMIN);
        if (!csrfVerify()) { flash('error','Requête invalide.'); redirect('views/admin/menu.php'); }

        $date     = trim($_POST['date_menu'] ?? date('Y-m-d'));
        $type     = trim($_POST['type_menu'] ?? 'dejeuner');
        $repasIds = array_map('intval', $_POST['repas_ids'] ?? []);

        if (empty($repasIds)) { flash('error','Sélectionnez au moins un plat.'); redirect('views/admin/menu.php'); }
        if ($this->mm->menuExistePourDate($date)) { flash('error',"Un menu existe déjà pour le $date."); redirect('views/admin/menu.php'); }

        if ($this->mm->creerMenu($date, $type, $repasIds)) flash('success','Menu publié avec succès !');
        else flash('error','Erreur lors de la création.');
        redirect('views/admin/menu.php');
    }

    public function gererRepas(): void {
        requireAuth(ROLE_ADMIN);
        if (!csrfVerify()) { flash('error','Requête invalide.'); redirect('views/admin/repas.php'); }

        $action = $_POST['action'] ?? '';
        if ($action === 'add') {
            $nom = trim($_POST['nom'] ?? ''); $cat = trim($_POST['categorie'] ?? '');
            if (!$nom||!$cat) flash('error','Nom et catégorie obligatoires.');
            elseif ($this->mm->addRepas($nom, $cat)) flash('success','Plat ajouté.');
            else flash('error','Erreur lors de l\'ajout.');
        } elseif ($action === 'edit') {
            $id = (int)($_POST['id']??0); $nom = trim($_POST['nom']??''); $cat = trim($_POST['categorie']??'');
            if ($this->mm->updateRepas($id, $nom, $cat)) flash('success','Plat modifié.');
            else flash('error','Erreur de modification.');
        } elseif ($action === 'delete') {
            $id = (int)($_POST['id']??0);
            if ($this->mm->deleteRepas($id)) flash('success','Plat supprimé.');
            else flash('error','Erreur de suppression.');
        }
        redirect('views/admin/repas.php?page='.($_GET['page']??1));
    }
}
