<?php
include('../../conn.php');
include("../../wfunction.php");
include("../../funcoes.php");


$id_folha = $_REQUEST['id_folha'];
$tipo_guia = $_REQUEST['tipo_guia'];  // 1 - GPS, 2 - FGTS , 3 - PIS,  4 - IR , 5 - VALE TRANSPORTE, 6 - VALE ALIMENTACAO/REFEICAO, 7 - IR de FERIAS, 8 - SINDICATO
$tipo_contrato = $_REQUEST['tipo_contrato']; // 1-Auton,2-Clt,3-Coop,4-AutoPJ
$mes_consulta = $_REQUEST['mes_consulta'];
$ano_consulta = $_REQUEST['ano_consulta'];
$usuario = carregaUsuario();

$nomeContratacao = array("1" => "Autonomo", "2" => "CLT", "3" => "Cooperado", "4" => "Autonomo/PJ");

$tabela_consulta = 'rh_folha';

if ($tipo_contrato != '2') {
    $tabela_consulta = 'folhas';
}

////////////////////////////
////////DADOS DA FOLHA  ////
////////////////////////////
$qrFolha = "SELECT     A.*,
            B.nome as nomeprojeto,
            C.nome as usuario
            FROM {$tabela_consulta} AS A
            LEFT JOIN projeto AS B ON (A.projeto=B.id_projeto)
            LEFT JOIN funcionario AS C ON(A.user=C.id_funcionario)
            WHERE A.id_folha = {$id_folha}";


$resultF = mysql_query($qrFolha);
$folha = mysql_fetch_assoc($resultF);


$mes = $folha['mes'];
$ano = $folha['ano'];
$projeto = $folha['projeto'];
$regiao = $folha['regiao'];

switch ($tipo_guia) {
    case 1:
        $texto = "GPS";
        $tipo = 169;
        $tipo_pg = 1;
        $subgrupo = 3;
        $id_nome = 33747464;
        break;
    case 2:
        $texto = "FGTS";
        $tipo = 167;
        $tipo_pg = 2;
        $subgrupo = 3;
        $id_nome = 33747463;
        break;
    case 3:
        $texto = "PIS";
        $tipo = 171;
        $tipo_pg = 3;
        $subgrupo = 3;
        $id_nome = 33747328;
        break;
    case 4:
        $texto = "IR";
        $tipo = 168;
        $tipo_pg = 4;
        $subgrupo = 3;
        $id_nome = 33747328;
        break;
//    case 5:
//        $texto = "VALE TRANSPORTE";
//        $tipo = 162;
//        $tipo_pg = 5;
//        $subgrupo = 2;
//        $id_nome = 3277; //FETRANSPOR
//
//        break;
//    case 6:
//        $texto = "VALE REFEIÇÃO / ALIMENTAÇÃO";
//        $tipo = 165;
//        $tipo_pg = 6;
//        $subgrupo = 2;
//        $id_nome = 3680; //SODEXO
//
//        break;
    case 7:
        $texto = "IR DE FÉRIAS";
        $tipo = 168;
        $tipo_pg = 7;
        $subgrupo = 3;
        $id_nome = 33747328;
        break;
    
     case 8:
        $texto = "PAGAMENTO DE SINDICATO";
        $tipo = 171;
        $tipo_pg = 8;
        $subgrupo = 3;
        $id_nome = ''; // SINDICATO
        
        $sql_sind = "SELECT d.id_sindicato,d.nome 
                    FROM  rh_folha_proc AS b
                    INNER JOIN rhsindicato AS d ON (b.id_sindicato = d.id_sindicato)
                    WHERE b.id_folha = {$id_folha}
                    GROUP BY d.id_sindicato ORDER BY d.nome;";
        $result_sind = mysql_query($sql_sind);
        $sindicatos['-1']='-- Selecione o Sindicato --';
        while ($row = mysql_fetch_assoc($result_sind)) {
            $sindicatos[$row['id_sindicato']] = "{$row['id_sindicato']} - {$row['nome']}";
        }
        $selectSindicato = montaSelect($sindicatos, null, array('name'=>'sindicato','id'=>'sinicato'));
}


