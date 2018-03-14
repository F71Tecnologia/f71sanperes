<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include('../../funcoes.php');
include("../../wfunction.php");
include('../../classes_permissoes/acoes.class.php'); 
include("../../classes/FolhaClass.php");
if(!include_once(ROOT_CLASS.'RhClass.php')) die ('Não foi possível incluir '.ROOT_CLASS.'RhClass.php');

error_reporting(0);
$usuario = carregaUsuario();
$objFolha = new Folha();
$acoes = new Acoes();
$rh = new RhClass();
$rh->AddClassExt('Clt'); 

//PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);

//CRITÉRIA
$objCriteria = new stdClass();
$objCriteria->id_regiao = $usuario['id_regiao'];
$objCriteria->status = 2;
$optionsProjetos = getProjetos($usuario['id_regiao']);

/**
 * VARIÁVEIS 
 */
$folha          = $_REQUEST['folha'];
$mes            = $_REQUEST['mes'];
$ano            = $_REQUEST['ano'];
$inicioFolha    = $_REQUEST['inicioFolha'];
$finalFolha     = $_REQUEST['finalFolha'];

/**
 * SETs
 */

$objFolha->setFolha($folha);
$objFolha->setMesFolha($mes);
$objFolha->setAnoFolha($ano);
$objFolha->setInicioFolha($inicioFolha);
$objFolha->setFinalFolha($finalFolha);

/**
 * PARTICIPANTES
 */
$objFolha->participantesParaAtualizarNaFolha();
$dadosFolha = $objFolha->getFolhaById($folha,array("A.*, B.nome as nome_projeto, B.regiao,B.cnpj,C.nome as nome_funcionario,DATE_FORMAT(A.data_proc,'%d/%m/%Y') as data_proc_br,CONCAT(DATE_FORMAT(A.data_inicio,'%d/%m/%Y'),' à ',DATE_FORMAT(A.data_fim,'%d/%m/%Y')) as periodo"),1);

/**
 * O METODO QUE FAZ OS CALCULOS 
 * DO CLT NA FOLHA SÓ VAI SER CHAMADO 
 * SE HOUVER PARTICIPANTES COM ALGUMA ALTERAÇÃO
 * QUE POSSA AFETAR NO CALCULO DA FOLHA
 */
$objFolha->calculoFolha();

/**
 * DADOS DO PARTICIPANTE DEPOIS QUE OS 
 * CALCULOS FORAM REALIZADOS E ESTÃO
 * ATUALIZADO
 */
$participantes = $objFolha->getParticipantesPorFolha();

/**
 * RETORNA TODOS OS CLTs QUE 
 * SOFRERAM ALGUM TIPO DE 
 * ALTERAÇÃO
 */
$participantesParaAtualizar = $objFolha->getParticipantesParaAtualizar();

/**
 * ARRRAY DE LICENÇAS
 */
//$arrayLicencas = $objFolha->getStatusLicenca();

/*
 * ARRAY DE RESCISAO
 */
//$arrayRescisao = $objFolha->getStatusRescisao();


