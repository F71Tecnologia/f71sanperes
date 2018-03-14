<?php
session_start();

if(empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/SaidaClass.php");
include("../../classes/global.php");

$container_full = true;

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);

$global = new GlobalClass();

$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>"Importação de Orçamento");
$breadcrumb_pages = array("Gestão de Orçamentos"=>"gestao_orcamentos.php");

if(isset($_POST['envio'])) {

    // Incluindo biblioteca PHPExcel
    require_once('phpexcel/Classes/PHPExcel/IOFactory.php');

    // Envio de arquivo
    $arquivo_nome = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'].'/intranet/finan/relatorios/uploads/').basename($_FILES['arquivo']['name']);
	move_uploaded_file($_FILES['arquivo']['tmp_name'], $arquivo_nome);

    // Abrindo arquivo
    try {
        $arquivo_tipo = PHPExcel_IOFactory::identify($arquivo_nome);
        $objeto = PHPExcel_IOFactory::createReader($arquivo_tipo)->load($arquivo_nome);
    } catch(Exception $erro) {
        die('Error loading file "'.pathinfo($arquivo_nome, PATHINFO_BASENAME).'": '.$erro->getMessage());
    }

    // VariÃ¡veis de suporte
    $planilhaNomes = $objeto->getSheetNames();
    $colunas = range('A', 'Z');

    // Criando arrays de descriÃ§Ãµes e valores
    $codigos = [];
    $descricoes = [];
    $unidades_nome = [];
    $unidades_id = [];
    $valores = [];

    // Percorrendo todas as planilhas
    foreach($objeto->getAllSheets() as $chave_planilha => $planilha) {

        // Nome da planilha
        $planilhaNome = $planilhaNomes[$chave_planilha];

        // Verificando se planilha Ã© de unidade
        if(preg_match("/^Unid|Unid |Unidade|Unidade [0-9]+$/", $planilhaNome)) {

            // Criando arrays da planilha
            $codigos[$chave_planilha] = [];
            $descricoes[$chave_planilha] = [];
            $valores[$chave_planilha] = [];

            // Definindo variÃ¡veis para percorrer toda a planilha
            $maiorLinha = $planilha->getHighestRow();
            $maiorColuna = $planilha->getHighestColumn();

            // Percorrendo planilha
            for($linha=3; $linha<=$maiorLinha; $linha++) {

                // Definindo variÃ¡vel para percorrer todas as linhas da planilha
                $dados = $planilha->rangeToArray('A'.$linha.':'.$maiorColuna.$linha, null, true, false);
//		print_array($dados);
                // Percorrendo linha
                foreach($dados[0] as $coluna => $dado) {

                    // Qual coluna e valor dela
                    @$coluna = $colunas[$coluna];
                    $dado = htmlentities($dado);

                    // Interromper loop corrente
                    if($dado == '#REF!' or
                        strstr($dado, htmlentities('TOTAL')) or
                        strstr($dado, htmlentities('SÃ£o Paulo'))
                    ) break;

                    // Inserindo nome da unidade
                    if(isset($coluna_unidade_nome) and $coluna_unidade_nome == $linha.$coluna) $unidades_nome[$chave_planilha] = $dado;

                    // Inserindo id da unidade
                    if(isset($coluna_unidade_id) and $coluna_unidade_id == $linha.$coluna) {
                        if(strstr($dado, ',')) {
                            $unidades_id[$chave_planilha] = trim(array_shift(explode(',', $dado)));
                        } elseif(strstr($dado, '.')) {
                            $unidades_id[$chave_planilha] = trim(array_shift(explode('.', $dado)));
                        } elseif(strstr($dado, ' e ')) {
                            $unidades_id[$chave_planilha] = explode(' e ', $dado);
                        } else {
                            $unidades_id[$chave_planilha] = $dado;
                        }
                    }

                    // Inserindo descriÃ§Ãµes
                    if(isset($coluna_descricao) and $coluna == $coluna_descricao) $descricoes[$chave_planilha][] = preg_replace('/^[0-9]+\.?([0-9]+)(\. | - )/', '', $dado);
//                    $descricoes[$chave_planilha][] = preg_replace('/^[0-9]+\.?([0-9]+)(\. | - )/', '', $dado);
                    // Inserindo cÃ³digos
                    if(isset($coluna_descricao) and $coluna == $coluna_descricao) {
                        preg_match('/^[0-9]+\.?[0-9]+/', $dado, $codigo);
                        if($codigo) $codigos[$chave_planilha][] = $codigo[0];
                    }

                    // Pegando Ãºltima chave criada na array descricoes
                    end($descricoes[$chave_planilha]);
                    $ultima_chave_descricao = key($descricoes[$chave_planilha]);

                    // Inserindo valores
                    if(isset($coluna_meses[$coluna]) and $coluna != $coluna_descricao) $valores[$chave_planilha][$ultima_chave_descricao][$coluna_meses[$coluna]] = number_format($dado, 2, ',', '.');

                    // Conhecendo coluna de nome de unidade
                    if($dado == htmlentities('UNIDADE:')) $coluna_unidade_nome = $linha.$colunas[array_search($coluna, $colunas)+2];

                    // Conhecendo coluna de id de unidade
                    if($dado == htmlentities('COD DA UNIDADE:')) $coluna_unidade_id = $linha.$colunas[array_search($coluna, $colunas)+2];

                    // Conhecendo coluna de descriÃ§Ãµes
                    if($dado == htmlentities('DESCRIÃ‡ÃƒO')) $coluna_descricao = $coluna;

                    // Conhecendo coluna de cada mÃªs
                    if(strstr($dado, htmlentities('MÃªs '))) {
                        $mes = str_replace(htmlentities('MÃªs '), '', $dado);
                        $coluna_meses[$coluna] = $mes;
                    }
                }
            }
        }

        // Zerando valores para prÃ³ximo loop
        $codigo = null;
        $coluna_descricao = null;
        $coluna_unidade_nome = null;
        $coluna_unidade_id = null;
        $coluna_meses = [];

    }
	
}
//print_array($descricoes);
$meses = range(1, 12);
?>
<!doctype html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Importação de Orçamento</title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="all">
        <link href="../../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="all">
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="all">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="<?=($container_full) ? 'container-full' : 'container'?>">
            <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro<small> - Importação de Orçamento</small></h2></div>
            <div class="panel panel-default hidden-print">
                <div class="panel-heading">Gestão de Orçamentos (Importação)</div>
            <?php if(!isset($_POST['envio'])) { ?>
            	<form id="envio-arquivo" method="post" enctype="multipart/form-data">
                    <div class="panel-body">
                        <input type="file" name="arquivo" id="arquivo" required>
                        <input type="hidden" name="envio" value="1">
                    </div>
                    <div class="panel-footer text-right">
                        <input type="submit" value="Importar" class="btn btn-info">
                    </div>
                </form>
            <?php } else { ?>
                <form id="opcoes-relatorio" class="form-horizontal" action="gestao_orcamentos_importacao_salvar.php" method="post">
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="select" class="col-lg-1 control-label">Unidade</label>
                            <div class="col-lg-5">
                                <select name="unidade" id="unidades" class="form-control">
                                    <option value="">Todas as unidades</option>
                                    <?php
                                    if(isset($unidades_nome)) {
                                        foreach($unidades_nome as $chave => $nome) {
                                            echo '<option value="'.$chave.'">'.$nome.'</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <label for="select" class="col-lg-1 control-label">Banco</label>
                            <div class="col-lg-3">
                                <?php echo montaSelect($global->carregaBancosByRegiao($usuario['id_regiao'], array(0 => "Selecione"), null), $bancoR, "id='banco' name='banco' class='required[custom[select]] form-control'"); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="select" class="col-lg-1 control-label">Início</label>
                            <div class="col-lg-5">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="data_inicio" name="data_inicio" value="<?php echo date('d/m/Y'); ?>">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                            <label for="select" class="col-lg-1 control-label">Duração</label>
                            <div class="col-lg-3">
                                <select name="duracao" id="duracao" class="form-control">
                                <?php foreach($meses as $mes) {
                                    $selecionado = ($mes == 12) ? 'selected="selected"' : null;
                                    $texto = ($mes == 1) ? 'mês' : 'meses';
                                    echo '<option value="'.$mes.'" '.$selecionado.'>'.$mes.' '.$texto.'</option>';
                                } ?>
                                </select>
                            </div>
                        </div>
                        <p>&nbsp;</p>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" id="relatorio"></table>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <button type="submit" class="btn btn-success" name="gerar" id ="gerar"><i class="fa fa-plus"></i> Salvar</button>
                        <input type="hidden" name="unidades_nome" value='<?php echo json_encode($unidades_nome); ?>'>
                        <input type="hidden" name="unidades_id" value='<?php echo json_encode($unidades_id); ?>'>
                        <input type="hidden" name="codigos" value='<?php echo json_encode($codigos); ?>'>
                        <input type="hidden" name="descricoes" value='<?php echo json_encode($descricoes); ?>'>
                        <input type="hidden" name="valores" value='<?php echo json_encode($valores); ?>'>
                    </div>
                </form>
            <?php } ?>
            </div>
            <?php include('../../template/footer.php'); ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../resources/js/highcharts/highcharts.js"></script>
        <script src="../../resources/js/highcharts/highcharts.drilldown.js"></script>
        <script src="../../resources/js/highcharts/highcharts.exporting.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../resources/js/financeiro/detalhado.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.maskMoney.js"></script>
        <script>			 
            $(document).ready(function(){

            <?php if(isset($_POST['envio'])) { ?>

                    var unidades = <?php echo json_encode($unidades_nome); ?>;
                    var descricoes = <?php echo json_encode($descricoes); ?>;
                    var codigos = <?php echo json_encode($codigos); ?>;
                    var valores = <?php echo json_encode($valores); ?>;
                    var meses = <?php echo json_encode($meses); ?>;

                    var $unidades = $('#unidades');
                    var $data_inicio = $('#data_inicio');
                    var $duracao = $('#duracao');
                    var $selectsData = $('#data_inicio, #duracao');
                    var $relatorio = $('#relatorio');

                    var todasUnidades = [];
                    $('#unidades option').each(function(index){
                        if(index==0) return true;
                        todasUnidades.push($(this).val());
                    });

                    var retorno;

                    gerar();

                    $data_inicio.datepicker({
                        dateFormat: 'dd/mm/yy',
                        changeMonth: true,
                        changeYear: true,
                        yearRange: '2005:c+1'
                    });

                    //if(!$unidades.val()) $selectsData.prop('disabled', true);

                    $unidades.change(function(){
                        //$selectsData.prop('disabled', ($(this).val() ? false : true));
                        gerar();
                    });

                    $selectsData.change(function(){
                        gerar();
                    });

                    function gerar() {
                        retorno = '';
                        conteudo();
                        rodape();
                        colocarConteudo();
                    }

                    function conteudo() {
                        var unidades = ($unidades.val()) ? [$unidades.val()] : todasUnidades;
                        for(i in unidades) {
                            adicionarConteudo('<tr><th colspan="2">'+$('#unidades option[value="'+unidades[i]+'"]').html()+'</th></tr><tr><th>Código</th><th>Descrição</th>');
                            for(m=1; m<=parseInt($duracao.val()); m++) {
                                adicionarConteudo('<th> Mês '+m+'</th>');
                            }
                            for(j in descricoes[unidades[i]]) {
                                if(codigos[unidades[i]][j] !== undefined && descricoes[unidades[i]][j] != '#N/A') {
                                    adicionarConteudo('</tr><tr><td>'+codigos[unidades[i]][j]+'</td><td>'+descricoes[unidades[i]][j]+'</td>');
                                    for(k=1; k<=parseInt($duracao.val()); k++) {
                                        adicionarConteudo('<td>'+valores[unidades[i]][j][k]+'</td>');
                                    }
                                }
                            }
                        }
                    }

                    function rodape() {
                        adicionarConteudo('</tr>');
                    }

                    function adicionarConteudo(conteudo) {
                        retorno += conteudo;
                    }

                    function colocarConteudo() {
                        $relatorio.html(retorno);
                    }

            <?php } ?>

            });
        </script>
    </body>
</html>
