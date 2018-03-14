<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
exit;
}

include "../conn.php";
include "../classes/regiao.php";

$id_user = $_COOKIE['logado'];
$cooperado = $_REQUEST['coop'];

//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
$data_cad = date('Y-m-d');
$user_cad = $_COOKIE['logado'];




$result_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '29' and id_clt = '$cooperado'");
$num_row_verifica = mysql_num_rows($result_verifica);
if($num_row_verifica == "0"){
	mysql_query("INSERT INTO rh_doc_status(tipo,id_clt,data,id_user) VALUES ('29','$cooperado','$data_cad', '$user_cad')");
}else{
	mysql_query("UPDATE rh_doc_status SET data = '$data_cad', id_user = '$user_cad' WHERE id_clt = '$cooperado' and tipo = '29'");
}
//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS

// PEGA O ID DO FUNCIONÁRIO LOGADO E SELECIONA OS DADOS DELE NA BASE DE DADOS
$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);

//FAZENDO UM SELECT NA TABELA MASTAR PARA PEGAR AS INFORMAÇÕES DA EMPRESA
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);

//SELECIONANDO A REGIAO AO QUAL ESTA LOGADO
$result_re = mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$row_user[id_regiao]'");
$row_re = mysql_fetch_array($result_re);




$codigo = sprintf("%04d",$Row['campo3']);

$dia = date('d');
$mes_h = date('m');
$ano = date('Y');

$meses = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
$MesInt = (int)$mes_h;
$mes = $meses[$MesInt];


//PEGANDO A ATIVIDADE 
include "../classes/curso.php";
$Ativ = new tabcurso();
$Ativ -> MostraCurso($Row['id_curso']);

$Atividade = $Ativ -> nome;
///

$link = "aprovadistrato.php?coop=$Row[0]";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>INTRANET - SUBSCRI&Ccedil;&Atilde;O DE QUOTAS - COOPERADO</title>
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	font-family:Arial, Helvetica, sans-serif;
}
-->
</style>

<style type='text/css' media='print'> 
.noprint
{ 
   display: none; 
} 
</style>

</head>

<body onload="javascript:window.print()" style="background-color:  #EBEBEB;"> 

<?php


$data_desligamento = '01/07/2012';

//INICIANDO O SELECT DO COOPERADO
$RE_ree = mysql_query("SELECT *,date_format(data_entrada, '%d/%m/%Y') as data_entrada ,date_format(data_saida, '%d/%m/%Y') as data_saida FROM autonomo WHERE id_regiao = 3 AND id_projeto = '3295'");
while($Row = mysql_fetch_array($RE_ree)):

	$RECoope = mysql_query("SELECT * FROM cooperativas WHERE id_coop = '$Row[id_cooperativa]'");
	$RowCoop = mysql_fetch_array($RECoope);
	
	
	
	$nome_regiao = mysql_result(mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$Row[id_regiao]'"),0);
	
	
	//VERIFICANDO SE VAI TER LOGO OU NÃO
	if($RowCoop['foto'] != "0"){
		$LOGO = "<img src='logos/coop_".$RowCoop['0'].$RowCoop['foto']."' width='120' height='86' />";
	}else{
		$LOGO = "";
	}	

?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#EAEAEA" style="margin-bottom:30px;">
  <tr>
    <td align="center" valign="top">
    
    <table width="700" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border:2px solid #666; height:1000px;">
      <tr>
        <td height="124"><p class="MsoHeader" align="center" style='text-align:center; color:#666'>
        <?=$LOGO?>
        <br />
        <span style="font-size:12px"><?=$RowCoop['nome']?></span>
        <br />
        <span style="font-size:10px"><?=$RowCoop['endereco']." Tel.: ".$RowCoop['tel']." CNPJ: ".$RowCoop['cnpj']?></span>
        
         </p></td>
        </tr>
      <tr>
        <td>
          <div style="margin:15px; font-family:Arial, Helvetica, sans-serif; font-size:13px" align="left">
            <p><strong>
            <?php
				 
				
				echo $nome_regiao;
				echo ", ";
				echo $dia.' de '.$mes.' de '.$ano;
			  ?>
            
            </strong></p>
            <p><strong>&Agrave; </strong><br />
                <strong><?=$RowCoop['nome']?></strong></p>
            <p><strong>&Agrave; Diretoria</strong></p>
            <p>&nbsp;</p>
            <p><em>Ref. Desligamento da  Cooperativa</em></p>
            <p>&nbsp;</p>
            <p align="justify"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Eu <strong><?=$Row['nome']?></strong>, brasileiro(a), <?=$Row['civil']?>, 
            <strong><?=$Atividade?></strong>, portador(a) do RG <strong><?=$Row['rg']?> </strong>e CPF <strong><?=$Row['cpf']?>,</strong> inscrito(a) no Coren/Crefito/CRM <strong><?=$Row['conselho']?></strong>,  residente a <strong><?=$Row['endereco']?></strong>, cooperado(a)  desta cooperativa desde <strong><?=$Row['data_entrada']?></strong>, venho solicitar meu <strong>DESLIGAMENTO</strong> junto a essa cooperativa a partir do dia <strong><?=$data_desligamento;?></strong> , motivo de for&ccedil;a maior.</p>
            <p>Atenciosamente,</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p><strong><?=$Row['nome']?>
            </strong></p>
            <p><strong>&nbsp;</strong></p>
            <p align="center"><strong>
              <?=$Row['endereco']." - ".$Row['bairro']." - ".$Row['cidade']." - ".$Row['uf']?><br />
              <em><?=$Row['tel_fixo']?></em></strong></p>
          </div></td>
      </tr>
     
    </table></td>
  </tr>
  <tr>
    <td align="center" valign="top" class='noprint'>
    <a href="<?=$link?>" style="text-decoration:none; color:#000">Imprimir Aprovação do Distrato</a>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    </td>
  </tr>
 
  
</table>

<?php endwhile; ?>
</body>
</html>