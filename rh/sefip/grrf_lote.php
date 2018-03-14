<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}

include_once "../../conn.php";
include_once "../../classes/funcionario.php";
include_once '../../classes_permissoes/regioes.class.php';
include_once "../../wfunction.php";


if (isset($_REQUEST['gerar_grrf_lote'])) {
    $sqlValores = mysql_query("SELECT * FROM import_grrf_lote WHERE ano = {$_REQUEST['ano_gerado']} AND mes = {$_REQUEST['mes_gerado']} AND id_projeto = {$_REQUEST['projeto_gerado']} AND id_clt IN(". implode(',', $_REQUEST[clt]).")") or die(mysql_error());
    while($rowValores = mysql_fetch_assoc($sqlValores)){ $arrValores[$rowValores[id_clt]] = $rowValores[valor]; }
//    echo '<pre>';
//    print_r($arrValores);
//    echo '</pre>';
    
    include_once("monta_arquivo_grrf_lote.php");
    
    exit;
}

if (isset($_GET[download])) {
    include_once("monta_arquivo_grrf_lote.php");
}


$usuario = carregaUsuario();
$optRegiao = getRegioes();
$arrProj = array('all' => "<< TODOS OS PROJETOS >>");
foreach ($optRegiao as $k => $proj) {
    $arrProj[$k] = $proj;
}
unset($arrProj['-1']);

//$ano = array("2013" => 2013, "2014" => 2014);

$ano = anosArray(null, null, array('' => "<< Ano >>"));
$mes = mesesArray();

