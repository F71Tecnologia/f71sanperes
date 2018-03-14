<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='login.php'>Logar</a> ";
    exit;
}

require("../conn.php");
require("../wfunction.php");


$usuario = carregaUsuario();

$projeto = $_REQUEST['pro'];
$regiao  = $_REQUEST['reg'];


$qr_clt = mysql_query("select A.id_regiao, A.id_projeto, A.id_clt, A.matricula, A.nome, A.pis, DATE_FORMAT(A.data_nasci,'%d/%m/%Y') as data_nasci, A.serie_ctps, 
    IF(A.uf_ctps = '', 'NÃO INFORMADO', A.uf_ctps) as uf_ctps, 
    A.rg, A.mae,
    C.nome as nome_projeto,
    REPLACE(REPLACE(A.cpf,'.',''),'-','') as cpf,
    A.campo1

FROM rh_clt as A
INNER JOIN regioes as B
ON A.id_regiao = B.id_regiao
INNER JOIN projeto as C
ON A.id_projeto = C.id_projeto
WHERE A.status IN(10,200) AND A.status_reg= 1
ORDER BY A.id_projeto, A.nome");


function format_cpf($cpf)   { 
   $cpf1 = substr($cpf,0,3);
   $cpf2 = substr($cpf,3,3);
   $cpf3 = substr($cpf,6,3);
   $cpf4 = substr($cpf,9,2);

   return $cpf1.'.'.$cpf2.'.'.$cpf3.'-'.$cpf4;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Relatório de PIS/NIT</title>
        <link href="../net1.css" rel="stylesheet" type="text/css"/>
        <script type="text/javascript" src="../jquery/jquery-1.4.2.min.js"></script>

        <style>
            .grid thead tr th {font-size: 12px!important;}
            .bt-edit{cursor: pointer;}  
           

        </style>
        <style media="print">
            form{ visibility:  hidden}
            
        </style>
        
    </head>

    <body class="novaintra">
        <div id="content" style="width: 90%;">
            <div id="head">
                <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                <div class="fleft">
                    <h2>Todos os Projetos</h2>
                    <h3>Relatório de PIS/NIT</h3>
                 
                </div>
                <div class="fright"> <?php include('../reportar_erro.php'); ?></div> 
            </div>
            <br class="clear">      
                <br/>         
          
        
            <div class="clear"></div>           
          
            <br/>
            <table width="96%" align="center" cellpadding="0" cellspacing="0" border="0" class="grid">
              
                <tbody>
                    <?php while ($row = mysql_fetch_assoc($qr_clt)) { 
                      
                        if($row['id_projeto'] != $projeto_anterior){
                    ?>   
                       <tr>
                           <td colspan="10" height="60" style="border:0;" valing="middle"><span style="color:   #0078FF; font-size: 16px; font-weight: bold;"><?php echo $row['nome_projeto'];?></span></td>
                       </tr> 
                            <tr style="background-color:  #a6a6a6; color: #000; font-weight: bold;">
                                <td>Matricula</td>
                                <td>Nome</td>
                                <td>Data de Nascimento</td>
                                <td>RG</td>
                                <td>CPF</td>
                                <td>PIS</td>
                                <td>Número da CTPS</td>
                                <td>Série da CTPS</td>                     
                                <td>UF da CTPS</td>                     
                                <td>Mãe</td>  
                            </tr>     
                            
                        <?php    
                        }                        
                    ?>
                        <tr class="<?php echo $count++ % 2 ? "even":"odd"?> ">
                            <td align="center"><?php echo $row['matricula'] ?></td>
                            <td align="left"><?php echo $row['nome'] ?></td>
                            <td align="center"><?php echo $row['data_nasci'] ?></td>
                            <td align="center"><?php echo $row['rg'] ?></td>
                            <td align="center"><?php echo format_cpf($row['cpf']) ?></td>                         
                            <td align="center"><?php echo ((int) $row['pis'] == 0)? 'SEM PIS':$row['pis'] ;?></td>                         
                            <td align="center"><?php echo $row['campo1'] ?></td>                         
                            <td align="center"><?php echo $row['serie_ctps'] ?></td>                         
                            <td align="center"><?php echo $row['uf_ctps'] ?></td>                         
                            <td align="left"><?php echo $row['mae'] ?></td>                         
                        </tr>
                    <?php 
                    
                    
                    $projeto_anterior = $row['id_projeto'];
                    
                        } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="9" class="txright"><strong>Total de funcionários:</strong></td>
                        <td><?php echo mysql_num_rows($qr_clt) ?></td>
                    </tr>
                </tfoot>
            </table>
            
          

        </div>
    </body>
</html>