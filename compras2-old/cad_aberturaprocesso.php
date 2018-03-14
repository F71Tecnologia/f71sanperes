<?php 
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "../conn.php";

include('../classes_permissoes/acoes.class.php');


	$id_compra = $_REQUEST['compra'];
	$regiao    = $_REQUEST['regiao'];
	$id_user = $_COOKIE['logado'];
	
	$descricao = strtoupper($_REQUEST['descricao']);

	
	$destino1 = "../anexo_cotacao/";
	$destino2 = "../anexo_docpreco/";
	$destino3 = "../anexo_minutaedital/";
	$destino4 = "../anexo_minutacontrato/";
	
	
    $file_name1 = $id_compra;
	$file_name2 = $id_compra;
	$file_name3 = $id_compra;
	$file_name4 = $id_compra;

	
    $file_type1 = $_FILES['cotacao']['type'];
	$file_type2 = $_FILES['docpreco']['type'];
	$file_type3 = $_FILES['minutaedital']['type'];
	$file_type4 = $_FILES['minutacontrato']['type'];
	
	
    $file_size1 = $_FILES['cotacao']['size'];
	$file_size2 = $_FILES['docpreco']['size'];
	$file_size3 = $_FILES['minutaedital']['size'];
	$file_size4 = $_FILES['minutacontrato']['size'];
	
	
    $file_tmp_name1 = $_FILES['cotacao']['tmp_name'];
	$file_tmp_name2 = $_FILES['docpreco']['tmp_name'];
    $file_tmp_name3 = $_FILES['minutaedital']['tmp_name'];
	$file_tmp_name4 = $_FILES['minutacontrato']['tmp_name'];
	

    $error1 = $_FILES['cotacao']['error'];
    $error2 = $_FILES['docpreco']['error'];
    $error3 = $_FILES['minutaedital']['error'];
    $error4 = $_FILES['minutacontrato']['error'];
  
		
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
			
	switch($file_type3) {				
			case 	"application/msword": $ext3 = 'doc';
			break;			
			case 	"application/pdf": $ext3 = 'pdf';
			break;			
			case 	 "image/jpeg": $ext3 = 'jpg';
			break;			
			case 	 "image/jpg": $ext3 = 'jpg';
			break;			
			case 	"application/vnd.openxmlformats-officedocument.wordprocessingml.document": $ext3 = 'docx';
			break;			
			case 	 "image/pjpeg": $ext3 = 'jpg';
			break;			
			case 	 "application/vnd.oasis.opendocument.text": $ext3 = 'odt';
			break;			
			case 	 "image/png": $ext3 = 'png';
			break;			
			case   'application/rtf': $ext3 = 'rtf';
			break;			
			case   'application/rtf': $ext3 = 'rtx';
			break; 					
			}
			
	switch($file_type4) {				
			case 	"application/msword": $ext4 = 'doc';
			break;			
			case 	"application/pdf": $ext4 = 'pdf';
			break;			
			case 	 "image/jpeg": $ext4 = 'jpg';
			break;			
			case 	 "image/jpg": $ext4 = 'jpg';
			break;			
			case 	"application/vnd.openxmlformats-officedocument.wordprocessingml.document": $ext4 = 'docx';
			break;			
			case 	 "image/pjpeg": $ext4 = 'jpg';
			break;			
			case 	 "application/vnd.oasis.opendocument.text": $ext4 = 'odt';
			break;			
			case 	 "image/png": $ext4 = 'png';
			break;			
			case   'application/rtf': $ext4 = 'rtf';
			break;			
			case   'application/rtf': $ext4 = 'rtx';
			break; 					
			}
			
$nomearq1 = $file_name1.".".$ext1;
$nomearq2 = $file_name2.".".$ext2;
$nomearq3 = $file_name3.".".$ext3;
$nomearq4 = $file_name4.".".$ext4;

			

if($error1 == 0){
    if(!is_uploaded_file($file_tmp_name1)){
        echo 'Erro ao processar arquivo!';
    }else{

        if(!move_uploaded_file($file_tmp_name1,$destino1."".$file_name1.".".$ext1)) {
            echo 'Não foi possível salvar o arquivo!';
        }else{
		
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
			
        }
    }
}

if($error3 == 0){
    if(!is_uploaded_file($file_tmp_name3)){
        echo 'Erro ao processar arquivo!';
    }else{

        if(!move_uploaded_file($file_tmp_name3,$destino3."".$file_name3.".".$ext3)) {
            echo 'Não foi possível salvar o arquivo!';
        }else{		

        }
    }
}

if($error4 == 0){
    if(!is_uploaded_file($file_tmp_name4)){
        echo 'Erro ao processar arquivo!';
    }else{

        if(!move_uploaded_file($file_tmp_name4,$destino4."".$file_name4.".".$ext4)) {
            echo 'Não foi possível salvar o arquivo!';
        }else{		
			
        }
    }
}


mysql_query("INSERT INTO anexo_abertura_proc VALUES('$id_compra', '$nomearq1','$nomearq2','$nomearq3','$nomearq4','$descricao')");


mysql_query("UPDATE compra2 SET acompanhamento = 2 WHERE id_compra=$id_compra");

$link = "ver_aberturaprocesso.php?compra=$id_compra"; 
echo "<script>location.href='".$link."';</script>";
}
?>
