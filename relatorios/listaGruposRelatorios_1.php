<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}

include "../conn.php";
include "../classes/funcionario.php";
include "../classes/RelatorioClass.php";
include '../classes_permissoes/regioes.class.php';
include "../wfunction.php";
include "../classes_permissoes/acoes.class.php";

$usuario = carregaUsuario();
$relatorio = new Relatorio();

if (isset($_REQUEST['method'])) {
    $id = $_REQUEST['id'];
    switch ($_REQUEST['method']) {
        case 'excluir':
            $retorno = $relatorio->excluirGrupo($id);
            echo json_encode(array('status' => $retorno));
            exit();
        case 'habilitar':
            $dados = array('status' => '1');
            $retorno = $relatorio->editarGrupo($dados, $id);
            echo json_encode(array('status' => $retorno));

            exit();
        case 'desabilitar':
            $dados = array('status' => '0');
            $retorno = $relatorio->editarGrupo($dados, $id);
            echo json_encode(array('status' => $retorno));
            exit();
    }
}


$projeto = montaQueryFirst("projeto", "nome", array('id_projeto' => $id_projeto));
$sql = "SELECT * FROM grupo_relatorio order by nome";
echo "<!-- {$sql} -->";
$qr_relatorio = mysql_query($sql) or die(mysql_error());
$num_rows = mysql_num_rows($qr_relatorio);

$qtd_hab = mysql_fetch_assoc(mysql_query("SELECT COUNT(id_grupo) as hab FROM grupo_relatorio WHERE status = 1"));
$qtd_des = mysql_fetch_assoc(mysql_query("SELECT COUNT(id_grupo) as des FROM grupo_relatorio WHERE status = 0"));
?>
<html>
    <head>
        <title>:: Intranet :: Transferências por Unidade</title>
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
            $(document).ready(function() {
                $(".excluir").click(function() {
                    if (confirm("Tem certeza que quer excluir permanentemente esse relatório? Essa ação não pode ser desfeita")) {
                        var id = $(this).data("id");
                        $.post('<?= $_SERVER['PHP_SELF'] ?>', {id: id, method: 'excluir'}, function(data) {
                            if (data.status) {
                                alert("Exlcuido com sucesso!");
                                window.location.reload();
                            } else {
                                alert("Erro ao exlcuir.");
                            }
                        }, 'json');
                    }
                });
                $(".habilita").click(function() {
                    var id = $(this).data("id");
                    $.post('<?= $_SERVER['PHP_SELF'] ?>', {id: id, method: 'habilitar'}, function(data) {
                        if (data.status) {
                            alert("Habilitado com sucesso!");
                            window.location.reload();
                        } else {
                            alert("Erro ao habilitar.");
                        }
                    }, 'json');
                });
                $(".desabilita").click(function() {
                    var id = $(this).data("id");
                    $.post('<?= $_SERVER['PHP_SELF'] ?>', {id: id, method: 'desabilitar'}, function(data) {
                        if (data.status) {
                            alert("Desabilitado com sucesso!");
                            window.location.reload();
                        } else {
                            alert("Erro ao desabilitar.");
                        }
                    }, 'json');
                });
            });
        </script>
    </head>
    <body class="novaintra" >        
        <div id="content">
            <form  name="form" action="" id="form1" method="post" id="form">
                <div id="head">
                    <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>Gestão de Grupos de Relatórios</h2>
                    </div>
                </div>
                <br class="clear">
                <br/>
                <a href="form_relatorio.php" class="botao">Adicionar novo</a>
                <a href="listaRelatorios.php" class="botao">Gestão de Relatórios</a>
                <?php if (!empty($qr_relatorio)) { ?>
                    <p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" value="Exportar para Excel" class="exportarExcel"></p>    
                    <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" class="grid" width="100%" style="page-break-after:auto;"> 
                        <thead>
                            <tr>
                                <th colspan="6"><?= (!isset($_REQUEST['todos_projetos'])) ? $projeto['nome'] : 'TODOS OS PROJETOS' ?></th>
                            </tr>
                            <tr>                                
                                <th>MÓDULO</th>
                                <th>NOME</th>
                                <th>DESCRIÇÃO</th>
                                <th colspan="3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($row_rel = mysql_fetch_assoc($qr_relatorio)) {
                                $class = ($cont++ % 2 == 0) ? "even" : "odd";
                                $cor = ($row_rel['status']) ? '' : 'back-red';
                                ?>
                                <tr class="<?php echo $class . " " . $cor ?>">
                                    <td> <?php echo ($row_rel['id_modulo'] == 2) ? "Recursos Humanos" : "" ?></td>
                                    <td><?php echo $row_rel['nome'] ?></td>
                                    <td> <?php echo $row_rel['descricao']; ?></td>

                                    <td style="text-align:center;"> <a href="form_grupo_rel.php?id=<?php echo $row_rel['id_grupo']; ?>"><img src="../imagens/icon-edit.gif" id="editarInf" title="Editar"></a></td>
                                    <td style="text-align:center;"> <a href="#" data-id="<?php echo $row_rel['id_grupo']; ?>" class="excluir"><img src="../imagens/icones/icon-trash.gif" id="editarInf" title="Excluir"></a></td>
                                    <td style="text-align:center;"> 
                                        <?php if ($row_rel['status'] == 1) { ?>
                                            <a href="#" data-id="<?php echo $row_rel['id_grupo']; ?>" class="desabilita"><img src="../imagens/icones/icon-delete.gif" id="editarInf" title="Desabilitar"></a>
                                        <?php } else { ?> 
                                            <a href="#" data-id="<?php echo $row_rel['id_grupo']; ?>" class="habilita"><img src="../imagens/icones/icon-accept.gif" id="editarInf" title="Habilitar"></a>
                                            <?php } ?>
                                    </td>

                                </tr>                                
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3"><strong>TOTAL:</strong></td>
                                <td colspan="3" align="center"><?php echo $num_rows ?></td>
                            </tr>
                            <tr>
                                <td colspan="3"><strong>TOTAL HABILITADOS:</strong></td>
                                <td colspan="3" align="center"><?php echo $qtd_hab['hab'] ?></td>
                            </tr>
                            <tr>
                                <td colspan="3"><strong>TOTAL DESABILITADOS:</strong></td>
                                <td colspan="3" align="center"><?php echo $qtd_des['des'] ?></td>
                            </tr>
                        </tfoot>
                    </table>
                <?php } ?>
            </form>
        </div>
    </body>
</html>