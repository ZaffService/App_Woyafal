<?php
namespace App\Entities;

use App\Core\Abstract\AbstractEntity;

class CompteurEntity extends AbstractEntity
{
    private int $id;
    private string $numero_compteur;
    private int $client_id;
    private string $adresse;
    private bool $actif;
    private string $created_at;
    private string $updated_at;

    public function __construct(
        int $id = 0,
        string $numero_compteur = '',
        int $client_id = 0,
        string $adresse = '',
        bool $actif = true,
        string $created_at = '',
        string $updated_at = ''
    ) {
        $this->id = $id;
        $this->numero_compteur = $numero_compteur;
        $this->client_id = $client_id;
        $this->adresse = $adresse;
        $this->actif = $actif;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    // Getters
    public function getId(): int { return $this->id; }
    public function getNumeroCompteur(): string { return $this->numero_compteur; }
    public function getClientId(): int { return $this->client_id; }
    public function getAdresse(): string { return $this->adresse; }
    public function isActif(): bool { return $this->actif; }
    public function getCreatedAt(): string { return $this->created_at; }
    public function getUpdatedAt(): string { return $this->updated_at; }

    // Setters
    public function setId(int $id): self { $this->id = $id; return $this; }
    public function setNumeroCompteur(string $numero): self { $this->numero_compteur = $numero; return $this; }
    public function setClientId(int $client_id): self { $this->client_id = $client_id; return $this; }
    public function setAdresse(string $adresse): self { $this->adresse = $adresse; return $this; }
    public function setActif(bool $actif): self { $this->actif = $actif; return $this; }

    public static function toObject(array $data): static
    {
        return new static(
            $data['id'] ?? 0,
            $data['numero_compteur'] ?? '',
            $data['client_id'] ?? 0,
            $data['adresse'] ?? '',
            (bool)($data['actif'] ?? true),
            $data['created_at'] ?? '',
            $data['updated_at'] ?? ''
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'numero_compteur' => $this->getNumeroCompteur(),
            'client_id' => $this->getClientId(),
            'adresse' => $this->getAdresse(),
            'actif' => $this->isActif(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt()
        ];
    }
}
