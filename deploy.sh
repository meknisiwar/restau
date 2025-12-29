#!/bin/bash

# Script de déploiement en production
# Usage: ./deploy.sh

echo "🚀 Déploiement en production..."

# Vérification de l'environnement
echo "📋 Vérification de l'environnement..."
if [ ! -f ".env.prod" ]; then
    echo "❌ Fichier .env.prod manquant"
    exit 1
fi

# Installation des dépendances optimisées
echo "📦 Installation des dépendances..."
composer install --no-dev --optimize-autoloader

# Clear et warmup du cache
echo "🧹 Nettoyage du cache..."
php bin/console cache:clear --env=prod --no-debug
php bin/console cache:warmup --env=prod --no-debug

# Migration de la base de données
echo "🗃️ Migration de la base de données..."
php bin/console doctrine:migrations:migrate --env=prod --no-interaction

# Optimisation des assets
echo "🎨 Optimisation des assets..."
php bin/console asset-map:compile

# Vérification de la sécurité
echo "🔒 Vérification de la sécurité..."
composer audit
php bin/console debug:config security --env=prod

# Permissions
echo "📂 Configuration des permissions..."
chmod -R 755 var/
chown -R www-data:www-data var/

echo "✅ Déploiement terminé !"
echo "🌐 Application prête pour la production"
