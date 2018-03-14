<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "conn.php";

$id = $_REQUEST['id'];
$id_user = $_COOKIE['logado'];
$regiao = $_REQUEST['regiao'];

$data = date('d/m/Y');

$result_local = mysql_query("SELECT * FROM regioes where id_regiao = '$regiao'");
$row_local = mysql_fetch_array($result_local);

switch($id){
case 1:

$result = mysql_query("SELECT * FROM processo");
$row_cont = mysql_num_rows($result);

$row_cont = $row_cont + 1;
$numero = sprintf("%04s",$row_cont);

$num_processo = $numero."/ANO";

?>
<html>
<head>
<title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="net1.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
.style35 {
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-weight: bold;
}
.style36 {font-size: 14px}
.style38 {
	font-size: 16px;
	font-weight: bold;
	font-family: Geneva, Arial, Helvetica, sans-serif;
	color: #FFFFFF;
}
a:link {
	color: #006600;
}
a:visited {
	color: #006600;
}
a:hover {
	color: #006600;
}
a:active {
	color: #006600;
}
.style40 {font-family: Geneva, Arial, Helvetica, sans-serif}
.style50 {font-family: Geneva, Arial, Helvetica, sans-serif; font-size: 10; font-weight: bold; color: #FFFFFF; }
.style53 {font-family: Arial, Verdana, Helvetica, sans-serif}
.style55 {font-size: 10}
.style56 {
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
}
.style57 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	color: #FF0000;
	font-size: 14px;
}
.style59 {font-family: Arial, Helvetica, sans-serif}
-->
</style>
</head>

<body bgcolor="#FFFFFF">
<div align="center">
  <table width="750" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr>
      <td colspan="4"><img src="layout/topo.gif" alt="" width="750"></td>
    </tr>
    <tr>
      <td width="21" rowspan="5" background="layout/esquerdo.gif">&nbsp;</td>
      <td width="341">&nbsp;</td>
      <td width="362">&nbsp;</td>
      <td width="26" rowspan="5" background="layout/direito.gif">&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td height="78" colspan="2" background="imagens/fundo_cima.gif"><div align="center"><span class="style38"><br>
        CONTROLE DE PROCESSOS INTERNOS</span><br>
        <br>
      </div></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><div align="center"></div></td>
    </tr>
    <tr>
      <td colspan="2"><br>
          <table  height="114" width="96%" border="1" align="center" cellspacing="0" bordercolor="#333333">
            <tr>
              <td height="45" bgcolor="#003300"><div align="right" class="style35">
                  <div align="center" class="style27 style36">DADOS DO PROCESSO<br>
                      <strong>
                      <?=$row_local['regiao']?>
                      </strong>- Data de Recebimento <strong>
                      <?=$data?>
                    </strong></div>
              </div></td>
            </tr>
            <tr>
              <td><span class="style40">
                <label> </label>
                </span>
                  <label> </label>
                  <span class="style40"><strong>
                  <label></label>
                  </strong></span>
                  <form action="processos.php" method="post" name='form1' id="form1" onSubmit="return validaForm()">
                    <br>
                    <table width="100%" border="0" cellpadding="0" cellspacing="1">
                      <tr>
                        <td colspan="4" class="style19"><div align="center"><span class="style40"><strong>N&uacute;mero do Processo:</strong></span> <span class="style57">
                            <?=$num_processo?>
                        </span></div></td>
                      </tr>
                      <tr>
                        <td width="16%" class="style19"><div align="right"><span class="style40"><strong>Raz&atilde;o social <br>
                          da Contratada</strong>:</span> </div></td>
                        <td colspan="3"><strong> &nbsp;&nbsp;
                              <input name="razao" type="text" id="razao" onFocus="document.all.razao.style.background='#CCFFCC'" onBlur="document.all.razao.style.background='#FFFFFF'" size="60" style=" font-weight:bold;" onChange="this.value=this.value.toUpperCase()">
                        </strong></td>
                      </tr>
                      <tr>
                        <td valign="top" class="style19"><div align="right"><span class="style40"><strong>Ano:</strong></span></div></td>
                        <td width="13%"><strong>
                          &nbsp;&nbsp;
                          <input name="ano" type="text" id="ano" style=" font-weight:bold;" onFocus="document.all.ano.style.background='#CCFFCC'" onBlur="document.all.ano.style.background='#FFFFFF'" onChange="this.value=this.value.toUpperCase()" size="4" maxlength="4">
                        </strong></td>
                        <td width="10%"><div align="right"><span class="style40"><strong>Regi&atilde;o:</strong></span></div></td>
                        <td width="61%">&nbsp;&nbsp;&nbsp;
                          <select name='regiao' class='campotexto' id='regiao' 
                      onFocus="document.all.regiao.style.background='#CCFFCC'" 
                      onBlur="document.all.regiao.style.background='#FFFFFF'">
                            <?php
$result=mysql_query("SELECT * FROM regioes");
while ($row = mysql_fetch_array($result)){

$row_regiao = "$row[id_regiao]";

print "<option value=$row[id_regiao]>$row[regiao] - $row[sigla]</option>";


}
?>
                        </select></td></tr>
                      
                      <tr>
                        <td valign="top" class="style19"><div align="right"><span class="style40"><strong>Assunto:</strong></span></div></td>
                        <td colspan="3">&nbsp;&nbsp;
                            <textarea name="assunto" cols="60" rows="5" id="assunto" 
                            onFocus="document.all.assunto.style.background='#CCFFCC'" 
                            onBlur="document.all.assunto.style.background='#FFFFFF'" 
                            style="font-weight:bold; background='#FFFFFF'" 
                            onChange="this.value=this.value.toUpperCase()"></textarea></td>
                      </tr>
                      <tr>
                        <td valign="top" class="style19">&nbsp;</td>
                        <td colspan="3">&nbsp;</td>
                      </tr>
                    </table>
                    <br>
                    <div align="center">
                      <table width="100%" border="0" cellspacing="0" cellpadding="0" style="display:none" id="tablearquivo">
                        <tr>
                          <td width="15%" align="right"><span class="style19">SELECIONE:</span></td>
                          <td width="85%"><span class="style19"> &nbsp;&nbsp;
                                <input name="arquivo" type="file" id="arquivo" size="60" />
                          </span></td>
                        </tr>
                      </table>
                      <br>
                      <input type="hidden" value="2" name="id">
                      <input type="hidden" value="<?=$regiao?>" name="regiao_atual">
                      <input type="submit" name="cadastrar" id="cadastrar" value="CADASTRAR PROCESSO">
                      
                      <script language="javascript">
function validaForm(){
           d = document.form1;

           if (d.razao.value == ""){
                     alert("O campo Razão social da Contratada deve ser preenchido!");
                     d.razao.focus();
                     return false;
          }

           if (d.assunto.value == ""){
                     alert("O campo Assunto deve ser preenchido!");
                     d.assunto.focus();
                     return false;
          }
		  
           
		return true;   }
</script>
                      <br>
                      <br>
                      <br>
                    </div>
                  </form></td>
            </tr>
          </table>
      <br>
          <hr color="#003300">
          <br>
          <table  height="63" width="96%" align="center" cellspacing="0" class='tarefa'>
            <tr>
              <td height="30" bgcolor="#003300"><div align="right" class="style35">
                  <div align="center" class="style27 style36">ACOMPANHAMENTO DE PROCESSOS CADASTRADOS</div>
              </div></td>
            </tr>
            <tr>
              <td height="31"><span class="style40">
                <label> </label>
                </span>
                  <label> </label>
                  <span class="style40"><strong>
                  <label></label>
                  </strong></span><?php

$result_proc1 = mysql_query("SELECT *,date_format(data_cad, '%d/%m/%Y')as data_cad ,date_format(data_fechamento, '%d/%m/%Y')as data_fechamento FROM processo ORDER BY id_processo");

print "
<table width='100%' border='0' cellpadding='0' cellspacing='1'>
<tr bgcolor='#003300'>
<td width='12%' class='style19'><div align='right' class='style50 style53'>
<div align='center'><span class='style55'>

PROCESSO N</span></div>

</div></td>
<td width='17%'><div align='center' class='style53 style27'><strong>

RAZ&Atilde;O</strong>

</div></td>
<td width='12%'><div align='center' class='style53 style27'><strong>

ABERTO EM
</strong>

</div></td>

<td width='10%'><div align='center' class='style53 style27'><strong>

REGIÃO
</strong>

</div></td>


<td width='12%'><div align='center' class='style53 style27'><strong><span class='style55'>

ABERTO POR
</span></strong>
</div></td>
<td width='20%' bgcolor='#003300'>
<div align='center' class='style53 style27'><strong>
ASSUNTO

</strong></div>
</td>
<td width='19%'><div align='center'><span class='style27 style56'>

ENCERRADO / DATA

</span></div></td>
</tr>";

$cont = "0";

while($row_proc1 = mysql_fetch_array($result_proc1)){

$result_fun = mysql_query("SELECT nome1 FROM funcionario where id_funcionario = '$row_proc1[id_user_cad]'");
$row_fun = mysql_fetch_array($result_fun);

$result_reg = mysql_query("SELECT regiao FROM regioes where id_regiao = '$row_proc1[id_regiao]'");
$row_reg = mysql_fetch_array($result_reg);

if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
if($row_proc1['status'] == "1"){
$mensagem = "<a href='processos.php?id=3&processo=$row_proc1[0]&regiao=$regiao'>Fechar Processo</a>";}
else
{$mensagem = "$row_proc1[data_fechamento]";};

$assunto = nl2br($row_proc1['assunto']);

print "
<tr bgcolor='$color'> 
  <td class='border2' align='center'>$row_proc1[num_processo]</td>	
  <td class='border2' align='center'>$row_proc1[razao]</td>
  <td class='border2' align='center'>$row_proc1[data_cad]</td>
  <td class='border2' align='center'>$row_reg[0]</td>
  <td class='border2' align='center'>$row_fun[0]</td>
  <td class='border2' align='center'>$assunto&nbsp;</td>
  <td class='border3' align='center'>$mensagem</td>
</tr>";

$cont ++;
}
print "</table>";
?>
        </table>        </td>
    </tr>
    
    <tr>
      <td colspan="4"><img src="layout/baixo.gif" alt="" width="750" height="38"></td>
    </tr>
  </table>
  </td>
  </tr>
  <br>
<?php
include "empresa.php";
$rod = new empresa();
$rod -> rodape();
?>
  </table>
</div>
</body>
</html>

<?php

break;

case 2: // AKI VAI RODAR O CADASTRO
		  
		  
$regiao = $_REQUEST['regiao'];
$numero = $_REQUEST['numero'];
$ano = $_REQUEST['ano'];
$razao = $_REQUEST['razao'];
$assunto = $_REQUEST['assunto'];
$regiao_atual = $_REQUEST['regiao_atual'];

$id_user = $_COOKIE['logado'];
$data_cad = date('Y-m-d');

//------RESOLVENDO O NUMERO DO PROCESSO NOVAMENTE------

$result = mysql_query("SELECT * FROM processo");
$row_cont = mysql_num_rows($result);

$row_cont = $row_cont + 1;
$numero = sprintf("%04s",$row_cont);

$num_processo = $numero."/".$ano;

//------RESOLVENDO O NUMERO DO PROCESSO NOVAMENTE------


mysql_query("INSERT INTO processo (id_regiao ,id_user_cad ,data_cad ,ano, num_processo ,razao ,assunto) VALUES 
('$regiao', '$id_user', '$data_cad', '$ano', '$num_processo', '$razao', '$assunto')")or die ("<hr>Erro no insert<br><hr>".mysql_error());


print "
<script>
alert (\"Informações gravadas com sucesso\");
location.href=\"processos.php?regiao=$regiao_atual&id=1\"
</script>
";


break;
case 3:

$regiao = $_REQUEST['regiao'];
$id_processo = $_REQUEST['processo'];
$data = date('Y-m-d');

mysql_query("UPDATE processo SET status = '2', data_fechamento='$data' WHERE id_processo = '$id_processo'");

print "
<script>
alert (\"Informações gravadas com sucesso\");
location.href=\"processos.php?regiao=$regiao&id=1\"
</script>
";

break;
}
/* Liberando o resultado */
//mysql_free_result($result);

/* Fechando a conexão */
//mysql_close($conn);

}

?>
