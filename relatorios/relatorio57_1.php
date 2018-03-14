<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include "../conn.php";
include "../funcoes.php";
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
 $meses[13] = "13º Salário - 1ª parcela";
 $meses[14] = "13º Salário - 2ª parcela";
 $meses[15] = "13º Salário - Integral";



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


switch ($mes){
    case 13: $sql_mes     = "AND A.terceiro = 1 AND A.tipo_terceiro = 1";
              $sql_folha = 'AND terceiro = 1 AND tipo_terceiro = 1';
         
        break;
    case 14: $sql_mes = "AND A.terceiro = 1 AND A.tipo_terceiro = 2";
           $sql_folha = 'AND terceiro = 1 AND tipo_terceiro = 2';
            
        break;
    case 15: $sql_mes = "AND A.terceiro = 1 AND A.tipo_terceiro = 3";
           $sql_folha = 'AND terceiro = 1 AND tipo_terceiro = 3';
            
        break;
    default: $sql_mes = "AND  A.mes = $mes AND A.terceiro != 1";
                $sql_folha = "AND terceiro != 1 AND mes = '$mes' AND ano = '$ano'";
            
}


$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$id_master'");
$row_master = mysql_fetch_assoc($qr_master);

$qr_projeto  = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$projeto'");
$row_projeto = mysql_fetch_assoc($qr_projeto);


 
$qr_relatorio = mysql_query("
SELECT clt.*, C.id_curso, C.nome as nome_curso,  D.horas_mes
    FROM 
            (
            SELECT B.id_clt, B.nome, A.id_folha, B.id_regiao, A.mes, A.ano,C.id_curso,B.sallimpo,C.nome as nome_clt, A.ids_movimentos_estatisticas, B.a5020, B.a5021,B.salliquido,B.a7001, C.rh_horario,a5037,A.terceiro,
            
            IF(MONTH(C.data_entrada) = A.mes AND A.terceiro != 1,
                                    sallimpo_real,
                                    IF(A.terceiro != 1,B.sallimpo,B.valor_dt)) as salario_base,
            

            IF(A.terceiro != 1, B.a5020, B.inss_dt) as inss,
            IF(A.terceiro != 1, B.a5021, B.ir_dt) as irrf,
            
           (SELECT id_curso_de FROM rh_transferencias WHERE id_clt=B.id_clt AND id_curso_de <> id_curso_para AND data_proc >= '{$dt_referencia}' ORDER BY id_transferencia ASC LIMIT 1) AS de,
           (SELECT id_curso_para FROM rh_transferencias WHERE id_clt=B.id_clt AND id_curso_de <> id_curso_para AND data_proc <= '{$dt_referencia}' ORDER BY id_transferencia DESC LIMIT 1) AS para,
           (SELECT id_horario_de FROM rh_transferencias WHERE id_clt=B.id_clt AND id_horario_de <> id_horario_para AND data_proc >= '{$dt_referencia}' ORDER BY id_transferencia ASC LIMIT 1) AS horario_de,
           (SELECT id_horario_para FROM rh_transferencias WHERE id_clt=B.id_clt AND id_horario_de <> id_horario_para AND data_proc <= '{$dt_referencia}' ORDER BY id_transferencia DESC LIMIT 1) AS horario_para,
           C.cpf    
          

             FROM rh_folha as A
            INNER JOIN rh_folha_proc as B
            ON A.id_folha = B.id_folha
            INNER JOIN rh_clt as C
            ON C.id_clt = B.id_clt
            WHERE A.regiao = $regiao  AND projeto = '$projeto' $sql_mes AND A.ano = $ano AND A.status = 3  AND B.status = 3 ) as clt

            LEFT JOIN curso AS C ON (IF(clt.para IS NOT NULL,C.id_curso=clt.para, IF(clt.de IS NOT NULL,C.id_curso=clt.de,C.id_curso=clt.id_curso)))
            LEFT JOIN rh_horarios AS D ON (IF(clt.horario_para IS NOT NULL,D.id_horario=clt.horario_para, IF(clt.horario_de IS NOT NULL,D.id_horario=clt.horario_de,D.id_horario=clt.rh_horario)))
    WHERE C.id_curso IN(1369,1370,1371,1372,1373,1374,1375,1376,1379,1380,1381,1387,1390,1391,1431,2012,1443,1444,1450,1451,1455,1471,1953,1952,2011,1950,1946,1942,1941,1940,1937,1936,1935,1934,1933,1932,1931,1930,1913,2010,1912,1910,1906,1902,1901,1900,1897,1896,1895,1894,1893,1892,1891,1890,1873,1872,2009,1870,1866,1862,1861,1860,1857,1856,1855,1853,1854,1852,1851,1850,1833,1832,2008,1830,1826,1822,1821,1820,1817,1816,1815,1814,1813,1812,1811,1810,1793,2007,1792,1790,1786,1782,1781,1780,1777,1776,1775,1774,1773,1772,1771,1770,1970,1971,1972,1973,1974,1975,1976,1977,1980,1981,1982,1986,1990,1992,1993,2013,2016,2017,2018,
            2022,2025,2028,2029,2030,2031,2032,2033,2034,2035,2036,2037,2038,2045,2057,2058,2059,2060,2062)        
  ORDER BY clt.nome
        ") or die(mysql_error());


   
///FOLHA           
$qr_folha = mysql_query("SELECT * FROM rh_folha WHERE  regiao = '$regiao' AND projeto = '$projeto' AND status = 3 $sql_folha") or die(mysql_error(e));
$row_folha = mysql_fetch_assoc($qr_folha);



///MOVIMENTOS
$qr_movimentos = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_movimento IN($row_folha[ids_movimentos_estatisticas])");
while($row_mov = mysql_fetch_assoc($qr_movimentos)){
    
    if($row_folha['terceiro'] == 1 and $row_mov['id_mov'] == 200){continue;}
    
    
    
    $qr_nome_mov = mysql_query("SELECT * FROM rh_movimentos WHERE id_mov = '$row_mov[id_mov]'");
    $row_nome_mov = mysql_fetch_assoc($qr_nome_mov);
    
    $tipo_movimento = ($row_mov['tipo_movimento'] == 'DEBITO' or $row_mov['tipo_movimento'] == 'DESCONTO')? 'DEBITO':'CREDITO';
    
    $nome_mov[$tipo_movimento][] = '('.$row_nome_mov['cod_ses'].') '.$row_mov['nome_movimento'];
    $movimentos[$row_mov['id_clt']][$tipo_movimento][ '('.$row_nome_mov['cod_ses'].') '.$row_mov['nome_movimento']] = $row_mov['valor_movimento'];
    
}


//outros NOMES
/*
 $nome_mov['CREDITO'][] = '(35) '.'ABONO FÉRIAS';
 $nome_mov['CREDITO'][] = '(37) '.'FÉRIAS PAGAS NO MES';
 $nome_mov['CREDITO'][] = '(37) '.'1/3 FÉRIAS';
*/



//NOMES DOS MOVIMENTOS
$nomes_credito  = array_unique($nome_mov['CREDITO']);
$nomes_debito   = array_unique($nome_mov['DEBITO']);
  
}
?>
<html>
    <head>
        <title>Relatório</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="../net1.css" rel="stylesheet" type="text/css">
        <script src="../jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
        <script src="../jquery/jquery.tools.min.js" type="text/javascript" ></script>
