<?php

include_once 'functions.php';


$error_info = parse_request(array(
        'mail' => null,
        'mailbox' => null,
));


if (!($error_info['mailbox'] && $error_info['mail']))
        exit( json_encode( array(
                'error' => 'Missing arguments aborting',
        )));
$system_last_response = system("perl fix_dovecot_mailbox.pl {$error_info['mail']} {$error_info['mailbox']}");

if (preg_match("/fixed/", $system_last_response)) {
        exit(json_encode(array(
                'success' => true,
        )));
} else if (preg_match("/error/", $system_last_response)) {
        exit(json_encode(array(
                'error' => true,
        )));
}
