<?php
session_start();
include 'contacts_utils.php';
include 'functions.php';

$contact = parse_request(array(
	'name' => null,
	'email' => null,
	'address' => null,
	'user_email' => null,
));


foreach (array_keys($contact) as $key => $value) {
	if (!is_null($contact[$key]))  exit( json_encode(array('error' => true)));
}

$response = add_contact($contact);

exit(json_encode($response)); //already JSONED!


