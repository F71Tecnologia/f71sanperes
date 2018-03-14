<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/FolhaClass.php");
include("../../classes/RhClass.php");

$usuario = carregaUsuario();
$objFolha = new Folha();
$rh = new RhClass();
$rh->AddClassExt('Clt'); 
$rh->setDefault();


if (validate($_REQUEST['method'])) {
            
    /**
     * VERIFICANDO FOLHAS ABERTAS
     */
    if($_REQUEST['method'] == 'getFolhasFinalizadas') {

        $html = "<div class='page-header'>
                <h3>Folhas</h3>
            </div>";

        $ano = validatePost('ano');
        $projeto = validatePost('projeto');

        // Example usage:
        $obj = new stdClass;
        $obj->ano = $ano;
        $obj->id_projeto = $projeto;
        $obj->status = 3;

        $folhas = $objFolha->getListaFolhas($obj);

        if (count($folhas) > 0) {

            $html .= "<table class='table table-striped table-hover'>
                <thead>
                    <th>COD.</th>
                    <th>Projeto</th>
                    <th>Gerado Por</th>
                    <th>Mês</th>
                    <th>QNTD CLTs</th>
                    <th colspan='2'>Ações</th>
                </thead>
                <tbody>";

            foreach ($folhas as $k => $folha) {
                $dt = "";
                if ($folha['terceiro'] == 1) {
                    switch ($folha['tipo_terceiro']) {
                        case 1:
                            $dt = "13º 1p";
                            break;
                        case 2:
                            $dt = "13º 2p";
                            break;
                        case 3:
                            $dt = "13º Int.";
                            break;
                    }
                }

                $html .= "<tr>
                        <td>{$folha['id_folha']}</td>
                        <td>{$folha['nome_projeto']}</td>
                        <td>{$folha['criado_por']}</td>
                        <td>" . mesesArray($folha['mes']) . " {$dt}</td>
                        <td>{$folha['quant_clt']}</td>
                        <td><img src=\"../../imagens/icones/icon-view.gif\" title=\"Visualizar\" class=\"bt-image center-block\" data-type=\"ver\" data-key=\"{$folha['id_folha']}\"></td>
                        <td><img src=\"../../imagens/icones/icon-exclamation.gif\" title=\"Desprocessar\" class=\"bt-image center-block\" data-type=\"desproc\" data-key=\"{$folha['id_folha']}\"></td>
                      </tr>";
            }
            $html .= "</tbody></table>";
        } else {
            $html = "<div class='alert alert-dismissable alert-danger'>
                    <strong>OPS!</strong> Não foi encontrado nenhum resultado. Tente novamente!
                  </div>";
        }

        echo utf8_encode($html);
        exit();
    }
    
    /**
     * GERANDO FOLHA
     */
    if($_REQUEST['method'] == "gerarFolha"){
        $retorno = array("status" => 0);
        
        //TRATANDO AS VARIAVEIS E PASSANDO PARA O OBJETO
        $obj = new stdClass;
        $obj->setRegiao = $usuario['id_regiao'];
        $obj->setProjeto = validatePost('projeto');
        $obj->setMes = str_pad(validatePost('mes'), 2, 0, STR_PAD_LEFT);
        $obj->setAno = validatePost('ano');
        $obj->setDataInicio = validatePost('dataInicio');
        $obj->setTerceiro = validatePost('terceiro');
        $obj->setTipoDecimo = validatePost('tipoTerceiro');
        $obj->setDataUltimaAtualizacao = date("Y-m-d H:i:s");
        $cadFolha = $objFolha->criaFolha($obj, false);
        
        if($cadFolha['status']){
            
            /**
             * SETs
             */
            $objFolha->setFolha($cadFolha['ultimoId']);
            $objFolha->setMesFolha(validatePost('mes'));
            $objFolha->setAnoFolha(validatePost('ano'));
            $objFolha->setInicioFolha(date('Y-d-m', str_replace("/","-",strtotime(validatePost('dataInicio')))));
            $objFolha->setFinalFolha($objFolha->ultimoDiaDoMes(validatePost('mes'), validatePost('ano')));
            
            
            /**
             * setIdRegiao($usuario['id_regiao']) => REGIAO
             * setIdProjeto(validatePost('projeto')) => PROJETO
             * setAtivo(1) => TODOS OS CLTS EM ATIVIDADE NORMAL, FERIAS, EVENTOS E REINTEGRAÇÃO
             * setRescisaoMes(1) => RESCISAO NO MES 
             * select() => MONTA O SELECT 
             * db->getCollection => RETORNA UMA COLEÇÃO 
             */
            $rh->Clt->setDateRangeField('data_demi');
            $rh->Clt->setDateRangeFmt('Y-m-d');
            $rh->Clt->setDateRangeIni($objFolha->getInicioFolha());
            $rh->Clt->setDateRangeEnd($objFolha->getFinalFolha());
            
            $clt = $rh->Clt->setIdRegiao($usuario['id_regiao'])->setIdProjeto(validatePost('projeto'))->setAtivo(1)->getRescisaoAnoMes($objFolha->getAnoFolha().$objFolha->getMesFolha())->select()->db->getCollection('id_clt');
            echo $rh->Clt->db->getLastQuery();
            
            /**
             * INSERT DE PARTICIPANTES DA FOLHA
             */
            $cadParticipantes = $objFolha->insertParticipantesFolha($clt,validatePost('mes'),validatePost('ano'));
            if($cadParticipantes){
                /**
                 * ATUALIZA A QUANTIDADE DE PARTICIPANTES DA FOLHA
                 */
                $objFolha->atualizaQntClt();
                $retorno = array("status" => 1);
            }            
        }
        
        echo json_encode($retorno);
        exit();
    }
    
    
    /**
     * EXCLUIR FOLHA
     */
    if($_REQUEST['method'] == "excluirFolha"){
        $retorno = array("status" => 0);
        
            if($objFolha->desativaFolha($_REQUEST['folha'], $_REQUEST['projeto'])){
                $retorno = array("status" => 1);
            }
            
        echo json_encode($retorno);
        exit();
    }
    
}

//PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);

//CRITÉRIA
$objCriteria = new stdClass();
$objCriteria->id_regiao = $usuario['id_regiao'];
$objCriteria->status = 2;

$optionsProjetos = getProjetos($usuario['id_regiao']);
$optionsAnos = $objFolha->getAnosFolhaFinalizada($usuario['id_regiao']);
$optionsMeses = mesesArray();
$optionsAnosNovaFolha = anosArray(date("Y")-2,date("Y"));
$folhasAbertas = $objFolha->getListaFolhas($objCriteria);
$optionsFormaDecimo = array(1 => "Primeira Parcela", 2 => "Segunda Parcela", 3 => "Integral");
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
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">
        <style>
            .boxTipoDecimo{display: none;}
            .bt-image{cursor: pointer; }
        </style>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>

        <div class="container">

            <div class="row">
                    
                <div class="col-lg-10">
                    <div class="bs-component">
                        <ul class="nav nav-tabs" style="margin-bottom: 15px;">
                            <li class="active"><a href="#abertas" data-toggle="tab">Folhas abertas</a></li>
                            <li><a href="#fechadas" data-toggle="tab">Relatórios</a></li>
                            <li><a href="#novas" data-toggle="tab">Nova Folha</a></li>
                        </ul>
                        <div id="myTabContent" class="tab-content">
                            <div class="tab-pane fade active in" id="abertas">
                                <?php 
                                if(count($folhasAbertas) == 0){
                                    echo "<div class='alert alert-dismissable alert-success'>
                                                <strong>Obaa!</strong> Parabéns, nenhuma folha aberta esse mês!
                                            </div>";
                                } else {
                                ?>
                                <form class="form-horizontal" action="" id="formVisualizarFolha" method="post">
                                    <input type="hidden" name="folha" value="" />
                                    <input type="hidden" name="mes" value="" />
                                    <input type="hidden" name="ano" value="" />
                                    <input type="hidden" name="inicioFolha" value=""/>
                                    <input type="hidden" name="finalFolha" value=""/>
                                    
                                    <table class='table table-striped table-hover'>
                                        <thead>
                                            <th>COD.</th>
                                            <th>Projeto</th>
                                            <th>Gerado Por</th>
                                            <th>Período</th>
                                            <th>nº CLTs</th>
                                            <th colspan="2">Ações</th>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($folhasAbertas as $folhasAb){ ?>
                                                <tr data-key="<?php echo $folhasAb['id_folha']; ?>">
                                                    <td><?php echo $folhasAb['id_folha']; ?></td>
                                                    <td><?php echo $folhasAb['nome_projeto']; ?></td>
                                                    <td><?php echo $folhasAb['criado_por']; ?></td>
                                                    <td><?php echo $folhasAb['periodo']; ?></td>
                                                    <td><?php echo $folhasAb['quant_clt']; ?></td>
                                                    <td><img src="../../imagens/icones/icon-view.gif" title="Visualizar" class="bt-image center-block visualizar" data-type="ver" data-key="<?php echo $folhasAb['id_folha']; ?>" data-mes="<?php echo $folhasAb['mes']; ?>" data-ano="<?php echo $folhasAb['ano']; ?>" data-inicio-folha="<?php echo $folhasAb['data_inicio']; ?>" data-final-folha="<?php echo $folhasAb['data_fim']; ?>"></td>
                                                    <td><img src="../../imagens/icones/icon-trash.gif" title="Excluir" class="bt-image center-block excluir" data-type="excluir" data-key="<?php echo $folhasAb['id_folha']; ?>" data-projeto="<?php echo $folhasAb['id_projeto']; ?>" ></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </form>
                                <?php } ?>
                            </div>
                            <div class="tab-pane fade" id="fechadas">
                                <form class="form-horizontal">
                                    <fieldset>
                                        <legend>Filtro</legend>
                                        <div class="form-group">
                                            <label for="projetoFinal" class="col-lg-2 control-label">Projeto</label>
                                            <div class="col-lg-10">
                                                <?php echo montaSelect($optionsProjetos, array("-1" => "« Todos »"), "id='projetoFinal' name='projetoFinal' class='form-control'") ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="anoFinalizada" class="col-lg-2 control-label">Competencia</label>
                                            <div class="col-lg-4">
                                                <?php echo montaSelect($optionsAnos, date('Y'), "id='anoFinalizada' name='anoFinalizada' class='form-control'") ?>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <div class="col-lg-10 col-lg-offset-2">
                                                <input type="button" class="btn btn-primary" id="buscarFinalizadas" value="Buscar">
                                            </div>
                                        </div>
                                    </fieldset>
                                </form>

                                <div id="conteudo-finalizado">

                                </div>
                            </div>
                            <div class="tab-pane fade" id="novas">
                                <form class="form-horizontal" id="formNova">
                                    <fieldset>
                                        <legend>Dados para nova Folha</legend>
                                        <div class="form-group">
                                            <label for="projetoNovo" class="col-lg-2 control-label">Projeto</label>
                                            <div class="col-lg-10">
                                                <?php echo montaSelect($optionsProjetos, array("-1" => "« Selecione »"), "id='projetoNovo' name='projetoNovo' class='form-control'") ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="mesAnoNovo" class="col-lg-2 control-label">Mês de Referência</label>
                                            <div class="col-lg-5">
                                                <?php echo montaSelect($optionsMeses, date('m'), "id='mesNovo' name='mesNovo' class='form-control'") ?>
                                            </div>
                                            <div class="col-lg-5">
                                                <?php echo montaSelect($optionsAnosNovaFolha, date('Y'), "id='anoNovo' name='anoNovo' class='form-control'") ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="inicioNovo" class="col-lg-2 control-label">Inicio da Folha</label>
                                            <div class="col-lg-10">
                                                <input type="text" class="form-control date_f" id="inicioNovo" name="inicioNovo" placeholder="Data">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="decimoTerceiro" class="col-lg-2 control-label">Decimo Terceiro</label>
                                            <div class="col-lg-1">
                                                <input type="radio" class="" id="terceiro" name="terceiro" value="1"> Sim
                                            </div>
                                            <div class="col-lg-1">
                                                <input type="radio" class="" id="terceiro" name="terceiro" value="0" checked="true"> Não
                                            </div>
                                        </div>
                                        <div class="form-group boxTipoDecimo">
                                            <label for="formaDePagamento" class="col-lg-2 control-label">Forma de Pagamento</label>
                                            <div class="col-lg-5">
                                                <?php echo montaSelect($optionsFormaDecimo, '', "id='tipoDecimo' name='tipoDecimo' class='form-control'") ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-lg-10 col-lg-offset-2">
                                                <input type="button" class="btn btn-success" id="gerarFolha" value="Gerar Folha">
                                            </div>
                                        </div>
                                    </fieldset>
                                </form>
                            </div>
                        </div>
                        <div id="source-button" class="btn btn-primary btn-xs" style="display: none;">&lt; &gt;</div></div>
                </div>

                <div class="col-lg-2">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h4 class="panel-title">Resumo do Mês (<?php echo date('m') ?>)</h4>
                        </div>
                        <div class="panel-body">
                            <div class="bs-component">
                                <table class="table table-striped table-hover ">
                                    <tbody>
                                        <tr class="success">
                                            <td>Finalizada</td>
                                            <td>2</td>
                                        </tr>
                                        <tr class="danger">
                                            <td>Abertas</td>
                                            <td>5</td>
                                        </tr>
                                    </tbody>
                                </table> 
                            </div>
                        </div>
                    </div>
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
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/rh/folha_oo/main.js"></script>
        <script>
            $(function(){
                $(".date_f").mask("99/99/9999");
                
                /**
                * VERIFICANDO SE É DECIMO TERCEIRO 
                * @returns {undefined}
                */
               $("body").on("click", "#terceiro", function(){
                   if($(this).val() == 1){
                       $(".boxTipoDecimo").show();
                   }else{
                       $(".boxTipoDecimo").hide();
                   }
               });

            });
        </script>
    </body>
</html>
