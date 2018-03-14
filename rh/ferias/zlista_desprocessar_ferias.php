<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include "../../conn.php";
include "../../classes/funcionario.php";
include '../../classes_permissoes/regioes.class.php';
include "../../wfunction.php";
include "../../classes_permissoes/acoes.class.php";

$usuario = carregaUsuario();
$optRegiao = getRegioes();
$ACOES = new Acoes();

if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    $cont = 0;

    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    
//    $id_unidade = $_REQUEST['unidade'];

    $condicao = (!isset($_REQUEST['todos_projetos'])) ? " AND f1.projeto = '$id_projeto' " : '';

    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto' => $id_projeto));
    $sql = "SELECT f1.id_ferias,f1.id_clt,f1.nome,
            DATE_FORMAT(f1.data_aquisitivo_ini, '%d/%m/%Y') AS data_aquisitivo_ini,
            DATE_FORMAT(f1.data_aquisitivo_fim,'%d/%m/%Y') AS data_aquisitivo_fim,
            DATE_FORMAT(f1.data_ini,'%d/%m/%Y') AS data_ini,
            DATE_FORMAT(f1.data_fim,'%d/%m/%Y') AS data_fim,
            DATE_FORMAT(f1.data_retorno,'%d/%m/%Y') AS data_retorno,
            f1.dias_ferias,
            DATE_FORMAT(f1.data_proc,'%d/%m/%Y %T') AS data_proc
            FROM rh_ferias AS f1 
            LEFT JOIN rh_ferias AS f2 ON (f1.id_clt = f2.id_clt AND f1.id_ferias < f2.id_ferias)
            WHERE f2.id_ferias IS NULL
            AND f1.regiao = '$id_regiao'
            AND f1.`status`=1 
            $condicao
            ORDER BY id_ferias DESC";
    echo "<!-- {$sql} -->";
    $qr_relatorio = mysql_query($sql) or die(mysql_error());
    $num_rows = mysql_num_rows($qr_relatorio);
}

$regiaoSel = (isset($_REQUEST['reg'])) ? $_REQUEST['reg'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['pro'])) ? $_REQUEST['pro'] : null;

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $regiaoSel;
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : $projetoSel;

?>
<html>
    <head>
        <title>:: Intranet :: Desprocessar Férias</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css"/>

        <link href="../../favicon.ico" rel="shortcut icon" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>

        <script>
            $(function() {
                var id_destination = "projeto";
                $('#regiao').ajaxGetJson("../../methods.php", {method: "carregaProjetos"}, function(data) {
                    removeLoading();
                    $("#" + id_destination).html(data);
                    var selected = $("input[name=hide_" + id_destination + "]").val();
                    if (selected !== undefined) {
                        $("#" + id_destination).val(selected);
                    }
                    $('#projeto').trigger('change');
                }, "projeto");
            });

            $(document).ready(function() {
                // instancia o validation engine no formulário
                $("#form1").validationEngine();
                
                $('#todos_projetos').click(function (){
                    $("#projeto").removeClass();
                 });
            });
            
            checkSelect = function(field) {
                var date = field.val();
                if (date == -1) {
                    return 'Selecione uma opção';
                }
            };
            
            
        </script>
    </head>
    <body class="novaintra" >        
        <div id="content">
            <form  name="form" action="" id="form1" method="post" id="form">
                <div id="head">
                    <img src="../../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>Desprocessar Férias</h2>
                    </div>
                </div>
                <br class="clear">
                <br/>
                <fieldset class="noprint">
                    <legend>Relatório</legend>
                    <div class="fleft">
                        <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
                        <input type="hidden" name="hide_unidade" id="hide_projeto" value="<?php echo $unidadeSel ?>" />
                        <p><label class="first">Região:</label> <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'validate[required,funcCall[checkSelect]]')); ?> </p>                        
                        <p><label class="first">Projeto:</label> <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto','class'=>'validate[required,funcCall[checkSelect]]')); ?> </p>
                    </div>
                    <br class="clear"/>
                    <p class="controls" style="margin-top: 10px;">
                        <?php
                        ///permissão para VISUALIZAR TODOS OS PROJETOS AO MESMO TEMPO
                        if ($ACOES->verifica_permissoes(85)) {
                            ?>
                            <input type="submit" name="todos_projetos" value="Gerar de Todos Projetos" id="todos_projetos"/>
                        <?php } ?>
                        <input type="submit" name="gerar" value="Gerar" id="gerar"/>
                    </p>
                </fieldset>

                <?php if ($num_rows!=0 && (isset($_POST['gerar']) || isset($_REQUEST['todos_projetos']))) { ?>
                    <p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" value="Exportar para Excel" class="exportarExcel"></p>    
                    <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" class="grid" width="100%" style="page-break-after:auto;"> 
                        <thead>
                            <tr>
                                <th colspan="8"><?= (!isset($_REQUEST['todos_projetos'])) ? $projeto['nome'] : 'TODOS OS PROJETOS' ?></th>
                            </tr>
                            <tr>
                                <th>NOME</th>
                                <th>INÍCIO DO AQUISITIVO</th>
                                <th>FIM DO AQUISITIVO</th>
                                <th>INÍCIO DASFÉRIAS</th>
                                <th>FIM DAS FÉRIAS</th>
                                <th>RETORNO DAS FÉRIAS</th>
                                <th>QTD DIAS</th>
                                <th>DATA DE CRIAÇÃO</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($row_rel = mysql_fetch_assoc($qr_relatorio)) {
                                $class = ($cont++ % 2 == 0) ? "even" : "odd"
                                ?>
                                <tr class="<?php echo $class ?>">
                                    <td><a href="rh_ferias_desprocessar.php?clt=<?= $row_rel['id_clt'] ?>&AMP;ferias=<?= $row_rel['id_ferias'] ?>&AMP;tela=1"><?php echo $row_rel['nome'] ?></a></td>
                                    <td align="center"><?php echo $row_rel['data_aquisitivo_ini']; ?></td>
                                    <td align="center"><?php echo $row_rel['data_aquisitivo_fim']; ?></td>
                                    <td align="center"><?php echo $row_rel['data_ini']; ?></td>
                                    <td align="center"><?php echo $row_rel['data_fim']; ?></td>
                                    <td align="center"><?php echo $row_rel['data_retorno']; ?></td>                       
                                    <td align="center"><?php echo $row_rel['dias_ferias']; ?></td>                       
                                    <td align="center"><?php echo $row_rel['data_proc']; ?></td>                       
                                </tr>                                
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="7"><strong>TOTAL:</strong></td>
                                <td align="center"><?php echo $num_rows ?></td>
                            </tr>
                        </tfoot>
                    </table>
                <?php }elseif($num_rows == 0 && (isset($_POST['gerar']) || isset($_REQUEST['todos_projetos']))) {
                            echo "<div id='message-box' class='message-red'>Nenhum registro encontrado para o filtro selecionado.</div>";
                       }  ?>
            </form>
        </div>
    </body>
</html>