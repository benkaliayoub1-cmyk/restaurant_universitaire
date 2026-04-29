<?php
// ============================================================
//  models/MenuModel.php
//  Prix affiché = MENU_PRIX_FIXE (0.200 DT) — toujours fixe
//  Les prix individuels des repas ne sont PAS affichés
// ============================================================
class MenuModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getPDO();
    }

    /** Menu d'une date (repas sans prix individuels) */
    public function getMenuByDate(string $date): ?array {
        $s = $this->db->prepare("SELECT * FROM menu WHERE dateMenu=? LIMIT 1");
        $s->execute([$date]);
        $menu = $s->fetch();
        if (!$menu) return null;

        $s2 = $this->db->prepare(
            "SELECT r.id_repas, r.nom, r.categorie
             FROM menu_repas mr
             JOIN repas r ON r.id_repas = mr.id_repas
             WHERE mr.id_menu = ?
             ORDER BY FIELD(r.categorie,'entree','plat','dessert','boisson','supplement'), r.nom"
        );
        $s2->execute([$menu['id_menu']]);
        $menu['repas']     = $s2->fetchAll();
        $menu['prix_fixe'] = MENU_PRIX_FIXE;
        return $menu;
    }

    public function getMenuDuJour(): ?array {
        return $this->getMenuByDate(date('Y-m-d'));
    }

    public function menuExistePourDate(string $date): bool {
        $s = $this->db->prepare("SELECT id_menu FROM menu WHERE dateMenu=?");
        $s->execute([$date]);
        return (bool)$s->fetch();
    }

    public function creerMenu(string $date, string $type, array $repasIds): int {
        $this->db->beginTransaction();
        try {
            $s = $this->db->prepare("INSERT INTO menu (dateMenu,typeMenu) VALUES (?,?)");
            $s->execute([$date, $type]);
            $menuId = (int)$this->db->lastInsertId();
            $ins = $this->db->prepare("INSERT INTO menu_repas (id_menu,id_repas) VALUES (?,?)");
            foreach ($repasIds as $rid) $ins->execute([$menuId, (int)$rid]);
            $this->db->commit();
            return $menuId;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return 0;
        }
    }

    public function getHistorique(int $limit = 30): array {
        $s = $this->db->prepare(
            "SELECT m.id_menu, m.dateMenu, m.typeMenu,
                    COUNT(DISTINCT mr.id_repas)  AS nb_repas,
                    COUNT(DISTINCT t.id_ticket)  AS nb_tickets
             FROM menu m
             LEFT JOIN menu_repas mr ON mr.id_menu = m.id_menu
             LEFT JOIN ticket t      ON t.id_menu  = m.id_menu AND t.status != 'annule'
             GROUP BY m.id_menu
             ORDER BY m.dateMenu DESC
             LIMIT ?"
        );
        $s->execute([$limit]);
        return $s->fetchAll();
    }

    // ---- Repas CRUD ----

    public function getAllRepas(int $limit = 200, int $offset = 0): array {
        $s = $this->db->prepare(
            "SELECT * FROM repas
             ORDER BY FIELD(categorie,'entree','plat','dessert','boisson','supplement'), nom
             LIMIT ? OFFSET ?"
        );
        $s->execute([$limit, $offset]);
        return $s->fetchAll();
    }

    public function countRepas(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM repas")->fetchColumn();
    }

    public function addRepas(string $nom, string $categorie): bool {
        // prix=0 car le prix affiché est MENU_PRIX_FIXE
        return $this->db->prepare("INSERT INTO repas (nom,prix,categorie) VALUES (?,0,?)")
                        ->execute([$nom, $categorie]);
    }

    public function updateRepas(int $id, string $nom, string $categorie): bool {
        return $this->db->prepare("UPDATE repas SET nom=?,categorie=? WHERE id_repas=?")
                        ->execute([$nom, $categorie, $id]);
    }

    public function deleteRepas(int $id): bool {
        return $this->db->prepare("DELETE FROM repas WHERE id_repas=?")->execute([$id]);
    }
}
