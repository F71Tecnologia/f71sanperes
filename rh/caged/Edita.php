<?php 
require_once("../../conn.php");
// busca clt
$qr_clt = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$_GET[ID]';");
$row_clt = mysql_fetch_assoc($qr_clt);
// busca etnia
$qr_etnia 	= mysql_query("SELECT * FROM etnias ORDER BY nome ASC;");
// buscando a escolatiadade
$qr_escolaridade = mysql_query("SELECT nome,cod FROM escolaridade WHERE id = '$row_clt[escolaridade]';");
$escolaridade = @mysql_result($qr_escolaridade,0);
$cod_escolaridade = @mysql_result($qr_escolaridade,0,1);
// buscando salario
$qr_curso = mysql_query("SELECT salario,cbo_codigo,id_curso,nome FROM curso WHERE id_curso = '$row_clt[id_curso]';");
$row_curso = mysql_fetch_assoc($qr_curso);
// buscando o status
$status_demi = array('60','61','62','81','100','80','63');
$datas_demi = array();
if(in_array($row_clt['status'],$status_demi)){
	$datas_demi['Data de admissão'] = $row_clt['data_entrada'];
	$datas_demi['Data de demissão'] = $row_clt['data_demi'];
}else{
	$datas_demi['Data de admissão'] = $row_clt['data_entrada'];
}
// verificando deficiencia
$deficiencia = array();
if(!empty($row_clt['deficiencia'])){
	$qr_deficiencia = mysql_query("SELECT nome FROM deficiencias WHERE id = '$row_clt[deficiencia]'");
	$deficiencia['Tipo de deficiência'] = @mysql_result($qr_deficiencia,0);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Visualizar cadastro</title>
<style type="text/css">
body{
	margin:0px;
	font-family:Verdana, Geneva, sans-serif;
	background-color: #F3F3F3;
}
#loading{
	position:absolute;
	top:50%;
	left:30%;
	width:40%;
	text-align:center;
	display:none;
}
</style>
<link href="css/estilo_visu.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js"></script>
<script type="application/javascript" src="../../jquery/priceFormat.js" ></script>
<script type="application/javascript" src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" ></script>
<script type="text/javascript">
$(function(){
	$('form').submit(function(e) {  
					$('#loading').fadeIn();   
	 					e.preventDefault();    
						$.post($(this).attr("action"),
						$(this).serialize(), 
						function(retorno) {
							
							$('#loading').fadeOut();
							if(retorno == '1'){
							parent.window.location.reload();
								if (parent.window.hs) {
									var exp = parent.window.hs.getExpander();
									if (exp) {
											exp.close();
									}
								}  
							}else{
								alert(retorno);
							}
						});});
	
	$("input[name*=salario]").priceFormat({
		prefix: '',
		centsSeparator: ',',
		thousandsSeparator: '.'
	}); 
	
	$("input[name*=data_nasci]").mask('99/99/9999');
	$("input[name*=data_entrada]").mask('99/99/9999');
	$("input[name*=data_demi]").mask('99/99/9999');
});
</script>
</head>
<body>
<div id="loading">
	<img src="imagens/ajax-loader.gif"  />
</div>
<div id="base">
<form id="form" name="form" action="actions/update.caged.php?ID=<?=$_GET['ID']?>" method="post">
    