$regiaoSel = (!empty($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario[id_regiao];
$projetoSel = (!empty($_REQUEST['projeto_gerado'])) ? $_REQUEST['projeto_gerado'] : $_REQUEST['projeto'];
$projetoSel = (!empty($projetoSel)) ? $projetoSel : -1;
$anoSel = (!empty($_REQUEST['ano'])) ? $_REQUEST['ano'] : date("Y");
$mesSel = (!empty($_REQUEST['mes'])) ? $_REQUEST['mes'] : date("m");

if (isset($_REQUEST['gerar'])) {

    $sql = "
    SELECT 
        A.fgts_cod,
        D.cod_movimentacao,
        A.id_clt,A.nome,
        B.id_regiao,B.regiao,
        C.id_projeto,C.nome as nome_projeto
    FROM 
        (rh_recisao A
        INNER JOIN regioes B ON A.id_regiao = B.id_regiao)
        INNER JOIN projeto C ON A.id_projeto = C.id_projeto
        INNER JOIN rhstatus D ON D.id_status = A.fgts_cod
    WHERE 
        MONTH(A.data_demi) = '$mesSel'
        AND YEAR(A.data_demi) = '$anoSel'
        AND A.status = '1'
        AND B.id_regiao IN($regiaoSel)
        AND A.id_projeto IN($projetoSel)
    ORDER BY B.id_regiao, A.nome ASC";

//    echo "<pre>$sql</pre>";
    $qr = mysql_query($sql);
    $qtdRegistro = mysql_num_rows($qr);
}
if (isset($_REQUEST['Importar'])) {
       
    
    if (is_uploaded_file($_FILES['importacao_grrf']['tmp_name'])) {
//        echo "<h1>" . "File ". $_FILES['importacao_grrf']['name'] ." transferido com sucesso ." . "</h1>";
//        echo "<h2>Exibindo o conteúdo:</h2>";
//        readfile($_FILES['importacao_grrf']['tmp_name']);
    }

    //Importar o arquivo transferido para o banco de dados
    $handle = fopen($_FILES['importacao_grrf']['tmp_name'], "r");
    $c=0;
    while (($data = fgets($handle))) {
        if($c == 0){ $c++;}
        else {
            //echo "A<br>";
            $d = explode(';', $data);

            $converterValor = str_replace('.','',$d[6]);
            $converterValor = str_replace(',','.',$converterValor);
            
//            echo " * $d[6] - $converterValor<br>";
            
            $verificacao = mysql_query("SELECT id_importacao FROM import_grrf_lote 
            WHERE id_clt = $d[0] AND ano = $d[1] AND mes = $d[2] AND id_projeto = $d[4]");
            if(mysql_num_rows($verificacao) > 0){
                $rowVerificacao = mysql_fetch_assoc($verificacao);
                $update = "UPDATE import_grrf_lote SET valor = '$converterValor', data_import = NOW(), user_import = '$_COOKIE[logado]' WHERE id_importacao = $rowVerificacao[id_importacao] LIMIT 1";
                mysql_query($update) or die(mysql_error());
            } else {
                $insert = "INSERT INTO import_grrf_lote (id_importacao, id_clt, ano, mes, id_projeto, valor, data_import, user_import)
                VALUES ('', '$d[0]', '$d[1]', '$d[2]', '$d[4]', '$converterValor', NOW(), '$_COOKIE[logado]');";
                mysql_query($insert) or die(mysql_error());
            }
        }
    }
  
    fclose($handle);
  
    echo "<h1>" . "File ". $_FILES['importacao_grrf']['name'] ." importado com sucesso ." . "</h1>";

}
$semValor = 0; ?>
<html>
    <head>
        <title>:: Intranet :: Relatório de Contribuição Sindical</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../favicon.ico" rel="shortcut icon" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        <style>
            @media screen   {
                .hideTela { 
                    display: none;
                }
            }
        </style>
        <script>
            $(function() {
                $('#regiao').ajaxGetJson("../../methods.php", {method: "carregaProjetos", id_projeto: <?=$projetoSel?>}, null, "projeto");
            });
        </script>
    </head>
    <body class="novaintra" >        
        <div id="content">
            <form  name="form" action="" method="post" id="form" enctype="multipart/form-data">
                <div id="head">
                    <img src="../../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>GRRF LOTE</h2>
                    </div>
                </div>
                <br class="clear">
                <br/>


                <fieldset class="noprint">
                    <div class="fleft">
                        <p><label class="first">Região:</label> <?php echo montaSelect($arrProj, $regiaoSel, array('name' => "regiao", 'id' => 'regiao')); ?> </p>                        
                        <p><label class="first">Projeto:</label> <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto')); ?> </p>
                        <p><label class="first">Mês:</label> <?php echo montaSelect($mes, $mesSel, array('name' => "mes", 'id' => 'mes')); ?> </p>
                        <p><label class="first">Ano:</label> <?php echo montaSelect($ano, $anoSel, array('name' => "ano", 'id' => 'ano')); ?> </p>
                    </div>

                    <br class="clear"/>

                    <p class="controls" style="margin-top: 10px;">
                        <input type="submit" name="gerar" value="Gerar" id="gerar"/>
                    </p>
                </fieldset>
                <?php if (isset($_REQUEST['gerar']) AND $_REQUEST[projeto] > 0) { ?>
                
                <fieldset class="noprint">
                    <div class="fleft">
                        <p><label class="first">Importar CSV:</label> <input type="file" name="importacao_grrf"></p>
                    </div>

                    <br class="clear"/>

                    <p class="controls" style="margin-top: 10px;">
                        <input type="submit" name="Importar" value="Importar" id="Importar"/>
                    </p>
                </fieldset>

                    <?php $i = 1; ?>
                    <p style="text-align: right; margin-top: 20px"><span style="color: red;">Salvar arquivo como CSV (separado por vírgulas)(*.csv)</span>&nbsp;&nbsp;&nbsp;<input type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" value="Exportar para Excel" class="exportarExcel"></p>    
                    <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" class="grid" width="100%" style="page-break-after:auto;"> 
                        <thead>
                            <tr>
                                <th>COD</th>
                                <th>ANO</th>
                                <th>MES</th>
                                <th>PROJETO</th>
                                <th class="hideTela">ID PROJETO</th>
                                <th>NOME</th>
                                <th>VALOR</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($rows = mysql_fetch_assoc($qr)) { 
                            $sqlVerif = mysql_fetch_assoc(mysql_query("SELECT valor FROM import_grrf_lote 
                            WHERE id_clt = $rows[id_clt] AND ano = $anoSel AND mes = $mesSel AND id_projeto = $rows[id_projeto]")); 
                            $valor = $sqlVerif[valor]; 
                            if($valor > 0){ $cor = 'style="background-color: #E3ECE3"'; } else { $cor = ''; $semValor = 1; } ?>
                            <tr>
                                <td <?=$cor?>><?=$rows[id_clt]?><input type="hidden" name="clt[]" value="<?=$rows[id_clt]?>"></td>
                                <td <?=$cor?>><?=$anoSel?></td>
                                <td <?=$cor?>><?=$mesSel?></td>
                                <td <?=$cor?>><?=$rows[nome_projeto]?></td>
                                <td <?=$cor?> class="hideTela"><?=$rows[id_projeto]?></td>
                                <td <?=$cor?>><?=$rows[nome]?></td>
                                <td <?=$cor?>><?= number_format($valor,2,',','.')?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                <?php } ?>  
                <?php if (isset($_REQUEST['gerar']) /*AND $semValor == 0*/ AND $qtdRegistro > 0) { ?>
                    <p class="controls" style="margin-top: 10px;">
                        Data Recolhimento: <input type="text" name="data" value="<?=date("d/m/Y")?>">
                        <input type="submit" name="gerar_grrf_lote" value="Gerar GRRF Lote">
                        <input type="hidden" name="projeto_gerado" value="<?=$projetoSel?>">
                        <input type="hidden" name="mes_gerado" value="<?=$mesSel?>">
                        <input type="hidden" name="ano_gerado" value="<?=$anoSel?>">
                    </p>
                <?php }?>
            </form>
        </div>
    </body>
</html>