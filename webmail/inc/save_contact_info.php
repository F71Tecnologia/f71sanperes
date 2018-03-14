<?php

include 'db_utils.php';

$original_mail = $_REQUEST['original_mail'];
$contact_name  = $_REQUEST['name'];
$contact_email = $_REQUEST['email'];
$contact_addr  = $_REQUEST['addr'];

if (filter_var($original_mail, FILTER_VALIDATE_EMAIL) && filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {
    set_contact_info($original_mail, $contact_name, $contact_email, $contact_addr);
}
else {
    exit('Email fornecido não é válido.');
}
