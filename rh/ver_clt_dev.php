<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="../login.php">Logar</a>';
    exit;
}

include('../conn.php');
include('../funcoes.php');
include('../upload/classes.php');
include('../classes/funcionario.php');
include('../classes/formato_data.php');
include('../classes/formato_valor.php');
include('../classes/EventoClass.php');
include('../classes_permissoes/acoes.class.php');

$Fun = new funcionario();
$Fun->MostraUser(0);
$Master = $Fun->id_master;
$ACOES = new Acoes();

//PEGANDO O ID DO CADASTRO

$id = 1;
$id_clt = $_REQUEST['clt'];
$id_ant = $_REQUEST['ant'];
$id_pro = $_REQUEST['pro'];
$id_reg = $_REQUEST['reg'];
$id_user = $_COOKIE['logado'];
$pagina = $_REQUEST['pagina'];
$data = date("Y-m-d");
$eventos = new Eventos();
$dadosEventos = $eventos->getTerminandoEventos($data, $id_reg, $id_pro, $id_clt);


$sql_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($sql_user);

$result = mysql_query(" SELECT *, date_format(data_entrada, '%d/%m/%Y') AS nova_data, date_format(data_saida, '%d/%m/%Y') AS data_saida2, date_format(dataalter, '%d/%m/%Y') AS dataalter2 FROM rh_clt WHERE id_clt = $id_clt");
$row = mysql_fetch_array($result);

