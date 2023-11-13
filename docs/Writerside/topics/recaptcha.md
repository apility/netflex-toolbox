# Google Recaptcha

The toolbox also adds a Recaptcha validator for laravel requests.

The toolbox adds the `recaptcha-v2`, validation rule, for example

```PHP
<?php

/// ... rest of class ommited

function rules() {
    return [
        'email' => 'simple_email',
        'firstname' => 'required',
        'surname' => 'required',
        'g-captcha-response' => 'recaptcha-v2',
    ];
}
```

## Checkbox

The checkbox is available as a built-in blade component, called `<x-toolbox::recaptcha.checkbox />`.

## Configuration

You can publish the configuration to set the site and private key. Using vendor
publish.

```Bash
$ php artisan vendor:publish --tag=toolbox-recaptcha-v2-config
```
