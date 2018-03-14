<?php // session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/SaidaClass.php");
include("../../classes/global.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$saida = new Saida();
$global = new GlobalClass();

if(isset($_REQUEST['filtrar'])){
    $id_projeto = $_REQUEST['projeto'];
    $id_regiao = $usuario['id_regiao'];
    $filtro = true;
    $result = $saida->getValidacaoAnexo($id_regiao);
    $total = mysql_num_rows($result);    
}

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
if(isset($_REQUEST['projeto'])){
    $projetoR = $_REQUEST['projeto'];    
    $dataIni = $_REQUEST['data_ini'];
    $dataFim = $_REQUEST['data_fim'];
}

$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>"Relatório de Validação de Anexos");
$breadcrumb_pages = array("Principal" => "../index.php");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Relatório de Validação de Anexos</title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        
        <!--<div class="container-full over-x">-->
        <div class="container">
            <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro<small> - Relatório de Validação de Anexos</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form1" id="form1">
                
                <?php if(isset($_SESSION['regiao'])){ ?>
                <!--resposta de algum metodo realizado-->
                <div class="alert alert-<?php echo $_SESSION['MESSAGE_TYPE']; ?> msg_cadsuporte">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <p><?php echo $_SESSION['MESSAGE'];
                    session_destroy(); ?></p>
                </div>
                <?php } ?>
                
                <div class="panel panel-default">
                    <div class="panel-heading">Relatório de Validação de Anexos</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Projeto</label>
                            <div class="col-lg-3">
                                <?php echo montaSelect($global->carregaProjetosByRegiao($usuario['id_regiao']), $projetoR, "id='projeto' name='projeto' class='validate[required,custom[select]] form-control'"); ?>
                            </div>
                            <label for="select" class="col-lg-2 control-label text-sm">Visualizar Saídas de</label>
                            <div class="col-lg-4">
                                <div class="input-group">
                                    <input type="text" class="input form-control data validate[required]" name="data_ini" id="data_ini" placeholder="Data Inicial" value="<?php echo $dataIni; ?>">
                                    <span class="input-group-addon">até</span>
                                    <input type="text" class="input form-control data validate[required]" name="data_fim" id="data_fim" placeholder="Data Final" value="<?php echo $dataFim; ?>">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <input type="submit" name="filtrar" id="filt" value="Gerar" class="btn btn-primary" />
                        <input type="hidden" name="hide_regiao" id="hide_regiao" value="<?php echo $projetoR; ?>" />
                        <input type="hidden" name="pausa" id="pausa" value="<?php echo $_SESSION['pausa']; ?>" />
                        <input type="hidden" name="volta" id="volta" value="<?php echo $_SESSION['regiao_select']; ?>" />
                    </div>
                </div>
            </form>
            
            <?php
            if ($filtro) {
                if ($total > 0) {
            ?>
            
            <table class='table table-bordered table-hover table-striped table-condensed text-sm valign-middle'>
                <thead>
                    <tr class="bg-primary">
                        <th>Código Saída</th>
                        <th>Data de Recebimento</th>
                        <th>Nome do Crédito</th>
                        <th>Conta Debitada</th>
                        <th>Tipo de Saída</th>
                        <th>Grupo</th>
                        <th>Subgrupo</th>
                        <!--<th>Descrição</th>-->
                        <th>Cadastrada por</th>
                        <th>Pago por</th>
                        <!--<th>Valor Adicional</th>-->
                        <th>Valor Total</th>
                        <th>Anexos</th>
                        <th>Editar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysql_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $row['saida_id']; ?></td>
                        <td><?php echo $row['saida_datarecibemento']; ?></td>
                        <td><?php echo $row['saida_nome']; ?></td>
                        <td><?php echo "AG: " . $row['banco_agencia'] . " <br /> C: " . $row['banco_conta']; ?></td>
                        <td><?php echo $row['entradasaida_nome']; ?></td>
                        <td><?php echo $row['grupo_nome']; ?></td>
                        <td><?php echo $row['subgrupo_nome']; ?></td>
                        <!--<td><?php echo $row['saida_especifica']; ?></td>--> 
                        <td><?php echo $row['funcionario_nome']; ?></td>
                        <td><?php echo $row['funcionariopg_nome']; ?></td>
                        <!--<td><?php echo ($row['saida_adicional'] == "") ? "" : formataMoeda($row['saida_adicional']); ?></td>-->
                        <td><?php echo formataMoeda($row['saida_valor']); ?></td>
                        
                        <?php
                        $res_file = $saida->getSaidaFile($row['saida_id']);
                        $tot_file = mysql_num_rows($res_file);
                        $comprovante = '';
                        
                        while($row_file = mysql_fetch_assoc($res_file)){
                            $nome_arquivo = '';
                            $nome_arquivo = $row_file['id_saida_file'].'.'.$row_file['id_saida'].$row_file['tipo_saida_file'];
                            if(file_exists("../../comprovantes/$nome_arquivo")){
                                $comprovante .= '<a target="_blank" title="Comprovante" class="bt-image margin_r5" data-type="editar" data-key="'.$row['saida_id'].'" href="../../comprovantes/'.$nome_arquivo.'"><i class="fa fa-paperclip text-info"></i></a>';
                            } else {
                                $comprovante .= '<a target="_blank" title="Comprovante" class="bt-image margin_r5" data-type="editar" data-key="'.$row['saida_id'].'" href="../../comprovantes/'.$nome_arquivo.'"><i class="fa fa-paperclip text-danger"></i></a>';
                            }
                        }
                        
                        $res_file_pg = $saida->getSaidaFilePg($row['saida_id']);
                        $tot_file_pg = mysql_num_rows($res_file_pg);
                        $comprovante_pg = '';
                        
                        while($row_file_pg = mysql_fetch_assoc($res_file_pg)){
                            $nome_arquivo_pg = '';                            
                            $nome_arquivo_pg = $row_file_pg['id_pg'].'.'.$row_file_pg['id_saida'].'_pg'.$row_file_pg['tipo_pg'];
                            if(file_exists("../../comprovantes/$nome_arquivo_pg")){
                                $comprovante_pg .= '<a target="_blank" title="Comprovante de Pagamento" class="bt-image" data-type="editar" data-key="'.$row['saida_id'].'" href="../../comprovantes/'.$nome_arquivo_pg.'"><i class="fa fa-paperclip text-info"></i></a>';
                            } else {
                                $comprovante_pg .= '<a target="_blank" title="Comprovante de Pagamento" class="bt-image" data-type="editar" data-key="'.$row['saida_id'].'" href="../../comprovantes/'.$nome_arquivo_pg.'"><i class="fa fa-paperclip text-danger"></i></a>';
                            }
                        }
                        ?>
                        
                        <td class="text-center">                            
                            <?php 
                            if($tot_file > 0 OR $row['saida_comprovante'] == 1){
                                echo $comprovante; 
                            }
                            
                            if($tot_file_pg > 0){
                                echo $comprovante_pg; 
                            }
                            ?>
                        </td>
                        <td class="text-center">
                            <a href="javascript:;" class="btn btn-xs btn-warning"><i title="Editar" class="bt-image fa fa-pencil" data-type="editar" data-key="<?php echo $row['saida_id']; ?>"></i></a>
                        </td>
                    </tr>
                    
                    <?php
                    $valor_soma = str_replace(",",".",$row['saida_valor']);
                    $adicional = str_replace(",",".",$row['saida_adicional']);
                    
                    $valor_total1 = $valor_total1 + $valor_soma + $adicional;
                    ?>
                    
                    <?php } ?>
                </tbody>
            </table>
            
            <div class="alert alert-dismissable alert-warning col-lg-6 text-right pull-right">                
                TOTAL: <?php echo "{$dataIni} a {$dataFim}: <strong> " . formataMoeda($valor_total1) . "</strong>"; ?>
            </div>
            
            <div class="clear"></div>
            
            <?php } else { ?>
                <div class="alert alert-danger top30">
                    Nenhum registro encontrado
                </div>
            <?php }
            } ?>
            
            <?php include('../../template/footer.php'); ?>
        </div>
        
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script src="../../resources/dropzone/dropzone.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script>
            $(function() {                
                $("#form1").validationEngine({promptPosition : "topRight"});
                
                //datepicker
                var options = new Array();
                options['language'] = 'pt-BR';
                $('.datepicker').datepicker(options);
            });
        </script>
    </body>
</html>