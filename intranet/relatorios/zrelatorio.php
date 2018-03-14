<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}
error_reporting(E_ALL);

include('../conn.php');
//include("../funcoes.php");
include("../wfunction.php");
include('../classes/global.php');

$lista = false;
$usuario = carregaUsuario();

//VERIFICA INFORMAÇÃO DE POST
if (validate($_REQUEST['filtrar'])) {
    $lista = true;
    $projeto = $_REQUEST['projeto'];
    $dataIni = $_REQUEST['dataIni'];
    $dataFim = $_REQUEST['dataFim'];
    $dataIni = (!empty($dataIni)) ? ConverteData($dataIni) : 'null';
    $dataFim = (!empty($dataFim)) ? ConverteData($dataFim) : 'null';

    if ($projeto == "-1") {
        $condProjeto = "AND p.id_regiao  = '{$usuario['id_regiao']}'";
    } else {
        $condProjeto = "AND p.id_projeto  = '{$projeto}'";
    }

    $query = "SELECT f.id_folha, f.projeto, p.id_regiao, f.terceiro, f.tipo_terceiro, p.regiao, p.nome, f.data_inicio
              FROM rh_folha AS f
              INNER JOIN projeto p ON f.projeto = p.id_projeto
              WHERE (f.status = '3' OR f.status = '2') AND p.id_master = {$usuario['id_master']} AND  f.data_inicio BETWEEN '$dataIni' AND '$dataFim' AND p.id_regiao != 36 $condProjeto
              ORDER BY f.regiao, f.projeto, f.data_inicio";

    $result = mysql_query($query);
}
?>
<html>
    <head>
        <title>RH - Relatório Gerencial</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />   
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>   
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt.js"></script>
        <script src="../js/global.js" type="text/javascript"></script>

        <script>
            $(function() {
                $("#dataIni, #dataFim").datepicker({minDate: new Date(2009, 1 - 1, 1)});
                $("#dataIni, #dataFim").datepicker({showMonthAfterYear: true});
            });
        </script>
        <style>
            p{
                padding: 3px;
            }
            .bt-rel_analitico{

                background-color:  #cccccc;
                color:#000;
                font-weight: bold;
                text-decoration: none;
                width:250px;
                height:50px;
                padding: 3px;
                margin-bottom: 10px;
                border: 1px solid  #9f9f9f;
            }
            .bt-rel_analitico:hover{
                color:   #FFF;
                background-color:    #31a2ec;
                text-decoration: underline;
            }
        </style>
    </head>
    <body class="novaintra">
        <form action="" method="post" name="form1">
            <div id="content">
                <div id="head">
                    <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>RH - Relatório Gerencial</h2>
                        <p>Controle de movimentação financeira do RH</p>
                    </div>
                </div>
                <br class="clear">

                <br/>
                <fieldset>
                    <legend>Filtro</legend>
                    <div class="fleft">
                        <p><label class="first">Projeto:</label> <?php echo montaSelect(GlobalClass::carregaProjetosByRegiao($usuario['id_regiao'], array("-1" => "« Todos os Projetos »")), $regiaoR, "id='projeto' name='projeto' class='required[custom[select]]'") ?></p>
                        <p>
                            <label class="first">Período: Início - </label><input type="text" id="dataIni" name="dataIni" class="validate[required]"/>
                            <label class="first">Fim - </label><input type="text" id="dataFim" name = "dataFim" class="validate[required]"/>
                        </p>
                    </div>
                    <br class="clear"/>
                    <p class="controls" style="margin-top: 10px;"><input type="submit" name="filtrar" value="Filtrar" id="filtrar" class="button" style="padding: 5px 25px; background: #f4f4f4; border: 1px solid #ccc; cursor: pointer;" /></p>
                </fieldset>
                <br/><br/>
                <?php if ($lista) { ?>
                    <?php
                    if (mysql_num_rows($result) == 0) {
                        echo "<div id='message-box' class='message-red'>Nenhum registro encontrado para o filtro selecionado.</div>";
                    } else {
                        $arrayFolha = array();
//                        $totalGps = $totalFgts = $totalPis = $totalIr = 0;
                        $arrayTotal = array();
                        while ($row_folha = mysql_fetch_assoc($result)) {
                            $arrayFolha[$row_folha['projeto']][] = $row_folha;
                        }
                        ?>
                        <table width="100%" cellspacing="0" cellpadding="0" class="grid">
                                <?php
                                    foreach ($arrayFolha as $projeto => $arrayValue) {
                                        foreach ($arrayValue as $value) {
                                            if ($projeto != $projetoAnterior) {
                                                ?>
                                                <thead>
                                                <tr>
                                                    <th colspan="7"><span class="dados"><?= $value['nome']; ?></span></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr class="subtitulo">
                                                    <td>ID folha</td>
                                                    <td>Mês/Ano</td>                                       
                                                    <td>GPS</td>
                                                    <td>FGTS</td>
                                                    <td>PIS</td>
                                                    <td>IR</td>
                                                    <!--<th class="separa">&nbsp</th>-->
                        <!--                                        <th>TRANSPORTE</th>
                                                    <th>ALIMENTAÇÃO</th>-->
                                                </tr>
                                                <?php
                                                $projetoAnterior = $projeto;
                                            }

                                            $sql = "SELECT (
                                                SELECT B.valor
                                                FROM pagamentos AS A
                                                LEFT JOIN saida AS B ON(A.id_saida=B.id_saida)
                                                WHERE A.id_folha = {$value['id_folha']} AND A.tipo_pg = 1 AND A.tipo_contrato_pg = 1 AND B.`status`= 2
                                                ORDER BY data_proc DESC
                                                LIMIT 1) AS gps, (
                                                SELECT B.valor
                                                FROM pagamentos AS A
                                                LEFT JOIN saida AS B ON(A.id_saida=B.id_saida)
                                                WHERE A.id_folha = {$value['id_folha']} AND A.tipo_pg = 2 AND A.tipo_contrato_pg = 1 AND B.`status`= 2
                                                ORDER BY data_proc DESC
                                                LIMIT 1) AS fgts, (
                                                SELECT B.valor
                                                FROM pagamentos AS A
                                                LEFT JOIN saida AS B ON(A.id_saida=B.id_saida)
                                                WHERE A.id_folha = {$value['id_folha']} AND A.tipo_pg = 3 AND A.tipo_contrato_pg = 1 AND B.`status`= 2
                                                ORDER BY data_proc DESC
                                                LIMIT 1) AS pis, (
                                                SELECT B.valor
                                                FROM pagamentos AS A
                                                LEFT JOIN saida AS B ON(A.id_saida=B.id_saida)
                                                WHERE A.id_folha = {$value['id_folha']} AND A.tipo_pg = 4 AND A.tipo_contrato_pg = 1 AND B.`status`= 2
                                                ORDER BY data_proc DESC
                                                LIMIT 1) AS ir, (
                                                SELECT B.valor
                                                FROM pagamentos AS A
                                                LEFT JOIN saida AS B ON(A.id_saida=B.id_saida)
                                                WHERE A.id_folha = {$value['id_folha']} AND A.tipo_pg = 5 AND A.tipo_contrato_pg = 1 AND B.`status`= 2
                                                ORDER BY data_proc DESC
                                                LIMIT 1) AS transporte, (
                                                SELECT B.valor
                                                FROM pagamentos AS A
                                                LEFT JOIN saida AS B ON(A.id_saida=B.id_saida)
                                                WHERE A.id_folha = {$value['id_folha']} AND A.tipo_pg = 6 AND A.tipo_contrato_pg = 1 AND B.`status`= 2
                                                ORDER BY data_proc DESC
                                                LIMIT 1) AS sodexo";
//                                        print_r($sql); exit;

                                            $query_controle = mysql_query($sql);
                                            $row_controle = mysql_fetch_assoc($query_controle);
                                            $tipos = array("1" => "gps", "2" => "fgts", "3" => "pis", "4" => "ir", "5" => "transporte", "6" => "sodexo");
                                            
                                            ?> 
                                            <tr>

                                                <td><span class="dados"><?= $value['id_folha'] ?></span></td>
                                                <?php
                                                if ($value['terceiro'] == '1') {
                                                    if ($value['tipo_terceiro'] == 3) {
                                                        $decimo3 = " - 13ª integral";
                                                    } else {
                                                        $decimo3 = " / 13ª ({$value['tipo_terceiro']}ª) Parcela";
                                                    }
                                                    ?> 
                                                <?php }   
                                                    $dt = explode('-', $value['data_inicio']);
                                                    $mes = mesesArray($dt[1]);
                                                    $ano = $dt[0];
                                                    
                                                ?>
                                                
                                                <td><span class="dados"><?= $mes.'/'.$ano. $decimo3 ?></span></td> 

                                                <?php
                                                
                                                for ($i = 1; $i <= 4; $i++) {
                                                    if (!empty($row_controle[$tipos[$i]])) {
                                                        $arrayTotal[$i]=$row_controle[$tipos[$i]]+$arrayTotal[$i];
                                                        ?>      

                                                <td align="center"><span class="dados"><?= formataMoeda($row_controle[$tipos[$i]]); ?></span></td> 

                                                    <?php } else { ?>
                                                        <td align="center"><span class="dados"><? echo '----'; ?></span></td> 
                                                        <?php
                                                    }
                                                }
                                                unset($decimo3);
                                                ?>    
                                            </tr>             
                                            <?php
                                        }
                                        
                                    }
//                                    echo'<pre>';
//                                    print_r($arrayTotal);
//                                    echo '</pre>';
//                                    exit;
                                    ?>
                                            
                                            <tr class="subtitulo">
                                                <td colspan="2">Totalizador:</td>
                                                <td><center><?= formataMoeda($arrayTotal[1]); ?></center></td>
                                                <td><center><?= formataMoeda($arrayTotal[2]); ?></center></td>
                                                <td><center><?= formataMoeda($arrayTotal[3]); ?></center></td>
                                                <td><center><?= formataMoeda($arrayTotal[4]); ?></center></td>
                                            </tr>
                                    
                  <?php          }
                                ?>
                            </tbody>
                        </table>                                
                    <?php } ?>
<?php // } ?>
            </div>
        </form>
    </body>
    <di  style="background-color: #c8ebf9 ">

    </di>
</html>