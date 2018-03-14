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
        $str_qr_relatorio = "SELECT id_clt AS id, nome, pis
            FROM rh_clt
            WHERE id_regiao = '$id_regiao' AND status = '10' ";
    }
    else {
        $str_qr_relatorio = "SELECT id_autonomo AS id, nome, pis
            FROM autonomo
            WHERE id_regiao = '$id_regiao' AND tipo_contratacao = '$tipo_contratacao' AND status = '1' ";
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
        <title>:: Intranet :: Relatório de Documentos</title>
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
                        <h2>Relatório de Documentos</h2>
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
                    <?php if($optSel == 1) { ?>
                        <thead>
                            <tr>
                                <th>NOME</th>
                                <th>CONTRATO</th>
                                <th>DISTRATO</th>
                                <th>TV SORRINDO</th>
                                <th>DECLARAÇÃO DE RENDA</th>
                                <th>CERTIFICADO</th>
                                <th>2ª VIA DE CONTRATO</th>
                                <th>ENCAMINHAMENTO BANCÁRIO</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) { $class = ($cont++ % 2 == 0)?"even":"odd"; ?>
                            <tr class="<?php echo $class ?>">
                                <td><?php echo $row_rel['nome']; ?></td>
                                <?php $qr_docs_autonomo = mysql_query("SELECT * FROM rh_documentos WHERE tipo_contratacao = '1' ORDER BY id_doc ASC");
				       while($docs_autonomo = mysql_fetch_assoc($qr_docs_autonomo)) {
                                           $qr_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '{$docs_autonomo['id_doc']}' AND id_clt = '{$row_rel['id']}'");  $verifica = mysql_num_rows($qr_verifica);
					   if(!empty($verifica)) {
                                                $img = '<img src="../imagens/assinado.gif" width="15" height="17">';
                                            } else {
                                                $img = '<img src="../imagens/naoassinado.gif" width="15" height="17">';
                                            } ?>
                                
                                <td class="documento"><?=$img?></td>
                                <?php } ?>
                            </tr>                                
                        <?php } ?>
                    </tbody>
                    <?php } ?>
                    <?php if($optSel == 2) { ?>
                        <thead>
                            <tr>
                                <th>NOME</th>
                                <th>EXAME ADMISSIONAL</th>
                                <th>FICHA DE CADASTRO CLT</th>
                                <th>CONTRATO DE TRABALHO</th>
                                <th>TV SORRINDO</th>
                                <th>INSCRIÇÃO NO PIS</th>
                                <th>CARTA DE REFERÊNCIA</th>
                                <th>SUSPENSÃO</th>
                                <th>ADVERTÊNCIA</th>
                                <th>AVISO PRÉVIO</th>
                                <th>DISPENSA</th>
                                <th>EXAME DEMISSIONAL</th>
                                <th>SOLICITAÇÃO DE VALE TRANSPORTE</th>
                                <th>DISPENSA DE VALE TRANSPORTE</th>
                                <th>SOLICITAÇÃO DO SALÁRIO FAMÍLIA</th>
                                <th>FICHA DE CADASTRO DO SALÁRIO FAMÍLIA</th>
                                <th>DEMISSÃO</th>
                                <th>CONTRATO DE EXPERIÊNCIA</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) { $class = ($cont++ % 2 == 0)?"even":"odd"; ?>
                            <tr class="<?php echo $class ?>">
                                <td><?php echo $row_rel['nome']; ?></td>
                                <?php $qr_docs_clt = mysql_query("SELECT * FROM rh_documentos WHERE tipo_contratacao = '2' ORDER BY id_doc ASC");
				       while($docs_clt = mysql_fetch_assoc($qr_docs_clt)) {
					   $qr_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '$docs_clt[id_doc]' AND id_clt = '{$row_rel['id']}'");  $verifica = mysql_num_rows($qr_verifica);
					   if((!empty($row_rel['pis'])) and $docs_clt['documento'] == "Inscrição no PIS") {
						 $img = '<img src="../imagens/assinado.gif" width="15" height="17">';
					   } else {
						   if(!empty($verifica)) {
							$img = '<img src="../imagens/assinado.gif" width="15" height="17">';
						   } else {
                                                        $img = '<img src="../imagens/naoassinado.gif" width="15" height="17">';
						   }
					   } ?> 
                 <td class="documento"><?=$img?></td>
                 <?php } ?>
                            </tr>                                
                        <?php } ?>
                    </tbody>
                    <?php } ?>
                    <?php if($optSel == 3) { ?>
                        <thead>
                            <tr>
                                <th>NOME</th>
                                <th>TV SORRINDO</th>
                                <th>FICHA DE ADESÃO</th>
                                <th>FICHA DE QUOTAS</th>
                                <th>FICHA DE CADASTRO</th>
                                <th>DESLIGAMENTO</th>
                                <th>PIS</th>
                                <th>DEVOLUÇÃO DE QUOTAS</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) { $class = ($cont++ % 2 == 0)?"even":"odd"; ?>
                            <tr class="<?php echo $class ?>">
                                <td><?php echo $row_rel['nome']; ?></td>
                                <?php $qr_docs_cooperado = mysql_query("SELECT * FROM rh_documentos WHERE tipo_contratacao = '3' ORDER BY id_doc ASC");
				       while($docs_cooperado = mysql_fetch_assoc($qr_docs_cooperado)) {
					   $qr_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '$docs_cooperado[id_doc]' AND id_clt = '{$row_rel['id']}'");  $verifica = mysql_num_rows($qr_verifica);
					   if((!empty($row_rel['pis'])) and $docs_cooperado['documento'] == "PIS") {
						 $img = '<img src="../imagens/assinado.gif" width="15" height="17">';
					   } else {
						   if(!empty($verifica)) {
							 $img = '<img src="../imagens/assinado.gif" width="15" height="17">';
						   } else {
							 $img = '<img src="../imagens/naoassinado.gif" width="15" height="17">';
						   }
					   } ?> 
                                <td class="documento"><?=$img?></td>
                                <?php } ?>
                            </tr>                                
                        <?php } ?>
                    </tbody>
                    <?php } ?>
                    <?php if($optSel == 4) { ?>
                        <thead>
                            <tr>
                                <th>NOME</th>
                                <th>TV SORRINDO</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) { $class = ($cont++ % 2 == 0)?"even":"odd"; ?>
                            <tr class="<?php echo $class ?>">
                                <td><?php echo $row_rel['nome']; ?></td>
                                <?php $qr_docs_cooperado = mysql_query("SELECT * FROM rh_documentos WHERE tipo_contratacao = '4' ORDER BY id_doc ASC");
				       while($docs_cooperado = mysql_fetch_assoc($qr_docs_cooperado)) {
					   $qr_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '$docs_cooperado[id_doc]' AND id_clt = '{$row_rel['id']}'");  $verifica = mysql_num_rows($qr_verifica);
					   if((!empty($row_rel['pis'])) and $docs_cooperado['documento'] == "PIS") {
						 $img = '<img src="../imagens/assinado.gif" width="15" height="17">';
					   } else {
						   if(!empty($verifica)) {
							 $img = '<img src="../imagens/assinado.gif" width="15" height="17">';
						   } else {
							 $img = '<img src="../imagens/naoassinado.gif" width="15" height="17">';
						   }
					   } ?> 
                                <td class="documento"><?=$img?></td>
                                <?php } ?>
                            </tr>                                
                        <?php } ?>
                    </tbody>
                    <?php } ?>
                </table>
                <?php  } ?>
            </form>
        </div>
    </body>
</html>