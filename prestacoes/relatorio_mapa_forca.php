<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a>";
    exit;
}

include('../conn.php');
include('../funcoes.php');
include('../wfunction.php');
include('../classes/pdf/fpdf.php');
include('../classes/mpdf54/mpdf.php');
include('../classes/imageToPdf.php');
require('../classes/fpdfi/fpdi.php');
include('PrestacaoContas.class.php');

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
//$optRegiao = getRegioes();
//$ACOES = new Acoes();

$regiao = $usuario['id_regiao'];
$master = $usuario['id_master'];

$result = null;
$btexportar = true;
$btfinalizar = true;
$historico = false;
$dataMesIni = date("Y-m") . "-31";
$mes2d = sprintf("%02d", $_REQUEST['mes']); //mes com 2 digitos
$nome_meses = array("01" => "Janeiro", "02" => "Fevereiro", "03" => "Março", "04" => "Abril", "05" => "Maio", "06" => "Junho", "07" => "Julho", "08" => "Agosto", "09" => "Setembro", "10" => "Outubro", "11" => "Novembro", "12" => "Dezembro");
$erros = 0;
$idsErros = array();
$msg = "";

$qr = mysql_query("SELECT A.nome, A.cpf, A.conselho, C.campo2 AS especialidade, A.tel_fixo, A.email, C.nome AS setor, B.especifica AS STATUS, C.cbo_codigo AS cbo, 
CASE WHEN C.cbo_codigo IN(5425,5426,5494,7229) 
THEN '0' /**CLINICA MÉDICA, PEDIATRA**/ WHEN C.cbo_codigo IN(3363,9200) 
THEN '1' /**ENFERMEIRO, TEC.ENFERMAGEM**/ WHEN C.cbo_codigo IN(4144,958,9463,773,2220,3379,2987,2894) 
THEN '2' /**GERENTE ADMINISTRATIVO, AUXILIAR ADMINISTRATIVO, AUXILIAR ADMINISTRATIVO DE PESSOAL, MAQUEIRO**/ WHEN C.cbo_codigo IN(6098,1002) 
THEN '3' /**ODONTOLOGIA**/ WHEN C.cbo_codigo IN(3804) THEN '4' /**AUXILIAR ADMINISTRATIVO, FARMACEUTICO**/ WHEN C.cbo_codigo IN(822,9514,6817,9332,9514,965) 
THEN '5' /**ASSISTENTE SOCIAL & TEC.RADIOLOGIA**/ END AS tipo
FROM rh_clt AS A 
LEFT JOIN rhstatus AS B ON(A.status = B.codigo)
LEFT JOIN curso AS C ON(A.id_curso = C.id_curso)
WHERE A.id_regiao = '{$regiao}' AND A.id_projeto = '{$_REQUEST['projetos']}' AND  
(MONTH(A.data_saida) > '{$mes2d}' AND YEAR(A.data_saida) >= '{$_REQUEST['ano']}' OR A.data_saida = '0000-00-00') AND A.`status` = 10
ORDER BY tipo");

class concat_pdf extends FPDI {

    var $files = array();

    function setFiles($files) {
        $this->files = $files;
    }

    function concat() {
        foreach ($this->files AS $file) {
            $ext = end(explode(".", $file));
            if (is_file($file) && $ext == "pdf") {
                $pagecount = $this->setSourceFile($file);
                for ($i = 1; $i <= $pagecount; $i++) {
                    $tplidx = $this->ImportPage($i);
                    $s = $this->getTemplatesize($tplidx);
                    $this->AddPage($s['w'] > $s['h'] ? 'L' : 'P', array($s['w'], $s['h']));
                    @$this->useTemplate($tplidx);
                }
            }
        }
    }

}

function normalizaNome($variavel) {
    $variavel = strtoupper($variavel);
    if (strlen($variavel) > 200) {
        $variavel = substr($variavel, 0, 200);
        $variavel = $variavel[0];
    }
    $nomearquivo = preg_replace("/ /", "_", $variavel);
    $nomearquivo = preg_replace("/[\/]/", "", $nomearquivo);
    $nomearquivo = preg_replace("/[ÁÀÂÃ]/i", "A", $nomearquivo);
    $nomearquivo = preg_replace("/[áàâãª]/i", "a", $nomearquivo);
    $nomearquivo = preg_replace("/[ÉÈÊ]/i", "E", $nomearquivo);
    $nomearquivo = preg_replace("/[éèê]/i", "e", $nomearquivo);
    $nomearquivo = preg_replace("/[ÍÌÎ]/i", "I", $nomearquivo);
    $nomearquivo = preg_replace("/[íìî]/i", "i", $nomearquivo);
    $nomearquivo = preg_replace("/[ÓÒÔÕ]/i", "O", $nomearquivo);
    $nomearquivo = preg_replace("/[óòôõº]/i", "o", $nomearquivo);
    $nomearquivo = preg_replace("/[ÚÙÛ]/i", "U", $nomearquivo);
    $nomearquivo = preg_replace("/[úùû]/i", "u", $nomearquivo);
    $nomearquivo = str_replace("Ç", "C", $nomearquivo);
    $nomearquivo = str_replace("ç", "c", $nomearquivo);

    return $nomearquivo;
}

function copiarArquivo($file, $novoNome) {
    $folderSave = dirname(__FILE__) . "/arquivos/";
    $extAr = explode(".", $file);
    $ext = end($extAr);
    if (is_file($file)) {
        if (!copy($file, $folderSave . $novoNome . "." . $ext))
            echo "erro ao copiar o arquivo de: {$file} <br/> PARA: " . $folderSave . $novoNome . "." . $ext;exit;
    }else {
        echo "erro ao copiar o arquivo(não existe): {$file}";
        exit;
    }
    return true;
}

//----- CARREGA PROJETOS COM PRESTAÇÕES FINALIZADAS NO MES SELECIONADO
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "finalizados") {
    
}

