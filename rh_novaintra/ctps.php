<?php
include ('../classes/ctpsClass.php');
include_once ('../classes/LogClass.php');
include ('../classes_permissoes/acoes.class.php');
include ('../wfunction.php');
include ('../conn.php');

$ctps = new CtpsClass();
$log = new Log();
$acao = NEW Acoes();

$ctps->setDefault(); 

//date_default_timezone_set('America/Brasilia');
//echo date_default_timezone_get();

$usuario = carregaUsuario();
$id_ctps = (isset($_REQUEST['id_ctps'])) ? $_REQUEST['id_ctps'] : 1;
$id_user = $usuario['id_funcionario'];
$id_regiao = $usuario['id_regiao'];
$nome = $_REQUEST['nome'];
$numero = $_REQUEST['numero'];
$serie = $_REQUEST['serie'];
$uf = $_REQUEST['uf'];
$obs = $_REQUEST['obs'];
$obs_preenchimento = $_REQUEST['obs_preenchimento'];
$preenchimento = $_REQUEST['preenchimento'];
$tab = $_REQUEST['tab'];
$data_cad = date('Y-m-d');
$data_ent = date('Y-m-d');
$data = date('d/m/Y');


$result_local = mysql_query("SELECT * FROM regioes where id_regiao = '$id_regiao'");
$row_local = mysql_fetch_array($result_local);

function formato_brasileiro($data){
    return implode('/', array_reverse(explode('-', $data)));
}

//INSERINDO AS INFORMAÇÕES DO FORM
if (isset($_REQUEST['receber_ctps']) && !empty($_REQUEST['receber_ctps'])) {

    $ctps->setDefault();
    $ctps->setIdRegiao($id_regiao);
    $ctps->setIdUserCad($id_user);
    $ctps->setNome($nome);
    $ctps->setNumero($numero);
    $ctps->setSerie($serie);
    $ctps->setUf($uf);
    $ctps->setObs($obs);
    $ctps->setObsPreenchimento($obs_preenchimento);
    $ctps->setPreenchimento($preenchimento);
    $ctps->setDataCad($data_cad);

    if ($ctps->insert()) {
        $error = 0;
        $id_ctps = $ctps->getIdControle();
        $msg = "Informações gravadas com sucesso de CTSP ($id_ctps) - $nome)";
    } else {
        $error = 1;
        $id_ctps = "";
        $msg = $ctps->getError();
    }
    
    $log->log('2', "CTPS ID $id_ctps cadastrado com sucesso", 'controlectps');
    
    ?>
    <html>
        <head>
            <script src="../js/jquery-1.10.2.min.js"></script>
            <script>
                alert("<?=$msg?>");
                <?php
                if($error){
                ?>
                $(document).ready(function() {
                    $(location).attr('href', '\ctps.php');
                });
                <?php
                }
                else {
                ?>
                $(document).ready(function() {
                    $("#frmReceber").attr("action","\ctps_protocolo.php");
                    $("#frmReceber").attr("target","_blank");
                    $("#frmReceber").submit();
                });
                <?php
                }
                ?>
                
            </script>
        </head>
        <body>
            <form id="frmReceber" method="POST">
                <input type="hidden" value="<?=$id_ctps?>" name="id_ctps">
                <input type="hidden" name="receber_ctps" id="receber_ctps" value="Receber">
            </form>
        </body>
    </html>
    <?php
}

if (!empty($_REQUEST['entregar_ctps'])) {

    $ctps->setDefault();
    $ctps->setIdControle($id_ctps);
    $ctps->setDataEnt($data_ent);
    $ctps->setIdUserEnt($id_user);
    $ctps->setAcompanhamento(2);
    $antigo = $log->getLinha('controlectps', $id_ctps);
    
    if($ctps->update()){
        $novo = $log->getLinha('controlectps', $id_ctps);
        $msg = "Situação alterada para CTPS ($id_ctps): Entregue";
        
    }else{

        $msg = $ctps->getError();

    }

    $log->log('2', "Entrega do CTPS ID $id_ctps", 'controlectps', $antigo, $novo);

    ?>
    <html>
        <head>
            <script src="../js/jquery-1.10.2.min.js"></script>
            <script>
                alert("<?=$msg?>");

                $(document).ready(function() {
                    $("#frmEntregar").attr("action","\ctps_protocolo.php");
                    $("#frmEntregar").attr("target","_blank");
                    $("#frmEntregar").submit();
                });
                
            </script>
        </head>
        <body>
            <form id="frmEntregar" method="POST">
                <input type="hidden" value="<?=$id_ctps?>" name="id_ctps">
                <input type="hidden" name="entregar_ctps" id="entregar_ctps" value="Entregar">
                
            </form>
            
        </body>
    </html>
    <?php
}

