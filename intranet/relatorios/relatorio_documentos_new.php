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

$opt = array("2"=>"CLT","1"=>"Aut�nomo","3"=>"Cooperado","4"=>"Aut�nomo/PJ");

if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "getList") {
    $id = $_REQUEST['id'];
    $contratacao = $_REQUEST['contratacao'];
    $html = "";
    
    if($contratacao == 2) {
        $sql_documento_anexados = "SELECT A.id_upload, A.arquivo 
                            FROM upload AS A
                            LEFT JOIN documento_clt_anexo AS B
                            ON B.id_upload = A.id_upload
                            WHERE B.id_clt = '{$id}'
                            AND B.anexo_status = 1
                            ORDER BY A.id_upload";
        $result_documento_anexados = mysql_query($sql_documento_anexados);
        
        $html .= "<h3>ANEXADOS<h3>";
        $html .= '<table cellpadding="0" cellspacing="0" border="0" class="grid" width="100%">';
        $html .= "<thead>";
        $html .= "<tr>";
        $html .= "<th>#</th>";
        $html .= "<th>DOCUMENTO</th>";
        $html .= "</tr>";
        $html .= "</thead>";
        $html .= "<tbody>";
        while($row_documento_anexados = mysql_fetch_assoc($result_documento_anexados)) {
            $html .= '<tr>';
            $html .= "<td> {$row_documento_anexados['id_upload']} </td>";
            $html .= "<td>{$row_documento_anexados['arquivo']}</td>";
            $html .= "</tr>";
        }
        $html .= "</tbody>";
        $html .= "</table>";
        
        $sql_documento_nao_anexados = "SELECT id_upload, arquivo 
                            FROM upload
                            WHERE id_upload NOT IN (SELECT id_upload FROM documento_clt_anexo WHERE id_clt = '{$id}')
                            ORDER BY id_upload";
        $result_documento_nao_anexados = mysql_query($sql_documento_nao_anexados);
        
        $html .= "<h3>N�O ANEXADOS<h3>";
        $html .= '<table cellpadding="0" cellspacing="0" border="0" class="grid" width="100%">';
        $html .= "<thead>";
        $html .= "<tr>";
        $html .= "<th>#</th>";
        $html .= "<th>DOCUMENTO</th>";
        $html .= "</tr>";
        $html .= "</thead>";
        $html .= "<tbody>";
        while($row_documento_nao_anexados = mysql_fetch_assoc($result_documento_nao_anexados)) {
            $html .= '<tr>';
            $html .= "<td> {$row_documento_nao_anexados['id_upload']} </td>";
            $html .= "<td>{$row_documento_nao_anexados['arquivo']}</td>";
            $html .= "</tr>";
        }
        $html .= "</tbody>";
        $html .= "</table>";
        
        echo utf8_encode($html);
        exit;
    } else {
        $sql_documento_anexados = "SELECT A.id_upload, A.arquivo 
                            FROM upload AS A
                            LEFT JOIN documento_autonomo_anexo AS B
                            ON B.id_upload = A.id_upload
                            WHERE B.id_autonomo = '{$id}'
                            AND B.anexo_status = 1
                            ORDER BY A.id_upload";
        $result_documento_anexados = mysql_query($sql_documento_anexados);
        
        $html .= "<h3>ANEXADOS<h3>";
        $html .= '<table cellpadding="0" cellspacing="0" border="0" class="grid" width="100%">';
        $html .= "<thead>";
        $html .= "<tr>";
        $html .= "<th>#</th>";
        $html .= "<th>DOCUMENTO</th>";
        $html .= "</tr>";
        $html .= "</thead>";
        $html .= "<tbody>";
        while($row_documento_anexados = mysql_fetch_assoc($result_documento_anexados)) {
            $html .= '<tr>';
            $html .= "<td> {$row_documento_anexados['id_upload']} </td>";
            $html .= "<td>{$row_documento_anexados['arquivo']}</td>";
            $html .= "</tr>";
        }
        $html .= "</tbody>";
        $html .= "</table>";
        
        $sql_documento_nao_anexados = "SELECT id_upload, arquivo 
                            FROM upload
                            WHERE id_upload NOT IN (SELECT id_upload FROM documento_autonomo_anexo WHERE id_autonomo = '{$id}')
                            AND id_upload NOT IN (6,7,8,11,12,13,14,15,16,17,18,19,22,23)
                            ORDER BY id_upload";
        $result_documento_nao_anexados = mysql_query($sql_documento_nao_anexados);
        
        $html .= "<h3>N�O ANEXADOS<h3>";
        $html .= '<table cellpadding="0" cellspacing="0" border="0" class="grid" width="100%">';
        $html .= "<thead>";
        $html .= "<tr>";
        $html .= "<th>#</th>";
        $html .= "<th>DOCUMENTO</th>";
        $html .= "</tr>";
        $html .= "</thead>";
        $html .= "<tbody>";
        while($row_documento_nao_anexados = mysql_fetch_assoc($result_documento_nao_anexados)) {
            $html .= '<tr>';
            $html .= "<td> {$row_documento_nao_anexados['id_upload']} </td>";
            $html .= "<td>{$row_documento_nao_anexados['arquivo']}</td>";
            $html .= "</tr>";
        }
        $html .= "</tbody>";
        $html .= "</table>";
        
        echo utf8_encode($html);
        exit;
    }
}

