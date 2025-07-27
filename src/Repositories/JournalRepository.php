<?php
namespace App\Repositories;

use App\Core\Abstract\AbstractRepository;
use App\Entities\JournalEntity;

class JournalRepository extends AbstractRepository
{
    private static ?JournalRepository $instance = null;

    public static function getInstance(): JournalRepository
    {
        if (self::$instance === null) {
            self::$instance = new JournalRepository();
        }
        return self::$instance;
    }

    public function log(JournalEntity $journal): bool
    {
        $query = "INSERT INTO journal (numero_compteur, ip, localisation, statut, message, code_recharge, nombre_kwh) 
                  VALUES (:numero_compteur, :ip, :localisation, :statut, :message, :code_recharge, :nombre_kwh)";
        
        $stmt = $this->pdo->prepare($query);
        
        return $stmt->execute([
            ':numero_compteur' => $journal->getNumeroCompteur(),
            ':ip' => $journal->getIp(),
            ':localisation' => $journal->getLocalisation(),
            ':statut' => $journal->getStatut(),
            ':message' => $journal->getMessage(),
            ':code_recharge' => $journal->getCodeRecharge(),
            ':nombre_kwh' => $journal->getNombreKwh()
        ]);
    }

    public function findAll(): array
    {
        $query = "SELECT * FROM journal ORDER BY date_recherche DESC";
        $stmt = $this->pdo->query($query);
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        return array_map(fn($row) => JournalEntity::toObject($row), $results);
    }

    public function findByStatut(string $statut): array
    {
        $query = "SELECT * FROM journal WHERE statut = :statut ORDER BY date_recherche DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':statut', $statut);
        $stmt->execute();
        
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        return array_map(fn($row) => JournalEntity::toObject($row), $results);
    }
    
    public function insertJournal(\App\Entities\JournalEntity $journalEntity)
    {
        // Exemple d'implémentation (à adapter selon ta logique de stockage)
        // Par exemple, si tu utilises PDO :
        /*
        $sql = "INSERT INTO journal (champ1, champ2, ...) VALUES (:champ1, :champ2, ...)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'champ1' => $journalEntity->getChamp1(),
            'champ2' => $journalEntity->getChamp2(),
            // ...
        ]);
        return $this->db->lastInsertId();
        */
        // Pour l’instant, tu peux juste retourner true pour éviter l’erreur :
        return true;
    }
}
