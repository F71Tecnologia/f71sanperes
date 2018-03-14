<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../wfunction.php');
include('../../classes/SindicatoClass.php');

$sindicato = $_REQUEST['sindicato'];

$usuario = carregaUsuario();
$row = getSindicatoID($sindicato);

session_write_close();

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Detalhes de Sindicatos");
$breadcrumb_pages = array("Gestão de RH"=>"../../rh", "Sindicatos"=>"index2.php");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Detalhes de Sindicatos</title>
        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <!--link href="../../resources/css/bootstrap-rh.css" rel="stylesheet" type="text/css"-->
    </head>
    <body>
    <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small>Detalhes de Sindicatos</small></h2></div>
                </div>
            </div>
            <div class="row">
                <form action="" class="form-horizontal" method="post" name="form1" id="form1" enctype="multipart/form-data" >
                    <input type="hidden" id="sindicato" name="sindicato" value="" />
                    <input type="hidden" id="home" name="home" />
                    <input type="hidden" id="caminho" name="caminho" />
                    <div class="col-lg-12">
                        <fieldset>
                            <legend>Dados do Sindicato</legend>
                            <label class='col-lg-2'>Regiao:</label><p class="col-lg-10"><?="{$row['reg_id']} - {$row['nome_regiao']}"?></p>
                            <label class='col-lg-2'>Nome:</label><p class="col-lg-10"><?=$row['nome']; ?></p>
                            <label class='col-lg-2'>Endereço:</label><p class="col-lg-10"><?=$row['endereco']; ?></p>

                            <label class='col-lg-2'>CNPJ:</label><p class="col-lg-4"><?=mascara_string("##.###.###/####-##", $row['cnpj']); ?></p>
                            <label class='col-lg-2'>Contato:</label><p class="col-lg-4"><?=$row['contato']; ?></p>

                            <label class='col-lg-2'>Fax:</label><p class="col-lg-4"><?=mascara_stringTel($row['fax']); ?></p>
                            <label class='col-lg-2'>Email:</label><p class="col-lg-4"><?=$row['email']; ?></p>

                            <label class='col-lg-2'>Celular:</label><p class="col-lg-4"><?=mascara_stringTel($row['cel']); ?></p>
                            <label class='col-lg-2'>Site:</label> <a href="http://<?php echo $row['site']; ?>" target="_blank"><?php echo $row['site']; ?></a></p>

                            <label class='col-lg-2'>Telefone:</label><p class="col-lg-10"><?=mascara_stringTel($row['tel']); ?></p>
                        </fieldset>
                    </div>
                    <div class="col-lg-12">
                        <fieldset>
                            <legend>Dados da Categoria</legend>
                            <label class='col-lg-2'>Mês de desconto:</label><p class="col-lg-4"><?=mesesArray($row['mes_desconto']); ?></p>
                            <label class='col-lg-2'>Mês de Dissídio:</label><p class="col-lg-4"><?=mesesArray($row['mes_dissidio']); ?></p>

                            <label class='col-lg-2'>Piso Salarial:</label><p class="col-lg-4"><?=($row['piso'] != '') ? formataMoeda($row['piso']) : ''; ?></p>
                            <label class='col-lg-2'>Multa do FGTS:</label><p class="col-lg-4"><?=($row['multa'] != '') ? "{$row['multa']}%" : ""; ?></p>

                            <label class='col-lg-2'>Férias (meses):</label><p class="col-lg-4"><?=$row['ferias']; ?></p>
                            <label class='col-lg-2'>Fração:</label><p class="col-lg-4"><?=$row['fracao']; ?></p>

                            <label class='col-lg-2'>13 (meses):</label><p class="col-lg-4"><?=$row['decimo_terceiro']; ?></p>
                            <label class='col-lg-2'>Recisão:</label><p class="col-lg-4"><?=$row['recisao']; ?></p>

                            <label class='col-lg-2'>Patronal:</label><p class="col-lg-4"><?=($row['pratonal'] == 1) ? 'SIM' : 'NÃO'; ?></p>
                            <label class='col-lg-2'>Evento Relacionado:</label><p class="col-lg-4"><?=($row['evento'] == '5019') ? 'CONTRIBUIÇÃO SINDICAL' : ''; ?></p>

                            <label class='col-lg-2'>Entidade Sindical:</label><p class="col-lg-10"><?=$row['entidade']; ?></p>
                        </fieldset>
                    </div>
                    <hr>
                    <div class="col-lg-12">
                        <input type="submit" class="btn btn-primary pull-right" value="Editar" name="editarSindicato" id="editarSindicato" data-type="editar" data-caminho="0" data-key="<?php echo $row['id_sindicato']; ?>" />
                    </div>
                </form>
            </div>
            <?php include_once '../../template/footer.php'; ?>
        </div><!-- /.content -->
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        
        <script>
            $(function() {
                $("#editarSindicato").click(function(){
                    var action = $(this).data("type");
                    var key = $(this).data("key");
                    var caminho = $(this).data("caminho");
                    
                    if (action === "editar") {
                        $("#sindicato").val(key);
                        $("#caminho").val(caminho);
                        $("#form1").attr('action','form_sindicato2.php');
                        $("#form1").submit();
                    }
                });
            });
        </script>
    </body>
</html>