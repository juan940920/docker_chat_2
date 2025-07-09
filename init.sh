echo "LEVANTANDO CONTENEDORES DE (DOCKER)"
cd backend_laravel_vue_bot
docker-compose up -d --build

docker-compose exec -T laravel composer install
docker-compose exec -T laravel php artisan key:generate
docker-compose exec -T laravel php artisan optimize
docker-compose exec -T laravel php artisan migrate:fresh
docker-compose exec -T laravel php artisan db:seed

echo "La App esta en http://localhost:80"
