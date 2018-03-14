<?php
if(empty($_COOKIE['logado'])) {
print "Efetue o Login<br><a href='../login.php'>Logar</a>";
exit;
}

include "../conn.php";
include "../classes/regiao.php";
include "../classes/projeto.php";

$ClasReg = new regiao();
$ClasPro = new projeto();

#SELECIONANDO O MASTAR PARA CARREGAR A IMAGEM
$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);

#RECEBENDO VARIAVEIS DO GET
$projeto 	= $_REQUEST['pro'];
$regiao 	= $_REQUEST['reg'];
$data_hoje 	= date('d/m/Y');

#CLASSE PEGANDO OS DADOS DO PROJETO
$ClasPro -> MostraProjeto($projeto);
$nome_pro = $ClasPro -> nome;

#CLASSE PEGANDO O NOME DA REGIAO
$ClasReg -> MostraRegiao($regiao);
$nome_regiao = $ClasReg -> regiao;


#SELECIONANDO AS LOCAÇÕES
$relocacao = mysql_query("SELECT * FROM unidade WHERE id_regiao = '$regiao' AND campo1 = '$projeto'");
$num_locacao = mysql_num_rows($relocacao);

?>
<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=iso-8859-1">
<title>Relat&oacute;rio de Atividades por Lota&ccedil;&atilde;o</title>
<link href="css/estrutura.css" rel="stylesheet" type="text/css">
<style media="screen">
    .linha{
        background-color:   #faf9f9;
        border-bottom: 1px solid  #ece8e8;
    }
    
</style>
<style  media="print" type="text/css">
    table.cabecalho{
        visibility:  hidden;
    }  
    
    
    table.funcoes {  border-collapse: collapse;    }
    table.funcoes tr{   border: 1px #000 solid;    }
    table.funcoes td{   border: 1px #000 solid;    }
    tr.secao{   border: 1px #000 solid;    }
    tr.linha{   border: 1px #000 solid;    }
    
    
</style>


</head>
<body style="background-color:#FFF; margin-top:30px; margin-bottom:30px;">
<table cellspacing="0" cellpadding="0" class="relacao" style="width:920px; border:0px; page-break-after:always;">
 <tr> 
    <td width="20%" align="center">
          <img src='../imagens/logomaster<?=$row_user['id_master']?>.gif' alt="" width='120' height='86' />
    </td>
    <td width="80%" align="center">
<strong>RELAT&Oacute;RIO DE ATIVIDADES POR LOTA&Ccedil;&Atilde;O</strong> DETALHADO<br>
         <?=$row_master['razao']?>
         <table width="474" border="0" align="center" cellpadding="4" cellspacing="1" style="font-size:12px;" class="cabecalho">
            <tr style="color:#FFF;">
              <td width="155" height="22" class="top">PROJETO</td>
              <td width="154" class="top">REGIÃO</td>
              <td width="137" class="top">LOTA&Ccedil;&Otilde;ES</td>
            </tr>
            <tr style="color:#333; background-color:#efefef;">
              <td height="20" align="center"><b><?=$nome_pro?></b></td>
              <td align="center"><b><?=$nome_regiao?></b></td>
              <td align="center"><b><?=$num_locacao?></b></td>
            </tr>
        </table>
    </td>
  </tr>
  <tr> 
    <td colspan="2">
    <?php
	
	if(!empty($num_locacao)) {
		$total_geral = '0';
		while($row_loc = mysql_fetch_array($relocacao)){
		
		$reatividade = mysql_query("SELECT * FROM curso WHERE id_regiao = '$regiao' and campo3 = '$projeto'");
		$numatividade = mysql_num_rows($reatividade);
	?>



  <div class="descricao"><b><?=$row_loc['unidade']?></b><br/><span style="font-size:10px; color:#265462;"><?=$row_loc['local']?></span></div>
 <table class="relacao" width="100%" cellpadding="3" cellspacing="1" class="funcoes">
   

<?php 

while($row_ativ = mysql_fetch_array($reatividade)){ 
$res_aut = mysql_query("SELECT nome,tipo_contratacao FROM autonomo WHERE id_regiao = '$regiao' and locacao = '$row_loc[unidade]' AND id_curso = '$row_ativ[0]' AND status = '1' AND tipo_contratacao != '2' ORDER BY nome");
$num_aut = mysql_num_rows($res_aut);

$res_clt = mysql_query("SELECT id_clt,nome,matricula, data_entrada FROM rh_clt WHERE id_regiao = '$regiao' and locacao = '$row_loc[unidade]' AND id_curso = '$row_ativ[0]' AND status < '60' ORDER BY nome");
$num_clt = mysql_num_rows($res_clt);

$style = ($num_clt+$num_aut == 0) ? "style='display:none'" : "";

$total_unidade += $num_aut + $num_clt;

$nometab = "tab".$row_ativ[0]."_".$row_loc[0];
?>
       <tr class="secao" <?=$style?> height="40">      
        <td colspan="2" valign="bottom"> Atividade</td> 
        <td valign="bottom">Salário</td> 
        <td valign="bottom">Quantidade</td>
      </tr>
      
      <tr  <?=$style?>>
        <td colspan="2" height="40"><?=$row_ativ['campo2']?></td>       
        <td><?php echo 'R$ '.number_format($row_ativ['salario'],2,',','.');?></td>
         <td width="13%"><?=$num_aut + $num_clt?></td>
        </tr>  
        
           <?php 
           /*
          while($row_aut = mysql_fetch_array($res_aut)){
			  
			  switch ($row_aut['tipo_contratacao']){
				  case 1:
				  $tp = "Autônomo";
				  break;
				  case 3:
				  $tp = "Colaborador";
				  break;
				  case 4:
				  $tp = "Autônomo/PJ";
				  break;
			  }  ?>
          <tr>
            <td width="99"><span style="color:#069"><?=$tp?></span></td>
            <td width="626"><span style="color:#069"><?=$row_aut['nome']?></span></td>
          </tr>
          <?php } */?>
          
          <?php
          while($row_clt = mysql_fetch_array($res_clt)){
              
              if($atv_anterior != $row_ativ['id_curso']){  ?>    
                    <tr>
                         <td>Matrícula</td>
                         <td>Nome</td>    
                         <td colspan="2">Data de Admissão</td>   
                        
                    </tr>          
               <?php   }  ?>
              
              
          <tr class="linha">
            <td><span style="color:#F90;"><?=$row_clt['matricula']?></span></td>           
            <td><span style="color:#F90;"><?=$row_clt['nome']?></span></td>
            <td colspan="2"><span style="color:#F90;"><?=implode('/',array_reverse(explode('-',$row_clt['data_entrada'])))?></span></td>
          </tr>
          <?php
		  $atv_anterior = $row_ativ['id_curso'];
	    }
		  ?>      
  
       <?php 
	   unset($num_clt);
	   unset($num_aut);
           
          }
	   
	   ?>

      <tr class="secao">
        <td colspan="2" align="center">TOTAL DE PROFISSIONAIS: <?=$total_unidade?></td>
      </tr>
  </table>
  <tr>
      <td coslspan="5"></td>
  </tr>

    <?php 
	
	$total_geral += $total_unidade;
	unset($total_unidade);
	} # END WHILE
	} # END IF
	
	?>
    <br />
    <table width="60%" border="0" cellspacing="0" cellpadding="0" align="center" class="relacao">
  <tr class="secao">
    <td align="center">Total Geral de Participantes: <?=$total_geral?></td>
  </tr>
</table>
    

    </td>
  </tr>
</table>
</body>
</html>
