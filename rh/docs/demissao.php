<?php
if(empty($_COOKIE['logado'])){
    print "Efetue o Login<br><a href='login.php'>Logar</a> ";
    exit;
}

include "../../conn.php";
include "../../funcoes.php";
include "../../classes/regiao.php";
include "../../wfunction.php";


function verificaEvento($id_clt){
    $retorno = array("status" => false);
    $query = montaQuery("rh_clt", "status,nome", "id_clt = '{$id_clt}'");
    $query_status = montaQuery("rhstatus", "especifica", "codigo = '{$query[1]['status']}'");
    if($query[1]['status'] == 10){
        $retorno = array("status" => true);
    }else{
        $retorno = array("status" => false, "dados" => array("licenca" => $query_status[1]['especifica'], "nome" => $query[1]['nome']));
    }
    
    return $retorno;
}

$eventos  = verificaEvento($_REQUEST['clt']);


if(isset($_REQUEST['method']) && $_REQUEST['method'] != ""){
    if($_REQUEST['method'] == "criaLog"){
        
        $retorno = array("status" => 0);
        $query = "SELECT * FROM funcionario WHERE id_funcionario = '{$_COOKIE['logado']}'";
        $sql = mysql_query($query) or die("Erro ao selecionar funcionario");
        if(mysql_num_rows($sql) > 0){
            $criado_em = date("Y-m-d H:i:s");
            $ip = $_SERVER['REMOTE_ADDR'];
            $titulo = "AGUARDAR DEMISSÃO";
            $descricao = "Colocou o funcionário " . utf8_decode($_REQUEST['nome_clt']) . " que se encontra no evento de " . utf8_decode($_REQUEST['evento']) . " para AGUARDANDO DEMISSÃO";
            while($rows = mysql_fetch_assoc($sql)){
                $cria_log = "INSERT INTO log (id_user,id_regiao,tipo_user,grupo_user,local,horario,ip,acao,status) VALUES ('{$_COOKIE['logado']}','{$_REQUEST['id_reg']}','{$rows['tipo_usuario']}','{$rows['grupo_usuario']}','{$titulo}','{$criado_em}','{$ip}','{$descricao}','1')";
                $sql = mysql_query($cria_log);
                if($sql){
                    $retorno = array("status" => 1);
                }
            }
        }
        
        echo json_encode($retorno);
        exit();
    }
}

$REG = new regiao();

$id_clt = $_REQUEST['clt'];
$tab = $_REQUEST['tab'];
$pro = $_REQUEST['pro'];
$id_reg = $_REQUEST['id_reg'];

$qr_empresa = mysql_query("SELECT * FROM rhempresa WHERE  id_regiao = $id_reg AND  id_projeto = '$pro'");
$row_empresa = mysql_fetch_assoc($qr_empresa);

$qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$id_reg'");
$row_regiao = mysql_fetch_assoc($qr_regiao);

$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_regiao[id_master]' ");
$row_master = mysql_fetch_assoc($qr_master);

$result_bol = mysql_query("SELECT *,date_format(data_entrada, '%d/%m/%Y')as data_entrada,date_format(data_saida, '%d/%m/%Y')as data_saida FROM rh_clt where id_clt = '$id_clt'");
$row = mysql_fetch_array($result_bol);

$result_curso = mysql_query("Select * from curso where id_curso = $row[id_curso]");
$row_curso = mysql_fetch_array($result_curso);

$result_pro = mysql_query("Select * from projeto where id_projeto = $pro");
$row_pro = mysql_fetch_array($result_pro);

$data = date('d/m/Y');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>PEDIDO DE DEMISSÃO</title>
<link href="../../net1.css" rel="stylesheet" type="text/css" />
<link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="../../js/ramon.js"></script>
<script language="javascript" type="text/javascript" src="../../js/jquery-1.8.3.min.js"></script>
<script language="javascript" type="text/javascript" src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
<script language="javascript" type="text/javascript" src="../../js/global.js"></script>
<script>
    $(function(){
        var evento      = $("#evento").val();
        var nome_event  = $("#nome_evento").val();
        var id_clt      = $("#clt").val();
        var nome_clt    = $("#nome_clt").val();
        var projeto     = $("#pro").val();
        var regiao      = $("#id_reg").val();
        if(evento == 1){
            thickBoxConfirm("Verificação de Evento", "Existe um evento de ("+nome_event+") para este funcionário, deseja realmente coloca-lo para aguardando demissão ?", 450, 350, function(data){
                if(data === true){
                    $.ajax({
                       url:"",
                       type:"POST",
                       dataType:"json",
                       data:{
                           method:"criaLog",
                           evento:nome_event,
                           id_clt:id_clt,
                           nome_clt:nome_clt,
                           projeto:projeto,
                           regiao:regiao
                       },
                       success: function(data){
                           
                       }
                    });
                }else if(data === false){
                    location.href="http://www.netsorrindo.com/intranet";
                }
            }, "confirm", true);
        }
        
        $("body").on("click",".ui-icon-closethick",function(){
            location.href="http://www.netsorrindo.com/intranet";
        });
        
        
    });
