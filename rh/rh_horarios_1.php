<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a> ";
}

include "../conn.php";
include "../wfunction.php";

$usuario = carregaUsuario();
$id_regiao = $_REQUEST['regiao'];
$total = 0;

if(isset($_REQUEST['method']) && $_REQUEST['method'] == "edhorario"){
    $qr_hora = montaQuery("rh_horarios", "*", array("id_horario"=>$_REQUEST['id_horario']));
    $horario = current($qr_hora);
    
    $qr_funcoes = montaQuery("curso", "id_curso,nome", array("campo3"=>$_REQUEST['id_projeto']),"nome");
    $optFuncioes = array();
    foreach ($qr_funcoes as $valor) {
        $optFuncioes[$valor['id_curso']] = $valor['id_curso']." - ".$valor['nome'];
    }
    $funcoeSel = $horario['funcao'];
    
    $html =
    "<div><fieldset>
        <p><label class=\"first\">Código:</label> {$horario['id_horario']}</p>
        <p><label class=\"first\">Função:</label> " . montaSelect($optFuncioes, $funcoeSel, array('name' => 'funcao', 'id' => 'funcao')) . " </p>
        <p><label class=\"first\">Nome Horário:</label> <input type=\"text\" name=\"horario\" id=\"horario\" value=\"{$horario['nome']}\" /> </p>
        <p><label class=\"first\">Chegada:</label> <input type=\"text\" name=\"entrada_1\" id=\"entrada_1\" value=\"{$horario['entrada_1']}\" class=\"maskHora\" size=\"8\" /> </p>
        <p><label class=\"first\">Saída Almoço:</label> <input type=\"text\" name=\"saida_1\" id=\"saida_1\" value=\"{$horario['saida_1']}\" class=\"maskHora\" size=\"8\" /> </p>
        <p><label class=\"first\">Retorno Almoço:</label> <input type=\"text\" name=\"entrada_2\" id=\"entrada_2\" value=\"{$horario['entrada_2']}\" class=\"maskHora\" size=\"8\" /> </p>
        <p><label class=\"first\">Fim Expediente:</label> <input type=\"text\" name=\"saida_2\" id=\"saida_2\" value=\"{$horario['saida_2']}\" class=\"maskHora\" size=\"8\" /> </p>
        <p class=\"controls\"><input type=\"button\" name=\"cancelar\" value=\"Cancelar\" /><input type=\"button\" name=\"salvar\" id=\"salvar\" value=\"Salvar\" /></p>
    </fieldset></div>";
    
    
    echo utf8_encode($html);
    exit;
}

/*$qr_master = montaQuery("master", "*", "id_master = '{$_SESSION['id_master']}'");
$row_master = current($qr_master);*/

////SELECT projeto
$qr_projeto = montaQuery('projeto', "id_projeto, nome", "id_regiao = '$id_regiao'", "nome");
$optProjeto = array();
foreach ($qr_projeto as $valor) {
    $optProjeto[$valor['id_projeto']] = $valor['nome'];
}
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : '';