$arrayBancosLiberados = array(104=>'BRADESCO COOPUSTTEC'); // A Dileane pediu para liberar este banco

$query_banco = mysql_query("SELECT id_banco FROM bancos WHERE id_regiao = '{$regiao}' AND id_projeto = '{$projeto}' AND status_reg = '1'");
$bancoSel = @mysql_result($query_banco, 0);
if ($bancoSel == 0) {
    echo "ESSE PROJETO NÃO TEM BANCO.";
    exit;
}

$regioes = mysql_result(mysql_query("SELECT  GROUP_CONCAT(id_regiao) as regioes FROM regioes WHERE id_master = (SELECT id_master FROM regioes WHERE id_regiao = {$regiao});"), 0);
$query_regiao = mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '{$regiao}'");
$nome_regiao = @mysql_result($query_regiao, 0);

if ($folha['terceiro'] == '1') {
    if ($folha['tipo_terceiro'] == 3) {
        $decimo3 = " - 13ª integral";
    } else {
        $decimo3 = " - 13ª ({$folha['tipo_terceiro']}ª) Parcela";
    }
}


$nome_completo = urldecode("<span class='textRemove'>$nomeContratacao[$tipo_contrato]</span> $texto <span class='viewNome'></span>" . mesesArray($mes) . "/$ano {$folha['nomeprojeto']} <span class='textRemove'> Folha: $id_folha $decimo3 - $nome_regiao</span>");


