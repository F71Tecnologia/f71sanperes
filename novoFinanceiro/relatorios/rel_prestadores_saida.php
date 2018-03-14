<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include "../../conn.php";
include "../../funcoes.php";
include "../../classes/funcionario.php";
include "../../classes_permissoes/regioes.class.php";
include "../../wfunction.php";

$usuario = carregaUsuario();
list($regiao) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));

///REGIÕES
$optRegiao = getRegioes($usuario['id_funcionario'], $usuario['id_master']);
$optProjeto = array("-1" => "« Selecione a Região »");

$meses = mesesArray(null);
$anoOpt = anosArray(null, null,array("-1"=>"« Selecione »"));

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : null;
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$mesesSel = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m');
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');

if (isset($_REQUEST['gerar'])) {

    $regiao = $_REQUEST['regiao'];
    $projeto = $_REQUEST['projeto'];
    $mes = $_REQUEST['mes'];
    $ano = $_REQUEST['ano'];

    $qr = "SELECT 
                DATE_FORMAT(A.dt_emissao_nf, '%d/%m/%Y') AS dt_emissao_nfbr,
                A.dt_emissao_nf, A.n_documento, A.valor as valor_bruto, A.tipo_nf,
                B.assunto,B.c_fantasia,A.status
        FROM saida AS A
        LEFT JOIN prestadorservico AS B ON (B.id_prestador = A.id_prestador)
        WHERE   A.id_regiao = '$regiao' AND A.id_projeto ='$projeto' AND A.tipo = 213 AND A.status IN (1,2) AND 
                A.mes_competencia = {$mes} AND A.ano_competencia = {$ano} 
        ORDER BY A.dt_emissao_nf";

    $qr_relatorio = mysql_query($qr) or die(mysql_error());
    $num_rows = mysql_num_rows($qr_relatorio);
    echo "<!-- $qr -->";
}
?>
<html>
    <head>
        <title>Relatório</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="../../net1.css" rel="stylesheet" type="text/css" >
        <script src="../../js/jquery-1.10.2.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript" ></script>

        <script>
            $(function() {
                $("#regiao").change(function() {
                    var $this = $(this);
                    if ($this.val() !== "-1") {
                        showLoading($this, "../../");
                        $.post('../../action.global.php', {regiao: $this.val()}, function(retorno) {
                            removeLoading();
                            $("#projeto").html(retorno);
                            $("#projeto").val($("#proSelected").val());
                        }, "html");
                    }
                }).trigger("change");
            });

        </script>
        <style media="print">
            fieldset{display: none;}
        </style>

    </head>
    <body class="novaintra" >        
        <div id="content">
            <div id="head">
                <img src="../../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;" />
                <div class="fleft">
                    <h2>Relatório de Notas Fiscais</h2>
                    <p></p>
                </div>
            </div>
            <br class="clear">

            <form  name="form" action="" method="post" id="form">
                <fieldset>
                    <legend>Relatório</legend>
                    <div class="fleft">
                        <input type="hidden" name="proSelected" id="proSelected" value="<?php echo $projetoSel ?>"/>
                        <p><label class="first">Região:</label> <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao')); ?> </p>
                        <p><label class="first">Projeto:</label> <?php echo montaSelect($optProjeto, $projetoSel, array('name' => "projeto", 'id' => 'projeto')); ?></p>
                        <p><label class="first">Competência:</label> <?php echo montaSelect($meses, $mesesSel, array('name' => "mes", 'id' => 'mes')); ?> <?php echo montaSelect($anoOpt, $anoSel, array('name' => "ano", 'id' => 'ano')); ?></p>
                    </div>

                    <br class="clear"/>

                    <p class="controls" style="margin-top: 10px;">
                        <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                        <input type="submit" name="gerar" value="Gerar" id="gerar"/>
                    </p>
                </fieldset>
            </form>
            <?php if (!empty($qr_relatorio) && isset($_POST['gerar'])) { 
                    if($num_rows > 0){ $count = 0;  ?>
                    <p id="excel" style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Relatório')" value="Exportar para Excel" class="exportarExcel"></p>
                    <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" class="grid" style="margin-top: 30px;">
                        <thead>
                            <tr style="font-size: 14px!important;">
                                <th>Emissão NF</th>
                                <th>Entrada da<br/>Mercadoria</th>
                                <th>Número NF</th>
                                <th>Descrição(Material/Serviço)</th>
                                <th>Nome do Fornecedor</th>
                                <th>Valor Bruto da NF</th>
                                <th>IRRF</th>
                                <th>ISS RF</th>
                                <th>PIS/COFINS/<br/>CSLL RF</th>                                    
                                <th>Valor Líquido da NF</th>
                                <th>Pago?</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php  while ($row_rel = mysql_fetch_assoc($qr_relatorio)) { ?>
                            <tr class="<?php echo ($count++ %2 == 0) ? "even":"odd"; ?>" style="height: 40px;">
                                <td align="center"><?php echo $row_rel['dt_emissao_nfbr'] ?></td>
                                <td><?php echo $row_rel[''] ?></td>
                                <td align="center"><?php echo $row_rel['n_documento'] ?></td>
                                <td><?php echo $row_rel['assunto'] ?></td>
                                <td><?php echo $row_rel['c_fantasia'] ?></td>
                                <td class="txright">R$ <?php echo number_format($row_rel['valor_bruto'], 2, ',', '.') ?></td>
                                <td class="txright">R$ 0,00</td>
                                <td class="txright">R$ 0,00</td> 
                                <td class="txright">R$ 0,00</td>
                                <td class="txright">R$ 0,00</td>
                                <td align="center"><?php echo ($row_rel['status']==1) ? "Não":"Sim"; ?></td>
                            </tr>
                        <?php } ?> 
                    </tbody>
                </table>
                <?php }else{ ?>
                    <br/>
                    <div id='message-box' class='message-yellow'>
                        <p>Nenhum resultado encontrado</p>
                    </div>
                <?php } ?> 
            <?php } ?> 
        </div>
    </body>
</html>