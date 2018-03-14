<?php
include ("../include/restricoes.php");
include ('../../conn.php');
/**
 * AutoComplete Field - PHP Remote Script
 *
 * This is a sample source code provided by fromvega.
 * Search for the complete article at http://www.fromvega.com
 *
 * Enjoy!
 *
 * @author fromvega
 *
 */

if(isset($_GET['pesquisa']) and $_GET['pesquisa'] != '')
{
$qr_clt = mysql_query("SELECT id_clt, nome,data_nasci,id_curso, data_entrada,data_saida, rg, cpf
						FROM rh_clt 	
						INNER JOIN funcionario_regiao_assoc
						ON funcionario_regiao_assoc.id_regiao = rh_clt.id_regiao
						WHERE rh_clt.nome LIKE '%$_GET[pesquisa]%' AND id_funcionario = '$_COOKIE[logado]'") or die(mysql_error());


$qr_autonomo = mysql_query("SELECT id_autonomo,tipo_contratacao,nome, data_nasci, id_curso, data_entrada,data_saida ,locacao, rg, cpf  
							FROM autonomo
							INNER JOIN funcionario_regiao_assoc
							ON funcionario_regiao_assoc.id_regiao = autonomo.id_regiao							
							WHERE autonomo.nome LIKE '%$_GET[pesquisa]%' GROUP BY id_autonomo");
	
	
?>

<table width="100%">

<?php
if(mysql_num_rows($qr_autonomo) != 0 or mysql_num_rows($qr_clt) != 0){
?>
                <tr class="secao">
                    <td width="50">COD</td>
                    <td>NOME</td>
                    <td width="50">CPF</td>
                    <td width="90">RG</td>
                    <td>DATA DE NASCIMENTO</td>	
                    <td>CARGO</td>
                    <td>UNIDADE</td>
                    <td>DATA DE ENTRADA</td>
                    <td>DATA DE SAÍDA</td> 	  
                </tr>
            
            <?php
            
            
            
                
                //RESULTADOS DA PESQUISA DOS CLT'S	
                while($row_clt = mysql_fetch_assoc($qr_clt)):
				
				$pesquisa_cpf = mysql_num_rows(mysql_query("SELECT * FROM rh_clt WHERE cpf = '$row_clt[cpf]'"));
				if($pesquisa_cpf >1){
				
				$repetido = 'style="background-color:#FF9F9F"';
				}
				
                ?>
                <tr class="linha_<?php if($linha++%2==0) { echo 'um'; } else { echo 'dois'; } ?> destaque" <?php echo $repetido; ?>>
                    <td>
                            <?php echo $row_clt['id_clt'];?>
                    </td>
                    <td align="left" style="text-transform:uppercase;">
                     <a href="cad_processo.php?trab=<?php echo $row_clt['id_clt']?>&projeto=<?php echo $row_clt['id_projeto']?>&tp=2" <?php echo $repetido; ?>>  <?php echo utf8_encode($row_clt['nome']);?>
                         <img src="../../rh/folha/sintetica/seta_<?php if($seta++%2==0) { echo 'um'; } else { echo 'dois'; } ?>.gif">     
                    </a>
                    </td>
                    <td><?php echo $row_clt['cpf'];?></td>
                    <td><?php echo $row_clt['rg'];?></td>
                    <td><?php echo  implode('/', array_reverse(explode('-',$row_clt['data_nasci'])))?></td>
                     
                    <td>
                        <?php 
                        //Pegando a atividade
                        $q_atividade = mysql_query("SELECT * FROM curso WHERE id_curso = '$row_clt[id_curso]'") or die (mysql_error());
                        $row_atividade = mysql_fetch_assoc($q_atividade);
                        echo utf8_encode($row_atividade['nome']);		
                        ?>            
                    </td>
                                            
                    <td><?php echo  utf8_encode($row_clt['locacao']);?></td>
                    <td><?php echo  implode('/', array_reverse(explode('-',$row_clt['data_entrada'])));?></td>
                     <td><?php echo implode('/', array_reverse(explode('-',$row_clt['data_saida'])));?></td>
                    
                </tr>                
                <?php             
                unset($repetido, $pesquisa_cpf);
                endwhile;
            
                        
                //RESULTADOS DA PESQUISA DOS AUTONOMOS, COOPERADOS E AUNTONOMOS/PJ
                while($row_autonomo = mysql_fetch_assoc($qr_autonomo)):
				
				
				$pesquisa_cpf = mysql_num_rows(mysql_query("SELECT * FROM autonomo WHERE cpf = '$row_autonomo[cpf]' AND status_reg = 1"))or die(mysql_error());
				if($pesquisa_cpf >1){
				
				$repetido = 'style="background-color:#FF9F9F"';
				}
				
                ?>
                <tr class="linha_<?php if($linha++%2==0) { echo 'um'; } else { echo 'dois'; } ?> destaque" <?php echo $repetido;?> >
                    <td>
                            <?php echo $row_autonomo['id_autonomo'];?>
                    </td>
                    <td align="left" style="text-transform:uppercase;">
                     <a href="cad_processo.php?trab=<?php echo $row_autonomo['id_autonomo']?>&projeto=<?php echo $row_autonomo['id_projeto']?>&tp=<?php echo $row_autonomo['tipo_contratacao'];?>"> <?php echo utf8_encode($row_autonomo['nome']);?>
                         <img src="../../rh/folha/sintetica/seta_<?php if($seta++%2==0) { echo 'um'; } else { echo 'dois'; } ?>.gif">     
                    </a>
                    </td>
                    <td><?php echo $row_autonomo['cpf'];?></td>
                    <td><?php echo $row_autonomo['rg'];?></td>
                    <td><?php echo  implode('/', array_reverse(explode('-',$row_autonomo['data_nasci'])))?></td>
                     
                    <td>
                        <?php 
                        //Pegando a atividade
                        $q_atividade = mysql_query("SELECT * FROM curso WHERE id_curso = '$row_autonomo[id_curso]'") or die (mysql_error());
                        $row_atividade = mysql_fetch_assoc($q_atividade);
                        echo utf8_encode($row_atividade['nome']);		
                        ?>            
                    </td>
                                            
                    <td><?php echo  utf8_encode($row_autonomo['locacao']);?></td>
                    <td><?php echo  implode('/', array_reverse(explode('-',$row_autonomo['data_entrada'])));?></td>
                     <td><?php echo implode('/', array_reverse(explode('-',$row_autonomo['data_saida'])));?></td>
                    
                </tr>
                
                
                
                <?php	
                
                unset($repetido, $pesquisa_cpf);
                endwhile;
                
 	} else {
		
            echo '<tr>
                    <td>Nenhum trabalhador foi encontrado!</td>
                  </tr>';
	}

	// return the array as json with PHP 5.2
	

	// or return using Zend_Json class
	//require_once('Zend/Json/Encoder.php');
	//echo Zend_Json_Encoder::encode($results);
}
?>

</table>