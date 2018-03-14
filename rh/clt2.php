<?php
if(empty($_COOKIE['logado'])){
	print 'Efetue o Login<br><a href="login.php">Logar</a>';
	exit;
}

header('Content-Type:text/html; charset=ISO-8859-1', true);

include('../conn.php');
include('../wfunction.php');
include('../funcoes.php');
include('../classes/abreviacao.php');
include('../classes/formato_data.php');

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$id_regiao = $usuario['id_regiao'];

$qr_user = mysql_query("SELECT * FROM funcionario WHERE id_Funcionario= '$_COOKIE[logado]'");
$row_user = mysql_fetch_assoc($qr_user);

$qr_master  = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]' ");
$row_master =  mysql_fetch_assoc($qr_master);

$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Edição de Participantes");
$breadcrumb_pages = array("Gestão de RH"=>"../rh");

if(!empty($_REQUEST['id'])){
    $id = $_REQUEST['id'];

    switch($id) {
    case 1:

        $busca  = $_REQUEST['procura'];
        $id_regiao = $_REQUEST['regiao'];
        $qr_busca    = mysql_query("SELECT id_clt, nome, id_regiao, id_projeto FROM rh_clt WHERE id_regiao = '$id_regiao' AND nome LIKE '%$busca%' AND status != '' AND status != '0'");
        $total_busca = mysql_num_rows($qr_busca);

        if(empty($total_busca) or empty($busca) or strlen($busca) == 1 or strlen($busca) == 2) {
            echo '<tr><td><a href="#" style="color:#C30; text-decoration:none; display:block; padding:3px; padding-left:5px;">Sua busca n&atilde;o retornou resultado</a></td></tr>';
        } else {
            while($row_busca = mysql_fetch_array($qr_busca)) {
                echo '
                <tr><td><a class="busca" href="ver_clt.php?reg='.$row_busca['id_regiao'].'&clt='.$row_busca[0].'&pro='.$row_busca['id_projeto'].'&pagina=clt"
                          onclick="document.all.ttdiv.style.display=none; 
                          document.all.username.value='.$row_busca['nome'].'">'.$row_busca['nome'].'</a></td></tr>';
            }
        }
        break;
    }
    exit;
}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Edição de Participantes</title>
        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>

        <div class="container">

            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS</h2></div>
                    <h3>Edição de Participantes</h3>
                </div>
            </div>
            

            <div class="row">
                <div class="col-lg-10">
                    <form id="form1" method="post">
                        <input type="hidden" name="home" id="home" value="" />
                        <input type="text" name="username" placeholder="Insira o nome do participante" onKeyUp="ajaxFunction();" id="username" class="form-control pull-left hidden-xs" />
                    </form>
                </div>
                <div class="col-lg-2">
                    <a class="btn btn-success pull-right" href="cadastroclt.php?regiao=<?=$id_regiao?>&pagina=clt"><i class="fa fa-plus"></i> Novo Cadastro</a>
                </div>
                <div class="col-lg-5" style="z-index: 10;">
                    <table class="table table-bordered" id="ttdiv" style="position: absolute; display:none; background-color: #FFF;">
                        <tbody id="tbdiv">
                            <!--tr>
                                <td><span style="font-size:13px;" id="spantt"></span></td>
                            </tr-->
                        </tbody>
                    </table>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-lg-12">
                    <?php 
                    //Eventos que exibirei
                    //$eventos = array('= 40','= 200','= 20','= 50','= 90','= 10','>= 60 AND codigo != 90 AND codigo != 200');
                    $eventos = array('= 200','= 20','= 30','= 40','= 50','= 51','= 52','= 70','= 80','= 90','= 100','= 110','= 10','= 60','= 61','= 62','= 63','= 64','= 65','= 81','= 101');
                    $eventos_rescisao = array('60','61','62','63','64','65','81','101');

                    $query = mysql_query("
                    SELECT b.nome AS nome_projeto, a.id_projeto, a.id_curso, a.data_entrada, a.id_clt, a.status, a.nome, a.assinatura, a.distrato, a.outros, a.campo3, a.locacao, a.foto, a.observacao, c.especifica
                    FROM rh_clt a
                    LEFT JOIN projeto b ON a.id_projeto = b.id_projeto
                    LEFT JOIN rhstatus c ON c.codigo = a.status
                    WHERE a.id_regiao = '$id_regiao' AND b.status_reg = '1' AND c.status_reg = '1' AND a.status IN(200,20,30,40,50,51,52,70,80,90,100,110,10,60,61,62,63,64,65,81,101)
                    ORDER BY c.especifica, b.id_projeto, a.nome ASC");
                    //Loop dos Eventos
                    while($row = mysql_fetch_assoc($query)) {
                        // Consulta dos Participantes
                        $array["{$row['status']}*{$row['especifica']}"][$row['nome_projeto']][] = $row;
                        $arTotais[$row['status']][] = $row[id_clt];
                    }
                    //echo "<pre>";print_r($array);exit;
                    $primeiro = 0;
                    foreach ($array as $k => $v) { 
                        $key = explode('*', $k);
                        $primeiro++; //echo "<pre>";print_r($v);exit;?>
                        <div class="panel panel-primary">
                            <div class="panel-heading pointer show" data-key=".<?=$key[0]?>">
                                <h3 class="panel-title"><?=$key[1]?> (<?=count($arTotais[$key[0]])?>)</h3>
                            </div>
                            <div class="panel-body table-responsive <?=$key[0]?>" <?php if($primeiro != 1) { echo 'style="display:none;"'; } ?>>
                                <?php $count = 0;
                                foreach ($v as $k2 => $v2) { //echo '<pre>';print_r($v2);exit; ?>
                                    <h4 class="td_show <?=$v2[$count]['status']?>">
                                        <i class="fa fa-chevron-right"></i> <?=$k2?> <!--span class="pull-right"><a class="btn btn-success" href="javascript:;" onclick="tableToExcel('tbRelatorio3315', 'Relatório')"><i class="fa fa-file-excel-o"></i> Exportar para Excel</a></span-->
                                    </h4>
                                    <table class="table table-striped table-hover <?=$v2[$count]['status']?>">
                                        <thead>
                                            <tr class="novo_tr">
                                                <th>&nbsp;</th>
                                                <th width="5%" align="center">COD</th>
                                                <th width="30%">&nbsp;&nbsp;NOME</th>
                                                <th width="25%">&nbsp;&nbsp;CARGO</th>
                                                <th width="20%" align="center"><?php if($v3['status'] == 10) { echo 'ENTRADA'; } else { echo 'DURA&Ccedil;&Atilde;O'; } ?></th>
                                                <th width="10%" align="center">PONTO</th>
                                                <th width="10%" align="center">DOCUMENTOS</th>
                                            </tr>
                                        </thead>
                                        <?php 
                                        $count++;
                                        foreach ($v2 as $k3 => $v3) { 
                                            $qr_curso     = mysql_query("SELECT * FROM curso WHERE id_curso = '$v3[id_curso]'");
                                            $row_curso    = mysql_fetch_array($qr_curso);

                                            $qr_evento    = mysql_query("SELECT * FROM rh_eventos WHERE id_clt = '$v3[id_clt]' AND cod_status = '$v3[status]' ORDER BY id_evento DESC");
                                            $row_evento   = mysql_fetch_array($qr_evento);

                                            $qr_ferias    = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$v3[id_clt]' ORDER BY id_ferias DESC");
                                            $row_ferias   = mysql_fetch_array($qr_ferias);

                                            $qr_rescisao  = mysql_query("SELECT * FROM rh_recisao WHERE id_clt = '$v3[id_clt]' ORDER BY id_recisao DESC");
                                            $row_rescisao = mysql_fetch_array($qr_rescisao);

                                            if($v3['assinatura'] == 1) {
                                                    $botao1 = '<a href="ver_tudo.php?id=18&projeto='.$v3[id_projeto].'&regiao='.$id_regiao.'&ass=0&bolsista='.$v3[id_clt].'&tipo=1&tab=rh_clt" title="Remover ASSINATURA do Contrato de '.$v3['nome'].'">
                                                                              <img src="../imagens/assinado.gif" alt="Contrato">
                                                                       </a>';
                                            } else {
                                                    $botao1 = '<a href="ver_tudo.php?id=18&projeto='.$v3[id_projeto].'&regiao='.$id_regiao.'&ass=1&bolsista='.$v3[id_clt].'&tipo=1&tab=rh_clt" title="Alterar o Contrato para ASSINADO de '.$v3['nome'].'">
                                                                              <img src="../imagens/naoassinado.gif" alt="Contrato">
                                                                       </a>';
                                            }

                                            if($v3['distrato'] == 1) {
                                                    $botao2 = '<a href="ver_tudo.php?id=18&projeto='.$v3[id_projeto].'&regiao='.$id_regiao.'&ass=0&bolsista='.$v3[id_clt].'&tipo=2&tab=rh_clt" title="Remover ASSINATURA do Distrato de '.$v3['nome'].'">
                                                                    <img src="../imagens/assinado.gif" alt="Distrato">
                                                                </a>';
                                            } else {
                                                    $botao2 = '<a href="ver_tudo.php?id=18&projeto='.$v3[id_projeto].'&regiao='.$id_regiao.'&ass=1&bolsista='.$v3[id_clt].'&tipo=2&tab=rh_clt" title="Alterar o Distrato para ASSINADO de '.$v3['nome'].'">
                                                                              <img src="../imagens/naoassinado.gif" alt="Distrato">
                                                                       </a>';
                                            }

                                            if($v3['outros'] == 1) {
                                                    $botao3 = '<a href="ver_tudo.php?id=18&projeto='.$v3[id_projeto].'&regiao='.$id_regiao.'&ass=0&bolsista='.$v3[id_clt].'&tipo=3&tab=rh_clt" title="Remover ASSINATURA de Outros Documentos de '.$v3['nome'].'">
                                                                              <img src="../imagens/assinado.gif" alt="Outros Documentos">
                                                                       </a>';
                                            } else {
                                                    $botao3 = '<a href="ver_tudo.php?id=18&projeto='.$v3[id_projeto].'&regiao='.$id_regiao.'&ass=1&bolsista='.$v3[id_clt].'&tipo=3&tab=rh_clt" title="Alterar Outros Documentos para ASSINADO de '.$v3['nome'].'">
                                                                              <img src="../imagens/naoassinado.gif" alt="Outros Documentos">
                                                                       </a>';
                                            }

                                            if(strstr($v3['campo3'],'INSERIR')) { 
                                                    $classe = 'amarelo'; $classe = 'warning'; 
                                            } elseif(strstr($v3['locacao'],'A CONFIRMAR')) { 
                                                    $classe = 'vermelho'; $classe = 'danger'; 
                                            } elseif($v3['foto'] == '1') {
                                                    $classe = 'verde_foto'; $classe = 'success'; 
                                            } elseif(!empty($v3['observacao'])) {
                                                    $classe = 'amarelo'; $classe = 'warning'; 
                                                    $observacao = 'title="Observações: '.$v3['observacao'].'"';
                                            } else {
                                                    $classe = 'verde';$classe = 'info'; 
                                            }
                                        ?>
                                            <tr>    
                                                <td class="<?=$classe?>">&nbsp;</td>
                                                <td><?=$v3[id_clt]?></td>
                                                <td align="left">
                                                    <a href="ver_clt.php?reg=<?=$id_regiao?>&clt=<?=$v3[id_clt]?>&pro=<?=$v3[id_projeto]?>&pagina=clt" class="participante" title="Editar cadastro de <?=$v3['nome']?>">
                                                        <?=abreviacao($v3['nome'], 4, 1)?>
                                                    </a>
                                                </td>
                                                <td align="left">&nbsp;&nbsp;<?=str_replace('CAPACITANDO EM', '', $row_curso['nome'])?></td>
                                                <td>
                                                    <?php if($v3['status'] == 40) {
                                                        echo formato_brasileiro($row_ferias['data_ini']).' - '.formato_brasileiro($row_ferias['data_fim']);
                                                    } elseif(in_array($v3['status'],$eventos_rescisao)) {
                                                        echo formato_brasileiro($row_rescisao['data_adm']).' - '.formato_brasileiro($row_rescisao['data_demi']);
                                                    } elseif($v3['status'] != 10) {
                                                        echo formato_brasileiro($row_evento['data']).' - '.formato_brasileiro($row_evento['data_retorno']);
                                                    } else {
                                                        echo formato_brasileiro($v3['data_entrada']);
                                                    } ?>
                                                </td>
                                                <td><a href="../folha_ponto.php?id=2&unidade=&regiao=<?=$id_regiao?>&pro=<?=$v3[id_projeto]?>&id_bol=<?=$v3[id_clt]?>&tipo=clt&caminho=0" title="Gerar folha de ponto para <?=$v3['nome']?>">Gerar</a></td>
                                                <td><?=$botao1.' '.$botao2.' '.$botao3?></td>
                                            </tr>
                                        <?php } ?>
                                        </table>
                                <?php } ?>
                            </div>
                        </div><?php
                    } ?>
                </div>
            </div>
        <?php include_once '../template/footer.php'; ?>
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
    </body>
</html>
<script type="text/javascript">
$().ready(function(){
    $('.show').click(function() {
        $('.panel-body').hide();
        var div = $(this).data('key');
        console.log(div);
        $(div).show();
        $(this).addClass('ativo');
        $('.show').not(this).removeClass('ativo');
    });
    /*$('#botao_localizacao').click(function() {
        $('#localizacao').show();
        $('#botao_localizacao').hide();
    });
    $('#fecha_localizacao').click(function() {
        $('#localizacao').hide();
        $('#botao_localizacao').show();
    });*/
    $('.participante').click(function(){
        console.log($(this).attr("href"));
    });
});

function ajaxFunction(){
    var xmlHttp;
    try {
        xmlHttp=new XMLHttpRequest();
    } catch (e) {
        try {
            xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
            try {
                xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {
                alert("Your browser does not support AJAX!");
                return false;
            }
        }
    }
    xmlHttp.onreadystatechange=function() {
        if(document.getElementById('username').value == ''){
            document.all.ttdiv.style.display="none";
	}else{
            document.all.ttdiv.style.display="";
            if(xmlHttp.readyState==3){
                document.all.tbdiv.innerHTML="<div align='center' style='background-color:#5C7E59'><img src='../imagens/carregando/CIRCLE_BALL.gif' align='absmiddle'>Aguarde</div>";
            }else if(xmlHttp.readyState==4){
                document.all.tbdiv.innerHTML=xmlHttp.responseText;
            }
        }
    }

    var enviando = escape(document.getElementById('username').value);
    xmlHttp.open("GET",'clt2.php?procura=' + enviando + '&id=1&regiao=<?=$id_regiao?>',true);
    xmlHttp.send(null);
  
}
</script>