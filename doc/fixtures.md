## Fixtures

This plugin comes with fixtures:

### Banners

Simply add this configuration on your fixture suite:

```yml
# config/packages/_sylius.yaml
sylius_fixtures:
    suites:
        default:
            fixtures:
                banner:
                    options:
                        random: 3
```
