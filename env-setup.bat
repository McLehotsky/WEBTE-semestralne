docker compose exec laravel php artisan key:generate
docker compose exec laravel php artisan config:clear
docker compose exec laravel php artisan cache:clear
docker compose exec laravel php artisan migrate
echo setup done