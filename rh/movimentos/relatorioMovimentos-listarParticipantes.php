<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>:: Intranet :: Relatório de Movimentos</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../favicon.ico" rel="shortcut icon" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript" ></script>
        <script src="../../js/global.js" type="text/javascript"></script>
    </head>
    <body class="novaintra">
        <div id="content">
            <form action="" method="post" name="form1" id="form1" enctype="multipart/form-data" >
                <div id="head">
                    <img src="../../imagens/logomaster<?php echo $usuario['id_master'] ?>.gif" class="fleft" style="margin-right: 25px;">
                    <div class="fleft">
                        <h2>Relatório de Movimentos</h2>
                        <p>Lista de Participantes</p>
                    </div>
                </div>

                <fieldset>
                    <legend>Filtro</legend>
                    <input type="hidden" name="id_projeto" id="id_projeto" value="<?php echo $projetoR ?>" />
                    <input type="hidden" name="id_projeto" id="id_projeto" value="<?php echo $regiaoR ?>" />
                    <input type="hidden" name="id_clt" id="id_clt" value="<?php echo $id ?>">
                    <p><label class="first">Região: </label> <?php echo $regiaoArray[1]['regiao']; ?></p>
                    <p><label class="first">Projeto:</label> <?php echo montaSelect(GlobalClass::carregaProjetosByRegiao($regiaoR), $projetoR, "id='projeto' name='projeto' class='required[custom[select]]'") ?></p>

                    <p class="controls"> <input type="submit" class="button" value="Filtrar" name="filtrar" /> </p>
                </fieldset>
                <?php
                if (isset($clt)) {
                    if (count($clt) > 0) {
                        ?>
                        <br/>
                        <p style="text-align: left; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Relatório')" value="Exportar para Excel" class="exportarExcel"></p>
                        <table id="tbRelatorio" cellpadding="0" cellspacing="0" border="0" class="grid" width="100%">
                            <thead>
                                <tr>
                                    <th>COD</th>
                                    <th>MATRÍCULA</th>
                                    <th>NOME</th>
                                    <th>CARGO</th>
                                    <th>UNIDADE</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($clt as $row_clt) { ?>
                                    <tr class="<?php echo ($count++ % 2 == 0) ? "odd" : "even" ?>">
                                        <td style="text-align:center;"><?php echo $row_clt['id_clt'] ?></td>
                                        <td style="text-align:center;"><?php echo $row_clt['matricula'] ?></td>
                                        <td><a href="?tela=2&AMP;id=<?php echo encrypt($row_clt['id_clt']); ?>&AMP;projeto=<?php echo encrypt($projetoR); ?>&AMP;regiao=<?php echo encrypt($regiaoR); ?>"><?= $row_clt['nome'] ?></a> 
                                            <?php
                                            if ($row_clt['status'] == '40') {
                                                echo '<span style="color:#069; font-weight:bold;">(Em Férias)</span>';
                                            } elseif ($row_clt['status'] == '200') {
                                                echo '<span style="color:red; font-weight:bold;">(Aguardando Demissão)</span>';
                                            }
                                            ?></td>
                                        <td><?= $row_clt['curso'] ?></td>
                                        <td style="text-align:center;"><?php echo $row_clt['locacao'] ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php } else { ?>
                        <br/>
                        <div id='message-box' class='message-yellow'>
                            <p>Nenhum registro encontrado</p>
                        </div>
                    <?php
                    }
                }
                ?>

            </form>
        </div><!-- /.content -->
    </body>
</html>
