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

$qr_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_user = mysql_fetch_assoc($qr_user);
$id_master = $row_user['id_master'];

// Buscando a Folha
list($regiao, $folha) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));

$qr_folha = mysql_query("SELECT *, date_format(data_inicio, '%d/%m/%Y') AS data_inicio_br,
                            date_format(data_fim, '%d/%m/%Y') AS data_fim_br,
                            date_format(data_proc, '%d/%m/%Y') AS data_proc_br,
                            (SELECT COUNT(id_clt) FROM rh_folha_proc WHERE id_folha = rh_folha.id_folha AND status_clt NOT IN(10,200)) as total_rescindidos
                            FROM rh_folha WHERE id_folha = '$folha' AND status = '3'") ;
$row_folha = mysql_fetch_assoc($qr_folha);

$qr_participantes = mysql_query("SELECT * FROM rh_folha as A
                                INNER JOIN rh_folha_proc as B 
                                ON A.id_folha = B.id_folha
                                WHERE A.id_folha = '$folha' AND A.status=3  ORDER BY B.nome;");

?>
<html>
    <head>
        <title>Relatório de Movimentos</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="../../net1.css" rel="stylesheet" type="text/css">
        <script src="../../jquery/jquery-1.4.2.min.js" type="text/javascript"></script>  
        <style media="print">
            form{ visibility: hidden;}
            .tabela{ border-collapse: collapse;}
           .nome_mov{ padding: 3px;
                 width: 200px;
                 height: 50px;
                 display:block;
            }
            .valor_mov{ width: 100px;
                        float: left;}
            
        </style>
    </head>
    <body class="novaintra">       
        <div id="content">
            <div id="head">
                <img src="../../imagens/logomaster<?php echo $id_master; ?>.gif" class="fleft" style="margin-right: 25px;" width="140" height="100"/>
                <div class="fleft">
                    <h2>Relatório de movimentos</h2>  
                    <p><strong><?php echo $row_folha['nome_mes'];?></strong></p>
                    <p><strong>Folha:</strong> <?php echo $row_folha['id_folha'];?></p>
                    <p><strong>Período:</strong> <?php echo $row_folha['data_inicio'];?> a  <?php echo $row_folha['data_fim'];?> </p>
                </div>
            </div>
            <br class="clear">
            <br/>           
            <div class="clear"></div>
            <table border="0" cellpadding="0" cellspacing="0" class="grid" style="margin-top: 30px;" width="100%">
               <tr>
                   <td>COD</td>
                   <td>NOME</td>
                   <td>DIAS</td>
                   <td>BASE</td>
                   <td>VALOR/DIA</td>
                   <td>RENDIMENTOS</td>
                   <td>DESCONTOS</td>
                   <td>LÍQUIDO</td>
               </tr>
             <?php
             while($row_participantes = mysql_fetch_assoc($qr_participantes)){
             ?>    
               <tr>
                   <td><?php echo $row_participantes['id_clt'];?></td>
                   <td><?php echo $row_participantes['nome'];?></td>
                   <td><?php echo $row_participantes['dias_trab'];?></td>
                   <td><?php echo $row_participantes['sallimpo_real'];?></td>
                   <td></td>
                   <td>                   
                   <?php
                    echo '<table>';
                    
                    $rendimentos_fixos = array('INSS' =>  $row_participantes['a5020'],
                                               'IRRF' => $row_participantes['a5021'],
                                               'SALARIO FAMILIA' => $row_participantes['a5020']);
                    foreach($rendimentos_fixos as $nome => $valor){
                        
                            echo '<tr>
                            <td style="border:0;">'.$chave.'</td>
                            <td style="border:0;">'.$valor.'</td>
                         </tr>';
                        
                    }              
                    
                   
                   ////RENDIMENTOS
                   $qr_mov_cred = mysql_query("SELECT * FROM rh_movimentos_clt 
                                                WHERE id_clt = '$row_participantes[id_clt]' 
                                                AND id_movimento IN($row_participantes[ids_movimentos_estatisticas])
                                                AND tipo_movimento = 'CREDITO'");
                   if(mysql_num_rows($qr_mov_cred) !=0){
                      
                    while($row_mov_cred = mysql_fetch_assoc($qr_mov_cred)){                        
                      echo '<tr style="border:0;">';
                      echo '<td style="border:0;" width="300">'.$row_mov_cred['nome_movimento'].'</td>';
                      echo '<td style="border:0;">  R$'.$row_mov_cred['valor_movimento'].'</td>';
                      echo '</tr>';                      
                     }                   
                    
                   }
                    echo '</table>';
                   ?>
                   </td>
                   <td>
                   <?php
                   ////RENDIMENTOS
                   $qr_mov_deb = mysql_query("SELECT * FROM rh_movimentos_clt 
                                                WHERE id_clt = '$row_participantes[id_clt]' 
                                                AND id_movimento IN($row_participantes[ids_movimentos_estatisticas])
                                                AND (tipo_movimento = 'DESCONTO' OR tipo_movimento = 'DEBITO')");
                   if(mysql_num_rows($qr_mov_deb) !=0){
                       echo '<table>';
                    while($row_mov_deb = mysql_fetch_assoc($qr_mov_deb)){                        
                      echo '<tr style="border:0;">';
                      echo '<td style="border:0;" width="300">'.$row_mov_deb['nome_movimento'].'</td>';
                      echo '<td style="border:0;">  R$'.$row_mov_deb['valor_movimento'].'</td>';
                      echo '</tr>';                      
                     }                   
                     echo '</table>';
                   }
                   ?>                       
                   </td>
                   
                   
                   <td><?php echo $row_participantes[''];?></td>
                   <td><?php echo $row_participantes['salliquido'];?></td>
               </tr>
              
                 
               <?php  
             }
             ?>  
               
               
               
               
               
            </table>
            
            
            
            
            <?php  
             
            ?>
        </div>
</body>
</html>