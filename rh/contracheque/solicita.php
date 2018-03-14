<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='www.netsorrindo.com.br/intranet/login.php'>Logar</a>";
exit;
}
include "../../conn.php";
include "../../wfunction.php";
include "../../funcoes.php";
include "../../classes/projeto.php";
include "../../classes/regiao.php";
include "../../classes/clt.php";
include "../../classes/curso.php";

$Projeto = new projeto();
$ClassRegiao = new regiao();
$ClassCLT = new clt();
$ClassCurso = new tabcurso();

$usuario = carregaUsuario();

$id = (!isset($_REQUEST['id'])) ? 1 : $_REQUEST['id'];

$sql = "SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'";
$result_user = mysql_query($sql, $conn);
$row_user = mysql_fetch_array($result_user);

$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);

//RECEBENDO A VARIAVEL CRIPTOGRAFADA
$enc = $_REQUEST['enc'];
$enc = str_replace("--","+",$enc);
$link = decrypt($enc);
$decript = explode("&",$link);

$regiao = $usuario['id_regiao'];
$folha = $decript[1];
//RECEBENDO A VARIAVEL CRIPTOGRAFADA
$qr_regiao = mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$regiao'");
$nome_regiao = mysql_result($qr_regiao, 0);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>:: INTRANET :: Contracheques</title>
<meta http-equiv="Cache-Control" content="No-Cache">
<meta http-equiv="Pragma"        content="No-Cache">
<meta http-equiv="Expires"       content="0">

<link href="../../adm/css/estrutura.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../../js/jquery-1.3.2.js"></script>
<script src="../../js/abas_anos.js" type="text/javascript"></script>


</head>

<body>
<div id="corpo">
	<div id="conteudo">

