<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a> ";
    exit();
}

include "../conn.php";
include "../includes.php";
include "../wfunction.php";

$usuario = carregaUsuario();

$id = (isset($_REQUEST['id'])) ? $_REQUEST['id'] : 1;

switch ($id) {
    case 1:

        $id_user = $_COOKIE['logado'];
        $regiao = $usuario['id_regiao'];
        ?>
        <html>
            <head>
                <title>:: Intranet ::</title>
                <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
                <link href="../net1.css" rel="stylesheet" type="text/css">
                <script language="JavaScript" type="text/JavaScript">
                    <!--
                    function formatar(mascara, documento){ 
                    var i = documento.value.length; 
                    var saida = mascara.substring(0,1); 
                    var texto = mascara.substring(i) 

                    if (texto.substring(0,1) != saida){ 
                    documento.value += texto.substring(0,1); 
                    } 

                    } 


                    function pula(maxlength, id, proximo){ 
                    if(document.getElementById(id).value.length >= maxlength){ 
                    document.getElementById(proximo).focus();
                    }
                    } 

                    function mascara_data(d){  
                    var mydata = '';  
                    data = d.value;  
                    mydata = mydata + data;  
                    if (mydata.length == 2){  
                    mydata = mydata + '/';  
                    d.value = mydata;  
                    }  
                    if (mydata.length == 5){  
                    mydata = mydata + '/';  
                    d.value = mydata;  
                    }  
                    if (mydata.length == 10){  
                    verifica_data(d);  
                    }  
                    } 

                    function verifica_data (d) {  

                    dia = (d.value.substring(0,2));  
                    mes = (d.value.substring(3,5));  
                    ano = (d.value.substring(6,10));  


                    situacao = "";  
                    // verifica o dia valido para cada mes  
                    if ((dia < 01)||(dia < 01 || dia > 30) && (  mes == 04 || mes == 06 || mes == 09 || mes == 11 ) || dia > 31) {  
                    situacao = "falsa";  
                    }  

                    // verifica se o mes e valido  
                    if (mes < 01 || mes > 12 ) {  
                    situacao = "falsa";  
                    }  

                    // verifica se e ano bissexto  
                    if (mes == 2 && ( dia < 01 || dia > 29 || ( dia > 28 && (parseInt(ano / 4) != ano / 4)))) {  
                    situacao = "falsa";  
                    }  

                    if (d.value == "") {  
                    situacao = "falsa";  
                    }  

                    if (situacao == "falsa") {  
                    alert("Data digitada é inválida, digite novamente!"); 
                    d.value = "";  
                    d.focus();  
                    }  

                    }

                    function validaForm(){
                    d = document.form1;

                    if (d.nome.value == ""){
                    alert("O campo Nome deve ser preenchido!");
                    d.nome.focus();
                    return false;
                    }

                    if (d.data.value == ""){
                    alert("O campo Data deve ser preenchido!");
                    d.data.focus();
                    return false;
                    }


                    return true;   
                    }

                    //-->
                </script>

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
                    .style41 {
                        font-family: Geneva, Arial, Helvetica, sans-serif;
                        color: #FFFFFF;
                        font-weight: bold;
                    }
                    .style42 {font-weight: bold}
                    .style43 {font-family: Arial, Helvetica, sans-serif}
                    -->
                </style>
            </head>

            <body bgcolor="#FFFFFF">
                <table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td align="center" valign="top"> 
                            <table width="750" border="0" cellpadding="0" cellspacing="0">
                                <tr> 
                                    <td colspan="4"><img src="../layout/topo.gif" width="750" height="38"></td>
                                </tr>

                                <tr>
                                    <td width="21" rowspan="4" background="../layout/esquerdo.gif">&nbsp;</td>
                                    <td bgcolor="#FFFFFF">&nbsp;</td>
                                    <td bgcolor="#FFFFFF">&nbsp;</td>
                                    <td width="26" rowspan="4" background="../layout/direito.gif">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="2" bgcolor="#FFFFFF"><br>
                                        <table width="97%" align="center" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td height="32"  colspan="6"  align="right" bgcolor="#FFF"> <?php include('../reportar_erro.php'); ?></td>
                                            </tr>
                                            <tr>
                                                <td height="20" colspan="6" bgcolor="#666666"><div align="center" class="style41">FERIADOS CADASTRADOS</div></td>
                                            </tr>
                                            <tr class="novo_tr">
                                                <td width="14%" height="20">DATA</td>
                                                <td width="40%">NOME</td>
                                                <td width="14%">TIPO</td>
                                                <td width="14%">MOVEL</td>
                                                <td width="19%">REGI&Atilde;O</td>
                                                <td width="14%">EXCLUIR</td>
                                            </tr>
                                            <?php
                                            $ANO = date('Y');
                                            ///AND YEAR(data)='$ANO'
                                            $result = mysql_query("SELECT *,date_format(data, '%d/%m')as data,date_format(data, '%Y')as anodata FROM rhferiados WHERE status='1' ORDER BY year(data),month(data),day(data) ASC");

                                            $cont = 0;
                                            while ($row = mysql_fetch_array($result)) {

                                                if ($row['id_regiao'] != "0") {
                                                    $result_regi = mysql_query("SELECT regiao FROM regioes where id_regiao = $row[id_regiao]");
                                                    $row_regi = mysql_fetch_array($result_regi);
                                                    $regiao_f = $row_regi['0'];
                                                } else {
                                                    $regiao_f = "Federal";
                                                }

                                                if ($row['status'] == "1") {
                                                    $deletar = "<a href=../rh/rh_feriados.php?id=3&feriado=$row[0]&regiao_atual=$regiao>Excluir</a>";
                                                } else {
                                                    $deletar = "-";
                                                }

                                                if ($row['movel'] == "1" and $row['anodata'] != $ANO) {
                                                    $status = "style='display:none'";
                                                } else {
                                                    $status = "";
                                                }

                                                $data_F = $row['data'] . "/" . $ANO;
                                                if ($row['movel'] == 0) {
                                                    $movel = 'Não';
                                                } else {
                                                    $movel = 'Sim';
                                                }

                                                if ($cont % 2) {
                                                    $classcor = "corfundo_um";
                                                } else {
                                                    $classcor = "corfundo_dois";
                                                }

                                                print "
                                                <tr $status class='novalinha $classcor'>
                                                <td align='center'>$data_F</td>
                                                <td >$row[nome]</td>
                                                <td align='center'>$row[tipo]</td>
                                                <td align='center'>$movel</td>
                                                <td align='center'>$regiao_f </td>
                                                <td align='center'>$deletar</td>
                                                </tr>
                                                ";

                                                $cont ++;
                                            }
                                            ?>
                                        </table>
                                        <br>
                                        <br>
                                        <form name="form1" action="rh_feriados.php" method="post" onSubmit="return validaForm()">
                                            <table  height="160" width="91%" border="0" align="center" cellspacing="0" class="bordaescura1px">
                                                <tr>
                                                    <td height="45" colspan="4" bgcolor="#CCCCCC"><div align="right" class="style35">
                                                            <div align="center" class="style27 style36"><img src="imagensrh/feriados.gif" alt="empresa" width="150" height="40"></div>
                                                        </div></td>
                                                </tr>
                                                <tr>
                                                    <td width="17%" height="50"><div align="right" class="style40 style35 style42 style40">
                                                            <div align="right">Nome do Feriado:</div>
                                                        </div>
                                                        <div align="center" class="style40"></div></td>
                                                    <td width="36%" height="50">
                                                        <span class="style40"><strong>
                                                                <label></label>
                                                            </strong>                </span><strong><span class="style40">
                                                                &nbsp;&nbsp;&nbsp;
                                                                <input name="nome" type="text" id="nome" size="30" onFocus="document.all.nome.style.background = '#CCFFCC'" 
                                                                       onBlur="document.all.nome.style.background = '#FFFFFF'" onChange="this.value = this.value.toUpperCase()">
                                                            </span></strong></td>
                                                    <td width="11%" height="50" colspan="-1">
                                                        <span class="style40"><strong>
                                                                <label></label>
                                                            </strong>                </span>
                                                        <div align="center" class="style40">
                                                            <div align="right"><strong>Data: </strong></div>
                                                        </div></td>
                                                    <td width="36%" height="50" colspan="-1"><strong><span class="style40">
                                                                &nbsp;&nbsp;&nbsp;
                                                                <input name="data" type="text" id="data" size="12" 
                                                                       onFocus="document.all.data.style.background = '#CCFFCC'" 
                                                                       onBlur="document.all.data.style.background = '#FFFFFF'"
                                                                       onKeyUp="mascara_data(this)" maxlength="10">
                                                            </span></strong></td>
                                                </tr>
                                                <tr>
                                                    <td width="17%" height="50"><div align="center" class="style40">
                                                            <div align="right"><strong>Tipo:<br>
                                                                </strong></div>
                                                        </div></td>
                                                    <td height="50" colspan="3">&nbsp;&nbsp;<strong>
                                                            <label>
                                                                <input type="radio" name="tipo" id="radio" value="1" onClick="document.all.linha_tabela.style.display = (document.all.linha_tabela.style.display == 'none') ? 'none' : 'none';">
                                                                <span class="style40">Federal</span></label>
                                                        </strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>
                                                            <label>
                                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                <input type="radio" name="tipo" id="radio2" value="2" checked onClick="document.all.linha_tabela.style.display = (document.all.linha_tabela.style.display == 'none') ? '' : '';">
                                                                <span class="style40">Regional</span></label>
                                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                            <label>
                                                                <input type="checkbox" name="datamovel" id="datamovel" value="1">
                                                                <span class="style40">Festa M&oacute;vel</span></label>
                                                        </strong>
                                                        <div align="center" class="style40"></div></td>
                                                </tr>
                                                <tr id="linha_tabela">
                                                    <td width="17%" height="50"><div align="center" class="style40">
                                                            <div align="right"><strong>Regi&atilde;o:<br>
                                                                </strong></div>
                                                        </div></td>
                                                    <td height="50" colspan="3"><strong>
                                                            &nbsp;&nbsp;&nbsp;
                                                            <select name='regiao' class='campotexto' id='regiao' 
                                                                    onFocus="document.all.regiao.style.background = '#CCFFCC'" 
                                                                    onBlur="document.all.regiao.style.background = '#FFFFFF'">
                                                                        <?php
                                                                        $result = mysql_query("SELECT * FROM regioes");
                                                                        while ($row = mysql_fetch_array($result)) {

                                                                            $row_regiao = "$row[id_regiao]";

                                                                            print "<option value=$row[id_regiao]>$row[regiao] - $row[sigla]</option>";
                                                                        }
                                                                        ?>
                                                            </select>
                                                        </strong></td>
                                                </tr>
                                            </table>
                                            <br>
                                            <label>
                                                <div align="center">
                                                    <input type="hidden" value="2" name="id">
                                                    <input type="hidden" value="<?= $regiao ?>" name="regiao_atual">
                                                    <input type="submit" name="button" id="button" value="Cadastrar">
                                                </div>
                                            </label>
                                            <br>          
                                        </form>
                                    </td>
                                </tr>

                                <tr>
                                    <td width="155" bgcolor="#FFFFFF">&nbsp;</td>
                                    <td width="549" bgcolor="#FFFFFF">&nbsp;</td>
                                </tr>

                                <tr valign="top"> 
                                    <td height="37" colspan="4"> <img src="../layout/baixo.gif" width="750" height="38"> 
                                        <?php
                                        include "../empresa.php";
                                        $rod = new empresa();
                                        $rod->rodape();
                                        ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </body>
        </html>
        <?php
        break;
    case 2:  //CADASTRANDO UM FERIADO

        $nome = $_REQUEST['nome'];
        $data = $_REQUEST['data'];
        $tipo = $_REQUEST['tipo'];
        $regiao = $_REQUEST['regiao'];
        $datamovel = $_REQUEST['datamovel'];

        $regiao_atual = $_REQUEST['regiao_atual'];

        if ($tipo == "1") {
            $tipo = "Federal";
            $regiao = "0";
        } else {
            $tipo = "Regional";
        }

        $id_user = $_COOKIE['logado'];
        $data_cad = date('Y-m-d');

        /*
          Função para converter a data
          De formato nacional para formato americano.
          Muito útil para você inserir data no mysql e visualizar depois data do mysql.
         */

        function ConverteData($Data) {
            if (strstr($Data, "/")) {//verifica se tem a barra /
                $d = explode("/", $Data); //tira a barra
                $rstData = "$d[2]-$d[1]-$d[0]"; //separa as datas $d[2] = ano $d[1] = mes etc...
                return $rstData;
            } elseif (strstr($Data, "-")) {
                $d = explode("-", $Data);
                $rstData = "$d[2]/$d[1]/$d[0]";
                return $rstData;
            } else {
                return "Data invalida";
            }
        }

        $data_f = ConverteData($data);


        mysql_query("INSERT INTO rhferiados (id_user_cad ,data_cad ,id_regiao ,tipo ,nome ,data,movel ,status) VALUES 
('$id_user', '$data_cad', '$regiao', '$tipo', '$nome', '$data_f','$datamovel', '1')") or die("Erro <br>" . mysql_error());

        print "
<script>
alert (\"Feriado cadastrado!\"); 
location.href=\"../rh/rh_feriados.php?id=1&regiao=$regiao_atual\"
</script>";

        break;
    case 3:  //DELETANDO UM FERIADO

        $id_feriado = $_REQUEST['feriado'];
        $regiao_atual = $_REQUEST['regiao_atual'];


        mysql_query("UPDATE rhferiados SET status = '0' WHERE id_feriado = '$id_feriado'") or die("Erro <br>" . mysql_error());

        print "
<script>
alert (\"Feriado deletado!\"); 
location.href=\"../rh/rh_feriados.php?id=1&regiao=$regiao_atual\"
</script>";


        break;
}

/* Liberando o resultado */
//mysql_free_result($result);

/* Fechando a conexão */
//mysql_close($conn);
?>

