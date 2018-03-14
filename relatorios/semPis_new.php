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

if(isset($_REQUEST['gravar'])) {
    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    $tipo_contratacao = $_REQUEST['tipo'];
    $id = $_REQUEST['id'];
    $pis = $_REQUEST['pis'];
    $data_pis = $_REQUEST['dataPis'];
    
    if($tipo_contratacao == 2) {
        for($cont2 = 0; !empty($pis[$cont2][0]) ;$cont2++) {
            $sql_update_pis = "UPDATE rh_clt
                                SET pis = '{$pis[$cont2]}', dada_pis = '{$data_pis[$cont2]}' 
                                WHERE id_clt = '{$id[$cont2]}' AND id_regiao = '{$id_regiao}' AND id_projeto = '{$id_projeto}' 
                                LIMIT 1 ";
            $qr_update_pis = mysql_query($sql_update_pis);
        }
    } else {
        for($cont2 = 0; !empty($pis[$cont2][0]) ;$cont2++) {
            $sql_update_pis = "UPDATE autonomo
                                SET pis = '{$pis[$cont2]}', dada_pis = '{$data_pis[$cont2]}'
                                WHERE id_autonomo = '{$id[$cont2]}' AND id_regiao = '{$id_regiao}' AND id_projeto = '{$id_projeto}' AND tipo_contratacao = '{$tipo_contratacao}' 
                                LIMIT 1 ";
            $qr_update_pis = mysql_query($sql_update_pis);
        }
    }
}

if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    $cont = 0;

    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    $tipo_contratacao = $_REQUEST['tipo'];
    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto'=>$id_projeto));
    $contratacao = ($tipo_contratacao == "2")? "clt" : "autonomo";
    
    if($tipo_contratacao == 2) {
        $str_qr_relatorio = "SELECT nome, date_format(dada_pis, '%d/%m/%Y') as data_pisbr, id_clt AS id, locacao
            FROM rh_clt
            WHERE id_regiao = '$id_regiao' AND status = '10' AND pis IN (0,'') ";
    }
    else {
        $str_qr_relatorio = "SELECT nome, date_format(dada_pis, '%d/%m/%Y') as data_pisbr, id_autonomo AS id, locacao
            FROM autonomo
            WHERE id_regiao = '$id_regiao' AND tipo_contratacao = '$tipo_contratacao' AND pis IN (0,'') AND status = '1' ";
    }
    if(!isset($_REQUEST['todos_projetos'])) {
        $str_qr_relatorio .= "AND id_projeto = '$id_projeto' ";
    }
    
    $str_qr_relatorio .= "ORDER BY nome";
    
    $qr_relatorio = mysql_query($str_qr_relatorio) or die(mysql_error());
}

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$optSel = (isset($_REQUEST['tipo'])) ? $_REQUEST['tipo'] : null;

?>
<html>
    <head>
        <title>:: Intranet :: RELATÓRIO DE PARTICIPANTES DO PROJETO SEM PIS</title>
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
                        <h2>RELATÓRIO DE PARTICIPANTES DO PROJETO SEM PIS</h2>
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
                        <p><label class="first">Tipo Contratação:</label> <?php echo montaSelect($opt, $optSel, array('name' => "tipo", 'id' => 'tipo')); ?> </p>
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
                                <th>NOME</th>
                                <th>PIS</th>
                                <th>DATA</th>
                                <th>UNIDADE</th>
                                <?php if($optSel == 1 || $optSel == 2) {?>
                                    <th>ARQUIVO</th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) { $class = ($cont++ % 2 == 0)?"even":"odd"; ?>
                            <tr class="<?php echo $class ?>">
                                <td><?php echo $row_rel['nome']; ?></td>
                                <input type="hidden" name="id[]" value="<?php echo $row_rel['id']; ?>"/>
                                <td><input type="text" name="pis[]"/></td>
                                <td><input type="date" name="dataPis[]"/></td>
                                <td><?php echo $row_rel['locacao']; ?></td>
                                <?php if($optSel == 1) {?>
                                <td>
                                    <a href="../rh/solicitacaoPisAut.php?pro=<?php echo $id_projeto ?>&id_reg=<?php echo $id_regiao; ?>&Aut=<?php echo $row_rel['id']; ?>" target="_blank">
                                        <img src="icones/icon-doc.gif"/>
                                    </a>
                                </td>
                                <?php } else if ($optSel == 2) {?>
                                <td>
                                    <a href="../rh/solicitacaopis.php?pro=<?php echo $id_projeto ?>&id_reg=<?php echo $id_regiao; ?>&clt=<?php echo $row_rel['id']; ?>" target="_blank">
                                        <img src="icones/icon-doc.gif"/>
                                    </a>
                                </td>
                                <?php } ?>
                            </tr>                                
                        <?php } ?>
                    </tbody>
                </table>
                <p class="controls" style="margin-top: 10px;">
                    <input type="submit" name="gravar" value="Gravar" id="Gravar"/>
                </p>
                <?php  } ?>
            </form>
        </div>
    </body>
</html>