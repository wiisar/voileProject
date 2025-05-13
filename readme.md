***Project VOILE***

To run the website you have to instal dockker :
```bash
# Install Docker
sudo apt-get update
sudo apt-get install docker-ce docker-ce-cli containerd.io

# Verify Docker installation
docker --version
```
and run this script to start docker container for the website :
```bash
./run.sh

```
When the container starts you can access to the website by http://localhost:8080/home.php

To stop the website and clean container you can run : 

```bash
./stop.sh

```
Vidéos :
- les héberger sur Youtube ?

Formulaire de contact :
- ajouter une FAQ dans la partie Contact
- auto-remplir le nom dans le formulaire de contact

Sécurité :
- quelles sont les failles actuelles ?
- quelles sont les pistes à améliorer ?
- sécurité concernant le champ isAdmin : géré par Ben avec injection SQL

Administrateurs :
- créér plusieurs types d'administrateurs en fonction de ce qu'il font
- autoriser des suppressions (stages, bateaux, etc...) sans supprimer de la BDD mais en ajoutant un champ isAdmin à activer ou désactiver pour le rendre visible ou pas sur le site

Autres fontionalités :
- un php spécial pour mettre toutes les pages à jour si on ajoute un onglet par exemple
- réfléchir aux autres fonctionalités possibles avec l'authentification (adhérent ou pas) ?
