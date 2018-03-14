<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a>";
    exit;
}

function printArr($arr) {
    echo '<pre>';
    print_r($arr);
    echo '</pre>';
}

include("../conn.php");
include("../classes/regiao.php");
include("../classes/projeto.php");
include("../classes/funcionario.php");
include("../classes_permissoes/regioes.class.php");
include("../classes_permissoes/acoes.class.php");
include("../wfunction.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$optRegiao = getRegioes();

$id_regiao = $usuario['id_regiao'];

$projetosOp = array("-1" => "« Selecione »");
$query = "SELECT id_projeto,nome FROM projeto WHERE id_regiao = '$id_regiao'";
$result = mysql_query($query) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
    $projetosOp[$row['id_projeto']] = $row['id_projeto'] . " - " . $row['nome'];
}

if (isset($_REQUEST['gerar'])) {
    $ano = $_REQUEST['ano'];
    $mes = str_pad($_REQUEST['mes'], 2, 0, STR_PAD_LEFT);
    $id_projeto = $_REQUEST['projeto'];
    $query = "SELECT a.*,
                b.nome AS curso_de,c.nome AS curso_para,
                b.valor AS salario_de,c.valor AS salario_para,
                d.nome AS banco_de,e.nome AS banco_para,
                i.nome AS sindicato_de,j.nome AS sindicato_para,
                h.nome AS clt_nome, h.cpf,
                DATE_FORMAT(a.data_proc,'%m/%Y') AS data_proc_br,
                (SELECT regiao FROM regioes WHERE id_regiao = a.id_regiao_de) AS regiao_de, 
                (SELECT regiao FROM regioes WHERE id_regiao = a.id_regiao_para) AS regiao_para,
                (SELECT nome FROM projeto WHERE id_projeto = a.id_projeto_de) AS projeto_de,
                (SELECT nome FROM projeto WHERE id_projeto = a.id_projeto_para) AS projeto_para,
                (SELECT unidade FROM unidade WHERE id_unidade = a.id_unidade_de) AS unidade_de,
                (SELECT unidade FROM unidade WHERE id_unidade = a.id_unidade_para) AS unidade_para,
                CONCAT(f.entrada_1,' - ',f.saida_1,' - ',f.entrada_2,' - ',f.saida_2) AS horario_de,
                CONCAT(g.entrada_1,' - ',g.saida_1,' - ',g.entrada_2,' - ',g.saida_2) AS horario_para,
                (SELECT nome FROM funcionario WHERE id_funcionario = a.id_usuario) AS usuario
                FROM rh_transferencias AS a
                INNER JOIN curso AS b ON (a.id_curso_de = b.id_curso)
                INNER JOIN curso AS c ON (a.id_curso_para = c.id_curso)
                INNER JOIN bancos AS d ON (a.id_banco_de = d.id_banco)
                INNER JOIN bancos AS e ON (a.id_banco_para = e.id_banco)
                INNER JOIN rh_horarios AS f ON (a.id_horario_de = f.id_horario)
                INNER JOIN rh_horarios AS g ON (a.id_horario_para = g.id_horario)
                INNER JOIN rh_clt AS h ON (a.id_clt = h.id_clt)
                LEFT JOIN rhsindicato AS i ON (a.id_sindicato_de = i.id_sindicato)
                LEFT JOIN rhsindicato AS j ON (a.id_sindicato_para = j.id_sindicato)
                WHERE (MONTH(a.data_proc) = '$mes' AND YEAR(a.data_proc) = '$ano') AND (a.id_regiao_de = '$id_regiao' OR a.id_regiao_para = '$id_regiao')
                ORDER BY h.nome";
    echo "<!-- $query -->";
    $result = mysql_query($query);
    $itens = array('regiao', 'projeto', 'unidade', 'curso', 'horario', 'banco', 'sindicato');
    $i = 0;
    while ($row = mysql_fetch_assoc($result)) {
        $list_transf[$row['id_transferencia']] = $row;
        foreach ($itens as $value) {
            $mudou[$i][$value] = ($row['id_' . $value . '_de'] != $row['id_' . $value . '_para']) ? true : false;
        }
        $mudou[$i]['salario'] = ($row['salario_de'] != $row['salario_para']) ? true : false;
        $i++;
    }


    echo '<!--';
    print_r($mudou);
    echo '-->';
}

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$mesSel = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m');
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>:: Intranet :: Relatório de Saída</title>
        
        <link href="../favicon.png" rel="shortcut icon" />
        
        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
         <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/global.js" type="text/javascript"></script>
        
    </head>
    
    <body>
        <?php include("../template/navbar_default.php"); ?>      
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Relatório de Transferência</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">

                <?php montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao')); ?>
                <?php montaSelect($projetosOp, $projetoSel, array('name' => "projeto", 'id' => 'projeto')); ?>
                
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório de Transferência</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="select" class="col-sm-4 control-label hidden-print">Mês/Ano Processamento</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect(mesesArray(), $mesSel, array('name' => 'mes', 'id' => 'mes', 'class' => 'form-control')); ?> 
                            </div>
                            
                            <div class="col-sm-3">
                                <?php echo montaSelect(anosArray(null, null, array('-1' => "« Selecione o ano »")), $anoSel, array('name' => 'ano', 'id' => 'ano', 'class' => 'form-control')); ?>
                                <?php echo $_REQUEST['data_ini']; ?> <span class="loader"></span>
                            </div>   
                            
                        </div>
                    </div>
                    <div class="panel-footer text-right hidden-print">
                        <?php if(isset($_POST[  'gerar'])) { ?>
                        <button type="button" onclick="tableToExcel('tbRelatorio', 'Relatório de Transferência Detalhada')" value="Exportar para Excel" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                        <?php } ?>
                            <button type="submit" name="gerar" id="gerar" value="Gerar" class="btn btn-primary"><span class="fa fa-filter"></span> Gerar</button>
                    </div>
                </div>
            </form>
            
            <?php if(isset($_POST['gerar'])){ ?>
                    <table id="tbRelatorio" class="table table-striped table-condensed table-bordered table-hover text-sm valign-middle" width="100%"> 
                        <thead>
                            <tr>
                                <th rowspan="2">#</th>
                                <th rowspan="2">NOME DO CLT</th>
                                <th rowspan="2">CPF</th>
                                <th rowspan="2">PROCESSAMENTO <br> (MÊS/ANO)</th>
                                <th colspan="2" class="text-center">UNIDADE</th>
                                <th colspan="2" class="text-center">FUNÇÃO</th>
                                <th colspan="2" class="text-center">SALÁRIO</th>
                            </tr>
                            <tr>
                                <th class="text-center">DE</th>
                                <th class="text-center">PARA</th>
                                <th class="text-center">DE</th>
                                <th class="text-center">PARA</th>
                                <th class-text-center>DE</th>
                                <th class="text-center">PARA</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 0;
                            foreach ($list_transf as $linha) {
                                ?>
                                <tr>
                                    <td style="text-align:center;"><?= $linha['id_clt'] ?></td>
                                    <td><?= $linha['clt_nome'] ?></td>
                                    <td style="text-align:center;"><?= $linha['cpf'] ?></td>
                                    <td style="text-align:center;"><?= $linha['data_proc_br'] ?></td>
                                    <td><?= "{$linha['id_unidade_de']} - {$linha['unidade_de']}" ?></td>
                                    <td <?= (!empty($mudou[$i]['unidade']))?"":"class=\"text-center\";" ?>><?= (!empty($mudou[$i]['unidade'])) ? "{$linha['id_unidade_para']} - {$linha['unidade_para']}":"-" ?></td>
                                    <td><?= "{$linha['id_curso_de']} - {$linha['curso_de']}" ?></td>
                                    <td <?= (!empty($mudou[$i]['curso']))?"":"class=\"text-center\";" ?>><?= (!empty($mudou[$i]['curso'])) ? "{$linha['id_curso_para']} - {$linha['curso_para']}":"-" ?></td>
                                    <td><?= "R$ " . number_format($linha['salario_de'], 2, ',', '.'); ?></td>
                                    <td <?= (!empty($mudou[$i]['salario']))?"":"class=\"text-center\";" ?>><?= (!empty($mudou[$i]['salario'])) ? "R$ " . number_format($linha['salario_para'], 2, ',', '.'):"-"; ?></td>
                                </tr>
                                <?php
                                $i++;
                            }
                            ?>
                        </tbody>
                    </table>
            
            <?php } ?>
            
            <?php include('../template/footer.php'); ?>
            <div class="clear"></div>
        </div>

        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        
        <script>
            $(function () {

                $('#projeto').change(function () {
                    $.post("<?= $_SERVER['PHP_SELF'] ?>", {method: 'getClt', projeto: $("#projeto").val()}, function (data) {
                        $("#sclt").html(data);
                    });
                });
                $('#projeto').trigger('change');


                $(".detalhes").click(function () {
                    var id_clt = $(this).data('id-clt');
                    var mes = $(this).data('mes');
                    var ano = $(this).data('ano');
                    var projeto = $(this).data('projeto');
                    $.post('relatorio_transferencia_detalhe.php', {id_clt: id_clt, mes: mes, ano: ano, projeto: projeto, detalhes: true}, function (data) {
                        thickBoxAlert('Detalhes', data, 900, 500);
                    });
                });
            });
        </script>
        
    </body>
</html>