<h3><?=$row_clt['nome']?></h3>
<table width="100%" cellpadding="0" cellspacing="0">
	<tr>
	  <td colspan="4" align="center">Dados</td>
    </tr>
            		
                    <?php if(isset($_GET['sexo'])) echo '<tr bgcolor="#FF8C8C">'; else echo '<tr>'?>
                      <td align="right">Sexo:</td>
                      <td align="left" colspan="3"><?php 
                            $sexo = array('F' => 'Feminino', 'M' => 'Masculino');
                            echo '<table cellpadding="0" cellspacing="0">';
                            foreach($sexo as $val => $s){
                                if($row_clt['sexo'] == $val or $row_clt['sexo'] == strtolower($val)){
                                    $checked = 'checked="checked"';
                                }
                                echo '<tr>';
                                echo '<td>';
                                echo '<input type="radio" '.$checked.' name="sexo" value="'.$val.'">';
                                echo '</td>';
                                echo '<td>';
                                echo $s;
                                echo '</td>';
                                echo '</tr>';
                                $checked = NULL;
                            }
                            echo '</table>';                            
                          ?></td>
                    </tr>
                    <?php if(isset($_GET['raca'])) echo '<tr bgcolor="#FF8C8C">'; else echo '<tr>'?>
                      <td align="right">Etinia:</td>
                      <td align="left" colspan="3">
                            <?php
                            echo '<select name="etnia">';
                            while($row_etinia = mysql_fetch_assoc($qr_etnia)){
                                    if($row_clt['etnia'] == $row_etinia['id']) {
                                            $selected = 'selected="selected"';
                                            $status = true;
                                    }
                                    echo '<option '.$selected.' value="'.$row_etinia['id'].'">';
                                    echo $row_etinia['nome'];
                                    echo '</option>';
                                    $selected = NULL;
                            }
                            if(!$status){
                                    echo '<option selected="selected" value="'.$row_clt['etnia'].'">';
                                    echo 'Selecione';
                                    echo '</option>';
                            }
                            echo '</select>';
                            unset($status,$selected);
                            ?>
                    </td>
                    </tr>
                    <?php if(isset($_GET['escolaridade'])) echo '<tr bgcolor="#FF8C8C">'; else echo '<tr>'?>
                      <td align="right">Grau de instru&ccedil;&atilde;o:</td>
                      <td align="left" colspan="3">
					  		<?php 
                            $query_escolaridade = mysql_query("SELECT * FROM escolaridade WHERE id != '12' ORDER BY nome ASC");
                            echo '<select name="escolaridade">';
                            while($row_escolaridade = mysql_fetch_assoc($query_escolaridade)){
                                if ($cod_escolaridade == $row_escolaridade['cod']){
                                    $selected = 'selected="selected"';
									$status = true;
									
								}
                                echo '<option '.$selected.' value="'.$row_escolaridade['cod'].'">';
                                echo $row_escolaridade['nome'];
                                echo '</option>';
                                $selected = NULL;
                            }
							if(!$status){
								echo '<option selected="selected" value="'.$cod_escolaridade.'" >';
								echo 'Selecione';
								echo '</option>';
							}
                            echo '</select>';
							unset($status,$selected);
                        ?>
                        </td>
                    </tr>
                     <?php if(isset($_GET['nascimento'])) echo '<tr bgcolor="#FF8C8C">'; else echo '<tr>'?>
                      <td align="right">Nascimento:</td>
                      <td align="left" colspan="3"><input type="text" value="<?=implode("/",array_reverse(explode("-",$row_clt['data_nasci'])))?>" name="data_nasci" /></td>
                    </tr>


       
          <tr>
            <td align="right">Curso:</td>
            <td colspan="2" align="left"><?=$row_curso['nome']?></td>
            <td>&nbsp;</td>
          </tr>
        <?php if(isset($_GET['cbo'])) echo '<tr bgcolor="#FF8C8C">'; else echo '<tr>'?>
          <td width="34%" align="right">CBO:</td>
          <td colspan="2" align="left"><input type="text" name="cbo_codigo" value="<?=$row_curso['cbo_codigo']?>"/><input type="hidden" name="id_curso" value="<?=$row_curso['id_curso']?>" /></td>
          <td width="32%">&nbsp;</td>
        </tr>
         <?php if(isset($_GET['salario'])) echo '<tr bgcolor="#FF8C8C">'; else echo '<tr>'?>
          <td align="right">Salario:</td>
          <td colspan="2" align="left"><input type="text" name="salario" value="<?=$row_curso['salario']?>"  /></td>
          <td>&nbsp;</td>
    <!--      </tr>
         <?php if(isset($_GET['horas'])) echo '<tr bgcolor="#FF8C8C">'; else echo '<tr>'?>
          <td align="right">Horario:</td>
        <td colspan="3" align="left"><?php 
                    $qr_horario = mysql_query("SELECT * FROM rh_horarios WHERE funcao = '$row_curso[id_curso]'");
                    echo '<select name="horario">';
                    while($row_horario = mysql_fetch_assoc($qr_horario)){
                        if($row_clt['rh_horario'] == $row_horario['id_horario']){
                            $selected = 'selected="selected"';
                        }
                        echo '<option $selected value="'.$row_horario['id_horario'].'">';
                        echo $row_horario['nome'].' '.$row_horario['entrada_1'].' - '.$row_horario['saida_1'].' - '.$row_horario['entrada_2'].' - '.$row_horario['saida_2'];
                        echo '</option>';
                        $selected = NULL;
                    }
                    echo '</select>';
                  ?> <a href="<?= "../rh_horarios.php?regiao=".$row_clt['id_regiao']; ?>" target="_blank" style="text-decoration:none; color:#333; font-size:10px">cadastrar horario</a></td>
        </tr>-->
        <?php 		$indice = 0;
                    foreach($datas_demi as $nome =>$data){
						if($indice == 0) $campo = 'data_entrada'; else $campo = 'data_demi';
                        echo '<tr>';
                        echo '<td align="right">'.$nome.'</td>';
                        echo '<td><input type="text" name="'.$campo.'" value="'.implode("/",array_reverse(explode("-",$data))).'" /></td>';
                        echo '<td></td>';
                        echo '<tr>';
						$indice++;
                    }
					unset($indice);
                ?>
         <?php if(isset($_GET['status_admin'])) echo '<tr bgcolor="#FF8C8C">'; else echo '<tr>'?>
          <td align="right">Tipo de admiss&atilde;o:</td>
          <td colspan="3" align="left"><?php 
                  // 
                  $tipo_movimento[''] = "Selecione um movimento";
                  $tipo_movimento[10] = "Primeiro emprego";
                  $tipo_movimento[20] = "Reemprego";
                  $tipo_movimento[25] = "Contrato por prazo determinado";	
                  $tipo_movimento[35] = "Reintegra&ccedil;&atilde;o";
                  $tipo_movimento[70] = "Transfer&ecirc;ncia de entrada";
                  echo '<select name="status_admi">';
                  foreach($tipo_movimento as $cod => $tipo){
                        if($row_clt['status_admi'] == $cod){
                            $selected = 'selected="selected"';
                            $status = true;				
                        }
                
                    echo '<option '.$selected.' value="'.$cod.'" >';
                    echo $tipo;
                    echo '</option>';
                    $selected = NULL;
                  }
                  if(!$status){
                     echo '<option selected="selected" value="'.$row_clt['status_admi'].'" >';
                     echo 'Selecione';
                     echo '</option>';
                  }
                  echo '</select>';
                  unset($tipo_movimento,$status);
                  ?></td>
        </tr>
		  
    <tr>
      <td colspan="4" align="center">Documenta&ccedil;&atilde;o</td>
    </tr>
     <?php if(isset($_GET['ctps']) or isset($_GET['serie']) or isset($_GET['uf'])) echo '<tr bgcolor="#FF8C8C">'; else echo '<tr>'?>
    	<td colspan="2"><table align="center">
   	      <tr>
   	        <td align="center" colspan="2">Carteira de trabalho</td>
          </tr>
   	      <tr>
   	        <td align="right">N&ordm;:</td>
   	        <td><input name="campo1" type="text" id="campo1" value="<?= $row_clt['campo1']?>" /></td>
          </tr>
   	      <tr>
   	        <td align="right">Serie:</td>
   	        <td><input name="serie_ctps" type="text" id="serie_ctps" value="<?=$row_clt['serie_ctps']?>" /></td>
          </tr>
   	      <tr>
   	        <td align="right">UF</td>
   	        <td><input name="uf_ctps" type="text" id="uf_ctps" value="<?=$row_clt['uf_ctps']?>" /></td>
          </tr>
        </table></td>
    	<td colspan="2"><table align="center">
    	  <tr >
    	    <td colspan="2" align="center">Outros</td>
  	    </tr>
    	   <?php if(isset($_GET['pis'])) echo '<tr bgcolor="#FF8C8C">'; else echo '<tr>'?>
    	    <td align="right">PIS:</td>
    	    <td><input type="text" name="pis" value="<?=$row_clt['pis']?>" /></td>
  	    </tr>
    	  <?php if(isset($_GET['cpf'])) echo '<tr bgcolor="#FF8C8C">'; else echo '<tr>'?>
    	    <td align="right">CPF:</td>
    	    <td><input type="text" name="cpf" value="<?=$row_clt['cpf']?>" /></td>
  	    </tr>
    	   <?php if(isset($_GET['cep'])) echo '<tr bgcolor="#FF8C8C">'; else echo '<tr>'?>
    	    <td align="right">CEP:</td>
    	    <td><input type="text" name="cep" value="<?=$row_clt['cep']?>"/></td>
  	    </tr>
  	  </table></td> 
   	</tr>
	
	<tr>
	  <td colspan="4" align="center"><input type="submit" value="Enviar" /></td>
	  </tr> </tr>
</table>
</form>

</div>
</body>
</html>