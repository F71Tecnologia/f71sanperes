<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}

include "../conn.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include "../wfunction.php";
include "../classes_permissoes/acoes.class.php";
include "../classes/LogClass.php";
$log = new Log();

$usuario = carregaUsuario();
$optRegiao = getRegioes();
$optMeses = mesesArray();

//ARRAY DE ANOS
for ($i = 2010; $i <= date('Y'); $i++) {
    $optAnos[$i] = $i;
}

$query = "SELECT * FROM rh_movimentos";
$sql = mysql_query($query) or die("Erro ao selecionar Movimentos");
$movimentos = array();
while($linhas_mov = mysql_fetch_assoc($sql)){
    $movimentos[$linhas_mov['cod']] = $linhas_mov['descicao']; 
}

$historico_movimentos = "SELECT A.*,A.projeto AS id_projeto, B.nome as nome_projeto, C.nome as nome_funcionario, DATE_FORMAT( A.criado_em, '%d/%m/%Y') AS data_criacao
                    FROM header_movimentos_lote AS A
                    LEFT JOIN projeto AS B ON(A.projeto = B.id_projeto)
                    LEFT JOIN funcionario AS C ON(A.criado_por = C.id_funcionario)";
$sql_historico = mysql_query($historico_movimentos) or die("Erro so selecionar histórico de movimentos em lote");
$dados_historico = array();
while($rows_historico = mysql_fetch_assoc($sql_historico)){
    $dados_historico[] = array(
        "id" => $rows_historico['id_header'],
        "projeto" => $rows_historico['nome_projeto'],
        "id_projeto" => $rows_historico['id_projeto'],
        "mes" => $rows_historico['mes_mov'],
        "ano" => $rows_historico['ano_mov'],
        "valor" => $rows_historico['valor_mov'],
        "por" => $rows_historico['nome_funcionario'],
        "em" => $rows_historico['data_criacao']
    );
}

if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'lancar_movimento'){
    
    $return = array("status" => 0);
    
    $data_cad = date("Y-m-d");
    $valor = str_replace(".", "", $_REQUEST['valor_mov']);
    $valor = str_replace(",", ".", $valor);
    
    //CADASTRO DO CABEÇALHO
    $query_header = "INSERT INTO header_movimentos_lote (regiao,projeto,mes_mov,ano_mov,valor_mov,criado_por) VALUES (
            '{$_REQUEST['regiao']}','{$_REQUEST['projeto']}','{$_REQUEST['mes']}','{$_REQUEST['ano']}','{$valor}','{$_COOKIE['logado']}'
    )";
    $sql_header = mysql_query($query_header) or die("Erro ao cadastrar header");
    if($sql_header){
        $id_header = mysql_insert_id();
        $dados_mov = "SELECT * FROM rh_movimentos WHERE cod = '{$_REQUEST['movimento']}'";
        $sql_dados_mov = mysql_query($dados_mov) or die("Erro ao selecionar movimentos");
        $d_mov = array();
        while($rows_mov = mysql_fetch_assoc($sql_dados_mov)){
            if($rows_mov['categoria'] == "CREDITO" && $rows_mov['incidencia'] == "FOLHA"){
                $incidencia = "5020,5021,5023";
            }else{
                $incidencia = "";
            }
            $d_mov = array(
                "id_mov" => $rows_mov['id_mov'],
                "cod" => $rows_mov['cod'],
                "nome_movimento" => $rows_mov['descicao'],
                "categoria" => $rows_mov['categoria'],
                "incidencia" => $incidencia
            );
        }

        $query = "";
        if(count($_REQUEST['clts']) > 0){
            $query .= "INSERT INTO rh_movimentos_clt (id_clt,id_regiao,id_projeto,mes_mov,ano_mov,id_mov,cod_movimento,tipo_movimento, nome_movimento,data_movimento,user_cad,valor_movimento,lancamento,incidencia,status,status_folha,status_ferias,status_reg,id_header_lote) VALUES ";
            foreach ($_REQUEST['clts'] as $clt){
                $query .= "( '{$clt}','{$_REQUEST['regiao']}','{$_REQUEST['projeto']}','{$_REQUEST['mes']}','{$_REQUEST['ano']}','{$d_mov['id_mov']}','{$d_mov['cod']}','{$d_mov['categoria']}','{$d_mov['nome_movimento']}','{$data_cad}','{$_COOKIE['logado']}','{$valor}','1','{$d_mov['incidencia']}','1','1','1','1','{$id_header}'),";
            }

            $query = substr($query, 0, -1);
            $sql_query = mysql_query($query) or die("Erro ao cadastrar movimentos em lote");
        }
    }
    
    if($sql_query){
        $return = array("status" => 1);
    }
    
    echo json_encode($return);
    $log->gravaLog('Movimentos em Lote', "Novo Movimento Registrado: ID".mysql_insert_id());
    exit();
    
}

