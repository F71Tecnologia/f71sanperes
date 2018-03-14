<?php
if(isset($_REQUEST['upload'])){
	include '../../conn.php';
        
        
	extract($_REQUEST);
        
//        $id_nota = $upload;
        $id_nota = $_REQUEST['id_nota'];
	$arquivo  = $_FILES['Filedata']['name'];
	$nome	  = md5(uniqid(pathinfo($arquivo, PATHINFO_BASENAME)));
	$extensao = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));
        
	$diretorio = 'notas';
	
        $selNotaFile = "SELECT * FROM notas_files WHERE id_notas = $id_nota LIMIT 1";
        $querySelNotaFile = mysql_query($selNotaFile);
        
        if (mysql_num_rows($querySelNotaFile)) {
            $updateNotaFiles = "UPDATE notas_files SET status = '0' WHERE id_notas = $id_nota";
            $queryNotaFiles = mysql_query($updateNotaFiles);
        }
//        echo "INSERT INTO notas_files (id_notas, tipo, status) VALUES ('$id_nota' , '$extensao', '1')";
        
        $qr_inser = mysql_query( "INSERT INTO notas_files (id_notas, tipo, status) VALUES ('$id_nota' , '$extensao', '1')") or die(mysql_error());																
        $id_file = (int) @mysql_insert_id();
        
	$up = move_uploaded_file($_FILES['Filedata']['tmp_name'],$diretorio.'/'.$id_file.'.'.$extensao);


	if( $qr_inser && $up) {
		$json_resposta['erro'] = false;
	} else {
		$json_resposta['erro'] = true;
	}

	if($extensao == 'pdf') { 
            $json_resposta['src'] = '../../intranet/img_menu_principal/pdf.png' ;
	} else {
		$json_resposta['src']  = $diretorio.'/'.$id_file.'.'.$extensao;
	}
	
	$json_resposta['ID']   = $id_file;
	  
        // importando arquivo para entradas
        $query = "SELECT id_entrada FROM notas_assoc WHERE id_notas = $id_nota";
        $result = mysql_query($query);
        
        if(mysql_num_rows($result) > 0){
            $id_entrada = mysql_fetch_assoc($result);
            
            $updateEntradaFiles = "UPDATE entrada_files SET status = '0' WHERE id_entrada = {$id_entrada['id_entrada']}";
            $queryEntradaFiles = mysql_query($updateEntradaFiles);
            
            $query2 = "INSERT INTO entrada_files (id_entrada,tipo_files,status) VALUES ({$id_entrada['id_entrada']},'.$extensao',1)";
            $r2 = mysql_query($query2);
            $id_entrada_files = mysql_insert_id();
            copy($diretorio.'/'.$id_file.'.'.$extensao, "../../novoFinanceiro/comprovantes/entrada/{$id_entrada_files}.$extensao");
            copy($diretorio.'/'.$id_file.'.'.$extensao, "../../finan/comprovantes/entrada/{$id_entrada_files}.$extensao");
        }
        
        
        $json_resposta['retonro'] = "positivo";
	echo json_encode($json_resposta);
	exit;
}



if(isset($_REQUEST['deletar'])){
	include "../../conn.php";

	@mysql_query("UPDATE notas_files SET status = '0' WHERE id_file = '$_REQUEST[id_anexo]' LIMIT 1");
	exit;
}


if(isset($_REQUEST['ordem'])) {
    include "../../conn.php";

    $id_anexo  = $_REQUEST['id_anexo'];
    $valor     = $_REQUEST['valor'];

   $qr_update = mysql_query("UPDATE notas_files SET ordem = '$valor' WHERE id_file = '$id_anexo' LIMIT 1");

    $json_resposta['erro'] = ($qr_update) ? false : true;

    echo json_encode($json_resposta);
    exit;
}



?>