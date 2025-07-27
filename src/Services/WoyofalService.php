<?php
namespace App\Services;

use App\Repositories\CompteurRepository;
use App\Repositories\ClientRepository;
use App\Repositories\AchatRepository;
use App\Repositories\JournalRepository;
use App\Entities\AchatEntity;
use App\Entities\JournalEntity;
use App\Src\Enums\ErrorEnum;
use App\Src\Enums\SuccessEnum;

class WoyofalService
{
    private CompteurRepository $compteurRepository;
    private ClientRepository $clientRepository;
    private AchatRepository $achatRepository;
    private JournalRepository $journalRepository;
    private TrancheService $trancheService;

    public function __construct(
        CompteurRepository $compteurRepository,
        ClientRepository $clientRepository,
        AchatRepository $achatRepository,
        JournalRepository $journalRepository,
        TrancheService $trancheService
    ) {
        $this->compteurRepository = $compteurRepository;
        $this->clientRepository = $clientRepository;
        $this->achatRepository = $achatRepository;
        $this->journalRepository = $journalRepository;
        $this->trancheService = $trancheService;
    }

    public function effectuerAchat(string $numeroCompteur, float $montant, string $ip, string $localisation = ''): array
    {
        try {
            // Validation du montant
            if ($montant <= 0) {
                $this->journaliser($numeroCompteur, $ip, $localisation, 'error', ErrorEnum::MONTANT_INVALIDE->value);
                return [
                    'success' => false,
                    'error' => ErrorEnum::MONTANT_INVALIDE->value,
                    'code' => 400
                ];
            }

            // Vérifier l'existence du compteur
            $compteur = $this->compteurRepository->findByNumero($numeroCompteur);
            if (!$compteur) {
                $this->journaliser($numeroCompteur, $ip, $localisation, 'error', ErrorEnum::COMPTEUR_NON_TROUVE->value);
                return [
                    'success' => false,
                    'error' => ErrorEnum::COMPTEUR_NON_TROUVE->value,
                    'code' => 404
                ];
            }

            // Récupérer le client
            $client = $this->clientRepository->findById($compteur->getClientId());
            if (!$client) {
                $this->journaliser($numeroCompteur, $ip, $localisation, 'error', 'Client non trouvé');
                return [
                    'success' => false,
                    'error' => 'Client non trouvé',
                    'code' => 404
                ];
            }

            // Vérifier le solde MAXITSA
            if (!$client->peutAcheter($montant)) {
                $this->journaliser($numeroCompteur, $ip, $localisation, 'error', ErrorEnum::SOLDE_INSUFFISANT->value);
                return [
                    'success' => false,
                    'error' => ErrorEnum::SOLDE_INSUFFISANT->value,
                    'code' => 400
                ];
            }

            // Calculer la consommation mensuelle actuelle
            $moisActuel = date('Y-m-01');
            $consommationMensuelle = $this->achatRepository->getConsommationMensuelle($client->getId(), $moisActuel);

            // Calculer les kWh et la tranche
            $calculTranche = $this->trancheService->calculerKwhEtTranche($montant, $consommationMensuelle);

            // Générer les codes
            $reference = $this->genererReference();
            $codeRecharge = $this->genererCodeRecharge();
            $dateAchat = date('Y-m-d H:i:s');

            // Créer l'achat
            $achat = new AchatEntity(
                0,
                $reference,
                $codeRecharge,
                $numeroCompteur,
                $client->getId(),
                $montant,
                $calculTranche['kwh_total'],
                $calculTranche['tranche_finale'],
                $calculTranche['prix_moyen_kwh'],
                $dateAchat
            );

            // Sauvegarder l'achat
            if (!$this->achatRepository->save($achat)) {
                $this->journaliser($numeroCompteur, $ip, $localisation, 'error', 'Erreur lors de la sauvegarde');
                return [
                    'success' => false,
                    'error' => ErrorEnum::ERREUR_SERVEUR->value,
                    'code' => 500
                ];
            }

            // Débiter le compte MAXITSA
            $client->debiterSolde($montant);
            $this->clientRepository->updateSolde($client->getId(), $client->getSoldeMaxitsa());

            // Journaliser le succès
            $this->journaliser($numeroCompteur, $ip, $localisation, 'success', SuccessEnum::ACHAT_SUCCESS, $codeRecharge, $calculTranche['kwh_total']);

            return [
                'success' => true,
                'data' => [
                    'compteur' => $numeroCompteur,
                    'reference' => $reference,
                    'code' => $codeRecharge,
                    'date' => date('d-m-Y H:i', strtotime($dateAchat)),
                    'tranche' => (string)$calculTranche['tranche_finale'],
                    'prix' => (string)$calculTranche['prix_moyen_kwh'],
                    'nbreKwt' => (string)$calculTranche['kwh_total'],
                    'client' => $client->getNomComplet()
                ]
            ];

        } catch (\Exception $e) {
            $this->journaliser($numeroCompteur, $ip, $localisation, 'error', $e->getMessage());
            return [
                'success' => false,
                'error' => ErrorEnum::ERREUR_SERVEUR->value,
                'code' => 500
            ];
        }
    }

    private function genererReference(): string
    {
        return 'WOY-' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }

    private function genererCodeRecharge(): string
    {
        return str_pad(rand(10000000000000, 99999999999999), 14, '0', STR_PAD_LEFT);
    }

    private function journaliser(string $numeroCompteur, string $ip, string $localisation, string $statut, string $message, string $codeRecharge = '', float $nombreKwh = 0): void
    {
        $journal = new JournalEntity(
            0,
            $numeroCompteur,
            $ip,
            $localisation,
            $statut,
            $message,
            $codeRecharge,
            $nombreKwh
        );

        $this->journalRepository->log($journal);
    }
}