if (!empty($_REQUEST['desfazer_ctps'])) {

    $ctps->setDefault();
    $ctps->setIdControle($id_ctps);
    $ctps->setDataEnt();
    $ctps->setIdUserEnt();
    $ctps->setAcompanhamento(1);
    $antigo = $log->getLinha('controlectps', $id_ctps);
    
    if($ctps->update()){
        $novo = $log->getLinha('controlectps', $id_ctps);
        $msg = "Situação alterada para CTPS ($id_ctps): Entregua desfeita";
        $ctps->select();


    }else{

        $msg = $ctps->getError();

    }

    $log->log('2', "Entrega desfeita do CTPS ID $id_ctps", 'controlectps', $antigo, $novo);
   
    ?>
    <html>
        <head>
            <script src="../js/jquery-1.10.2.min.js"></script>
            <script>
                alert("<?=$msg?>");
                $(document).ready(function() {
                    $(location).attr('href', '\ctps.php');
                });
            </script>
        </head>
    </html>
    <?php
    exit;
   
}


$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel" => "../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form1", "ativo" => "Carteiras de Trabalho");

if ($_REQUEST[caminho] == 1) {
    $breadcrumb_config = array("nivel" => "../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form1", "ativo" => "Carteiras de Trabalho");
    $breadcrumb_pages = array("Lista Projetos" => "ver.php", "Visualizar Projeto" => "javascript:void(0);", "Lista Participantes" => "javascript:void(0);", "Visualizar Participante" => "javascript:void(0);");
    $breadcrumb_attr = array(
        "Visualizar Projeto" => "class='link-sem-get' data-projeto='{$row['id_projeto']}' data-form='form1' data-url='ver.php'",
        "Lista Participantes" => "class='link-sem-get' data-projeto='{$row['id_projeto']}' data-form='form1' data-url='bolsista.php'",
        "Visualizar Participante" => "class='link-sem-get' data-pro='{$row['id_projeto']}' data-clt='$clt' data-form='form1' data-url='ver_clt.php'"
    );
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Carteiras de Trabalho</title>
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
        <link href="../resources/css/bootstrap-note.css" rel="stylesheet" type="text/css">
      
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Carteiras de Trabalho</small></h2></div>
                </div><!-- /.col-lg-12 -->
            </div><!-- /.row -->
            <ul class="nav nav-tabs" style="margin-bottom: 15px;">
                <li class="active"><a href="#aserementregues" data-toggle="tab">A Serem Entregues</a></li>
                <?php
                if($acao->verifica_permissoes(17)){ 
                ?>
                <li class=""><a href="#recebimento" data-toggle="tab">Recebimento de Carteira</a></li>
                <?php
                }
                ?>
                <li class=""><a href="#entregue" data-toggle="tab">Carteira Entregues</a></li>
            </ul>
            <div id="myTabContent" class="tab-content">
                <div class="tab-pane active" id="aserementregues">
                    <div class="note note-warning text-center">
                        <h4>CONTROLE DE CARTEIRAS A SEREM ENTREGUES</h4>
                    </div>
                    <table class="table table-condensed table-bordered table-hover">
                        <thead>
                            <tr class="bg-primary valign-middle">
                                <th class="text-center" width="14%">RECEBIMENTO</th>
                                <th width="17%">NOME</th>
                                <th width="15%">NÚMERO</th>
                                <th width="15%">SERIE</th>
                                <th class="text-center" width="10%">UF</th>
                                <th class="text-center" width="15%">PREENCHIMENTO</th>
                                <?php
                                if($acao->verifica_permissoes(18)){ 
                                ?>
                                <th width="14%"></th>
                                <?php
                                }
                                ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $ctps->setDefault();
                            $ctps->setIdRegiao($id_regiao);
                            $ctps->setAcompanhamento(1);
                            $ctps->select();

                            if ($ctps->getCountRow() > 0) {
                                while ($ctps->getRow()) {
                                    ?>
                                    <tr class="valign-middle">
                                        <td class="text-center"><?=$ctps->getDataCad('d/m/Y'); ?></td>
                                        <td><?=$ctps->getNome(); ?></td>
                                        <td><?=$ctps->getNumero(); ?></td>
                                        <td><?=$ctps->getSerie(); ?></td>
                                        <td class="text-center"><?= $ctps->getUf(); ?></td>
                                        <td class="text-center"><?= $ctps->getLabelPreenchimento(); ?></td>
                                        <?php
                                        if($acao->verifica_permissoes(18) ){ 
                                        ?>
                                        <td class="text-center">
                                            <?php
                                            if($acao->verifica_permissoes(18)){ 
                                            ?>    
                                            <form action="ctps.php" method="post" name="form">
                                                <input type="hidden" name="regiao" value="<?=$ctps->getIdRegiao();?>">
                                                <input type="hidden" name="id_ctps" value="<?=$ctps->getIdControle();?>">
                                                <input class="btn btn-primary" type="submit" name="entregar_ctps" id="entregar_ctps" value="Entregar">
                                                
                                            <?php
                                            }
                                            ?>
                                            </form>
                                            
                                            <a class="btn btn-info" href="ctps_impressao.php?id=<?php echo $ctps->getIdControle();?>"> Protocolo</a>

                                        <?php
                                        }
                                        ?>
                                    </tr>
                                <?php
                                    }
                                } else {
                                    ?>
                                <tr class="warning">
                                    <td colspan="7">Nenhuma Carteira a Ser Entregue!</td>
                                </tr>
                                <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane" id="recebimento">
                    <div class="note note-warning text-center">
                        <h4>DADOS DA CARTEIRA RECEBIDA<br>
                            <strong><?= $row_local['regiao'] ?></strong> - Data de Recebimento <strong><?= $data ?></strong>
                        </h4>
                    </div>
                    <form class="form-horizontal" action="<?=$_SERVER['PHP_SELF']?>" enctype="multipart/form-data" method="post" name='receber_ctps' id="receber_ctps">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">Nome:</label>
                                    <div class="col-lg-9">
                                        <input class="form-control validate[required,maxSize[250]]" name="nome" type="text" id="nome" value="<?=$nome?>">
                                    </div>
                                </div><!-- /.form-group -->
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">Número:</label>
                                    <div class="col-lg-4">
                                        <input class="form-control validate[required,maxSize[150]]" name="numero" type="text" id="numero" value='<?=$numero?>'>
                                    </div>
                                    <label class="col-lg-1 control-label">Série:</label>
                                    <div class="col-lg-2">
                                        <input class="form-control validate[required,maxSize[150]]" name="serie" type="text" id="serie" value='<?=$serie?>'>
                                    </div>
                                    <label class="col-lg-1 control-label">UF:</label>
                                    <div class="col-lg-1">
                                        <input class="form-control validate[required,minSize[2],maxSize[2]]" name="uf" type="text" id="uf" maxlength="2" value='<?=$uf?>'>
                                    </div>
                                </div><!-- /.form-group -->
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">Observações:</label>
                                    <div class="col-lg-9">
                                        <input class="form-control validate[maxSize[250]]" name="obs" type="text" id="obs">
                                    </div>
                                </div><!-- /.form-group -->
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">Preenchimento:</label>
                                    <div class="col-lg-2">
                                        <div class="radio">
                                            <label>
                                                <input  type="radio" name="preenchimento" id="preenchimento" value="1" onClick="document.all.linha.style.display = (document.all.linha.style.display == 'none') ? 'none' : 'none';" checked>
                                                Admissão
                                            </label>
                                        </div>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="preenchimento" id="preenchimento2" value="2" onClick="document.all.linha.style.display = (document.all.linha.style.display == 'none') ? 'none' : 'none';"> 
                                                Rescisão</label>
                                            </label>
                                        </div>
                                        <!--div class="radio">
                                            <label>
                                                <input type="radio" name="preenchimento" id="preenchimento3" value="3" onClick="document.all.linha.style.display = (document.all.linha.style.display == 'none') ? 'none' : 'none';">
                                                Férias
                                            </label>
                                        </div-->
                                    </div>
                                    <div class="col-lg-2">
                                        <!--div class="radio">
                                            <label>
                                                <input type="radio" name="preenchimento" id="preenchimento4" value="4" onClick="document.all.linha.style.display = (document.all.linha.style.display == 'none') ? 'none' : 'none';">
                                                13º Salário
                                            </label>
                                        </div-->
                                        <!--div class="radio">
                                            <label>
                                                <input type="radio" name="preenchimento" id="preenchimento5" value="5" onClick="document.all.linha.style.display = (document.all.linha.style.display == 'none') ? 'none' : 'none';">
                                                Licença
                                            </label>
                                        </div-->
                                        <!--div class="radio">
                                            <label>
                                                <input type="radio" name="preenchimento" id="preenchimento6" value="6" onClick="document.all.linha.style.display = (document.all.linha.style.display == 'none') ? '' : '';" > 
                                                Outros
                                            </label>
                                        </div-->
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="preenchimento" id="preenchimento7" value="7" onClick="document.all.linha.style.display = (document.all.linha.style.display == 'none') ? '' : '';" > 
                                                Atualização
                                            </label>
                                        </div>
                                    </div>
                                </div><!-- /.form-group -->
                                <!--div class="form-group" id="linha" style="display:none">
                                    <label class="col-lg-2 control-label">Descreva:</label>
                                    <div class="col-lg-9">
                                        <input class="form-control" name="obs_preenchimento" type="text" id="obs_preenchimento">
                                    </div>
                                </div--><!-- /.form-group -->

                                <div class="form-group" style="display:none" id="tablearquivo">
                                    <label class="col-lg-2 control-label">SELECIONE:</label>
                                    <div class="col-lg-9">
                                        <input class="form-control" name="arquivo" type="file" id="arquivo" />
                                    </div>
                                </div><!-- /.form-group -->
                            </div>
                            <div class="panel-footer text-center">
                                <input type="hidden" value="<?=$id_regiao?>" name="regiao">
                                <input type="submit" class="btn btn-success" name="receber_ctps" id="receber_ctps" value="GERAR PROTOCOLO DE RECEBIMENTO">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane" id="entregue">
                    <table class="table table-hover table-condensed table-bordered">
                        <thead>
                            <tr class="bg-primary valign-middle">
                                <th width="19%">NOME</th>
                                <th width="14%">RECEBIMENTO</th>
                                <th width="15%">RECEBIDO POR</th>
                                <th width="15%">ENTREGUE EM</th>
                                <th width="12%">ENTREGUE POR</th>
                                <th width="10%">PREENCHIMENTO</th>
                                <?php
                                if($acao->verifica_permissoes(97)){ 
                                ?>
                                <th width="15%"></th>
                                <?php
                                }
                                ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $ctps->setDefault();
                            $ctps->setIdRegiao($id_regiao);
                            $ctps->setAcompanhamento(2);
                            $ctps->select();

                            if ($ctps->getCountRow() > 0) {
                                while ($ctps->getRow()) {
                                    ?>
                                    <tr class="valign-middle">
                                        <td><?= $ctps->getNome() ?></td>
                                        <td><?= $ctps->getDataCad('d/m/Y'); ?></td>
                                        <td><?= $ctps->getRecebidoPor(); ?></td>
                                        <td><?= formato_brasileiro($ctps->getDataEnt2()); ?></td>
                                        <td><?= $ctps->getEntreguePor(); ?></td>
                                        <td><?= $ctps->getLabelPreenchimento(); ?></td>
                                        <?php
                                        if($acao->verifica_permissoes(97)){ 
                                        ?>
                                        <td class="text-center">
                                        <form action="<?=$_SERVER['PHP_SELF']?>" method="post" name="form">
                                            <input type="hidden" name="regiao" value="<?=$ctps->getIdRegiao();?>">
                                            <input type="hidden" name="id_ctps" value="<?=$ctps->getIdControle();?>">
                                            <input class="btn btn-primary" type="submit" name="desfazer_ctps" id="desfazer_ctps" value="Desfazer">
                                        </form>
                                        </td>
                                        <?php
                                        }
                                        ?>
                                    </tr>
                                        <?php
                                    }
                                } else {
                                    ?>
                                <tr class="warning">
                                    <td colspan="7">Nenhuma Carteira Entregue!</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <div class="col-lg-12 form-group" style="display:none" id="tablearquivo2">
                        <label class="col-lg-2 control-label">SELECIONE:</label>
                        <div class="col-lg-9">
                            <input name="arquivo2" class="form-control" type="file" id="arquivo2" />
                        </div>
                    </div>
                </div>
            </div>
        <?php include_once ('../template/footer.php'); ?>
        </div>
        
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../uploadfy/scripts/jquery.uploadify.v2.1.0.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script src="../jquery/datepicker-lite/jquery-ui-1.8.4.custom.min.js"></script>
        <script src="../jquery/jquery.form.js"></script>
        <script src="../js/formatavalor.js"></script>

        <script>
            $(function() {
                    $("#receber_ctps").validationEngine();
                    $("a[href='#<?=$_REQUEST['tab']?>']").tab('show');                            
            });   
            
        </script>      
        
        
        <script language="javascript">
        //o parâmentro form é o formulario em questão e t é um booleano 
        function ticar(form, t) {
            campos = form.elements;
            for (x = 0; x < campos.length; x++)
                if (campos[x].type == "checkbox")
                    campos[x].checked = t;
        }
        </script> 
        
    </body>
</html>
