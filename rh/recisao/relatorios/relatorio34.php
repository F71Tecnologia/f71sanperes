<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='login.php'>Logar</a> ";
    exit;
}

require("../conn.php");
require("../wfunction.php");

$usuario = carregaUsuario();
$projeto = $_GET['pro'];

$nome_projeto = mysql_result(mysql_query("SELECT nome FROM projeto WHERE id_projeto = $projeto"),0) ;


if(isset($_POST['gerar'])){ 
    
 $mes = $_POST['mes'];
 $ano = $_POST['ano'];
 
 
//SELECIONANDO OS DADOS DO RELATÓRIO
$qr = "select B.id_clt, B.nome, C.nome as nome_curso,  D.nome as sindicato, D.cnpj,A.a5019 as valor_contribuicao, B.rh_sindicato
        FROM rh_folha_proc  as A 
        INNER JOIN rh_clt as B
        ON B.id_clt = A.id_clt
        INNER JOIN curso as C
        ON C.id_curso = B.id_curso
        INNER JOIN rhsindicato as D
        ON D.id_sindicato = B.rh_sindicato
        WHERE A.mes = '$mes' AND  A.ano = '$ano' AND A.id_projeto = '$projeto' AND A.status = 3  AND A.a5019 !='0.00' 
        ORDER BY B.rh_sindicato,B.nome;";



$result = mysql_query($qr);
echo "<!-- \r\n $qr \r\n-->";
$total = mysql_num_rows($result);

$count = 0;

$qr_sem_sindicato = mysql_query("SELECT A.id_clt,A.nome, B.nome  as funcao , A.rh_sindicato , C.nome as sindicato,A.id_curso
        FROM rh_clt AS A
        INNER JOIN curso as B
        ON B.id_curso = A.id_curso
        LEFT JOIN rhsindicato as C
        ON C.id_sindicato = A.rh_sindicato
        WHERE A.id_projeto = $projeto AND  A.rh_sindicato = 0
        ORDER BY A.rh_sindicato,nome;");

}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Relatório de Funcionários com Contribuição Sindical</title>
        <link href="../net1.css" rel="stylesheet" type="text/css"/>
        <script type="text/javascript" src="../jquery/jquery-1.4.2.min.js"></script>
        <style media="screen">
            thead{ margin-top: 50px;}
            .grid thead tr th {font-size: 12px!important;}
            .bt-edit{cursor: pointer;}
            #head_impressao{ display: none;}
        </style>
        
        <style media="print">
            .grid{   page-break-after: always;
                font-size: 10px;}
            .grid thead{   font-size: 10px;}
        fieldset{ display:none; }
        .suporte{ display:none;}
         #head_impressao{ display: block;}
         #head{ display: none;}
         
        </style>
    </head>

    <body class="novaintra">
        <div id="content" style="width: 90%;">
            <div id="head">
                <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                <div class="fleft">
                    <h2><?php echo $nome_projeto;?></h2>
                    <h3>Relatório de Funcionários com Contribuição Sindical</h3>
                </div>
                <div class="fright suporte"> <?php include('../reportar_erro.php'); ?></div> 
            </div>
            <br class="clear"/>
            <br/>
                  <form  name="form" action="" method="post" id="form">
                <fieldset>
                    <legend>Dados</legend>
                    <div class="fleft">
                        <p><label class="first">Mês:</label>
                            <select  name="mes" id="mes">
                                <option value="">Selecione...</option>
                                <?php 
                                $qr_mes = mysql_query("SELECT * FROM ano_meses");
                                while($row_mes = mysql_fetch_assoc($qr_mes)){    
                                    $selected = ($row_mes['num_mes'] == $mes)?'selected="selected"':'';
                                    echo '<option value="'.$row_mes['num_mes'].'" '.$selected.'>'.$row_mes['nome_mes'].'</option>';  
                                }
                                ?>
                            </select>
                        </p>
                        <p><label class="first">Ano:</label>
                        <select name="ano">
                        <?php
                            for($i=2012;$i<=date('Y');$i++){ 
                                $selected = ($i == $ano)?'selected="selected"':'';
                                echo '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';  }
                        ?>
                        </select>
                        </p>
                       </div>
  
                    <br class="clear"/>                
                    <p class="controls" style="margin-top: 10px;">                      
                                <input type="submit" name="gerar" value="Gerar" id="gerar"/>
                    </p>
                </fieldset>
        </form>
               
               
                    <?php 
                    if(isset($_POST['gerar'])){
                    
                    if(mysql_num_rows($result) == 0){  
                        
                    }else {
                                while ($row = mysql_fetch_assoc($result)) {                    
                                     //echo 'Sem contribuições sindicais neste mês.';

                                if($row['rh_sindicato'] != $sindicato_anterior){   

                                $valor_total = mysql_result(mysql_query("SELECT SUM(A.a5019) as valor_total  
                                                            FROM rh_folha_proc  as A 
                                                            INNER JOIN rh_clt as B
                                                            ON B.id_clt = A.id_clt
                                                            INNER JOIN curso as C
                                                            ON C.id_curso = B.id_curso
                                                            INNER JOIN rhsindicato as D
                                                            ON D.id_sindicato = B.rh_sindicato
                                                            WHERE A.mes = '$mes' AND  A.ano = '$ano' AND A.id_projeto = '$projeto' AND A.status = 3  AND A.a5019 !='0.00' 
                                                            AND B.rh_sindicato ='$row[rh_sindicato]' 
                                                            "),0);
                                        
                                    if($count2 !=0 ) { echo '</table>'; }
                                 ?>
                                        
                                         <!-- exibido somente para impressão-->
                                        <div id="head_impressao">
                                           <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                                           <div class="fleft">
                                               <h2><?php echo $nome_projeto;?></h2>
                                               <h3>Relatório de Funcionários com Contribuição Sindical</h3>
                                           </div>
                                           <div class="fright suporte"> <?php include('../reportar_erro.php'); ?></div> 
                                       </div>
                                       <!------------------------------------->
            
            
                                        <table width="96%" align="center" cellpadding="0" cellspacing="0" border="0" class="grid"  style="margin-bottom: 30px;">
                                         <thead >
                                         <tr>
                                            <th colspan='2'>
                                                <?php echo $row['rh_sindicato'].' - '.$row['sindicato'];?>
                                            </th>
                                             <th><?php echo 'CNPJ: '.$row['cnpj']; ?> </th>
                                            <th>TOTAL: R$ <?php echo number_format($valor_total,2,',','.');?></th>
                                         </tr>
                                         </thead>    
                                         <thead>
                                            <tr>
                                                <th>CLT</th>
                                                <th>Nome</th>
                                                <th>Função</th>
                                                <th>Valor</th>
                                           </tr>
                                        </thead>


                                 <?php   
                                }
                               $count2++;
                               $count++;

                               if($count == 15){ $quebra_pagina = 'style=" page-break-after:   always;"';} else {$quebra_pagina = ''; $count =0;}

                                 ?>
                                    <tr class="<?php echo $count++ % 2 ? "even":"odd"?> " <?php echo $quebra_pagina;?>>
                                        <td><?php echo $row['id_clt'] ?></td>
                                        <td><?php echo $row['nome'] ?></td>
                                        <td><?php echo $row['nome_curso'] ?></td>  
                                        <td align="left"> R$ <?php echo number_format($row['valor_contribuicao'],2,',','.') ?></td>  

                                    </tr>
                                <?php 

                                $sindicato_anterior = $row['rh_sindicato'];


                                 if($count2 == $total){ echo '</table>';}
                                } ?>       

                                              
                              
                                    
                       <?php 
                       ////////SEM SINDICATO
                       
                       if(mysql_num_rows($qr_sem_sindicato) !=0) { ?> 

                             <!-- exibido somente para impressão-->
                            <div id="head_impressao">
                               <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                               <div class="fleft">
                                   <h2><?php echo $nome_projeto;?></h2>
                                   <h3>Relatório de Funcionários com Contribuição Sindical</h3>
                               </div>
                               <div class="fright suporte"> <?php include('../reportar_erro.php'); ?></div> 
                           </div>
                           <!------------------------------------->
                           
                   <table width="96%" align="center" cellpadding="0" cellspacing="0" border="0" class="grid"  style="margin-bottom: 30px; page-break-after: avoid;">
                        <thead > 
                        <tr>
                           <th colspan='3'>TRABALHADORES SEM SINDICATO</th>
                        </tr>
                        </thead>    
                        <thead>
                           <tr>
                               <th>CLT</th>
                               <th>Nome</th>
                               <th>Função</th>
                          </tr>
                        </thead>                        
                        <?php  while($row = mysql_fetch_assoc($qr_sem_sindicato)){

                             ?>
                                    <tr class="<?php echo $count++ % 2 ? "even":"odd"?> ">
                                        <td><?php echo $row['id_clt'] ?></td>
                                        <td><?php echo $row['nome'] ?></td>
                                        <td><?php echo $row['funcao'] ?></td>                            
                                    </tr>
                       <?php  }   ?>     
                       </table>
                       <?php } 
           
                    }
           
               }?>                       
                                  
        </div>
    </body>
</html>