$meses = mesesArray(null);
$anos = anosArray(null, null, array("-1" => "« Selecione »"));

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
$projR = (isset($_REQUEST['projetos'])) ? $_REQUEST['projetos'] : null;
$mesR = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : null;
$anoR = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');



$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$breadcrumb_config = array("nivel" => "../", "key_btn" => "3", "area" => "Recursos Humanos", "ativo" => "Relatório Mapa Força de Trabalho", "id_form" => "form1");
$breadcrumb_pages = array("Lista Projetos" => "../rh/ver.php");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Relatório Mapa Força de Trabalho</title>

        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">


    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>      
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RELATÓRIO - <small>Mapa Força de Trabalho</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Dados</div>
                    <div class="panel-body">

                        <div class="form-group" >

                            <label for="select" class="col-sm-3 control-label hidden-print" >Projeto</label>
                            <div class="col-sm-3">
                                <select name="projetos" id="projetos" class="form-control">
                                    <?php
                                    $qr_unidades = mysql_query("SELECT * from projeto WHERE id_regiao = '{$regiao}'");
                                    while ($linha_projetos = mysql_fetch_assoc($qr_unidades)) {
                                        $selected = "";
                                        if ($linha_projetos['id_projeto'] == $projR) {
                                            $selected = "Selected='selected'";
                                        }
                                        ?>
                                        <option value="<?php echo $linha_projetos['id_projeto']; ?>" <?php echo $selected; ?>> <?php echo $linha_projetos['nome']; ?> </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="select" class="col-sm-3 control-label hidden-print" >Mês</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect($meses, $mesR, "id='mes' name='mes' class='form-control validate[custom[select]]'") ?>  
                            </div>
                            <div class="col-sm-3">
                                <?php echo montaSelect($anos, $anoR, "id='ano' name='ano' class='form-control validate[custom[select]]'") ?>
                            </div>
                        </div> 
                    </div>

                    <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>

                    <div class="panel-footer text-right hidden-print controls">
                        <?php if (!empty($qr_unidades) && (isset($_POST['gerar']))) { ?>
                            <button type="button" onclick="tableToExcel('forcaTrabalho', 'Relatório CNES')" value="Exportar para Excel" class="btn btn-success" ><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                            <button type="button" form="formPdf" name="pdf" data-title="Relatório Força de Trabalho" data-id="forcaTrabalho" id="pdf" value="Gerar PDF" class="btn btn-danger"><i class="fa fa-file-pdf-o"></i> Gerar PDF</button>
                        <?php } ?>
                        <button type="submit" name="gerar" value="Gerar" id="gerar" class="btn btn-primary"><span class="fa fa-filter"></span> Gerar</button>
                    </div>
                </div>


                <?php if (!empty($qr_unidades) && (isset($_POST['gerar']))) { ?>   
                
                    <table class="table table-striped table-condensed table-bordered table-hover text-sm valign-middle" id="forcaTrabalho">

                        <tr>
                            <td colspan="9" class="text-center"><h4>Relatório de Mapa de Força</h4></td>
                        </tr>
                        <tr>
                            <th>NOME</th>
                            <th>CPF</th>
                            <th>CRM</th>
                            <th>ESPECIALIDADE</th>
                            <th>PLANTÃO</th>
                            <th>CHEFIA DE EQUIPE</th>
                            <th>TELEFONE</th>
                            <th>E-MAIL</th>
                            <th>FÉRIAS/LICENÇA</th>
                        </tr>
                        <?php
                        $cabecalho = "";
                        while ($linha_result = mysql_fetch_assoc($qr)) {
                            ?>       

                            <?php if ($linha_result['tipo'] == "0") { ?>
                                <?php if ($cabecalho != $linha_result['tipo']) { ?>
                                    <?php $cabecalho = $linha_result['tipo']; ?>
                                    <tr>
                                        <th colspan="9">
                                            <h3>MÉDICOS - <?php echo $nome_meses[$mes2d] . " " . $anoR; ?> </h3>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>NOME</th>
                                        <th>CPF</th>
                                        <th>CRM</th>
                                        <th>ESPECIALIDADE</th>
                                        <th>PLANTÃO</th>
                                        <th>CHEFIA DE EQUIPE</th>
                                        <th>TELEFONE</th>
                                        <th>E-MAIL</th>
                                        <th>FÉRIAS/LICENÇA</th>

                                    </tr>
                                <?php } ?>
                            <?php } ?>

                            <?php if ($linha_result['tipo'] == "1") { ?>
                                <?php if ($cabecalho != $linha_result['tipo']) { ?>
                                    <?php $cabecalho = $linha_result['tipo']; ?>
                                    <tr>
                                        <th colspan="9">
                                            <h3>ENFERMAGEM - <?php echo $nome_meses[$mes2d] . " " . $anoR; ?></h3>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>NOME</th>
                                        <th>CPF</th>
                                        <th>COREN</th>
                                        <th>ESPECIALIDADE</th>
                                        <th >PLANTÃO 24HS RETROATIVO</th>
                                        <th >CHEFIA DE EQUIPE</th>
                                        <th>TELEFONE</th>
                                        <th>E-MAIL</th>
                                        <th>FÉRIAS/LICENÇA</th>

                                    </tr>
                                <?php } ?>
                            <?php } ?>

                            <?php if ($linha_result['tipo'] == "2") { ?>
                                <?php if ($cabecalho != $linha_result['tipo']) { ?>
                                    <?php $cabecalho = $linha_result['tipo']; ?>
                                    <tr>
                                        <th colspan="9">
                                            <h3>ADMINISTRATIVOS -  <?php echo $nome_meses[$mes2d] . " " . $anoR; ?></h3>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>NOME</th>
                                        <th>CPF</th>
                                        <th>MAT.</th>
                                        <th>ESPECIALIDADE</th>
                                        <th>PLANTÃO</th>
                                        <th>SETOR</th>
                                        <th>TELEFONE</th>
                                        <th>E-MAIL</th>
                                        <th>FÉRIAS/LICENÇA</th>

                                    </tr>
                                <?php } ?>
                            <?php } ?>

                            <?php if ($linha_result['tipo'] == "3") { ?>
                                <?php if ($cabecalho != $linha_result['tipo']) { ?>
                                    <?php $cabecalho = $linha_result['tipo']; ?>
                                    <tr>
                                        <th colspan="9">
                                            <h3>ODONTOLOGIA -  <?php echo $nome_meses[$mes2d] . " " . $anoR; ?></h3>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>NOME</th>
                                        <th>CPF</th>
                                        <th>CRO</th>
                                        <th>ESPECIALIDADE</th>
                                        <th>PLANTÃO</th>
                                        <th>SETOR</th>
                                        <th>TELEFONE</th>
                                        <th>E-MAIL</th>
                                        <th>FÉRIAS/LICENÇA</th>

                                    </tr>
                                <?php } ?>
                            <?php } ?>

                            <?php if ($linha_result['tipo'] == "4") { ?>
                                <?php if ($cabecalho != $linha_result['tipo']) { ?>
                                    <?php $cabecalho = $linha_result['tipo']; ?>
                                    <tr>
                                        <th colspan="9">
                                            <h3>FARMÁCIA -  <?php echo $nome_meses[$mes2d] . " " . $anoR; ?></h3>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>NOME</th>
                                        <th>CPF</th>
                                        <th>CRO</th>
                                        <th>ESPECIALIDADE</th>
                                        <th>PLANTÃO</th>
                                        <th>SETOR</th>
                                        <th>TELEFONE</th>
                                        <th>E-MAIL</th>
                                        <th>FÉRIAS/LICENÇA</th>

                                    </tr>
                                <?php } ?>
                            <?php } ?>

                            <?php if ($linha_result['tipo'] == "5") { ?>
                                <?php if ($cabecalho != $linha_result['tipo']) { ?>
                                    <?php $cabecalho = $linha_result['tipo']; ?>
                                    <tr>
                                        <th colspan="9">
                                            <h3>SERVIÇO SOCIAL E TECNICO DE RAIO X -  <?php echo $nome_meses[$mes2d] . " " . $anoR; ?></h3>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>NOME</th>
                                        <th>CPF</th>
                                        <th>CRESS</th>
                                        <th>ESPECIALIDADE</th>
                                        <th>PLANTÃO</th>
                                        <th>SETOR</th>
                                        <th>TELEFONE</th>
                                        <th>E-MAIL</th>
                                        <th>FÉRIAS/LICENÇA</th>
                                    </tr>
                                <?php } ?>
                            <?php } ?>

                            <tr>
                                <td><?php echo $linha_result['nome']; ?></td>
                                <td><?php echo $linha_result['cpf']; ?></td>
                                <td><?php echo $linha_result['conselho']; ?></td>
                                <td><?php echo $linha_result['especialidade']; ?></td>
                                <td><?php ?></td>
                                <td style="text-align: center">
                                    <?php
                                    if ($linha_result['tipo'] == 2 || $linha_result['tipo'] == 3 || $linha_result['tipo'] == 4 || $linha_result['tipo'] == 5) {
                                        echo $linha_result['setor'];
                                    }
                                    ?>
                                </td>
                                <td><?php echo $linha_result['tel_fixo']; ?></td>
                                <td><?php echo $linha_result['email']; ?></td>
                                <td><?php echo $linha_result['STATUS']; ?></td>
                            </tr>

                            <?php } ?>            
                        <?php } ?>            
                    </table>
                    
                </form>
                <?php include('../template/footer.php'); ?>
                <div class="clear"></div>
            </div>

            <script src="../js/jquery-1.10.2.min.js"></script>
            <!--<script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>-->
            <script src="../resources/js/bootstrap.min.js"></script>
            <script src="../resources/js/tooltip.js"></script>
            <script src="../resources/js/main.js"></script>
            <script src="../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
            <script src="../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
            <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
            <script src="../js/global.js"></script>
            
            <?php if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) { ?>
            <script>
                $(function(){
                    $("#form1").validationEngine();
                });
                
//                var tabela = $('#tabela').html();
//                var title = $('title').html();
//                $('#tabelaPdf').val(tabela);
//                $('#titlePdf').val(title);
            </script>
        <?php } ?>

    </body>
</html>
