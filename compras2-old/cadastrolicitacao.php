<?php
include "../conn.php";

$id_compra= $_REQUEST['compra'];
$opcao = $_REQUEST['opcao'];
$motivo = $_REQUEST['motivo'];
$fornecedor_escolhido = $_REQUEST['fornecedor_id'];

if($opcao == '1')
{
	mysql_query("UPDATE compra2 SET fornecedor_escolhido = '$fornecedor_escolhido' WHERE id_compra='$id_compra'");
}

if($opcao == '0')
{
	mysql_query("UPDATE compra2 SET motivo = '$motivo' WHERE id_compra='$id_compra'");
}

	$destino0 = "../anexo_atas/";	
    $file_name0 = $id_compra;
    $file_type0 = $_FILES['ata']['type'];	
    $file_size0 = $_FILES['ata']['size'];
    $file_tmp_name0 = $_FILES['ata']['tmp_name'];
    $error0 = $_FILES['ata']['error'];
	
	
	switch($file_type0) {				
			case 	"application/msword": $ext0 = 'doc';
			break;			
			case 	"application/pdf": $ext0 = 'pdf';
			break;			
			case 	 "image/jpeg": $ext0 = 'jpg';
			break;			
			case 	 "image/jpg": $ext0 = 'jpg';
			break;			
			case 	"application/vnd.openxmlformats-officedocument.wordprocessingml.document": $ext0 = 'docx';
			break;			
			case 	 "image/pjpeg": $ext0 = 'jpg';
			break;			
			case 	 "application/vnd.oasis.opendocument.text": $ext0 = 'odt';
			break;			
			case 	 "image/png": $ext0 = 'png';
			break;			
			case   'application/rtf': $ext0 = 'rtf';
			break;			
			case   'application/rtf': $ext0 = 'rtx';
			break; 					
			}
			
$nomearq0 = $file_name0.".".$ext0;

if($error0 == 0){
    if(!is_uploaded_file($file_tmp_name0)){
        echo 'Erro ao processar arquivo!';
    }else{

        if(!move_uploaded_file($file_tmp_name0,$destino0."".$file_name0.".".$ext0)) {
            echo 'Não foi possível salvar o arquivo!';
        }else{
		
        }
    }
}
	
	
	


for ($i = 0; $i < count($_POST['fornecedor_site_id']); $i++) {
	

//echo $_FILES['anexo_proposta']['name'][$i]." - ".$_REQUEST['fornecedor_site_id'][$i]."<br>";

	$array_forn = $_REQUEST['fornecedor_site_id'][$i];


	$destino1 = "../anexo_propostas/";	
    $file_name1 = $id_compra."_".$_REQUEST['fornecedor_site_id'][$i];
    $file_type1 = $_FILES['anexo_proposta']['type'][$i];	
    $file_size1 = $_FILES['anexo_proposta']['size'][$i];
    $file_tmp_name1 = $_FILES['anexo_proposta']['tmp_name'][$i];
    $error1 = $_FILES['anexo_proposta']['error'][$i];		
		
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
			
if($_FILES['anexo_proposta']['type'][$i] == '')
{
$nomearq1 = 0;
}else
{
$nomearq1 = $file_name1.".".$ext1;
}
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


mysql_query("UPDATE fornecedor_site SET anexo_proposta='$nomearq1' WHERE fornecedor_site_id= '$array_forn'");







}



if($opcao == '1')
{
mysql_query("UPDATE compra2 SET anexo_ata='$file_name0.$ext0', acompanhamento='7'  WHERE id_compra= '$id_compra'");

$link = "ver_licitacao.php?compra=$id_compra"; 
echo "<script>location.href='".$link."';</script>";

}

if($opcao == '0')
{
mysql_query("UPDATE compra2 SET acompanhamento='5'  WHERE id_compra= '$id_compra'");


$link = "../gestaocompras2.php"; 
echo "<script>location.href='".$link."';</script>";

}





?>