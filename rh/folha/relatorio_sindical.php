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

$usuario = carregaUsuario();

// Buscando a Folha
list($regiao, $id_folha) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));
$link_voltar = 'ver_folha.php?enc='.$_REQUEST['enc'];

$rowFolha = montaQueryFirst("rh_folha", "mes,ano,ids_movimentos_estatisticas,projeto", "id_folha={$id_folha}");
$ids_folhaStatistica = $rowFolha['ids_movimentos_estatisticas'];

$rowProjeto = montaQueryFirst("projeto", "id_projeto,nome", "id_projeto={$rowFolha['projeto']}");

$sql = "SELECT A.id_clt,A.id_regiao,A.mes,A.ano,A.nome,A.status_clt,A.cpf,A.a5019,
            C.nome AS sindicato,C.cnpj,C.id_sindicato,A.salbase
            FROM rh_folha_proc AS A
            LEFT JOIN rh_clt AS B ON (A.id_clt = B.id_clt)
            LEFT JOIN rhsindicato AS C ON (B.rh_sindicato = C.id_sindicato)
            WHERE A.id_folha = {$id_folha} AND A.a5019 != 0 AND A.status = 3
                                        
        UNION

        SELECT A.id_clt,A.id_regiao,A.mes,A.ano,A.nome,A.status_clt,A.cpf, D.valor_movimento AS a5019,
              C.nome AS sindicato,C.cnpj,C.id_sindicato,A.salbase
              FROM rh_folha_proc AS A
              LEFT JOIN rh_clt AS B ON (A.id_clt = B.id_clt)
              LEFT JOIN rhsindicato AS C ON (A.id_sindicato = C.id_sindicato)
              LEFT JOIN rh_movimentos_clt AS D ON (D.id_clt = A.id_clt AND D.cod_movimento = 5019)
        WHERE A.id_folha = {$id_folha} AND A.status = 3 AND A.a5019 = 0 AND D.id_movimento IN 
                ({$ids_folhaStatistica})
        ORDER BY sindicato,nome";
                
$result = mysql_query($sql) or die(mysql_error());
echo "<!-- {$sql} -->";

$qr_to = "SELECT SUM(a5019) as total, SUM(salbase) AS total_base, id_sindicato FROM ( $sql ) AS temp GROUP BY cnpj";
$rsTo = mysql_query($qr_to);
$arTo = array();
$arToLiq = array();
while($row_to = mysql_fetch_assoc($rsTo)){
    $arTo[$row_to['id_sindicato']] = $row_to['total'];
    $arToLiq[$row_to['id_sindicato']] = $row_to['total_base'];
}
$sindOld = "";

$c = 0;
?>
<html>
    <head>
        <title>Relatório</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="../../net1.css" rel="stylesheet" type="text/css">
        <script src="../../jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
        <script src="../../jquery/jquery.tools.min.js" type="text/javascript" ></script>
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../resources/js/main.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
    </head>
    <body class="novaintra" >        
        <div id="content">
            <div id="head">
                <img src="../../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;" width="140" height="100"/>
                <div class="fleft">
                    <h2>Relatório Sindical da folha de pagamento - <?php echo $rowProjeto['nome'] ?></h2>
                    <p>Folha referente ao mes de <?php echo mesesArray($rowFolha['mes']). " de ". $rowFolha['ano'] ?> </p>
                </div>
            </div>
            <br class="clear">
            <br/>
                    <p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" value="Exportar para Excel" class="exportarExcel"> <a name="pdf" data-title="Relatório Sindical da Folha de Pagamento" data-id="tbRelatorio" id="pdf" value="Gerar PDF" style="cursor: pointer"><i class="fa fa-file-pdf-o"></i> Gerar PDF</a></p>    
                    

            <table border="0" cellpadding="0" cellspacing="0" class="grid" width="100%" id="tbRelatorio"> 
                <thead>
                    <tr>
                        <th>COD</th>
                        <th>CPF</th>
                        <th>Funcionário</th>
                        <th>Sindicato</th>
                        <th>CNPJ</th>
                        <th>Contribuíção</th>
                        <th>Salário Bruto</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($row = mysql_fetch_assoc($result)){ 
                    if($sindOld != $row['id_sindicato']){
                        if($c > 0){
                            echo "<tr class='subtitulo'><td colspan='5' class=\"txright\">Total</td><td>R$ ".number_format($arTo[$sindOld], 2, ",",".")."</td><td>R$ ".number_format($arToLiq[$sindOld], 2, ",",".")."</td></tr>";
                        }
                        $sindOld = $row['id_sindicato'];
                    }
                    ?>
                <tr class="<?php echo ($c ++ %2 == 0) ? 'even' : 'odd'; ?> ">
                    <td><?php echo $row['id_clt']?></td>
                    <td><?php echo $row['cpf']?></td>
                    <td><?php echo $row['nome']?></td>
                    <td><?php echo $row['sindicato']?></td>
                    <td><?php echo $row['cnpj']?></td>
                    <td>R$ <?php echo $row['a5019']?></td>
                    <td>R$ <?php echo $row['salbase']?></td>
                </tr>
                <?php }
                
                echo "<tr class='subtitulo'><td colspan='5' class=\"txright\">Total</td><td>R$ ".number_format($arTo[$sindOld], 2, ",",".")."</td><td>R$ ".number_format($arToLiq[$sindOld], 2, ",",".")."</td></tr>";
                
                ?>
                </tbody>
            </table>
            <div class="clear"></div>
        </div>
    </body>
</html>