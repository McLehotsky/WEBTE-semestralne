docker compose exec laravel php artisan key:generate
docker compose exec laravel php artisan config:clear
docker compose exec laravel php artisan cache:clear
docker compose exec laravel php artisan migrate
docker compose exec laravel php artisan serve --host=0.0.0.0 --port=8001
echo setup done
echo CookedPDF: http://localhost:8001
echo FastAPI Swagger: http://localhost:8000/docs