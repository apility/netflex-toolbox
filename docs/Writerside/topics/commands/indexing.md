# Indexing

Indexing can sometimes be problematic, if the data is not in its expected format. Therefore, we have
added a few helpers that can help you deal with this problem in a repeatable way.

We have added a few functions that will use the database backend ids and compare them to ids available in
elastic search and will then try to reindex all missing entries, customers (or to a lesser extent newsletters).

Here are the following commands and their basic options.

| Command                                   | Description                                     |
|-------------------------------------------|-------------------------------------------------|
| `tb:customers:index`                      | Index one or all missing customers              |
| `tb:directory:index --directory-id=12345` | Index all missing entries in a single directory |
| `tb:newsletter:index 123`                 | Index a single newsletter                       |

## Fixing malformed data

Most of the time, invalid data is the reason why indexing fails. We have implemented a middleware like
system to allow you to register data fixers that will mutate the data before reindexing occurs.

### AddingMiddlewares

In order to apply middlewares to the indexed data, you can publish the `config/indexers.php` file by publishing
the `toolbox-config` key using the following command.

```Bash
$ php artisan vendor:publish --tag=toolbox-config
```

This should create a new configuration file named `config/indexers.php`. Add your mutators to it to adjust the data
prior to indexing.

```PHP
<?php
return [
    'customers' => [
        /// Some middleware you created, using class string syntax,
        NormalizeEmailMiddleware::class,
        
        /// A built in middleware using object syntax
        Netflex\Toolbox\Pipeline\FormatDate::make('fromDate', true, true, 'Europe/Oslo')
    ],
];
```

#### Making middlewares.

A middleware can be any class with a `handle` method. You can create this middleware yourself, but if you make something
generic that can be useful, we would like to include it into this package.

Here is a psuedo-code implementation of a mutation middleware that normalizes email addresses, should
elastic search want this.

```PHP
<?php

class NormalizeEmailMiddleware {
    public function handle(\stdClass $object, \Closure $next) {
        if($object->email) $object->email = strtolower($object->email);
        return $next($object);
    }
}
?>
```

#### Built in middlewares

There is currently only a single middleware implemented, which fixes dates
It can be created using the `make` method on the `FormatDate` class.

| Arguments   | Required | Type           | Default | Description                                                                                                                                           |
|-------------|----------|----------------|---------|-------------------------------------------------------------------------------------------------------------------------------------------------------|
| `$field`    | Yes      | `string`       |         | Which field on the indexed object you want to fix                                                                                                     |
| `$date`     | No       | `boolean`      | `true`  | Do you want to include the date part of the parsed datetime in your indexed data                                                                      |
| `$time`     | No       | `boolean`      | `true`  | Do you want to include the time part of the parsed datetime in your indexed data                                                                      |
| `$timezone` | No       | `string\|null` | `null`  | Which timezone do you want to store the date in. Even if your timestamp has timezone information embedded, the output timezone might not be the same. |
