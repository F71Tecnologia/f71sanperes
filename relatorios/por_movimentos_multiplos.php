<?php
if(empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="../login.php">Logar</a>';
    exit;
}

include('../conn.php');
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include('../wfunction.php');
include("../classes/global.php");


$Fun = new funcionario();
$Fun->MostraUser(0);
$Master = $Fun->id_master;
$REGIOES = new Regioes();

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$global = new GlobalClass();

$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form-lista", "ativo"=>"Relatório por Movimentos Múltiplos");
$breadcrumb_pages = array("Gestão de RH"=>"/intranet/rh/principalrh.php");

$ano = 2016;
$filtro = $_REQUEST['filtrar'];
$movimentosArray = array();
 
if(isset($filtro)){
	
    $projeto = $_REQUEST['projeto'];
    $mes = $_REQUEST['mes'];
    $ano = $_REQUEST['ano'];        
    $status = $_REQUEST['status_folha'];        
    $movimentosRequest = $_REQUEST['mov'];
    $movimentos = implode(',',$_REQUEST['mov']);
    $projeto_sql = ($projeto > '0') ? "AND A.id_projeto = {$projeto}" : null;
    
    /**
     * LISTA DE MOVIMENTOS
     */
    $listaMovimentos = "SELECT A.id_mov, A.nome_movimento FROM rh_movimentos_clt AS A 
                        WHERE A.`status` = '{$status}' 
                        AND (A.mes_mov = '{$mes}' AND A.ano_mov = '{$ano}' || (A.lancamento = 2)) {$projeto_sql} 
                        GROUP BY A.nome_movimento";
    
    $sqlListaMovimentos = mysql_query($listaMovimentos);
    while($rowsListaMov = mysql_fetch_assoc($sqlListaMovimentos)){
        $movimentosArray[$rowsListaMov['id_mov']] = $rowsListaMov['nome_movimento'];
    }
//    print_r($movimentosArray);
    /**
     * MOVIMENTOS E VALORES
     */
//    print_r($movimentos);

    $sql_descontados = "SELECT A.qnt_horas, A.qnt, A.id_movimento, A.id_regiao, A.id_projeto, B.unidade, B.nome, C.nome as funcao, C.letra, C.numero, 
                                B.id_unidade, A.mes_mov, A.ano_mov, A.nome_movimento, A.valor_movimento, DATE_FORMAT(B.data_entrada,'%d/%m/%Y') AS data_entrada
                                FROM rh_movimentos_clt AS A
                                LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
                                LEFT JOIN curso AS C ON(B.id_curso = C.id_curso)	
                            WHERE A.`status` = '{$status}' AND A.id_mov IN ({$movimentos}) AND 
                                    ((A.mes_mov = '{$mes}' AND A.ano_mov = '{$ano}') || (A.lancamento = 2)) {$projeto_sql} AND B.nome IS NOT NULL
                            ORDER BY B.nome, C.nome, C.letra, C.numero";
    
    $qr_descontados = mysql_query($sql_descontados);
    $total_descontados = mysql_num_rows($qr_descontados);
     
}
?>
<!doctype html>
<html>
<head>
    <meta charset="iso-8859-1">
    <title>Relatório por Movimentos Múltiplos</title>
    <!-- Bootstrap -->
    <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
    <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
    <link href="../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
    <link href="../resources/css/main.css" rel="stylesheet" media="all">
    <link href="../resources/css/font-awesome.css" rel="stylesheet" media="all">
    <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
    <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="all">
    <link href="../css/progress.css" rel="stylesheet" type="text/css">
    <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
    
    
    
