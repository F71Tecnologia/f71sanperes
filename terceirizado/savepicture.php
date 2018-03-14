<?php
    include('../conn.php');
    
    $based64Image=substr($_POST['imgBase64'], strpos($_POST['imgBase64'], ',')+1);
    $barcode = substr($_POST['barcode'], 4, strlen($_POST['barcode']));
    $tipo = substr($_POST['barcode'], 0, 4);
    $image = imagecreatefromstring(base64_decode($based64Image));
    
    // 4000 - Terceirizado
    if($tipo == '4000')
    {
	$sql = "select count(id_terceirizado) as total from terceirizado where id_terceirizado = '$barcode'";
    // 3000 - Autonomo
    }elseif($tipo == '3000')
    {
	$sql = "select count(id_autonomo) as total from autonomo where id_autonomo = '$barcode'";
    // 2000 - CLT
    }else
    {
	$sql = "select count(pis) as total from rh_clt where digits(pis) = '".trim($_POST['barcode'])."'";
	$barcode = $_POST['barcode'];
	$tipo = '2000';
    }

    $res = mysql_query($sql) or die("Query fail: " . mysqli_error());
    
    $row = mysql_fetch_row($res);

    $tot = $row[0];
    if($tot == 1)
    {
    	$fileName='';
	$path = "../fotos/";
	if($image != false)
	{
	    $fileName='000'.$tipo.'_cracha_'.$barcode."_".time().'.png';

	    $data = date("Y-m-d");
	    $dataf = date("d/m/Y");
	    $hora = date("H:i:s");
	    
	    if($tipo == '4000')
	    {
		$sql = "insert into terceiro_ponto(id_terceirizado, data, hora, imagem) values('{$barcode}', '".$data ."', '".$hora."', '{$fileName}')";
	    }elseif($tipo == '3000')
	    {
		$sql = "insert into terceiro_ponto(id_autonomo, data, hora, imagem) values('{$barcode}', '".$data ."', '".$hora."', '{$fileName}')";
	    }elseif($tipo == '2000')
	    {
		$sql = "insert into terceiro_ponto(pis, data, hora, imagem) values('{$barcode}', '".$data ."', '".$hora."', '{$fileName}')";
	    }
	    mysql_query ($sql) or die ("Ops! Erro<br>" . mysql_error(). " sql = [".$sql."]");

	    if(!imagepng($image, $path.$fileName))
	    {
    //          fail;
	    }
	    $ret = 0;
	}
	else
	{
    //          fail;
	}
    }else
    {
	$ret = 1;
    }
    $data = array(array('resp' => $ret, 'dataHora' => $dataf.' '.$hora));
    echo json_encode($data);
?>
