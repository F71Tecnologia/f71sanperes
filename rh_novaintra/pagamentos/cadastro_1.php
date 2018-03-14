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
        $selectSindicato = montaSelect($sindicatos, null, array('name'=>'sindicato','id'=>'sinicato', 'class' => 'form-control input-sm'));
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
//    $valor = str_replace('.', '', $_POST['valor']);
    $valor = str_replace(',', '.',str_replace('.', '', $_POST['valor']));
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
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Pagamentos</title>
        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/add-ons.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../../jquery/thickbox/thickbox.css" type="text/css" media="screen" />
    </head>
    <body class="transparent">
        <form name="form1" id="form1" action="" method="post" enctype="multipart/form-data" class="form-horizontal">
            <input type="hidden" name="id_folha" id="id_folha" value="<?php echo $id_folha ?>" />
            <input type="hidden" name="tipo_contrato" id="tipo_contrato" value="<?php echo $tipo_contrato ?>" />
            <input type="hidden" name="tipo_guia" id="tipo_guia" value="<?php echo $tipo_guia ?>" />
            <input type="hidden" name="subgrupo" id="subgrupo" value="<?php echo $subgrupo ?>" />
            <input type="hidden" name="fregiao" id="fregiao" value="<?php echo $regiao ?>" />
            <input type="hidden" name="fprojeto" id="fprojeto" value="<?php echo $projeto ?>" />
            <input type="hidden" name="mes" id="mes" value="<?php echo $mes ?>" />
            <input type="hidden" name="ano" id="ano" value="<?php echo $ano ?>" />
            <input type="hidden" name="tipo_pg" id="tipo_pg" value="<?php echo $tipo_pg ?>" />
            <input type="hidden" name="id_nome" id="id_nome" value="<?php echo $id_nome ?>" />
            <input type="hidden" name="mes_consulta" id="mes_consulta" value="<?php echo $mes_consulta ?>" />
            <input type="hidden" name="ano_consulta" id="ano_consulta" value="<?php echo $ano_consulta ?>" />
            <div class="row text-center no-margin">
                <div class="col-xs-12 form-group">
                    <h5 class="esconderTitulo"><?=$nome_completo?></h5>
                </div>
                <?php if ($texto == "VALE TRANSPORTE" || $texto == "VALE REFEIÇÃO / ALIMENTAÇÃO") { ?>
                    <div class="col-xs-12 form-group">
                        <label class="control-label col-xs-3">Tipo: </label>
                        <div class="col-xs-8">
                            <input name="tipo_pgt" id="tipo_pgto" class="j_tipo_pgto" type="radio" value="2" checked="checked"/> Lote
                            <input name="tipo_pgt" id="tipo_pgto" class="j_tipo_pgto" type="radio" value="1"/> Unitário
                        </div>
                    </div>
                    <div class="col-xs-12 form-group">
                        <label class="control-label col-xs-3">Tipo de Pagamento: </label>
                        <div class="col-xs-1 text-left checkbox">
                            <label for="recarga"><input name="tipo_pgto[]" id="recarga" class="j_verifica" type="checkbox" value="1"  checked="checked"/> Recarga</label>
                        </div>
                        <div class="col-xs-1 text-left checkbox">
                            <label for="cancelamento"><input name="tipo_pgto[]" id="cancelamento" class="j_verifica" type="checkbox" value="2"/> Cancelamento</label>
                        </div>
                        <div class="col-xs-1 text-left checkbox">
                            <label for="segunda_via"><input name="tipo_pgto[]" id="segunda_via" class="j_verifica" type="checkbox" value="3"/> Segunda Via</label>
                        </div>
                    </div>
                    <div class="col-xs-12 form-group mostrarNome">
                        <label class="control-label col-xs-3">Nome: </label>
                        <div class="col-xs-8"><input name="nomeTitulo" type="text" id="nomeTitulo" class="form-control input-sm" /></div>
                    </div>
                    <input type="hidden" name="nome_alt" id="nome_alt" value="<?= $nome_completo ?>" />
                    <input type="hidden" name="nome" id="nome" value="" />
                <?php } ?>
                <div class="col-xs-12 form-group">
                    <label class="control-label col-xs-3">Valor: R$</label>
                    <div class="col-xs-3"><input name="valor" type="text" id="valor" class="form-control input-sm" /></div>
                    <label class="control-label col-xs-1">Data:</label>
                    <div class="col-xs-4"><input name="data" type="text" id="data" class="form-control input-sm inputPequeno validate[required]" /></div>
                </div>
                <?php if ($texto == "PAGAMENTO DE SINDICATO") { ?>
                <div class="col-xs-12 form-group">
                    <label class="control-label col-xs-3">Sindicato:</label>
                    <div class="col-xs-8"><?= $selectSindicato ?></div>
                </div>
                <?php } ?>
                <div class="col-xs-12 form-group">
                    <label class="control-label col-xs-3">Banco:</label>
                    <div class="col-xs-8">
                        <select name="bancos" id="bancos" class="form-control input-sm">
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
                            } ?>
                        </select>
                    </div>
                </div>
                <div class="col-xs-12 form-group">
                    <label class="control-label col-xs-3">Selecione um arquivo:</label>
                    <div class="col-xs-4">
                        <input type="file" name="arquivo1" id="arquivo1" class="form-control" />
                    </div>
                    <div class="col-xs-4">
                        <span class="form-control text-danger text-left no-border">Aguarde a mensagem de conclus&atilde;o!</span>
                    </div>
                    <input type="hidden" name="nome" id="nome" value="<?= $nome_completo ?>" />
                    <div id="progressBar"></div>
                </div>
                <?php if ($texto == 'GPS') { ?>
                    <div class="col-xs-12 form-group">
                        <label class="control-label col-xs-3">Código de barras:</label>
                        <div class="col-xs-1 text-left no-padding-r checkbox">
                            <label><input name="cod_barra" type="radio" value="1"/> Sim</label>
                        </div>
                        <div class="col-xs-1 text-left no-padding-r checkbox">
                            <label><input name="cod_barra" type="radio" value="0"/> Não</label>
                        </div>
                    </div>
                    <div class="col-xs-offset-1 col-xs-10">
                        <div class="form-group campo_codigo_gerais" style="display:none;">
                            <div class="col-xs-12 input-group pull-left">
                                <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais1" class="form-control"/>
                                <div class="input-group-addon no-padding-hr">.</div>
                                <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais2" class="form-control"/>
                                <div class="input-group-addon no-padding-hr">.</div>
                                <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais3" class="form-control"/>
                            <div class="input-group-addon no-padding-hr"></div><!--/div>
                            <div class="col-xs-3 input-group pull-left"-->
                                <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais4" class="form-control"/>
                                <div class="input-group-addon no-padding-hr">.</div>
                                <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais5" class="form-control"/>
                            <div class="input-group-addon no-padding-hr"></div><!--/div>
                            <div class="col-xs-3 input-group pull-left"-->
                                <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais6" class="form-control"/>
                                <div class="input-group-addon no-padding-hr">.</div>
                                <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais7" class="form-control"/>
                            <div class="input-group-addon no-padding-hr"></div><!--/div>
                            <div class="col-xs-3 input-group pull-left"-->
                                <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais8" class="form-control"/> 
                            </div>
                            <input type="hidden" name="nome" id="nome" value="<?= $nome_completo ?>" />
                            <div id="progressBar"></div>
                        </div>
                    </div>
                <?php } ?>
                <?php if ($texto == "FGTS") { ?> 
                    <div class="col-xs-12 form-group">
                        <label class="control-label col-xs-3">SEFIP:</label>
                        <div class="col-xs-8"><input type="file" name="arquivo2" id="arquivo2" class="form-control" /></div>
                        <input type="hidden" name="nome" id="nome" value="<?= $nome_completo ?>" />
                    </div>
                <?php } ?>
                <?php if ($texto == "IR DE FÉRIAS") { ?> 
                    <div class="col-xs-12 form-group">
                        <label class="control-label col-xs-3">Lista de funcionários:</label>
                        <div class="col-xs-8"><input type="file" name="arquivo2" id="arquivo2" class="form-control" /></div>
                        <input type="hidden" name="nome" id="nome" value="<?= $nome_completo ?>" />
                    </div>
                <?php } ?>
                <div class="col-xs-12 form-group">
                    <input type="hidden" name="folha_projeto" id="folha_projeto" value="<?php echo $folha['projeto'] ?>" />
                    <input type="hidden" name="folha_regiao" id="folha_regiao"  value="<?php echo $folha['regiao'] ?>" />
                    <input type="hidden" name="acao" value="cadastrar"/>
                    <input type="button" value="Enviar" name="enviar" id="enviar" class="btn btn-primary"/>
                </div>
                <div class="col-xs-12 form-group">
                    <p class="aviso"></p>
                </div>
            </div>
        </form>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.mask.min.js" type="text/javascript"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../uploadfy/scripts/jquery.uploadify.v2.1.0.js" type="text/javascript"></script>
        <script src="../../uploadfy/scripts/swfobject.js" type="text/javascript"></script>
        <script src="../../jquery/priceFormat.js" type="text/javascript"></script>
        <script type="text/javascript" src="../../jquery/thickbox/thickbox.js"></script>
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
                });
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
                
               
           console.log($('#tipo_guia').val());

                $('#enviar').click(function(){
                         
                    var aviso = $('.aviso');
                    var arquivo = $('#arquivo1');
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
                    
                    if($('#tipo_guia').val() == 2 || $('#tipo_guia').val() == 7 ){
                    var arquivo2 = $('#arquivo2');
                    var extensao_arquivo2 = (arquivo2.val().substring(arquivo2.val().lastIndexOf("."))).toLowerCase(); 
                        if(arquivo2.val() == '' || extensao_arquivo2 != '.pdf'){                
                            aviso.html('O arquivo não foi anexado ou não é um PDF.');   
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
            });
        </script>
    </body>
</html>
