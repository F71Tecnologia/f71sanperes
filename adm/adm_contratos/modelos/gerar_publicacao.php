<?php
include('../include/restricoes.php');
include('../../../conn.php');
include('../../../classes/formato_valor.php');
include('../../../classes/formato_data.php');
include('../../../classes/valor_extenso.php');



$projeto = $_POST['projeto_id'];

$obrigacoes_anexo_2_id  = $_POST['id_anexo_2'];



$qr_anexo_2 = mysql_query("SELECT * FROM obrigacoes_anexo_2 WHERE obrigacoes_anexo_2_id = '$obrigacoes_anexo_2_id' AND status = 1");
$row_anexo_2 = mysql_fetch_assoc($qr_anexo_2);

$nome_regiao = mysql_result(mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$row_anexo_2[id_regiao]'"),0);

$nome_projeto = mysql_result(mysql_query("SELECT nome FROM projeto WHERE id_projeto = '$row_anexo_2[id_projeto]'"),0);


$qr_parceiros = mysql_query("SELECT * FROM parceiros WHERE id_regiao = '$row_anexo_2[id_regiao]'");
$row_parceiro = mysql_fetch_assoc($qr_parceiros);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		
<style media="print">
body{
	font-size:10px;
	width:350px;
	
}
</style>
</head>

<body>	

	<div style="text-align:center;"><img src="../../adm_parceiros/logo/<?php echo $row_parceiro['parceiro_logo'];?>"  width="60" height="60" /></div>
<br />

<?php
list($projeto_ano, $projeto_mes, $projeto_dia) = explode('-',$row_anexo_2['inicio_projeto']);



switch($projeto_mes){
	case '01': $mes_hj = 'Janeiro';
	break;
	case '02': $mes_hj = 'Fevereiro';
	break;
	case '03': $mes_hj = 'Março';
	break;
	case '04': $mes_hj = 'Abril';
	break;
	case '05': $mes_hj = 'Maio';
	break;
	case '06': $mes_hj = 'Junho';
	break;
	case '07': $mes_hj = 'Julho';
	break;
	case '08': $mes_hj = 'Agosto';
	break;
	case '09': $mes_hj = 'Setembro';
	break;
	case '10': $mes_hj = 'Outubro';
	break;
	case '11': $mes_hj = 'Novembro';
	break;
	case '12': $mes_hj = 'Dezembro';
	break;
	
}


?>



<div style="text-align:right;"><?php echo $nome_regiao; ?> , <?php echo $projeto_dia.' de '.$mes_hj.' de '.$projeto_ano ?> </div>
<br />


    	A  <strong> <?php echo $row_anexo_2['prefeitura']; ?></strong> apresenta o extrato de relatório de execução física e financeira do termo de parceria assinado com a <strong>OSCIP -  <?php echo $row_anexo_2['nome_oscip']; ?></strong>, apresentado conforme o modelo do anexo II – Decreto n.º 3.100/99, e faz saber:
<strong>Extrato de Relatório de Execução Física e Financeira do Termo de Parceria</strong>
<br />

  
  
 
  Custo do projeto:
  <em> Valor global estimado de até  R$ <?php echo number_format(formato_real($row_anexo_2['cat_desp_previsto']),2,',','.').' ('.htmlentities(valorPorExtenso($row_anexo_2['cat_desp_previsto']),ENT_COMPAT,'UTF-8'),')'; ?></em>
   <br />
     
  Local de realiza&ccedil;&atilde;o do projeto: <?php echo $row_anexo_2['local_projeto'] ?>
 <br />
 
    Data de assinatura do TP: <?php echo implode('/', array_reverse(explode('-', $row_anexo_2['data_assinatura']))); ?>

    In&iacute;cio do projeto:
   <?php echo implode('/', array_reverse(explode('-', $row_anexo_2['inicio_projeto']))); ?>
    T&eacute;rmino:
    <?php		
	echo implode('/', array_reverse(explode('-', $row_anexo_2['termino_projeto'])));
	
	?>
<br />
    
  Objetivos do projeto: <em><?php echo $row_anexo_2['obj_projeto']; ?></em>
  <br />
  
   Resultados alcan&ccedil;ados:   Considerando o TERMO DE PARCERIA celebrado entre a  OSCIP e a Prefeitura Municipal de <?php echo $nome_regiao ?>  conclu&iacute;mos que o <?php echo $nome_projeto; ?> teve um desempenho satisfat&oacute;rio, contribuindo efetivamente para o &ecirc;xito desta parceria. Obedecendo aos crit&eacute;rios estipulados no programa de Trabalho cumprindo os requisitos necess&aacute;rios.
   <br />
   
  Custos de Implementa&ccedil;&atilde;o do Projeto    
      <table>      
        <tr>
          <td align="right">Categorias de despesas</td>
          <td>Previsto</td>
          <td>Realizado</td>
          <td>Diferen&ccedil;a</td>
        </tr>
        <tr>
          <td><em>Despesas Operacionais do projeto</em></td>
          <td  align="center"><em>R$ <?php echo formato_real($row_anexo_2['cat_desp_previsto']); ?></em></td>
          <td  align="center"><em> R$ <?php echo formato_real($row_anexo_2['cat_desp_realizado']); ?></em>
          </td>
          <td  align="center"> 
          <div class="diferenca">
            <?php  echo 'R$ '.formato_real($row_anexo_2['cat_desp_diferenca']);  ?>
          </div>
          </td>
        </tr>
        <tr>
          <td align="center">TOTAIS:</td>
          <td align="center"><em>R$ <?php echo formato_real($row_anexo_2['total_previsto']); ?></em></td>
          <td align="center">
         
        <em>  R$ <?php echo formato_real($row_anexo_2['total_realizado']); ?></em>
         
          
          </td>
          <td align="left">
           <div class="diferenca">
           <em>  <?php 
                    echo 'R$ '.formato_real($row_anexo_2['total_diferenca']);                                               
                  ?></em>
           </div>
          </td>
        </tr>
      </table>
      
      
      
    
  
       Nome da OSCIP: <?php echo $row_anexo_2['nome_oscip'] ;?>
                <br />
              Endere&ccedil;o:<?php echo $row_anexo_2['endereco_oscip']; ?>
			  
              Cidade:
              <?php echo $row_anexo_2['cidade_oscip'] ?>
              UF: <?php echo  $row_anexo_2['uf_oscip']  ?></td>
              CEP: <?php echo  $row_anexo_2['cep_oscip']  ?><br />
              
              Tel: <?php echo  $row_anexo_2['tel_oscip']  ?> Fax:<?php echo  $row_anexo_2['fax_oscip']  ?><br />
              
              E-mail: <?php echo  $row_anexo_2['email_oscip']  ?><br />
              
Nome do respons&aacute;vel pelo projeto: <?php echo  $row_anexo_2['nome_responsavel']  ?> Cargo / Fun&ccedil;&atilde;o: Presidente
              
</table>

</body>
</html>