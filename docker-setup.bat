@echo off
echo 🚀 [1/6] Spúšťam kontajnery...
docker-compose up -d --build

echo 📝 [2/6] Kopírujem .env a generujem APP_KEY...
docker-compose exec laravel cp .env.example .env

echo 📦 [3/6] Inštalujem Composer balíky...
docker-compose exec laravel composer install

echo 🎨 [4/6] Inštalujem a buildujem frontend (vite)...
docker-compose exec vite npm install
docker-compose exec vite npm run build

echo 🗄️ [5/6] Spúšťam migrácie...
docker-compose exec laravel php artisan migrate

echo 🗄️ [6/7] Povolujem stahovanie suborov...
docker compose exec laravel php artisan storage:link

echo ✅ [7/7] Príprava dokončená. Laravel beží na porte 8001, FastAPI na 8000.
echo -------------------------------------------
echo Laravel: http://localhost:8001
echo FastAPI Swagger: http://localhost:8000/docs
pause
