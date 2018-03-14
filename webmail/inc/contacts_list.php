<?php
session_start();
include 'contacts_utils.php';
include 'functions.php';

$request = parse_request(array(
	'user_email' => null,
	'pattern' => null,
));

$response = list_contacts($request['user_email'], $request['pattern']);

header('Content-type: application/json');
exit(json_encode($response));