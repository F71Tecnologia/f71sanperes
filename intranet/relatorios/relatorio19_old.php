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

$usuario = carregaUsuario();
$optRegiao = getRegioes();
$ACOES = new Acoes();

$opt = array("2"=>"CLT","1"=>"Autônomo","3"=>"Cooperado","4"=>"Autônomo/PJ");

if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "getList") {
    $id_curso = $_REQUEST['id_curso'];
    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    
    $sql_clt = "SELECT nome
                FROM rh_clt
                WHERE id_curso = '{$id_curso}'
                AND id_regiao = '{$id_regiao}' 
                AND id_projeto = '{$id_projeto}'
                AND status < '60'
                ORDER BY nome";
    $result_clt = mysql_query($sql_clt);

    $html .= "<h3>CLT<h3>";
    $html .= '<table cellpadding="0" cellspacing="0" border="0" class="grid" width="100%">';
    $html .= "<thead>";
    $html .= "<tr>";
    $html .= "<th>NOME</th>";
    $html .= "</tr>";
    $html .= "</thead>";
    $html .= "<tbody>";
    while($row_clt = mysql_fetch_assoc($result_clt)) {
        $html .= '<tr>';
        $html .= "<td> {$row_clt['nome']} </td>";
        $html .= "</tr>";
    }
    $html .= "</tbody>";
    $html .= "</table>";

    $sql_autonomo = "SELECT nome 
                        FROM autonomo
                        WHERE id_curso = '{$id_curso}'
                        AND id_regiao = '{$id_regiao}' 
                        AND status = '1'
                        id_projeto = '{$id_projeto}'
                        ORDER BY nome";
    $result_autonomo = mysql_query($sql_autonomo);

    $html .= "<h3>AUTÔNOMO<h3>";
    $html .= '<table cellpadding="0" cellspacing="0" border="0" class="grid" width="100%">';
    $html .= "<thead>";
    $html .= "<tr>";
    $html .= "<th>NOME</th>";
    $html .= "</tr>";
    $html .= "</thead>";
    $html .= "<tbody>";
    while($row_autonomo = mysql_fetch_assoc($result_autonomo)) {
        $html .= '<tr>';
        $html .= "<td> {$row_autonomo['nome']} </td>";
        $html .= "</tr>";
    }
    $html .= "</tbody>";
    $html .= "</table>";

    echo utf8_encode($html);
    exit;
}

if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    $cont = 0;
    $arrayStatus = array(10, 40, 50, 51, 52);
    $status = implode(",", $arrayStatus);

    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto'=>$id_projeto));
    
    $str_qr_relatorio = "SELECT A.id_curso, A.nome, B.id_projeto, B.nome as nome_projeto
            FROM curso AS A
            LEFT JOIN projeto AS B
            ON B.id_projeto = A.campo3
            WHERE A.id_regiao = '$id_regiao' ";
    if(!isset($_REQUEST['todos_projetos'])) {
        $str_qr_relatorio .= "AND A.campo3 = '$id_projeto' ";
    }
    
    $str_qr_relatorio .= "ORDER BY A.nome ";
    
    $qr_relatorio = mysql_query($str_qr_relatorio) or die(mysql_error());
}

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$optSel = (isset($_REQUEST['tipo'])) ? $_REQUEST['tipo'] : null;

?>
<html>
    <head>
        <title>:: Intranet :: Relatório de Atividade por Lotação</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/global.js" type="text/javascript"></script>

        <script>
            $(function() {
                $(".bt-image").on("click", function() {
                    var id_curso = $(this).data("id_curso");
                    var projeto = $(this).data("projeto");
                    var regiao = $(this).data("regiao");
                    var nome = $(this).parents("tr").find("td:first").next().html();
                    thickBoxIframe(nome, "relatorio19.php", {id_curso: id_curso, projeto: projeto, regiao: regiao, method: "getList"}, "625-not", "500");
                });
            });
            $(function() {
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, null, "projeto");
            });
        </script>
        <style>
            .colEsq{
                width: auto;
                min-height: 0px;
                border-right: 0px;
                margin-right: 0px;
                float: none;
            }
            .bt-image{
                width: 18px;
                cursor: pointer;
            }
            h3 {text-align: center;}
        </style>
    </head>
    <body class="novaintra" >        
        <div id="content">
            <form  name="form" action="" method="post" id="form">
                <div id="head">
                    <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>Relatório de Atividade por Lotação</h2>
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
                        <?php ///permissão para VISUALIZAR TODOS OS PROJETOS AO MESMO TEMPO
                       if($ACOES->verifica_permissoes(85)) { ?>
                            <input type="submit" name="todos_projetos" value="Gerar de Todos Projetos" id="todos_projetos"/>
                      <?php } ?>
                            
                        <input type="submit" name="gerar" value="Gerar" id="gerar"/>
                    </p>
                </fieldset>

                <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])){ ?>
                <p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" value="Exportar para Excel" class="exportarExcel"></p>    
                <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" class="grid" width="100%" style="page-break-after:auto;"> 
                        <thead>
                            <tr>
                                <th>COD.</th>
                                <th>ATIVIDADE</th>
                                <th>QUANTIDADE</th>
                                <th>VISUALIZAR</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) { 
                        $sql_qtd_clt = "SELECT count(id_clt) FROM rh_clt WHERE id_curso = {$row_rel['id_curso']} AND id_regiao = '{$id_regiao}' AND id_projeto = '{$row_rel['id_projeto']}' AND status < '60' ";
                        $result_qtd_clt = mysql_query($sql_qtd_clt);
                        $qtd_clt = mysql_fetch_row($result_qtd_clt);
                        $sql_qtd_autonomo = "SELECT count(id_autonomo) FROM autonomo WHERE id_curso = {$row_rel['id_curso']} AND id_regiao = '{$id_regiao}' AND id_projeto = '{$row_rel['id_projeto']}' AND status = '1' ";
                        $result_qtd_autonomo = mysql_query($sql_qtd_autonomo);
                        $qtd_autonomo = mysql_fetch_row($result_qtd_autonomo);
                        $total = $qtd_clt[0]+$qtd_autonomo[0];
                        if($total > 0) {
                        $class = ($cont++ % 2 == 0)?"even":"odd";
                        ?>
                            <tr class="<?php echo $class ?>">
                                <td><?php echo $row_rel['id_curso']; ?></td>
                                <td><?php echo $row_rel['nome']." - ".$row_rel['nome_projeto'] ?></td>
                                <td> <?php echo $total; ?></td>
                                <td class="center"><img src="../imagens/icones/icon-docview.gif" title="Visualizar Participantes" class="bt-image" data-id_curso="<?php echo $row_rel['id_curso']; ?>" data-projeto="<?php echo $row_rel['id_projeto']; ?>" data-regiao="<?php echo $id_regiao; ?>"/></td>
                            </tr>                                
                        <?php }
                        } ?>
                    </tbody>
                </table>
                <?php  } ?>
            </form>
        </div>
    </body>
</html>