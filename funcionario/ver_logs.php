<?php

include("../conn.php");
include("../wfunction.php");
include('../classes/global.php');

$funcionario = $_REQUEST['funcionario'];

$result_user = mysql_query("SELECT nome, nome1 FROM funcionario WHERE id_funcionario = '$funcionario'");
$row_user = mysql_fetch_array($result_user);

// Preparando Paginacao
$nav = "ver_logs.php?funcionario=" . $funcionario . "&pagina=%d%s";
$max_logs = 100;
$numero_pagina = 0;

if (isset($_GET['pagina'])) {
    $numero_pagina = $_GET['pagina'];
}
$start_log = $numero_pagina * $max_logs;

$qr_prelog = "SELECT *, date_format(horario, '%d/%m/%Y - %H:%i:%s')AS data FROM log WHERE id_user = '$funcionario' ORDER BY id_log DESC";
$qr_limit_log = sprintf("%s LIMIT %d, %d", $qr_prelog, $start_log, $max_logs);

$qr_log = mysql_query($qr_limit_log) or die(mysql_error());
$all_logs = mysql_query($qr_prelog);

$total_logs = mysql_num_rows($all_logs);
$total_paginas = ceil($total_logs / $max_logs) - 1;
//

$html = "<html>
          <head>
              <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
              <link href=\"../net1.css\" rel=\"stylesheet\" type=\"text/css\" />
              <link href=\"../css/cupertino/jquery-ui-1.9.2.custom.css\" rel=\"stylesheet\" type=\"text/css\" />
              <link href=\"../favicon.ico\" rel=\"shortcut icon\" />
              <script src=\"../js/jquery-1.8.3.min.js\" type=\"text/javascript\"></script>
              <script src=\"../js/jquery-ui-1.9.2.custom.min.js\" type=\"text/javascript\"></script>
              <script src=\"../js/global.js\" type=\"text/javascript\"></script>
          </head>
          <body class='novaintra'>
            <div id='content' style='margin: auto;'>
            <div class='fleft'>
                        <h2>Sistema - Gestor de Funcionários</h2>
                        <p>Log do Funcionário - {$row_user['nome']}</p>
                        <p><a style='font-size:11px;' href='../log/" . $funcionario . ".txt'>Ver o arquivo em txt</a><p>
                    </div>
                <table cellpadding='0' cellspacing='0' border='0' class='grid' width='100%'>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>DATA E HORA</th>
                            <th>REGIÃO</th>
                            <th>LOCAL</th>
                            <th>AÇÃO</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>";
                        $cnt = 0;
                        while ($log = mysql_fetch_assoc($qr_log)) {
                            $class = ($cnt++ % 2 == 0) ? "odd" : "even";
                            $result_reg = mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$log[id_regiao]'");
                            $row_reg = mysql_fetch_array($result_reg);
                            $html .= "<tr class='{$class}'>
                                        <td>{$log['id_log']}</td>
                                        <td align='center'>{$log['data']}</td>
                                        <td>".acentoMaiusculo($row_reg['regiao'])."</td>
                                        <td>".acentoMaiusculo($log['local'])."</td>
                                        <td>".acentoMaiusculo($log['acao'])."</td>
                                        <td align='center'>{$log['ip']}</td>
                                      </tr>";
                        }
               $html .="<tr>
                            <td colspan='6' align='right'>";
               echo $html;
                            // Paginação
               
                        if ($numero_pagina > 0) {
                            ?>
                            <a href="<?php printf($nav, $currentPage, 0, $string); ?>">&laquo; Primeira</a>&nbsp;
                        <?php }
                        if ($numero_pagina == 0) {
                            ?>
                            <span class="morto">&laquo; Primeira</span>&nbsp;
                        <?php }
                        if ($numero_pagina > 0) {
                            ?>
                            <a href="<?php printf($nav, $currentPage, max(0, $numero_pagina - 1), $string); ?>">&#8249; Anterior</a>&nbsp;
                        <?php }
                        if ($numero_pagina == 0) {
                            ?>
                            <span class="morto">&#8249; Anterior</span>&nbsp;
                        <?php }
                        if ($numero_pagina < $total_paginas) {
                            ?>
                            <a href="<?php printf($nav, $currentPage, min($total_paginas, $numero_pagina + 1), $string); ?>">Próxima &#8250;</a>&nbsp;
                        <?php }
                        if ($numero_pagina >= $total_paginas) {
                            ?>
                            <span class="morto">Próxima &#8250;</span>&nbsp;                   
                        <?php }
                        if ($numero_pagina < $total_paginas) {
                            ?>
                            <a href="<?php printf($nav, $currentPage, $total_paginas, $string); ?>">Última &raquo;</a>
                        <?php }
                        if ($numero_pagina >= $total_paginas) {
                            ?>
                            <span class="morto">Última &raquo;</span>
                        <?php
                        }
// Fim da Paginação
    print "     </td>
              </tr>
            </tbody>
        </table>
         <p class='controls'> 
                    <input type='button' name='voltar' id='voltar' value='Voltar' onclick='window.location = ".'"index.php"'." '/> 
                </p>
    </div>
</body>
</html>";

    
    
//print "<br><center><b><font color=#FFFFFF>Log do Funcionário $row_user[nome1] <a style='font-size:11px;' href='log/" . $row_user['id_funcionario'] . ".txt'>ver arquivo txt</a></font></b></center>


?>