<?php
    if (empty($_COOKIE['logado'])) {
        print "<script>location.href = '../../login.php?entre=true';</script>";
        exit;
    }    
    include("../../conn.php");
    include("../../wfunction.php");
    include("../../empresa.php");
    
    $result = mysql_query("SELECT * FROM curso WHERE status = 1 ORDER BY id_regiao,nome , letra, numero");
    
    
    // Configurações header para forçar o download
    header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
    header ("Cache-Control: no-cache, must-revalidate");
    header ("Pragma: no-cache");
    header ("Content-type: application/x-msexcel");
    header ("Content-Disposition: attachment; filename=\"Funcoes.xls\"" );
    header ("Content-Description: PHP Generated Data" );
?>
    <table border="1" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
        <tr>
            <td style="text-align: left; background-color: #e8e8e8;">id_curos </td>
            <td style="text-align: left; background-color: #e8e8e8;">Nome</td>
            <td style="text-align: left; background-color: #e8e8e8;">id_regiao </td>
            <td style="text-align: left; background-color: #e8e8e8;">CBO</td>
            <td style="text-align: left; background-color: #e8e8e8;">Salário</td>
            <td style="text-align: left; background-color: #e8e8e8;">Carga horária</td>
    </tr>
        <?php
        while($row = mysql_fetch_array($result)){
        $letra = $row['letra'];
        $numero = $row['numero'];
        ?>
        <tr>
            <td style="text-align: center; font-weight: bold;"><?php echo $row['id_curso']; ?></td>
            <td><?php echo $row['nome']." ".$letra." ".$numero; ?></td>
            <td style="text-align: center; font-weight: bold;"><?php echo $row['id_regiao']; ?></td>
            <td><?php echo $row['cbo_nome']; ?></td>
            <td><?php echo $row['salario']; ?></td>
            <td style="text-align: center; font-weight: bold;"><?php echo $row['hora_semana']; ?></td>
        </tr>
        <?php } ?>
    </table>