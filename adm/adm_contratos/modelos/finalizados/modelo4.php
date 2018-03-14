<?php
include('../../include/restricoes.php');
include('../../../../conn.php');
include('../../../../classes/formato_valor.php');
include('../../../../classes/formato_data.php');
include('../../../../classes/valor_extenso.php');

$id_entregue 	= $_POST['entregue_id'];
$ano_competencia = $_POST['ano_competencia'];

$qr_anexo_2 = mysql_query("SELECT * FROM obrigacoes_anexo_2 WHERE entregue_id = '$id_entregue' AND ano_competencia = '$ano_competencia' AND status = 1");
$row_anexo_2 = mysql_fetch_assoc($qr_anexo_2);




?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script type="text/javascript" src="../../../jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../../../jquery/mascara/jquery.maskedinput-1.2.2.js" ></script>
<script type="text/javascript" src="../../../jquery/priceFormat.js" ></script>
<title>Publica&ccedil;&atilde;o</title>


<style type="text/css">
.pontilhado {
	border-bottom:1px dotted #999;
	padding-bottom:2px;
	font-style:italic;
	font-weight:bold;
}
td.secao {
	background-color:#eee;
	text-align:right;
	font-weight:bold;
	padding-right:5px;
}
tr.secao td {
	background-color:#eee;
	text-align:center;
	font-weight:bold;
}


 #total_repasse{
	
		background-color:transparent;
	 border:0px;
	  margin:0;
	}
</style>

<style type="text/css" media="print">
#total_repasse{
	
		background-color:transparent;
	 border:0px;
	  margin:0;
	  font-size:11.3px;
	  letter-spacing:10	px;
	}
</style>
</head>
<body style="text-align:center; margin:0; background-color:#efefef; font-family:Arial, Helvetica, sans-serif; font-size:13px; text-transform:uppercase;">

<form name="form" action="../gerar_publicacao.php" method="post">

