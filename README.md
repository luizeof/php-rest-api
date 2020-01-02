# php-rest-api

Easy way to create a PHP REST API with ```Basic Authentication```

## Installing

To install `luizeof/php-rest-api`, run this command on your terminal:

```bash
composer require luizeof/php-rest-api
```

## Importing

Import `autoload` on your project:

```php
require __DIR__ . '/vendor/autoload.php';
```

## Sample Usage

```php
<?php

try {

    $api = luizeof\RestAPI();

    // Use this callback to make your username / password
    // validation logic returning true or false
    $api->validate_auth( function() use ($api) {

        if ($api->get_username() == "luiz") {
            return true;
        } else {
            return false;
        }

    }); // validate_auth()

    $arr = array('id' => 1, 'title' => "Exemplo", 'date' => "01/01/2020", 'tags'=> array("php","docker","flutter"), 'author' => "luizeof");

    $api->array_to_json($arr);

    $api->request_success();

} catch (Throwable $t) {

    $api->request_error($t);

} catch (Exception $e) {

    $api->request_error($e);

} finally {

    unset($api);

}

?>
```
