<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="../login.php">Logar</a>';
    exit;
}

header("Location: /intranet/rh_novaintra/ver_clt.php?id_clt={$_REQUEST['clt']}"); exit;

include('../conn.php');
include('../classes/global.php');
include('../classes/clt.php');
include("../classes/FeriasClass.php");
include('../wfunction.php');
include('../funcoes.php');
include('../upload/classes.php');
include('../classes/funcionario.php');
include('../classes/formato_data.php');
include('../classes/formato_valor.php');
include('../classes/EventoClass.php');
include('../classes_permissoes/acoes.class.php');
include ("../classes/LogClass.php");


$log = new Log();
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

//$objAcoes = new Acoes();
//$usuario = carregaUsuario();
//$id_regiao = $usuario['id_regiao'];
//$id_clt = $_REQUEST['id_clt'];
$feriasObj = new Ferias();
$feriasObj->calcFerias->setIdClt($id_clt);
$listaFeria = $feriasObj->calcFerias->getFeriasPorClt();

//if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'verEventos'){
//    $type = $_REQUEST['type'];
//    $id = $_REQUEST['id'];
//    
//    switch($type){
//        case 'F�rias':$type = 3; break;
//        case 'Rescis�o':$type = 4; break;
//        case 'Admiss�o':$type = 1; break;
//        default:$type = 2; break;
//    }
//    
//    $sql = "SELECT * FROM eventos_anexos WHERE id_tipo_evento = $type AND id_evento = $id";
//    $query = mysql_query($sql);
//    $rows = mysql_num_rows($query);
//    echo json_encode($rows);
//    exit();
//}

$sql_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($sql_user);

$result = mysql_query(" SELECT *, date_format(data_entrada, '%d/%m/%Y') AS nova_data, date_format(data_saida, '%d/%m/%Y') AS data_saida2, date_format(dataalter, '%d/%m/%Y') AS dataalter2 FROM rh_clt WHERE id_clt = $id_clt");
$row = mysql_fetch_array($result);

