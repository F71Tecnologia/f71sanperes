<?php

include 'contacts_utils.php';
include 'functions.php';

$request = parse_request(array(
	'user_email'	=> null,
	'contact_email' => null,
));

$response = delete_contact($request);


exit (json_encode($response));