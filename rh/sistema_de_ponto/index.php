<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

include('../../conn.php');
include('../../wfunction.php');
include('classes/PontoClass.php');

//DADOS DO USUARIO
$usuario = carregaUsuario();
$usuario['id_projeto'] = $usuario['id_regiao'];
//PAGINAS
$arr_paginas = array('Lista de Ponto', 'Importar Ponto', 'Relatório de Movimentos');

$ponto = new Ponto();
$erro = $ponto->error;

//LISTA DE PONTOs
$lista = $ponto->listaPontosImportado();

//MOVIMENTOS LANÇADOS PELO PONTO
$lista_proj_finalizado = $ponto->listaMovimentoPonto();

//PROJETO
$projetos = getProjetos($_REQUEST['regiao']);
$proj_selected = isset($_REQUEST['projeto']) ? $_REQUEST['projeto'] : null;

?>
<html>
    <head>
        <title>:: Intranet :: SISTEMA DE PONTO</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link href="../../favicon.ico" rel="shortcut icon"/>
        <link href="../../favicon.ico" rel="shortcut icon" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="css/style.css" rel="stylesheet" type="text/css" />
        <link href="css/extra_css.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <script src="js/jquery-1.9.1.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        <script type="text/javascript" src="../../js/jquery.price_format.2.0.min.js"></script>
        <script type="text/javascript" src="js/jquery.form.js"></script>
        <script>
            $(function() {
                var count = 0;

                /***********************UPLOAD DE ARQUIVO**********************/
                $('input[value=Enviar]').click(function() {
                    var regiao = $("#regiao").val();
                    $('#visualizar').html('<p> Enviando...</p>');
                    $('#page_controller').ajaxForm({
                        dataType: 'json',
                        url: 'actions/enviar_arquivo.php',
                        data: {
                            regiao: regiao
                        },
                        success: function(data) {
                            if (!data.status) {
                                
                                var html = "<ul>";
                                $.each(data.erro, function(key, val) {
                                    console.log(val);
                                    html += "<li class='vermelho'>" + val + "</li>";
                                });
                                html += "</ul>";
                                $("#visualizar").html(html);
                            } else {
                                history.go(0);
                            }
                        },
                        error: function(data) {
                            console.log(data);
                        }
                    }).submit();
                });

                /*********************BUSCANDO PARTICIPANTES DO PONTO**********/
                $(".listarPonto").click(function() {
                    $(".loading").remove();
                    $(this).after("<img src='imagens/loading.gif' class='loading'/>");
                    var id = $(this).attr("data-key");
                    
                    $.ajax({
                        url: "actions/methods.php",
                        dataType: "json",
                        type: "POST",
                        data: {
                            id_ponto: id,
                            method: "listaPontos"
                        },
                        success: function(data) {
                            $(".loading").remove();
                            if (data.status) {
                                var html = "<table class='lista_pontos'>";
                                html += "<thead><tr><th colspan='8' style='text-align:center; text-transform:uppercase; font-size:14px;'>Folha de Ponto</th></tr>";
                                html += "<tr><th></th><th>PIS</th><th>Nome</th><th>Horas Trabalhadas</th><th>Hora Extra</th><th>Horas Atrasadas</th><th colspan='2' style='text-align: center' >Salário Normal</th></tr></thead><tbody>";
                                $.each(data.dados, function(key, val) {
                                    var checado = "";
                                    var zero_trabalhado = "";
                                    if (val.horas_trabalhadas == "00:00:00") {
                                        zero_trabalhado = "vermelho";
                                        count++
                                        html += "<tr class='" + zero_trabalhado + "'><td class='campo_pequeno'><input type='hidden' name='participante[]' class='participante alerta_amarelo' value='" + val.pis + "' checked='checked'  /><img src='imagens/icon_alert.png' name='alert' class='alert' title='alerta: Erro nos dados do participante'  /></td><td>" + val.pis + "</td><td>" + val.nome + "</td><td>" + val.horas_trabalhadas + "</td><td> " + val.horas_extras + " </td><td>" + val.horas_atrasos + "</td><td>" + val.salario_normal + "</td><td class='campo_pequeno'><img src='imagens/detalhes.jpg' title='detalhes' class='icon_detalhes' data-pis=' " + val.pis + "' data-nome='" + val.nome + "' /></td></tr>";
                                    } else {
                                        html += "<tr class='" + zero_trabalhado + "'><td class='campo_pequeno'><input type='hidden' name='participante[]' class='participante alerta_amarelo' value='" + val.pis + "' checked='checked'  /></td><td>" + val.pis + "</td><td>" + val.nome + "</td><td>" + val.horas_trabalhadas + "</td><td> " + val.horas_extras + " </td><td>" + val.horas_atrasos + "</td><td>" + val.salario_normal + "</td><td class='campo_pequeno'><img src='imagens/detalhes.jpg' title='detalhes' class='icon_detalhes' data-pis=' " + val.pis + "' data-nome='" + val.nome + "' /></td></tr>";
                                    }
                                });
                                html += "</tbody></table>";
                                html += "<input type='button' name='finalizar_ponto' id='finalizar_ponto' class='botao ajuste_botao' value='Finalizar' />";
                                
                                $("#lista_pontos").html(html);
                                $('table tbody tr:odd').addClass('zebraUm');
                                $('table tbody tr:even').addClass('zebraDois');

                                if (count > 0) {
                                    $("#finalizar_ponto").hide();
                                    $(".confirmacao").remove();
                                    $(".lista_pontos").after("<div class='confirmacao'><p>Você tem " + count + " participante com as Horas trabalhadas zeradas, Deseja continuar ? <input type='button' name='sim' class='sim botao_pequeno' value='Sim' /></p></div>");
                                } else {
                                    $(".confirmacao").remove();
                                }

                            } else {
                                history.go(0);
                            }
                        }
                    });
                });

                /*********************BUSCANDO PARTICIPANTES DO PONTO**********/
                $(".listarMovimentos").click(function() {
                    $(".loading").remove();
                    $(this).after("<img src='imagens/loading.gif' class='loading'/>");
                    var id = $(this).attr("data-key");
                    $.ajax({
                        url: "actions/methods.php",
                        dataType: "json",
                        type: "POST",
                        data: {
                            id_header: id,
                            method: "listaDetalhesMovimentoPonto"
                        },
                        success: function(data) {
                            $(".loading").remove();
                            if (data.status) {

                                var html = "<table class='lista_movimentos'>";
                                var clt = 0;
                                var projeto = "";
                                $.each(data.dados, function(key, val) {
                                    if (projeto != val.nome_projeto) {
                                        projeto = val.nome_projeto;
                                        html += "<thead><tr><th colspan='7'>Movimentos (" + val.nome_projeto + " - " + val.mes + "/" + val.ano + ") </th></tr></thead>";
                                        html += "<tbody>";
                                    }
                                    if (clt != val.id_clt) {
                                        clt = val.id_clt;
                                        html += "<tr class='celula_destaque'><td colspan='7'>" + val.nome_clt + " - " + val.nome_projeto + "</td>";
                                        html += "<tr class='destaque_titulo'><td style='padding-left: 148px'></td><td>ID</td><td class='padding_mov'>MOVIMENTO</td><td>TIPO</td><td>COMPETÊNCIA</td><td>DATA LANÇAMENTO</td><td>VALOR</td></tr>";
                                    }
                                    html += "<tr><td style='padding-left: 148px'></td><td>" + val.id_mov + "</td><td class='padding_mov'>" + val.nome_mov + "</td><td>" + val.tipo_mov + "</td><td>" + val.mes + "/" + val.ano + "</td><td>" + val.data_lanc + "</td><td>R$ " + val.valor_mov + "</td></tr>";
                                });
                                html += "</tbody></table>";

                                $("#resultado").html(html);
                                $('table tbody tr:odd').addClass('zebraUm');
                                $('table tbody tr:even').addClass('zebraDois');
                            } else {
                                history.go(0);
                            }
                        }
                    });
                });

                /********************MODAL DE DETALHES*************************/
                $("#lista_pontos").on("click", ".icon_detalhes", function() {
                    var pis = $(this).data("pis");
                    var nome = $(this).data("nome");
                    $.ajax({
                        url: "actions/methods.php",
                        dataType: "html",
                        type: "POST",
                        data: {
                            pis: pis,
                            nome: nome,
                            method: "detalhesPonto"
                        },
                        success: function(data) {
                            $("#detalhes").html(data).css({display: "block"});
                            thickBoxModal("Detalhes do ponto", "#detalhes", "530", "990");
                        }
                    });

                });

                /*******************NÃO****************************************/
                $(".colDir").on("click", ".sim", function() {
                    $(".confirmacao").remove();
                    $("#finalizar_ponto").show();
                });

                /************************FINALIZA******************************/
                $("#lista_pontos").on("click", "#finalizar_ponto", function() {
                    var dados = $("#page_controller").serialize();
                    $.ajax({
                        url: "actions/methods.php?method=finalizaPonto&" + dados,
                        dataType: "json",
                        type: "POST",
                        success: function(data) {
                            if (data.status) {
                                history.go(0);
                            }
                        }
                    });
                });
                
                /************************DESPROCESSAR**************************/
                $(".desprocessarPonto").click(function(){
                    var header = $(this).attr("data-key");
                    thickBoxConfirm("Desprocessar Ponto","Tem certeza que deseja desprocessar esse ponto?",400,350,function(data){
                        if(data == true){
                            $.ajax({
                                url:"actions/methods.php",
                                dataType:"json",
                                type:"POST",
                                data:{
                                    id_header:header,
                                    method:"desprocessarPonto"
                                },
                                success: function(data){
                                    if(data.status){
                                        history.go(0);
                                    }
                                }
                            });
                        }
                    });
                });
                
                /***************REMOVENDO DA LISTA DE PONTO NÃO FINALIZADOS****/
                $('.removerPontoNaoFinalizados').click(function(){
                    var header = $(this).attr("data-key");
                    thickBoxConfirm("Remover Ponto","Tem certeza que deseja remover esse ponto?",400,350,function(data){
                        if(data == true){
                            $.ajax({
                                url:"actions/methods.php",
                                dataType:"json",
                                type:"POST",
                                data:{
                                    id_header:header,
                                    method:"removerPonto"
                                },
                                success: function(data){
                                    if(data.status){
                                        history.go(0);
                                    }
                                }
                            });
                        }
                    });
                });
                
            });
        </script>
    </head>
    <body class="novaintra" data-type="adm">
        <form method="post" id="page_controller" enctype="multipart/form-data">
            <input type="hidden" name="regiao" id="regiao" value="<?php echo $_REQUEST['regiao'] ?>" />
            <input type="hidden" name="abashow" value="0" id="abashow" />
            <div id="content">
                <div id="geral">
                    <div id="topo">
                        <div class="conteudoTopo">
                            <div class="imgTopo">
                                <img src="../../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 15px; margin-left: 10px;"/>
                            </div>
                            <h2 style="text-transform: uppercase;  font-size: 16px; color: #494949;  float: left; padding-top: 65px; padding-left: 23px;">Administração de Sistema de Ponto</h2>
                        </div> 
                    </div>

                    <div id="conteudo">
                        <div class="colEsq">
                            <div class="titleEsquerda">Menu</div>
                            <ul id="nav">                                
                                <?php foreach ($arr_paginas as $key => $pagina) { ?>
                                    <li><a href="javascript:;" onclick="$('#abashow').val(<?= $key ?>)" data-item="<?= $key ?>" class="bt-menu <?= ($pagina_ativa == $key) ? ' aselected ' : ''; ?>"><?= $pagina; ?></a></li>
                                <?php } ?>
                            </ul>
                        </div>
                        <div class="colDir ajuste_col_dir" id="teste1">
                            <div>processando os dados...</div>
                            <div style="background: url(../../imagens/carregando/loading.gif) no-repeat; width: 220px; height:19px;"></div>
                                <?php foreach ($arr_paginas as $key => $value) { ?>
                                    <div id="item<?= $key ?>" style="display: none;" >
                                    <?php
                                        $file = 'includes/item_' . $key . '.php';
                                        if (is_file($file)) {
                                            include_once $file;
                                        } else {
                                            echo 'Erro 404. Página não encontrada!';
                                        }
                                    ?>
                                </div>
                                <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div id="detalhes"></div>
        </form>
    </body>
</html>