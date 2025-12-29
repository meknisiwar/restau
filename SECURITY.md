# 🔒 Guide de Sécurité - Restaurant Platform

## ✅ Mesures de Sécurité Implémentées

### 1. Protection contre XSS (Cross-Site Scripting)
- **Auto-escaping Twig** : Activé par défaut pour tous les templates
- **Content Security Policy** : Headers HTTP configurés
- **Validation des entrées** : Filtrage et assainissement des données utilisateur
- **Headers de sécurité** : X-Content-Type-Options, X-Frame-Options, X-XSS-Protection

### 2. Protection contre l'Injection SQL
- **ORM Doctrine** : Requêtes préparées automatiques
- **Validation des paramètres** : Vérification stricte des entrées
- **Type casting** : Validation des types de données
- **Requêtes paramétrées** : Aucune concaténation directe de SQL

### 3. Protection CSRF (Cross-Site Request Forgery)
- **Tokens CSRF** : Activés sur tous les formulaires
- **Validation côté serveur** : Vérification obligatoire des tokens
- **Session sécurisée** : Configuration HttpOnly, Secure, SameSite

### 4. Authentification et Autorisation
- **Hashage des mots de passe** : bcrypt/Argon2
- **Rate limiting** : Protection contre le brute force
- **Sessions sécurisées** : Configuration optimisée
- **Contrôle d'accès** : Rôles utilisateur stricts

### 5. Headers de Sécurité HTTP
```
X-Content-Type-Options: nosniff
X-Frame-Options: DENY
X-XSS-Protection: 1; mode=block
Strict-Transport-Security: max-age=31536000; includeSubDomains
Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net
```

## 🔧 Configuration de Sécurité

### Variables d'Environnement (.env.prod)
```bash
APP_ENV=prod
APP_DEBUG=0
APP_SECRET=GENEREZ_UNE_CLE_SECURISEE_DE_32_CARACTERES
DATABASE_URL=mysql://user:password@host/database
```

### Configuration Symfony
- **framework.yaml** : CSRF activé, sessions sécurisées
- **security.yaml** : Authentification renforcée
- **twig.yaml** : Auto-escaping HTML activé

## 📝 Checklist de Déploiement

### Avant le Déploiement
- [ ] Vérifier que APP_ENV=prod
- [ ] Vérifier que APP_DEBUG=0
- [ ] Générer un APP_SECRET sécurisé
- [ ] Configurer HTTPS
- [ ] Configurer les logs de sécurité
- [ ] Tester les formulaires CSRF
- [ ] Vérifier les permissions de fichiers

### Commandes de Déploiement
```bash
# Installation optimisée
composer install --no-dev --optimize-autoloader

# Cache de production
php bin/console cache:clear --env=prod --no-debug
php bin/console cache:warmup --env=prod --no-debug

# Vérification de sécurité
php security-check.php

# Permissions
chmod -R 755 var/
chown -R www-data:www-data var/
```

## 🔍 Monitoring et Logs

### Logs de Sécurité
- **security.log** : Tentatives d'authentification
- **auth.log** : Connexions et déconnexions
- **critical.log** : Erreurs critiques

### Surveillance
- Monitoring des tentatives de connexion échouées
- Alertes sur les erreurs critiques
- Logs des violations CSRF
- Surveillance des accès admin

## 🚨 Plan de Réponse aux Incidents

### En cas de Tentative d'Intrusion
1. Analyser les logs de sécurité
2. Bloquer les IP suspectes
3. Renforcer les mots de passe
4. Vérifier l'intégrité des données

### Mise à Jour de Sécurité
1. Surveiller les alertes Symfony
2. Appliquer les correctifs rapidement
3. Tester sur environnement de test
4. Déployer en production

## 📋 Tests de Sécurité

### Tests Manuels
- Tentatives d'injection SQL
- Tests XSS sur les formulaires
- Vérification des tokens CSRF
- Tests d'autorisation

### Outils Recommandés
- **Symfony Security Checker**
- **OWASP ZAP** pour les tests de pénétration
- **Audit Composer** pour les vulnérabilités

## 🌐 Configuration Serveur Web

### Apache (.htaccess)
```apache
# Sécurité des headers
Header always set X-Frame-Options "DENY"
Header always set X-Content-Type-Options "nosniff"
Header always set X-XSS-Protection "1; mode=block"

# Protection des fichiers sensibles
<Files ".env*">
    Require all denied
</Files>

# HTTPS redirect
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### Nginx
```nginx
# Headers de sécurité
add_header X-Frame-Options "DENY" always;
add_header X-Content-Type-Options "nosniff" always;
add_header X-XSS-Protection "1; mode=block" always;

# Protection des fichiers
location ~ /\. {
    deny all;
}

location ~ \.env {
    deny all;
}
```

---

**⚠️ Important** : Testez toujours les configurations de sécurité sur un environnement de test avant la production.
