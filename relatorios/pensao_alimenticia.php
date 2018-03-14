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

$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form-lista", "ativo"=>"Relatório de Pensão Alimenticia");
$breadcrumb_pages = array("Gestão de RH"=>"/intranet/rh/principalrh.php");

$ano = date('Y');
$filtro = $_REQUEST['filtrar'];
$movimentosArray = array();
 
if(isset($filtro)){
	
    $projeto = $_REQUEST['projeto'];
    $mes = sprintf("%02d",$_REQUEST['mes']);
    $ano = $_REQUEST['ano'];        
    $status = $_REQUEST['status_folha'];        
    $movimentos = $_REQUEST['mov'];
    $projeto_sql = ($projeto > '0') ? "AND A.id_projeto = {$projeto}" : null;
         
    $criteria = "";
    if(!empty($projeto) && $projeto != "-1"){
        $criteria = " AND A.id_projeto = '{$projeto}' ";
    }
    
    /**
     * MOVIMENTOS E VALORES
     */
    $sql_descontados = "SELECT A.id_clt, A.id_unidade, D.nome as nome_projeto, A.nome, C.cod_mov, C.nome_mov, C.base, C.valor_mov,B.favorecido,B.cpf,B.agencia,B.conta,B.id_lista_banco,E.banco,G.id_unidade,G.unidade
                            FROM rh_clt AS A 
                            LEFT JOIN favorecido_pensao_assoc AS B ON(A.id_clt = B.id_clt) 
                            LEFT JOIN itens_pensao_para_contracheque AS C ON(C.cpf_favorecido = REPLACE(REPLACE(B.cpf,'.',''),'-','') AND C.`status` = 1) 
                            LEFT JOIN projeto AS D ON(A.id_projeto = D.id_projeto) 
                            LEFT JOIN listabancos AS E ON B.id_lista_banco = E.id_lista
                            LEFT JOIN rh_folha AS F ON(C.id_folha = F.id_folha)
                            LEFT JOIN unidade AS G ON(A.id_unidade = G.id_unidade)
                            WHERE A.pensao_alimenticia = 1 AND F.mes = '{$mes}' AND F.ano = '{$ano}' {$criteria}";
    echo "<!-- SQL: {$sql_descontados} -->";
    $qr_descontados = mysql_query($sql_descontados) or die(mysql_error());
    $total_descontados = mysql_num_rows($qr_descontados);

     
}
?>
<!doctype html>
<html>
<head>
    <meta charset="iso-8859-1">
    <title>Relatório de Pensão Alimentícia</title>
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
    
    <script src="../js/jquery-1.10.2.min.js"></script>
    
</head>
<body>
	<?php include("../template/navbar_default.php"); ?>
        
    <div class="<?=($container_full) ? 'container-full' : 'container'?>">
        <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Relatório de Pensão Alimentícia</small></h2></div>
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
                </div>
            
                    <div class="panel-footer text-right controls">
                        <?php if(!empty($total_descontados) && (isset($_POST['filtrar']))) { ?>
                        <button type="button" value="exportar para excel" onclick="tableToExcel('table_excel', 'Inss')" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                        <button type="button" form="formPdf" name="pdf" data-title="Relatório Pensão Alimentícia" data-id="table_excel" id="pdf" value="Gerar PDF" class="btn btn-danger"><span class="fa fa-file-pdf-o"></span> Gerar PDF</button>
                        <?php } ?>
                        <button type="submit" name="filtrar" id="filt" value="Filtrar" class="btn btn-primary"><span class="fa fa-filter"></span>Filtrar</button>
                    </div>
                </div>
        
        <?php
        if($filtro) {
            if($total_descontados > 0) {
        ?>
      
        <table class="table table-bordered table-hover table-condensed text-sm valign-middle" id="table_excel">
            <thead>                    
                <tr>
                    <th>Unidade</th>
                    <th>Nome</th>
                    <th>Beneficiário</th>                        
                    <th>CPF</th>
                    <th>Banco</th>
                    <th>Agencia</th>
                    <th>Conta</th>                        
                    <th>Movimento</th>
                    <th>Base</th> 
                    <th>Valor</th>                        
                       
                </tr>
            </thead>
            <?php $totalDesconto = 0; ?>
            <tbody>
                <?php
                while($row_descontado = mysql_fetch_assoc($qr_descontados)) { 
//                    if ($row_descontado['id_clt'] == 1985) {
//                        $sqlDescontoPensao = "SELECT valor_movimento
//                                              FROM rh_movimentos_clt
//                                              WHERE id_clt = 1985 
//                                              AND cod_movimento = 80034 
//                                              AND mes_mov = MONTH(NOW()) 
//                                              AND ano_mov = '2017' 
//                                              AND valor_movimento = '126.00' 
//                                              AND data_movimento = '2017-01-24'";
//                        $queryDescontoPensao = mysql_query($sqlDescontoPensao);
//                        $resultDescontoPensao = mysql_result($queryDescontoPensao,0);
//                        $row_descontado['valor_mov'] += $resultDescontoPensao;
//                    }
                ?>
                <tr class="linhasParticipantes">
                    <td><?php echo $row_descontado['unidade']; ?></td>
                    <td><?php echo $row_descontado['nome']; ?></td>
                    <td><?php echo $row_descontado['favorecido']; ?></td>
                    <td><?php echo $row_descontado['cpf']; ?></td>
                    <td><?php echo $row_descontado['banco']; ?></td>
                    <td><?php echo $row_descontado['agencia']; ?></td>
                    <td><?php echo $row_descontado['conta']; ?></td>
                    <td><?php echo $row_descontado['nome_mov']; ?></td>
                    <td><?php echo 'R$ ' . number_format($row_descontado['base'], 2, ',', '.'); ?></td>
                    <td><?php echo 'R$ ' . number_format($row_descontado['valor_mov'], 2, ',', '.');  ?></td>
                    <?php $totalDesconto += $row_descontado['valor_mov']; ?>
                </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr class='danger'>
                    <td colspan="9" class='text-right'>Total Geral:</td>
                    <td><?php echo 'R$ ' .  number_format($totalDesconto,2,',','.'); ?></td> 
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
    <script>
        $(function(){
            
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

