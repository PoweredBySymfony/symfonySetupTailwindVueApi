<h1 align="center">R5.05 - Projet n°2 : Api REST Event </h1>

<h2 align="center">Groupe de:</h2>
<p align="center">Xavier TROUCHE</p>
<p align="center">Maxime PETIT</p>
<p align="center">Lisa ACHOUR</p>
<p align="center"><i>Groupe G5 de l'IUT de Montpellier-Sète</i></p>
<p align="center"><i>dans le cadre du cours de Frameworks Web</i></p>

## Liens utiles
- [Dépôt Git](https://github.com/PoweredBySymfony/Projet-API-REST)
- [Sujet du projet](https://mgasquet.github.io/R5.A.05-ProgrammationAvancee-Web/tutorials/projet2)
- [Lien vers le tableau Trello](https://github.com/orgs/PoweredBySymfony/projects/3)

## Lancer le projet

> [!NOTE]
> Ce présent tutoriel part du principe que vous avez mis en place l'image Docker [but3-web-container](https://gitlabinfo.iutmontp.univ-montp2.fr/progweb-but3/docker).

> [!IMPORTANT]
> Si, pendant l'installation des dépendances de Symfony, on vous demande si vous souhaitez installer une recipe, **répondez oui**.
> Si vous avez accidentellement répondu non, supprimez le module (`composer remove ...`) et réinstallez-le (`composer require ...`).

1) Se positionner dans le dossier `/shared/public_html` et exécuter la commande suivante:
```shell
git https://github.com/PoweredBySymfony/Projet-API-REST.git music_api
```

2) Si ce n'est pas déjà fait, déclarer l'URL de la base de données dans le fichier `.env` en remplaçant la ligne `DATABASE_URL=...` par:
```shell
DATABASE_URL=mysql://root:root@db:3306/music_api
```

> [!NOTE]
> Si une base de données du nom d'*annuaire* existe déjà au sein de la BDD du conteneur Docker, pensez à la renommer ou, au cas échéant, à changer la cible de *DATABASE_URL*.

3) Dans le terminal du conteneur Docker (via Docker Desktop ou via la CLI), se placer dans le dossier `/shared/public_html/s5-web-projet1` et exécuter les commandes suivantes:
```shell
composer install
# Le composer install a tendance à se figer à l'étape du clear cache, si c'est le cas, redémarrer Docker
# puis relancer la commande afin que la configuration se finisse correctement
php bin/console doctrine:database:create
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

### Configuration
Une configuration générique des variables d'environnement se trouve dans le fichier `.env`. Si vous avez besoin d'y apporter des modifications, créez et utilisez le fichier `.env.local`.

### Fixtures

- Commande pour charger les données, cela générera 100 utilisateurs, il suffit de la lancer 1 seule fois pour les créer
```
php bin/console doctrine:fixtures:load
```
Cela générera les éléments suivants :

- 10 utilisateurs avec des rôles assignés aléatoirement (ROLE_USER, ROLE_ADMIN, ROLE_ORGANIZER (pour les organisateurs d'évènements), ROLE_ARTIST)). Chaque utilisateur possède un nom, prénom, email, login unique et un mot de passe par défaut. Le mot de passe de chaque utilisateur est simplement password. 
- 5 scènes avec un nom et une capacité maximale de participants aléatoire. 
- 10 événements musicaux avec une date de début, de fin, un prix et une adresse générée. Les utilisateurs sont associés de manière aléatoire aux événements. 
- Chaque scène est associée à des événements musicaux, et pour chaque scène, deux parties de concert sont créées. Ces parties de concert associent également un artiste aléatoire du pool d’utilisateurs et incluent des détails comme la date et l'heure.

## Fonctionnement de l'api
Pour accéder au swagger, simplement accéder à la route `/api` ([via ce lien](http://localhost/music_api/public/api) si vous utilisez le docker but3-web-container).

## Routes de l'API

### Utilisateur
- `GET /api/users` - Récupère la collection de ressources Utilisateur.
- `POST /api/users` - Crée une ressource Utilisateur.
- `GET /api/users/{id}` - Récupère une ressource Utilisateur spécifique.
- `DELETE /api/users/{id}` - Supprime une ressource Utilisateur.
- `PATCH /api/users/{id}` - Met à jour une ressource Utilisateur.

#### Securité Utilisateur 
- Pour supprimer un utilisateur, il faut être connecté en tant qu'administrateur ou etre le propriétaire du compte.
- Pour mettre à jour un utilisateur, il faut être connecté en tant qu'administrateur ou etre le propriétaire du compte.


### EvenementMusical
- `GET /api/evenement_musicals` - Récupère la collection de ressources EvenementMusical.
- `POST /api/evenement_musicals` - Crée une ressource EvenementMusical.
- `GET /api/evenement_musicals/{id}` - Récupère une ressource EvenementMusical spécifique.
- `DELETE /api/evenement_musicals/{id}` - Supprime une ressource EvenementMusical.
- `PATCH /api/evenement_musicals/{id}` - Met à jour une ressource EvenementMusical.

#### Securité EvenementMusical
- Pour supprimer un EvenementMusical, il faut être connecté en tant qu'administrateur ou etre le propriétaire de l'événement.
- Pour mettre à jour un EvenementMusical, il faut être connecté et etre l'organisateur de l'événement.


### PartieConcert
- `GET /api/partie_concerts` - Récupère la collection de ressources PartieConcert.
- `POST /api/partie_concerts` - Crée une ressource PartieConcert.
- `GET /api/partie_concerts/{id}` - Récupère une ressource PartieConcert spécifique.
- `DELETE /api/partie_concerts/{id}` - Supprime une ressource PartieConcert.
- `PATCH /api/partie_concerts/{id}` - Met à jour une ressource PartieConcert.

#### Securité PartieConcert
- Pour supprimer une PartieConcert, il faut être connecté en tant qu'administrateur ou etre l'organisateur de la partie de concert.
- Pour mettre à jour une PartieConcert, il faut être connecté et etre l'organisateur de l'évènement auquel est rattaché la partie de concert.

### Scène
- `GET /api/scenes` - Récupère la collection de ressources Scène.
- `POST /api/scenes` - Crée une ressource Scène.
- `GET /api/scenes/{id}` - Récupère une ressource Scène spécifique.
- `DELETE /api/scenes/{id}` - Supprime une ressource Scène.
- `PATCH /api/scenes/{id}` - Met à jour une ressource Scène.

#### Securité Scène
- Pour supprimer une Scène, il faut être connecté en tant qu'administrateur ou etre l'organisateur de l'évènement auquel est rattaché la scène.
- Pour mettre à jour une Scène, il faut être connecté en tant qu'administrateur ou etre l'organisateur de l'évènement auquel est rattaché la scène.

### Authentification
- `POST /api/auth` - Crée un jeton utilisateur.

## Securisation de l'API avec JWT

### Mise en place

```
   php bin/console lexik:jwt:generate-keypair
