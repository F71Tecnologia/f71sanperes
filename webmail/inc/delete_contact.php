<?php

include 'db_utils.php';

$contact_email = $_REQUEST['contact_email'];

delete_contact($contact_email);
