<?php
namespace App\Repositories;

use App\Core\Abstract\AbstractRepository;
use App\Entities\ClientEntity;

class ClientRepository extends AbstractRepository
{
    public function findById(int $id): ?ClientEntity
    {
        $query = "SELECT * FROM clients WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $data ? ClientEntity::toObject($data) : null;
    }

    public function findByTelephone(string $telephone): ?ClientEntity
    {
        $query = "SELECT * FROM clients WHERE telephone = :telephone";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':telephone', $telephone);
        $stmt->execute();
        
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $data ? ClientEntity::toObject($data) : null;
    }

    public function updateSolde(int $clientId, float $nouveauSolde): bool
    {
        $query = "UPDATE clients SET solde_maxitsa = :solde, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':solde', $nouveauSolde);
        $stmt->bindParam(':id', $clientId);
        
        return $stmt->execute();
    }

    public function findAll(): array
    {
        $query = "SELECT * FROM clients ORDER BY id";
        $stmt = $this->pdo->query($query);
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        return array_map(fn($row) => ClientEntity::toObject($row), $results);
    }
}