```



## Fonctionnalités et contraintes obligatoires
### Gestion des utilisateurs
- [X] **Inscription** : L'utilisateur peut s'inscrire en fournissant les informations suivantes : login, email, mot de passe et visibilité du profil (visible/masqué).
  - [X] **Code unique** : Lors de l'inscription, l'utilisateur peut choisir de saisir un code unique ou laisser l'application en générer un aléatoirement. Ce code est vérifié en temps réel pour éviter les duplications (utilisation de JavaScript natif pour les requêtes asynchrones).
  - [X] **Visibilité du profil** : Les utilisateurs peuvent définir leur profil comme visible (listé) ou masqué (non listé). Les profils visibles apparaissent sur la page principale du site.

- [X] **Affichage des profils** :
  - La page principale liste tous les profils visibles.
  - Chaque profil est accessible via une URL unique, par exemple `/profil/{code}`, que le profil soit visible ou masqué.
  - Une route renvoie les informations d’un profil au format JSON pour une utilisation future dans d'autres projets.

- [X] **Gestion des données de profil** :
  - Le profil de l'utilisateur contient la dernière date de modification et la dernière date de connexion, automatiquement mises à jour.

### Édition du profil
- [X] Les utilisateurs peuvent **éditer** leur profil pour ajouter des informations supplémentaires (numéro de téléphone, pays, adresse, réseaux sociaux, etc.).
  - [X] Le **formulaire d'édition** est pré-rempli avec les informations actuelles de l'utilisateur.
  - [X] Les utilisateurs peuvent modifier leur code unique ou demander la génération d’un nouveau code aléatoire.
  - [X] Les utilisateurs peuvent supprimer leur profil ou changer la visibilité de leur profil (visible/masqué).

### Rôle d'administrateur
- [X] Certains utilisateurs ont le rôle d'administrateur, leur permettant de :
  - Visualiser les profils masqués sur la page principale.
  - Supprimer des profils d'autres utilisateurs, sauf ceux qui ont également un rôle d'administrateur.

### Maintenance du site
- [X] Un **mode maintenance** peut être activé via le fichier `services.yaml`, redirigeant toutes les pages vers un message d'indisponibilité.

### Création d'utilisateurs via la ligne de commande
- [X] Une commande Symfony permet de **créer des utilisateurs** depuis le terminal avec les informations nécessaires et le choix du rôle (normal/administrateur).

### Limitations sur le JavaScript
- [X] L'utilisation de JavaScript est limitée au JavaScript natif sans framework.

## Fonctionnalités pour le Premier Rendu (27 octobre 2024)

Pour ce premier rendu intermédiaire, l'API doit répondre aux critères suivants basés sur le TD4 :

### Gestion d'utilisateurs
- [X] Implémentation de la ressource `/utilisateurs` permettant la gestion des utilisateurs.

### Groupes de normalisation, dénormalisation et validation
- [X] Mise en place de groupes de normalisation, dénormalisation, et validation pour structurer les données des utilisateurs.

### Gestion des sous-ressources
- [X] Exemple de sous-ressource : `/utilisateurs/{id}/evenements`, permettant la gestion des publications associées aux utilisateurs.

### Prise en charge des verbes HTTP
- [X] Support pour les verbes HTTP essentiels : `GET`, `POST`, `PATCH`, `DELETE`.

### Authentification par JWT
- [X] Authentification avec JWT pour sécuriser les sessions. Les tokens sont envoyés dans les cookies pour faciliter l'identification des utilisateurs.

### StateProcessor
- [X] Création d'un **StateProcessor** pour gérer des états spécifiques lors des opérations sur les ressources, par exemple, pour marquer automatiquement un utilisateur comme auteur d'une publication.

### Sécurité des actions
- [X] Mise en place de restrictions de sécurité pour les actions, garantissant que seules les opérations autorisées sont exécutables par les utilisateurs.

### Générateur de groupes
- [X] Génération automatique des groupes de validation, normalisation, et dénormalisation pour assurer la cohérence des données.

### Répartition du travail
Dans les grandes lignes:
- Xavier T.:
   - Mise en place du projet
   - securité des actions
   - groupe de validation
   - mise en place de fixtures
   - exposition des ressources
   - gestion des erreurs
- Lisa A.:
   - mise en place des processors evenement, scene, partie concert
   - générateur de groupes
   - gestion des sous-ressources
   - authentification par JWT
   - gestiond des erreurs
[//]: # (TODO: Ajouter les tâches de Lisa)
- Petit Maxime.:
   - mise en place des processors evenement, scene
   - gestion de verbes Api

Pour plus de détails, voir les commits, les pull requests et le [Trello](https://github.com/orgs/PoweredBySymfony/projects/3).