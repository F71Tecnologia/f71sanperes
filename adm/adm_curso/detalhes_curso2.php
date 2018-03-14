<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../wfunction.php');
include('../../classes/FuncoesClass.php');

$usuario = carregaUsuario();

$sql = FuncoesClass::getRhHorario($_REQUEST['curso']);

$row = FuncoesClass::getCursosID($_REQUEST['curso']);
$id_regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['projeto'];

//trata tipo de insalubridade
if($row['tipo_insalubridade'] == 1){
    $insalubridade = "20%";
}elseif($row['tipo_insalubridade'] == 2){
    $insalubridade = "40%";
}

//trata mes abono
if($row['mes_abono'] == 0){
    $mes_abono = '';
}else{
    $mes_abono = mesesArray($row['mes_abono']);
}

//trata cbo
if($row['nome_cbo'] == ''){
    $cbo = '';
}else{
    $cbo = $row['nome_cbo'] . " - " . $row['cod'];
}

$regiao_selecionada = $_REQUEST['hide_regiao'];
$projeto_selecionado = $_REQUEST['hide_projeto'];

$_SESSION['regiao_select'] = $regiao_selecionada;
$_SESSION['projeto_select'] = $projeto_selecionado;
session_write_close();

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Função ".$row['nome_funcao']);
$breadcrumb_pages = array(/*"Gestão de RH"=>"../../rh", */"Gestão de Funções"=>"index2.php");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Função <?=$row['nome_funcao']?></title>
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
    </head>
    <body>
    <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small>Função <?=$row['nome_funcao']?></small></h2></div>
                </div>
            </div>
            <form action="" method="post" name="form1" id="form1" enctype="multipart/form-data" >
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Dados da Função
                        <input type="hidden" id="curso" name="curso" value="" />
                    </div>
                    <div class="panel-body">
                        <div class="col-md-6 no-padding-l border-r">
                            <p><label>Regiao:</label> <?php echo $row['regiao']; ?></p>
                            <p><label>Projeto:</label> <?php echo $row['nome_projeto']; ?></p>
                            <p><label>Tipo de Contratação:</label> <?php echo $row['tipo_contratacao_nome']; ?></p>
                            <p><label>Área:</label> <?php echo $row['area']; ?></p>
                            <p><label>CBO:</label> <?php echo $cbo; ?></p>
                            <p><label>Local:</label> <?php echo $row['local']; ?></p>
                            <p><label>Início:</label> <?php echo $row['data_ini']; ?></p>
                            <p><label>Final:</label> <?php echo $row['data_fim']; ?></p>
                            <p><label>Salário:</label> <?php echo formataMoeda($row['salario']); ?></p>
                        </div>
                        <div class="col-md-6 no-padding-r">
                            <p><label>Mês Abono:</label> <?php echo $mes_abono; ?></p>
                            <p><label>Insalubridade:</label> <?php echo $insalubridade; ?></p>
                            <p><label>Quantidade de Salários:</label> <?php echo $row['qnt_salminimo_insalu']; ?></p>
                            <p><label>Valor:</label> <?php echo formataMoeda($row['valor']); ?></p>
                            <p><label>Parcelas:</label> <?php echo $row['parcelas']; ?></p>
                            <p><label>Quota:</label> <?php echo formataMoeda($row['quota']); ?></p>
                            <p><label>Parcela das Quotas:</label> <?php echo $row['num_quota']; ?></p>
                            <p><label>Qtd. Máxima de Contratação:</label> <?php echo $row['qnt_maxima']; ?></p>
                            <p><label>Descrição:</label> <?php echo $row['descricao']; ?></p>
                        </div>
                    </div>
                    <?php                 
                    while($rst = mysql_fetch_array($sql)){
                        //trata folga
                        if ($rst['folga'] == "3") {
                            $folga = "FINAL DE SEMANA";
                        } elseif ($rst['folga'] == "1") {
                            $folga = "FOLGA NO SÁBADO";
                        } elseif ($rst['folga'] == "2") {
                            $folga = "FOLGA NO DOMINGO";
                        } elseif ($rst['folga'] == "5") {
                            $folga = "PLANTONISTA";
                        } else {
                            $folga = "SEM FOLGAS";
                        } ?>
                        <div class="panel-heading border-t">Dados do Horário</div>
                        <div class="panel-body">
                            <div class="col-md-6">
                                <p><label>Nome do Horário: </label> <?php echo $rst['nome']; ?></p>
                                <p><label>Observações: </label> <?php echo $rst['obs']; ?></p> 
                                <p><label>Horas Mês: </label><?php echo $rst['horas_mes']; ?></p>
                            </div>
                            <div id="col-md-6">                        
                                <p><label>Dias Mês: </label><?php echo $rst['dias_mes']; ?></p>
                                <p><label>Dias Semana: </label><?php echo $rst['dias_semana']; ?></p>
                                <p><label>Folgas: </label><?php echo $folga; ?></p>
                            </div>
                            <div class="col-md-12">
                                <p>
                                    <label>Preenchimento:</label>
                                    <span class="tr-bg-active"><?php echo $rst['entrada_1']; ?></span>
                                    <span class="tr-bg-active"><?php echo $rst['saida_1']; ?></span>
                                    <span class="tr-bg-active"><?php echo $rst['entrada_2']; ?></span>
                                    <span class="tr-bg-active"><?php echo $rst['saida_2']; ?></span>
                                </p>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="panel-footer text-right">
                        <input type="button" class="btn btn-default" name="voltar" id="voltar" value="Voltar" onclick="window.location = 'index.php';" />
                        <input type="submit" class="btn btn-primary" value="Editar" name="editarFuncao" id="editarFuncao" data-type="editar" data-key="<?=$row['id_curso']?>" />
                    </div>
                </div>
            </form>
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
                $("#editarFuncao").click(function(){
                    var action = $(this).data("type");
                    var key = $(this).data("key");
                    
                    if (action === "editar") {
                        $("#curso").val(key);
                        $("#form1").attr('action','edit_curso2.php');
                        $("#form1").submit();
                    }
                });                                
            });
        </script>
    </body>
</html>