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
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript" ></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        <script type="text/javascript">

            $(document).ready(function() {
                // instancia o validation engine no formulário
                $("#form1").validationEngine();
            });


            var checkAnos = function(field) {
                var anoIni = parseInt($("#ano_ini").val());
                var anoFim = parseInt($("#ano_fim").val());

                if (anoFim < anoIni && anoIni != '' && anoFim != '') {
                    return 'O ano de fim não pode ser menor que o ano de início do período!';
                } else {
                    return true;
                }
            };
            var checkMeses = function(field) {
                var anoIni = parseInt($("#ano_ini").val());
                var anoFim = parseInt($("#ano_fim").val());
                var mesIni = parseInt($("#mes_ini").val());
                var mesFim = parseInt($("#mes_fim").val());

                if (anoFim === anoIni && mesFim < mesIni && mesIni != '' && mesFim != '') {
                    return 'O mês de fim deve ser maior que o mês de início do período!';
                }
                return true;
            };
        </script>

        <style type="text/css">
            .movimentos{
                border-collapse: collapse;
                width: 100%;
            }
            .th-title, .th-subtitle{
                font-size: 1.2em;
                font-weight: bolder;
                display: block;
            }
            .th-subtitle{font-weight: normal;}

            .voltar, .voltar:hover, .voltar:active, voltar:visited{
                background-image: linear-gradient(to bottom, #eee, #ddd); 
                color: black;
                text-decoration: none;
                display: inline-block;
                padding: 2px 5px;
                border: 1px solid #999;
                border-radius: 2px;
                -webkit-border-radius: 2px;
                -moz-border-radius: 2px;
            }
            .voltar:hover{
                border-color: #666;
            }

        </style>
    </head>
    <body class="novaintra">
        <div id="content">
            <form action="?tela=3" method="post" name="form1" id="form1" enctype="multipart/form-data" >
                <div id="head">
                    <img src="../../imagens/logomaster<?php echo $usuario['id_master'] ?>.gif" class="fleft" style="margin-right: 25px;">
                    <div class="fleft">
                        <h2>Relatório de Movimentos</h2>
                        <p>Lista de Participantes</p>
                    </div>
                </div>

                <fieldset>
                    <legend>Dados do Funcionário</legend>
                    <input type="hidden" name="id_clt" id="id_clt" value="<?php echo $id_clt; ?>">
                    <input type="hidden" name="id_projeto" id="id_projeto" value="<?php echo $id_projeto; ?>">
                    <input type="hidden" name="id_regiao" id="id_regiao" value="<?php echo $id_regiao; ?>">
                    <p><label class="first">Região: </label> <?php echo $regiaoArray[1]['regiao']; ?></p>
                    <p><label class="first">Projeto: </label> <?php echo $projetoArray[1]['nome']; ?></p>
                    <p><label class="first">Funcionário: </label> <?php echo $clt[1]['nome']; ?></p>
                    <p><label class="first">Cargo: </label> <?php echo $clt[1]['curso']; ?></p>
                </fieldset>
                <fieldset>
                    <legend>Filtro</legend>
                    <p>
                        <label class="first">Início do Período:</label>
                        <?php echo montaSelect(mesesArray(), (isset($mesIni)) ? $mesIni : date('m') - 1, 'name="mes_ini" id="mes_ini" class="validate[required,funcCall[checkMeses]]"'); ?> /
                        <input type="number" value="<?php echo (isset($ano)) ? $ano : date('Y'); ?>" name="ano" id="ano" style="width: 4em;" class="validate[required]">
                    </p>

                    <p class="controls">
                        <input type="submit" class="button" value="Filtrar" name="filtrar" /> 
                        <a href="?tela=1&AMP;regiao=<?php echo $id_regiao; ?>&AMP;projeto=<?php echo $id_projeto; ?>" class="button voltar">Voltar</a>
                    </p>

                </fieldset>

                <br>

                <?php
                if (isset($movimentos)) {
                    if (count($movimentos) > 0) {
                        for ($ano = $ano; $ano <= $anoFim; $ano++) {
                            for ($mes = 1; $mes <= 13; $mes++) {
                                $count = 0;
                                if (!empty($movimentos[$ano][sprintf("%02d", $mes)])) {
                                    ?>

                                    <table class="movimentos grid">
                                        <thead>
                                            <tr>
                                                <th style="text-align: left;">
                                                    <span class="th-title">Mês dos Movimentos:</span>
                                                    <span class="th-subtitle"><?php echo ($mes != 13) ? mesCurto($mes) . '/' . $ano : '13º Salário' . '/' . $ano; ?></span>
                                                </th>
                                            </tr>
                                        </thead>
                                    </table>
                                    <table class="movimentos grid">
                                        <thead>
                                            <tr>
                                                <th style="width:20%;">Cod</th>
                                                <th style="width:20%;">Tipo</th>
                                                <th style="width:40%;">Nome</th>
                                                <th style="width:20%;">Valor</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $total = 0;
                                            foreach ($movimentos[$ano][sprintf("%02d", $mes)] as $key => $rowMov) {
                                                $total = ($rowMov['tipo'] == 'CREDITO') ? $total + $rowMov['valor'] : $total - $rowMov['valor'];
                                                ?>

                                                <tr class="<?php
                                                echo ($count++ % 2 == 0) ? "odd" : "even";
                                                echo ' ';
                                                echo ($rowMov['tipo'] == 'CREDITO') ? 'back-green' : 'back-red';
                                                ?>">
                                                    <td style="text-align: center;"><?php echo $key ?></td>
                                                    <td style="text-align: center;"><?php echo $rowMov['tipo'] ?></td>
                                                    <td><?php echo $rowMov['nome'] ?></td>
                                                    <td style="text-align: center;">R$ <?php echo number_format($rowMov['valor'], 2, ',', '.') ?></td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3" style="text-align:right; font-size: 1.1em;"><strong>Total:</strong></td>
                                                <td class="<?php echo ($total >= 0) ? 'back-green' : 'back-red'; ?>" style="text-align: center;">R$ 
                                                    <?php
                                                    echo number_format($total, 2, ',', '.');
                                                    unset($total);
                                                    ?>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <br>
                                    <?php
                                }
                            }
                        }
                    } else {
                        ?>
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
