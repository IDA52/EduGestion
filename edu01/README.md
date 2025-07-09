# EduGestion - Application de Gestion Académique

## 📋 Description

EduGestion est une application web complète de gestion académique développée en PHP 8+ avec MySQL 8+. Elle permet la gestion des étudiants, enseignants, notes, emplois du temps, utilisateurs et rapports, avec une interface moderne et sécurisée.

## 🚀 Fonctionnalités principales

- Authentification sécurisée (CSRF, sessions, limitation des tentatives)
- Gestion des rôles et permissions (Administrateur, Enseignant, Personnel)
- Tableau de bord avec statistiques et actions rapides
- Gestion CRUD des enseignants, étudiants, classes, matières, notes, utilisateurs
- Gestion visuelle des emplois du temps
- Génération de rapports et export de données
- Interface responsive (Bootstrap 5)
- Script d'installation automatique
- Données de test incluses

## 🛠️ Technologies Utilisées

- **Backend** : PHP 8+
- **Base de données** : MySQL 8+
- **Frontend** : HTML5, CSS3, JavaScript (Vanilla)
- **Framework CSS** : Bootstrap 5
- **Icônes** : Font Awesome 6
- **Graphiques** : Chart.js
- **Serveur** : Apache/Nginx

## 📦 Structure du Projet

```
edu01/
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── app.js
├── database/
│   └── edugestion.sql
├── includes/
│   ├── header.php
│   └── footer.php
├── pages/
│   ├── 404.php
│   ├── classes.php
│   ├── dashboard.php
│   ├── emploi_temps.php
│   ├── enseignants.php
│   ├── etudiants.php
│   ├── login.php
│   ├── logout.php
│   ├── matieres.php
│   ├── notes.php
│   ├── parametres.php
│   ├── profil.php
│   ├── rapports.php
│   └── utilisateurs.php
├── uploads/
├── config.php
├── index.php
├── install.php
└── README.md
```

> **Remarque :** Les pages d'absences, d'assignation et de délibération ne sont pas encore implémentées. La gestion des emplois du temps se fait via `emploi_temps.php` et le profil utilisateur via `profil.php`.

## ⚡ Installation rapide

1. **Cloner le projet**
   ```bash
   git clone https://github.com/votre-repo/edugestion.git
   cd edugestion
   ```
2. **Lancer le script d'installation**
   - Rendez-vous sur `http://localhost/edu01/install.php` dans votre navigateur
   - Suivez les instructions pour créer la base de données et insérer les données de test
3. **Configurer l'application**
   - Vérifiez/ajustez les paramètres dans `config.php` (connexion MySQL, URL, etc.)
4. **Accéder à l'application**
   - Ouvrez `http://localhost/edu01` dans votre navigateur

## 🔑 Comptes de Test (démonstration)

- **Administrateur**
  - Email : admin@edugestion.com
  - Mot de passe : admin123
  - Rôle : Accès complet
- **Enseignant**
  - Email : prof@edugestion.com
  - Mot de passe : prof123
  - Rôle : Gestion des notes, emplois du temps
- **Personnel**
  - Email : personnel@edugestion.com
  - Mot de passe : personnel123
  - Rôle : Consultation des données

> **Astuce :** Les mots de passe de ces comptes de test sont en clair pour faciliter la connexion rapide.

## 🧪 Données de test

La base de données (`database/edugestion.sql`) contient des utilisateurs, enseignants, étudiants, classes, matières, notes, etc. pour tester immédiatement toutes les fonctionnalités.

## 🔒 Sécurité

- Protection CSRF sur tous les formulaires
- Validation et nettoyage des entrées utilisateur
- Protection contre les injections SQL (requêtes préparées)
- Gestion sécurisée des sessions
- Limitation des tentatives de connexion
- Journalisation des activités
- Permissions strictes selon le rôle

**Recommandations :**
- Utilisez HTTPS en production
- Changez les mots de passe par défaut après installation
- Limitez l'accès au dossier `uploads/` via le serveur web

## 📱 Responsive & Accessibilité

- Interface 100% responsive (Bootstrap 5)
- Compatible mobile, tablette, desktop
- Navigation accessible et ergonomique

## 📤 Export & Rapports

- Export des bulletins, emplois du temps, listes, etc. (PDF/CSV)
- Génération de rapports personnalisés via la page `rapports.php`

## 🛠️ Personnalisation

- Modifiez les styles dans `assets/css/style.css`
- Ajoutez vos scripts dans `assets/js/app.js`
- Adaptez les pages dans le dossier `pages/`

## 🤝 Aide & Support

- Pour toute question ou bug, ouvrez une issue sur le dépôt GitHub
- Documentation technique à venir
- Contributions bienvenues !

## 📚 API REST (prévue)

- Une API REST sécurisée est prévue pour l'intégration avec d'autres systèmes (non encore implémentée)

---

© 2024 EduGestion. Tous droits réservés. 