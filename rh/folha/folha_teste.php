<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include "../../conn.php";
include('../../funcoes.php');
include "../../classes/funcionario.php";
include '../../classes_permissoes/regioes.class.php';
include("../../wfunction.php");

function verifica_campo($valor){
    
    if(!empty($valor) and $valor != 0){
        return number_format($valor,2,',','.');
    }
    
}

$qr_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_user = mysql_fetch_assoc($qr_user);
$id_master = $row_user['id_master'];


// Buscando a Folha
list($regiao, $id_folha) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));
$link_voltar =  'ver_folha.php?enc='.$_REQUEST['enc'];

$qr_folha = mysql_query("select A.id_folha, A.projeto, B.nome as nome_projeto, DATE_FORMAT(A.data_inicio,'%d/%m/%Y') as data_inicio, 
                        DATE_FORMAT(A.data_fim,'%d/%m/%Y') as data_fim, C.nome_mes, A.ids_movimentos_estatisticas,mes, ano
                        FROM rh_folha as A 
                        INNER JOIN projeto as B
                        ON B.id_projeto = A.projeto
                        INNER JOIN ano_meses as C
                        ON C.num_mes = A.mes
                        WHERE A.id_folha = '$id_folha'") or die(mysql_error()) ;
$row_folha = mysql_fetch_assoc($qr_folha);   


///PEGANDO MOVIMENTOS DA FOLHA
$qr_movimento = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_movimento IN($row_folha[ids_movimentos_estatisticas])");
while($row_mov = mysql_fetch_assoc($qr_movimento)){
    
    $tipo_mov = ($row_mov['tipo_movimento'] != 'CREDITO')? 'DEBITO' : 'CREDITO';    
    $movimentos_folha[$row_mov['id_clt']][$tipo_mov][$row_mov['nome_movimento']] = $row_mov['valor_movimento'];
    
    // Acrescenta os Movimentos nas Bases de INSS e IRRF
$incidencias = explode(',', $row_mov['incidencia']);	

        //BASE INSS
        if(in_array(5020,$incidencias)) {	
                if($tipo_mov == 'CREDITO') {						
                        $movimentos_folha[$row_mov['id_clt']]['BASE INSS'] += $row_mov['valor_movimento'];
                } elseif($tipo_mov == 'DEBITO') {						
                         $movimentos_folha[$row_mov['id_clt']]['BASE INSS'] -= $row_mov['valor_movimento'];
                }
        }

       //BASE IRRF
        if(in_array(5021,$incidencias)) {	
                if($tipo_mov == 'CREDITO') {						
                        $movimentos_folha[$row_mov['id_clt']]['BASE IRRF'] += $row_mov['valor_movimento'];
                } elseif($tipo_mov == 'DEBITO') {						
                         $movimentos_folha[$row_mov['id_clt']]['BASE IRRF'] -= $row_mov['valor_movimento'];
                }
        }

       //BASE INSS
        if(in_array(5023,$incidencias)) {	
                if($tipo_mov == 'CREDITO') {						
                        $movimentos_folha[$row_mov['id_clt']]['BASE FGTS'] += $row_mov['valor_movimento'];
                } elseif($tipo_mov == 'DEBITO') {						
                         $movimentos_folha[$row_mov['id_clt']]['BASE FGTS'] -= $row_mov['valor_movimento'];
                }
        }
    
    
}
///////



$dt_referencia = $row_folha['ano'].'-'.$row_folha['mes'].'-01';
$qr_folha_proc = mysql_query("SELECT folha.*, C.nome as funcao FROM 
                                (   SELECT A.*,MONTH(B.data_entrada) as mes_admissao,B.id_curso as id_curso_clt,
                                    (SELECT id_curso_de FROM rh_transferencias WHERE id_clt=A.id_clt AND id_curso_de <> id_curso_para AND data_proc >= '$dt_referencia' ORDER BY id_transferencia ASC LIMIT 1) AS de,
                                    (SELECT id_curso_para FROM rh_transferencias WHERE id_clt=A.id_clt AND id_curso_de <> id_curso_para AND data_proc <= '$dt_referencia' ORDER BY id_transferencia DESC LIMIT 1) AS para
                                    FROM rh_folha_proc  as A 
                                    INNER JOIN rh_clt as B
                                    ON A.id_clt = B.id_clt
                                    WHERE A.id_folha = '$id_folha' ) as folha
                                    LEFT JOIN curso AS C ON (IF(folha.para IS NOT NULL,C.id_curso=folha.para, IF(folha.de IS NOT NULL,C.id_curso=folha.de,C.id_curso=folha.id_curso_clt)))
                                    ORDER BY folha.nome") or die(mysql_error());

?>
<html>
    <head>
        <title>Folha Analítica</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="../../net1.css" rel="stylesheet" type="text/css">      
         <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
            <script src="../../js/highslide-with-html.js" type="text/javascript"></script>
            <script src="../../js/global.js" type="text/javascript"></script>
       <style media="print">
            form{ visibility: hidden;}
            .link_voltar{ visibility: hidden;}     
            .negrito{ font-weight: bold;}
             table tr td{ font-size: 9px;}
              
        </style>
        <style>          
            .grid{margin-bottom: 10px;}
        </style>
    </head>
    <body class="novaintra" >       
        <div id="content" style="width:800px;" >
            <div id="head">
               <div class="link_voltar"><a href="<?php echo $link_voltar;?>" title="Voltar para a folha"> <img src="../../imagens/back.png" width="30" height="30"/> </a> </div>
               <img src="../../imagens/logomaster<?php echo $id_master; ?>.gif" class="fleft" style="margin-right: 25px;" width="140" height="100"/>
                <div class="fleft">
                    <h2>Folha Analítica</h2>  
                    <p><strong><?php echo $row_folha['nome_projeto']?></strong></p>
                    <p><strong><?php echo mesesArray($row_folha['mes']).'/'.$row_folha['ano'];?></strong></p>
                    <p><strong>Folha:</strong> <?php echo $row_folha['id_folha'];?></p>
                    <p><strong>Período:</strong> <?php echo $row_folha['data_inicio'];?> a  <?php echo $row_folha['data_fim'];?> </p>
                </div>
            </div>
            <br class="clear">
            <br>
           <?php          
            if(mysql_num_rows($qr_folha_proc) == 0 ){
            }else {
          

                while($folha = mysql_fetch_assoc($qr_folha_proc)){                    
                    
                    
                    if(in_array($folha['status_clt'],array(60,61,62,81,63,101,64,65,66 ))){                 
                        include('folha_teste_rescisao.php');                 
                    }elseif(!in_array($folha['status_clt'],array(60,61,62,81,63,101,64,65,66 )) ){
                        include('folha_teste_em_atividade.php');
             } 
             ?>            
            
           
<table border="0" cellpadding="0" cellspacing="0" class="grid essatb" width="600" id="tabela" style="font-size: 11px;" align="center">              
                <tr>
                    <td colspan="4"><strong>COD.</strong> <?php echo $folha['id_clt'];?></td>
                </tr>
                <tr>
                    <td colspan="2"><strong>NOME:</strong> <?php echo $folha['nome']?></td>
                    <td colspan="2"><strong>FUNÇÃO:</strong> <?php echo $folha['funcao']?></td>
                </tr>
                 <tr>
                   <td colspan="2" style="background-color:  transparent; text-align: center;"> <strong>PROVENTOS</strong></td>
                   <td colspan="2" style="background-color:   transparent; text-align: center;"> <strong>DESCONTOS</strong></td>
                </tr> 
                  
                <tr style="text-align: center;font-size: 11px;">
                    <td colspan="2">
                        
                        <table style="font-size: 11px;" width="100%"> 
                            <?php
                             foreach (array_reverse($movimentos_folha[$folha['id_clt']]['CREDITO']) as $nome => $valor){
                                 if($valor == 0)continue;
                                 $totalizador_proventos += $valor;
                                 $total_rend_clt += $valor;
                            ?>
                            <tr>
                                <td style="border:0;"><?php echo $nome?></td>
                                <td style="border:0;">R$ <?php echo number_format($valor,2,',','.')?></td>
                            </tr>
                            <?php } ?>    
                        </table>
                        
                    </td>
                    <td valign="top" colspan="2">
                        
                        <table style="font-size: 11px;" width="100%"> 
                            <?php
                            ///DEDUÇÕES
                             foreach ($movimentos_folha[$folha['id_clt']]['DEBITO'] as $nome => $valor){
                                 if($valor == 0)continue;
                                 $totalizador_deducoes += $valor;
                                 $total_desco_clt       += $valor;
                            ?>
                            <tr>
                                <td style="border:0;"><?php echo $nome?></td>
                                <td style="border:0;">R$ <?php echo number_format($valor,2,',','.')?></td>
                            </tr>
                            <?php } ?>        
                        </table>
                        
                    </td>
                </tr>
                
                 <tr style="text-align: center;">
                    <td colspan="2">
                         TOTAL: R$ <?php echo number_format($total_rend_clt,2,',','.'); ?>
                    </td>
                     <td colspan="2">
                         TOTAL: R$ <?php echo number_format($total_desco_clt,2,',','.'); ?>
                     </td> 
                </tr>
                
                <?php
                 $total_liquido =(($total_rend_clt - $total_desco_clt) < 0)? 0: $total_rend_clt - $total_desco_clt;
                 $totalizador_liquido += $total_liquido;                
                ?>                
               
                <tr>
                    <td><strong>BASE INSS:</strong><BR> R$ <?php echo number_format($movimentos_folha[$folha['id_clt']]['BASE INSS'],2,',','.');?></td>
                     <td><strong>BASE IRRF:</strong> <BR>R$ <?php echo number_format($movimentos_folha[$folha['id_clt']]['BASE IRRF'],2,',','.');?></td>
                     <td><strong>BASE FGTS:</strong> <BR> R$ <?php echo number_format($movimentos_folha[$folha['id_clt']]['BASE FGTS'],2,',','.');?></td>
                    <td><strong>VALOR LÍQUIDO:</strong><br> <?php echo 'R$ '.number_format($total_liquido,2,',','.');?> </td>
                </tr>
          </table>
            
             <?php
             $base_inss_total +=$movimentos_folha[$folha['id_clt']]['BASE INSS'];
            unset($gratificacoes,$movimentos,$total_desco_clt,$total_rend_clt,$faltas,$reembolso_vt,$diferenca_salarial,$base,$gratificacao); 
            }
           
            }
            echo $base_inss_total;
         
           ?>
              <div class="clear"></div>
        </div>
         
</body>
</html>