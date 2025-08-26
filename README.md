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

git clone https://github.com/taizceccon/leilasnacksetdouceurs.git
cd leilasnacksetdouceurs
2. Installer les dépendances PHP

composer install
3. Configurer la base de données (fichier .env)
DATABASE_URL="mysql://user:password@127.0.0.1:3306/leila_snacks?serverVersion=8.0"

4. Appliquer les migrations

php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

5. Configurer l’envoi d’emails en développement (Mailpit) .env

MAILER_DSN=smtp://mailpit:1025

6. Lancer le serveur Symfony
symfony server:start  

Lancement avec Docker
docker-compose up --build
Cela démarrera :
Symfony (PHP + Apache), MySQL, MongoDB, phpMyAdmin, Mailpit

Accéder 
http://localhost:8080

Accéder à l’interface Mailpit
http://localhost:8025

Accéder à PhpMyAdmin
http://localhost:8899/

🤝 Contribution
Les contributions sont les bienvenues !

📄 Licence
Ce projet est sous licence MIT.