$result_data_entrada = mysql_query("
SELECT data_entrada, DATE_ADD(data_entrada, INTERVAL '89' DAY) AS data_contratacao, CASE WHEN data_entrada < DATE_SUB(CURDATE(), INTERVAL '90' DAY) THEN 'Contratado' WHEN data_entrada > DATE_SUB(CURDATE(), INTERVAL '90' DAY) AND data_entrada <= CURDATE() THEN 'Em experi�ncia at� ' ELSE 'Aguardando' END AS status_contratacao FROM rh_clt WHERE id_clt = '$id_clt'") or die(mysql_error());
$row2 = mysql_fetch_assoc($result_data_entrada);

$data_contratacao = implode('/', array_reverse(explode('-', $row2['data_contratacao'])));
$status_contratacao = $row2['status_contratacao'];

$result_pro = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$id_pro'");
$row_pro = mysql_fetch_array($result_pro);

$sql_user2 = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$row[useralter]'");
$row_user2 = mysql_fetch_array($sql_user2);

$sql_user3 = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$row[sis_user]'");
$row_user3 = mysql_fetch_array($sql_user3);

$result_ban = mysql_query("SELECT * FROM bancos WHERE id_regiao = '$id_reg' AND id_projeto = '$id_pro'");

if ($row[status] >= '60' AND $row[status] != '200') {
    $texto = "<font color=red><b>Data de sa�da:</b> $row[data_saida2]</font><br>";
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

$sql_qtd_clt = mysql_query("SELECT A.*, B.nome AS nome_projeto, B.id_master
        FROM rh_clt AS A
        LEFT JOIN projeto AS B ON (A.id_projeto = B.id_projeto)
        WHERE B.id_master = '{$Master}' AND A.nome = '{$row['nome']}' AND A.cpf = '{$row['cpf']}' AND A.pis = '{$row['pis']}' ORDER BY B.nome") or die(mysql_error());


if ($_COOKIE['logado'] == 179) {
    echo "SELECT A.*, B.nome AS nome_projeto, B.id_master
        FROM rh_clt AS A
        LEFT JOIN projeto AS B ON (A.id_projeto = B.id_projeto)
        WHERE B.id_master = '{$Master}' AND A.nome = '{$row['nome']}' AND A.cpf = '{$row['cpf']}' AND A.pis = '{$row['pis']}' ORDER BY B.nome";
}

$tot_clt = mysql_num_rows($sql_qtd_clt);

/*
 *  para trazer as licensas m�dicas com mais de 15 dias
 */
if ($row['status'] == 20) {
    $licenca = $eventos->getEventosSeguidos($id_clt, 20);
}

// a vari�vel indica se o funcion�rio pode ou n�o ser rescindido, deacordo com a regra da licen�a maternidade
$indResPosMaternidade = $eventos->rescisaoPosMaternidade($id_clt);
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

            $().ready(function () {
<?php if ($row['foto'] == '1') { ?>
                    $("#bt_deletar").show();
<?php } ?>

<?php if (isset($_REQUEST['entregaCTPS']) && $_REQUEST['entregaCTPS'] == 0) { ?>
                    alert('ATEN��O: N�o h� registro de entrada de CTPS para este CLT.');
<?php } ?>

                $("#fileQueue").hide();
                $("#bt_deletar").click(function () {
                    $.post('../include/excluir_foto.php',
                            {nome: '<?= $_GET['reg'] ?>_<?= $_GET['pro'] ?>_<?= $_GET['clt'] ?>.gif', clt: '<?= $_GET['clt'] ?>'},
                    function () {
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
                    'onOpen': function () {
                        $("#fileQueue").show();
                    },
                    'onAllComplete': function () {
                        $("#bt_deletar").show('slow');
                        $('#imgFile').attr('src', '../fotosclt/<?= $_GET['reg'] ?>_<?= $_GET['pro'] ?>_<?= $_GET['clt'] ?>.gif');
                        $("#fileQueue").hide('slow');
                        $('#bt_enviar').uploadifySettings('buttonText', 'Alterar foto');
                    },
                    'scriptData': {'regiao': <?= $_GET['reg'] ?>, 'projeto': <?= $_GET['pro'] ?>, 'clt': <?= $_GET['clt'] ?>}
                });

                // UPLOAD DO ARQUIVO DE EVENTO
                $(".anexar-atestado").click(function () {
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
                    beforeSend: function () {
                        status.empty();
                        var percentVal = '0%';
                        bar.width(percentVal);
                        percent.html(percentVal);
                    },
                    uploadProgress: function (event, position, total, percentComplete) {
                        var percentVal = percentComplete + '%';
                        $('progress').attr('value', percentComplete);
                        $(".progress-bar span").css("width", percentComplete + "%");
                        percent.html(percentVal);
                    },
                    success: function () {
                        var percentVal = '100%';
                        $('progress').attr('value', '100');
                        $(".progress-bar span").css("width", "100%");
                        percent.html(percentVal);
                    },
                    complete: function (xhr) {
                        status.html(xhr.responseText);
                        status.removeClass("hidden");
                    }
                });

                // FIM DO UPLOAD DO ARQUIVO DE EVENTO

                // AMANDA
                // MAX: FOI COLOCADO A EXCESS�O DESSE ID_CLT 5395, POIS ELA SOLICITOU A DEMISS�O, NESTE CASO SEGUNDO A REJANE, PODE SER DEMITIDO
                // O IDEAL � COLOCAR UMA FLAG
                $(".indResPosMat").click(function () {
                    var clt_prov = $("#clt_prov").val();
                    console.log(clt_prov);
                    if (clt_prov != 6562 || clt_prov != 5196 || clt_prov != 5199) {
                        if ($("#indicativo").val() == 'N') {
                            alert('Esta funcion�ria n�o pode ser demitida pois possui estabilidade de 30 dias ap�s o t�rmino da licen�a maternidade.');
                            return false;
                        }
                    }
                });
                
                $('#tipoDoc').on('change', function(){
                   var tipoDoc = $(this).val();
                   var userfile = $('#userFile').val();
                   if(tipoDoc != '-1') {
                       $('#envFile').removeClass('hide');
                   } else {
                       $('#envFile').addClass('hide');
                   }
                });
                
                $('.crud span').on('click', function(){
                    
                    var crud = $(this).data('crud');
                    var anexo = $(this).data('key');
                    
                    if(crud == 'deletar') {
                        $.ajax({
                            url:'action.upload.php',
                            method:'post',
                            dataType:'json',
                            data:{deletar:'deletar', anexo:anexo},
                            success:function(data){
                                if(data == 1){
//                                    console.log(anexo);
                                    $('#doc'+anexo).remove();
                                    alert('Documento removido com sucesso!');
                                    location.reload(); 
                                }
                            }
                        });
                    } else if(crud == 'visualizar') {
                        window.open('http://'+anexo, '_blank');
                    }
                });
                
                $('#lightBox').on('click', function(){
                    $('#evDocType').val('');
                    $('#evDocId').val('');
                    $('#evDocsFile').val('');
                    
                    $('#eventoDocs').addClass('hide');
                    $(this).addClass('hide');
                });
                
                $('.icon-anexo').on('click',function(){
                    
                    var key = $(this).data('key');
                    switch(key) {
                        case 'upload':
                            
                            var type = $(this).data('type');
                            var id = $(this).data('id');
                            
                            $('#eventoDocs').removeClass('hide');
                            $('#lightBox').removeClass('hide');
                            
                            if(type == 'Admiss�o') {
                                    type = 1;
                            } else if(type == 'F�rias') {
                                    type = 3;
                            } else if(type == 'Rescis�o') {
                                    type = 4;
                            } else {
                                    type = 2;
                            }
                            
                            $('#evDocType').val(type);
                            $('#evDocId').val(id);

                        break;
                        case 'visualizar':
                            
                            var link = $(this).data('url');
                            window.open(window.location.protocol + "//" + window.location.host + "/" + link);

                        break;
                        case 'deletar':
                            
                            var type = $(this).data('type');
                            var id = $(this).data('id');
                            var idanexo = $(this).data('idanexo');

                            if(type == 'Admiss�o') {
                                    type = 1;
                            } else if(type == 'F�rias') {
                                    type = 3;
                            } else if(type == 'Rescis�o') {
                                    type = 4;
                            } else {
                                    type = 2;
                            }

                            $.post('actionUploadEventos.php', {deletar:'deletar', id:id, type:type, idAnexo:idanexo}).done(function(data){
                               if(data) {
                                   alert('Anexo excluido com sucesso');
                                   location.reload();
                               }
                            });

                        break;
                    }
                });                
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
            
            .hide {
                display: none;
            }
            
            #lightBox {
                z-index: 8999;
                width: 100%;
                height: 2000px;
                position: fixed;
                top: 0;
                background-color: rgba(0,0,0,.7);
            }
            
            #evDocsHead {
                height: 50px;
                background: linear-gradient(#8585ff,#4c4cff);
                border-top-left-radius: 5px;
                border-top-right-radius: 5px; 
            }
            
            #eventoDocs {
                z-index: 9999;
                margin: 0 auto;
                position: fixed;
                top: 30px;
                left: 33%;
                border-radius: 5px;
                width: 33%;
                box-shadow: 0 0 20px #171717;
                background-color: white;
            }
            
            #evDocsTitle {
                line-height: 50px;
                font-size: 19px;
                color: #fff;
            }
        </style>
    </head>
    <body>
        <div id="fileQueue"></div>
        <div id="corpo">
            <?php if ($licenca['soma'] > 15) { ?>
                <div class="avisos_eventos">
                    <p><img src="../imagens/icones/icon-exclamation.gif" title="Aten��o"> <strong>Aten��o:</strong> Este funcion�rio possui licen�a m�dica com mais de <strong>15 dias</strong>. � nesser�rio marcar per�cia.</p>
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
                                <h2 style="float:left; font-size:18px;" >VISUALIZAR <span class="clt">CLT</span> <br><br>
                                    MATR�CULA: <?php echo formato_matricula($row['matricula']) ?>
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
                                            <a href="../rh_novaintra/bolsista.php?regiao=<?= $id_reg ?>&projeto=<?= $id_pro ?>" onclick="window.close();">&laquo; Visualizar Participantes</a>
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


                            <?php if($_COOKIE['logado'] != 395){ ?>
                            <input type="file" id="bt_enviar" name="bt_enviar"/>
                            <?php } ?>

                            <a href="#" id="bt_deletar" style="display:none; position:relative; top:5px;"><img src="../imagens/excluir_foto.gif"></a>
                        </td>
                        <td width="84%" bgcolor="#F3F3F3" valign="top">
                            <b>N� do processo:</b> <?php echo formato_num_processo($row['n_processo']) ?> / <?php echo formato_matricula($row['matricula']) ?><br>
                            <b><?= $row['campo3'] ?> - <?= $row['nome'] ?></b><br>
                            <b>CPF:</b> <?= $row['cpf'] ?><br>
                            <b>Data de Entrada:</b> <?= $row['nova_data'] ?><br>
                            <?= $texto ?>
                            <b>Projeto:</b> <?= $row_pro['id_projeto'] ?> - <?= $row_pro['nome'] ?><br>

                            <?php
                            if ($row['status'] == 200) {

                                echo '<span style="color:red;">Aguardando Demiss�o</span><br>';
                            } else {

                                if ($status_contratacao == 'Contratado') {
                                    echo '<span style="color:#00F;">' . $status_contratacao . '</span><br>';
                                } elseif ($status_contratacao == 'Em experi�ncia at� ') {
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
                                    echo '<span style="background-color:  #8bdd5e;"> Org�o regulamentador verificado. </span>';
                                } else {
                                    echo '<span style="background-color:   #fe9898"; color: #FFF;">Org�o regulamentador n�o verificado.</span>';
                                }
                            }
                            ?>
                            <br>
                            <?php
                            $data_cad = $row['data_cad'];
                            $data_import = $row['data_importacao'];
                            $ultim_atualizacao = explode(" ", $row['data_ultima_atualizacao']);
                            if ($data_cad == "0000-00-00" or $data_import != null) {
                                $cadastrado_import = "Importado <b>";
                                $data = implode("/", array_reverse(explode("-", $data_import)));
                            } else {
                                $cadastrado_import = "Cadastrado por <b>";
                                $data = implode("/", array_reverse(explode("-", $row['data_cad'])));
                            }

                            if ($row['hora_cad'] != null) {
                                $hora_cadastrada = "e hor�rio '. {$row['hora_cad']}";
                            }
                            ?>
                            <i><?php echo $cadastrado_import . " " . $row_user3['nome'] . '</b> na data ' . $data . '</b> ' . $hora_cadastrada; ?></i>
                            <br><i><?php echo 'Ultima Altera��o feita por <b>' . $row_user2['nome'] . '<br></b> na data ' . $row['dataalter2'] . '</b> e hor�rio ' . $ultim_atualizacao[1] ?></i><br>
                            <?php 
                                if($id_user === '363' || $id_user === '356' || $id_user === '352'){ 
                                    $nome_clt_txt = $row_user2['id_funcionario'];                                    
                                    echo'<form method="post" action="../action_txt_atividades.php">
                                         <input type="submit" name="btn_txt" id="btn_txt" class="botao" value="Ver Log de Atividades">
                                         <input type="hidden" name="nome_txt" id="nome_txt" value="'.$nome_clt_txt.'">
                                         </form>
                                    ';
                                }
                            ?>
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
                                                    $get_atividade = mysql_query("SELECT A.*,B.cod as cbo_cod,B.nome as cbo_nome FROM curso AS A
                                                                                        LEFT JOIN rh_cbo AS B ON (A.cbo_codigo = B.id_cbo)
                                                                                        WHERE A.id_curso = '$row[id_curso]'");
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

                                                    <b>Atividade:</b> <?= $atividade['id_curso'] ?> - <?= $atividade['nome'] ?> 
                                                    <?php
                                                    if (!empty($atividade['cbo_cod'])) {
                                                        echo '(' . $atividade['cbo_cod'] . ')';
                                                    }
                                                    ?><br>
                                                    <b>Unidade:</b> <?= $row['locacao'] ?><br>
                                                    <b>Sal�rio:</b>
                                                    <?php
                                                    if (!empty($atividade['salario'])) {
                                                        echo "R$ ";
                                                        echo number_format($atividade['salario'], 2, ',', '.');
                                                    } else {
                                                        echo "<i>N�o informado</i>";
                                                    }
                                                    ?>
                                                    &nbsp;&nbsp;<b>Tipo de Pagamento:</b> 
                                                    <?php
                                                    if (!empty($pg['tipopg'])) {
                                                        echo $pg['tipopg'];
                                                    } else {
                                                        echo "<i>N�o informado</i>";
                                                    }
                                                    ?><br>
                                                    <b>Ag�ncia:</b> 
                                                    <?php
                                                    if (!empty($row['agencia'])) {
                                                        echo $row['agencia'] . '' . $row['agencia_dv'];
                                                    } else {
                                                        echo "<i>N�o informado</i>";
                                                    }
                                                    ?>
                                                    &nbsp;&nbsp;<b>Conta:</b> 
                                                    <?php
                                                    if (!empty($row['conta'])) {
                                                        echo $row['conta'] . '' . $row['conta_dv'];
                                                    } else {
                                                        echo "<i>N�o informado</i>";
                                                    }
                                                    ?>
                                                    &nbsp;&nbsp;<b>Banco:</b>
                                                    <?php
                                                    if (!empty($nome_banco)) {
                                                        echo $nome_banco;
                                                    } else {
                                                        echo "<i>N�o informado</i>";
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
                                    echo "Sem Observa��es";
                                } else {
                                    echo "Observa��es<p>&nbsp;</p> $row[observacao]";
                                }

                                echo "<br />";

                                //FLAG NO CADASTRO DO CLT
                                /**
                                 * REINTEGRA��O 
                                 * FLAG NO RH_CLT 
                                 * E COLOCAR A RESCIS�O PARA 0
                                 */
                                if ($row['reintegracao']) {

                                    //VARI�VEIS
                                    $regiao = $_REQUEST['reg'];
                                    $id_clt = $_REQUEST['clt'];

                                    //VERIFICANDO SE EXISTE RESCIS�O COM STATUS 1, POR CONTA DE UMA REINTEGRA��O
                                    $query_rescisao = "SELECT * FROM rh_recisao AS A WHERE A.id_clt = '{$id_clt}' AND A.status = '0'";
                                    $sql_rescisao = mysql_query($query_rescisao) or die("Erro ao selecioar rescisao");
                                    $dados_rescisao = mysql_fetch_assoc($sql_rescisao);
                                    $rescisao = $dados_rescisao['id_recisao'];

                                    $link = str_replace('+', '--', encrypt("$regiao&$id_clt&$rescisao"));
                                    echo "<a href='recisao/nova_rescisao_2.php?enc={$link}&reitegracao=1' class='visualizar_rescisao'>Termo Rescis�rio</a>";
                                }
                                ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div id="observacoes">
                                <?php
                                if (empty($row['observacao'])) {
                                    echo "Sem Observa��es";
                                } else {
                                    echo "Observa��es<p>&nbsp;</p> $row[observacao]";
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
                        <td colspan="2"><h1><span>MENU DE EDI��O</span></h1></td>
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
                                    <?php if ($_COOKIE['logado'] != 395) { ?>
                                    <a href="abertura_processo.php?clt=<?= $row['0'] ?>&pro=<?= $id_pro ?>&pagina=<?= $pagina ?>&reg=<?= $id_reg ?>" class="botao">Abertura de processo</a>
                                    


                                    <?php
                                } }

                                if ($ACOES->verifica_permissoes(14) || $_COOKIE['logado'] == 395) {
                                    ?>
                                    <!-- linha 1 -->
                                    <a href="alter_clt.php?clt=<?= $row['0'] ?>&pro=<?= $id_pro ?>&pagina=<?= $pagina ?>" class="botao">Editar</a>

                                    <a href="formulario_dependentes_ir.php?clt=<?= $row['0'] ?>&pro=<?= $id_pro ?>&reg=<?= $id_reg ?>" class="botao">Dependentes IR</a>
<?php if ($_COOKIE['logado'] != 395) { ?>
                                    <a href="direction/index.php?clt=<?= $row['0'] ?>" class="botao">Mapa de Deslocamento</a>
<?php } ?>
                                    <?php
                                }
                                //VERIFICA SE O PROJETO EST� DESATIVADO
                                if ($row_pro['status_reg'] == 1) {


                                    if ($ACOES->verifica_permissoes(15) && $ativo) {
                                        ?>

                                        <a href="../tvsorrindo.php?bol=<?= $row['id_antigo'] ?>&clt=<?= $row['0'] ?>&pro=<?= $id_pro ?>&tipo=2" target="_blank" class="botao">TV Sorrindo</a>
                                        <?php
                                    }

                                    if ($ACOES->verifica_permissoes(78) && $ativo) {
                                        ?>
                                        <a href="salariofamilia/safami.php?pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>&clt=<?= $row['0'] ?>" target="_blank" class="botao"> Cad. do Sal�rio Fam�lia</a>
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
                                        <a href="../relatorios/gerar_relatorio.php?documento=ctps_impressao&clt=<?= $row['0'] ?>" class="botao">Entregar CTPS</a>    
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
                                    if ($ACOES->verifica_permissoes(20) && $_COOKIE['logado'] != 395) {
                                        ?>
                                        <a href="gerarPonto.php?regiao=<?= $id_reg ?>&pro=<?= $id_pro; ?>&id=<?= $id_user ?>&clt=<?= $row['0'] ?>" target="_blank" class="botao"  style="font-size:12px;">Gerar Apontamento</a>
                                        <?php
                                    }
                                    if ($ACOES->verifica_permissoes(20)) {
                                        ?>  
                                        <a href="contratoclt.php?id_reg=<?= $id_reg ?>&clt=<?= $row['0'] ?>" target="_blank" class="botao" style="font-size:12px;">Contrato de Trabalho</a>
                                        <a href="../relatorios/gerar_relatorio.php?documento=termo_sigilo_confidencialidade&clt=<?= $row['0'] ?>" target="_blank" class="botao" style="font-size:12px;">Termo de Sigilo</a>
                                        <a href="../rh_novaintra/cracha_provisorio_imprimir.php?id_clt=<?= $row['0'] ?>" target="_blank" class="botao" style="font-size:12px;">Cracha Provis�rio</a>
                                        <?php
                                    }
                                    if ($ACOES->verifica_permissoes(20)) {
                                        ?>  
                                        <a href="../relatorios/gerar_relatorio.php?documento=contrato_experiencia&clt=<?= $row['0'] ?>" target="_blank" class="botao" style="font-size:12px;">Contrato de Experi�ncia</a>

                                        <?php
                                    }
                                    if ($ACOES->verifica_permissoes(80) && $ativo) {
                                        ?>  
                                        <?php if ($_COOKIE['logado'] != 395) { ?>
                                        <a href="rh_transferencia.php?clt=<?= $row['0'] ?>" target="_blank" class="botao" style="font-size:12px;">Transfer�ncias</a>
                                        <?php
                                    } }
                                    //if($ACOES->verifica_permissoes(79)) {
                                    ?>  
                                    <a href="../registrodeempregado.php?bol=<?= $row['id_antigo'] ?>&pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>&clt=<?= $row['0'] ?>" target="_blank" class="botao">Registro de empregado</a></p>
                                <!--<a href="../registrodeempregado_pordata.php?bol=<?= $row['id_antigo'] ?>&pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>&clt=<?= $row['0'] ?>&tela=1" target="_blank" class="botao">Registro de empregado Por Data</a></p>-->
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
                                    <p><a href="salariofamilia/safami.php?pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>&clt=<?= $row['0'] ?>" target="_blank" class="botao">Benef�cios</a>
                                        <?php
                                    }
                                    if ($ACOES->verifica_permissoes(23) && $ativo) {
                                        ?>  
                                        <a href="vt/vt.php?pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>&clt=<?= $row['0'] ?>" target="_blank" class="botao">Vale Transporte</a>
                                        <?php
                                    }
                                    if ($ACOES->verifica_permissoes(24) && $ativo) {
                                        ?>  
                                        <a href="cartadereferencia.php?pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>&clt=<?= $row['0'] ?>" target="_blank" class="botao" style="font-size:12px;">Carta de Refer�ncia</a></p>
                                    <?php
                                }
                                if ($ACOES->verifica_permissoes(25) && $ativo) {
                                    ?>   
                                    <!-- linha 5 -->
                                    <!--<p><a href="../rh/notifica/advertencia.php?clt=<?= $row['0'] ?>&tab=bolsista<?= $id_pro ?>&pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>" target="_blank" class="botao">Advert�ncia</a>-->
                                    <?php if ($_COOKIE['logado'] != 395) { ?>
                                    <p><a href="../rh/notifica/advertencia.php?clt=<?= $row['0'] ?>&pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>" target="_blank" class="botao">Medidas Disciplinares</a>
                                    <?php } ?>
                                        <?php
                                    }
                                    if ($ACOES->verifica_permissoes(26) && $ativo) {
                                        ?>  
                                        <!--<a href="../rh/notifica/form_suspencao.php?clt=<?= $row['0'] ?>&tab=bolsista<?= $id_pro ?>&pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>" target="_blank" class="botao">Suspens�o</a>-->
                                        <?php
                                    }
                                    if ($ACOES->verifica_permissoes(27)) {
                                        ?>  
                                        <!--<a href="../relatorios/fichafinanceira_clt.php?reg=<?= $id_reg ?>&pro=<?= $id_pro ?>&tipo=2&tela=2&id=<?= $row['0'] ?>" target="_blank" class="botao">Ficha Financeira</a></p>-->
                                        <a href="log_ficha_financeira.php?reg=<?= $id_reg ?>&pro=<?= $id_pro ?>&tipo=2&tela=2&id=<?= $row['0'] ?>" target="_blank" class="botao">Ficha Financeira</a></p>
                                    <a href="../relatorios/gerar_relatorio.php?documento=kit&clt=<?= $row['0'] ?>&reg=<?= $id_reg ?>&pro=<?= $id_pro ?>" target="_blank" class="botao">Kit Admissional</a></p>
                                    <?php
                                }
                                if ($ACOES->verifica_permissoes(28)) {
                                    ?>  
                                    <input type="hidden" name="clt_prov" id="clt_prov" value="<?php echo $_GET['clt']; ?>"
                                           <p><input type="hidden"  name="indicativo" id="indicativo" value="<?php echo $indResPosMaternidade['indicativo']; ?>"/></p>
                                    <!-- linha 6 -->
        <?php // if(!in_array($row['status'],array('60','61','62','63','64','65','66','81','101'))) {      ?>
                                    <p>
                                    <?php if ($row['status'] == 10) { ?>
                                                <!--a href="docs/dispensa.php?clt=<?= $row['0'] ?>&tab=bolsista<?= $id_pro ?>&pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>" target="_blank" class="botao indResPosMat">Dispensa</a>
                                                <a href="docs/demissao.php?clt=<?= $row['0'] ?>&tab=bolsista<?= $id_pro ?>&pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>" target="_blank" class="botao indResPosMat">Demiss�o</a-->
                                        <?php if ($_COOKIE['logado'] != 395) { ?>
                                            <a href="docs/rescisao_configuracao.php?clt=<?= $row['0'] ?>" target="_blank" class="botao indResPosMat">Rescis�o</a>
                                            
        <?php } ?>  
        <?php } ?>  

                                        <?php
                                        //}
                                        if ($ACOES->verifica_permissoes(30)) {
                                            ?>  
                                            <a href="demissionalclt.php?pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>&clt=<?= $row['0'] ?>" target="_blank" class="botao" style="font-size:12px;">Exame Demissional</a></p>
                                            <?php
                                        }
                                    }
                                    if ($ACOES->verifica_permissoes(94)) {
                                        ?>  
                                    <?php if ($_COOKIE['logado'] != 395) { ?>
                                    <p><a href="estabilidade_provisoria/?pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>&id_clt=<?= $row['0'] ?>" target="_blank" class="botao" style="font-size:12px;">Estabilidade Provis�ria</a>
                                    <?php } ?>
                                        <a href="../relatorios/gerar_relatorio.php?documento=recibo_entrega_ctps&clt=<?= $row['0'] ?>" target="_blank" class="botao" style="font-size:10px;">Entrega de Carteira de Trabalho e Previdencia Social </a>
                                        <a href="../relatorios/gerar_relatorio.php?documento=declaracao_dependentes&clt=<?= $row['0'] ?>" target="_blank" class="botao" style="font-size:10px;">Declara��o de Dependentes</a></p>
        <?php
    }
    ?>

                                <p><a href="declaracao_jornada_semanal.php?pro=<?= $id_pro ?>&reg=<?= $id_reg ?>&clt=<?= $row['0'] ?>" target="_blank" class="botao" style="font-size:10px;">Declara��o de Jornada Semanal</a>
                                <!--<a href="compensacao_horas_trabalho.php?pro=<?= $id_pro ?>&reg=<?= $id_reg ?>&clt=<?= $row['0'] ?>" target="_blank" class="botao" style="font-size:10px;">Compensa��o Horas de Trabalho</a>-->
                                    <a href="acordo_compensacao.php?pro=<?= $id_pro ?>&reg=<?= $id_reg ?>&clt=<?= $row['0'] ?>" target="_blank" class="botao" style="font-size:12px;">Acordo de Compensa��o</a>
                                    <a href="../relatorios/gerar_relatorio.php?documento=termo_responsabilidade&clt=<?= $row['0'] ?>" target="_blank" class="botao" style="font-size:10px;">Termo de Responsabilidade</a></p>

<?php }   //FIM VERIFICA��O      ?>
<?php if ($ACOES->verifica_permissoes(90) && ($row['status'] >= 60 && $row['status'] != 200)) { ?>
                                <?php if ($_COOKIE['logado'] != 395) { ?>
                                <a href="cadastroclt.php?projeto=<?= $id_pro ?>&regiao=<?= $id_reg ?>&id=<?= $row['0'] ?>" target="_blank" class="botao">Recadastrar</a>
                                <?php } ?>
                            <?php } ?>
                            <?php if ($ACOES->verifica_permissoes(117) && ($row['status'] < 60 && $row['status'] != 200) && $_COOKIE['logado'] != 395) { ?>
                                <a href="alter_curso_clt.php?id_clt=<?= $row['0'] ?>" target="_blank" class="botao">EDITAR FUN��O</a>
                                <a href="alter_curso_clt.php?id_clt=<?= $row['0'] ?>" target="_blank" class="botao" style="font-size:10px;">Ficha Estabelecimento Sa�de</a>
                            <?php } ?>
                            <?php if ($ACOES->verifica_permissoes(90) && ($row['status'] >= 60 && $row['status'] != 200)) { ?>
                                <?php if ($_COOKIE['logado'] != 395) { ?>
                                <a href="reintegracaoclt.php?projeto=<?= $id_pro ?>&regiao=<?= $id_reg ?>&id=<?= $row['0'] ?>" target="_blank" class="botao">Reintegra��o</a>
                                <?php } ?>
                            <?php } ?>
                            <?php if ($ACOES->verifica_permissoes(117)) { ?>
                                <a href="retencao_inss.php?projeto=<?= $id_pro ?>&regiao=<?= $id_reg ?>&id=<?= $row['0'] ?>" target="_blank" class="botao">Reten��o INSS</a>
                            <?php } ?>
                            <?php if ($ACOES->verifica_permissoes(117)){ ?>
                                <a href="ficha_anotacoes.php?projeto=<?= $id_pro ?>&regiao=<?= $id_reg ?>&id=<?= $row['0'] ?>" target="_blank" class="botao">Ficha de Anota��es</a>
                            <?php } ?>
                                <a target="_blank" class="botao" href="domicilioProfissional.php?id=<?=$row['0']?>">Domic�lio Profissional</a>
                        </td>
                    </tr>

<?php if ($ACOES->verifica_permissoes(62)) { ?>  
                    <?php if ($_COOKIE['logado'] != 395) { ?>
                    <tr id="ancora_documentos">
                        <td colspan="2"><h1><span>UPLOAD DE DOCUMENTOS</span></h1></td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="2">
                            <?php 
                            
                                $sqlDocEx = "SELECT A.*, B.arquivo, DATE_FORMAT(A.data_cad, '%H:%i - %d/%m/%Y') AS data
                                                FROM documento_clt_anexo AS A
                                                        LEFT JOIN upload AS B ON (B.id_upload = A.id_upload)
                                                WHERE A.id_clt = $id_clt AND anexo_status = 1
                                                ORDER BY A.ordem ASC";
                                $queryDocEx = mysql_query($sqlDocEx);
                                
                                while($rowDocEx = mysql_fetch_assoc($queryDocEx)) {
                                    $arrDocEx[] = $rowDocEx;
                                    $arrFiles[] = $rowDocEx['id_upload'];
                                }
                            ?>
                            <?php if ($_COOKIE['logado'] != 395) { ?>
                            <form id="uploadDoc" enctype="multipart/form-data" action="action.upload.php" method="POST">
                                <input type="hidden" name="MAX_FILE_SIZE" value="9999999" />
                                <input type="hidden" name="id_clt" value="<?php echo $id_clt ?>" />
                                <input type="hidden" name="id_pro" value="<?php echo $id_pro ?>" />
                                <input type="hidden" name="id_ant" value="<?php echo $id_ant ?>" />
                                <input type="hidden" name="id_reg" value="<?php echo $id_reg ?>" />
                                
                                <b>Tipo de Documento:</b> <select name="tipoDoc" id="tipoDoc">
                                    <option value="-1">� Selecione �</option>
                                    <?php
                                        $qr_documentos = mysql_query("SELECT * FROM upload WHERE status_reg = '1'");
                                        while ($documento = mysql_fetch_assoc($qr_documentos)) {
                                        if(!in_array($documento['id_upload'], $arrFiles)) {
                                    ?>
                                            <option value="<?= $documento['id_upload'] ?>"><?= $documento['arquivo'] ?></option>
                                    <?php } } ?>
                                </select><br><br>
                                
                                <b>Selecione o arquivo:</b> <input name="userfile" id="userFile" type="file" />
                                <input class="hide" type="submit" id="envFile" name="enviar" value="Enviar Arquivo" />
                                
                            </form>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" id="documentosAnexos">
                            <?php // echo $msg = ($_REQUEST['upSuccess'] == true)? "Upload Realizado com Sucesso<br>" : null; ?>
                            <form id="deleteDoc" action="action.upload.php" method="post">
                                <input type="hidden" name="docToDelete" value="" />
                                <div id="docsUpados">
                                    <?php if(!empty($arrDocEx)){?>
                                        <table width="100%" border="0" cellpadding="4" cellspacing="0" style="font-size:13px;">
                                            <tr bgcolor="#dddddd">
                                                <th width="60%"><strong>DOCUMENTO</strong></th>
                                                <th width="30%"><strong>DATA</strong></th>
                                                <th width="10%"><strong></strong></th>
                                            </tr>
                                            <?php foreach($arrDocEx AS $key){ 
                                                if ($cont++ % 2) {
                                                    $color = "#fafafa";
                                                } else {
                                                    $color = "#f3f3f3";
                                                }
                                            ?>
                                            <tr id="doc<?= $key['anexo_id']; ?>" bgcolor="<?php echo $color; ?>">
                                                <td width="60%">
                                                    <?php echo $key['ordem'] . ' - ' . $key['arquivo']; ?>
                                                </td>
                                                <td width="30%" align="center"><?php echo $key['data'];?></td>
                                                <td class="crud" width="10%" align="center"><span style="cursor:pointer" data-crud="visualizar" data-key="<?= $_SERVER['SERVER_NAME'] .'/'. $key['anexo_diretorio'] . $key['anexo_nome']; ?>" title="Visualizar"><img src="../imagens/ver_anexo.gif" width="20" height="20"></span> <span style="cursor:pointer" data-crud="deletar" data-key="<?= $key['anexo_id']; ?>" title="Deletar"><img src="../imagens/excluir.png" width="20" height="20"></span></td>
                                            </tr>
                                            <?php } ?>
                                        </table>
                                    <?php } ?>
                                </div>
                            </form>
                        </td>
                    </tr>
<?php } ?>


<?php
if ($ACOES->verifica_permissoes(63)) {
    if ($ativo) {
        ?>      
                    <?php if ($_COOKIE['logado'] != 395) { ?>
                            <tr>
                                <td colspan="2"><h1><span>ENCAMINHAMENTO DE CONTA</span></h1></td>
                            </tr>
                    <?php } ?>
                            <?php if ($_COOKIE['logado'] != 395) { ?>
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
                            <?php if ($_COOKIE['logado'] != 395) { ?>
                            <tr>
                                <td colspan="2">
                                    <form action="../declarabancos.php" method="post" name="form1" target="_blank">
                                        <b>Escolha o Banco:</b>&nbsp;&nbsp;
                                        <select name="banco" id="banco">
                                            <option value="hsbc">Bradesco</option>
                                        </select>
                                        <input type="submit" name="btn_hsbc" id="btn_hsbc" value="Gerar Encaminhamento de Conta">
                                        <input type="hidden" name="tipo" id="tipo" value="2">
                                        <input type="hidden" name="bolsista" id="bolsista" value="<?= $row['0'] ?>">
                                        <input type="hidden" name="regiao" id="regiao" value="<?= $id_reg ?>">
                                    </form> 
                                </td>
                            </tr>
                            <?php } ?>
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
        if (($row_docs['documento'] == 'Inscri��o no PIS') and ( $emissao == true)) {
            $img = "<img src='../imagens/assinado.gif' width='15' height='17' align='absmiddle'>";
            echo "<td class='linha' align='center'>$img</td>";
        } elseif (($row_docs['documento'] != 'Inscri��o no PIS') or ( $emissao == false)) {
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
    $qr_historico_eventos = mysql_query("SELECT * FROM rh_eventos WHERE id_clt = '$id_clt' AND id_regiao = '$id_reg' AND id_projeto = '$id_pro' AND nome_status!='' AND status = '1' AND cod_status != 40;") or die(mysql_error());

    $qr_historico_ferias = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$id_clt' AND regiao = '$id_reg' AND projeto = '$id_pro' AND status = '1' ") or die(mysql_error());

    $qr_historico_rescisao = mysql_query("SELECT * FROM rh_recisao WHERE id_clt = '$id_clt' AND id_regiao = '$id_reg' AND id_projeto = '$id_pro' AND status = '1' ") or die(mysql_error());
    $qr_historico_clt = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$id_clt' AND id_regiao = '$id_reg' AND id_projeto = '$id_pro' AND status!=0") or die(mysql_error());

    while ($row_clt = mysql_fetch_assoc($qr_historico_clt)):

        $historico[] = array(
            'nome' => 'Admiss�o',
            'inicio' => $row_clt['data_entrada'],
            'fim' => '',
            'duracao' => '',
            'id_evento' => '1',
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
            'nome' => 'F�rias',
            'inicio' => $row_ferias['data_ini'],
            'fim' => $row_ferias['data_fim'],
            'duracao' => ($row_ferias['data_fim'] - $row_ferias['data_ini']),
            'id_evento' => $row_ferias['id_ferias'],
            'status' => '',
        );

    endwhile;

    while ($row_recisao = mysql_fetch_assoc($qr_historico_rescisao)):

        $historico[] = array(
            'nome' => 'Rescis�o',
            'inicio' => $row_recisao['data_demi'],
            'fim' => '',
            'duracao' => '',
            'id_evento' => $row_recisao['id_recisao'],
            'status' => '',
        );
    endwhile;

    $cod_status = array(20, 50, 51);
    
    foreach ($historico as $chave => $inicio) {
        ?>
                                    <div id="">
                                        
                                    </div>
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
                                                <?php 
                                                    
                                                    $type = $historico[$chave]['nome'];
                                                    $id = $historico[$chave]['id_evento'];
                                                    switch($type){
                                                        case 'F�rias':$type = 3; break;
                                                        case 'Rescis�o':$type = 4; break;
                                                        case 'Admiss�o':$type = 1; break;
                                                        default:$type = 2; break;
                                                    }

                                                    $sql = "SELECT * FROM eventos_anexos WHERE id_tipo_evento = $type AND id_evento = $id AND status = 1";
                                                    $query = mysql_query($sql);
                                                    $arrEventos = mysql_fetch_assoc($query);
                                                    $rows = mysql_num_rows($query);
                                                    if($rows == 0 && $_COOKIE['logado'] != 395) { ?>) { 
                                                ?>
                                                        <img title="Anexar Documento" data-key="upload" data-id="<?=$historico[$chave]['id_evento']?>" data-type="<?=$historico[$chave]['nome']?>" src="../img_menu_principal/anexo.png" class="icon-anexo">
                                                    <?php } else { ?>
                                                        <?php if ($_COOKIE['logado'] != 395) { ?>
                                                        <img title="Excluir Documento " data-key="deletar" data-idanexo="<?= $arrEventos['id_anexo'] ?>" data-id="<?=$historico[$chave]['id_evento']?>" data-type="<?=$historico[$chave]['nome']?>" src="../imagens/excluir.png" class="icon-anexo" />
                                                        <?php } ?>
                                                    <?php } ?>
                                            </td>

                                            <td style="text-align: center;">
                                                <?php if($rows == 0) { ?>
                                                    <img src="../imagens/ver_anexo.gif" class="icon-anexo disable">
                                                <?php } else { ?>
                                                    <img title="Visualizar Documento" data-key="visualizar" data-url="<?= $arrEventos['caminho'].'/'.$arrEventos['doc_name'].'.'.$arrEventos['doc_type']; ?>" data-id="<?=$historico[$chave]['id_evento']?>" data-type="<?=$historico[$chave]['nome']?>" src="../imagens/ver_anexo.gif" class="icon-anexo">
                                                <?php } ?>                                            </td>
                                        </tr>
                                            <!--<td style="text-align: center;">
                                                <?php if (!empty($historico[$chave]['status']) && in_array($historico[$chave]['status'], $cod_status)) { ?>
                                                    <a href="#eventos" data-id="<?= $historico[$chave]['id_evento'] ?>" class="anexar-atestado" data-click="1"><img src="../img_menu_principal/anexo.png" class="icon-anexo"></a>
                                                <?php  } else { ?>
                                                    <img src="../img_menu_principal/anexo.png" class="icon-anexo disable">
                                                <?php } ?>
                                            </td>

                                            <td style="text-align: center;">
                                                <?php if (!empty($historico[$chave]['status']) && in_array($historico[$chave]['status'], $cod_status)) { ?>
                                                    <a href="lista_AnexoEventos.php?id=<?= $historico[$chave]['id_evento'] ?>"><img src="../imagens/ver_anexo.gif" class="icon-anexo"></a>
                                                <?php } else { ?>
                                                    <img src="../imagens/ver_anexo.gif" class="icon-anexo disable">
                                                <?php } ?>
                                            </td>-->
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
                            <td colspan="2"><h1><a name="eventos"><span>HISTORICO DE FERIAS</span></a></h1></td>
                        </tr>
                        <td colspan="2">
                            <table width="100%" border="0" cellpadding="4" cellspacing="0" style="font-size:13px;">
                                <thead>
                                    <tr bgcolor="#dddddd">
                                        <td>Periodo Aquisitivo</td>
                                        <td>Per�odo De F�rias</td>
                                        <td>PDF</td>
                                    </tr>
                                </thead>
                                <tbody>

    <?php foreach ($listaFeria['registros'] as $row_ferias) { ?>

                                        <tr>
                                            <td><?php echo "{$row_ferias['data_aquisitivo_iniBR']} &agrave; {$row_ferias['data_aquisitivo_fimBR']}" ?></td>
                                            <td><?= "{$row_ferias['data_iniBR']} &agrave; {$row_ferias['data_fimBR']}" ?></td>
                                            <td class="text-center">
                                                <a href="/intranet/?class=ferias/processar&method=telaAvisoFerias&id_ferias=<?= $row_ferias['id_ferias'] ?>" class="btn btn-default btn-xs" title="Ver Aviso de F&eacute;rias" target="_blank">
                                                    <img src="../imagens/icons/att-generic.png" style="width: 1.5em; height: 1.5em;" alt="Ver Aviso de F&eacute;rias"
                                                         <i class="text-danger fa fa-file-pdf-o" alt="Ver PDF de F&eacute;rias Novas"></i>
                                                </a>
                                                <a href="/intranet/rh/arquivos/ferias/ferias_<?= $row_ferias['id_clt'] ?>_<?= $row_ferias['id_ferias'] ?>.pdf" class="btn btn-default btn-xs" title="Ver PDF" target="_blank">
                                                    <!--<img src="../../imagens/icons/att-pdf.png" style="width: 1.5em; height: 1.5em;" alt="Ver PDF">-->
                                                    <img src="../imagens/icons/att-pdf.png" style="width: 1.5em; height: 1.5em;" alt="Ver PDF"></i>
                                                </a> 
                                <!--                    <a href="/intranet/rh_novaintra/ferias/arquivos/ferias_<?= $row_ferias['id_clt'] ?>_<?= $row_ferias['id_ferias'] ?>.pdf" class="btn btn-default btn-xs" title="Ver PDF de F�rias Novas" target="_blank">
                                                    img src="../../imagens/icons/att-pdf.png" style="width: 1.5em; height: 1.5em;" alt="Ver PDF"
                                                    <i class="text-danger fa fa-file-pdf-o" alt="Ver PDF de F�rias Novas"></i>
                                                </a>-->
                                                <a href="/intranet/?class=ferias/processar&method=gerarPdf&id_ferias=<?= $row_ferias['id_ferias'] ?>&value=pdf" class="btn btn-default btn-xs" title="Ver PDF de F&eacute;rias Novas" target="_blank">
                                                    <img src="../imagens/icons/att-pdf.png" style="width: 1.5em; height: 1.5em;" alt="Ver PDF"
                                                         <i class="text-danger fa fa-file-pdf-o" alt="Ver PDF de F�rias Novas"></i>
                                                </a>
                                            </td>
        <?php if ($ACOES->verifica_permissoes(87)) { ?>
                                                <td class="text-center">
                                                    <!--<button type="button" class="btn btn-danger btn-xs desprocessar_ferias" data-ferias="<?= $row_ferias['id_ferias'] ?>" data-ferias="<?= $row_ferias['clt'] ?>" data-toggle="tooltip" title="Desprocessar F�rias"><i class="fa fa-trash-o"></i></button>-->
                                                    <a href="../../rh/ferias/rh_ferias_desprocessar.php?clt=<?php echo $row_ferias['clt']; ?>&ferias=<?php echo $row_ferias['id_ferias']; ?>&tela=1" class="btn btn-danger btn-xs"  data-toggle="tooltip" title="Desprocessar F�rias"><i class="fa fa-trash-o"></i></a>
                                                    <!--<a href="rh_ferias_desprocessar.php?clt=<?php echo $id_clt; ?>&ferias=<?php echo $id_ferias; ?>&tela=1" title="Desprocessar F�rias"><img src="../imagensrh/deletar.gif" /></a>-->
                                                </td>

        <?php } ?>

                                        </tr>
                                        <?php } ?>
                                </tbody>
                            </table>

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
                <p class="left"><img style="position:relative; top:7px;" src="../imagens/logomaster<?= $Master ?>.gif" width="66" height="46"> <b><?= $master['razao'] ?></b>&nbsp;&nbsp;Acesso Restrito � Funcion&aacute;rios</p>
                <p class="right"><br><br><a href="#corpo">Subir ao topo</a></p>
                <div class="clear"></div>
            </div>
        </div>
        <div id="lightBox" class="hide"></div>
            <div id="eventoDocs" class="hide">
                <form id="evDocsUpload" method="post" action="actionUploadEventos.php" enctype="multipart/form-data">
                    <input type="hidden" name="evDocType" id="evDocType" value="" />
                    <input type="hidden" name="evDocId" id="evDocId" value="" />
                    <input type="hidden" name="idClt" id="idClt" value="<?= $id_clt; ?>" />
                    <input type="hidden" name="regClt" id="regClt" value="<?= $id_reg; ?>" />
                    <input type="hidden" name="proClt" id="proClt" value="<?= $id_pro; ?>" />
                    
                    <div id="evDocsHead"><p id="evDocsTitle">Selecione o arquivo para upload</p></div>
                    <br>
                    <input type="file" name="evDocsFile" id="evDocsFile" />
                    <button type="submit" name="enviar">Enviar Arquivo</button>
                </form>
        </div>
        <script src="../resources/dropzone/dropzone.js"></script>
        <script type="text/javascript">
            var Accordion1 = new Spry.Widget.Accordion("Accordion1", {enableAnimation: false, useFixedPanelHeights: false, defaultPanel: -1});
        </script>
    </body>
</html>