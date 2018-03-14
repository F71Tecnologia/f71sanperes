<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include "../conn.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include("../wfunction.php");


$Fun = new funcionario();
$Fun->MostraUser(0);
$Master = $Fun->id_master;
$REGIOES = new Regioes();

$usuario = carregaUsuario();





$rsMeses = montaQuery('ano_meses', "num_mes, nome_mes");
$meses = array('' => '<< Mês >>');
foreach ($rsMeses as $valor) {
    $meses[$valor['num_mes']] = $valor['nome_mes'];
}
$mesesSel = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : '';

$anoOpt = array('' => '<< Ano >>');
for ($i = 2012; $i <= date('Y'); $i++) {

    $anoOpt[$i] = $i;
}
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : '';




If (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {

    $id_master = $_REQUEST['master'];
    $id_regiao = $_REQUEST[regiao];
    $id_projeto = $_REQUEST[projeto];
    $mes = $_REQUEST['mes'];
    $ano = $_REQUEST['ano'];
    $dt_referencia = $ano . '-' . $mes . '-01';

    $where_projeto = (isset($_REQUEST['todos_projetos']))?"AND A.id_projeto = '$id_projeto'":'';
    

    $qr_relatorio = mysql_query("SELECT A.*, DATE_FORMAT(A.data_adm, '%d/%m/%Y') as dt_admissao, 
                            DATE_FORMAT(A.data_demi, '%d/%m/%Y') as dt_demissao, B.nome as nome_projeto
                            FROM rh_recisao  as A
                            INNER JOIN projeto as B
                            ON B.id_projeto = A.id_projeto
                            WHERE A.id_regiao = '$id_regiao' $where_projeto
                            AND MONTH(A.data_demi) = '$mes' AND YEAR(A.data_demi) = '$ano' AND A.status = 1;") or die(mysql_error());
}
?>
<html>
    <head>
        <title>Relatório</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="../net1.css" rel="stylesheet" type="text/css">
        <script src="../jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
        <script src="../jquery/jquery.tools.min.js" type="text/javascript" ></script>

        <script>
            $(function () {


                $('#master').change(function () {
                    var id_master = $(this).val();
                    $('#regiao').next().html('<img src="../img_menu_principal/loader16.gif"/>');
                    $.ajax({
                        url: '../action.global.php?master=' + id_master,

                        success: function (resposta) {
                            $('#regiao').html(resposta);
                            $('#regiao').next().html('');
                        }
                    });

                    $('#regiao').trigger('change')
                });



                $('#regiao').change(function () {
                    var id_regiao = $(this).val();

                    $('#projeto').next().html('<img src="../img_menu_principal/loader16.gif"/>');
                    $.ajax({
                        url: '../action.global.php?regiao=' + id_regiao,
                        success: function (resposta) {
                            $('#projeto').html(resposta);
                            $('#projeto').next().html('');
                        }
                    });


                });

                $('#master').trigger('change');

            });

        </script>
        <style media="screen">
            table{ font-size: 10px;}
            .regiao { color:   #0078FF; 
                      font-size: 16px; 
                      font-weight: bold;
            }
            .projeto { color:     #000b0b; 
                       font-size: 16px; 

            }
        </style>
        <style media="print">
            fieldset{display: none;}
            body{ background-color: #FFF;}
        </style>

    </head>
    <body class="novaintra" >        
        <div id="content" style="width:1200px;">
            <div id="head">
                <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;" width="140" height="100"/>
                <div class="fleft">
                    <h2>Relatório de Rescisão</h2>
                    <p></p>
                </div>
            </div>
            <br class="clear">
            <br/>

            <form  name="form" action="" method="post" id="form">
                <fieldset>
                    <legend>Relatório</legend>
                    <div class="fleft">

                        <p><label class="first">Região:</label> 
                            <select name="regiao" id="regiao">

                                <?php $REGIOES->Preenhe_select_por_master($Master, $regiao) ?>

                            </select>
                            <span class="loader"></span></p>                        
                        <p><label class="first">Projeto:</label> <?php echo montaSelect($optProjeto, $projetoSel, array('name' => "projeto", 'id' => 'projeto')); ?><span class="loader"></span></p>                        
                        <p><label class="first">Mês:</label> <?php echo montaSelect($meses, $mesesSel, array('name' => "mes", 'id' => 'mes')); ?><span class="loader"></span></p>                        
                        <p><label class="first">Ano:</label> <?php echo montaSelect($anoOpt, $anoSel, array('name' => "ano", 'id' => 'ano')); ?><span class="loader"></span></p>                        
                    </div>


                    <br class="clear"/>

                    <p class="controls" style="margin-top: 10px;">
                        <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                        <?php
                        ///permissão para VISUALIZAR TODOS OS PROJETOS AO MESMO TEMPO
                        if ($ACOES->verifica_permissoes(85)) {
                            ?>
                            <button type="submit" name="todos_projetos" value="gerar de todos os projetos" id="todos_projetos" class="btn btn-warning"><span class="fa fa-filter"></span> Filtrar Todos os Projetos</button>
                        <?php } ?>
                        <input type="submit" name="gerar" value="Gerar" id="gerar"/>
                        zxzxzz
                    </p>
                </fieldset>
            </form>
            <?php
            if (isset($_POST['gerar'])) {

                if (mysql_num_rows($qr_relatorio) != 0) {
                    while ($row_rel = mysql_fetch_assoc($qr_relatorio)) {

                        if ($cont_topo == 0) {
                            $cont_topo = 1;
                            echo '<h3>' . $row_rel['nome_projeto'] . ' - COMP. ' . mesesArray($mes) . '/' . $ano . '</h3>';
                            ?>
                        </table>
                        <table border="0" cellpadding="0" cellspacing="0" class="grid" >
                            <tr class="titulo">
                                <td width="30%">NOME</td>
                                <td width="10%">DATA DE ADMISSÃO</td>
                                <td width="10%">DATA DE DEMISSÃO</td>                                                        
                            </tr>  
                        <?php } ?>  

                        <tr style="font-size:11px;">
                            <td><?php echo $row_rel['nome'] ?></td>
                            <td align="center"><?php echo $row_rel['dt_admissao'] ?></td>
                            <td align="center"><?php echo $row_rel['dt_demissao'] ?></td>


                        </tr>

                        <?php
                        $regiaoAnt = $row_rel['id_regiao'];
                        $projetoAnt = $row_rel['id_projeto'];
                    }
                    echo '</table>';
                } else {

                    echo 'Nenhum rescindido nesta na competência ' . mesesArray($mes) . '/' . $ano . '!';
                }
            }
            ?>  
            <div class="clear"></div>
    </div>


</body>
</html>