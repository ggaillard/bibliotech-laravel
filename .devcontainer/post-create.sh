#!/usr/bin/env bash
# Préparation du projet après la création du Codespace.
# IDEMPOTENT : peut être relancé à tout moment (bash .devcontainer/post-create.sh).
set -e
umask 0002
cd /workspace/projetLaravel

echo "▶ Dépendances PHP (composer install)…"
composer install --no-interaction --prefer-dist --no-progress

if [ ! -f .env ]; then
  echo "▶ Création du fichier .env"
  cp .env.example .env
fi
if ! grep -q '^APP_KEY=base64:' .env; then
  php artisan key:generate --no-interaction
fi

echo "▶ Attente du serveur MySQL…"
for i in $(seq 1 60); do
  if php -r 'new PDO("mysql:host=mysql;port=3306", "root", "root");' 2>/dev/null; then break; fi
  sleep 2
done

echo "▶ Création des tables et des données de démonstration…"
php artisan migrate --force --seed

# Apache (www-data) doit pouvoir écrire dans storage/ et bootstrap/cache/
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwX storage bootstrap/cache
find storage bootstrap/cache -type d -exec chmod g+s {} +

echo ""
echo "✅ BiblioTech est prêt : onglet PORTS → port 8000 (application), 8080 (phpMyAdmin)."
echo "   Vérification : php artisan test --group=socle"
