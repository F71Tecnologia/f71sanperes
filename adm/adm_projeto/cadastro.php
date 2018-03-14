<?php 
include('../include/restricoes.php');
include "../../funcoes.php";
include "../include/criptografia.php";

if($_POST['pronto'] == "cadastro") {
	
	if(isset($_POST["dias_semana"])) {
	    $dias_semana = NULL;
	    foreach($_POST["dias_semana"] as $dia) {
             $dias_semana .= "$dia / ";
        }
	}

	$campo = array("Nome", "Área", "Quantidade de Aulas", "Dias da Semana");
	$valor = array($_POST['nome'], $_POST['area'], $_POST['qnt_aulas'], $_POST["dias_semana"]);
	for($a=0; $a<=3; $a++) {
		if(empty($valor[$a])) {
		  header("Location: cadastro.php?nulo=$campo[$a]&m=$_POST[link_master]");
		  exit;
		}
	}
	
	$Maior = mysql_query("SELECT MAX(aula_id) FROM aulas");
    $id = (mysql_result($Maior,0)+1);
	
	mysql_query("INSERT INTO aulas (aula_master, nome, descricao, area, carga_horaria, qnt_aulas, data_ini, data_fim, dias_semana, status, autor_criacao, data_criacao) VALUES ('$_POST[master]', '$_POST[nome]', '$_POST[descricao]', '$_POST[area]', '$_POST[carga_horaria]', '$_POST[qnt_aulas]', '$_POST[data_ini]', '$_POST[data_fim]', '$dias_semana', 'on', '$_COOKIE[logado]', NOW())") or die(mysql_error());
	header("Location: cursos.php?sucesso=curso&m=$_POST[link_master]&curso=$id");
	
}
?>
<html>
<head>
<title>Administração de Cursos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../css/estrutura.css" rel="stylesheet" type="text/css">
<script src="../../js/ramon.js" type="text/javascript"></script>
<script language="javascript" type="text/javascript">
function validaForm(){
d = document.cadastro;
if (d.nome.value == "" ){
alert("O campo Nome deve ser preenchido!");
d.nome.focus();
return false;
}
if (d.area.value == "" ){
alert("O campo Área deve ser preenchido!");
d.area.focus();
return false;
}
if (d.qnt_aulas.value == "" ){
alert("O campo Quantidade de Aulas deve ser preenchido!");
d.qnt_aulas.focus();
return false;
}
return true;   
}
</script>
</head>
<body>
   <div id="corpo">
        <div id="menu" class="parceiro">
            <?php include "include/menu.php"; ?>
        </div>
        <div id="conteudo">
            <h1><span>Cadastro de Curso</span></h1>
            <?php if($_GET['nulo']) { echo "O campo <b>$_GET[nulo]</b> não pode ficar em branco!"; } ?>
   <form name="cadastro" method="post" action="<?=$_SERVER['PHP_SELF']?>" onSubmit="return validaForm()">          
<table cellspacing="0" cellpadding="4" class="relacao">
  <tr>
    <td class="secao">Nome:</td>
    <td><input type="text" id="nome" name="nome" size="50"></td>
  </tr>
  <tr>
    <td class="secao">Descrição:</td>
    <td><textarea id="descricao" name="descricao" cols="38" rows="2"></textarea></td>
  </tr>
  <tr>
    <td class="secao">Área</td>
    <td><input type="text" id="area" name="area" size="50"></td>
  </tr>
  <tr>
    <td class="secao">Carga Horária:</td>
    <td class="descricao"><input type="text" id="carga_horaria" name="carga_horaria" size="2" onKeyUp="pula(4,this.id,qnt_aulas.id)" style="text-align:right;"> h</td>
  </tr>
  <tr>
    <td class="secao">Quantidade de Aulas:</td>
    <td class="descricao"><input type="text" id="qnt_aulas" name="qnt_aulas" size="2" onKeyUp="pula(4,this.id,data_ini.id)" style="text-align:right;"> aulas</td>
  </tr>
  <tr>
    <td class="secao">Data de Início:</td>
    <td class="descricao"><input type="text" id="data_ini" name="data_ini" size="8" onKeyPress="formatar('00/00/0000', this)" 
onkeyup="pula(10,this.id,data_fim.id)"> ex: 16/03/2010</td>
  </tr>
  <tr>
    <td class="secao">Data de Fim:</td>
    <td class="descricao"><input type="text" id="data_fim" name="data_fim" size="8" onKeyPress="formatar('00/00/0000', this)" 
onkeyup="pula(10,this.id,semana.id)"> ex: 24/11/2010</td>
  </tr>
  <tr>
    <td class="secao">Dias da Semana:</td>
    <td style="font-size:12px;"><input type="checkbox" id="semana" name="dias_semana[]" value="Segunda"> Seg &nbsp;&nbsp;<input type="checkbox" name="dias_semana[]" value="Terça"> Ter &nbsp;&nbsp;<input type="checkbox" name="dias_semana[]" value="Quarta"> Qua &nbsp;&nbsp;<input type="checkbox" name="dias_semana[]" value="Quinta"> Qui &nbsp;&nbsp;<input type="checkbox" name="dias_semana[]" value="Sexta"> Sex &nbsp;&nbsp;<input type="checkbox" name="dias_semana[]" value="Sábado"> Sab &nbsp;&nbsp;<input type="checkbox" name="dias_semana[]" value="Domingo"> Dom</td>
  </tr>
  <tr>
    <td colspan="2" align="center"> 
     <input name="master" value="<?=$Master?>" type="hidden" />
     <input name="link_master" value="<?=$_GET['m']?>" type="hidden" />
     <input name="pronto" value="cadastro" type="hidden" />
     <input value="Cadastrar" type="submit" class="botao" style="float:right;" />
    </td>
  </tr>
</table>
      </form>
        </div>
        <div id="rodape">
            <?php include "include/rodape.php"; ?>
        </div>
   </div>
</body>
</html>