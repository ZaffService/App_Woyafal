<?php
namespace App\Services;

class TrancheService
{
    private const TRANCHES = [
        1 => ['min' => 0, 'max' => 150, 'prix' => 91],
        2 => ['min' => 151, 'max' => 250, 'prix' => 102],
        3 => ['min' => 251, 'max' => 400, 'prix' => 116],
        4 => ['min' => 401, 'max' => PHP_INT_MAX, 'prix' => 132]
    ];

    public function calculerKwhEtTranche(float $montant, float $consommationMensuelle = 0): array
    {
        $kwhTotal = 0;
        $montantRestant = $montant;
        $trancheUtilisee = 1;
        $prixMoyenKwh = 0;
        $details = [];

        foreach (self::TRANCHES as $numTranche => $tranche) {
            if ($montantRestant <= 0) break;

            $kwhDejaConsommes = max(0, $consommationMensuelle - $tranche['min']);
            $kwhDisponiblesDansTranche = max(0, $tranche['max'] - max($tranche['min'], $consommationMensuelle));

            if ($kwhDisponiblesDansTranche > 0) {
                $kwhPossibles = $montantRestant / $tranche['prix'];
                $kwhAchetes = min($kwhPossibles, $kwhDisponiblesDansTranche);
                
                if ($kwhAchetes > 0) {
                    $coutTranche = $kwhAchetes * $tranche['prix'];
                    $kwhTotal += $kwhAchetes;
                    $montantRestant -= $coutTranche;
                    $trancheUtilisee = $numTranche;
                    
                    $details[] = [
                        'tranche' => $numTranche,
                        'kwh' => $kwhAchetes,
                        'prix_kwh' => $tranche['prix'],
                        'cout' => $coutTranche
                    ];
                }
            }

            $consommationMensuelle += $kwhAchetes ?? 0;
        }

        $prixMoyenKwh = $kwhTotal > 0 ? ($montant - $montantRestant) / $kwhTotal : 0;

        return [
            'kwh_total' => round($kwhTotal, 2),
            'tranche_finale' => $trancheUtilisee,
            'prix_moyen_kwh' => round($prixMoyenKwh, 2),
            'montant_utilise' => $montant - $montantRestant,
            'montant_restant' => $montantRestant,
            'details' => $details
        ];
    }

    public function getPrixTranche(int $tranche): float
    {
        return self::TRANCHES[$tranche]['prix'] ?? 0;
    }

    public function getAllTranches(): array
    {
        return self::TRANCHES;
    }
}
