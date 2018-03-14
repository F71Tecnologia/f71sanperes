<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a> ";
} else {
    include "../conn.php";
    include('../wfunction.php');
    $usuario = carregaUsuario();


    $id_user = $_COOKIE['logado'];
    $result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
    $row_user = mysql_fetch_array($result_user);
    $result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
    $row_master = mysql_fetch_array($result_master);


    $regiao = $usuario['id_regiao'];

    $result_projetos = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$regiao' AND status_reg = '1'");

    if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'unidade') {
        $projeto = $_REQUEST['projeto'];
        $unidades = "<option>« Selecione »</option>";
        $result_unidades = mysql_query("SELECT * FROM unidade WHERE campo1 = '$projeto'");
        while ($row_unidades = mysql_fetch_array($result_unidades)) {
            $unidades .= "<option value=\"{$row_unidades['id_unidade']}\">{$row_unidades['unidade']}</option>";
        }
        echo utf8_encode($unidades);
        exit();
    }

    $projetosOp = array("-1" => "« Selecione »");
    $query = "SELECT id_projeto,nome FROM projeto WHERE id_regiao = '$regiao'";
    $result = mysql_query($query) or die(mysql_error());
    while ($row = mysql_fetch_assoc($result)) {
        $projetosOp[$row['id_projeto']] = $row['id_projeto'] . " - " . $row['nome'];
    }
    ?>
    <html>
        <head>
            <meta http-equiv=Content-Type content="text/html; charset=iso-8859-1">
            <title>Relatórios de Gestão</title>
            <link href="css/estrutura.css" rel="stylesheet" type="text/css">
            <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
            <script>
                $(document).ready(function() {
                    $("#projeto").change(function() {
                        $.post("<?= $_SERVER['PHP_SELF'] ?>", {method: 'unidade', projeto: $("#projeto option:selected").val()}, function(data) {
                            $("#unidade").html(data);
                        });
                    });
                    $("#projeto").trigger("change");
                });
            </script>
        </head>
        <body>
            <div id="corpo">
                <div id="topo">
                    <?php include "include/topo.php"; ?>
                </div>
                <div id="conteudo">
                    <h1 style="margin:50px;"><span>RELATÓRIOS</span> RELATÓRIOS DE GESTÃO</h1>

                    <form action="relatorio2.php" method="post" name="form1" id="form1" style="margin-bottom:50px;">     
                        <table cellspacing="0" cellpadding="4" class="relacao">
                            <tr>
                                <td colspan="2" align="center">RELATÓRIO DE PARTICIPANTES DO PROJETO</td>
                            </tr>
                            <tr>
                                <td align="right">Selecione o Projeto:</td>
                                <td><?php echo montaSelect($projetosOp, $projetoSel, array('name' => "projeto", 'id' => 'projeto')); ?></td>
                            </tr>
                            <tr>
                                <td align="right">Selecione a Unidade:</td>
                                <td>
                                    <select name="unidade" id="unidade" class="campotexto">
                                        <option>« Selecione o Projeto »</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td align="right">Tipo de Contratação:</td>
                                <td>
                                    <select name="tipo" id="tipo" class="campotexto">
                                        <option value="1">Autônomo</option>
                                        <option value="2">CLT</option>
                                        <option value="3">Colaborador</option>
                                        <option value="4">Autônomo / PJ</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td align="right">Digite o ano de refer&ecirc;ncia:</td>
                                <td>
                                    <input name="ano" id="ano" type="text" class="campotexto" size="5">
                                </td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td>
                                    <input type="submit" class="botao" value="Gerar Relat&oacute;rio">
                                    <input type="hidden" name="regiao" value="<?= $regiao ?>">
                                </td>
                            </tr>
                        </table>
                    </form>

                    <form action="relatorio3.php" method="post" name="form2" id="form2" style="margin-bottom:50px;">    
                        <table cellspacing="0" cellpadding="4" class="relacao">
                            <tr>
                                <td colspan="2" align="center">RELATÓRIO TOTALIZADOR DO PROJETO</td>
                            </tr>
                            <tr>
                                <td align="right">Digite o ano de refer&ecirc;ncia:</td>
                                <td>
                                    <input name="ano_2" type="text" class="campotexto" id="ano_2" size="5">
                                </td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td>
                                    <input type="submit" class="botao" value="Gerar Relat&oacute;rio">
                                    <input type="hidden" name="tela" id="tela" value='1'>
                                    <input type="hidden" name="regiao" value="<?= $regiao ?>">
                                    <input type="hidden" name="projeto" value="<?= $projeto ?>">
                                </td>
                            </tr>
                        </table>
                    </form>

                    <form action="relatorio4.php" method="post" name="form3" id="form3" style="margin-bottom:50px;">
                        <table cellspacing="0" cellpadding="4" class="relacao">
                            <tr>
                                <td colspan="2" align="center">RELATÓRIO DE CAPACITAÇÃO</td>
                            </tr>
                            <tr>
                                <td align="right">Selecione o Projeto:</td>
                                <td><?php echo montaSelect($projetosOp, $projetoSel, array('name' => "projeto", 'id' => 'projeto')); ?></td>
                            </tr>
                            <tr>
                                <td align="right">Selecione o curso:</td>
                                <td>
                                    <select name="curso" id="curso">
                                        <?php
                                        $result_curso = mysql_query("SELECT * FROM curso WHERE campo3 = '$projeto' ORDER BY nome ASC");
                                        while ($row_curso = mysql_fetch_array($result_curso)) {
                                            ?>
                                            <option value="<?= $row_curso[0] ?>"><?= $row_curso['nome'] ?></option>
    <?php } ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td align="right">Início:</td>
                                <td><input name="ini_dia" type="text" class="campotexto" id="ini_dia" size="2" maxlength="2"> /
                                    <input name="ini_mes" type="text" class="campotexto" id="ini_mes" size="2" maxlength="2"> /
                                    <input name="ini_ano" type="text" class="campotexto" id="ini_ano" size="4" maxlength="4"></td>
                            </tr>
                            <tr>
                                <td align="right">Fim:</td>
                                <td><input name="fim_dia" type="text" class="campotexto" id="fim_dia" size="2" maxlength="2"> /
                                    <input name="fim_mes" type="text" class="campotexto" id="fim_mes" size="2" maxlength="2"> /
                                    <input name="fim_ano" type="text" class="campotexto" id="fim_ano" size="4" maxlength="4"></td>
                            </tr>
                            <tr>
                                <td align="right">Carga Horária</td>
                                <td><input style="text-align:right;" name="carga_horaria" type="text" class="campotexto" id="carga_horaria" size="4"> hs</td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td><select name="modalidade" id="modalidade">
                                        <option>Presencial</option>
                                        <option>A distância</option>
                                    </select></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td><input type="submit" class="botao" value="Gerar Relat&oacute;rio">
                                    <input type="hidden" name="regiao" value="<?= $regiao ?>"></td>
                            </tr>
                        </table>
                    </form>
                </div>
                <div id="rodape"></div>
            </div>
        </body>
    </html>
<?php } ?>