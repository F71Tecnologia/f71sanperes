<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include "../conn.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include("../wfunction.php");


$Fun = new funcionario();
$Fun->MostraUser(0);
$Master = $Fun->id_master;
$REGIOES = new Regioes();

$usuario = carregaUsuario();


///MASTER
$master = montaQuery('master', "id_master,razao", "status =1");
$optMaster = array();
foreach ($master as $valor) {
    $optMaster[$valor['id_master']] = $valor['id_master'] . ' - ' . $valor['razao'];
}
$masterSel = (isset($_REQUEST['master'])) ? $_REQUEST['master'] : $usuario['id_master'];



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

 
$qr_relatorio = mysql_query("
SELECT clt.*, C.id_curso, C.nome as nome_curso
    FROM 
            (
            SELECT B.id_clt, B.nome, A.id_folha, B.id_regiao, A.mes, A.ano,C.id_curso,B.sallimpo,C.nome as nome_clt, A.ids_movimentos_estatisticas, B.a5020, B.a5021,B.salliquido,sallimpo_real,status_clt,
           (SELECT id_curso_de FROM rh_transferencias WHERE id_clt=B.id_clt AND id_curso_de <> id_curso_para AND data_proc >= '{$dt_referencia}' ORDER BY id_transferencia ASC LIMIT 1) AS de,
           (SELECT id_curso_para FROM rh_transferencias WHERE id_clt=B.id_clt AND id_curso_de <> id_curso_para AND data_proc <= '{$dt_referencia}' ORDER BY id_transferencia DESC LIMIT 1) AS para,
               B.a6005
         
            FROM rh_folha as A
            INNER JOIN rh_folha_proc as B
            ON A.id_folha = B.id_folha
            INNER JOIN rh_clt as C
            ON C.id_clt = B.id_clt
            
                WHERE A.regiao = '$regiao'AND projeto = '$projeto' AND A.mes = '$mes' AND A.ano = '$ano' AND A.status = 3 AND A.terceiro != 1 AND B.status = 3  AND B.id_clt NOT IN(53750) AND B.status_clt = 10 ) as clt
            LEFT JOIN curso AS C ON (IF(clt.para IS NOT NULL,C.id_curso=clt.para, IF(clt.de IS NOT NULL,C.id_curso=clt.de,C.id_curso=clt.id_curso)))
        ORDER BY clt.nome
        ") or die(mysql_error());


}
?>
<html>
    <head>
        <title>Relatório</title>
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
             .separador{ display: none;}
             .nome_rend{ margin-bottom: 5px;}
                </style>
          <style media="print">
            fieldset{display: none;}
            .separador{ display: block;}
        </style>
       
    </head>
    <body class="novaintra" >        
        <div id="content" style="width:1200px;">
            <div id="head">
                <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;" width="140" height="100"/>
                <div class="fleft">
                    <h2>Relatório de Incidênciasde INSS</h2>
                    <p></p>
                </div>
            </div>
            <br class="clear">
            <br/>

            <form  name="form" action="" method="post" id="form">
                <fieldset>
                    <legend>Relatório</legend>
                    <div class="fleft">
                        <p><label class="first">Master:</label> <?php echo montaSelect($optMaster, $masterSel, array('name' => "master", 'id' => 'master')); ?></p>
                        <p><label class="first">Região:</label> <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao')); ?> <span class="loader"></span></p>                        
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
           if(!empty($qr_relatorio) and isset($_POST['gerar'])){                 
             
         $qr_reg= mysql_query("SELECT * FROM regioes WHERE id_regiao = $regiao");
         $row_reg = mysql_fetch_assoc($qr_reg);
         
         $array_status_rescisao = array(60,61,62,63,64,65,66,81,101);
         ?>
            <p class="separador"><strong>Região: </strong><?php echo $row_reg['regiao']?></p>
            <p class="separador"><strong>Competência:</strong> <?php echo mesesArray($mes)?>/ <?php echo $ano; ?></p>
            
            
                 <table border="0" cellpadding="0" cellspacing="0" class="grid" width="100%" style="page-break-after:auto;"> 
                <tr class="titulo">
                    <td>Nome</td>
                    <td>Função</td>
                    <td>Salário</td>
                    <td>Rendimentos</td>                                   
                    <td></td>  
                </tr>
                 
                 
                 <?php    
                 $contador =0;
                 while($row_rel = mysql_fetch_assoc($qr_relatorio)){
                   
                     $contador++;
                   if($contador == 14){                       
                       echo '<tr height="60" class="separador"><td colspan="5">&nbsp;</td></tr>';
                       $contador == 0;
                   }    
                   
                                
                   if(in_array($row_rel['status_clt'],$array_status_rescisao )){  $rescisao = true;    } else { $rescisao = false; }
                   
                    if($rescisao == 1){
                                
                               $qr_rescisao = mysql_query("SELECT *,IF(fator = 'empregador' AND aviso = 'indenizado', aviso_valor ,0) as valor_aviso_previo 
                                                           FROM rh_recisao WHERE id_clt = '$row_rel[id_clt]' AND status = 1"); 
                                $row_resc = mysql_fetch_assoc($qr_rescisao);
                                
                                 $totalizador_salario += $row_resc['saldo_salario'];
                                 $salario               = $row_resc['saldo_salario'];
                    }else {
                            $totalizador_salario += $row_rel['sallimpo_real'];
                            $salario             = $row_rel['sallimpo_real'];
                    }
                   ?>           
                <tr style="font-size:11px; ">
                     <td><?php echo ($rescisao == 1)? $row_rel['id_clt'].' - '.$row_rel['nome_clt'].'<br>(RESCISÃO)': $row_rel['id_clt'].' - '.$row_rel['nome_clt'];?></td>
                     <td><?php echo  $row_rel['nome_curso'];?></td>
                     <td align="center"> R$ <?php echo ($rescisao == 1)? number_format($row_resc['saldo_salario'],2,',','.') : number_format($row_rel['sallimpo_real'],2,',','.')?></td>
                        <td>
                            <table   width="100%">
                            <?php 
                         
                            if($rescisao == 1){                            
                                
                                $array_recisao13['13º RESCISÃO']      = $row_resc['dt_salario'];
                                $array_recisao13['13º INDENIZADO']    = $row_resc['terceiro_ss'];
                                $array_recisao['INSALUBRIDADE']       = $row_resc['insalubridade'];
                                $array_recisao['ADICIONAL NOTURNO']   = $row_resc['adicional_noturno'];
                                $array_recisao['DSR']                 = $row_resc['dsr'];
                                $array_recisao['AVISO PRÉVIO']        = $row_resc['valor_aviso_previo'];                                
                            
                                
                               
                                
                           //////VERIFCANDO A TABELA DE MOVIMENTOS DA NOVA RESCISÃO
                            if(substr($row_resc['data_proc'],0,10) >= '2013-04-04'){

                                    $qr_movimentos = mysql_query("SELECT B.descicao, B.id_mov, A.valor, B.campo_rescisao, A.nome_movimento
                                    FROM rh_movimentos_rescisao as A 
                                    INNER JOIN
                                    rh_movimentos as B
                                    ON A.id_mov = B.id_mov
                                    WHERE A.id_clt = '$row_resc[id_clt]' 
                                    AND A.id_rescisao = '$row_resc[id_recisao]' 
                                    AND  status = 1 AND (A.incidencia = '5020,5021,5023' OR A.id_mov = 62 ) AND A.id_mov not in(61) ") or die(mysql_error());
                                    while($row_movimentos = mysql_fetch_assoc($qr_movimentos)){  

                                           $array_recisao[$row_movimentos['nome_movimento']] = $row_movimentos['valor'];                                        
                                    }
                                
                            } else {
                                
                            

                                $qr_movimento = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_clt = '$id_clt' AND mes_mov = 16 AND status = 1 
                                                             AND (incidencia = '5020,5021,5023' OR id_mov = 62)   ");
                                while($row_movimento = mysql_fetch_assoc($qr_movimento)){    
                                    
                                   $array_recisao[$row_movimento['nome_movimento']] = $row_movimento['valor'];     
                                   
                                }  
                               
                              
                            }
                           /////////////////////////////////////////////////////
                            /////////////////////////////////////
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                              foreach($array_recisao13 as $nome_campo => $valor) {
                                    
                                    if($valor != 0){
                                        echo '<tr style="border:0;">
                                            <td style="border:0;">'.$nome_campo.'</td>
                                            <td style="border:0;"> R$ '.number_format($valor,2,',','.').'</td>
                                            </tr>'; 
                                         $total_mov       += $valor;
                                         $totalizador_mov += $valor;
                                    }                                 
                                }
                                
                                
                                
                                foreach($array_recisao as $nome_campo => $valor) {
                                    
                                    if($valor != 0){
                                        echo '<tr style="border:0;">
                                            <td style="border:0;">'.$nome_campo.'</td>
                                            <td style="border:0;"> R$ '.number_format($valor,2,',','.').'</td>
                                            </tr>'; 
                                         $total_mov       += $valor;
                                         $totalizador_mov += $valor;
                                    }                                 
                                }   
                            
                                
                            
                            
                            
                            
                              ////MOVIMENTOS DA FOLHA  
                            }else if(!empty($row_rel[ids_movimentos_estatisticas])){
                                
                                
                                if($row_rel['a6005'] != '0.00'){
                                 echo '<tr style="border:0;">
                                        <td style="border:0;">SALÁRIO MATERNIDADE:</td>
                                        <td style="border:0;"> R$ '.number_format($row_rel['a6005'],2,',','.').'</td>
                                    </tr>';                                 
                                    $total_mov       += $row_rel['a6005'];
                                    $totalizador_mov  += $row_rel['a6005'];
                                }
                                
                                ///OBS: na folha não precisa puxar o movimento de falta, pois o salário já vem descontado as faltas
                                    $qr_movi = mysql_query("SELECT * FROM rh_movimentos_clt 
                                                         WHERE id_movimento IN($row_rel[ids_movimentos_estatisticas]) 
                                                         AND id_clt = '$row_rel[id_clt]'
                                                         AND incidencia = '5020,5021,5023' AND id_mov NOT IN(62)") or die(mysql_error());
                                while ($row_mov = mysql_fetch_assoc($qr_movi)){ 
                                    
                                    
                                    if($row_mov['tipo_movimento'] == 'CREDITO'){ 
                                             
                                             $total_mov       += $row_mov['valor_movimento'];
                                             $totalizador_mov += $row_mov['valor_movimento'];
                                    }else {      
                                             $total_mov       -= $row_mov['valor_movimento'];
                                             $totalizador_mov -= $row_mov['valor_movimento'];
                                    }
                                       
                                    echo '<tr style="border:0;">
                                             <td style="border:0;">'.$row_mov['nome_movimento'].':</td>
                                             <td style="border:0;"> R$ '.number_format($row_mov['valor_movimento'],2,',','.').'</td>
                                             </tr>';                                          
                                     }  
                                  }
                                  $total_bruto += $total_mov+$salario;
                                  
                                  
                                  if(!empty($total_mov)){                                      
                                      echo '<tr style="border:0;">
                                                <td align="right" style="border:0;">TOTAIS:</td>
                                                <td style="border:0;">'.number_format($total_mov,2,',','.').'</td>
                                            </tr>';
                                  }
                                  ?>
                              
                            </table>
                        </td>
                        <td align="center">R$ <?php echo number_format($total_mov+$salario,2,',','.') ; ?></td>                     
                 </tr>                                
                 <?php  
                 unset($total_mov);
                  } 
                  echo '<tr style="text-align:center;font-weight:bold;">
                            <td colspan="2"></td>
                            <td align="center">'.number_format($totalizador_salario,2,',','.').'</td>
                            <td align="center">'.number_format($totalizador_mov,2,',','.').'</td>
                            <td align="center">'.number_format($total_bruto,2,',','.').'</td>
                        </tr>';
                  echo '</table>';
                }?>  
            <div class="clear"></div>
        </div>
</body>
</html>