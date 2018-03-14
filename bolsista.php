<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
} else {

    include('conn.php');
    include('funcoes.php');
    include_once("classes/EventoClass.php");
    include('classes_permissoes/acoes.class.php');
    $permissao = new Acoes();
    $permissao = $permissao->getAcoes($_COOKIE['logado'], $_REQUEST['regiao']);

    $projeto = $_REQUEST['projeto'];
    $regiao = $_REQUEST['regiao'];


//OBJ EVENTO
    $data = date("Y-m-d");
    $eventos = new Eventos();
    $dadosEventos = $eventos->getTerminandoEventos($data, $regiao, $projeto);
    $dadosArrayEventos = $eventos->array_dados;
    $status = $eventos->getStatus();

// Bloqueio Administração
//echo bloqueio_administracao($regiao);
// LISTA E CONTA A QUANTIDADE DE AUTONOMOS INATIVOS DO PROJETO
    $result_inativos = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y') AS data_entrada2, date_format(data_saida, '%d/%m/%Y') AS data_saida2 FROM autonomo WHERE status = '0' and tipo_contratacao = '1' and id_projeto = '$projeto' ORDER BY nome ASC");
    $contado_inativos = mysql_num_rows($result_inativos);

// LISTA E CONTA A QUANTIDADE DE COOPERADOS INATIVOS DO PROJETO
    $result_coop_ina = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y') AS data_entrada2, date_format(data_saida, '%d/%m/%Y') AS data_saida2 FROM autonomo WHERE status = '0' and tipo_contratacao = '3' and id_projeto = '$projeto' ORDER BY nome ASC");
    $contado_coop_ina = mysql_num_rows($result_coop_ina);

// LISTA E CONTA A QUANTIDADE DE CLTS INATIVOS DO PROJETO 
    $result_clt_ina = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y') AS data_entrada2, date_format(data_saida, '%d/%m/%Y') AS data_saida2 FROM rh_clt WHERE status >= '60' and id_projeto = '$projeto' ORDER BY nome ASC");
    $contado_clt_ina = mysql_num_rows($result_clt_ina);

// LISTA E CONTA A QUANTIDADE DE PJ INATIVOS DO PROJETO
    $result_pj_ina = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y') AS data_entrada2, date_format(data_saida, '%d/%m/%Y') AS data_saida2 FROM autonomo WHERE status = '0' and tipo_contratacao = '4' and id_projeto = '$projeto' ORDER BY nome ASC");
    $contado_pj_ina = mysql_num_rows($result_pj_ina);

// TOTAL DE AUTONOMOS INATIVOS DO PROJETO
    $result_total_inativos = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y') AS data_entrada2, date_format(data_saida, '%d/%m/%Y') AS data_saida2 FROM autonomo WHERE status = '0' and id_projeto = '$projeto' ORDER BY nome ASC");
//echo "SELECT *, date_format(data_entrada, '%d/%m/%Y') AS data_entrada2, date_format(data_saida, '%d/%m/%Y') AS data_saida2 FROM autonomo WHERE status = '0' and id_projeto = '$projeto' ORDER BY nome ASC";
    $row_total_inativos = mysql_num_rows($result_total_inativos);

    $total_inativos = $contado_inativos + $contado_clt_ina + $contado_coop_ina + $contado_pj_ina;

// LISTA E CONTA A QUANTIDADE DE AUTONOMOS ATIVOS DO PROJETO
    $result_ativos = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y') AS data_entrada2, date_format(data_saida, '%d/%m/%Y') AS data_saida2 FROM autonomo WHERE status = '1' and tipo_contratacao = '1' and id_projeto = '$projeto' ORDER BY nome ASC");
    $contado_ativos = mysql_num_rows($result_ativos);

// LISTA E CONTA A QUANTIDADE DE COOPERADOS ATIVOS DO PROJETO
    $result_coop_ati = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y') AS data_entrada2, date_format(data_saida, '%d/%m/%Y') AS data_saida2 FROM autonomo WHERE status = '1' and tipo_contratacao = '3' and id_projeto = '$projeto' ORDER BY nome ASC");
    $contado_coop_ati = mysql_num_rows($result_coop_ati);

// LISTA E CONTA A QUANTIDADE DE PJ ATIVOS DO PROJETO
    $result_pj_ati = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y') AS data_entrada2, date_format(data_saida, '%d/%m/%Y') AS data_saida2 FROM autonomo WHERE status = '1' and tipo_contratacao = '4' and id_projeto = '$projeto' ORDER BY nome ASC");
    $contado_pj_ati = mysql_num_rows($result_pj_ati);

// LISTA E CONTA A QUANTIDADE DE CLTS ATIVOS DO PROJETO 
    $result_clt_ati = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y') AS data_entrada2, date_format(data_saida, '%d/%m/%Y') AS data_saida2 FROM rh_clt WHERE status < '60' AND status != 0 AND id_projeto = '$projeto' ORDER BY nome ASC");
    $contado_clt_ati = mysql_num_rows($result_clt_ati);


    $total_ativos = $contado_ativos + $contado_clt_ati + $contado_coop_ati + $contado_pj_ati;

//SELECIONANDO O ULTIMO PARTICIPANTE CADASTRADO
    $result_ultimo = mysql_query("SELECT * FROM autonomo WHERE id_bolsista=(SELECT MAX(id_bolsista) FROM autonomo WHERE id_projeto = '$projeto')");
    $row_ultimo = mysql_fetch_array($result_ultimo);

    $result3 = mysql_query("SELECT * FROM autonomo WHERE id_projeto = '$projeto' ORDER BY id_bolsista");
    $mostra1 = mysql_fetch_array($result3);

// PEGANDO O MAIOR NUMERO AUTONOMO
    $result_maior = mysql_query("SELECT CAST(campo3 AS UNSIGNED) campo3, nome, MAX(campo3) FROM autonomo WHERE id_regiao = '$regiao' AND id_projeto = '$projeto' AND campo3 != 'INSERIR' GROUP BY campo3 DESC LIMIT 0,1");
    $row_maior = mysql_fetch_array($result_maior);

// PEGANDO O MAIOR NUMERO CLT
    $result_maior_clt = mysql_query("SELECT CAST(campo3 AS UNSIGNED) campo3, nome, MAX(campo3) FROM rh_clt WHERE id_regiao = '$regiao' AND id_projeto = '$projeto' AND campo3 != 'INSERIR' GROUP BY campo3 DESC LIMIT 0,1");
    $row_maior_clt = mysql_fetch_array($result_maior_clt);

// FUNÇÃO NOME
    function abreviacao($nome) {

        list($primeiro_nome, $segundo_nome, $terceiro_nome, $quarto_nome, $quinto_nome) = explode(' ', $nome);

        if ($quarto_nome == "DAS" or $quarto_nome == "DA" or $quarto_nome == "DE" or $quarto_nome == "DOS" or $quarto_nome == "DO" or $quarto_nome == "E") {
            $nome_abreviado = "$primeiro_nome $segundo_nome $terceiro_nome $quarto_nome $quinto_nome";
        } else {
            $nome_abreviado = "$primeiro_nome $segundo_nome $terceiro_nome $quarto_nome";
        }

        return $nome_abreviado;
    }

    /*
     * Recuperar advertencias
     */
    $query_adv = "SELECT a.*,b.nome,b.id_projeto,b.id_regiao,
                    count(a.id_doc) AS num_adv 
                    FROM rh_doc_status AS a 
                    INNER JOIN rh_clt AS b ON (a.id_clt = b.id_clt)
                    WHERE b.id_projeto = '$projeto'
                    AND a.tipo in(10) 
                    AND a.status_reg = 1
                    AND b.status < 60
                    GROUP BY a.id_clt";
    $return_adv = mysql_query($query_adv);
    $num_row_adv = mysql_num_rows($return_adv);
    while ($rowadv = mysql_fetch_array($return_adv)) {
        $dadosadv[] = $rowadv;
    }

    $query_susp = "SELECT a.*,b.nome,b.id_projeto,b.id_regiao,
                    count(a.id_doc) AS num_susp 
                    FROM rh_doc_status AS a 
                    INNER JOIN rh_clt AS b ON (a.id_clt = b.id_clt)
                    WHERE b.id_projeto = '$projeto'
                    AND a.tipo in(9) 
                    AND a.status_reg = 1
                    AND b.status < 60
                    GROUP BY a.id_clt";
    $return_susp = mysql_query($query_susp);
    $num_row_susp = mysql_num_rows($return_susp);
    while ($rowsusp = mysql_fetch_array($return_susp)) {
        $dadossusp[] = $rowsusp;
    }
    ?>
    <html>
        <head>
            <title>:: Intranet ::</title>
            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
            <link href="net1.css" rel="stylesheet" type="text/css" />
            <link href="rh/css/estrutura_projeto.css" rel="stylesheet" type="text/css">
            <link href="css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css">
            <link href="css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
            <link rel="shortcut icon" href="favicon.ico">
            <script src="js/jquery-1.8.3.min.js" type="text/javascript"></script>
            <script src="js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
            <script src="js/global.js" type="text/javascript"></script>
            <script src="js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
            <script src="js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
            <script src="http://malsup.github.com/jquery.form.js" type="text/javascript"></script>
            <script>
                $(function() {

                    $("#data_prorrogada").datepicker();

                    $(".prorrogar").click(function() {
                        //RECUPERA ID_EVENTO
                        var eventos = $(this).attr("data-key");
                        $("#id_evento").val(eventos);
                        //RECUPERA DATA_RETORNO
                        var data_retorno = $(this).attr("data-retorno");
                        $("#data_retorno").val(data_retorno);

                        $("#data_prorrogada").val('');
                        $("#dias").val('');

                        $("#modal_motivo").show();
                        thickBoxModal("Motivo de prorrogação", "#modal_motivo", 300, 450);

                    });

                    $('#calc-data').click(function() {
                        var id = $("#id_evento").val();
                        var dias = $("#dias").val();
                        $.post('methods.php', {id: id, calcData: true, qtdDias: dias}, function(data) {
                            if (data != 0) {
                                $("#data_prorrogada").val(data.data);
                            } else {
                                alert('Falha ao carregar evento!');
                                exit();
                            }
                        }, 'json');
                    });

                    $("#dias").change(function() {
                        if ($(this).val() < 0) {
                            $(this).val(0);
                        }
                    });


                    $("#finalizar").click(function() {
                        var id_evento = $("#id_evento").val();
                        var id_user = $("#id_user").val();
                        var data_retorno = $("#data_retorno").val();
                        var mensagem = $("textarea[name='motivo']").val();
                        var data_prorrogada = $("#data_prorrogada").val();
                        $.ajax({
                            url: "methods.php",
                            type: "POST",
                            dataType: "json",
                            data: {
                                id_evento: id_evento,
                                id_user: id_user,
                                data_retorno: data_retorno,
                                mensagem: mensagem,
                                data_prorrogada: data_prorrogada,
                                method: "prorroga_evento"
                            },
                            success: function(data) {
                                if (data.status) {
                                    thickBoxClose("#modal_motivo");
                                    history.go(0);
                                } else {
                                    var html = "";
                                    $.each(data.erro, function(key, value) {
                                        html += "<p>" + value + "</p><br />";
                                    });
                                    $("#message_erro").html(html);
                                }
                            }
                        });

                    });

                    $(".voltar").click(function() {
                        var eventos = $(this).attr("data-key");
                        $("#id_evento").val(eventos);
                        $.ajax({
                            url: "methods.php",
                            type: "POST",
                            dataType: "json",
                            data: {
                                method: "cadEvento",
                                id_evento: eventos
                            },
                            success: function(data) {
                                if (data.status) {
                                    history.go(0);
                                }
                            }
                        });
                    });

                    $(".acao_ocultar").hover(function() {
                        var $class = $(this).attr("data-remove");
                        $(this).animate({marginRight: "70px"}).addClass("acao_ativada");
                        $(this).html("<p class='acao_ativada' data-remove='" + $class + "' style='margin-left: 15px; font-weight: bold; color: chocolate; font-size: 15px; margin-top: -5px;'>Ocultar</p>");
                    });

                    $("body").on("click", ".acao_ativada", function() {
                        var $class = "." + $(this).attr("data-remove");
                        $($class).remove();
                    });

                    //SCRIPT PARA UPLOAD DE CURRICULO
                    $("#curriculoForm").validationEngine({promptPosition : "topLeft"});
                    $('#curriculoForm').ajaxForm({
                        clearForm: true,
                        dataType:  'json',
                        type:      'post',
                        uploadProgress: function(event, position, total, percentComplete) {
                            if(percentComplete > 99){
                                percentComplete = 99;
                            }
                            $('progress').attr('value',percentComplete);
                            $('#porcentagem').html(percentComplete+'% aguarde..');
                        },
                        success: function(data) {
                            $('progress').attr('value','100');
                            $('#porcentagem').html('100%');
                            console.log(data);
                            if(data.status==1){
                                $(".ui-icon-closethick").trigger('click');
                                $('.actived').attr('src','imagens/assinado.gif').attr('alt','Visualizar').addClass('bt-ver-curriculo').removeClass('bt-upload-curriculo').removeClass('actived');
                            }else{
                                $("#message_erro_curriculo").html('Erro ao processar: '+data.msg);
                            }
                        }
                    });
                    
                    $("body").on('click','.bt-upload-curriculo',function(){
                        $("#modal_curriculo").show();
                        $('progress').attr('value','0');
                        $('#porcentagem').html('0%');
                        $("#message_erro_curriculo").html('');
                        var botao = $(this);
                        var tr = botao.parents("tr");
                        var nome = tr.find(".participante").html();
                        $("#nome_up_curriculo").html(nome);
                        $("#id_fun").val(botao.data('id'));
                        $("#tipo_contrato").val(botao.data('type'));
                        $('.actived').removeClass('actived');
                        botao.addClass('actived');
                        thickBoxModal("Enviar Currículo", "#modal_curriculo", 280, 450);
                    });
                    
                    $("body").on('click','.bt-ver-curriculo',function(){
                        var botao = $(this);
                        var tr = botao.parents("tr");
                        var nome = tr.find(".participante").html();
                        $('.actived').removeClass('actived');
                        botao.addClass('actived');
                        //var html = "";
                        $.post('rh/curriculo/visualiza.php',{id: botao.data('id'), tipo:botao.data('type')},function(data){
                            if(data.status == 1){
                                $("#imgVerCurri").attr('src',"imagens/icons/att-"+data.type+".png");
                                $("#aVerCurri").attr('href','rh/curriculo/'+data.doc);
                                thickBoxModal("Ver Currículo: "+nome, "#modal_curriculo_v", 320, 360);
                                $("#deletaCurriculo").attr('data-type',botao.data('type')).attr('data-id',botao.data('id')).attr('data-doc',data.doc);
                            }
                        }, "json");
                    });
                    
                    $("#deletaCurriculo").click(function(){
                        var ok = confirm("Atenção, essa ação é irreversível. Deseja realmente excluir este currículo?");
                        if(ok){
                            var botao = $(this);
                            $.post('rh/curriculo/upload_curriculo.php',{method:'deletaCurriculo', id_fun: botao.data('id'), tipo_contrato:botao.data('type'), doc:botao.data('doc')},function(data){
                                if(data.status == 1){
                                    $(".ui-icon-closethick").trigger('click');
                                    $('.actived').attr('src','imagens/naoassinado.gif').attr('alt','Enviar').addClass('bt-upload-curriculo').removeClass('bt-ver-curriculo').removeClass('actived');
                                }
                            }, "json");
                        }
                    });
                });
            </script>

            <style type="text/css">
                .avisos_eventos,.aviso_advertencia{
                    border-bottom: 1px solid #ccc;
                    padding: 25px;
                    box-sizing: border-box;
                    background: #FFE2E2;
                    position: relative;
                    display: table;
                    width: 100%;
                }
                .avisos_eventos h2{
                    color: #930;
                    margin: 10px 0px;
                }
                .avisos_eventos li{
                    font-family: arial;
                    font-size: 12px;
                    line-height: 27px;
                    margin-left: 15px;
                }
                .false{
                    color:#D90000;
                }
                .true {
                    color:#339933;
                }
                .botao_eventos{
                    border: 1px solid #ccc;
                    padding: 5px 8px;
                    background: #f5f5f5;
                    color: #333;
                    margin: 2px 4px;
                    cursor: pointer;
                    float: right;
                }
                #modal_motivo{
                    display: none;
                }
                .lista_eventos{
                    position: relative;
                    width: 100%;

                }
                .col_esq{
                    width: 62%;
                    float: left;
                    border-bottom: 1px solid #F7CCCC;
                    height: 32px;
                }
                .col_dir{
                    width: 38%;
                    float: left;
                    border-bottom: 1px solid #F7CCCC;
                    height: 32px;
                }
                .acao_ocultar{
                    border: 10px solid transparent;
                    width: 0px;
                    height: 0px;
                    display: block;
                    position: absolute;
                    right: 29px;
                    border-bottom-color: #E53939;
                    cursor: pointer;
                }            
                .titulo_categoria{
                    display: block;
                    border-bottom: 9px solid #F1AAAA;
                    padding: 4px 10px;
                    color: #333;
                    text-transform: uppercase;
                    font-size: 13px;
                }
                .titulo_evento{
                    font-weight: bold;
                    font-size: 11px;
                    padding: 2px;
                    padding-left: 31px;
                    color: #682;
                    text-transform: uppercase;
                    width: 100%;
                    box-sizing: border-box;
                    padding-top: 3px;
                }
                .funcionario_evento{
                    padding-left: 55px;
                    line-height: 28px;
                }
                .funcionario_evento span{
                    float: right;
                    padding-right: 20px;
                    font-weight: bold;
                    font-style: italic;
                    color: red;
                    font-size: 13px;
                }
                .aviso_advertencia .titulo_categoria{
                    border-bottom-color: #ffff66;
                }
                .aviso_advertencia{
                    background-color: #ffffcc;
                }
                .aviso_advertencia h2{
                    color: #999900;
                    margin: 10px 0px;
                }
                #modal_curriculo, #modal_curriculo_v{
                    display: none;
                }
                .bt-upload-curriculo, .bt-ver-curriculo{
                    cursor: pointer;
                }
                progress{
                    width: 200px;
                    height: 17px;
                }
                #message_erro_curriculo{
                    color: red;
                    font-size: 12px;
                    font-weight: bold;
                    margin-top: 5px;
                }
                #imgVerCurri{
                    width: 200px;
                    margin-top: 18px;
                    cursor:pointer;
                }
            </style>
        </head>
        <body>
            <form name="eventos" id="eventos" action="">
                <input type="hidden" name="id_evento" id="id_evento" value="" />
                <input type="hidden" name="id_user" id="id_user" value="<?php echo $_COOKIE['logado'] ?>" />
                <input type="hidden" name="data_retorno" id="data_retorno" value="" />
                <div id="modal_motivo">
                    <p style="text-align: left;">
                        <label for="dias" style=" font-weight: bold;" >Quantidade de dias (a partir da data atual de retorno):</label><br />
                        <input type="number" name="dias"  min="0"  id="dias" style="width: 4em; height: 30px; margin: 3px 0px;" > <button type="button" id="calc-data">Calcular Prorrogação</button>
                    </p>

                    <p>
                        <label for="data_prorrogada" style="float: left; font-weight: bold;" >Data de Prorrogação</label><br />
                        <input style="width: 425px; height: 30px; margin: 3px 0px;" type="text" name="data_prorrogada" id="data_prorrogada" value="" />
                    </p>

                    <p>
                        <label for="motivo" style="float: left; font-weight: bold;">Digite o motivo:</label><br />
                        <textarea name="motivo" style="width: 425px; height: 80px; margin: 3px 0px;"></textarea>
                    </p>
                    <input type="submit" name="finalizar" id="finalizar" style="float: right" value="Finalizar" />
                    <div id="message_erro"></div>
                </div>
            </form>
            <div id="modal_curriculo">
                <form name="curriculoForm" id="curriculoForm" action="rh/curriculo/upload_curriculo.php">
                    <input type="hidden" name="id_fun" id="id_fun" value="" />
                    <input type="hidden" name="tipo_contrato" id="tipo_contrato" value="" />
                    <p style="text-align: left;">
                        <label for="data_termino" style=" font-weight: bold;" >Nome:</label><br />
                        <span id="nome_up_curriculo">...</span>
                    </p>
                    <br/><br/>
                    <p>
                        <label for="file_curriculo" style="float: left; font-weight: bold;" >Currículo</label><br />
                        <input style="width: 425px; height: 30px; margin: 3px 0px;" type="file" name="file_curriculo" id="file_curriculo" class="validate[required,custom[docsType]]" />
                    </p>
                    <br/>
                    <p>Só serão aceitos currículos com extensão: png, jpg, pdf, doc e docx.</p>
                    <br/>
                    <progress value="0" max="100"></progress><span id="porcentagem">0%</span>
                    <input type="submit" name="up_curriculo" id="up_curriculo" style="float: right" value="Enviar" />
                    <div id="message_erro_curriculo"></div>
                </form>
            </div>
            <div id="modal_curriculo_v">
                <p><a href="" target="_blanck" id="aVerCurri"><img id="imgVerCurri" src='imagens/icons/att-jpg.png'/></a></p>
                <br/><br/>
                <p><input type="button" name="deletaCurriculo" id="deletaCurriculo" style="float: right" value="Deletar Currículo" /></p>
            </div>
            <div id="corpo" style="width:95%;">
                <?php if (count($dadosEventos) > 0 && in_array(88, $permissao)) { ?>

                    <div class="avisos_eventos">
                        <div class="acao_ocultar" data-remove="avisos_eventos"></div>  
                        <h2>LISTA DE FUNCIONÁRIOS PARA RETORNO DE EVENTOS</h2>
                        <?php
                        foreach ($dadosArrayEventos as $key => $dados) {
                            if ($key == 0) {
                                ?>
                                <h3 class="titulo_categoria">» Fora do prazo</h3>
                                <?php foreach ($dados as $key => $valores) { ?>
                                    <p class="titulo_evento"><?php echo $status[$key] . " (" . count($valores) . ")"; ?></p>  
                                    <?php for ($i = 0; $i < count($valores); $i++) { ?>
                                        <div class="col_esq">
                                            <p class="funcionario_evento"><?php echo $valores[$i]["data_retorno"] . " - " . $valores[$i]["nome_clt"]; ?></p>
                                        </div>
                                        <div class="col_dir">
                                            <a href='javascript:;' class='botao_eventos voltar' data-key="<?php echo $valores[$i]["id_evento"]; ?>" data-retorno="<?php echo $valores[$i]["data_retorno"]; ?>">Voltar para Atividade Normal</a><a href='javascript:;' class='botao_eventos prorrogar' data-key="<?php echo $valores[$i]["id_evento"]; ?>" data-retorno="<?php echo $valores[$i]["data_retorno"]; ?>">Prorrogar Evento</a>    
                                        </div>
                                    <?php } ?>
                                <?php } ?>

                            <?php } else if ($key == 1) { ?>

                                <h3 class="titulo_categoria">» Terminam o evento HOJE!</h3>
                                <?php foreach ($dados as $key => $valores) { ?>
                                    <p class="titulo_evento"><?php echo $status[$key] . " (" . count($valores) . ")"; ?></p>  
                                    <?php for ($i = 0; $i < count($valores); $i++) { ?>
                                        <div class="col_esq">
                                            <p class="funcionario_evento"><?php echo $valores[$i]["data_retorno"] . " - " . $valores[$i]["nome_clt"]; ?></p>
                                        </div>
                                        <div class="col_dir">
                                            <a href='javascript:;' class='botao_eventos voltar' data-key="<?php echo $valores[$i]["id_evento"]; ?>" data-retorno="<?php echo $valores[$i]["data_retorno"]; ?>">Voltar para Atividade Normal</a><a href='javascript:;' class='botao_eventos prorrogar' data-key="<?php echo $valores[$i]["id_evento"]; ?>" data-retorno="<?php echo $valores[$i]["data_retorno"]; ?>">Prorrogar Evento</a>    
                                        </div> 
                                    <?php } ?>
                                <?php } ?>

                            <?php } else { ?>

                                <h3 class="titulo_categoria">» No prazo</h3>
                                <?php foreach ($dados as $key => $valores) { ?>
                                    <p class="titulo_evento"><?php echo $status[$key] . " (" . count($valores) . ")"; ?></p>  
                                    <?php for ($i = 0; $i < count($valores); $i++) { ?>
                                        <?php
                                        $data_retorno = date("Y-m-d", strtotime(str_replace("/", "-", $valores[$i]["data_retorno"])));
                                        $dias = $eventos->nova_qnt_dias(date("Y-m-d"), $data_retorno);
                                        ?>
                                        <div class="col_esq">
                                            <p class="funcionario_evento"><?php echo $valores[$i]["data_retorno"] . " - " . $valores[$i]["nome_clt"]; ?></p>
                                        </div>   
                                        <div class="col_dir">
                                            <p class="funcionario_evento"><span>restando <b><?php echo $dias; ?></b> dias para o evento. </span></p>
                                        </div>

                                    <?php } ?>
                                <?php } ?>

                                <?php
                            }
                        }
                        ?>
                    </div>
                <?php }
                if (count($dadosEventos) > 0 && in_array(91, $permissao)) {?>
                <div class='aviso_advertencia'>
                    <div class="acao_ocultar" data-remove="aviso_advertencia"></div>
                    <h2>FUNCIONÁRIOS COM ADVERTÊNCA E SUSPENÇÃO</h2>
                    <?php
                    if ($num_row_adv) {
                        ?>
                    <h3 class="titulo_categoria">Advertências</h3>
                        <?php
                        foreach ($dadosadv as $medDis) {
                            if ($medDis['num_adv'] > 0) {
                                ?>
                                <div class="col_esq">
                                    <?php
                                    echo "Funcionário {$medDis['nome']} com <strong>{$medDis['num_adv']} advertência(s)</strong>!";
                                    ?>
                                </div>
                                <div class="col_dir">
                                    <?php if ($medDis['num_adv'] >= 2) { ?>
                                        <a href="rh/notifica/advertencia.php?clt=<?= $medDis['id_clt'] ?>&pro=<?= $medDis['id_projeto'] ?>&id_reg=<?= $medDis['id_regiao'] ?>" class="botao_eventos">Suspender</a>
                                    <?php } ?>
                                </div>
                                <?php
                            }
                        }
                    }
                    ?>
                    <?php
                    if ($num_row_susp) {
                        ?>
                    <h3 class="titulo_categoria">Suspenções</h3>
                        <?php
                        foreach ($dadossusp as $medDis) {
                            if ($medDis['num_susp'] > 0) {
                                ?>
                                <div class="col_esq">
                                    <?php
                                    echo "Funcionário {$medDis['nome']} com <strong>{$medDis['num_susp']} suspemsão(ões)</strong>!";
                                    ?>
                                </div>
                                <div class="col_dir">
                                    <!--<a href="rh/notifica/advertencia.php?clt=<?= $medDis['id_clt'] ?>&pro=<?= $medDis['id_projeto'] ?>&id_reg=<?= $medDis['id_regiao'] ?>" class="botao_eventos">Demitir</a>-->
                                </div>
                                <?php
                            }
                        }
                    }
                    ?>
                </div>
                <?php } ?>
            </div>

            <div id="conteudo">
                <table align="center" width="100%" cellspacing="0" cellpadding="12" style="font-size:13px; line-height:22px;">
                    <tr>
                        <td align="right"><?php include('reportar_erro.php'); ?></td>
                    </tr>
                    <tr>
                        <td>
                            <div style="border-bottom:2px solid #F3F3F3; margin-top:10px;">
                                <h2 style="float:left; font-size:18px;">TODOS OS PARTICIPANTES</h2>
                                <p style="float:right;"><a href="ver.php?projeto=<?= $projeto ?>&regiao=<?= $regiao ?>">&laquo; Voltar</a></p>
                                <div class="clear"></div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>

                            <?php /*
                              <table width='100%' style="font-family:Arial, Helvetica, sans-serif; text-align:center; font-weight:bold; font-size:11px; line-height:20px;">
                              <tr>
                              <td height="18" colspan="6" style="background-color:#666; color:#FFF;">
                              TOTALIZADORES DO PROJETO - TOTAL DE CADASTROS
                              </td>
                              </tr>
                              <tr style="background-color:#CFC; color:#030;">
                              <td width="16%" rowspan="2">CADASTRADOS ATIVOS</td>
                              <td width="27%">TOTAL DE CADASTROS</td>
                              <td width="16%">AUT&Ocirc;NOMOS</td>
                              <td width="11%">CLT</td>
                              <td width="12%">COOPERADOS</td>
                              <td width="16%">AUTÔNOMOS / PJ</td>
                              </tr>
                              <tr style="background-color:#EEE; color:#000;">
                              <td><?=$total_ativos?></td>
                              <td><?=$contado_ativos?></td>
                              <td><?=$contado_clt_ati?></td>
                              <td><?=$contado_coop_ati?></td>
                              <td><?=$contado_pj_ati?></td>
                              </tr>
                              <tr style="background-color:#FCC; color:#030;">
                              <td rowspan="2">CADASTROS INATIVOS</td>
                              <td>TOTAL DE CADASTROS</td>
                              <td>AUT&Ocirc;NOMOS</td>
                              <td>CLT</td>
                              <td>COOPERADOS</td>
                              <td>AUTÔNOMOS / PJ</td>
                              </tr>
                              <tr style="background-color:#EEE; color:#000;">
                              <td><?=$total_inativos?></td>
                              <td><?=$contado_inativos?></td>
                              <td><?=$contado_clt_ina?></td>
                              <td><?=$contado_coop_ina?></td>
                              <td><?=$contado_pj_ina?></td>
                              </tr>
                              <tr>
                              <td colspan="6" style="background-color:#666; color:#FFF;">
                              ULTIMO AUTONOMO OU COOPERADO CADASTRADO: <span style="font-weight:normal;"><?=$row_maior['campo3']." / ".$row_maior['nome']?></span>
                              </td>
                              </tr>
                              <tr>
                              <td colspan="6" style="background-color:#666; color:#FFF;">
                              ULTIMO CLT CADASTRADO: <span style="font-weight:normal;"><?=$row_maior_clt['campo3']." / ".$row_maior_clt['nome']?></span>
                              </td>
                              </tr>
                              </table>
                             */ ?>

                            <table cellpadding="3" cellspacing="3" style="font-size:11px; width:30%; background-color:#FAFAFA; margin-top:10px;">
                                <tr>
                                    <td width="8%"><div style="background-color:#339933; text-align:center;">ok</div></td>
                                    <td width="92%">Regularizado com foto</td>
                                </tr>
                                <tr>
                                    <td><div style="background-color:#FFFFCC; text-align:center;">ok</div></td>
                                    <td>Regularizado</td>
                                </tr>
                                <tr>
                                    <td><div id="divquadrado" style="background-color:#FB797C; text-align:center;">!</div></td>
                                    <td>Com Observa&ccedil;&atilde;o / Sem C&oacute;digo / Sem Unidade</td>
                                </tr>
                            </table>

                            <?php if ($_GET['sucesso'] == "edicao") { ?>
                                <div style="background-color:#696; border:1px solid #033; color:#FFF; padding:4px; font-size:13px; margin-top:10px;">
                                    Participante atualizado com sucesso!
                                </div>
                                <?php
                            }

                            echo '<br>
                            <fieldset>
                            <legend>Busca:</legend>
                            <form name="formPesquisa">
                                <input type="hidden" name="regiao" value="' . $_REQUEST['regiao'] . '">
                                <input type="hidden" name="projeto" value="' . $_REQUEST['projeto'] . '">
                                <input type="text" name="pesquisa" placeholder="Nome, Matricula, CPF" value="' . $_REQUEST['pesquisa'] . '">
                                <input type="submit" value="Pesquisar">
                            </form>
                            </fieldset>';
                            if (isset($_REQUEST['pesquisa']) AND ! empty($_REQUEST['pesquisa'])) {
                                $valorPesquisa = explode(' ', $_REQUEST['pesquisa']);
                                foreach ($valorPesquisa as $valuePesquisa) {
                                    $pesquisa[] .= "nome LIKE '%" . $valuePesquisa . "%'";
                                }
                                $pesquisa = implode(' AND ', $pesquisa);
                                $auxPesquisa = " AND (($pesquisa) OR (CAST(matricula AS CHAR) = '{$_REQUEST['pesquisa']}') OR (REPLACE(REPLACE(cpf, '.', ''), '-', '') = '{$_REQUEST['pesquisa']}' OR cpf = '{$_REQUEST['pesquisa']}'))";

                                $sqlPesquisaUnidade = mysql_query("
                                SELECT id_unidade FROM autonomo WHERE status = '1' AND id_projeto = '$projeto' $auxPesquisa
                                UNION
                                SELECT id_unidade FROM rh_clt WHERE id_projeto = '$projeto' AND (status < '60' OR status = '200') AND status != 0 $auxPesquisa");
                                while ($rowPesquisaUnidade = mysql_fetch_assoc($sqlPesquisaUnidade)) {
                                    if ($rowPesquisaUnidade['id_unidade'] == '') {
                                        $pesquisaUnidade[-1] = "''";
                                    } else {
                                        $pesquisaUnidade[$rowPesquisaUnidade['id_unidade']] = $rowPesquisaUnidade['id_unidade'];
                                    }
                                }
                                $auxPesquisaUnidade = " AND id_unidade IN(" . implode(',', $pesquisaUnidade) . ")";
                            }


                            $result_unidades = mysql_query("SELECT * FROM unidade WHERE campo1 = '$projeto' $auxPesquisaUnidade AND status_reg=1 ORDER BY unidade ASC");

                            while ($row_unidades = mysql_fetch_array($result_unidades)) {
                                ?>

                                <p id="excel" style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio<?=$row_unidades['0']?>', 'Relatório')" value="Exportar para Excel" class="exportarExcel"></p>
                                <table id="tbRelatorio<?=$row_unidades['0']?>" cellpadding="8" cellspacing="0" style="border:0px; background-color:#f5f5f5; margin:20px auto; width:100%;">
                                    <tr>
                                        <td colspan="10" class="show">
                                            &nbsp;<span style='color:#F90; font-size:32px;'>&#8250;</span> 
                                            <?= $row_unidades['0'] . " - " . $row_unidades['unidade'] ?>
                                        </td>
                                    </tr>
                                    <tr class="novo_tr">
                                        <td width="3%">&nbsp;</td>
                                        <td width="3%" align="center">COD</td>
                                        <td width="3%" align="center">ID</td>
                                        <td width="32%">NOME</td>
                                        <td width="20%">CARGO</td>
                                        <td width="20%">HORÁRIOS</td>
                                        <td width="10%"  align="center">CPF</td>
                                        <td width="10%" align="center">ENTRADA</td>
                                        <td width="9%" align="center">CONTRATA&Ccedil;&Atilde;O</td>
                                        <td width="4%" align="center">PONTO</td>
                                        <!--td width="9%">DOCUMENTOS</td-->
                                        <td width="9%">CURRÍCULOS</td>
                                    </tr>

                                    <?php
                                    $result_participantes = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y') AS data_entrada2, date_format(data_saida, '%d/%m/%Y') AS data_saida2 FROM autonomo WHERE id_unidade = '{$row_unidades['id_unidade']}' AND status = '1' AND id_projeto = '$projeto' $auxPesquisa ORDER BY nome ASC");

                                    while ($row_bolsistas = mysql_fetch_array($result_participantes)) {

                                        $result_curso = mysql_query("SELECT * FROM curso WHERE id_curso = '$row_bolsistas[id_curso]'");
                                        $row_curso = mysql_fetch_array($result_curso);

                                        switch ($row_bolsistas['tipo_contratacao']) {
                                            case 1:
                                                $contratacao = "AUTÔNOMO";
                                                break;
                                            case 3:
                                                $contratacao = "COOPERADO";
                                                break;
                                            case 4:
                                                $contratacao = "AUTÔNOMO PJ";
                                                break;
                                        }


                                        if ($row_bolsistas['tipo_contratacao'] == 3) {

                                            $qr_verificacao1 = mysql_num_rows(mysql_query("SELECT *, date_format(data, '%d/%m/%Y') AS data FROM rh_doc_status WHERE tipo = 26 AND id_clt = '$row_bolsistas[0]'"));
                                            $qr_verificacao2 = mysql_num_rows(mysql_query("SELECT *, date_format(data, '%d/%m/%Y') AS data FROM rh_doc_status WHERE tipo = 29 AND id_clt = '$row_bolsistas[0]'"));
                                            $qr_verificacao3 = mysql_num_rows(mysql_query("SELECT *, date_format(data, '%d/%m/%Y') AS data FROM rh_doc_status WHERE tipo = 33 AND id_clt = '$row_bolsistas[0]'"));

                                            if ($qr_verificacao1 != 0) {
                                                $ass = "<img src='imagens/assinado.gif' border='0' title='Ficha de adesão emitida'>";
                                            } else {

                                                $ass = " <img src='imagens/naoassinado.gif' border='0' alt='Distrato' title='Ficha de adesão'>";
                                            }

                                            if ($qr_verificacao2 != 0) {

                                                $ass2 = "<img src='imagens/assinado.gif' border='0' title='Distrato emitido'>";
                                            } else {
                                                $ass2 = "<img src='imagens/naoassinado.gif' border='0' title='Distrato'> ";
                                            }

                                            if ($qr_verificacao3 != 0) {

                                                $ass3 = "<img src='imagens/assinado.gif' border='0' title='Devolução quotas emitida'>";
                                            } else {
                                                $ass3 = "  <img src='imagens/naoassinado.gif' border='0' title='Devolução quotas'>  ";
                                            }
                                        } else {
                                            // --------------- VERIFICANDO ASSINATURAS DE BOLSISTAS ---------------------------------------------------------

                                            if ($row_bolsistas['assinatura'] == "1") {
                                                $ass = "<a href=ver_tudo.php?id=18&projeto=$projeto&regiao=$regiao&ass=0&bolsista=$row_bolsistas[0]&tipo=1 title='Clique para REMOVER ASSINATURA do Contrato'>
                                                                <img src='imagens/assinado.gif' border='0' alt='Contrato'></a>";
                                            } else {
                                                $ass = "<a href='ver_tudo.php?id=18&projeto=$projeto&regiao=$regiao&ass=1&bolsista=$row_bolsistas[0]&tipo=1' title='Clique para alterar o Contrato para ASSINADO'>
                                                                <img src='imagens/naoassinado.gif' border='0' alt='Contrato'></a>";
                                            }

                                            if ($row_bolsistas['distrato'] == "1") {
                                                $ass2 = "<a href='ver_tudo.php?id=18&projeto=$projeto&regiao=$regiao&ass=0&bolsista=$row_bolsistas[0]&tipo=2' title='Clique para REMOVER ASSINATURA do Distrato'>
                                                                <img src='imagens/assinado.gif' border='0' alt='Distrato'></a>";
                                            } else {
                                                $ass2 = "<a href='ver_tudo.php?id=18&projeto=$projeto&regiao=$regiao&ass=1&bolsista=$row_bolsistas[0]&tipo=2' title='Clique para alterar o Distrato para ASSINADO'>
                                                                <img src='imagens/naoassinado.gif' border='0' alt='Distrato'></a>";
                                            }

                                            if ($row_bolsistas['outros'] == "1") {
                                                $ass3 = "<a href='ver_tudo.php?id=18&projeto=$projeto&regiao=$regiao&ass=0&bolsista=$row_bolsistas[0]&tipo=3' title='Clique para REMOVER ASSINATURA de Outros Documentos'>
                                                                <img src='imagens/assinado.gif' border='0' alt='Outros Documentos'></a>";
                                            } else {
                                                $ass3 = "<a href='ver_tudo.php?id=18&projeto=$projeto&regiao=$regiao&ass=1&bolsista=$row_bolsistas[0]&tipo=3' title='Clique para alterar Outros Documentos para ASSINADO'>
                                                                <img src='imagens/naoassinado.gif' border='0' alt='Outros Documentos'></a>";
                                            }
                                        }
                                        // --------------- VERIFICANDO ASSINATURAS DE BOLSISTAS ---------------------------------------------------------

                                        $color = "background-color:#FFC;";
                                        $textcor = "ok";

                                        if ($row_bolsistas['campo3'] == "INSERIR") {
                                            $color = "background-color:#FB797C";
                                            $textcor = "!";
                                        }

                                        if ($row_bolsistas['locacao'] == "1 - A CONFIRMAR") {
                                            $color = "background-color:#FB797C;";
                                            $textcor = "!";
                                        }

                                        if ($row_bolsistas['foto'] == "1") {
                                            $nome_imagem = $regiao . "_" . $projeto . "_" . $row_bolsistas['0'] . ".gif";
                                            $color = "background-color:#393; color:#000;";
                                            $textcor = "ok";
                                        }

                                        if (!empty($row_bolsistas['observacao'])) {
                                            $color = "background-color:#FB797C;";
                                            $obs = "title=\"Observações: $row_bolsistas[observacao]\"";
                                            $textcor = "!";
                                        }
                                        
                                        $Acurriculo = "<img src='imagens/naoassinado.gif' border='0' alt='Enviar' class='bt-upload-curriculo' data-type='{$row_bolsistas['tipo_contratacao']}' data-id='{$row_bolsistas['id_autonomo']}'>";
                                        if($row_bolsistas['curriculo']==1){
                                            $Acurriculo = "<img src='imagens/assinado.gif' border='0' alt='Visualizar' class='bt-ver-curriculo' data-type='{$row_bolsistas['tipo_contratacao']}' data-id='{$row_bolsistas['id_autonomo']}'>";
                                        }
                                        ?>

                                        <tr class="linha_<?php
                                        if ($alternateColor++ % 2 == 0) {
                                            echo "um";
                                        } else {
                                            echo "dois";
                                        }
                                        ?>" style="font-size:12px;">
                                            <td><div style="text-align:center;<?= $color ?>"><?= $textcor ?></div></td>
                                            <td align="center"><?= $row_bolsistas['campo3'] ?></td>
                                            <td align="center"></td>
                                            <td><a class="participante" href="ver_bolsista.php?reg=<?= $regiao ?>&bol=<?= $row_bolsistas['0'] ?>&pro=<?= $projeto ?>" <?= $obs ?>><?= abreviacao($row_bolsistas['nome']) ?></a></td>
                                            <td><?php echo str_replace('CAPACITANDO EM', '', $row_curso['nome']); ?></td>
                                            <td></td>
                                            <td align="center"><?php echo $row_bolsistas['cpf'] ?></td>
                                            <td align="center"><?= $row_bolsistas['data_entrada2'] ?></td>
                                            <td align="center"><?= $contratacao ?></td>
                                            <td align="center"><a href='folha_ponto.php?id=2&unidade=<?= $row_unidades['0'] ?>&regiao=<?= $regiao ?>&pro=<?= $projeto ?>&id_bol=<?= $row_bolsistas['0'] ?>&tipo=aut'>Gerar</a></td>
                                            <!--td align="center"><?= $ass . ' ' . $ass2 . ' ' . $ass3 ?></td-->
                                            <td align="center"><?= $Acurriculo ?></td>
                                        </tr>

                                        <?php
                                        unset($obs);
                                    }

                                    // -------------- AKI TERMINA APENAS BOLSISTAS E COMEÇA CLT ----------------------------

                                    $result_clt = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y') AS data_entrada2, date_format(data_saida, '%d/%m/%Y') AS data_saida2 FROM rh_clt WHERE id_projeto = '$projeto' AND id_unidade = '{$row_unidades['id_unidade']}' AND (status < '60' OR status = '200') AND status != 0 $auxPesquisa ORDER BY nome ASC");

                                    while ($row_clt = mysql_fetch_array($result_clt)) {

                                        // --------------- VERIFICANDO ASSINATURAS DE CLT ---------------------------------------------------------

                                        if ($row_clt['assinatura'] == "1") {
                                            $cltass = "<a href='ver_tudo.php?id=18&projeto=$projeto&regiao=$regiao&ass=0&bolsista=$row_clt[0]&tipo=1&tab=rh_clt' title='Clique para REMOVER ASSINATURA do Contrato'>
                                                                <img src='imagens/assinado.gif' border='0' alt=''></a>";
                                        } else {
                                            $cltass = "<a href='ver_tudo.php?id=18&projeto=$projeto&regiao=$regiao&ass=1&bolsista=$row_clt[0]&tipo=1&tab=rh_clt' title='Clique para alterar o Contrato para ASSINADO'>
                                                                <img src='imagens/naoassinado.gif' border='0' alt=''></a>";
                                        }

                                        if ($row_clt['distrato'] == "1") {
                                            $cltass2 = "<a href='ver_tudo.php?id=18&projeto=$projeto&regiao=$regiao&ass=0&bolsista=$row_clt[0]&tipo=2&tab=rh_clt' title='Clique para REMOVER ASSINATURA do Distrato'>
                                                                <img src='imagens/assinado.gif' border='0' alt=''></a>";
                                        } else {
                                            $cltass2 = "<a href='ver_tudo.php?id=18&projeto=$projeto&regiao=$regiao&ass=1&bolsista=$row_clt[0]&tipo=2&tab=rh_clt' title='Clique para alterar o Distrato para ASSINADO'>
                                                                <img src='imagens/naoassinado.gif' border='0' alt=''></a>";
                                        }

                                        if ($row_clt['outros'] == "1") {
                                            $cltass3 = "<a href='ver_tudo.php?id=18&projeto=$projeto&regiao=$regiao&ass=0&bolsista=$row_clt[0]&tipo=3&tab=rh_clt' title='Clique para REMOVER ASSINATURA de Outros Documentos'>
                                                                <img src='imagens/assinado.gif' border='0' alt=''></a>";
                                        } else {
                                            $cltass3 = "<a href='ver_tudo.php?id=18&projeto=$projeto&regiao=$regiao&ass=1&bolsista=$row_clt[0]&tipo=3&tab=rh_clt' title='Clique para alterar Outros Documentos para ASSINADO'>
                                                                <img src='imagens/naoassinado.gif' border='0' alt=''></a>";
                                        }

                                        // --------------- VERIFICANDO ASSINATURAS DE CLT ---------------------------------------------------------

                                        $color = "background-color:#FFC;";
                                        $textcor = "ok";

                                        if ($row_clt['campo3'] == "INSERIR") {
                                            $color = "background-color:#FB797C;";
                                            $textcor = "!";
                                        }

                                        if ($row_clt['locacao'] == "1 - A CONFIRMAR") {
                                            $color = "background-color:#FB797C;";
                                            $textcor = "!";
                                        }

                                        if ($row_clt['foto'] == "1") {
                                            $color = "background-color:#393; color:#000;";
                                            $textcor = "ok";
                                        }

                                        if (!empty($row_clt['observacao'])) {
                                            $color = "background-color:#FB797C;";
                                            $obs = "title=\"Observações: $row_clt[observacao]\"";
                                            $textcor = "!";
                                        }

                                        $CLTcurriculo = "<img src='imagens/naoassinado.gif' border='0' alt='Enviar' class='bt-upload-curriculo' data-type='2' data-id='{$row_clt['id_clt']}'>";
                                        if($row_clt['curriculo']==1){
                                            $CLTcurriculo = "<img src='imagens/assinado.gif' border='0' alt='Visualizar' class='bt-ver-curriculo' data-type='2' data-id='{$row_clt['id_clt']}'>";
                                        }

                                        $result_curso_clt = mysql_query("SELECT * FROM curso WHERE id_curso = '$row_clt[id_curso]'");
                                        $row_curso_clt = mysql_fetch_array($result_curso_clt);
                                        
                                        $qr_horario = mysql_query("SELECT * FROM rh_horarios WHERE id_horario = {$row_clt['rh_horario']} ") or die(mysql_error());
                                        $row_horario = mysql_fetch_assoc($qr_horario);
                                        ?>

                                        <tr class="linha_<?php
                                        if ($alternateColor++ % 2 == 0) {
                                            echo "um";
                                        } else {
                                            echo "dois";
                                        }
                                        ?>" style="font-size:12px;">
                                            <td><div style="text-align:center;<?= $color ?>"><?= $textcor ?></div></td>
                                            <td align="center"> <?= $row_clt['matricula'] ?></td>
                                            <td align="center"> <?= $row_clt['id_clt'] ?></td>
                                            <td><a class="participante" href="rh/ver_clt.php?reg=<?= $row_clt['id_regiao'] ?>&clt=<?= $row_clt['0'] ?>&ant=<?= $row_clt['1'] ?>&pro=<?= $projeto ?>&pagina=bol" <?= $obs ?>> <?= abreviacao($row_clt['nome']) ?> </a></td>
                                            <td><?php echo str_replace('CAPACITANDO EM', '', $row_curso_clt['nome']); ?></td>
                                            <td><?php echo $row_horario['nome'];?></td>
                                            <td align="center"><?= $row_clt['cpf'] ?></td>
                                            <td align="center"><?= $row_clt['data_entrada2'] ?></td>
                                            <td align="center">CLT</td>
                                            <td align='center'><a href='folha_ponto.php?id=2&unidade=<?= $row_unidades['0'] ?>&regiao=<?= $regiao ?>&pro=<?= $projeto ?>&id_bol=<?= $row_clt['0'] ?>&tipo=clt'>Gerar</a>
                                            </td>
                                            <!--td align="center" ><?= $cltass . ' ' . $cltass2 . ' ' . $cltass3 ?></td-->
                                            <td align="center" ><?= $CLTcurriculo ?></td>
                                        </tr>
                                        <?php
                                        unset($obs);
                                    }
                                    ?>
                                </table>

                            <?php } ?>

                            <table cellpadding="8" cellspacing="0" style="border:0px; background-color:#f5f5f5; margin:0px auto; margin-top:100px; width:100%;">
                                <tr>
                                    <td colspan="7" class="show" align="center" style="background-color:#930; color:#FFF;">PARTICIPANTES DESATIVADOS</td>
                                </tr>
                                <tr class="novo_tr">
                                    <td width="5%">COD</td>
                                    <td width="30%">NOME</td>
                                    <td width="20%">UNIDADE</td>
                                    <td  width="10%" align="center">CPF</td>
                                    <td width="20%" align="center">ENTRADA - SAÍDA</td>
                                    <td width="15%" align="center">CONTRATAÇÃO</td>
                                    <td width="15%" align="center">CURRÍCULO</td>
                                </tr>

                                <?php
                                while ($row2 = mysql_fetch_array($result_total_inativos)) {

                                    switch ($row2['tipo_contratacao']) {
                                        case 1:
                                            $contratacao = "AUTÔNOMO";
                                            break;
                                        case 2:
                                            $contratacao = "CLT";
                                            break;
                                        case 3:
                                            $contratacao = "COOPERADO";
                                            break;
                                        case 4:
                                            $contratacao = "AUTÔNOMO / PJ";
                                            break;
                                    }
                                    
                                    $AtuCurrDes = "<img src='imagens/naoassinado.gif' border='0' alt='Enviar' class='bt-upload-curriculo' data-type='{$row2['tipo_contratacao']}' data-id='{$row2['id_clt']}'>";
                                    if($row2['curriculo']==1){
                                        $AtuCurrDes = "<img src='imagens/assinado.gif' border='0' alt='Visualizar' class='bt-ver-curriculo' data-type='{$row2['tipo_contratacao']}' data-id='{$row2['id_clt']}'>";
                                    }
                                    ?>

                                    <tr class="linha_<?php
                                    if ($alternateColor++ % 2 == 0) {
                                        echo "um";
                                    } else {
                                        echo "dois";
                                    }
                                    ?>" style="font-size:12px;">
                                        <td><?= $row2['campo3'] ?></td>
                                        <td><a class="participante" href="ver_bolsista.php?reg=<?= $regiao ?>&bol=<?= $row2['0'] ?>&pro=<?= $projeto ?>"><?= $row2['nome'] ?></a></td>
                                        <td><?= $row2['locacao'] ?></td>
                                        <td align="center"><?= $row2['cpf'] ?></td>
                                        <td align="center"><?= $row2['data_entrada2'] . ' - ' . $row2['data_saida2'] ?></td>
                                        <td align="center"><?= $contratacao ?></td>
                                        <td align="center"><?= $AtuCurrDes ?></td>
                                    </tr>

                                    <?php
                                }

                                // -------------- AKI TERMINA APENAS BOLSISTAS E COMEÇA CLT ----------------------------

                                $result_clt2 = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y') AS data_entrada2, date_format(data_demi, '%d/%m/%Y') AS data_saida2 FROM rh_clt WHERE (status >= '60' AND status != '200') AND id_projeto = '$projeto' ORDER BY nome");

                                while ($row_clt2 = mysql_fetch_array($result_clt2)) {

                                    $result_curso_clt2 = mysql_query("SELECT * FROM curso WHERE id_curso = '$row_clt2[id_curso]'");
                                    $row_curso_clt2 = mysql_fetch_array($result_curso_clt2);
        
                                    $CltCurrDes = "<img src='imagens/naoassinado.gif' border='0' alt='Enviar' class='bt-upload-curriculo' data-type='2' data-id='{$row_clt2['id_clt']}'>";
                                    if($row_clt2['curriculo']==1){
                                        $CltCurrDes = "<img src='imagens/assinado.gif' border='0' alt='Visualizar' class='bt-ver-curriculo' data-type='2' data-id='{$row_clt2['id_clt']}'>";
                                    }
                                    ?>

                                    <tr class="linha_<?php
                                    if ($alternateColor++ % 2 == 0) {
                                        echo "um";
                                    } else {
                                        echo "dois";
                                    }
                                    ?>" style="font-size:12px;">
                                        <td><?= $row_clt2['matricula'] ?></td>
                                        <td><a class="participante" href='rh/ver_clt.php?reg=<?= $row_clt2['id_regiao'] ?>&clt=<?= $row_clt2['0'] ?>&ant=<?= $row_clt2['1'] ?>&pro=<?= $projeto ?>&pagina=bol'<?= $row_clt2[obs] ?>><?= $row_clt2['nome'] ?></a></td>
                                        <td><?= $row_clt2['locacao'] ?></td>
                                        <td align="center"><?= $row_clt2['cpf'] ?></td>
                                        <td align="center"><?= $row_clt2['data_entrada2'] . ' - ' . $row_clt2['data_saida2'] ?></td>
                                        <td align="center">CLT</td>
                                        <td align="center"><?= $CltCurrDes ?></td>
                                    </tr>

                                <?php } ?>

                            </table>
                        <?php } ?>
                        <div style="width:95%; margin:0px auto; font-size:13px; padding-bottom:4px; margin-top:15px; text-align:right;">
                            <a href="#corpo" title="Subir navegação">Subir ao topo</a>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

</body>
</html>