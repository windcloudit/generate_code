# laravel generate code
An artisan command to generate service, provider, action, controller, unit test

### Install with composer

```sh
composer require WindCloud/generate_code
```

#### Add the provider to app config (You don't need to do this if using Laravel >= 5.5)
```sh
WindCloud\GenerateCode\GenerateCodeServiceProvider
```

#### Use artisan to publish the config
```sh
php artisan vendor:publish --provider="WindCloud\GenerateCode\GenerateCodeServiceProvider" --tag=config
```

License
----

MIT
