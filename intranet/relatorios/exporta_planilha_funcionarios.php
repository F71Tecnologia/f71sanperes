<?php
include "../conn.php";
include "../wfunction.php";

function formataCbo($cbo) {
    $cbo = RemoveEspacos(RemoveCaracteresGeral($cbo));
    return sprintf("%07s", substr($cbo, 0, 3) . '-' . substr($cbo, 4, 5));
}

$cont = 0;
$id_regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['projeto'];

$projeto = montaQueryFirst("projeto", "nome", array('id_projeto' => $id_projeto));



$str_qr_relatorio = "SELECT A.matricula, A.id_clt, H.nome AS locacao, A.nome, A.cpf, G.area_nome, B.nome AS nome_curso,  
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
                            LEFT JOIN rh_horarios AS F ON (F.funcao = A.rh_horario)
                            LEFT JOIN areas AS G ON (G.area_id = B.area_funcao)
                            LEFT JOIN projeto AS H ON (H.id_projeto = A.id_projeto)
                            WHERE A.id_regiao = '$id_regiao' AND A.tipo_contratacao = '2' AND (D.codigo < '60' OR (D.codigo > '66' AND D.codigo <> '81' AND D.codigo <> '101'))";

if ($id_projeto != '-1') {
    $str_qr_relatorio .= "AND A.id_projeto = '$id_projeto' ";
}

$str_qr_relatorio .= "ORDER BY A.id_projeto,A.nome";

$qr_relatorio = mysql_query($str_qr_relatorio) or die(mysql_error());

header("Content-type: application/vnd.ms-excel");
header("Content-type: application/force-download");
header("Content-Disposition: attachment; filename=planilha_funcionario.xls");
header("Pragma: no-cache");    
      
echo'  <table border=1> 
            <tr>
                <th>MATRICULA</th>
                <th>ID</th>
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
            </tr>';
             
 while ($row_rel = mysql_fetch_assoc($qr_relatorio)) {
     echo '<tr> 
            <td>'.RemoveCaracteresGeral($row_rel['matricula']).'</td>
            <td>'.RemoveCaracteresGeral($row_rel['id_clt']).'</td>
            <td>'.$row_rel['locacao'].'</td>
            <td>'.RemoveCaracteresGeral(RemoveAcentos($row_rel['nome'])).'</td>
            <td>'.RemoveCaracteresGeral($row_rel['cpf']).'</td>
            <td>'.$row_rel['area_nome'].'</td>
            <td>'.$row_rel['nome_curso'].'</td>
            <td>'.$row_rel['data_nascibr'].'</td>
            <td>'.$row_rel['data_entradabr'].'</td>
            <td>'.$row_rel['data_examebr'].'</td>
            <td>'.$row_rel['sexo'].'</td>
            <td>'.formataCbo($row_rel['cbo']).'</td>
            <td>'.$row_rel['afastado'].'</td>
            <td>'.$row_rel['motivoAfastamento'].'</td>
            <td>'.$row_rel['pis'].'</td>
            <td>'.$row_rel['numero_ctps'] . '/' . $row_rel['serie_ctps'] . '-' . $row_rel['uf_ctps'].'</td>
            <td>'.$row_rel['brPdh'].'</td>
            <td>'.$row_rel['regRevezamento'].'</td>
            <td>'.$row_rel['rg'].'</td>
            <td>'.$row_rel['email'].'</td>
            <td>'.$row_rel['nomeDef'].'</td>
        </tr>';                                
 }             
    echo '  </table>';
    unset($qr_relatorio);
?>