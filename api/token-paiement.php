<?php

header('Content-Type: application/json');

define("URL_TOKEN", "https://openapiuat.airtel.africa/auth/oauth2/token");
define("client_id", "903e50a2-b93e-4c1e-94fe-e37d66922cfa");
define("client_secret", "64d165bc-ce70-49ca-97f7-a494fe6db90a");

$dataParams = array(
    "client_id" => client_id,
    "client_secret" => client_secret,
    "grant_type" => "client_credentials"
);


