<?php
if(empty($_COOKIE['logado'])){
    print "Efetue o Login<br><a href='www.netsorrindo.com.br/intranet/login.php'>Logar</a>";
    exit;
}

include "../../conn.php";
include "../../wfunction.php";
include "../../funcoes.php";
include "../../classes/projeto.php";
include "../../classes/regiao.php";
include "../../classes/clt.php";
include "../../classes/curso.php";
include "../../classes/ContraChequeClass.php";
include "../../classes_permissoes/acoes.class.php";

$Projeto = new projeto();
$ClassRegiao = new regiao();
$ClassCLT = new clt();
$ClassCurso = new tabcurso();
$ContraCheque = new ContraCheque();
$ACOES = new Acoes();

$usuario = carregaUsuario();

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "ativo"=>"Contra Cheque","id_form"=>"form1");
$breadcrumb_pages = array("Gestão de RH"=>"/intranet/rh/principalrh.php");

$id = (!isset($_REQUEST['id'])) ? 1 : $_REQUEST['id'];

$sql = "SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'";
$result_user = mysql_query($sql, $conn);
$row_user = mysql_fetch_array($result_user);

$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);

//RECEBENDO A VARIAVEL CRIPTOGRAFADA
$enc = $_REQUEST['enc'];
$enc = str_replace("--","+",$enc);
$link = decrypt($enc);
$decript = explode("&",$link);

$regiao = $usuario['id_regiao'];
$folha = $decript[1];

$ContraCheque->getAnosFolha($regiao);
$primeiro_ano = $ContraCheque->ano_ini;
$ultimo_ano = $ContraCheque->ano_fim;

//RECEBENDO A VARIAVEL CRIPTOGRAFADA
$qr_regiao = mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$regiao'");
$nome_regiao = mysql_result($qr_regiao, 0);

$filtro = false;

$anoR = date('Y');

if(isset($_REQUEST['filtrar'])){
    $filtro = true;
    $projetoR = $_REQUEST['projeto'];
    $anoR = $_REQUEST['ano'];
        
    $RE = $ContraCheque->getFolhaCC($projetoR, $anoR);
    $total = mysql_num_rows($RE);
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>:: INTRANET :: Contracheques</title>
        
        <link rel="shortcut icon" href="../../favicon.png" />
        
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">        
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>               
    </head>
    
    <body class="novaintra">
        
        <?php include("../../template/navbar_default.php"); ?>
        
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Contra Cheque</small></h2></div>
            
            <div id="content">
                <form action="" method="post" name="form1" id="form1" class="form-horizontal top-margin1">
                    <input type="hidden" name="home" id="home" value="" />
                    <input type="hidden" name="ano_sel" id="ano_sel" value="<?php echo $anoR; ?>" />
                    
                    <div class="panel panel-default hidden-print">
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="select" class="col-lg-2 control-label">Projeto</label>
                                <div class="col-lg-4">
                                    <?php echo montaSelect(getProjetos($regiao), $projetoR, "id='projeto' name='projeto' class='required[custom[select]] form-control'"); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="select" class="col-lg-2 control-label">Ano</label>
                                <div class="col-lg-4">
                                    <?php echo montaSelect(AnosArray($primeiro_ano, $ultimo_ano), $anoR, "id='ano' name='ano' class='required[custom[select]] form-control'"); ?> 
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer text-right">
                            <input type="submit" name="filtrar" id="filt" value="Filtrar" class="btn btn-primary" />
                        </div>
                    </div>
                    
                    <?php
                    if($filtro){
                        if ($total > 0) {
                    ?>                                            
                            <div class="panel-group" id="accordion-example">                                
                                <div class="panel sanf acord_sefip">                                    
                                    <div class="panel-body">
                                        <table class='table table-striped table-hover table-condensed table-bordered'>
                                            <thead>
                                                <tr class="bg-primary valign-middle">
                                                    <th>COD</th>
                                                    <th>MÊS</th>
                                                    <th>PERÍODO</th>
                                                    <th>Nº DE PARTICIPANTES</th>
                                                    <th>INDIVIDUAL</th>
                                                    <th>TODOS</th>
                                                    <th>ARQUIVO CSV</th>
                                                    <?php if($ACOES->verifica_permissoes(128)){ ?>
                                                    <th>VALIDAÇÃO</th>
                                                    <?php } ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php                                                
                                                while($Row = mysql_fetch_assoc($RE)){
                                                    $mes = $ClassRegiao -> MostraMes($Row['mes']);
                                                    
                                                    //-- ENCRIPTOGRAFANDO A VARIAVEL
                                                    $linkvario = encrypt("$regiao&todos&$Row[id_folha]");
                                                    $linkvario = str_replace("+","--",$linkvario);
                                                    //-- ---------------------------
                                                    if($Row['terceiro'] == 1) {
                                                        switch ($Row['tipo_terceiro']) {
                                                            case 1:
                                                            $exibicao = "<b>13º Primeira parcela</b>";
                                                            break;
                                                            case 2:
                                                            $exibicao = "<b>13º Segunda parcela</b>";
                                                            break;
                                                            case 3:
                                                            $exibicao = "<b>13º Integral</b>";
                                                            break;
                                                        }
                                                    } else {
                                                        $exibicao = "<b>$mes</b>";
                                                    }
                                                ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $Row['id_folha']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $exibicao; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $Row['data_inicio']." até ".$Row['data_fim']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $Row['clts']." Participantes"; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="listaIndividual.php?id_folha=<?php echo $Row['id_folha']; ?>">
                                                            <i class="tooo btn btn-xs btn-primary fa fa-user bt-image" title="Gerar por pessoa" data-toggle="tooltip" data-placement="top" title="" data-original-title="Gerar por pessoa"></i>
                                                        </a>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="listaTodos.php?id_folha=<?php echo $Row['id_folha']; ?>">
                                                            <i class="tooo btn btn-xs btn-primary fa fa-users bt-image" title="Gerar de Todos" data-toggle="tooltip" data-placement="top" title="" data-original-title="Gerar de Todos"></i>
                                                        </a>
                                                        <a href="contra_cheque_oo.php?enc=<?= $linkvario ?>" target="_blank">
                                                            <i class="tooo btn btn-xs btn-warning fa fa-users bt-image" title="Gerar de Todos em Arquivo Unico" data-toggle="tooltip" data-placement="top" title="" data-original-title="Gerar de Todos em Arquivo Unico"></i>
                                                        </a>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="geracontra_txt.php?enc=<?php echo $linkvario; ?>">
                                                            <i class="tooo btn btn-xs btn-success fa fa-file-excel-o bt-image" title="Gerar CSV" data-toggle="tooltip" data-placement="top" title="" data-original-title="Gerar CSV"></i>
                                                        </a>
                                                    </td>
                                                    <?php if($ACOES->verifica_permissoes(128)){ ?>
                                                    <td class="text-center">
                                                        <a href="contra_cheque_oo.php?validate=1&enc=<?php echo $linkvario; ?>" target="_blank">
                                                            <i class="tooo btn btn-xs btn-warning fa fa-list bt-image" title="Validar Líquido" data-toggle="tooltip" data-placement="top" title="" data-original-title="Validar Líquido"></i>
                                                        </a>
                                                    </td>
                                                    <?php } ?>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>                                    
                                </div>                                
                            </div>                        
                    
                    <?php } else { ?>
                        <div class="alert alert-danger top30">
                            Nenhum registro encontrado
                        </div>
                    <?php }
                    }
                    ?>
                    
                </form>
            </div>
        </div>    
    </body>
</html>