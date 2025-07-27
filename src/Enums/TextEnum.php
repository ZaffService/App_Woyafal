<?php
namespace App\Src\Enums;

enum TextEnum: string
{
    case QUESTION_BASE_EXISTANTE = "Avez-vous déjà une base de données ? (oui/non): ";
    case QUESTION_NOM_BASE = "Entrez le nom de votre base de données existante: ";
    case READLINE_NOM_NEW_BASE = "Entrez le nom de la nouvelle base de données: ";
}
