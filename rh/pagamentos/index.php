<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}
//error_reporting(E_ALL);

include('../../conn.php');
include("../../funcoes.php");
include("../../wfunction.php");

$lista = false;
$usuario = carregaUsuario();



//VERIFICA INFORMAÇÃO DE POST
if (validate($_REQUEST['filtrar'])) {
    $lista = true;
    $mes = str_pad($_REQUEST['mes'], 2, "0", STR_PAD_LEFT);
    $ano = $_REQUEST['ano'];

    if ($_COOKIE['logado'] == 204) {
        $regiao = '36';
        $usuario['id_master'] = 1;
    } else {
        $regiao = $usuario['id_regiao'];
    }


    $tipoPagamento = $_REQUEST['tipo_pagamento'];
    $tipoContratacao = $_REQUEST['tipo_contrato'];

    switch ($tipoPagamento) {

        case 1: //"1"=>"Pagamentos Folha"

            switch ($tipoContratacao) {

                case 1: $tabela_folha = 'folhas';
                    $contratacao = " AND f.contratacao = '1' ";
                    break;

                case 2: $tabela_folha = 'rh_folha';
                    $contratacao = " ";
                    $tipo_contrato_pg = 1;
                    break;

                case 3: $tabela_folha = 'folhas';
                    $contratacao = " AND f.contratacao = '3' ";
                    $tipo_contrato_pg = 2;
                    break;

                case 4: $tabela_folha = 'folhas';
                    $contratacao = " AND f.contratacao = '4' ";
                    break;
            }

            if ($_COOKIE['logado'] != 257) {

                $query = "SELECT f.id_folha, f.projeto, p.id_regiao, f.terceiro, f.tipo_terceiro, p.regiao, p.nome
                            FROM $tabela_folha f
                            INNER JOIN projeto p ON f.projeto = p.id_projeto
                            WHERE (f.status = '3' OR f.status = '2') AND 
                                 p.id_master = {$usuario['id_master']} AND 
                                 f.mes = '{$mes}' AND 
                                 f.ano = '{$ano}' AND 
                                 p.id_regiao != 36 AND
                                 p.id_regiao  = '{$regiao}'
                                 $contratacao
                                    ORDER BY f.projeto";
            } else {
                $query = "SELECT f.id_folha, f.projeto, p.id_regiao, f.terceiro, f.tipo_terceiro, p.regiao, p.nome
                            FROM $tabela_folha f
                            INNER JOIN projeto p ON f.projeto = p.id_projeto
                            WHERE (f.status = '3' OR f.status = '2') AND 
                                 p.id_master = {$usuario['id_master']} AND 
                                 f.mes = '{$mes}' AND 
                                 f.ano = '{$ano}' AND 
                                 p.id_regiao  = '{$regiao}'
                                 $contratacao
                                    ORDER BY f.projeto";
            }

            break;

        case 2: //"2" => "Pagamentos Rescisão"
            switch ($tipoContratacao) {

                case 2:
                    $query = "SELECT A.id_recisao, A.id_clt as clt, B.nome AS nome_clt,D.nome AS nprojeto, A.total_liquido, C.id_regiao, C.regiao, D.id_projeto, D.nome, E.*, F.*,F.status as status_saida, G.especifica as nome_status, lek.*
                        FROM rh_recisao AS A
                        LEFT JOIN rh_clt AS B ON B.id_clt = A.id_clt
                        LEFT JOIN regioes AS C ON A.id_regiao = C.id_regiao
                        LEFT JOIN projeto AS D ON A.id_projeto = D.id_projeto
                        LEFT JOIN pagamentos_especifico AS E ON (E.id_clt = A.id_clt)
                        LEFT JOIN saida AS F ON (E.id_saida = F.id_saida)
                        LEFT JOIN rhstatus AS G ON (A.motivo = G.codigo)
                        LEFT JOIN (
                        SELECT BB.`status` as status_multa,BB.id_clt FROM saida_files as AA 
                        INNER JOIN saida as BB
                        ON AA.id_saida = BB.id_saida
                        WHERE BB.tipo = '167' AND AA.multa_rescisao = 1) as lek ON (lek.id_clt = A.id_clt)
                        WHERE 
                            MONTH(A.data_demi) = '$mes'
                            AND YEAR(A.data_demi) = '$ano'
                            AND A.status = '1' 
                            AND A.id_regiao = '{$regiao}'
                            AND D.id_master = {$usuario['id_master']}
                            AND A.rescisao_complementar = '0'
                                GROUP BY B.id_clt
                                ORDER BY C.id_regiao,D.nome,B.nome";
                    break;

                case 5:
                    $query = $query = "SELECT A.id_rescisao AS id_recisao, A.id_estagiario as clt, B.nome AS nome_clt,D.nome AS nprojeto, A.total_liquido, C.id_regiao, C.regiao, D.id_projeto, D.nome, E.*, F.*,F.status as status_saida, '' as nome_status
                        FROM rh_rescisao_estagiario AS A
                        LEFT JOIN estagiario AS B ON B.id_estagiario = A.id_estagiario
                        LEFT JOIN regioes AS C ON A.id_regiao = C.id_regiao
                        LEFT JOIN projeto AS D ON A.id_projeto = D.id_projeto
                        LEFT JOIN pagamentos_especifico_estagiario AS E ON (E.id_estagiario = A.id_estagiario)
                        LEFT JOIN saida AS F ON (E.id_saida = F.id_saida)
                        WHERE 
                            MONTH(A.data_fim) = '$mes'
                            AND YEAR(A.data_fim) = '$ano'
                            AND A.status = '1' 
                            AND A.id_regiao = '{$regiao}'
                            AND D.id_master = {$usuario['id_master']}
                                GROUP BY B.id_estagiario
                                ORDER BY C.id_regiao,D.nome,B.nome";
                    break;
            }


            break;

        case 3: //"3" => "Pagamentos Férias"
            $query = "SELECT A.*,B.regiao as nome_regiao, C.nome as nome_projeto,  D.nome as nome_clt, D.banco
                    FROM rh_ferias as A
                    INNER JOIN regioes as B
                    ON B.id_regiao = A.regiao
                    INNER JOIN projeto as C
                    ON C.id_projeto = A.projeto
                    INNER JOIN rh_clt as D
                    ON A.id_clt = D.id_clt
                    WHERE A.status = 1 AND B.id_master = $usuario[id_master] AND A.mes = $mes AND A.ano = $ano AND B.id_regiao = '{$regiao}' ORDER BY projeto, D.nome;";

            break;

        case 4: // "4" => pagamento RPA
            //A PEDIDO DA DILEANE, HOJE DIA 12/09/2014 ALTEREI A CONDIÇÃO PARA LISTAR O PAGAMENTO DE RPA.
            //APARTIR DE HOJE INVES DE LISTAR DE ACORDO COM O MES E O ANO DA DATA DE GERAÇAO (MONTH(data_geracao) = '$mes' AND YEAR(data_geracao) = '$ano')
            //SERÁ LISTADO DE ACORDO COM O MES E O ANO DA COMPETENCIA ---- AMANDA
            if(strtotime("$ano-$mes-01") > strtotime('2017-02-28')) {
                $query = "SELECT A.*, A.id_unidade_pag AS unidade_pag, B.nome, B.id_unidade, C.nome AS nome_projeto,C.id_projeto, B.banco, B.conta, B.conta_dv, B.agencia, B.agencia_dv, B.tipo_pagamento,B.nome_banco, B.cpf,B.pis,E.nome AS funcao, I.unidade
                        FROM rpa_autonomo AS A
                        LEFT JOIN autonomo AS B ON (A.id_autonomo = B.id_autonomo)
                        LEFT JOIN projeto AS C ON (C.id_projeto = B.id_projeto)
                        LEFT JOIN regioes AS D ON (D.id_regiao = B.id_regiao)
                        LEFT JOIN curso AS E ON (B.id_curso = E.id_curso)
                        LEFT JOIN rpa_saida_assoc AS F ON (F.id_rpa = A.id_rpa)
                        LEFT JOIN saida AS G ON (G.id_saida = F.id_saida)
                        LEFT JOIN saida_files AS H ON (G.id_saida = H.id_saida)
                        LEFT JOIN unidade AS I ON (I.id_unidade = IF(A.id_unidade_pag IS NULL, B.id_unidade, A.id_unidade_pag))
                        WHERE A.mes_competencia = '$mes' AND A.ano_competencia = '$ano' AND id_regiao_pag = '$regiao'
                        -- GROUP BY B.id_autonomo
                        ORDER BY B.id_projeto,B.nome";
            } else {
                $query = "SELECT A.*, A.id_unidade_pag as unidade_pag, B.nome, B.id_unidade, C.nome AS nome_projeto,C.id_projeto, B.banco, B.conta, B.conta_dv, B.agencia, B.agencia_dv, B.tipo_pagamento,B.nome_banco, B.cpf,B.pis,E.nome AS funcao, I.unidade
                        FROM  rpa_autonomo as A
                        LEFT JOIN autonomo as B ON (A.id_autonomo = B.id_autonomo)
                        LEFT JOIN projeto as C ON (C.id_projeto = B.id_projeto)
                        LEFT JOIN regioes as D ON (D.id_regiao = B.id_regiao)
                        LEFT JOIN curso AS E ON (B.id_curso = E.id_curso)
                        LEFT JOIN rpa_saida_assoc AS F ON (F.id_rpa = A.id_rpa)
                        LEFT JOIN saida AS G ON (G.id_saida = F.id_saida)
                        LEFT JOIN saida_files AS H ON (G.id_saida = H.id_saida)
                        LEFT JOIN unidade AS I ON (I.id_unidade = IF(A.id_unidade_pag IS NULL, B.id_unidade, A.id_unidade_pag))
                        WHERE A.mes_competencia = '$mes' AND A.ano_competencia = '$ano' AND (B.id_regiao = '$regiao' OR id_regiao_pag = '$regiao')
                        GROUP BY B.id_autonomo
                        ORDER BY B.id_projeto,B.nome";  
            }
            
            
            
            break;

        case 5:
            // SQL VT
            $query = "SELECT A.id_projeto, A.nome AS nome_projeto, B.id_regiao, B.regiao AS nome_regiao FROM projeto AS A
                        LEFT JOIN regioes AS B ON(A.id_regiao=B.id_regiao)
                        WHERE A.id_regiao='$usuario[id_regiao]' AND A.status_reg=1 AND B.`status`=1 AND A.status_reg=1;";
//            echo '<br>'.$query.'<br>';
            //botar pra pegar o vr também.
            break;
        case 6:
            switch ($tipoContratacao) {

                case 1: $tabela_folha = 'folhas';
                    $contratacao = " AND f.contratacao = '1' ";
                    break;

                case 2: $tabela_folha = 'rh_folha';
                    $contratacao = " ";
                    $tipo_contrato_pg = 1;
                    break;

                case 3: $tabela_folha = 'folhas';
                    $contratacao = " AND f.contratacao = '3' ";
                    $tipo_contrato_pg = 2;
                    break;

                case 4: $tabela_folha = 'folhas';
                    $contratacao = " AND f.contratacao = '4' ";
                    break;
            }
            $query = "SELECT f.id_folha, f.projeto, p.id_regiao, f.terceiro, f.tipo_terceiro, p.regiao, p.nome
                            FROM $tabela_folha f
                            INNER JOIN projeto p ON f.projeto = p.id_projeto
                            WHERE (f.status = '3' OR f.status = '2') AND 
                                 p.id_master = {$usuario['id_master']} AND 
                                 f.mes = '{$mes}' AND 
                                 f.ano = '{$ano}' AND 
                                 p.id_regiao  = '{$regiao}'
                                 $contratacao
                                    ORDER BY f.projeto";
            break;

        case 7: // "7" => pagamento estagiário
            //HOJE 19/08/2016 ESTOU INCLUINDO A QUERY PARA PAGAMENTO DOS ESTAGIÁRIOS QUE SERÃO INLCUSOS FUTURAMENTE
            $query = "SELECT A.*, B.nome,  C.nome as nome_projeto,C.id_projeto, B.banco, B.conta, B.agencia, B.tipo_pagamento,B.nome_banco, B.cpf,B.pis,E.nome as funcao
                        FROM  rpa_estagiario as A
                        LEFT JOIN estagiario as B ON (A.id_estagiario = B.id_estagiario)
                        LEFT JOIN projeto as C ON (C.id_projeto = B.id_projeto)
                        LEFT JOIN regioes as D ON (D.id_regiao = B.id_regiao)
                        LEFT JOIN curso AS E ON (B.id_curso = E.id_curso)
                        LEFT JOIN rpa_saida_assoc AS F ON (F.id_rpa_estagiario = A.id_rpa_estagiario)
                        LEFT JOIN saida AS G ON (G.id_saida = F.id_saida)
                        LEFT JOIN saida_files AS H ON (G.id_saida = H.id_saida)
                        WHERE A.mes_competencia = '$mes' AND A.ano_competencia = '$ano' AND D.id_regiao = '{$regiao}'
                        GROUP BY B.id_estagiario
                        ORDER BY B.id_projeto,B.nome";

//                        echo $query;
            break;
        case 8:
            $query = "SELECT A.*, B.c_fantasia,  C.nome as nome_projeto,C.id_projeto, B.nome_banco, B.conta, B.conta_dv, B.agencia, B.agencia_dv, B.c_cpf
                        FROM  rpa_autonomo as A
                        LEFT JOIN prestadorservico as B ON (A.id_prestador = B.id_prestador)
                        LEFT JOIN projeto as C ON (C.id_projeto = B.id_projeto)
                        LEFT JOIN regioes as D ON (D.id_regiao = B.id_regiao)
                        LEFT JOIN rpa_saida_assoc AS F ON (F.id_rpa = A.id_rpa)
                        LEFT JOIN saida AS G ON (G.id_saida = F.id_saida)
                        LEFT JOIN saida_files AS H ON (G.id_saida = H.id_saida)
                        WHERE A.mes_competencia = '$mes' AND A.ano_competencia = '$ano' AND D.id_regiao = '{$regiao}' AND B.prestador_tipo = 3
                        GROUP BY B.id_prestador
                        ORDER BY B.id_projeto,B.c_fantasia;";
            break;
    }

    echo '<!-- ** ';
    print_r($query);
    echo ' -->';
    $result = mysql_query($query);
}

