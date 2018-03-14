<?php

//include_once('root_dir.php');
include_once('../../conn.php');
include_once('../../wfunction.php');
include_once('../../classes/FeriasProgramadasClass.php');
include_once('../../classes_permissoes/acoes.class.php');
if(!include_once(ROOT_LIB.'fw.class.php')) die ('Não foi possível incluir '.ROOT_LIB.'fw.class.php, a partir de '.__FILE__);
 
$charset = mysql_set_charset('utf8');
$usuario = carregaUsuario();
$objAcoes = new Acoes();
$objFeriasProg = new FeriasProgramadasClass();
$rh = new \hub\FwClass();

//Recupera variaveis
$mesR = (!empty($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m');
$anoR = (!empty($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
$projetoR = (!empty($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$auxProjeto = ($projetoR > 0) ? " AND B.id_projeto = '$projetoR' " : null;

//Pegando as variaveis para os montar o calendario
$data = date('m*Y*N*t', mktime(0, 0, 0, $mesR, '01', $anoR));
$dataP = date('m*Y', mktime(0, 0, 0, $mesR+1, '01', $anoR));
$dataA = date('m*Y', mktime(0, 0, 0, $mesR-1, '01', $anoR));
list($mes, $ano, $diaSemanaPrimeiroDia, $ultimoDia) = explode('*', $data); 
list($mesP, $anoP) = explode('*', $dataP); 
list($mesA, $anoA) = explode('*', $dataA); 

//Options dos meses
$mesesArray = mesesArray();
unset($mesesArray[-1]);

//Options dos Projetos
$projetoArray = getProjetos($usuario['id_regiao']);
$projetoArray[-1] = 'Todos os Projetos';
$programacao = $objFeriasProg->getFeriasProgramadas($mes, $ano, $usuario['id_regiao'], $auxProjeto);

foreach ($programacao as $key => $value) {
    
    $clts_array[] = $value['id_clt'];
    
}

$id_clts = implode(',', $clts_array);

$rh->Clt->setIdClt($id_clts); 
$feriasPendente = $rh->Ferias->getPeriodoAquisitivoPendente();

if(isset($_REQUEST['method']) && $_REQUEST['method'] == "excluir_ferias_prog"){
    $objFeriasProg = new FeriasProgramadasClass();
    $objFeriasProg->setIdFeriasProgramadas($_REQUEST['id']);
    $objFeriasProg->deletarFeriasProgramadas();
    
    echo json_encode(array('status'=>1));
    exit; 
}
if(isset($_REQUEST['method']) && $_REQUEST['method'] == "ferias_agendada"){

    $objFeriasProg = new FeriasProgramadasClass();
    $objFeriasProg->setIdFeriasProgramadas($_REQUEST['id']);
    $objFeriasProg->getVerAgendamentosDia();
    
//    echo json_encode(array('status'=>1));
    exit; 
}


?>

<script src="../../resources/js/print.js" type="text/javascript"></script>
 <script type="text/javascript">
    $(document).ready(function() {

        $(".deletar").on("click", function() {
             var id_ferias_prog = $(this).data("key");
             
             
            bootConfirm("Você deseja realmente excluir este agendamento?", "Exclusão de Férias Agendadas", function(verdadeiro){
                console.log(verdadeiro);
                if(verdadeiro === true){
                    $.post("ferias_calendario.php", {method:"excluir_ferias_prog", id:id_ferias_prog}, function(data){
                        if(data.status==1){
                           $("#"+id_ferias_prog).remove();
                           //window.location('index.php');
                        }else{
                           alert("erro ao excluir");
                        }
                    },"json");
                }
            }, "danger");
        });
        
        // exibe Férias Agendadas do clt
    

 
        $("#popup").hide();
        
//        $(".td_esconde, .td_aquisitivo").hide(); //exibir o campo com as datas
        
//        $(".imprimir").click(function(){
//             var id_ferias_prog = $(this).data("key");
//             var aquisit = $(".pegadata_"+id_ferias_prog).val();
//             
//            $(".td_esconde, .pegadata_"+id_ferias_prog).show();
//        }); 
//        $(".td_aquisitivo").change(function(){
//            var id_ferias = $(this).data("id");
//            var ano = $(this).val();
            $(".imprimir").click(function(){ //enviar para outra pagina
                var id_ferias = $(".td_aquisitivo").data("id");
                var ano = $(".td_aquisitivo").data("ano");
                if(ano != ""){
    //              alert("SIM"); 
                    window.open ("ferias_processar2.php?ano="+ano+"&id_ferias="+id_ferias, "_blank");
    //        $("#popup").show();
    //        $("#tabela_ferias").hide();
                } 
//            }); 
        });
        
//        $('.imprimir_todos').click(function(){
//	if( $('.impressao_lote:checked').length == 0 ){
//		alert('Selecione almenos 1');
//	}else{
//		var val = new Array();
////		
//		$('.impressao_lote:checked').each(function(){
//			val.push($(this).val());
//			alert(val);
//		});
//
//		$.ajax({
//			url:'ferias_processar2.php',
//			type:'POST',
//			data:'valor=' + val,
//			success:function(data){
////				$('.exibe').html(data);
//                                 window.open ("ferias_processar2.php?ano="+ano+"&id_ferias="+val, "_blank");
//			}
//		});
//	}
//	return false;
//});

        
        
        $(".imprimir_todos").click(function(){
            var valor = $('.impressao_lote:checked').val();
//            alert(valor);
            $("#form_attr").attr("action","ferias_processar2.php");   
        });
        $(".gerar_ferias_lote").click(function(){
            $("#form_attr").attr("action","../../?class=ferias/processar&id_clt=");   
        });
        
    });
       

</script>  
<style>
    .ver_agendados:hover {
        background-color: #666!important; 
        color: #FFF!important;
    }
    
    #popup{
     position: fixed;
     top: 0; bottom: 0;
     left: 0; right: 0;
     margin: auto;
     width: auto;
     height: auto;
     padding: 20px;
     border: solid 1px #331;
     background: #ffffd0;
     display: none;
     z-index: 1000;  
    }
    
</style>


<div id="tabela_ferias">
<table class="table table-condensed text-sm">
    <div class="panel-heading hidden-print">
        <span class="badge badge-warning">
            <?php $c=0;
                foreach ($programacao as $key => $value) {
                    $dataProgramada = $value['inicioBR'];
                    $DataAtual = date('d/m/Y');
                    $result = count($dataProgramada);
                    if($dataProgramada > date($DataAtual ,  strtotime("+15 Days"))){
                        $c++;
                    } 
                }
            echo $c;
            ?>
        </span> <?php echo utf8_encode("Férias");?> faltando 15 dias ou menos</div> 
    <thead>
        <!--Filtro do calendario-->
        <tr class="valign-middle no-border">
            <th colspan="1" class="text-center hidden-print"><a class="btn btn-info ProximoAnterior <?=($anoA < date("Y") - 4)?'hide':null?>" data-mes="<?= $mesA ?>" data-ano="<?= $anoA ?>"><i class="fa fa-angle-double-left"></i><span class="hidden-md hidden-sm hidden-xs"> M&ecirc;s</span><span class="hidden-sm hidden-xs"> Anterior</span></a></th>
            <th colspan="4" class="text-center">
                <div class="col-xs-12 text-center">
                    <div class="input-group">
                        <?= montaSelect($projetoArray, $projetoR, 'class="form-control text-center trocaData" id="projeto"')?>
                        <div class="input-group-addon"></div>
                        <?= montaSelect($mesesArray, $mes, 'class="form-control text-center trocaData" id="mes"')?>
                        <div class="input-group-addon">/</div>
                        <?= montaSelect(anosArray(null, ($anoP+1 > date("Y")+1) ? $anoP+1 : null, $default), $ano, 'class="form-control text-center trocaData" id="ano"')?>
                    </div>
                </div>
            </th>
            <th colspan="1" class="text-center hidden-print"><a class="btn btn-info ProximoAnterior" data-mes="<?= $mesP ?>" data-ano="<?= $anoP ?>"><span class="hidden-sm hidden-xs">Pr&oacute;ximo </span><span class="hidden-md hidden-sm hidden-xs">M&ecirc;s </span><i class="fa fa-angle-double-right"></i></a></th>
            <th colspan="1" class="text-center hidden-print">
                <?php if($objAcoes->verifica_permissoes(100)){ ?><a href="ferias_programadas_form.php" class="btn btn-success"><i class="fa fa-plus"></i><span class="hidden-xs"> Novo</span><span class="hidden-md hidden-sm hidden-xs"> Agendamento</span></a><?php } ?>
            </th>
            <th class="hidden-print"><?php echo "<a class='bt-image btn btn-xs btn-primary' title='Imprimir Tabela de Agendamento' onClick='window.print()' target='_blank'><i class='fa fa-print fa-2x'></i></a>" ?></th>
        </tr>
        <!--Cabeçalho do calendario-->
        <!--<tr class="valign-middle bordered bg-primary">
            <th class="text-center bordered">Dom<span class="hidden-xs">ingo</span></th>
            <th class="text-center bordered">Seg<span class="hidden-xs">unda</span></th>
            <th class="text-center bordered">Ter<span class="hidden-xs">&ccedil;a</span></th> 
            <th class="text-center bordered">Qua<span class="hidden-xs">rta</span></th>
            <th class="text-center bordered">Qui<span class="hidden-xs">nta</span></th>
            <th class="text-center bordered">Sex<span class="hidden-xs">ta</span></th>
            <th class="text-center bordered">S&aacute;b<span class="hidden-xs">ado</span></th>
        </tr>-->
    </thead>
    </table>
         <?php //      $auxNomeUnidade = null;
                    
                      ?>
    <form action="#" method="post" id="form_attr">
            <table class="table table-striped table-hover text-sm valign-middle" id="tbRelatorio">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Projeto</th>
                        <th>Cargo</th>
                        <th><?php echo utf8_encode("Período de Gozo") ?></th>
                        <th class="hidden-print"><?php echo utf8_encode("Impressão") ?></th>
                        <th class="hidden-print"><?php echo utf8_encode("Upload") ?></th>
                        <th class="hidden-print"><?php echo utf8_encode("Ações") ?></th>
                        <th class="td_esconde hidden-print" id="td_esconde" onClick="esconde()">Periodo Aquisitivo</th>
                        <th class="hidden-print"><?php echo utf8_encode("Detalhamento") ?></th>
                        <th class="hidden-print"><?php echo utf8_encode("Impressão") ?></th>
                    </tr>
                </thead>
                <tbody> 
                    <?php 
//                    print_array($programacao);
                    foreach ($programacao as $key => $value) {
                        
//                  echo  $id_projeto . ' - ' . $value['nome_projeto'];
                    $anos = anosArray(substr($value['data_entrada'], 0, 4));
//                    $sql_ferias = mysql_query("SELECT * FROM rh_ferias_programadas WHERE id_clt = {$value['id_clt']} ");
//                    print_array($feriasPendente);
//                    if($auxNomeUnidade != $value['unidade'])echo "<h5>{$value['unidade']}</h5>";
                    ?>
                    
                    <tr id="<?php echo $value['id_ferias_programadas']?>">
                        <td class=""><?php echo $value['nome'];   if($value['inicioBR'] > date($DataAtual ,  strtotime("+15 Days"))){
                                echo "   <span class='badge badge-warning hidden-print'>1</span>";
                            };?></td>
                        
                        <td><?php echo $value['nome_projeto'];?></td>
                        <td><?php echo $value['funcao'];?></td>
                        <td ><?php echo $value['inicioBR'];?><?php echo utf8_encode(" à ") ?><?php echo $value['fimBR'];?></td>
                        <?php 
                        foreach ($feriasPendente[$value['id_clt']] as $k => $v) {

                        ?>
                        <td>
                            <?php if($value['impressao']==1){ echo "<a  class='bt-image btn btn-xs btn-info' title='Visualizar Impressos' data-key='{$value['id_ferias_programadas']}' href='javascript:;'><i class='fa fa-check'></i></a></td>";}else{ echo " ";} ?>
                        </td>
                        <td>
                            <?php if($value['upload']==1){echo "<a  class='bt-image btn btn-xs btn-success' title='Arquivos' data-key='{$value['id_ferias_programadas']}' target='_blank' href='../../{$value['nome_arq_upload']}'><i class='fa fa-upload'></i></a></td>";}else{ echo " ";} ?>
                        </td>
                       
                        
                        <td>
                            <div class="hidden-print">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                        <?php echo utf8_encode("Ações")?>
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu pull-right" role="menu">
                                        <li><?php echo "<a style='color:#fff' class='bt-image  btn btn-xs btn-primary imprimir' data-key='{$value['id_ferias_programadas']}' title='Imprimir Aviso'><i class='fa fa-print'></i>  Imprimir Aviso</a>" ?>
                                        </li>
                                        <li> <?php echo "<a style='color:#fff' target='_blank' class='bt-image btn btn-xs btn-warning' title='Gerar Férias' data-type='visualizar' data-key='30' href='ferias_processar.php?id_clt={$value['id_clt']}'><i class='fa fa-plane'></i>  Gerar ".utf8_encode("férias")."</a>" ?>
                                        </li>
                                        <li><?php echo "<a style='color:#fff' class='bt-image btn btn-xs btn-danger deletar' title='Excluir Agendamento' data-key='{$value['id_ferias_programadas']}' href='javascript:;'><i class='fa fa-trash'></i>  Excluir</a></td>" ?>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                       
                        <td class=" td_aquisitivo pegadata_<?=$value['id_ferias_programadas']?>" data-id='<?php echo $value['id_ferias_programadas'] ?>' data-ano='<?php echo $value['data_aquisitivo_ini']." - ".$value['data_aquisitivo_fim'];?>' >
                            <?php echo $v['data_aquisitivo_ini']." - ".$v['data_aquisitivo_fim'];?>
                        </td>
                        <td>
                            <?php echo "<a  class='btn btn-xs btn-default detalhamento_ferias' title='Detalhamento' data-key='{$value['id_ferias_programadas']}' href='javascript:;'><i class='fa fa-search'></i></a></td>"; ?>
                        </td>
                        <td>
                            <?php echo "<input type='checkbox' name='id_ferias[]' class='impressao_lote' title='impressao_lote' value='{$value['id_ferias_programadas']}'>"; ?>
                        </td>
                        <?php 
                        } 
                        ?>
                    </tr> <input class="hidden" value="<?php echo $value['aq_inicioBR']." - ".$value['aq_fimBR'];?>" name="ano[]">                    
                    <?php   $auxNomeUnidade = $value['unidade'];
                        
                    } 
                    ?>
                
                    <?php
                    if(empty($programacao)) { echo 'Não há funcionários Programados';}                        
                    ?>
                
                </tbody>
            </table>
        
        <div class="hidden-print text-right">
            <div class="btn-group">
                <?php
                if($_COOKIE['logado'] != 395){
                ?>
                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    GERAR LOTE
                    <span class="caret"></span>
                </button>
                <?php
                }
                ?> 
                <ul class="dropdown-menu pull-right" role="menu">
<!--                <li><?php echo "<a style='color:#fff' class='bt-image  btn btn-xs btn-primary imprimir' data-key='{$value['id_ferias_programadas']}' title='Imprimir Aviso'><i class='fa fa-print'></i>  Imprimir Aviso</a>" ?>
                    </li>-->
                    <li><input style="width: 200px;" type="submit" value="Gerar Aviso de Gozo" class=" btn btn-primary imprimir_todos fa fa-print"></li>
                    <!--<li><input style="width: 200px;" type="submit" value="Gerar Férias" class=" btn btn-warning gerar_ferias_lote fa fa-airplane"></li>-->


                </ul>
            </div>
        </div>
        
        
        
        <!--<div class="text-right"> <input type="submit" value="Gerar Aviso" class=" btn btn-primary imprimir_todos fa fa-print"></div>-->
    </form>
<!--        <tr class="bordered">
            <?php $count=0; 
            //quadros em branco para meses iniciados no meio da semana
            if($diaSemanaPrimeiroDia < 7){ 
                for($i=1; $i <= $diaSemanaPrimeiroDia; $i++) {$resto = (++$count % 7); ?>
                    <td class="bordered valign-top text-right text-bold active" style="height: 60px; width: 14%;"></td>
                    <?=($resto == 0) ? '</tr><tr>' : null;
                }
            }
            //Pega os agendamentos do mes
            $programacao = $objFeriasProg->getFeriasProgramadas($mes, $ano, $usuario['id_regiao'], $auxProjeto);
            //Monta os dias dos meses
            for($i=1; $i <= $ultimoDia; $i++) { 
                $resto = (++$count % 7); 
                $qtdDia = 0;
                //Verifica se tem agendameto no dia
                foreach ($programacao as $value) {
                    if("$ano$mes".sprintf("%02d",$i) >= str_replace('-','',$value['inicio']) AND "$ano$mes".sprintf("%02d",$i) <= str_replace('-','',$value['fim']))
                        $qtdDia++;
                } ?>
                <td class="ver_agendados bordered valign-middle text-bold pointer hover <?=($resto == 0 || $resto == 1) ? 'text-danger warning' : null?> <?=("$i$mes$ano" == date('dmY')) ? 'success text-primary' : null?>" data-qtd="<?=$qtdDia?>" data-data="<?="$ano-$mes-".sprintf("%02d",$i)?>" style="height: 60px; width: 14%;">
                    Dia do mes
                    <div class="col-xs-12 text-right no-padding-r valign-top"><?= $i ?></div>
                    Quantidade agendada no dia
                    <div class="col-xs-9 no-padding-hr">
                        <i class="fa fa-<?= ($qtdDia > 0) ? 'plane' : null ?> text-info" style="font-size: 25px!important;"> <?= ($qtdDia > 0) ? $qtdDia : '&nbsp;' ?></i>
                    </div>
                </td>
                <?=($resto == 0) ? '</tr><tr>' : null;
            } 
            //Quadros em branco para meses terminados no meio da semana
            $diaSemanaUltimoDia = date("N", mktime(0, 0, 0, $mes, $ultimoDia, $ano));
            for($i=($diaSemanaUltimoDia == 7) ? 0 : $diaSemanaUltimoDia; $i <= $resto; $i++) { $resto = (++$count % 7); ?>
                <td class="bordered valign-top text-right text-bold active" style="height: 60px; width: 14%;"></td>
                <?=($resto == 0) ? '</tr><tr>' : null;
            } ?>
        </tr>-->
</div>
 