if (isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])) {

    $projeto = $_REQUEST['projeto'];

    $result = mysql_query("SELECT 
                        A.id_curso,A.nome as funcao,A.salario,
                        B.id_horario,B.nome,B.entrada_1,B.saida_1,B.entrada_2,B.saida_2,B.dias_semana,B.horas_mes,B.horas_semanais,B.horas_trabalho,B.horas_folga,B.dias_mes,B.folga
                        FROM curso AS A
                        LEFT JOIN rh_horarios AS B ON (A.id_curso=B.funcao)
                        WHERE campo3 = {$projeto} AND B.id_horario IS NOT NULL;");

    $total = mysql_num_rows($result);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>RH - Horários</title>
        <link rel="shortcut icon" href="../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt.js" type="text/javascript"></script>

        <script src="../js/global.js" type="text/javascript"></script>

        <style>
            .grid thead tr th {font-size: 12px!important;}
            .bt-edit{cursor: pointer;}
        </style>

        <script>
            $(function(){
                $(".bt-edit").click(function(){
                    var bt = $(this);
                    thickBoxIframe("Edição de Horário", "rh_horarios_1.php", {id_horario: bt.attr("data-key"), id_projeto: $("#projeto").val(), method: "edhorario"}, 600, 400);
                });
            });
        </script>
    </head>

    <body class="novaintra">
        <form action="" method="post" name="form1">
            <div id="content" style="width: 90%;">
                <div id="head">
                    <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>RH - Horários</h2>
                        <p>Gerenciamento dos horários vinculados as funções</p>
                    </div>
                    <div class="fright"> <?php include('../reportar_erro.php'); ?></div> 
                </div>
                <br class="clear"/>
                <br/>
                <fieldset>
                    <legend>Filtro</legend>
                    <div class="fleft">
                        <p><label class="first">Projeto:</label> <?php echo montaSelect($optProjeto, $projetoSel, array('name' => 'projeto', 'id' => 'projeto')); ?></p>
                    </div>
                    <div class="fright" style="margin-right: 25px;">
                        <!-- <img src="../imagens/status.jpg" /> -->
                    </div>
                    <br class="clear"/>
                    <p class="controls" style="margin-top: 10px;"><input type="submit" name="filtrar" value="Filtrar" id="filtrar"/></p>
                </fieldset>                
                <br/>
                <br/>
                
                <?php if ($total > 0) { ?>
                    <table cellpadding="0" cellspacing="0" border="0" class="grid">
                        <thead>
                            <tr>
                                <th colspan="3">Função</th>
                                <th class="tbdivisao">&nbsp;</th>
                                <th colspan="6">Horário</th>
                                <th class="tbdivisao">&nbsp;</th>
                                <th colspan="4">Horas</th>
                                <th class="tbdivisao">&nbsp;</th>
                                <th colspan="2">Dias</th>
                                <th rowspan="2">Editar</th>
                            </tr>
                            <tr>
                                <th>Cod</th>
                                <th>Função</th>
                                <th>Salário</th>
                                <th class="tbdivisao">&nbsp;</th>
                                <th>Cod</th>
                                <th>Horário</th>
                                <th>Chegada</th>
                                <th>Saída Almoço</th>
                                <th>Retorno Almoço</th>
                                <th>Fim Expediente</th>
                                <th class="tbdivisao">&nbsp;</th>
                                <th>Mês</th>
                                <th>Semana</th>
                                <th>Trabalho</th>
                                <th>Folga</th>
                                <th class="tbdivisao">&nbsp;</th>
                                <th>Mês</th>
                                <th>Semana</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysql_fetch_assoc($result)) { ?>
                                <tr>
                                    <td><?php echo $row['id_curso'] ?></td>
                                    <td><?php echo $row['funcao'] ?></td>
                                    <td class="txcenter"><?php echo $row['salario'] ?></td>
                                    <td class="tbdivisao">&nbsp;</td>
                                    <td><?php echo $row['id_horario'] ?></td>
                                    <td><?php echo $row['nome'] ?></td>
                                    <td class="txcenter"><?php echo $row['entrada_1'] ?></td>
                                    <td class="txcenter"><?php echo $row['saida_1'] ?></td>
                                    <td class="txcenter"><?php echo $row['entrada_2'] ?></td>
                                    <td class="txcenter"><?php echo $row['saida_2'] ?></td>
                                    <td class="tbdivisao">&nbsp;</td>
                                    <td class="txcenter"><?php echo $row['horas_mes'] ?></td>
                                    <td class="txcenter"><?php echo $row['horas_semanais'] ?></td>
                                    <td class="txcenter"><?php echo $row['horas_trabalho'] ?></td>
                                    <td class="txcenter"><?php echo $row['horas_folga'] ?></td>
                                    <td class="tbdivisao">&nbsp;</td>
                                    <td class="txcenter"><?php echo $row['dias_mes'] ?></td>
                                    <td class="txcenter"><?php echo $row['dias_semana'] ?></td>
                                    <td class="txcenter"><a href="javascript:;"><img width="16" height="16" border="0" alt="Editar" src="../imagens/editar.gif" data-key="<?php echo $row['id_horario'] ?>" class="bt-edit" /></a></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="16" class="txright"><strong>Total de horários:</strong></td>
                                <td colspan="3"><?php echo $total ?></td>
                            </tr>
                        </tfoot>
                    </table>
                <?php } ?>
            </div>
        </form>
    </body>
</html>