?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet ::</title>

        <link rel="shortcut icon" href="favicon.ico" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/add-ons.min.css" rel="stylesheet">
        <link href="../../jquery/thickbox/thickbox.css" type="text/css" media="screen" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">
        <link href="../../js/lightbox.css" type="text/css" media="screen"/>
        <link href="../../js/highslide.css" type="text/css"/>
        <link href="../../net1.css" rel="stylesheet" type="text/css"> 
        
        <style>
            .essatb table span{display: block; }
            .box-legenda{
                position: absolute;
                left: 0px;
                top: 0px;
                width: 100%; 
                height: 100%; 
            }
            .legenda-novo-clt{
                background: #f4b04f; 
                width: 5px; 
                height: 100%; 
                float: left;
            }

            .legenda-clt-licencas{
                background: #5ebd5e; 
                width: 5px; 
                height: 100%; 
                float: left;
            }
            
            .legenda-clt-faltas{
                background: #e66454; 
                width: 5px; 
                height: 100%; 
                float: left;
            }
            
            .legenda-clt-rescisao{
                background: #857198; 
                width: 5px; 
                height: 100%; 
                float: left;
            }
        </style>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>

        <div class="container-full">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="row padding-xs-vr">
                        <div class="col-xs-4 text-left">
                            <strong>FOLHA: </strong><?php echo $folha;  ?>
                        </div>
                        <div class="col-xs-4 text-left">
                            <strong></strong>
                        </div>
                        <div class="col-xs-4 text-right">
                            <strong>CNPJ: </strong><?php echo $dadosFolha['cnpj'];  ?>
                        </div>
                    </div>
                    <div class="row padding-xs-vr">
                        <div class="col-xs-4 text-left">
                            <strong>Região: </strong> <?php echo $dadosFolha['regiao'];  ?>
                        </div>
                        <div class="col-xs-4 text-center">
                            <strong>Data da Folha: </strong> <?php echo $dadosFolha['periodo'];  ?>
                        </div>
                        <div class="col-xs-4 text-right">
                            <strong>Participantes: </strong> <?php echo $dadosFolha['clts'];  ?>
                        </div>
                    </div>
                    <div class="row padding-xs-vr">
                        <div class="col-xs-4 text-left">
                            <strong>Data de Processamento: </strong> <?php echo $dadosFolha['data_proc_br'];  ?>
                        </div>
                        <div class="col-xs-4 text-center">
                            <strong>Gerado por: </strong> <?php echo $dadosFolha['nome_funcionario'];  ?>
                        </div>
                        <div class="col-xs-4 text-right">
                            <strong>Total de rescindidos: </strong>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="panel-body">
                    <div class="col-md-2 legenda" data-key="mostrar_todos">
                        <div class="tr-bg-active pointer"><span class="btn-label bg-dark-gray fa fa-file bordered"></span>Todos</div>
                    </div>
                    <div class="col-md-2 legenda" data-key="entrada">
                        <div class="tr-bg-active pointer "><span class="btn-label bg-warning fa fa-file bordered"></span>Admissão</div>
                    </div>
                    <div class="col-md-2 legenda" data-key="evento">
                        <div class="tr-bg-active pointer"><span class="btn-label bg-success fa fa-file bordered"></span>Licen&ccedil;a</div>
                    </div>
                    <div class="col-md-2 legenda" data-key="faltas">
                        <div class="tr-bg-active pointer"><span class="btn-label bg-danger fa fa-file bordered"></span>Faltas</div>
                    </div>
                    <div class="col-md-2 legenda" data-key="ferias">
                        <div class="tr-bg-active pointer"><span class="btn-label bg-info fa fa-file bordered"></span>F&eacute;rias</div>
                    </div>
                    <div class="col-md-2 legenda" data-key="rescisao">
                        <div class="tr-bg-active pointer"><span class="btn-label bg-pa-purple fa fa-file bordered"></span>Rescis&atilde;o</div>
                    </div>
                    <hr>
                    
                    <?php echo (count($participantesParaAtualizar) > 0) ? "Participantes Atualizados: " . count($participantesParaAtualizar):""; ?>
                    <ul>
                        <?php foreach ($participantesParaAtualizar as $keys => $dados){ ?>
                            <li><?php echo $keys ." - ". $dados['nome'] ." Em ". $dados['ultimaAtualizacao']; ?></li>
                        <?php } ?>
                    </ul>
                    <?php $participantesParaAtualizar = array(); ?>
                    
                    <table id="tabela" class="table table-bordered table-condensed table-hover text-sm essatb">
                        <thead>
                            <tr class="valign-middle bg-primary" >
                                <th class="text-center" >COD</th>
                                <th>NOME</th>
                                <th class="text-center">ADMISSÃO</th>
                                <th class="text-center">SALÁRIO</th>
                                <th class="text-center">DIAS</th>
                                <th class="text-center">BASE</th>
                                <th class="text-center">RENDIMENTOS</th>
                                <th class="text-center">DESCONTOS</th>
                                <th class="text-center">INSS</th>
                                <th class="text-center">IRRF</th>
                                <th class="text-center">FAMÍLIA</th>
                                <th class="text-center">LÍQUIDO</th>
                                <th class="text-center">AÇÕES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($participantes as $keys => $values){ ?>
                                <tr class="destaque valign-middle">
                                    <td class="text-right" width="70" style="position: relative;" data-status-clt="<?php echo $values['status_clt'] ?>" >
                                        <div class="box-legenda">
                                            <?php if($values['novo_em_folha'] == 1){ ?><div class="legenda-novo-clt"></div><?php } ?>
                                            <?php if($values['possui_faltas'] == 1){ ?><div class="legenda-clt-faltas"></div><?php } ?>
                                            <?php if(in_array($values['status_clt'],$objFolha->getArrayLicenca())){ ?><div class="legenda-clt-licencas"></div><?php } ?>
                                            <?php if(in_array($values['status_clt'],$objFolha->getArrayRescisao())){ ?><div class="legenda-clt-rescisao"></div><?php } ?>
                                        </div>
                                        <?php echo $values['id_clt']; ?>
                                    </td>
                                    <td class="text-left" width="330"  style="font-size: 11px;"><?php echo $values['nome']; ?></td>
                                    <td class="text-left" width="50"   style="font-size: 11px;"><?php echo $values['dataEntrada']; ?></td>
                                    <td class="text-left" width="90"  style="font-size: 11px;"><?php echo "R$ " . number_format($values['salbase'],2,',','.'); ?></td>
                                    <td class="text-center" width="15" style="font-size: 11px;"><?php echo $values['dias_trab']; ?></td>
                                    <td class="text-right" width="100" style="font-size: 11px;"><?php echo "R$ " . number_format($values['sallimpo'],2,',','.'); ?></td>
                                    <td class="text-right" width="90" style="font-size: 11px;"><?php echo "R$ " . number_format($values['rend'],2,',','.'); ?></td>
                                    <td class="text-right" width="90" style="font-size: 11px;"><?php echo "R$ " . number_format($values['desco'],2,',','.'); ?></td>
                                    <td class="text-right" width="90" style="font-size: 11px;"><p style="display: block; width: 100%; height: 5px;" data-toggle="tooltip" data-original-title="<?php echo $values['base_inss'] ." x ". $values['aliquota']; ?>"><?php echo "R$ " . number_format($values['inss'],2,',','.'); ?></p></td>
                                    <td class="text-right" width="90" style="font-size: 11px;"><p style="display: block; width: 100%; height: 5px;" data-toggle="tooltip" data-original-title="<?php if(!empty($values['imprenda']) && $values['imprenda'] != '0.00'){ echo "(((" . ($values['base_irrf'] + $values['valor_deducao_dep_ir_total'] + $values['inss']) . " - " . $values['inss'] .") - ". $values['valor_deducao_dep_ir_total'].") x " . $values['t_imprenda'] . ") - " . $values['d_imprenda']; } ?>"><?php echo "R$ " . number_format($values['imprenda'],2,',','.'); ?></p></td>
                                    <td class="text-right" width="90" style="font-size: 11px;"><?php echo "R$ " . number_format($values['salfamilia'],2,',','.'); ?></td>
                                    <td class="text-right" width="90" style="font-size: 11px;"><?php echo "R$ " . number_format($values['salliquido'],2,',','.'); ?></td>
                                    <td class="text-right" width="90" style="font-size: 11px;">
                                        <a href="javascript:;" class="visualizar-calculo" data-clt="<?php echo $values['id_clt']; ?>">
                                            <button type="button" class="btn btn-success btn-xs">
                                                <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                                            </button>
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                            <tr class="totais">
                                <td colspan="2"></td>
                                <td class="text-bold">TOTAIS:</td>
                                <td class="text-right text-bold"></td>
                                <td class="text-right text-bold"></td>
                                <td class="text-right text-bold"></td>
                                <td class="text-right text-bold"></td>
                                <td class="text-right text-bold"></td>
                                <td class="text-right text-bold"></td>
                                <td class="text-right text-bold"></td>
                                <td class="text-right text-bold"></td>
                                <td class="text-right text-bold"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="panel-footer">
                    <div class="col-xs-6 text-left no-padding-l">
                        <a href="../../rh/folha/ver_folha_analitica.php" class="btn btn-info">Folha Analitica</a>
                    </div>
                    <div class="col-xs-6 text-right no-padding-r">
                        <a href="../../rh/folha/ver_folha_analitica_1.php" class="btn btn-warning">Folha Analitica Detalhada</a>
                    </div>
                    <div class="clear"></div>
                </div>                
            </div>

            <footer>
                <div class="row">
                    <div class="page-header"></div>
                    <div class="pull-right"><a href="#top">Voltar ao topo</a></div>
                    <div class="col-lg-12">
                        <p>Pay All Fast 3.0</p>
                        <p>Todos os direitos reservados <a href="http://f71.com.br" rel="nofollow" target="_blank">F71 Sistemas</a>.</p>
                    </div>
                </div>
            </footer>
        </div>

        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../uploadfy/scripts/jquery.uploadify.v2.1.0.js" type="text/javascript"></script>
        <script src="../../uploadfy/scripts/swfobject.js" type="text/javascript"></script>
        <script src="../../jquery/priceFormat.js" type="text/javascript"></script>
        <script>
            $(function(){
                /**
                * ABRINDO MODAL DE CALCULOS 
                */
                $('body').on('click',".visualizar-calculo",function () {
                    console.log($(this));
                    var url = 'visualizar_calculos.php';
                    var id_clt = $(this).data('clt');
                    $.post(url,{id_clt:id_clt},function(data){
                        bootDialog(data,'||');
                        $("[data-toggle='tooltip']").tooltip(); 
                    });
                });
            });
        </script>
    </body>
</html>
