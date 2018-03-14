<?php

/*
 * CONTROLLER: eventos/intex.php 
 * TELA:       principal
 */
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>:: Intranet :: Eventos</title>
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css"/>
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        <script>
            $(document).ready(function () {
                $("#form1").validationEngine(); // add validation engine

                $(".page").click(function () { // quando clica na as setas de paginação
                    var pagina = $(this).data("page");                    
                    $("#paginacao").val(pagina);
                    $("#form1").submit();
                });
                $(".inicial").click(function () { // quando clica no botão das iniciais
                    var letra = $(this).html();
                    $("#inicial").val(letra);
                    $("#paginacao").val(1);
                    $("#form1").submit();
                });

                $(".tab-status").click(function () { // abas
                    var status = $(this).data("status");
                    $("#rhstatus").val(status);
                    $("#form1").submit();
                });

                $("#tudo").click(function () { // limpa a pesquisa por nome e recarrega com todos os dados
                    $("#clt_nome").val("");
                    $("#form1").submit();
                });
                $("#filtrar").click(function () { // limpa o input com a inicial para não dar erro na query
                    $("#inicial").val('');
                    $("#form1").submit();
                });
                
                $("#exportarExcel").click(function () {                    
                    //$("#tbRelatoriob img:last-child").remove(); 
                    //var html = $("#tbRelatoriob").html(); 
                    //$("#data_xls").val(html);   
                    $("#form2").submit();                                           
                });
            });
        </script>
        <style>
            ul.painel-tab{
                width: 100%;
                list-style-type: none;
                display: table;
                padding: 0;
                border-bottom: 2px solid #eee;
            }
            ul.painel-tab li{
                display: inline-block;
                float: left;
            }
            ul.painel-tab li a, ul.painel-tab li a:hover{
                display: inline-block;
                text-align: center;
                padding: 10px 20px;
                text-decoration: none;
                color: #555;    

                border-top-left-radius: 5px;
                -webkit-border-top-left-radius: 5px;
                -moz-border-top-left-radius: 5px;

                border-top-right-radius: 5px;
                -webkit-border-top-right-radius: 5px;
                -moz-border-top-right-radius: 5px;
            }
            ul.painel-tab li a:hover{
                background-color: #efefef;
                margin-bottom: -2px;
                border-bottom: 2px solid #efefef;
            }
            ul.painel-tab li a.ativo, ul.painel-tab li a.ativo:hover{
                color: #66A9E4;
                border: 2px solid #eee;
                border-bottom-color: #fff;
                margin-bottom: -2px;
                background-color: #fff;
            }

            .painel-filtro{
                border: 2px solid #eee;
                padding: 5px;
                margin-bottom: 15px;
            }
            .painel-filtro, a.btn-paginacao, .back-red{
                border-radius: 5px;
                -webkit-border-radius: 5px;
                -moz-border-radius: 5px;
            }

            .back-red{ padding: 10px; }

            a.btn-paginacao{
                display: inline-block;
                padding: 5px;
                margin: 3px;
                text-decoration: none;
                font-weight: bold;
                color: #66A9E4;
                border: 1px solid #ccc;
                width: 1.2em;
                text-align: center;
            }
            a.btn-paginacao:hover{
                background-color: #efefef;
            }
            .status-pag{
                font-weight: bold;
            }

            .back-blue{
                background: #99ccff;
                border: 1px solid #0066cc;
                color: #0066cc;
            }
        </style>
    </head>
    <body class="novaintra">        
        <div id="content">
            <form  name="form" action="#" id="form1" method="post">
                <div id="head">
                    <img src="../../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;" alt="">
                    <div class="fleft">
                        <h2>Eventos</h2>
                        <h3><?= $regiao['regiao'] ?> <?= (isset($projeto)) ? " - " . $projeto['nome'] : ""; ?></h3>
                    </div>
                </div>
                <br class="clear">
                <br/>

                <div class="painel-filtro">
                    <p style="margin:10px;">
                        <strong>Projeto:</strong>
                        <?= montaSelect($projetosOp, $projetoSel, array('name' => "projeto", 'id' => 'projeto')); ?>
                        |
                        <strong>Nome do Funcionário:</strong>
                        <input type="text" name="clt_nome" id="clt_nome" value="<?= (isset($_REQUEST['clt_nome']) || !empty($_REQUEST['clt_nome'])) ? $_REQUEST['clt_nome'] : "" ?>">


                        <input type="submit" value="Filtrar" id="filtrar">
                    </p>
                </div>
                <?php if (isset($participantes) && !empty($participantes)) { ?>

                    <input type="hidden" name="rhstatus" id="rhstatus" value="<?= $rhstatus ?>">
                    <input type="hidden" name="paginacao" id="paginacao" value="<?= $pagina ?>">
                    <input type="hidden" name="inicial" id="inicial" value="<?= $_REQUEST['inicial'] ?>">

                    <?php if (!$consultaByNome) { ?>
                        <ul class="painel-tab">
                            <?php
                            foreach ($btn_status as $value) {
                                $ativo = ($rhstatus == $value['rhstatus']) ? "ativo" : "";
                                echo "<li><a href=\"{$value['href']}\" title=\"{$value['title']}\" data-status=\"{$value['rhstatus']}\" class=\"tab-status {$ativo}\">{$value['html']}</a></li>";
                            }
                            ?>
                        </ul>
                        <div>
                            <p style="text-align: left; margin-top: 20px"><button type="button" value="Exportar" class="btn btn-success" id="exportarExcel"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button></p>
                        </div>

                        <div class="painel-paginacao">
                            <a href="#" class="btn-paginacao page" title="Voltar para pág. <?= $pagina - 1 ?>" data-page="<?= ($pagina > 1) ? $pagina - 1 : null ?>">&lt;&lt;</a>
                            <span class="status-pag"><?= $posicao + 1 ?> à <?= ($posicao + 100 <= $total) ? $posicao + 100 : $total ?> de <?= $total ?></span>
                            <a href="#" class="btn-paginacao page" title="Avançar para pág. <?= $pagina + 1 ?>" data-page="<?= ($posicao + 100 <= $total) ? $pagina + 1 : null ?>">>></a>
                            |
                            <?php
                            $letras = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
                            for ($i = 0; $i < count($letras); $i++) {
                                ?>
                                <a href="#" class="btn-paginacao inicial" title="Listar funcionários com nomes iniciados em <?= $letras[$i] ?>"><?= $letras[$i] ?></a>
                            <?php } ?>
                        </div>
                    <?php } else { ?>
                        <p><input type="button" id="tudo" value="Limpar pesquisa"></p><br>
                    <?php } ?>

                    <?php foreach ($participantes as $id_projeto => $participante) { ?>
                        <div id="tbRelatoriob">
                            <table id="tbRelatorio" style="border-collapse: collapse; width:100%; page-break-after:auto; border:0;" class="grid"> 
                                <thead>
                                    <tr>
                                        <th colspan="6"><?= $id_projeto . " - " . $participante[0]['nome_projeto'] ?></th>
                                    </tr>
                                    <tr>
                                        <th style="width: 10%">COD</th>
                                        <th style="width: 30%">NOME</th>
                                        <th style="width: 25%">CARGO</th>
                                        <th style="width: 10%">STATUS</th>
                                        <th style="width: 15%">DURAÇÃO</th>
                                        <th style="width: 10%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($participante as $value) {  
                                        $class = ($cont++ % 2 == 0) ? "even" : "odd";
                                        ?>
                                        <tr class="<?php echo $class ?>">
                                            <td style="text-align:center;"><?= $value['id_clt'] ?></td>
                                            <td style="text-align:left;"><?= abreviacao($value['nome'], 4, 1) ?>
                                            </td>
                                            <td style="text-align:left;"><?= $value['curso'] ?></td>
                                            <td style="text-align:center;"><?= $value['status'] . " - " . $value['nome_status'] ?></td>
                                            <td style="text-align:center;"><?php
                                                if ($value['status'] != 10) {
    //                                                echo ($value['pericia'] == 1) ? $value['data'] . ' - ' . $value['data_retorno'] : $value['data'] . ' - ' . $value['data_retorno_final'];
                                                    echo  $value['data'] . ' - ' . $value['data_retorno'];
                                                } else {
                                                    echo 'N/A';
                                                }
                                                ?></td>
                                            <td style="text-align:center;">
                                                <div>
                                                    <?php if($_COOKIE['logado'] != 395){ ?>
                                                    <a href="index.php?tela=acao_evento&amp;clt=<?= $value['id_clt'] ?>&amp;regiao=<?= $regiao['id_regiao'] ?>"  class="participante" title="Inserir evento para <?= $value['nome'] ?>" style="text-decoration: none;">
                                                        <img src="../../imagens/icones/icon-filego.gif" title="Inserir evento para <?= $value['nome'] ?>" alt="">
                                                    </a>
                                                    <?php } ?>
                                                    &nbsp;&nbsp;
                                                    <?php if ($value['status'] == 20) { ?>
                                                    <a href="print_licmedica.php?clt=<?= $value['id_clt'] ?>" class="participante" title="Imprimir Requerimento" style="text-decoration: none;" target="_blank">
                                                        <img src="../../imagens/icones/icon-print.gif" title="Imprimir Requerimento" alt="">
                                                    </a>
                                                    <?php } ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
            <!--                        <tfoot>
                                    <tr>
                                        <td colspan="4"><strong>TOTAL:</strong></td>
                                        <td style="text-align:center;"><?php echo $num_rows ?></td>
                                    </tr>
                                </tfoot>-->
                            </table>
                        </div>
                        <br>

                        <?php
                    }
                    if (!$consultaByNome) {
                        ?>
                        <div class="painel-paginacao">
                            <a href="#" class="btn-paginacao page" title="Voltar para pág. <?= $pagina - 1 ?>" data-page="<?= ($pagina > 1) ? $pagina - 1 : null ?>">&lt;&lt;</a>
                            <span class="status-pag"><?= $posicao + 1 ?> à <?= ($posicao + 100 <= $total) ? $posicao + 100 : $total ?> de <?= $total ?></span>
                            <a href="#" class="btn-paginacao page" title="Avançar para pág. <?= $pagina + 1 ?>" data-page="<?= ($posicao + 100 <= $total) ? $pagina + 1 : null ?>">>></a>
                            |
                            <?php
                            $letras = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
                            for ($i = 0; $i < count($letras); $i++) {
                                ?>
                                <a href="#" class="btn-paginacao inicial" title="Listar funcionários com nomes iniciados em <?= $letras[$i] ?>"><?= $letras[$i] ?></a>
                            <?php } ?>
                        </div>
                        <?php
                    }
                } else if (!empty($status_nome)) {
                    ?>
                    <div class="back-blue"><img src="../../imagens/icones/icon-exclamation-disabled.gif" alt="INFORMAÇÃO"> O funcionário está com o status <strong><?= $status_nome ?></strong></div>
                <?php } else { ?>
                    <div class="back-red"><img src="../../imagens/icones/icon-error.gif" title="ATENÇÃO" alt="ATENÇÃO"> Não há resultados para essa pesquisa.</div>
                    <?php } ?>
                    
            </form>   
            <form id="form2" method="post" action="/intranet/rh/eventos1/exportarExcel.php" target='a_blank'> 
                <input type="hidden" id="data_xls" name="data_xls" value="">
            </form>
        </div>
    </body>
</html>