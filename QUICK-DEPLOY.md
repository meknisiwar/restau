# 🎯 Instructions Rapides de Déploiement

## 🚀 Option 1: Railway.app (Plus Simple)

### 1. Préparation GitHub
```bash
# Dans PowerShell
cd c:\laragon\www\restau
git add .
git commit -m "Ready for production deployment"

# Créez un repo sur GitHub puis :
git remote add origin https://github.com/VOTRE_USERNAME/restaurant-platform.git
git push -u origin main
```

### 2. Déploiement Railway
1. **Allez sur** [railway.app](https://railway.app)
2. **Connectez GitHub** et cliquez "New Project"
3. **Sélectionnez** votre repository `restaurant-platform`
4. **Ajoutez PostgreSQL** : New Service → PostgreSQL
5. **Variables** (dans l'onglet Variables) :
   ```
   APP_ENV=prod
   APP_DEBUG=0
   APP_SECRET=9478e5e00fcb2a444f655e9e4da21f0d
   ```

### 3. C'est tout ! 🎉
- Railway déploie automatiquement
- URL fournie : `https://votre-app.up.railway.app`
- SSL/HTTPS automatique

---

## 🌟 Option 2: Render.com

### 1. Sur Render.com
1. **Nouveau Web Service** depuis GitHub
2. **Commandes** :
   - Build: `composer install --no-dev --optimize-autoloader && php bin/console cache:clear --env=prod`
   - Start: `php -S 0.0.0.0:$PORT -t public/`
3. **Variables** :
   ```
   APP_ENV=prod
   APP_DEBUG=0
   APP_SECRET=9478e5e00fcb2a444f655e9e4da21f0d
   ```

### 2. Base de données
- Créez un PostgreSQL sur Render
- Copiez l'URL dans `DATABASE_URL`

---

## ⚡ Option 3: Heroku

### 1. Installation Heroku CLI
```bash
# Chocolatey (recommandé)
choco install heroku-cli
```

### 2. Déploiement
```bash
heroku create restaurant-platform-VOTRE-NOM
heroku addons:create heroku-postgresql:essential-0
heroku config:set APP_ENV=prod APP_DEBUG=0 APP_SECRET=9478e5e00fcb2a444f655e9e4da21f0d
git push heroku main
heroku run php bin/console doctrine:migrations:migrate
```

---

## 🧪 Test Local en Mode Production

```powershell
.\test-prod.ps1
```
Ouvrez http://localhost:8000 pour tester

---

## ✅ Vérifications Post-Déploiement

1. **Page d'accueil** fonctionne
2. **Connexion/Inscription** OK
3. **Commandes** fonctionnelles
4. **Admin panel** accessible (`/admin`)
5. **HTTPS** activé automatiquement

---

## 🆘 Problèmes Courants

### "Class not found"
```bash
composer dump-autoload --optimize
```

### Base de données non créée
```bash
php bin/console doctrine:database:create --env=prod
php bin/console doctrine:migrations:migrate --env=prod
```

### Erreur de cache
```bash
php bin/console cache:clear --env=prod
```

---

## 🎊 Fonctionnalités Prêtes

✅ **Menu & Commandes** - Prix en DT sans décimales  
✅ **Réservations** - Système complet  
✅ **Événements** - Gestion et inscriptions  
✅ **Administration** - Panel complet  
✅ **Sécurité** - CSRF, XSS, SQL protection  
✅ **Responsive** - Mobile & desktop  
✅ **Production** - Optimisé et sécurisé  

**Votre restaurant platform est prêt pour la production ! 🚀**
