***Project VOILE***
- Salome RODRIGUEZ
- Benjamin MANEM

## Running with Docker
To run the website you have to install docker :
```bash
# Install Docker
sudo apt-get update
sudo apt-get install docker-ce docker-ce-cli containerd.io

# Verify Docker installation
docker --version
```
Before running the website you can modify credential in var.env to specify strong password.
and run this script to start docker containers for the website :
```bash
./run.sh

```
When the containers are running you can access to the website by http://localhost:8080/home.php

To stop the website and clean containers you can run : 

```bash
./stop.sh
```
## Running at ENSEIRB-MATMECA

If you want to run the website at ENSEIRB-MATMECA you have to :

1. Clone the repository to your working directory:
```bash
git clone https://github.com/wiisar/voileProject.git
```
5. Create a database if needed:
```bash
mysql -u your-username -p -e "CREATE DATABASE voile_db;"
```

6. Import the database structure and data:
```bash
# Import structure
mysql -u your-username -p voile_db < bdd/1-init-bdd-structure.sql

# Import data
mysql -u your-username -p voile_db < bdd/2-init-bdd-data.sql
```

7. Update the database connection settings in bddconnexion.php:
```php
// Open bddconnexion.php and modify these credentials
$host = "localhost"; // or specific ENSEIRB database host
$user = "your-username"; // your ENSEIRB username
$password = "your-password"; // your database password
$database = "voile_db"; // the name of your database
```
8. Push html code on zzz serveur:

You can use this command to send html directory on your zzz.bordeaux-inp.fr server:

```bash
./push_tftp.sh
```
This script will ask for your ftp password on ZZZ.

and after you can visit the website http://[username].zzz.bordeaux-inp.fr/home.php
```text
VoileProject/
├── bdd/                       # Scripts SQL pour la base de données
│   ├── drop-bdd.sql           # Script pour supprimer toutes les tables/triggers
│   ├── init-bdd-structure.sql # Script pour créer la structure de la base (tables, triggers)
│   ├── init-bdd-data.sql      # Script pour insérer les données de test
│
├── html/                      # Code source du site web
│   ├── adminpage/             # Pages d'administration (gestion séjours, utilisateurs, etc.)
│   │   ├── sejours.php        # Gestion des séjours (tableau, boutons gérer/supprimer)
│   │   ├── sejour_details.php # Détail d'un séjour (participants, ajout/suppression)
│   │   └── ...                # Autres pages d'administration
│   │
│   ├── phpfunc/               # Fonctions PHP réutilisables
│   │   ├── bddconnexion.php   # Connexion à la base de données
│   │   ├── addSejour.php      # Formulaire/traitement pour ajouter un séjour
│   │   ├── deleteSejour.php   # Suppression d'un séjour
│   │   ├── navbar.php         # Barre de navigation commune
│   │   └── ...                # Autres fonctions PHP
│   │
│   ├── style/                 # Feuilles de style CSS
│   │   └── style.css          # Styles principaux du site
│   │
│   ├── script/                # Scripts JavaScript
│   │   ├── display.js         # JS pour affichage dynamique (ex: formulaires)
│   │   ├── userUpdate.js      # JS pour mise à jour utilisateur
│   │   └── ...                # Autres scripts JS
│   │
│   ├── home.php               # Page d'accueil du site
│   └── ...                    # Autres pages principales (login, trip, etc.)
│
├── var.env                    # Variables d'environnement (identifiants, mots de passe, etc.)
├── run.sh                     # Script pour lancer les conteneurs Docker
├── stop.sh                    # Script pour arrêter/clean les conteneurs Docker
├── push_tftp.sh               # Script pour envoyer le site sur le serveur ZZZ
├── readme.md                  # Documentation et instructions d'installation/utilisation
└── ...                        # Autres fichiers racines éventuels
```