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
                        DATE_FORMAT(A.data_fim,'%d/%m/%Y') as data_fim, C.nome_mes, A.ids_movimentos_estatisticas, A.ano
                        FROM rh_folha as A 
                        INNER JOIN projeto as B
                        ON B.id_projeto = A.projeto
                        INNER JOIN ano_meses as C
                        ON C.num_mes = A.mes
                        WHERE A.id_folha = '$id_folha'") or die(mysql_error()) ;
$row_folha = mysql_fetch_assoc($qr_folha);   

 $qr_folha_proc = mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = '$id_folha' AND status_clt  IN(60,61,62,81,63,101,64,65,66) ");


?>
<html>
    <head>
        <title>Relat�rio de Rescis�es</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="../../net1.css" rel="stylesheet" type="text/css">
      
         <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
            <script src="../../js/highslide-with-html.js" type="text/javascript"></script>
            <script src="../../js/global.js" type="text/javascript"></script>
       <style media="print">
            form{ visibility: hidden;}
            .link_voltar{ visibility: hidden;}     
            .negrito{ font-weight: bold;
                
}
            
        </style>
    </head>
    <body class="novaintra" >       
        <div id="content" >
            <div id="head">
               <div class="link_voltar"><a href="<?php echo $link_voltar;?>" title="Voltar para a folha"> <img src="../../imagens/back.png" width="30" height="30"/> </a> </div>
               <img src="../../imagens/logomaster<?php echo $id_master; ?>.gif" class="fleft" style="margin-right: 25px;" width="140" height="100"/>
                <div class="fleft">
                    <h2>Relat�rio de Rescis�es</h2>  
                    <p><strong><?php echo $row_folha['mes'];?></strong></p>
                    <p><strong>Folha:</strong> <?php echo $row_folha['id_folha'];?></p>
                    <p><strong>Per�odo:</strong> <?php echo $row_folha['data_inicio'];?> a  <?php echo $row_folha['data_fim'];?> </p>
                </div>
            </div>
            <br class="clear">
            <br>
           <?php          
            if(mysql_num_rows($qr_folha_proc) == 0 ){
            }else {
           ?>
        <p style="text-align: right;"><input type="button" onclick="tableToExcel('tabela', 'Relat�rio de Resc�es')" value="Exportar para Excel" class="exportarExcel"></p>
        
           <table border="0" cellpadding="0" cellspacing="0" class="grid essatb" width="100%" id="tabela">              
                <tr>
                    <td>COD.</td>
                    <td>NOME</td>
                   <td  aling="center" style="background-color:  #a0e1fd; text-align: center;"> <strong>PROVENTOS</strong></td>
                   <td  aling="center" style="background-color:   #c29090; text-align: center;"> <strong>DESCONTOS</strong></td>
                   <td><strong>VALOR L�QUIDO</strong></td>
                
                </tr>
            
             <?php 

                while($folha = mysql_fetch_assoc($qr_folha_proc)){
                    
                    
                    /////BUSCANDO A RECIS�O
                    $qr_recisao = mysql_query("SELECT *, DATE_FORMAT(data_proc, '%Y-%m-%d') as data_proc2,
                                               IF(motivo = 65, aviso_valor,'') as aviso_pg_funcionario,
                                               IF(motivo != 65, aviso_valor,'') as aviso_credito,
                                               IF(motivo = 64, a479,'') as multa_a479,
                                               IF(motivo = 63, a480,'') as multa_a480,
                                               YEAR(data_demi) as ano_demissao
                                               FROM rh_recisao WHERE id_clt = '$folha[id_clt]' and status = 1");
                    $rescisao  = mysql_fetch_assoc($qr_recisao);                 
                             
                    if($rescisao['ano_demissao'] != $row_folha['ano']) continue;
                    //VERIFCANDO A TABELA DE MOVIMENTOS DA NOVA RESCIS�O
                            if(substr($rescisao['data_proc2'],0,10) >= '2013-04-04'){

                                    $qr_movimentos = mysql_query("SELECT B.descicao, B.id_mov, A.valor, B.campo_rescisao
                                    FROM rh_movimentos_rescisao as A 
                                    INNER JOIN
                                    rh_movimentos as B
                                    ON A.id_mov = B.id_mov
                                    WHERE A.id_clt = '$rescisao[id_clt]' 
                                    AND A.id_rescisao = '$rescisao[id_recisao]' 
                                    AND A.status = 1") or die(mysql_error());
                                    while($row_movimentos = mysql_fetch_assoc($qr_movimentos)){  

                                            $movimentos[$row_movimentos['campo_rescisao']] += $row_movimentos['valor'];   
                                    }
                                
                                
                                $gratificacao       = $movimentos[52];     
                                $adicional_noturno  = $movimentos[55];
                                $hora_extra         = $movimentos[56];
                                $dsr                = $movimentos[58];
                                $diferenca_salarial = $movimentos[80];
                                $ajuda_custo        = $movimentos[82];
                                $vale_transporte    = $movimentos[107];
                                $vale_refeicao      = $movimentos[108];                                
                                $pensao_alimenticia    = $movimentos[100];
                                $adiantamento_salarial = $movimentos[101];
                                $desconto_vale_transporte = $movimentos[106];
                                $desconto_vale_alimentacao = $movimentos[109];
                                $outros                    = $movimentos[115];
                                $faltas                   = $movimentos[117];
                             
                                
                           
                            } else {
                                
                            

                                $qr_movimento = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_clt = '$id_clt' AND mes_mov = 16 AND status = 1");
                                while($row_movimento = mysql_fetch_assoc($qr_movimento)){      
                                    switch($row_movimento['id_mov']){

                                        case 198: $vale_transporte = $row_movimento['valor_movimento'];
                                            break;
                                        case 151: $vale_refeicao        = $row_movimento['valor_movimento'];
                                            break;
                                        case 14:  $diferenca_salarial = $row_movimento['valor_movimento'];
                                        break;
                                        case 76: $outros = $row_movimento['valor_movimento'];
                                            break;
                                    }
                                }  
                               
                                $gratificacao       =  $row_rescisao['gratificacao'];
                                $adicional_noturno  = $row_rescisao['adicional_noturno'];
                                $hora_extra         = $row_rescisao['hora_extra'];
                                $dsr                = $row_rescisao['dsr'];
                                $ajuda_custo        = $row_rescisao['ajuda_custo'];       
                                $desconto_vale_transporte = $row_rescisao['desconto_vale_transporte'];
                            }

                        ////////TOTALIZADORES
                        
                                       
                            
                       $totalizador_saldo_salario           += $rescisao['saldo_salario'];
                       $totalizador_ferias_pr               += $rescisao['ferias_pr'];
                       $totalizador_ferias_venc               += $rescisao['ferias_vencidas'];
                       $totalizador_um_terco                += $rescisao['umterco_fv'] + $rescisao['umterco_fp'];
                       $totalizador_dt_salario              += $rescisao['dt_salario'];
                       $totalizador_terceiro_ss             += $rescisao['terceiro_ss'];
                       $totalizador_aviso_credito           += $rescisao['aviso_credito'];
                       $totalizador_gratificacao            += $gratificacao;
                       $totalizador_insalubridade           += $rescisao['insalubridade'];
                       $totalizador_adicional_noturno       += $adicional_noturno;
                       $totalizador_hora_extra              += $hora_extra;
                       $totalizador_dsr                     += $dsr;
                       $totalizador_a477                    += $rescisao['a477'];
                       $totalizador_multa_a479              += $rescisao['multa_a479'];
                       $totalizador_sal_familia             += $salario_familia;
                       $totalizador_diferenca_salarial     += $diferenca_salarial;
                       $totalizador_ajuda_custo             += $ajuda_custo;
                       $totalizador_vale_transporte         += $vale_transporte;
                       $totalizador_vale_refeicao           += $vale_refeicao;
                       $totalizador_arredondamento_positivo     += $rescisao['arredondamento_positivo'];
                       $totalizador_total_rendimentos       += $rescisao['total_rendimento'];
                       $totalizador_pensao_alimenticia      += $pensao_alimenticia;
                       $totalizador_adiantamento_salarial    += $adiantamento_salarial;
                       $totalizador_multa_a480                  += $rescisao['multa_a480'];
                       $totalizador_desconto_vale_transporte    += $desconto_vale_transporte;
                       $totalizador_desconto_vale_alimentacao   += $desconto_vale_alimentacao;
                        $totalizador_inss_ss                    += $rescisao['inss_ss'];
                        $totalizador_ir_ss                      += $rescisao['ir_ss'];
                        $totalizador_inss_dt                    += $rescisao['inss_dt'];
                        $totalizador_ir_dt                    += $rescisao['ir_dt'];
                        $totalizador_aviso_pg_funcionario       += $rescisao['aviso_pg_funcionario'];
                        $totalizador_devolucao                  += $rescisao['devolucao'];
                        $totalizador_outros                   += $outros;
                        $totalizador_valor_faltas             += $rescisao['valor_faltas'];
                        $totalizador_total_deducao             += $rescisao['total_deducao'];
                        $totalizador_total_liquido             += $rescisao['total_liquido'];
                        
                        
                        
                        $array_proventos = array('SALDO DE SAL�RIO'                 => $rescisao['saldo_salario'],
                                            'F�RIAS PROPORCIONAIS'                  => $rescisao['ferias_pr'],
                                            'F�RIAS VENCIDAS'                       => $rescisao['ferias_vencidas'],
                                            '1/3 CONSTITUCIONAL <br> DE F�RIAS'     => $rescisao['umterco_fv'] + $rescisao['umterco_fp'],
                                            '13� SAL�RIO'                           => $rescisao['dt_salario'],
                                            '13� SAL�RIO (Aviso-Pr�vio Indenizado)' => $rescisao['terceiro_ss'],
                                            'AVISO PR�VIO'                          => $rescisao['aviso_credito'],
                                            'GRATIFICA��ES'                         => $gratificacao,
                                            'INSALUBRIDADE'                         => $rescisao['insalubridade'],
                                            'ADICIONAL NOTURNO'                     => $adicional_noturno,
                                            'HORAS EXTRAS'                          => $hora_extra,
                                            'DSR'                                   => $dsr,
                                            'MULTA ART. 477'                        => $rescisao['a477'],
                                            'MULTA ART. 479/CLT'                    => $rescisao['multa_a479'],
                                            'SAL�RIO FAM�LIA'                       => $rescisao['salario_familia'],
                                            'DIFEREN�A SALARIAL'                    => $diferenca_salarial,
                                            'AJUDA DE CUSTO'                        => $ajuda_custo,
                                            'VALE TRANSPORTE'                       => $vale_transporte,
                                            'VALE REFEI��O'                         => $vale_refeicao,
                                            'AJUSTE DE SALDO DEVEDOR'               => $rescisao['arredondamento_positivo'],
                                            'LEI 12.506 (AVISO PR�VIO)'               => $rescisao['lei_12_506'],
                                            
                                );
                        
                        $array_deducoes = array('PENS�O ALIMENT�CA'                             => $pensao_alimenticia,
                                                'ADIANTAMENTO SALARIAL'                         => $adiantamento_salarial,
                                                'ADIANTAMENTO 13� TERCEIRO'                     => '',
                                                'MULTA ART.480'                                 => $rescisao['multa_a480'],
                                                'EMPR�STIMO EM CONSIGNA��O'                     => '',
                                                'VALE TRANSPORTE'                               => $desconto_vale_transporte,
                                                'VALE ALIMENTA��O'                              => $desconto_vale_alimentacao,
                                                'PREVID�NCIA SOCIAL <br> (INSS SALDO DE SAL�RIO)' => $rescisao['inss_ss'],
                                                'IRRF <br>SALDO DE SAL�RIO'                     => $rescisao['ir_ss'],
                                                'AVISO PR�VIO <br> PAGO PELO FUNCION�RIO'       => $rescisao['aviso_pg_funcionario'],
                                                'PREVID�NCIA SOCIAL 13� SAL�RIO <BR> (INSS 13� SAL�RIO)' => $rescisao['inss_dt'],
                                                'IRRF <br> 13� SAL�RIO'                         => $rescisao['ir_dt'],
                                                'DEVOLU��O DE CR�DITO INDEVIDO'                 => $rescisao['devolucao'],
                                                'OUTROS'                                        => $outros,
                                                'FALTAS'                                        => $rescisao['valor_faltas']+ $faltas,
                            );
                        
                        
                        //TOTALIZADORES
                             $TOTALIZADORES['CREDITO']['SALDO DE SAL�RIO']                    += $array_proventos['SALDO DE SAL�RIO'];
                            $TOTALIZADORES['CREDITO']['F�RIAS PROPORCIONAIS']                  += $array_proventos['F�RIAS PROPORCIONAIS'];
                            $TOTALIZADORES['CREDITO']['F�RIAS VENCIDAS']                       += $array_proventos['F�RIAS VENCIDAS'];
                            $TOTALIZADORES['CREDITO']['1/3 CONSTITUCIONAL <br> DE F�RIAS']     += $array_proventos['1/3 CONSTITUCIONAL <br> DE F�RIAS'];
                            $TOTALIZADORES['CREDITO']['13� SAL�RIO']                           += $array_proventos['13� SAL�RIO'];
                            $TOTALIZADORES['CREDITO']['13� SAL�RIO (Aviso-Pr�vio Indenizado)'] += $array_proventos['13� SAL�RIO (Aviso-Pr�vio Indenizado)'];
                            $TOTALIZADORES['CREDITO']['AVISO PR�VIO']                          += $array_proventos['AVISO PR�VIO'];
                            $TOTALIZADORES['CREDITO']['GRATIFICA��ES']                         += $array_proventos['GRATIFICA��ES'];
                            $TOTALIZADORES['CREDITO']['INSALUBRIDADE']                         += $array_proventos['INSALUBRIDADE'];
                            $TOTALIZADORES['CREDITO']['ADICIONAL NOTURNO']                     += $array_proventos['ADICIONAL NOTURNO'];
                            $TOTALIZADORES['CREDITO']['HORAS EXTRAS']                          += $array_proventos['HORAS EXTRAS'];
                            $TOTALIZADORES['CREDITO']['DSR']                                   += $array_proventos['DSR'];
                            $TOTALIZADORES['CREDITO']['MULTA ART. 477']                        += $array_proventos['MULTA ART. 477'];
                            $TOTALIZADORES['CREDITO']['MULTA ART. 479/CLT']                    += $array_proventos['MULTA ART. 479/CLT'];
                            $TOTALIZADORES['CREDITO']['SAL�RIO FAM�LIA']                       += $array_proventos['SAL�RIO FAM�LIA'];
                            $TOTALIZADORES['CREDITO']['DIFEREN�A SALARIAL']                    += $array_proventos['DIFEREN�A SALARIAL'];
                            $TOTALIZADORES['CREDITO']['AJUDA DE CUSTO']                        += $array_proventos['AJUDA DE CUSTO'];
                            $TOTALIZADORES['CREDITO']['VALE TRANSPORTE']                       += $array_proventos['VALE TRANSPORTE'];
                            $TOTALIZADORES['CREDITO']['VALE REFEI��O']                         += $array_proventos['VALE REFEI��O'];
                            $TOTALIZADORES['CREDITO']['AJUSTE DE SALDO DEVEDOR']               += $array_proventos['AJUSTE DE SALDO DEVEDOR'];
                        
                            $TOTALIZADORES['DEBITO']['PENS�O ALIMENT�CA']                       += $array_deducoes['PENS�O ALIMENT�CA'];
                            $TOTALIZADORES['DEBITO']['ADIANTAMENTO SALARIAL']                   += $array_deducoes['ADIANTAMENTO SALARIAL'];
                            $TOTALIZADORES['DEBITO']['ADIANTAMENTO 13� TERCEIRO']               += $array_deducoes['ADIANTAMENTO 13� TERCEIRO'];
                            $TOTALIZADORES['DEBITO']['MULTA ART.480']                           += $array_deducoes['MULTA ART.480'];
                            $TOTALIZADORES['DEBITO']['EMPR�STIMO EM CONSIGNA��O']               += $array_deducoes['EMPR�STIMO EM CONSIGNA��O'];
                            $TOTALIZADORES['DEBITO']['VALE TRANSPORTE']                             += $array_deducoes['VALE TRANSPORTE'];
                            $TOTALIZADORES['DEBITO']['VALE ALIMENTA��O']                            += $array_deducoes['VALE ALIMENTA��O'];
                            $TOTALIZADORES['DEBITO']['PREVID�NCIA SOCIAL <br> (INSS SALDO DE SAL�RIO)'] += $array_deducoes['PREVID�NCIA SOCIAL <br> (INSS SALDO DE SAL�RIO)'];
                            $TOTALIZADORES['DEBITO']['IRRF <br>SALDO DE SAL�RIO']                       += $array_deducoes['IRRF <br>SALDO DE SAL�RIO'];
                            $TOTALIZADORES['DEBITO']['AVISO PR�VIO <br> PAGO PELO FUNCION�RIO']         += $array_deducoes['AVISO PR�VIO <br> PAGO PELO FUNCION�RIO'];
                            $TOTALIZADORES['DEBITO']['PREVID�NCIA SOCIAL 13� SAL�RIO <BR> (INSS 13� SAL�RIO)']      += $array_deducoes['PREVID�NCIA SOCIAL 13� SAL�RIO <BR> (INSS 13� SAL�RIO)'];
                            $TOTALIZADORES['DEBITO']['IRRF <br> 13� SAL�RIO']                       += $array_deducoes['IRRF <br> 13� SAL�RIO'];
                            $TOTALIZADORES['DEBITO']['DEVOLU��O DE CR�DITO INDEVIDO']               += $array_deducoes['DEVOLU��O DE CR�DITO INDEVIDO'];
                            $TOTALIZADORES['DEBITO']['OUTROS']                                      += $array_deducoes['OUTROS'];
                            $TOTALIZADORES['DEBITO']['FALTAS']                                      += $array_deducoes['FALTAS'];
                        
                ?>
                <tr style="text-align: center;">
                    <td><?php echo $folha['id_clt']; ?></td>
                    <td><?php echo $folha['nome']; ?></td>
                    <td>
                        <table> 
                            <?php
                             foreach ($array_proventos as $nome => $valor){
                                 if($valor == 0)continue;
                                 $totalizador_proventos += $valor;
                                 $total_rend_clt += $valor;
                            ?>
                            <tr>
                                <td style="border:0;"><?php echo $nome?></td>
                                <td style="border:0;">R$ <?php echo number_format($valor,2,',','.')?></td>
                            </tr>
                            <?php } ?>                           
                            <tr>
                                <td class="negrito" style="border:0;"><strong>TOTAL DE PROVENTOS</strong></td>
                                <td style="border:0;">R$ <?php echo number_format($total_rend_clt,2,',','.'); ?></td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table> 
                            <?php
                            ///DEDU��ES
                             foreach ($array_deducoes as $nome => $valor){
                                 if($valor == 0)continue;
                                 $totalizador_deducoes += $valor;
                                 $total_desco_clt       += $valor;
                            ?>
                            <tr>
                                <td style="border:0;"><?php echo $nome?></td>
                                <td style="border:0;">R$ <?php echo number_format($valor,2,',','.')?></td>
                            </tr>
                            <?php } ?>                           
                            <tr>
                                <td class="negrito" style="border:0;"><strong>TOTAL DE DEDU��ES</strong></td>
                                <td style="border:0;"> R$ <?php echo number_format($total_desco_clt,2,',','.'); ?></td>  
                            </tr>                         
                        </table>
                    </td>
                    <td><?php 
                        $total_liquido =(($total_rend_clt - $total_desco_clt) < 0)? 0: $total_rend_clt - $total_desco_clt;
                        $totalizador_liquido += $total_liquido;
                        echo 'R$ '.number_format($total_liquido,2,',','.');
                    ?>
                    </td>
                </tr> 
                
            <?php            
            unset($gratificacao,$movimentos,$total_desco_clt,$total_rend_clt);
            }
            ?>
                <tr style="text-align: center; font-weight: bold;">
                    <td colspan="2">TOTAIS:</td>
                    <td><?php echo number_format($totalizador_proventos,2,',','.');?></td>
                    <td><?php echo number_format($totalizador_deducoes,2,',','.')?></td>
                    <td><?php echo number_format($totalizador_liquido, 2,',','.')?></td>
                </tr>    
                
                <tr>
                    <td colspan="5">
                
                            <!----TOTALIZADORES-->  
                         <table border="0" cellpadding="0" cellspacing="0" class="grid essatb" width="40%" style="margin-top: 20px;">
                             <tr>
                                 <td colspan="3" style="text-align: center; background-color:  #cccccc;">TOTALIZADORES</td>
                             </tr>
                             <tr class="titulo">
                                 <td>NOME</td>
                                 <td>PROVENTOS</td>
                                 <td>DESCONTOS</td>
                             </tr>
                        <?php
                        foreach($TOTALIZADORES as $tipo_mov => $mov){

                          foreach($mov as $nome_mov => $valor){  

                              if(!empty($valor)){
                                  echo '<tr> 
                                            <td>'.$nome_mov.'</td>';  


                                    if($tipo_mov == 'CREDITO'){

                                        echo '<td align="center"> R$ '.number_format($valor,2,',','.').'</td>
                                              <td></td>';       
                                        $total_credito += $valor;
                                    }   else {

                                        echo '<td></td>
                                              <td align="center"> R$ '.number_format($valor,2,',','.').'</td>';  
                                    $total_debito += $valor;

                                    } 

                                  echo '</tr>';
                              }   
                          }

                        }
                        ?> 
                             <tr>
                                 <td></td>
                                 <td align="center">R$ <?php echo number_format($total_credito,2,',','.')?></td>
                                 <td align="center">R$ <?php echo number_format($total_debito,2,',','.')?></td>
                             </tr>
                        </table>      
                </td>
           </tr>  
              
           </table>    
              <?php } ?>
        </div>

</body>
</html>