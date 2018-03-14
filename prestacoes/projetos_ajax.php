<?php

include '../conn.php';

header( 'Cache-Control: no-cache' );
header( 'Content-type: application/xml; charset="utf-8"', true );

$id_projeto = mysql_real_escape_string( $_REQUEST['sel_projeto'] );
$projetos = array();

    $sql = "SELECT id_projeto, nome FROM projeto ORDER BY nome";
    
    $res = mysql_query( $sql );
	while ( $row = mysql_fetch_assoc( $res ) ) {
            $projetos[] = array('id_projetos' => $row['id_projeto'], 'nome'  => $row['nome'], );
	}
	echo( json_encode( $projetos ) );