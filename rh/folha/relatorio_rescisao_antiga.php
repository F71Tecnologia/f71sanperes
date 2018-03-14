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
                        DATE_FORMAT(A.data_fim,'%d/%m/%Y') as data_fim, C.nome_mes, A.ids_movimentos_estatisticas
                        FROM rh_folha as A 
                        INNER JOIN projeto as B
                        ON B.id_projeto = A.projeto
                        INNER JOIN ano_meses as C
                        ON C.num_mes = A.mes
                        WHERE A.id_folha = '$id_folha'") or die(mysql_error()) ;
$row_folha = mysql_fetch_assoc($qr_folha);   

 $qr_folha_proc = mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = '$id_folha' AND status_clt  IN(60,61,62,81,63,101,64,65,66)");


?>
<html>
    <head>
        <title>Gerar IRRF</title>
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
        <div id="content" style="width:3000px;">
            <div id="head">
               <div class="link_voltar"><a href="<?php echo $link_voltar;?>" title="Voltar para a folha"> <img src="../../imagens/back.png" width="30" height="30"/> </a> </div>
               <img src="../../imagens/logomaster<?php echo $id_master; ?>.gif" class="fleft" style="margin-right: 25px;" width="140" height="100"/>
                <div class="fleft">
                    <h2>Relatório de Rescisões</h2>  
                    <p><strong><?php echo $row_folha['mes'];?></strong></p>
                    <p><strong>Folha:</strong> <?php echo $row_folha['id_folha'];?></p>
                    <p><strong>Período:</strong> <?php echo $row_folha['data_inicio'];?> a  <?php echo $row_folha['data_fim'];?> </p>
                </div>
            </div>
            <br class="clear">
            <br/>
            
            
            
           <?php          
            if(mysql_num_rows($qr_folha_proc) == 0 ){
            }else {
           ?>
        <p style="text-align: right;"><input type="button" onclick="tableToExcel('tabela', 'Relatório de Rescões')" value="Exportar para Excel" class="exportarExcel"></p>
        
           <table border="0" cellpadding="0" cellspacing="0" class="grid essatb" width="100%" id="tabela">
               <tr>
                   <td colspan="2"></td>                   
                   <td colspan="20" aling="center" style="background-color:  #a0e1fd; text-align: center;"> <strong>PROVENTOS</strong></td>
                   <td colspan="16" aling="center" style="background-color:   #c29090; text-align: center;"> <strong>DESCONTOS</strong></td>
               </tr>
                <tr>
                    <td>COD.</td>
                    <td>NOME</td>
                    <td>SALDO DE SALÁRIO</td>
                     <td> FÉRIAS  <BR>PROPORCIONAIS</td>  
                     <td>FÉRIAS VENCIDAS</td>
                     <td> 1/3 CONSTITUCIONAL <br> DE FÉRIAS</td>    
                    <td> 13º SALÁRIO</td>                    
                    <td> 13º SALÁRIO <br>(Aviso-Prévio Indenizado)</td>                    
                    <td> AVISO PRÉVIO</td>
                    <td> GRATIFICAÇÕES</td>
                    <td> INSALUBRIDADE</td>
                    <td> ADICIONAL NOTURNO</td>
                    <td> HORAS EXRAS</td>
                    <td> DSR</td>
                    <td> MULTA ART. 477</td>
                     <td>MULTA ART. 479/CLT</td>
                     <td>SALÁRIO FAMÍLIA</td>
                     <td>DIFERENÇA SALARIAL</td>
                     <td>AJUDA DE CUSTO</td>
                     <td>VALE TRANSPORTE</td>
                     <td>VALE REFEIÇÃO</td>
                     <td> AJUSTE DE <BR>SALDO DEVEDOR</td>
                     <td class="negrito"><strong>TOTAL DE PROVENTOS</strong></td>
                     
                     <td>PENSÃO ALIMENTÍCA</td>
                     <td>ADIANTAMENTO SALARIAL</td>
                     <td>ADIANTAMENTO 13º TERCEIRO</td>
                     <td>MULTA ART.480</td>
                     <td>EMPRÉSTIMO EM CONSIGNAÇÃO</td>
                     <td>VALE TRANSPORTE</td>
                     <td>VALE ALIMENTAÇÃO</td>
                    <td>PREVIDÊNCIA SOCIAL <BR>   (INSS SALDO DE SALÁRIO)</td>
                    <td>IRRF <br>SALDO DE SALÁRIO</td>                  
                    <td>PREVIDÊNCIA SOCIAL 13º SALÁRIO <BR> (INSS 13º SALÁRIO) </td>
                    <td>IRRF <br> 13º SALÁRIO</td>                  
                    <td> AVISO PRÉVIO <br> PAGO PELO FUNCIONÁRIO</td>
                    <td>DEVOLUÇÃO DE CRÉDITO INDEVIDO</td>
                    <td>OUTROS</td>                 
                    <td>FALTAS</td>
                    <td><strong>TOTAL DE DESCONTOS</strong></td>
                    <td><strong>VALOR LÍQUIDO</strong></td>
                
                </tr>
            
             <?php 

                while($folha = mysql_fetch_assoc($qr_folha_proc)){

                    /////BUSCANDO A RECISÃO
                    $qr_recisao = mysql_query("SELECT *, DATE_FORMAT(data_proc, '%Y-%m-%d') as data_proc2,
                                               IF(motivo = 65, aviso_valor,'') as aviso_pg_funcionario,
                                               IF(motivo != 65, aviso_valor,'') as aviso_credito,
                                               IF(motivo = 64, a479,'') as multa_a479,
                                               IF(motivo = 63, a480,'') as multa_a480
                                               FROM rh_recisao WHERE id_clt = '$folha[id_clt]' and status = 1");
                    $rescisao  = mysql_fetch_assoc($qr_recisao);                 
                             
                    
                    //VERIFCANDO A TABELA DE MOVIMENTOS DA NOVA RESCISÃO
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
                       
                ?>
                <tr style="text-align: center;">
                    <td><?php echo $folha['id_clt']; ?></td>
                    <td><?php echo $folha['nome']; ?></td>
                    <td><?php echo verifica_campo($rescisao['saldo_salario']) ?></td>  
                    <td><?php echo verifica_campo($rescisao['ferias_pr']);?></td>
                    <td><?php echo verifica_campo($rescisao['ferias_vencidas']);?></td>
                    <td><?php echo verifica_campo($rescisao['umterco_fv'] + $rescisao['umterco_fp'])?></td>
                    <td><?php echo verifica_campo($rescisao['dt_salario']); ?></td>
                    <td><?php echo verifica_campo($rescisao['terceiro_ss'])?></td>
                   <td><?php echo verifica_campo($rescisao['aviso_credito']) ?></td>
                   <td><?php echo verifica_campo($gratificacao);?></td>
                   <td><?php echo verifica_campo($rescisao['insalubridade']); ?></td>
                   <td><?php echo verifica_campo($adicional_noturno);?></td>
                   <td><?php echo verifica_campo($hora_extra); ?></td>
                   <td><?php echo verifica_campo($dsr); ?></td>
                   <td><?php echo verifica_campo($rescisao['a477'])?></td>
                    <td><?php echo verifica_campo($rescisao['multa_a479'])?></td>
                    <td><?php echo verifica_campo($rescisao['salario_familia']); ?></td>
                    <td><?php echo verifica_campo($diferenca_salarial);?></td>
                    <td><?php echo verifica_campo($ajuda_custo); ?></td>
                    <td><?php echo verifica_campo($vale_transporte); ?></td>
                    <td><?php echo verifica_campo($vale_refeicao) ?></td>
                   <td><?php echo verifica_campo($rescisao['arredondamento_positivo']); ?></td>
                   <td><strong><?php echo verifica_campo($rescisao['total_rendimento']); ?> </strong></td>                   
                   <td><?php echo verifica_campo($pensao_alimenticia); ?></td>
                   <td><?php echo verifica_campo($adiantamento_salarial); ?></td>
                   <td></td>                 
                   <td><?php echo verifica_campo($rescisao['multa_a480']); ?></td>       
                   <td></td>
                   <td><?php echo verifica_campo($desconto_vale_transporte); ?></td>                
                   <td><?php echo verifica_campo($desconto_vale_alimentacao); ?></td>                
                     <td><?php echo verifica_campo($rescisao['inss_ss']); ?></td>
                     <td><?php echo verifica_campo($rescisao['ir_ss']); ?></td>
                     <td><?php echo verifica_campo($rescisao['inss_dt']);?></td>
                     <td><?php echo verifica_campo($rescisao['ir_dt']); ?></td>
                     <td><?php echo verifica_campo($rescisao['aviso_pg_funcionario']); ?></td>
                     <td><?php echo verifica_campo($rescisao['devolucao']); ?></td>
                     <td><?php echo verifica_campo($outros); ?></td>
                    <td><?php echo verifica_campo($rescisao['valor_faltas']); ?></td>
                    <td><strong><?php echo verifica_campo($rescisao['total_deducao']); ?></strong></td>
                    <td><strong><?php echo verifica_campo($rescisao['total_liquido']); ?></strong></td>                     
                </tr>
            <?php            
            unset($gratificacao,$movimentos);
            }
            ?>
                <tr style="text-align: center; font-weight: bold;">
                    <td colspan="2">TOTAIS:</td>
                    <td><?php echo number_format($totalizador_saldo_salario,2,',','.')?></td>
                    <td><?php echo  number_format($totalizador_ferias_pr,2,',','.');?></td>
                    <td><?php echo  number_format($totalizador_ferias_venc,2,',','.');?></td>
                    <td><?php echo  number_format($totalizador_um_terco,2,',','.');?></td>
                    <td><?php echo  number_format($totalizador_dt_salario,2,',','.');?></td>
                    <td><?php echo  number_format($totalizador_terceiro_ss,2,',','.');?></td>
                    <td><?php echo  number_format($totalizador_aviso_credito,2,',','.');?></td>
                    <td><?php echo  number_format($totalizador_gratificacao,2,',','.');?></td>
                    <td><?php echo  number_format($totalizador_insalubridade,2,',','.');?></td>
                    <td><?php echo  number_format($totalizador_adicional_noturno,2,',','.');?></td>
                    <td><?php echo  number_format($totalizador_hora_extra,2,',','.');?></td>
                    <td><?php echo  number_format($totalizador_dsr,2,',','.');?></td>
                    <td><?php echo  number_format($totalizador_a477,2,',','.');?></td>
                    <td><?php echo  number_format($totalizador_multa_a479,2,',','.');?></td>
                    <td><?php echo  number_format($totalizador_sal_familia,2,',','.');?></td>
                    <td><?php echo  number_format($totalizador_diferenca_salarial,2,',','.');?></td>
                    <td><?php echo  number_format($totalizador_ajuda_custo,2,',','.');?></td>
                    <td><?php echo  number_format($totalizador_vale_transporte,2,',','.');?></td>
                    <td><?php echo  number_format($totalizador_vale_refeicao,2,',','.');?></td>
                    <td><?php echo  number_format($totalizador_arredondamento_positivo,2,',','.');?></td>
                    <td><?php echo  number_format($totalizador_total_rendimentos,2,',','.');?></td>
                    <td><?php echo  number_format($totalizador_pensao_alimenticia,2,',','.');?></td>
                    <td><?php echo  number_format($totalizador_adiantamento_salarial,2,',','.');?></td>
                    <td></td>
                    <td><?php echo  number_format($totalizador_multa_a480,2,',','.');?></td>
                    <td></td>
                    <td><?php echo  number_format($totalizador_vale_transporte,2,',','.');?></td>
                    <td><?php echo  number_format($totalizador_vale_refeicao,2,',','.');?></td>
                    <td><?php echo  number_format($totalizador_inss_ss,2,',','.');?></td>
                    <td><?php echo  number_format($totalizador_ir_ss,2,',','.');?></td>
                    <td><?php echo  number_format($totalizador_inss_dt,2,',','.');?></td>
                    <td><?php echo  number_format($totalizador_ir_dt,2,',','.');?></td>
                    <td><?php echo  number_format($totalizador_aviso_pg_funcionario,2,',','.');?></td>
                    <td><?php echo  number_format($totalizador_devolucao,2,',','.');?></td>
                    <td><?php echo  number_format($totalizador_outros,2,',','.');?></td>
                    <td><?php echo  number_format($totalizador_valor_faltas,2,',','.');?></td>
                    <td><?php echo  number_format($totalizador_total_deducao,2,',','.');?></td>
                    <td><?php echo  number_format($totalizador_total_liquido,2,',','.');?></td>
                </tr>    
                
            <?php
            echo '</table>';
            }
           ?>
            
        </div>

</body>
</html>