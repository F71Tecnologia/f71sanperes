<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include "../conn.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include("../wfunction.php");
error_reporting(1);

$Fun = new funcionario();
$Fun->MostraUser(0);
$Master = $Fun->id_master;
$REGIOES = new Regioes();

$usuario = carregaUsuario();


///MASTER
$master = montaQuery('master', "id_master,razao", "status = 1");
$optMaster = array();
foreach ($master as $valor) {
    $optMaster[$valor['id_master']] = $valor['id_master'] . ' - ' . $valor['razao'];
}
$masterSel = (isset($_REQUEST['master'])) ? $_REQUEST['master'] : $usuario['id_master'];



If(isset($_REQUEST['gerar'])){
    
$id_master = $_REQUEST['master'];    
$regiaosql = ($_REQUEST['regiao'] == '')? '' : "AND A.id_regiao = $_REQUEST[regiao]" ;    
$projetosql = ($_REQUEST['projeto'] == '')? '' : "AND A.id_projeto = $_REQUEST[projeto]" ;    



 
$qr_relatorio = mysql_query("SELECT A.id_clt, A.matricula, A.nome,B.nome as funcao, A.cpf,  A.pis , C.regiao as nome_regiao, D.nome as nome_projeto, A.id_regiao, A.id_projeto
FROM rh_clt as A
INNER JOIN curso as B
ON A.id_curso = B.id_curso
INNER JOIN regioes as C
ON C.id_regiao = A.id_regiao
INNER JOIN projeto as D
ON D.id_projeto = A.id_projeto
WHERE   A.status = 10 $regiaosql $projetosql  
ORDER BY C.id_regiao,D.id_projeto, A.nome") or die(mysql_error());
    
}


?>
<html>
    <head>
        <title>Relatório de CNES</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="../net1.css" rel="stylesheet" type="text/css">
        <script src="../jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
        <script src="../jquery/jquery.tools.min.js" type="text/javascript" ></script>
       
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
        </style>
        <style media="print">
            fieldset{display: none;}
        </style>
       
    </head>
    <body class="novaintra" >        
        <div id="content" style="width:1200px;">
            <div id="head">
                <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;" width="140" height="100"/>
                <div class="fleft">
                    <h2>Relatório para o  controle de ponto </h2>
                    <p></p>
                </div>
            </div>
            <br class="clear">
            <br/>

            <form  name="form" action="" method="post" id="form">
                <fieldset>
                    <legend>Relatório de CNES</legend>
                    <div class="fleft">
                        <p><label class="first">Master:</label> <?php echo montaSelect($optMaster, $masterSel, array('name' => "master", 'id' => 'master')); ?></p>
                        <p><label class="first">Região:</label> <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao')); ?> <span class="loader"></span></p>                        
                        <p><label class="first">Projeto:</label> <?php echo montaSelect($optProjeto, $projetoSel, array('name' => "projeto", 'id' => 'projeto')); ?><span class="loader"></span></p>                        
                     </div>
  
                    <br class="clear"/>
                
                    <p class="controls" style="margin-top: 10px;">
                      <span class="fleft erro"><?php if($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                      <input type="submit" name="gerar" value="Gerar" id="gerar"/>
                    </p>
                </fieldset>
        </form>
             <?php
                if(!empty($qr_relatorio) and isset($_POST['gerar'])){                 
                
                 echo '   <table border="0" cellpadding="0" cellspacing="0" class="grid" width="100%">';   
                    while($row_rel = mysql_fetch_assoc($qr_relatorio)){

                            if($row_rel['id_regiao'] != $regiaoAnt){  echo '<tr><td colspan="13" height="50" class="regiao">'.$row_rel['regiao'].'</td></tr>';   }        
                            if($row_rel['id_projeto'] != $projetoAnt){  echo '<tr><td colspan="13" height="80" class="projeto">'.$row_rel['nome_projeto'].'</td></tr>';   
                            ?>           
                                <tr class="titulo">
                                    <td>Nome</td>
                                    <td>Função</td>
                                   <td>CPF</td>
                                   <td>Unidade</td>
                                   <td>PIS</td>
                                   <td>Matrícula</td>
                                       
                                </tr>
                          <?php  }  ?>  
                                
                 <tr>
                     <td><?php echo $row_rel['nome']?></td>
                     <td><?php echo $row_rel['funcao']?></td>
                     <td><?php echo $row_rel['cpf']?></td>
                     <td><?php echo $row_rel['nome_projeto']?></td>
                     <td><?php echo $row_rel['pis']?></td>
                     <td><?php echo $row_rel['matricula']?></td>
                 </tr>
                                
                 <?php               
                 $regiaoAnt = $row_rel['id_regiao'];
                 $projetoAnt = $row_rel['id_projeto'];
                  
                  } 
                  echo '</table>';
                }?>  
            <div class="clear"></div>
        </div>
  

</body>
</html>