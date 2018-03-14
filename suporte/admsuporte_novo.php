<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
}

if (isset($_REQUEST['fechados'])) {
    print_r($_REQUEST['fechados']);
}

include("../conn.php");
include("../wfunction.php");
include("../classes/global.php");

$id_user = $_COOKIE['logado'];

if (empty($_REQUEST['tela'])) {
    $tela = '1';
} else {
    $tela = $_REQUEST['tela'];
}

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$global = new GlobalClass();

function suporte($status, $prioridade) {
    switch ($status) {
        case 1:
//            $imagem = '<img src="imgsuporte/aberto.png" alt="Aberto" title="Aberto" width="18" height="18" />';
            $imagem = '<div href="#" class="btn btn-primary btn-sm bt disabled" data-key="1">ABERTO</div>';
            break;
        case 2:
//            $imagem = '<img src="imgsuporte/respondido.png" alt="Respondido" title="Respondido" width="18" height="18" />';
            $imagem = '<a href="#" class="btn btn-info btn-sm bt disabled" data-key="2">RESPONDIDO</a>';
            break;
        case 3:
//            $imagem = '<img src="imgsuporte/replicado.png" alt="Replicado" title="Replicado" width="18" height="18" />';
            $imagem = '<a href="#" class="btn btn-warning btn-sm bt disabled" data-key="3">REPLICADO</a>';
            break;
        case 4:
//            $imagem = '<img src="imgsuporte/finalizado.png" alt="Fechado" title="Fechado" width="18" height="18" />';
            $imagem = '<a href="#" class="btn btn-danger btn-sm bt disabled" >FECHADO</a>';
            break;
    }

    switch ($prioridade) {
        case 1:
            $prioridade_cor = '#5cb85c';
            $prioridade_nome = 'Baixa';
            break;
        case 2:
            $prioridade_cor = '#f0ad4e';
            $prioridade_nome = 'Média';
            break;
        case 3:
            $prioridade_cor = '#ffc266';
            $prioridade_nome = 'Alta';
            break;
        case 4:
            $prioridade_cor = '#d9534f';
            $prioridade_nome = 'Urgente';
            break;
    }

    $retorno = array($prioridade_cor, $prioridade_nome, $imagem);
    return $retorno;
}

$tipos_suporte = array('1' => 'INFORMAÇÃO', '2' => 'RECLAMAÇÃO', '3' => 'INCLUSÃO', '4' => 'EXCLUSÃO', '5' => 'ERRO', '6' => 'SUGESTÃO', '7' => 'ALTERAÇÃO');

$breadcrumb_config = array("nivel" => "../", "key_btn" => "6", "area" => "Sistema", "id_form" => "form1", "ativo" => "suporte");
$breadcrumb_pages = array();

