<?php

require_once __DIR__ . '/lib/http_client.php';

$first_name = 'Fname';
$last_name = 'Lane';
$gender = 'boy';
$nid = 10;
$connection = HttpClient::connect(
    'mkb.com',
    80
);
$images = $connection->doGet(
    'mkb/generate',
    array(
        'first_name' => $first_name,
        'last_name' => $last_name,
        'gender' => $gender,
        'unique_id' => $nid
    )
);
print '<pre>' . htmlspecialchars(print_r($images,1)) . '</pre>';