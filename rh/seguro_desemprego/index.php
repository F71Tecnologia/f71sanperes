<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

if (isset($_REQUEST['download']) && !empty($_REQUEST['download'])) {
    $file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'arquivos' . DIRECTORY_SEPARATOR . $_REQUEST['download'];
    $name_file_download = isset($_REQUEST['name_file']) ? $_REQUEST['name_file'] : $_REQUEST['download'];
    header("Content-Type: application/save");
    header("Content-Length:" . filesize($file));
    header('Content-Disposition: attachment; filename="' . $name_file_download . '"');
    header("Content-Transfer-Encoding: binary");
    header('Expires: 0');
    header('Pragma: no-cache');
    $fp = fopen("$file", "r");
    fpassthru($fp);
    fclose($fp);
    exit();
}

include "../../conn.php";
include "../../wfunction.php";
include('../../classes/global.php');
include 'classes/DaoSd.class.php';


$dao = new DaoSd();

$usuario = carregaUsuario();

$qr_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]' ") or die(mysql_error());
$row_user = mysql_fetch_assoc($qr_user);


$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_assoc($qr_master);


$optMeses = mesesArray();
$optAnos = anosArray();
$optRegiao = getRegioes();

$mesSel = isset($_POST['mes']) ? str_pad($_POST['mes'], 2, '0', STR_PAD_LEFT) : date('m');
$anoSel = isset($_POST['ano']) ? $_POST['ano'] : date('Y');
//$regiaoSel = isset($_POST['regiao']) ? $_POST['regiao'] : $usuario['id_regiao'];
//$regiaoSel = isset($_POST['regiao']) ? $_POST['regiao'] : FALSE;
$projetoSel = isset($_POST['projeto']) ? $_POST['projeto'] : FALSE;
$cnpj_master = isset($_POST['cnpj_master']) ? $_POST['cnpj_master'] : FALSE;
if ($cnpj_master == 'on') {
    $arr['tipo_cnpj'] = 'master';
}
if (isset($_POST['acao']) && $_POST['acao'] == 'relatorio') {
    $cond_projeto = (empty($projetoSel) || $projetoSel == "-1") ? '' : "AND A.id_projeto='$projetoSel'";
    $cond_regiao = (empty($regiaoSel)) ? '' : "AND A.id_regiao='$regiaoSel'";
    $arr = array(
        'cond_projeto'=>$cond_projeto, 
        'cond_regiao'=>$cond_regiao, 
        'anoSel'=>$anoSel, 
        'mesSel'=>$mesSel
    );
    $dados = $dao->listar($arr);
}

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Seguro Desemprego");
$breadcrumb_pages = array("Gestão de RH" => "../");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>:: Intranet :: Seguro Desemprego</title>
        
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <script>
            
        </script>
    </head>

    <body>
        <?php include("../../template/navbar_default.php"); ?>
        
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="glyphicon glyphicon-user"></span> - Recursos Humanos<small> - Seguro Desemprego</small></h2></div>
            
            <form action="" method="post" name="form1" class="form-horizontal top-margin1">
                <div class="panel panel-default">
                    <div class="panel-heading"><?php echo $master['nome']; ?> <input type="hidden" id="master" name="master" value="<?=$usuario['id_master'];?>"></div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Projeto: </label>
                            <div class="col-lg-9">
                               <?php echo montaSelect(GlobalClass::carregaProjetos($usuario['id_master'],array('-1'=>'- Todos -'), false), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class'=>'form-control')) ;   ?>                               
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Competência: </label>
                            <div class="col-lg-4">
                                <div class="input-daterange input-group">
                                    <?php echo montaSelect($optMeses, $mesSel, array('name' => 'mes', 'id' => 'mes', 'class' => 'input form-control')); ?>
                                    <span class="input-group-addon de">de</span>
                                    <?php echo montaSelect($optAnos, $anoSel, array('name' => "ano", 'id' => 'ano', 'class' => 'input form-control')); ?>
                                </div>
                            </div>                            
                        </div>       
                        
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">CNPJ Master: </label>
                            <div class="col-lg-4">                                 
                                <input type="checkbox" <?= (isset($cnpj_master) && ($cnpj_master == 'on')) ? ' checked="checked" ' : ''; ?> name="cnpj_master" />
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <input type="hidden" name="acao" value="relatorio"/>
                        <input type="submit" name="filtrar" value="Filtrar" id="filtrar" class="btn btn-primary"/>
                    </div>
                </div>
                
                <div id="resp"></div>
                
                <?php if(!empty($_REQUEST['filtrar'])){ ?>
                <?php if (isset($dados) && !empty($dados)) { ?>
                    <table class="table table-striped table-hover table-condensed table-bordered essatb" id="folha"> 
                        <thead>
                            <tr class="bg-primary valign-middle">
                                <th>
                                    <input type="checkbox" id="check_all" value="1" name="check_all"/>
                                </th>
                                <th>Cod</th>
                                <th>Nome</th>
                                <th>Data de admissão</th>
                                <th>Data de demissão</th>
                                <th>Projeto</th>
                                <!--<th>Imprimir</th>-->
                                <th>Importação</th>
                            </tr>
                        </thead>
                        <?php
                        foreach ($dados as $funcionario) {
                            ?>
                            <tbody>
                                <tr>
                                    <td><input type="checkbox" class="checks" name="id_clt[]" value="<?= $funcionario['id_clt']; ?>"/></td>
                                    <td><?= $funcionario['id_clt']; ?></td>
                                    <td><?= $funcionario['nome']; ?></td>
                                    <td><?= $funcionario['data_adm_f']; ?></td>
                                    <td><?= $funcionario['data_demi_f']; ?></td>
                                    <td><?= $funcionario['nome_projeto']; ?></td>
                                    <!--<td style="text-align:center;"><a href="form_sd.php?id=<?php //$funcionario['id_clt']; ?>" target="_blank" class="btn btn-default"><i class="fa fa-print"></i> Imprimir</a></td>-->
                                    <td class="text-center">
                                        <a href="javascript:;" onclick="criar_arquivo(<?= $mesSel; ?>, <?= $anoSel; ?>, <?= $funcionario['id_clt']; ?>)" id="link_<?= $funcionario['id_clt']; ?>" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i> Gerar</a>
                                    </td>
                                </tr>
                            </tbody>
                            <?php
                        }
                        ?>
                    </table>
                <?php } else { ?>
                    <div id="item0">
                        <div class="alert alert-danger">
                            <p>Não há registros de rescisões nesta competência.</p>
                        </div>                              
                    </div>
                <?php } 
                } ?>

                <div id="debug_sd"></div>

                <?php if (!empty($dados)) { ?>
                    <div style="text-align: right;" >
                        <div id="resp_sd"></div>
                        <br>
                        <input type="button" class="btn btn-primary btn-sm" onclick="gerar_todos(<?= $mesSel; ?>, <?= $anoSel; ?>);" id="bt_gerar_sd" name="gerar" value="Gerar Selecionados" />
                    </div>
                <?php } ?>
            </form>                        
            
            <?php include('../../template/footer.php'); ?>
        </div>                        
        
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="....//js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script>
            $(function () {
                $('#regiao').ajaxGetJson("../../methods.php", {method: "carregaProjetos"}, null, "projeto");
            });
            
            $("#check_all").click(function(){
                $('.checks').prop('checked', this.checked);                
            });
            
            $("#check_all").prop('checked',false).trigger('click');
            
            function criar_arquivo(mes, ano, id_clt) {
                $('#link_' + id_clt).after('<span id="span_' + id_clt + '"><i class="fa fa-refresh fa-spin"></i> <span>');
                $('#link_' + id_clt).remove();
                $.post('/intranet/rh/seguro_desemprego/controlador.php', {mes: mes, ano: ano, id_clt: id_clt <?= (isset($cnpj_master) && ($cnpj_master != FALSE)) ? ' , cnpj_master: "master"' : ''; ?>}, function(data) {
                
                var erros = '';
                var erros_qnt = 0;
                
                for (var key1 in data.erros) {
                    for (var key2 in data.erros[key1]) {
                        for (var key3 in data.erros[key1][key2]) {
                            erros += '<p style="font-weight: normal">ERRO DE '+key1+' -> '+data.erros[key1][key2][key3]+'</p>';
                        }
                        erros_qnt++;
                    }
                }
                
                if(erros_qnt>0){
                    $('#span_' + id_clt).after('<a href="javascript:;" class="btn btn-danger btn-sm"><i class="fa fa-exclamation-circle"></i> Erro</a>');
                    $('#span_' + id_clt).remove();
                    
                    if(erros_qnt == 1){
                        $('#resp').append('<div class="alert alert-danger"><h4> FOI ENCONTRADO '+erros_qnt+' ERRO.</h4>'+erros+'</div>');
                    }else{
                        $('#resp').append('<div class="alert alert-danger"><h4> FORAM ENCONTRADOS '+erros_qnt+' ERROS.</h4>'+erros+'</div>');
                    }                    
                }else{
                    $('#span_' + id_clt).after('<a href="?download=' + data.arquivo + '&name_file=DESLIGAMENTOS_' + mes + '_' + ano + '_' + id_clt + '.SD" class="btn btn-success btn-sm"><i class="fa fa-download"></i> Baixar</a>');
                    $('#span_' + id_clt).remove();
                }
//                    $('#debug_'+id_clt).html('<textarea style="width:100%; height: 130px; white-space:nowrap;">'+data.textarea+'</textarea>'+data.sql);
//                    console.log(data);c
//                    $('#debug_'+id).html(data);
                }, 'json');
            }
            
            function gerar_todos(mes, ano) {
                var  checkValues = $('.checks:checked').map(function(){
                    return $(this).val();
                }).get();
                
                $('#bt_gerar_sd').attr('disabled', 'disabled');
                $('#bt_gerar_sd').val('Processando...');
                $('#resp_sd').html('<p><i class="fa fa-refresh fa-2x fa-spin"></i></p>');
                
                $.post('/intranet/rh/seguro_desemprego/controlador.php', {acao: '2', mes: mes, ano: ano, clts: checkValues, <?= (isset($cnpj_master) && ($cnpj_master != FALSE)) ? ' , cnpj_master: "master"' : ''; ?>}, function(data) {
                    var erros = '';
                    var erros_qnt = 0;

                    for (var key1 in data.erros) {
                        for (var key2 in data.erros[key1]) {
                            for (var key3 in data.erros[key1][key2]) {
                                erros += '<p style="font-weight: normal">ERRO DE '+key1+' -> '+data.erros[key1][key2][key3]+'</p>';
                            }
                            erros_qnt++;
                        }
                    }
        
                    if(erros_qnt>0){
                        $('#bt_gerar_sd').removeAttr('disabled');
                        $('#bt_gerar_sd').val('Gerar Selecionados');    
                        $('#resp_sd').html('<br><a href="javascript:;" class="btn btn-danger btn-sm"><i class="fa fa-exclamation-circle"></i> Erro de dados</a><br><br>');
                        
                        if(erros_qnt == 1){
                            $('#resp').append('<div class="alert alert-danger"><h4> FOI ENCONTRADO '+erros_qnt+' ERRO.</h4>'+erros+'</div>');
                        }else{
                            $('#resp').append('<div class="alert alert-danger"><h4> FORAM ENCONTRADOS '+erros_qnt+' ERROS.</h4>'+erros+'</div>');
                        }
                    }else{        
                        $('#bt_gerar_sd').removeAttr('disabled');
                        $('#bt_gerar_sd').val('Gerar Selecionados');    
                        $('#resp_sd').html('<br><a href="?download=' + data.arquivo + '&name_file=DESLIGAMENTOS_' + mes + '_' + ano + '.SD"  class="btn btn-success btn-sm"><i class="fa fa-download"></i> Arquivo Pronto! Clique aqui para baixar</a><br><br><br>');    
                    }
                }, 'json');
            }
        </script>
    </body>
</html>
