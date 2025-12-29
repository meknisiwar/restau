# Test de l'application en mode production localement
Write-Host "🧪 Test en mode production..." -ForegroundColor Cyan

# Sauvegarde de l'environnement actuel
if (Test-Path ".env.local") {
    Copy-Item ".env.local" ".env.local.bak" -Force
    Write-Host "✅ Sauvegarde de .env.local créée" -ForegroundColor Green
}

try {
    # Configuration production
    Copy-Item ".env.prod" ".env.local" -Force
    Write-Host "✅ Configuration production activée" -ForegroundColor Green
    
    # Clear cache
    Write-Host "🧹 Nettoyage du cache..." -ForegroundColor Yellow
    php bin/console cache:clear --env=prod --no-debug
    php bin/console cache:warmup --env=prod --no-debug
    
    # Test des routes principales
    Write-Host "🌐 Test des routes principales..." -ForegroundColor Yellow
    php bin/console debug:router --env=prod | Select-String "app_home"
    
    # Test de la configuration de sécurité
    Write-Host "🔒 Vérification de la sécurité..." -ForegroundColor Yellow
    php bin/console debug:config framework csrf_protection --env=prod
    
    # Serveur de test
    Write-Host "🚀 Démarrage du serveur de test..." -ForegroundColor Green
    Write-Host "📱 Ouvrez http://localhost:8000 pour tester" -ForegroundColor Cyan
    Write-Host "⚠️  Appuyez sur Ctrl+C pour arrêter" -ForegroundColor Yellow
    
    $env:PORT = "8000"
    php -S localhost:8000 -t public/
    
} catch {
    Write-Host "❌ Erreur: $($_.Exception.Message)" -ForegroundColor Red
} finally {
    # Restauration de l'environnement
    if (Test-Path ".env.local.bak") {
        Move-Item ".env.local.bak" ".env.local" -Force
        Write-Host "✅ Configuration originale restaurée" -ForegroundColor Green
    } else {
        Remove-Item ".env.local" -Force -ErrorAction SilentlyContinue
        Write-Host "✅ Configuration de test supprimée" -ForegroundColor Green
    }
    
    # Clear cache de développement
    php bin/console cache:clear --env=dev
    Write-Host "🧹 Cache de développement restauré" -ForegroundColor Green
}
