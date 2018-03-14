<?php

if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a>";
    exit;
}

include "../conn.php";

$projeto = $_REQUEST['projeto'];
$regiao = $_REQUEST['regiao'];

$qrClt = mysql_query("SELECT * FROM rh_clt WHERE id_clt IN (" . implode(",", $_REQUEST['idclt']) . ")");
            while ($row = mysql_fetch_array($qrClt)) {
                if ($_REQUEST['pisClt'][$row['id_clt']] != '') {
                    $dadoClt = array($row['id_clt'],$_REQUEST[pisClt][$row[id_clt]],$_REQUEST[dataPisClt][$row[id_clt]]);
                        $updatePisClt = mysql_query("UPDATE rh_clt SET
                                                    pis = '$dadoClt[1]',
                                                    dada_pis = '$dadoClt[2]'
                                                    where id_clt = $dadoClt[0] LIMIT 1");
                } else {
                    header("Location: semPis.php?reg=$regiao&pro=$projeto");
                    
                }
    }

$qrAut = mysql_query("SELECT * FROM autonomo WHERE id_autonomo IN (" . implode(",", $_REQUEST['idaut']) . ")");
            while ($rowAut = mysql_fetch_array($qrAut)) {
                
                if ($_REQUEST['pisAut'][$rowAut['id_autonomo']] != '') {
                    $teste = array($rowAut['id_autonomo'],$_REQUEST[pisAut][$rowAut[id_autonomo]],$_REQUEST[dataPisAut][$rowAut[id_autonomo]]);
                        $updatePis = mysql_query("UPDATE autonomo SET
                                                    pis = '$teste[1]',
                                                    dada_pis  = '$teste[2]'
                                                    where id_autonomo = $teste[0] LIMIT 1");
                } else {
                    header("Location: semPis.php?reg=$regiao&pro=$projeto");
                }
                
    }
?>
