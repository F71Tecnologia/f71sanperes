<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include "../../conn.php";
include "../../classes/funcionario.php";
include '../../classes_permissoes/regioes.class.php';
include "../../wfunction.php";

$usuario = carregaUsuario();
$optRegiao = getRegioes();

if (isset($_REQUEST['gerar'])) {
    $cont = 0;
    $arrayStatus = array(10,20,30,40,50,51,52);
    $status = implode(",", $arrayStatus);

    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto'=>$id_projeto));
    $sql = "SELECT D.nome as unidade, A.nome, DATE_FORMAT(A.data_entrada, '%d/%m/%Y') as dt_admissao,  E.nome as funcao, F.especifica
                            FROM rh_clt as A
                            LEFT JOIN projeto as D ON (D.id_projeto = A.id_projeto)
                            INNER JOIN curso as E ON (E.id_curso = A.id_curso)
                            LEFT JOIN rhstatus AS F ON (F.codigo = A.status)
                            WHERE A.status IN($status)
                            AND A.id_regiao = '$id_regiao' AND A.id_projeto = '$id_projeto' ORDER BY A.nome";
    echo "<!-- {$sql} -->";
    $qr_relatorio = mysql_query($sql) or die(mysql_error());
}

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
?>
<html>
    <head>
        <title>:: Intranet :: Saídas com erro 404</title>
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
                        <h2>Saídas com erro 404</h2>
                    </div>
                </div>
                <br class="clear">
                <br/>


                <fieldset class="noprint">
                    <legend>Relatório</legend>
                    <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
                    <p><label class="first">Região:</label> <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao')); ?> </p>                        
                    <p><label class="first">Projeto:</label> <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto')); ?> </p>

                    <p class="controls" >
                        <input type="submit" name="gerar" value="Filtrar" id="filtrar"/>
                    </p>
                </fieldset>

                <?php if (!empty($qr_relatorio) && isset($_POST['gerar'])) { ?>
                <p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" value="Exportar para Excel" class="exportarExcel"></p>    
                <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" class="grid" width="100%" style="page-break-after:auto;"> 
                        <thead>
                            <tr>
                                <th colspan="4"><?php echo $projeto['nome'] ?></th>
                            </tr>
                            <tr>
                                <th>NOME</th>
                                <th>FUNÇÃO</th>
                                <th>STATUS</th>
                                <th>DATA DE ADMISSÃO</th>   
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) { $class = ($cont++ % 2 == 0)?"even":"odd"?>
                            <tr class="<?php echo $class ?>">
                                <td><?php echo $row_rel['nome'] ?></td>
                                <td> <?php echo $row_rel['funcao']; ?></td>
                                <td> <?php echo $row_rel['especifica']; ?></td>
                                <td align="center"><?php echo $row_rel['dt_admissao']; ?></td>                       
                            </tr>                                
                        <?php } ?>
                    </tbody>
                </table>
                <?php  } ?>
            </form>
        </div>
    </body>
</html>