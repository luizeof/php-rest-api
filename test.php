<?php

require 'php-rest-api-class.php';

try {

    $api = new RestAPI();

    $api->validate_auth(function () use ($api) {

        if ($api->get_username() == "luiz") {
            return true;
        } else {
            return false;
        }
    });

    $arr = array('id' => 1, 'title' => "Exemplo", 'date' => "01/01/2020", 'tags' => array("php", "docker", "flutter"), 'author' => "luizeof");

    $api->array_to_json($arr);

    $api->request_success();
} catch (Throwable $t) {

    $api->request_error($t);
} catch (Exception $e) {

    $api->request_error($e);
} finally {

    unset($api);
}
