<?php
namespace App\Entities;

use App\Core\Abstract\AbstractEntity;

class ClientEntity extends AbstractEntity
{
    private int $id;
    private string $nom;
    private string $prenom;
    private string $telephone;
    private string $email;
    private float $solde_maxitsa;
    private string $created_at;
    private string $updated_at;

    public function __construct(
        int $id = 0,
        string $nom = '',
        string $prenom = '',
        string $telephone = '',
        string $email = '',
        float $solde_maxitsa = 0.0,
        string $created_at = '',
        string $updated_at = ''
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->telephone = $telephone;
        $this->email = $email;
        $this->solde_maxitsa = $solde_maxitsa;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    // Getters
    public function getId(): int { return $this->id; }
    public function getNom(): string { return $this->nom; }
    public function getPrenom(): string { return $this->prenom; }
    public function getTelephone(): string { return $this->telephone; }
    public function getEmail(): string { return $this->email; }
    public function getSoldeMaxitsa(): float { return $this->solde_maxitsa; }
    public function getCreatedAt(): string { return $this->created_at; }
    public function getUpdatedAt(): string { return $this->updated_at; }

    // Setters
    public function setId(int $id): self { $this->id = $id; return $this; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }
    public function setPrenom(string $prenom): self { $this->prenom = $prenom; return $this; }
    public function setTelephone(string $telephone): self { $this->telephone = $telephone; return $this; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }
    public function setSoldeMaxitsa(float $solde): self { $this->solde_maxitsa = $solde; return $this; }

    public function getNomComplet(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    public function peutAcheter(float $montant): bool
    {
        return $this->solde_maxitsa >= $montant;
    }

    public function debiterSolde(float $montant): void
    {
        if ($this->peutAcheter($montant)) {
            $this->solde_maxitsa -= $montant;
        }
    }

    public static function toObject(array $data): static
    {
        return new static(
            $data['id'] ?? 0,
            $data['nom'] ?? '',
            $data['prenom'] ?? '',
            $data['telephone'] ?? '',
            $data['email'] ?? '',
            (float)($data['solde_maxitsa'] ?? 0),
            $data['created_at'] ?? '',
            $data['updated_at'] ?? ''
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'nom' => $this->getNom(),
            'prenom' => $this->getPrenom(),
            'telephone' => $this->getTelephone(),
            'email' => $this->getEmail(),
            'solde_maxitsa' => $this->getSoldeMaxitsa(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt()
        ];
    }
}
