<?php

// Verificando se o usuário está logado
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="../../../login.php">Logar</a>';
    exit;
}

// Incluindo Arquivos
require('../../conn.php');
include('../../funcoes.php');

// Buscando a Folha
list($regiao, $folha) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));

// Incluindo Arquivos
include('../../classes/calculos.php');
include('../../classes/abreviacao.php');
include('../../classes/formato_valor.php');
include('../../classes/formato_data.php');
include('../../classes/valor_proporcional.php');
include('../../classes/FolhaClass.php');
include('../../classes/regiao.php');
include("../../wfunction.php");

$Regi = new regiao();
$Trab = new proporcional();
$objFolha = new Folha();
$unidadesSel = (isset($_REQUEST['id_unidade']))?$_REQUEST['id_unidade']:"6";

$unidades = array();
$queryUnidade = "SELECT * FROM unidade AS A WHERE A.id_regiao = '{$regiao}'";
$sqlUindades = mysql_query($queryUnidade) or die("Erro ao selecionar unidades");
if(mysql_num_rows($sqlUindades) > 0){
    while($rowsUnidade = mysql_fetch_assoc($sqlUindades)){
        $unidades[$rowsUnidade['id_unidade']] = $rowsUnidade['unidade'];
    }
}


$qr_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_user = mysql_fetch_assoc($qr_user);
$id_master = $row_user['id_master'];

