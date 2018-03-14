<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include "../conn.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include("../wfunction.php");


function formata_numero($num){
    
    if(strstr($num,'.')  and !empty($num)){
        return number_format($num,2,',','.');
    } else {
        return $num;
    }
}

$usuario = carregaUsuario();
$id_clt  = $_REQUEST['id'];

 $qr_clt = mysql_query("SELECT A.id_clt, A.nome,CONCAT(A.endereco,A.numero,', ',A.bairro,', ',A.cidade,' - ',A.uf) as endereco,
                DATE_FORMAT(A.data_nasci,'%d/%m/%Y') as data_nasci,
                DATE_FORMAT(A.data_entrada,'%d/%m/%Y') as data_entrada,
                DATE_FORMAT(A.data_demi,'%d/%m/%Y') as data_demi,
                A.nacionalidade,A.naturalidade,A.tipo_conta,
                A.agencia, A.conta,(C.nome) as nome_banco,
                A.cpf,A.rg,A.titulo,A.campo1,A.pis,
                B.nome as nome_curso, FORMAT(B.salario,2) as salario
                 FROM rh_clt as A 
                INNER JOIN curso as B
                ON B.id_curso = A.id_curso 
                LEFT JOIN bancos as C
                ON A.banco = C.id_banco
                WHERE A.id_clt = $id_clt;");
 $row_clt = mysql_fetch_assoc($qr_clt);

//ANO
$optAnos = array();
for ($i = 2010; $i <= date('Y'); $i++) {
    $optAnos[$i] = $i;
}
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');



//
//error_reporting(-1);


?>
<html>
    <head>
        <title>Gerar IRRF</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="../net1.css" rel="stylesheet" type="text/css">
        <script src="../jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
        <script src="../jquery/jquery.tools.min.js" type="text/javascript" ></script>
        <script src="../js/global.js" type="text/javascript" ></script>
        <script>
            $(function(){           
             
                
            });
            
        </script>
        <style media="print">
            
            fieldset{ display: none;}
            
        </style>


    </head>
    <body class="novaintra">       
        <div id="content">
            <div id="head">
                <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;" width="140" height="100"/>
                <div class="fleft">
                    <h2>FICHA FINANCEIRA CLT</h2>
                    <p></p>
                    <table class="grid" border="1" cellspacing="0" cellpadding="0" width="100%"> 
                        <tr>
                            <td align="right"><strong>COD.:</strong></td>
                            <td colspan="5"><?php echo $row_clt['id_clt']; ?></td>
                        </tr>
                        <tr>
                            <td align="right"><strong>Nome:</strong></td>
                            <td colspan="5"><?php echo $row_clt['nome']; ?></td>
                        </tr>
                        <tr>
                            <td align="right"><strong>Data de Nascimento:</strong></td>
                            <td><?php echo $row_clt['data_nasci']; ?></td>
                            <td align="right"><strong>Nacionalidade:</strong></td>
                            <td ><?php echo $row_clt['nacionalidade']; ?></td>
                            <td align="right"><strong>Naturalidade:</strong></td>
                            <td><?php echo $row_clt['naturalidade']; ?></td>
                        </tr>
                           <tr>
                            <td align="right"><strong>CPF:</strong></td>
                            <td><?php echo $row_clt['cpf']; ?></td>
                            <td align="right"><strong>RG:</strong></td>
                            <td><?php echo $row_clt['rg']; ?></td>
                            <td align="right"><strong>Título:</strong></td>
                            <td><?php echo $row_clt['titulo']; ?></td>
                        </tr>
                        <tr>
                            <td align="right"><strong>CTPS:</strong></td>
                            <td><?php echo $row_clt['campo1']; ?></td>
                            <td align="right"><strong>PIS/PASEP:</strong></td>
                            <td colspan="3"><?php echo $row_clt['pis']; ?></td>
                        </tr>
                        <tr>
                            <td align="right"><strong>Função:</strong></td>
                            <td><?php echo $row_clt['nome_curso']; ?></td>
                            <td align="right"><strong>Admissão:</strong></td>
                            <td><?php echo $row_clt['data_entrada']; ?></td>
                            <td align="right"><strong>Afastamento:</strong></td>
                            <td><?php echo $row_clt['data_demis']; ?></td>
                        </tr>
                        <tr>
                            <td align="right"><strong>Tipo de Pag.:</strong></td>
                            <td><?php echo $row_clt['tipo_conta']; ?></td>
                            <td align="right"><strong>Salário:</strong></td>
                            <td><?php echo $row_clt['salario']; ?></td>
                            <td align="right"><strong>Agência:</strong></td>
                            <td><?php echo $row_clt['conta']; ?></td>
                        </tr>
                        <tr>
                            <td align="right"><strong>Conta:</strong></td>
                            <td><?php echo $row_clt['conta']; ?></td>
                            <td align="right"><strong>Banco:</strong></td>
                            <td colspan="5"><?php echo $row_clt['nome_banco']; ?></td>
                           
                        </tr>
                       
                    </table>
                </div>
            </div>
            <br class="clear">
            <br/>

            <form  name="form" action="" method="post" id="form">
                <fieldset>
                    <legend>FICHA FINANCEIRA</legend>
                    <div class="fleft">
                        <p><label class="first">Ano:</label> <?php echo montaSelect($optAnos, $anoSel, array('name' => "ano", 'id' => 'ano')); ?></p>
                    </div>
  
                    <br class="clear"/>
                
                    <p class="controls" style="margin-top: 10px;">
                      <span class="fleft erro"><?php if($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                        <input type="hidden" name="id_master" value="<?php echo $id_master;?>"/>
                        <input type="hidden" name="id_clt" value="<?php echo $id_clt;?>"/>
                        <input type="submit" name="historico" value="Exibir histórico" id="historico"/>
                        <input type="submit" name="gerar" value="Gerar" id="gerar"/>
                    </p>
                </fieldset>
        </form>
        <?php
        if(isset($_POST['gerar'])){
        
                $id_clt = $_POST['id_clt'] ;
                $ano    = $_POST['ano'];

                $qr_clt = mysql_query("SELECT A.id_clt, A.nome,CONCAT(A.endereco,A.numero,', ',A.bairro,', ',A.cidade,' - ',A.uf) as endereco,
                DATE_FORMAT(A.data_nasci,'%d/%m/%Y') as data_nasci,
                DATE_FORMAT(A.data_entrada,'%d/%m/%Y') as data_entrada,
                DATE_FORMAT(A.data_demi,'%d/%m/%Y') as data_demi,
                A.nacionalidade,A.naturalidade,A.tipo_conta,
                A.agencia, A.conta,(C.nome) as nome_banco,
                A.cpf,A.rg,A.titulo,A.campo1,A.pis,
                B.nome as nome_curso, FORMAT(B.salario,2) as salario
                 FROM rh_clt as A 
                INNER JOIN curso as B
                ON B.id_curso = A.id_curso 
                LEFT JOIN bancos as C
                ON A.banco = C.id_banco
                WHERE A.id_clt = $id_clt;");
                $row_clt = mysql_fetch_assoc($qr_clt);

                $salario =  array_fill(-1, 12, null);
                $salario[-1] = '0001';
                $salario[-0] = 'SALÁRIO';

                         $qr_folha= mysql_query("select A.id_folha,A.terceiro,A.mes, A.ids_movimentos_estatisticas, B.* 
                                                from rh_folha as A 
                                                INNER JOIN rh_folha_proc as B 
                                                ON A.id_folha = B.id_folha
                                                WHERE B.id_clt = $id_clt  AND A.status = 3 AND B.status = 3 AND A.ano = $ano; ");
                        while($row_folha = mysql_fetch_assoc($qr_folha)){

                          $mes_folha = (int)$row_folha['mes'];

                           //////////////////////////////////////////////////
                          ///pegando os movimentos da tbela rh_folha_proc  
                          //////////////////////////////////////////////////
                          $qr_mov        = mysql_query("SELECT * from rh_movimentos WHERE (mov_lancavel != 1 OR cod IN(5019)) AND cod NOT IN(9996,0001) GROUP BY cod");
                          while($row_mov = mysql_fetch_assoc($qr_mov)){

                                $nome_campo = 'a'.$row_mov['cod'];
                                $categoria  = $row_mov['categoria'];

                                //Verificando o valor para a folha de décimo terceiro
                                if($row_folha['terceiro'] == 1 and $row_mov['cod'] == 5029){ $valor_movimento = $row_folha['salliquido'];} 
                                     else{ $valor_movimento = $row_folha[$nome_campo]; }

                                        if($valor_movimento != '0.00' and $valor_movimento != ''){

                                        $salario[(int)$row_folha['mes']] = $row_folha['sallimpo'];          

                                        if(!isset($mov[$categoria][$nome_campo])){               
                                            $mov[$categoria][$nome_campo]             =  array_fill(-1, 12, null);
                                            $mov[$categoria][$nome_campo][-1]         =  $row_mov['cod'];
                                            $mov[$categoria][$nome_campo][0]          =  $row_mov['descicao'];
                                            $mov[$categoria][$nome_campo][$mes_folha] =  $valor_movimento;  
                                        } else {
                                           $mov[$categoria][$nome_campo][$mes_folha] =  $valor_movimento;
                                        }
                                }
                          }

                          //////////////////////////////////////////////////
                          ///pegando os movimentos da tabela rh_movimentos_clt
                          //////////////////////////////////////////////////

                          $qr_mov = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_movimento IN($row_folha[ids_movimentos_estatisticas]) AND id_clt = '$id_clt'  AND mes_mov = '$row_folha[mes]' AND ano_mov = '$ano'") or die(mysql_error()) ;
                          while($row_mov = mysql_fetch_assoc($qr_mov)){

                              $tipo_mov = $row_mov['tipo_movimento'];
                              $cod_mov  = $row_mov['cod_movimento'];


                            if(!isset( $mov[$tipo_mov][$cod_mov])){ 
                                   $mov[$tipo_mov][$cod_mov]                =  array_fill(-1, 12, null);
                                   $mov[$tipo_mov][$cod_mov][-1]            =  $row_mov['cod_movimento'];
                                   $mov[$tipo_mov][$cod_mov][0]             =  $row_mov['nome_movimento'];
                                   $mov[$tipo_mov][$cod_mov][$mes_folha]    =   $row_mov['valor_movimento'];
                               } else {
                                  $mov[$tipo_mov][$cod_mov][$mes_folha] = $row_mov['valor_movimento'];    
                               }
                           }
                          /////////////////////////////////////////  
                        }        
                        $mov_tipos = array('CREDITO','DEBITO','DESCONTO');
       ?>                 
            <p></p> 
            <table cellspacing="0" cellpadding="0" class="grid" border="1" width="100%">
            <tr class="secao_pai">
                <td align="center">COD</td>
                <td align="center">NOME</td>
                <td align="center">JAN</td>
                <td align="center">FEV</td>
                <td align="center">MAR</td>
                <td align="center">ABR</td>
                <td align="center">MAI</td>
                <td align="center">JUN</td>
                <td align="center">JUL</td>
                <td align="center">AGO</td>
                <td align="center">SET</td>
                <td align="center">OUT</td>
                <td align="center">NOV</td>
                <td align="center">DEZ</td>
                <td align="center">TOTAL</td>
            </tr>

            <?php
                    //linha salário
                    echo '<tr class="linha_um">';
                        for($i =-1; $i<13;$i++){   
                            echo '<td>'.formata_numero($salario[$i]).'</td>';  

                            }
                        echo '<td>'.  number_format((array_sum(array_slice($salario,2,12))),2,',','.').'</td>';

                    echo '</tr>';    

                    foreach($mov_tipos as $chave => $tipo){    
                            foreach($mov[$tipo] as $array){ 

                                 if($alternateColor++%2==0) { $class ='linha_um'; } else { $class='linha_dois'; } 
                                 echo '<tr class="'.$class.'">';
                                   for($i =-1; $i<=13;$i++){                       

                                       if($i<13){ echo '<td >'.formata_numero($array[$i]).'</td>';} else { echo '<td>'.  number_format((array_sum(array_slice($array,2,12))),2,',','.').'</td>';}

                                        if($i > 0){
                                            //Totalizadores por tipo de rendimento
                                            if($tipo == 'CREDITO' and $array[$i] != ''){                                   
                                                    $total_mensal_rend[$i] += $array[$i];                                    
                                                }  else {
                                                    $total_mensal_desc[$i] += $array[$i];  
                                            }
                                        }

                                    }
                                echo '</tr>';
                            }


                     ////Linha dos totais de CREDITO E DÉBITO


                          if($tipo == 'CREDITO') { 
                                 echo '<tr>'; 
                                for($i =0; $i<=13;$i++){ 
                                $rendimentos = ( (($total_mensal_rend[$i]+$salario[$i])== 0) or (($total_mensal_rend[$i]+$salario[$i])== 1))?'': $total_mensal_rend[$i]+$salario[$i];
                                $total_rend[$i] += $rendimentos;


                                  switch ($i){
                                      case 0:  echo '<td align="right" colspan="2">Total de rendimentos</td>'; 
                                          break;
                                      case 13:   
                                                  $total_rend[$i] =  array_sum($total_rend);                          
                                                  echo '<td>'.formata_numero(array_sum($total_rend)).'</td>';   
                                          break;
                                      default :  echo '<td>'.formata_numero($rendimentos).'</td>'; 
                                  }

                                }
                                echo '</tr>';
                          }
                        elseif($chave == 2)  {

                                echo '<tr>'; 
                                   for($i =0; $i<=13;$i++){ 
                                            $descontos   = (($total_mensal_desc[$i]) == 0)?'': $total_mensal_desc[$i];
                                            $total_desco[$i] += $descontos; 

                                            switch($i){
                                                case 0:  echo '<td align="right" colspan="2">Total de descontos</td>'; 
                                                    break;
                                                case 13:    $total_desco[$i] =  array_sum(array_slice($total_desco,2,12));  
                                                            echo '<td>'.formata_numero(array_sum(array_slice($total_desco,2,12))).'</td>';        
                                                    break;
                                                default :echo '<td>'.formata_numero($descontos).'</td>'; 
                                            }
                                   }
                                echo '</tr>';
                          }



                    }

            

             echo '<tr>
                   <td align="right" colspan="2">Valor líquido</td>';

                 for($i =1; $i<=13;$i++){  

                     $soma = (($total_rend[$i]- $total_desco[$i]) == 0)? '': $total_rend[$i]- $total_desco[$i];
                     echo '<td>'.formata_numero($soma).'</td>';

                 }
                 echo '</tr>';
                 echo '</table>';
         
                 
                 
        
        } ///FIM IF(isset($_POST['gerar])
        ?>
            
            
            
            
            
            <div class="clear"></div>
        </div>
  

</body>
</html>