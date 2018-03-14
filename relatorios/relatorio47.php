<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a>";
    exit;
}
include("../conn.php");
include("../classes/regiao.php");
include("../classes/projeto.php");
include("../classes/funcionario.php");
include("../classes_permissoes/regioes.class.php");
include("../classes_permissoes/acoes.class.php");
include("../wfunction.php");

$Fun = new funcionario();
$Fun->MostraUser(0);
$Master = $Fun->id_master;
$REGIOES = new Regioes();

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$ACOES = new Acoes();

///REGIÃO
$regiao = montaQuery('regioes', "id_regiao,regiao", "status = 1");
$optRegiao = array();
foreach ($regiao as $valor) {
    $optRegiao[$valor['id_regiao']] = $valor['id_regiao'] . ' - ' . $valor['regiao'];
}
$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];



If(isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])){
    
  
$id_regiao = $_REQUEST['regiao'];  
$id_projeto = $_REQUEST['projeto'];
 
$sql = "SELECT D.nome as unidade, A.nome,  C.especifica as tipo , DATE_FORMAT(B.`data`,'%d/%m/%Y') as data_inicio, 
                            DATE_FORMAT(B.data_retorno,'%d/%m/%Y') as data_retorno, E.nome as funcao
                            
                            FROM rh_clt as A
                            left JOIN rh_eventos as B
                            ON A.id_clt = B.id_clt
                            left JOIN rhstatus as C
                            ON C.codigo = B.cod_status
                            LEFT JOIN projeto as D
                            ON D.id_projeto = B.id_projeto
                            INNER JOIN curso as E
                            ON E.id_curso = A.id_curso
                            WHERE B.cod_status IN(20,30,50,51,52,70,90,100,80,110,200)
                            AND B.id_regiao = '$id_regiao' ";
if(!isset($_REQUEST['todos_projetos'])) {
        $sql .= "AND B.id_projeto = '$id_projeto' ";
    }


$qr_relatorio = mysql_query($sql) or die(mysql_error());


}
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>:: Intranet :: Relatório de Licenças Médicas</title>
        
        <link href="../favicon.png" rel="shortcut icon" />
        
        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">


    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>
        
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - Relatório <small> - Relatório de Licenças Médicas</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                    <div class="panel-body">
                        <div class="form-group" >
                            <label for="select" class="col-sm-2 control-label hidden-print" >Região</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                            </div>
                            
                             <label for="select" class="col-sm-1 control-label hidden-print">Projeto</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect($optProjeto, $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                            </div>
                             
                             <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                        </div>
                    </div>
                    
                        <div class="panel-footer text-right hidden-print">
                            <?php if (!empty($qr_relatorio) and isset($_POST['gerar']) || isset($_REQUEST['todos_projetos'])) { ?>
                                <button type="button" onclick="tableToExcel('tbRelatorio', 'Licencas Médicas')" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                            <?php } ?>
                                <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                            <?php ///permissão para VISUALIZAR TODOS OS PROJETOS AO MESMO TEMPO
                            if ($ACOES->verifica_permissoes(85)) {
                                ?>
                                <button type="submit" name="todos_projetos" id="todos_projetos" value="Gerar de Todos os Projetos" class="btn btn-warning"><span class="fa fa-filter"></span> Filtrar Todos os Projetos</button>
                            <?php } ?>
                                <button type="submit" name="gerar" id="gerar" value="gerar" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar</button>
                        </div>
                    </div>
                
            <?php if(!empty($qr_relatorio) and isset($_POST['gerar']) || isset($_REQUEST['todos_projetos'])) { ?>
                <table class="table table-striped table-bordered table-hover text-sm valign-middle" id="tbRelatorio">
                 <thead>
                     <tr>
                        <td style="text-align:center;">UNIDADE</td>
                         <td style="text-align:center;">NOME</td>
                         <td style="text-align:center;">FUNÇÃO</td>
                         <td style="text-align:center;">EVENTO</td>
                         <td style="text-align:center;">DATA DE AFASTAMENTO</td>
                         <td style="text-align:center;">DATA DE RETORNO</td>                      
                     </tr>
                 </thead>
                 <?php    
                 $contador =0;
                 while($row_rel = mysql_fetch_assoc($qr_relatorio)){
                   
                     $contador++;
                   if($contador == 14){                       
                       echo '<tr height="60" class="separador"><td colspan="5">&nbsp;</td></tr>';
                       $contador == 0;
                   }      
            ?>
                <tbody>
                        <tr>
                            <td align="center"><?php echo $row_rel['unidade']?></td>
                            <td align="left"><?php echo $row_rel['nome']?></td>
                            <td align="center"> <?php echo $row_rel['funcao'];?></td>
                            <td align="center"><?php echo $row_rel['tipo']; ?></td>
                            <td align="center"><?php echo $row_rel['data_inicio'];?></td>    
                            <td align="center"><?php echo $row_rel['data_retorno'];?></td>       
                          </tr>
      
                </tbody>
                <?php 
                  } ?>
                </table>
                   <?php }else { ?>
                <div id="message-box" class="alert alert-danger">
                    <span class="fa fa-exclamation-triangle"></span>Nenhum registro encontrado
                </div>
            <?php }
             ?>
            </form>
            <?php include('../template/footer.php'); ?>
            <div class="clear"></div>
        </div>

        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        
      <script>
         $(function(){    
        
        
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
                
                $('#regiao').trigger('change');  
        });     
            
        </script>
    </body>
</html>
