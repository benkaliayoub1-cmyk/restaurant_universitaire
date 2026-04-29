<?php

class TicketModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getPDO();
    }

    /**
     * Expirer les tickets dont le menu est passé — LOGIQUE PHP
     * Appelée explicitement depuis les pages (pas de trigger SQL)
     */
    public function expirerTickets(): void {
        $this->db->exec(
            "UPDATE ticket t
             JOIN menu m ON m.id_menu = t.id_menu
             SET t.status = 'annule'
             WHERE t.status = 'valide'
               AND m.dateMenu < CURDATE()"
        );
    }

    /**
     * Créer un ticket — montant toujours = MENU_PRIX_FIXE
     */
    public function creerTicket(int $etudiantId, int $menuId, string $mode = 'especes', string $transD17 = ''): ?array {
        // Vérifier que le menu est pour aujourd'hui
        $s = $this->db->prepare("SELECT dateMenu FROM menu WHERE id_menu=?");
        $s->execute([$menuId]);
        $menu = $s->fetch();
        if (!$menu || $menu['dateMenu'] !== date('Y-m-d')) return null;

        // Vérifier pas de double réservation aujourd'hui
        if ($this->aDejaTicketAujourdhui($etudiantId)) return null;

        $qr = strtoupper(bin2hex(random_bytes(8))) . '-' . $etudiantId . '-' . time();

        $this->db->prepare(
            "INSERT INTO ticket (dateAchat,status,qrCode,montantTotal,transactionD17,id_etudiant,id_menu)
             VALUES (NOW(),'valide',?,?,?,?,?)"
        )->execute([$qr, MENU_PRIX_FIXE, $transD17 ?: null, $etudiantId, $menuId]);

        return $this->getById((int)$this->db->lastInsertId());
    }

    public function aDejaTicketAujourdhui(int $etudiantId): bool {
        $s = $this->db->prepare(
            "SELECT COUNT(*) FROM ticket t
             JOIN menu m ON m.id_menu=t.id_menu
             WHERE t.id_etudiant=? AND m.dateMenu=CURDATE()
               AND t.status IN ('valide','utilise')"
        );
        $s->execute([$etudiantId]);
        return (int)$s->fetchColumn() > 0;
    }
    /*
 * Recette journalière pour le graphique CanvasJS
 * Retourne un tableau [timestamp_ms => montant] pour les 30 derniers jours
 */
public function getRecettesParJour(int $jours = 30): array {
    $s = $this->db->prepare(
        "SELECT m.dateMenu, SUM(t.montantTotal) AS recette
         FROM ticket t
         JOIN menu m ON m.id_menu = t.id_menu
         WHERE t.status != 'annule'
           AND m.dateMenu >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
         GROUP BY m.dateMenu
         ORDER BY m.dateMenu ASC"
    );
    $s->execute([$jours]);
    $rows = $s->fetchAll();

    $dataPoints = [];
    foreach ($rows as $row) {
        // Convertir la date en timestamp millisecondes (format CanvasJS)
        $ts = strtotime($row['dateMenu']) * 1000;
        $dataPoints[] = [
            'x' => $ts,
            'y' => (float)$row['recette']
        ];
    }
    return $dataPoints;
}
/**
 * Données pour le donut chart : Tickets utilisés vs non utilisés du jour
 * Utilisé vs Non utilisé (valide = réservé mais pas encore scanné)
 */