if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    $cont = 0;
    $arrayStatus = array(10, 40, 50, 51, 52);
    $status = implode(",", $arrayStatus);

    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    $tipo_contratacao = $_REQUEST['tipo'];
    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto'=>$id_projeto));
    $contratacao = ($tipo_contratacao == "2")? "clt" : "autonomo";
    
    if($tipo_contratacao == 2) {
        $str_qr_relatorio = "SELECT A.nome, A.id_curso, A.id_clt AS id, B.nome AS nome_curso, B.salario 
            FROM rh_clt AS A
            LEFT JOIN curso AS B
            ON B.id_curso = A.id_curso
            WHERE A.id_regiao = '$id_regiao' AND A.tipo_contratacao = '$tipo_contratacao' ";
    }
    else {
        $str_qr_relatorio = "SELECT A.nome,A.id_curso, A.id_autonomo AS id, B.nome AS nome_curso, B.salario
            FROM autonomo AS A
            INNER JOIN curso AS B
            ON B.id_curso = A.id_curso 
            WHERE A.id_regiao = '$id_regiao' AND A.tipo_contratacao = '$tipo_contratacao' ";
    }
    if(!isset($_REQUEST['todos_projetos'])) {
        $str_qr_relatorio .= "AND A.id_projeto = '$id_projeto' ";
    }
    
    $str_qr_relatorio .= "ORDER BY A.nome";
    
    $qr_relatorio = mysql_query($str_qr_relatorio) or die(mysql_error());
}

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$optSel = (isset($_REQUEST['tipo'])) ? $_REQUEST['tipo'] : null;

?>
<html>
    <head>
        <title>:: Intranet :: Sindicato de Participantes Ativos</title>
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
                    var id = $(this).data("id");
                    var contratacao = $(this).data("contratacao");
                    var nome = $(this).parents("tr").find("td:first").html();
                    thickBoxIframe(nome, "relatorio_documentos_new.php", {id: id, contratacao: contratacao, method: "getList"}, "625-not", "500");
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
                        <h2>Relat�rio de Sindicatos de Participantes Ativos</h2>
                    </div>
                </div>
                <br class="clear">
                <br/>


                <fieldset class="noprint">
                    <legend>Relat�rio</legend>
                    <div class="fleft">
                        <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
                        <p><label class="first">Regi�o:</label> <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao')); ?> </p>                        
                        <p><label class="first">Projeto:</label> <?php echo montaSelect(array("-1" => "� Selecione a Regi�o �"), $projetoSel, array('name' => "projeto", 'id' => 'projeto')); ?> </p>
                        <p><label class="first">Tipo Contrata��o:</label> <?php echo montaSelect($opt, $optSel, array('name' => "tipo", 'id' => 'tipo')); ?> </p>
                    </div>

                    <br class="clear"/>

                    <p class="controls" style="margin-top: 10px;">
                        <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo j� existente!'; ?></span>
                        <?php ///permiss�o para VISUALIZAR TODOS OS PROJETOS AO MESMO TEMPO
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
                                <th colspan="4"><?php echo $projeto['nome'] ?></th>
                            </tr>
                            <tr>
                                <th>NOME</th>
                                <th>FUN��O</th>
                                <th>SAL�RIO</th>   
                                <th>DOCUMENTOS</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) { $class = ($cont++ % 2 == 0)?"even":"odd"; ?>
                            <tr class="<?php echo $class ?>">
                                <td><?php echo $row_rel['nome'] ?></td>
                                <td> <?php echo $row_rel['nome_curso']; ?></td>
                                <td align="center"><?php echo number_format($row_rel['salario'],2,',','.'); ?></td>            
                                <td class="center"><img src="../imagens/icones/icon-docview.gif" title="Documentos" class="bt-image" data-id="<?php echo $row_rel['id']; ?>" data-contratacao="<?php echo $tipo_contratacao; ?>" /></td>
                            </tr>                                
                        <?php } ?>
                    </tbody>
                </table>
                <?php  } ?>
            </form>
        </div>
    </body>
</html>