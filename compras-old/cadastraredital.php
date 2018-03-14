<?php 
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "../conn.php";

include('../classes_permissoes/acoes.class.php');


	$nedital  = $_REQUEST["nedital"];
	$nprocadm = $_REQUEST["nprocadm"]; 
	$nproclic = $_REQUEST["nproclic"];
	

	$id_compra = $_REQUEST['compra'];
	$regiao    = $_REQUEST['regiao'];
	$id_user = $_COOKIE['logado'];
	

	$destino1 = "../anexo_edital/";
	$destino2 = "../anexo_chamamento/";
	
	
    $file_name1 = $id_compra;
	$file_name2 = $id_compra;
	
	
    $file_type1 = $_FILES['up_edital']['type'];
	$file_type2 = $_FILES['up_chamamento']['type'];
	
	
    $file_size1 = $_FILES['up_edital']['size'];
	$file_size2 = $_FILES['up_chamamento']['size'];
	
	
    $file_tmp_name1 = $_FILES['up_edital']['tmp_name'];
	$file_tmp_name2 = $_FILES['up_chamamento']['tmp_name'];
	

    $error1 = $_FILES['up_edital']['error'];
    $error2 = $_FILES['up_chamamento']['error'];
  
		
	switch($file_type1) {				
			case 	"application/msword": $ext1 = 'doc';
			break;			
			case 	"application/pdf": $ext1 = 'pdf';
			break;			
			case 	 "image/jpeg": $ext1 = 'jpg';
			break;			
			case 	 "image/jpg": $ext1 = 'jpg';
			break;			
			case 	"application/vnd.openxmlformats-officedocument.wordprocessingml.document": $ext1 = 'docx';
			break;			
			case 	 "image/pjpeg": $ext1 = 'jpg';
			break;			
			case 	 "application/vnd.oasis.opendocument.text": $ext1 = 'odt';
			break;			
			case 	 "image/png": $ext1 = 'png';
			break;			
			case   'application/rtf': $ext1 = 'rtf';
			break;			
			case   'application/rtf': $ext1 = 'rtx';
			break; 					
			}
			
		switch($file_type2) {				
			case 	"application/msword": $ext2 = 'doc';
			break;			
			case 	"application/pdf": $ext2 = 'pdf';
			break;			
			case 	 "image/jpeg": $ext2 = 'jpg';
			break;			
			case 	 "image/jpg": $ext2 = 'jpg';
			break;			
			case 	"application/vnd.openxmlformats-officedocument.wordprocessingml.document": $ext2 = 'docx';
			break;			
			case 	 "image/pjpeg": $ext2 = 'jpg';
			break;			
			case 	 "application/vnd.oasis.opendocument.text": $ext2 = 'odt';
			break;			
			case 	 "image/png": $ext2 = 'png';
			break;			
			case   'application/rtf': $ext2 = 'rtf';
			break;			
			case   'application/rtf': $ext2 = 'rtx';
			break; 					
			}
			
			
$nomearq1 = $file_name1.".".$ext1;
$nomearq2 = $file_name2.".".$ext2;
			

if($error1 == 0){
    if(!is_uploaded_file($file_tmp_name1)){
        echo 'Erro ao processar arquivo!';
    }else{

        if(!move_uploaded_file($file_tmp_name1,$destino1."".$file_name1.".".$ext1)) {
            echo 'Não foi possível salvar o arquivo!';
        }else{
          /*  echo 'Processo concluido com sucesso!<br>';
            echo "Nome do arquivo: $arquivo<br>";
            echo "Tipo de arquivo: $file_type<br>";
            echo "Tamanho em byte: $file_size<br>";
			*/
						mysql_query("INSERT INTO anexo_chamamento VALUES('','$id_compra','$nomearq1','1',NOW(),'$id_user')");
			
			
        }
    }
}

if($error2 == 0){
    if(!is_uploaded_file($file_tmp_name2)){
        echo 'Erro ao processar arquivo!';
    }else{

        if(!move_uploaded_file($file_tmp_name2,$destino2."".$file_name2.".".$ext2)) {
            echo 'Não foi possível salvar o arquivo!';
        }else{
          /*  echo 'Processo concluido com sucesso!<br>';
            echo "Nome do arquivo: $arquivo<br>";
            echo "Tipo de arquivo: $file_type<br>";
            echo "Tamanho em byte: $file_size<br>";
			*/
			
						mysql_query("INSERT INTO anexo_chamamento VALUES('','$id_compra','$nomearq2','2',NOW(),'$id_user')");
			
        }
    }
}





mysql_query("UPDATE compra SET acompanhamento = 3, nedital = '$nedital', nprocadm = '$nprocadm', nproclic = '$nproclic' WHERE id_compra=$id_compra");

$link = "../gestaocompras.php"; 
echo "<script>location.href='".$link."';</script>";
}
?>