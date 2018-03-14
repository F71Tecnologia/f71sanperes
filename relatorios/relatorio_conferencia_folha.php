<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}

include "../conn.php";
include "../classes/funcionario.php";
include "../classes/EventoClass.php";
include '../classes_permissoes/regioes.class.php';
include('../funcoes.php');
include "../wfunction.php";
include "../classes_permissoes/acoes.class.php";

$usuario = carregaUsuario();
$optRegiao = getRegioes();
$ACOES = new Acoes();
$eventos = new Eventos();

if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {

    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    $ano = $_REQUEST['ano'];
    $mes = str_pad($_REQUEST['mes'], 2, "0", STR_PAD_LEFT);


    switch ($_REQUEST['tipo-consulta']) {
        case 1: // rescisao
            $sql = "SELECT a.id_clt,a.nome,a.cpf,b.rescisao_complementar,
            (SELECT nome FROM curso WHERE curso.id_curso = a.id_curso) AS curso,
            DATE_FORMAT(b.data_adm, '%d/%m/%Y') AS data_adm,
            DATE_FORMAT(b.data_demi, '%d/%m/%Y') AS data_demi,
            b.data_proc,
            b.total_liquido,
            b.id_recisao
            FROM rh_clt AS a
            INNER JOIN rh_recisao AS b ON (b.id_clt = a.id_clt)
            WHERE a.id_regiao = '$id_regiao' AND a.id_projeto = '$id_projeto'
            AND b.status = 1
            AND DATE_FORMAT(b.data_demi, '%Y-%m') = '$ano-$mes'
            ORDER BY a.nome";
            break;
        case 2: // ativos
            $sql = "SELECT ativo.*,C.unidade FROM 
                        (SELECT a.id_clt,a.nome,a.cpf,a.id_unidade,
                                (SELECT unidade_de FROM rh_transferencias WHERE id_clt=a.id_clt AND unidade_de <> unidade_para AND data_proc >= '{$ano}-{$mes}-01' ORDER BY id_transferencia ASC LIMIT 1) AS de,
                                (SELECT unidade_para FROM rh_transferencias WHERE id_clt=a.id_clt AND unidade_de <> unidade_para AND data_proc <= '{$ano}-{$mes}-01' ORDER BY id_transferencia DESC LIMIT 1) AS para,
                                (SELECT nome FROM curso WHERE curso.id_curso = a.id_curso) AS curso,
                        DATE_FORMAT(data_entrada, '%d/%m/%Y') AS data_entrada
                        FROM rh_clt AS a
                        WHERE status in(10,200) AND id_regiao ='{$id_regiao}' AND id_projeto = '{$id_projeto}' AND tipo_contratacao = '2'
                        AND DATE_FORMAT(data_entrada,'%Y-%m') <= '$ano-$mes'
                        ORDER BY nome) AS ativo
                    LEFT JOIN unidade AS C ON (IF(ativo.para IS NOT NULL,C.unidade=ativo.para, IF(ativo.de IS NOT NULL,C.unidade=ativo.de,C.id_unidade=ativo.id_unidade)))";
            break;
        case 3: // férias
            $sql = "SELECT a.id_clt,a.nome,a.cpf,
            (SELECT nome FROM curso WHERE curso.id_curso = a.id_curso) AS curso,
            DATE_FORMAT(b.data_ini, '%d/%m/%Y') AS ferias_ini,
            DATE_FORMAT(b.data_fim, '%d/%m/%Y') AS ferias_fim,
            b.total_liquido, b.ir
            FROM rh_clt AS a
            INNER JOIN rh_ferias AS b ON (a.id_clt = b.id_clt)
            WHERE a.id_regiao = '$id_regiao' AND a.id_projeto = '$id_projeto'
            AND b.status = 1
            AND '{$ano}-{$mes}' BETWEEN DATE_FORMAT(b.data_ini,'%Y-%m') AND DATE_FORMAT(b.data_fim,'%Y-%m')
            ORDER BY a.nome";
            break;
        case 4: // eventos
            $listaEventos = $eventos->listarCltEmEvento($id_regiao, $id_projeto, '', '', '', '', TRUE);
//            $sql = "SELECT a.id_clt,a.nome,a.cpf,b.nome_status,b.cod_status,
//            (SELECT nome FROM curso WHERE curso.id_curso = a.id_curso) AS curso,
//            DATE_FORMAT(b.data, '%d/%m/%Y') AS data_ini,
//            DATE_FORMAT(b.data_retorno, '%d/%m/%Y') AS data_retorno
//            FROM rh_clt AS a
//            INNER JOIN rh_eventos AS b ON (a.id_clt = b.id_clt)
//            WHERE a.id_regiao = '$id_regiao' AND a.id_projeto = '$id_projeto'
//            AND b.cod_status != 40
//            AND b.status = 1
//            AND '$ano-$mes' BETWEEN DATE_FORMAT(b.data,'%Y-%m') AND DATE_FORMAT(b.data_retorno,'%Y-%m')
//            ORDER BY a.nome";
            break;

        default:
            break;
    }


    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto' => $id_projeto));
    $arrayX = array(1, 2, 3); // valores q precisao executar querys
    if (in_array($_REQUEST['tipo-consulta'], $arrayX)) {
        echo "<!-- {$sql} -->";
        $qr_relatorio = mysql_query($sql) or die(mysql_error());
        $num_rows = mysql_num_rows($qr_relatorio);
    }
}

