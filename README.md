oauth-php-sdk
==================

This PHP - SDK helps developer to connect to REST Api php application via OAuth2.

Example
-------
``php
<?php

use OAuth2\Factory;

require_once __DIR__ . './../vendor/autoload.php';

$config = array(
    'baseUri' => 'htttp://api.example.com',
    'client_id'     => 'client_id',
    'client_secret' => 'client_secret',
    'grant_type'    => 'password',
    'username'    => 'username',
    'password'    => 'password',
);

$factory  = new Factory();
$api = $factory->createApi($config);
try {
    $response = $api->call('v1/users.json', 'GET', $params = array());
    if ('200' == $response->getStatusCode()) {
        $users = json_decode($response->getContent());

        var_dump($users);
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

