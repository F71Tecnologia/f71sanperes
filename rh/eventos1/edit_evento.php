<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>:: Intranet :: Eventos: <?= $row_evento['nome_status'] ?> :: <?= $row_clt['nome'] ?></title>
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css"/>
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.ui.datepicker-pt-BR.js" type="text/javascript"></script>
        <script src="../../js/ramon.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        <script src="edit_evento.js" type="text/javascript"></script>
        <script>
            $(function () {
                $('.data').datepicker();
            });
        </script>
        <style>
            .ui-datepicker{
                font-size: 12px;
            }
            .data{width:7em;}
        </style>
    </head>
    <body class="novaintra">
        <div id="content">       
            <div id="head">
                <img src="../../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;" alt="">
                <div class="fleft">
                    <h2>Eventos</h2>
                    <h3><?= $row_clt['regiao'] ?> <?= " - " . $row_clt['projeto'] ?></h3>
                    <h4><?= '(' . $row_clt['id_clt'] . ') ' . $row_clt['nome'] ?></h4>
                </div>
            </div>
            <br class="clear">
            <br/>
            <a href="index.php?tela=acao_evento&AMP;clt=<?= $row_clt['id_clt'] ?>&AMP;regiao=<?= $regiao ?>" class="botao"> &lt;&lt; Voltar</a>
            <br>
            <?php if (isset($resp)) {
                ?>
                <div class="<?= $resp['class'] ?>" style="margin:10px 0; padding: 10px;"><?= $resp['msg'] ?></div>
            <?php }
            ?>


            <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="post" id="form1" style="width: 100%;">
                <fieldset>
                    <legend>Edição de Evento</legend>
                    <p><label for="" class="first">Participante:</label> <?= $row_clt['nome'] ?><input type="hidden" name="id_clt" id="id_clt" value="<?= $row_clt['id_clt'] ?>"></p>
                    <p><label for="" class="first">Ocorrência:</label> <?= $row_evento['nome_status'] ?><input type="hidden" name="evento" id="evento" value="<?= $row_evento['cod_status'] ?>"></p>
                    <p><label for="" class="first">Data:</label>
                        <?php
//                        if ($row_evento['cod_status'] != 10) { // status 10 nao pode modificar esse campo 
//                            if (!empty($row_evento['data_retorno_final_br']) && $row_evento['data_retorno_final_br'] == "00/00/0000") { // se não estiver vazio não pode modificar
//                                
                        ?>
                                <!--<input type="text" name="data" id="data" class="data" value="//<?= $row_evento['data_br'] ?>" class="validate[required]">-->
                        <?php
//                            } else {
//                                echo $row_evento['data_br'];
//                                echo "<input type=\"hidden\" min=\"0\" size=\"3\" maxlength=\"3\" name=\"data\" id=\"data\" value=\"{$row_evento['data_br']}\"/>";
//                            }
//                        } else {
                        echo $row_evento['data_br'];
                        echo "<input type=\"hidden\" min=\"0\" size=\"3\" maxlength=\"3\" name=\"data\" id=\"data\" value=\"{$row_evento['data_br']}\"/>";
//                        }
                        ?>
                    </p>
                    <p><label for="" class="first">Qtd. Dias:</label> 
                        <?php
                        if ($row_evento['cod_status'] != 10) { // status 10 nao pode modificar esse campo 
                            if (!empty($row_evento['data_retorno_final_br']) && $row_evento['data_retorno_final_br'] == "00/00/0000") { // se não estiver vazio não pode modificar
                                ?>
                                <input type="number" min="0" name="dias" id="dias" class="dias" style="width:5em;" value="<?= $row_evento['dias'] ?>" class="validate[required]">
                                <?php
                            } else {
                                echo $row_evento['dias'];
                                echo "<input type=\"hidden\" min=\"0\" size=\"3\" maxlength=\"3\" name=\"dias\" id=\"dias\" value=\"{$row_evento['dias']}\"/>";
                            }
                        } else {
                            echo $row_evento['dias'];
                            echo "<input type=\"hidden\" min=\"0\" size=\"3\" maxlength=\"3\" name=\"dias\" id=\"dias\" value=\"{$row_evento['dias']}\"/>";
                        }
                        ?>
                    </p>
                        <?php // if ($tem_pericia) { ?>
                        <p id="row_retorno"><label for="data_retorno" class="first">Data de Retorno:</label> 
                            <?php if ($row_evento['cod_status'] != 10) { // status 10 nao pode modificar esse campo   ?>
                                <input type="text" name="data_retorno" class="data" id="data_retorno" value="<?= $row_evento['data_retorno_br'] ?>" class="validate[required]">
                                <?php
                            } else {
                                echo $row_evento['data_retorno_br'];
                                echo "<input type=\"hidden\" name=\"data_retorno\" id=\"data_retorno\" class=\"data\" value=\"{$row_evento['data_retorno_br']}\"/>";
                            }
                            ?>
                        </p>
                        <?php // } ?>
<!--                    <p id="row_retorno_final"><label for="data_final" class="first">Data Final de Retorno:</label> 
                        <?php
                        if ($row_evento['cod_status'] != 10) { // status 10 nao pode modificar esse campo
                            if (!empty($row_evento['data_retorno_final_br']) && $row_evento['data_retorno_final_br'] == "00/00/0000") { // se não estiver vazio não pode modificar
                                ?>
                                <input type="text" name="data_final" class="data" id="data_final" value="" class="validate[required]">
                                <?php
                            } else {
                                echo $row_evento['data_retorno_final_br'];
                                echo "<input type=\"hidden\" name=\"data_final\" id=\"data_final\" class=\"data\" value=\"{$row_evento['data_retorno_final_br']}\"/>";
                            }
                        } else {
                            echo $row_evento['data_retorno_br'];
                            echo "<input type=\"hidden\" name=\"data_final\" id=\"data_final\" class=\"data\" value=\"{$row_evento['data_retorno_final_br']}\"/>";
                        }
                        ?>
                    </p>-->
                    <p><label for="" class="first">Observação:</label> <textarea name="observacao" id="observacao" rows="3" style="width: 300px" class="validate[required]"><?= $row_evento['obs'] ?></textarea></p>
                    <input type="hidden" name="id_evento" id="id_evento" value="<?= $row_evento['id_evento'] ?>">
                    <p class="controls"><input type="submit" name="salvar" id="salvar" value="Salvar"></p>
                </fieldset>
            </form>
        </div>
    </body>
</html>
