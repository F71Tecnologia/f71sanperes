<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a>";
    exit;
} else {

    include "../conn.php";

    $id_user = $_COOKIE['logado'];
    $result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
    $row_user = mysql_fetch_array($result_user);
    $result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
    $row_master = mysql_fetch_array($result_master);

    $projeto = $_REQUEST['pro'];
    $regiao = $_REQUEST['reg'];

    $result_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$projeto'");
    $row_projeto = mysql_fetch_array($result_projeto);

    $data_hoje = date('d/m/Y');

    $resultCom = mysql_query("SELECT A.nome,D.nome as funcao,D.salario,DATE_FORMAT(A.data_exame, '%d/%m/%Y') AS data
                                    FROM rh_clt AS A
                                    LEFT JOIN curso AS D ON (A.id_curso=D.id_curso)
                                    WHERE A.id_projeto = {$projeto} AND A.status IN (10, 20, 40, 50, 51, 52, 70, 90, 200, 110) AND (A.data_exame IS NOT NULL AND A.data_exame != '00/00/0000')
                                    GROUP BY A.id_clt
                                    ORDER BY A.nome") or die(mysql_error());

    $resultSem = mysql_query("SELECT A.nome,D.nome as funcao,D.salario,DATE_FORMAT(A.data_exame, '%d/%m/%Y') AS data
                                    FROM rh_clt AS A
                                    LEFT JOIN curso AS D ON (A.id_curso=D.id_curso)
                                    WHERE A.id_projeto = {$projeto} AND A.status IN (10, 20, 40, 50, 51, 52, 70, 90, 200, 110) AND (A.data_exame IS NULL OR A.data_exame = '00/00/0000')
                                    GROUP BY A.id_clt
                                    ORDER BY A.nome") or die(mysql_error());
    ?>
    <html>
        <head>
            <meta http-equiv=Content-Type content="text/html; charset=iso-8859-1">
            <title>Relat&oacute;rio de Participantes do Projeto</title>
            <link href="css/estrutura.css" rel="stylesheet" type="text/css">
        </head>
        <body style="background-color:#FFF; margin-top:30px; margin-bottom:30px;">
            <table cellspacing="0" cellpadding="0" class="relacao" style="width:970px; border:0px; page-break-after:always;">
                <tr> 
                    <td width="20%" align="center">
                        <img src='../imagens/logomaster<?= $row_user['id_master'] ?>.gif' alt="" width='120' height='86' />
                    </td>
                    <td width="80%" align="center" colspan="2">
                        <strong>RELAT&Oacute;RIO DE PARTICIPANTES DO PROJETO</strong><br>
                        <?= $row_master['razao'] ?>
                        <table width="500" border="0" align="center" cellpadding="4" cellspacing="1" style="font-size:12px;">
                            <tr style="color:#FFF;">
                                <td width="150" height="22" class="top">PROJETO</td>
                                <td width="150" class="top">REGIÃO</td>
                                <td width="200" class="top">TOTAL DE PARTICIPANTES</td>
                            </tr>
                            <tr style="color:#333; background-color:#efefef;">
                                <td height="20" align="center"><b><?= $row_projeto['nome'] ?></b></td>
                                <td align="center"><b><?= $row_projeto['regiao'] ?></b></td>
                                <td align="center"><b><?= $num ?></b></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr> 
                    <td colspan="3">
                        <div class="descricao">Relat&oacute;rio de funções e salários</div>
                        <h3>Funcionários que realizaram o Exame Admissional</h3>
                        <?php if (mysql_num_rows($resultCom) > 0) { ?>
                            <table class="relacao" width="100%" cellpadding="3" cellspacing="1">
                                <tr class="secao">
                                    <td width="32%">Nome</td>  
                                    <td width="36%">Função</td>  
                                    <td width="22%">Data do Exame</td>            
                                </tr>
                                <?php 
                                $cor = 0;
                                while ($row = mysql_fetch_array($resultCom)) { 
                                ?>
                                    <tr bgcolor="<?php echo ($cor++ % 2 == 0)? "#FAFAFA":"#F3F3F3"; ?>" style="font-weight:normal; padding:4px;">
                                        <td><?= $row['nome'] ?></td>      
                                        <td><?php echo $row['funcao']; ?></td>
                                        <td><?php echo $row['data']; ?></td>
                                    </tr>
                                <?php } ?>
                                <tr class="secao">
                                    <td colspan="10" align="center">TOTAL DE PARTICIPANTES: <?php echo mysql_num_rows($resultCom); ?></td>
                                </tr>
                            </table>
                        <?php } ?>
                        
                        <br/>
                        <br/>
                        <br/>
                        
                        <h3>Funcionários que <u>FALTAM</u> realizar o Exame Admissional</h3>
                        <?php if (mysql_num_rows($resultSem) > 0) { ?>
                            <table class="relacao" width="100%" cellpadding="3" cellspacing="1">
                                <tr class="secao">
                                    <td width="32%">Nome</td>  
                                    <td width="36%">Função</td>  
                                </tr>
                                <?php 
                                $cor = 0;
                                while ($row = mysql_fetch_array($resultSem)) { 
                                ?>
                                    <tr bgcolor="<?php echo ($cor++ % 2 == 0)? "#FAFAFA":"#F3F3F3"; ?>" style="font-weight:normal; padding:4px;">
                                        <td><?= $row['nome'] ?></td>      
                                        <td><?php echo $row['funcao']; ?></td>
                                    </tr>
                                <?php } ?>
                                <tr class="secao">
                                    <td colspan="10" align="center">TOTAL DE PARTICIPANTES: <?php echo mysql_num_rows($resultSem); ?></td>
                                </tr>
                            </table>
                        <?php } ?>
                        
                    </td>
                </tr>
            </table>
        </body>
    </html>
<?php } ?>