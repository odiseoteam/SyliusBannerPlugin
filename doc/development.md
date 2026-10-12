[← Extending](extending.md) · [Docs index](README.md)

# Development

The plugin runs on the shared [Sylius Test Application](https://github.com/Sylius/TestApplication),
with Docker.

## Running it

```bash
make init            # dependencies, assets and containers
make database-init   # database and migrations
make load-fixtures
```

The shop is then at `http://localhost`, and the admin panel at `http://localhost/admin`
(`sylius` / `sylius`). `make init` copies `compose.override.dist.yml` to `compose.override.yml`:
change the ports there if they are taken.

After changing the assets of the plugin, rebuild them:

```bash
docker compose run --rm nodejs
```

## The suites

They are the ones CI runs:

```bash
make phpunit         # unit, integration and functional tests
make behat
make phpstan
make ecs
make composer-unused
```

| Suite | Where | Covers |
| ----- | ----- | ------ |
| Unit | `tests/Unit` | Classes on their own |
| Integration | `tests/Integration` | Services, repository, fixtures and migrations against the database |
| Functional | `tests/Functional` | The admin panel, the shop and the API over HTTP |
| Behat | `features` | The admin panel and the shop as a user sees them |

PHPUnit and Behat use the `sylius_test` database, apart from the one of the running application.

## The matrix

CI runs the suites on Sylius 2.0 to 2.3, PHP 8.2 to 8.4, MySQL and PostgreSQL. A second job installs
the plugin on a clean Sylius-Standard with the Symfony Flex recipe kept in `recipe/`; run it
locally against any empty database:

```bash
DATABASE_URL=mysql://root@127.0.0.1/banner_install .github/scripts/fresh-install.sh /tmp/banner-install
```

---

[← Extending](extending.md) · [Docs index](README.md)
