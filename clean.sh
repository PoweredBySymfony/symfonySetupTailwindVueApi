#!/bin/bash

# Dossier de base du projet
BASE_DIR=$(pwd)

echo "Nettoyage du projet Symfony..."

# Suppression des entités dans src/Entity
echo "Suppression des entités dans src/Entity..."
find "$BASE_DIR/src/Entity" -type f -name '*.php' -delete

# Suppression des contrôleurs dans src/Controller
echo "Suppression des contrôleurs dans src/Controller..."
find "$BASE_DIR/src/Controller" -type f -name '*.php' -delete

# Suppression des formulaires dans src/Form
echo "Suppression des fichiers Form dans src/Form..."
find "$BASE_DIR/src/Form" -type f -name '*.php' -delete

# Suppression des migrations dans migrations/
echo "Suppression des fichiers de migration dans migrations/..."
find "$BASE_DIR/migrations" -type f -name '*.php' -delete

# Suppression des fichiers Twig dans templates/ sauf base.html.twig, et suppression des dossiers vides
echo "Suppression des fichiers dans templates/ sans supprimer base.html.twig, et suppression des dossiers vides..."
find "$BASE_DIR/templates" -type f -name '*.html.twig' ! -name 'base.html.twig' -delete

# Suppression des dossiers vides dans templates/
find "$BASE_DIR/templates" -type d -empty -delete

# Suppression des fichiers de cache et logs
echo "Suppression des fichiers de cache et logs..."
rm -rf "$BASE_DIR/var/cache" "$BASE_DIR/var/log"

echo "Nettoyage terminé ! Les fichiers et dossiers vides ont été supprimés."
