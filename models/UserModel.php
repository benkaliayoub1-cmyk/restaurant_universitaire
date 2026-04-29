<?php
// ============================================================
//  models/UserModel.php
// ============================================================
class UserModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getPDO();
    }

    public function login(string $email, string $password): ?array {
        $s = $this->db->prepare(
            "SELECT u.*,
                CASE
                    WHEN a.id IS NOT NULL THEN 'admin'
                    WHEN c.id IS NOT NULL THEN 'caissier'
                    WHEN e.id IS NOT NULL THEN 'etudiant'
                    ELSE 'unknown'
                END AS role,
                e.matricule, e.soldeD17, c.postId
             FROM utilisateur u
             LEFT JOIN admin    a ON a.id = u.id
             LEFT JOIN caissier c ON c.id = u.id
             LEFT JOIN etudiant e ON e.id = u.id
             WHERE u.email = ?"
        );
        $s->execute([$email]);
        $user = $s->fetch();
        if (!$user || !password_verify($password, $user['motDePasse'])) return null;
        unset($user['motDePasse']);
        return $user;
    }

    public function registerEtudiant(string $nom, string $email, string $pwd, string $matricule): bool {
        try {
            $this->db->beginTransaction();
            $hash = password_hash($pwd, PASSWORD_DEFAULT);
            $s = $this->db->prepare("INSERT INTO utilisateur (nom,email,motDePasse) VALUES (?,?,?)");
            $s->execute([$nom, $email, $hash]);
            $id = (int)$this->db->lastInsertId();
            $this->db->prepare("INSERT INTO etudiant (id,matricule,soldeD17) VALUES (?,?,0)")
                     ->execute([$id, $matricule]);
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function emailExists(string $email): bool {
        $s = $this->db->prepare("SELECT id FROM utilisateur WHERE email=?");
        $s->execute([$email]);
        return (bool)$s->fetch();
    }

    public function matriculeExists(string $mat): bool {
        $s = $this->db->prepare("SELECT id FROM etudiant WHERE matricule=?");
        $s->execute([$mat]);
        return (bool)$s->fetch();
    }

    public function getById(int $id): ?array {
        $s = $this->db->prepare(
            "SELECT u.id, u.nom, u.email,
                CASE WHEN a.id IS NOT NULL THEN 'admin'
                     WHEN c.id IS NOT NULL THEN 'caissier'
                     WHEN e.id IS NOT NULL THEN 'etudiant'
                     ELSE 'unknown' END AS role,
                e.matricule, e.soldeD17
             FROM utilisateur u
             LEFT JOIN admin    a ON a.id=u.id
             LEFT JOIN caissier c ON c.id=u.id
             LEFT JOIN etudiant e ON e.id=u.id
             WHERE u.id=?"
        );
        $s->execute([$id]);
        return $s->fetch() ?: null;
    }

    public function updateProfile(int $id, string $nom, string $email): bool {
        return $this->db->prepare("UPDATE utilisateur SET nom=?,email=? WHERE id=?")
                        ->execute([$nom, $email, $id]);
    }

    public function updatePassword(int $id, string $newPwd): bool {
        return $this->db->prepare("UPDATE utilisateur SET motDePasse=? WHERE id=?")
                        ->execute([password_hash($newPwd, PASSWORD_DEFAULT), $id]);
    }

    public function getAll(): array {
        return $this->db->query(
            "SELECT u.id, u.nom, u.email,
                CASE WHEN a.id IS NOT NULL THEN 'admin'
                     WHEN c.id IS NOT NULL THEN 'caissier'
                     WHEN e.id IS NOT NULL THEN 'etudiant'
                     ELSE 'unknown' END AS role,
                e.matricule
             FROM utilisateur u
             LEFT JOIN admin    a ON a.id=u.id
             LEFT JOIN caissier c ON c.id=u.id
             LEFT JOIN etudiant e ON e.id=u.id
             ORDER BY u.id DESC"
        )->fetchAll();
    }

    public function deleteUser(int $id): bool {
        return $this->db->prepare("DELETE FROM utilisateur WHERE id=?")->execute([$id]);
    }

    public function updateUser(int $id, string $nom, string $email): bool {
        return $this->db->prepare("UPDATE utilisateur SET nom=?,email=? WHERE id=?")
                        ->execute([$nom, $email, $id]);
    }

    public function countEtudiants(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM etudiant")->fetchColumn();
    }
}
