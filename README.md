# 1. Créer le projet
composer create-project symfony/skeleton mon-blog
cd mon-blog

# 2. Installer les dépendances
composer require symfony/orm-pack
composer require symfony/form
composer require symfony/validator
composer require symfony/twig-bundle
composer require --dev symfony/maker-bundle

# 3. Configurer la base de données dans .env
DATABASE_URL="mysql://root:@127.0.0.1:3306/symfony_blog"

# 4. Créer la base de données
php bin/console doctrine:database:create
php bin/console make:migration
php bin/console doctrine:migrations:migrate

# 5. Lancer le serveur
symfony server:start
