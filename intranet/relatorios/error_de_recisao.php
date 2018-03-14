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
include "../classes/FolhaClass.php";

$id_regiao = $_REQUEST['regiao'];
$usuario = carregaUsuario();
$optRegiao = getRegioes();
$ACOES = new Acoes();
$folha = new Folha();

$sql = "SELECT *
        FROM rh_recisao
        WHERE DAY(data_proc) >= 21 AND DAY(data_proc) < 28 AND MONTH(data_proc) = 08 AND 
        YEAR(data_proc) = 2014 AND id_regiao = '45' AND status = 1 AND id_clt != 7821 AND id_clt != 8334";

$query = mysql_query($sql);


echo "<!-- QUERY:: {$sql} -->";

?>
<html>
    <head>
        <title>:: Intranet :: Previsão de Gasto</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/global.js" type="text/javascript"></script>
        <script src="../js/ramon.js" type="text/javascript"></script>


        <script>
            $(function() {

            });
        </script>
        <style>
            #total_anos{
                display: block;
                margin-top: 509px;
                margin-left: 10px;
                text-align: right;
                margin-right: 10px;
            }
            #total_anos p{
                font-family: arial;
                color: #333;
                font-size: 15px;
            }
            #total_anos span{
                font-weight: bold;
            }
            #fgts_folha{
                display: none;
            }
            .lista_fgts{
                border: 1px solid #ccc;
                padding: 5px;
                width: 207px;
                height: 500px;
                float: left;
                margin: 0px 10px;
                box-sizing: border-box;
            }
            .lista_fgts h3{
                border-bottom: 3px solid #333;
            }
            .lista_fgts h2{
                font-size: 16px;
                text-align: right;
                margin: 0px;
                background: #F5F3F3;
                width: 100%;
                padding: 5px;
                box-sizing: border-box;
            }
            .lista_fgts p{
                border-bottom: 1px dotted #ccc;
            }
            .header{
                font-weight: bold;
                background: #F3F3F3 !important;
                font-size: 11px !important;
                color: #333;
            }
            .footer{
                font-weight: bold;
                background: #F3F3F3;
            }

            #totalizador{
                border: 1px solid #ccc;
                padding: 5px;
                margin: 10px 10px;
                width: 363px;
                height: 358px;
                background: #f3f3f3;
                float: left;
            }
            #totalizador p{
                border-bottom: 1px dotted #ccc;
                padding-bottom: 2px;
            }
            #totalizador span{
                font-weight: bold;
                float: right;
            }
            .semborda{
                border: 0px !important;
            }
            .titulo{
                font-weight: bold;
                color: #000;
                text-align: center;
                font-size: 14px;
                margin: 5px 0px 20px 0px;
                border: 2px solid #B1A8A8 !important;
                padding: 1px 0px;
                background: #DFDFDF;
                height: 35px;
            }
            .compactar{
                float: right;
                font-family: verdana;
                font-size: 10px;
                font-weight: bold;
                color: #CA1E17;
                text-transform: uppercase;
                cursor: pointer;
            }

            .compactar:before{
                content: " -";
                background: #1D1A1A;
                border-radius: 65%;
                padding: 1px 5px;
                font-weight: bold;
                color: #fff;
                margin-right: 5px;
            }

            .area{
                border: 2px solid;
                height: 16px;
                width: 99%;
                margin-left: 5px;
                border-bottom: 0px;
            }
            .box{
                border: 0px solid #ccc;
                padding: 10px;
                box-sizing: border-box;
                margin: 5px;
                width: 1285px;
            }
            .col-esq, .col-dir{
                float: left;
                margin: 0px 5px;
                width: 590px;
            }

            .col-esq label, .col-dir label{
                width: 200px !important;
            }

            .inputPequeno{
                width: 324px;
                height: 27px;
                padding: 10px;
            }

            .selectPequeno{
                width: 324px;
                height: 28px;
                padding: 0px;
            }
        </style>

    </head>
    <body class="novaintra" >  
        <form  name="form" action="" method="post" id="form">
                <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" class="grid" width="1000px" style="page-break-after:auto; border: 0px; margin: 0 auto"> 
                    <thead>
                        <tr style="font-size:10px !important;">
                            <th colspan="10">RELATÓRIO DE ERRO DE CALCULO DE INSS</th>
                        </tr>
                    </thead>
                    <thead>
                        <tr style="font-size:10px !important;">
                            <th rowspan="2">ID</th>
                            <th rowspan="2">NOME</th>
                            <th rowspan="2">SALÁRIO BASE</th>
                            <th rowspan="2">INSALUBRIDADE</th>
                            <th rowspan="2">RENDIMENTOS FIXOS</th>
                            <th rowspan="2">VALOR DO AVISO</th>
                            <th rowspan="2">LEI 12/506</th>
                            <th rowspan="2">INSS PAGO</th>
                            <th rowspan="2">INSS CORRETO</th>
                            <th rowspan="2">Direfença</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    while ($row_rel = mysql_fetch_assoc($query)) {
                        /////////////////////
                        // MOVIMENTOS FIXOS /////
                        ///////////////////

                        $qr_folha = mysql_query("select  A.ids_movimentos_estatisticas, B.id_clt,A.mes
                        FROM rh_folha as A
                        INNER JOIN rh_folha_proc as B
                        ON A. id_folha = B.id_folha
                        WHERE B.id_clt   = '{$row_rel['id_clt']}'  AND B.status = 3 AND A.terceiro = 2 
                        AND A.data_inicio >= DATE_SUB(NOW(), INTERVAL 13 MONTH) ORDER BY A.ano,A.mes");

                        $movimentos = 0;
                        $total_rendi = 0;
                        while ($row_folha = mysql_fetch_assoc($qr_folha)) {
                            if (!empty($row_folha[ids_movimentos_estatisticas])) {

                                $qr_movimentos = mysql_query("SELECT *
                               FROM rh_movimentos_clt
                               WHERE id_movimento IN($row_folha[ids_movimentos_estatisticas]) AND incidencia = '5020,5021,5023'  AND tipo_movimento = 'CREDITO' AND id_clt = '{$row_rel['id_clt']}' AND id_mov NOT IN(56,200) ");
                                while ($row_mov = mysql_fetch_assoc($qr_movimentos)) {
                                    $movimentos += $row_mov['valor_movimento'];
                                }
                            }
                        }

                        if ($movimentos > 0) {
                            $total_rendi = $movimentos / 12;
                        } else {
                            $total_rendi = 0;
                        }

                        /////////////////////
                        // FIM MOVIMENTOS FIXOS /////                 
                        
                        $valor_aviso = $row_rel['sal_base'] + $total_rendi + $row_rel['insalubridade'];
                        
                    ?>    
                        <tr class="<?php echo $class ?>" style="font-size:11px;">
                            <td align="left"><?php echo $row_rel['id_clt']; ?></td>
                            <td align="left"><?php echo $row_rel['nome']; ?></td>
                            <td align="left"><?php echo "R$ " . number_format($row_rel['sal_base'], 2 ,",","."); ?></td>
                            <td align="left"><?php echo "R$ " . number_format($row_rel['insalubridade'], 2 ,",","."); ?></td>
                            <td align="left"><?php echo "R$ " . number_format($total_rendi, 2 ,",","."); ?></td>
                            <td align="left"><?php echo number_format($row_rel['sal_base'], 2 ,",",".") . " + " .  number_format($row_rel['insalubridade'], 2 ,",",".") . " + " . number_format($total_rendi, 2 ,",",".") . " = R$ " . number_format($row_rel['sal_base'] + $row_rel['insalubridade'] + $total_rendi, 2 ,",","."); ?></td>
                            <td align="left"><?php echo "R$ " . number_format($row_rel['lei_12_506'], 2 ,",","."); ?></td>
                            <td align="left"><?php echo number_format($row_rel['lei_12_506'],2,",",".") ." + ". number_format($valor_aviso,2,",",".") ." + ". number_format($row_rel['dt_salario'],2,",",".") ." + ". number_format($row_rel['terceiro_ss'],2,",",".") . " = R$ " . number_format($row_rel['lei_12_506'] + $valor_aviso + $row_rel['dt_salario'] + $row_rel['terceiro_ss'], 2 ,",","."); ?></td>
                            <td align="left"><?php echo "R$ " . number_format($row_rel['dt_salario'] + $row_rel['terceiro_ss'], 2 ,",","."); ?></td>
                            <td align="left"><?php echo "R$ " . number_format(($row_rel['lei_12_506'] + $valor_aviso + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) - ($row_rel['dt_salario'] + $row_rel['terceiro_ss']),2,",","."); ?></td>
                            <?php $total +=  ($row_rel['lei_12_506'] + $valor_aviso + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) - ($row_rel['dt_salario'] + $row_rel['terceiro_ss']); ?>
                        </tr>
                        
                    <?php $total_rendi = 0; ?>
                    <?php } ?>
                        <tr class="<?php echo $class ?>" style="font-size:11px;">
                            <td align="right" colspan="9">Total</td>
                            <td align="left"><?php echo "R$ " . number_format($total,2,",","."); ?></td>
                        </tr>
                </tbody>
            </table>                
        </form>
        
    </body>
</html>