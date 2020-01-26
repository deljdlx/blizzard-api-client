<?php

use ElBiniou\BlizzardApi\Client;

require(__DIR__.'/../__bootstrap.php');
require(__DIR__.'/../configuration/client-dist.php');


session_start();



$client = new Client(
    $clientId,
    $clientKey,
    $redirectURL
);

if($client->listen()) {

    $data = $client->query('/data/wow/creature-family/index', 'GET', [
        'namespace'=> 'static-eu',
        'locale' => 'fr_FR'
    ]);

    header('Content-type: application/json');
    echo json_encode($data);



}

