# Symfony Project Setup

## 1. Creer un nouveau projet Symfony

```bash
# Creer un projet avec la derniere version stable
symfony new my_project

# Creer un projet avec la derniere version LTS
symfony new my_project --version=lts

# Creer un projet avec une version specifique
symfony new my_project --version=6.4

# Creer un projet avec la version de developpement
symfony new my_project --version=next

# Les commandes precedentes creent des projets minimaux.
# Pour une application web, utiliser l'option suivante
# afin d'installer toutes les dependances communes :
symfony new my_project --webapp

# Creer un projet base sur l'application de demonstration Symfony
symfony new my_project --demo
```

## 2. Creer un projet Symfony 8.x

### Avec le CLI Symfony

```bash
# Application web traditionnelle
symfony new my_project_directory --version="8.0.*" --webapp

# Microservice, application console ou API
symfony new my_project_directory --version="8.0.*"
```

### Avec Composer

```bash
# Application web traditionnelle
composer create-project symfony/skeleton:"8.0.*" my_project_directory
cd my_project_directory
composer require webapp

# Microservice, application console ou API
composer create-project symfony/skeleton:"8.0.*" my_project_directory
```

## 3. Installer les dependances

```bash
composer require symfony/twig-bundle
composer require symfony/orm-pack
composer require symfony/form
composer require symfony/validator
composer require --dev symfony/maker-bundle
```

## 4. Configurer la base de donnees

Dans le fichier `.env`, definir la variable `DATABASE_URL` :

```env
DATABASE_URL="mysql://root:@127.0.0.1:3306/symfony_blog"
```

## 5. Initialiser la base de donnees

```bash
# Creer la base de donnees
php bin/console doctrine:database:create

# Generer les fichiers de migration
php bin/console make:migration

# Appliquer les migrations
php bin/console doctrine:migrations:migrate
```

## 6. Lancer le serveur de developpement

```bash
symfony server:start
```