<script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/global.js" type="text/javascript"></script>
       
        <script>
         $(function(){     
             
     
         $('#master_').change(function(){	
                var id_master = $(this).val();
                  $('#regiao_').next().html('<img src="../img_menu_principal/loader16.gif"/>');
                $.ajax({                        
                        url : '../action.global.php?master='+id_master,
                      
                        success :function(resposta){			
                                        $('#regiao_').html(resposta);
                                        $('#regiao_').next().html('');
                                }		
                        });
                 
                  $('#regiao_').trigger('change')
                });	
       
        
        
        $('#regiao_').change(function(){	
                var id_regiao = $(this).val();
              
                $('#projeto_').next().html('<img src="../img_menu_principal/loader16.gif"/>');
                $.ajax({		
                        url : '../action.global.php?regiao='+id_regiao,                        
                        success :function(resposta){			
                                        $('#projeto_').html(resposta);	
                                        $('#projeto_').next().html('');        
                                    }		
                        });
                
                        
                });	
                
          $('#master_').trigger('change');      
             
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
        </style>
          <style media="print">
            fieldset{display: none;}
            .separador{ display: block;}
        </style>
       
    </head>
    <body class="novaintra" >        
        <div id="content" style="width:3000px;">
            <div id="head">
                <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;" width="140" height="100"/>
                <div class="fleft">
                    <h2>Relatório de Folha de Pagamento</h2>
                    <p></p>
                </div>
            </div>
            <br class="clear">
            <br/>

            <form  name="form" action="" method="post" id="form">
                <fieldset style="width: 1000px;">
                    <legend>Relatório</legend>
                    <div class="fleft">
                        <p><label class="first">Master:</label> <?php echo montaSelect($optMaster, $masterSel, array('name' => "master", 'id' => 'master_')); ?></p>
                        <p><label class="first">Região:</label> <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao_')); ?> <span class="loader"></span></p>                        
                        <p><label class="first">Projeto:</label> <?php echo montaSelect($optProjeto, $projetoSel, array('name' => "projeto", 'id' => 'projeto_')); ?><span class="loader"></span></p>                        
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
                ?>
            
         <p style="text-align: left; padding-left: 30px;"><input type="button" onclick="tableToExcel('folha', 'Folha')" value="Exportar para Excel" class="exportarExcel"></p>  
   
         <?php
         $qr_reg= mysql_query("SELECT * FROM regioes WHERE id_regiao = $regiao");
         $row_reg = mysql_fetch_assoc($qr_reg);
         $qr_pro = mysql_query("SELECT * FROM projeto WHERE id_projeto = $projeto");
         $row_pro = mysql_fetch_assoc($qr_pro);
         ?>
            <p class="separador"><strong>Região: </strong><?php echo $row_reg['regiao']?></p>
            <p class="separador"><strong>Projeto: </strong><?php echo $row_pro['nome']?></p>
            <p class="separador"><strong>Competência:</strong> <?php echo mesesArray($mes)?>/ <?php echo $ano; ?></p>
            
            
                 <table border="0" cellpadding="0" cellspacing="0" class="grid essatb" width="100%" style="page-break-after:auto;"  id="folha"> 
                
                <tr>
                    <td align="center" colspan="<?php echo (count($nomes_credito) +   ((count($nomes_debito)+3)) + 7)?>7"> <?php echo $row_master['razao'].' - '.$row_projeto['nome'];?>
                        <strong>  <?php echo $row_master['razao'].' - '.$row_projeto['nome'];?> </strong>
                    </td>
                </tr>
                <tr rowspan="2"  class="titulo">
                    <td colspan="5"></td>
                    
                    <td colspan="2" style="background-color: #cccccc ">ESCALA</td> 
                    <td colspan="<?php echo count($nomes_credito);?>" style="background-color: #cdeacd">PROVENTOS</td>
                    <td colspan="<?php echo count($nomes_debito)+3;?>" style="background-color:  #cfcfcf ">DESCONTOS</td>
                    <td></td>
                </tr>      
                     
               <tr class="titulo">
                    <td>NOME</td>
                    <td>CPF</td>
                    <td>CARGO</td>
                    <td>ESPECIALIDADE</td>
                    <td>SALARIO BASE</td>
                    <td>HORAS ESCALADAS</td>
                    <td>HORAS TRABALHADAS</td>
                   <?php
                   foreach($nomes_credito as $titulo_mov){
                       echo '<td>'.RemoveAcentos($titulo_mov).'</td>';
                   }
                   ?>
                   <?php
                    foreach($nomes_debito as $titulo_mov){
                       echo '<td>'.RemoveAcentos($titulo_mov).'</td>';
                   } ?>
                    <td>DESCONTO VALE TRANSPORTE</td>
                    <td>INSS</td>
                    <td>IRRF</td>
                    <td>SALARIO LIQUIDO</td>
                </tr>
               
                 
                 
                 <?php    
                 $contador =0;
                 while($row_rel = mysql_fetch_assoc($qr_relatorio)){
                   
                 
                     
                     //férias
                     if($row_rel['a5037'] != '0.00'){
                         continue;
                          /*      $qr_ferias = mysql_query("SELECT * FROM  rh_ferias WHERE id_clt = '$row_rel[id_clt]' AND status = 1 AND ano = '$row_rel[ano]'") or die (mysql_error());
                                $row_ferias = mysql_fetch_assoc($qr_ferias);                        

                                   // $nome_mov['CREDITO'][] = '(35) '.'ABONO FÉRIAS';
                                  // $nome_mov['CREDITO'][] = '(37) '.'FÉRIAS PAGAS NO MES';
                                  // $nome_mov['CREDITO'][] = '(37) '.'1/3 FÉRIAS';

                           $movimentos[$row_rel['id_clt']]['CREDITO']['(37) '.'FÉRIAS PAGAS NO MES'] = $row_ferias['valor_total_ferias'];
                           $movimentos[$row_rel['id_clt']]['CREDITO']['(35) '.'ABONO FÉRIAS']      = $row_ferias['abono pecuniario'];
                           $movimentos[$row_rel['id_clt']]['CREDITO']['(37) '.'1/3 FÉRIAS']        = $row_ferias['umterco'];

                            $inss =  $row_ferias['inss'];
                            $irrf =  $row_ferias['irrf'];
                            $valor_liquido = $row_ferias['total_liquido']; 
                 //    print_r($movimentos);
                     }else {
                           $inss = $row_rel['a5020'];
                           $irrf = $row_rel['a5021'];
                           $valor_liquido = $row_rel['salliquido']; */
                     }
                        
                     
                     
                $salario_base = $row_rel['salario_base'];    
                $inss = $row_rel['inss'];
                $irrf = $row_rel['irrf'];
                $valor_liquido = $row_rel['salliquido']; 

                        
              $verifica_rescisao =  mysql_query("SELECT * FROM rh_recisao 
                                                WHERE id_clt = '$row_rel[id_clt]' 
                                                AND MONTH(data_demi) = $mes AND YEAR(data_demi) = $ano AND status = 1")   ;  
              if(mysql_num_rows($verifica_rescisao) != 0 ) continue;
       ?>
                                
                 <tr style="font-size:11px;">                    
                     <td><?php echo RemoveAcentos($row_rel['nome_clt'])?></td>
                     <td><?php echo formatCPFCNPJ($row_rel['cpf'])?></td>
                     <td><?php echo RemoveAcentos($row_rel['nome_curso'])?></td>
                     <td></td>
                     <td> R$ <?php echo number_format($salario_base,2,',','.')?></td>
                     <td align="center"><?php echo $row_rel['horas_mes'];?></td>
                     <td></td>
                    <?php
                    foreach($nomes_credito as $mov){    echo '<td align="center">';
                                                                if(!empty($movimentos[$row_rel['id_clt']]['CREDITO'][$mov])){
                                                                echo 'R$ '.number_format($movimentos[$row_rel['id_clt']]['CREDITO'][$mov],2,',','.');
                                                                }
                                                                
                                                                echo '</td>';   }
                    ?>  
                    <?php
                    foreach($nomes_debito as $mov){     echo '<td align="center">';
                                                                if(!empty($movimentos[$row_rel['id_clt']]['DEBITO'][$mov])){
                                                                echo 'R$ '.number_format($movimentos[$row_rel['id_clt']]['DEBITO'][$mov],2,',','.');
                                                                }
                                                                
                                                                echo '</td>';  }
                    ?>  
                     <td align="center"><?php echo ($row_rel['a7001'] !='0.00')? 'R$ '.number_format($row_rel['a7001'],2,',','.'):'';?></td>
                     <td align="center"><?php echo ($inss  !='0.00')? 'R$ '.number_format($inss,2,',','.'):'';?></td>
                     <td align="center"><?php echo ($irrf  !='0.00')? 'R$ '.number_format($irrf,2,',','.'):'';?></td>
                     <td align="center"><?php echo ($valor_liquido !='0.00')? 'R$ '.number_format($valor_liquido,2,',','.'):'';?></td>
                     
                 </tr>
                                
                 <?php               
            
                  
                  } 
                  echo '</table>';
                }?>  
            <div class="clear"></div>
        </div>
  

</body>
</html>