<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../../login.php?entre=true';</script>";
    exit;
}

include "../../conn.php";
include "../../funcoes.php";
include "../../classes/funcionario.php";
include "../../classes_permissoes/regioes.class.php";
include "../../wfunction.php";

function formato_real($valor,$qnt=2) {
	$valor_formatado = number_format($valor, $qnt, ',', '.');
	return $valor_formatado;

}

$usuario = carregaUsuario();

// Buscando a Folha
list($regiao, $id_folha) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));
$link_voltar = 'ver_folha.php?enc=' . $_REQUEST['enc'];

$sqlFolha = "SELECT A.*,DATE_FORMAT(A.data_inicio, '%d/%m/%Y') AS data_inicio_br, 
                    DATE_FORMAT(A.data_fim, '%d/%m/%Y') AS data_fim_br,
                    DATE_FORMAT(A.data_proc, '%d/%m/%Y') AS data_proc_br,
                    B.nome
                    FROM rh_folha AS A
                    LEFT JOIN funcionario AS B ON (A.user = B.id_funcionario)
                    WHERE A.id_folha = {$id_folha}";
$rs_folha = mysql_query($sqlFolha);
$row_folha = mysql_fetch_assoc($rs_folha);
$projeto = montaQueryFirst("projeto", "*", "id_projeto = {$row_folha['projeto']}");
$row_empresa = montaQueryFirst("rhempresa", "*", "id_projeto = {$row_folha['projeto']}");
$row_regiao = montaQueryFirst("regioes", "*", "id_regiao = {$row_folha['regiao']}");

$sql = "SELECT 	A.id_clt,A.id_regiao,A.mes,A.ano,A.nome,A.status_clt,A.cpf,A.sallimpo_real,A.valor_dt,
                A.rend,A.desco,A.inss,A.valor_dt,A.meses,A.dias_trab,A.a5020,A.a5035,A.inss_dt,A.inss_rescisao,
                A.a5021, A.a5036, A.ir_dt, A.ir_rescisao, A.a5022, A.salliquido, 
                B.cpf, B.conselho,
                TRIM(C.nome) as funcao
                FROM rh_folha_proc AS A
                LEFT JOIN rh_clt AS B ON (A.id_clt = B.id_clt)
                LEFT JOIN curso AS C ON (A.id_curso = C.id_curso)
                WHERE A.id_folha = {$id_folha} AND A.status = 3
                ORDER BY A.nome";
$result = mysql_query($sql) or die(mysql_error());