</head>
<body>
	<?php include("../template/navbar_default.php"); ?>
        
    <div class="<?=($container_full) ? 'container-full' : 'container'?>">
        <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Relatório por Movimentos Múltiplos</small></h2></div>
        <form action="" method="post" class="form-horizontal top-margin1" name="form1" id="form1">

            <?php if(isset($_SESSION['regiao'])){ ?>                
            <!--resposta de algum metodo realizado-->
            <div class="alert alert-<?php echo $_SESSION['MESSAGE_TYPE']; ?> msg_cadsuporte">
                <button type="button" class="close" data-dismiss="alert">Ã—</button>
                <p><?php echo $_SESSION['MESSAGE'];
                session_destroy(); ?></p>
            </div>
            <?php } ?>

            <div class="panel panel-default hidden-print">
                <div class="panel-heading">Relatório Detalhado</div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="select" class="col-lg-2 control-label">Projeto:</label>
                        <div class="col-lg-8">
                            <?php echo montaSelect($global->carregaProjetosByRegiao($usuario['id_regiao']), $projeto, "id='projeto' name='projeto' class='required[custom[select]] form-control'"); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="select" class="col-lg-2 control-label">Mês:</label>
                        <div class="col-lg-8">
                            <div class="input-daterange input-group" id="bs-datepicker-range">
                                <?php echo montaSelect(mesesArray(), $mes, "id='mes' name='mes' class='required[custom[select]] form-control'"); ?>
                                <span class="input-group-addon">Ano</span>
                                <?php echo montaSelect(AnosArray(null,null), $ano, "id='ano' name='ano' class='required[custom[select]] form-control'"); ?>                                
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="select" class="col-lg-2 control-label">Status da Folha:</label>
                        <div class="col-lg-2">
                            <div class="input-daterange input-group" id="bs-datepicker-range">
                                <input type="radio" name="status_folha" value="1" <?php if($_REQUEST['status_folha'] == 1) { ?> checked="checked" <?php } ?> /> Folha Aberta
                            </div>
                        </div>
                        <div class="col-lg-1"></div>
                        <div class="col-lg-2">
                            <div class="input-daterange input-group" id="bs-datepicker-range">
                                <input type="radio" name="status_folha" value="5" <?php if($_REQUEST['status_folha'] == 5) { ?> checked="checked" <?php } ?> /> Folha Finalizada
                            </div>
                        </div>
                        <div class="col-lg-5"></div>
                    </div>
                    <?php if($filtro) { ?>
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Movimentos:</label>
                            <div class="col-lg-8">
                                <div class="input-daterange input-group" id="bs-datepicker-range">
                                    <select id="mov" multiple="multiple" name="mov[]" class="hide required[custom[select]] form-control">
                                        <?php foreach ($movimentosArray as $key => $value) { ?>
                                        <option value="<?=$key?>" <?php if(in_array($key, $movimentosRequest)){echo 'selected';}?>><?=mb_convert_case($value, MB_CASE_TITLE, 'iso-8859-1');?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="panel-footer text-right controls">
                        <?php if(!empty($total_descontados) && (isset($_POST['filtrar']))) { ?>
                            <button type="button" value="exportar para excel" onclick="tableToExcel('table_excel', 'Inss')" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                            <button type="button" form="formPdf" name="pdf" data-title="Relatorio Por Movimentos" data-id="table_excel" id="pdf" value="Gerar PDF" class="btn btn-danger">Gerar PDF</button>
                        <?php } ?>
                            <button type="submit" name="filtrar" id="filt" value="Filtrar" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar</button>
                    </div>
                </div>
        
        <?php
        if($filtro) {
            if($total_descontados > 0) {
        ?>

        <table class="table table-bordered table-hover table-condensed text-sm valign-middle" id="table_excel">
            <thead>                    
                <tr class="bg-primary">
                    <th>Nome</th>
                    <th>Função</th>
                    <th>Data Entrada</th>
                    <th>Lançamento</th>
                    <th>Movimento</th>
                    <th>Quantidade</th>
                    <th>Valor</th>                        
                </tr>
            </thead>
            <?php $totalDesconto = 0; ?>
            <tbody>
                <?php
                while($row_descontado = mysql_fetch_assoc($qr_descontados)) { 

                ?>
                <tr class="linhasParticipantes">
                    <td><?php echo $row_descontado['nome']; ?></td>
                    <td><?php echo $row_descontado['funcao'] . " - " . $row_descontado['letra'] . $row_descontado['numero']; ?></td>
                    <td><?php echo $row_descontado['data_entrada']; ?></td>
                    <td><?php echo $row_descontado['mes_mov'] . " / " . $row_descontado['ano_mov']; ?></td>
                    <td><?php echo $row_descontado['nome_movimento']; ?></td>
                    <td><?php echo ($row_descontado['qnt'] > 0) ? $row_descontado['qnt'] : ($row_descontado['qnt_horas'] != '00:00:00') ? $row_descontado['qnt_horas'] : null ?></td>
                    <td><?php echo number_format($row_descontado['valor_movimento'], 2, ',', '.');  ?></td>
                    <?php $totalDesconto += $row_descontado['valor_movimento']; ?>
                </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr class='danger'>
                    <td colspan="6" class='text-right'>Total Geral:</td>
                    <td><?php echo number_format($totalDesconto,2,',','.'); ?></td> 
                </tr>
            </tfoot>

        </table>
        
        <?php } else { ?>
            <div class="alert alert-danger top30">                    
                Nenhum registro encontrado
            </div>
        <?php }
        } ?>

    </form>
   
        <?php include('../template/footer.php'); ?>
    </div>

    <script src="../js/jquery-1.10.2.min.js"></script>
    <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
    <script src="../resources/js/bootstrap.min.js"></script>
    <script src="../resources/js/tooltip.js"></script>
    <script src="../resources/js/main.js"></script>
    <script src="../js/global.js"></script>
    <script src="../resources/js/bootstrap-multiselect.js" type="text/javascript"></script>
    <script>
        $(function(){
           
            $('#mov').multiselect({
                enableFiltering: true,
                includeSelectAllOption: true,
                maxHeight: 300
            });
           
           $('#master').change(function(){	
                var id_master = $(this).val();
                  $('#regiao').next().html('<img src="../img_menu_principal/loader16.gif"/>');
                $.ajax({                        
                        url : '../action.global.php?master='+id_master,
                      
                        success :function(resposta){			
                                        $('#regiao').html(resposta);
                                        $('#regiao').next().html('');
                                }		
                        });
                 
                  $('#regiao').trigger('change')
                });	
       
        
        
            $('#regiao').change(function(){	
                var id_regiao = $(this).val();
              
                $('#projeto').next().html('<img src="../img_menu_principal/loader16.gif"/>');
                $.ajax({		
                    url : '../action.global.php?regiao='+id_regiao,                        
                    success :function(resposta){			
                                    $('#projeto').html(resposta);	
                                    $('#projeto').next().html('');        
                                }		
                    });


            });	
                
          $('#master').trigger('change');  
            
           $("body").on("click","input[name='filtroTipo']",function(){
                var valor = $(this).val();
                if(valor == 2){
                    $(".linhasParticipantes").hide();
                }else{
                    $(".linhasParticipantes").show();
                }
           });
        });
    </script>
            
    </body>
</html>