//$ObjFunc = new FuncionarioClass();
//$funcionarios = $ObjFunc->listFuncionariosAtivos();
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Suporte</title>
        <link href="../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <!--<link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">-->
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <!--<link href="../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">-->
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <!--<link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />-->
    </head>
    <body> 
        <?php include("../template/navbar_default.php"); ?>

        <div class="container">
            <div class="page-header box-sistema-header"><h2><span class="glyphicon glyphicon-phone"></span> - Sistema<small> - Suporte</small></h2></div>
            
            <div class="legenda margin_b20" role="tablelist">
                <?php if ($tela == '1') { ?>
                <div class="col-sm-4">
                    <select class="form-control" id="chamados" name="chamados">
                        <option value="1">ABERTO</option>
                        <option value="2">RESPONDIDO</option>
                        <option value="3">REPLICADO</option>
                        <!--<option value="4">FECHADO</option>-->
                        <!--<option value="0">TODOS OS CHAMADOS FECHADOS</option>--> 
                   </select>
                </div>
                <div class="col-sm-2">
                    <a href="suporte_fechados.php" class="btn btn-danger btn btn-sm" target="_blank">Chamados Fechados</a>
                </div>
                    <?php
//                       if(isset($_GET['dev'])){
                        $total_aberto = 0;
                        $total_respondido = 0;
                        $total_replicado = 0;
                        $total_fechado = 0;
                        $sql_sup = "SELECT * FROM suporte";
                        $query_suporte = mysql_query($sql_sup);
                        while($row_suporte = mysql_fetch_array($query_suporte)){
                            if($row_suporte['status']==1){
                                $total_aberto++;
                            }
                            if($row_suporte['status']==2){
                                $total_respondido++;
                            }
                            if($row_suporte['status']==3){
                                $total_replicado++;
                            }
                            if($row_suporte['status']==4){
                                $total_fechado++;
                            }
                        } 
                       ?>
                       <div class="margin_t10">
                            <div class="bt btn btn-default btn-xs"><a href="javascript:;">Total abertos: <span class="badge"><?= $total_aberto; ?> </span></a></div>
                            <div class="bt btn btn-default btn-xs"><a href="javascript:;">Total respondidos: <span class="badge"> <?= $total_respondido; ?> </span></a></div>
                            <div class="bt btn btn-default btn-xs"><a href="javascript:;">Total replicados: <span class="badge"><?= $total_replicado; ?> </span></a></div>
                            <div class="bt btn btn-default btn-xs"><a href="javascript:;">Total fechados: <span class="badge"> <?= $total_fechado; ?> </span></a></div>
                       </div> 
                <?php  } ?>
            </div>
            
                <div class="panel panel-default">
                    <div class="panel-heading">Painel de Suporte</div>
                        <div class="panel-body">
                          <?php if ($tela == '1') { //LISTAGEM DE CHAMADOS?>
                            
                     <table class="table table-bordered text-sm valign-middle j_mostrar" >

                    <?php
                    foreach ($tipos_suporte as $tipo_id => $tipo_nome) {

                        if ($tipo_id == 8) {
                            $filtro1 = "WHERE sup.status = '4'";
                            $filtro2 = NULL;
                            $filtro3 = "ORDER BY sup.id_suporte DESC";
                        } else {
                            $filtro1 = "WHERE sup.tipo    = '$tipo_id'";
                            $filtro2 = "AND   sup.status != '4'";
                            $filtro3 = "ORDER BY sup.prioridade DESC, sup.id_suporte DESC";
                        }

                        $qr_suporte = mysql_query("SELECT sup.id_suporte, sup.assunto, sup.prioridade, sup.status, func.nome1, reg.regiao, date_format(sup.data_cad, '%d/%m/%Y às %H:%i') AS data_cad
							  	   FROM suporte sup
						     INNER JOIN funcionario func ON func.id_funcionario = sup.user_cad
						      LEFT JOIN regioes reg      ON reg.id_regiao = sup.id_regiao
							            $filtro1
							            $filtro2
						                $filtro3");
                        $total_suporte = mysql_num_rows($qr_suporte);


                        if (!empty($total_suporte)) {
                            ?>
                            <tr> 
                                <td colspan="7"> <?= $tipo_nome ?> (<?= $total_suporte ?> chamados)  </td>
                            </tr>
                            <tr>
                                <td >Chamado</td>
                                <td >Assunto</td>
                                <td >Aberto Por</td>
                                <td >Data de Abertura</td>
                                <td >Prioridade</td>
                                <td >Situação</td>
                            </tr>

                            <?php
                            while ($row_suporte = mysql_fetch_array($qr_suporte)) {

                                $suporte = suporte($row_suporte['status'], $row_suporte['prioridade']);
                                ?>

                                <tr class="mutacao linha_<?php
                                if ($linha++ % 2 == 0) {
                                    echo 'um';
                                } else {
                                    echo 'dois';
                                }
                                ?>" data-type="<?= $row_suporte['status'] ?>">
                                    <td ><a href="admsuporte.php?tela=2&chamado=<?= $row_suporte['id_suporte'] ?>" title="Abrir o chamado"><?= sprintf('%04d', $row_suporte['id_suporte']) ?></a></td>
                                    <td><?= $row_suporte['assunto'] ?></td>
                                    <td><?= $row_suporte['nome1'] ?></td>
                                    <td><?= $row_suporte['data_cad'] ?></td>
                                    <td style="background-color:<?= $suporte[0] ?>;"><?= $suporte[1] ?></td>
                                    <td><?= $suporte[2] ?></td>
                                </tr>

                                <?php
                            }
                        }
                    }
                    ?>

                </table>
                
                <?php } elseif ($tela == '2') {
        
                    //VISUALIZAR CHAMADO
                $user_cad = $_COOKIE['logado'];
                $chamado = $_REQUEST['chamado'];
                
                $qr_suporte = mysql_query("SELECT *, date_format(data_cad, '%d/%m/%Y às %H:%i') AS data_cad FROM suporte WHERE id_suporte = '$chamado'");
                $row_suporte = mysql_fetch_array($qr_suporte);

                $qr_funcionario = mysql_query("SELECT nome1 FROM funcionario WHERE id_funcionario = '$row_suporte[user_cad]'");
                $funcionario = mysql_result($qr_funcionario, 0);

                $data_cad = date('Y-m-d H:i:s');

                $ocorrencia = $tipos_suporte[$row_suporte['tipo']];
                
                switch ($row_suporte['prioridade']) {
                    case 1:
                        $prioridade_cor = '#FC9';
                        $prioridade_nome = 'Baixa';
                        break;
                    case 2:
                        $prioridade_cor = '#FC6';
                        $prioridade_nome = 'Média';
                        break;
                    case 3:
                        $prioridade_cor = '#F90';
                        $prioridade_nome = 'Alta';
                        break;
                    case 4:
                        $prioridade_cor = '#F30';
                        $prioridade_nome = 'Urgente';
                        break;
                }
                
                switch ($row_suporte['exibicao']) {
                    case 1:
                        $exibicao = 'Particular';
                        break;
                    case 2:
                        $exibicao = 'Todos do Grupo';
                        break;
                    case 3:
                        $exibicao = 'Pública';
                        break;
                }
                
                 if (!empty($row_suporte['arquivo'])) {
                    $img = '<a href="arquivos/suporte_' . $row_suporte['id_regiao'] . '_' . $row_suporte['0'] . $row_suporte['arquivo'] . '" rel="lightbox" title="Anexo">Abrir anexo</a>';
                } else {
                    $oculta_anexo = ' style="display:none;"';
                }

                if (!empty($row_suporte['quant'])) {
                    $nome_arquivo = 'historico_chamado_' . $chamado . '.txt';
                } else {
                    $oculta_historico = ' style="display:none;"';
                }
                ?>
                            
                <table class="table table-bordered text-sm valign-middle">
                    <tr>
                        <td width="15%" class="secao_resposta">Tipo de Ocorr&ecirc;ncia</td>
                        <td width="35%"><?= $ocorrencia ?></td>
                        <td width="15%" class="secao_resposta">Prioridade</td>
                        <td width="35%" style="background-color:<?= $prioridade_cor ?>;"><?= $prioridade_nome ?></td>
                    </tr>
                    
                    <tr>
                        <td class="secao_resposta">Data de Abertura</td>
                        <td><?= $row_suporte['data_cad'] ?></td>
                        <td class="secao_resposta">Aberto Por</td>
                        <td><?= $funcionario ?></td>
                    </tr>
                    
                    <tr>
                        <td class="secao_resposta">Assunto</td>
                        <td><?= $row_suporte['assunto'] ?></td>
                        <td class="secao_resposta">Exibi&ccedil;&atilde;o</td>
                        <td><?= $exibicao ?></td>
                    </tr>
                    
                    <tr>
                        <td class="secao_resposta">Mensagem</td>
                        <td colspan="3"><?= nl2br($row_suporte['mensagem']) ?></td>
                    </tr>
                    
                    <tr<?= $oculta_anexo ?>>
                        <td class="secao_resposta">Anexo</td>
                        <td colspan="3"><?= $img ?></td>
                    </tr>

                    <tr<?= $oculta_historico ?>>
                        <td class="secao_resposta">Histórico</td>
                        <td colspan="3">
                    <tr>
                        <td class="secao_resposta">Página de origem:</td>
                        <td colspan="3"><?= $row_suporte['suporte_pagina'] ?></td>
                    </tr>
                    
                    <?php
                    $filename = "/home/ispv/public_html/intranet/suporte/arquivos/$nome_arquivo";
                    $handle = fopen($filename, "r");
                    $conteudo = fread($handle, filesize($filename));
                    $conteudo = str_replace("\n\r", "<br>", $conteudo);
                    $conteudo = str_replace("\r", "<br>", $conteudo);
                    print $conteudo;
                    fclose($handle);
                    ?>
                    </td>
                    </tr>
                    
                    <?php if (!empty($row_suporte['resposta'])) { ?>
                        <tr>
                            <td colspan="4" class="secao_resposta" style="text-align:left;">Resposta</td>
                        </tr>
                        <tr>
                            <td colspan="4"><?= nl2br($row_suporte['resposta']); ?></td>
                        </tr>
                    <?php } if ($row_suporte['status'] != '2' and $row_suporte['status'] != '4') { ?>
                    <tr>
                        <td>
                            <form class="form-horizontal" action="admsuporte.php" method="post" id="form1">
                                <table class="table table-bordered text-sm valign-middle">
                                    <tr>
                                        <td colspan="4" class="secao_resposta" style="text-align:left;">Resposta</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4"><textarea name="resposta" cols="70" rows="7" class="linha" id="StMensagem" 
                                                                  onChange="this.value=this.value.toUpperCase()"></textarea></td>
                                    <tr style="display:none;">
                                        <td class="secao_resposta">Enviar Arquivo</td>
                                        <td colspan="3">
                                            <input name="StAnexo" type="file" class="linha" id="StAnexo" size="40" />                
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <input type="hidden" name="chamado" value="<?= $chamado ?>">
                                            <input type="hidden" name="tela" value="3">
                                            <input name="btnOK" type="submit" value="RESPONDER CHAMADO" />
                                        </td>
                                    </tr>
                                </table>
                            </form>
                        </td>
                    </tr>
                        
                         <?php } if ($row_suporte['status'] != '4') { ?>
                        <tr>
                            <td align="center" colspan="4">
                                <p>&nbsp;</p>
                                <img src="imgsuporte/finalizar.png" width="20" height="20" border="0"> <a href="suporte.php?tela=3&chamado=<?= $chamado ?>&regiao=<?= $row_suporte['id_regiao'] ?>" title="Fechar Chamado">FECHAR CHAMADO DEVIDO A FALTA DE INFORMA&Ccedil;&Otilde;ES OU SOLICITA&Ccedil;&Otilde;ES FORA DE CONTEXTO</a>
                            </td>
                        </tr>
                        <?php } ?>
                </table>
                            
                <?php
            } else {            
                
                $user_res = $_COOKIE['logado'];
                $chamado = $_REQUEST['chamado'];
                $resposta = $_REQUEST['resposta'];
                $data = date('Y-m-d');

                mysql_query("UPDATE suporte SET resposta = '$resposta', user_res = '$user_res', data_res = '$data', ultima_alteracao = '$data', status = '2' WHERE id_suporte = '$chamado'");
                ?>

                <script>
                    alert("Resposta enviada com sucesso!");
                    location.href = 'admsuporte.php';
                </script>

                 <?php } ?>    
                
                <div class="fechado" style="width: 89.7%; background: #FFF; margin: 0 auto; padding: 28px;">   
                    <table class="table table-bordered text-sm valign-middle j_mostrar" >           
                    
                        <?php
                foreach ($tipos_suporte as $tipo_id => $tipo_nome) {

                    if ($tipo_id == 8) {
                        $filtro1 = "WHERE sup.status = '4'";
                        $filtro2 = NULL;
                        $filtro3 = "ORDER BY sup.id_suporte DESC";
                    } else {
                        $filtro1 = "WHERE sup.tipo    = '$tipo_id'";
                        $filtro2 = "AND   sup.status = '4'";
                        $filtro3 = "ORDER BY sup.prioridade DESC, sup.id_suporte DESC";
                    }
                    
                    $sqlSuporte = "SELECT sup.id_suporte, sup.assunto, sup.prioridade, sup.status, func.nome1, reg.regiao, date_format(sup.data_cad, '%d/%m/%Y às %H:%i') AS data_cad
                                                                           FROM suporte sup
                                                             INNER JOIN funcionario func ON func.id_funcionario = sup.user_cad
                                                              LEFT JOIN regioes reg      ON reg.id_regiao = sup.id_regiao
                                                                            $filtro1
                                                                            $filtro2
                                                                        $filtro3";
                    
                    $qr_suporte = mysql_query($sqlSuporte);
                    $total_suporte = mysql_num_rows($qr_suporte);
                    

                    if (!empty($total_suporte)) {
                        ?>
                        <tr> 
                            <td colspan="7" class="secao_pai" > FECHADOS (<?= $total_suporte ?> chamados)  </td>
                        </tr>
                        <tr class="secao">
                            <td width="10%">Chamado</td>
                            <td width="30%">Assunto</td>
                            <td width="18%">Aberto Por</td>
                            <td width="18%">Data de Abertura</td>
                            <td width="12%">Prioridade</td>
                            <td width="12%">Situa&ccedil;&atilde;o</td>
                        </tr>

                        <?php
                        while ($row_suporte = mysql_fetch_array($qr_suporte)) {

                            $suporte = suporte($row_suporte['status'], $row_suporte['prioridade']);
                            ?>

                            <tr class="mutacao linha_<?php
                    if ($linha++ % 2 == 0) {
                        echo 'um';
                    } else {
                        echo 'dois';
                    }
                            ?>" data-type="<?= $row_suporte['status'] ?>">
                                <td ><a href="admsuporte.php?tela=2&chamado=<?= $row_suporte['id_suporte'] ?>" title="Abrir o chamado"><?= sprintf('%04d', $row_suporte['id_suporte']) ?></a></td>
                                <td><?= $row_suporte['assunto'] ?></td>
                                <td><?= $row_suporte['nome1'] ?></td>
                                <td><?= $row_suporte['data_cad'] ?></td>
                                <td style="background-color:<?= $suporte[0] ?>;"><?= $suporte[1] ?></td>
                                <td><?= $suporte[2] ?></td>
                            </tr>

                            <?php
                        }
                    }
                }
                ?>
                            
                </table>
                            
                    </div> <!--fechamento panel-body-->
                </div> <!--fechamento panel-body-->
            </div> <!--fechamneto panel-default-->
            
            <?php include("../template/footer.php"); ?>
        </div><!-- /.container -->

        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script src="../resources/dropzone/dropzone.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script src="../js/jquery.form.js" type="text/javascript"></script>
        <!--<script src="../resources/js/sistema/gestao_portal/index.js" type="text/javascript"></script>-->
        
        <script>
            jQuery(document).ready(function(){
                jQuery("#chamados").change(function(){
                    var status = jQuery(this).val();
                    if(status!="todos"){
                        jQuery(".mutacao").addClass("hidden");
                        jQuery(".mutacao[data-type="+status+"]").removeClass("hidden");
                    }else{
                        jQuery(".mutacao").removeClass("hidden");
                    }
                });
                
//                jQuery(".fechado").hide();                
//                //jQuery(".j_mostrar").hide();              
//                $(".fechado").attr({"target" : _blank})
//                jQuery(".j_visualizarFechado").click(function(){
//                    jQuery(this).text("Fechar Visualização");
//                    jQuery(".j_mostrar").show(1,function(){
//                        jQuery(".fechado").toggle(1, function(){
//                            jQuery("body").scrollTop(1350);
//                        }); 
//                        exit;
//                    });  
//                });                
            });
            
        </script>

    </body>
</html>