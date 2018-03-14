
<?php
include 'imap_connection.php';
include 'functions.php';

$request_method = isset($_REQUEST['dump_mailboxes'])? $_REQUEST['dump_mailboxes']: false;
if (!in_array($request_method, array(
    'dump_mailboxes',
    'read_mailbox_from_file',
    ))) die("ERROR:: expected parameters does not matches");


//header('Content-type: application/json');
exit(
    response_handler(array(
        'imap_stream' => $imap_stream,
        'hostref' => $hostref,
        'email' => $email,
        'password' => $password,
        'request_method' => $request_method,
    ))
);

function response_handler($_) {
    foreach ($_ as $key => $value) { $$key = $value; } //Declara as keys do array associativom passado como argumento, como variáveis setadas com o valor referente à chave

    $full_filename =  getcwd().'/mailboxes/'.preg_replace("{\@|\.}", '_', $email).'.json';
    $mailboxes_clean = array();
    $response = array();
    $request_response = '';

    if ($request_method == 'read_mailbox_from_file' && file_exists($full_filename)) {
        $response_handle = fopen($full_filename, 'r');
        $request_response = fread($response_handle, filesize($full_filename));
        fclose($response_handle);
        return $request_response;
    }

    $mailboxes = imap_list($imap_stream, $hostref, '*');
    
    if (is_array($mailboxes)) {
        foreach ($mailboxes as $box) {
            //$box = imap_utf7_decode($box);
            $box = preg_replace('/^[^}]*}/i', '', $box);
            $mailboxes_clean[] = $box;
        }

        imap_close($imap_stream);

        $response = 'não';
        $request_response = json_encode(array(
            'mailboxes'      => $response,
            'mailboxes_tree' => mount_mailbox_tree($mailboxes_clean),
            )
        );

    }
    $request_response = mb_convert_encoding($request_response, 'UTF-8', 'UTF7-IMAP');
    $response_handle = fopen($full_filename, 'w');
    fwrite($response_handle, $request_response);
    fclose ($response_handle);
        
    return $request_response;
}