$c = 0;
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>:: Intranet :: Folha Finalizada de CLT (<?= $row_folha['id_folha'] ?>)</title>
        <link href="sintetica/folha.css" rel="stylesheet" type="text/css">
        <link href="../../favicon.ico" rel="shortcut icon">
        <link href="../../js/highslide.css" rel="stylesheet" type="text/css" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/highslide-with-html.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        <script type="text/javascript">
            hs.graphicsDir = '../../images-box/graphics/';
            hs.outlineType = 'rounded-white';
        </script>
        <style type="text/css">
            .highslide-html-content { width:600px; padding:0px; }
            .essatb{
                margin: 0px auto;
                text-align: left;
                width: 98%;
                font-size: 11px;
                line-height: 40px;
            }
        </style>
    </head>
    <body>
        <div id="corpo">

            <table cellspacing="4" cellpadding="0" id="topo">
                <tr height="30">
                    <td width="15%" rowspan="3" valign="middle" align="center">
                        <img src="../../imagens/logomaster<?php echo $projeto['id_master']; ?>.gif" width="110" height="79">
                    </td>
                    <td  style="font-size:12px;">
                        <b><?php echo $projeto['nome']." (". mesesArray($row_folha['mes'])." / ".$row_folha['ano'].")"; ?></b>               
                    </td>
                    <td colspan="2">  <b>CNPJ: </b><?php echo $row_empresa['cnpj']; ?></td>
                </tr>

                <tr>
                    <td width="35%"><b>Data da Folha:</b> <?= $row_folha['data_inicio_br'] . ' &agrave; ' . $row_folha['data_fim_br'] ?></td>
                    <td width="30%"><b>Região:</b> <?= $row_regiao['id_regiao'] . ' - ' . $row_regiao['regiao'] ?></td>
                    <td width="20%"><b>Participantes:</b> <?= $row_folha['clts'] ?></td>
                </tr>
                <tr>
                    <td><b>Data de Processamento:</b> <?= $row_folha['data_proc_br'] ?></td>
                    <td><b>Gerado por:</b> <?= $row_folha['nome'] ?></td>
                    <td><b>Folha:</b> <?= $id_folha ?></td>
                </tr>
                <tr>
                    <td colspan="3"></td>
                    <td><b>Total de rescindidos:</b> <?= $row_folha['total_rescindidos'] ?></td>
                </tr>
            </table>


            <table cellpadding="0" cellspacing="1" id="folha">
                <tr>
                    <td colspan="2">
                        <a href="<?= $link_voltar ?>" class="voltar">Voltar</a>
                    </td>
                    <td colspan="8">
                        <div style="float:right;">
                            <div class="legenda"><div class="nota entrada"></div>Admissão</div>
                            <div class="legenda"><div class="nota evento"></div>Licen&ccedil;a</div>
                            <div class="legenda"><div class="nota faltas"></div>Faltas</div>
                            <div class="legenda"><div class="nota ferias"></div>F&eacute;rias</div>
                            <div class="legenda"><div class="nota rescisao"></div>Rescis&atilde;o</div>
                        </div>
                    </td>
                </tr>
            </table>

            <!--p style="text-align: right;"><input type="button" onclick="tableToExcel('tabela', 'Folha Analitica')" value="Exportar para Excel" class="exportarExcel"></p-->
            <table cellpadding="0" cellspacing="1" id="tabela" class="essatb">
                <tr class="secao">
                    <td>COD</td>
                    <td style="padding-left:5px;">NOME</td>
                    <td>CPF</td>
                    <td>CONSELHO/ORGÃO</td>
                    <td>FUNÇÃO</td>
                    <td ><?php
                        if (isset($decimo_terceiro)) {
                            echo 'MESES';
                        } else {
                            echo 'DIAS';
                        }
                        ?></td>
                    <td>BASE</td>
                    <td>RENDIMENTOS</td>
                    <td>DESCONTOS</td>
                    <td>INSS</td>
                    <td>IRRF</td>
                    <td>FAM&Iacute;LIA</td>
                    <td>L&Iacute;QUIDO</td>
                </tr>
                <?php while ($row_participante = mysql_fetch_assoc($result)) {?>
                    <tr class="<?php echo ($c ++ % 2 == 0) ? 'linha_um' : 'linha_dois'; ?> ">
                        <td><?php echo $row_participante['id_clt'] ?></td>
                        <td align="left" width="300"><?php echo $row_participante['nome'] ?></td>
                        <td width="120"><?php echo $row_participante['cpf'] ?></td>
                        <td><?php echo $row_participante['conselho'] ?></td>
                        <td align="left" ><?php echo $row_participante['funcao'] ?></td>
                        <td width="20"><?php if ($row_participante['valor_dt'] != '0.00') {
                                    echo $row_participante['meses'];
                                } else {
                                    echo $row_participante['dias_trab'];
                                } ?></td>
                        <td><?= formato_real($row_participante['sallimpo_real'] + $row_participante['valor_dt']) ?></td>
                        <td><?= formato_real($row_participante['rend']) ?></td>
                        <td><?= formato_real($row_participante['desco']) ?></td>
                        <td ><?= formato_real($row_participante['a5020'] + $row_participante['a5035'] + $row_participante['inss_dt'] + $row_participante['inss_rescisao']) ?></td>
                        <td ><?= formato_real($row_participante['a5021'] + $row_participante['a5036'] + $row_participante['ir_dt'] + $row_participante['ir_rescisao']) ?></td>
                        <td ><?= formato_real($row_participante['a5022']) ?></td>
                        <td><?= formato_real($row_participante['salliquido']) ?></td>
                    </tr>
                <?php } ?>
                <tr class="totais">
                    <td colspan="6">
                        TOTAIS:
                    </td>
                    <td><?= formato_real($row_folha['total_limpo'] + $row_folha['valor_dt']) ?></td>
                    <td><?= formato_real($row_folha['rendi_indivi']) ?></td>
                    <td><?= formato_real($row_folha['descon_indivi']) ?></td>
                    <td><?= formato_real($row_folha['total_inss'] + $row_folha['inss_dt'] + $row_folha['inss_rescisao'] + $row_folha['inss_ferias']) ?></td>
                    <td><?= formato_real($row_folha['total_irrf'] + $row_folha['ir_dt'] + $row_folha['ir_rescisao'] + $row_folha['ir_ferias']) ?></td>
                    <td><?= formato_real($row_folha['total_familia']) ?></td>
                    <td><?= formato_real($row_folha['total_liqui']) ?></td>
                </tr>
            </table>
            <div class="clear"></div>
        </div>
    </body>
</html>