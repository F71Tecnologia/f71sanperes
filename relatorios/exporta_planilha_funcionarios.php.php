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


function formataCbo($cbo) {
    $cbo = RemoveEspacos(RemoveCaracteresGeral($cbo));
    return sprintf("%07s", substr($cbo, 0, 3) . '-' . substr($cbo, 4, 5));
}

if (isset($_REQUEST['exportar'])) {
    $cont = 0;
    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    
    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto' => $id_projeto));

    header("Content-type: application/vnd.ms-excel");
    header("Content-type: application/force-download");
    header("Content-Disposition: attachment; filename=planilha_funcionario.xls");
    header("Pragma: no-cache");
    
    $str_qr_relatorio = "SELECT A.matricula, H.nome AS locacao, A.nome, A.cpf, G.area_nome, B.nome AS nome_curso,  
                            DATE_FORMAT(A.data_nasci, '%d/%m/%Y') AS data_nascibr, 
                            DATE_FORMAT(A.data_entrada, '%d/%m/%Y') AS data_entradabr, 
                            DATE_FORMAT(A.data_exame, '%d/%m/%Y') AS data_examebr,
                            A.sexo, C.cod AS cbo, 
                            if(A.`status` = 10, 'N','S') AS afastado, 
                            if(A.`status` = 10, '', D.especifica) AS motivoAfastamento,
                            A.pis, A.campo1 AS numero_ctps, A.serie_ctps, A.uf_ctps, 
                            if(A.deficiencia = '', 'NA', if(A.deficiencia = 6, 'BR', 'PDH')) AS brPdh, 
                            F.horas_semanais AS regRevezamento, F.folga,
                            A.rg, A.email, E.nome AS nomeDef, A.id_projeto
                            FROM rh_clt AS A
                            LEFT JOIN curso AS B ON (B.id_curso = A.id_curso)
                            LEFT JOIN rh_cbo AS C ON (C.id_cbo = B.cbo_codigo)
                            LEFT JOIN rhstatus AS D ON (D.codigo = A.`status`)
                            LEFT JOIN deficiencias AS E ON (E.id = A.deficiencia)
                            LEFT JOIN rh_horarios AS F ON (F.funcao = A.id_curso)
                            LEFT JOIN areas AS G ON (G.area_id = B.area_funcao)
                            LEFT JOIN projeto AS H ON (H.id_projeto = A.id_projeto)
                            WHERE A.id_regiao = '$id_regiao' AND A.tipo_contratacao = '2' AND (D.codigo < '60' OR (D.codigo > '66' AND D.codigo <> '81' AND D.codigo <> '101')) ";

    if ($id_projeto != '-1') {
        $str_qr_relatorio .= "AND A.id_projeto = '$id_projeto' ";
    }

    $str_qr_relatorio .= "ORDER BY A.id_projeto,A.nome";

    $qr_relatorio = mysql_query($str_qr_relatorio) or die(mysql_error());
}
?>
<html>
    <body >        
        <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" class="grid" width="100%" style="page-break-after:auto;"> 
            <thead>
                <tr>
                    <th>MATRICULA</th>
                    <th>EMPRESA/FILIAL</th>
                    <th>FUNCIONÁRIO</th>
                    <th>CPF</th>
                    <th>SETOR</th>
                    <th>FUNÇÃO</th>
                    <th>DATA DE NASCIMENTO</th>
                    <th>DATA DE ADMISSÃO</th>
                    <th>DATA DO ÚLTIMO EXAME</th>
                    <th>SEXO</th>
                    <th>CBO</th>
                    <th>AFASTADO?</th>
                    <th>MOTIVO DO AFASTAMENTO</th>
                    <th>NIT(PIS/PASEP)</th>
                    <th>CTPS/SÉRIE</th>
                    <th>BR/PDH</th>
                    <th>REGIME DE REVEZAMENTO</th>
                    <th>IDENTIDADE</th>
                    <th>EMAIL</th>
                    <th>DEFICIÊNCIA?</th>
                </tr>
            </thead>
            <tbody>
<?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) {
    $class = ($cont++ % 2 == 0) ? "even" : "odd"; ?>
                    <tr class="<?php echo $class ?>">
                        <td><?php echo RemoveCaracteresGeral($row_rel['matricula']); ?></td>
                        <td><?php echo $row_rel['locacao'] ?></td>
                        <td><?php echo RemoveCaracteresGeral(RemoveAcentos($row_rel['nome'])) ?></td>
                        <td><?php echo RemoveCaracteresGeral($row_rel['cpf']); ?></td>
                        <td><?php echo $row_rel['area_nome']; ?></td>
                        <td><?php echo $row_rel['nome_curso']; ?></td>
                        <td><?php echo $row_rel['data_nascibr']; ?></td>
                        <td><?php echo $row_rel['data_entradabr']; ?></td>
                        <td><?php echo $row_rel['data_examebr']; ?></td>
                        <td><?php echo $row_rel['sexo']; ?></td>
                        <td><?php echo formataCbo($row_rel['cbo']); ?></td>
                        <td><?php echo $row_rel['afastado']; ?></td>
                        <td><?php echo $row_rel['motivoAfastamento']; ?></td>
                        <td><?php echo $row_rel['pis']; ?></td>
                        <td><?php echo $row_rel['numero_ctps'] . '/' . $row_rel['serie_ctps'] . '-' . $row_rel['uf_ctps']; ?></td>
                        <td><?php echo $row_rel['brPdh']; ?></td>
                        <td><?php echo $row_rel['regRevezamento']; ?></td>
                        <td><?php echo $row_rel['rg']; ?></td>
                        <td><?php echo $row_rel['email']; ?></td>
                        <td><?php echo $row_rel['nomeDef']; ?></td>
                    </tr>                                
                <?php } ?>
            </tbody>
        </table>
    </form>
</body>
</html>