if(isset($_REQUEST['method']) && $_REQUEST['method'] == "desprocessarMovimento"){
    $return = array("status" => 0);
    $query = "DELETE FROM header_movimentos_lote WHERE id_header = '{$_REQUEST['header']}'";
    $query_linhas = "DELETE FROM rh_movimentos_clt WHERE id_header_lote = '{$_REQUEST['header']}'";
    $sql_desprocessa = mysql_query($query) or die("Erro ao remover header");
    $sql_desprocessa_linhas = mysql_query($query_linhas) or die("Erro ao remover linhas de movimentos");
    if($sql_desprocessa){
        $return = array("status" => 1);
    }
    
    echo json_encode($return);
    $log->gravaLog('Movimentos em Lote', "Movimentos em Lote desprocessados: ID{$_REQUEST['header']}");
    exit();
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == "visualizarParticipantes") {
    $return = array("status" => 0); 
    $sql = "SELECT A.id_clt, C.nome as nome_clt, B.nome, A.nome_movimento, A.valor_movimento 
        FROM `rh_movimentos_clt` AS A
        LEFT JOIN projeto AS B ON(A.id_projeto = B.id_projeto)
        LEFT JOIN rh_clt AS C ON(A.id_clt = C.id_clt)
        WHERE id_header_lote = '{$_REQUEST['header']}'";
    $visualiza_verifica = mysql_query($sql) or die("erro ao selecionar participantes");
    $dados = array();
    if($visualiza_verifica){
        while($linha = mysql_fetch_assoc($visualiza_verifica)){
            $dados[] = array("id_clt" => $linha['id_clt'], "clt" => utf8_encode($linha['nome_clt']), "projeto" => utf8_encode($linha['nome']) ,"movimento" => utf8_encode($linha['nome_movimento']),  "valor" => $linha['valor_movimento']);
        }
        $return = array("status" => 1, "dados" => $dados);
    }
    
    echo json_encode($return);
    exit();
}

if (isset($_REQUEST['filtrar'])) {
    $cont = 0;
    $arrayStatus = array(10,20,30,40,50,51,52);
    $status = implode(",", $arrayStatus);

    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    
    $cond_funcao = (isset($_REQUEST['funcao']) && !empty($_REQUEST['funcao']) && $_REQUEST['funcao'] != '-1')?" AND E.id_curso= '{$_REQUEST['funcao']}' ":"";
    
    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto'=>$id_projeto));
    $sql = "SELECT D.nome as unidade, A.nome, A.id_clt, DATE_FORMAT(A.data_entrada, '%d/%m/%Y') as dt_admissao,  E.nome as funcao, F.especifica
                            FROM rh_clt as A
                            LEFT JOIN projeto as D ON (D.id_projeto = A.id_projeto)
                            INNER JOIN curso as E ON (E.id_curso = A.id_curso)
                            LEFT JOIN rhstatus AS F ON (F.codigo = A.status)
                            WHERE A.status IN($status)
                            AND A.id_regiao = '$id_regiao' $cond_funcao";
    if(!isset($_REQUEST['todos_projetos'])) {
        $sql .= "AND A.id_projeto = '$id_projeto' ";
    }
    $sql .= "ORDER BY A.nome";
    //$sql .= " LIMIT 20 ";
    echo "<!-- {$sql} -->";
    $qr_relatorio = mysql_query($sql) or die(mysql_error());
    $num_rows = mysql_num_rows($qr_relatorio);
}

$regiaoSel = (isset($_REQUEST['reg'])) ? $_REQUEST['reg'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['pro'])) ? $_REQUEST['pro'] : null;
$funcaoSel = (isset($_REQUEST['funcao'])) ? $_REQUEST['funcao'] : null;
$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $regiaoSel;
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : $projetoSel;
$mesSel = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : null;
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : null;

