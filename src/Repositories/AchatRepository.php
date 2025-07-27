<?php
namespace App\Repositories;

use App\Core\Abstract\AbstractRepository;
use App\Entities\AchatEntity;

class AchatRepository extends AbstractRepository
{
    public function save(AchatEntity $achat): bool
    {
        $query = "INSERT INTO achats (reference, code_recharge, numero_compteur, client_id, montant, nombre_kwh, tranche, prix_kwh, date_achat) 
                  VALUES (:reference, :code_recharge, :numero_compteur, :client_id, :montant, :nombre_kwh, :tranche, :prix_kwh, :date_achat)";
        
        $stmt = $this->pdo->prepare($query);
        
        return $stmt->execute([
            ':reference' => $achat->getReference(),
            ':code_recharge' => $achat->getCodeRecharge(),
            ':numero_compteur' => $achat->getNumeroCompteur(),
            ':client_id' => $achat->getClientId(),
            ':montant' => $achat->getMontant(),
            ':nombre_kwh' => $achat->getNombreKwh(),
            ':tranche' => $achat->getTranche(),
            ':prix_kwh' => $achat->getPrixKwh(),
            ':date_achat' => $achat->getDateAchat()
        ]);
    }

    public function findByReference(string $reference): ?AchatEntity
    {
        $query = "SELECT * FROM achats WHERE reference = :reference";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':reference', $reference);
        $stmt->execute();
        
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $data ? AchatEntity::toObject($data) : null;
    }

    public function findByClientId(int $clientId): array
    {
        $query = "SELECT * FROM achats WHERE client_id = :client_id ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':client_id', $clientId);
        $stmt->execute();
        
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        return array_map(fn($row) => AchatEntity::toObject($row), $results);
    }

    public function getConsommationMensuelle(int $clientId, string $mois): float
    {
        $query = "SELECT COALESCE(SUM(nombre_kwh), 0) as total 
                  FROM achats 
                  WHERE client_id = :client_id 
                  AND DATE_TRUNC('month', date_achat::date) = :mois::date";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':client_id', $clientId);
        $stmt->bindParam(':mois', $mois);
        $stmt->execute();
        
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return (float)($result['total'] ?? 0);
    }
}