$result_data_entrada = mysql_query("
SELECT data_entrada, DATE_ADD(data_entrada, INTERVAL '90' DAY) AS data_contratacao, CASE WHEN data_entrada < DATE_SUB(CURDATE(), INTERVAL '90' DAY) THEN 'Contratado' WHEN data_entrada > DATE_SUB(CURDATE(), INTERVAL '90' DAY) AND data_entrada <= CURDATE() THEN 'Em experiência até ' ELSE 'Aguardando' END AS status_contratacao FROM rh_clt WHERE id_clt = '$id_clt'") or die(mysql_error());
$row2 = mysql_fetch_assoc($result_data_entrada);

$data_contratacao = implode('/', array_reverse(explode('-', $row2['data_contratacao'])));
$status_contratacao = $row2['status_contratacao'];

$result_pro = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$id_pro'");
$row_pro = mysql_fetch_array($result_pro);

$sql_user2 = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$row[useralter]'");
$row_user2 = mysql_fetch_array($sql_user2);

$result_ban = mysql_query("SELECT * FROM bancos WHERE id_regiao = '$id_reg' AND id_projeto = '$id_pro'");

if ($row['status'] == '62') {
    $texto = "<font color=red><b>Data de saída:</b> $row[data_saida2]</font><br>";
} else {
    $texto = NULL;
}

$nome_para_arquivo = $row['1'];

if ($row['foto'] == '1') {
    $nome_imagem = $id_reg . '_' . $id_pro . '_' . $row['0'] . '.gif';
} else {
    $nome_imagem = 'semimagem.gif';
}

$qr_status = mysql_query("SELECT tipo FROM rhstatus WHERE codigo = '$row[status]'");
$ativo = (mysql_result($qr_status, 0) == "recisao") ? false : true;

$sql_qtd_clt = mysql_query("SELECT A.*, B.nome AS nome_projeto
                            FROM rh_clt AS A
                            LEFT JOIN projeto AS B ON (A.id_projeto = B.id_projeto)
                            WHERE A.nome = '{$row['nome']}' AND A.cpf = '{$row['cpf']}' AND A.pis = '{$row['pis']}' ORDER BY B.nome") or die(mysql_error());
$tot_clt = mysql_num_rows($sql_qtd_clt);

/*
 *  para trazer as licensas médicas com mais de 15 dias
 */
if ($row['status'] == 20) {
    $licenca = $eventos->getEventosSeguidos($id_clt, 20);
}
?>
<html>
    <head>
        <title>:: Intranet ::</title>
        <link rel='shortcut icon' href='../favicon.ico'>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="css/estrutura_participante.css" rel="stylesheet" type="text/css">
        <script type="text/javascript" src="../SpryAssets/SpryAccordion.js"></script>
        <script type="text/javascript" src="../js/jquery-1.8.3.min.js"></script>
        <script type="text/javascript" src="../uploadfy/scripts/swfobject.js"></script>
        <script type="text/javascript" src="../uploadfy/scripts/jquery.uploadify.v2.1.0.js"></script>
        <script type="text/javascript" src="../js/shadowbox.js"></script>
        <script type="text/javascript" src="../js/jquery.form.js"></script>
        <link rel="stylesheet" type="text/css" href="../js/shadowbox.css">
        <link rel="stylesheet" type="text/css" href="css/spry.css">
        <link rel="stylesheet" type="text/css" href="../uploadfy/css/default.css" />
        <link rel="stylesheet" type="text/css" href="../uploadfy/css/uploadify.css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../css/progress.css" rel="stylesheet" type="text/css">
        <script src="../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script src="http://malsup.github.com/jquery.form.js" type="text/javascript"></script>
        <link href="../js/highslide.css" rel="stylesheet" type="text/css"  /> 
        <script type="text/javascript" src="../js/highslide-with-html.js"></script> 
        <script type="text/javascript">
            Shadowbox.init();
        </script>
        <script type="text/javascript">


            hs.graphicsDir = '../images-box/graphics/';
            hs.outlineType = 'rounded-white';

            $().ready(function() {



<?php if ($row['foto'] == '1') { ?>
                    $("#bt_deletar").show();
<?php } ?>

<?php if (isset($_REQUEST['entregaCTPS']) && $_REQUEST['entregaCTPS'] == 0) { ?>
                    alert('ATENÇÃO: Não há registro de entrada de CTPS para este CLT.');
<?php } ?>

                $("#fileQueue").hide();
                $("#bt_deletar").click(function() {
                    $.post('../include/excluir_foto.php',
                            {nome: '<?= $_GET['reg'] ?>_<?= $_GET['pro'] ?>_<?= $_GET['clt'] ?>.gif', clt: '<?= $_GET['clt'] ?>'},
                    function() {
                        $("#imgFile").attr('src', '../fotos/semimagem.gif');
                        $("#bt_deletar").hide();
                        $('#bt_enviar').uploadifySettings('buttonText', 'Adicionar foto');
                    }

                    );
                });

                $("#bt_enviar").uploadify({
                    'uploader': '../uploadfy/scripts/uploadify.swf',
                    'script': '../uploadfy/scripts/uploadify.php',
                    'folder': '../../../fotos',
                    'buttonText': '<?php if ($row['foto'] == '1') { ?>Alterar<?php } else { ?>Adicionar<?php } ?> foto',
                    'queueID': 'fileQueue',
                    'cancelImg': '../uploadfy/cancel.png',
                    'auto': true,
                    'method': 'post',
                    'multi': false,
                    'fileDesc': 'Gif',
                    'fileExt': '*.gif;*.jpg;',
                    'onOpen': function() {
                        $("#fileQueue").show();
                    },
                    'onAllComplete': function() {
                        $("#bt_deletar").show('slow');
                        $('#imgFile').attr('src', '../fotosclt/<?= $_GET['reg'] ?>_<?= $_GET['pro'] ?>_<?= $_GET['clt'] ?>.gif');
                        $("#fileQueue").hide('slow');
                        $('#bt_enviar').uploadifySettings('buttonText', 'Alterar foto');
                    },
                    'scriptData': {'regiao': <?= $_GET['reg'] ?>, 'projeto': <?= $_GET['pro'] ?>, 'clt': <?= $_GET['clt'] ?>}
                });

                // UPLOAD DO ARQUIVO DE EVENTO
                $(".anexar-atestado").click(function() {
                    var evento = $(this).data("id");
                    //var click = $(this).data("click");
                    $("#id_evento").val(evento); // muda o val do input #id_evento
                    //$("#form_up_evento").removeClass('hidden'); // exibe o form de upload
                    $("#form_up_evento").show('fast'); // exibe o form de upload
                });

                var bar = $('.bar');
                var percent = $('.percent');
                var status = $('#status');

                $('#form_up_evento').validationEngine({promptPosition: "topLeft"});
                $('#form_up_evento').ajaxForm({
                    clearForm: true,
                    beforeSend: function() {
                        status.empty();
                        var percentVal = '0%';
                        bar.width(percentVal);
                        percent.html(percentVal);
                    },
                    uploadProgress: function(event, position, total, percentComplete) {
                        var percentVal = percentComplete + '%';
                        //bar.width(percentVal);
                        $('progress').attr('value', percentComplete);
                        $(".progress-bar span").css("width", percentComplete + "%");
                        //bar.animate({width: percentVal}, 500);
                        percent.html(percentVal);
                    },
                    success: function() {
                        var percentVal = '100%';
                        $('progress').attr('value', '100');
                        $(".progress-bar span").css("width", "100%");
//                        bar.width(percentVal);
                        percent.html(percentVal);
                    },
                    complete: function(xhr) {
                        status.html(xhr.responseText);
                        status.removeClass("hidden");
                    }
                });

                // FIM DO UPLOAD DO ARQUIVO DE EVENTO

            });
        </script>
        <link rel="stylesheet" type="text/css" href="../js/highslide.css" />
        <link rel="stylesheet" href="../js/lightbox.css" type="text/css" media="screen" />
        <style type="text/css">
            .back-green{
                background: #BAFBB1;
                border: 1px solid #5E9952;
                color: #0EB307;
                padding: 5px;
                margin: 10px 0;
            }
            .hidden{
                display: none;
            }

            #form_up_evento{
                padding: 5px;
                margin: 10px 0;
                background-color: #fafafa;
                border: 1px solid #eee;
            }

            .avisos_eventos{
                border: 1px solid #ccc;
                padding: 8px;
                box-sizing: border-box;
                background: #FFCACB;
                color: #D90000;
                font-size:1.2em;
            }
            .avisos_eventos h2{
                color: #930;
                margin: 10px 0px;
            }
            .avisos_eventos li{
                list-style: none;
                font-family: arial;
                font-size: 12px;
                line-height: 20px;
                margin-left: 15px;
            }
            .false{
                color:#D90000;
            }
            .true {
                color:#339933;
            }
            .icon-anexo{
                width: 20px;
                height: 20px;
            }
            .icon-anexo:hover{
                background-color: rgba(0,255,255,.25);
                -webkit-box-shadow: 0px 0px 8px 0px rgba(0, 255, 255, 0.75);
                -moz-box-shadow:    0px 0px 8px 0px rgba(0, 255, 255, 0.75);
                box-shadow:         0px 0px 8px 0px rgba(0, 255, 255, 0.75);
            }
            .disable, .disable:hover{
                opacity: .3;
                background-color: transparent;
                -webkit-box-shadow: none;
                -moz-box-shadow:    none;
                box-shadow:         none;
            }
            a.btn-aviso{
                display: inline-block;
                padding: 5px 8px;
                margin: 3px;
                background-color: #F5F5F5;
                border: 1px solid #ccc;
                border-radius: 3px;
                -moz-border-radius: 3px;
                -webkit-border-radius: 3px;
                color:#333;
            }
            a.btn-aviso:hover{
                background-color: #eee;
                color: #555;
            }
        </style>
    </head>
    <body>
        <div id="fileQueue"></div>
        <div id="corpo">
            <?php if ($licenca['soma'] >15) { ?>
                <div class="avisos_eventos">
                    <p><img src="../imagens/icones/icon-exclamation.gif" title="Atenção"> <strong>Atenção:</strong> Este funcionário possui licença médica com mais de <strong>15 dias</strong>. É nesserário marcar perícia.</p>
                </div>
            <?php } ?>
            <div id="conteudo">
                <table align="center" width="100%" cellspacing="0" cellpadding="12" style="font-size:13px; line-height:22px;">
                    <tr>
                        <td colspan="2">

                            <div style="float:right;"><?php include('../reportar_erro.php'); ?></div>
                            <div style="clear:right;"></div>

                            <?php if ($_GET['sucesso'] == 'cadastro') { ?>
                                <div id="sucesso">
                                    Participante cadastrado com sucesso!
                                </div>
                            <?php } ?>
                            <div style="border-bottom:2px solid #F3F3F3; margin-top:10px;">
                                <h2 style="float:left; font-size:18px;">VISUALIZAR <span class="clt">CLT</span> <br><br>
                                    MATRÍCULA: <?php echo formato_matricula($row['matricula']) ?>
                                </h2>
                                <p style="float:right;">
                                    <?php if ($_GET['sucesso'] == 'cadastro') { ?>
                                        <a href="cadastroclt.php?regiao=<?= $id_reg ?>&projeto=<?= $id_pro ?>">&laquo; Cadastrar Outro Participante</a>
                                        <?php
                                    } else {
                                        if ($_GET['pagina'] == 'clt') {
                                            ?>
                                            <a href="clt.php?regiao=<?= $id_reg ?>">&laquo; Visualizar Participantes</a>
                                        <?php } elseif ($_GET['pagina'] == 'bol') { ?>
                                            <a href="../bolsista.php?regiao=<?= $id_reg ?>&projeto=<?= $id_pro ?>">&laquo; Visualizar Participantes</a>
                                            <?php
                                        }
                                    }
                                    ?>
                                </p>
                                <div class="clear"></div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td width="16%" rowspan="2" valign="top" align="center">
                            <img src="../fotosclt/<?= $nome_imagem ?>" name="imgFile" width="100" height="130" id="imgFile" style="margin-top:-12px; margin-bottom:5px;"/>


                            <input type="file" id="bt_enviar" name="bt_enviar"/>


                            <a href="#" id="bt_deletar" style="display:none; position:relative; top:5px;"><img src="../imagens/excluir_foto.gif"></a>
                        </td>
                        <td width="84%" bgcolor="#F3F3F3" valign="top">
                            <b>Nº do processo:</b> <?php echo formato_num_processo($row['n_processo']) ?> / <?php echo formato_matricula($row['matricula']) ?><br>
                            <b><?= $row['campo3'] ?> - <?= $row['nome'] ?></b><br>
                            <b>CPF:</b> <?= $row['cpf'] ?><br>
                            <b>Data de Entrada:</b> <?= $row['nova_data'] ?><br>
                            <?= $texto ?>
                            <b>Projeto:</b> <?= $row_pro['id_projeto'] ?> - <?= $row_pro['nome'] ?><br>

                            <?php
                            if ($row['status'] == 200) {

                                echo '<span style="color:red;">Aguardando Demissão</span><br>';
                            } else {

                                if ($status_contratacao == 'Contratado') {
                                    echo '<span style="color:#00F;">' . $status_contratacao . '</span><br>';
                                } elseif ($status_contratacao == 'Em experiência até ') {
                                    echo '<span style="font-size:14px; font-style:inherit; color:#F00;">' . $status_contratacao . ' ' . $data_contratacao . '</span><br>';
                                } elseif ($status_contratacao == 'Aguardando') {
                                    echo '<span style="color:black;">' . $status_contratacao . '</span><br>';
                                }

                                $qr_status = mysql_query("SELECT especifica FROM rhstatus WHERE codigo = '$row[status]'");

                                if ($row['status'] != 10) {
                                    echo '<div style="color:#F00; font-size:14px;">' . @mysql_result($qr_status, 0) . '</div>';
                                } else {
                                    echo '<div style="color:#06F;">' . @mysql_result($qr_status, 0) . '</div>';
                                }
                            }
                            ?>
                            <br>
                            <?php
                            if (!empty($row['orgao'])) {

                                if (!empty($row['verifica_orgao'])) {
                                    echo '<span style="background-color:  #8bdd5e;"> Orgão regulamentador verificado. </span>';
                                } else {
                                    echo '<span style="background-color:   #fe9898"; color: #FFF;">Orgão regulamentador não verificado.</span>';
                                }
                            }
                            ?>
                            <br>
                            <i><?php echo 'Ultima Alteração feita por <b>' . $row_user2['nome1'] . '</b> na data ' . $row['dataalter2']; ?></i>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table cellpadding="0" cellspacing="0" width="100%" style="color: #fe9898">
                                <tr>
                                    <td>
                                        <div id="Accordion1" class="Accordion" tabindex="0">
                                            <div class="AccordionPanel">
                                                <div class="AccordionPanelTab">&nbsp;</div>
                                                <div class="AccordionPanelContent">
                                                    <?php
                                                    $get_atividade = mysql_query("SELECT * FROM curso WHERE id_curso = '$row[id_curso]'");
                                                    $atividade = mysql_fetch_assoc($get_atividade);
                                                    $get_pg = mysql_query("SELECT * FROM tipopg WHERE id_tipopg = '$row[tipo_pagamento]'");
                                                    $pg = mysql_fetch_assoc($get_pg);

                                                    if ($row['banco'] == '9999') {
                                                        $nome_banco = $row['nome_banco'];
                                                    } else {
                                                        $get_banco = mysql_query("SELECT nome FROM bancos WHERE id_banco = '$row[banco]'");
                                                        $row_banco = mysql_fetch_array($get_banco);
                                                        $nome_banco = $row_banco[0];
                                                    }
                                                    ?>

                                                    <b>Atividade:</b> <?= $atividade['id_curso'] ?> - <?= $atividade['nome'] ?> <?php
                                                    if (!empty($atividade['cbo_codigo'])) {
                                                        echo '(' . $atividade['cbo_codigo'] . ')';
                                                    }
                                                    ?><br>
                                                    <b>Unidade:</b> <?= $row['locacao'] ?><br>
                                                    <b>Salário:</b>
                                                    <?php
                                                    if (!empty($atividade['salario'])) {
                                                        echo "R$ ";
                                                        echo number_format($atividade['salario'], 2, ',', '.');
                                                    } else {
                                                        echo "<i>Não informado</i>";
                                                    }
                                                    ?>
                                                    &nbsp;&nbsp;<b>Tipo de Pagamento:</b> 
                                                    <?php
                                                    if (!empty($pg['tipopg'])) {
                                                        echo $pg['tipopg'];
                                                    } else {
                                                        echo "<i>Não informado</i>";
                                                    }
                                                    ?><br>
                                                    <b>Agência:</b> 
                                                    <?php
                                                    if (!empty($row['agencia'])) {
                                                        echo $row['agencia'];
                                                    } else {
                                                        echo "<i>Não informado</i>";
                                                    }
                                                    ?>
                                                    &nbsp;&nbsp;<b>Conta:</b> 
                                                    <?php
                                                    if (!empty($row['conta'])) {
                                                        echo $row['conta'];
                                                    } else {
                                                        echo "<i>Não informado</i>";
                                                    }
                                                    ?>
                                                    &nbsp;&nbsp;<b>Banco:</b>
                                                    <?php
                                                    if (!empty($nome_banco)) {
                                                        echo $nome_banco;
                                                    } else {
                                                        echo "<i>Não informado</i>";
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>   
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div id="observacoes">
                                <?php
                                if (empty($row['observacao'])) {
                                    echo "Sem Observações";
                                } else {
                                    echo "Observações<p>&nbsp;</p> $row[observacao]";
                                }
                                ?>
                            </div>
                            <div class="avisos_eventos">
                                <ul>
                                    <?php foreach ($dadosEventos as $eventos) { ?>
                                        <?php $tipo = ($eventos['dias_restantes'] != 0) ? "false" : "true"; ?>
                                        <li class="<?php echo $tipo; ?>">
                                            <?php
                                            echo
                                            "<b>" . $eventos['data_retorno'] . "</b> - " .
                                            $eventos['nome_clt'] . " termina o evento " .
                                            $eventos['status_de'] . ", restando " . $eventos['dias_restantes'] . " dias para o evento </br>";
                                            ?>
                                        </li>
                                    <?php } ?>
                                </ul>   
                            </div>
                        </td>                                                
                    </tr>

                    <?php if ($tot_clt > 1) { ?>                    
                        <tr>
                            <td colspan="2">
                                <div id="observacoes">  
                                    Colaborador trabalha em mais de uma unidade
                                    <ul>
                                        <?php while ($row_clt = mysql_fetch_assoc($sql_qtd_clt)) { ?>
                                            <li><?php echo $row_clt['nome_projeto']; ?></li>                                                                   
                                        <?php } ?>
                                    </ul>                      
                                </div>
                            </td>
                        </tr>
                    <?php } ?>

                    <tr>
                        <td colspan="2"><h1><span>MENU DE EDIÇÃO</span></h1></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="menu">
                            <?php
// Consulta para Links
                            $result_entregar = mysql_query("SELECT * FROM controlectps WHERE nome = '$row[nome]'");
                            $num_row_entregar = mysql_num_rows($result_entregar);
                            if ($num_row_entregar != "0") {
                                $row_entregar = mysql_fetch_array($result_entregar);
                                $target = 'target="_blank"';
                                $link_ctps = "../ctps_entregar.php?case=1&regiao=$id_reg&id=$row_entregar[0]";
                            } else {
                                $link_ctps = "ver_clt.php?reg=$id_reg&clt=$id_clt&ant=$id_ant&pro=$id_pro&pagina=bol&entregaCTPS=0";
                                $target = '';
                            }

                            if (!empty($row['pis'])) {
                                $statusBotao = 'none';
                                $emissao = true;
                            } else {
                                $statusBotao = 'inline';
                                $emissao = false;
                            }
                            ?>

                            <p>
                                <?php
                                if ($ACOES->verifica_permissoes(72) && $ativo) {
                                    ?>
                                    <!-- linha 1 -->
                                    <a href="abertura_processo.php?clt=<?= $row['0'] ?>&pro=<?= $id_pro ?>&pagina=<?= $pagina ?>&reg=<?= $id_reg ?>" class="botao">Abertura de processo</a>


                                    <?php
                                }

                                if ($ACOES->verifica_permissoes(14)) {
                                    ?>
                                    <!-- linha 1 -->
                                    <a href="alter_clt.php?clt=<?= $row['0'] ?>&pro=<?= $id_pro ?>&pagina=<?= $pagina ?>" class="botao">Editar</a>

                                    <a href="formulario_dependentes_ir.php?clt=<?= $row['0'] ?>&pro=<?= $id_pro ?>&reg=<?= $id_reg ?>" class="botao">Dependentes IR</a>

                                    <a href="direction/index.php?clt=<?= $row['0'] ?>" class="botao">Mapa de Deslocamento</a>

                                    <?php
                                }
//VERIFICA SE O PROJETO ESTÁ DESATIVADO
                                if ($row_pro['status_reg'] == 1) {


                                    if ($ACOES->verifica_permissoes(15) && $ativo) {
                                        ?>

                                        <a href="../tvsorrindo.php?bol=<?= $row['id_antigo'] ?>&clt=<?= $row['0'] ?>&pro=<?= $id_pro ?>&tipo=2" target="_blank" class="botao">TV Sorrindo</a>
                                        <?php
                                    }

                                    if ($ACOES->verifica_permissoes(78) && $ativo) {
                                        ?>
                                        <a href="salariofamilia/safami.php?pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>&clt=<?= $row['0'] ?>" target="_blank" class="botao"> Cad. do Salário Família</a>
                                        <?php
                                    }

                                    if ($ACOES->verifica_permissoes(16)) {
                                        ?>         
                                        <a href="../rendimento/index.php?clt=<?= $row['0'] ?>&pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>" class="botao" target="_blank" style="font-size:12px;">Informe de Rendimento</a>

                                    </p>

                                    <!-- linha 2 -->
                                    <p> <?php
                                    }
                                    if ($ACOES->verifica_permissoes(17) && $ativo) {
                                        ?>  

                                        <a href="../ctps.php?regiao=<?= $id_reg ?>&id=1&clt=<?= $row['0'] ?>" target="_blank" class="botao">Receber CTPS</a>
                                        <?php
                                    }
                                    if ($ACOES->verifica_permissoes(18)) {
                                        ?> 
                                        <a href="<?= $link_ctps ?>" <?php echo $target; ?> class="botao">Entregar CTPS</a>    
                                        <?php
                                    }


                                    if ($ACOES->verifica_permissoes(61)) {
                                        ?>       

                                        <a href="solicitacaopis.php?pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>&clt=<?= $row['0'] ?>" target="_blank" class="botao" style="font-size:12px;"> Cadastro PIS</a>
                                        <?php
                                    }
                                    if ($ACOES->verifica_permissoes(19) && $ativo) {
                                        ?>    
                                        <!-- linha 3 -->
                                    <p><a href="admissional_clt.php?clt=<?= $row['0'] ?>&pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>" target="_blank" class="botao" style="font-size:12px;">Exame Admissional</a>
                                        <?php
                                    }
                                    if ($ACOES->verifica_permissoes(20)) {
                                        ?>
                                        <a href="gerarPonto.php?regiao=<?= $id_reg ?>&pro=<?= $id_pro; ?>&id=<?= $id_user ?>&clt=<?= $row['0'] ?>" target="_blank" class="botao"  style="font-size:12px;">Gerar Apontamento</a>
                                        <?php
                                    }
                                    if ($ACOES->verifica_permissoes(20)) {
                                        ?>  
                                        <a href="contratoclt.php?id_reg=<?= $id_reg ?>&clt=<?= $row['0'] ?>" target="_blank" class="botao" style="font-size:12px;">Contrato de Trabalho</a>
                                        <?php
                                    }
                                    if ($ACOES->verifica_permissoes(20)) {
                                        ?>  
                                        <a href="contratocltexp.php?id_reg=<?= $id_reg ?>&clt=<?= $row['0'] ?>" target="_blank" class="botao" style="font-size:12px;">Contrato de Experiência</a>
                                        <?php
                                    }
                                    if ($ACOES->verifica_permissoes(80) && $ativo) {
                                        ?>  
                                        <a href="rh_transferencia.php?clt=<?= $row['0'] ?>" target="_blank" class="botao" style="font-size:12px;">Transferência de Unidade</a>
                                        <?php
                                    }
                                    //if($ACOES->verifica_permissoes(79)) {
                                    ?>  
                                    <a href="../registrodeempregado.php?bol=<?= $row['id_antigo'] ?>&pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>&clt=<?= $row['0'] ?>" target="_blank" class="botao">Registro de empregado</a></p>
                                <a href="../registrodeempregado_pordata.php?bol=<?= $row['id_antigo'] ?>&pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>&clt=<?= $row['0'] ?>&tela=1" target="_blank" class="botao">Registro de empregado Por Data</a></p>
                                <?php
                                //}

                                if ($ACOES->verifica_permissoes(21)) {
                                    ?>  
                                    <a href="../fichadecadastroclt.php?bol=<?= $row['id_antigo'] ?>&pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>&clt=<?= $row['0'] ?>" target="_blank" class="botao">Ficha de Cadastro</a></p>
                                    <?php
                                }

                                if ($ACOES->verifica_permissoes(22) && $ativo) {
                                    ?>  
                                    <!-- linha 4 -->
                                    <p><a href="salariofamilia/safami.php?pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>&clt=<?= $row['0'] ?>" target="_blank" class="botao">Benefícios</a>
                                        <?php
                                    }
                                    if ($ACOES->verifica_permissoes(23) && $ativo) {
                                        ?>  
                                        <a href="vt/vt.php?pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>&clt=<?= $row['0'] ?>" target="_blank" class="botao">Vale Transporte</a>
                                        <?php
                                    }
                                    if ($ACOES->verifica_permissoes(24) && $ativo) {
                                        ?>  
                                        <a href="cartadereferencia.php?pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>&clt=<?= $row['0'] ?>" target="_blank" class="botao" style="font-size:12px;">Carta de Referência</a></p>
                                    <?php
                                }
                                if ($ACOES->verifica_permissoes(25) && $ativo) {
                                    ?>   
                                    <!-- linha 5 -->
                                    <!--<p><a href="../rh/notifica/advertencia.php?clt=<?= $row['0'] ?>&tab=bolsista<?= $id_pro ?>&pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>" target="_blank" class="botao">Advertência</a>-->
                                    <p><a href="../rh/notifica/advertencia.php?clt=<?= $row['0'] ?>&pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>" target="_blank" class="botao">Advertência</a>
                                        <?php
                                    }
                                    if ($ACOES->verifica_permissoes(26) && $ativo) {
                                        ?>  
                                        <a href="../rh/notifica/form_suspencao.php?clt=<?= $row['0'] ?>&tab=bolsista<?= $id_pro ?>&pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>" target="_blank" class="botao">Suspensão</a>
                                        <?php
                                    }
                                    if ($ACOES->verifica_permissoes(27)) {
                                        ?>  
                                        <a href="../relatorios/fichafinanceira_clt.php?reg=<?= $id_reg ?>&pro=<?= $id_pro ?>&tipo=2&tela=2&id=<?= $row['0'] ?>" target="_blank" class="botao">Ficha Financeira</a></p>
                                    <?php
                                }
                                if ($ACOES->verifica_permissoes(28)) {
                                    ?>  
                                    <!-- linha 6 -->
                                    <?php // if(!in_array($row['status'],array('60','61','62','63','64','65','66','81','101'))) {    ?>
                                    <p><a href="docs/dispensa.php?clt=<?= $row['0'] ?>&tab=bolsista<?= $id_pro ?>&pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>" target="_blank" class="botao">Dispensa</a>
                                        <?php
                                        //}
                                        //if($ACOES->verifica_permissoes(29)  && $ativo) {
                                        ?>    
                                        <a href="docs/demissao.php?clt=<?= $row['0'] ?>&tab=bolsista<?= $id_pro ?>&pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>" target="_blank" class="botao">Demissão</a>
                                        <?php
                                        //}
                                        if ($ACOES->verifica_permissoes(30)) {
                                            ?>  
                                            <a href="demissionalclt.php?pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>&clt=<?= $row['0'] ?>" target="_blank" class="botao" style="font-size:12px;">Exame Demissional</a></p>
                                        <?php
                                    }
                                }
                                ?>

                                <a href="declaracao_jornada_semanal.php?pro=<?= $id_pro ?>&reg=<?= $id_reg ?>&clt=<?= $row['0'] ?>" target="_blank" class="botao" style="font-size:12px;">Declaração de Jornada Semanal</a></p>

                            <?php }   //FIM VERIFICAÇÃO      ?>
                            <?php if ($ACOES->verifica_permissoes(90) && ($row['status'] >= 60 && $row['status'] != 200)) { ?>
                                <a href="cadastroclt.php?projeto=<?= $id_pro ?>&regiao=<?= $id_reg ?>&id=<?= $row['0'] ?>" target="_blank" class="botao">Recadastrar</a>
                            <?php } ?>
                        </td>

                    </tr>

                    <?php
                    if ($ACOES->verifica_permissoes(62)) {
                        ?>  

                        <tr>
                            <td colspan="2">
                                <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
                                    <tr bgcolor="#dddddd">
                                        <td><strong>DOCUMENTOS</strong></td>
                                        <td colspan="2"> </td>         
                                        <td  align="center"><strong>STATUS</strong></td>
                                        <td  align="center"><strong>DATA</strong></td>
                                    </tr>
                                    <?php
                                    $qr_documentos = mysql_query("SELECT * FROM upload ORDER BY ordem") or die(mysql_error());
                                    while ($row_documentos = mysql_fetch_assoc($qr_documentos)):

                                        $verifica_anexo = mysql_num_rows(mysql_query("SELECT * FROM documento_clt_anexo WHERE id_upload = '$row_documentos[id_upload]' AND id_clt = '$row[0]' AND anexo_status = 1"));

                                        if ($row_documentos['id_upload'] == 13 and $row['contrato_medico'] == 0)
                                            continue;

                                        if ($row_documentos['id_upload'] != 14) {


                                            $onclick = "OnClick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\"";
                                            $visualizar = ($verifica_anexo != 0) ? '<a href="ver_documentos.php?id=' . $row_documentos['id_upload'] . '&clt=' . $id_clt . '" ' . $onclick . ' title="VISUALIZAR">
													 		 <img src="../imagens/ver_anexo.gif" width="20" height="20" />	  	
														  </a>' : '';
                                            $status = ($verifica_anexo == 0) ? '<img src="../imagens/naoassinado.gif" />' : '<img src="../imagens/assinado.gif" />';

                                            $anexar = '<a href="anexar_documento.php?clt=' . $row['0'] . '&id=' . $row_documentos['id_upload'] . '" title="ANEXAR/EDITAR"> <img src="../img_menu_principal/anexo.png" class="icon-anexo" /></a>';

                                            $data = @mysql_result(mysql_query("SELECT date_format(data_cad, '%d/%m/%Y') as data FROM documento_clt_anexo WHERE id_clt = '$id_clt' AND id_upload = '$row_documentos[id_upload]'  ORDER BY data_cad DESC"), 0);

                                            if ($row_documentos['id_upload'] == 13) {
                                                $visualizar = '<a href="contrato_medico.php?clt=' . $row[0] . '" title="VISUALIZAR"> 
								<img src="../imagens/ver_anexo.gif" width="20" height="20" />	  	
							</a>';
                                                $anexar = '';
                                                $status = '<img src="../imagens/assinado.gif" />';
                                            }

//BRUNO CRITÉRIOS DE AVALIAÇÃO
                                            if ($row_documentos['id_upload'] == 19) {
                                                $verifica_linha = mysql_num_rows(mysql_query("SELECT * FROM rh_avaliacao_clt WHERE clt_id = " . $row[0]));

                                                $visualizar = ($verifica_linha == 0) ? '' : '<a href="ver_avaliacao_clt.php?clt=' . $row[0] . '"><img src="../imagens/ver_anexo.gif" width="20" height="20" /></a>';
                                                $anexar = ($verifica_linha == 0) ? '<a href="avaliacao_clt.php?clt=' . $row[0] . '&reg=' . $_GET["reg"] . '&pro=' . $_GET["pro"] . '"><img src="../img_menu_principal/anexo.png" class="icon-anexo" /></a>' : '';

                                                $status = ($verifica_linha == 0) ? '<img src="../imagens/naoassinado.gif" />' : '<img src="../imagens/assinado.gif" />';
                                                $data = @mysql_result(mysql_query("SELECT date_format(data_cadastro, '%d/%m/%Y') as data FROM rh_avaliacao_clt WHERE clt_id = '$row[0]'"), 0);
                                            }
// FIM CRITÉRIOS DE AVALIAÇÃO
                                        } else {

                                            $qr_processo = mysql_query("SELECT *,DATE_FORMAT(data_cad, '%d/%m/%Y') as data FROM processos_interno WHERE id_clt = '$id_clt' AND proc_interno_status = 1");
                                            $row_processo = mysql_fetch_assoc($qr_processo);
                                            $verifica_processo = mysql_num_rows($qr_processo);

                                            $status = ($verifica_processo == 0) ? '<img src="../imagens/naoassinado.gif" />' : '<img src="../imagens/assinado.gif" />';
                                            $data = $row_processo['data'];
                                            $visualizar = '<a href="ver_abertura_proc.php?clt=' . $row[0] . '" title="VISUALIZAR"> 
					<img src="../imagens/ver_anexo.gif" width="20" height="20" />	  	
				</a>';
                                        }

                                        if ($cont++ % 2) {
                                            $color = "#fafafa";
                                        } else {
                                            $color = "#f3f3f3";
                                        }
                                        ?>
                                        <tr bgcolor="<?php echo $color; ?>" height="25">
                                            <td ><?php echo $row_documentos['arquivo'] ?></td>
                                            <td align="center"><?php echo $anexar; ?></td>
                                            <td align="center"><?php echo $visualizar; ?></td>
                                            <td align="center"><?php echo $status; ?></td>  
                                            <td align="center"><?php echo $data; ?></td>      
                                        </tr>

                                        <?php
                                    endwhile;
                                    ?>



                                </table>
                            </td>
                        </tr>	 






                        <tr id="ancora_documentos">
                            <td colspan="2"><h1><span>UPLOAD DE DOCUMENTOS</span></h1></td>
                        </tr>
                        <tr>
                            <td colspan="2">

                                <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
                                    <tr>
                                        <td style="font-weight:bold;" id="fotosDocumentos">
                                            <?php
                                            // Exclusão do Documento
                                            if (isset($_GET['deleta_documento'])) {
                                                if (file_exists($_GET['deleta_documento'])) {
                                                    unlink($_GET['deleta_documento']);
                                                    echo 'Documento deletado com sucesso!<br>';
                                                }
                                            }
                                            //

                                            $diretorio_padrao = $_SERVER["DOCUMENT_ROOT"] . "/";
                                            $diretorio_padrao .= "intranet/documentos/";
                                            $dirInternet = "../documentos/";
                                            $DeldirInternet = "documentos/";

                                            $regiao = sprintf("%03d", $id_reg);
                                            $projeto = sprintf("%03d", $id_pro);

                                            $Dir = $regiao . "/" . $projeto . "/"; // O NOME DA PASTA ONDE VAI SER CRIADO A PASTA DO USUARIO
                                            $novoDir = $row['tipo_contratacao'] . "_" . $row[0]; // O NOME DA PASTA DO USUARIO
                                            $DirCom = $Dir . $novoDir;

                                            $dir = $diretorio_padrao . $DirCom;
                                            $dirInternet .= $DirCom;
                                            $DeldirInternet .= $DirCom;
                                            // Abre um diretorio conhecido, e faz a leitura de seu conteudo
                                            if (is_dir($dir)) {
                                                if ($dh = opendir($dir)) {
                                                    while (($file = readdir($dh)) !== false) {
                                                        if ($file == "." or $file == "..") {
                                                            $nada;
                                                        } else {
                                                            $tipoArquivo = explode("_", $file);
                                                            $tipoArquivo = explode(".", $tipoArquivo[2]);

                                                            $select = new upload();
                                                            $TIPO = $select->mostraTipo($tipoArquivo[0]);

                                                            $DirFinal = $dirInternet . "/" . $file;
                                                            $DelDirFinal = $DeldirInternet . "/" . $file;

                                                            // Renomeia o arquivo se estiver sem extensão
                                                            if (!strstr($DirFinal, '.jpg') and ! strstr($DirFinal, '.gif') and ! strstr($DirFinal, '.png')) {
                                                                $de = $DirFinal;
                                                                $para = $DirFinal . '.jpg';
                                                                rename($de, $para);
                                                                $DirFinal .= '.jpg';
                                                            }

                                                            // Criando Array para Options no Select
                                                            $ja_documentos[] = $file;

                                                            echo "<div class='documentos'>";
                                                            echo "<a class='documento' href='" . $DirFinal . "' rel='shadowbox[documentos]' title='Visualizar $TIPO'>";
                                                            echo "<img src='" . $DirFinal . "' width='75' height='75' border='0' alt='$TIPO'></a>";
                                                            echo "<a href='$_SERVER[PHP_SELF]?$_SERVER[QUERY_STRING]&deleta_documento=$DirFinal#ancora_documentos'>deletar</a>";
                                                            echo "</div>";
                                                        }
                                                    }
                                                    closedir($dh);
                                                }
                                            }

                                            // Criando Array para Options no Select
                                            if (!empty($ja_documentos)) {
                                                foreach ($ja_documentos as $documento) {
                                                    $documento = explode('_', $documento);
                                                    $tipo_documento = explode('.', $documento[2]);
                                                    $tipo_documento = $tipo_documento[0];
                                                    $tipos_ja_documentos[] = $tipo_documento;
                                                }
                                            }
                                            //
                                            ?>
                                        </td>
                                    </tr>
                                    <?php if (count($tipos_ja_documentos) != 5) { ?>
                                        <tr>
                                            <td>
                                                <div id="foto">
                                                    <br><input type="file" name="uploadDoc" id="uploadDoc">
                                                </div></td>
                                        </tr>
                                        <tr>
                                            <td>

                                                <div id="upload_documentos" style="display:none;">
                                                    <table width="0%" border="0" cellspacing="0" cellpadding="0">
                                                        <tr>
                                                            <td>
                                                                <div id="BarUploadDoc" style="margin-bottom:10px; display:none;"></div>
                                                                <b>Tipo de Documento:</b>&nbsp;&nbsp;
                                                                <select name="select" id="select_doc" >
                                                                    <option  selected value="">Escolha um tipo abaixo</option>
                                                                    <?php
                                                                    $qr_documentos = mysql_query("SELECT * FROM  upload	 WHERE status_reg = '1'");
                                                                    while ($documento = mysql_fetch_assoc($qr_documentos)) {
                                                                        if (!in_array($documento['id_upload'], $tipos_ja_documentos)) {
                                                                            ?>
                                                                            <option value="<?= $documento['id_upload'] ?>"><?= $documento['arquivo'] ?></option>
                                                                            <?php
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                                <a class="botao" id="Upar" style="cursor:pointer; float:none; margin-top:8px;">Enviar Documento</a>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <td>

                                            <table border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td>

                                                        <script language="javascript">
                                                            $().ready(function() {
                                                                var tipo_contratacao = '<?= $row['tipo_contratacao'] ?>';
                                                                var regiao = '<?= sprintf('%03d', $id_reg); ?>';
                                                                var projeto = '<?= sprintf('%03d', $id_pro); ?>';
                                                                var id_participante = '<?= sprintf('%03d', $id_clt); ?>';

                                                                $("#uploadDoc").uploadify({
                                                                    'uploader': '../uploadfy/scripts/uploadify.swf',
                                                                    'script': '../include/upload_doc.php',
                                                                    'buttonImg': '../imagens/botao_upload.jpg',
                                                                    'buttonText': '',
                                                                    'cancelImg': '../uploadfy/cancel.png',
                                                                    'width': '156',
                                                                    'height': '46',
                                                                    'fileDesc': 'Jpg, Gif, Png',
                                                                    'fileExt': '*.gif;*.jpg;*.png',
                                                                    'auto': false,
                                                                    'method': 'post',
                                                                    'multi': false,
                                                                    'queueID': 'BarUploadDoc',
                                                                    'onSelect': function() {
                                                                        $("#upload_documentos").show();

                                                                    },
                                                                    'onComplete': function(event, queueID, fileObj, response, data) {

                                                                        if (response != 1) {
                                                                            $("#upload_documentos").hide();

                                                                            $.post('../include/fotos_documentos.php', {
                                                                                'id_regiao': regiao,
                                                                                'id_projeto': projeto,
                                                                                'tipo_contratacao': tipo_contratacao,
                                                                                'id_participante': id_participante
                                                                            }, function(dados) {
                                                                                $("#fotosDocumentos").html(dados);
                                                                            });
                                                                        } else {
                                                                            alert('Erro ao enviar o arquivo!');
                                                                        }
                                                                    },
                                                                    'scriptData': {'reg': regiao,
                                                                        'projeto': projeto,
                                                                        'ID_participante': id_participante,
                                                                        'tipo_contratacao': tipo_contratacao

                                                                    }
                                                                });



                                                                $('#Upar').click(function() {
                                                                    if ($('#select_doc').val() != '') {
                                                                        $('#uploadDoc').uploadifySettings('scriptData', {'tipo_documento': $('#select_doc').val()});
                                                                        $('#uploadDoc').uploadifyUpload();
                                                                        $('#BarUploadDoc').slideDown('slow');


                                                                    } else {
                                                                        alert('Selecione um tipo de documento');
                                                                    }
                                                                });
                                                            });

                                                            function Confirm(a) {
                                                                var arquivo = a;
                                                                input_box = confirm("Deseja realmente excluir o documento?");
                                                                if (input_box == true) {
                                                                    location.href = "<?= $_SERVER['PHP_SELF'] ?>?<?= $_SERVER['QUERY_STRING'] ?>&foto=deletado#ancora_documentos";
                                                                }
                                                            }
                                                        </script>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>

                                <?php
                            }
                            ?>  </td>
                    </tr>



                    <?php
                    if ($ACOES->verifica_permissoes(63)) {
                        if ($ativo) {
                            ?>  
                            <tr>
                                <td colspan="2"><h1><span>ENCAMINHAMENTO DE CONTA</span></h1></td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <form action="../declarabancos.php" method="post" name="form1" target="_blank">
                                        <b>Escolha o Banco:</b>&nbsp;&nbsp;
                                        <select name="banco" id="banco">
                                            <?php
                                            while ($row_ban = mysql_fetch_array($result_ban)) {
                                                print "<option value=$row_ban[id_banco]>$row_ban[nome]</option>";
                                            };
                                            ?>
                                        </select>
                                        <input type="submit" value="Gerar Encaminhamento de Conta">
                                        <input type="hidden" name="tipo" id="tipo" value="2">
                                        <input type="hidden" name="bolsista" id="bolsista" value="<?= $row['0'] ?>">
                                        <input type="hidden" name="regiao" id="regiao" value="<?= $id_reg ?>">
                                    </form> 
                                </td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td colspan="2"><h1><span>CONTROLE DE DOCUMENTOS</span></h1></td>
                        </tr>
                        <tr>
                            <td colspan="2">

                                <table width="100%" border="0" cellpadding="4" cellspacing="0" style="font-size:13px;">
                                    <tr bgcolor="#dddddd">
                                        <td width="70%"><strong>DOCUMENTO</strong></td>
                                        <td width="15%" align="center"><strong>STATUS</strong></td>
                                        <td width="15%" align="center"><strong>DATA</strong></td>
                                    </tr>
                                    <?php
                                    $cont = "1";
                                    $tipo_contratacao = '2';

                                    $result_docs = mysql_query("SELECT * FROM rh_documentos WHERE tipo_contratacao = '$tipo_contratacao' ORDER BY documento");

                                    while ($row_docs = mysql_fetch_array($result_docs)) {
                                        if ($cont % 2) {
                                            $color = "#fafafa";
                                        } else {
                                            $color = "#f3f3f3";
                                        }

                                        $result_verifica = mysql_query("SELECT *,date_format(data, '%d/%m/%Y')as data FROM rh_doc_status WHERE tipo = '$row_docs[0]' and id_clt = '$row[0]'");
                                        $num_row_verifica = mysql_num_rows($result_verifica);
                                        $row_verifica_doc = mysql_fetch_array($result_verifica);

                                        if ($num_row_verifica != "0") {
                                            $img = "<img src='../imagens/assinado.gif' width='15' height='17' align='absmiddle'>";
                                            $data = $row_verifica_doc['data'];
                                        } else {
                                            $img = "<img src='../imagens/naoassinado.gif' width='15' height='17' align='absmiddle'>";
                                            $data = "";
                                        }
                                        echo "<tr bgcolor=$color>";
                                        echo "<td class='linha'>$row_docs[documento]</td>";
                                        //echo "<td class='linha' align='center'>$img</td>";
                                        if (($row_docs['documento'] == 'Inscrição no PIS') and ( $emissao == true)) {
                                            $img = "<img src='../imagens/assinado.gif' width='15' height='17' align='absmiddle'>";
                                            echo "<td class='linha' align='center'>$img</td>";
                                        } elseif (($row_docs['documento'] != 'Inscrição no PIS') or ( $emissao == false)) {
                                            echo "<td class='linha' align='center'>$img</td>";
                                        }
                                        echo "<td align='center'>$data</td>";
                                        echo "</tr>";


                                        $cont++;
                                        $img = "";
                                        $data = "";
                                    }
                                    ?>
                                    <tr>
                                        <td colspan="3" align="center" class="linha" style="font-size:16px;"><img src="../imagens/assinado.gif" width="15" height="17" align="absmiddle"> Emitido  <img src="../imagens/naoassinado.gif" width="15" height="17" align="absmiddle"> N&atilde;o Emitido</td>
                                    </tr>
                                </table>

                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><h1><a name="eventos"><span>CONTROLE DE EVENTOS</span></a></h1></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <table width="100%" border="0" cellpadding="4" cellspacing="0" style="font-size:13px;">
                                    <tr bgcolor="#dddddd">


                                        <td>Evento</td>
                                        <td>Data</td>
                                        <td>Data de retorno</td>
                                        <td>Dias</td>
                                        <td>Anexar <br> Documento</td>
                                        <td>Ver <br> Documento</td>
                                    </tr>
                                    <?php
                                    $qr_historico_eventos = mysql_query("SELECT * FROM rh_eventos WHERE id_clt = '$id_clt' AND id_regiao = '$id_reg' AND id_projeto = '$id_pro' AND nome_status!='' AND status = '1' ") or die(mysql_error());

                                    $qr_historico_ferias = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$id_clt' AND regiao = '$id_reg' AND projeto = '$id_pro' AND status = '1' ") or die(mysql_error());

                                    $qr_historico_rescisao = mysql_query("SELECT * FROM rh_recisao WHERE id_clt = '$id_clt' AND id_regiao = '$id_reg' AND id_projeto = '$id_pro' AND status = '1' ") or die(mysql_error());
                                    $qr_historico_clt = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$id_clt' AND id_regiao = '$id_reg' AND id_projeto = '$id_pro' AND status!=0") or die(mysql_error());

                                    while ($row_clt = mysql_fetch_assoc($qr_historico_clt)):

                                        $historico[] = array(
                                            'nome' => 'Admissão',
                                            'inicio' => $row_clt['data_entrada'],
                                            'fim' => '',
                                            'duracao' => '',
                                            'id_evento' => '',
                                            'status' => '',
                                        );

                                    endwhile;

                                    while ($row_evento = mysql_fetch_assoc($qr_historico_eventos)):

                                        $historico[] = array(
                                            'nome' => $row_evento['nome_status'],
                                            'inicio' => $row_evento['data'],
                                            'fim' => $row_evento['data_retorno'],
                                            'duracao' => $row_evento['dias'],
                                            'id_evento' => $row_evento['id_evento'],
                                            'status' => $row_evento['cod_status'],
                                        );
                                    endwhile;

                                    while ($row_ferias = mysql_fetch_assoc($qr_historico_ferias)):

                                        $historico[] = array(
                                            'nome' => 'Férias',
                                            'inicio' => $row_ferias['data_ini'],
                                            'fim' => $row_ferias['data_fim'],
                                            'duracao' => ($row_ferias['data_fim'] - $row_ferias['data_ini']),
                                            'id_evento' => '',
                                            'status' => '',
                                        );

                                    endwhile;

                                    while ($row_recisao = mysql_fetch_assoc($qr_historico_rescisao)):

                                        $historico[] = array(
                                            'nome' => 'Rescisão',
                                            'inicio' => $row_recisao['data_demi'],
                                            'fim' => '',
                                            'duracao' => '',
                                            'id_evento' => '',
                                            'status' => '',
                                        );
                                    endwhile;

                                    $cod_status = array(20, 50, 51);

                                    foreach ($historico as $chave => $inicio) {
                                        ?>
                                        <tr class="linha_<?= ($cor++ % 2) ? 'um' : 'dois' ?>">

                                            <td><?php echo $historico[$chave]['nome']; ?></td>

                                            <td><?php echo formato_brasileiro($historico[$chave]['inicio']); ?></td>

                                            <td>
                                                <?php
                                                if ($historico[$chave]['fim'] != '0000-00-00') {
                                                    echo formato_brasileiro($historico[$chave]['fim']);
                                                }
                                                ?>
                                            </td>

                                            <td><?php if (!empty($historico[$chave]['duracao'])) echo $historico[$chave]['duracao']; ?></td>

                                            <td style="text-align: center;">
                                                <?php if (!empty($historico[$chave]['status']) && in_array($historico[$chave]['status'], $cod_status)) { ?>
                                                    <a href="#eventos" data-id="<?= $historico[$chave]['id_evento'] ?>" class="anexar-atestado" data-click="1"><img src="../img_menu_principal/anexo.png" class="icon-anexo"></a>
                                                <?php } else { ?>
                                                    <img src="../img_menu_principal/anexo.png" class="icon-anexo disable">
                                                <?php } ?>
                                            </td>

                                            <td style="text-align: center;">
                                                <?php if (!empty($historico[$chave]['status']) && in_array($historico[$chave]['status'], $cod_status)) { ?>
                                                    <a href="lista_AnexoEventos.php?id=<?= $historico[$chave]['id_evento'] ?>"><img src="../imagens/ver_anexo.gif" class="icon-anexo"></a>
                                                <?php } else { ?>
                                                    <img src="../imagens/ver_anexo.gif" class="icon-anexo disable">
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </table>
                                <form action="../include/upload_atestado.php" method="post" id="form_up_evento" class="hidden" enctype="multipart/form-data">
                                    <div style="margin: .5em 0;">
                                        <input type="file" name="atestado" id="atestado" class="validate[required,custom[docsType]]">
                                        <input type="hidden" name="id_evento" id="id_evento" value="">
                                        <input type="hidden" name="reg" id="reg" value="<?= sprintf('%03d', $id_reg); ?>">
                                        <input type="hidden" name="projeto" id="projeto" value="<?= sprintf('%03d', $id_pro); ?>">
                                        <input type="hidden" name="ID_participante" id="id_participante" value="<?= sprintf('%03d', $id_clt); ?>">
                                        <input type="hidden" name="tipo_contratacao" id="tipo_contratacao" value="2">
                                        <input type="submit" value="Salvar">
                                    </div>

                                    <progress max="100" value="0">
                                        <!-- Browsers that support HTML5 progress element will ignore the html inside `progress` element. Whereas older browsers will ignore the `progress` element and instead render the html inside it. -->
                                        <div class="progress-bar">
                                            <span style="width:0%"></span>
                                        </div>
                                    </progress>
                                    <div id="status" class="hidden back-green"></div>
                                </form>
                            </td>
                        </tr>

                        <?php
                    }

                    if ($ACOES->verifica_permissoes(14)) {
                        ?>     
                        <tr>
                            <td colspan="2"><h1><span>CONTROLE DE MOVIMENTOS</span></h1></td>
                        </tr>
                        <tr>
                            <td colspan="2"></td>
                        </tr>

                        <?php
                    }
                    ?>  
                </table>
            </div>
            <div id="rodape">
                <?php
                $qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$Master'");
                $master = mysql_fetch_assoc($qr_master);
                ?>
                <p class="left"><img style="position:relative; top:7px;" src="../imagens/logomaster<?= $Master ?>.gif" width="66" height="46"> <b><?= $master['razao'] ?></b>&nbsp;&nbsp;Acesso Restrito à Funcion&aacute;rios</p>
                <p class="right"><br><br><a href="#corpo">Subir ao topo</a></p>
                <div class="clear"></div>
            </div>
        </div>
        <script type="text/javascript">
            var Accordion1 = new Spry.Widget.Accordion("Accordion1", {enableAnimation: false, useFixedPanelHeights: false, defaultPanel: -1});
        </script>
    </body>
</html>