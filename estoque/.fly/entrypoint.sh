#!/usr/bin/env sh

# Run user scripts, if they exist
for f in /var/www/html/.fly/scripts/*.sh; do
    # Bail out this loop if any script exits with non-zero status code
    bash "$f" -e
done

# Regra exclusiva para o ambiente de teste com SQLite
if [ "$DB_CONNECTION" = "sqlite" ]; then
    echo "Ambiente de teste detectado (SQLite). Garantindo estrutura do banco..."
    mkdir -p /var/www/html/database
    touch /var/www/html/database/database.sqlite
    php artisan migrate --force
fi

if [ $# -gt 0 ]; then
    # If we passed a command, run it as root
    exec "$@"
else
    exec supervisord -c /etc/supervisor/supervisord.conf
fi