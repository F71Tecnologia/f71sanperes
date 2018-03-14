<?php
include 'contacts_utils.php';
include 'functions.php';

$request = parse_request(array(
	'user_email' 	=>  null,
	'original_mail' => null,
	'contact_name'  => null,
	'contact_email' => null,
	'contact_addr'  => null,
));

foreach (array('user_email', 'contact_email' ) as $key) {
	if (!filter_var($request[$key], FILTER_VALIDATE_EMAIL)) exit (json_encode(array('error'=> 'Not a valid Email')));
}

$response = set_contact_info($request);

exit(json_encode($response));