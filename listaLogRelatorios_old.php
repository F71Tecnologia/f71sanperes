<!DOCTYPE html>
<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
} else {

    header("Content-Type:text/html; charset=ISO-8859-1", true);
    include('conn.php');
    include('classes/funcionario.php');
    include('wfunction.php');
    
    $usuario = carregaUsuario();

    ////////////////////////////////////////////////////////////////////////////
    /////////////////////// gravando log de relatorios /////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    if (isset($_REQUEST['par']) && $_REQUEST['par'] == TRUE) {
        $url = explode("?", $_REQUEST['url']);
        $date = date("Y-m-d H:i:s");
        $idUsuario = (isset($_REQUEST['id'])) ? $_REQUEST['id'] : $usuario['id_funcionario'];
        $query = "INSERT INTO relatorios_log (nome_arquivo,data_acesso,id_usuario) VALUES ('$url[0]','$date','$idUsuario');";
        echo $query;
        $result = mysql_query($query);
        echo ($result) ? TRUE : FALSE;
    } else {

        $Fun = new funcionario();
        $Fun->MostraUser(0);
        $Master = $Fun->id_master;
        $Id = $Fun->id_funcionario;

        $qr_funcionario = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$Id'");
        $funcionario = mysql_fetch_array($qr_funcionario);

        $projeto = $_REQUEST['projeto'];
        $regiao = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
        
        function relatorioNovo($data){
            $arr_date = explode('/', $data);
            $data = mktime(0,0,0,$arr_date[1],$arr_date[0],$arr_date[2]);
            $hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
            $qtd_dias = $hoje - $data;
            $qtd_dias = (int)floor( $qtd_dias / (60 * 60 * 24));
            return ($qtd_dias <= 14) ? '<span class="rel-novo">Novo!</span>' : '';
        }
        
        
        ?>
        <html>
            <head>
                <meta charset="ISO-8859-9">
                <title>:: Intranet :: Pelatórios de Logs</title>
                <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
                <link rel="shortcut icon" href="../favicon.ico" />
                <link href="net1.css" rel="stylesheet" type="text/css" />
                <link href="css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
                <link href="favicon.ico" rel="shortcut icon" />
                <script src="js/jquery-1.8.3.min.js" type="text/javascript"></script>
                <script src="js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
                <script src="js/global.js" type="text/javascript"></script>
                <style>
                    .linha_um{
                        background-color: #FAFAFA;
                    }
                    .linha_dois{
                        background-color: #F3F3F3;
                    }
                    .rel-novo{
                        display: inline-block;
                        background-color: #0099ff;
                        color: white;
                        padding: 2px 3px;
                        border: 1px solid #0088ee;
                        border-radius: 3px;
                        -webkit-border-radius: 3px;
                        -moz-border-radius: 3px;
                    }
                </style>
                <script type="text/javascript">
                    $(document).ready(function() {
                        $(".tb-relatorios a").click(function() {
                            var url = $(this).attr('href');
                            $.post('<?= $_SERVER['PHP_SELF'] ?>', {url: url, id:<?= $Id ?>, par: true}, function(data) {
                                if (data === true) {
                                    windows.open(url);
                                }
                            });
                        });
                    });

                </script>
            </head>
            <body class="novaintra" >        
                <div id="content">
                    <h2>Relatórios de Logs</h2>

                    <table width="100%" border="0" cellpadding="4" cellspacing="0" style="font-size:13px;" class="tb-relatorios">
                        <thead style="background-color: #dddddd;">
                        <th width="75%" style="text-align: left"><strong>NOME DO RELAT&Oacute;RIO</strong></th>
                        <th width="25%" align="center"><strong>GERAR DOCUMENTO</strong></th>
                        </thead>
                        <tbody>
                            <tr class="linha_<?= ($classe++ % 2 == 0) ? "um" : "dois"; ?>">
                                <td>Relatório de Log de Acesso aos Relatórios <?= relatorioNovo('11/06/2014') ?></td>
                                <td align="center"><a href='relatorios/relatorio_log_rel_acesso_1.php?reg=<?= $regiao ?>&pro=<?= $projeto ?>' target='_blank'> <img src='imagens/ver_relatorio.gif' /></a></td>
                            </tr>
                            <tr class="linha_<?= ($classe++ % 2 == 0) ? "um" : "dois"; ?>">
                                <td>Relatório de Quantidade de Acessos aos Relatórios <?= relatorioNovo('11/06/2014') ?></td>
                                <td align="center"><a href='relatorios/relatorio_log_rel_acesso_2.php?reg=<?= $regiao ?>&pro=<?= $projeto ?>' target='_blank'> <img src='imagens/ver_relatorio.gif' /></a></td>
                            </tr>
                            <tr class="linha_<?= ($classe++ % 2 == 0) ? "um" : "dois"; ?>">
                                <td>Relatório de Desprocessamento de Férias <?= relatorioNovo('30/06/2014') ?></td>
                                <td align="center"><a href='relatorios/relatorio_log_ferias_desprocessar.php?reg=<?= $regiao ?>&pro=<?= $projeto ?>' target='_blank'> <img src='imagens/ver_relatorio.gif' /></a></td>
                            </tr>
                            <tr class="linha_<?= ($classe++ % 2 == 0) ? "um" : "dois"; ?>">
                                <td>Relatório de Desprocessamento de Transferências <?= relatorioNovo('01/07/2014') ?></td>
                                <td align="center"><a href='relatorios/relatorio_log_transf_desprocessar.php?reg=<?= $regiao ?>&pro=<?= $projeto ?>' target='_blank'> <img src='imagens/ver_relatorio.gif' /></a></td>
                            </tr>
                            <tr class="linha_<?= ($classe++ % 2 == 0) ? "um" : "dois"; ?>">
                                <td>Relatório de Log Eventos <?= relatorioNovo('15/07/2014') ?></td>
                                <td align="center"><a href='relatorios/relatorio_log_eventos.php?reg=<?= $regiao ?>&pro=<?= $projeto ?>' target='_blank'> <img src='imagens/ver_relatorio.gif' /></a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </body>
        </html>
        <?php
    }
}
?>
