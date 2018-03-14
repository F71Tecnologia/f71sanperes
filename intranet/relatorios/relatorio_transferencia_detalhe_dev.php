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
    $itens = $_REQUEST['campos'];
    print_r($itens);
    $i = 0;
    while ($row = mysql_fetch_assoc($result)) {
        $list_transf[$row['id_transferencia']] = $row;
        foreach ($itens as $value) {
            $class_red[$i][$value] = ($row['id_' . $value . '_de'] != $row['id_' . $value . '_para']) ? true : false;
        }
        $class_red[$i]['salario'] = ($row['salario_de'] != $row['salario_para']) ? true : false;
        $i++;
    }


    echo '<!--';
    print_r($class_red);
    echo '-->';
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
        <style>
            .text-center{
                text-align: center;
            }
            .chckbx{
                padding: 0 10px;
            }
        </style>
        <script>
            $(function () {

                $('#projeto').change(function () {
                    $.post("<?= $_SERVER['PHP_SELF'] ?>", {method: 'getClt', projeto: $("#projeto").val()}, function (data) {
                        $("#sclt").html(data);
                    });
                });
                $('#projeto').trigger('change');


                $("input[name=slct]").click(function () {
                    if ($(this).val() == 1) {
                        // colocar select para todos
                    }
                });
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
                    <!--<p><label class="first">Projeto:</label> <?php echo montaSelect($projetosOp, $projetoSel, array('name' => "projeto", 'id' => 'projeto')); ?> </p>-->
                    <p><label class="first">Mês/Ano de Processamento:</label> <?php echo montaSelect(mesesArray(), $mesSel, array('name' => 'mes', 'id' => 'mes')); ?> / <?php echo montaSelect(anosArray(date('Y') - 5, date('Y'), array('-1' => "« Selecione o ano »")), $anoSel, array('name' => 'ano', 'id' => 'ano')); ?></p>
                    <!--<p><label class="first">Periodo:</label> <input name="data_ini" id="data_ini" type="text" size="10" maxlength="10" class="date" value="<?php echo $_REQUEST['data_ini']; ?>"> <label style="font-weight: bold;">até</label> <input name="data_fim" id="data_fim" type="text" size="10" maxlength="10" class="date" value="<?php echo $_REQUEST['data_fim']; ?>"></p>-->
                    <p><label class="first">Campos de Exibição:</label>
                        <label class="chckbx" for="slct-tds">
                            <input type="radio" nome="slct" id="slct-tds" value="1">Selecionar Todos
                        </label>
                        <label class="chckbx" for="slct-nd">
                            <input type="radio"  nome="slct" id="slct-nd" value="0">Selecionar Nenhum
                        </label>
                    </p>
                    <p>
                        <label class="first">&emsp;</label>
                        <label for="campo_regiao" class="chckbx">
                            <input type="checkbox" name="campos[]" id="campo_regiao" value="regiao"> Região
                        </label>
                        <label for="campo_projeto" class="chckbx">
                            <input type="checkbox" name="campos[]" id="campo_projeto" value="projeto"> Projeto
                        </label>
                        <label for="campo_unidade" class="chckbx">
                            <input type="checkbox" name="campos[]" id="campo_unidade" value="unidade"> Unidade
                        </label>
                        <label for="campo_funcao" class="chckbx">
                            <input type="checkbox" name="campos[]" id="campo_funcao" value="curso"> Função
                        </label>
                        <label for="campo_hora" class="chckbx">
                            <input type="checkbox" name="campos[]" id="campo_hora" value="horario"> Horário
                        </label>
                        <label for="campo_salario" class="chckbx">
                            <input type="checkbox" name="campos[]" id="campo_salario" value="salario"> Salário
                        </label>
                        <label for="campo_sind" class="chckbx">
                            <input type="checkbox" name="campos[]" id="campo_sind" value="sindicato"> Sindicato
                        </label>
                        <label for="campo_banco" class="chckbx">
                            <input type="checkbox" name="campos[]" id="campo_banco" value="banco"> Banco
                        </label>
                    </p>

                    <p class="controls">
                        <input type="submit" name="gerar" value="Gerar" id="gerar"/>
                    </p>
                </fieldset>
                <?php if (!empty($list_transf)) { ?>
                    <p id="excel" style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Relatório')" value="Exportar para Excel" class="exportarExcel"></p>
                    <table id="tbRelatorio" class="grid" width="100%" style="border-collapse:collapse;"> 
                        <thead>
                            <tr>
                                <th rowspan="2">#</th>
                                <th rowspan="2">NOME DO CLT</th>
                                <th rowspan="2">CPF</th>
                                <th rowspan="2">PROCESSAMENTO <br> (MÊS/ANO)</th>
                                <th colspan="2">REGIÃO</th>
                                <th colspan="2">PROJETO</th>
                                <th colspan="2">UNIDADE</th>
                                <th colspan="2">FUNÇÃO</th>
                                <th colspan="2">Horário</th>
                                <th colspan="2">SALÁRIO</th>
                                <th colspan="2">SINDICATO</th>
                                <th colspan="2">BANCO</th>
                            </tr>
                            <tr>
                                <th>DE</th>
                                <th>PARA</th>
                                <th>DE</th>
                                <th>PARA</th>
                                <th>DE</th>
                                <th>PARA</th>
                                <th>DE</th>
                                <th>PARA</th>
                                <th>DE</th>
                                <th>PARA</th>
                                <th>DE</th>
                                <th>PARA</th>
                                <th>DE</th>
                                <th>PARA</th>
                                <th>DE</th>
                                <th>PARA</th>
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
                                    <?php if (in_array('regiao', $items)) { ?>
                                        <td><?= "{$linha['id_regiao_de']} - {$linha['regiao_de']}" ?></td>
                                        <td <?= (!empty($class_red[$i]['regiao'])) ? "" : "class=\"text-center\";" ?>><?= (!empty($class_red[$i]['regiao'])) ? "{$linha['id_regiao_para']} - {$linha['regiao_para']}" : "-" ?></td>
                                    <?php } ?>
                                    <?php if (in_array('projeto', $items)) { ?>
                                        <td><?= "{$linha['id_projeto_de']} - {$linha['projeto_de']}" ?></td>
                                        <td <?= (!empty($class_red[$i]['projeto'])) ? "" : "class=\"text-center\";" ?>><?= (!empty($class_red[$i]['projeto'])) ? "{$linha['id_projeto_para']} - {$linha['projeto_para']}" : "-" ?></td>
                                    <?php } ?>
                                    <?php if (in_array('unidade', $items)) { ?>
                                        <td><?= "{$linha['id_unidade_de']} - {$linha['unidade_de']}" ?></td>
                                        <td <?= (!empty($class_red[$i]['unidade'])) ? "" : "class=\"text-center\";" ?>><?= (!empty($class_red[$i]['unidade'])) ? "{$linha['id_unidade_para']} - {$linha['unidade_para']}" : "-" ?></td>
                                    <?php } ?>
                                    <?php if (in_array('curso', $items)) { ?>
                                        <td><?= "{$linha['id_curso_de']} - {$linha['curso_de']}" ?></td>
                                        <td <?= (!empty($class_red[$i]['curso'])) ? "" : "class=\"text-center\";" ?>><?= (!empty($class_red[$i]['curso'])) ? "{$linha['id_curso_para']} - {$linha['curso_para']}" : "-" ?></td>
                                    <?php } ?>
                                    <?php if (in_array('horario', $items)) { ?>
                                        <td><?= $linha['horario_de'] ?></td>
                                        <td <?= (!empty($class_red[$i]['horario'])) ? "" : "class=\"text-center\";" ?>><?= (!empty($class_red[$i]['horario'])) ? $linha['horario_para'] : "-" ?></td>
                                    <?php } ?>
                                    <?php if (in_array('salario', $items)) { ?>
                                        <td><?= "R$ " . number_format($linha['salario_de'], 2, ',', '.'); ?></td>
                                        <td <?= (!empty($class_red[$i]['salario'])) ? "" : "class=\"text-center\";" ?>><?= (!empty($class_red[$i]['salario'])) ? "R$ " . number_format($linha['salario_para'], 2, ',', '.') : "-"; ?></td>
                                    <?php } ?>                                           
                                    <?php if (in_array('sindicato', $items)) { ?>
                                        <td><?= "{$linha['id_sindicato_de']} - {$linha['sindicato_de']}" ?></td>
                                        <td <?= (!empty($class_red[$i]['sindicato'])) ? "" : "class=\"text-center\";" ?>><?= (!empty($class_red[$i]['sindicato'])) ? "{$linha['id_sindicato_para']} - {$linha['sindicato_para']}" : "-" ?></td>
                                    <?php } ?>
                                    <?php if (in_array('banco', $items)) { ?>
                                        <td><?= "{$linha['id_banco_de']} - {$linha['banco_de']}" ?></td>
                                        <td <?= (!empty($class_red[$i]['banco'])) ? "" : "class=\"text-center\";" ?>><?= (!empty($class_red[$i]['banco'])) ? "{$linha['id_banco_para']} - {$linha['banco_para']}" : "-" ?></td>
                                    <?php } ?>
                                </tr>
                                <?php
                                $i++;
                            }
                            ?>
                        </tbody>
                    </table>
                <?php } ?>
            </form>

        </div>
    </body>
</html>