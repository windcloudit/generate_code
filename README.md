# laravel generate code
An artisan command to generate service, provider, action, controller, unit test

### How to install

Config yur composer.json
```sh
    "repositories": [
        {
            "type": "git",
            "url": "https://github.com/windcloudit/laravel-git-sniffer.git"
        },
        {
            "type": "git",
            "url": "https://github.com/windcloudit/generate_code.git"
        }
    ],
```

Config in require-dev
```sh
    "require-dev": {
        "windcloud/generate-code": "^1.0.0"
    },
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
