# AppWoyofal - API de Simulation Woyofal

## Description
AppWoyofal est une API REST qui simule le système de prépaiement électrique de la SENELEC (Woyofal). Elle permet aux clients d'acheter du crédit électrique en utilisant leur solde MAXITSA selon un système de tranches tarifaires.

## Fonctionnalités

### 🔋 Achat de crédit électrique
- Vérification de l'existence du compteur
- Validation du solde MAXITSA du client
- Calcul automatique des kWh selon les tranches tarifaires
- Génération de code de recharge unique
- Journalisation complète des transactions

### 📊 Système de tranches
- **Tranche 1** : 0-150 kWh à 91 FCFA/kWh
- **Tranche 2** : 151-250 kWh à 102 FCFA/kWh  
- **Tranche 3** : 251-400 kWh à 116 FCFA/kWh
- **Tranche 4** : +400 kWh à 132 FCFA/kWh

Les tranches se remettent à zéro chaque mois.

## Architecture

### Principes appliqués
- **SOLID** : Respect des 5 principes de conception
- **Injection de dépendances** : Via un container IoC personnalisé
- **Repository Pattern** : Séparation de la logique d'accès aux données
- **Service Layer** : Logique métier centralisée

### Structure du projet
\`\`\`
AppWoyofal/
├── App/Core/           # Classes core (Database, Container, Abstract)
├── src/
│   ├── Controllers/    # Contrôleurs API
│   ├── Services/       # Services métier
│   ├── Repositories/   # Accès aux données
│   ├── Entities/       # Modèles de données
│   └── Enums/         # Énumérations
├── routes/            # Configuration des routes
├── migrations/        # Scripts de migration
├── seeders/          # Données de test
└── public/           # Point d'entrée web
\`\`\`

## Installation

### Prérequis
- PHP 8.0+
- PostgreSQL
- Composer
- Docker (optionnel)

### Installation locale
\`\`\`bash
# Cloner le projet
git clone <repository-url>
cd appwoyofal

# Installer les dépendances
composer install

# Configurer l'environnement
cp .env.example .env
# Éditer .env avec vos paramètres de base de données

# Exécuter les migrations
php migrations/Migration.php

# Insérer les données de test
php seeders/Seeder.php

# Démarrer le serveur de développement
php -S localhost:8000 -t public/
\`\`\`

### Installation avec Docker
\`\`\`bash
# Construire et démarrer les services
docker-compose up -d

# L'API sera accessible sur http://localhost:8080
\`\`\`

## Utilisation de l'API

### Endpoints disponibles

#### GET /
Informations sur le service
\`\`\`json
{
  "data": {
    "service": "AppWoyofal",
    "version": "1.0.0",
    "description": "API de simulation du système de prépaiement électrique Woyofal"
  },
  "statut": "success",
  "code": 200,
  "message": "Service AppWoyofal opérationnel"
}
\`\`\`

#### POST /achat
Effectuer un achat de crédit électrique

**Paramètres :**
\`\`\`json
{
  "numero_compteur": "123456789",
  "montant": 5000,
  "localisation": "Dakar, Sénégal"
}
\`\`\`

**Réponse succès :**
\`\`\`json
{
  "data": {
    "compteur": "123456789",
    "reference": "WOY-20250727001",
    "code": "12345678901234",
    "date": "27-07-2025 10:30",
    "tranche": "1",
    "prix": "91",
    "nbreKwt": "54.95",
    "client": "Mamadou DIOP"
  },
  "statut": "success",
  "code": 200,
  "message": "Achat effectué avec succès"
}
\`\`\`

**Réponse erreur :**
\`\`\`json
{
  "data": null,
  "statut": "error",
  "code": 404,
  "message": "Le numéro de compteur non retrouvé"
}
\`\`\`

## Base de données

### Tables principales
- **clients** : Informations des clients et solde MAXITSA
- **compteurs** : Compteurs électriques associés aux clients
- **achats** : Historique des achats de crédit
- **journal** : Journalisation de toutes les requêtes

## Tests

### Avec REST Client (VS Code)
Créer un fichier `test.http` :

```http
### Test du service
GET http://localhost:8000/

### Test d'achat réussi
POST http://localhost:8000/achat
Content-Type: application/json

{
  "numero_compteur": "123456789",
  "montant": 5000,
  "localisation": "Dakar, Sénégal"
}

### Test compteur inexistant
POST http://localhost:8000/achat
Content-Type: application/json

{
  "numero_compteur": "999999999",
  "montant": 5000
}
