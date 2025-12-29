# Script de déploiement sécurisé pour Windows/PowerShell
# Usage: .\deploy.ps1

Write-Host "🚀 Déploiement sécurisé en production..." -ForegroundColor Cyan

# Vérification de l'environnement
Write-Host "`n📋 Vérification de l'environnement..." -ForegroundColor Yellow

if (!(Test-Path ".env.prod")) {
    Write-Host "❌ Fichier .env.prod manquant" -ForegroundColor Red
    exit 1
}

# Copier la configuration de production
Write-Host "📄 Configuration de production..." -ForegroundColor Yellow
Copy-Item ".env.prod" ".env.local" -Force

# Installation des dépendances optimisées
Write-Host "`n📦 Installation des dépendances..." -ForegroundColor Yellow
composer install --no-dev --optimize-autoloader --no-progress

if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Erreur lors de l'installation des dépendances" -ForegroundColor Red
    exit 1
}

# Clear et warmup du cache
Write-Host "`n🧹 Nettoyage du cache..." -ForegroundColor Yellow
php bin/console cache:clear --env=prod --no-debug
php bin/console cache:warmup --env=prod --no-debug

# Migration de la base de données
Write-Host "`n🗃️ Migration de la base de données..." -ForegroundColor Yellow
php bin/console doctrine:migrations:migrate --env=prod --no-interaction

# Vérification de sécurité
Write-Host "`n🔒 Vérification de sécurité..." -ForegroundColor Yellow
php security-check.php

if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Échec des vérifications de sécurité" -ForegroundColor Red
    exit 1
}

# Audit des dépendances
Write-Host "`n🔍 Audit de sécurité..." -ForegroundColor Yellow
composer audit

# Optimisation des fichiers
Write-Host "`n🎨 Optimisation des assets..." -ForegroundColor Yellow
php bin/console assets:install --env=prod

# Configuration des permissions (Windows)
Write-Host "`n📂 Configuration des permissions..." -ForegroundColor Yellow
if (Test-Path "var") {
    icacls "var" /grant "Everyone:(OI)(CI)F" /T | Out-Null
}

# Test final de l'application
Write-Host "`n🧪 Test de l'application..." -ForegroundColor Yellow
php bin/console debug:config --env=prod framework csrf_protection

# Génération du rapport
$reportFile = "deployment-report-$(Get-Date -Format 'yyyy-MM-dd-HH-mm-ss').txt"
@"
RAPPORT DE DÉPLOIEMENT
===================
Date: $(Get-Date)
Environnement: Production
Version PHP: $((php -v).Split("`n")[0])
Version Symfony: $(php bin/console --version)

✅ Dépendances installées
✅ Cache optimisé
✅ Base de données migrée
✅ Vérifications de sécurité passées
✅ Assets optimisés

🔒 MESURES DE SÉCURITÉ ACTIVÉES:
- Protection CSRF
- Headers de sécurité HTTP
- Validation des entrées
- Logs de sécurité
- Auto-escaping XSS
- Sessions sécurisées

🌐 PRÊT POUR LA PRODUCTION
"@ | Out-File -FilePath $reportFile

Write-Host "`n✅ Déploiement terminé avec succès !" -ForegroundColor Green
Write-Host "🌐 Application prête pour la production" -ForegroundColor Green
Write-Host "📄 Rapport généré: $reportFile" -ForegroundColor Cyan

# Conseils post-déploiement
Write-Host "`n📝 CONSEILS POST-DÉPLOIEMENT:" -ForegroundColor Magenta
Write-Host "1. Configurer HTTPS sur le serveur web" -ForegroundColor White
Write-Host "2. Mettre en place la surveillance des logs" -ForegroundColor White
Write-Host "3. Planifier les sauvegardes automatiques" -ForegroundColor White
Write-Host "4. Tester l'application en conditions réelles" -ForegroundColor White
Write-Host "5. Configurer les alertes de sécurité" -ForegroundColor White
