#!/bin/bash
# Build script pour Railway.app

set -e

echo "🚀 Build Railway en cours..."

# Installation des dépendances
composer install --no-dev --optimize-autoloader --no-progress

# Configuration de l'environnement
export APP_ENV=prod
export APP_DEBUG=0

# Cache
php bin/console cache:clear --env=prod --no-debug --no-warmup
php bin/console cache:warmup --env=prod --no-debug

# Assets
php bin/console assets:install --env=prod

echo "✅ Build Railway terminé !"
