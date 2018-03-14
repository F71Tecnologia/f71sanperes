<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../conn.php');
include('../classes/global.php');
include('../wfunction.php');
include('../classes/cooperativa.php');
$usuario = carregaUsuario();

$cooperativa = cooperativa::getCoop($_REQUEST['cooperativa']);
$regiao = montaQueryFirst("regioes", "*", "id_regiao={$cooperativa['id_regiao']}");
$_SESSION['voltarCooperativa']['id_regiao'] = $cooperativa['id_regiao'];


if ($cooperativa['tipo'] == '1')
    $cooperativa['tipo'] = 'COOPERATIVA';
if ($cooperativa['tipo'] == '2')
    $cooperativa['tipo'] = 'PESSOA JURÍDICA';

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Detalhes Cooperativa");
$breadcrumb_pages = array("Cooperativas" => "cooperativa_nova2.php");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Detalhes Cooperativa</title>
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
            <form id="form1" method="post">
                <input type="hidden" name="home" id="home" value="">
            </form>
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small>Detalhes Cooperativa</small></h2></div>
                    <input type="hidden" name="home" id="home">
                </div><!-- /.col-lg-12 -->
            </div><!-- /.row -->
            <div class="row">
                <fieldset>
                    <div class="col-lg-12">
                        <legend class="note note-info">Dados da Empresa Contratada</legend>
                        <p>
                            <label for="tipo">Tipo:</label>
                            <?=$cooperativa['tipo']?>
                        </p>
                        <p>
                            <label for="nome">Razão Social:</label>
                            <?=$cooperativa['nome']?>   
                        </p>
                        <p>
                            <label for="fantasia">Nome Fantasia:</label>
                            <?=$cooperativa['fantasia']?>
                        </p>
                        <p>
                            <label>Endereço:</label>
                            <?=$cooperativa['endereco']?>
                        </p>
                    </div><!-- /.col-lg-12 -->
                    <div class="col-lg-6">
                        <p>
                            <label>Bairro:</label>
                            <?=$cooperativa['bairro']?>
                        </p>
                        <p>
                            <label>CEP:</label>
                            <?=$cooperativa['cooperativa_cep']?>
                        </p>
                        <p>
                            <label>CNPJ:</label>
                            <?=$cooperativa['cnpj']?>
                        </p>
                        <p>
                            <label>CNAE:</label>
                            <?=$cooperativa['cooperativa_cnae']?>
                        </p>
                        <p>
                            <label>Contato:</label>
                            <?=$cooperativa['contato']?>
                        </p>
                        <p>
                            <label>Cel:</label>
                            <?=$cooperativa['cel']?>
                        </p>
                    </div><!-- /.col-lg-6 -->
                    <div class="col-lg-6">
                        <p>
                            <label>Cidade:</label>
                            <?=$cooperativa['cidade']?>
                        </p>
                        <p>
                            <label>UF:</label>
                            <?=$cooperativa['cooperativa_uf']?>
                        </p>
                        <p>
                            <label>FPAS:</label>
                            <?=$cooperativa['cooperativa_fpas']?>
                        </p>
                        <p class="first">&nbsp;</p>
                        <p>
                            <label>Telefone:</label>
                            <?=$cooperativa['tel']?>
                        </p>
                        <p>
                            <label>Fax:</label>
                            <?=$cooperativa['fax']?>
                        </p>
                    </div><!-- /.col-lg-6 -->
                    <div class="col-lg-12">
                        <p class="clear">
                            <label>E-mail:</label>
                            <?=$cooperativa['email']?>
                        </p>

                        <p>
                            <label>Site:</label>
                            <?=$cooperativa['site']?>
                        </p>
                    </div><!-- /.col-lg-12 -->
                </fieldset>
            </div><!-- /.row -->
            <div class="row">
                <fieldset>
                    <div class="col-lg-12">
                        <legend class="note note-info">Dados dos Administradores</legend>
                    </div>
                    <div class="col-lg-6">
                        <fieldset>
                            <legend class="note note-warning">Presidente</legend>
                            <p>
                                <label for="presidente">Nome:</label>
                                <?=$cooperativa['presidente']?>
                            </p>
                            <p>
                                <label for="rgp">RG:</label>
                                <?=$cooperativa['rgp']?>
                            </p>
                            <p>
                                <label for="matriculap">Matricula:</label>
                                <?=$cooperativa['matriculap']?>
                            </p>
                            <p>
                                <label for="cpfp">CPF:</label>
                                <?=$cooperativa['cpfp']?>
                            </p>
                            <p>
                                <label for="enderecop">Endereço:</label>
                                <?=$cooperativa['enderecop']?>
                            </p>
                        </fieldset>
                    </div><!-- /.col-lg-6 -->
                    <div class="col-lg-6">
                        <fieldset>
                            <legend class="note note-warning">Diretor</legend>
                            <p>
                                <label for="Diretor">Nome:</label>
                                <?=$cooperativa['diretor']?>
                            </p>
                            <p>
                                <label for="rgd">RG:</label>
                                <?=$cooperativa['rgd']?>
                            </p>
                            <p>
                                <label for="matriculad">Matricula:</label>
                                <?=$cooperativa['matriculad']?>
                            </p>
                            <p>
                                <label for="cpfd">CPF:</label>
                                <?=$cooperativa['cpfd']?>
                            </p>
                            <p>
                                <label for="enderecod">Endereço:</label>
                                <?=$cooperativa['enderecod']?>
                            </p>
                        </fieldset>
                    </div><!-- /.col-lg-6 -->
                    <div class="col-lg-12">
                        <p>
                            <label for="entidade">Entidade Sindical Vinculada:</label>
                            <?=$cooperativa['entidade']?>
                        </p>
                    </div><!-- /.col-lg-12 -->
                    <div class="col-lg-6">
                        <p>
                            <label for="fundo">Fundo reserva:</label>
                            <?=$cooperativa['fundo']?>
                        </p>
                        <p>
                            <label for="taxa">Taxa Administrativa:</label>
                            <?=$cooperativa['taxa']?>
                        </p>
                    </div><!-- /.col-lg-6 -->
                    <div class="col-lg-6">
                        <p>
                            <label for="parcela">Quantidade de Parcelas:</label>
                            <?=$cooperativa['parcelas']?>
                        </p>
                        <p>
                            <label for="bonificacao">Bonificação:</label>
                            <?=$cooperativa['bonificacao']?>
                        </p>
                    </div><!-- /.col-lg-6 -->
                    <div class="col-lg-12">
                        <p>
                            <label class="first">Logo:</label>
                            <?php
                            if (isset($cooperativa['foto']) && !empty($cooperativa['foto'])) { ?>
                                <img data-src="holder.js/140x140" class="img-thumbnail" src="<?="logos/coop_".$cooperativa['id_coop'].$cooperativa['foto']?>" style="width: 140px; height: 140px;">
                                <!--img src="<?="logos/coop_" . $cooperativa['id_coop'] . $cooperativa['foto']; ?>" title="Logo da Cooperativa" alt="Logo da Cooperativa" style="min-width:100px; min-height: 100px;"-->
                            <?php }else{ ?>
                                Não disponível.
                            <?php } ?>
                        </p>
                        <p>
                            <label for="cursos" class="first">Realizador do Curso de Cooperativismo:</label>
                            <?=$cooperativa['cursos']?>
                        </p>
                    </div><!-- /.col-lg-12 -->
                </fieldset>
            </div><!-- /.row -->
            <div class="row">
                <fieldset>
                    <div class="col-lg-12">
                        <legend class="note note-info">Dados Bancários</legend>
                        <p>
                            <label class="banco">Banco:</label>
                            <?php
                            $result_banco = mysql_query("SELECT id_banco,nome FROM bancos where id_regiao = '$regiao'");
                            while ($row_banco = mysql_fetch_array($result_banco)) {
                                if($cooperativa['id_banco'] == $row_banco['id_banco']){
                                    echo $row_banco['id_banco'].' - '.$row_banco['nome'];
                                }
                            } ?>
                        </p>
                    </div><!-- /.col-lg-12 -->
                </fieldset>
            </div><!-- /.row -->
            <?php include_once ('../template/footer.php'); ?>
        </div>
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
    </body>
</html>
