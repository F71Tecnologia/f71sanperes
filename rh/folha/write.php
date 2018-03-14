<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$id_folha = $_REQUEST['id_folha'];
$cookie = $_REQUEST['cookie'];

file_put_contents("pdf_{$id_folha}_{$cookie}.html", urldecode($_POST['dom']));
?>