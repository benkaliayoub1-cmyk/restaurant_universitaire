<?php
// ============================================================
//  controllers/TicketController.php
// ============================================================
class TicketController {
    private TicketModel $tm;
    private MenuModel   $mm;

    public function __construct() {
        $this->tm = new TicketModel();
        $this->mm = new MenuModel();
    }

    public function reserver(): void {
        requireAuth(ROLE_ETUDIANT);
        if (!csrfVerify()) {
            flash('error','Requête invalide.');
            redirect('views/student/reservation.php');
        }

        $menu = $this->mm->getMenuDuJour();
        if (!$menu) {
            flash('error', 'Aucun menu disponible aujourd\'hui.');
            redirect('views/student/dashboard.php');
        }

        $etudiantId = (int)$_SESSION['user_id'];
        if ($this->tm->aDejaTicketAujourdhui($etudiantId)) {
            flash('error', 'Vous avez déjà réservé aujourd\'hui.');
            redirect('views/student/tickets.php');
        }

        $mode     = trim($_POST['mode_paiement'] ?? 'especes');
        $transD17 = '';
        if ($mode === 'd17') {
            $transD17 = 'D17-SIM-' . strtoupper(bin2hex(random_bytes(4)));
        }

        $ticket = $this->tm->creerTicket($etudiantId, (int)$menu['id_menu'], $mode, $transD17);

        if ($ticket) {
            flash('success', 'Réservation effectuée avec succès !');
            redirect('views/student/ticket_view.php?id=' . $ticket['id_ticket']);
        }
        flash('error', 'Erreur lors de la réservation. Veuillez réessayer.');
        redirect('views/student/reservation.php');
    }

    public function scanner(): void {
        requireAuth(ROLE_CAISSIER);
        if (!csrfVerify()) {
            flash('error','Requête invalide.');
            redirect('views/cashier/scan.php');
        }

        $qr = trim($_POST['qr_code'] ?? '');
        if (!$qr) {
            flash('error', 'Veuillez saisir un code QR.');
            redirect('views/cashier/scan.php');
        }

        $ticket = $this->tm->getByQrCode($qr);
        if (!$ticket) {
            $_SESSION['scan_result'] = ['ok' => false, 'msg' => 'QR code introuvable dans la base de données.'];
        } else {
            $result = $this->tm->validerTicket((int)$ticket['id_ticket'], (int)$_SESSION['user_id']);
            $_SESSION['scan_result'] = $result;
        }
        redirect('views/cashier/scan.php');
    }
}
