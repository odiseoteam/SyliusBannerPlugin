#!/usr/bin/env sh
#
# Installs this checkout on a clean Sylius-Standard, with Flex applying recipe/, and checks the result.
#
#     DATABASE_URL=mysql://root@127.0.0.1/banner_install .github/scripts/fresh-install.sh <work dir> [Sylius-Standard version]

set -eu

PLUGIN_DIR="$(cd "$(dirname "$0")/../.." && pwd)"
WORK_DIR="${1:?Usage: fresh-install.sh <work dir> [Sylius-Standard version]}"
SYLIUS_VERSION="${2:-^2.0}"
ENDPOINT_PORT=8765

mkdir -p "$WORK_DIR"
WORK_DIR="$(cd "$WORK_DIR" && pwd)"

echo "== Serving the recipe of this checkout"
php "$PLUGIN_DIR/.github/scripts/flex-endpoint.php" "$WORK_DIR/flex"
php -S "127.0.0.1:$ENDPOINT_PORT" -t "$WORK_DIR/flex" >/dev/null 2>&1 &
trap 'kill $! 2>/dev/null || true' EXIT

echo "== Creating a clean Sylius-Standard $SYLIUS_VERSION"
composer create-project sylius/sylius-standard "$WORK_DIR/app" "$SYLIUS_VERSION" --no-interaction --no-scripts --no-progress
cd "$WORK_DIR/app"

echo "== Requiring the plugin"
composer config repositories.odiseo-banner path "$PLUGIN_DIR"
composer config extra.symfony.allow-contrib true
# The local endpoint is plain http.
composer config secure-http false
SYMFONY_ENDPOINT="http://127.0.0.1:$ENDPOINT_PORT/index.json" composer require "odiseoteam/sylius-banner-plugin:*@dev" --no-interaction --no-scripts --no-progress

echo "== Checking what the recipe did"
grep -q 'Odiseo\\SyliusBannerPlugin\\OdiseoSyliusBannerPlugin::class' config/bundles.php
test -f config/packages/odiseo_sylius_banner.yaml
test -f config/routes/odiseo_sylius_banner.yaml

echo "== Checking the application"
bin/console lint:container
bin/console doctrine:database:create --if-not-exists --no-interaction
bin/console doctrine:migrations:migrate --no-interaction
# Sylius-Standard's own plugins drift from their migrations: only this plugin's tables are checked.
if bin/console doctrine:schema:update --dump-sql | grep -i 'odiseo_banner'; then
    echo "The migrations don't match the mapping." >&2
    exit 1
fi
bin/console debug:router odiseo_banner_admin_banner_index >/dev/null
bin/console debug:router odiseo_banner_shop_partial_banner >/dev/null
bin/console debug:container odiseo_banner.repository.banner >/dev/null

echo "== Fresh install OK"
