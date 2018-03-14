<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}

include "../conn.php";
include "../wfunction.php";
include "../classes/RelatorioClass.php";

$usuario = carregaUsuario();
$rel = new Relatorio();

$opGrupo = $rel->arrayGrupoSelect();

/*
 * Por enquanto n�o h� relat�rios em outros m�dulos, ent�o, por hora fica assim.
 * Mas � para puchar do banco dps.
 */
$opModulo = array(2 => "Recursos Humanos");

$resposta = null;

if (isset($_REQUEST['salvar'])) {
    unset($_REQUEST['salvar']);
    if (isset($_REQUEST['id_relatorio']) && !empty($_REQUEST['id_relatorio'])) {
        $dados['id_relatorio'] = $_REQUEST['id_relatorio'];
    }
    $dados['id_modulo'] = $_REQUEST['id_modulo'];
    $dados['id_grupo'] = $_REQUEST['id_grupo'];
    $dados['nome'] = $_REQUEST['nome'];
    $dados['url'] = $_REQUEST['url'];
    $dados['descricao'] = $_REQUEST['descricao'];
    $resultado = $rel->salvar($dados);

    $resposta = "<div class='alerta back-green'><img src='../imagens/green-status.gif'> <strong>Salvo com sucesso!</strong></div>";
}
if (isset($_REQUEST['id'])) {
    $relatorio = $rel->carregaRelatorios(null, null, $_REQUEST['id']);
    $relatorio = $relatorio[0];
}
?>
<html>
    <head>
        <title>:: Intranet :: Formul�rio de Relat�ios</title>
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
        <style>
            .alerta{
                padding:10px;
                margin:10px;
            }
        </style>
        <script>
            $(function() {
                var id_destination = "projeto";
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, function(data) {
                    removeLoading();
                    $("#" + id_destination).html(data);
                    var selected = $("input[name=hide_" + id_destination + "]").val();
                    if (selected !== undefined) {
                        $("#" + id_destination).val(selected);
                    }
                    $('#projeto').trigger('change');
                }, "projeto");

                $("#projeto").change(function() {
                    var $this = $(this);
                    if ($this.val() != "-1") {
                        $.post('<?= $_SERVER['PHP_SELF'] ?>', {projeto: $this.val(), id_regiao: $('#regiao').val(), method: "carregafuncao"}, function(data) {
                            var selected = "";
                            if (data.stunid == 1) {
                                var unid = "<option value='-1'>� Selecione �</option>\n";
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
            });

            $(document).ready(function() {
                // instancia o validation engine no formul�rio
                $("#form1").validationEngine();
            });
            checkDate = function(field) {
                var date = field.val();
                if (date == -1) {
                    return 'Selecione uma Data';
                }
            };
        </script>
    </head>
    <body class="novaintra" >        
        <div id="content">
            <form  name="form" action="<?= $_SERVER['PHP_SELF'] ?>" id="form1" method="post" id="form">
                <div id="head">
                    <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>Formul�rio de Relat�rios</h2>
                    </div>
                </div>
                <br class="clear">
                <br/>

                <?= $resposta ?>
                <a href="listaRelatorios.php" class="botao">Voltar para Gest�o de Relat�rios</a>
                <br><br>
                <fieldset class="noprint">
                    <legend>Relat�rio</legend>
                    <div class="fleft">
                        <?php if(isset($relatorio['id_relatorio']) && !empty($relatorio['id_relatorio'])){ ?>
                        <input type="hidden" name="id_relatorio" id="id_relatorio" value="<?= $relatorio['id_relatorio'] ?>">
                        <?php } ?>
                        <p>
                            <label class="first">M�dulo</label>
                            <?= montaSelect($opModulo, $relatorio['id_modulo'], array('name' => "id_modulo", 'id' => 'id_modulo')); ?>
                        </p>
                        <p>
                            <label class="first">Grupo:</label>
                            <?= montaSelect($opGrupo, $relatorio['id_grupo'], array('name' => "id_grupo", 'id' => 'id_grupo')); ?>
                        </p>
                        <p>
                            <label class="first">Nome do Relat�rio:</label>
                            <input type="text" name="nome" id="nome" value="<?= $relatorio['nome']; ?>" style="width: 20em;">
                        </p>                        
                        <p>
                            <label class="first">URL do Relat�rio:</label>
                            <input type="text" name="url" id="url" value="<?= $relatorio['url'] ?>"  style="width: 20em;" placeholder="/intranet/relatorios/nome_do_relatorio.php"> 
                        </p>
                        <p>
                            <label class="first">Desci��o (Opcional):</label>
                            <textarea name="descricao" id="descricao" style="width: 20em;"><?= $relatorio['descricao'] ?></textarea>
                        </p>

                    </div>

                    <br class="clear"/>

                    <p class="controls" style="margin-top: 10px;">

                        <input type="submit" name="salvar" value="Salvar" id="salvar"/>
                    </p>
                </fieldset>
            </form>
        </div>
    </body>
</html>