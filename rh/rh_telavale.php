<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a> ";
}

include "../conn.php";

$id = $_REQUEST['id'];
$id_user = $_COOKIE['logado'];
$regiao = $_REQUEST['regiao'];

$data = date('d/m/Y');

$result_local = mysql_query("SELECT * FROM regioes where id_regiao = '$regiao'");
$row_local = mysql_fetch_array($result_local);

if (empty($_REQUEST['nome'])) {

    echo "<script language=\"JavaScript\">
function TelefoneFormat(Campo, e) {
var key = '';
var len = 0;
var strCheck = '0123456789';
var aux = '';
var whichCode = (window.Event) ? e.which : e.keyCode;
if (whichCode == 13 || whichCode == 8 || whichCode == 0)
{
return true;  // Enter backspace ou FN qualquer um que n�o seja alfa numerico
}
key = String.fromCharCode(whichCode);
if (strCheck.indexOf(key) == -1){
return false;  //N�O E VALIDO
}
aux =  Telefone_Remove_Format(Campo.value);
len = aux.length;
if(len>=10)
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
if(len<=9)
{
tmp = 5;
}
else
{
tmp = 6;
}
aux = '';
for(i = 0; i < len; i++)
{
if(i==0)
{
aux = '(';
}
aux += Telefone.charAt(i);
if(i+1==2)
{
aux += ')';
}
if(i+1==tmp)
{
aux += '-';
}
}
return aux ;
}
function  Telefone_Remove_Format(Telefone)
{
var strCheck = '0123456789';
var len = i = aux = '';
len = Telefone.length;
for(i = 0; i < len; i++)
{
if (strCheck.indexOf(Telefone.charAt(i))!=-1)
{
aux += Telefone.charAt(i);
}
}
return aux;
}
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
situacao = \"\";  
// verifica o dia valido para cada mes  
if ((dia < 01)||(dia < 01 || dia > 30) && (  mes == 04 || mes == 06 || mes == 09 || mes == 11 ) || dia > 31) {  
situacao = \"falsa\";  
}  
// verifica se o mes e valido  
if (mes < 01 || mes > 12 ) {  
situacao = \"falsa\";  
}  
// verifica se e ano bissexto  
if (mes == 2 && ( dia < 01 || dia > 29 || ( dia > 28 && (parseInt(ano / 4) != ano / 4)))) {  
situacao = \"falsa\";  
}  
if (d.value == \"\") {  
situacao = \"falsa\";  
}  
if (situacao == \"falsa\") {  
alert(\"Data digitada � inv�lida, digite novamente!\"); 
d.value = \"\";  
d.focus();  
}  
}
function FormataValor(objeto,teclapres,tammax,decimais) 
{
var tecla            = teclapres.keyCode;
var tamanhoObjeto    = objeto.value.length;
if ((tecla == 8) && (tamanhoObjeto == tammax))
{
tamanhoObjeto = tamanhoObjeto - 1 ;
}
if (( tecla == 8 || tecla == 88 || tecla >= 48 && tecla <= 57 || tecla >= 96 && tecla <= 105 ) && ((tamanhoObjeto+1) <= tammax))
{
vr    = objeto.value;
vr    = vr.replace( \"/\", \"\" );
vr    = vr.replace( \"/\", \"\" );
vr    = vr.replace( \",\", \"\" );
vr    = vr.replace( \".\", \"\" );
vr    = vr.replace( \".\", \"\" );
vr    = vr.replace( \".\", \"\" );
vr    = vr.replace( \".\", \"\" );
tam    = vr.length;
if (tam < tammax && tecla != 8)
{
tam = vr.length + 1 ;
}
if ((tecla == 8) && (tam > 1))
{
tam = tam - 1 ;
vr = objeto.value;
vr = vr.replace( \"/\", \"\" );
vr = vr.replace( \"/\", \"\" );
vr = vr.replace( \",\", \"\" );
vr = vr.replace( \".\", \"\" );
vr = vr.replace( \".\", \"\" );
vr = vr.replace( \".\", \"\" );
vr = vr.replace( \".\", \"\" );
}
//C�lculo para casas decimais setadas por parametro
if ( tecla == 8 || tecla >= 48 && tecla <= 57 || tecla >= 96 && tecla <= 105 )
{
if (decimais > 0)
{
if ( (tam <= decimais) )
{ 
objeto.value = (\"0,\" + vr) ;
}
if( (tam == (decimais + 1)) && (tecla == 8))
{
objeto.value = vr.substr( 0, (tam - decimais)) + ',' + vr.substr( tam - (decimais), tam ) ;    
}
if ( (tam > (decimais + 1)) && (tam <= (decimais + 3)) &&  ((vr.substr(0,1)) == \"0\"))
{
objeto.value = vr.substr( 1, (tam - (decimais+1))) + ',' + vr.substr( tam - (decimais), tam ) ;
}
if ( (tam > (decimais + 1)) && (tam <= (decimais + 3)) &&  ((vr.substr(0,1)) != \"0\"))
{
objeto.value = vr.substr( 0, tam - decimais ) + ',' + vr.substr( tam - decimais, tam ) ; 
}
if ( (tam >= (decimais + 4)) && (tam <= (decimais + 6)) )
{
objeto.value = vr.substr( 0, tam - (decimais + 3) ) + '.' + vr.substr( tam - (decimais + 3), 3 ) + ',' + vr.substr( tam - decimais, tam ) ;
}
if ( (tam >= (decimais + 7)) && (tam <= (decimais + 9)) )
{
objeto.value = vr.substr( 0, tam - (decimais + 6) ) + '.' + vr.substr( tam - (decimais + 6), 3 ) + '.' + vr.substr( tam - (decimais + 3), 3 ) + ',' + vr.substr( tam - decimais, tam ) ;
}
if ( (tam >= (decimais + 10)) && (tam <= (decimais + 12)) )
{
objeto.value = vr.substr( 0, tam - (decimais + 9) ) + '.' + vr.substr( tam - (decimais + 9), 3 ) + '.' + vr.substr( tam - (decimais + 6), 3 ) + '.' + vr.substr( tam - (decimais + 3), 3 ) + ',' + vr.substr( tam - decimais, tam ) ;
}
if ( (tam >= (decimais + 13)) && (tam <= (decimais + 15)) )
{
objeto.value = vr.substr( 0, tam - (decimais + 12) ) + '.' + vr.substr( tam - (decimais + 12), 3 ) + '.' + vr.substr( tam - (decimais + 9), 3 ) + '.' + vr.substr( tam - (decimais + 6), 3 ) + '.' + vr.substr( tam - (decimais + 3), 3 ) + ',' + vr.substr( tam - decimais, tam ) ;
}
}
else if(decimais == 0)
{
if ( tam <= 3 )
{ 
objeto.value = vr ;
}
if ( (tam >= 4) && (tam <= 6) )
{
if(tecla == 8)
{
objeto.value = vr.substr(0, tam);
window.event.cancelBubble = true;
window.event.returnValue = false;
}
objeto.value = vr.substr(0, tam - 3) + '.' + vr.substr( tam - 3, 3 ); 
}
if ( (tam >= 7) && (tam <= 9) )
{
if(tecla == 8)
{
objeto.value = vr.substr(0, tam);
window.event.cancelBubble = true;
window.event.returnValue = false;
}
objeto.value = vr.substr( 0, tam - 6 ) + '.' + vr.substr( tam - 6, 3 ) + '.' + vr.substr( tam - 3, 3 ); 
}
if ( (tam >= 10) && (tam <= 12) )
{
if(tecla == 8)
{
objeto.value = vr.substr(0, tam);
window.event.cancelBubble = true;
window.event.returnValue = false;
}
objeto.value = vr.substr( 0, tam - 9 ) + '.' + vr.substr( tam - 9, 3 ) + '.' + vr.substr( tam - 6, 3 ) + '.' + vr.substr( tam - 3, 3 ); 
}
if ( (tam >= 13) && (tam <= 15) )
{
if(tecla == 8)
{
objeto.value = vr.substr(0, tam);
window.event.cancelBubble = true;
window.event.returnValue = false;
}
objeto.value = vr.substr( 0, tam - 12 ) + '.' + vr.substr( tam - 12, 3 ) + '.' + vr.substr( tam - 9, 3 ) + '.' + vr.substr( tam - 6, 3 ) + '.' + vr.substr( tam - 3, 3 ) ;
}            
}
}
}
else if((window.event.keyCode != 8) && (window.event.keyCode != 9) && (window.event.keyCode != 13) && (window.event.keyCode != 35) && (window.event.keyCode != 36) && (window.event.keyCode != 46))
{
window.event.cancelBubble = true;
window.event.returnValue = false;
}
} 
</script>";
    
    //Segunda parte do script, recebendo os dados para gerar os arquivos
    //$RELATORIO = $_REQUEST['relatorio'];

    $DATA_INI = $_REQUEST['data_ini'];
    $DATA_FINAL = $_REQUEST['data_final'];
    $MES = $_REQUEST['mes'];
    $STATUS = $_REQUEST['status'];
    $REGIAO = $_REQUEST['regiao'];
    /*
      if($STATUS == 'CRIAR'){

      $data_ini=explode("/",$DATA_INI);
      $d_ini = $data_ini[0];
      $m_ini = $data_ini[1];
      $a_ini = $data_ini[2];

      $data_ini_MYSQL = $a_ini.'-'.$m_ini.'-'.$d_ini;

      $data_final=explode("/",$DATA_FINAL);
      $d_final = $data_final[0];
      $m_final = $data_final[1];
      $a_final = $data_final[2];

      $data_final_MYSQL = $a_final.'-'.$m_final.'-'.$d_final;

      $ANO = date('Y');

      //Analiza se o protocolo j� do m�s j� foi cadastrado.
      $result = mysql_query("SELECT * FROM rh_vale_protocolo WHERE id_reg = '$REGIAO' AND mes='$MES' AND ano='$ANO'");

      //Caso a QUERY acima exista no banco de dados, a vai�vel $num_row_verifica ter� valor 0.
      $num_row_verifica = mysql_num_rows($result);
      if($num_row_verifica == 0){
      mysql_query("INSERT rh_vale_protocolo SET id_reg='$REGIAO', mes='$MES',ano='$ANO', data_ini='$data_ini_MYSQL', data_fim='$data_final_MYSQL', user='$id_user', data=CURDATE()");
      }else{
      $result = mysql_query("SELECT * FROM ano_meses WHERE num_mes='$MES'");
      $row = mysql_fetch_array($result);
      echo "<script> alert( 'O m�s de $row[nome_mes] n�o pode ser gerado novamente!'); </script>";
      }
      }
     */
    $ANO = date('Y');
    ?>
    <html>
        <head>
            <title>:: Intranet :: Vale Transporte</title>
            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
            <link href="../net1.css" rel="stylesheet" type="text/css">
            <link rel="shortcut icon" href="../favicon.ico" />
            <style type="text/css">
                <!--
                body {
                    margin:0px;
                }
                .style35 {
                    font-family: Geneva, Arial, Helvetica, sans-serif;
                    font-weight: bold;
                }
                .style36 {
                    font-size: 14px;
                    font-family: Verdana, Geneva, sans-serif;
                }
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
                .style51 {
                    font-family: arial, verdana, "ms sans serif";
                    font-weight: bold;
                }
                .style52 {font-family: arial, verdana, "ms sans serif"}
                .style53 {font-family: Arial, Verdana, Helvetica, sans-serif}
                .style55 {font-size: 10}
                .style56 {font-family: Arial, Verdana, Helvetica, sans-serif; font-weight: bold; }
                -->
            </style>
            <script language="javascript"> 

                //o par�mentro form � o formulario em quest�o e t � um booleano 
                function ticar(form, t) { 
                    campos = form.elements; 
                    for (x=0; x<campos.length; x++) 
                    if (campos[x].type == "checkbox") campos[x].checked = t; 
                }
              
                function FormataValor(objeto,teclapres,tammax,decimais) 
                {

                    var tecla            = teclapres.keyCode;
                    var tamanhoObjeto    = objeto.value.length;

                    if ((tecla == 8) && (tamanhoObjeto == tammax))
                    {
                        tamanhoObjeto = tamanhoObjeto - 1 ;
                    }



                    if (( tecla == 8 || tecla == 88 || tecla >= 48 && tecla <= 57 || tecla >= 96 && tecla <= 105 ) && ((tamanhoObjeto+1) <= tammax))
                    {

                        vr    = objeto.value;
                        vr    = vr.replace( "/", "" );
                        vr    = vr.replace( "/", "" );
                        vr    = vr.replace( ",", "" );
                        vr    = vr.replace( ".", "" );
                        vr    = vr.replace( ".", "" );
                        vr    = vr.replace( ".", "" );
                        vr    = vr.replace( ".", "" );
                        tam    = vr.length;
                    
                        if (tam < tammax && tecla != 8)
                        {
                            tam = vr.length + 1 ;
                        }

                        if ((tecla == 8) && (tam > 1))
                        {
                            tam = tam - 1 ;
                            vr = objeto.value;
                            vr = vr.replace( "/", "" );
                            vr = vr.replace( "/", "" );
                            vr = vr.replace( ",", "" );
                            vr = vr.replace( ".", "" );
                            vr = vr.replace( ".", "" );
                            vr = vr.replace( ".", "" );
                            vr = vr.replace( ".", "" );
                        }
                
                        //C�lculo para casas decimais setadas por parametro
                        if ( tecla == 8 || tecla >= 48 && tecla <= 57 || tecla >= 96 && tecla <= 105 )
                        {
                            if (decimais > 0)
                            {
                                if ( (tam <= decimais) )
                                { 
                                    objeto.value = ("0," + vr) ;
                                }
                                if( (tam == (decimais + 1)) && (tecla == 8))
                                {
                                    objeto.value = vr.substr( 0, (tam - decimais)) + ',' + vr.substr( tam - (decimais), tam ) ;    
                                }
                                if ( (tam > (decimais + 1)) && (tam <= (decimais + 3)) &&  ((vr.substr(0,1)) == "0"))
                                {
                                    objeto.value = vr.substr( 1, (tam - (decimais+1))) + ',' + vr.substr( tam - (decimais), tam ) ;
                                }
                                if ( (tam > (decimais + 1)) && (tam <= (decimais + 3)) &&  ((vr.substr(0,1)) != "0"))
                                {
                                    objeto.value = vr.substr( 0, tam - decimais ) + ',' + vr.substr( tam - decimais, tam ) ; 
                                }
                                if ( (tam >= (decimais + 4)) && (tam <= (decimais + 6)) )
                                {
                                    objeto.value = vr.substr( 0, tam - (decimais + 3) ) + '.' + vr.substr( tam - (decimais + 3), 3 ) + ',' + vr.substr( tam - decimais, tam ) ;
                                }
                                if ( (tam >= (decimais + 7)) && (tam <= (decimais + 9)) )
                                {
                                    objeto.value = vr.substr( 0, tam - (decimais + 6) ) + '.' + vr.substr( tam - (decimais + 6), 3 ) + '.' + vr.substr( tam - (decimais + 3), 3 ) + ',' + vr.substr( tam - decimais, tam ) ;
                                }
                                if ( (tam >= (decimais + 10)) && (tam <= (decimais + 12)) )
                                {
                                    objeto.value = vr.substr( 0, tam - (decimais + 9) ) + '.' + vr.substr( tam - (decimais + 9), 3 ) + '.' + vr.substr( tam - (decimais + 6), 3 ) + '.' + vr.substr( tam - (decimais + 3), 3 ) + ',' + vr.substr( tam - decimais, tam ) ;
                                }
                                if ( (tam >= (decimais + 13)) && (tam <= (decimais + 15)) )
                                {
                                    objeto.value = vr.substr( 0, tam - (decimais + 12) ) + '.' + vr.substr( tam - (decimais + 12), 3 ) + '.' + vr.substr( tam - (decimais + 9), 3 ) + '.' + vr.substr( tam - (decimais + 6), 3 ) + '.' + vr.substr( tam - (decimais + 3), 3 ) + ',' + vr.substr( tam - decimais, tam ) ;
                                }
                            }
                            else if(decimais == 0)
                            {
                                if ( tam <= 3 )
                                { 
                                    objeto.value = vr ;
                                }
                                if ( (tam >= 4) && (tam <= 6) )
                                {
                                    if(tecla == 8)
                                    {
                                        objeto.value = vr.substr(0, tam);
                                        window.event.cancelBubble = true;
                                        window.event.returnValue = false;
                                    }
                                    objeto.value = vr.substr(0, tam - 3) + '.' + vr.substr( tam - 3, 3 ); 
                                }
                                if ( (tam >= 7) && (tam <= 9) )
                                {
                                    if(tecla == 8)
                                    {
                                        objeto.value = vr.substr(0, tam);
                                        window.event.cancelBubble = true;
                                        window.event.returnValue = false;
                                    }
                                    objeto.value = vr.substr( 0, tam - 6 ) + '.' + vr.substr( tam - 6, 3 ) + '.' + vr.substr( tam - 3, 3 ); 
                                }
                                if ( (tam >= 10) && (tam <= 12) )
                                {
                                    if(tecla == 8)
                                    {
                                        objeto.value = vr.substr(0, tam);
                                        window.event.cancelBubble = true;
                                        window.event.returnValue = false;
                                    }
                                    objeto.value = vr.substr( 0, tam - 9 ) + '.' + vr.substr( tam - 9, 3 ) + '.' + vr.substr( tam - 6, 3 ) + '.' + vr.substr( tam - 3, 3 ); 
                                }
                                if ( (tam >= 13) && (tam <= 15) )
                                {
                                    if(tecla == 8)
                                    {
                                        objeto.value = vr.substr(0, tam);
                                        window.event.cancelBubble = true;
                                        window.event.returnValue = false;
                                    }
                                    objeto.value = vr.substr( 0, tam - 12 ) + '.' + vr.substr( tam - 12, 3 ) + '.' + vr.substr( tam - 9, 3 ) + '.' + vr.substr( tam - 6, 3 ) + '.' + vr.substr( tam - 3, 3 ) ;
                                }            
                            }
                        }
                    }
                    else if((window.event.keyCode != 8) && (window.event.keyCode != 9) && (window.event.keyCode != 13) && (window.event.keyCode != 35) && (window.event.keyCode != 36) && (window.event.keyCode != 46))
                    {
                        window.event.cancelBubble = true;
                        window.event.returnValue = false;
                    }
                } 

            </script>
        </head>

        <body bgcolor="#FFFFFF">
            <table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">

                <tr>
                    <td align="center" valign="top"> 
                        <table width="750" border="0" cellpadding="0" cellspacing="0">

                            <td colspan="4"><img src="../layout/topo.gif" width="750" height="38"></td>
                </tr>

                <tr>
                    <td width="21" rowspan="3" background="../layout/esquerdo.gif">&nbsp;</td>
                    <td width="155" bgcolor="#FFFFFF">&nbsp;</td>
                    <td width="549" bgcolor="#FFFFFF">&nbsp;</td>
                    <td width="26" rowspan="3" background="../layout/direito.gif">&nbsp;</td>
                </tr>



                <tr>
                    <td height="24" colspan="2" bgcolor="#FFFFFF" ><table  height="114" width="95%" align="center" cellspacing="0" class="bordaescura1px">
                            <tr>
                                <td> 
                                    <a href="../principalrh.php?regiao=<?php echo $regiao; ?>&id=1" class="voltar"> 
                                        <img src="../img_menu_principal/voltar.png"/>
                                    </a> 
                                </td>
                                <td align="right"  colspan='3' ><?php include('../reportar_erro.php'); ?></td>
                            </tr>
                            <tr> 
                            <tr>
                                <td colspan="100%" height="45" bgcolor="#666666">
                                    <div align="right" class="style35">
                                        <div align="center" class="style27 style36">GERENCIAMENTO DE VALE TRANSPORTE<br></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="100%" align="center" bgcolor="#FFFFFF">
                                    <script language="javascript">
                                        function validaForm(){
                                            d = document.form1;

                                            if (d.nome.value == ""){
                                                alert("O campo Nome deve ser preenchido!");
                                                d.nome.focus();
                                                return false;
                                            }

                                            return true;   }

                                    </script>
                                    <table width="90%" border="0" cellspacing="0" cellpadding="0">
                                        <tr class="campotexto">
                                            <td align="center" valign="baseline"><br>
                                                <br>
                                                <a href="rh_valeconcessionarias.php"><img src="../imagens/vt1.jpg" width="88" height="60" border="0"></a><br>
                                                CADASTRO DE CONCESSION&Aacute;RIAS<br>
                                                <br>
                                                <br>
                                                <br></td>
                                            <td align="center" valign="baseline"><br>      <br>
                                                <a href="rh_valetarifas.php?regiao=<?= $regiao ?>"><img src="../imagens/vt2.jpg" width="60" height="60" border="0"></a><br>
                                                CADASTRO DE TARIFAS<br>
                                                <br>
                                                <br>
                                                <br></td>
                                            <td align="center" valign="baseline"><br>      <br>
                                                <a href="rh_valerelatorios.php?regiao=<?= $regiao ?>"><img src="../imagens/vt3.jpg" width="88" height="60" border="0"></a><br>
                                                RELAT&Oacute;RIOS E SOLICITA&Ccedil;&Atilde;O<br></td>
                                        </tr>
                                    </table>
                                
                                    <p><a href="vt/importacao_usu.php">Gerar arquivo de importa��o de Usu�rio</a></p>
                                
                                </td>
                            </tr>
                        </table></td>
                </tr>
                <tr>
                    <td bgcolor="#FFFFFF">&nbsp;</td>
                    <td bgcolor="#FFFFFF"><div align="center"></div></td>
                </tr>


                <tr valign="top"> 
                    <td height="37" colspan="4"> <img src="../layout/baixo.gif" width="750" height="38"> 
                        <div align="center" class="style6"><br>

                            <?php
                            include "../empresa.php";
                            $rod = new empresa();
                            $rod->rodape();
                            ?>
                        </div></td>
                </tr>
            </table>
        </td>
    </tr>
    </table>
    </body>
    </html>

    <?php
} else { // AKI VAI RODAR O CADASTRO
    $tipo_cad = $_REQUEST['tipo_cad'];

    if ($tipo_cad == "1") {                  //CADASTRANDO CONCESSIONARIAS
        $regiao = $_REQUEST['regiao'];

        $nome = $_REQUEST['nome'];
        $logo = $_REQUEST['logo'];

        $id_user = $_COOKIE['logado'];
        $data_cad = date('Y-m-d');

        if ($logo == "1") {    // ----------------- AQUI TEM ARQUIVO -------------------
            $arquivo = isset($_FILES['arquivo']) ? $_FILES['arquivo'] : FALSE;

            if ($arquivo[type] != "image/x-png" && $arquivo[type] != "image/pjpeg" && $arquivo[type] != "image/gif" && $arquivo [type] != "image/jpe") {     //aki a imagem nao corresponde com as exten��es especificadas
                print "<center>
     <hr><font size=2><b>
     Tipo de arquivo n�o permitido: $arquivo[type], os �nicos padr�es permitidos s�o (gif - jpg - jpeg - png)<br>
     $arquivo[type] <br><br>
     <a href='rh_vale.php?regiao=$regiao'>Voltar</a>
     </b></font>";

                exit;
            } else {  //aqui o arquivo � realente de imagem e vai ser carregado para o servidor
                $arr_basename = explode(".", $arquivo['name']);
                $file_type = $arr_basename[1];

                if ($file_type == "gif") {
                    $tipo_name = ".gif";
                } if ($file_type == "jpg" or $arquivo[type] == "jpeg") {
                    $tipo_name = ".jpg";
                } if ($file_type == "png") {
                    $tipo_name = ".png";
                }

                $logo = $tipo_name;

                mysql_query("INSERT INTO rh_concessionarias(id_regiao,id_user,nome,logo,data) values 
('$regiao','$id_user','$nome','$logo','$data_cad')") or die("<hr>Erro no insert<br><hr>" . mysql_error());

                $row_id = mysql_insert_id();

                // Resolvendo o nome e para onde o arquivo ser� movido
                $diretorio = "logo/";

                $nome_tmp = $regiao . "logo_vale" . $row_id . $tipo_name;
                $nome_arquivo = "$diretorio$nome_tmp";

                move_uploaded_file($arquivo['tmp_name'], $nome_arquivo) or die("Erro ao enviar o Arquivo: $nome_arquivo");
            } //aqui fecha o IF que verificar se o arquivo tem a exten��o especificada
        } else {    //AQUI EST� SEM A LOGO
            mysql_query("INSERT INTO rh_concessionarias(id_regiao,id_user,nome,tipo,data) values 
('$regiao','$id_user','$nome','$tipo','$data_cad')") or die("<hr>Erro no insert<br><hr>" . mysql_error());
        }

        print "
<script>
alert (\"Informa��es gravadas com sucesso\");
location.href=\"rh_vale.php?regiao=$regiao\"
</script>
";
    } elseif ($tipo_cad == "2") {                  //CADASTRADO VALOR DE VALE
        $regiao = $_REQUEST['regiao'];

        $concessionaria = $_REQUEST['concessionaria'];
        $tipo2 = $_REQUEST['tipo2'];
        $valor = $_REQUEST['valor'];
        $intinerario = $_REQUEST['intinerario'];
        $descriao = $_REQUEST['descriao'];
        $codigo = $_REQUEST['codigo'];

        $valor = str_replace(".", "", $valor);

        $id_user = $_COOKIE['logado'];
        $data_cad = date('Y-m-d');

        mysql_query("INSERT INTO rh_tarifas(tipo,valor,itinerario,descricao,id_concessionaria,id_user,data,id_regiao,codigo) 
values 
('$tipo2','$valor','$intinerario','$descriao','$concessionaria','$id_user','$data_cad','$regiao','$codigo')") or die
                        ("<hr>Erro no insert<br><hr>" . mysql_error());

        print "
<script>
alert (\"Informa��es gravadas com sucesso\");
location.href=\"rh_vale.php?regiao=$regiao\"
</script>
";
    } elseif ($tipo_cad == "3") {  //REMOVER TARIFA
        $regiao = $_REQUEST['regiao'];
        $tarifa = $_REQUEST['tarifa'];

        mysql_query("UPDATE rh_tarifas SET status_reg = '0' where id_tarifas = '$tarifa'");

        print "
<script>
alert (\"Informa��es gravadas com sucesso\");
location.href=\"rh_vale.php?regiao=$regiao\"
</script>
";
    }
}
/* Liberando o resultado */
//mysql_free_result($result);

/* Fechando a conex�o */
//mysql_close($conn);
?>
