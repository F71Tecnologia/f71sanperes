<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}

include "../conn.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include "../wfunction.php";
include "../classes_permissoes/acoes.class.php";

$usuario = carregaUsuario();
$optRegiao = getRegioes();
$ACOES = new Acoes();
$id_evento = $_REQUEST['id'];

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'excluir_anexo_evento') {
    $id_anexo = $_REQUEST['id_anexo'];
    $arquivo = mysql_fetch_assoc(mysql_query("SELECT * FROM anexo_eventos WHERE id_anexo = $id_anexo"));
    $retorn = unlink("../anexo_atestado/{$arquivo['nome']}");
    $return2 = mysql_query("DELETE FROM anexo_eventos WHERE id_anexo=$id_anexo");
    echo json_encode(array('status' => ($retorn && $return2)));
    exit();
}

$query = "SELECT *,DATE_FORMAT(data,'%d/%m/%Y %T') AS data FROM anexo_eventos WHERE id_evento = '{$id_evento}' ORDER BY id_anexo";
$result = mysql_query($query);
list($id_clt, $id_regiao, $id_projeto, $clt_nome) = mysql_fetch_row(mysql_query("SELECT id_clt,id_regiao,id_projeto,nome FROM rh_clt WHERE id_clt = (SELECT id_clt FROM rh_eventos WHERE id_evento = $id_evento)"));

switch ($_REQUEST['voltar']) {
    case 1:
        $link = "eventos1/index.php?tela=acao_evento&clt=$id_clt&regiao=$id_regiao";
        break;

    default:
        $link = "ver_clt.php?reg=$id_regiao&clt=$id_clt&ant=0&pro=$id_projeto&pagina=bol";
        break;
}

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
                    if (confirm("Deseja realmente excluir esse anexo? Essa ação é irreversível.")) {
                        var id_anexo = $(this).data('id');
                        $.post("<?= $_SERVER['PHP_SELF'] ?>", {method: 'excluir_anexo_evento', id_anexo: id_anexo}, function(data) {
                            if (data.status) {
                                alert("Exclusão realizada com sucesso.");
                                $("#tr-" + id_anexo).remove();
                            }
                        }, 'json');
                    }
                });
            });
        </script>
        <style>
            .icon-anexo{
                width: 20px;
                height: 20px;
            }
        </style>
    </head>
    <body class="novaintra" >        
        <div id="content">
            <form  name="form" action="" id="form1" method="post" id="form">
                <div id="head">
                    <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2></h2>
                        <h3>Anexos dos Eventos</h3>
                    </div>
                </div>
                <br class="clear">

                <a href="<?= $link ?>" class="botao"><< Voltar</a>
                <br>
                <div><strong>Funcionário:</strong> <?= $clt_nome ?></div>
                <?php if (!empty($result)) { ?>
                    <p style="text-align: left; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" value="Exportar para Excel" class="exportarExcel"></p>    
                    <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" class="grid" width="100%" style="page-break-after:auto;"> 
                        <thead>
                            <tr>
                                <th>DATA</th>
                                <th>ARQUIVO</th>
                                <th>VISUALIZAR</th>
                                <th>EXCLUIR</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($row_rel = mysql_fetch_assoc($result)) {
                                $class = ($cont++ % 2 == 0) ? "even" : "odd"
                                ?>
                                <tr class="<?php echo $class ?>"  id="tr-<?php echo $row_rel['id_anexo'] ?>">
                                    <td align="center"><?php echo $row_rel['data'] ?></td>                     
                                    <td><?php echo $row_rel['nome'] ?></td>                     
                                    <td align="center"><a href="../anexo_atestado/<?php echo $row_rel['nome'] ?>" target="_blank"><img src="../imagens/ver_anexo.gif" class="icon-anexo" title="Ver"></a></td>
                                    <td align="center">
                                        <?php if ($ACOES->verifica_permissoes(92)) { ?>
                                            <a href="#" data-id="<?php echo $row_rel['id_anexo'] ?>" class="excluir"><img src="../imagens/icon-excluir.png" title="Excluir"></a>
                                        <?php } else { ?>
                                            <img src="../imagens/icon-excluir-old.png" title="Excluir">
                                        <?php } ?>
                                    </td>
                                </tr>                                
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } ?>
            </form>
        </div>
    </body>
</html>