<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include "../../conn.php";
include "../../classes/funcionario.php";
include '../../classes_permissoes/regioes.class.php';
include "../../wfunction.php";
include "../../funcoes.php";

$usuario = carregaUsuario();
$optRegiao = getRegioes();

if (isset($_REQUEST['gerar'])) {
    $cont = 0;
    /*$arrayStatus = array(10,20,30,40,50,51,52);
    $status = implode(",", $arrayStatus);*/

    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    
    if(isset($_REQUEST['data_ini']) AND !empty($_REQUEST['data_ini'])){
        $dataIni = converteData($_REQUEST['data_ini']);
        $dataIni = " AND s.data_vencimento >= '$dataIni' ";
    }
    if(isset($_REQUEST['data_fim']) AND !empty($_REQUEST['data_fim'])){
        $dataFim = converteData($_REQUEST['data_fim']);
        $dataFim = " AND s.data_vencimento <= '$dataFim' ";
    }
    
    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto'=>$id_projeto));
    $sql = "SELECT 
                s.id_saida idSaida,
                s.nome nomeSaida,
                s.especifica descricaoSaida,
                CAST(REPLACE(REPLACE(s.adicional,'.',''),',','.') AS DECIMAL(10,2)) adicional,
                CAST(REPLACE(REPLACE(s.valor,'.',''),',','.') AS DECIMAL(10,2)) valor,
                s.comprovante comprovante,
                DATE_FORMAT(s.data_vencimento, '%d/%m/%Y') dataRecebimento,
                es.nome nomeSubgrupo,
                eg.nome_grupo nomeGrupo,
                b.conta contaBanco,
                b.agencia agenciaBanco,
                e.nome tipoSaida,
                f.nome nomeFuncionario,
                fpg.nome nomeFuncionarioPg
            FROM 
                saida s,
                entradaesaida_subgrupo es,
                entradaesaida_grupo eg,
                entradaesaida e,
                bancos b,
                funcionario f,
                funcionario fpg
            WHERE 
                es.id = s.entradaesaida_subgrupo_id
                AND es.entradaesaida_grupo = eg.id_grupo
                AND s.tipo = e.id_entradasaida
                AND s.id_regiao = '$id_regiao'
                AND s.id_projeto = '$id_projeto'
                /*AND s.id_regiao = '45'
                AND s.id_projeto = '3302'*/
                AND b.id_banco = s.id_banco 
                AND b.status_reg = '1'
                AND s.id_user = f.id_funcionario
                AND s.id_userpg = fpg.id_funcionario
                $dataIni
                $dataFim
            ORDER BY s.data_vencimento ASC
                ";
    $qr_relatorio = mysql_query($sql);
}

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
?>
<html>
    <head>
        <title>:: Intranet :: Relatório Saídas</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../favicon.ico" rel="shortcut icon" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>

        <script>
            $(function() {
                $('#regiao').ajaxGetJson("../../methods.php", {method: "carregaProjetos"}, null, "projeto");
                
                $('.date').datepicker({
                        dateFormat: 'dd/mm/yy',
                        changeMonth: true,
                        changeYear: true
                });
            });
        </script>
    </head>
    <body class="novaintra" >        
        <div id="content">
            <form  name="form" action="" method="post" id="form">
                <div id="head">
                    <img src="../../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>Relatório Saídas</h2>
                    </div>
                </div>
                <br class="clear">
                <br/>


                <fieldset class="noprint">
                    <legend>Relatório</legend>
                    <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
                    <p><label class="first">Região:</label> <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao')); ?> </p>                        
                    <p><label class="first">Projeto:</label> <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto')); ?> </p>
                    <p><label class="first">Ver Saídas de</label> <input name="data_ini" id="data_ini" type="text" size="10" maxlength="10" class="date" value="<?php echo $_REQUEST['data_ini']; ?>"> <label style="font-weight: bold;">até</label> <input name="data_fim" id="data_fim" type="text" size="10" maxlength="10" class="date" value="<?php echo $_REQUEST['data_fim']; ?>"></p>

                    <p class="controls" >
                        <input type="submit" name="gerar" value="Gerar" id="gerar"/>
                    </p>
                </fieldset>

                <?php if (!empty($qr_relatorio) && isset($_POST['gerar'])) { ?>
                <p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" value="Exportar para Excel" class="exportarExcel"></p>    
                <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" class="grid" width="100%" style="page-break-after:auto;"> 
                        <thead>
                            <tr>
                                <th colspan="16"><?php echo $projeto['nome'] ?></th>
                            </tr>
                            <tr>
                                <th colspan="3"></th>
                                <th>CÓDIGO SAÍDA</th>
                                <th>DATA DE RECEBIMENTO</th>
                                <th>NOME DO CRÉDITO</th>
                                <th>CONTA DEBITADA</th>
                                <th>TIPO DE SAÍDA</th>
                                <th>GRUPO</th>
                                <th>SUBGRUPO</th>
                                <th>DESCRIÇÃO</th>
                                <th>CADASTRADA POR</th>
                                <th>PAGO POR</th>
                                <th>VALOR ADICIONAL</th>
                                <th>VALOR TOTAL</th>  
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                        while ($row_rel = mysql_fetch_assoc($qr_relatorio)) {
                            //$class = ($cont++ % 2 == 0)?"even":"odd";
                            $link_encryptado = encrypt('ID='.$row_rel['idSaida'].'&tipo=0');
                            $link_encryptado_pg = encrypt('ID='.$row_rel['idSaida'].'&tipo=1');

                            $qr_quant = mysql_query("SELECT * FROM saida_files WHERE id_saida = ".$row_rel['idSaida']);
                            $comprovante = '';
                            while($row_quant = mysql_fetch_assoc($qr_quant)){
                                $nomeArquivo = '';
                                $nomeArquivo = $row_quant['id_saida_file'].'.'.$row_quant['id_saida'].$row_quant['tipo_saida_file'];
                                if(file_exists("../../comprovantes/$nomeArquivo")){
                                    $comprovante .= '<a target="_blank" title="Comprovante" href="../../comprovantes/'.$nomeArquivo.'"><img src="../../financeiro/imagensfinanceiro/attach-32.png"  /></a>
                                    <!--<a target="_blank" title="Comprovante" href="../view/comprovantes.php?'.$link_encryptado.'"><img src="../../financeiro/imagensfinanceiro/attach-32.png"  /></a>-->';
                                }else{
                                    $comprovante .= '<a target="_blank" title="Comprovante" href="../../comprovantes/'.$nomeArquivo.'"><img style="border: 1px solid #F00;" src="../../financeiro/imagensfinanceiro/attach-32.png"  /></a>';
                                }
                            }
                            
                            $qr_quant2 = mysql_query("SELECT * FROM saida_files_pg WHERE id_saida = ".$row_rel['idSaida']);
                            $comprovantePg = '';
                            while($row_quant2 = mysql_fetch_assoc($qr_quant2)){
                                $nomeArquivoPg = '';
                                $nomeArquivoPg = $row_quant2['id_pg'].'.'.$row_quant2['id_saida'].'_pg'.$row_quant2['tipo_pg'];
                                if(file_exists("../../comprovantes/$nomeArquivoPg")){
                                    $comprovantePg .= '<a target="_blank" title="Comprovante Pagamento" href="../../comprovantes/'.$nomeArquivoPg.'"><img src="../../financeiro/imagensfinanceiro/attach-32.png"  /></a>
                                    <!--<a target="_blank" title="Comprovante" href="../view/comprovantes.php?'.$link_encryptado.'"><img src="../../financeiro/imagensfinanceiro/attach-32.png"  /></a>-->';
                                }else{
                                    $comprovantePg .= '<a target="_blank" title="Comprovante Pagamento" href="../../comprovantes/'.$nomeArquivoPg.'"><img style="border: 1px solid #F00;" src="../../financeiro/imagensfinanceiro/attach-32.png"  /></a>';
                                }
                            }
                            $quanti2 = mysql_num_rows($qr_quant2);
                            
                            $totalSaida += $row_rel['valor'];
                            ?>
                            <tr class="<?php echo $class ?>">
                                <td><a href="../cad_edit_saida.php?id=<?php echo $row_rel['idSaida']; ?>&amp;rel&amp;keepThis=true&amp;TB_iframe=true&amp;width=800&amp;height=600" class="thickbox"><img src="../../imagens/icone_lapis.png" width="15" heigth="15"></a></td>
                                <td>
                                    <?php if(mysql_num_rows($qr_quant) > 0 OR $row_rel['comprovante'] == 1){ echo $comprovante; } ?>
                                </td>
                                <td>
                                    <?php if(mysql_num_rows($qr_quant2) > 0){ echo $comprovantePg; } ?></td>
                                <td><?php echo $row_rel['idSaida']; ?></td>
                                <td><?php echo $row_rel['dataRecebimento']; ?></td>
                                <td><?php echo $row_rel['nomeSaida']; ?></td>
                                <td>AG: <?php echo $row_rel['agenciaBanco']; ?> / C: <?php echo $row_rel['contaBanco']; ?></td>
                                <td><?php echo $row_rel['tipoSaida']; ?></td>
                                <td><?php echo $row_rel['nomeGrupo']; ?></td>
                                <td><?php echo $row_rel['nomeSubgrupo']; ?></td>
                                <td><?php echo $row_rel['descricaoSaida']; ?></td>
                                <td><?php echo $row_rel['nomeFuncionario']; ?></td>
                                <td><?php echo $row_rel['nomeFuncionarioPg']; ?></td>
                                <td><?php echo number_format($row_rel['adicional'],2,',','.'); ?></td>
                                <td><?php echo number_format($row_rel['valor'],2,',','.'); ?></td>
                            </tr>   
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr class="<?php echo $class ?>">
                            <th colspan="13" style="text-align: right;">TOTAL: </th>
                            <th colspan="2"><?php echo number_format($totalSaida,2,',','.'); ?></th>
                        </tr>   
                    </tfoot>
                </table>
                <?php  } ?>
            </form>
        </div>
    </body>
</html>