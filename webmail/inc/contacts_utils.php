<?php
session_start();

/*
 * Adicionar contato de usuário no sistema
 */

function add_contact($hashContact) {
    $contactFilename = returnFilePath($hashContact['user_email']);
    unset($hashContact['user_email']); //do not needed;

    $contact_buffer = array();
    if (file_exists($contactFilename)) {
        $FH = fopen($contactFilename, 'r+');
        $contactFiles = fread($FH, filesize($contactFilename));
        fclose($FH);

        $buffer = (array) json_decode($contactFiles, true);
        array_push($buffer['contacts'],$hashContact);
        $contact_buffer['contacts'] =  $buffer['contacts'];

        $FH = fopen($contactFilename, 'w');
        fwrite($FH, json_encode($contact_buffer));
        fclose($FH);
    } else {
        $FH = fopen($contactFilename, 'w');

        $contact_buffer['contacts'] = array($hashContact);
        fwrite($FH, json_encode($contact_buffer));
        fclose($FH);
    }

    return (file_exists($contactFilename))? $contact_buffer: array('error'=> true);
}



/*
 * Lista contatos
 */
function list_contacts($user_email, $search = '.') {
    $filePath = returnFilePath($user_email);
    if (!file_exists($filePath)) return array('ERROR' => 'File Not Found');
    $FH = fopen($filePath, 'r');
    $contacts = fread($FH, filesize($filePath));
    fclose($FH);
    $contacts_collection = (array) json_decode($contacts, true);

    $query = array(
        'results'=> array(),
    );
    foreach ($contacts_collection as $collections) {
        foreach ($collections as $key => $value) {
            (fetch_contact_info($collections[$key], $search, 'name'))?
                $query['results'][] = $collections[$key]: null;
        }
    }

    return $query;
}

function fetch_contact_info($contacts, $search = '.', $matchOn) {
    if (preg_match("/$search/i", $contacts[$matchOn])) {
       return true;
    }
    return false;
}


/*
 * Informação de contato por email
 */
function get_contact_info($getter) {
    if (!$getter) return;

    $fullFilename = returnFilePath($getter['user_email']);
    unset($getter['user_email']);

    $FH = fopen($fullFilename, 'r');
    $file = fread($FH, filesize($fullFilename));
    fclose($FH);
    $contacts = (array) json_decode($file, true);

    $info_fetch = '';

    foreach ($contacts['contacts'] as $key => $value) {
        ((fetch_contact_info($contacts['contacts'][$key], $getter['contact_email'], 'email'))?
            $info_fetch = $contacts['contacts'][$key]
            :null);
    }


    return $info_fetch;
}



/*
  Atualiza informações de contato
  Dado um array de array de  arrays (sim, assim mesmo)
  Implementa a alteração usando alg. FILO (First in Last Out)
*/
function set_contact_info($setter) {
    $fullFilename = returnFilePath($setter['user_email']);
    unset($setter['user_email']);

    $FH = fopen($fullFilename, 'r');
    $contacts = (array) json_decode(fread($FH, filesize($fullFilename)), true);
    fclose($FH);

    foreach ($contacts['contacts'] as $key => $value) {
        $buffer_arr = array_pop($contacts['contacts']);

        if (fetch_contact_info($buffer_arr, $setter['original_mail'], 'email')) {
            $buffer_arr = array(
                'name' => $setter['contact_name'],
                'email' => $setter['contact_email'],
                'address' => $setter['contact_addr'],
            );

            array_unshift($contacts['contacts'], $buffer_arr);

        } else {
            array_unshift($contacts['contacts'], $buffer_arr);
        }
    }

    $FH = fopen($fullFilename, 'w');
    fwrite($FH, json_encode($contacts));
    fclose($FH);

    return $contacts;
}


/*
 * Deleta contato
 */
function delete_contact($setter) {

   $fullFilename = returnFilePath($setter['user_email']);
   unset($setter['user_email']);

   $FH = fopen($fullFilename, 'r');
   $contacts = (array) json_decode(fread($FH, filesize($fullFilename)), true);
   fclose($FH);

    foreach ($contacts['contacts'] as $key => $value) {
        $buffer_arr = array_pop($contacts['contacts']);

        if (!fetch_contact_info($buffer_arr, $setter['contact_email'], 'email')) {
            array_unshift($contacts['contacts'], $buffer_arr);
        }
    }

    $FH = fopen($fullFilename, 'w');
    fwrite($FH, json_encode($contacts));
    fclose($FH);

    return $contacts;
}

function returnFilePath($email) {
    if (!$email) return;
    return 'user_contacts/'.preg_replace('/\W+/', '_', $email).'_contacts.json';
}