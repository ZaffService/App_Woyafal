<?php
namespace App\Controllers;

use App\Core\Abstract\AbstractController;
use App\Services\WoyofalService;

class WoyofalController extends AbstractController
{
    private WoyofalService $woyofalService;

    public function __construct(WoyofalService $woyofalService)
    {
        $this->woyofalService = $woyofalService;
    }

    public function achat(): void
    {
        $data = $this->getJsonInput();

        $numeroCompteur = $data['numero_compteur'] ?? '';
        $montant = (float)($data['montant'] ?? 0);

        if (empty($numeroCompteur)) {
            $this->renderJson([
                'data' => null,
                'statut' => 'error',
                'code' => 400,
                'message' => 'Le numéro de compteur est requis'
            ], 400);
        }

        $ip = $this->getClientIp();
        $localisation = $data['localisation'] ?? 'Non spécifiée';

        $result = $this->woyofalService->effectuerAchat($numeroCompteur, $montant, $ip, $localisation);

        if ($result['success']) {
            $this->renderJson([
                'data' => $result['data'],
                'statut' => 'success',
                'code' => 200,
                'message' => 'Achat effectué avec succès'
            ], 200);
        } else {
            $this->renderJson([
                'data' => null,
                'statut' => 'error',
                'code' => $result['code'],
                'message' => $result['error']
            ], $result['code']);
        }
    }

    public function index(): void
    {
        $this->renderJson([
            'data' => [
                'service' => 'AppWoyofal',
                'version' => '1.0.0',
                'description' => 'API de simulation du système de prépaiement électrique Woyofal'
            ],
            'statut' => 'success',
            'code' => 200,
            'message' => 'Service AppWoyofal opérationnel'
        ], 200);
    }

    protected function getJsonInput(): array
    {
        $input = file_get_contents('php://input');
        return json_decode($input, true) ?? [];
    }
}
