<?php

if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='../login.php'>Logar</a> ";
}else{

include('../conn.php');

$id = $_REQUEST['id'];

switch($id){

case 1:

$id_user = $_COOKIE['logado'];
$regiao = $_REQUEST['regiao'];
$mes = date('m');

$meses = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');

$query_master      = mysql_query("SELECT master.id_master, master.razao FROM regioes 
									INNER JOIN master 
									ON regioes.id_master = master.id_master
									WHERE regioes.id_regiao = '$regiao'") or die (mysql_error());
									
$row_master		   = mysql_fetch_assoc($query_master);

?>
<html>
<head>
        <title>:: Intranet :: Prestador de Serviço</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="prestador.css" rel="stylesheet" type="text/css" />
        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script src="../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript" ></script>
        <script src="../js/global.js" type="text/javascript"></script>
        
        
        <script>
            $(function() {
                $(".data").mask("99/99/9999");
            });
        </script>
        <style>
            .data{width: 80px;}
            .colEsq{
                float: left;
                width: 55%;
                margin-top: -10px;
            }
            fieldset{
                margin-top: 10px;
            }
            fieldset legend{
                font-family: 'Exo 2', sans-serif;
                font-size: 16px!important;
                font-weight: bold;
            }
            .first{
                vertical-align: 0!important;
            }
            .first-2{
                vertical-align: 0!important;
            }
        </style>
    </head>
    <body class="novaintra">
        <div id="content" style="width: 850px;">
            
                <div id="head">
                    <img src="../imagens/logomaster<?php echo $row_master['id_master'] ?>.gif" class="fleft" style="margin-right: 25px;">
                    <div class="fleft">
                        <h2>Sindicatos</h2>
                    </div>
                </div>
                
                <fieldset>
                    <legend>Sindicatos Cadastrados</legend>
                    <table cellpadding="0" cellspacing="0" border="0" class="grid" style="width: 98%; margin: 10px;">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Mês de desconto</th>
                                <th>Mês de dissídio</th>
                                <th>Telefone</th>
                                <th>Contato</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                        $result = mysql_query("SELECT * FROM rhsindicato WHERE id_regiao = '$regiao' AND status = '1'");
                        while($row = mysql_fetch_array($result)){
                            $mes_desconto = $meses[$row['mes_desconto']];
                            $mes_dissidio = $meses[$row['mes_dissidio']];
                            if ($linha++ % 2 == 0) {
                                print '<tr class="odd">';
                            } else {
                                print '<tr class="even">';
                            }
                            print "
                            <td><a title='{$row['nome']}' href='../rh/rh_sindicatos.php?acao=2&regiao=$regiao&sindicato=$row[0]'>{$row['nome']}</a></td>
                            <td>$mes_desconto</td>
                            <td>$mes_dissidio</td>
                            <td>$row[tel]</td>
                            <td>$row[contato]</td>
                            </tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </fieldset>
            
                <form action="rh_sindicatos_form.php?acao=1&regiao=<?php echo $regiao; ?>" method="post" name="form1" onSubmit="return validaForm()">
                    <p class="controls"> 
                        <input type="submit" name="submit" id="submit" value="Cadastrar">
                    </p>
                </form>
            
            <script language="javascript">
            function validaForm(){
                       d = document.form1;

                       if (d.nome.value == ""){
                                 alert("O campo Nome deve ser preenchido!");
                                 d.nome.focus();
                                 return false;
                      }

                       if (d.endereco.value == ""){
                                 alert("O campo Endereco deve ser preenchido!");
                                 d.endereco.focus();
                                 return false;
                      }

                       if (d.cnpj.value == ""){
                                 alert("O campo CNPJ deve ser preenchido!");
                                 d.cnpj.focus();
                                 return false;
                      }

                       if (d.contato.value == ""){
                                 alert("O campo Contato deve ser preenchido!");
                                 d.contato.focus();
                                 return false;
                      }


                            return true;   }
            </script>
        </div>
    </body>
</html>
<?php

break;

case 2:  //CADASTRANDO OS DADOS

$regiao = $_REQUEST['regiao'];
$id_user_cad = $_COOKIE['logado'];
$data_cad = date('Y-m-d');

$nome = $_REQUEST['nome'];
$endereco = $_REQUEST['endereco'];
$cnpj = $_REQUEST['cnpj'];
$tel = $_REQUEST['tel'];
$fax = $_REQUEST['fax'];
$contato = $_REQUEST['contato'];
$cel = $_REQUEST['cel'];
$email = $_REQUEST['email'];
$site = $_REQUEST['site'];
$mes_desconto = $_REQUEST['mes_desconto'];
$mes_dissidio = $_REQUEST['mes_dissidio'];
$piso = $_REQUEST['piso'];
$multa = $_REQUEST['multa'];
$ferias = $_REQUEST['ferias'];
$fracao = $_REQUEST['fracao'];
$decimo_terceiro = $_REQUEST['decimo_terceiro'];
$recisao = $_REQUEST['recisao'];
$pratonal = $_REQUEST['pratonal'];
$evento = $_REQUEST['evento'];
$entidade = $_REQUEST['entidade'];

mysql_query("INSERT INTO rhsindicato(id_regiao ,id_user_cad ,data_cad ,nome ,endereco ,cnpj ,tel ,fax ,contato ,cel ,email ,site ,mes_desconto ,mes_dissidio ,piso ,multa ,ferias ,fracao ,decimo_terceiro ,recisao ,pratonal ,evento ,entidade ) VALUES ('$regiao', '$id_user_cad', '$data_cad', '$nome', '$endereco', '$cnpj', '$tel', '$fax', '$contato', '$cel', '$email', '$site', '$mes_desconto', '$mes_dissidio', '$piso', '$multa', '$ferias', '$fracao', '$decimo_terceiro', '$recisao', '$pratonal', '$evento', '$entidade')") or die ("ERRO<BR>".mysql_error());


print "
<script>
alert (\"Sindicato cadastrado!\"); 
location.href=\"../rh/rh_sindicatos.php?id=1&regiao=$regiao\"
</script>";


break;
case 3:  //MOSTRANDO OS DADOS

$regiao = $_REQUEST['regiao'];
$sindicato = $_REQUEST['sindicato'];

$result = mysql_query("SELECT * FROM rhsindicato WHERE id_sindicato = '$sindicato'");
$row = mysql_fetch_array($result);

print "<html>
<head>
<title>:: Intranet ::</title>
<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
<link href='../net2.css' rel='stylesheet' type='text/css'>
<style type='text/css'>
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
.style34 {
	font-size: 12px;
	font-weight: bold;
	color: #FFFFFF;
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
.style41 {
	font-family: Geneva, Arial, Helvetica, sans-serif;
	color: #FFFFFF;
	font-weight: bold;
}
-->
</style>
</head>
<body bgcolor='#FFFFFF'>";

print "
<table width='750' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' align='center'>
  <tr>
    <td colspan='4'><img src='../layout/topo.gif' width='750' height='38' /></td>
  </tr>
  
  
  <tr>
    <td width='21' rowspan='2' background='../layout/esquerdo.gif'>&nbsp;</td>
    <td colspan='2'><br />
      <table  height='450' width='95%' border='1' align='center' cellspacing='0' bordercolor='#CCFF99'>
            <tr>
              <td height='45' colspan='6' bgcolor='#CCFF99'><div align='right' class='style35'>
                  <div align='center' class='style27 style36'><img src='imagensrh/sindicatos.gif' alt='empresa' width='100' height='40' /></div>
              </div></td>
            </tr>
            <tr>
              <td width='20%'><div align='right' class='style40 style35'><strong>Nome:</strong></div></td>
              <td width='80%' colspan='5'>&nbsp;&nbsp;<span class='style40'>$row[nome]              </span></td>
        </tr>
            <tr>
              <td><div align='right' class='style35'>Endere&ccedil;o:</div></td>
              <td colspan='5'>&nbsp;&nbsp;<span class='style40'>$row[endereco]</span></td>
        </tr>
            <tr>
              <td colspan='6'><span class='style35'>CNPJ:&nbsp;&nbsp;</span><span class='style40'>$row[cnpj]</span><span class='style35'> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tel.:&nbsp;&nbsp;</span><span class='style40'>$row[tel] </span><span class='style35'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fax:&nbsp;&nbsp;</span><span class='style40'>$row[fax]</span> </td>
        </tr>
            <tr>
              <td colspan='6'><span class='style35'>Contato: </span><span class='style40'>&nbsp;$row[contato] </span><span class='style35'>&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Cel: <span class='style40'>&nbsp;</span></span><span class='style40'>$row[cel] </span> </td>
        </tr>
            <tr>
              <td colspan='6'><span class='style35 style40'>E-mail:&nbsp;&nbsp;</span><span class='style40'><span class='style40'>$row[email] </span></span><span class='style35 style40'> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Site:
              </span><span class='style40'><span class='style40'>$row[site] </span></span> </td>
        </tr>
            <tr>
              <td colspan='6' bgcolor='#CCFF99'><div align='center'><span class='style35'>DADOS DA CATEGORIA</span></div></td>
            </tr>
            <tr>
              <td colspan='6'><span class='style35'>M&ecirc;s de Desconto:&nbsp;              </span><span class='style40'><span class='style40'>$row[mes_desconto] </span></span><span class='style35'> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;M&ecirc;s de Diss&iacute;dio:&nbsp;&nbsp;</span><span class='style40'><span class='style40'>$row[mes_dissidio] </span></span><span class='style35'>&nbsp;&nbsp;&nbsp;</span></td>
        </tr>
            <tr>
              <td height='26' colspan='6'><p><span class='style35'>Piso Salarial:&nbsp;</span><span class='style40'>&nbsp;R$ $row[piso] </span>                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class='style35'>Multa do FGTS:</span>
                  <span class='style40'>$row[multa] </span>              % </p></td>
            </tr>
            <tr>
              <td colspan='6' bgcolor='#FFFFFF'><span class='style35'>F&eacute;rias (meses)</span>:
                &nbsp;<span class='style40'>$row[ferias] </span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class='style35'>Fra&ccedil;&atilde;o</span>: <span class='style40'>$row[fracao] </span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class='style35'>13  (meses):</span>                <span class='style40'>$row[decimo_terceiro] </span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class='style35'>Rescis&atilde;o  (meses):</span><span class='style40'>&nbsp;$row[recisao] </span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class='style35'>Patronal: </span><span class='style40'>$row[pratonal] </span> </td>
        </tr>
            <tr>
              <td colspan='6' bgcolor='#FFFFFF'><span class='style35'>Evento Relacionado:<span class='style40'>&nbsp;&nbsp;</span></span><span class='style40'><span class='style40'>$row[evento]</span></span><span class='style35'> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Entidade Sindical:</span><span class='style40'>&nbsp;<span class='style40'>$row[entidade] </span> </span></td>
        </tr>
      </table>
        <br />
		<br />
		<center>
		<a href='javascript:history.go(-1)' class='link'><img src='../imagens/voltar.gif' border=0></a>
		</center>
		<br />
		<br />
		</td>
    <td width='26' rowspan='2' background='../layout/direito.gif'>&nbsp;</td>
  </tr>
  <tr>
    <td width='155'>&nbsp;</td>
    <td width='549'>&nbsp;</td>
  </tr>
  <tr valign='top'>
    <td height='37' colspan='4' bgcolor='#5C7E59'><img src='../layout/baixo.gif' width='750' height='38' />
        <div align='center' class='style6'><strong>Intranet do Instituto Sorrindo Para a Vida</strong> - Acesso Restrito 
          a Funcion&aacute;rios <br />
      </div></td>
  </tr>
</table>
</body></html>
";


break;
}
}
/* Liberando o resultado */
//mysql_free_result($result);

/* Fechando a conexão */
//mysql_close($conn);
?>
