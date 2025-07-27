<?php 
namespace App\Seeders;
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Enums\SuccessEnum;
use App\Src\Enums\ErrorEnum;

class Seeder
{
    private \PDO $pdo;
    private Database $database;

    public function __construct()
    {
        $this->database = Database::getInstance();
    }

    public function run(): bool
    {
        if (!$this->checkDatabaseExists()) {
            echo ErrorEnum::ECHEC_CONNEXION->value . " La base de données n'existe pas. Veuillez d'abord exécuter les migrations.\n";
            return false;
        }

        try {
            $this->pdo = $this->database->getConnection();
            $this->pdo->beginTransaction(); 

            $this->seedClients();
            $this->seedCompteurs();
            $this->seedAchats();
            $this->seedJournal();

            $this->pdo->commit();
            echo SuccessEnum::MIGRATION_SUCCESS->value . " Données insérées avec succès.\n";
            return true;

        } catch (\PDOException $e) {
            if (isset($this->pdo)) {
                $this->pdo->rollBack();
            }
            error_log("Erreur lors du seeding: " . $e->getMessage());
            echo ErrorEnum::ECHEC_CREATION_TABLE->value . " Erreur : " . $e->getMessage() . "\n";
            return false;
        }
    }

    private function checkDatabaseExists(): bool
    {
        try {
            $this->pdo = $this->database->getConnection();
            
            $tables = ['clients', 'compteurs', 'achats', 'journal'];
            foreach ($tables as $table) {
                $result = $this->pdo->query("SELECT to_regclass('public.$table')");
                if ($result->fetchColumn() === null) {
                    echo "La table '$table' n'existe pas. Veuillez d'abord exécuter les migrations.\n";
                    return false;
                }
            }
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    private function seedClients(): void
    {
        echo "Insertion des données dans la table 'clients'...\n";

        $clients = [
            [
                'nom' => 'DIOP',
                'prenom' => 'Mamadou',
                'telephone' => '771234567',
                'email' => 'mamadou.diop@email.com',
                'solde_maxitsa' => 50000.00
            ],
            [
                'nom' => 'FALL',
                'prenom' => 'Fatou',
                'telephone' => '779876543',
                'email' => 'fatou.fall@email.com',
                'solde_maxitsa' => 75000.00
            ],
            [
                'nom' => 'NDIAYE',
                'prenom' => 'Aminata',
                'telephone' => '775555666',
                'email' => 'aminata.ndiaye@email.com',
                'solde_maxitsa' => 30000.00
            ],
            [
                'nom' => 'SARR',
                'prenom' => 'Ousmane',
                'telephone' => '778888999',
                'email' => 'ousmane.sarr@email.com',
                'solde_maxitsa' => 100000.00
            ]
        ];

        $stmt = $this->pdo->prepare("
            INSERT INTO clients (nom, prenom, telephone, email, solde_maxitsa)
            VALUES (:nom, :prenom, :telephone, :email, :solde_maxitsa)
        ");

        foreach ($clients as $client) {
            $stmt->execute($client);
        }

        echo count($clients) . " clients insérés avec succès.\n";
    }

    private function seedCompteurs(): void
    {
        echo "Insertion des données dans la table 'compteurs'...\n";

        $compteurs = [
            [
                'numero_compteur' => '123456789',
                'client_id' => 1,
                'adresse' => 'Dakar, Plateau, Rue 10'
            ],
            [
                'numero_compteur' => '987654321',
                'client_id' => 2,
                'adresse' => 'Saint-Louis, Centre-ville'
            ],
            [
                'numero_compteur' => '555666777',
                'client_id' => 3,
                'adresse' => 'Thiès, Quartier Résidentiel'
            ],
            [
                'numero_compteur' => '888999000',
                'client_id' => 4,
                'adresse' => 'Mbour, Zone Touristique'
            ],
            [
                'numero_compteur' => '111222333',
                'client_id' => 1,
                'adresse' => 'Dakar, Almadies, Villa 25'
            ]
        ];

        $stmt = $this->pdo->prepare("
            INSERT INTO compteurs (numero_compteur, client_id, adresse)
            VALUES (:numero_compteur, :client_id, :adresse)
        ");

        foreach ($compteurs as $compteur) {
            $stmt->execute($compteur);
        }

        echo count($compteurs) . " compteurs insérés avec succès.\n";
    }

    private function seedAchats(): void
    {
        echo "Insertion des données dans la table 'achats'...\n";

        $achats = [
            [
                'reference' => 'WOY-20250727001',
                'code_recharge' => '12345678901234',
                'numero_compteur' => '123456789',
                'client_id' => 1,
                'montant' => 5000.00,
                'nombre_kwh' => 54.95,
                'tranche' => 1,
                'prix_kwh' => 91.00,
                'date_achat' => '2025-07-27 10:30:00'
            ],
            [
                'reference' => 'WOY-20250727002',
                'code_recharge' => '98765432109876',
                'numero_compteur' => '987654321',
                'client_id' => 2,
                'montant' => 10000.00,
                'nombre_kwh' => 109.89,
                'tranche' => 1,
                'prix_kwh' => 91.00,
                'date_achat' => '2025-07-27 14:15:00'
            ]
        ];

        $stmt = $this->pdo->prepare("
            INSERT INTO achats (reference, code_recharge, numero_compteur, client_id, montant, nombre_kwh, tranche, prix_kwh, date_achat)
            VALUES (:reference, :code_recharge, :numero_compteur, :client_id, :montant, :nombre_kwh, :tranche, :prix_kwh, :date_achat)
        ");

        foreach ($achats as $achat) {
            $stmt->execute($achat);
        }

        echo count($achats) . " achats insérés avec succès.\n";
    }

    private function seedJournal(): void
    {
        echo "Insertion des données dans la table 'journal'...\n";

        $journaux = [
            [
                'numero_compteur' => '123456789',
                'ip' => '192.168.1.1',
                'localisation' => 'Dakar, Sénégal',
                'statut' => 'success',
                'message' => 'Achat effectué avec succès',
                'code_recharge' => '12345678901234',
                'nombre_kwh' => 54.95
            ],
            [
                'numero_compteur' => '999999999',
                'ip' => '192.168.1.2',
                'localisation' => 'Thiès, Sénégal',
                'statut' => 'error',
                'message' => 'Le numéro de compteur non retrouvé',
                'code_recharge' => '',
                'nombre_kwh' => 0
            ],
            [
                'numero_compteur' => '987654321',
                'ip' => '10.0.0.1',
                'localisation' => 'Saint-Louis, Sénégal',
                'statut' => 'success',
                'message' => 'Achat effectué avec succès',
                'code_recharge' => '98765432109876',
                'nombre_kwh' => 109.89
            ]
        ];

        $stmt = $this->pdo->prepare("
            INSERT INTO journal (numero_compteur, ip, localisation, statut, message, code_recharge, nombre_kwh)
            VALUES (:numero_compteur, :ip, :localisation, :statut, :message, :code_recharge, :nombre_kwh)
        ");

        foreach ($journaux as $journal) {
            $stmt->execute($journal);
        }

        echo count($journaux) . " entrées de journal insérées avec succès.\n";
    }
}

// Point d'entrée
(new Seeder())->run();
