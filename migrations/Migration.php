<?php
namespace App\Migrations;
require_once __DIR__ . '/../vendor/autoload.php';

use PDO;
use App\Core\Database;
use App\Src\Enums\ErrorEnum;
use App\Src\Enums\TextEnum;
use App\Src\Enums\SuccessEnum;

class Migration
{
    private string $dbName;
    private Database $database;

    public function __construct()
    {
        $this->database = Database::getInstance();
        $this->dbName = getenv('DB_NAME') ?: $_ENV['DB_NAME'] ?? 'woyafal';
        $this->database->setDatabaseName($this->dbName);
    }

    public function run(): void
    {
        echo "--- Lancement de la migration AppWoyofal ---\n\n";
        $this->migrateTables();
        echo "\n--- Migration terminée avec succès ---\n";
        echo "Vous pouvez maintenant exécuter le seeder avec : php seeders/Seeder.php\n\n";
    }

    private function migrateTables(): void
    {
        $pdo = $this->database->getConnection();

        try {
            echo "Création des tables...\n";
            $this->createClientsTable($pdo);
            $this->createCompteursTable($pdo);
            $this->createAchatsTable($pdo);
            $this->createJournalTable($pdo);
            echo SuccessEnum::MIGRATION_SUCCESS->value . " dans '{$this->dbName}'.\n\n";
        } catch (\PDOException $e) {
            exit(ErrorEnum::ECHEC_CREATION_TABLE->value . $e->getMessage() . "\n");
        }
    }

    private function createClientsTable(PDO $pdo): void
    {
        try {
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS clients (
                    id SERIAL PRIMARY KEY,
                    nom VARCHAR(100) NOT NULL,
                    prenom VARCHAR(100) NOT NULL,
                    telephone VARCHAR(20) UNIQUE NOT NULL,
                    email VARCHAR(255),
                    solde_maxitsa DECIMAL(10,2) DEFAULT 0.00,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                );
            ");
            
            echo "✓ Table 'clients' créée avec succès.\n";
           
        } catch (\Throwable $th) {
            throw new \Exception("Erreur lors de la création de la table clients: " . $th->getMessage());
        }
    }

    private function createCompteursTable(PDO $pdo): void
    {
        try {
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS compteurs (
                    id SERIAL PRIMARY KEY,
                    numero_compteur VARCHAR(20) UNIQUE NOT NULL,
                    client_id INTEGER NOT NULL,
                    adresse TEXT NOT NULL,
                    actif BOOLEAN DEFAULT TRUE,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
                );
            ");
            
            echo "✓ Table 'compteurs' créée avec succès.\n";
           
        } catch (\Throwable $th) {
            throw new \Exception("Erreur lors de la création de la table compteurs: " . $th->getMessage());
        }
    }

    private function createAchatsTable(PDO $pdo): void
    {
        try {
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS achats (
                    id SERIAL PRIMARY KEY,
                    reference VARCHAR(50) UNIQUE NOT NULL,
                    code_recharge VARCHAR(14) NOT NULL,
                    numero_compteur VARCHAR(20) NOT NULL,
                    client_id INTEGER NOT NULL,
                    montant DECIMAL(10,2) NOT NULL,
                    nombre_kwh DECIMAL(8,2) NOT NULL,
                    tranche INTEGER NOT NULL,
                    prix_kwh DECIMAL(6,2) NOT NULL,
                    date_achat TIMESTAMP NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
                );
            ");
            
            echo "✓ Table 'achats' créée avec succès.\n";
           
        } catch (\Throwable $th) {
            throw new \Exception("Erreur lors de la création de la table achats: " . $th->getMessage());
        }
    }

    private function createJournalTable(PDO $pdo): void
    {
        try {
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS journal (
                    id SERIAL PRIMARY KEY,
                    numero_compteur VARCHAR(20) NOT NULL,
                    ip VARCHAR(45) NOT NULL,
                    localisation VARCHAR(255),
                    statut VARCHAR(20) NOT NULL CHECK (statut IN ('success', 'error')),
                    message TEXT,
                    code_recharge VARCHAR(14),
                    nombre_kwh DECIMAL(8,2) DEFAULT 0,
                    date_recherche TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                );
            ");
            
            echo "✓ Table 'journal' créée avec succès.\n";
           
        } catch (\Throwable $th) {
            throw new \Exception("Erreur lors de la création de la table journal: " . $th->getMessage());
        }
    }
}

// Point d'entrée
(new Migration())->run();
