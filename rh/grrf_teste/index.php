<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include "../../conn.php";
include "../../classes/funcionario.php";
include '../../classes_permissoes/regioes.class.php';
include("../../wfunction.php");


$Fun = new funcionario();
$Fun->MostraUser(0);
$Master = $Fun->id_master;
$REGIOES = new Regioes();

$usuario = carregaUsuario();


///MASTER
$regioes = montaQuery('regioes', "id_regiao,regiao", "status =1 AND status_reg = 1 AND id_master = '$usuario[id_master]'");
$optRegioes = array('' => 'Selecione...');
foreach ($regioes as $valor) {
    $optRegioes[$valor['id_regiao']] = $valor['id_regiao'] . ' - ' . $valor['regiao'];
}
$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];



$rsMeses = montaQuery('ano_meses', "num_mes, nome_mes");
$meses = array('' => '<< Mês >>' );
foreach ($rsMeses as $valor) {
    $meses[$valor['num_mes']] = $valor['nome_mes'];
}
$mesesSel = (isset($_REQUEST['mes']))?$_REQUEST['mes']:'';

$anoOpt = array('' => '<< Ano >>');
for($i=2012; $i<=date('Y');$i++){
    
    $anoOpt[$i] = $i; 
}
$anoSel = (isset($_REQUEST['ano']))?$_REQUEST['ano']:'';


If(isset($_REQUEST['gerar'])){
    
$id_master = $_REQUEST['master'];    
$regiao = $_REQUEST['regiao'];  
$projeto = $_REQUEST['projeto'];

$mes = $_REQUEST['mes'];
$ano = $_REQUEST['ano'];

$dt_referencia = $ano.'-'.$mes.'-'.'01';

 
$qr_relatorio = mysql_query("SELECT A.*,C.nome as nome_projeto, DATE_FORMAT(data_entrada, '%d/%m/%Y') as dt_admissao,
                        DATE_FORMAT(B.data_demi, '%d/%m/%Y') as dt_demissao, D.nome as funcao
                        FROM rh_recisao as A
                        INNER JOIN rh_clt as B
                        ON A.id_clt = B.id_clt  
                        INNER JOIN projeto as C
                        ON C.id_projeto = A.id_projeto
                        INNER JOIN curso as D
                        ON B.id_curso = D.id_curso
                        WHERE A.id_regiao = '$regiao' AND A.id_projeto = '$projeto' AND MONTH(A.data_demi) = '$mes' AND YEAR(A.data_demi) = '$ano' AND A.status = 1;") or die(mysql_error());


}
?>
<html>
    <head>
        <title>Relatório</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="../../net1.css" rel="stylesheet" type="text/css">
        <script src="../../jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
        <script src="../../jquery/jquery.tools.min.js" type="text/javascript" ></script>
       
        <script>
         $(function(){     
             
     
       
        
        
        $('#regiao').change(function(){	
                var id_regiao = $(this).val();
              
                $('#projeto').next().html('<img src="../../img_menu_principal/loader16.gif"/>');
                $.ajax({		
                        url : '../../action.global.php?regiao='+id_regiao,                        
                        success :function(resposta){			
                                        $('#projeto').html(resposta);	
                                        $('#projeto').next().html('');        
                                    }		
                        });
                
                        
                });	
                
          $('#regiao').trigger('change');      
             
        });     
            
        </script>
        <style media="screen">
            table{ font-size: 10px;}
            .regiao { color:   #0078FF; 
                      font-size: 16px; 
                      font-weight: bold;
            }
            .projeto { color:     #000b0b; 
                      font-size: 16px; 
                      
            }
            
             .nome_rend{ margin-bottom: 5px;}
                </style>
          <style media="print">
            fieldset{display: none;}
           
        </style>
       
    </head>
    <body class="novaintra" >        
        <div id="content" style="width:1200px;">
            <div id="head">
                <img src="../../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;" width="140" height="100"/>
                <div class="fleft">
                    <h2>GRRF TESTE</h2>
                    <p></p>
                </div>
            </div>
            <br class="clear">
            <br/>

            <form  name="form" action="" method="post" id="form">
                <fieldset>
                    <legend>Dados</legend>
                    <div class="fleft">
                        <p><label class="first">Região:</label> <?php echo montaSelect($optRegioes, $regiaoSel, array('name' => "regiao", 'id' => 'regiao')); ?> <span class="loader"></span></p>                        
                        <p><label class="first">Projeto:</label> <?php echo montaSelect($optProjeto, $projetoSel, array('name' => "projeto", 'id' => 'projeto')); ?><span class="loader"></span></p>                        
                        <p><label class="first">Mês:</label> <?php echo montaSelect($meses, $mesesSel, array('name' => "mes", 'id' => 'mes')); ?><span class="loader"></span></p>                        
                        <p><label class="first">Ano:</label> <?php echo montaSelect($anoOpt, $anoSel, array('name' => "ano", 'id' => 'ano')); ?><span class="loader"></span></p>                        
                     </div>
  
                    <br class="clear"/>
                
                    <p class="controls" style="margin-top: 10px;">
                      <span class="fleft erro"><?php if($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                      <input type="submit" name="gerar" value="Gerar" id="gerar"/>
                    </p>
                </fieldset>
            </form>
                <?php
                if(mysql_num_rows($qr_relatorio) == 0){
                    
                    echo '<p>Nenhuma rescisão encontrada nesta competência.</p>';
                } else {
                 ?>   
                <table border="0" cellpadding="0" cellspacing="0" class="grid" width="100%" style="page-break-after:auto;"> 
                    <tr>
                        <td>COD</td>
                        <td>NOME</td>
                        <td>UNIDADE</td>
                        <td>FUNÇÃO</td>
                        <td>DATA DE ADMISSÃO</td>
                        <td>DATA DE DEMISSÃO</td>
                    </tr>
                  <?php
                  while($row_rel = mysql_fetch_assoc($qr_relatorio)){
                   ?>   
                    <tr>
                        <td><?php echo $row_rel['id_clt'];?></td>
                        <td><?php echo $row_rel['nome'];?></td>
                        <td><?php echo $row_rel['nome_projeto'];?></td>
                        <td><?php echo $row_rel['funcao'];?></td>
                        <td><?php echo $row_rel['dt_admissao'];?></td>
                        <td><?php echo $row_rel['dt_demissao'];?></td>
                    </tr> 
                     
                 <?php } ?>      
                </table> 
                <div style="text-align:center;">
                <form method="post" action="gerar_arquivo.php">
                    <input name="mes" type="hidden" value="<?php echo $mes?>"/>
                    <input name="ano" type="hidden" value="<?php echo $ano?>"/>
                    <input name="regiao" type="hidden" value="<?php echo $regiao?>"/>
                    <input name="projeto" type="hidden" value="<?php echo $projeto?>"/>
                    <input name="gerar" type="submit" value="GERAR ARQUIVO"/>                
                </form>
                </div>
                <?php
                }
                ?>
                
                
                
         
            
            <div class="clear"></div>
        </div>
</body>
</html>