<table style="margin:50px auto; width:790px; border:1px solid #222; text-align:left; padding:10px; background-color:#fff;" cellpadding="4" cellspacing="1">
  <tr>
    <td colspan="6">
      <p align="center"><strong>DECRETO N&ordm; 3.100, DE  30 DE JUNHO DE 1999.</strong><br />ANEXO II</p>
      <p align="center"> <?php echo $row_anexo_2['prefeitura']; ?></p></td>
  </tr>
  <tr class="secao">
    <td colspan="6">Extrato de Relat&oacute;rio de Execu&ccedil;&atilde;o F&iacute;sica e Financeira de Termo de Parceria</td>
  </tr>
  <tr>
    <td colspan="6">&nbsp;</td>
  </tr>
  <tr>
    <td class="secao">Custo do projeto:</td>
    <td colspan="5">R$ <?php echo number_format($row_anexo_2['custo_projeto'],2,',','.').' ('.htmlentities(valorPorExtenso($row_anexo_2['custo_projeto']),ENT_COMPAT,'UTF-8'),')'; ?></td>
  </tr>
  <tr>
    <td class="secao">Local de realiza&ccedil;&atilde;o do projeto:</td>
    <td colspan="5"><?php echo $row_anexo_2['local_projeto']; ?></td>
  </tr>
  <tr>
    <td class="secao" width="25%">Data de assinatura do TP:</td>
    <td width="15%"><?php echo implode('/', array_reverse(explode('-', $row_anexo_2['data_assinatura']))); ?></td>
    <td class="secao" width="15%">In&iacute;cio do projeto:</td>
    <td width="15%"><?php echo implode('/', array_reverse(explode('-', $row_anexo_2['inicio_projeto']))); ?></td>
    <td class="secao" width="15%">T&eacute;rmino:</td>
    <td width="15%">
    <?php		
	echo implode('/', array_reverse(explode('-', $row_anexo_2['termino_projeto'])));
	
	?>
	</td>
  </tr>
  <tr>
    <td class="secao">Objetivos do projeto:</td>
    <td colspan="5"><?php echo $row_anexo_2['obj_projeto']; ?></td>
  </tr>
  <tr>
    <td class="secao">Resultados alcan&ccedil;ados:</td>
    <td colspan="5">Considerando o TERMO DE PARCERIA celebrado entre a  OSCIP e a Prefeitura Municipal de <?php echo $row_regiao['regiao']; ?> conclu&iacute;mos que o <?php echo $row_projeto['nome']; ?> teve um desempenho satisfat&oacute;rio, contribuindo efetivamente para o &ecirc;xito desta parceria. Obedecendo aos crit&eacute;rios estipulados no programa de Trabalho cumprindo os requisitos necess&aacute;rios.</td>
  </tr>
  <tr>
    <td colspan="6" align="center"><p>&nbsp;</p>
      <style type="text/css">
	  table#financeiro td {
		  background-color:#fff;
	  }
	  </style>
      <table style="background-color:#000; width:75%;" cellpadding="4" cellspacing="1" id="financeiro">
        <tr>
          <td colspan="4" align="center"><strong>Custos de Implementa&ccedil;&atilde;o do Projeto</strong></td>
        </tr>
        <tr>
          <td align="right"><strong>Categorias de despesas</strong></td>
          <td>Previsto</td>
          <td>Realizado</td>
          <td>Diferen&ccedil;a</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td  align="left">R$ <?php echo formato_real($row_anexo_2['cat_desp_previsto']); ?></td>
          <td  align="left">     
          R$ <?php echo formato_real($row_anexo_2['cat_desp_realizado']); ?>    
          </td>
          <td align="left"> 
           <div class="diferenca">
              <?php 
         
              echo 'R$ '.formato_real($row_anexo_2['cat_desp_diferenca']);
              
              ?>
            </div>
          </td>
        </tr>
        <tr>
          <td align="right"><strong>TOTAIS:</strong></td>
          <td>R$ <?php echo formato_real($row_anexo_2['total_previsto']); ?></td>
          
          <td align="left">
              <div class="realizado">
              
              R$ <?php echo formato_real($row_anexo_2['total_realizado']); ?>
              
              </div>
          
          </td>
          
          <td align="left">
                  <?php 
                    echo 'R$ '.formato_real($row_anexo_2['total_diferenca']);                                               
                  ?>
          </td>
        </tr>
                  </table>
                  <p>&nbsp;</p></td>
              </tr>
              <tr>
                <td class="secao">Nome da OSCIP:</td>
                <td colspan="5"><?php echo $row_anexo_2['nome_oscip'] ;?></td>
              </tr>
              <tr>
                <td class="secao">Endere&ccedil;o:</td>
                <td colspan="5"><?php echo $row_anexo_2['endereco_oscip']; ?></td>
              </tr>
              <tr>
                <td class="secao">Cidade:</td>
                <td><?php echo $row_anexo_2['cidade_oscip'] ?></td>
                <td class="secao">UF:</td>
                <td><?php echo  $row_anexo_2['uf_oscip']  ?></td>
                <td class="secao">CEP:</td>
                <td><?php echo  $row_anexo_2['cep_oscip']  ?></td>
              </tr>
              <tr>
                <td class="secao">Tel:</td>
                <td><?php echo  $row_anexo_2['tel_oscip']  ?></td>
                <td class="secao">Fax:</td>
                <td><?php echo  $row_anexo_2['fax_oscip']  ?></td>
                <td class="secao">E-mail:</td>
                <td><?php echo  $row_anexo_2['email_oscip']  ?></td>
              </tr>
              <tr>
                <td class="secao">Nome do respons&aacute;vel pelo projeto:</td>
                <td colspan="5"><?php echo  $row_anexo_2['nome_responsavel']  ?></td>
              </tr>
              <tr>
                <td class="secao">Cargo / Fun&ccedil;&atilde;o:</td>
                <td colspan="5">Presidente</td>
              </tr>
              
             
				<tr>
                	<td colspan="6" align="center">
           
                      <input name="id_anexo_2" type="hidden" value="<?php echo $row_anexo_2['obrigacoes_anexo_2_id']?>"/>                    
                      <input type="submit" name="gerar" value="Gerar"/>
              		</td>
              </tr>
            </table>
            </form>
 
</body>
</html>