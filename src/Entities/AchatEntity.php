<?php
namespace App\Entities;

use App\Core\Abstract\AbstractEntity;

class AchatEntity extends AbstractEntity
{
    private int $id;
    private string $reference;
    private string $code_recharge;
    private string $numero_compteur;
    private int $client_id;
    private float $montant;
    private float $nombre_kwh;
    private int $tranche;
    private float $prix_kwh;
    private string $date_achat;
    private string $created_at;

    public function __construct(
        int $id = 0,
        string $reference = '',
        string $code_recharge = '',
        string $numero_compteur = '',
        int $client_id = 0,
        float $montant = 0.0,
        float $nombre_kwh = 0.0,
        int $tranche = 1,
        float $prix_kwh = 0.0,
        string $date_achat = '',
        string $created_at = ''
    ) {
        $this->id = $id;
        $this->reference = $reference;
        $this->code_recharge = $code_recharge;
        $this->numero_compteur = $numero_compteur;
        $this->client_id = $client_id;
        $this->montant = $montant;
        $this->nombre_kwh = $nombre_kwh;
        $this->tranche = $tranche;
        $this->prix_kwh = $prix_kwh;
        $this->date_achat = $date_achat;
        $this->created_at = $created_at;
    }

    // Getters
    public function getId(): int { return $this->id; }
    public function getReference(): string { return $this->reference; }
    public function getCodeRecharge(): string { return $this->code_recharge; }
    public function getNumeroCompteur(): string { return $this->numero_compteur; }
    public function getClientId(): int { return $this->client_id; }
    public function getMontant(): float { return $this->montant; }
    public function getNombreKwh(): float { return $this->nombre_kwh; }
    public function getTranche(): int { return $this->tranche; }
    public function getPrixKwh(): float { return $this->prix_kwh; }
    public function getDateAchat(): string { return $this->date_achat; }
    public function getCreatedAt(): string { return $this->created_at; }

    // Setters
    public function setId(int $id): self { $this->id = $id; return $this; }
    public function setReference(string $reference): self { $this->reference = $reference; return $this; }
    public function setCodeRecharge(string $code): self { $this->code_recharge = $code; return $this; }
    public function setNumeroCompteur(string $numero): self { $this->numero_compteur = $numero; return $this; }
    public function setClientId(int $client_id): self { $this->client_id = $client_id; return $this; }
    public function setMontant(float $montant): self { $this->montant = $montant; return $this; }
    public function setNombreKwh(float $kwh): self { $this->nombre_kwh = $kwh; return $this; }
    public function setTranche(int $tranche): self { $this->tranche = $tranche; return $this; }
    public function setPrixKwh(float $prix): self { $this->prix_kwh = $prix; return $this; }
    public function setDateAchat(string $date): self { $this->date_achat = $date; return $this; }

    public static function toObject(array $data): static
    {
        return new static(
            $data['id'] ?? 0,
            $data['reference'] ?? '',
            $data['code_recharge'] ?? '',
            $data['numero_compteur'] ?? '',
            $data['client_id'] ?? 0,
            (float)($data['montant'] ?? 0),
            (float)($data['nombre_kwh'] ?? 0),
            $data['tranche'] ?? 1,
            (float)($data['prix_kwh'] ?? 0),
            $data['date_achat'] ?? '',
            $data['created_at'] ?? ''
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'reference' => $this->getReference(),
            'code_recharge' => $this->getCodeRecharge(),
            'numero_compteur' => $this->getNumeroCompteur(),
            'client_id' => $this->getClientId(),
            'montant' => $this->getMontant(),
            'nombre_kwh' => $this->getNombreKwh(),
            'tranche' => $this->getTranche(),
            'prix_kwh' => $this->getPrixKwh(),
            'date_achat' => $this->getDateAchat(),
            'created_at' => $this->getCreatedAt()
        ];
    }
}