$qr_folha = mysql_query("SELECT A.id_folha, B.nome as nome_projeto, DATE_FORMAT(A.data_inicio,'%d/%m/%Y') as data_inicio, 
                        DATE_FORMAT(A.data_fim,'%d/%m/%Y') as data_fim, C.nome_mes, A.ids_movimentos_estatisticas
                        FROM rh_folha as A 
                        INNER JOIN projeto as B ON B.id_projeto = A.projeto
                        INNER JOIN ano_meses as C ON C.num_mes = A.mes
                        WHERE A.id_folha = $folha");
$row_folha = mysql_fetch_assoc($qr_folha);

$dados = array();
$qry_pis = "SELECT A.id_clt, A.id_projeto, C.id_unidade, C.unidade, B.nome as nome_projeto, A.mes, A.ano, A.nome, 
                                    (A.base_inss) AS base, ((A.base_inss) * 0.01) as valor  FROM rh_folha_proc AS A 
                                    LEFT JOIN projeto AS B ON(A.id_projeto = B.id_projeto) 
                                    LEFT JOIN rh_clt AS C ON(A.id_clt = C.id_clt)
                                    WHERE A.id_folha = '{$folha}' AND C.id_unidade = '{$unidadesSel}'";
                                    
$sql_pis  = mysql_query($qry_pis) or die("Erro ao verificar pis");
while($rows = mysql_fetch_assoc($sql_pis)){
    $dados[] = $rows;  
}

?>
 

<html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
            <title>:: Intranet :: Relatório de PIS (<?= $folha ?>)</title>
            <!--<link href="sintetica/folha.css" rel="stylesheet" type="text/css">-->
            <link href="../../net1.css" rel="stylesheet" type="text/css">
            <link href="../../favicon.ico" rel="shortcut icon">
            <!--<link href="../../js/highslide.css" rel="stylesheet" type="text/css" />-->
            <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
            <script src="../../js/highslide-with-html.js" type="text/javascript"></script>
            <script src="../../js/global.js" type="text/javascript"></script>
            <script type="text/javascript">
                hs.graphicsDir = '../../images-box/graphics/';
                hs.outlineType = 'rounded-white';

                $(function() {
                   
                });

            </script>
            <style type="text/css">
                .even{ background: #f1f1f1;}
                .odd{ background: #f4f4f4;}
                #tabela tr{
                    font-size:10px;
                    text-align: left;
                    padding: 10px;
                    box-sizing: border-box;
                }	
                #tabela td{
                    height: 30px;
                    text-align: left;
                    padding: 10px;
                    box-sizing: border-box;
                }	
                .totalizador tr, .totalizador td {
                    border: 1px solid #ccc;
                }
                
            </style>
        </head>
        <body class="novaintra" >
            <div id="content">

                    <div id="head">
                    <!--<div class="link_voltar"><a href="<?php echo $link_voltar; ?>" title="Voltar para a folha"> <img src="../../imagens/back.png" width="30" height="30"/> </a> </div>-->
                    <img src="../../imagens/logomaster<?php echo $id_master; ?>.gif" class="fleft" style="margin-right: 25px;" width="140" height="100"/>
                    <div class="fleft">
                        <h2>Relatório de PIS</h2>  
                        <p><strong><?php echo $row_folha['nome_mes']; ?></strong></p>
                        <p><strong>Folha:</strong> <?php echo $row_folha['id_folha']; ?></p>
                        <p><strong>Período:</strong> <?php echo $row_folha['data_inicio']; ?> a  <?php echo $row_folha['data_fim']; ?> </p>
                    </div>
                </div>
                <br class="clear">
                <br/>

                <form  name="form" action="" method="post" id="form">
                    <fieldset>
                        <legend>Dados</legend>
                        <div class="fleft">
                            <p><label class="first">Unidade:</label> <?php echo montaSelect($unidades, $unidadesSel, array('name' => "id_unidade", 'id' => 'id_unidade')); ?></p>
                        </div>

                        <br class="clear"/>                
                        <p class="controls" style="margin-top: 10px;">
                            <input type="submit" name="gerar" value="Gerar" id="gerar"/>
                        </p>
                    </fieldset>
                </form>
                <div class="clear"></div>
                
                <?php if (isset($_POST['gerar'])) { ?>
                
                <p style="text-align: right;"><input type="button" onclick="tableToExcel('tabela', 'Folha Analitica')" value="Exportar para Excel" class="exportarExcel"></p>
                <table cellpadding="0" cellspacing="0" id="tabela" width="100%" class="grid">       
                    <?php $titulo = 1; $totalBase = 0; $totalValor = 0; $contador = 0; ?>
                    <?php foreach ($dados as $dadosPis){ ?>
                        <?php $contador++; if($titulo == 1){ ?>
                            <?php $titulo++; ?>
                            <thead>
                                <tr style="">
                                    <td colspan="4" style="font-size: 16px; text-align: center; text-transform: uppercase; background: #ccc">Movimentos de PIS Competência (<?php echo $dadosPis['mes']."/".$dadosPis['ano'] . " - " . $dadosPis['nome_projeto']; ?>)</td> 
                                </tr>
                                <tr class="" style="background: #ddd !important; color: #333; line-height: 0px !important;">
                                    <td>ID</td>
                                    <td>NOME</td>
                                    <td>BASE</td>
                                    <td>VALOR</td>
                                </tr>
                            </thead>
                            <tbody>
                        <?php } ?>
                        <tr class="<?php echo ($contador %2) ? "even":"odd"; ?>">
                            <td><?php echo $dadosPis['id_clt']; ?></td>
                            <td><?php echo $dadosPis['nome']; ?></td>
                            <td><?php echo "R$ " . number_format($dadosPis['base'],2,',','.'); $totalBase += $dadosPis['base']; ?></td>
                            <td><?php echo "R$ " . number_format($dadosPis['valor'],2,',','.'); $totalValor += $dadosPis['valor']; ?></td>
                        </tr>
                    <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2"></td>
                                <td><?php echo "<b>R$ " . number_format($totalBase,2,',','.') . "</b>"; ?></td>
                                <td><?php echo "<b>R$ " . number_format($totalValor,2,',','.') . "</b>"; ?></td>
                            </tr>
                        </tfoot>
                    </table>
                <div class="clear"></div>
                <br />
                
                <?php } ?>
            </div>
        </body>
    </html>
    