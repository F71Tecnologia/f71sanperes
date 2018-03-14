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
 $meses[13] = "13º Salário - 1ª parcela";
 $meses[14] = "13º Salário - 2ª parcela";
 $meses[15] = "13º Salário - Integral";

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


switch ($mes){
    case 13: $sql_mes     = "AND A.terceiro = 1 AND A.tipo_terceiro = 1";
         
        break;
    case 14: $sql_mes = "AND A.terceiro = 1 AND A.tipo_terceiro = 2";
            
        break;
    case 15: $sql_mes = "AND A.terceiro = 1 AND A.tipo_terceiro = 3";
            
        break;
    default: $sql_mes = "AND  A.mes = $mes AND A.terceiro != 1";
            
}
//Pengand o nome dos movimentos de folha
$qr_mov = mysql_query("SELECT * FROM rh_movimentos WHERE mov_lancavel = 1");
while($row_mov = mysql_fetch_assoc($qr_mov))
{
    
    if($row_mov['categoria'] == 'CREDITO'){
        $NOMES_CREDITO[] = $row_mov['descicao'];
    }   else {
        $NOMES_DEBITO[] = $row_mov['descicao'];
    }
}



//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
$qr_trabalhadores = mysql_query(" SELECT B.id_clt as id_trab, 'clt' as tipo_contrato, B.nome, A.id_folha,A.regiao,A.projeto
                                    FROM rh_folha as A
                                    INNER JOIN rh_folha_proc as B
                                    ON A.id_folha = B.id_folha
                                    WHERE   A.status = 3 AND B.status = 3 $sql_mes  AND A.ano = $ano
                                   AND id_regiao = $regiao AND id_projeto = $projeto
                                        
                                    UNION
                                    
                                    SELECT A.id_autonomo AS id_trab, 'rpa' as tipo_contrato, B.nome, '', B.id_regiao as regiao , B.id_projeto as projeto
                                    FROM rpa_autonomo as A
                                    INNER JOIN autonomo as B
                                    ON A.id_autonomo = B.id_autonomo
                                    INNER JOIN rpa_saida_assoc as C
                                    ON C.id_rpa = A.id_rpa
                                    INNER JOIN saida as D
                                    ON D.id_saida = C.id_saida
                                    WHERE MONTH(data_geracao) = $mes AND YEAR(A.data_geracao) = $ano AND D.status = 2 AND C.tipo_vinculo = 1 
                                     AND B.id_regiao = $regiao AND B.id_projeto = $projeto
                                    ORDER BY TRIM(nome)") or die(mysql_error());



while($row_trab = mysql_fetch_assoc($qr_trabalhadores)){
    
  
    
    if($row_trab['tipo_contratacao'] == 'rpa'){
        
     $FOLHA[]
        
        
        
        
    } else {
        
        
        
        
        
        
    }
 
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}

exit;









 
$qr_relatorio = mysql_query("
                SELECT clt.*, C.id_curso, C.nome as nome_curso, C.hora_mes
                    FROM 

                            (
                            SELECT B.id_clt, B.nome, A.id_folha, B.id_regiao, A.mes, A.ano,C.id_curso,B.sallimpo,C.nome as nome_clt, A.ids_movimentos_estatisticas, 
                            B.salliquido, B.a5019 as contri_sindical, B.a5020 as inss, B.a5021 as irrf, B.a5022 as sal_familia, B.valor_dt, B.inss_dt,B.ir_dt,
                            B.a6005 as sal_maternidade,B.a7001 as desc_vt,B.a8003 as vale_refeicao,
                           (SELECT id_curso_de FROM rh_transferencias WHERE id_clt=B.id_clt AND id_curso_de <> id_curso_para AND data_proc >= '{$dt_referencia}' ORDER BY id_transferencia ASC LIMIT 1) AS de,
                           (SELECT id_curso_para FROM rh_transferencias WHERE id_clt=B.id_clt AND id_curso_de <> id_curso_para AND data_proc <= '{$dt_referencia}' ORDER BY id_transferencia DESC LIMIT 1) AS para,
                           C.cpf,
                           IF(MONTH(C.data_entrada) = A.mes, B.sallimpo_real, B.sallimpo) as salario_folha


                             FROM rh_folha as A
                            INNER JOIN rh_folha_proc as B
                            ON A.id_folha = B.id_folha
                            INNER JOIN rh_clt as C
                            ON C.id_clt = B.id_clt
                            WHERE   A.ano = $ano   $sql_mes AND A.status = 3  AND B.status = 3 ) as clt

                            LEFT JOIN curso AS C ON (IF(clt.para IS NOT NULL,C.id_curso=clt.para, IF(clt.de IS NOT NULL,C.id_curso=clt.de,C.id_curso=clt.id_curso)))
                WHERE C.id_curso IN(6,8,14,20,21,22,30,32,38,44,45,46,54,56,62,68,69,70,78,80,86,92,93,94,102,104,110,116,117,118,162,168,170,171,172,173,174,175,180,222,224,230,236,237,238,244,260,262,268,274,275,276,282,292,
                                                                                                                  294,300,306,307,308,314,324,326,332,338,339,340,346,356,358,364,370,371,372,378,388,390,396,402,403,404,410,451,453,459,465,466,467,473)        
                        ORDER BY clt.id_folha,clt.nome
                        ") or die(mysql_error());

                
                           
while($row_rel = mysql_fetch_assoc($qr_relatorio)){

    $id_folha = $row_rel['id_folha'];
    $id_clt   = $row_rel['id_clt'];
    $nome_clt = $row_rel['nome_clt'];

    if($id_folha != $folhaANT){
        $FOLHAS[]         = $id_folha;
    }
    $folhaANT = $id_folha;
    
    
    $CLT[$id_folha][] = $id_clt;
  
    
    
    $DADOS_CLT[$id_folha][$id_clt]['NOME_CLT']    = $row_rel['nome_clt'];
    $DADOS_CLT[$id_folha][$id_clt]['CPF']         = $row_rel['cpf'];
    $DADOS_CLT[$id_folha][$id_clt]['FUNÇÃO']      = $row_rel['nome_curso'];
    $DADOS_CLT[$id_folha][$id_clt]['SALÁRIO']     = $row_rel['salario_folha'];
    $DADOS_CLT[$id_folha][$id_clt]['HORA MÊS']    = $row_rel['hora_mes'];
    
    
    $MOVIMENTOS[$id_folha][$id_clt]['CREDITO']['CONTRIBUIÇÃO SINDICAL']     = $row_rel['contri_sindical'];
    $MOVIMENTOS[$id_folha][$id_clt]['DEBITO']['DESCONTO VALE TRANSPORTE']  = $row_rel['a7001'];
    $MOVIMENTOS[$id_folha][$id_clt]['DEBITO']['INSS']                      = $row_rel['inss'];
    $MOVIMENTOS[$id_folha][$id_clt]['DEBITO']['IRRF']                      = '300';//$row_rel['irrf'];
    
    
    //VERIFICANDO RESCISÃO
    $qr_rescisao =  mysql_query("SELECT *
                            FROM rh_recisao 
                            WHERE id_clt = '$row_rel[id_clt]' 
                             AND MONTH(data_demi) = $mes AND YEAR(data_demi) = $ano AND status = 1");  
    
    
  if(mysql_num_rows($qr_rescisao) == 0){
    

        ///////////////////////////////
        /////  MOVIMENTOS  DE FOLHA////
        ///////////////////////////////
       $qr_movimentos = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_movimento IN($row_rel[ids_movimentos_estatisticas])");
       while($row_mov =@ mysql_fetch_assoc($qr_movimentos)){    

           if($ano == 2012 and $row_mov['id_mov'] == 56 and $row_mov['valor_movimento'] == 135.60){
               $valor = 124.40;
           } else {
               $valor = $row_mov['valor_movimento'];
           }
           $tipo_movimento = ($row_mov['tipo_movimento'] == 'DEBITO' or $row_mov['tipo_movimento'] == 'DESCONTO')? 'DEBITO':'CREDITO';

           $MOVIMENTOS[$id_folha][$id_clt][$tipo_movimento][$row_mov['nome_movimento']] = $valor; 
       }

}




 //////////////////////
 /////  RESCISÂO ////
 ////////////////////
while($row_resc = mysql_fetch_assoc($qr_rescisao)){
    
    
            // Aviso Prévio
            if($row_resc['fator'] == 'empregado' and $row_resc['aviso'] == 'indenizado') {
                    $aviso_previo_debito  = $row_resc['aviso_valor'];
            } else {
                    $aviso_previo_credito = $row_resc['aviso_valor'];
            }

            
    
            // Multa de Atraso
            if($row_resc['motivo'] != '63' and $row_resc['motivo'] != '64' and $row_resc['motivo'] != '65') {
                    $multa_479 = $row_resc['a479'];
            } else {
                    $multa_480 = $row_resc['a479'];
            }
    
                //MOVIMENTOS RESCISÃO
                $qr_movimento = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_clt = '$row_resc[id_clt]' AND mes_mov = 16 AND status = 1");
                while($row_movimento = mysql_fetch_assoc($qr_movimento)){


                    switch($row_movimento['id_mov']){

                         case 14:
                         case 206: $diferenca_salarial =  $row_movimento['valor_movimento'];
                            break;

                             case 192:
                             case 228:
                             case 201:
                             case 202:
                             case 203:
                             case 197:
                             case 196: $gratificacoes +=  $row_movimento['valor_movimento'];            
                            break;
                            case 198: $reembolso_vt = $row_movimento['valor_movimento'];
                        case 62: $faltas = $row_movimento['valor_movimento'];
                         break;

                     case 208: $pagamento_indevido = $row_movimento['valor_movimento'];
                         break;

                     case 231: $reembolso_vale_vt_debito = $row_movimento['valor_movimento'];
                         break;
                      case 232: $acerto_salario = $row_movimento['valor_movimento'];
                          break;

                      case 54: $pensao_alimenticia =  $row_movimento['valor_movimento'];
                               $percentual_pensao = '15%';
                          break;
                      case 63: $pensao_alimenticia =  $row_movimento['valor_movimento'];
                               $percentual_pensao = '20%';
                          break;
                         case 244: $adiantamento_ferias = $row_movimento['valor_movimento'];
                             break;
                  
                    }
                }
       
    
    //PROVENTOS
    $MOVIMENTOS[$id_folha][$id_clt]['CREDITO']['SALDO DE SALÁRIO']              = $row_resc['saldo_salario'];
     $MOVIMENTOS[$id_folha][$id_clt]['CREDITO']['GRATIFICAÇÕES']                 = $gratificacoes;
     $MOVIMENTOS[$id_folha][$id_clt]['CREDITO']['ADICIONAL DE INSALUBRIDADE']    = $row_resc['insalubridade'];
     $MOVIMENTOS[$id_folha][$id_clt]['CREDITO']['ADICIONAL DE PERICULOSIDADE']   = $row_resc['periculosidade'];
     $MOVIMENTOS[$id_folha][$id_clt]['CREDITO']['ADICIONAL NOTURNO']             = $row_resc['adicional_noturno'];
     $MOVIMENTOS[$id_folha][$id_clt]['CREDITO']['HORAS EXTRAS']                  = $row_resc['extra'];
     $MOVIMENTOS[$id_folha][$id_clt]['CREDITO']['GORJETAS']                      = $row_resc['gorjetas'];
     $MOVIMENTOS[$id_folha][$id_clt]['CREDITO']['DSR']                           = $row_resc['dsr'];
     $MOVIMENTOS[$id_folha][$id_clt]['CREDITO']['INDENIZAÇÃO ART.477']           = $row_resc['a477'];
     $MOVIMENTOS[$id_folha][$id_clt]['CREDITO']['INDENIZAÇÃO ART.479']           = $multa_479;
     $MOVIMENTOS[$id_folha][$id_clt]['CREDITO']['SALÁRIO FAMÍLIA']               = $row_resc['sal_familia'];
     $MOVIMENTOS[$id_folha][$id_clt]['CREDITO']['13º SALÁRIO PROPORCIONAL']       = $row_resc['dt_salario'];
     $MOVIMENTOS[$id_folha][$id_clt]['CREDITO']['FÉRIAS PROPORCIONAIS']          = $row_resc['ferias_pr'];
     $MOVIMENTOS[$id_folha][$id_clt]['CREDITO']['FÉRIAS VENCIDAS']               = $row_resc['ferias_vencidas'];
     $MOVIMENTOS[$id_folha][$id_clt]['CREDITO']['1/3 DE FÉRIAS NA RESCISÃO']     = $row_resc['umterco_fv'] + $row_resc['umterco_fp'];
     $MOVIMENTOS[$id_folha][$id_clt]['CREDITO']['AVISO PRÉVIO INDENIZADO']       = $aviso_previo_credito;
     $MOVIMENTOS[$id_folha][$id_clt]['CREDITO']['13º SALÁRIO INDENIZADO']        = $row_resc['terceiro_ss'];
     $MOVIMENTOS[$id_folha][$id_clt]['CREDITO']['FÉRIAS INDENIZADAS']            = $row_resc['um_avo_ferias_indenizadas'];
     $MOVIMENTOS[$id_folha][$id_clt]['CREDITO']['FÉRIAS EM DOBRO']               = $row_resc['fv_dobro'];
     $MOVIMENTOS[$id_folha][$id_clt]['CREDITO']['1/3 FÉRIAS EM DOBRO']           = $row_resc['um_terco_ferias_dobro'];
     $MOVIMENTOS[$id_folha][$id_clt]['CREDITO']['DIFERENÇA SALARIAL']            = $diferenca_salarial;
     $MOVIMENTOS[$id_folha][$id_clt]['CREDITO']['AJUDA DE CUSTO']                = $row_resc['ajuda_custo'];
     $MOVIMENTOS[$id_folha][$id_clt]['CREDITO']['LEI 12.506']                    = $row_resc['qnt_dias_lei_12_506'];
     $MOVIMENTOS[$id_folha][$id_clt]['CREDITO']['ACERTO DE SALÁRIO']             = $acerto_salario;
     $MOVIMENTOS[$id_folha][$id_clt]['CREDITO']['REEMBOLSO VALE TRANSPORTE']     = $reembolso_vt;
     $MOVIMENTOS[$id_folha][$id_clt]['CREDITO']['AJUSTE DO SALDO DEVEDOR']       = $row_resc['arredondamento_positivo'];
    
     //DESCONTOS
     
     $MOVIMENTOS[$id_folha][$id_clt]['DEBITO']['PENSÃO ALIMENTÍCIA']             = $pensao_alimenticia;
     $MOVIMENTOS[$id_folha][$id_clt]['DEBITO']['ADIANTAMENTO SALARIAL']          = $row_resc['adiantamento'];
     $MOVIMENTOS[$id_folha][$id_clt]['DEBITO']['ADIANTAMENTO DE 13ª SALARIO']    = $row_resc['adiantamento_13'];
     $MOVIMENTOS[$id_folha][$id_clt]['DEBITO']['AVISO PRÉVIO INDENIZADO']        = $aviso_previo_debito;
     $MOVIMENTOS[$id_folha][$id_clt]['DEBITO']['INDENIZAÇÃO ART. 480']           = $multa_480;
     $MOVIMENTOS[$id_folha][$id_clt]['DEBITO']['VALE TRANSPORTE']                = $row_resc['desconto_vale_transporte'];
     $MOVIMENTOS[$id_folha][$id_clt]['DEBITO']['INSS']                           = $row_resc['inss_ss'];
     $MOVIMENTOS[$id_folha][$id_clt]['DEBITO']['INSS SOBRE 13º SALÁRIO']         = $row_resc['inss_dt'];
     $MOVIMENTOS[$id_folha][$id_clt]['DEBITO']['IRRF']                           = $row_resc['ir_ss'];
     $MOVIMENTOS[$id_folha][$id_clt]['DEBITO']['IRRF SOBRE 13º SALÁRIO']         = $row_resc['ir_dt'];
     $MOVIMENTOS[$id_folha][$id_clt]['DEBITO']['DEVOLUÇÃO DE CREDITO INDEVIDO']  = $row_resc['devolucao'] + $pagamento_indevido;
     $MOVIMENTOS[$id_folha][$id_clt]['DEBITO']['FALTAS']                         = $row_resc['valor_faltas'];
     $MOVIMENTOS[$id_folha][$id_clt]['DEBITO']['ADIANTAMENTO DE FÉRIAS']         = $adiantamento_ferias;
     $MOVIMENTOS[$id_folha][$id_clt]['DEBITO']['DESCONTO VALE TRANSPORTE']       = $reembolso_vale_vt_debito;
     $MOVIMENTOS[$id_folha][$id_clt]['DEBITO']['OUTROS']                         = $row_desconto['valor_movimento'] + $row_rescisao['outros'];
   
    
    
}

                
    
    
    
    

}                           

  
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
            
    <?php if(mysql_num_rows($qr_relatorio) !=0 and isset($_POST['gerar'])){         
        
         $qr_reg= mysql_query("SELECT * FROM regioes WHERE id_regiao = $regiao");
         $row_reg = mysql_fetch_assoc($qr_reg);
        ?>
            
            <p style="text-align: left; padding-left: 30px;"><input type="button" onclick="tableToExcel('folha', 'Folha')" value="Exportar para Excel" class="exportarExcel"></p>  
            <p class="separador"><strong>Região: </strong><?php echo $row_reg['regiao']?></p>
            <p class="separador"><strong>Competência:</strong> <?php echo mesesArray($mes)?>/ <?php echo $ano; ?></p>
            
           
                 <?php    
                 $contador =0;
                     
                 if(!empty($movimentos[$row_rel['id_clt']]['DEBITO']['DESCONTO VALE TRANSPORTE'])) {
                     
                     $vale_transporte = $movimentos[$row_rel['id_clt']]['DEBITO']['DESCONTO VALE TRANSPORTE'];
                 }elseif(!empty($row_rel['a7001'])){
                     
                     $vale_transporte = $row_rel['a7001'];
                 }
                 
                 
                 
                 
           foreach(array_unique($FOLHAS) as $id_folha){
               
               
               print_R($id_folha);
           
               foreach($CLT[$id_folha] as $id_clt){
                   
                   foreach($MOVIMENTOS[$id_folha][$id_clt]['CREDITO'] as $nome_movimento => $valor_movi){
                       
                       $nome_credito[] =  $nome_movimento;                   }
                   
               }               
           }
           echo '<pre>';
                print_r(array_unique($nome_credito));
              //print_r($FOLHA);
          echo '</pre>';

         

               ?>
               
                 <table border="0" cellpadding="0" cellspacing="0" class="grid essatb" width="100%" style="page-break-after:auto;"  id="folha"> 
                
                        <tr rowspan="2"  class="titulo">
                            <td></td>
                            <td>NOME</td>
                            <td>CPF</td>
                            <td>CARGO</td> 
               
                        </tr>
                        
               
                 </table>
               <?php
               
               
          // }
      
                
    }
                ?>  
            <div class="clear"></div>
        </div>
  

</body>
</html>