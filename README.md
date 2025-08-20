#  Leila Snacks & Douceurs

##  Description

**Leila Snacks** est un site e-commerce artisanal proposant des snacks sucrés et salés faits maison.  
Les utilisateurs peuvent découvrir, rechercher et commander en ligne des produits gourmands de qualité.

---

##  Fonctionnalités principales

-  Recherche de produits par mots-clés avec affichage dynamique des résultats  
-  Affichage des produits par catégories : *Snacks*, *Douceurs*, *Packs & Coffrets*  
-  Pages produits avec description, prix, image, vidéo  
-  Formulaire de contact sécurisé avec validation RGPD et envoi d’email via **Symfony Mailer**  
-  Sécurité : intégration optionnelle de **reCAPTCHA v3** pour éviter les spams  
-  Pages statiques : *Mentions légales*, *Conditions générales*, *À propos*  
-  Gestion des erreurs et affichage de flash messages utilisateur  
-  Test d’envoi d’e-mails en développement via **Mailpit**  
-  Panier : ajout, mise à jour, suppression, validation de commande  
-  Gestion des avis clients  
-  Interface d’administration : gestion des produits, catégories, avis  
-  Paiement en ligne sécurisé via **Stripe** (avec webhooks)  
-  Inscription et authentification des utilisateurs

---

## 🛠 Installation locale (avec Symfony CLI)

> **Prérequis** : PHP ≥ 8.1, Composer, Symfony CLI, MySQL, Mailpit (ou Docker)

### 1. Cloner le dépôt

```bash
git clone https://github.com/taizceccon/leilasnacksetdouceurs.git
cd leilasnacksetdouceurs
2. Installer les dépendances PHP
bash

composer install
3. Configurer la base de données (fichier .env)
dotenv
Copier le code
DATABASE_URL="mysql://user:password@127.0.0.1:3306/leila_snacks?serverVersion=8.0"
4. Appliquer les migrations
bash

php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
5. Configurer l’envoi d’emails en développement (Mailpit)
dotenv
Copier le code
MAILER_DSN=smtp://mailpit:1025
6. Lancer le serveur Symfony
bash

symfony server:start
7. Accéder à l’interface Mailpit
arduino

http://localhost:8080
Lancement avec Docker
bash

docker-compose up --build
Cela démarrera :

Symfony (PHP + Apache)

MySQL

MongoDB

phpMyAdmin

Mailpit

🤝 Contribution
Les contributions sont les bienvenues !
Merci de créer une issue ou une pull request pour toute amélioration ou correction.

📄 Licence
Ce projet est sous licence MIT.
