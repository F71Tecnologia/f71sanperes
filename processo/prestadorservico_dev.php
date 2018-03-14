<?php
include('include/restricoes.php');
include('../funcoes.php');
include('include/criptografia.php');
include('../classes/formato_data.php');
include('../wfunction.php');

$id = $_REQUEST['id'];
$id_user = $_COOKIE['logado'];
$regiao = $_REQUEST['regiao'];
$projeto = $_REQUEST['selprojeto'];


//echo "<pre>";
//print_r($_REQUEST);exit;

if(empty($id))
    $id = 1;

switch ($id) {
    case 1:
        //SELECIONANDO O INSTITUTO PARAR CARREGAR A LOGO
        $result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
        $row_user = mysql_fetch_array($result_user);
        $result_master = mysql_query("SELECT * FROM master where id_master = '$row_user[id_master]'");
        $row_master = mysql_fetch_array($result_master);
        //SELECIONANDO O INSTITUTO PARAR CARREGAR A LOGO
       
        $rs_medidas = montaQuery("prestador_medida","*",null,"medida ASC");
        $padraoCampoMedida = 17; //VALOR PADRÃO PARA UNIDADE DE MEDIDA (VALOR MENSAL)
        
        $qr_projeto = mysql_query("SELECT id_projeto, nome FROM projeto WHERE id_regiao = '{$regiao}' AND id_projeto = '{$projeto}'");
        ?>
        <html>
            <head>
                <title>:: Intranet ::</title>
                <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
                <link href="estilo_financeiro.css" rel="stylesheet" type="text/css">
                <link href="../adm/css/estrutura.css" rel="stylesheet" type="text/css">
                <link href="../jquery/validationEngine/jquery.validationEngine.jquery.css" rel="stylesheet" type="text/css">
                <link href="../net1.css" rel="stylesheet" type="text/css">
                
                <script src="../jquery/jquery-1.4.2.min.js" type="text/javascript"></script>

                <script src="../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript"></script>
                <script src="../jquery/validationEngine/jquery.validationEngine.js" type="text/javascript"></script>
                <script src="../jquery/validationEngine/jquery.validationEngine-pt.js" type="text/javascript"></script>

                <link href="../jquery/fancybox/fancybox/jquery.fancybox-1.3.4.css" rel="stylesheet" type="text/css">
                <script src="../jquery/fancybox/fancybox/jquery.fancybox-1.3.4.js" type="text/javascript"></script>
                <script src="../jquery/fancybox/fancybox/jquery.fancybox-1.3.4.pack.js" type="text/javascript"></script>
                <script type="text/javascript">
                    $(function(){
                        $("a#anexar").fancybox({
                            'overlayShow'	: true,
                            'transitionIn'	: 'elastic',
                            'transitionOut'	: 'elastic'
                        				
                        });

                        $(".documentos").fancybox({
                            'overlayShow'	: true,
                            'transitionIn'	: 'elastic',
                            'transitionOut'	: 'elastic'
                        				
                        });
                    });
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

                    .style38 {
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
                    }.style40 {font-family: Geneva, Arial, Helvetica, sans-serif}
                    .style41 {
                        font-family: Geneva, Arial, Helvetica, sans-serif;
                        color: #FFFFFF;
                        font-weight: bold;
                    }
                    .style43 {font-family: Arial, Helvetica, sans-serif}
                    .style45 {font-family: Arial, Helvetica, sans-serif; }

                    tr.linha_dois:hover{
                        background-color: #C1C1C1;
                        color: #000;	
                    }

                    tr.linha_um:hover{
                        background-color: #C1C1C1;
                        color: #000;	
                    }

                    .expirado{
                        background-color:#FF9090;
                        color:#000;
                        font-size:10px;
                    }

                    .expirado:hover{
                        background-color:#C1C1C1;
                        color:#000;
                    }

                    .tabela{
                        font-size:10px;

                    }
                    -->
                </style>
                <script language='javascript'>
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
                        if (strCheck.indexOf(key) == -1){
                            return false;  //NÃO E VALIDO
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
                            //Cálculo para casas decimais setadas por parametro
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

                    $(function(){
                        
                        $("#corpoCreate").hide();
                        
                        $('.data_nasc').mask('99/99/9999');
                        $('#dataInicio').mask('99/99/9999');
                        $('#dataFinal').mask('99/99/9999');
                        
                        $('#form1').validationEngine();
                        
                        $('.c_tipo').change( function(){
                            if($(this).val() == 3){
                                $('#dependente').fadeIn();
                            } else {
                                $('#dependente').fadeOut();
                            }
                        });
                        
                        $('.adicionar').click( function() {
                            $('.data_nasc').mask('99/99/9999');
                            var campos = "<div><table style=\"background-color:  #EFEFEF;width:100%;\" class='relacao'><tr height='35'><td class='secao'> Nome:</td><td align='left'><input name='dependente_nome[]' type='text' style='background:#FFFFFF;' onchange='this.value=this.value.toUpperCase()' size='90' /></td></tr><tr height='35'><td class='secao'> Grau de Parentesco:</td><td align='left'><input name='dependente_parentesco[]' type='text'  style='background:#FFFFFF;' onchange='this.value=this.value.toUpperCase()' size='30' /></span></td></tr><tr height='35'><td class='secao'> Data de Nascimento:</td><td align='left'><input name='dependente_nascimento[]' type='text'  style='background:#FFFFFF;' onchange='this.value=this.value.toUpperCase()' size='10' class='data_nasc' /></span></td></tr> <tr height='35'><td colspan='2'>&nbsp;</td></tr><tr><td><a href='#' onclick='$(this).parent().parent().parent().remove(); return false;'>Remover</a</td></tr></table><br></div>";
                            $('#tabela_dependente').append(campos);
                        });
                        
                        $("#btCad").click(function(){
                            $("#conteudo").hide();
                            $("#corpoCreate").show();
                        });
                        
                        $("#btCancelar").click(function(){
                            $("#conteudo").show();
                            $("#corpoCreate").hide();
                            //$("input", "#corpoCreate").val(''); se limpar tudo vai apagar os dados do contratante
                        });
                    });
                </script>
            </head>
            <body class="novaintra">	
                <div id="corpo">
                    <div id="conteudo">
                        <div> <a style="color:#900;text-decoration:underline;margin-bottom:10px;" href="../adm/prestador.php?m=<?= $link_master ?>"> << Voltar </a> </div>
                        
                        
                        <div style="float:right;margin-top:20px;"> <?php include('../reportar_erro.php'); ?>  </div>

                        <img src="../imagens/logomaster<?php echo $row_master['id_master'] ?>.gif"/>

                        <h1>CADASTRO DE PRESTADORES DE SERVI&Ccedil;O</h1>

                        <table width="98%" align="center" border="0" class="grid" cellpadding="0" cellspacing="0">
                            <thead>
                                <tr>
                                    <th colspan="6">EMPRESAS AGUARDANDO TÉRMINO DO CADASTRO</th>
                                </tr>
                                <tr class="titulos" height="50" align="center">
                                    <th>N.</th>
                                    <th>PROCESSO</th>
                                    <th>RAZÃO SOCIAL</th>
                                    <th>VALOR LIMITE</th>
                                    <th>VALOR PAGO</th>
                                    <th>COMPLETAR CADASTRO</th>

                                </tr>
                            </thead>
                            <?php
                            $result_faltacad = mysql_query("SELECT * FROM prestadorservico  WHERE id_regiao = '$regiao' AND prestador_tipo=0 ORDER BY numero");
                            while ($row_faltacad = mysql_fetch_assoc($result_faltacad)) {
                                ?>

                                <tr class="expirado" height="60px">
                                    <?php
                                    echo "<td align='center'>$row_faltacad[id_prestador]</td>";
                                    echo "<td align='center'>$row_faltacad[numero]</td>";
                                    echo "<td>$row_faltacad[c_razao]</td>";
                                    //echo "<td align='center'>".number_format($row_faltacad['valor_limite'],2,',','.')."</td>";
                                    echo "<td align='center'>N/D</td>";

                                    echo "<td align='center'>" . $row_faltacad['valor'] . "</td>";
                                    echo "<td align=\"center\"><a href=editar_prestador.php?m=$link_master&id=3&regiao=$regiao&prestador=$row_faltacad[id_prestador]&compra=$row_faltacad[id_compra]>COMPLETAR CADASTRO</a></td>";
                                    echo "</tr>";
                                }
                                ?>
                        </table>
                        <br>

                        <?php
                       
                        while ($row_projeto = mysql_fetch_assoc($qr_projeto)):
                            
                            $result_empresas = mysql_query("SELECT * FROM prestadorservico  WHERE id_regiao = '$regiao' AND id_projeto = '$row_projeto[id_projeto]' AND prestador_tipo<>0 ORDER BY numero");
                            $num_empresas = mysql_num_rows($result_empresas);
                            print_r($num_empresas);
                            
                            if (empty($num_empresas))
                                continue;
                            ?>

                            <table width="98%" border="0" align="center" style="border-bottom-color:#fff; border-left-color:#fff; border-right-color:#fff; border-top-color:#fff; border-color:#fff; size:1px">
                                <tr>
                                    <td colspan="9" align="center" bgcolor="#979797" class="titulo_tabela1">EMPRESAS CADASTRADAS DO PROJETO: <?= $row_projeto['id_projeto'] . ' - ' . $row_projeto['nome']; ?></td>
                                </tr>
                                <tr class="titulos">
                                    <td width="8%" bgcolor="#CCCCCC"><div align="center" class="valor">N.</div>
                                    <td width="8%" bgcolor="#CCCCCC"><div align="center" class="valor">PROCESSO</div></td>
                                    <td width="36%" bgcolor="#CCCCCC"><div align="center" class="valor">RAZ&Atilde;O SOCIAL</div></td>
                                    <td width="25%" bgcolor="#CCCCCC"><div align="center" class="valor">DOCUMENTOS</div></td>
                                    <td width="8%" bgcolor="#CCCCCC"><div align="center" class="valor">STATUS</div></td>
                                    <td width="8%" bgcolor="#CCCCCC"><div align="center" class="valor">DECLARAÇÃO DE DEPENDENTES</br> (Somente p/ Pessoa Física)</div></td>
                                    <td bgcolor="#CCCCCC">VALOR LIMITE</td>
                                    <td>VALOR PAGO</td>
                                    <td width="17%" bgcolor="#CCCCCC"><div align="center" class="total">AÇÕES</div></td>
                                </tr>
                                <?php
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

                                    unset($expirado);

                                    ///VERIFICA os documento copm prazo de validade expirados
                                    $qr_documentos = mysql_query("SELECT * FROM prestador_tipo_doc;") or die(mysql_error());
                                    while ($row_documentos = mysql_fetch_assoc($qr_documentos)):

                                        $qr_anexo = mysql_query("SELECT * FROM prestador_documentos WHERE prestador_tipo_doc_id = '$row_documentos[prestador_tipo_doc_id]' 
																									AND id_prestador = '$row_empresas[0]' ORDER BY data_vencimento DESC") or die(mysql_error());
                                        if (mysql_num_rows($qr_anexo) != 0) {
                                            $row_anexo = mysql_fetch_assoc($qr_anexo);

                                            //VERIFICA VENCIMENTO
                                            list($vencimento_ano, $vencimento_mes, $vencimento_dia) = explode('-', $row_anexo['data_vencimento']);
                                            $data_vencimento = mktime(0, 0, 0, $vencimento_mes, $vencimento_dia, $vencimento_ano);
                                            $data_hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

                                            if ($data_vencimento < $data_hoje) {
                                                $expirado = 'class="expirado"';
                                            }
                                        }

                                    endwhile;
                                    /////////////////////////////////////////////////////////

                                    $class = ($alternateColor++ % 2 == 0 ) ? 'class="linha_um"' : 'class="linha_dois"';
                                    ?>
                                    <tr  <?php
                                            if ($expirado) {
                                                echo $expirado;
                                            } else {
                                                echo $class;
                                            }
                                    ?>>

                                        <td><div align='center'><?php echo $row_empresas['id_prestador']; ?></div></td>
                                        <td align='center'>
                                            <a href='impressao.php?prestador=<?php echo $row_empresas[0]; ?>&id=1&regiao=<?php echo $regiao; ?>&projeto=<?php echo $row_projeto['id_projeto']; ?>'><?php echo $row_empresas['numero']; ?></a>
                                        </td>
                                        <td><?php echo $row_empresas['c_razao']; ?></td>
                                        <td>
                                            <table width="100%" class="tabela">
                                                <?php
                                                $qr_documentos = mysql_query("SELECT * FROM prestador_tipo_doc ORDER BY ordem;") or die(mysql_error());
                                                while ($row_documentos = mysql_fetch_assoc($qr_documentos)):

                                                    $qr_anexo = mysql_query("SELECT * FROM prestador_documentos WHERE prestador_tipo_doc_id = '$row_documentos[prestador_tipo_doc_id]' 
																			AND id_prestador = '$row_empresas[0]' ORDER BY data_vencimento DESC") or die(mysql_error());


                                                    if (mysql_num_rows($qr_anexo) == 0) {

                                                        echo '<tr>
								<td><span style="">' . $row_documentos['prestador_tipo_doc_nome'] . '</span></td>
								<td>	
									 <a href="anexar_documento.php?tipo=' . $row_documentos['prestador_tipo_doc_id'] . '&id_prestador=' . $row_empresas[0] . '&regiao=' . $regiao . '&master=' . $link_master . '" id="anexar" title="Anexar ' . $row_documentos['prestador_tipo_doc_nome'] . '">  <img src="../img_menu_principal/anexar.png" width="20" heigth="20"/> </a> <br>
								</td>
								
								
								';
                                                    } else {


                                                        $row_anexo = mysql_fetch_assoc($qr_anexo);

                                                        //VERIFICA VENCIMENTO
                                                        list($vencimento_ano, $vencimento_mes, $vencimento_dia) = explode('-', $row_anexo['data_vencimento']);
                                                        $data_vencimento = mktime(0, 0, 0, $vencimento_mes, $vencimento_dia, $vencimento_ano);
                                                        $data_hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

                                                        if ($data_vencimento < $data_hoje) {
                                                            echo '<tr>
										<td><span style="color:#FF4848; font-weight:bold;">' . $row_documentos['prestador_tipo_doc_nome'] . '</td>
										<td> <a href="anexar_documento.php?tipo=' . $row_documentos['prestador_tipo_doc_id'] . '&id_prestador=' . $row_empresas[0] . '&regiao=' . $regiao . '&master=' . $link_master . '" id="anexar" style="color:#FF4848;" title="Renovar ' . $row_documentos['prestador_tipo_doc_nome'] . '">  <img src="../img_menu_principal/renovar.png" width="20" heigth="20"/> </a></span> 
										</td>
										<td><a href="action.visualiza_documentos.php?prestador=' . $row_empresas[0] . '&tp=' . $row_documentos['prestador_tipo_doc_id'] . '" class="documentos"><img src="../imagens/ver_anexo.gif" width="20" height="20"/></a></td>
								   </tr>';
                                                        } else {



                                                            echo '<tr>
									<td><span style=""><strong>' . $row_documentos['prestador_tipo_doc_nome'] . ' </strong></span> 
									</td>
										<td><a href="action.visualiza_documentos.php?prestador=' . $row_empresas[0] . '&tp=' . $row_documentos['prestador_tipo_doc_id'] . '" class="documentos"><img src="../imagens/ver_anexo.gif" width="20" height="20"/></a></td>';

                                                            /* <td><a href="ver_documentos.php?id='.$row_anexo['prestador_documento_id'].'" id="anexar" title="Visualizar '.$row_documentos['prestador_tipo_doc_nome'].'"> <img src="../img_menu_principal/ver.png" width="20" heigth="20"/> </a> 
                                                              </td>
                                                              </tr>'; */
                                                        }
                                                    }
                                                endwhile;
                                                ?>
                                            </table>
                                        </td>
                                        <td  align=\"center\"><?php echo $status; ?></td>

                                        <?php
                                        if ($row_empresas['prestador_tipo'] == '3') {

                                            $qr_dependente = mysql_query("SELECT * FROM prestador_dependente WHERE prestador_id = '$row_empresas[0]'  AND  prestador_dep_status = '1'") or die(mysql_error());
                                            $verifica_dep = mysql_num_rows($qr_dependente);

                                            if ($verifica_dep != 0) {
                                                echo "<td align=\"center\"><a href=\"pdf_dependentes.php?i&prestador=$row_empresas[0]\" target=\"_blank\">Gerar</a></td>";
                                            } else {

                                                echo "<td align=\"center\">&nbsp;</td>";
                                            }
                                        } else {

                                            echo "<td>&nbsp;</td>";
                                        }


                                        $query_pg = mysql_query("SELECT SUM(REPLACE(saida.valor,',','.')) as total FROM
 		prestador_pg INNER JOIN saida ON prestador_pg.id_saida = saida.id_saida WHERE prestador_pg.status_reg = '1' AND prestador_pg.id_prestador = '$row_empresas[0]' AND saida.status != '0'");
                                        $row_total = mysql_fetch_assoc($query_pg);

                                        print "<td align=\"center\">" . number_format($row_empresas['valor_limite'], 2, ',', '.') . "</td>";


                                        print "<td align=\"center\">" . number_format($row_total['total'], 2, ',', '.') . "</td>";


                                        print "<td align=\"center\">
                                                    <a href=visualizar_prestador.php?m=$link_master&id=3&regiao=$regiao&prestador=$row_empresas[0]&compra=$row_empresas[id_compra]>Visualizar</a><br/><br/>
                                                    <a href=editar_prestador.php?m=$link_master&id=3&regiao=$regiao&prestador=$row_empresas[0]&compra=$row_empresas[id_compra]>Editar</a><br/><br/>
                                                    <a href='duplicar_prestador.php?prestador=$row_empresas[0]'>Duplicar</a>
                                                </td>
                                            </tr>";
                                    }
                                    ?>
                            </table>
                            <br>
                        <?php endwhile; ?>

                        <br>
                        <a href="../processo/relatorioprestadores.php?regiao=<?= $regiao ?>" target="_blank">
                            <img src="../imagens/verbolsista.gif" alt="abertura" width="190" height="31" border="0"></a>
                        </a>
                        &nbsp;
                        <a href="#">
                            <img src="../imagens/castrobolsista.gif" width="190" height="31" border="0" id="btCad" ></a> 
                        <br>
                    </div>

                    <div id="corpoCreate">

                        <form action="prestadorservico.php" name="form1" id="form1" method="post" onSubmit="return validaForm()">

                            <table id="cadastro"  class="relacao">
                                <tr>
                                    <td colspan="6">
                                        <br/>
                                        <div id="message-box" class="message-yellow">
                                            <p>Atenção, os campos com a sigla <span style="color:red">I.P.C</span> são campos importantes para a prestação de contas, favor preencher corretamente!</p>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="titulo_tabela1">
                                    <td height="21" colspan="6" > DADOS DO PROJETO </td>
                                </tr>
                                <tr>
                                    <td width="19%" height="30">Projeto:</td>
                                    <td width="81%" height="30" align="left">
                                        <?php
                                        $result_projeto = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$regiao'  AND (status_reg = '1' OR status_reg = '0')");
                                        print "<select name='projeto'>";
                                        while ($row_projeto = mysql_fetch_array($result_projeto)) {
                                            print "<option value='{$row_projeto['id_projeto']}'>{$row_projeto['id_projeto']} - {$row_projeto['nome']}</option>";
                                        }
                                        print "</select>";
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="19%" height="30">Data Início:</td>
                                    <td width="81%" height="30" align="left">
                                        Data Início: <input type="text" name="dataInicio" id="dataInicio" /><span style="color:red">I.P.C</span>
                                    </td>
                                    <td>
                                        Data Término:
                                    </td>
                                    <td colspan="4">
                                        <input type="text" name="dataFinal" id="dataFinal" /><span style="color:red">I.P.C</span>
                                    </td>
                                </tr>

                                <tr>
                                    <td height="31" colspan="6"  class="titulo_tabela1">DADOS DO CONTRATANTE</td>
                                </tr>
                                <tr>
                                    <td class="secao"> Contratante:</td>
                                    <td align="left" colspan="5">
                                        <input name="contratante" type="text" id="contratante" 
                                               value="<?= $row_master['razao'] ?>" size="90"  />
                                    </td>
                                </tr>
                                <tr>
                                    <td height="35" class="secao_nova">Endere&ccedil;o:</td>
                                    <td height="35" colspan="5" align="left">
                                        <input name="endereco" type="text" id="endereco" size="90" 
                                               onfocus="document.all.endereco.style.background='#CCFFCC'" onBlur="document.all.endereco.style.background='#FFFFFF'" style="background:#FFFFFF;" onChange="this.value=this.value.toUpperCase()"  class="validate[required]" value="<?= $row_master['endereco'] ?>"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="35" class="secao_nova">CNPJ:</td>
                                    <td height="35" colspan="5" align="left">
                                        <input name="cnpj" type="text" id="cnpj" style="background:#FFFFFF; text-transform:uppercase;"
                                               onfocus="document.all.cnpj.style.background='#CCFFCC'"  value="<?= $row_master['cnpj'] ?>"
                                               onblur="document.all.cnpj.style.background='#FFFFFF'"
                                               onkeypress="formatar('##.###.###/####-##', this)" 
                                               onkeyup="pula(18,this.id,c_fantasia.id)" size="20" maxlength="18" />
                                    </td>
                                </tr>
                                <tr>
                                    <td height="35"   class="secao">Responsavel:</td>
                                    <td align="left">
                                        <input name="responsavel" type="text" id="responsavel" value="<?= $row_master['responsavel'] ?>" size="40" />
                                    </td>
                                    <td >Estado civil:</td>
                                    <td colspan="3" align="left"> <input name="civil" type="text" id="civil" value="<?= $row_master['civil'] ?>" size="20" />  </td>
                                </tr>
                                <tr>
                                    <td height="35"  class="secao">Nacionalidade:</td>
                                    <td align="left">
                                        <input name="nacionalidade" type="text" id="nacionalidade" value="<?= $row_master['nacionalidade'] ?>" size="40" />
                                    </td>
                                    <td>
                                        Forma&ccedil;&atilde;o: 
                                    </td>
                                    <td colspan="3 " align="left">
                                        <input name="formacao" type="text" id="formacao" value="<?= $row_master['formacao'] ?>" size="20" />
                                    </td>
                                </tr>
                                <tr>
                                    <td height="35"  class="secao_nova">RG:</td>
                                    <td align="left">  <input name="rg" type="text" id="rg" size="20" maxlength="14" value="<?= $row_master['rg'] ?>"/>  </td>
                                    <td>CPF:</td>
                                    <td colspan="3" align="left"> <input name="cpf" type="text" id="cpf" value="<?= $row_master['cpf'] ?>" size="20" /></td>
                                </tr>
                                <tr>
                                    <td height="31" colspan="6" class="titulo_tabela1">DADOS DA EMPRESA CONTRATADA</td>
                                </tr>
                                <tr>  	

                                    <td class="secao">Tipo: </td>
                                    <td colspan="5" align="left" > <input name="c_tipo" type="radio" class="c_tipo" size="38"   value="1"   onfocus="document.all.c_site.style.background='#CCFFCC'"     onblur="document.all.c_site.style.background='#FFFFFF'"
                                                                          style="background:#FFFFFF; text-transform:lowercase;" /> 
                                        <strong>1</strong> - Pessoa Jurídica<br>


                                        <input name="c_tipo" type="radio" class="c_tipo" size="38"   value="2"   onfocus="document.all.c_site.style.background='#CCFFCC'"     onblur="document.all.c_site.style.background='#FFFFFF'"
                                               style="background:#FFFFFF; text-transform:lowercase;" /> <strong>2</strong> - Pessoa Jurídica - Cooperativa</br>


                                        <input name="c_tipo" type="radio" class="c_tipo" size="38"   value="3"   onfocus="document.all.c_site.style.background='#CCFFCC'"     onblur="document.all.c_site.style.background='#FFFFFF'"
                                               style="background:#FFFFFF; text-transform:lowercase;" /> <strong>3</strong> - Pessoa Física</br>


                                        <input name="c_tipo" type="radio" class="c_tipo" size="38"   value="4"   onfocus="document.all.c_site.style.background='#CCFFCC'"     onblur="document.all.c_site.style.background='#FFFFFF'"
                                               style="background:#FFFFFF; text-transform:lowercase;" /><strong>4</strong> - Pessoa Jurídica - Prestador de Serviço</br>


                                        <!---ADICIONADO  MAIS DOIS TIPOS DE PESSOA JURÍDICA  DIA 25/08/2011 ---->




                                        <input name="c_tipo" type="radio" class="c_tipo" size="38"   value="5"   onfocus="document.all.c_site.style.background='#CCFFCC'"     onblur="document.all.c_site.style.background='#FFFFFF'"
                                               style="background:#FFFFFF; text-transform:lowercase;" /><strong>5</strong> - Pessoa Jurídica - Administradora</br>


                                        <input name="c_tipo" type="radio" class="c_tipo" size="38"   value="6"   onfocus="document.all.c_site.style.background='#CCFFCC'"     onblur="document.all.c_site.style.background='#FFFFFF'"
                                               style="background:#FFFFFF; text-transform:lowercase; " /><strong>6</strong> - Pessoa Jurídica - Publicidade</br>



                                        <input name="c_tipo" type="radio" class="c_tipo" size="38"   value="7"   onfocus="document.all.c_site.style.background='#CCFFCC'"     
                                               onblur="document.all.c_site.style.background='#FFFFFF'"      style="background:#FFFFFF; text-transform:lowercase; " /><strong>7</strong> - Pessoa Jurídica Sem Retenção</br>
                                        </span>



                                    </td>

                                </tr>

                                <tr id="dependente" style="display:none;">  
                                    <td valign="top">

                                        <span class="titulos" style="display:block; text-align:center;"><strong>Dados do(s) Dependente(s): <br><span class="adicionar" style="cursor:pointer"><img src="../imagens/adicionar_dep.gif" width="36" height="26" title="Adicionar Dependente."/></span> </strong> </span>   
                                    </td>	                       
                                    <td  colspan="6" id="tabela_dependente">        




                                        <div id="tabela_dependente" style="background-color: #DEF;padding-top:10px;">
                                            <table style="background-color:  #EFEFEF;width:100%;" class="relacao" >
                                                <tr height='35'>
                                                    <td  class="secao">Nome:</td>
                                                    <td align="left"> <input name='dependente_nome[]' type='text' style='background:#FFFFFF;' onchange='this.value=this.value.toUpperCase()' size='90' /></td>
                                                </tr>

                                                <tr height='35'>
                                                    <td class="secao">Grau de Parentesco: </td>
                                                    <td align="left"><input name='dependente_parentesco[]' type='text'  style='background:#FFFFFF;' onchange='this.value=this.value.toUpperCase()' size='30' /></td>
                                                </tr>

                                                <tr height='35'>
                                                    <td class="secao">Data de Nascimento:</td>
                                                    <td align="left"> <input name='dependente_nascimento[]' type='text'  style='background:#FFFFFF;' onchange='this.value=this.value.toUpperCase()' size='10'  class='data_nasc'/> </td>
                                                </tr> 

                                                <tr height='35'><td colspan='2'>&nbsp;</td></tr>



                                            </table>

                                        </div>

                                    </td>



                                </tr>
                                <tr>
                                    <td>Existe Contrato:</td>
                                    <td colspan="5" align="left">
                                        <input type="radio" name="prestacao_contas" checked="" value="1"/>Sim
                                        <input type="radio" name="prestacao_contas" value="0"/>Não
                                        <span style="color:red">I.P.C</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="35" >Nome Fantasia:</td>
                                    <td colspan="5" align="left">
                                        <input name="c_fantasia" type="text" id="c_fantasia" style="background:#FFFFFF;" 
                                               onfocus="document.all.c_fantasia.style.background='#CCFFCC'" 
                                               onblur="document.all.c_fantasia.style.background='#FFFFFF'" 
                                               onchange="this.value=this.value.toUpperCase()" size="90" /> <span style="color:red">I.P.C</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="35" >Raz&atilde;o Social:</td>
                                    <td colspan="5" align="left">
                                        <input name="c_razao" type="text" id="c_razao" size="90" 
                                               onfocus="document.all.c_razao.style.background='#CCFFCC'" 
                                               onblur="document.all.c_razao.style.background='#FFFFFF'" 
                                               style="background:#FFFFFF;" onChange="this.value=this.value.toUpperCase()" /> <span style="color:red">I.P.C</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="35" >Endere&ccedil;o:?</td>
                                    <td colspan="5" align="left">
                                        <input name="c_endereco" type="text" id="c_endereco" size="90" 
                                               onfocus="document.all.c_endereco.style.background='#CCFFCC'" 
                                               onblur="document.all.c_endereco.style.background='#FFFFFF'" 
                                               style="background:#FFFFFF;" onChange="this.value=this.value.toUpperCase()" />
                                    </td>
                                </tr>
                                <tr>
                                    <td height="35" >CNPJ:</td>  
                                    <td align="left">
                                        <input name="c_cnpj" type="text" id="c_cnpj" 
                                               style="background:#FFFFFF; text-transform:uppercase;"
                                               onfocus="document.all.c_cnpj.style.background='#CCFFCC'" 
                                               onblur="document.all.c_cnpj.style.background='#FFFFFF'" 
                                               onkeyup="pula(18,this.id,c_ie.id)"
                                               onkeypress="formatar('##.###.###/####-##', this)" size="18" maxlength="18" /> <span style="color:red">I.P.C</span>
                                    </td>
                                    <td>IE:</td>
                                    <td align="left">  <input name="c_ie" type="text" id="c_ie" size="15" onFocus="document.all.c_ie.style.background='#CCFFCC'" onBlur="document.all.c_ie.style.background='#FFFFFF'" style="background:#FFFFFF;" /></td>
                                    <td>CCM:</td>
                                    <td align="left"><input name="c_im" type="text" id="c_im" size="15" onFocus="document.all.c_im.style.background='#CCFFCC'" onBlur="document.all.c_im.style.background='#FFFFFF'" style="background:#FFFFFF;" /></td>
                                </tr>
                                <tr>
                                    <td height="35" >Telefone:</td>
                                    <td  align="left">
                                        <input name='c_tel' type='text' id='c_tel' size='12' 
                                               onkeypress="return(TelefoneFormat(this,event))" 
                                               onkeyup="pula(13,this.id,c_fax.id)" 
                                               onfocus="document.all.c_tel.style.background='#CCFFCC'" 
                                               onblur="document.all.c_tel.style.background='#FFFFFF'" 
                                               style="background:#FFFFFF;" />
                                    </td>
                                    <td class="secao">Fax:</td>
                                    <td align="left">
                                        <input name="c_fax" type="text" id="c_fax" size="12" 
                                               onkeypress="return(TelefoneFormat(this,event))" 
                                               onkeyup="pula(13,this.id,c_email.id)" 
                                               onfocus="document.all.c_fax.style.background='#CCFFCC'" 
                                               onblur="document.all.c_fax.style.background='#FFFFFF'" 
                                               style="background:#FFFFFF;" />
                                    </td>
                                    <td class="secao"> E-mail: </td>
                                    <td align="left"> <input name="c_email" type="text" id="c_email" size="25" 
                                                             onfocus="document.all.c_email.style.background='#CCFFCC'" 
                                                             onblur="document.all.c_email.style.background='#FFFFFF'" 
                                                             style="background:#FFFFFF; text-transform:lowercase;" />
                                    </td>


                                </tr>
                                <tr>
                                    <td height="35" class="secao">Responsavel:</td>
                                    <td align="left">
                                        <input name="c_responsavel" type="text" id="c_responsavel" size="40"
                                               style="background:#FFFFFF;" 
                                               onfocus="document.all.c_responsavel.style.background='#CCFFCC'" 
                                               onblur="document.all.c_responsavel.style.background='#FFFFFF'" 
                                               onchange="this.value=this.value.toUpperCase()" />
                                    </td>
                                    <td class="secao">Estado civil:</td>
                                    <td colspan="3" align="left">
                                        <input name="c_civil" type="text" id="c_civil" size="20"
                                               style="background:#FFFFFF;" 
                                               onfocus="document.all.c_civil.style.background='#CCFFCC'" 
                                               onblur="document.all.c_civil.style.background='#FFFFFF'" 
                                               onchange="this.value=this.value.toUpperCase()" />
                                    </td>
                                </tr>
                                <tr>
                                    <td height="35" class="secao">Nacionalidade:</td>
                                    <td align="left">
                                        <input name="c_nacionalidade" type="text" id="c_nacionalidade" size="40" 
                                               style="background:#FFFFFF;" 
                                               onfocus="document.all.c_nacionalidade.style.background='#CCFFCC'" 
                                               onblur="document.all.c_nacionalidade.style.background='#FFFFFF'" 
                                               onchange="this.value=this.value.toUpperCase()" />
                                    </td>
                                    <td class="secao">Forma&ccedil;&atilde;o: </td>
                                    <td  colspan="3" align="left">
                                        <input name="c_formacao" type="text" id="c_formacao" size="20" 
                                               style="background:#FFFFFF;" 
                                               onfocus="document.all.c_formacao.style.background='#CCFFCC'" 
                                               onblur="document.all.c_formacao.style.background='#FFFFFF'" 
                                               onchange="this.value=this.value.toUpperCase()" />
                                    </td>
                                </tr>
                                <tr>
                                    <td height="35" class="secao">RG:</td>
                                    <td align="left">
                                        <input name="c_rg" type="text" id="c_rg" 
                                               onkeypress="formatar('##.###.###-##', this)" size="20" maxlength="14" 
                                               onfocus="document.all.c_rg.style.background='#CCFFCC'" 
                                               onblur="document.all.c_rg.style.background='#FFFFFF'" 
                                               style="background:#FFFFFF;" />
                                    </td>
                                    <td class="secao">CPF:</td>
                                    <td  colspan="3" align="left">
                                        <input name="c_cpf" type="text" id="c_cpf" 
                                               onkeypress="formatar('###.###.###-##', this)" size="20" maxlength="14" 
                                               onkeyup="pula(14,this.id,c_email2.id)" 
                                               onfocus="document.all.c_cpf.style.background='#CCFFCC'" 
                                               onblur="document.all.c_cpf.style.background='#FFFFFF'" 
                                               style="background:#FFFFFF;" />
                                    </td>
                                </tr>
                                <tr>
                                    <td height="35" class="secao">E-mail: </td>
                                    <td align="left"> 
                                        <input name="c_email2" type="text" id="c_email2" size="30" 
                                               onfocus="document.all.c_email2.style.background='#CCFFCC'" 
                                               onblur="document.all.c_email2.style.background='#FFFFFF'" 
                                               style="background:#FFFFFF; text-transform:lowercase;" />
                                    </td>
                                    <td class="secao">Site: </td>
                                    <td  colspan="4" align="left">
                                        <input name="c_site" type="text" id="c_site" size="38" 
                                               onfocus="document.all.c_site.style.background='#CCFFCC'" 
                                               onblur="document.all.c_site.style.background='#FFFFFF'"
                                               style="background:#FFFFFF; text-transform:lowercase;" />
                                    </td>
                                </tr>

                                <tr>
                                    <td height="25" class="titulo_tabela1"  colspan="6">DADOS DA PESSOA DE  CONTATO NA CONTRATADA</td>
                                </tr>
                                <tr>
                                    <td height="35" class="secao">Nome Completo:</td>
                                    <td  colspan="5">
                                        <input name="co_responsavel" type="text" id="co_responsavel" size="27"
                                               style="background:#FFFFFF;" 
                                               onfocus="document.all.co_responsavel.style.background='#CCFFCC'" 
                                               onblur="document.all.co_responsavel.style.background='#FFFFFF'" 
                                               onchange="this.value=this.value.toUpperCase()" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="secao">Telefone:</td>
                                    <td align="left">
                                        <input name='co_tel' type='text' id='co_tel' size='12' 
                                               onkeypress="return(TelefoneFormat(this,event))" 
                                               onkeyup="pula(13,this.id,co_fax.id)" 
                                               onfocus="document.all.co_tel.style.background='#CCFFCC'" 
                                               onblur="document.all.co_tel.style.background='#FFFFFF'" 
                                               style="background:#FFFFFF;" />
                                    </td>
                                    <td class="secao"> Fax:</td>
                                    <td  colspan="3" align="left">
                                        <input name="co_fax" type="text" id="co_fax" size="12" 
                                               onkeypress="return(TelefoneFormat(this,event))" 
                                               onkeyup="pula(13,this.id,co_civil.id)" 
                                               onfocus="document.all.co_fax.style.background='#CCFFCC'" 
                                               onblur="document.all.co_fax.style.background ='#FFFFFF'" 
                                               style="background:#FFFFFF;" />
                                    </td>
                                </tr>
                                <tr>

                                    <td height="35" class="secao"> Email: </td>
                                    <td  colspan="5">
                                        <input name="co_email" type="text" id="co_email" size="30" 
                                               onfocus="document.all.co_email.style.background='#CCFFCC'" 
                                               onblur="document.all.co_email.style.background='#FFFFFF'" 
                                               style="background:#FFFFFF; text-transform:lowercase;" />
                                    </td>
                                </tr>
                                <tr>
                                    <td height="35"  class="secao">Estado civil:</td>
                                    <td align="left">
                                        <input name="co_civil" type="text" id="co_civil" size="20"
                                               style="background:#FFFFFF;" 
                                               onfocus="document.all.co_civil.style.background='#CCFFCC'" 
                                               onblur="document.all.co_civil.style.background='#FFFFFF'" 
                                               onchange="this.value=this.value.toUpperCase()" />
                                    </td>
                                    <td class="secao"> Nacionalidade:</td>
                                    <td  colspan="4" align="left">
                                        <input name="co_nacionalidade" type="text" id="co_nacionalidade" size="27" 
                                               style="background:#FFFFFF;" 
                                               onfocus="document.all.co_nacionalidade.style.background='#CCFFCC'" 
                                               onblur="document.all.co_nacionalidade.style.background='#FFFFFF'" 
                                               onchange="this.value=this.value.toUpperCase()" />
                                    </td>
                                <tr>
                                    <td height="35"  class="secao"> Data de Nascimento:</td>
                                    <td	colspan="5" align="left">
                                        <input name="co_data_nasc" type="text" id="co_data_nasc" size="27" 
                                               style="background:#FFFFFF;" 
                                               onfocus="document.all.co_data_nasc.style.background='#CCFFCC'" 
                                               onblur="document.all.co_data_nasc.style.background='#FFFFFF'" 
                                               onchange="this.value=this.value.toUpperCase()"  class='data_nasc' />
                                    </td>

                                </tr>




                                <tr>
                                    <td height="25" colspan="6" bgcolor="#C9C9C9">Sócio 1</td>
                                </tr>
                                <tr>
                                    <td height="35"  class="secao">Nome Completo:</td>
                                    <td align="left">
                                        <input name="co_responsavel_socio1" type="text" id="co_responsavel_socio1" size="27"
                                               style="background:#FFFFFF;" 
                                               onfocus="document.all.co_responsavel_socio1.style.background='#CCFFCC'" 
                                               onblur="document.all.co_responsavel_socio1.style.background='#FFFFFF'" 
                                               onchange="this.value=this.value.toUpperCase()" />
                                    </td>
                                    <td  class="secao">Telefone:</td>
                                    <td align="left">
                                        <input name='co_tel_socio1' type='text' id='co_tel_socio1' size='12' 
                                               onkeypress="return(TelefoneFormat(this,event))" 
                                               onkeyup="pula(13,this.id,co_fax.id)" 
                                               onfocus="document.all.co_tel_socio1.style.background='#CCFFCC'" 
                                               onblur="document.all.co_tel_socio1.style.background='#FFFFFF'" 
                                               style="background:#FFFFFF;" />
                                    </td>
                                    <td  class="secao">Fax:</td>
                                    <td align="left"> 
                                        <input name="co_fax_socio1" type="text" id="co_fax_socio1" size="12" 
                                               onkeypress="return(TelefoneFormat(this,event))" 
                                               onkeyup="pula(13,this.id,co_civil.id)" 
                                               onfocus="document.all.co_fax_socio1.style.background='#CCFFCC'" 
                                               onblur="document.all.co_fax_socio1.style.background='#FFFFFF'" 
                                               style="background:#FFFFFF;" />
                                    </td>
                                </tr>
                                <tr>
                                    <td height="35"  class="secao">Email: </td>
                                    <td colspan="5" align="left">
                                        <input name="co_email_socio1" type="text" id="co_email_socio1" size="30" 
                                               onfocus="document.all.co_email_socio1.style.background='#CCFFCC'" 
                                               onblur="document.all.co_email_socio1.style.background='#FFFFFF'" 
                                               style="background:#FFFFFF; text-transform:lowercase;" />
                                    </td>
                                </tr>
                                <tr>
                                    <td height="35"  class="secao">Estado civil:</td>
                                    <td  align="left">
                                        <input name="co_civil_socio1" type="text" id="co_civil_socio1" size="20"
                                               style="background:#FFFFFF;" 
                                               onfocus="document.all.co_civil_socio1.style.background='#CCFFCC'" 
                                               onblur="document.all.co_civil_socio1.style.background='#FFFFFF'" 
                                               onchange="this.value=this.value.toUpperCase()" />
                                    </td>
                                    <td  class="secao"> Nacionalidade:</td>
                                    <td colspan="3" align="left"> <input name="co_nacionalidade_socio1" type="text" id="co_nacionalidade_socio1" size="27" 
                                                                         style="background:#FFFFFF;" 
                                                                         onfocus="document.all.co_nacionalidade_socio1.style.background='#CCFFCC'" 
                                                                         onblur="document.all.co_nacionalidade_socio1.style.background='#FFFFFF'" 
                                                                         onchange="this.value=this.value.toUpperCase()" />
                                    </td>
                                <tr>
                                    <td height="35"  class="secao"> Data de Nascimento:</td>
                                    <td colspan="5" align="left">
                                        <input name="co_data_nasc_socio1" type="text" id="co_data_nasc_socio1" size="27" 
                                               style="background:#FFFFFF;" 
                                               onfocus="document.all.co_data_nasc_socio1.style.background='#CCFFCC'" 
                                               onblur="document.all.co_data_nasc_socio1.style.background='#FFFFFF'" 
                                               onchange="this.value=this.value.toUpperCase()"  class='data_nasc' />
                                    </td>
                                </tr>

                                <tr>
                                    <td height="35"  class="secao">Município: </td>
                                    <td colspan="5" align="left">
                                        <input name="co_municipio_socio1" type="text" id="co_municipio_socio1" size="30" 
                                               onfocus="document.all.co_municipio_socio1.style.background='#CCFFCC'" 
                                               onblur="document.all.co_municipio_socio1.style.background='#FFFFFF'" 
                                               style="background:#FFFFFF; text-transform:lowercase;" /></td>
                                </tr>




                                <tr>
                                    <td height="25" colspan="6" bgcolor="#C9C9C9">Sócio 2</span></div></td>
                                </tr>
                                <tr>
                                    <td height="35"  class="secao">Nome Completo:</td>
                                    <td align="left">
                                        <input name="co_responsavel_socio2" type="text" id="co_responsavel_socio2" size="27"
                                               style="background:#FFFFFF;" 
                                               onfocus="document.all.co_responsavel_socio2.style.background='#CCFFCC'" 
                                               onblur="document.all.co_responsavel_socio2.style.background='#FFFFFF'" 
                                               onchange="this.value=this.value.toUpperCase()" />
                                    </td>
                                    <td  class="secao">Telefone:</td>
                                    <td   align="left">
                                        <input name='co_tel_socio2' type='text' id='co_tel_socio2' size='12' 
                                               onkeypress="return(TelefoneFormat(this,event))" 
                                               onkeyup="pula(13,this.id,co_fax.id)" 
                                               onfocus="document.all.co_tel_socio2.style.background='#CCFFCC'" 
                                               onblur="document.all.co_tel_socio2.style.background='#FFFFFF'" 
                                               style="background:#FFFFFF;" />
                                    </td>
                                    <td class="secao">Fax:</td>
                                    <td align="left">
                                        <input name="co_fax_socio2" type="text" id="co_fax_socio2" size="12" 
                                               onkeypress="return(TelefoneFormat(this,event))" 
                                               onkeyup="pula(13,this.id,co_civil.id)" 
                                               onfocus="document.all.co_fax_socio2.style.background='#CCFFCC'" 
                                               onblur="document.all.co_fax_socio2.style.background='#FFFFFF'" 
                                               style="background:#FFFFFF;" />
                                    </td>
                                </tr>
                                <tr>
                                    <td height="35" class="secao" >Estado civil:</td>
                                    <td align="left">
                                        <input name="co_civil_socio2" type="text" id="co_civil_socio2" size="20"
                                               style="background:#FFFFFF;" 
                                               onfocus="document.all.co_civil_socio2.style.background='#CCFFCC'" 
                                               onblur="document.all.co_civil_socio2.style.background='#FFFFFF'" 
                                               onchange="this.value=this.value.toUpperCase()" />
                                    </td>
                                    <td class="secao">Nacionalidade:</td>
                                    <td colspan="5" align="left">
                                        <input name="co_nacionalidade_socio2" type="text" id="co_nacionalidade_socio2" size="27" 
                                               style="background:#FFFFFF;" 
                                               onfocus="document.all.co_nacionalidade_socio2.style.background='#CCFFCC'" 
                                               onblur="document.all.co_nacionalidade_socio2.style.background='#FFFFFF'" 
                                               onchange="this.value=this.value.toUpperCase()" />

                                    </td>
                                <tr>
                                    <td height="35" class="secao" > Data de Nascimento:</td>
                                    <td colspan="5" align="left">
                                        <input name="co_data_nasc_socio2" type="text" id="co_data_nasc_socio2" size="27" 
                                               style="background:#FFFFFF;" 
                                               onfocus="document.all.co_data_nasc_socio2.style.background='#CCFFCC'" 
                                               onblur="document.all.co_data_nasc_socio2.style.background='#FFFFFF'" 
                                               onchange="this.value=this.value.toUpperCase()"  class='data_nasc' />
                                    </td>
                                </tr>

                                <tr>
                                    <td height="35"  class="secao">Email: </td>
                                    <td colspan="5" align="left">
                                        <input name="co_email_socio2" type="text" id="co_email_socio2" size="30" 
                                               onfocus="document.all.co_email_socio2.style.background='#CCFFCC'" 
                                               onblur="document.all.co_email_socio2.style.background='#FFFFFF'" 
                                               style="background:#FFFFFF; text-transform:lowercase;" />
                                    </td>
                                </tr>
                                <tr>
                                    <td height="35" class="secao" >Município:</td>
                                    <td colspan="5" align="left"> 
                                        <input name="co_municipio_socio2" type="text" id="co_municipio_socio2" size="30" 
                                               onfocus="document.all.co_municipio_socio2.style.background='#CCFFCC'" 
                                               onblur="document.all.co_municipio_socio2.style.background='#FFFFFF'" 
                                               style="background:#FFFFFF; text-transform:lowercase;" />
                                    </td>
                                </tr>


                                <tr>
                                    <td height="29" colspan="6"  class="titulo_tabela1">DADOS BANCÁRIOS</td>
                                </tr>
                                <tr>
                                <tr>
                                    <td height="44"  class="secao">Nome do banco:</td>
                                    <td colspan="5" align="left">
                                        <input name="co_nome_banco" type="text" id="co_nome_banco" size="20" 
                                               style="background:#FFFFFF;" 
                                               onfocus="document.all.co_nome_banco.style.background='#CCFFCC'" 
                                               onblur="document.all.co_nome_banco.style.background='#FFFFFF'" 
                                               onchange="this.value=this.value.toUpperCase()" />
                                    </td>
                                </tr>


                                <tr>
                                    <td height="35"  class="secao">Agência:</td>
                                    <td align="left">
                                        <input name="co_agencia" type="text" id="co_agencia" size="20"
                                               style="background:#FFFFFF;" 
                                               onfocus="document.all.co_agencia.style.background='#CCFFCC'" 
                                               onblur="document.all.co_agencia.style.background='#FFFFFF'" 
                                               onchange="this.value=this.value.toUpperCase()" />
                                    </td>
                                    <td  class="secao">Conta:</td>
                                    <td colspan="3" align="left">
                                        <input name="co_conta" type="text" id="co_conta" size="27" 
                                               style="background:#FFFFFF;" 
                                               onfocus="document.all.co_conta.style.background='#CCFFCC'" 
                                               onblur="document.all.co_conta.style.background='#FFFFFF'" 
                                               onchange="this.value=this.value.toUpperCase()" />

                                    </td>
                                <tr>



                                <tr>
                                    <td height="29" colspan="6"  class="titulo_tabela1">OBJETO DO CONTRATO</td>
                                </tr>
                                <tr>
                                    <td height="44" class="secao" colspan="2">Munic&iacute;pio onde ser&aacute; executado o servi&ccedil;o:</td>
                                    <td colspan="4" align="left">
                                        <input name="co_municipio" type="text" id="co_municipio" size="20" 
                                               style="background:#FFFFFF;" 
                                               onfocus="document.all.co_municipio.style.background='#CCFFCC'" 
                                               onblur="document.all.co_municipio.style.background='#FFFFFF'" 
                                               onchange="this.value=this.value.toUpperCase()" />
                                    </td>
                                </tr>
                                <tr>
                                    <td height="44"  class="secao" colspan="2">Assunto:</td>
                                    <td colspan="4" align="left">
                                        <input name="assunto" type="text" id="assunto" size="20" 
                                               style="background:#FFFFFF;" 
                                               onfocus="document.all.assunto.style.background='#CCFFCC'" 
                                               onblur="document.all.assunto.style.background='#FFFFFF'" 
                                               onchange="this.value=this.value.toUpperCase()" /> <span style="color:red">I.P.C</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td  class="secao" colspan="2">Data do Processo:</td>
                                    <td colspan="4" align="left">
                                        <input name="data_proc" type="text" id="data_proc" size="10" 
                                               onkeyup="mascara_data(this)" maxlength="10"
                                               onfocus="document.all.data_proc.style.background='#CCFFCC'" 
                                               onblur="document.all.data_proc.style.background='#FFFFFF'" 
                                               style="background:#FFFFFF;" />
                                    </td>
                                </tr>
                                <tr>
                                    <td height="102" colspan="6" align="center">

                                        <label>
                                            <textarea name="objeto" id="objeto" cols="45" rows="5" 
                                                      onfocus="document.all.objeto.style.background='#CCFFCC'" 
                                                      onblur="document.all.objeto.style.background='#FFFFFF'" 
                                                      style="background:#FFFFFF;"
                                                      onchange="this.value=this.value.toUpperCase()"></textarea>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="27" colspan="6" class="titulo_tabela1">ESPECIFICA&Ccedil;&Atilde;O DO TIPO DE SERVI&Ccedil;O A SER PRESTADO</td>
                                </tr>
                                <tr>
                                    <td height="102" colspan="6" align="center">
                                        <label>
                                            <textarea name="especificacao" id="especificacao" cols="45" rows="5" 
                                                      onfocus="document.all.especificacao.style.background='#CCFFCC'" 
                                                      onblur="document.all.especificacao.style.background='#FFFFFF'" 
                                                      style="background:#FFFFFF;"
                                                      onchange="this.value=this.value.toUpperCase()"></textarea>
                                        </label>
                                    </td>
                                </tr>
                                <tr style="display:">
                                    <td height="46"  >ANEXO I &ndash;  VALOR R$</td>
                                    <td>
                                        <input name="valor" type="text" id="valor" size="20" 
                                               onkeydown="FormataValor(this,event,20,2)" 
                                               onfocus="document.all.valor.style.background='#CCFFCC'" 
                                               onblur="document.all.valor.style.background='#FFFFFF'" 
                                               style="background:#FFFFFF;"/> <span style="color:red">I.P.C</span>
                                    </td>
                                    <td  class="secao">DATA:</td>
                                    <td colspan="4" align="left">
                                        <input name="data_inicio" type="text" id="data_inicio" size="10" 
                                               onkeyup="mascara_data(this)" maxlength="10"
                                               onfocus="document.all.data_inicio.style.background='#CCFFCC'" 
                                               onblur="document.all.data_inicio.style.background='#FFFFFF'" 
                                               style="background:#FFFFFF;" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>Unidade de Medida:</td>
                                    <td>
                                        <select id="medida" name="medida" style="width: 153px;">
                                            <?php
                                            foreach ($rs_medidas as $medida) {
                                                $select = ($medida['id_medida']==$padraoCampoMedida)?" selected='selected'":"";
                                                echo "<option$select value='{$medida['id_medida']}'>" . $medida['medida'] . "</option>";
                                            }
                                            ?>
                                        </select> <span style="color:red">I.P.C</span>
                                    </td>
                                    <td></td>
                                    <td colspan="4" align="left">
                                    </td>
                                </tr>
                                <tr>
                                    <td height="46" colspan="6" align="center" valign="middle" >
                                        <input type="hidden" name="id" value="2">
                                        <input type="hidden" name="regiao" value="<?= $regiao ?>">
                                        <label>
                                            <input type="submit" name="Submit" id="button" value="Cadastrar">
                                        </label>
                                        <label>
                                            <input type="button" name="cancelar" id="btCancelar" value="Cancelar">
                                        </label>
                                    </td>
                                </tr>
                            </table>


                        </form>
                    </div>

                </div>
                <div id="rodape">
                    <?php echo $row_master['razao'] ?></div>
            </div>
        </body> 
        </html>
        <script language="javascript">
            function validaForm(){
                d = document.form1;
                if (d.endereco.value == ""){
                    alert("O campo Endereço deve ser preenchido!");
                    d.endereco.focus();
                    return false;
                }
                if (d.cnpj.value == ""){
                    alert("O campo CNPJ deve ser preenchido!");
                    d.cnpj.focus();
                    return false;
                }
                if (d.c_fantasia.value == ""){
                    alert("O campo Nome Fantasia deve ser preenchido!");
                    d.c_fantasia.focus();
                    return false;
                }
                if (d.c_razao.value == ""){
                    alert("O campo Razão Social deve ser preenchido!");
                    d.c_razao.focus();
                    return false;
                }
                if (d.c_endereco.value == ""){
                    alert("O campo Endereço deve ser preenchido!");
                    d.c_endereco.focus();
                    return false;
                }
                if (d.c_cnpj.value == ""){
                    alert("O campo CNPJ deve ser preenchido!");
                    d.c_cnpj.focus();
                    return false;
                }
                if (d.c_ie.value == ""){
                    alert("O campo  IE deve ser preenchido!");
                    d.c_ie.focus();
                    return false;
                }
                if (d.c_im.value == ""){
                    alert("O campo IM deve ser preenchido!");
                    d.c_im.focus();
                    return false;
                }
                if (d.c_responsavel.value == ""){
                    alert("O campo Responsavel deve ser preenchido!");
                    d.c_responsavel.focus();
                    return false;
                }
                if (d.c_rg.value == ""){
                    alert("O campo RG deve ser preenchido!");
                    d.c_rg.focus();
                    return false;
                }
                if (d.c_cpf.value == ""){
                    alert("O campo CPF deve ser preenchido!");
                    d.c_cpf.focus();
                    return false;
                }
                if (d.co_responsavel.value == ""){
                    alert("O campo Responsavel deve ser preenchido!");
                    d.co_responsavel.focus();
                    return false;
                }
                if (d.co_tel.value == ""){
                    alert("O campo Telefone deve ser preenchido!");
                    d.co_tel.focus();
                    return false;
                }
                if (d.co_municipio.value == ""){
                    alert("O campo Municipio deve ser preenchido!");
                    d.co_municipio.focus();
                    return false;
                }
                if (d.assunto.value == ""){
                    alert("O campo Assunto deve ser preenchido!");
                    d.assunto.focus();
                    return false;
                }
                if (d.data_proc.value == ""){
                    alert("O campo Data do Processo deve ser preenchido!");
                    d.data_proc.focus();
                    return false;
                }
                if (d.objeto.value == ""){
                    alert("O campo Objeto deve ser preenchido!");
                    d.objeto.focus();
                    return false;
                }
                if (d.especificacao.value == ""){
                    alert("O campo Especificação deve ser preenchido!");
                    d.especificacao.focus();
                    return false;
                }
                return true;   }
        </script>
        <?php
        break;
    case 2:
        
        //INSERINDO AS INFORMAÇÕES
        $id_projeto = $_REQUEST['projeto'];
        $id_user = $_COOKIE['logado'];
        $aberto_em = date('Y-m-d');
        $regiao = $_REQUEST['regiao'];
        $aberto_por = $_REQUEST['aberto_por'];
        $contratante = $_REQUEST['contratante'];
        $endereco = $_REQUEST['endereco'];
        $cnpj = $_REQUEST['cnpj'];
        $responsavel = $_REQUEST['responsavel'];
        $civil = $_REQUEST['civil'];
        $nacionalidade = $_REQUEST['nacionalidade'];
        $formacao = $_REQUEST['formacao'];
        $rg = $_REQUEST['rg'];
        $cpf = $_REQUEST['cpf'];
        $c_fantasia = $_REQUEST['c_fantasia'];
        $c_razao = $_REQUEST['c_razao'];
        $c_endereco = $_REQUEST['c_endereco'];
        $c_cnpj = $_REQUEST['c_cnpj'];
        $c_ie = $_REQUEST['c_ie'];
        $c_im = $_REQUEST['c_im'];
        $c_tel = $_REQUEST['c_tel'];
        $c_fax = $_REQUEST['c_fax'];
        $c_email = $_REQUEST['c_email'];
        $c_responsavel = $_REQUEST['c_responsavel'];
        $c_civil = $_REQUEST['c_civil'];
        $c_nacionalidade = $_REQUEST['c_nacionalidade'];
        $c_formacao = $_REQUEST['c_formacao'];
        $c_rg = $_REQUEST['c_rg'];
        $c_cpf = $_REQUEST['c_cpf'];
        $c_email2 = $_REQUEST['c_email2'];
        $c_site = $_REQUEST['c_site'];
        
        $co_responsavel = $_REQUEST['co_responsavel'];
        $co_tel = $_REQUEST['co_tel'];
        $co_fax = $_REQUEST['co_fax'];
        $co_civil = $_REQUEST['co_civil'];
        $co_nacionalidade = $_REQUEST['co_nacionalidade'];
        $co_email = $_REQUEST['co_email'];
        $co_municipio = $_REQUEST['co_municipio'];
        $data_nasc = converteData($_REQUEST['co_data_nasc']);

        $co_responsavel_socio1 = $_REQUEST['co_responsavel_socio1'];
        $co_tel_socio1 = $_REQUEST['co_tel_socio1'];
        $co_fax_socio1 = $_REQUEST['co_fax_socio1'];
        $co_civil_socio1 = $_REQUEST['co_civil_socio1'];
        $co_nacionalidade_socio1 = $_REQUEST['co_nacionalidade_socio1'];
        $co_email_socio1 = $_REQUEST['co_email_socio1'];
        $co_municipio_socio1 = $_REQUEST['co_municipio_socio1'];
        $data_nasc_socio1 = converteData($_REQUEST['co_data_nasc_socio1']);
        

        $co_responsavel_socio2 = $_REQUEST['co_responsavel_socio2'];
        $co_tel_socio2 = $_REQUEST['co_tel_socio2'];
        $co_fax_socio2 = $_REQUEST['co_fax_socio2'];
        $co_civil_socio2 = $_REQUEST['co_civil_socio2'];
        $co_nacionalidade_socio2 = $_REQUEST['co_nacionalidade_socio2'];
        $co_email_socio2 = $_REQUEST['co_email_socio2'];
        $co_municipio_socio2 = $_REQUEST['co_municipio_socio2'];
        $data_nasc_socio2 = converteData($_REQUEST['co_data_nasc_socio2']);

        $co_nome_banco = $_REQUEST['co_nome_banco'];
        $co_agencia = $_REQUEST['co_agencia'];
        $co_conta = $_REQUEST['co_conta'];

        $assunto = $_REQUEST['assunto'];
        $objeto = $_REQUEST['objeto'];
        $especificacao = $_REQUEST['especificacao'];
        $valor = $_REQUEST['valor'];
        $data_inicio = $_REQUEST['data_inicio'];
        $data_proc = $_REQUEST['data_proc'];
        $valor = str_replace(".", "", $valor);
        $prestador_tipo = $_REQUEST['c_tipo'];
        $dependente_nome = $_REQUEST['dependente_nome'];
        $dependente_parentesco = $_REQUEST['dependente_parentesco'];
        $dependente_nascimento = $_REQUEST['dependente_nascimento'];
        $contratado_em = converteData($_REQUEST['dataInicio']);
        $encerrado_em = converteData($_REQUEST['dataFinal']);
        
        $medida = $_REQUEST['medida'];
        $prestacao_contas = $_REQUEST['prestacao_contas'];
        
        $data_inicio_f = converteData($data_inicio);
        $data_proc_f = converteData($data_proc);
        // GERANDO A NÚMERAÇÃO DO PROCESSO
        $ano_proc1 = explode("-", $data_proc_f);
        $ano_proc2 = "$ano_proc1[0]";
        $ano_proc3 = str_split($ano_proc2, 2);
        $ano_proc = "$ano_proc3[1]";
        $num_reg = sprintf("%03s", $regiao);
        $result_cont = mysql_query("SELECT * FROM prestadorservico where id_regiao = '$regiao' AND prestador_tipo<>0");
        $row_cont = mysql_num_rows($result_cont);
        $row_cont = $row_cont + 1;
        $num_id = sprintf("%03s", $row_cont);
        //$num_id = sprintf("%04s", $row_cont);
        $num_ano = sprintf("%0s", $row_cont);
        $numero = $num_id . "/" . date('Y');
        
        mysql_query("INSERT INTO prestadorservico
(
id_regiao, 
id_projeto, 
id_medida,
aberto_por, 
aberto_em, 
contratante, 
numero, 
endereco, 
cnpj, 
responsavel, 
civil, 
nacionalidade, 
formacao, 
rg, 
cpf, 
c_fantasia, 
c_razao, 
c_endereco, 
c_cnpj, 
c_ie, 
c_im, 
c_tel, 
c_fax, 
c_email, 
c_responsavel, 
c_civil, 
c_nacionalidade, 
c_formacao, 
c_rg, 
c_cpf, 
c_email2, 
c_site, 
co_responsavel, 
co_tel, 
co_fax, 
co_civil, 
co_nacionalidade, 
co_email, 
co_municipio, 
assunto, 
objeto, 
especificacao, 
valor, 
data, 
data_proc, 
acompanhamento,
prestador_tipo,
c_data_nascimento,
co_responsavel_socio1,
co_tel_socio1,
co_fax_socio1,
co_civil_socio1, 
co_nacionalidade_socio1,
co_email_socio1,
co_municipio_socio1 ,
data_nasc_socio1,
co_responsavel_socio2,
co_tel_socio2,
co_fax_socio2,
co_civil_socio2,
co_nacionalidade_socio2,
co_email_socio2,
co_municipio_socio2,
data_nasc_socio2,
nome_banco,
agencia,
conta,
contratado_em,
encerrado_em,
prestacao_contas
) 

VALUES 
('$regiao',
'$id_projeto',
'$medida',
'$id_user',
'$aberto_em',
'$contratante',
'$numero',
'$endereco',
'$cnpj',
'$responsavel',
'$civil',
'$nacionalidade',
'$formacao','$rg','$cpf','$c_fantasia','$c_razao','$c_endereco','$c_cnpj',
'$c_ie','$c_im','$c_tel','$c_fax','$c_email','$c_responsavel','$c_civil','$c_nacionalidade','$c_formacao',
'$c_rg','$c_cpf','$c_email2','$c_site','$co_responsavel','$co_tel','$co_fax','$co_civil',
'$co_nacionalidade','$co_email','$co_municipio','$assunto','$objeto','$especificacao','$valor','$data_inicio_f'
,'$data_proc_f','1','$prestador_tipo','$data_nasc','$co_responsavel_socio1',
'$co_tel_socio1',
'$co_fax_socio1',
'$co_civil_socio1', 
'$co_nacionalidade_socio1',
'$co_email_socio1',
'$co_municipio_socio1' ,
'$data_nasc_socio1',
'$co_responsavel_socio2',
'$co_tel_socio2',
'$co_fax_socio2',
'$co_civil_socio2',
'$co_nacionalidade_socio2',
'$co_email_socio2',
'$co_municipio_socio2',
'$data_nasc_socio2',
'$co_nome_banco',
'$co_agencia',
'$co_conta',
'$contratado_em',
'$encerrado_em',
'$prestacao_contas'    
)") or die("Erro <br>" . mysql_error());


        $ultimo_id = mysql_insert_id();


        if ($prestador_tipo == 3) {

            foreach ($dependente_nome as $chave => $valor) {

                if (empty($dependente_nome[$chave]))
                    continue;

                $dependente_nascimento[$chave] = implode('-', array_reverse(explode('/', $dependente_nascimento[$chave])));

                mysql_query("INSERT INTO prestador_dependente (prestador_id, prestador_dep_nome, prestador_dep_parentesco, prestador_dep_data_nasc, prestador_dep_status) 
																				  VALUES 
																				  ('$ultimo_id','$dependente_nome[$chave]', '$dependente_parentesco[$chave]', '$dependente_nascimento[$chave]','1');");
            }
            print "
			<script>
			alert (\"$numero - Dasos cadastrados!\"); ";



            print"location.href=\"prestadorservico.php?id=1&regiao=$regiao\"
		</script>";
            break;
        } else {

            print "
                <script>
                alert (\"$numero - Dasos cadastrados!\"); 
                location.href=\"prestadorservico.php?regiao=$regiao\"
                </script>";
            break;
        }



    case 3:  //MOTRANDO TODOS OS DADOS DA EMPRESA
//EXCLUIR DEPENDENTES
        if (isset($_GET['excluir'])) {
            $id = $_GET['excluir'];

            mysql_query("UPDATE prestador_dependente SET prestador_dep_status = '0' WHERE prestador_dep_id = '$id' LIMIT 1") or die(mysql_error());
            unset($id);
        }

//FIM EXCLUIR DEPENDENTES





        $id_prestador = $_REQUEST['prestador'];
        $result_prestador = mysql_query("SELECT *,date_format(aberto_em, '%d/%m/%Y')as aberto_em 
,date_format(contratado_em, '%d/%m/%Y')as contratado_em ,date_format(encerrado_em, '%d/%m/%Y')as 
encerrado_em,date_format(data_proc, '%d/%m/%Y')as data_proc2 FROM prestadorservico WHERE id_prestador = '$id_prestador'") or die("Erro no SELECT<BR>" . mysql_error());
        $row = mysql_fetch_array($result_prestador);
        ?>
        <html>
            <head>
                <title>:: Intranet ::</title>
                <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
                <link href="../net1.css" rel="stylesheet" type="text/css">
                <script language="javascript" src="../js/ramon.js"></script>
                <script src="../jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
                <script src="../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript"></script>

                <script src="../jquery/validationEngine/jquery.validationEngine-pt.js" type="text/javascript"></script>
                <script src="../jquery/validationEngine/jquery.validationEngine.js" type="text/javascript"></script>
                <link href="../jquery/validationEngine/validationEngine.jquery.css" rel="stylesheet" type="text/css">

                <script>


                    $(function(){
                        	
                        	
                        $('.data_nasc').mask('99/99/9999');
                        	
                        	
                        $('#form1').validationEngine();
                        	
                        	
                        $('.tipo').change( function(){
                        		
                            if($(this).val() == 3){
                                $('.novo_dep').fadeIn('slow');
                            } else {
                                $('.novo_dep').fadeOut();
                            }
                        		
                        });
                        	
                        $('.adicionar').click( function() {
                        		
                        		
                            $('.data_nasc').mask('99/99/9999');
                        	
                        	
                            var campos = "<table width='100%' bgcolor='#FFFFFF'><tr><td  width=\"20%\"><span class=\"style35\"> NOME:</span></td><td  width=\"80%\"><span class=\"style35\"><input name=\"add_dep_nome[]\" type=\"text\" id=\"valor\" size=\"40\"onfocus=\"document.all.dep_nome.style.background='#CCFFCC'\"onblur=\"document.all.dep_nome.style.background='#FFFFFF'\" style=\"background:#FFFFFF;\"  size=\"80\" /></span></td></tr><tr><td><span class=\"style35\">PARENTESCO:</span></td><td><span class=\"style35\" c> <input name=\"add_dep_parentesco[]\" type=\"text\" id=\"dep_parentesco\" size=\"10\" maxlength=\"10\"onfocus=\"document.all.dep_parentesco.style.background='#CCFFCC'\" onblur=\"document.all.dep_parentesco.style.background='#FFFFFF'\"style=\"background:#FFFFFF;\" /></span></td></tr><tr><td><span class=\"style35\">DATA DE NASCIMENTO:</span></td><td><span class=\"style35\"> <input name=\"add_dep_data_nasc[]\" type=\"text\" id=\"dep_data_nasc\" size=\"10\" onkeyup=\"mascara_data(this)\" maxlength=\"10\" onfocus=\"document.all.dep_data_nasc.style.background='#CCFFCC'\"onblur=\"document.all.dep_data_nasc.style.background='#FFFFFF'\"style=\"background:#FFFFFF;\" /></span></td></tr><tr><td colspan=\"6\"  height=\"1\" bgcolor=\"#DEF\">&nbsp</td></tr></table>";
                        		
                            $('#tabela_dependente').append(campos);
                        		
                        		
                        });
                        	

                        	
                    });







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
                    .style38 {

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
                    }.style40 {font-family: Geneva, Arial, Helvetica, sans-serif}
                    .style41 {
                        font-family: Geneva, Arial, Helvetica, sans-serif;
                        color: #FFFFFF;
                        font-weight: bold;
                    }
                    .style43 {font-family: Arial, Helvetica, sans-serif}
                    .style45 { font-family: Arial, Helvetica, sans-serif; }
                    -->
                </style>
            </head>
            <body>
                <?php
                print"
<form action='prestadorservico.php' method='post' name='form1' id=\"form1\">
<table width='780' align='center' cellspad='5' bgcolor='#FFFFFF' class='bordaescura1px'>
<tr>
<td height='31' colspan='6' bgcolor='#CCCCCC'><div align='right' class='style35'>
<div align='center' class='style35'>DADOS DO CONTRATANTE</div>
</div></td>
</tr>
<tr>
<td height='35'><div align='right' class='style40 style35'><strong>Contratante:</strong></div></td>
<td height='35' colspan='5'><input name='contratante' type='text' id='contratante' value='INSTITUTO SORRINDO PARA A VIDA' size='90' disabled='disabled' />
</td>
</tr>
<tr>
<td height='35'><div align='right' class='style40 style35'><strong>Endere&ccedil;o:</strong></div></td>
<td height='35' colspan='5'>
<input name='endereco' type='text' id='endereco' size='90' value='$row[endereco]'
onfocus=\"document.all.endereco.style.background='#CCFFCC'\" onblur=\"document.all.endereco.style.background='#FFFFFF'\" style=\"background:#FFFFFF;\" onchange=\"this.value=this.value.toUpperCase()\" />
</td>
</tr>
<tr>
<td height='35'><div align='right' class='style35'>CNPJ:</div></td>
<td height='35' colspan='5'><span class='style35'>
<input name='cnpj' type='text' id='cnpj' value='$row[cnpj]'
style='background:#FFFFFF; text-transform:uppercase;'
onfocus=\"document.all.cnpj.style.background='#CCFFCC'\" 
onblur=\"document.all.cnpj.style.background='#FFFFFF'\"
onkeypress=\"formatar('##.###.###/####-##', this)\" 
onkeyup=\"pula(18,this.id,c_fantasia.id)\" size=\"20\" maxlength=\"18\" />
</span></td>
</tr>
<tr>
<td height=\"35\" colspan=\"6\"><span class=\"style35\">&nbsp;&nbsp;Responsavel:
<input name=\"responsavel\" type=\"text\" id=\"responsavel\" value=\"Luiz Carlos Mandia\" size=\"40\" disabled=\"disabled\" />
Estado civil:
<input name=\"civil\" type=\"text\" id=\"civil\" value=\"Casado\" size=\"20\" disabled=\"disabled\" />
</span></td>
</tr>
<tr>
<td height=\"35\" colspan=\"6\"><span class=\"style35\">Nacionalidade:
<input name=\"nacionalidade\" type=\"text\" id=\"nacionalidade\" value=\"Brasileira\" size=\"40\" disabled=\"disabled\" />
Forma&ccedil;&atilde;o: <span class=\"style35 style40\">
<input name=\"formacao\" type=\"text\" id=\"formacao\" value=\"Administrador\" size=\"20\" disabled=\"disabled\" />
</span></span></td>
</tr>
<tr>
<td height=\"35\" colspan=\"6\"><span class=\"style35 style40\">&nbsp;RG:<span class=\"style35\">
<input name=\"rg\" type=\"text\" id=\"rg\" size=\"20\" maxlength=\"14\" value=\"3.531.222-1\" disabled=\"disabled\" />
</span>&nbsp;&nbsp;CPF: <span class=\"style35\">
<input name=\"cpf\" type=\"text\" id=\"cpf\" value=\"570.072.418-91\" size=\"20\" disabled=\"disabled\" />
</span> </span></td>
</tr>
<tr>
<td height=\"31\" colspan=\"6\" bgcolor=\"#CCCCCC\"><div align=\"right\" class=\"style35\">
<div align=\"center\" class=\"style35\">DADOS DA EMPRESA CONTRATADA</div>
</div></td>
</tr>
<tr>
<td height=\"35\" colspan=\"6\"><span class=\"style35\"><strong>Nome Fantasia:</strong></span>
<input name=\"c_fantasia\" type=\"text\" id=\"c_fantasia\" value='$row[c_fantasia]'
style=\"background:#FFFFFF;\" 
onfocus=\"document.all.c_fantasia.style.background='#CCFFCC'\" 
onblur=\"document.all.c_fantasia.style.background='#FFFFFF'\" 
onchange=\"this.value=this.value.toUpperCase()\" size=\"90\" /></td>
</tr>
<tr>
<td height=\"35\" colspan=\"6\"><span class=\"style35\">Raz&atilde;o Social:
<input name=\"c_razao\" type=\"text\" id=\"c_razao\" size=\"90\" value='$row[c_razao]'
onfocus=\"document.all.c_razao.style.background='#CCFFCC'\" 
onblur=\"document.all.c_razao.style.background='#FFFFFF'\" 
style=\"background:#FFFFFF;\" onchange=\"this.value=this.value.toUpperCase()\" />
</span></td>
</tr>
<tr>
<td height=\"35\" colspan=\"6\"><span class=\"style35\">&nbsp;Endere&ccedil;o:
<input name=\"c_endereco\" type=\"text\" id=\"c_endereco\" size=\"90\" value='$row[c_endereco]'
onfocus=\"document.all.c_endereco.style.background='#CCFFCC'\" 
onblur=\"document.all.c_endereco.style.background='#FFFFFF'\" 
style=\"background:#FFFFFF;\" onchange=\"this.value=this.value.toUpperCase()\" />
</span></td>
</tr>
";
                ?>


            <tr>


                <td height="35" colspan="6"><span class="style35">Tipo: </span>



                    <input type="radio" name="tipo_prestador" value="1" <?php if ($row['prestador_tipo'] == 1) echo 'checked=checked' ?> class="tipo"><strong>1</strong> - Pessoa Jurídica<br>

                    &nbsp;&nbsp; <input type="radio" name="tipo_prestador" value="2" <?php if ($row['prestador_tipo'] == 2) echo 'checked=checked' ?>><strong>2</strong> - Pessoa Jurídica - Cooperativa<br>


                    &nbsp;&nbsp; <input type="radio" name="tipo_prestador" value="3" <?php if ($row['prestador_tipo'] == 3) echo 'checked=checked' ?> class="tipo"><strong>3</strong> - Pessoa Física<br>

                    &nbsp;&nbsp;   <input type="radio" name="tipo_prestador" value="4" <?php if ($row['prestador_tipo'] == 4) echo 'checked=checked' ?> class="tipo"><strong>4</strong> - Pessoa Jurídica - Prestador de Serviço<br>

                    &nbsp;&nbsp;  <input type="radio" name="tipo_prestador" value="5" <?php if ($row['prestador_tipo'] == 5) echo 'checked=checked' ?> class="tipo"><strong>5 </strong>- Pessoa Jurídica - Administradora<br>


                    &nbsp;&nbsp;   <input type="radio" name="tipo_prestador" value="6" <?php if ($row['prestador_tipo'] == 6) echo 'checked=checked' ?> class="tipo"><strong>6</strong> - Pessoa Jurídica - Publicidade<br>

                    &nbsp;&nbsp;   <input type="radio" name="tipo_prestador" value="7" <?php if ($row['prestador_tipo'] == 7) echo 'checked=checked' ?> class="tipo"><strong>7</strong> - Pessoa Jurídica Sem Retenção  <br>

                </td>
            </tr>

            <?php
            print"
<tr>
<td height=\"35\" colspan=\"6\"><span class=\"style35\">&nbsp;CNPJ:
<input name=\"c_cnpj\" type=\"text\" id=\"c_cnpj\" value='$row[c_cnpj]'
style=\"background:#FFFFFF; text-transform:uppercase;\"
onfocus=\"document.all.c_cnpj.style.background='#CCFFCC'\" 
onblur=\"document.all.c_cnpj.style.background='#FFFFFF'\" 
onkeyup=\"pula(18,this.id,c_ie.id)\"
onkeypress=\"formatar('##.###.###/####-##', this)\" size=\"18\" maxlength=\"18\" />
&nbsp;IE:
<input name=\"c_ie\" type=\"text\" id=\"c_ie\" size=\"15\" value='$row[c_ie]'
onfocus=\"document.all.c_ie.style.background='#CCFFCC'\" onblur=\"document.all.c_ie.style.background='#FFFFFF'\" style=\"background:#FFFFFF;\" />
CCM:
<input name=\"c_im\" type=\"text\" id=\"c_im\" size=\"15\" value='$row[c_im]'
onfocus=\"document.all.c_im.style.background='#CCFFCC'\" onblur=\"document.all.c_im.style.background='#FFFFFF'\" style=\"background:#FFFFFF;\" />
</span></td>
</tr>
<tr>
<td height=\"35\" colspan=\"6\"><span class=\"style35\">Telefone:
<input name='c_tel' type='text' id='c_tel' size='12' value='$row[c_tel]'
onkeypress=\"return(TelefoneFormat(this,event))\" 
onkeyup=\"pula(13,this.id,c_fax.id)\" 
onfocus=\"document.all.c_tel.style.background='#CCFFCC'\" 
onblur=\"document.all.c_tel.style.background='#FFFFFF'\" 
style=\"background:#FFFFFF;\" />
Fax:
<input name=\"c_fax\" type=\"text\" id=\"c_fax\" size=\"12\" value='$row[c_fax]'
onkeypress=\"return(TelefoneFormat(this,event))\" 
onkeyup=\"pula(13,this.id,c_email.id)\" 
onfocus=\"document.all.c_fax.style.background='#CCFFCC'\" 
onblur=\"document.all.c_fax.style.background='#FFFFFF'\" 
style=\"background:#FFFFFF;\" />
E-mail: <span class=\"style35 style40\">
<input name=\"c_email\" type=\"text\" id=\"c_email\" size=\"25\" value='$row[c_email]'
onfocus=\"document.all.c_email.style.background='#CCFFCC'\" 
onblur=\"document.all.c_email.style.background='#FFFFFF'\" 
style=\"background:#FFFFFF; text-transform:lowercase;\" />
</span> </span></td>
</tr>
<tr>
<td height=\"35\" colspan=\"6\"><span class=\"style35\">&nbsp;&nbsp;Responsavel:
<input name=\"c_responsavel\" type=\"text\" id=\"c_responsavel\" size=\"40\" value='$row[c_responsavel]'
style=\"background:#FFFFFF;\" 
onfocus=\"document.all.c_responsavel.style.background='#CCFFCC'\" 
onblur=\"document.all.c_responsavel.style.background='#FFFFFF'\" 
onchange=\"this.value=this.value.toUpperCase()\" />
Estado civil:
<input name=\"c_civil\" type=\"text\" id=\"c_civil\" size=\"20\" value='$row[c_civil]'
style=\"background:#FFFFFF;\" 
onfocus=\"document.all.c_civil.style.background='#CCFFCC'\" 
onblur=\"document.all.c_civil.style.background='#FFFFFF'\" 
onchange=\"this.value=this.value.toUpperCase()\" />
</span></td>
</tr>
<tr>
<td height=\"35\" colspan=\"6\"><span class=\"style35\">Nacionalidade:
<input name=\"c_nacionalidade\" type=\"text\" id=\"c_nacionalidade\" size=\"40\" value='$row[c_nacionalidade]'
style=\"background:#FFFFFF;\" 
onfocus=\"document.all.c_nacionalidade.style.background='#CCFFCC'\" 
onblur=\"document.all.c_nacionalidade.style.background='#FFFFFF'\" 
onchange=\"this.value=this.value.toUpperCase()\" />
Forma&ccedil;&atilde;o: <span class=\"style35 style40\">
<input name=\"c_formacao\" type=\"text\" id=\"c_formacao\" size=\"20\" value='$row[c_formacao]'
style=\"background:#FFFFFF;\" 
onfocus=\"document.all.c_formacao.style.background='#CCFFCC'\" 
onblur=\"document.all.c_formacao.style.background='#FFFFFF'\" 
onchange=\"this.value=this.value.toUpperCase()\" />
</span></span></td>
</tr>
<tr>
<td height=\"35\" colspan=\"6\"><span class=\"style35 style40\">&nbsp;RG:<span class=\"style35\">
<input name=\"c_rg\" type=\"text\" id=\"c_rg\" value='$row[c_rg]'
onkeypress=\"formatar('##.###.###-##', this)\" size=\"20\" maxlength=\"14\" 
onfocus=\"document.all.c_rg.style.background='#CCFFCC'\" 
onblur=\"document.all.c_rg.style.background='#FFFFFF'\" 
style=\"background:#FFFFFF;\" />
</span>&nbsp;&nbsp;CPF: <span class=\"style35\">
<input name=\"c_cpf\" type=\"text\" id=\"c_cpf\" value='$row[c_cpf]'
onkeypress=\"formatar('###.###.###-##', this)\" size=\"20\" maxlength=\"14\" 
onkeyup=\"pula(14,this.id,c_email2.id)\" 
onfocus=\"document.all.c_cpf.style.background='#CCFFCC'\" 
onblur=\"document.all.c_cpf.style.background='#FFFFFF'\" 
style=\"background:#FFFFFF;\" />
</span> </span></td>
</tr>
<tr>
<td height=\"35\" colspan=\"6\"><span class=\"style35\">&nbsp;E-mail: <span class=\"style35 style40\">
<input name=\"c_email2\" type=\"text\" id=\"c_email2\" size=\"30\" value='$row[c_email2]'
onfocus=\"document.all.c_email2.style.background='#CCFFCC'\" 
onblur=\"document.all.c_email2.style.background='#FFFFFF'\" 
style=\"background:#FFFFFF; text-transform:lowercase;\" />
</span></span>&nbsp;<span class=\"style35\">&nbsp;Site: <span class=\"style35 style40\">
<input name=\"c_site\" type=\"text\" id=\"c_site\" size=\"38\" value='$row[c_site]'
onfocus=\"document.all.c_site.style.background='#CCFFCC'\" 
onblur=\"document.all.c_site.style.background='#FFFFFF'\"
style=\"background:#FFFFFF; text-transform:lowercase;\" />
</span></span></td>
</tr>
<tr>
<td colspan=\"6\" bgcolor=\"#CCCCCC\"><div align=\"center\"><span class=\"style35\">DADOS DA PESSOA DE  CONTATO NA CONTRATADA</span> </div></td>
</tr>
<tr>
<td height=\"35\" colspan=\"6\"><span class=\"style35\">&nbsp;&nbsp;Nome Completo:
<input name=\"co_responsavel\" type=\"text\" id=\"co_responsavel\" size=\"27\" value='$row[co_responsavel]'
style=\"background:#FFFFFF;\" 
onfocus=\"document.all.co_responsavel.style.background='#CCFFCC'\" 
onblur=\"document.all.co_responsavel.style.background='#FFFFFF'\" 
onchange=\"this.value=this.value.toUpperCase()\" />
&nbsp;Telefone:
<input name='co_tel' type='text' id='co_tel' size='12' value='$row[co_tel]'
onkeypress=\"return(TelefoneFormat(this,event))\" 
onkeyup=\"pula(13,this.id,co_fax.id)\" 
onfocus=\"document.all.co_tel.style.background='#CCFFCC'\" 
onblur=\"document.all.co_tel.style.background='#FFFFFF'\" 
style=\"background:#FFFFFF;\" />
&nbsp;Fax:
<input name=\"co_fax\" type=\"text\" id=\"co_fax\" size=\"12\" value='$row[co_fax]'
onkeypress=\"return(TelefoneFormat(this,event))\" 
onkeyup=\"pula(13,this.id,co_civil.id)\" 
onfocus=\"document.all.co_fax.style.background='#CCFFCC'\" 
onblur=\"document.all.co_fax.style.background='#FFFFFF'\" 
style=\"background:#FFFFFF;\" />
</span></td>
</tr>
<tr>
<td height=\"35\" colspan=\"6\"><span class=\"style35\">Estado civil:
<input name=\"co_civil\" type=\"text\" id=\"co_civil\" size=\"20\" value='$row[co_civil]'
style=\"background:#FFFFFF;\" 
onfocus=\"document.all.co_civil.style.background='#CCFFCC'\" 
onblur=\"document.all.co_civil.style.background='#FFFFFF'\" 
onchange=\"this.value=this.value.toUpperCase()\" />

Nacionalidade:
<input name=\"co_nacionalidade\" type=\"text\" id=\"co_nacionalidade\" size=\"27\" value='$row[co_nacionalidade]'
style=\"background:#FFFFFF;\" 
onfocus=\"document.all.co_nacionalidade.style.background='#CCFFCC'\" 
onblur=\"document.all.co_nacionalidade.style.background='#FFFFFF'\" 
onchange=\"this.value=this.value.toUpperCase()\" />
</span></td>
</tr>
<tr>
<td height=\"35\" colspan=\"6\"><span class=\"style35\">&nbsp;Email: <span class=\"style35 style40\">
<input name=\"co_email\" type=\"text\" id=\"co_email\" size=\"30\" value='$row[co_email]'
onfocus=\"document.all.co_email.style.background='#CCFFCC'\" 
onblur=\"document.all.co_email.style.background='#FFFFFF'\" 
style=\"background:#FFFFFF; text-transform:lowercase;\" />
</span></span></td>
</tr>
<tr>
<td colspan=\"6\" bgcolor=\"#CCCCCC\"><div align=\"center\" class=\"style35\">OBJETO DO CONTRATO</div></td>
</tr>
<tr>
<td height=\"44\" colspan=\"6\"><span class=\"style35\">Munic&iacute;pio onde ser&aacute; executado o servi&ccedil;o:<span class=\"style35 style40\">
<input name=\"co_municipio\" type=\"text\" id=\"co_municipio\" size=\"20\" value='$row[co_municipio]'
style=\"background:#FFFFFF;\" 
onfocus=\"document.all.co_municipio.style.background='#CCFFCC'\" 
onblur=\"document.all.co_municipio.style.background='#FFFFFF'\" 
onchange=\"this.value=this.value.toUpperCase()\" />
</span></span></td>
</tr>
<tr>
<td height=\"44\" colspan=\"6\"><span class=\"style35\">Assunto:<span class=\"style35 style40\">
<input name=\"assunto\" type=\"text\" id=\"assunto\" size=\"20\" value='$row[assunto]'
style=\"background:#FFFFFF;\" 
onfocus=\"document.all.assunto.style.background='#CCFFCC'\" 
onblur=\"document.all.assunto.style.background='#FFFFFF'\" 
onchange=\"this.value=this.value.toUpperCase()\" />
</span>&nbsp;Data do Processo:
<input name=\"data_proc\" type=\"text\" id=\"data_proc\" size=\"10\" value='$row[data_proc2]'
onkeyup=\"mascara_data(this)\" maxlength=\"10\"
onfocus=\"document.all.data_proc.style.background='#CCFFCC'\" 
onblur=\"document.all.data_proc.style.background='#FFFFFF'\" 
onKeyUp=\"mascara_data(this); pula(10,this.id,objeto.id)\"
style=\"background:#FFFFFF;\" />
</span></td>
</tr>
<tr>
<td height=\"102\" colspan=\"6\"><div align=\"center\">
<label>
<textarea name=\"objeto\" id=\"objeto\" cols=\"45\" rows=\"5\"
onfocus=\"document.all.objeto.style.background='#CCFFCC'\" 
onblur=\"document.all.objeto.style.background='#FFFFFF'\" 
style=\"background:#FFFFFF;\"
onchange=\"this.value=this.value.toUpperCase()\">$row[objeto]</textarea>
</label>
</div></td>
</tr>
<tr>
<td colspan=\"6\" bgcolor=\"#CCCCCC\"><div align=\"center\" class=\"style35\">ESPECIFICA&Ccedil;&Atilde;O DO TIPO DE SERVI&Ccedil;O A SER PRESTADO</div></td>
</tr>
<tr>
<td height=\"102\" colspan=\"6\"><div align=\"center\">
<label>
<textarea name=\"especificacao\" id=\"especificacao\" cols=\"45\" rows=\"5\" 
onfocus=\"document.all.especificacao.style.background='#CCFFCC'\" 
onblur=\"document.all.especificacao.style.background='#FFFFFF'\" 
style=\"background:#FFFFFF;\"
onchange=\"this.value=this.value.toUpperCase()\">$row[especificacao]</textarea>
</label>
</div></td>
</tr>
<tr style=\"display:none\">
<td height=\"46\" colspan=\"6\" ><span class=\"style35\"> &nbsp;&nbsp;ANEXO I &ndash;  VALOR R$
<input name=\"valor\" type=\"text\" id=\"valor\" size=\"20\" 
onkeydown=\"FormataValor(this,event,20,2)\" 
onfocus=\"document.all.valor.style.background='#CCFFCC'\" 
onblur=\"document.all.valor.style.background='#FFFFFF'\" 
style=\"background:#FFFFFF;\"/>
</span> <span class=\"style35\">DATA: &nbsp;
<input name=\"data_inicio\" type=\"text\" id=\"data_inicio\" size=\"10\" 
onkeyup=\"mascara_data(this)\" maxlength=\"10\"
onfocus=\"document.all.data_inicio.style.background='#CCFFCC'\" 
onblur=\"document.all.data_inicio.style.background='#FFFFFF'\" 
style=\"background:#FFFFFF;\" />
</span></td>
</tr>

<tr>
	<td colspan=\"6\">";

            if ($row['prestador_tipo'] == 3) {

                echo "<table width=\"100%\"id=\"mostrar_dep\" style=\"display:block;\" class=\"novo_dep\"> 
	 <tr>
				
				<td bgcolor=\"#CCCCCC\"  align=\"center\">
				 <span class=\"adicionar\" style=\"cursor:pointer;\"> <img src=\"../imagens/adicionar_dep.gif\" width=\"36\" height=\"26\" title=\"Adicionar Dependente.\" /></span> 
				</td>
				
				<td colspan=\"5\" bgcolor=\"#CCCCCC\" align=\"center\">
				
				<div align=\"center\" class=\"style35\">DEPENDENTES</div>
			
				</td>
				
		</tr>";


                echo "
		<t r>
				<td colspan=\"6\">
				
				<div id=\"tabela_dependente\" style=\"width:900px;\">
				
				<table width=\"100%\" bgcolor='#FFFFFF'>";


                $qr_dependente = mysql_query("SELECT * FROM prestador_dependente WHERE prestador_id = '$id_prestador' AND prestador_dep_status = '1'") or die(mysql_error());



                $verifica = mysql_num_rows($qr_dependente);

                if ($verifica != 0) {

                    while ($row_dependente = mysql_fetch_assoc($qr_dependente)):

                        echo "
					
				<tr >
					<td width=\"20%\">
						<span class=\"style35\"> NOME:</span>
						
						</td>
					<td  width=\"90%\">
				
						<input name=\"dep_nome[]\" type=\"text\" id=\"valor\" size=\"40\" 
					
					onfocus=\"document.all.dep_nome.style.background='#CCFFCC'\" 
					onblur=\"document.all.dep_nome.style.background='#FFFFFF'\" 
					style=\"background:#FFFFFF; text-align:left;\" value=\"" . $row_dependente['prestador_dep_nome'] . "\" size=\"80\" class=\"validate[required]\" />
					
					</td>
				</tr>
				<tr>
					
					<td>
					<span class=\"style35\">PARENTESCO:</span>
					</td>
					
					<td>
					<span class=\"style35\"> 
					<input name=\"dep_parentesco[]\" type=\"text\" id=\"dep_parentesco\" size=\"10\" 
					
					onfocus=\"document.all.dep_parentesco.style.background='#CCFFCC'\" 
					onblur=\"document.all.dep_parentesco.style.background='#FFFFFF'\" 
					style=\"background:#FFFFFF;\" value=\"" . $row_dependente['prestador_dep_parentesco'] . " \" class=\"validate[required]\"/>
					</span>
					</td>
				</tr>
				<tr>
						<td>
						<span class=\"style35\">DATA DE NASCIMENTO:</span>
					</td>
					
						
						<td>
						<span class=\"style35\"> <input name=\"dep_data_nasc[]\" type=\"text\" id=\"dep_data_nasc\" size=\"10\" 
					onkeyup=\"mascara_data(this)\" maxlength=\"10\"
					onfocus=\"document.all.dep_data_nasc.style.background='#CCFFCC'\" 
					onblur=\"document.all.dep_data_nasc.style.background='#FFFFFF'\" 
					style=\"background:#FFFFFF;\" value=\"";
                        if ($row_dependente['prestador_dep_data_nasc'] != '0000-00-00') {
                            echo implode('/', array_reverse(explode('-', $row_dependente['prestador_dep_data_nasc'])));
                        }

                        echo "\"  class=\"validate[required]\"/>
					</span></td>
				</tr>
				
				<tr>
				<td  colspan=\"2\" height=\"1\" bgcolor=\"#DEF\"><a href=\"prestadorservico.php?id=3&regiao=$regiao&prestador=$id_prestador&excluir=" . $row_dependente['prestador_dep_id'] . " \" onclick=\"return(comfirm(\'Deseja excluir o dependente:" . $row_dependente['prestador_dep_nome'] . " ?')\" title=\"Excluir Dependente\">Excluir</a></td>
				</tr>
				<input type=\"hidden\" name=\"ids_dependente[]\" value=\"" . $row_dependente['prestador_dep_id'] . "\"/>";

                    endwhile;
                }
                echo'		</table>
			</div>
		</td>
	</tr>';
            } else {



                echo "<table width=\"100%\"id=\"mostrar_dep\" style=\"display:block;\">
	 <tr class=\"novo_dep\"  style=\"display:none;\">
				
				<td bgcolor=\"#CCCCCC\"  align=\"center\">
				 <span class=\"adicionar\" style=\"cursor:pointer;\"> <img src=\"../imagens/adicionar_dep.gif\" width=\"36\" height=\"26\" title=\"Adicionar Dependente.\" /></span> 
				</td>
				
				<td colspan=\"5\" bgcolor=\"#CCCCCC\" align=\"center\">				
					<div align=\"center\" class=\"style35\">DEPENDENTES</div>			
				</td>
				
		</tr>";


                echo "
		<tr class=\"novo_dep\">
				<td colspan=\"6\">
				
				<div id=\"tabela_dependente\" style=\"width:900px;\">
				
				<table width=\"100%\" bgcolor='#FFFFFF'>";
            }

            print '</table></td></tr>';
            echo"

</table>
<center>
<br>
<input type='hidden' name='id' value='4'>
<input type='hidden' name='regiao' value='$regiao'>
<input type='hidden' name='id_prestador' value='$id_prestador'>
<br>
<input type='submit' name='Submit' id='button' value='Atualizar'>
</center>
</form>
";


            break;

        case 4:




            $id_prestador = $_REQUEST['id_prestador'];



//VERIFICA SE O PRESTADOR É DO TIPO PESSSOAL FÍSICA

            if ($_POST['tipo_prestador'] == 3) {

                if (!empty($_POST['dep_nome'])) {

                    //RECEBE ARRAYS PARA ATUALIZAR O DEPENDENTE
                    $dependente_nome = $_POST['dep_nome'];
                    $dependente_parentesco = $_POST['dep_parentesco'];
                    $dependente_nascimento = $_POST['dep_data_nasc'];
                    $dependente_id = $_POST['ids_dependente'];


                    foreach ($dependente_id as $chave => $valor) {
                        if (!empty($dependente_nome[$chave]) or !empty($dependente_parentesco[$chave]) or !empty($dependente_nascimento[$chave])) {

                            $data_nasc2 = implode('-', array_reverse(explode('/', trim($dependente_nascimento[$chave]))));
                            $parentesco = trim($dependente_parentesco[$chave]);
                            $nome = trim($dependente_nome[$chave]);

                            mysql_query("UPDATE prestador_dependente SET prestador_dep_nome = '$nome', prestador_dep_parentesco = '$parentesco',  	prestador_dep_data_nasc = '$data_nasc2' WHERE prestador_dep_id = '$dependente_id[$chave]';") or die(mysql_error());
                        }
                        unset($data_nasc, $parentesco, $nome);
                    }
                }
                //FIM ATUALIZAR DEPENDENTE
                //INSERE NOVOS DEPENDENTES
                if (!empty($_POST['add_dep_nome'])) {

                    $add_dep_nome = $_POST['add_dep_nome'];
                    $add_dep_parentesco = $_POST['add_dep_parentesco'];
                    $add_dep_data_nasc = $_POST['add_dep_data_nasc'];


                    foreach ($add_dep_nome as $chave => $valor) {

                        if (empty($add_dep_nome[$chave]) or empty($add_dep_parentesco[$chave]) or empty($add_dep_data_nasc[$chave]))
                            continue;


                        $data_nasc2 = implode('-', array_reverse(explode('/', $add_dep_data_nasc[$chave])));

                        mysql_query("INSERT INTO prestador_dependente (prestador_id,prestador_dep_nome, prestador_dep_parentesco, prestador_dep_data_nasc, prestador_dep_status)
					VALUES
					('$id_prestador', '$add_dep_nome[$chave]', '$add_dep_parentesco[$chave]', '$data_nasc2', '1');") or die(mysql_error());
                    }
                }//FIM INSERE DEPENDENTES
            } else {

                $qr_dependente2 = mysql_query("SELECT * FROM prestador_dependente WHERE  prestador_id = '$id_prestador'");
                $verifica = mysql_num_rows($qr_dependente2);
                if ($verifica != 0)
                    mysql_query("UPDATE prestador_dependente SET prestador_dep_status = 0 WHERE prestador_id = '$id_prestador' ");
            }




            $id_prestador = $_REQUEST['id_prestador'];
            $id_projeto = $_REQUEST['projeto'];
            $id_user = $_COOKIE['logado'];
            $regiao = $_REQUEST['regiao'];
            $endereco = $_REQUEST['endereco'];
            $cnpj = $_REQUEST['cnpj'];
            $c_fantasia = $_REQUEST['c_fantasia'];
            $c_razao = $_REQUEST['c_razao'];
            $c_endereco = $_REQUEST['c_endereco'];
            $c_cnpj = $_REQUEST['c_cnpj'];
            $c_ie = $_REQUEST['c_ie'];
            $c_im = $_REQUEST['c_im'];
            $c_tel = $_REQUEST['c_tel'];
            $c_fax = $_REQUEST['c_fax'];
            $c_email = $_REQUEST['c_email'];
            $c_responsavel = $_REQUEST['c_responsavel'];
            $c_civil = $_REQUEST['c_civil'];
            $c_nacionalidade = $_REQUEST['c_nacionalidade'];
            $c_formacao = $_REQUEST['c_formacao'];
            $c_rg = $_REQUEST['c_rg'];
            $c_cpf = $_REQUEST['c_cpf'];
            $c_email2 = $_REQUEST['c_email2'];
            $c_site = $_REQUEST['c_site'];
            $co_responsavel = $_REQUEST['co_responsavel'];
            $co_tel = $_REQUEST['co_tel'];
            $co_fax = $_REQUEST['co_fax'];
            $co_civil = $_REQUEST['co_civil'];
            $co_nacionalidade = $_REQUEST['co_nacionalidade'];
            $co_email = $_REQUEST['co_email'];
            $co_municipio = $_REQUEST['co_municipio'];
            $assunto = $_REQUEST['assunto'];
            $objeto = $_REQUEST['objeto'];
            $especificacao = $_REQUEST['especificacao'];
            $data_proc = $_REQUEST['data_proc'];
            $prestador_tipo = $_POST['tipo_prestador'];

            $data_inicio_f = converteData($data_inicio);
            $data_proc_f = converteData($data_proc);


            $result_cont = mysql_query("SELECT * FROM prestadorservico where id_regiao = '$regiao' AND prestador_tipo<>0");
            $row_cont = mysql_num_rows($result_cont);
            $row_cont = $row_cont + 1;
            $num_id = sprintf("%03s", $row_cont);
            $numero = $num_id . "/" . date('Y');


            mysql_query("UPDATE prestadorservico SET 
endereco = '$endereco', 
cnpj = '$cnpj', 
c_fantasia = '$c_fantasia', 
c_razao = '$c_razao', 
c_endereco = '$c_endereco', 
c_cnpj = '$c_cnpj', 
c_ie = '$c_ie', 
c_im = '$c_im', 
c_tel = '$c_tel', 
c_fax = '$c_fax', 
c_email = '$c_email', 
c_responsavel = '$c_responsavel', 
c_civil = '$c_civil', 
c_nacionalidade = '$c_nacionalidade', 
c_formacao = '$c_formacao', 
c_rg = '$c_rg', 
c_cpf = '$c_cpf', 
c_email2 = '$c_email2', 
c_site = '$c_site', 
co_responsavel = '$co_responsavel', 
co_tel = '$co_tel', 
co_fax = '$co_fax', 
co_civil = '$co_civil', 
co_nacionalidade = '$co_nacionalidade', 
co_email = '$co_email', 
co_municipio = '$co_municipio', 
assunto = '$assunto', 
objeto = '$objeto', 
especificacao = '$especificacao',
prestador_tipo = '$prestador_tipo',
data_proc = '$data_proc_f' WHERE id_prestador = '$id_prestador' ") or die("Erro<br>" . mysql_error());
            print "
<script>
alert (\"$id_prestado - Dasos Atualizados!\"); 
location.href=\"prestadorservico.php?id=1&regiao=$regiao\"
</script>";
    } // FECHANDO O   CASE

    /* Liberando o resultado */
//mysql_free_result($result);
    /* Fechando a conexão */
//mysql_close($conn);
    ?>