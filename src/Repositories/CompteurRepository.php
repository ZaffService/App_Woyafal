<?php
namespace App\Repositories;

use App\Core\Abstract\AbstractRepository;
use App\Entities\CompteurEntity;

class CompteurRepository extends AbstractRepository
{
    public function findByNumero(string $numeroCompteur): ?CompteurEntity
    {
        $query = "SELECT * FROM compteurs WHERE numero_compteur = :numero AND actif = true";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':numero', $numeroCompteur);
        $stmt->execute();
        
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $data ? CompteurEntity::toObject($data) : null;
    }

    public function findByClientId(int $clientId): array
    {
        $query = "SELECT * FROM compteurs WHERE client_id = :client_id AND actif = true";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':client_id', $clientId);
        $stmt->execute();
        
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        return array_map(fn($row) => CompteurEntity::toObject($row), $results);
    }

    public function findAll(): array
    {
        $query = "SELECT * FROM compteurs ORDER BY id";
        $stmt = $this->pdo->query($query);
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        return array_map(fn($row) => CompteurEntity::toObject($row), $results);
    }
}
