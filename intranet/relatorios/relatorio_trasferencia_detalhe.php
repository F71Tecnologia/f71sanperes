<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}

function printArr($arr) {
    echo '<pre>';
    print_r($arr);
    echo '</pre>';
}

include "../conn.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include "../wfunction.php";
include "../funcoes.php";

$usuario = carregaUsuario();
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
    $query = "SELECT id_transferencia,id_clt,
                DATE_FORMAT(data_proc,'%m/%Y') AS data_proc_br,
                (SELECT nome FROM rh_clt WHERE id_clt = a.id_clt) AS clt_nome,
                (SELECT cpf FROM rh_clt WHERE id_clt = a.id_clt) AS cpf
                FROM rh_transferencias AS a
                WHERE (a.id_projeto_de = '$id_projeto' OR a.id_projeto_para = '$id_projeto')
                AND MONTH(a.data_proc) = '$mes' AND YEAR(a.data_proc) = '$ano'";
    echo "<!-- $query -->";
    $result = mysql_query($query);
    while ($row = mysql_fetch_assoc($result)) {
        $list_transf[$row['id_transferencia']] = $row;
    }

} else if (isset($_REQUEST['detalhes'])) {
    $ano = $_REQUEST['ano'];
    $mes = str_pad($_REQUEST['mes'], 2, 0, STR_PAD_LEFT);
    $id_projeto = $_REQUEST['projeto'];
    $id_clt = $_REQUEST['id_clt'];
    $query = "SELECT a.*,b.nome AS curso_nome,b.valor AS valor_de,c.valor AS valor_para,h.nome AS clt_nome,
                (SELECT regiao FROM regioes WHERE id_regiao = a.id_regiao_de) AS regiao_de, 
                (SELECT regiao FROM regioes WHERE id_regiao = a.id_regiao_para) AS regiao_para,
                (SELECT nome FROM projeto WHERE id_projeto = a.id_projeto_de) AS projeto_de,
                (SELECT nome FROM projeto WHERE id_projeto = a.id_projeto_para) AS projeto_para,
                (SELECT unidade FROM unidade WHERE id_unidade = a.id_unidade_de) AS unidade_de,
                (SELECT unidade FROM unidade WHERE id_unidade = a.id_unidade_para) AS unidade_para,
                (SELECT nome FROM funcionario WHERE id_funcionario = a.id_usuario) AS usuario
                FROM rh_transferencias AS a
                INNER JOIN curso AS b ON (a.id_curso_de = b.id_curso)
                INNER JOIN curso AS c ON (a.id_curso_para = c.id_curso)
                INNER JOIN rh_horarios AS d ON (a.id_horario_de = d.id_horario)
                INNER JOIN rh_horarios AS e ON (a.id_horario_para = e.id_horario)
                INNER JOIN rhsindicato AS f ON (a.id_sindicato_de = f.id_sindicato)
                INNER JOIN rhsindicato AS g ON (a.id_sindicato_para = g.id_sindicato)
                INNER JOIN rh_clt AS h ON (a.id_clt = e.id_clt)
                WHERE (a.id_projeto_de = '$id_projeto' OR a.id_projeto_para = '$id_projeto')
                AND MONTH(a.data_proc) = '$mes' AND YEAR(a.data_proc) = '$ano'
                AND id_clt = $id_clt
                ORDER BY id_transferencia";
    echo "<!-- $query -->";
}

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$mesSel = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m');
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
?>
<html>
    <head>
        <title>:: Intranet :: Relatório Saídas</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/global.js" type="text/javascript"></script>

        <script>
            $(function () {

                $('#projeto').change(function () {
                    $.post("<?= $_SERVER['PHP_SELF'] ?>", {method: 'getClt', projeto: $("#projeto").val()}, function (data) {
                        $("#sclt").html(data);
                    });
                });
                $('#projeto').trigger('change');
            });
        </script>
    </head>
    <body class="novaintra" >
        <div id="content">
            <form  name="form" action="" method="post" id="form">
                <div id="head">
                    <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>Relatório de Transferência</h2>
                    </div>
                </div>
                <br class="clear">
                <br/>
                <fieldset class="noprint">
                    <legend>Relatório de Transferência</legend>
                    <!--<p><label class="first">Região:</label> <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao')); ?> </p>-->                        
                    <p><label class="first">Projeto:</label> <?php echo montaSelect($projetosOp, $projetoSel, array('name' => "projeto", 'id' => 'projeto')); ?> </p>
                    <p><label class="first">Mês/Ano de Referência:</label> <?php echo montaSelect(mesesArray(), $mesSel, array('name' => 'mes', 'id' => 'mes')); ?> / <?php echo montaSelect(anosArray(date('Y') - 5, date('Y') + 5, array('-1' => "« Selecione o ano »")), $anoSel, array('name' => 'ano', 'id' => 'ano')); ?></p>
                    <!--<p><label class="first">Periodo:</label> <input name="data_ini" id="data_ini" type="text" size="10" maxlength="10" class="date" value="<?php echo $_REQUEST['data_ini']; ?>"> <label style="font-weight: bold;">até</label> <input name="data_fim" id="data_fim" type="text" size="10" maxlength="10" class="date" value="<?php echo $_REQUEST['data_fim']; ?>"></p>-->
                    <p class="controls">
                        <input type="submit" name="gerar" value="Gerar" id="gerar"/>
                    </p>
                </fieldset>
                <?php if (!empty($list_transf)) { ?>
                    <p id="excel" style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Relatório')" value="Exportar para Excel" class="exportarExcel"></p>
                    <table id="tbRelatorio" class="grid" width="100%" style="border-collapse:collapse;"> 
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>NOME DO CLT</th>
                                <th>CPF</th>
                                <th>PROCESSAMENTO <br> (MÊS/ANO)</th>
                                <th>DETALHES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($list_transf as $linha) { ?>
                            <tr>
                                <td style="text-align:center;"><?= $linha['id_clt'] ?></td>
                                <td><?= $linha['clt_nome'] ?></td>
                                <td style="text-align:center;"><?= $linha['cpf'] ?></td>
                                <td style="text-align:center;"><?= $linha['data_proc_br'] ?></td>
                                <td style="text-align:center;"><a href="#" class="detalhes" data-id-clt="<?= $linha['id_clt'] ?>"><img src="../imagens/icones/icon-docview.gif"></a></td>
                            </tr>
                            <?php } ?>
                            <tr>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                <?php } ?>
            </form>
        </div>
    </body>
</html>