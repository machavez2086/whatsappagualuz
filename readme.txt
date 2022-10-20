Sistema cbse
php app/console doctrine:schema:update --force --env=test
php app/console doctrine:schema:update --force
php app/console doctrine:schema:validate para validar las relaciones de base de dato
php app/console sacspro:groups:create
php app/console assets:install
php app/console sonata:media:fix-media-context

php composer.phar install --ignore-platform-reqs

iniciar el servidor de websocket
php7.0 app/console gos:websocket:server


sudo apt install php-amqp

Crear base de datos
$ mysqldump --user=root --password whatsappcompanies2 > dump.sql
$ mysql -u root -p
$ create database whatsappcompanies2;
importar antigua base de datos
$ mysql -u root -p whatsappcompanies2 < dump.sql

curl -X POST --header 'Content-Type: application/json' --header 'Accept: application/json' -d '{ "token": "sdfdsfdfsdfhsdhfsdf-sdfdsfkjdsh-df" }' 'http://localhost/whatsapp-agent/web/app.php/api/pushmessage/1442'
