<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}
error_reporting(E_ALL);
/*
 * LAST UPDATE
 * RAMON LIMA
 * 11/04/2013
 */

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
    $tipoPagamento = $_REQUEST['tipo_pagamento'];
    $tipoContratacao = $_REQUEST['tipo_contrato'];

    switch ($tipoPagamento) {
        
        
        case 1: //"1"=>"Pagamentos Folha"
            
                switch($tipoContratacao){

                case 1: $tabela_folha = 'folhas';
                         $contratacao = " AND f.contratacao = '1' ";    
                         
                        break;
                    
                case 2: $tabela_folha = 'rh_folha';
                        $contratacao = " ";
                        $tipo_contrato_pg = 1;
                        break;

                case 3:  $tabela_folha = 'folhas';
                         $contratacao = " AND f.contratacao = '3' ";
                          $tipo_contrato_pg = 2;
                         break;

                case 4:  $tabela_folha = 'folhas';
                         $contratacao = " AND f.contratacao = '4' ";

                }
            
          
            
            
            $query = "SELECT f.id_folha, f.projeto, p.id_regiao, f.terceiro, f.tipo_terceiro, p.regiao, p.nome
                        FROM $tabela_folha f
                        INNER JOIN projeto p ON f.projeto = p.id_projeto
                        WHERE (f.status = '3' OR f.status = '2') AND 
                             p.id_master = {$usuario['id_master']} AND 
                             f.mes = '{$mes}' AND 
                             f.ano = '{$ano}' AND 
                             p.id_regiao != '36' 
                             $contratacao
                                ORDER BY f.projeto";
            break;
            
            
            
        case 2: //"2" => "Pagamentos Rescisão"
            $query = "SELECT A.id_recisao, A.id_clt as clt, B.nome AS nome_clt,D.nome AS nprojeto, A.total_liquido, C.id_regiao, C.regiao, D.id_projeto, D.nome, E.*, F.*,F.status as status_saida, lek.*
                        FROM rh_recisao AS A
                        LEFT JOIN rh_clt AS B ON B.id_clt = A.id_clt
                        LEFT JOIN regioes AS C ON A.id_regiao = C.id_regiao
                        LEFT JOIN projeto AS D ON A.id_projeto = D.id_projeto
                        LEFT JOIN pagamentos_especifico AS E ON (E.id_clt = A.id_clt)
                        LEFT JOIN saida AS F ON (E.id_saida = F.id_saida)
                        LEFT JOIN (
                        SELECT BB.`status` as status_multa,BB.id_clt FROM saida_files as AA 
                        INNER JOIN saida as BB
                        ON AA.id_saida = BB.id_saida
                        WHERE BB.tipo = '167' AND AA.multa_rescisao = 1) as lek ON (lek.id_clt = A.id_clt)
                        WHERE 
                            MONTH(A.data_demi) = '$mes'
                            AND YEAR(A.data_demi) = '$ano'
                            AND A.status = '1' 
                            AND A.id_regiao != '36'
                            AND D.id_master = {$usuario['id_master']}
                                GROUP BY B.id_clt
                                ORDER BY C.id_regiao,D.nome,B.nome";
            break;
        case 3: //"3" => "Pagamentos Férias"
            $query = "SELECT A.*,B.regiao as nome_regiao, C.nome as nome_projeto,  D.nome as nome_clt
                    FROM rh_ferias as A
                    INNER JOIN regioes as B
                    ON B.id_regiao = A.regiao
                    INNER JOIN projeto as C
                    ON C.id_projeto = A.projeto
                    INNER JOIN rh_clt as D
                    ON A.id_clt = D.id_clt
                    WHERE A.status = 1 AND B.id_master = $usuario[id_master] AND A.mes = '$mes' AND A.ano = '$ano';";
           
            break;
        case 4:
            $query =   "SELECT A.*, B.nome,  C.nome as nome_projeto, B.banco, B.conta, B.agencia, B.tipo_pagamento,B.nome_banco, B.cpf
                        FROM  rpa_autonomo as A
                        INNER JOIN autonomo as B 
                        ON A.id_autonomo = B.id_autonomo
                        INNER JOIN projeto as C
                        ON C.id_projeto = B.id_projeto
                        WHERE B.id_regiao = '$usuario[id_regiao]' AND MONTH(data_geracao) = '$mes' AND YEAR(data_geracao) = '$ano'
            ORDER BY B.id_projeto,B.nome";
            break;
        
        
    }
    echo "<!-- $query --> \r\n";
    $result = mysql_query($query);
}

