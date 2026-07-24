web: php artisan migrate --force && (php artisan storage:link || true) && php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan octane:start --server=frankenphp --host=0.0.0.0 --port=$PORT
reverb: php artisan reverb:start --host=0.0.0.0 --port=$PORT
queue: php artisan queue:work --tries=3 --sleep=3