////////////////////////////////////////////////////////////////////
////// PEGANDO OS DADOS ENVIADOS PELO FORMULÁRIO   /////////////////
////////////////////////////////////////////////////////////////////
if (isset($_POST['acao'])) {
    
    $mes_consulta = $_REQUEST['mes_consulta'];
    $ano_consulta = $_REQUEST['ano_consulta'];
    $banco = $_POST['bancos'];
    $id_user = $_COOKIE['logado'];
    $nome = $_POST['nome'];
    $especificacao = "";
    $tipo_guia = $_POST['tipo_guia'];
    $adicional = 0;
    $valor = str_replace('.', '', $_POST['valor']);
    $data = implode('-', array_reverse(explode('/', $_POST['data'])));
    $tipo_contrato = $_POST['tipo_contrato'];
    $id_folha = $_POST['id_folha'];
    $mes_pg = str_pad($_POST['mes'], 2, "0", STR_PAD_LEFT);
    $ano_pg = $_POST['ano'];
    $tipo_pg = $_POST['tipo_pg'];
    $tipo_descricao = $_REQUEST['tipo_pgt'];
    $subgrupo = $_POST['subgrupo'];
    $folha_projeto = $_POST['folha_projeto'];
    $folha_regiao = $_POST['folha_regiao'];
    $id_nome = $_POST['id_nome'];
    $cod_barra_gerais = implode('', $_POST['campo_codigo_gerais']);
//    $qr_banco = mysql_query("SELECT * FROM bancos WHERE id_banco = $banco");
//    $row_banco = mysql_fetch_assoc($qr_banco);
    $arquivo_1 = $_FILES['arquivo1'];
    $arquivo_2 = $_FILES['arquivo2'];

    // novo - sindicato
    $sindicato = (isset($_REQUEST['sindicato']))?$_REQUEST['sindicato']:'NULL';
    
    $diretorio_destino = '../../comprovantes/';

    ///DADOS DO BANCO
    $qr_banco = mysql_query("SELECT * FROM bancos WHERE id_banco = '{$banco}'");
    $row_banco = mysql_fetch_assoc($qr_banco);

    if ($tipo_guia == 5 || $tipo_guia == 6) {
        if (!empty($nome)) {
            $nome = $nome;
        } else {
            $nome = $_REQUEST['nome_alt'];
        }
    }
    
    
    // add o nome do sinticato
    if($tipo_guia == 8){
        $result_sind = mysql_query("SELECT id_sindicato,nome FROM rhsindicato WHERE id_sindicato = {$sindicato}");
        $sind_row = mysql_fetch_assoc($result_sind);
        $nome .= " SINDICATO: {$sind_row['id_sindicato']} - {$sind_row['nome']} ";
    }
    
    $nome = strip_tags($nome);

    //INSERINDO SAÍDA
    $sql = "INSERT INTO saida (id_regiao, id_projeto, id_banco, id_user, nome, id_nome, especifica, tipo, adicional, valor, data_proc, data_vencimento, status,comprovante, nosso_numero, tipo_boleto, cod_barra_gerais, id_referencia, id_tipo_pag_saida, entradaesaida_subgrupo_id,rh_sindicato)
           VALUES ('$row_banco[id_regiao]', '$row_banco[id_projeto]', '$banco', '$_COOKIE[logado]', '$nome','$id_nome', '$nome', '$tipo', '$adicional', '$valor',NOW(), '$data',  '1', '2', '$nosso_numero', '2', '$cod_barra_gerais','1', '1', '$subgrupo','{$sindicato}') ";
    mysql_query($sql);
    $id_saida = mysql_insert_id();


    ///INSERINDO ANEXO 
    $sql_anexos = "INSERT INTO saida_files (id_saida,tipo_saida_file) VALUES ('$id_saida','.pdf')";

    mysql_query($sql_anexos);

    $id_saida_files_1 = mysql_insert_id();
    $nome_arquivo1 = $id_saida_files_1 . '.' . $id_saida . '.pdf';

    move_uploaded_file($arquivo_1['tmp_name'], $diretorio_destino . $nome_arquivo1);


    ////INSERINDO ANEXO SEFIP
    if ($tipo_guia == 2 and !empty($arquivo_2['tmp_name'])) {

        mysql_query($sql_anexos);

        $id_saida_files_2 = mysql_insert_id();
        $nome_arquivo2 = $id_saida_files_2 . '.' . $id_saida . '.pdf';

        move_uploaded_file($arquivo_2['tmp_name'], $diretorio_destino . $nome_arquivo2);
    }

    ////INSERINDO o 2º ANEXO IR DE FERIAS
    if ($tipo_guia == 7 && !empty($arquivo_2['tmp_name'])) {
        mysql_query($sql_anexos);

        $id_saida_files_2 = mysql_insert_id();
        $nome_arquivo2 = $id_saida_files_2 . '.' . $id_saida . '.pdf';

        move_uploaded_file($arquivo_2['tmp_name'], $diretorio_destino . $nome_arquivo2);
    }    

    // controle de pagamentos adicionado em 20/09/2010 as 17:00 hs
    switch ($tipo_contrato) {

        case 2: $tipo_contrato_pg = 1;
            break;
        case 3: $tipo_contrato_pg = 2;
            break;
    }


    $sql_pagamentos = "INSERT INTO pagamentos (id_saida,tipo_contrato_pg,id_folha, mes_pg, ano_pg, tipo_pg, tipo_descricao) VALUES ('$id_saida','$tipo_contrato_pg','$id_folha','$mes_pg','$ano_pg','$tipo_pg','$tipo_descricao')";
    mysql_query($sql_pagamentos);


    /*     * **************GRAVA EM OUTRA TABELA O RELACIONAMENTO DE TIPOS DE PAGAMENTO***************** */
    $ultimo_id = mysql_insert_id();
    $countP = 0;
    $items = $_REQUEST['tipo_pgto'];
    $sql = "INSERT INTO pagamentos_tipo (id_pg,tipo_pg) VALUES ";
    foreach ($items as $key => $value) {
        $sql .= " (" . $ultimo_id . "," . $value . ")";
        $countP++;
        if ($countP++ >= 1) {
            $sql .= ",";
        }
    }

    $sql = substr($sql, 0, -1);
    mysql_query($sql);

    /*     * ******************************************************************************************* */

    echo 'Envio concluído...';
    echo "<script> 
            setTimeout(function(){
            window.parent.location.href = 'http://" . $_SERVER['HTTP_HOST'] . "/intranet/rh/pagamentos/index.php?id=1&regiao=$regiao&mes=$mes_consulta&ano=$ano_consulta&filtrar=1&tipo_pagamento=1&tipo_contrato=2';
            parent.eval('tb_remove()')
            },3000)    
    </script>";
    exit;
}