//CARREGA TIPOS DE PAGAMENTOS PARA SELECT
$tiposPg = array("1" => "Pagamentos Folha", "2" => "Pagamentos Rescisão", "3" => "Pagamentos Férias", "4" => "Pagamentos RPA", "5" => "VALES ( VT / VR / VA )", "6" => "Pagamento de Sindicatos", "7" => "Pagamentos de Contrato de Estagiário", "8" => "Pagamento Prestador");

//CARREGA TIPOS DE CONTRATAÇÃO PARA SELECT
$rsTiposCont = montaQuery('tipo_contratacao', "tipo_contratacao_id,tipo_contratacao_nome");
$tiposCont = array();
foreach ($rsTiposCont as $valor) {
    $tiposCont[$valor['tipo_contratacao_id']] = $valor['tipo_contratacao_nome'];
}

//MONTA SELECT PARA MES
$optMes = array();
for ($i = 1; $i <= 12; $i++) {
    $optMes[$i] = mesesArray($i);
}

//MONTA SELECT PARA ANOS
$optAnos = array();
for ($i = 2009; $i <= date('Y') + 1; $i++) {
    $optAnos[$i] = $i;
}

//SETANDO VARIAVIES DE RETORNO DOS SELECTS
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
$mesSel = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m') - 1;
$tipoContSel = (isset($_REQUEST['tipo_contrato'])) ? $_REQUEST['tipo_contrato'] : "2";
$ttiposPgSel = (isset($_REQUEST['tipo_pagamento'])) ? $_REQUEST['tipo_pagamento'] : "";
?>
<html>
    <head>
        <title>RH - Pagamentos</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../uploadfy/css/uploadify.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="../../jquery/thickbox/thickbox.css" type="text/css" media="screen" />        
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>        
        <script src="../../jquery/priceFormat.js" type="text/javascript"></script>
        <script src="../../uploadfy/scripts/jquery.uploadify.v2.1.0.js" type="text/javascript"></script>
        <script src="../../uploadfy/scripts/swfobject.js" type="text/javascript"></script>
        <script src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript"></script>
        <script type="text/javascript" src="../../jquery/thickbox/thickbox.js"></script>
        <script src="../../js/global.js" type="text/javascript"></script>

        <script>
            $(function(){
            $(".bt,.bt-rpa").css('cursor', 'pointer');
            $(".bt-ver").css('cursor', 'pointer');
            $(".bt-criar").css('cursor', 'pointer');
            $('#tipo_pagamento').change(function(){
            if ($(this).val() == 4){
            $('#tipo_contrato').val(1);
            } else{
            $('#tipo_contrato').val(2);
            };
            });
            $(".bt").click(function(){
            var botao = $(this);
            var id = botao.data('key');
            var type = botao.data('type');
            var title = botao.data('title');
            var classe = botao.parent().attr('class');
            var tipo_contrato = botao.data('tipo_contrato');
            if (classe != ""){
            //ja existe saída
            thickBoxIframe("Detalhes " + title, "index_popup.php", {id: id, tipo: type, tipo_contrato: tipo_contrato}, 850, 450);
            } else{
            thickBoxIframe("Detalhes " + title, "cadastro_1.php", {id: id, tipo: type, contratacao: $("#tipo_contrato").val()}, 850, 450, null, false);
            callFunctionsCad();
            }
            });
            $(".bt-ver").click(function(){
            var botao = $(this)
                    var id = botao.data('key');
            var type = botao.data('tipo');
            var mes = botao.data('mes');
            var ano = botao.data('ano');
            thickBoxIframe("Rescisão", "popup_comprovante.php", {id_clt: id, tipo: type, mes: mes, ano: ano}, 850, 450);
            });
            $(".bt-criar").click(function(){
            var botao = $(this)
                    var id = botao.data('key');
            var type = botao.data('tipo');
            var mes = botao.data('mes');
            var ano = botao.data('ano');
            thickBoxIframe("Rescisão", "popup_cadresci.php", {id_clt: id, tipo: type, mes: $("#mes").val(), ano: $("#ano").val()}, 850, 450);
            });
            /*****FORMULÁRIO DE CADASTRO VT E VR ****/

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
            if ($(this).val() == 1){
            $('.campo_codigo_gerais').show();
            } else{
            $('.campo_codigo_gerais').hide();
            $('#campo_codigo_gerais1, #campo_codigo_gerais2, #campo_codigo_gerais3, #campo_codigo_gerais5 , #campo_codigo_gerais4, #campo_codigo_gerais6, #campo_codigo_gerais7, #campo_codigo_gerais8').val('');
            }
            })
                    $('#campo_codigo_gerais1, #campo_codigo_gerais2, #campo_codigo_gerais3, #campo_codigo_gerais5').keyup(function(){ limita_caractere($(this), 5, 1) });
            $('#campo_codigo_gerais4, #campo_codigo_gerais6').keyup(function(){ limita_caractere($(this), 6, 1) });
            $('#campo_codigo_gerais7').keyup(function(){ limita_caractere($(this), 1, 1) });
            $('#campo_codigo_gerais8').keyup(function(){
            if ($(this).val().length >= 14){
            $(this).blur();
            var valor = $(this).val().substr(0, limite);
            $(this).val(valor);
            }
            });
            function limita_caractere(campo, limite, muda_campo){
            var tamanho = campo.val().length;
            if (tamanho >= limite){
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
            if (arquivo.val() != '' && extensao_arquivo == '.pdf'){
            arquivo.css('background-color', '#51b566')
                    .css('color', '#FFF');
            aviso.html('');
            }

            if (extensao_arquivo != '.pdf') {
            arquivo.css('background-color', ' #f96a6a')
                    .css('color', '#FFF');
            aviso.html('Este arquivo não é um PDF.');
            }


            });
            $('form').submit(function(){

            var aviso = $('.aviso');
            var arquivo = $('#arquivo1');
            var extensao_arquivo = (arquivo.val().substring(arquivo.val().lastIndexOf("."))).toLowerCase();
            var arquivo2 = $('#arquivo2');
            var extensao_arquivo = (arquivo.val().substring(arquivo.val().lastIndexOf("."))).toLowerCase();
            var count = 0;
            $(".j_verifica:checked").each(function(i, value){
            count++;
            });
            if ($('#tipo_guia').val() == 5 || $('#tipo_guia').val() == 6){
            if (count < 1){
            aviso.html('Selecione um tipo de pagamento');
            return false;
            }
            }

            if ($('#valor').val() == ''){
            aviso.html('Digite o valor.');
            return false;
            }

            if ($('#data').val() == ''){
            aviso.html('Digite a data.');
            return false;
            }

            if ($('#bancos').val() == ''){
            aviso.html('Selecione o banco.');
            return false;
            }

            if (arquivo.val() == ''){
            aviso.html('O arquivo não foi anexado');
            return false;
            }

            if ($('#tipo_guia').val() == 2){

            if (arquivo2.val() == ''){
            aviso.html('O arquivo não foi anexado');
            return false;
            }

            }

            if (extensao_arquivo != '.pdf'){
            aviso.html('Este arquivo não é um PDF.');
            return false;
            }

            });
            /**JQUERY GORDO*/
            $(".j_tipo_pgto").click(function(){
            $(this).each(function(i, value){
            if ($(this).val() == "1"){
            $(".mostrarNome").show();
            $(".textRemove").hide();
            $("#nomeTitulo").val("");
            $(".viewNome").text("").show();
            } else if ($(this).val() == "2"){
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
            $('#tipo_pagamento').on('change', function(){
            var tipo_pag = $('#tipo_pagamento').val();
            if (tipo_pag == '7') {
            $('#tipo_contrato').val('5');
            } else if (tipo_pag == '8') {
            $('#tipo_contrato').val('6');
            }
            });
            /*******/
            });
            var callFunctionsCad = function(){
            $('#valor').priceFormat({
            prefix: '',
                    centsSeparator: ',',
                    thousandsSeparator: '.'
            });
            var id_saida = 0;
            $('#data').mask('99/99/9999');
            $('#data').datepicker({
            dateFormat: 'dd/mm/yy',
                    changeMonth: true,
                    changeYear: true
            });
            $('p.botao').click(function(){
            var valor = parseInt($('#valor').val());
            if ($('#progressBar').html() != "" || valor > 0){

            var cod1 = $('#campo_codigo_gerais1').val();
            var cod2 = $('#campo_codigo_gerais2').val();
            var cod3 = $('#campo_codigo_gerais3').val();
            var cod4 = $('#campo_codigo_gerais4').val();
            var cod5 = $('#campo_codigo_gerais5').val();
            var cod6 = $('#campo_codigo_gerais6').val();
            var cod7 = $('#campo_codigo_gerais7').val();
            var cod8 = $('#campo_codigo_gerais8').val();
            var cod_barra_gerais = cod1 + cod2 + cod3 + cod4 + cod5 + cod6 + cod7 + cod8;
            $.post('actions/cadastra.php', {
            id_folha    : $("#id_folha").val(),
                    tipo_contrato : $("#tipo_folha").val(),
                    tipo 	: $("#tipo").val(),
                    subgrupo    : $("#subgrupo").val(),
                    nome 	: $('#nome').val(),
                    valor 	: $('#valor').val(),
                    data 	: $('#data').val(),
                    regiao 	: $('#fregiao').val(),
                    projeto     : $('#fprojeto').val(),
                    banco       : $('#bancos').val(),
                    mes_pg      : $('#mes').val(),
                    ano_pg      : $('#ano').val(),
                    tipo_pg     : $('#tipo_pg').val(),
                    folha_regiao: $('#folha_regiao').val(),
                    folha_projeto: $('#folha_projeto').val(),
                    cod_barra_gerais: cod_barra_gerais
            },
                    function(result){
                    id_saida = result;
                    $('#arquivo').uploadifySettings('scriptData', {'id_saida'   : id_saida <?php if ($texto == "GPS") { ?>, tipo_gps: 1 <?php } ?>});
                    $('#arquivo').uploadifyUpload();
                    $('#arquivo2').uploadifySettings('scriptData', {'id_saida'   : id_saida <?php if ($texto == "GPS") { ?>, tipo_gps: 2 <?php } ?> });
                    $('#arquivo2').uploadifyUpload();
                    });
            reseta();
            } else{
            alert('Por favor anexe um arquivo');
            }
            });
            var Parametros = {
            'uploader'  : '../../uploadfy/scripts/uploadify.swf',
                    'script'    : 'actions/upload.php',
                    'cancelImg' : '../../uploadfy/cancel.png',
                    'auto'      : false,
                    'buttonText': 'Anexar PDF',
                    'folder'    : '../comprovantes',
                    'queueID'   : 'progressBar',
                    'scriptData': {'id_saida'   : id_saida <?php if ($texto == "GPS") { ?>, tipo_gps: 1 <?php } ?> },
                    'fileDesc'  : 'Somente arquivos PDF',
                    'fileExt'   : '*.pdf;',
                    'onComplete': function(a, b, c, d){
                    alert('Concluido com sucesso!');
                    location.reload();
                    },
                    'onAllComplete': function(){
                    }
            }

            //USADO SOMENTE NA GPS                                                  
            var Parametros2 = {
            'uploader'  : '../../uploadfy/scripts/uploadify.swf',
                    'script'    : 'actions/upload.php',
                    'cancelImg' : '../../uploadfy/cancel.png',
                    'auto'      : false,
                    'buttonText': 'Anexar PDF',
                    'folder'    : '../comprovantes',
                    'queueID'   : 'progressBar',
                    'scriptData': {'id_saida'   : id_saida, tipo_gps: 2 },
                    'fileDesc'  : 'Somente arquivos PDF',
                    'fileExt'   : '*.pdf;',
                    'onComplete': function(a, b, c, d){
                    alert('Concluido com sucesso!');
                    location.reload();
                    },
                    'onAllComplete': function(){
                    }
            }

            $('#arquivo').uploadify(Parametros);
            $('#arquivo2').uploadify(Parametros2);
            function reseta(){
            $("input[type*='text']").each(function(){
            $(this).val('');
            });
            }

            $('input[name=cod_barra]').change(function(){
            if ($(this).val() == 1){
            $('.campo_codigo_gerais').show();
            } else{
            $('.campo_codigo_gerais').hide();
            $('#campo_codigo_gerais1, #campo_codigo_gerais2, #campo_codigo_gerais3, #campo_codigo_gerais5 , #campo_codigo_gerais4, #campo_codigo_gerais6, #campo_codigo_gerais7, #campo_codigo_gerais8').val('');
            }
            });
            $('#campo_codigo_gerais1, #campo_codigo_gerais2, #campo_codigo_gerais3, #campo_codigo_gerais5').keyup(function(){ limita_caractere($(this), 5, 1) });
            $('#campo_codigo_gerais4, #campo_codigo_gerais6').keyup(function(){ limita_caractere($(this), 6, 1) });
            $('#campo_codigo_gerais7').keyup(function(){ limita_caractere($(this), 1, 1) });
            $('#campo_codigo_gerais8').keyup(function(){
            if ($(this).val().length >= 14){
            $(this).blur();
            var valor = $(this).val().substr(0, limite);
            $(this).val(valor);
            }
            });
            function limita_caractere(campo, limite, muda_campo){
            var tamanho = campo.val().length;
            if (tamanho >= limite){
            campo.next().focus();
            var valor = campo.val().substr(0, limite);
            campo.val(valor);
            }
            }
            }
        </script>
        <style>
            p{
                padding: 3px;
            }
            .bt-rel_analitico{
                display: inline-table;
                background-color:  #cccccc;
                color:#000;
                font-weight: bold;
                text-decoration: none;
                /*width:250px;*/
                /*height:50px;*/
                padding: 5px;
                margin-bottom: 10px;
                border: 1px solid  #9f9f9f;
            }
            .bt-rel_analitico:hover{
                color:   #FFF;
                background-color:    #31a2ec;
                text-decoration: underline;
            }
        </style>
    </head>
    <body class="novaintra">
        <form action="" method="post" name="form1">
            <div id="content">
                <div id="head">
                    <img src="../../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>RH - Pagamentos</h2>
                        <p>Controle de movimentação financeira do RH</p>
                    </div>
                </div>
                <br class="clear">

                <br/>
                <fieldset>
                    <legend>Filtro</legend>
                    <div class="fleft">
                        <p><label class="first-2">Tipo de Pagamento:</label> <?php echo montaSelect($tiposPg, $ttiposPgSel, array('name' => 'tipo_pagamento', 'id' => 'tipo_pagamento')); ?></p>

                        <p><label class="first-2">Tipo de Contratação:</label> <?php echo montaSelect($tiposCont, $tipoContSel, array('name' => 'tipo_contrato', 'id' => 'tipo_contrato')); ?></p>
                        <p><label class="first-2">Data de Referencia:</label> <?php echo montaSelect($optMes, $mesSel, array('name' => "mes", 'id' => 'mes')); ?> <?php echo montaSelect($optAnos, $anoSel, array('name' => "ano", 'id' => 'ano')); ?></p>
                    </div>
                    <div class="fright" style="margin-right: 25px;">
                        <img src="imagens/status2.gif" />
                    </div>
                    <br class="clear"/>
                    <p class="controls" style="margin-top: 10px;"><input type="submit" name="filtrar" value="Filtrar" id="filtrar" class="button" style="padding: 5px 25px; background: #f4f4f4; border: 1px solid #ccc; cursor: pointer;" /></p>
                </fieldset>
                <br/><br/>
                <?php if ($lista) { ?>
                    <?php
                    if (mysql_num_rows($result) == 0) {
                        echo "<div id='message-box' class='message-red'>Nenhum registro encontrado para o filtro selecionado.</div>";
                    } else {

                        //RESULTADO PRARA FOLHA DE PAGAMENTO CLT OU COOPERADO
                        if ($tipoPagamento == 1) {
                            ?>
                            <table width="100%" cellspacing="0" cellpadding="0" class="grid">
                                <thead>
                                    <tr>
                                        <th>ID folha</th>
                                        <th>Região</th>
                                        <th>Projeto</th>
                                        <?php if ($_COOKIE['logado'] == 204) { ?>
                                            <th>VT</th>
                                        <?php } ?>
                                        <th>GPS</th>
                                        <th>FGTS</th>
                                        <th>PIS</th>
                                        <th>IR</th>
                                        <th>IR de Férias</th>
                                        <th>IR de Rescisões</th>
                                        <!--<th class="separa">&nbsp</th>-->
            <!--                                        <th>TRANSPORTE</th>
                                        <th>ALIMENTAÇÃO</th>-->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $cor = 0;
                                    while ($row_folha = mysql_fetch_assoc($result)) {
                                        // verificação de 13ª salario.
                                        if ($row_folha['terceiro'] == '1') {
                                            if ($row_folha['tipo_terceiro'] == 3) {
                                                $decimo3 = " - 13ª integral";
                                            } else {
                                                $decimo3 = " - 13ª ($row_folha[tipo_terceiro]ª) Parcela";
                                            }
                                        }

                                        $sql = "
                                            SELECT (
                                            SELECT IF(B.estorno != 0 ,'estorno', B.status) FROM pagamentos AS A 
                                            LEFT JOIN saida AS B ON(A.id_saida=B.id_saida) 
                                            WHERE A.mes_pg='{$mes}' AND A.ano_pg = '{$ano}' AND A.id_folha = {$row_folha['id_folha']} AND A.tipo_pg = 1 AND A.tipo_contrato_pg = $tipo_contrato_pg ORDER BY data_proc DESC LIMIT 1) as gps,

                                            (SELECT IF(B.estorno != 0 ,'estorno', B.status) FROM pagamentos AS A 
                                            LEFT JOIN saida AS B ON(A.id_saida=B.id_saida) 
                                            WHERE A.mes_pg='{$mes}' AND A.ano_pg = '{$ano}' AND A.id_folha = {$row_folha['id_folha']} AND A.tipo_pg = 2 AND A.tipo_contrato_pg = $tipo_contrato_pg ORDER BY data_proc DESC LIMIT 1) as fgts,

                                            (SELECT  IF(B.estorno != 0 ,'estorno', B.status) FROM pagamentos AS A 
                                            LEFT JOIN saida AS B ON(A.id_saida=B.id_saida) 
                                            WHERE A.mes_pg='{$mes}' AND A.ano_pg = '{$ano}' AND A.id_folha = {$row_folha['id_folha']} AND A.tipo_pg = 3 AND A.tipo_contrato_pg = $tipo_contrato_pg ORDER BY data_proc DESC LIMIT 1) as pis,

                                            (SELECT  IF(B.estorno != 0 ,'estorno', B.status) FROM pagamentos AS A 
                                            LEFT JOIN saida AS B ON(A.id_saida=B.id_saida) 
                                            WHERE A.mes_pg='{$mes}' AND A.ano_pg = '{$ano}' AND A.id_folha = {$row_folha['id_folha']} AND A.tipo_pg = 4 AND A.tipo_contrato_pg = $tipo_contrato_pg ORDER BY data_proc DESC LIMIT 1) as ir,
                                                
                                            (SELECT IF(B.estorno != 0,'estorno', B.status) FROM pagamentos AS A
                                            LEFT JOIN saida AS B ON(A.id_saida=B.id_saida)
                                            WHERE A.mes_pg='{$mes}' AND A.ano_pg = '{$ano}' AND A.id_folha = {$row_folha['id_folha']} AND A.tipo_pg = 7 AND A.tipo_contrato_pg = $tipo_contrato_pg ORDER BY data_proc DESC LIMIT 1) AS irDeFerias,
                                            
                                            (SELECT IF(B.estorno != 0,'estorno', B.status) FROM pagamentos AS A
                                            LEFT JOIN saida AS B ON(A.id_saida=B.id_saida)
                                            WHERE A.mes_pg='{$mes}' AND A.ano_pg = '{$ano}' AND A.id_folha = {$row_folha['id_folha']} AND A.tipo_pg = 9 AND A.tipo_contrato_pg = $tipo_contrato_pg ORDER BY data_proc DESC LIMIT 1) AS irDeRescisao
                                    ";


//                                            (SELECT  IF(B.estorno != 0 ,'estorno', B.status) FROM pagamentos AS A 
//                                            LEFT JOIN saida AS B ON(A.id_saida=B.id_saida) 
//                                            WHERE A.mes_pg='{$mes}' AND A.ano_pg = '{$ano}' AND A.id_folha = {$row_folha['id_folha']} AND A.tipo_pg = 5 AND A.tipo_contrato_pg = $tipo_contrato_pg ORDER BY data_proc DESC LIMIT 1) as transporte,
//                                            
//                                            (SELECT  IF(B.estorno != 0 ,'estorno', B.status) FROM pagamentos AS A 
//                                            LEFT JOIN saida AS B ON(A.id_saida=B.id_saida) 
//                                            WHERE A.mes_pg='{$mes}' AND A.ano_pg = '{$ano}' AND A.id_folha = {$row_folha['id_folha']} AND A.tipo_pg = 6 AND A.tipo_contrato_pg = $tipo_contrato_pg ORDER BY data_proc DESC LIMIT 1) as sodexo,


                                        $query_controle = mysql_query($sql);
                                        echo '<!-- ' . $sql . ' -->';
                                        $row_controle = mysql_fetch_assoc($query_controle);
                                        $tipos = array("1" => "gps", "2" => "fgts", "3" => "pis", "4" => "ir", "5" => "irDeFerias", "6" => "irDeRescisao");
                                        ?>

                                        <tr class="<?php echo ($cor++ % 2 == 0) ? "even" : "odd"; ?>">
                                            <td><span class="dados"><?= $row_folha['id_folha'] ?></span></td>
                                            <td><span class="dados"><?= $row_folha['id_regiao'] . " - " . $row_folha['regiao']; ?></span></td>
                                            <td><span class="dados"><?= $row_folha['nome'] . $decimo3 ?></span></td>
                                            <?php
                                            for ($i = 1; $i <= 6; $i++) {
                                                switch ($row_controle[$tipos[$i]]) {

                                                    case '0':
                                                        $color[$i] = "cor-2";
                                                        $link_guias[$i] = 'cadastro_1.php';
                                                        break;
                                                    case 1:
                                                        $color[$i] = "cor-1";
                                                        $link_guias[$i] = 'visualizar_guias_saidas.php';

                                                        break;
                                                    case 2:
                                                        $color[$i] = "cor-3";
                                                        $link_guias[$i] = 'visualizar_guias_saidas.php';
                                                        break;

                                                    case 'estorno': $color[$i] = 'cor-4';
                                                        $link_guias[$i] = 'visualizar_guias_saidas.php';
                                                        break;

                                                    default: $color[$i] = '';
                                                        $link_guias[$i] = 'cadastro_1.php';
                                                }
                                            }
                                            ?> 
                                            <?php if ($_COOKIE['logado'] == 204) { ?>
                                                <td align="center" class="<?= $color[1] ?>">
                                                    <a href="form_guia.php?id_folha=<?= $row_folha['id_folha'] ?>&tipo_contrato=<?= $tipoContratacao; ?>&keepThis=true&TB_iframe=true&width=850" class="thickbox" > 
                                                        <img src="../imagensrh/gps.jpg" />
                                                    </a>                                      
                                                </td>
                                            <?php } ?>
                                            <!-----    GPS    ----------------------->
                                            <td align="center" class="<?= $color[1] ?>">

                                                <a href="<?php echo $link_guias[1]; ?>?id_folha=<?= $row_folha['id_folha'] ?>&tipo_guia=1&tipo_contrato=<?php echo $tipoContratacao; ?>&mes_consulta=<?php echo $mes; ?>&ano_consulta=<?php echo $ano; ?>&regiao=<?php echo $regiao; ?>&keepThis=true&TB_iframe=true&width=850" class="thickbox" > 
                                                    <img src="../imagensrh/gps.jpg" />
                                                </a>                                      
                                            </td>

                                            <!-----    FGTS   ----------------------->
                                            <td align="center" class="<?= $color[2] ?>">
                                                <a href="<?php echo $link_guias[2]; ?>?id_folha=<?= $row_folha['id_folha'] ?>&tipo_guia=2&tipo_contrato=<?php echo $tipoContratacao; ?>&mes_consulta=<?php echo $mes; ?>&ano_consulta=<?php echo $ano; ?>&regiao=<?php echo $regiao; ?>&keepThis=true&TB_iframe=true&width=850" class="thickbox" > 
                                                    <img src="../imagensrh/log_fgts.jpg" />
                                                </a>  

                                            </td>

                                            <!-----    PIS    ----------------------->
                                            <td align="center" class="<?= $color[3] ?>">
                                                <a href="<?php echo $link_guias[3]; ?>?id_folha=<?= $row_folha['id_folha'] ?>&tipo_guia=3&tipo_contrato=<?php echo $tipoContratacao; ?>&mes_consulta=<?php echo $mes; ?>&ano_consulta=<?php echo $ano; ?>&keepThis=true&TB_iframe=true&width=850" class="thickbox" > 
                                                    <img src="../imagensrh/pis.jpg" />
                                                </a>  
                                            </td>                                           

                                            <!-----    IR    ----------------------->
                                            <td align="center" class="<?= $color[4] ?>">
                                                <a href="<?php echo $link_guias[4]; ?>?id_folha=<?= $row_folha['id_folha'] ?>&tipo_guia=4&tipo_contrato=<?php echo $tipoContratacao; ?>&mes_consulta=<?php echo $mes; ?>&ano_consulta=<?php echo $ano; ?>&keepThis=true&TB_iframe=true&width=850" class="thickbox" > 
                                                    <img src="../imagensrh/ir.jpg" />
                                                </a>  
                                            </td>

                                            <!-----    IR FERIAS    ------------------->
                                            <td align="center" class="<?= $color[5] ?>">
                                                <a href="<?php echo $link_guias[5]; ?>?id_folha=<?= $row_folha['id_folha'] ?>&tipo_guia=7&tipo_contrato=<?php echo $tipoContratacao; ?>&mes_consulta=<?php echo $mes; ?>&ano_consulta=<?php echo $ano; ?>&keepThis=true&TB_iframe=true&width=850" class="thickbox" > 
                                                    <img src="../imagensrh/ir.jpg" />
                                                </a>  
                                            </td>


                                            <!-----    IR RESCISÕES    ------------------->
                                            <td align="center" class="<?= $color[6] ?>">
                                                <a href="<?php echo $link_guias[6]; ?>?id_folha=<?= $row_folha['id_folha'] ?>&tipo_guia=9&tipo_contrato=<?php echo $tipoContratacao; ?>&mes_consulta=<?php echo $mes; ?>&ano_consulta=<?php echo $ano; ?>&keepThis=true&TB_iframe=true&width=850" class="thickbox" > 
                                                    <img src="../imagensrh/ir.jpg" />
                                                </a>  
                                            </td>


                                                                                                                                                                                                                                            <!--<td class="separa">&nbsp</td>-->

                                            <!--
                                                                                        <td align="center" class="<?= $color[5] ?>">
                                                                                            <a href="<?php echo $link_guias[5]; ?>?id_folha=<?= $row_folha['id_folha'] ?>&tipo_guia=5&tipo_contrato=<?php echo $tipoContratacao; ?>&mes_consulta=<?php echo $mes; ?>&ano_consulta=<?php echo $ano; ?>&keepThis=true&TB_iframe=true&width=850" class="thickbox" > 
                                                                                                <img src="imagens/transporte.png" width="50px" style="cursor: pointer" />
                                                                                            </a>  
                                                                                        </td>
                                            
                                                                                        <td align="center" class="<?= $color[6] ?>">
                                                                                            <a href="<?php echo $link_guias[6]; ?>?id_folha=<?= $row_folha['id_folha'] ?>&tipo_guia=6&tipo_contrato=<?php echo $tipoContratacao; ?>&mes_consulta=<?php echo $mes; ?>&ano_consulta=<?php echo $ano; ?>&keepThis=true&TB_iframe=true&width=850" class="thickbox" > 
                                                                                                <img src="imagens/sodexo.jpg" width="60px" style="cursor: pointer" />
                                                                                            </a>  
                                                                                        </td>-->
                                        </tr>
                                        <?php
                                        unset($color, $decimo3);
                                    }
                                    ?>
                                </tbody>
                            </table>
                        <?php } elseif ($tipoPagamento == 2) { ?>
                            <?php if ($tipoContratacao == 2) { ?>
                                <table width="100%" cellspacing="0" cellpadding="0" class="grid">
                                    <thead>
                                        <tr>
                                            <th>Projeto</th>
                                            <th>ID CLT</th>
                                            <th>Nome</th>
                                            <th>Valor</th>
                                            <th>Tipo</th>
                                            <th>Rescisão</th>
                                            <th>Multa</th>
                                            <th style=" width: 10%; ">Rescisão Complementar</th>
                                            <th style=" width: 10%; ">Multa Complementar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $cor = 0;

                                        while ($row_resci = mysql_fetch_assoc($result)) {

//                                        if($_COOKIE['logado'] == 179){
//                                            echo "<pre>";
//                                                print_r($row_resci);
//                                            echo "</pre>";
//                                        }

                                            $qr_verifica_recisao = mysql_query("SELECT MAX(B.status) as status 
                                                                            FROM pagamentos_especifico as A
                                                                            INNER JOIN saida as B ON (B.id_saida = A.id_saida)
                                                                            WHERE A.id_clt = '{$row_resci['clt']}' AND B.tipo = 31;");
                                            $row_verifica = mysql_fetch_assoc($qr_verifica_recisao);

                                            $query_saida_multa = mysql_query("SELECT b.status FROM saida_files as a 
                                                                                INNER JOIN saida as b
                                                                                ON a.id_saida = b.id_saida
                                                                                WHERE b.id_clt = '{$row_resci['clt']}' AND b.tipo IN(34) AND a.multa_rescisao = 1 ORDER BY b.status DESC;");
                                            //                                                               WHERE b.id_clt = '{$row_resci['clt']}' AND b.tipo IN(167,31) AND a.multa_rescisao = 1 ORDER BY b.status DESC;");
                                            $row_saida_multa = mysql_fetch_assoc($query_saida_multa);

                                            $resc = mysql_query("SELECT id_recisao FROM rh_recisao WHERE id_clt = {$row_resci['clt']} AND status = 1 AND vinculo_id_rescisao IS NOT NULL;");

                                            while ($row1 = mysql_fetch_array($resc)) {
                                                $sql_resci = "SELECT B.id_rescisao, C.vinculo_id_rescisao, A.`status`
                                                        FROM saida AS A
                                                        LEFT JOIN pagamentos_especifico AS B ON (A.id_saida = B.id_saida)
                                                        LEFT JOIN rh_recisao AS C ON (B.id_rescisao = C.id_recisao)
                                                        LEFT JOIN saida_files AS D ON (D.id_saida = A.id_saida)
                                                        WHERE A.id_clt = {$row_resci['clt']} AND C.`status` = 1 AND C.vinculo_id_rescisao IS NOT NULL AND B.id_rescisao = {$row1['id_recisao']} AND D.multa_rescisao <> 2;";


                                                $sql_mul_compl = "SELECT B.id_rescisao, C.vinculo_id_rescisao, A.`status`
                                                        FROM saida AS A
                                                        LEFT JOIN pagamentos_especifico AS B ON (A.id_saida = B.id_saida)
                                                        LEFT JOIN rh_recisao AS C ON (B.id_rescisao = C.id_recisao)
                                                        LEFT JOIN saida_files AS D ON (D.id_saida = A.id_saida)
                                                        WHERE A.id_clt = {$row_resci['clt']} AND C.`status` = 1 AND C.vinculo_id_rescisao IS NOT NULL AND B.id_rescisao = {$row1['id_recisao']} AND D.multa_rescisao = 2;";

                                                $query_resci_complementar = mysql_query($sql_resci);
                                                $rescisao[$row1['id_recisao']] = mysql_fetch_assoc($query_resci_complementar);

                                                $query_mul_compl = mysql_query($sql_mul_compl);
                                                $multaCompl[$row1['id_recisao']] = mysql_fetch_assoc($query_mul_compl);
                                            }


                                            switch ($row_verifica['status']) {
                                                case 1:
                                                    $color['re'] = "cor-1";
                                                    break;
                                                case 2:
                                                    $color['re'] = "cor-3";
                                                    break;
                                                default: $color['re'] = '';
                                            }

                                            switch ($row_saida_multa['status']) {
                                                case 1:
                                                    $color['mu'] = "cor-1";
                                                    break;
                                                case 2:
                                                    $color['mu'] = "cor-3";
                                                    break;
                                                default: $color['mu'] = '';
                                            }

                                            foreach ($rescisao as $idResciao => $dados) {
                                                switch ($dados['status']) {
                                                    case 1:
                                                        $color['resci_complementar'] = "cor-1";
                                                        break;
                                                    case 2:
                                                        $color['resci_complementar'] = "cor-3";
                                                        break;
                                                    default: $color['resci_complementar'] = '';
                                                }
                                                $arrayR[$row_resci['clt']][$idResciao] = $color['resci_complementar'];
                                            }
                                            foreach ($multaCompl as $idRescisao2 => $dadosMC) {
                                                switch ($dadosMC['status']) {
                                                    case 1:
                                                        $color['mulC'] = "cor-1";
                                                        break;
                                                    case 2:
                                                        $color['mulC'] = "cor-3";
                                                        break;
                                                    default: $color['mulC'] = '';
                                                }
                                                $arrayMC[$row_resci['clt']][$idRescisao2] = $color['mulC'];
                                                unset($multaCompl);
                                            }
                                            ?>
                                                    <!--         e                           <tr>
                                                                                <td colspan="7"><? //= $sql_resci;   ?></td>
                                                                            </tr>-->
                                            <tr class="<?php echo ($cor++ % 2 == 0) ? "even" : "odd"; ?>"> 
                                                <td><?php echo $row_resci['nprojeto'] ?></td>
                                                <td class="center"><?php echo $row_resci['clt'] ?></td>
                                                <td><?php echo $row_resci['nome_clt'] ?></td>
                                                <td>R$ <?php echo number_format($row_resci['total_liquido'], 2, ",", ".") ?></td>
                                                <td> <?php echo $row_resci['nome_status']; ?></td>
                                                <td align="center" class="<?= $color['re'] ?>">
                                                    <?php
                                                    if ($color['re'] == "") {
                                                        ?>

                                                        <a href="detalhes_novo.php?id_clt=<?php echo $row_resci['clt'] ?>&mes=<?php echo $mes; ?>&ano=<?php echo $ano; ?>&id_rescisao=<?php echo $row_resci['id_recisao'] ?>&tipo=2&keepThis=true&TB_iframe=true&width=850" class="thickbox" > 
                                                            <img border="0px" src="imagens/saida-32.png" width="18" height="18">
                                                        </a>
                                                    <?php } else { ?>
                                                        <a href="visualizar_resc_ferias.php?id_clt=<?php echo $row_resci['clt'] ?>&mes=<?php echo $mes; ?>&ano=<?php echo $ano; ?>&id_rescisao=<?php echo $row_resci['id_recisao'] ?>&regiao=<?php echo $regiao; ?>&tipo=2&keepThis=true&TB_iframe=true&width=930" class="thickbox" > 
                                                            <img border="0px" src="../folha/imagens/verfolha.gif" width="18" height="18" />
                                                        </a>
                                                    <?php } ?>
                                                </td>
                                                <td align="center" class="<?= $color['mu'] ?>">
                                                    <?php if ($color['mu'] == "") { ?>

                                                        <a href="detalhes_novo.php?id_clt=<?php echo $row_resci['clt'] ?>&mes=<?php echo $mes; ?>&ano=<?php echo $ano; ?>&id_rescisao=<?php echo $row_resci['id_recisao'] ?>&tipo=3&keepThis=true&TB_iframe=true&width=850" class="thickbox" > 
                                                            <img border="0px" src="imagens/saida-32.png" width="18" height="18">
                                                        </a>

                                                    <?php } else { ?>

                                                        <a href="visualizar_resc_ferias.php?id_clt=<?php echo $row_resci['clt'] ?>&mes=<?php echo $mes; ?>&ano=<?php echo $ano; ?>&id_rescisao=<?php echo $row_resci['id_recisao'] ?>&regiao=<?php echo $regiao; ?>&tipo=3&keepThis=true&TB_iframe=true&width=930" class="thickbox" > 
                                                            <img border="0px" src="../folha/imagens/verfolha.gif" width="18" height="18" />
                                                        </a>
                                                    <?php } ?>

                                                </td>
                                                <!--Parte de Vinculo de rescisão-->    
                                                <td>
                                                    <div>
                                                        <?php
                                                        foreach ($arrayR as $idClt => $rescisao) {
                                                            foreach ($rescisao as $idRescisao => $cores) {
                                                                ?>
                                                                <div class="<?= $cores; ?>" style="float:left; width:100%" align="center">
                                                                    <?php if ($cores == '') {
                                                                        ?>
                                                                        <a href="detalhes_novo.php?id_clt=<?php echo $row_resci['clt'] ?>&mes=<?php echo $mes; ?>&ano=<?php echo $ano; ?>&id_rescisao=<?php echo $idRescisao; ?>&tipo=4&keepThis=true&TB_iframe=true&width=850" class="thickbox" > 
                                                                            <img border="0px" src="imagens/saida-32.png" width="18" height="18">
                                                                        </a>

                                                                    <?php } else { ?>

                                                                        <a href="visualizar_resc_ferias.php?id_clt=<?php echo $row_resci['clt'] ?>&mes=<?php echo $mes; ?>&ano=<?php echo $ano; ?>&id_rescisao=<?php echo $idRescisao; ?>&regiao=<?php echo $regiao; ?>&tipo=4&keepThis=true&TB_iframe=true&width=930" class="thickbox" > 
                                                                            <img border="0px" src="../folha/imagens/verfolha.gif" width="18" height="18" />
                                                                        </a>

                                                                    <?php }
                                                                    ?>
                                                                </div>
                                                                <?php
                                                            }
                                                        }
                                                        unset($arrayR);
                                                        unset($rescisao);
                                                        ?> 
                                                    </div>          
                                                </td>
                                                <!--Parte de Vinculo de rescisão-->    
                                                <td>
                                                    <div>
                                                        <?php
                                                        foreach ($arrayMC as $idClt => $multaComplementar) {
                                                            foreach ($multaComplementar as $idRescisao => $cores) {
                                                                ?>
                                                                <div class="<?= $cores; ?>" style="float:left; width:100%" align="center">
                                                                    <?php if ($cores == '') {
                                                                        ?>
                                                                        <a href="detalhes_novo.php?id_clt=<?php echo $row_resci['clt'] ?>&mes=<?php echo $mes; ?>&ano=<?php echo $ano; ?>&id_rescisao=<?php echo $idRescisao; ?>&tipo=5&keepThis=true&TB_iframe=true&width=850" class="thickbox" > 
                                                                            <img border="0px" src="imagens/saida-32.png" width="18" height="18">
                                                                        </a>

                                                                    <?php } else { ?>

                                                                        <a href="visualizar_resc_ferias.php?id_clt=<?php echo $row_resci['clt'] ?>&mes=<?php echo $mes; ?>&ano=<?php echo $ano; ?>&id_rescisao=<?php echo $idRescisao; ?>&regiao=<?php echo $regiao; ?>&tipo=5&keepThis=true&TB_iframe=true&width=930" class="thickbox" > 
                                                                            <img border="0px" src="../folha/imagens/verfolha.gif" width="18" height="18" />
                                                                        </a>

                                                                    <?php }
                                                                    ?>
                                                                </div>
                                                                <?php
                                                            }
                                                        }
                                                        unset($arrayMC);
                                                        unset($multaComplementar);
                                                        ?> 
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        unset($cor);
                                        unset($color);
                                        ?>
                                    </tbody>   
                                </table>
                            <?php } else if ($tipoContratacao == 5) { ?>
                                <table width="100%" cellspacing="0" cellpadding="0" class="grid">
                                    <thead>
                                        <tr>
                                            <th>Projeto</th>
                                            <th>ID Estagiário</th>
                                            <th>Nome</th>
                                            <th>Valor</th>
                                            <th>Tipo</th>
                                            <th>Rescisão</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $cor = 0;

                                        while ($row_resci = mysql_fetch_assoc($result)) {

                                            $qr_verifica_recisao = mysql_query("SELECT MAX(B.status) as status 
                                                                            FROM pagamentos_especifico_estagiario as A
                                                                            INNER JOIN saida as B ON (B.id_saida = A.id_saida)
                                                                            WHERE A.id_estagiario = '{$row_resci['clt']}' AND B.tipo = 31;");
                                            $row_verifica = mysql_fetch_assoc($qr_verifica_recisao);

                                            switch ($row_verifica['status']) {
                                                case 1:
                                                    $color['re'] = "cor-1";
                                                    break;
                                                case 2:
                                                    $color['re'] = "cor-3";
                                                    break;
                                                default: $color['re'] = '';
                                            }

                                            ?>
                                                    <!--         e                           <tr>
                                                                                <td colspan="7"><? //= $sql_resci;   ?></td>
                                                                            </tr>-->
                                            <tr class="<?php echo ($cor++ % 2 == 0) ? "even" : "odd"; ?>"> 
                                                <td><?php echo $row_resci['nprojeto'] ?></td>
                                                <td class="center"><?php echo $row_resci['clt'] ?></td>
                                                <td><?php echo $row_resci['nome_clt'] ?></td>
                                                <td>R$ <?php echo number_format($row_resci['total_liquido'], 2, ",", ".") ?></td>
                                                <td> <?php echo $row_resci['nome_status']; ?></td>
                                                <td align="center" class="<?= $color['re'] ?>">
                                                    <?php
                                                    if ($color['re'] == "") {
                                                        ?>

                                                        <a href="detalhes_novo_estagiario.php?id_estagiario=<?php echo $row_resci['clt'] ?>&mes=<?php echo $mes; ?>&ano=<?php echo $ano; ?>&id_rescisao=<?php echo $row_resci['id_recisao'] ?>&tipo=2&keepThis=true&TB_iframe=true&width=850" class="thickbox" > 
                                                            <img border="0px" src="imagens/saida-32.png" width="18" height="18">
                                                        </a>
                                                    <?php } else { ?>
                                                        <a href="visualizar_resc_ferias_estagiario.php?id_estagiario=<?php echo $row_resci['clt'] ?>&mes=<?php echo $mes; ?>&ano=<?php echo $ano; ?>&id_rescisao=<?php echo $row_resci['id_recisao'] ?>&regiao=<?php echo $regiao; ?>&tipo=2&keepThis=true&TB_iframe=true&width=930" class="thickbox" > 
                                                            <img border="0px" src="../folha/imagens/verfolha.gif" width="18" height="18" />
                                                        </a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        unset($cor);
                                        unset($color);
                                        ?>
                                    </tbody>   
                                </table>
                            <?php } ?>
                            <?php
                        } elseif ($tipoPagamento == 3) {
                            $cor = 0;
                            ?>        
                            <table width="100%" cellspacing="0" cellpadding="0" class="grid">
                                <thead>
                                    <tr>
                                        <th>Projeto</th>
                                        <th>ID CLT</th>
                                        <th>Nome</th>
                                        <th>Valor</th>
                                        <th></th>
                                    </tr>
                                </thead>      
                                <tbody>
                                    <?php
                                    while ($row = mysql_fetch_assoc($result)) {


                                        $qr_verifica_saida = mysql_query("SELECT MAX(B.status) as status FROM pagamentos_especifico as A 
                                                                                            INNER JOIN saida as B
                                                                                            ON B.id_saida = A.id_saida
                                                                                            WHERE A.id_clt = '{$row['id_clt']}' AND B.tipo = 8 AND A.ano = $ano AND A.mes = $mes;");
                                        $row_verifica = mysql_fetch_assoc($qr_verifica_saida);

                                        switch ($row_verifica['status']) {
                                            case 1:
                                                $color['ferias'] = "cor-1";
                                                break;
                                            case 2:
                                                $color['ferias'] = "cor-3";
                                                break;
                                            default: $color['ferias'] = '';
                                        }
                                        ?>
                                        <tr class="<?php echo ($cor++ % 2 == 0) ? "even" : "odd"; ?>">
                                            <td><?php echo $row['nome_projeto'] ?></td>
                                            <td><?php echo $row['id_clt'] ?></td>
                                            <td><?php echo $row['nome_clt'] ?></td>
                                            <td><?php echo number_format($row['total_liquido'], 2, ',', '.') ?></td>
                                            <td align="center" class="<?= $color['ferias'] ?>">
                                                <?php if ($color['ferias'] == "") { ?>

                                                    <a href="detalhes_novo.php?id_clt=<?php echo $row['id_clt'] ?>&mes=<?php echo $mes; ?>&ano=<?php echo $ano; ?>&id_ferias=<?php echo $row['id_ferias']; ?>&tipo=1&keepThis=true&TB_iframe=true&width=850" class="thickbox" > 
                                                        <img border="0px" src="imagens/saida-32.png" width="18" height="18">
                                                    </a>  

                                                <?php } else { ?>                                            
                                                    <a href="visualizar_resc_ferias.php?id_clt=<?php echo $row['id_clt'] ?>&mes=<?php echo $mes; ?>&ano=<?php echo $ano; ?>&id_ferias=<?php echo $row['id_ferias']; ?>&regiao=<?php echo $regiao; ?>&tipo=1&keepThis=true&TB_iframe=true&width=930" class="thickbox" > 
                                                        <img border="0px" src="../folha/imagens/verfolha.gif" width="18" height="18" />
                                                    </a>

                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <?php
                                    } unset($cor);
                                    unset($color);
                                    ?>    
                                </tbody>  
                            </table>

                            <?php
                        } elseif ($tipoPagamento == 4) {
                            $cor = 0;
                            ?>
                            <div style="margin-bottom: 20px;">
                                <a href="<?php printf("rel_rpa_analitico_2.php?mes=%d&ano=%d", $mes, $ano) ?>" class="bt-rel_analitico" target="_blank">Ver RPA Analítico</a>
                            </div> 
                            <table width="100%" cellspacing="0" cellpadding="0" class="grid">
                                <thead>
                                    <tr>
                                        <th>Unidade</th>
                                        <th>ID AUTONOMO</th>
                                        <th>Nome</th>
                                        <!--<th>Função</th>-->
                                        <th>CPF</th>
                                        <th>PIS</th>
                                        <th>Banco</th>
                                        <th>Agência</th>
                                        <th>Agência DV</th>
                                        <th>Conta</th>
                                        <th>Conta DV</th>
                                        <th>Valor</th>
                                        <th>RPA</th>
                                        <th>GPS</th>
                                        <th>IR</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($row = mysql_fetch_assoc($result)) {
                                        $qr_verifica = mysql_query("SELECT  
                                                                    (SELECT IF(B.estorno != 0 ,'estorno', B.status)
                                                                    FROM rpa_saida_assoc as A
                                                                    INNER JOIN saida as B 
                                                                    ON A.id_saida = B.id_saida
                                                                    WHERE id_rpa = '{$row[id_rpa]}' AND tipo_vinculo = 1  ORDER BY data_proc DESC LIMIT 1) as rpa_normal,

                                                                    (SELECT IF(B.estorno != 0 ,'estorno', B.status)
                                                                    FROM rpa_saida_assoc as A
                                                                    INNER JOIN saida as B 
                                                                    ON A.id_saida = B.id_saida
                                                                    WHERE id_rpa =  '{$row[id_rpa]}' AND tipo_vinculo = 2  ORDER BY data_proc DESC LIMIT 1) as rpa_gps,

                                                                    (SELECT  IF(B.estorno != 0 ,'estorno', B.status)
                                                                    FROM rpa_saida_assoc as A
                                                                    INNER JOIN saida as B 
                                                                    ON A.id_saida = B.id_saida
                                                                    WHERE id_rpa =  '{$row[id_rpa]}' AND tipo_vinculo = 3  ORDER BY data_proc DESC LIMIT 1) as rpa_ir") or die(mysql_error());
                                        $row_verifica = mysql_fetch_assoc($qr_verifica);

                                        switch ($row_verifica['rpa_normal']) {
                                            case 1: $color['rpa_normal'] = 'cor-1';
                                                $pagina_rpa_normal = 'visualizar_rpa_saidas.php';
                                                break;

                                            case 2: $color['rpa_normal'] = 'cor-3';
                                                $pagina_rpa_normal = 'visualizar_rpa_saidas.php';
                                                break;

                                            case 'estorno': $color['rpa_normal'] = 'cor-4';
                                                $pagina_rpa_normal = 'visualizar_rpa_saidas.php';
                                                break;

                                            case '0': $color['rpa_normal'] = 'cor-5';
                                                $pagina_rpa_normal = 'visualizar_rpa_saidas.php';
                                                break;

                                            default: $color['rpa_normal'] = '';
                                                $pagina_rpa_normal = 'cadastro_rpa_guias.php';
                                        }

                                        switch ($row_verifica['rpa_gps']) {
                                            case 1: $color['rpa_gps'] = 'cor-1';
                                                $pagina_gps = 'visualizar_rpa_saidas.php';
                                                break;

                                            case 2: $color['rpa_gps'] = 'cor-3';
                                                $pagina_gps = 'visualizar_rpa_saidas.php';
                                                break;

                                            case 'estorno': $color['rpa_gps'] = 'cor-4';
                                                $pagina_gps = 'visualizar_rpa_saidas.php';
                                                break;

                                            case '0': $color['rpa_gps'] = 'cor-5';
                                                $pagina_rpa_normal = 'visualizar_rpa_saidas.php';
                                                break;

                                            default: $color['rpa_gps'] = '';
                                                $pagina_gps = 'cadastro_rpa_guias.php';
                                        }

                                        switch ($row_verifica['rpa_ir']) {
                                            case 1: $color['rpa_ir'] = 'cor-1';
                                                $pagina_ir = 'visualizar_rpa_saidas.php';
                                                break;

                                            case 2: $color['rpa_ir'] = 'cor-3';
                                                $pagina_ir = 'visualizar_rpa_saidas.php';
                                                break;

                                            case 'estorno': $color['rpa_ir'] = 'cor-4';
                                                $pagina_ir = 'visualizar_rpa_saidas.php';
                                                break;

                                            case '0': $color['rpa_ir'] = 'cor-5';
                                                $pagina_rpa_normal = 'visualizar_rpa_saidas.php';
                                                break;

                                            default: $color['rpa_ir'] = '';
                                                $pagina_ir = 'cadastro_rpa_guias.php';
                                        }

                                        if ($row['id_projeto'] != $projetoAnt and ! empty($projetoAnt)) {

                                            echo'<tr height="40"  style="background-color: #c8ebf9"><td colspan="9" align="right" style="font-weight:bold;">SUBTOTAL:</td><td colspan="4"> R$ ' . number_format($totalizador_projeto, 2, ',', '.') . '</td></tr>';
                                            $totalizador_projeto = 0;
                                        }
                                        ?>
                                        <tr class="<?php echo ($cor++ % 2 == 0) ? "even" : "odd"; ?>">
                                            <td><?php echo $row['unidade']
                        ;
                        ?></td>
                                            <td align="center"><?php echo $row['id_autonomo'] ?></td>
                                            <td>
                                                <?php echo $row['nome']; ?>
                                                <input type="hidden" name="id_rpa" value="<?= $row['id_rpa'] ?>">
                                            </td>
                                            <!--<td><?php echo $row['funcao']; ?></td>-->
                                            <td><?php echo $row['cpf'] ?></td>
                                            <td><?php echo $row['pis'] ?></td>
                                            <td><?php echo $row['nome_banco'] ?></td>
                                            <td><?php echo $row['agencia'] ?></td>
                                            <td><?php echo $row['agencia_dv'] ?></td>
                                            <td><?php echo $row['conta'] ?></td>
                                            <td><?php echo $row['conta_dv'] ?></td>
                                            <td><?php echo number_format($row['valor_liquido'], 2, ',', '.'); ?></td>
                                            <td align="center" class="<?php echo $color['rpa_normal']; ?>"> 

                                                <a href="<?php echo $pagina_rpa_normal; ?>?id_rpa=<?php echo $row['id_rpa'] ?>&tipo_guia=1&id_autonomo=<?php echo $row['id_autonomo']; ?>&mes_consulta=<?php echo $mes; ?>&ano_consulta=<?php echo $ano; ?>&keepThis=true&TB_iframe=true&width=850" class="thickbox" ><img src="../imagensrh/gps.jpg"  ></a>
                                            </td>
                                            <td align="center" class="<?php echo $color['rpa_gps']; ?>">                                    
                                                <a href="<?php echo $pagina_gps; ?>?id_rpa=<?php echo $row['id_rpa'] ?>&tipo_guia=2&id_autonomo=<?php echo $row['id_autonomo']; ?>&mes_consulta=<?php echo $mes; ?>&ano_consulta=<?php echo $ano; ?>&keepThis=true&TB_iframe=true&width=850" class="thickbox" ><img src="../imagensrh/gps.jpg"  ></a>
                                            </td>
                                            <td align="center" class="<?php echo $color['rpa_ir']; ?>">
                                                <a href="<?php echo $pagina_ir; ?>?id_rpa=<?php echo $row['id_rpa'] ?>&tipo_guia=3&id_autonomo=<?php echo $row['id_autonomo']; ?>&mes_consulta=<?php echo $mes; ?>&ano_consulta=<?php echo $ano; ?>&keepThis=true&TB_iframe=true&width=850" class="thickbox" ><img src="../imagensrh/ir.jpg" /></a>
                                            </td>
                                            <td align="center">
                                                <a target="_blank" href="../rpa/folha_de_rosto.php?id_rpa=<?php echo $row['id_rpa'] ?>" ><img style="height:30px" src="imagens/pdf.jpg" /></a>
                                            </td>
                                        </tr>
                                        <?php
                                        $totalizador_projeto += $row[valor_liquido];
                                        $total_regiao += $row['valor_liquido'];
                                        $projetoAnt = $row['id_projeto'];
                                    }
                                    echo'<tr height="40" style="background-color: #c8ebf9"><td colspan="10" align="right" style="font-weight:bold;">SUBTOTAL:</td><td colspan="4"> R$ ' . number_format($totalizador_projeto, 2, ',', '.') . '</td></tr>';
                                    echo'<tr height="40" style="background-color: #c8ebf9"><td colspan="10" align="right" style="font-weight:bold;">TOTAL: </td><td colspan="4"> R$ ' . number_format($total_regiao, 2, ',', '.') . '</td></tr>';
                                    unset($cor);
                                    unset($color);
                                } elseif ($tipoPagamento == 5) {
                                    $cor = 0;
                                    ?>
                                    <!-- TABLE VT -->
                                <table width="100%" cellspacing="0" cellpadding="0" class="grid">
                                    <thead>
                                        <tr>
                                            <th>ID Projeto</th>
                                            <th>Nome Projeto</th>
                                            <th>ID Região</th>
                                            <th>Região</th>
                                            <th>VT</th>
                                            <th>VR</th>
                                            <th>VA</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        while ($row = mysql_fetch_array($result)) {
                                            $total_saidas_vt = mysql_num_rows(mysql_query("SELECT * FROM saida WHERE id_projeto = '{$row[id_projeto]}' AND mes_competencia = '{$mes}' AND ano_competencia = '{$ano}' AND tipo = '12' AND status != '0'"));
                                            $total_saidas_pagas_vt = mysql_num_rows(mysql_query("SELECT * FROM saida WHERE id_projeto = '{$row[id_projeto]}' AND mes_competencia = '{$mes}' AND ano_competencia = '{$ano}' AND tipo = '12' AND status = '2'"));
                                            $total_saidas_vr = mysql_num_rows(mysql_query("SELECT * FROM saida WHERE id_projeto = '{$row[id_projeto]}' AND mes_competencia = '{$mes}' AND ano_competencia = '{$ano}' AND tipo = '13' AND status != '0'"));
                                            $total_saidas_pagas_vr = mysql_num_rows(mysql_query("SELECT * FROM saida WHERE id_projeto = '{$row[id_projeto]}' AND mes_competencia = '{$mes}' AND ano_competencia = '{$ano}' AND tipo = '13' AND status = '2'"));
                                            $total_saidas_va = mysql_num_rows(mysql_query("SELECT * FROM saida WHERE id_projeto = '{$row[id_projeto]}' AND mes_competencia = '{$mes}' AND ano_competencia = '{$ano}' AND tipo = '16' AND status != '0'"));
                                            $total_saidas_pagas_va = mysql_num_rows(mysql_query("SELECT * FROM saida WHERE id_projeto = '{$row[id_projeto]}' AND mes_competencia = '{$mes}' AND ano_competencia = '{$ano}' AND tipo = '16' AND status = '2'"));
                                            ?>
                                            <tr class="<?php echo ($cor++ % 2 == 0) ? "even" : "odd"; ?> even">
                                                <td class="center"><?= $row['id_projeto']; ?></td>
                                                <td class="center"><?= $row['nome_projeto'] ?></td>
                                                <td class="center"><?= $row['id_regiao']; ?></td>
                                                <td class="center"><?= $row['nome_regiao'] ?></td>
                                                <td align="center" class="<?php
                                                if ($total_saidas_vt) {
                                                    echo ($total_saidas_vt == $total_saidas_pagas_vt) ? 'cor-3' : 'cor-1';
                                                }
                                                ?>">
                                                    <a href="form_guia.php?tela=5&id_projeto=<?= $row['id_projeto'] ?>&mes=<?= $mes ?>&ano=<?= $ano ?>&keepThis=true&TB_iframe=true&width=850" class="thickbox">
                                                        <img src="/intranet/imagens/icones/icon-doc.gif" />
                                                    </a>
                                                </td>
                                                <td align="center" class="<?php
                                                if ($total_saidas_vr) {
                                                    echo ($total_saidas_vr == $total_saidas_pagas_vr) ? 'cor-3' : 'cor-1';
                                                }
                                                ?>">
                                                    <a href="form_guia.php?tela=6&id_projeto=<?= $row['id_projeto'] ?>&mes=<?= $mes ?>&ano=<?= $ano ?>&keepThis=true&TB_iframe=true&width=850" class="thickbox" > 
                                                        <img src="../imagensrh/gps.jpg" />
                                                    </a>                                      
                                                </td>
                                                <td align="center" class="<?php
                                                if ($total_saidas_va) {
                                                    echo ($total_saidas_va == $total_saidas_pagas_va) ? 'cor-3' : 'cor-1';
                                                }
                                                ?>">
                                                    <a href="form_guia.php?tela=7&id_projeto=<?= $row['id_projeto'] ?>&mes=<?= $mes ?>&ano=<?= $ano ?>&keepThis=true&TB_iframe=true&width=850" class="thickbox" > 
                                                        <img src="/intranet/imagens/icones/icon-doc.gif" />
                                                    </a>                                      
                                                </td>
                                            </tr>
                                        <?php } unset($cor) ?>
                                    </tbody>
                                </table>
                            <?php } else if ($tipoPagamento == 6) { ?>
                                <table width="100%" cellspacing="0" cellpadding="0" class="grid">
                                    <thead>
                                        <tr>
                                            <th>ID folha</th>
                                            <th>Região</th>
                                            <th>Projeto</th>
                                            <th>PG. SINDICATO</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        while ($row = mysql_fetch_array($result)) {
                                            $sql = "SELECT (SELECT IF(B.estorno != 0 ,'estorno', B.status) FROM pagamentos AS A 
                                                    LEFT JOIN saida AS B ON(A.id_saida=B.id_saida) 
                                                    WHERE A.mes_pg='{$mes}' AND A.ano_pg = '{$ano}' AND A.tipo_pg = 8 AND A.id_folha = {$row['id_folha']} AND A.tipo_contrato_pg = 1 ORDER BY data_proc DESC LIMIT 1) AS sindicato";
                                            $query_controle = mysql_query($sql);
                                            echo '<!-- ' . $sql . ' -->';
                                            $row_controle = mysql_fetch_assoc($query_controle);
//                                            echo mysql_num_rows($query_controle);
                                            switch ($row_controle['sindicato']) {
                                                case '0':
                                                    $color = "cor-2";
                                                    $link_guias = 'cadastro_1.php'; // depois alterar o nome para cadastro_1.php
                                                    break;
                                                case 1:
                                                    $color = "cor-1";
                                                    $link_guias = 'visualizar_guias_saidas.php';

                                                    break;
                                                case 2:
                                                    $color = "cor-3";
                                                    $link_guias = 'visualizar_guias_saidas.php';
                                                    break;
                                                case 'estorno': $color[$i] = 'cor-4';
                                                    $link_guias = 'visualizar_guias_saidas.php';
                                                    break;

                                                default: $color = '';
                                                    $link_guias = 'cadastro_1.php'; // depois alterar o nome para cadastro_1.php
                                            }
                                            ?>
                                            <tr class="<?php echo ($cor++ % 2 == 0) ? "even" : "odd"; ?>">
                                                <td><span class="dados"><?= $row['id_folha'] ?></span></td>
                                                <td><span class="dados"><?= $row['id_regiao'] . " - " . $row['regiao']; ?></span></td>
                                                <td><span class="dados"><?= $row['nome'] . $decimo3 ?></span></td>
                                                <td align="center" class="<?= $color ?>">
                                                    <a href="<?php echo $link_guias; ?>?id_folha=<?= $row['id_folha'] ?>&tipo_guia=8&tipo_contrato=<?php echo $tipoContratacao; ?>&mes_consulta=<?php echo $mes; ?>&ano_consulta=<?php echo $ano; ?>&regiao=<?php echo $regiao; ?>&keepThis=true&TB_iframe=true&width=850" class="thickbox" > 
                                                        <img src="../folha/imagens/verfolha.gif" />
                                                    </a>                                      
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        unset($cor);
                                        ?>           
                                    </tbody>
                                </table>
                                <?php
                            } elseif ($tipoPagamento == 7) {
                                $cor = 0;
                                ?>
                                <div style="margin-bottom: 20px;">
                                    <a href="<?php printf("rel_rpa_analitico_estagiario.php?mes=%d&ano=%d", $mes, $ano) ?>" class="bt-rel_analitico" target="_blank">Ver Recibo de Pagamento de Estágio Analítico</a>
                                </div> 
                                <table width="100%" cellspacing="0" cellpadding="0" class="grid">
                                    <thead>
                                        <tr>
                                            <th>Projeto</th>
                                            <th>ID ESTAGIÁRIO</th>
                                            <th>Nome</th>
                                            <!--<th>Função</th>-->
                                            <th>CPF</th>
                                            <th>PIS</th>
                                            <th>Banco</th>
                                            <th>Agência</th>
                                            <th>Conta</th>
                                            <th>Valor</th>
                                            <th>RPE</th>
                                            <th></th>
                                            <!--<th>IR</th>-->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        while ($row = mysql_fetch_assoc($result)) {
                                            $qr_verifica = mysql_query("SELECT  
                                                                    (SELECT IF(B.estorno != 0 ,'estorno', B.status)
                                                                    FROM rpa_saida_assoc as A
                                                                    INNER JOIN saida as B 
                                                                    ON A.id_saida = B.id_saida
                                                                    WHERE id_rpa_estagiario = '{$row[id_rpa_estagiario]}' AND tipo_vinculo = 1  ORDER BY data_proc DESC LIMIT 1) as rpa_normal,

                                                                    (SELECT IF(B.estorno != 0 ,'estorno', B.status)
                                                                    FROM rpa_saida_assoc as A
                                                                    INNER JOIN saida as B 
                                                                    ON A.id_saida = B.id_saida
                                                                    WHERE id_rpa_estagiario =  '{$row[id_rpa_estagiario]}' AND tipo_vinculo = 2  ORDER BY data_proc DESC LIMIT 1) as rpa_gps,

                                                                    (SELECT  IF(B.estorno != 0 ,'estorno', B.status)
                                                                    FROM rpa_saida_assoc as A
                                                                    INNER JOIN saida as B 
                                                                    ON A.id_saida = B.id_saida
                                                                    WHERE id_rpa_estagiario =  '{$row[id_rpa_estagiario]}' AND tipo_vinculo = 3  ORDER BY data_proc DESC LIMIT 1) as rpa_ir") or die(mysql_error());
                                            $row_verifica = mysql_fetch_assoc($qr_verifica);

                                            switch ($row_verifica['rpa_normal']) {
                                                case 1: $color['rpa_normal'] = 'cor-1';
                                                    $pagina_rpa_normal = 'visualizar_rpa_saidas_estagiario.php';
                                                    break;

                                                case 2: $color['rpa_normal'] = 'cor-3';
                                                    $pagina_rpa_normal = 'visualizar_rpa_saidas_estagiario.php';
                                                    break;

                                                case 'estorno': $color['rpa_normal'] = 'cor-4';
                                                    $pagina_rpa_normal = 'visualizar_rpa_saidas_estagiario.php';
                                                    break;

                                                case '0': $color['rpa_normal'] = 'cor-5';
                                                    $pagina_rpa_normal = 'visualizar_rpa_saidas_estagiario.php';
                                                    break;

                                                default: $color['rpa_normal'] = '';
                                                    $pagina_rpa_normal = 'cadastro_rpa_guias_estagiario.php';
                                            }

                                            switch ($row_verifica['rpa_gps']) {
                                                case 1: $color['rpa_gps'] = 'cor-1';
                                                    $pagina_gps = 'visualizar_rpa_saidas_estagiario.php';
                                                    break;

                                                case 2: $color['rpa_gps'] = 'cor-3';
                                                    $pagina_gps = 'visualizar_rpa_saidas_estagiario.php';
                                                    break;

                                                case 'estorno': $color['rpa_gps'] = 'cor-4';
                                                    $pagina_gps = 'visualizar_rpa_saidas_estagiario.php';
                                                    break;

                                                case '0': $color['rpa_gps'] = 'cor-5';
                                                    $pagina_rpa_normal = 'visualizar_rpa_saidas_estagiario.php';
                                                    break;

                                                default: $color['rpa_gps'] = '';
                                                    $pagina_gps = 'cadastro_rpa_guias_estagiario.php';
                                            }

                                            switch ($row_verifica['rpa_ir']) {
                                                case 1: $color['rpa_ir'] = 'cor-1';
                                                    $pagina_ir = 'visualizar_rpa_saidas_estagiario.php';
                                                    break;

                                                case 2: $color['rpa_ir'] = 'cor-3';
                                                    $pagina_ir = 'visualizar_rpa_saidas_estagiario.php';
                                                    break;

                                                case 'estorno': $color['rpa_ir'] = 'cor-4';
                                                    $pagina_ir = 'visualizar_rpa_saidas_estagiario.php';
                                                    break;

                                                case '0': $color['rpa_ir'] = 'cor-5';
                                                    $pagina_rpa_normal = 'visualizar_rpa_saidas_estagiario.php';
                                                    break;

                                                default: $color['rpa_ir'] = '';
                                                    $pagina_ir = 'cadastro_rpa_guias_estagiario.php';
                                            }

                                            if ($row['id_projeto'] != $projetoAnt and ! empty($projetoAnt)) {

                                                echo'<tr height="40"  style="background-color: #c8ebf9"><td colspan="9" align="right" style="font-weight:bold;">SUBTOTAL:</td><td colspan="4"> R$ ' . number_format($totalizador_projeto, 2, ',', '.') . '</td></tr>';
                                                $totalizador_projeto = 0;
                                            }
                                            ?>
                                            <tr class="<?php echo ($cor++ % 2 == 0) ? "even" : "odd"; ?>">
                                                <td><?php echo $row['nome_projeto']; ?></td>
                                                <td align="center"><?php echo $row['id_estagiario'] ?></td>
                                                <td>
                                                    <?php echo $row['nome']; ?>
                                                    <input type="hidden" name="id_rpa_estagiario" value="<?= $row['id_rpa_estagiario'] ?>">
                                                </td>
                                                <!--<td><?php echo $row['funcao']; ?></td>-->
                                                <td><?php echo $row['cpf'] ?></td>
                                                <td><?php echo $row['pis'] ?></td>
                                                <td><?php echo $row['nome_banco'] ?></td>
                                                <td><?php echo $row['agencia'] ?></td>
                                                <td><?php echo $row['conta'] ?></td>
                                                <td><?php echo number_format($row['valor_liquido'], 2, ',', '.'); ?></td>
                                                <td align="center" class="<?php echo $color['rpa_normal']; ?>"> 
                                                    <a href="<?php echo $pagina_rpa_normal; ?>?id_rpa_estagiario=<?php echo $row['id_rpa_estagiario'] ?>&tipo_guia=1&id_estagiario=<?php echo $row['id_estagiario']; ?>&mes_consulta=<?php echo $mes; ?>&ano_consulta=<?php echo $ano; ?>&keepThis=true&TB_iframe=true&width=850" class="thickbox" ><img src="../imagensrh/gps.jpg"  ></a>
                                                </td>
                <!--                                            <td align="center" class="<?php echo $color['rpa_gps']; ?>">                                    
                                                    <a href="<?php echo $pagina_gps; ?>?id_rpa_estagiario=<?php echo $row['id_rpa_estagiario'] ?>&tipo_guia=2&id_estagiario=<?php echo $row['id_estagiario']; ?>&mes_consulta=<?php echo $mes; ?>&ano_consulta=<?php echo $ano; ?>&keepThis=true&TB_iframe=true&width=850" class="thickbox" ><img src="../imagensrh/gps.jpg"  ></a>
                                                </td>
                                                <td align="center" class="<?php echo $color['rpa_ir']; ?>">
                                                    <a href="<?php echo $pagina_ir; ?>?id_rpa_estagiario=<?php echo $row['id_rpa_estagiario'] ?>&tipo_guia=3&id_estagiario=<?php echo $row['id_estagiario']; ?>&mes_consulta=<?php echo $mes; ?>&ano_consulta=<?php echo $ano; ?>&keepThis=true&TB_iframe=true&width=850" class="thickbox" ><img src="../imagensrh/ir.jpg" /></a>
                                                </td>-->
                                                <td align="center">
                                                    <a target="_blank" href="../rpe/folha_de_rosto.php?id_rpe=<?php echo $row['id_rpa_estagiario'] ?>" ><img style="height:30px" src="imagens/pdf.jpg" /></a>
                                                </td>
                                            </tr>
                                            <?php
                                            $totalizador_projeto += $row[valor_liquido];
                                            $total_regiao += $row['valor_liquido'];
                                            $projetoAnt = $row['id_projeto'];
                                        }
                                        echo'<tr height="40" style="background-color: #c8ebf9"><td colspan="8" align="right" style="font-weight:bold;">SUBTOTAL:</td><td colspan="4"> R$ ' . number_format($totalizador_projeto, 2, ',', '.') . '</td></tr>';
                                        echo'<tr height="40" style="background-color: #c8ebf9"><td colspan="8" align="right" style="font-weight:bold;">TOTAL: </td><td colspan="4"> R$ ' . number_format($total_regiao, 2, ',', '.') . '</td></tr>';
                                        unset($cor);
                                        unset($color);
                                    } elseif ($tipoPagamento == 8) {
                                        $cor = 0;
                                        ?>
                                    <div style="margin-bottom: 20px;">
                                        <a href="<?php printf("rel_rpa_analitico_prestador.php?mes=%d&ano=%d", $mes, $ano) ?>" class="bt-rel_analitico" target="_blank">Ver RPA Analítico</a>
                                    </div> 
                                    <table width="100%" cellspacing="0" cellpadding="0" class="grid">
                                        <thead>
                                            <tr>
                                                <th>Projeto</th>
                                                <th>ID PRESTADOR</th>
                                                <th>Nome</th>
                                                <th>CPF</th>
                                                <th>Banco</th>
                                                <th>Agência</th>
                                                <th>Agência DV</th>
                                                <th>Conta</th>
                                                <th>Conta DV</th>
                                                <th>Valor</th>
                                                <th>RPA</th>
                                                <th>GPS</th>
                                                <th>IR</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            while ($row = mysql_fetch_assoc($result)) {
                                                $qr_verifica = mysql_query("SELECT  
                                                                    (SELECT IF(B.estorno != 0 ,'estorno', B.status)
                                                                    FROM rpa_saida_assoc as A
                                                                    INNER JOIN saida as B 
                                                                    ON A.id_saida = B.id_saida
                                                                    WHERE A.id_rpa = '{$row['id_rpa']}' AND tipo_vinculo = 1  ORDER BY data_proc DESC LIMIT 1) as rpa_normal,

                                                                    (SELECT IF(B.estorno != 0 ,'estorno', B.status)
                                                                    FROM rpa_saida_assoc as A
                                                                    INNER JOIN saida as B 
                                                                    ON A.id_saida = B.id_saida
                                                                    WHERE A.id_rpa =  '{$row['id_rpa']}' AND tipo_vinculo = 2  ORDER BY data_proc DESC LIMIT 1) as rpa_gps,

                                                                    (SELECT  IF(B.estorno != 0 ,'estorno', B.status)
                                                                    FROM rpa_saida_assoc as A
                                                                    INNER JOIN saida as B 
                                                                    ON A.id_saida = B.id_saida
                                                                    WHERE A.id_rpa =  '{$row['id_rpa']}' AND tipo_vinculo = 3  ORDER BY data_proc DESC LIMIT 1) as rpa_ir") or die(mysql_error());
                                                $row_verifica = mysql_fetch_assoc($qr_verifica);

                                                switch ($row_verifica['rpa_normal']) {
                                                    case 1: $color['rpa_normal'] = 'cor-1';
                                                        $pagina_rpa_normal = 'visualizar_rpa_saidas_prestador.php';
                                                        break;

                                                    case 2: $color['rpa_normal'] = 'cor-3';
                                                        $pagina_rpa_normal = 'visualizar_rpa_saidas_prestador.php';
                                                        break;

                                                    case 'estorno': $color['rpa_normal'] = 'cor-4';
                                                        $pagina_rpa_normal = 'visualizar_rpa_saidas_prestador.php';
                                                        break;

                                                    case '0': $color['rpa_normal'] = 'cor-5';
                                                        $pagina_rpa_normal = 'visualizar_rpa_saidas_prestador.php';
                                                        break;

                                                    default: $color['rpa_normal'] = '';
                                                        $pagina_rpa_normal = 'cadastro_rpa_guias.php';
                                                }

                                                switch ($row_verifica['rpa_gps']) {
                                                    case 1: $color['rpa_gps'] = 'cor-1';
                                                        $pagina_gps = 'visualizar_rpa_saidas_prestador.php';
                                                        break;

                                                    case 2: $color['rpa_gps'] = 'cor-3';
                                                        $pagina_gps = 'visualizar_rpa_saidas_prestador.php';
                                                        break;

                                                    case 'estorno': $color['rpa_gps'] = 'cor-4';
                                                        $pagina_gps = 'visualizar_rpa_saidas_prestador.php';
                                                        break;

                                                    case '0': $color['rpa_gps'] = 'cor-5';
                                                        $pagina_rpa_normal = 'visualizar_rpa_saidas_prestador.php';
                                                        break;

                                                    default: $color['rpa_gps'] = '';
                                                        $pagina_gps = 'cadastro_rpa_guias.php';
                                                }

                                                switch ($row_verifica['rpa_ir']) {
                                                    case 1: $color['rpa_ir'] = 'cor-1';
                                                        $pagina_ir = 'visualizar_rpa_saidas_prestador.php';
                                                        break;

                                                    case 2: $color['rpa_ir'] = 'cor-3';
                                                        $pagina_ir = 'visualizar_rpa_saidas_prestador.php';
                                                        break;

                                                    case 'estorno': $color['rpa_ir'] = 'cor-4';
                                                        $pagina_ir = 'visualizar_rpa_saidas_prestador.php';
                                                        break;

                                                    case '0': $color['rpa_ir'] = 'cor-5';
                                                        $pagina_rpa_normal = 'visualizar_rpa_saidas_prestador.php';
                                                        break;

                                                    default: $color['rpa_ir'] = '';
                                                        $pagina_ir = 'cadastro_rpa_guias.php';
                                                }

                                                if ($row['id_projeto'] != $projetoAnt and ! empty($projetoAnt)) {

                                                    echo'<tr height="40"  style="background-color: #c8ebf9"><td colspan="9" align="right" style="font-weight:bold;">SUBTOTAL:</td><td colspan="4"> R$ ' . number_format($totalizador_projeto, 2, ',', '.') . '</td></tr>';
                                                    $totalizador_projeto = 0;
                                                }
                                                ?>
                                                <tr class="<?php echo ($cor++ % 2 == 0) ? "even" : "odd"; ?>">
                                                    <td><?php echo $row['nome_projeto']; ?></td>
                                                    <td align="center"><?php echo $row['id_prestador'] ?></td>
                                                    <td>
                                                        <?php echo $row['c_fantasia']; ?>
                                                        <input type="hidden" name="id_rpa" value="<?= $row['id_rpa'] ?>">
                                                    </td>
                                                    <td><?php echo $row['c_cpf'] ?></td>
                                                    <td><?php echo $row['nome_banco'] ?></td>
                                                    <td><?php echo $row['agencia'] ?></td>
                                                    <td><?php echo $row['agencia_dv'] ?></td>
                                                    <td><?php echo $row['conta'] ?></td>
                                                    <td><?php echo $row['conta_dv'] ?></td>
                                                    <td><?php echo number_format($row['valor_liquido'], 2, ',', '.'); ?></td>
                                                    <td align="center" class="<?php echo $color['rpa_normal']; ?>"> 

                                                        <a href="<?php echo $pagina_rpa_normal; ?>?id_rpa=<?php echo $row['id_rpa'] ?>&tipo_guia=1&id_prestador=<?php echo $row['id_prestador']; ?>&mes_consulta=<?php echo $mes; ?>&ano_consulta=<?php echo $ano; ?>&keepThis=true&TB_iframe=true&width=850" class="thickbox" ><img src="../imagensrh/gps.jpg"  ></a>
                                                    </td>
                                                    <td align="center" class="<?php echo $color['rpa_gps']; ?>">                                    
                                                        <a href="<?php echo $pagina_gps; ?>?id_rpa=<?php echo $row['id_rpa'] ?>&tipo_guia=2&id_prestador=<?php echo $row['id_prestador']; ?>&mes_consulta=<?php echo $mes; ?>&ano_consulta=<?php echo $ano; ?>&keepThis=true&TB_iframe=true&width=850" class="thickbox" ><img src="../imagensrh/gps.jpg"  ></a>
                                                    </td>
                                                    <td align="center" class="<?php echo $color['rpa_ir']; ?>">
                                                        <a href="<?php echo $pagina_ir; ?>?id_rpa=<?php echo $row['id_rpa'] ?>&tipo_guia=3&id_prestador=<?php echo $row['id_prestador']; ?>&mes_consulta=<?php echo $mes; ?>&ano_consulta=<?php echo $ano; ?>&keepThis=true&TB_iframe=true&width=850" class="thickbox" ><img src="../imagensrh/ir.jpg" /></a>
                                                    </td>
                                                </tr>
                                                <?php
                                                $totalizador_projeto += $row[valor_liquido];
                                                $total_regiao += $row['valor_liquido'];
                                                $projetoAnt = $row['id_projeto'];
                                            }
                                            echo'<tr height="40" style="background-color: #c8ebf9"><td colspan="10" align="right" style="font-weight:bold;">SUBTOTAL:</td><td colspan="4"> R$ ' . number_format($totalizador_projeto, 2, ',', '.') . '</td></tr>';
                                            echo'<tr height="40" style="background-color: #c8ebf9"><td colspan="10" align="right" style="font-weight:bold;">TOTAL: </td><td colspan="4"> R$ ' . number_format($total_regiao, 2, ',', '.') . '</td></tr>';
                                            unset($cor);
                                            unset($color);
                                        }
                                        ?>
                                    <?php } ?><?php } ?>
                                </div>
                                </form>
                                </body>
                            <div  style="background-color: #c8ebf9 ">
                            </div>
                            </html>
