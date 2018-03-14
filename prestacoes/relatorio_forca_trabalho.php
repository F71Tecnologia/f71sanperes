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
$erros = 0;
$idsErros = array();
$msg = "";

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

/**/

//----- CARREGA PROJETOS COM PRESTAÃƒâ€¡Ãƒâ€¢ES FINALIZADAS NO MES SELECIONADO
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "finalizados") {
    
}

$meses = mesesArray(null);
$anos = anosArray(null, null, array("-1" => "« Selecione »"));

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIORIO SELECIONADO */
$projR = (isset($_REQUEST['projetos'])) ? $_REQUEST['projetos'] : null;
$mesR = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : null;
$anoR = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$breadcrumb_config = array("nivel" => "../", "key_btn" => "3", "area" => "Recursos Humanos", "ativo" => "Relatório Força de Trabalho", "id_form" => "form1");
$breadcrumb_pages = array("Lista Projetos"=>"../rh/ver.php");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Relatório Força de Trabalho</title>

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
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RELATÓRIO - <small>Força de Trabalho</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Dados</div>
                    <div class="panel-body">

                        <div class="form-group" >
<!--                            <label for="select" class="col-sm-3 control-label hidden-print" >Região</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                            </div>-->

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
                            <button type="button" onclick="tableToExcel('tabela', 'Relatório CNES')" value="Exportar para Excel" class="btn btn-success" ><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                        <?php } ?>
                            <button type="submit" name="gerar" value="Gerar" id="gerar" class="btn btn-primary"><span class="fa fa-filter"></span> Gerar</button>
                    </div>
                </div>


                <?php if (!empty($qr_unidades) && (isset($_POST['gerar']))) { ?>        
                    <table class="table table-striped table-condensed table-bordered table-hover text-sm valign-middle" id="forcaTrabalho">
                        
                        <thead>
                            
                            <tr>
                               <th>MATRÍCULA</th>
                                <!--<th>ID FUNCIONAL</th>-->
                                <th>VÍNCULO</th>
                                <th>VINCULO EMPREGATÍCIO</th>
                                <th>NOME</th>
                                <th>SEXO</th>
                                <th>CPF</th>
                                <th>CARGO</th>
                                <th>ESPECIALIDADE</th>
                                <th>ÁREA</th>
                                <th>NÍVEL</th>
                                <th>DATA DE ADMISSÃO</th>
                                <th>LOTAÇÃO</th>
                                <th>LOTAÇÃO GERAL</th>
                                <th>SITUAÇÃO (MÉDICO 24h)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            /**
                             * @author Juarez Garritano
                             * Na tela antiga (relatorio_forca_trabalho_old.php) não mostrava os campos MATRICULA e ID FUNCIONAL, não estavam sendo mostrados
                             * então adicionei esse campo na query e na tabela e deixei o campo ID FUNCIONAL comentado.
                             */
                            $qr = mysql_query("SELECT A.matricula, A.sexo, A.cpf, C.nome AS locacao, D.nome AS oss,
                                                UCASE(A.nome) AS nome, UCASE(B.nome) AS cargo, UCASE(E.nome) AS nivel, DATE_FORMAT(A.data_entrada,'%d/%m/%Y') AS data_admissao,
                                                IF (A.tipo_contratacao = '1','RPA','CLT') AS tipo_contratacao,
                                                IF (B.campo2 = B.nome,'SEM ESPECIALIDADE', UCASE(B.campo2)) AS especialidade,
                                                IF (B.area = 'Saúde','FIM','MEIO') AS area_func,
                                                IF (F.horas_semanais != '', F.horas_semanais , '') AS horas
                                                FROM rh_folha AS FL
                                                LEFT JOIN rh_folha_proc AS FP ON(FL.id_folha = FP.id_folha)
                                                LEFT JOIN rh_clt AS A ON(A.id_clt = FP.id_clt)
                                                LEFT JOIN curso AS B ON(A.id_curso = B.id_curso)
                                                LEFT JOIN projeto AS C ON(A.id_projeto = C.id_projeto)
                                                LEFT JOIN master AS D ON(C.id_master = D.id_master)
                                                LEFT JOIN escolaridade AS E ON(A.escolaridade = E.id)
                                                LEFT JOIN rh_horarios AS F ON(F.funcao = B.id_curso)
                                                WHERE A.id_regiao = '{$regiao}' AND A.id_projeto = '{$_REQUEST['projetos']}' AND FL.mes = '{$mes2d}' AND FL.ano = '{$anoR}' AND FL.tipo_terceiro = 3 GROUP BY A.id_clt ORDER BY A.nome");
                                                
                        while ($linha_result = mysql_fetch_assoc($qr)) {
                        ?>
                        
                        <tr>
                            <td><?php echo $linha_result['matricula']; ?></td>
                            <!--<td></td>-->    
                            <td><?php echo $linha_result['tipo_contratacao']; ?></td>
                            <td>OSS</td>
                            <td><?php echo $linha_result['nome']; ?></td>
                            <td style="text-align: center"><?php echo $linha_result['sexo']; ?></td>
                            <td><?php echo $linha_result['cpf']; ?></td>
                            <td><?php echo $linha_result['cargo']; ?></td>
                            <td><?php echo $linha_result['especialidade']; ?></td>
                            <td><?php echo $linha_result['area_func']; ?></td>
                            <td><?php echo $linha_result['nivel']; ?></td>
                            <td><?php echo $linha_result['data_admissao']; ?></td>
                            <td><?php echo $linha_result['locacao']; ?></td>
                            <td style="text-align: center;"><?php echo $linha_result['oss']; ?></td>
                            <td style="text-align: center"><?php echo $linha_result['horas'] . "h"; ?></td>
                        </tr>

                        </tbody>
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

        <script>
            $(function () {
                var id_destination = "projeto";
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, function (data) {
                    removeLoading();
                    $("#" + id_destination).html(data);
                    var selected = $("input[name=hide_" + id_destination + "]").val();
                    if (selected !== undefined) {
                        $("#" + id_destination).val(selected);
                    }
                    $('#projeto').trigger('change');
                }, "projeto");

                $("#projeto").change(function () {
                    var $this = $(this);
                    if ($this.val() != "-1") {
                        $.post('<?= $_SERVER['PHP_SELF'] ?>', {projeto: $this.val(), id_regiao: $('#regiao').val(), method: "carregafuncao"}, function (data) {
                            var selected = "";
                            if (data.stunid == 1) {
                                var unid = "<option value='-1'>« Selecione »</option>\n";
                                for (var i in data.unidade) {
                                    selected = "";
                                    if (i == "<?= $unidadeSel ?>") {
                                        selected = "selected=\"selected\" ";
                                    }
                                    unid += "<option value='" + i + "' " + selected + ">" + data.unidade[i] + "</option>\n";
                                }
                                $("#unidade").html(unid);
                            }
                        }, "json");
                    }
                });

                $('#projeto').ajaxGetJson("../methods.php", {method: "carregaFuncoes", default: "2"}, null, "funcao");
            });

            $(document).ready(function () {
                // instancia o validation engine no formulário
                $("#form1").validationEngine();
            });
            checkDate = function (field) {
                var date = field.val();
                if (date == -1) {
                    return 'Selecione uma Data';
                }
            };
        </script>

    </body>
</html>