?>
<html>
    <head>
        <title>RH - Pagamentos</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js"></script>
        <script type="text/javascript" src="../../jquery/datepicker-lite/jquery-ui-1.8.4.custom.min.js"></script>
        <link rel="stylesheet" type="text/css" href="../../jquery/datepicker-lite/jquery-ui-1.8.4.custom.css"/>
        <script type="text/javascript" src="../../uploadfy/scripts/jquery.uploadify.v2.1.0.js"></script>
        <script type="text/javascript" src="../../uploadfy/scripts/swfobject.js"></script>
        <script type="text/javascript" src="../../jquery/mascara/jquery.maskedinput-1.2.2.js"></script>
        <script type="text/javascript" src="../../jquery/priceFormat.js"></script>
        <link rel="stylesheet" type="text/css" href="../../uploadfy/css/uploadify.css"/>

        <script type="text/javascript" src="../../jquery/mascara/jquery.maskedinput-1.2.2.js"></script>
        <style>
            .aviso { color: #f96a6a;
                     font-weight: bold;
            }
            h3{ color: #0bbfe7; } 
        </style>
        <script>
            $(function(){
                
                $(".mostrarNome").hide();
                
                $('#valor').priceFormat({
                    prefix: '',
                    centsSeparator: ',',
                    thousandsSeparator: '.'
                });
                var id_saida = 0;
	
                $('#data').datepicker({
                    dateFormat: 'dd/mm/yy',
                    changeMonth: true,
                    changeYear: true
                });
        
                function reseta(){
                    $("input[type*='text']").each(function(){
                        $(this).val('');
                    });
                }

                $('input[name=cod_barra]').change(function(){    
                    if($(this).val() == 1){        
                        $('.campo_codigo_gerais').show();
                    } else{
                        $('.campo_codigo_gerais').hide();
                        $('#campo_codigo_gerais1, #campo_codigo_gerais2, #campo_codigo_gerais3, #campo_codigo_gerais5 , #campo_codigo_gerais4, #campo_codigo_gerais6, #campo_codigo_gerais7, #campo_codigo_gerais8').val(''); 
                    }    
                })
                $('#campo_codigo_gerais1, #campo_codigo_gerais2, #campo_codigo_gerais3, #campo_codigo_gerais5') .keyup(function(){ limita_caractere($(this), 5, 1) });
                $('#campo_codigo_gerais4, #campo_codigo_gerais6').keyup(function(){ limita_caractere($(this), 6, 1) });
                $('#campo_codigo_gerais7').keyup(function(){ limita_caractere($(this), 1, 1) });  

                $('#campo_codigo_gerais8').keyup(function(){
                    if ($(this).val().length >= 14){
                        $(this).blur(); 
                        var valor = $(this).val().substr(0, limite);
                        $(this).val(valor) ;         
                    }    
                });

                function limita_caractere(campo, limite, muda_campo){    
                    var tamanho = campo.val().length;   

                    if(tamanho >= limite ){
                        campo.next().focus();
                        var valor = campo.val().substr(0, limite);
                        campo.val(valor)        
                    } 
                }


                //////VALIDANDO
                $('#arquivo1,#arquivo2').change(function(){
             
                    var aviso = $('.aviso');
                    var arquivo = $(this);
                    var extensao_arquivo = (arquivo.val().substring(arquivo.val().lastIndexOf("."))).toLowerCase();            
           
            
                    if(arquivo.val() != '' && extensao_arquivo == '.pdf'){
                        arquivo.css('background-color', '#51b566')
                        .css('color','#FFF');
                        aviso.html('');
                    } 
            
                    if(extensao_arquivo != '.pdf') {
                        arquivo.css('background-color', ' #f96a6a')
                        .css('color','#FFF');
                        aviso.html('Este arquivo não é um PDF.');
                    }
            
            
                });
           

                $('#enviar').click(function(){
                                
                    var aviso = $('.aviso');
                    var arquivo = $('#arquivo1');
                    var extensao_arquivo = (arquivo.val().substring(arquivo.val().lastIndexOf("."))).toLowerCase();   
            
                    var arquivo2 = $('#arquivo2');
                    if(arquivo2.val() != ''){
                        console.log('aqui');
                    }
                    var extensao_arquivo = (arquivo.val().substring(arquivo.val().lastIndexOf("."))).toLowerCase();     
                    
                    var count = 0;
                    $(this).attr('disabled','disabeld');
                    $(".j_verifica:checked").each(function(i, value){
                        count++;
                    });
                    
                    if($('#tipo_guia').val() == 5 || $('#tipo_guia').val() == 6){
                        if(count < 1){
                            aviso.html('Selecione um tipo de pagamento');
                            $("#enviar").removeAttr('disabled');
                            return false;
                        }
                    }
                    
                    if($('#valor').val() == ''){                
                        aviso.html('Digite o valor.');
                        $("#enviar").removeAttr('disabled');
                        return false;
                    }            
            
                    if($('#data').val() == ''){                
                        aviso.html('Digite a data.');
                        $("#enviar").removeAttr('disabled');
                        return false;
                    }            
            
                    if($('#bancos').val() == ''){                
                        aviso.html('Selecione o banco.');
                        $("#enviar").removeAttr('disabled');
                        return false;
                    }            
            
                    if(arquivo.val() == ''){                
                        aviso.html('O arquivo não foi anexado');   
                        $("#enviar").removeAttr('disabled');
                        return false;
                    }     
            
                    if($('#tipo_guia').val() == 2 || $('#tipo_guia').val() == 8 ){
                    
                        if(arquivo2.val() == ''){                
                            aviso.html('O arquivo não foi anexado');   
                            $("#enviar").removeAttr('disabled');
                            return false;
                        } 
              
                    }            
                    
                    if(extensao_arquivo != '.pdf'){                
                        aviso.html('Este arquivo não é um PDF.');
                        $("#enviar").removeAttr('disabled');
                        return false;
                    }
                    $('#form1').submit();
                });
                
                /**JQUERY GORDO*/
                $(".j_tipo_pgto").click(function(){
                    $(this).each(function(i, value){
                        if($(this).val() == "1"){
                            $(".mostrarNome").show();
                            $(".textRemove").hide();
                            $("#nomeTitulo").val("");
                            $(".viewNome").text("").show();
                        }else if($(this).val() == "2"){
                            $(".mostrarNome").hide();
                            $(".textRemove").show();
                            $(".viewNome").hide();
                        }
                    });
                });
                                
                $("#nomeTitulo").keyup(function(){
                    var nome = ($(this).val());
                    $(".viewNome").css({textTransform:"uppercase"}).html(' - ' + nome + ' - ');
                    $("#nome").val($(".nomeCompleto").text());
                });
            })
        </script>
    </head>
    <body>
        <form name="form1" id="form1" action="zcadastro_1.php" method="post" enctype="multipart/form-data">

            <input type="hidden" name="id_folha" id="id_folha" value="<?php echo $id_folha ?>" />
            <input type="hidden" name="tipo_contrato" id="tipo_contrato" value="<?php echo $tipo_contrato ?>" />
            <input type="hidden" name="tipo_guia" id="tipo" value="<?php echo $tipo_guia ?>" />
            <input type="hidden" name="subgrupo" id="subgrupo" value="<?php echo $subgrupo ?>" />
            <input type="hidden" name="fregiao" id="fregiao" value="<?php echo $regiao ?>" />
            <input type="hidden" name="fprojeto" id="fprojeto" value="<?php echo $projeto ?>" />
            <input type="hidden" name="mes" id="mes" value="<?php echo $mes ?>" />
            <input type="hidden" name="ano" id="ano" value="<?php echo $ano ?>" />
            <input type="hidden" name="tipo_pg" id="tipo_pg" value="<?php echo $tipo_pg ?>" />
            <input type="hidden" name="id_nome" id="id_nome" value="<?php echo $id_nome ?>" />
            <input type="hidden" name="mes_consulta" id="mes_consulta" value="<?php echo $mes_consulta ?>" />
            <input type="hidden" name="ano_consulta" id="ano_consulta" value="<?php echo $ano_consulta ?>" />

            <table width="90%" border="0" align="center" cellpadding="5" cellspacing="0">

                <tr>
                    <td colspan="4" align="center" class="esconderTitulo">
                        <?php
                        echo "<span class='nomeCompleto'> " . $nome_completo . "</span>";
                        ?>
                    </td>
                </tr>

                <?php if ($texto == "VALE TRANSPORTE" || $texto == "VALE REFEIÇÃO / ALIMENTAÇÃO") { ?>
                    <br />
                    <tr>
                        <td width="269">&nbsp;</td>
                        <td width="205" align="right"><span style="font-size:12px;">Tipo: </span></td>
                        <td width="1081">
                            <input name="tipo_pgt" id="tipo_pgto" class="j_tipo_pgto" type="radio" value="2" checked="checked"/> Lote
                            <input name="tipo_pgt" id="tipo_pgto" class="j_tipo_pgto" type="radio" value="1"/> Unitário
                        <td width="36">&nbsp;</td>
                    </tr

                    <tr>
                        <td width="269">&nbsp;</td>
                        <td width="205" align="right"><span style="font-size:12px;">Tipo de Pagamento: </span></td>
                        <td width="1081">
                            <input name="tipo_pgto[]" id="recarga" class="j_verifica" type="checkbox" value="1"  checked="checked"/><label for="recarga">Recarga</label>
                            <input name="tipo_pgto[]" id="cancelamento" class="j_verifica" type="checkbox" value="2"/><label for="cancelamento">Cancelamento</label>
                            <input name="tipo_pgto[]" id="segunda_via" class="j_verifica" type="checkbox" value="3"/><label for="segunda_via">Segunda Via</label>
                        </td>
                        <td width="36">&nbsp;</td>
                    </tr>

                    <tr class="mostrarNome">
                        <td width="269">&nbsp;</td>
                        <td width="205" align="right"><span style="font-size:12px;">Nome: </span></td>
                        <td width="1081"><input name="nomeTitulo" type="text" id="nomeTitulo" size="65" /></td>
                        <td width="36">&nbsp;</td>
                    </tr>

                    <tr>
                        <td><input type="hidden" name="nome_alt" id="nome_alt" value="<?= $nome_completo ?>" /></td>
                        <td><input type="hidden" name="nome" id="nome" value="" /></td>
                    </tr>
                <?php } ?>

                <tr>
                    <td width="269">&nbsp;</td>
                    <td width="205" align="right"><span style="font-size:12px;">Valor : R$</span></td>
                    <td width="1081"><input name="valor" type="text" id="valor" size="13" /></td>
                    <td width="36">&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td align="right"><span style="font-size:12px;">Data :</span></td>
                    <td><input name="data" type="text" id="data" size="13" /></td>
                    <td>&nbsp;</td>
                </tr>

                <?php if ($texto == "PAGAMENTO DE SINDICATO") { ?>
                <tr>
                    <td>&nbsp;</td>
                    <td align="right"><span style="font-size:12px;">Sindicato :</span></td>
                    <td><?= $selectSindicato ?></td>
                </tr>
                <?php } ?>

                <tr>
                    <td>&nbsp;</td>
                    <td align="right"><span style="font-size:12px;">Banco :</span></td>
                    <td>
                        <label for="bancos"></label>
                        <select name="bancos" id="bancos">
                            <option value="">Selecione...</option>
                            <?php
                            $query_banco = mysql_query("SELECT * FROM bancos WHERE id_regiao = '{$usuario['id_regiao']}' AND status_reg = '1'");
                            foreach ($arrayBancosLiberados as $idBanco => $nomeBanco) {
                                $selected = ($bancoSel == $banco['id_banco']) ? "selected='selected'":"";
                              echo "<option  value='$idBanco' {$selected}>$idBanco -  $nomeBanco</option>";  
                            }
                            while ($banco = mysql_fetch_array($query_banco)) {
                                $selected = ($bancoSel == $banco['id_banco']) ? "selected='selected'":"";
                                echo "<option  value='{$banco['id_banco']}' {$selected}>{$banco['id_banco']} - {$banco['nome']}</option>";
                            } 
                            ?>
                        </select></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="2" align="right" valign="midlle">Selecione um arquivo</td>
                    <td>
                        <input type="file" name="arquivo1" id="arquivo1" />         
                        <br />
                        <span style="color:#F00; font-size:10px;" >Aguarde a mensagem de conclus&atilde;o!</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><input type="hidden" name="nome" id="nome" value="<?= $nome_completo ?>" /></td>
                    <td colspan="2"><div id="progressBar"></div></td>
                    <td>&nbsp;</td>
                </tr>

                <?php if ($texto == 'GPS') { ?>
                    <tr>
                        <td></td>
                        <td align="right">Código de barras:</td>
                        <td colspan="3">
                            <input name="cod_barra" type="radio" value="1"/> Sim<br>
                            <input name="cod_barra" type="radio" value="0"/> Não <br>
                        </td>
                    </tr>
                    <tr class="campo_codigo_gerais" style="display:none;"> 
                        <td></td>
                        <td></td>
                        <td colspan="2">
                            <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais1" style="width:50px;"/>.
                            <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais2" style="width:50px;"/>.
                            <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais3" style="width:50px;"/>
                            <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais4" style="width:60px;"/>.
                            <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais5" style="width:50px;"/>
                            <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais6" style="width:60px;"/>.
                            <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais7" style="width:30px;"/>
                            <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais8" style="width:130px;"/>    
                        </td>    
                    </tr>
                    <!--tr>
                        <td colspan="2" align="right" valign="midlle"><?php if ($texto == "GPS") { ?> GPS<?php } ?></td>
                        <td><input type="file" name="arquivo1" id="arquivo1" />         
                            <br />
                            <span style="color:#F00; font-size:10px;" >Aguarde a mensagem de conclus&atilde;o!</span></td>
                        <td>&nbsp;</td>
                    </tr-->
                    <tr>
                        <td><input type="hidden" name="nome" id="nome" value="<?= $nome_completo ?>" /></td>
                        <td colspan="2"><div id="progressBar"></div></td>
                        <td>&nbsp;</td>
                    </tr>
                <?php } ?>
                <?php if ($texto == "FGTS") { ?> 
                    <tr>
                        <td colspan="2" align="right" valign="midlle"> SEFIP </td>
                        <td> <input type="file" name="arquivo2" id="arquivo2" />  </td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td><input type="hidden" name="nome" id="nome" value="<?= $nome_completo ?>" /></td>

                    </tr>
                <?php } 
                    if($texto == "IR DE FÉRIAS"){ ?>
                    <tr>
                        <td colspan="2" align="right" valign="midlle"> Lista de funcionários </td>
                        <td> <input type="file" name="arquivo2" id="arquivo2" />  </td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td><input type="hidden" name="nome" id="nome" value="<?= $nome_completo ?>" /></td>

                    </tr>
                <?php  }
                ?>

                <tr>
                    <td colspan="4" align="center">
                        <input type="hidden" name="folha_projeto" id="folha_projeto" value="<?php echo $folha['projeto'] ?>" />
                        <input type="hidden" name="folha_regiao" id="folha_regiao"  value="<?php echo $folha['regiao'] ?>" />

                        <p class="aviso"></p>
                            <input type="hidden" name="acao" value="cadastrar"/>
                            <input type="button" value="Enviar" name="enviar" id="enviar" class="botaoGordo"/>
                    </td>
                </tr>
            </table>

        </form>
    </body>
</html>