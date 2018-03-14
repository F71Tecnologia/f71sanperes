<?php
session_start();
include 'db_utils.php';

$search_pattern = $_REQUEST['pattern'] ? $_REQUEST['pattern'] : '%';

$response = list_contacts($_SESSION['email'], $search_pattern);
$response = json_encode(array(
    'results' => $response
));

header('Content-type: application/json');
exit($response);