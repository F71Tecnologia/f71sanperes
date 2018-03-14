<?php
include('../../conn.php');
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}

include('../../funcoes.php');
include('../../wfunction.php');
include('../../classes/global.php');
include('../../classes/curso.php');
include('../../classes/projeto.php');
include('../../classes/rescisao.php');

$Curso = new tabcurso();
$ClasPro = new projeto();

$regiao = $_REQUEST['regiao'];

$projetoR = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$anoR = (isset($_REQUEST['ano_referente'])) ? $_REQUEST['ano_referente'] : date('Y');
$mesR = (isset($_REQUEST['mes_referente'])) ? $_REQUEST['mes_referente'] : date('m');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Intranet :: Rescis&atilde;o</title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />

        <link href="../../js/jquery.ui.theme.css" rel="stylesheet" type="text/css" />
        <link href="../../js/jquery.ui.datepicker.css" rel="stylesheet" type="text/css" />
        <link href="../../js/highslide.css" rel="stylesheet" type="text/css" />
        <script src="../../js/highslide-with-html.js" type="text/javascript"></script>

        <script type="text/javascript" src="../../js/ramon.js"></script>
        <script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js"></script>
        <script type="text/javascript" src="../../js/jquery.ui.core.js"></script>
        <script type="text/javascript" src="../../js/jquery.ui.widget.js"></script>
        <script type="text/javascript" src="../../js/jquery.ui.datepicker.js"></script>

            <script>
                hs.graphicsDir = '../../images-box/graphics/';
                hs.outlineType = 'rounded-white';
            </script>
            
            <style>
                body {
                    background-color:#FAFAFA; 
                    text-align:center; 
                    margin:0px;
                }
                
                #corpo {
                    width:90%; 
                    background-color:#FFF; 
                    margin:0px auto; 
                    text-align:left; 
                    padding-top:20px; 
                    padding-bottom:10px;
                }

                #movimentos{
                    border-collapse: collapse;
                }       
                
                #movimentos tr{ 
                    border: 1px  #E8E8E8 solid;
                }     
                
                #movimentos td{ 
                    border: 1px #E8E8E8  solid;
                }        
                
                #movimentos thead{ 
                    font-weight: bold; 
                    text-align: center;
                }     

                /*form com filtro da consulta*/
                form.filtro{
                    margin: 10px auto;
                    width: 95%;
                }
                
                .tot{
                    background: #979494;
                    color: #fff;                    
                }                
                
                #report{
                    float:right; 
                    margin-right:20px;
                }
                
                #limpa{
                    clear:right;
                }
                
                #topo{
                    width:95%; 
                    margin:0px auto;
                }

            </style>
    </head>
    <body class='novaintra'>
        <div id="corpo">
            
            <div id="report">
                <?php include('../../reportar_erro.php'); ?>      
            </div>

            <div id="limpa"></div>

            <div id="topo" style="">
                <div style="float:left; width:25%;">
                    <a href="recisao2.php?regiao=<?= $regiao; ?>">
                        <img src="../../imagens/voltar.gif">
                    </a>
                </div>
                <div style="float:left; width:50%; text-align:center; font-family:Arial; font-size:24px; font-weight:bold; color:#000;">
                    RESCIS&Atilde;O POR COMPETÊNCIA
                </div>
                <div style="float:right; width:25%; text-align:right; font-family:Arial; font-size:12px; color:#333;">
                    <br><b>Data:</b> <?= date('d/m/Y'); ?>
                </div>
                <div style="clear:both;"></div>
            </div>

            <table width="95%" align="center" border="0" cellpadding="8" cellspacing="0" style="margin-top:20px;">
                <tr bgcolor="#999999">
                    <td colspan="4" class="show">
                        <span style="color:#F90; font-size:32px;">&#8250;</span> Relatório das rescisões por competência
                    </td>
                </tr>
            </table>

            <form action="" method="post" class="filtro">
                <fieldset>
                    <legend>Filtro</legend>
                    <input type="hidden" name="filtro" value="1" />
                    <p>
                        <label class="first">Projeto:</label>
                        <?php echo montaSelect(GlobalClass::carregaProjetosByRegiao($regiao, array("-1" => "Todos")), $projetoR, "id='projeto' name='projeto' class='required[custom[select]]'") ?>
                    </p>
                    <p>
                        <label class="first">Ano Referente:</label> <?php echo montaSelect(anosArray(),$anoR,"id='ano_referente' name='ano_referente'"); ?>
                    </p>
                    <p>
                        <label class="first">Mês Referente:</label> <?php echo montaSelect(mesesArray(),$mesR,"id='mes_referente' name='mes_referente'"); ?>
                    </p>
                    <p class="controls"><input type="submit" value="Consultar" class="button" name="consultar" /></p>
                </fieldset>
            </form>
            
            <?php
            if (isset($_REQUEST['filtro']) && !empty($_REQUEST['filtro'])) {
                
                // Consulta de clts Aguardando Demissão
                $qr_aguardo = getCLTAguardando($regiao, $projetoR, $mesR, $anoR);
                $total_aguardo = mysql_num_rows($qr_aguardo);

                if (!empty($total_aguardo)) {
                    ?>

                    <table width="95%" align="center" border="0" cellpadding="8" cellspacing="0" style="margin-top:20px;">
                        <tr bgcolor="#999999">
                            <td colspan="6" class="show">
                                <span style="color:#F90; font-size:32px;">&#8250;</span> Participantes aguardando a Rescis&atilde;o
                            </td>
                        </tr>
                        <tr class="novo_tr">
                            <td>COD</td>
                            <td>NOME</td>
                            <td>FUNÇÃO</td>
                            <td>DATA DE ENTRADA</td>
                            <td>DATA DE SAÍDA</td>
                        </tr>

                        <?php
                        while ($row_aguardo = mysql_fetch_array($qr_aguardo)) {                                                   

                            // Encriptografando a variável
                            $link = str_replace('+', '--', encrypt("{$regiao}&{$row_aguardo['id_clt']}"));
                            ?>

                            <tr style="background-color:<?php
                            if ($cor++ % 2 != 0) {
                                echo '#F0F0F0';
                            } else {
                                echo '#FDFDFD';
                            }
                            ?>">
                                <td><?= $row_aguardo['id_clt']; ?></td>
                                <td><a target="_blank" href="recisao2.php?tela=2&enc=<?= $link; ?>"><?= $row_aguardo['nome']; ?></a></td>
                                <td><?= $row_aguardo['nome_curso']; ?></td>
                                <td><?= $row_aguardo['data_admissao']; ?></td>
                                <td><?= $row_aguardo['data_demissao'];; ?></td>
                            </tr>

                        <?php } ?>

                    </table>

                <?php } ?>

                <table width="95%" align="center" border="0" cellpadding="8" cellspacing="0" style="margin-top:20px;">
                    <tr bgcolor="#999999">
                        <td colspan="9" class="show">
                            <span class="seta" style="color:#F90; font-size:32px;">&#8250;</span> Participantes Desativados
                        </td>
                    </tr>
                    <tr class="novo_tr">
                        <td>COD</td>
                        <td>NOME</td>
                        <td>FUNÇÃO</td>
                        <td align="center">DATA DE ENTRADA</td>
                        <td align="center">DATA DE SAÍDA</td>
                        <td align="center">RESCIS&Atilde;O</td>
                        <td>VALOR</td>
                        <td align="center">COMPLEMENTAR</td>
                        <td>VALOR</td>
                    </tr>

                    <?php
                    // Consulta de Clts que foram demitidos
                    $qr_demissao = getCLTDemitidos($regiao, $projetoR, $mesR, $anoR);
                    $total_rescisao = mysql_num_rows($qr_demissao);
                    $total_geral = getTotalRescisao($regiao, $projetoR, $mesR, $anoR);
                    
                    while ($row_demissao = mysql_fetch_array($qr_demissao)) {
                        
                        $id_clt = $row_demissao['id_clt'];
                        $id_rescisao = $row_demissao['id_recisao'];
                        $id_complementar = $row_demissao['id_complementar'];                        
                        $valor_complementar = $row_demissao['valor_complementar'];
                        
                        if($valor_complementar == ''){
                            $valor_complementar = '';
                        }else{                            
                            $valor_complementar = "R$ ".number_format($row_demissao['valor_complementar'], 2, ',', '.');
                        }
                        
                        // encriptografando a variável
                        $link = str_replace('+', '--', encrypt("{$regiao}&{$id_clt}&{$id_rescisao}"));    
                        $link2 = str_replace('+', '--', encrypt("{$regiao}&{$id_clt}&{$id_complementar}"));    
                        
                        if (substr($row_demissao['data_proc'], 0, 10) >= '2013-04-04') {
                            $link_nova_rescisao = "nova_rescisao_2.php?enc=$link";
                            $link_complementar = "nova_rescisao_2.php?enc=$link2";
                        } else {
                            $link_nova_rescisao = "nova_rescisao.php?enc=$link";
                            $link_complementar = "nova_rescisao.php?enc=$link2";
                        }
                        ?>

                        <tr style="background-color:<?php
                        if ($cor++ % 2 != 0) {
                            echo '#F0F0F0';
                        } else {
                            echo '#FDFDFD';
                        }
                        ?>">
                            <td><?= $row_demissao['id_clt']; ?></td>
                            <td><?= $row_demissao['nome']; ?></td>
                            <td><?= $row_demissao['nome_curso']; ?></td>
                            <td align="center"><?= $row_demissao['data_admissao'] ?></td>
                            <td align="center"><?= $row_demissao['data_demissao'] ?></td>
                            
                            <td align="center">
                                <?php if (empty($total_rescisao)) { ?>
                                <img src="../../imagens/pdf.gif" border="0" style="opacity:0.2; filter:alpha(opacity=20)" />
                                <?php } else { ?>
                                    <a href="<?= $link_nova_rescisao; ?>" class="link" target="_blank" title="Visualizar Rescisão"><img src="../../imagens/pdf.gif" border="0"></a>
                                <?php } ?>
                            </td>
                            
                            <td> R$ <?= number_format($row_demissao['total_liquido'], 2, ',', '.'); ?> </td>
                            
                            <td align="center">
                                <?php if($row_demissao['id_complementar'] != '') { ?>
                                    <a href="<?= $link_complementar; ?>" class="link" target="_blank" title="Visualizar Rescisão"><img src="../../imagens/pdf.gif" border="0"></a>
                                <?php } ?>
                            </td>
                            
                            <td> <?= $valor_complementar; ?> </td>
                        </tr>
                    <?php } ?>
                    
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="tot">TOTAL: R$ <?= number_format($total_geral['total_geral'], 2, ',', '.'); ?></td>                       
                        <td></td>
                        <td></td>
                    </tr>
                </table>
            
            <?php } ?>
            
        </div>
    </body>
</html>