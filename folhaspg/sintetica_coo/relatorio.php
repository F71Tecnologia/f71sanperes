<?php
// Incluindo Arquivos
require('../../conn.php');
include('../../classes/formato_valor.php');
include('../../classes/formato_data.php');
include('../../funcoes.php');
include('../../classes/valor_proporcional.php');

// Definindo Classe Cálculos
$Trab = new proporcional();

// Id do Participante e Id da Folha
list($autonomo,$folha) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));

// Consulta da Folha
$qr_folha    = mysql_query("SELECT * FROM folhas WHERE id_folha = '$folha' AND status = '2'");
$row_folha   = mysql_fetch_array($qr_folha);
$data_inicio = $row_folha['data_inicio'];
$data_fim    = $row_folha['data_fim'];
$ano         = $row_folha['ano'];
$mes         = $row_folha['mes'];
$mes_int     = (int)$mes;

// Redefinindo Variáveis de Décimo Terceiro
if($row_folha['terceiro'] != 1) {
	$decimo_terceiro = NULL;
} else {
	$decimo_terceiro = 1;
	$tipo_terceiro   = $row_folha['tipo_terceiro'];
}

// Consulta da Região
$qr_regiao = mysql_query("SELECT id_regiao FROM regioes WHERE id_regiao = '$row_folha[regiao]'");
$regiao    = mysql_result($qr_regiao, 0);

// Consulta do Projeto
$qr_projeto = mysql_query("SELECT id_projeto FROM projeto WHERE id_projeto = '$row_folha[projeto]'");
$projeto    = mysql_result($qr_projeto, 0);

// Consulta do Participante da Folha
$qr_participante  = mysql_query("SELECT * FROM folha_autonomo WHERE id_autonomo = '$autonomo' AND id_folha = '$folha' AND status = '2'");
$row_participante = mysql_fetch_array($qr_participante);

// Consulta do Participante
$qr_autonomo  = mysql_query("SELECT * FROM autonomo WHERE id_autonomo = '$autonomo'");
$row_autonomo = mysql_fetch_assoc($qr_autonomo);

// Consulta do Curso do Particpante
$qr_curso = mysql_query("SELECT nome FROM curso WHERE id_curso = '$row_autonomo[id_curso]'");
		  
// Calculando a Folha
include('calculos_folha.php');
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>:: Intranet :: <?=$row_participante['nome']?></title>
<link href="folha.css" rel="stylesheet" type="text/css">
</head>
<body style="background-color:#FFF; margin:0px;">
<div id="corpo">
   <table cellspacing="4" cellpadding="0" id="topo">
      <tr>
        <td>
        	<?='('.$row_participante['id_autonomo'].') '.$row_participante['nome']?>
        </td>
      </tr>
    </table>
     
    <table cellpadding="0" cellspacing="0" id="relatorio">
      <tr id="salario" style="border:0px;">
	    <td colspan="2">SAL&Aacute;RIO</td>
	    <td><a href="../../alter_bolsista.php?bol=<?=$row_participante['id_autonomo']?>&pro=<?=$projeto?>" target="_blank">Editar Cadastro <img src="seta_transparente.png"></a></td>
	  </tr>
      <tr class="linha_um">
        <td class="nome">SAL&Aacute;RIO CONTRATUAL</td>
        <td class="valor">R$ <?=formato_real($salario_limpo)?></td>
        <td class="descricao"><?=@mysql_result($qr_curso, 0)?></td>    
      </tr>
      <tr class="linha_dois">
        <td class="nome">VALOR/DIA</td>
        <td class="valor">R$ <?php echo formato_real($diaria); ?></td>
        <td class="descricao">R$ <?php echo formato_real($salario_limpo).' / 30 dias'; ?></td>
      </tr>
      <tr class="linha_um">
        <td class="nome">DIAS TRABALHADOS</td>
        <td class="valor"><?=$dias?> dias</td>
        <td class="descricao">
        <?php if($row_participante['data_entrada'] > $data_inicio and $row_participante['data_entrada'] < $data_fim) {
				  echo 'FOI CONTRATADO EM '.formato_brasileiro($row_participante['data_entrada']).'<div class="clear"></span>';
		      } if($row_participante['data_saida'] > $data_inicio and $row_participante['data_saida'] < $data_fim) {
				  echo 'FOI DEMITIDO EM '.formato_brasileiro($row_participante['data_saida']);
		      } ?>
        </td>
      </tr>
      <tr class="linha_dois">
        <td class="nome">SAL&Aacute;RIO</td>
        <td class="valor">R$ <?=formato_real($salario)?></td>
        <td class="descricao">R$ <?=formato_real($diaria)?> x <?=$dias?> dias</td>   
      </tr>
    </table>
</div>
</body>
</html>