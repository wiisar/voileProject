***Project VOILE***
*** Auteurs ***
Salome RODRIGUEZ
Benjamin MANEM

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
git clone https://github.com/your-username/voile-project.git
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

7. Update the database connection settings in bddconnexion.php to match ENSEIRB environment:
```php
// Open bddconnexion.php and modify these credentials
$host = "localhost"; // or specific ENSEIRB database host
$user = "your-username"; // your ENSEIRB username
$password = "your-password"; // your database password
$database = "voile_db"; // the name of your database
```

You can use this command to send html directory on your zzz.bordeaux-inp.fr server:
```bash
./push_tftp.sh
```
This script will ask for your ftp password on ZZZ.

and after you can visit the website http://[username].zzz.bordeaux-inp.fr/home.php


