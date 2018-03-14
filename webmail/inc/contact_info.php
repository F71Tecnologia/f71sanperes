<?php

include 'contacts_utils.php';
include 'functions.php';

$request = parse_request(array(
	'contact_email' => null,
	'user_email' => null,
));

if ($request) {
	$contact_info = json_encode(get_contact_info($request));
    exit ($contact_info);
}
else {
    exit(json_encode(array('error' => true)));
}
