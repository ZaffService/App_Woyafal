<?php
namespace App\Entities;

use App\Core\Abstract\AbstractEntity;

class JournalEntity extends AbstractEntity
{
    private int $id;
    private string $numero_compteur;
    private string $ip;
    private string $localisation;
    private string $statut;
    private string $message;
    private string $code_recharge;
    private float $nombre_kwh;
    private string $date_recherche;

    public function __construct(
        int $id = 0,
        string $numero_compteur = '',
        string $ip = '',
        string $localisation = '',
        string $statut = '',
        string $message = '',
        string $code_recharge = '',
        float $nombre_kwh = 0.0,
        string $date_recherche = ''
    ) {
        $this->id = $id;
        $this->numero_compteur = $numero_compteur;
        $this->ip = $ip;
        $this->localisation = $localisation;
        $this->statut = $statut;
        $this->message = $message;
        $this->code_recharge = $code_recharge;
        $this->nombre_kwh = $nombre_kwh;
        $this->date_recherche = $date_recherche;
    }

    // Getters
    public function getId(): int { return $this->id; }
    public function getNumeroCompteur(): string { return $this->numero_compteur; }
    public function getIp(): string { return $this->ip; }
    public function getLocalisation(): string { return $this->localisation; }
    public function getStatut(): string { return $this->statut; }
    public function getMessage(): string { return $this->message; }
    public function getCodeRecharge(): string { return $this->code_recharge; }
    public function getNombreKwh(): float { return $this->nombre_kwh; }
    public function getDateRecherche(): string { return $this->date_recherche; }

    public static function toObject(array $data): static
    {
        return new static(
            $data['id'] ?? 0,
            $data['numero_compteur'] ?? '',
            $data['ip'] ?? '',
            $data['localisation'] ?? '',
            $data['statut'] ?? '',
            $data['message'] ?? '',
            $data['code_recharge'] ?? '',
            (float)($data['nombre_kwh'] ?? 0),
            $data['date_recherche'] ?? ''
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'numero_compteur' => $this->getNumeroCompteur(),
            'ip' => $this->getIp(),
            'localisation' => $this->getLocalisation(),
            'statut' => $this->getStatut(),
            'message' => $this->getMessage(),
            'code_recharge' => $this->getCodeRecharge(),
            'nombre_kwh' => $this->getNombreKwh(),
            'date_recherche' => $this->getDateRecherche()
        ];
    }
}