?>
<html>
    <head>
        <title>:: Intranet :: Lançar Movimentos Em Lote</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/global.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine_2.6.2.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script>
            $(function() {
                
                $("#form").validationEngine();
                
                var id_destination = "projeto";
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, function(data){
                    removeLoading();
                    $("#" + id_destination).html(data);
                    var selected = $("input[name=hide_" + id_destination + "]").val();
                    if (selected !== undefined) {
                        $("#" + id_destination).val(selected);
                    }
                    $('#projeto').trigger('change');
                }, "projeto");
                
                
                $('#projeto').ajaxGetJson("../methods.php", {method: "carregaFuncoes", default:"2"}, null, "funcao");
                
                
                $("body").on("click", "#todos", function() {
                    if ($(this).is(":checked")) {
                        $(".clts").attr("checked", true);
                    } else {
                        $(".clts").attr("checked", false);
                    }
                });
                
                $(".desprocessar").click(function(){
                    var header = $(this).data("key");
                    thickBoxConfirm("Desprocessar Movimentos", "Deseja realmente desprocessar?", 500, 350, function(data){
                        if(data == true){
                            $.ajax({
                               type:"post",
                               dataType:"json",
                               data:{
                                   method:"desprocessarMovimento",
                                   header:header
                               },
                               success:function(data){
                                   $(".tr_" + header).remove();
                                   $("#lista_funcionarios").html("");
                               }
                            });
                        }
                    });
                });
                
                
                $("body").on("click",".visualizar",function(){
                    $("#tbRelatorio").remove();
                    var id_header = $(this).data("key");
                    var projeto = $(this).data("projeto");
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        data:{
                            method:"visualizarParticipantes",
                            header: id_header,
                            projeto:projeto
                        },
                        success: function(data) {
                            var html = "";
                            if(data.status == 1){
                                html += "<table id='tbRelatorio' border='0' cellpadding='0' cellspacing='0' class='grid' width='100%' style='page-break-after:auto;'><thead><tr><th colspan='6'>PARTICIPANTES</th></tr><tr style='font-size:10px !important;'><th rowspan='2'>ID</th><th rowspan='2'>NOME</th><th rowspan='2'>PROJETO</th><th rowspan='2'>MOVIMENTO</th><th rowspan='2'>VALOR</th></tr></thead>";
                                    $.each(data.dados, function(k, v){
                                        html += "<tr class='' style='font-size:11px;'><td align='center'>" +v.id_clt+ "</td><td align='left'><label for='id_clt_"+v.id+"'>" +v.clt+ "</label></td><td align='left'>" +v.projeto+ "</td><td align='left'>" +v.movimento+ "</td><td align='right'>R$ " +v.valor+ "</td></tr>";
                                    });
                                html += "</table>";
                                $("#lista_funcionarios").html(html);
                            }
                        }
                    });
                });
                
                $("#lancar_movimento").click(function(){
                    var dados  = $("#form").serialize();
                    
                    //if invalid do nothing
                    if(!$("#form").validationEngine('validate')){
                        return false;
                    }
                    $(".carregando").show();
                    $.ajax({
                        url:"rh_movimentos_lote.php?method=lancar_movimento&" + dados,
                        type:"post",
                        dataType:"json",
                        success: function(data){
                            if(data.status){
                                window.location.assign("rh_movimentos_lote.php");
                            }
                        }
                    });
                });
                    
            });
        </script>
        <style>
            .carregando{
                width: 100%;
                height: 100%;
                position: fixed;
                top: 0px;
                left: 0px;
                background: #fff;
                opacity: 0.95;
                display: none;
            }
            .carregando img{
                width: 160px;
                box-sizing: border-box;
                text-align: center;
                margin-left: 0px;
            }
            .carregando .box-message{
                position: absolute;
                top: 150px;
                left: 43%;
                background: #F8F8F8;
                padding: 15px;
                box-sizing: border-box;
                box-shadow: 5px 5px 80px #333;
            }
            .carregando .box-message p{
                font-family: arial;
                font-size: 14px;
                color: #333;
                font-weight: bold;
                text-align: center;
            }
        </style>
    </head>
    <body class="novaintra" >        
        <div id="content">
            <div class="carregando">
                <div class="box-message">
                    <img src="../imagens/loading2.gif" />
                    <p>Aguarde...</p>
                </div>
            </div>
            <form  name="form" action="" method="post" id="form">
                <div id="head">
                    <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>Lançar Movimentos Em Lote</h2>
                    </div>
                </div>
                <br class="clear">
                <br/>
                
                <fieldset class="noprint">
                    <legend>Relatório</legend>
                    <div class="fleft">
                        <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
                        <input type="hidden" name="hide_funcao" id="hide_funcao" value="<?php echo $funcaoSel ?>" />
                        <p><label class="first">Região:</label> <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao')); ?> </p>                        
                        <p><label class="first">Projeto:</label> <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto')); ?> </p>
                        <p><label class="first">Função:</label> <?php echo montaSelect(array("-1" => "« Selecione o Projeto »"), $funcaoSel, array('name' => "funcao", 'id' => 'funcao')); ?> </p>
                        <?php if (isset($_POST['filtrar'])) { ?>
                            <div id="lancar_mov">
                                <p><label class="first">Mês:</label> <?php echo montaSelect($optMeses, $mesSel, array('name' => "mes", 'id' => 'mes', 'class' => "validate[required, custom[select]]")); ?> </p>
                                <p><label class="first">Ano:</label> <?php echo montaSelect($optAnos, $anoSel, array('name' => "ano", 'id' => 'ano')); ?> </p>
                                <p><label class="first" for="lancar">Selecione um Movimento:</label><?php echo montaSelect($movimentos, $movSelected, array('name' => "movimento", 'id' => 'movimento')); ?></p>
                                <p><label class="first" for="lancar">Valor do Movimento:</label><input type="text" name="valor_mov" id="valor_mov" value="" class="validate[required]" /></p>
                            </div>
                        <?php } ?>
                    </div>

                    <br class="clear"/>

                    <p class="controls" style="margin-top: 10px;">
                        <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                        <?php if (!empty($qr_relatorio) && isset($_POST['filtrar'])) { ?>
                            <input type="button" name="lancar_movimento" value="Lançar Movimentos" id="lancar_movimento"/>
                        <?php } ?>
                        <input type="submit" name="filtrar" value="Filtrar" id="filtrar"/>
                    </p>
                </fieldset>
                <?php if(count($dados_historico) > 0){ ?>
                <fieldset style="margin-top: 10px;">
                        <legend>Histórico de Movimentos</legend>
                        <table border="0" cellpadding="0" cellspacing="0" class="grid" id="tbHistrico" width='100%'>
                            <thead>
                                <tr style="text-align: left">
                                    <th>ID</th>
                                    <th>PROJETO</th>
                                    <th>MÊS</th>
                                    <th>ANO</th>
                                    <th>VALOR</th>
                                    <th>CRIADO POR</th>
                                    <th>DATA DE CRIAÇÃO</th>
                                    <th colspan="2">AÇÃO</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dados_historico as $dados){ ?>
                                    <tr class="tr_<?php echo $dados['id']; ?>">
                                        <td><?php echo $dados['id']; ?></td>
                                        <td><?php echo $dados['projeto']; ?></td>
                                        <td><?php echo $optMeses[$dados['mes']]; ?></td>
                                        <td><?php echo $dados['ano']; ?></td>
                                        <td><?php echo number_format($dados['valor'],2,',','.'); ?></td>
                                        <td><?php echo $dados['por']; ?></td>
                                        <td><?php echo $dados['em']; ?></td>
                                        <td><a href="javascript:;" data-key='<?php echo $dados['id']; ?>' data-projeto="<?php echo $dados['id_projeto']; ?>" class="visualizar" style="text-decoration: none; color: #0000CC; font-weight: bold; cursor: pointer;"><img src="../imagens/icones/icon-view-dis.gif" title="visualizar" /></a></td>
                                        <td><a href="javascript:;" data-key='<?php echo $dados['id']; ?>' class="desprocessar" style="text-decoration: none; color: #0000CC; font-weight: bold; cursor: pointer;"><img src="../imagens/icones/icon-delete.gif" title="desprocessar" /></a></td>
                                    </tr>
                                <?php } ?>
                            </tbody>  
                        </table>
                    </fieldset>    
                <?php } ?>
                
                <?php if (!empty($qr_relatorio) && isset($_POST['filtrar'])) { ?>
                <p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" value="Exportar para Excel" class="exportarExcel"></p>    
                <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" class="grid" width="100%" style="page-break-after:auto;"> 
                        <thead>
                            <tr>
                                <th colspan="5"><?php echo $projeto['nome'] ?></th>
                            </tr>
                            <tr style="text-align: left">
                                <th><input type="checkbox" name="todos" id="todos" /></th>
                                <th>NOME</th>
                                <th>FUNÇÃO</th>
                                <th>STATUS</th>
                                <th>DATA DE ADMISSÃO</th>   
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) { $class = ($cont++ % 2 == 0)?"even":"odd"?>
                            <tr class="<?php echo $class ?>" style="text-align: left">
                                <td><input type="checkbox" name="clts[]" class="clts clt_<?php echo $row_rel['id_clt'] ?> validate[minCheckbox[1]]" data-prompt-position='centerRight' value="<?php echo $row_rel['id_clt'] ?>"  /></td>
                                <td><?php echo $row_rel['nome'] ?></td>
                                <td> <?php echo $row_rel['funcao']; ?></td>
                                <td> <?php echo $row_rel['especifica']; ?></td>
                                <td><?php echo $row_rel['dt_admissao']; ?></td>                       
                            </tr>                                
                        <?php } ?>
                    </tbody>
                        <tfoot>
                            <tr style="text-align: right;">
                                <td colspan="4">Total:</td>
                                <td style="text-align: left;"><?php echo $num_rows?></td>
                            </tr>
                    </tfoot>
                </table>
                <?php  } ?>
            </form>
            <div id="lista_funcionarios" style='margin-top:30px !important'></div>
        </div>
    </body>
</html>