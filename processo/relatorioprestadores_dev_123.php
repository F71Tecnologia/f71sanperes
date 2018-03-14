<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='login.php'>Logar</a> ";
    exit;
}

include "../conn.php";

$id = $_REQUEST['1'];

$id_user = $_COOKIE['logado'];

$regiao = $_REQUEST['regiao'];
?>

<html>

    <head>

        <title>:: Intranet ::</title>

        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

        <link href="../net1.css" rel="stylesheet" type="text/css">

        <style type="text/css"	>

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

            .style43 {font-family: Arial, Helvetica, sans-serif}

            .style45 {font-size: 14px; font-family: Arial, Helvetica, sans-serif; }

            .style29 {

                font-family: Arial, Verdana, Helvetica, sans-serif;
                font-size:9.5px;



                font-weight: bold;

            }

            -->

        </style>

        <script language='javascript'>

            function mascara_data(d) {

                var mydata = '';

                data = d.value;

                mydata = mydata + data;

                if (mydata.length == 2) {

                    mydata = mydata + '/';

                    d.value = mydata;

                }

                if (mydata.length == 5) {

                    mydata = mydata + '/';

                    d.value = mydata;

                }

                if (mydata.length == 10) {

                    verifica_data(d);

                }

            }

            function verifica_data(d) {

                dia = (d.value.substring(0, 2));

                mes = (d.value.substring(3, 5));

                ano = (d.value.substring(6, 10));

                situacao = "";

        // verifica o dia valido para cada mes  

                if ((dia < 01) || (dia < 01 || dia > 30) && (mes == 04 || mes == 06 || mes == 09 || mes == 11) || dia > 31) {

                    situacao = "falsa";

                }

        // verifica se o mes e valido  

                if (mes < 01 || mes > 12) {

                    situacao = "falsa";

                }

        // verifica se e ano bissexto  

                if (mes == 2 && (dia < 01 || dia > 29 || (dia > 28 && (parseInt(ano / 4) != ano / 4)))) {

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

            function TelefoneFormat(Campo, e) {

                var key = '';

                var len = 0;

                var strCheck = '0123456789';

                var aux = '';

                var whichCode = (window.Event) ? e.which : e.keyCode;

                if (whichCode == 13 || whichCode == 8 || whichCode == 0)

                {

                    return true;  // Enter backspace ou FN qualquer um que não seja alfa numerico

                }

                key = String.fromCharCode(whichCode);

                if (strCheck.indexOf(key) == -1) {

                    return false;  //NÃO E VALIDO

                }

                aux = Telefone_Remove_Format(Campo.value);

                len = aux.length;

                if (len >= 10)

                {

                    return false;	//impede de digitar um telefone maior que 10

                }

                aux += key;

                Campo.value = Telefone_Mont_Format(aux);

                return false;

            }

            function  Telefone_Mont_Format(Telefone)

            {

                var aux = len = '';

                len = Telefone.length;

                if (len <= 9)

                {

                    tmp = 5;

                }

                else

                {

                    tmp = 6;

                }

                aux = '';

                for (i = 0; i < len; i++)

                {

                    if (i == 0)

                    {

                        aux = '(';

                    }

                    aux += Telefone.charAt(i);

                    if (i + 1 == 2)

                    {

                        aux += ')';

                    }

                    if (i + 1 == tmp)

                    {

                        aux += '-';

                    }

                }

                return aux;

            }

            function  Telefone_Remove_Format(Telefone)

            {

                var strCheck = '0123456789';

                var len = i = aux = '';

                len = Telefone.length;

                for (i = 0; i < len; i++)

                {

                    if (strCheck.indexOf(Telefone.charAt(i)) != -1)

                    {

                        aux += Telefone.charAt(i);

                    }

                }

                return aux;

            }

            function formatar(mascara, documento) {

                var i = documento.value.length;

                var saida = mascara.substring(0, 1);

                var texto = mascara.substring(i)

                if (texto.substring(0, 1) != saida) {

                    documento.value += texto.substring(0, 1);

                }

            }

            function pula(maxlength, id, proximo) {

                if (document.getElementById(id).value.length >= maxlength) {

                    document.getElementById(proximo).focus();

                }

            }

            function FormataValor(objeto, teclapres, tammax, decimais)

            {

                var tecla = teclapres.keyCode;

                var tamanhoObjeto = objeto.value.length;

                if ((tecla == 8) && (tamanhoObjeto == tammax))

                {

                    tamanhoObjeto = tamanhoObjeto - 1;

                }

                if ((tecla == 8 || tecla == 88 || tecla >= 48 && tecla <= 57 || tecla >= 96 && tecla <= 105) && ((tamanhoObjeto + 1) <= tammax))

                {

                    vr = objeto.value;

                    vr = vr.replace("/", "");

                    vr = vr.replace("/", "");

                    vr = vr.replace(",", "");

                    vr = vr.replace(".", "");

                    vr = vr.replace(".", "");

                    vr = vr.replace(".", "");

                    vr = vr.replace(".", "");

                    tam = vr.length;

                    if (tam < tammax && tecla != 8)

                    {

                        tam = vr.length + 1;

                    }

                    if ((tecla == 8) && (tam > 1))

                    {

                        tam = tam - 1;

                        vr = objeto.value;

                        vr = vr.replace("/", "");

                        vr = vr.replace("/", "");

                        vr = vr.replace(",", "");

                        vr = vr.replace(".", "");

                        vr = vr.replace(".", "");

                        vr = vr.replace(".", "");

                        vr = vr.replace(".", "");

                    }

        //Cálculo para casas decimais setadas por parametro

                    if (tecla == 8 || tecla >= 48 && tecla <= 57 || tecla >= 96 && tecla <= 105)

                    {

                        if (decimais > 0)

                        {

                            if ((tam <= decimais))

                            {

                                objeto.value = ("0," + vr);

                            }

                            if ((tam == (decimais + 1)) && (tecla == 8))

                            {

                                objeto.value = vr.substr(0, (tam - decimais)) + ',' + vr.substr(tam - (decimais), tam);

                            }

                            if ((tam > (decimais + 1)) && (tam <= (decimais + 3)) && ((vr.substr(0, 1)) == "0"))

                            {

                                objeto.value = vr.substr(1, (tam - (decimais + 1))) + ',' + vr.substr(tam - (decimais), tam);

                            }

                            if ((tam > (decimais + 1)) && (tam <= (decimais + 3)) && ((vr.substr(0, 1)) != "0"))

                            {

                                objeto.value = vr.substr(0, tam - decimais) + ',' + vr.substr(tam - decimais, tam);

                            }

                            if ((tam >= (decimais + 4)) && (tam <= (decimais + 6)))

                            {

                                objeto.value = vr.substr(0, tam - (decimais + 3)) + '.' + vr.substr(tam - (decimais + 3), 3) + ',' + vr.substr(tam - decimais, tam);

                            }

                            if ((tam >= (decimais + 7)) && (tam <= (decimais + 9)))

                            {

                                objeto.value = vr.substr(0, tam - (decimais + 6)) + '.' + vr.substr(tam - (decimais + 6), 3) + '.' + vr.substr(tam - (decimais + 3), 3) + ',' + vr.substr(tam - decimais, tam);

                            }

                            if ((tam >= (decimais + 10)) && (tam <= (decimais + 12)))

                            {

                                objeto.value = vr.substr(0, tam - (decimais + 9)) + '.' + vr.substr(tam - (decimais + 9), 3) + '.' + vr.substr(tam - (decimais + 6), 3) + '.' + vr.substr(tam - (decimais + 3), 3) + ',' + vr.substr(tam - decimais, tam);

                            }

                            if ((tam >= (decimais + 13)) && (tam <= (decimais + 15)))

                            {

                                objeto.value = vr.substr(0, tam - (decimais + 12)) + '.' + vr.substr(tam - (decimais + 12), 3) + '.' + vr.substr(tam - (decimais + 9), 3) + '.' + vr.substr(tam - (decimais + 6), 3) + '.' + vr.substr(tam - (decimais + 3), 3) + ',' + vr.substr(tam - decimais, tam);

                            }

                        }

                        else if (decimais == 0)

                        {

                            if (tam <= 3)

                            {

                                objeto.value = vr;

                            }

                            if ((tam >= 4) && (tam <= 6))

                            {

                                if (tecla == 8)

                                {

                                    objeto.value = vr.substr(0, tam);

                                    window.event.cancelBubble = true;

                                    window.event.returnValue = false;

                                }

                                objeto.value = vr.substr(0, tam - 3) + '.' + vr.substr(tam - 3, 3);

                            }

                            if ((tam >= 7) && (tam <= 9))

                            {

                                if (tecla == 8)

                                {

                                    objeto.value = vr.substr(0, tam);

                                    window.event.cancelBubble = true;

                                    window.event.returnValue = false;

                                }

                                objeto.value = vr.substr(0, tam - 6) + '.' + vr.substr(tam - 6, 3) + '.' + vr.substr(tam - 3, 3);

                            }

                            if ((tam >= 10) && (tam <= 12))

                            {

                                if (tecla == 8)

                                {

                                    objeto.value = vr.substr(0, tam);

                                    window.event.cancelBubble = true;

                                    window.event.returnValue = false;

                                }

                                objeto.value = vr.substr(0, tam - 9) + '.' + vr.substr(tam - 9, 3) + '.' + vr.substr(tam - 6, 3) + '.' + vr.substr(tam - 3, 3);

                            }

                            if ((tam >= 13) && (tam <= 15))

                            {

                                if (tecla == 8)

                                {

                                    objeto.value = vr.substr(0, tam);

                                    window.event.cancelBubble = true;

                                    window.event.returnValue = false;

                                }

                                objeto.value = vr.substr(0, tam - 12) + '.' + vr.substr(tam - 12, 3) + '.' + vr.substr(tam - 9, 3) + '.' + vr.substr(tam - 6, 3) + '.' + vr.substr(tam - 3, 3);

                            }

                        }

                    }

                }

                else if ((window.event.keyCode != 8) && (window.event.keyCode != 9) && (window.event.keyCode != 13) && (window.event.keyCode != 35) && (window.event.keyCode != 36) && (window.event.keyCode != 46))

                {

                    window.event.cancelBubble = true;

                    window.event.returnValue = false;

                }

            }

        </script>

    </head>

    <body bgcolor="#FFFFFF">
        <div style="margin-left:1190px;margin-top:20px;"> <?php include('../reportar_erro.php'); ?> </div>

        <table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">

            <tr>

                <td align="center" valign="top"> 

                    <table width="750" border="0" cellpadding="0" cellspacing="0">

                        <tr>

                            <td colspan="2"><div align="center"><span class="style38"><br>

                                        RELAT&Oacute;RIO DE EMPRESAS CADASTRADAS</span><br>

                                    <br>

                                </div></td>

                        </tr>

                        <tr>

                            <td bgcolor="#FFFFFF">&nbsp;</td>

                            <td bgcolor="#FFFFFF"><div align="center"></div></td>

                        </tr>

                        <tr>

                            <td colspan="2" bgcolor="#FFFFFF"><br>

<?php
$result_empresas = mysql_query("SELECT *,date_format(data_proc, '%d/%m/%Y') as data_proc FROM prestadorservico where id_regiao = '$regiao'");



while ($row_empresas = mysql_fetch_array($result_empresas)) {



    if ($row_empresas['acompanhamento'] == "1") {

        $status = "Aberto";
    } else if ($row_empresas['acompanhamento'] == "2") {

        $status = "Aguardando Aprovação";
    } else if ($row_empresas['acompanhamento'] == "3") {

        $status = "Aprovado";
    } else if ($row_empresas['acompanhamento'] == "4") {

        $status = "Finalizado";
    } else if ($row_empresas['acompanhamento'] == "5") {

        $status = "Não Aprovado";
    }



    print "

<font face='Verdana, Arial' size='-1'>

<table width='95%' border='1' align='center' cellpadding='3' cellspacing='0' bordercolor='#ffFFff'>


<tr>

<td colspan='5' bgcolor='#990000'><div align='center' class='style41'><br>EMPRESA<br><br></div></td>

</tr>

<tr>

  <td colspan='5' bgcolor='#CCCCCC'><div align='right'>

    <DIV align='center' class='style29'>NUMERO PROCESSO: <strong>$row_empresas[numero]</strong></DIV>

  </div></td>

  </tr>

<tr>

<td width='18%' bgcolor='#ffffff' class='style29'><div align='right'>Endere&ccedil;o Contratante:</div></td>

<td colspan='3' bgcolor='#F7F7F7'class='style29'><div align='left'><strong>&nbsp;$row_empresas[endereco]</strong></div></td>

<td width='26%' bgcolor='#F7F7F7'class='style29'><div align='left'>CNPJ: <strong>$row_empresas[cnpj]</strong></div></td>

</tr>

<tr>

  <td bgcolor='#CCCCCC' class='style29'><div align='right'>Nome Fantasia:</div></td>

  <td colspan='2' bgcolor='#ffffff' class='style29'><strong>&nbsp;$row_empresas[c_fantasia]</strong></td>

  <td width='15%' bgcolor='#CCCCCC'class='style29' ><div align='right'>CNPJ:</div></td>

  <td bgcolor='#ffffff' class='style29' ><strong>&nbsp;$row_empresas[c_cnpj]</strong></td>

</tr>

<tr>

  <td bgcolor='#CCCCCC' class='style29'><div align='right'>Raz&atilde;o Social:</div></td>

  <td colspan='2' bgcolor='#F7F7F7' class='style29'><strong>&nbsp;$row_empresas[c_razao]</strong></td>

  <td bgcolor='#CCCCCC' class='style29'><div align='right'>IE:</div></td>

  <td bgcolor='#F7F7F7' class='style29'><strong>&nbsp;$row_empresas[c_ie]</strong></td>

</tr>

<tr>

  <td bgcolor='#CCCCCC' class='style29'><div align='right'>Endere&ccedil;o:</div></td>

  <td colspan='2' bgcolor='#ffffff' class='style29'><strong>&nbsp;$row_empresas[c_endereco]</strong></td>

  <td bgcolor='#CCCCCC' class='style29'><div align='right'>CCM:</div></td>

  <td bgcolor='#ffffff' class='style29'>&nbsp;<strong>$row_empresas[c_im]</strong></td>

</tr>

<tr>

  <td bgcolor='#CCCCCC' class='style29'><div align='right'>Telefone:</div></td>

  <td width='19%' bgcolor='#F7F7F7' class='style29'> <strong>&nbsp;$row_empresas[c_tel]</strong></td>

  <td width='22%' bgcolor='#F7F7F7' class='style29'>Fax:<strong>$row_empresas[c_fax]</strong></td>

  <td bgcolor='#CCCCCC' class='style29'><div align='right'>E-mail:</div></td>

  <td bgcolor='#F7F7F7' class='style29'><strong>&nbsp;$row_empresas[c_email]</strong></td>

</tr>

<tr>

  <td bgcolor='#CCCCCC' class='style29'><div align='right'>Responsavel:</div></td>

  <td colspan='2' bgcolor='#ffffff' class='style29'><strong>&nbsp;$row_empresas[c_responsavel]</strong></td>

  <td bgcolor='#CCCCCC' class='style29'><div align='right'>Site:</div></td>

  <td bgcolor='#ffffff' class='style29'><strong>&nbsp;$row_empresas[c_site]</strong></td>

</tr>

<tr>

  <td bgcolor='#CCCCCC' class='style29'><div align='right'>RG:</div></td>

  <td bgcolor='#F7F7F7' class='style29'><strong>&nbsp;$row_empresas[c_rg]</strong></td>

  <td bgcolor='#F7F7F7' class='style29'>CPF:<strong>&nbsp;$row_empresas[c_cpf]</strong></td>

  <td bgcolor='#CCCCCC' class='style29'><div align='right'>E-mail:</div></td>

  <td bgcolor='#F7F7F7' class='style29'><strong>&nbsp;$row_empresas[c_email2]</strong></td>

</tr>

<tr>

  <td bgcolor='#CCCCCC' class='style29'><div align='right'>Nome Contato:</div></td>

  <td colspan='2' bgcolor='#ffffff' class='style29'><strong>&nbsp;$row_empresas[co_responsavel]</strong></td>

  <td bgcolor='#CCCCCC' class='style29'><div align='right'>Telefone:</div></td>

  <td bgcolor='#ffffff' class='style29'><strong>&nbsp;$row_empresas[co_tel]</strong></td>

</tr>

<tr>

  <td bgcolor='#CCCCCC' class='style29'><div align='right'>E-mail do contato:</div></td>

  <td colspan='2' bgcolor='#F7F7F7' class='style29'><strong>&nbsp;$row_empresas[co_email]</strong></td>

  <td bgcolor='#CCCCCC' class='style29'><div align='right'>Fax:</div></td>

  <td bgcolor='#F7F7F7' class='style29'><strong>&nbsp;$row_empresas[co_fax]</strong></td>

</tr>

<tr>

  <td bgcolor='#CCCCCC' class='style29'><div align='right'>Municipio:</div></td>

  <td bgcolor='#ffffff' class='style29' colspan='2'><strong>&nbsp;$row_empresas[co_municipio]</strong></td>


  <td bgcolor='#CCCCCC' class='style29'><div align='right'>Data:</div></td>

  <td bgcolor='#ffffff' class='style29'><strong>$row_empresas[data_proc]</strong></td>

</tr>
<tr>

  <td bgcolor='#CCCCCC' class='style29'><div align='right'>Nome do Banco:</div></td>

  <td bgcolor='#ffffff' class='style29'  colspan='2'><strong>&nbsp;$row_empresas[nome_banco]</strong></td>


  <td bgcolor='#CCCCCC' class='style29'><div align='right'>Agencia:</div></td>

  <td bgcolor='#ffffff' class='style29'><strong>$row_empresas[agencia]</strong></td>

</tr>
<tr>

  <td bgcolor='#CCCCCC' class='style29'><div align='right'>Conta:</div></td>

  <td bgcolor='#ffffff' class='style29'  colspan='2'><strong>&nbsp;$row_empresas[conta]</strong></td>


  <td bgcolor='#CCCCCC' class='style29'><div align='right'>Valor Limite:</div></td>

  <td bgcolor='#ffffff' class='style29'><strong>$row_empresas[valor_limite]</strong></td>

</tr>

<tr>

  <td bgcolor='#CCCCCC'class='style29' ><div align='right'>Objeto:</div></td>

  <td colspan='4' bgcolor='#F7F7F7' class='style29'><strong>&nbsp;$row_empresas[objeto]</strong></td>

  </tr>

<tr>

  <td bgcolor='#CCCCCC' class='style29'><div align='right'>

    <div align='right' class='style29'>Especifica&ccedil;&atilde;o do Servi&ccedil;o:</div></td>

  <td colspan='4' bgcolor='#ffffff' class='style29'><strong>&nbsp;$row_empresas[especificacao]</strong></td>

</tr>

<tr>

<td colspan='5' bgcolor='#990000' class='style29'><div align='center' class='style41'><br>PAGAMENTOS EFETUADOS<br><br></div></td>

</tr>

<tr>

  <td bgcolor='#CCFFCC' class='style29'><div align='right'>

    <div align='center' class='style29'><strong>N&Uacute;MERO</strong></div>

  </div></td>

  <td bgcolor='#CCFFCC' class='style29'><div align='center' class='style29'><strong>VALOR</strong></div></td>

  <td bgcolor='#CCFFCC' class='style29'><div align='center' class='style29'><strong>DATA</strong></div></td>

  <td bgcolor='#CCFFCC' class='style29'><div align='center' class='style29'><strong>DOCUMENTO</strong></div></td>

  <td bgcolor='#CCFFCC' class='style29'><div align='center' class='style29'><strong>STATUS</strong></div></td>

</tr>";



    $result_pg = mysql_query("SELECT *,date_format(data, '%d/%m/%Y')as data FROM prestador_pg where id_prestador = '$row_empresas[0]' ORDER BY parcela") or die("Erro" . mysql_error());



    while ($row_pg = mysql_fetch_array($result_pg)) {



        $valor = str_replace(",", ".", $row_pg['valor']);

        $valor_f = number_format($valor, 2, ",", ".");



        if ($row_pg['gerado'] == "2") {

            $impresso = "Ja foi impresso";
        } else {

            $impresso = "N&atilde;o foi impresso";
        }



        print "

<tr>

<td><div align='center' class='style29'>$row_pg[parcela]</div></td>

<td><div align='center' class='style29'>$valor_f</div></td>

<td><div align='center' class='style29'>$row_pg[data]</div></td>

<td><div align='center' class='style29'>$row_pg[documento]</div></td>

<td><div align='center' class='style29'>$impresso</div></td>

</tr>";
    }



    print "



</table> </font>

<br>

<hr>

<br>";
}
?>

                            </td>

                        </tr>

                        <tr>

                            <td width="155" bgcolor="#FFFFFF">&nbsp;</td>

                            <td width="549" bgcolor="#FFFFFF">&nbsp;</td>

                        </tr>

                        <tr valign="top"> 

                            <td height="37" colspan="4">

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
/* Liberando o resultado */

//mysql_free_result($result);

/* Fechando a conexão */

mysql_close($conn);
?>