<?php

if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

error_reporting(E_ALL);


if (isset($_REQUEST['download']) && !empty($_REQUEST['download'])) {
    $tipo = isset($_REQUEST['tipo']) ? $_REQUEST['tipo'] : $_REQUEST['tipo'];
    $file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'arquivos'.DIRECTORY_SEPARATOR.$tipo. DIRECTORY_SEPARATOR . $_REQUEST['download'];
    $name_file_download = isset($_REQUEST['name_file']) ? $_REQUEST['name_file'] : $_REQUEST['download'];
    
    header("Content-Type: application/save");
    header("Content-Length:" . filesize($file));
    header('Content-Disposition: attachment; filename="' . $name_file_download . '"');
    header("Content-Transfer-Encoding: binary");
    header('Expires: 0');
    header('Pragma: no-cache');
    $fp = fopen("$file", "r");
    fpassthru($fp);
    fclose($fp);
    exit();
}

include "../../conn.php";
include "../../classes/funcionario.php";
include '../../classes_permissoes/regioes.class.php';
include "../../wfunction.php";

$usuario = carregaUsuario();
$optRegiao = getRegioes();

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;

if (isset($_REQUEST['gerar'])) {

    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto'=>$id_projeto));
    $sql = "SELECT A.id_clt, D.nome as unidade, A.nome, DATE_FORMAT(A.data_entrada, '%d/%m/%Y') as dt_admissao,  E.nome as funcao, F.especifica
                            FROM rh_clt as A
                            LEFT JOIN projeto as D ON (D.id_projeto = A.id_projeto)
                            INNER JOIN curso as E ON (E.id_curso = A.id_curso)
                            LEFT JOIN rhstatus AS F ON (F.codigo = A.status)
                            WHERE A.status < 60
                            AND A.id_regiao = '$regiaoSel' AND A.id_projeto = '$projetoSel' ORDER BY A.nome";
    echo "<!-- {$sql} -->";
    $qr_relatorio = mysql_query($sql) or die(mysql_error());
}


?>
<html>
    <head>
        <title>:: Intranet :: PIS EM LOTE</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../favicon.ico" rel="shortcut icon" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>

        <script>
            $(function() {
                $('#regiao').ajaxGetJson("../../methods.php", {method: "carregaProjetos"}, null, "projeto");
            });
        </script>
    </head>
    <body class="novaintra" >        
        <div id="content">
            <form  name="form" action="" method="post" id="form">
                <div id="head">
                    <img src="../../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>PIS EM LOTE</h2>
                    </div>
                </div>
                <br class="clear">
                <br/>


                <fieldset class="noprint">
                    <legend>Relatório</legend>
                    <div class="fleft">
                        <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
                        <p><label class="first">Região:</label> <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao')); ?> </p>                        
                        <p><label class="first">Projeto:</label> <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto')); ?> </p>
                    </div>

                    <br class="clear"/>

                    <p class="controls" style="margin-top: 10px;">
                        <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                        <input type="submit" name="gerar" value="Gerar" id="gerar"/>
                    </p>
                </fieldset>

                <?php if (!empty($qr_relatorio) && isset($_POST['gerar'])) { ?>
                <p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" value="Exportar para Excel" class="exportarExcel"></p>    
                <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" class="grid" width="100%" style="page-break-after:auto;"> 
                        <thead>
                            <tr>
                                <th colspan="5"><?php echo $projeto['nome'] ?></th>
                            </tr>
                            <tr>
                                <th>ID</th>
                                <th>NOME</th>
                                <th>FUNÇÃO</th>
                                <th>STATUS</th>
                                <th>DATA DE ADMISSÃO</th>   
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) { $class = ($cont++ % 2 == 0)?"even":"odd"?>
                            <tr class="<?php echo $class ?>">
                                <td><?php echo $row_rel['id_clt'] ?></td>
                                <td><?php echo $row_rel['nome'] ?></td>
                                <td> <?php echo $row_rel['funcao']; ?></td>
                                <td> <?php echo $row_rel['especifica']; ?></td>
                                <td align="center"><?php echo $row_rel['dt_admissao']; ?></td>                       
                            </tr>                                
                        <?php } ?>
                    </tbody>
                </table>
                <div style="text-align: right; margin-top: 20px;">
                    <input type="button" name="gerar" value="Gerar Arquivo" id="gerar">
                </div>
                <?php  } ?>
            </form>
        </div>
    </body>
</html>
