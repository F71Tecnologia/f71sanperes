<?php
if(empty($_COOKIE['logado'])) {
	print 'Efetue o Login<br><a href="login.php">Logar</a>';
	exit();
}

include('../conn.php');
include('../wfunction.php');

$usuario = carregaUsuario();

$projeto = $_REQUEST['pro'];
$regiao  = (!empty($_REQUEST['regiao']))?$_REQUEST['regiao']:$usuario['id_regiao'];

//$sql = "select t.*, c.campo2 from terceirizado t inner join curso c on t.id_curso = c.id_curso";
$sql = "select t.*, c.campo2, p.c_fantasia from terceirizado t inner join curso c on (t.id_curso = c.id_curso) left join prestadorservico as p on (t.id_prestador=p.id_prestador)";
//echo '<!-- '.$sql.' -->';
$result = mysql_query($sql);

?>
<html>
<head>
<title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../net1.css" rel="stylesheet" type="text/css" />
<link href="../rh/css/estrutura_projeto.css" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="../favicon.ico">
<script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
<script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
<script src="../js/global.js" type="text/javascript"></script>
</head>
<body>
<div id="corpo" style="width:95%;">
<div id="conteudo">
<table align="center" width="100%" cellspacing="0" cellpadding="12" style="font-size:13px; line-height:22px;">
<tr>
  	<td align="right"><?php include('reportar_erro.php');?></td>
  </tr>
  <tr>
    <td>
      <div style="border-bottom:2px solid #F3F3F3; margin-top:10px;">
           <h2 style="float:left; font-size:18px;">TODOS OS TERCEIRIZADOS</h2>
           <p style="float:right;"><a href="ver.php?projeto=<?=$projeto?>&regiao=<?=$regiao?>">&laquo; Voltar</a></p>
           <div class="clear"></div>
      </div>
    </td>
  </tr>
  <tr>
    <td>
 
<?php if($_GET['sucesso'] == "edicao"): ?>
    <div style="background-color:#696; border:1px solid #033; color:#FFF; padding:4px; font-size:13px; margin-top:10px;">
      Participante atualizado com sucesso!
    </div>
<?php endif; ?>
<p id="excel" style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Relatório')" value="Exportar para Excel" class="exportarExcel"></p>
<table id="tbRelatorio" cellpadding="8" cellspacing="0" style="border:0px; background-color:#f5f5f5; margin:0px auto; margin-top:1px; width:100%;">
    <tr>
	<td class="show" colspan="9">
	    <span style="color:#F90; font-size:32px;">></span>
	    TERCEIRIZADOS
	</td>
    </tr>
   <tr class="novo_tr">
       <td width="5%">COD</td>
       <td width="30%">NOME</td>
       <td width="20%">CARGO</td>
       <td  width="10%" align="center">CPF</td>
       <td  width="10%" align="center">Prestador</td>
   </tr>
   
   <?php while ($row = mysql_fetch_array($result)): ?>

    <tr class="linha_<?php echo ($alternateColor++%2==0)?"um":"dois"; ?>" style="font-size:12px;">
        <td><?=$row['id_terceirizado']?></td>
        <td><a class="participante" href="alterterceiro.php?reg=<?=$regiao?>&pro=<?=$projeto?>&id=<?=$row['id_terceirizado']?>"><?=$row['nome']?></a></td>
        <td><?=$row['campo2']?></td>
        <td align="center"><?=$row['cpf']?></td>
        <td align="center"><?=$row['c_fantasia']?></td>
	</tr>
    
    <?php endwhile; ?>

</table>

<div style="width:95%; margin:0px auto; font-size:13px; padding-bottom:4px; margin-top:15px; text-align:right;">
    <a href="#corpo" title="Subir navegação">Subir ao topo</a>
</div>
</td>
</tr>
</table>
</div>
</div>
</body>
</html>