</script>

</head>

<body>
<?php
if(empty($_REQUEST['data_demi'])){
	$d = explode("-",date('Y-m-d'));
	$data_fim = date('d/m/Y', mktime(0,0,0, $d[1], $d[2] + 30, $d[0]));
?>
	<form method="post" action="<?=$_SERVER['PHP_SELF']?>" name="form">
	<table width="400" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="bordaescura1px">
	<tr height="35">
            <td height="28" colspan="2" class="show" align="center">« Demissão »</td>
	  </tr>
	<tr height="35">
	  <td width="141" height="28" align="right" class="secao">Data do aviso:</td>
	  <td width="257" height="28">&nbsp;&nbsp;
      <input type="text" name="data_aviso" id="data_aviso" size="12" onkeyup="mascara_data(this)" class="campotexton" value="<?=date('d/m/Y')?>"/></td>
	  </tr>
	<tr height="35">
	  <td height="28" class="secao" ><span class="red">Data da demiss&atilde;o:</span></td>
	<td height="28">&nbsp;&nbsp;&nbsp;<input type="text" name="data_demi" id="data_demi" size="12"  value="<?=$data_fim?>" onkeyup="mascara_data(this)" class="campotexton"/></td></tr>
	<tr>
	  <td height="28" class="secao" >Aviso pr&eacute;vio:</td>
	  <td>&nbsp;&nbsp;
      <select name="aviso" id="aviso" class="campotexton">
        <option>Trabalhado</option>
        <option>Indenizado</option>
      </select>
      
      </td>
	  </tr>
	<?php if (!empty($row['observacao'])) { ?>
            <tr>
                <td colspan="2" class="cor-4">
                    <h4 style="margin-left:15px; margin-right:15px;">Observações:</strong></h4>
                    <p style="margin-left:15px; margin-right:15px;"><?=$row['observacao']?></p>
                </td>
            </tr>
        <?php } ?>
	<tr height="30"><td height="65" colspan="2" align="center"><input type="submit" value="Enviar" class="botao" /></td></tr></table>
	<input type="hidden" name="clt" id="clt" value="<?=$id_clt?>"/>
	<input type="hidden" name="pro" id="pro" value="<?=$pro?>"/>
	<input type="hidden" name="id_reg" id="id_reg" value="<?=$id_reg?>"/>
        <?php if($eventos["status"] == false){ ?>
        <input type='hidden' name='nome_evento' id='nome_evento' value='<?php echo $eventos["dados"]["licenca"]; ?>' />
            <input type='hidden' name='evento' id='evento' value='1' />
            <input type='hidden' name='nome_clt' id='nome_clt' value='<?php echo $eventos["dados"]["nome"]; ?>' />
        <?php } ?>
</form>

<?php
	exit;
}else{
	
	$data_demi 	= $_REQUEST['data_demi'];
	$data_aviso = $_REQUEST['data_aviso'];
	$pro 		= $_REQUEST['pro'];
	$id_reg 	= $_REQUEST['id_reg'];
	$dtAviso = converteData($data_aviso);
            
          
       
	//PEQUENA FUNÇÃO PARA QUEBRAR A DATA 
	if(strstr($data_demi, "/")){
		$d = explode ("/", $data_demi);
		$dia = $d[0];
		$mes = $d[1];
		$ano = $d[2];
		
		$Dat = $ano."-".$mes."-".$dia;
	}
	
	
	//------------- GRAVANDO NA TABELA DOCUMENTOS GERADOS
	$data_cad = date('Y-m-d');
	$user_cad = $_COOKIE['logado'];
	
        
        if($_COOKIE['logado'] != 87) {
	$result_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '31' and id_clt = '$id_clt'");
	$num_row_verifica = mysql_num_rows($result_verifica);
	if($num_row_verifica == "0"){
		mysql_query("INSERT INTO rh_doc_status(tipo,id_clt,data,id_user) VALUES ('31','$id_clt','$data_cad', '$user_cad')");
	}else{
		mysql_query("UPDATE rh_doc_status SET data = '$data_cad', id_user = '$user_cad' WHERE id_clt = '$id_clt' and tipo = '31'");
	}
	//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
	
	
	// GRAVANDO NA RH_CLT A DATA DO PEDIDO DE EMISSÃO
	mysql_query("UPDATE rh_clt SET data_demi = '$Dat', data_aviso = '$dtAviso', status = '200' WHERE id_clt = '$id_clt' LIMIT 1");
	
	// GRAVANDO NA TABELA RH EVENTOS
	//mysql_query("INSERT INTO rh_eventos (id_clt, id_regiao, id_projeto, cod_status, data, status, status_reg) VALUES ('$id_clt', '$id_reg', '$pro', '991', '$Dat', '1', '1')") or die(mysql_error());
		
        }
	#RESOLVENDO QUAL ARQUIVO VAI SER CHAMADO
	//-- ENCRIPTOGRAFANDO A VARIAVEL
	$link = encrypt("$id_clt&$pro&$id_reg&$data_demi&$data_aviso"); 
	$link = str_replace("+","--",$link);
	// -----------------------------

	if($_REQUEST['aviso'] == "Trabalhado"){
		echo "<script> location.href = '2avisotrabalhado.php?enc=$link'; </script>";
	}elseif($_REQUEST['aviso'] == "Indenizado"){
		echo "<script> location.href = '2avisotrabalhado.php?enc=$link&p=1'; </script>";
	}
	

?>
<table width="700" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="bordaescura1px">
  <tr>
    <td height="40"><div class="style29" align="center">PEDIDO DE DEMISSÃO</div></td>
  </tr>
  <tr>
    <td><div style="padding:7px;">
      <p align="center" style='text-align:left'><span class="style4">
       <?php
        echo $REG -> RegiaoLogado();
		echo ", ";
		echo $REG -> MostraDataCompleta($data);
        ?>
      
      </span></p>
      <p class="style3"><br />
        <br />
        Ao, 
		<?php
		$REG -> EmpresaRegiaoLogado();
		echo $REG -> nome;
		
		?>
		<br />
		<br />
		<br />
      </p>
      <p class="style3">Prezados Senhores:<br />
        <br />
      </p>

      <p class="style3">
        Por razões particulares venho apresentar-lhes minha demissão do emprego que ocupo nesta empresa desde <span class="red"><?=$row['data_entrada']?></span>.
Tendo interesse em desligar-me imediatamente, solicito-lhes a dispensa do cumprimento do aviso prévio previsto nas disposições legais vigentes.
Aguardando um pronunciamento favorável, subscrevo-me.
<br />
      <br />
      <br />
      </p>

      <p class="style3">Sem mais,  agradecemos.<br />

        <br />
      </p>

      <p class="style3">Atenciosamente,<br />
        </p>
      <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
        <tr>
          <td width="50%" align="center"><span class="style31">______________________________________.<br />
            <b><span style="font-size:9.0pt;line-height:150%;font-family:verdana;

color:red"><?php print "$row[nome]"; ?></span></b></span></td>
          <td width="50%">O empregado deverá:</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td><p>(&nbsp;&nbsp;&nbsp;&nbsp; )&nbsp;  Cumprir o aviso pr&eacute;vio<br />
          (&nbsp;&nbsp;&nbsp;&nbsp; )&nbsp;  Ser dispensado do aviso pr&eacute;vio<br />
          (&nbsp;&nbsp;&nbsp;&nbsp; )&nbsp;  ....................................................</p></td>
        </tr>
      </table>
      <p>Ciente:&nbsp;<span class="style4">
        <?php
        echo $REG -> RegiaoLogado();
		echo ", ";
		echo $REG -> MostraDataCompleta($data);
        ?>
      </span></p>
      <p class="style3" align="center"><br />
        <span class="style31">______________________________________.<br />
        <b><span style="font-size:9.0pt;line-height:150%;font-family:verdana;

color:red">
<?php
$REG -> EmpresaRegiaoLogado();
echo $REG -> nome;

?></span></b></span><br />
      </p>

      <p align="center" class="style3">&nbsp;</p>

    
    
    </div>
    </td>

  </tr>

  <tr>

    <td colspan="5"><div align="center">

      <p>
<?php
echo '<div style="font-family: Arial, Helvetica, sans-serif; font-size: 12px;color: black" align="center">';
	echo '<div style="font-weight: bold"> '.$row_master['razao'].' </div>';
	echo '<br>';
	echo '<div>CNPJ: '.$row_master['cnpj'].'</div>';
	echo '<div> '.$row_master['endereco'].' </div>';
	echo '<div> '.$row_master['telefone'].' </div>';
	echo '</div>';


?><span class='style13 style3 style4'>&nbsp;</span>

        <span class='style13 style3 style4'>&nbsp;</span>    <span class='style13'></p>

      <p>&nbsp;</p>
    </div>    
      </tr>

</table>

</body>

</html>

<?php
}