$regiaoSel = (isset($_REQUEST['reg'])) ? $_REQUEST['reg'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['pro'])) ? $_REQUEST['pro'] : null;
$funcaoSel = (isset($_REQUEST['funcao'])) ? $_REQUEST['funcao'] : null;

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $regiaoSel;
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : $projetoSel;


$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
$mesSel = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m');
$tipoSel = (isset($_REQUEST['tipo-consulta'])) ? $_REQUEST['tipo-consulta'] : null;

/////////////////////////// array de anos //////////////////////////////////////
$arrayAnos[-1] = '« Selecione o Ano »';
for ($i = date('Y'); $i >= date('Y') - 10; $i--) {
    $arrayAnos[$i] = $i;
}
?>
<html>
    <head>
        <title>:: Intranet :: Conferência de Folha</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css"/>

        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script src="../js/global.js" type="text/javascript"></script>

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

                var myArray = [];
                $(".ev").each(function () {
                    if ($.inArray($(this).data('id'), myArray) !== -1) {
                        $("." + $(this).data('id')).addClass("alert");
                        //console.log($.inArray($(this).data('id'),myArray));
                    }
                    myArray.push($(this).data('id'));
                });
            });

            $(document).ready(function () {
                // instancia o validation engine no formulário
                $("#form1").validationEngine();
            });
            checkSelect = function (field) {
                var date = field.val();
                if (date <= 0 || date == null || date == '') {
                    return 'Selecione um Valor';
                }
            };
        </script>
    </head>
    <body class="novaintra" >        
        <div id="content">
            <form  name="form" id="form1" action="" method="post" id="form">
                <div id="head">
                    <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>Relatório de Conferência de Folha</h2>
                    </div>
                </div>
                <br class="clear">
                <br/>


                <fieldset class="noprint">
                    <legend>Relatório</legend>
                    <div class="fleft">
                        <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
                        <input type="hidden" name="hide_funcao" id="hide_funcao" value="<?php echo $funcaoSel ?>" />
                        <p><label class="first">Região:</label> <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'validate[required,funcCall[checkSelect]]')); ?> </p>                        
                        <p><label class="first">Projeto:</label> <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'validate[required,funcCall[checkSelect]]')); ?> </p>
                        <p>
                            <label class="first">Tipo:</label>
                            <select name="tipo-consulta" id="tipo-consulta" class="validate[required,funcCall[checkSelect]]">
                                <option value="0">-- Selecione --</option>
                                <option value="1" <?= ($tipoSel == 1) ? 'selected="selected"' : '' ?>>Rescisão</option>
                                <option value="2" <?= ($tipoSel == 2) ? 'selected="selected"' : '' ?>>Ativos</option>
                                <option value="3" <?= ($tipoSel == 3) ? 'selected="selected"' : '' ?>>Férias</option>
                                <option value="4" <?= ($tipoSel == 4) ? 'selected="selected"' : '' ?>>Eventos</option>
                            </select>
                        </p>
                        <p><label class="first">Período:</label> <?php echo montaSelect(mesesArray(), $mesSel, array('name' => "mes", 'id' => 'mes', 'class' => 'validate[required,funcCall[checkSelect]]')); ?> / <?php echo montaSelect($arrayAnos, $anoSel, array('name' => "ano", 'id' => 'ano', 'class' => 'validate[required,funcCall[checkSelect]]')); ?></p>

                    </div>

                    <br class="clear"/>

                    <p class="controls" style="margin-top: 10px;">
                        <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                        <?php
                        ///permissão para VISUALIZAR TODOS OS PROJETOS AO MESMO TEMPO
                        if ($ACOES->verifica_permissoes(85)) {
                            ?>
                            <input type="submit" name="todos_projetos" value="Gerar de Todos Projetos" id="todos_projetos"/>
                        <?php } ?>
                        <input type="submit" name="gerar" value="Gerar" id="gerar"/>
                    </p>
                </fieldset>

                <?php
                if (isset($_POST['gerar']) || isset($_REQUEST['todos_projetos'])) {
                    if ($_REQUEST['tipo-consulta'] == 1) { // rescisao
                        ?>
                        <p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" value="Exportar para Excel" class="exportarExcel"></p>    
                        <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" class="grid" width="100%" style="page-break-after:auto;"> 
                            <thead>
                                <tr>
                                    <th colspan="9"><?php echo $projeto['nome'] ?> - RESCISÃO</th>
                                </tr>
                                <tr>
                                    <th>ID</th>
                                    <th>NOME</th>
                                    <th>CPF</th>
                                    <th>FUNÇÃO</th>
                                    <th>DATA DE ADMISSÃO</th>   
                                    <th>DATA DE DEMISSÃO</th>   
                                    <th>COMPLEMENTAR</th>   
                                    <th>VALOR LIQUIDO</th>   
                                    <th></th>   
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total = 0;
                                while ($row_rel = mysql_fetch_assoc($qr_relatorio)) {
                                    $class = ($cont++ % 2 == 0) ? "even" : "odd"
                                    ?>
                                    <tr class="<?php echo $class ?>">
                                        <td><?php echo $row_rel['id_clt'] ?></td>
                                        <td><?php echo $row_rel['nome'] ?></td>
                                        <td> <?php echo $row_rel['cpf']; ?></td>
                                        <td> <?php echo $row_rel['curso']; ?></td>
                                        <td align="center"><?php echo $row_rel['data_adm']; ?></td>                       
                                        <td align="center"><?php echo $row_rel['data_demi']; ?></td>                       
                                        <td align="center"><?php echo ($row_rel['rescisao_complementar']) ? "SIM" : "NÃO"; ?></td>                       
                                        <td align="center">R$ <?php echo number_format($row_rel['total_liquido'], 2, ',', '.'); ?></td>                       
                                        <td style="text-align:center;">
                                            <?php
                                            $id_clt = $row_rel['id_clt'];
                                            $id_rescisao = $row_rel['id_recisao'];
                                            $link = str_replace('+', '--', encrypt("$id_regiao&$id_clt&$id_rescisao"));
                                            if (substr($row_rel['data_proc'], 0, 10) >= '2013-04-04') {
                                                $link_nova_rescisao = "nova_rescisao_2.php?enc=$link";
                                            } else {
                                                $link_nova_rescisao = "nova_rescisao.php?enc=$link";
                                            }
                                            ?>
                                            <a href="../rh/recisao/<?= $link_nova_rescisao; ?>" class="link" target="_blank" title="Visualizar Rescisão"><img src="../imagens/pdf.gif" border="0"></a>
                                        </td>
                                    </tr>        
                                    <?php $total += $row_rel['total_liquido']; ?>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="7">Total:</td>
                                    <td align="center">Participantes: <?php echo $num_rows; ?></td>
                                    <td align="center">R$ <?php echo number_format($total, 2, ',', '.'); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                        <?php
                    } else if ($_REQUEST['tipo-consulta'] == 2) { // ativos
                        ?>
                        <p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" value="Exportar para Excel" class="exportarExcel"></p>    
                        <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" class="grid" width="100%" style="page-break-after:auto;"> 
                            <thead>
                                <tr>
                                    <th colspan="5"><?php echo $projeto['nome'] ?> - ATIVOS</th>
                                </tr>
                                <tr>
                                    <th>ID</th>
                                    <th>NOME</th>
                                    <th>CPF</th>
                                    <th>FUNÇÃO</th>
                                    <th>DATA DE ADMISSÃO</th>   
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($row_rel = mysql_fetch_assoc($qr_relatorio)) {
                                    $class = ($cont++ % 2 == 0) ? "even" : "odd";

                                    // verificação da data para dar ênfase nos funcionários que entraram no mês selecionado
                                    $data = explode('/', $row_rel['data_entrada']);
                                    $data = "{$data[2]}-{$data[1]}";
                                    $data_comp = "$ano-$mes";
                                    if ($data == $data_comp) {
                                        $class .= " back-red";
                                    }
                                    ?>
                                    <tr class="<?php echo $class ?>">
                                        <td><?php echo $row_rel['id_clt'] ?></td>
                                        <td><?php echo $row_rel['nome'] ?></td>
                                        <td> <?php echo $row_rel['cpf']; ?></td>
                                        <td> <?php echo $row_rel['curso']; ?></td>
                                        <td align="center"><?php echo $row_rel['data_entrada']; ?></td>             
                                    </tr>                                
                                <?php } ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4">Total:</td>
                                    <td align="center"><?php echo $num_rows ?></td>
                                </tr>
                            </tfoot>
                        </table>
                        <?php
                    } else if ($_REQUEST['tipo-consulta'] == 3) { // férias
                        ?>
                        <p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" value="Exportar para Excel" class="exportarExcel"></p>    
                        <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" class="grid" width="100%" style="page-break-after:auto;"> 
                            <thead>
                                <tr>
                                    <th colspan="9"><?php echo $projeto['nome'] ?> - FÉRIAS</th>
                                </tr>
                                <tr>
                                    <th>ID</th>
                                    <th>NOME</th>
                                    <th>CPF</th>
                                    <th>FUNÇÃO</th>
                                    <th>INÍCIO DAS FÉRIAS</th>   
                                    <th>FIM DAS FÉRIAS</th>   
                                    <th>IR</th>   
                                    <th>VALOR LIQUIDO</th> 
                                    <th></th> 
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total = 0;
                                while ($row_rel = mysql_fetch_assoc($qr_relatorio)) {
                                    $class = ($cont++ % 2 == 0) ? "even" : "odd"
                                    ?>
                                    <tr class="<?php echo $class ?>">
                                        <td><?php echo $row_rel['id_clt'] ?></td>
                                        <td><?php echo $row_rel['nome'] ?></td>
                                        <td> <?php echo $row_rel['cpf']; ?></td>
                                        <td> <?php echo $row_rel['curso']; ?></td>
                                        <td align="center"><?php echo $row_rel['ferias_ini']; ?></td>                       
                                        <td align="center"><?php echo $row_rel['ferias_fim']; ?></td>                       
                                        <td align="center">R$ <?php echo $row_rel['ir']; ?></td> 
                                        <td align="center">R$ <?php echo $row_rel['total_liquido']; ?></td>    
                                        <td align="center">
                                            <?php
                                            $link = encrypt("$id_regiao&2&{$row_rel['id_clt']}");
                                            $link2 = str_replace("+", "--", $link);
                                            ?>
                                            <a href='../rh/ferias/index.php?enc=<?= $link2 ?>' target="_blank"><img src="../imagens/pdf.gif" border="0"></a>
                                        </td>
                                    </tr>          
                                    <?php
                                    $total += $row_rel['total_liquido'];
                                    $total_ir += $row_rel['ir'];
                                    ?>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5">Total:</td>
                                    <td align="center"><?php echo "Participantes: " . $num_rows ?></td>
                                    <td align="center"><?php echo "R$ " . number_format($total_ir, 2, ",", "."); ?></td>
                                    <td align="center"><?php echo "R$ " . number_format($total, 2, ",", "."); ?></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                        <?php
                    } else if ($_REQUEST['tipo-consulta'] == 4) { // EVENTOS
//                        echo '<pre>';
//                        print_r($listaEventos);
//                        echo '</pre>';
                        ?>
                        <p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" value="Exportar para Excel" class="exportarExcel"></p>    
                        <?php foreach ($listaEventos as $listaProjeto) { ?>
                            <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" class="grid" width="100%" style="page-break-after:auto;"> 
                                <thead>
                                    <tr>
                                        <th colspan="8"><?php echo $projeto['nome'] ?> - EVENTOS</th>
                                    </tr>
                                    <tr>
                                        <th>ID</th>
                                        <th>NOME</th>
                                        <th>CPF</th>
                                        <th>FUNÇÃO</th>
                                        <th>INÍCIO DO EVENTO</th>   
                                        <th>FIM DO EVENTO</th>   
                                        <th>STATUS</th>   
                                        <th></th>   
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
//                                while ($row_rel = mysql_fetch_assoc($qr_relatorio)) {
                                    foreach ($listaProjeto as $row_rel) {
                                        $class = ($cont++ % 2 == 0) ? "even" : "odd"
                                        ?>
                                        <tr class="<?php echo $class ?> <?php echo $row_rel['id_clt'] ?>">
                                            <td class="ev" data-id="<?php echo $row_rel['id_clt'] ?>"><?php echo $row_rel['id_clt'] ?></td>
                                            <td><?php echo $row_rel['nome'] ?></td>
                                            <td> <?php echo $row_rel['cpf']; ?></td>
                                            <td> <?php echo $row_rel['curso']; ?></td>
                                            <td align="center"><?php echo $row_rel['data']; ?></td>                       
                                            <td align="center"><?php echo $row_rel['data_retorno']; ?></td>                       
                                            <td align="center"><?php echo $row_rel['nome_status']; ?></td>   
                                            <td align="center"><a href="../rh/eventos1/index.php?tela=acao_evento&clt=<?= $row_rel['id_clt'] ?>&regiao=<?= $id_regiao ?>" target="_blank">Formulário de Eventos</a></td>                       
                                        </tr>                                
                                    <?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="7">Total:</td>
                                        <td align="center"><?php echo count($listaProjeto); ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                            <?php
                        }
                    }
                }
                ?>
            </form>
        </div>
    </body>
</html>