public function getStatsValidationDuJour(): array {
    $s = $this->db->query(
        "SELECT
            SUM(CASE WHEN t.status = 'utilise' THEN 1 ELSE 0 END) AS utilises,
            SUM(CASE WHEN t.status = 'valide'  THEN 1 ELSE 0 END) AS valides
         FROM ticket t
         JOIN menu m ON m.id_menu = t.id_menu
         WHERE m.dateMenu = CURDATE()"
    );
    $row = $s->fetch();

    $utilises = (int)($row['utilises'] ?? 0);
    $valides  = (int)($row['valides']  ?? 0);

    return [
        'utilises'    => $utilises,
        'non_utilises'=> $valides,
        'total'       => $utilises + $valides,
        'dataPoints'  => [
            ['y' => $utilises, 'name' => 'Entrées validées',     'color' => '#3A5A40'],
            ['y' => $valides,  'name' => 'Réservés non scannés', 'color' => '#C8553D'],
        ]
    ];
}

    public function getById(int $id): ?array {
        $s = $this->db->prepare(
            "SELECT t.*, m.dateMenu, m.typeMenu,
                    u.nom AS etudiant_nom, u.email AS etudiant_email, e.matricule
             FROM ticket t
             JOIN menu m        ON m.id_menu  = t.id_menu
             JOIN etudiant e    ON e.id        = t.id_etudiant
             JOIN utilisateur u ON u.id        = e.id
             WHERE t.id_ticket=?"
        );
        $s->execute([$id]);
        return $s->fetch() ?: null;
    }

    public function getByQrCode(string $qr): ?array {
        $s = $this->db->prepare(
            "SELECT t.*, m.dateMenu, m.typeMenu,
                    u.nom AS etudiant_nom, e.matricule
             FROM ticket t
             JOIN menu m        ON m.id_menu = t.id_menu
             JOIN etudiant e    ON e.id       = t.id_etudiant
             JOIN utilisateur u ON u.id       = e.id
             WHERE t.qrCode=?"
        );
        $s->execute([$qr]);
        return $s->fetch() ?: null;
    }

    public function getByEtudiant(int $etudiantId): array {
        $s = $this->db->prepare(
            "SELECT t.*, m.dateMenu, m.typeMenu
             FROM ticket t
             JOIN menu m ON m.id_menu=t.id_menu
             WHERE t.id_etudiant=?
             ORDER BY t.dateAchat DESC"
        );
        $s->execute([$etudiantId]);
        return $s->fetchAll();
    }

    /** Stats étudiant calculées en PHP */
    public function getStatsEtudiant(int $etudiantId): array {
        $tickets = $this->getByEtudiant($etudiantId);
        $stats = ['total'=>0,'utilises'=>0,'valides'=>0,'annules'=>0,'depense'=>0.0];
        foreach ($tickets as $t) {
            $stats['total']++;
            if ($t['status']==='utilise')       $stats['utilises']++;
            elseif ($t['status']==='valide')    $stats['valides']++;
            else                                 $stats['annules']++;
            if ($t['status']!=='annule')         $stats['depense'] += (float)$t['montantTotal'];
        }
        return $stats;
    }

    /** Valider un ticket (scan caissier) — logique PHP */
    public function validerTicket(int $ticketId, int $caissierId): array {
        $ticket = $this->getById($ticketId);
        if (!$ticket)
            return ['ok'=>false,'msg'=>'Ticket introuvable.'];
        if ($ticket['status'] !== 'valide')
            return ['ok'=>false,'msg'=>'Ticket non valide (statut : '.$ticket['status'].').'];
        if ($ticket['dateMenu'] !== date('Y-m-d'))
            return ['ok'=>false,'msg'=>"Ce ticket n'est pas valable aujourd'hui ({$ticket['dateMenu']})."];

        $this->db->prepare("UPDATE ticket SET status='utilise' WHERE id_ticket=?")->execute([$ticketId]);
        $this->db->prepare("INSERT INTO validation_ticket (id_ticket,id_caissier,dateValidation) VALUES (?,?,NOW())")
                 ->execute([$ticketId, $caissierId]);

        return ['ok'=>true,'msg'=>'Accès autorisé — Bon appétit ! 🎉','ticket'=>$ticket];
    }

    public function countTicketsDuJour(): int {
        return (int)$this->db->query(
            "SELECT COUNT(*) FROM ticket t
             JOIN menu m ON m.id_menu=t.id_menu
             WHERE m.dateMenu=CURDATE() AND t.status IN ('valide','utilise')"
        )->fetchColumn();
    }

    public function getAll(int $limit = 100): array {
        $s = $this->db->prepare(
            "SELECT t.*, m.dateMenu, m.typeMenu, u.nom AS etudiant_nom, e.matricule
             FROM ticket t
             JOIN menu m        ON m.id_menu  = t.id_menu
             JOIN etudiant e    ON e.id        = t.id_etudiant
             JOIN utilisateur u ON u.id        = e.id
             ORDER BY t.dateAchat DESC LIMIT ?"
        );
        $s->execute([$limit]);
        return $s->fetchAll();
    }

    /** Stats admin calculées en PHP */
    public function getStatsAdmin(): array {
        $today = date('Y-m-d');
        $s = $this->db->prepare(
            "SELECT t.montantTotal FROM ticket t
             JOIN menu m ON m.id_menu=t.id_menu
             WHERE m.dateMenu=? AND t.status!='annule'"
        );
        $s->execute([$today]);
        $jour = $s->fetchAll();

        $all = $this->db->query("SELECT montantTotal FROM ticket WHERE status!='annule'")->fetchAll();

        $topJours = $this->db->query(
            "SELECT m.dateMenu, COUNT(t.id_ticket) AS nb
             FROM ticket t JOIN menu m ON m.id_menu=t.id_menu
             WHERE t.status!='annule'
             GROUP BY m.dateMenu ORDER BY nb DESC LIMIT 7"
        )->fetchAll();

        return [
            'tickets_jour'   => count($jour),
            'recette_jour'   => array_sum(array_column($jour, 'montantTotal')),
            'total_tickets'  => count($all),
            'recette_totale' => array_sum(array_column($all, 'montantTotal')),
            'top_jours'      => $topJours,
        ];
    }

    public function getValidationsDuJour(int $caissierId): array {
        $s = $this->db->prepare(
            "SELECT vt.*, t.montantTotal, m.dateMenu, m.typeMenu,
                    u.nom AS etudiant_nom, e.matricule
             FROM validation_ticket vt
             JOIN ticket t      ON t.id_ticket = vt.id_ticket
             JOIN menu m        ON m.id_menu   = t.id_menu
             JOIN etudiant e    ON e.id         = t.id_etudiant
             JOIN utilisateur u ON u.id         = e.id
             WHERE vt.id_caissier=? AND DATE(vt.dateValidation)=CURDATE()
             ORDER BY vt.dateValidation DESC"
        );
        $s->execute([$caissierId]);
        return $s->fetchAll();
    }
}

