<?php
namespace App\Src\Enums;

enum ErrorEnum: string
{
    case ECHEC_CONNEXION = "❌ Erreur de connexion à la base de données: ";
    case ECHEC_CREATE_DATABASE = "❌ Erreur lors de la création de la base de données: ";
    case ECHEC_CREATION_TABLE = "❌ Erreur lors de la création des tables: ";
    case COMPTEUR_NON_TROUVE = "Le numéro de compteur non retrouvé";
    case SOLDE_INSUFFISANT = "Solde insuffisant sur votre compte MAXITSA";
    case MONTANT_INVALIDE = "Le montant doit être supérieur à 0";
    case ERREUR_SERVEUR = "Erreur serveur interne";
}