<?php
if($id == 1){
?>
<div style="margin-left:810px;"><?php include("../../reportar_erro.php"); ?> </div>
    <div class="right"></div>
    <br />

   <img src="../../imagens/logomaster<?=$row_user['id_master']?>.gif" width="110" height="79">
    	<h3>RECIBOS DE PAGAMENTO</h3>
		<p>&nbsp;</p>
        <h3 style="text-transform:uppercase;"> <?php echo $nome_regiao;?></h3>

            <?php

			$RE = mysql_query("SELECT *, date_format(data_inicio, '%d/%m/%Y') as data_inicio,
                          date_format(data_fim, '%d/%m/%Y') as data_fim
			                   FROM rh_folha
                         WHERE regiao = '$regiao'
                         AND status = '3'
                         ORDER BY projeto, ano");

			$cont = 0;

      while ($Row = mysql_fetch_array($RE)):

				$Projeto -> MostraProjeto($Row['projeto']);
				$Pnome = $Projeto -> nome;


				if($projeto_anterior != $Row['projeto']) {

					echo '<h3 class="titulo_projeto">'.$Row['projeto'].' - '.$Pnome.'</h3>';
				}

        if($projeto_anterior != $Row['projeto'] || $ano_anterior != $Row['ano'] ) {

          $total_anos = mysql_num_rows(mysql_query("SELECT *,date_format(data_inicio, '%d/%m/%Y')as data_inicio,date_format(data_fim, '%d/%m/%Y')as data_fim FROM rh_folha where regiao = '$regiao' and status = '3' AND ano = '$Row[ano]' AND projeto = '$Row[projeto]' ORDER BY projeto,ano"));


          $display = ($Row['ano'] == date('Y'))? 'block': 'none';
          ?>
          <a href="javascript:;" class="titulo_ano"><?php echo $Row['ano'];?></a>

          <table width="100%" border="0" cellspacing="0" cellpadding="0" class="folhas" style="display:<?php echo $display;?>;">
            <tr class="secao_nova">
              <td>COD</td>
              <td>Nome</td>
              <td>Mês</td>
              <td>Período</td>
              <td>Nº de participantes</td>
              <td>Individual</td>
              <td>Todos</td>
              <td>ARQUIVO CSV</td>
            </tr>

            <?php
          }

				$mes = $ClassRegiao -> MostraMes($Row['mes']);

				//-- ENCRIPTOGRAFANDO A VARIAVEL
				$linkunico = encrypt("$regiao&$Row[0]");
				$linkunico = str_replace("+","--",$linkunico);
				//-- ---------------------------

				//-- ENCRIPTOGRAFANDO A VARIAVEL
				$linkvario = encrypt("$regiao&todos&$Row[id_folha]");
				$linkvario = str_replace("+","--",$linkvario);
				//-- ---------------------------
                                if($Row['terceiro'] == 1) {
                                        switch ($Row['tipo_terceiro']) {
                                            case 1:
                                            $exibicao = "<b>13º Primeira parcela</b>";
                                            break;
                                            case 2:
                                            $exibicao = "<b>13º Segunda parcela</b>";
                                            break;
                                            case 3:
                                            $exibicao = "<b>13º Integral</b>";
                                            break;
                                        }
                                    } else {
                                        $exibicao = "<b>$mes</b>";
                                    } 

				if($cont % 2){ $classcor="linha_um"; }else{ $classcor="linha_dois"; }

			?>
            <tr class="<?=$classcor?>" >
              <td height="30" align="left" width="50">&nbsp;&nbsp;<?=$Row['0']?></td>
              <td  align="center" width="200"><?=$Pnome?></td>
              <td  align="center"  width="150"><span style="color:#F00"><?=$exibicao?></span></td>
              <td  align="center"  width="200"><?=$Row['data_inicio']." at&eacute; ".$Row['data_fim']?></td>
              <td align="center"  width="150"><span style="color:#00F"><?=$Row['clts']." Participantes "?></span></td>
              <td align="center"  width="90">
              <a href="solicita.php?id=2&enc=<?=$linkunico?>" target="_blank">
              <img src="../imagensrh/user.gif" border="0" alt="Gerar para uma única pessoa" title="Gerar para uma única pessoa"></a></td>
              <td width="8%" align="center"  width="90">
              <a href="solicita.php?enc=<?=$linkvario?>&id=3">
              <img src="../imagensrh/group.gif" alt="Gerar de Todos" title="Gerar de Todos" border="0" /></a></td>
              <td width="8%" align="center"  width="90">
                <a href="geracontra_txt.php?enc=<?= $linkvario ?>&id=3">
                    <img src="../../imagens/icones/icon-docview.gif" alt="Gerar CSV" title="Gerar de Todos" border="0" />
                </a>
              </td>
              </tr>
            <?php
			$cont ++;

			$projeto_anterior = $Row['projeto'];
			$ano_anterior = $Row['ano'];

			if($cont == $total_anos){
        echo '<tr><td colspan="7">&nbsp;</td></tr>';
        echo '</table>';

        $cont = 0;
     }

     endwhile;
			?>
<?php

mysql_free_result($RE);

}elseif($id == 2){



?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center" valign="middle"><br />
      <table width="80%" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
        <tr class="fundo_azul">
          <td height="27"><div class="titulo_claro">Recibos de Pagamentos</div></td>
        </tr>
        <tr>
          <td width="20%" height="72" align="center"><br />
            <table width="90%" border="0" cellspacing="0" cellpadding="0" class="bordaescura1px">
              <tr>
                <td height="26" class="fundo_claro">cod</td>
                <td height="26" class="fundo_claro">NOME</td>
                <td height="26" class="fundo_claro">ATIVIDADE</td>
                <td height="26" class="fundo_claro">VALOR</td>
                <td height="26" class="fundo_claro">GERAR</td>
              </tr>
              <?php

			$RE = mysql_query("SELECT *	FROM rh_folha_proc WHERE id_folha = '$folha' and status = '3' ORDER BY nome");
			$cont = 0;
            while($Row = mysql_fetch_array($RE)){


				$ClassCLT -> MostraClt($Row['id_clt']);
                                
                                //PEGA A CURSO DO PERIODO
                                $sql_transf = "
                                SELECT id_curso_para, id_curso_de
                                FROM rh_transferencias 
                                WHERE id_clt = $Row[id_clt]
                                AND LAST_DAY(data_proc) >= LAST_DAY('$Row[ano]-$Row[mes]-01')
                                ORDER BY data_proc ASC LIMIT 1";
                                $sql_transf = mysql_fetch_assoc(mysql_query($sql_transf));
                                if(!empty($sql_transf['id_curso_de'])){
                                    $id_curso = $sql_transf['id_curso_de'];
                                }else{
                                    $id_curso = $ClassCLT -> id_curso;
                                }
                                
				//$id_curso = $ClassCLT -> id_curso;
				//$NomeCurso = $ClassCurso -> campo2;
                                $ClassCurso -> MostraCurso($id_curso);
                                $NomeCurso = $ClassCurso -> nome;


				//-- ENCRIPTOGRAFANDO A VARIAVEL
				$linkunico = encrypt("$regiao&$Row[id_clt]&$Row[id_folha]");
				$linkunico = str_replace("+","--",$linkunico);
				//-- ---------------------------

				if($cont % 2){ $classcor="corfundo_um"; }else{ $classcor="corfundo_dois"; }
			?>
              <tr class="novalinha <?=$classcor?>">
                <td width="7%" height="30" align="center"><?=$Row['cod']?></td>
                <td width="42%" align="left"><?=$Row['nome']?></td>
                <td width="33%" align="left"><?=$NomeCurso?></td>
                <td width="11%" align="center"><span style="color:#00F">
                  <?=" R$ ".$Row['salliquido']?>
                </span></td>
                <td width="7%" align="center">
                <a href="geracontra_4.php?enc=<?=$linkunico?>">
                <img src="../../imagens/pdf.gif" width="24" height="30" border="0" alt="Gerar PDF" title="Gerar PDF"/>
                </a>
                </td>
              </tr>
              <?php
			$cont ++;
			}
			?>
            </table></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
      </table>
<?php
//-- ENCRIPTOGRAFANDO A VARIAVEL
$linkvoltar = encrypt("$regiao&1");
$linkvoltar = str_replace("+","--",$linkvoltar);
//-- ---------------------------
?>

      <br />
      <span aling="rigth"><a href='solicita.php?id=1&enc=<?=$linkvoltar?>' class='link'><img src='../../imagens/voltar.gif' border="0" /></a>
      </span> <br /></td>
  </tr>
</table>
<br />


<?php
}elseif($id == 3){

// RECEBENDO VARIAVEIS
$enc1 = $_REQUEST['enc'];
$enc = str_replace("--","+",$enc1);
$link = decrypt($enc);

$decript = explode("&",$link);

$regiao 	= $decript[0];
$clt 		= $decript[1];
$id_folha 	= $decript[2];
// RECEBENDO VARIAVEIS

$RE = mysql_query("SELECT * FROM rh_folha where id_folha = '$id_folha' and status = '3' ");
$Row = mysql_fetch_array($RE);


//-- ENCRIPTOGRAFANDO A VARIAVEL
$vai = encrypt("$regiao&todos&$id_folha");
$vai = str_replace("+","--",$vai);
//-- ---------------------------
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center" valign="middle"><br />
      <table width="80%" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
        <tr class="fundo_azul">
          <td height="27"><div class="titulo_claro">Gerando</div></td>
        </tr>
        <tr>
          <td width="20%" height="72" align="center">

          <?php
		  $max    = 50;
		  $pedaco = ceil($Row['clts'] / $max);


		  echo '<table width="80%" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">';
		  echo '<tr class="novalinha">';

		  $a = 1;
		  $maxini = 0;
		  $maxfim = 0;


		  for($i=1 ; $i <= $pedaco ; $i ++){

			  $maxfim = $maxfim + $max;
			  if($i != 1){
				  $maxini = $maxini + $max;
			  }

			  if($pedaco == $i){
				  $maxfim = $Row['clts'];
			  }

			  echo "<td>
			  <a href='geracontra_4.php?enc=$vai&ini=$maxini' target='_blank'>
			  <img src='../../imagens/pdf.gif' width='24' height='30' border='0' alt='Gerar PDF' title='Gerar PDF'/>
			  <br /> Gerar de $maxini a $maxfim</a>
			  </td>";

			  if($a == 5){
				  echo '</tr><tr>';
				  $a = 1;
			  }

			  $a ++;
		  }

		  echo '</tr>';
		  echo '</table>';

          ?>

          <br /></td>
        </tr>
      </table>
      <?php
//-- ENCRIPTOGRAFANDO A VARIAVEL
$linkvoltar = encrypt("$regiao&1");
$linkvoltar = str_replace("+","--",$linkvoltar);
//-- ---------------------------

?>



      <br />
      <span aling="rigth"><a href='solicita.php?id=1&enc=<?=$linkvoltar?>' class='link'><img src='../../imagens/voltar.gif' border="0" /></a>
      </span> <br /></td>
  </tr>
</table>




<?php
}
?>


     </div>
</div></div>
</body>
</html>