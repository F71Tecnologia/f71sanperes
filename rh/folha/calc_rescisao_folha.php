<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a> ";
    exit;
}

include "../../conn.php";
include "../../wfunction.php";


$usuario = carregaUsuario();
$id_folha = $_REQUEST['folha'];
$tipo = $_REQUEST['tipo'];




$array_tipos = array(1 => 'VALOR RESCIS�O', 2 => 'VALOR PAGO NA RESCIS�O', 3 => 'INSS RESCIS�O', 4 => 'IRRF RESCIS�O');

$formulas = array(1 => '(Total de rendimentos - Saldo de sal�rio)',
                  2 => '(Total das dedu��es + Total l�quido) - (INSS + INSS 13�) + (IRRF + IRRF 13�)',
                  3 => '(INSS + INSS 13�)',
                  4 => '(IRRF + IRRF 13�)');


?>
<html>
    <head>
        <title>:: Intranet :: RH - Transfer�ncia de Unidade</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../favicon.ico" rel="shortcut icon" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt.js" type="text/javascript"></script>

        <script src="../../js/global.js" type="text/javascript"></script>      
     <style>
         .totalizador{ font-weight: bold;
                      color:   #666666; }
         .resultado{
             color:#000;
             
         }
     </style>   
    </head>
    
    <body id="page-rh-trans" class="novaintra">
        <div id="content">
         
          <?php 
                echo '<h3>'.$array_tipos[$tipo].'</h3>';
                echo '<p>'.$formulas[$tipo].'</p>';
                
                echo '  <table width="100%" cellspacing="0" cellpadding="0" class="grid" style="font-size:10px;">';
                //cabe�alhos
                switch($tipo){

                    case 1: echo '<thead>
                                    <th>ID_CLT</th>
                                    <th>NOME</th>
                                    <th>TOTAL DE RENDIMENTOS</th>
                                    <th>SALDO DE SAL�RIO</th>
                                    <th>VALOR RESCIS�O</th>    
                                </thead>';
                        break;
                    case 2: echo '<thead>
                                    <th>ID_CLT</th>
                                    <th>NOME</th>
                                    <th>TOTAL DE DEDU��O</th>
                                    <th>TOTAL L�QUIDO</th>
                                    <th>INSS</th>
                                    <th>INSS 13�</th>
                                    <th>IRRF</th>
                                    <th>IRRF 13� </th>    
                                    <th>VALOR PAGO NA RESCIS�O</th>    
                                </thead>';
                        break;

                    case 3:
                            echo '<thead>
                                    <th>ID_CLT</th>
                                    <th>NOME</th>
                                    <th>INSS </th>
                                    <th>INSS 13�</th>
                                    <th>INSS RESCIS�O</th>
                                    
                                </thead>';
                        break;
                    case 4:
                            echo '<thead>
                                    <th>ID_CLT</th>
                                    <th>NOME</th>
                                    <th>IRRF </th>
                                    <th>IRRF 13�</th>
                                    <th>IRRF RESCIS�O</th>
                                </thead>';
                        break; 
                }
            $qr_folha = mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = '$id_folha' AND status_clt != 10");
            while($folha = mysql_fetch_assoc($qr_folha)){

               $qr_recisao = mysql_query("SELECT * FROM rh_recisao WHERE id_clt = '$folha[id_clt]' ");
               $rescisao = mysql_fetch_assoc($qr_recisao);


               $valor_rescisao      = $rescisao['total_rendimento'] - $rescisao['saldo_salario'];
               $inss_total          = $rescisao['previdencia_ss'] + $rescisao['previdencia_dt'];
               $ir_total            = $rescisao['ir_ss'] + $rescisao['ir_dt'];
               $valor_pago_rescisao = ($rescisao['total_deducao'] + $rescisao['total_liquido']) - $inss_total - $ir_total ;


               ///TOTALIZADORES
               $totalizador_total_rendimentos   += $rescisao['total_rendimento'];
               $totalizador_total_deducao       += $rescisao['total_deducao'];
               $totalizador_total_liquido       += $rescisao['total_liquido'];
               $totalizador_saldo_salario       += $rescisao['saldo_salario'];
               $totalizador_inss                += $rescisao['previdencia_ss'];
               $totalizador_inss_dt             += $rescisao['previdencia_dt'];
               $totalizador_total_inss          += $inss_total;
               $totalizador_ir                  += $rescisao['ir_ss'];
               $totalizador_ir_dt               += $rescisao['ir_dt'];
                $totalizador_total_ir           += $ir_total;
               $totalizador_valor_rescisao      += $valor_rescisao;
               $totalizador_valor_pago_rescisao += $valor_pago_rescisao;

               switch($tipo){

                   case 1:
                            echo '<tr>
                                        <td>'.$folha['id_clt'].'</td>
                                        <td>'.$folha['nome'].'</td>
                                        <td align="center">'.number_format($rescisao['total_rendimento'],2,',','.').'</td>
                                        <td align="center">'.number_format($rescisao['saldo_salario'],2,',','.').'</td>
                                        <td align="center">'.number_format($valor_rescisao,2,',','.').'</td>
                                  </tr>';           
                       break;    
                   case 2:
                       echo '<tr>
                                        <td>'.$folha['id_clt'].'</td>
                                        <td>'.$folha['nome'].'</td>
                                        <td align="center">'.number_format($rescisao['total_deducao'],2,',','.').'</td>
                                        <td align="center">'.number_format($rescisao['total_liquido'],2,',','.').'</td>
                                        <td align="center">'.number_format($rescisao['previdencia_ss'],2,',','.').'</td>
                                        <td align="center">'.number_format($rescisao['previdencia_dt'],2,',','.').'</td>
                                        <td align="center">'.number_format($rescisao['ir_ss'],2,',','.').'</td>
                                        <td align="center">'.number_format($rescisao['ir_dt'],2,',','.').'</td>
                                        <td align="center">'.number_format($valor_pago_rescisao,2,',','.').'</td>                         
                                  </tr>';  

                       break;

                     case 3:
                            echo '<tr>
                                        <td>'.$folha['id_clt'].'</td>
                                        <td>'.$folha['nome'].'</td>
                                        <td align="center">'.number_format($rescisao['previdencia_ss'],2,',','.').'</td>
                                        <td align="center">'.number_format($rescisao['previdencia_dt'],2,',','.').'</td>
                                        <td align="center">'.number_format($inss_total,2,',','.').'</td>
                                  </tr>';           
                       break;     
                   
                    case 4:
                            echo '<tr>
                                        <td>'.$folha['id_clt'].'</td>
                                        <td>'.$folha['nome'].'</td>
                                        <td align="center">'.number_format($rescisao['ir_ss'],2,',','.').'</td>
                                        <td align="center">'.number_format($rescisao['ir_dt'],2,',','.').'</td>
                                        <td align="center">'.number_format($ir_total,2,',','.').'</td>
                                  </tr>';           
                       break;     
               }


            }



            ///EXIBINDO TOTALIZADORES
              switch($tipo){       
                   case 1:
                        echo '<tr class="totalizador">
                                <td colspan="2" align="right">TOTAIS:</td>
                               <td align="center">'.number_format($totalizador_total_rendimentos,2,',','.').'</td>
                               <td align="center">'.number_format($totalizador_saldo_salario,2,',','.').'</td>
                               <td align="center" class="resultado">'.number_format($totalizador_valor_rescisao,2,',','.').'</td>
                         </tr>';      
                        break;

                      case 2:
                        echo '<tr class="totalizador">
                                <td colspan="2" align="right">TOTAIS:</td>
                               <td align="center">'.number_format($totalizador_total_deducao,2,',','.').'</td>
                               <td align="center">'.number_format($totalizador_total_liquido,2,',','.').'</td>
                               <td align="center">'.number_format($totalizador_inss,2,',','.').'</td>
                               <td align="center">'.number_format($totalizador_inss_dt,2,',','.').'</td>
                               <td align="center">'.number_format($totalizador_ir,2,',','.').'</td>
                               <td align="center">'.number_format($totalizador_ir_dt,2,',','.').'</td>
                               <td align="center" class="resultado">'.number_format($totalizador_valor_pago_rescisao,2,',','.').'</td>
                         </tr>';      
                        break;

                     case 3:
                        echo '<tr class="totalizador">
                                <td colspan="2" align="right">TOTAIS:</td>
                               <td align="center">'.number_format($totalizador_inss,2,',','.').'</td>
                               <td align="center">'.number_format($totalizador_inss_dt,2,',','.').'</td>
                               <td align="center" class="resultado">'.number_format($totalizador_total_inss,2,',','.').'</td>
                         </tr>';      
                        break;
                    
                       case 4:
                        echo '<tr class="totalizador">
                                <td colspan="2" align="right">TOTAIS:</td>
                               <td align="center">'.number_format($totalizador_ir,2,',','.').'</td>
                               <td align="center">'.number_format($totalizador_ir_dt,2,',','.').'</td>
                               <td align="center" class="resultado">'.number_format($totalizador_total_ir,2,',','.').'</td>
                         </tr>';      
                        break;


              }?>

            </table>
            
        </div>
    </body>
</html>