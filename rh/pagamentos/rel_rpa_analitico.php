<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}
error_reporting(E_ALL);
/*
 * LAST UPDATE
 * RAMON LIMA
 * 11/04/2013
 */

include('../../conn.php');
include("../../funcoes.php");
include("../../wfunction.php");
$lista = false;
$usuario = carregaUsuario();
$mes = $_REQUEST['mes'];
$ano = $_REQUEST['ano'];
$id_regiao = $_REQUEST['regiao'];

$usuario = carregaUsuario();

//echo  "SELECT A.*, B.nome, B.cpf, C.nome as nome_projeto, C.id_projeto
//                        FROM  rpa_autonomo as A
//                        INNER JOIN autonomo as B 
//                        ON A.id_autonomo = B.id_autonomo
//                        INNER JOIN projeto as C 
//                        ON C.id_projeto = B.id_projeto
//                        INNER JOIN regioes as D
//                        ON D.id_regiao = B.id_regiao
//                        INNER JOIN rpa_saida_assoc as E
//                        ON E.id_rpa = A.id_rpa
//                        INNER JOIN saida as F
//                        ON F.id_saida = E.id_saida
//                        WHERE  MONTH(data_geracao) = '$mes' AND YEAR(data_geracao) = '$ano' AND D.id_master = $usuario[id_master] AND F.status IN(1,2)
//                        GROUP BY A.id_autonomo                            
//                        ORDER BY B.id_projeto,B.nome";

$qr_rpa = mysql_query(" SELECT A.*, B.nome, B.cpf, C.nome as nome_projeto, C.id_projeto
                        FROM  rpa_autonomo as A
                        INNER JOIN autonomo as B 
                        ON A.id_autonomo = B.id_autonomo
                        INNER JOIN projeto as C 
                        ON C.id_projeto = B.id_projeto
                        INNER JOIN regioes as D
                        ON D.id_regiao = B.id_regiao
                        INNER JOIN rpa_saida_assoc as E
                        ON E.id_rpa = A.id_rpa
                        INNER JOIN saida as F
                        ON F.id_saida = E.id_saida
                        WHERE  MONTH(data_geracao) = '$mes' AND YEAR(data_geracao) = '$ano' AND D.id_master = $usuario[id_master] AND F.status IN(1,2)
                        GROUP BY A.id_autonomo                            
                        ORDER BY B.id_projeto,B.nome");

?>
<html>
    <head>
        <title>RH - Pagamentos</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../uploadfy/css/uploadify.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="../../jquery/thickbox/thickbox.css" type="text/css" media="screen" />        
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>        
        <script src="../../jquery/priceFormat.js" type="text/javascript"></script>
        <script src="../../uploadfy/scripts/jquery.uploadify.v2.1.0.js" type="text/javascript"></script>
        <script src="../../uploadfy/scripts/swfobject.js" type="text/javascript"></script>
        <script src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript"></script>
        <script type="text/javascript" src="../../jquery/thickbox/thickbox.js"></script>
        <script src="../../js/global.js" type="text/javascript"></script>        
    </head>
    <body class="novaintra">
        <form action="" method="post" name="form1">
            <div id="content">
                <div id="head">
                    <img src="../../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>Relatório Analítico - RPA</h2>
                        <p></p>
                    </div>
                </div>
                <br class="clear">

                <br/>            
                <br/><br/>
                <?php
                if(mysql_num_rows($qr_rpa) != 0){
                 ?>   
                  <table width="100%" cellspacing="0" cellpadding="0" class="grid">
                    
                    <thead>
                       <th>Nº RPA</th>
                            <th>UNIDADE</th>
                            <th>NOME</th>
                            <th>CPF</th>
                            <th>HORA MÊS</th>
                            <th>VALOR BRUTO</th>
                            <th>INSS</th>
                            <th>IR</th>
                            <th>VALOR LÍQUIDO</th>
                      
                </thead>
             
                   
                <?php 
                while($row_rpa = mysql_fetch_assoc($qr_rpa)){
                               
                       if($row_rpa['id_projeto'] != $projetoAnt and !empty($projetoAnt)){
                             echo'<tr height="40" style="background-color: #c8ebf9">
                                <td colspan="4" align="right" style="font-weight:bold;">SUBTOTAIS:</td>
                                <td align="center"> R$ '.number_format($subtotal_bruto,2,',','.').'</td>
                                <td align="center"> R$ '.number_format($subtotal_inss,2,',','.').'</td>
                                <td align="center"> R$ '.number_format($subtotal_irrf,2,',','.').'</td>
                                <td align="center"> R$ '.number_format($subtotal_liquido,2,',','.').'</td>
                                    
                             </tr>';
                          unset($subtotal_inss,$subtotal_irrf, $subtotal_liquido );
                           }
                    
                    
                    
                    echo '<tr>';
                        echo '<td>'.$row_rpa['id_rpa'].'</td>';
                        echo '<td>'.$row_rpa['nome_projeto'].'</td>';
                        echo '<td>'.$row_rpa['nome'].'</td>';
                        echo '<td>'.$row_rpa['cpf'].'</td>';
                        echo '<td align="center">'.$row_rpa['hora_mes'].'</td>';
                        echo '<td align="center">'.number_format($row_rpa['valor'],2,',','.').'</td>';
                        echo '<td align="center">'.number_format($row_rpa['valor_inss'],2,',','.').'</td>';
                        echo '<td align="center">'.number_format($row_rpa['valor_ir'],2,',','.').'</td>';
                        echo '<td align="center">'.number_format($row_rpa['valor_liquido'],2,',','.').'</td>';
                    echo '</tr>';    
                          
                          $subtotal_bruto        += $row_rpa['valor'];
                          $subtotal_inss         += $row_rpa['valor_inss'];
                          $subtotal_irrf         += $row_rpa['valor_ir'];
                          $subtotal_liquido      += $row_rpa['valor_liquido'];
                          
                          $totalizador_bruto     += $row_rpa['valor'];
                          $totalizador_inss      += $row_rpa['valor_inss'];
                          $totalizador_irrf      += $row_rpa['valor_ir'];
                          $totalizador_liquido   += $row_rpa['valor_liquido'];
                          $projetoAnt            = $row_rpa['id_projeto'];
                }
                          echo'<tr height="40" style="background-color: #c8ebf9">
                                <td colspan="4" align="right" style="font-weight:bold;">SUBTOTAIS:</td>
                                <td align="center"> R$ '.number_format($subtotal_bruto,2,',','.').'</td>
                                <td align="center"> R$ '.number_format($subtotal_inss,2,',','.').'</td>
                                <td align="center"> R$ '.number_format($subtotal_irrf,2,',','.').'</td>
                                <td align="center"> R$ '.number_format($subtotal_liquido,2,',','.').'</td>
                                    
                             </tr>';
                          echo'<tr height="40" style="background-color: #c8ebf9">
                                <td colspan="4" align="right" style="font-weight:bold;">TOTAIS:</td>
                                <td align="center"> R$ '.number_format($totalizador_bruto,2,',','.').'</td>
                                <td align="center"> R$ '.number_format($totalizador_inss,2,',','.').'</td>
                                <td align="center"> R$ '.number_format($totalizador_irrf,2,',','.').'</td>
                                <td align="center"> R$ '.number_format($totalizador_liquido,2,',','.').'</td>
                                    
                             </tr>';
                      
                
                echo '</table>';
                
               
                            
                }
                ?>
            </table>
            </div>
        </form>
    </body>
</html>