//CARREGA TIPOS DE PAGAMENTOS PARA SELECT
$tiposPg = array("1" => "Pagamentos Folha", "2" => "Pagamentos Rescisão", "3" => "Pagamentos Férias", "4" => "Pagamentos RPA");

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
for ($i = 2009; $i <= date('Y'); $i++) {
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
                $(".bt,.bt-rpa").css('cursor','pointer');
                $(".bt-ver").css('cursor','pointer');
                $(".bt-criar").css('cursor','pointer');
                
                $('#tipo_pagamento').change(function(){
                    
                    if($(this).val() == 4){
                        $('#tipo_contrato').val(1)
                    };
                })
                
                
                $(".bt").click(function(){
                  
                    
                    var botao  = $(this)
                    var id     = botao.data('key');
                    var type   = botao.data('type');
                    var title  = botao.data('title');
                    var classe = botao.parent().attr('class');
                    var tipo_contrato = botao.data('tipo_contrato')
                   
                    
                    if(classe!=""){
                        //ja existe saída
                        thickBoxIframe("Detalhes "+title, "index_popup.php", {id: id, tipo: type, tipo_contrato: tipo_contrato}, 850, 450);
                    }else{
                        thickBoxIframe("Detalhes "+title, "cadastro_1.php", {id: id, tipo: type, contratacao: $("#tipo_contrato").val()}, 850, 450,null, false);
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
                    
                    if($('#progressBar').html() != "" || valor > 0){

                        var   cod1 = $('#campo_codigo_gerais1').val();
                        var   cod2 = $('#campo_codigo_gerais2').val();
                        var   cod3 = $('#campo_codigo_gerais3').val();
                        var   cod4 = $('#campo_codigo_gerais4').val();
                        var   cod5 = $('#campo_codigo_gerais5').val();
                        var   cod6 = $('#campo_codigo_gerais6').val();
                        var   cod7 = $('#campo_codigo_gerais7').val();
                        var   cod8 = $('#campo_codigo_gerais8').val();

                        var cod_barra_gerais = cod1+cod2+cod3+cod4+cod5+cod6+cod7+cod8;

                        $.post('actions/cadastra.php',
                        {
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
                    }else{
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
                    'onComplete': function(a,b,c,d){		
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
                    'onComplete': function(a,b,c,d){		
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
                        campo.val(valor);
                    }
                }
            }
        </script>
        <style>
            .bt-rel_analitico{
               
                background-color:  #cccccc;
                color:#000;
                font-weight: bold;
                text-decoration: none;
                width:250px;
                height:50px;
                padding: 3px;
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
                        <img src="imagens/status.jpg" />
                    </div>
                    <br class="clear"/>
                    <p class="controls" style="margin-top: 10px;"><input type="submit" name="filtrar" value="Filtrar" id="filtrar"/></p>
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
                                        <th>GPS</th>
                                        <th>FGTS</th>
                                        <th>PIS</th>
                                        <th>IR</th>
                                        <th class="separa">&nbsp</th>
                                        <th>TRANSPORTE</th>
                                        <th>ALIMENTAÇÃO</th>
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
                                            SELECT MAX(B.`status`) FROM pagamentos AS A 
                                            LEFT JOIN saida AS B ON(A.id_saida=B.id_saida) 
                                            WHERE A.mes_pg='{$mes}' AND A.ano_pg = '{$ano}' AND A.id_folha = {$row_folha['id_folha']} AND A.tipo_pg = 1 AND A.tipo_contrato_pg = $tipo_contrato_pg) as gps,

                                            (SELECT MAX(B.`status`) FROM pagamentos AS A 
                                            LEFT JOIN saida AS B ON(A.id_saida=B.id_saida) 
                                            WHERE A.mes_pg='{$mes}' AND A.ano_pg = '{$ano}' AND A.id_folha = {$row_folha['id_folha']} AND A.tipo_pg = 2 AND A.tipo_contrato_pg = $tipo_contrato_pg) as fgts,

                                            (SELECT MAX(B.`status`) FROM pagamentos AS A 
                                            LEFT JOIN saida AS B ON(A.id_saida=B.id_saida) 
                                            WHERE A.mes_pg='{$mes}' AND A.ano_pg = '{$ano}' AND A.id_folha = {$row_folha['id_folha']} AND A.tipo_pg = 3 AND A.tipo_contrato_pg = $tipo_contrato_pg) as pis,

                                            (SELECT MAX(B.`status`) FROM pagamentos AS A 
                                            LEFT JOIN saida AS B ON(A.id_saida=B.id_saida) 
                                            WHERE A.mes_pg='{$mes}' AND A.ano_pg = '{$ano}' AND A.id_folha = {$row_folha['id_folha']} AND A.tipo_pg = 4 AND A.tipo_contrato_pg = $tipo_contrato_pg) as ir
                                    ";
                                            
                                       
                                        $query_controle = mysql_query($sql);
                                        $row_controle = mysql_fetch_assoc($query_controle);
                                        $tipos = array("1" => "gps", "2" => "fgts", "3" => "pis", "4" => "ir");
                                        ?>

                                        <tr class="<?php echo ($cor++ % 2 == 0) ? "even" : "odd"; ?>">
                                            <td><span class="dados"><?= $row_folha['id_folha'] ?></span></td>
                                            <td><span class="dados"><?= $row_folha['id_regiao'] . " - " . $row_folha['regiao']; ?></span></td>
                                            <td><span class="dados"><?= $row_folha['nome'] . $decimo3 ?></span></td>
                                            <?php
                                            for ($i = 1; $i <= 4; $i++) {
                                                if ($row_controle[$tipos[$i]] != "") {
                                                    switch ($row_controle[$tipos[$i]]) {
                                                        case 0:
                                                            $color[$i] = "cor-2";
                                                            $link_guias = 'cadastro_1.php' ;
                                                            break;
                                                        case 1:
                                                            $color[$i] = "cor-1";
                                                             $link_guias = 'visualizar_guias_saidas.php' ;
                                                            break;
                                                        case 2:
                                                            $color[$i] = "cor-3";
                                                              $link_guias = 'visualizar_guias_saidas.php' ;
                                                            break;
                                                        default: $color[$i] = '';
                                                                 $link_guias = 'cadastro_1.php' ;
                                                    }
                                                } else {
                                                    $color[$i] = '';
                                                     $link_guias = 'cadastro_1.php' ;
                                                }
                                            }
                                            ?>                                           
                                            
                                            <!-----    GPS    ----------------------->
                                            <td align="center" class="<?= $color[1] ?>">
                                                <a href="<?php echo $link_guias;?>?id_folha=<?= $row_folha['id_folha'] ?>&tipo_guia=1&tipo_contrato=<?php echo $tipoContratacao;?>&keepThis=true&TB_iframe=true&width=850" class="thickbox" > 
                                                   <img src="../imagensrh/gps.jpg" />
                                                </a>                                      
                                               </td>
                                               
                                            
                                            <td align="center" class="<?= $color[2] ?>">
                                                   <a href="<?php echo $link_guias;?>?id_folha=<?= $row_folha['id_folha'] ?>&tipo_guia=2&tipo_contrato=<?php echo $tipoContratacao;?>&keepThis=true&TB_iframe=true&width=850" class="thickbox" > 
                                                   <img src="../imagensrh/log_fgts.jpg" />
                                                </a>  
                                              
                                                </td>
                                                
                                            <td align="center" class="<?= $color[3] ?>">
                                                
                                                <a href="<?php echo $link_guias;?>?id_folha=<?= $row_folha['id_folha'] ?>&tipo_guia=3&tipo_contrato=<?php echo $tipoContratacao;?>&keepThis=true&TB_iframe=true&width=850" class="thickbox" > 
                                                   <img src="../imagensrh/pis.jpg" />
                                                </a>  
                                            </td>                                           
                                            
                                            <td align="center" class="<?= $color[4] ?>">
                                                  <a href="<?php echo $link_guias;?>?id_folha=<?= $row_folha['id_folha'] ?>&tipo_guia=4&tipo_contrato=<?php echo $tipoContratacao;?>&keepThis=true&TB_iframe=true&width=850" class="thickbox" > 
                                                   <img src="../imagensrh/ir.jpg" />
                                                  </a>  
                                            </td>
                                            
                                            
                                            <td class="separa">&nbsp</td>
                                            <td class="txcenter">-</td>
                                            <td class="txcenter">-</td>
                                        </tr>
                                        <?php
                                        unset($color, $decimo3);
                                    }
                                    ?>
                                </tbody>
                            </table>
                        <?php }elseif($tipoPagamento == 2) { ?>
                            <table width="100%" cellspacing="0" cellpadding="0" class="grid">
                                <thead>
                                    <tr>
                                        <th>Projeto</th>
                                        <th>ID CLT</th>
                                        <th>Nome</th>
                                        <th>Valor</th>
                                        <th>Rescisão</th>
                                        <th>Multa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $cor = 0;
                                    while ($row_resci = mysql_fetch_assoc($result)) {
                                        
                                        
                                             $query_saida_multa = mysql_query("SELECT b.status FROM saida_files as a 
                                                    INNER JOIN saida as b
                                                    ON a.id_saida = b.id_saida
                                                    WHERE b.id_clt = '$row_resci[clt]' AND b.tipo IN(167,170) AND a.multa_rescisao = 1");
                                        $row_saida_multa = mysql_fetch_assoc($query_saida_multa);                
                                        $num_saida_multa = mysql_num_rows($query_saida_multa);

                                        
                                        
                                        
                                        switch ($row_resci['status_saida']) {
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
                                    ?>
                                    <tr class="<?php echo ($cor++ % 2 == 0) ? "even" : "odd"; ?>">
                                        <td><?php echo $row_resci['nprojeto']?></td>
                                        <td class="center"><?php echo $row_resci['clt']?></td>
                                        <td><?php echo $row_resci['nome_clt']?></td>
                                        <td>R$ <?php echo number_format($row_resci['total_liquido'],2,",",".")?></td>
                                        <td align="center" class="<?= $color['re'] ?>">
                                            <?php if($color['re']==""){ ?>
                                            
                                            <a href="detalhes_novo.php?id_clt=<?php echo $row_resci['clt']?>&mes=<?php echo $mes; ?>&ano=<?php echo $ano;?>&id_rescisao=<?php echo $row_resci['id_recisao']?>&tipo=2&keepThis=true&TB_iframe=true&width=850" class="thickbox" > 
                                                <img border="0px" src="imagens/saida-32.png" width="18" height="18">
                                            </a>
                                            <?php }else{ ?>
                                              <a href="visualizar_resc_ferias.php?id_clt=<?php echo $row_resci['clt']?>&mes=<?php echo $mes; ?>&ano=<?php echo $ano;?>&id_rescisao=<?php echo $row_resci['id_recisao']?>&tipo=2&keepThis=true&TB_iframe=true&width=850" class="thickbox" > 
                                               <img border="0px" src="../folha/imagens/verfolha.gif" width="18" height="18" />
                                            </a>
                                            <?php } ?>
                                        </td>
                                        <td align="center" class="<?= $color['mu'] ?>">
                                            <?php if($color['mu']== ""){ ?>
                                            
                                            <a href="detalhes_novo.php?id_clt=<?php echo $row_resci['clt']?>&mes=<?php echo $mes; ?>&ano=<?php echo $ano;?>&id_rescisao=<?php echo $row_resci['id_recisao']?>&tipo=3&keepThis=true&TB_iframe=true&width=850" class="thickbox" > 
                                                <img border="0px" src="imagens/saida-32.png" width="18" height="18">
                                            </a>
                                            
                                            <?php }else{ ?>
                                            
                                               <a href="visualizar_resc_ferias.php?id_clt=<?php echo $row_resci['clt']?>&mes=<?php echo $mes; ?>&ano=<?php echo $ano;?>&id_rescisao=<?php echo $row_resci['id_recisao']?>&tipo=3&keepThis=true&TB_iframe=true&width=850" class="thickbox" > 
                                               <img border="0px" src="../folha/imagens/verfolha.gif" width="18" height="18" />
                                            </a>
                                            <?php } ?>
                                        
                                        </td>
                                    </tr>
                                <?php
                                unset($color);
                            }
                            ?>
                                    
                      <?php } elseif($tipoPagamento == 3) { ?>        
                                   
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
                               <?php while($row = mysql_fetch_assoc($result)) {
                                   
                                   $qr_verifica_saida = mysql_query("SELECT MAX(B.status) as status FROM pagamentos_especifico as A 
                                                                    INNER JOIN saida as B
                                                                    ON B.id_saida = A.id_saida
                                                                    WHERE A.id_clt = '$row[id_clt]' AND (B.tipo = 76 OR B.tipo = 156);");
                                   $row_verifica = mysql_fetch_assoc($qr_verifica_saida);
                                   
                                   switch ($row_verifica['status']){
                                           case 1:
                                                $color['ferias'] = "cor-1";
                                                break;
                                            case 2:
                                                $color['ferias'] = "cor-3";
                                                break;
                                            default: $color['ferias'] = '';
                                   }
                                   
                                   
                                   ?>
                                    
                                    <tr>
                                        <td><?php echo $row['nome_projeto']?></td>
                                        <td><?php echo $row['id_clt']?></td>
                                        <td><?php echo $row['nome_clt']?></td>
                                        <td><?php echo number_format($row['total_liquido'],2,',','.')?></td>
                                        <td align="center" class="<?= $color['ferias'] ?>">
                                           <?php if($color['ferias']== ""){ ?>
                                            
                                                <a href="detalhes_novo.php?id_clt=<?php echo $row['id_clt']?>&mes=<?php echo $mes; ?>&ano=<?php echo $ano;?>&id_ferias=<?php echo $row['id_ferias']?>&tipo=1&keepThis=true&TB_iframe=true&width=850" class="thickbox" > 
                                                    <img border="0px" src="imagens/saida-32.png" width="18" height="18">
                                                </a>  
                                            
                                            <?php } else { ?>                                            
                                                <a href="visualizar_resc_ferias.php?id_clt=<?php echo $row['id_clt']?>&mes=<?php echo $mes; ?>&ano=<?php echo $ano;?>&id_ferias=<?php echo $row['id_ferias']?>&tipo=1&keepThis=true&TB_iframe=true&width=850" class="thickbox" > 
                                                    <img border="0px" src="../folha/imagens/verfolha.gif" width="18" height="18" />
                                                 </a>
                                            
                                            <?php } ?>
                                         </td>
                                    </tr>
                                   
                                    
                                <?php        
                                   }
                                ?>    
                                </tbody>  
                                 </table>
                                    
                        <?php }elseif($tipoPagamento == 4) { ?>
                                    
                                <div style="margin-bottom: 20px;"><a href="<?php printf("rel_rpa_analitico.php?mes=%d&ano=%d",$mes,$ano)?>" class="bt-rel_analitico" target="_blank">Ver RPA Analítico</a></div> 
                                    
                              <table width="100%" cellspacing="0" cellpadding="0" class="grid">
                                <thead>
                                    <tr>
                                        <th>Projeto</th>
                                        <th>ID AUTONOMO</th>
                                        <th>Nome</th>
                                        <th>CPF</th>
                                        <th>Banco</th>
                                        <th>Agência</th>
                                        <th>Conta</th>
                                        <th>Valor</th>
                                        <th>RPA</th>
                                        <th>GPS</th>
                                        <th>IR</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php 
                                while($row = mysql_fetch_assoc($result)){
                                    
                                    $qr_verifica = mysql_query("SELECT * FROM 
                                                                    (SELECT MAX(B.status) as  rpa_normal 
                                                                    FROM rpa_saida_assoc as A
                                                                    INNER JOIN saida as B 
                                                                    ON A.id_saida = B.id_saida
                                                                    WHERE id_rpa = '{$row[id_rpa]}' AND tipo_vinculo = 1 ) as rpa_normal,

                                                                    (SELECT MAX(B.status) as  rpa_gps 
                                                                    FROM rpa_saida_assoc as A
                                                                    INNER JOIN saida as B 
                                                                    ON A.id_saida = B.id_saida
                                                                    WHERE id_rpa =  '{$row[id_rpa]}' AND tipo_vinculo = 2 ) as rpa_gps,

                                                                    (SELECT MAX(B.status) as  rpa_ir 
                                                                    FROM rpa_saida_assoc as A
                                                                    INNER JOIN saida as B 
                                                                    ON A.id_saida = B.id_saida
                                                                    WHERE id_rpa =  '{$row[id_rpa]}' AND tipo_vinculo = 3 ) as rpa_ir") or die(mysql_error());
                                   $row_verifica = mysql_fetch_assoc($qr_verifica);
                                    
                                   
                                   switch($row_verifica['rpa_normal']){                                       
                                       case 1: $color['rpa_normal'] = 'cor-1';
                                               $pagina_rpa_normal = 'visualizar_rpa_saidas.php';
                                           break;
                                       
                                       case 2: $color['rpa_normal'] = 'cor-3';
                                               $pagina_rpa_normal        = 'visualizar_rpa_saidas.php';
                                           break;                 
                                       
                                       default: $color['rpa_normal'] = '';
                                                $pagina_rpa_normal = 'cadastro_rpa_guias.php';
                                   }
                                   
                                   
                                   switch($row_verifica['rpa_gps']){                                       
                                       case 1: $color['rpa_gps'] = 'cor-1';
                                               $pagina_gps = 'visualizar_rpa_saidas.php';
                                           break;
                                       
                                       case 2: $color['rpa_gps'] = 'cor-3';
                                               $pagina_gps        = 'visualizar_rpa_saidas.php';
                                           break;                 
                                       
                                       default: $color['rpa_gps'] = '';
                                                $pagina_gps = 'cadastro_rpa_guias.php';
                                   }
                                   
                                   switch($row_verifica['rpa_ir']){                                       
                                       case 1: $color['rpa_ir'] = 'cor-1';
                                               $pagina_ir = 'visualizar_rpa_saidas.php';
                                           break;
                                       case 2: $color['rpa_ir'] = 'cor-3';
                                               $pagina_ir = 'visualizar_rpa_saidas.php';
                                           break;                                       
                                       default: $color['rpa_ir'] = '';
                                              $pagina_ir = 'cadastro_rpa_guias.php';
                                   }
                                   
                              
                                    ?>
                                    <tr>
                                    <td><?php echo $row[nome_projeto];?></td>
                                    <td align="center"><?php echo $row[id_autonomo]?></td>
                                    <td><?php echo $row[nome];?></td>
                                    <td><?php echo $row[cpf]?></td>
                                    <td><?php echo $row[nome_banco]?></td>
                                    <td><?php echo $row[agencia]?></td>
                                    <td><?php echo $row[conta]?></td>
                                    <td><?php echo number_format($row[valor_liquido],2,',','.'); ?></td>
                                    <td align="center" class="<?php echo $color['rpa_normal'];?>">                                    
                                        <a href="<?php echo $pagina_rpa_normal;?>?id_rpa=<?php echo $row['id_rpa']?>&tipo_guia=1&id_autonomo=<?php echo $row['id_autonomo']; ?>&keepThis=true&TB_iframe=true&width=850" class="thickbox" ><img src="../imagensrh/gps.jpg"  ></a>
                                    </td>
                                    <td align="center" class="<?php echo $color['rpa_gps'];?>">                                    
                                        <a href="<?php echo $pagina_gps;?>?id_rpa=<?php echo $row['id_rpa']?>&tipo_guia=2&id_autonomo=<?php echo $row['id_autonomo']; ?>&keepThis=true&TB_iframe=true&width=850" class="thickbox" ><img src="../imagensrh/gps.jpg"  ></a>
                                    </td>
                                    <td align="center" class="<?php echo $color['rpa_ir'];?>">
                                            <a href="<?php echo $pagina_ir;?>?id_rpa=<?php echo $row['id_rpa']?>&tipo_guia=3&id_autonomo=<?php echo $row['id_autonomo']; ?>&keepThis=true&TB_iframe=true&width=850" class="thickbox" ><img src="../imagensrh/ir.jpg" /></a>
                                    </td>
                                </tr>
                            <?php       
                                }
                              
                           } ?>           
                    <?php } ?>
                <?php } ?>
            </div>
        </form>
    </body>
</html>