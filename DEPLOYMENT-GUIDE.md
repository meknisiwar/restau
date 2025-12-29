# 🚀 Guide de Déploiement - Restaurant Platform

## Option 1: Railway.app (Recommandé - Le plus simple)

### Étape 1: Créer un compte Railway
1. Allez sur [Railway.app](https://railway.app)
2. Créez un compte avec GitHub
3. Cliquez sur "New Project"

### Étape 2: Connecter votre repository
1. Créez un nouveau repository sur GitHub :
   - Allez sur [GitHub.com](https://github.com)
   - Cliquez "New repository"
   - Nom: `restaurant-platform`
   - Public ou Private
   - Cliquez "Create repository"

2. Poussez votre code vers GitHub :
```bash
git remote add origin https://github.com/VOTRE_USERNAME/restaurant-platform.git
git branch -M main
git push -u origin main
```

### Étape 3: Déployer sur Railway
1. Sur Railway, cliquez "Deploy from GitHub repo"
2. Sélectionnez votre repository `restaurant-platform`
3. Railway détectera automatiquement votre application PHP

### Étape 4: Configuration des variables d'environnement
Dans l'onglet "Variables" de votre projet Railway, ajoutez :

```
APP_ENV=prod
APP_DEBUG=0
APP_SECRET=VOTRE_CLE_SECRETE_DE_32_CARACTERES
DATABASE_URL=${{Postgres.DATABASE_URL}}  # Auto-configuré par Railway
PORT=${{PORT}}  # Auto-configuré par Railway
```

### Étape 5: Ajouter une base de données
1. Dans votre projet Railway, cliquez "New Service"
2. Sélectionnez "PostgreSQL"
3. Railway va automatiquement configurer DATABASE_URL

### Étape 6: Déploiement automatique
- Railway redéploiera automatiquement à chaque push sur GitHub
- Votre application sera accessible sur `https://VOTRE-APP.up.railway.app`

---

## Option 2: Render.com

### Étape 1: Créer un compte Render
1. Allez sur [Render.com](https://render.com)
2. Créez un compte avec GitHub

### Étape 2: Nouveau Web Service
1. Cliquez "New +" > "Web Service"
2. Connectez votre repository GitHub
3. Configuration :
   - **Name**: `restaurant-platform`
   - **Environment**: `Web Service`
   - **Build Command**: `./railway-build.sh`
   - **Start Command**: `php -S 0.0.0.0:$PORT -t public/`

### Étape 3: Variables d'environnement
```
APP_ENV=prod
APP_DEBUG=0
APP_SECRET=VOTRE_CLE_SECRETE
DATABASE_URL=postgresql://...  # De votre base PostgreSQL Render
```

### Étape 4: Base de données PostgreSQL
1. Créez un nouveau service PostgreSQL sur Render
2. Copiez l'URL de connexion dans DATABASE_URL

---

## Option 3: Heroku

### Étape 1: Installer Heroku CLI
```bash
# Windows (via chocolatey)
choco install heroku-cli

# Ou télécharger depuis https://devcenter.heroku.com/articles/heroku-cli
```

### Étape 2: Créer l'application
```bash
heroku create restaurant-platform-VOTRE-NOM
```

### Étape 3: Ajouter PostgreSQL
```bash
heroku addons:create heroku-postgresql:mini
```

### Étape 4: Configuration
```bash
heroku config:set APP_ENV=prod
heroku config:set APP_DEBUG=0
heroku config:set APP_SECRET=VOTRE_CLE_SECRETE_32_CHARS
```

### Étape 5: Déploiement
```bash
git push heroku main
heroku run php bin/console doctrine:migrations:migrate
```

---

## 🔧 Post-Déploiement

### 1. Vérifications
- [ ] Application accessible via HTTPS
- [ ] Base de données connectée
- [ ] Formulaires fonctionnels
- [ ] Authentification OK
- [ ] Admin accessible

### 2. Configuration DNS (Optionnel)
Pour utiliser votre propre domaine :
1. Achetez un domaine (ex: sur Namecheap, OVH)
2. Configurez les DNS pour pointer vers votre hébergeur
3. Configurez SSL/HTTPS

### 3. Surveillance
- Configurez les alertes d'erreur
- Surveillez les logs
- Configurez des sauvegardes automatiques

---

## 🆘 Dépannage

### Erreur "Class not found"
```bash
composer dump-autoload --optimize
```

### Erreur de base de données
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### Erreur de permissions
```bash
chmod -R 755 var/
```

### Erreur de cache
```bash
php bin/console cache:clear --env=prod
```

---

## 🎉 Félicitations !

Votre Restaurant Platform est maintenant en ligne et sécurisé !

**URL de test**: Votre application sera accessible sur l'URL fournie par votre hébergeur.

**Accès admin**: `/admin` avec vos identifiants admin.

**Fonctionnalités disponibles**:
- ✅ Commande en ligne
- ✅ Réservations
- ✅ Gestion des événements
- ✅ Panel d'administration
- ✅ Sécurité renforcée (CSRF, XSS, SQL Injection)
- ✅ Responsive design
- ✅ Prix en Dinar Tunisien
