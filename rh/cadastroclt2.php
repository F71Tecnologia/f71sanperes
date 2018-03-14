<?php
if(empty($_COOKIE['logado'])) {
	print 'Efetue o Login<br><a href="../login.php">Logar</a>';
	exit;
}

include('../conn.php');
include('../classes/regiao.php');

$id_user     = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user    = mysql_fetch_array($result_user);
$REG = new regiao();

if(empty($_REQUEST['update'])) {
	
$id_regiao = $_REQUEST['regiao'];
$projeto   = $_REQUEST['projeto'];
$idcurso   = $_REQUEST['idcursos'];

if($id_regiao == '28') {
	$qr_maior = mysql_query("SELECT MAX(campo3) FROM rh_clt WHERE id_regiao = '$id_regiao' AND id_projeto = '$projeto' AND campo3 != 'INSERIR'");
	$codigo   = mysql_result($qr_maior,0) + 1;
} else {
	$resut_maior = mysql_query("SELECT CAST(campo3 AS UNSIGNED) campo30, MAX(campo3) FROM rh_clt WHERE id_regiao = '$id_regiao' AND id_projeto = '$projeto' AND campo3 != 'INSERIR' GROUP BY campo30 ASC");
	$row_maior   = mysql_num_rows($resut_maior); 
	$codigo      = $row_maior + 1;
}
?>
<html>
<head>
<title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="shortcut icon" href="../favicon.ico">
<link href="css/estrutura_cadastro.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="consulta.js"></script>
<script src="../js/ramon.js" type="text/javascript" language="javascript"></script>
<link href="../js/jquery.ui.theme.css" rel="stylesheet" type="text/css" />
<link href="../js/jquery.ui.datepicker.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../js/jquery-1.3.2.js"></script>
<script type="text/javascript" src="../js/jquery.ui.core.js"></script>
<script type="text/javascript" src="../js/jquery.ui.widget.js"></script>
<script type="text/javascript" src="../js/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../js/jquery.ui.datepicker-pt-BR.js"></script>
<script type="text/javascript">
$(function(){
	var tipoVerifica = 0;
	$("select[name*='banco']").change(function(){
		function tipoPgCheque(){
			$("select[name='tipopg']").find('option').attr('disabled',false).attr('selected',false);
			$("select[name='tipopg']").find('option').each(function(){
				if($(this).text() == "Cheque"){
					$(this).attr('selected',true);
				}else{
					$(this).attr('disabled',true);
				}
				
			});
		}
		
		function tipoPgConta(){
			$("select[name='tipopg']").find('option').attr('disabled',false).attr('selected',false);
			$("select[name='tipopg']").find('option').each(function(){
				if($(this).text() == "Depósito em Conta Corrente"){
					$(this).attr('selected',true);
				}else{
					$(this).attr('disabled',true);
				}	
				
			});
		}
		
		var valor = $(this).val();
		if(valor == 0){
			desabilita()
			tipoPgCheque();
			tipoVerifica = 1;
			
		}else if(valor == 9999){
			Ativa()
			tipoPgCheque();
			tipoVerifica = 2;
		}else{
			Ativa();
			tipoPgConta();
			tipoVerifica = 3;
			$("input[name='nomebanco']").attr("disabled", true);
		}
		
		
		
		
	});
	
	function desabilita(){
		
		$("input[name*='conta']").attr("disabled", true);
		$("input[type*='radio'][name*='radio_tipo_conta']").attr("disabled", true);
		$("input[name*='agencia']").attr("disabled", true);
		$("input[name='nomebanco']").attr("disabled", true);
	}
	
	function Ativa(){
		$("input[name*='conta']").attr("disabled", false);
		$("input[type*='radio'][name*='radio_tipo_conta']").attr("disabled", false);
		$("input[name*='agencia']").attr("disabled", false);
		$("input[name='nomebanco']").attr("disabled", false);
	}
	
	$("input[type*='button'][name*='Submit']").click(function(){
		var indice = new Array();
		if(tipoVerifica == 3){
			if($("input[name*='conta']").val() == ''){
				indice.push("Conta");
			}
			if($("input[name*='agencia']").val() == ''){
				indice.push("Agencia");
			}
			indiceRadio = 0;
			$("input[name*='radio_tipo_conta']").each(function(){
				if($(this).is(':checked')){
					indiceRadio = 1;
				}
			});
			
			if(indiceRadio == 0){
				indice.push("tipo de conta");
			}
			
			
		}else if(tipoVerifica == 2){
			if($("input[name*='conta']").val() == ''){
				indice.push("Conta");
			}
			if($("input[name*='agencia']").val() == ''){
				indice.push("Agencia");
			}
			indiceRadio = 0;
			$("input[name*='radio_tipo_conta']").each(function(){
				if($(this).is(':checked')){
					indiceRadio = 1;
				}
			});
			
			if(indiceRadio == 0){
				indice.push("tipo de conta");
			}
			
			if($("input[name*='nomebanco']").val() == ""){
				indice.push("Nome do banco");
			}
		}
		
		if(indice.length > 0){
			alert("Preencha o(s) dado(s) "+indice.join(', '));
		}else{
			
			$('#form1').submit();
		}
	});
});
</script>
</head>
<body>
<div id="corpo">
<table align="center" width="100%" cellspacing="0" cellpadding="12" style="font-size:13px; line-height:22px;">
  <tr>
    <td>
  <div style="border-bottom:2px solid #F3F3F3; margin-top:10px;">
       <h2 style="float:left; font-size:18px;">
           CADASTRAR <span class="clt">CLT</span>
       </h2>
       <p style="float:right;">
		   <?php if($_GET['pagina'] == "clt") { ?>
           <a href="clt.php?regiao=<?=$id_regiao?>">&laquo; Voltar</a>
           <?php } else { ?>
           <a href="../ver.php?regiao=<?=$id_regiao?>&projeto=<?=$projeto?>">&laquo; Voltar</a>
           <?php } ?>
       </p>
       <div class="clear"></div>
  </div>
  <p>&nbsp;</p>
<form action="<?=$_SERVER['PHP_SELF']?>" method="post" name="form1" enctype="multipart/form-data" onSubmit="return validaForm()">
<table cellpadding="0" cellspacing="1" class="secao">
  <tr>
    <td colspan="2" class="secao_pai" style="border-top:1px solid #777;">DADOS DO PROJETO</td>
  </tr>
  <tr>
  <td class="secao">C&oacute;digo:</td>
     <td><?=$codigo?> <input name="codigo" size="3" type="text" id="codigo" value="<?=$codigo?>" /></td>
  </tr>
  <tr>
    <td width="25%" class="secao">Projeto:</td>
    <td width="75%">   
  <?php if(!empty($projeto)) {
	  		$qr_projeto = mysql_query("SELECT nome FROM projeto WHERE id_projeto = '$projeto'");
  			echo $projeto.' - '.mysql_result($qr_projeto, 0);
  		} else { ?>
			
	 <select name="projetos" id="projetos" onChange="location.href=this.value;">
        <option selected disabled>--Selecione--</option>
    <?php $qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$id_regiao' AND status_reg = '1'");
          while($row_projeto = mysql_fetch_array($qr_projeto)) { ?>
		<option value="cadastroclt.php?regiao=<?=$id_regiao?>&projeto=<?=$row_projeto['id_projeto']?>"><?=$row_projeto['id_projeto'].' - '.$row_projeto['nome']?></option>
			
	<?php } ?>
     </select>
		
	<?php } ?>
    </td>
  </tr> 
  <tr style="display:none;">
    <td class="secao">Vínculo:</td>
    <td>
  <select name="vinculo" id="vinculo">
	<?php $result_vinculo = mysql_query("SELECT * FROM rhempresa WHERE id_regiao = '$id_regiao'");
          while($row_vinculo = mysql_fetch_array($result_vinculo)) {
                print "<option value='$row_vinculo[0]'>$row_vinculo[id_empresa] - $row_vinculo[razao]</option>";
          } ?>
  </select>
    </td>
  </tr>
  <tr style="display:none;">
     <td class="secao">Tipo Contrata&ccedil;&atilde;o:</td>
     <td><label><input name="contratacao" type="radio" class="reset" id="contratacao" value="2" checked="checked"> CLT</label></td>
  </tr>
  <tr>
  <td class="secao">Curso:</td>
  <td>
  <?php $qr_curso = mysql_query("SELECT * FROM curso WHERE id_regiao = '$id_regiao' AND campo3 = '$projeto' AND tipo = '2' ORDER BY nome ASC");
        $verifica_curso = mysql_num_rows($qr_curso);
		if(!empty($verifica_curso)) {
			
			if(!empty($idcurso)) {
				$var_disabled = 'display:none;';
			}
			
  			print "<select name='idcursos' id='idcursos' onChange='location.href=this.value;'>
       				    <option style='margin-bottom:3px;$var_disabled' value='' selected disabled>--Selecione--</option>";
			
			while($row_curso = mysql_fetch_array($qr_curso)) {
				
				$margem++;
				
				if($margem != $verifica_curso) {
					$var_margem = ' style="margin-bottom:3px;"';
				} else {
					$var_margem = NULL;
				}
				
				$salario = number_format($row_curso['salario'],2,',','.');
				
				if($row_curso['0'] == $idcurso) {
					print "<option value='$row_curso[0]' selected$var_margem>$row_curso[0] - $row_curso[campo2] (Valor: $salario)</option>";
				} else {
					print "<option value='cadastroclt.php?regiao=$id_regiao&projeto=$projeto&idcursos=$row_curso[0]'$var_margem>$row_curso[0] - $row_curso[campo2] (Valor: $salario)</option>";
				}
				
			}
			
			print '</select>';
		} else {
			
			if(empty($projeto)) {
				print 'Selecione um Projeto';
			} else {
				print 'Nenhum Curso Cadastrado para o Projeto';
			}
				  
		} ?>
  </td>
 </tr>
  <tr>
  <td class="secao">Horário:</td>
  <td>
  <?php $qr_horarios = mysql_query("SELECT * FROM rh_horarios WHERE funcao = '$idcurso' AND id_regiao = '$id_regiao'");
  		$verifica_horario = mysql_num_rows($qr_horarios);
		  if(!empty($verifica_horario)) {
			
			print '<select name="horario" id="horario">
				   <option style="margin-bottom:3px;" selected disabled>--Selecione--</option>';
				   
			while($row_horarios = mysql_fetch_array($qr_horarios)) {
  
				$margem2++;
					
				if($margem2 != $verifica_horario) {
					$var_margem2 = ' style="margin-bottom:3px;"';
				} else {
					$var_margem2 = NULL;
				}
			
				print "<option value='$row_horarios[0]'$var_margem2>$row_horarios[0] - $row_horarios[nome] ( $row_horarios[entrada_1] - $row_horarios[saida_1] - $row_horarios[entrada_2] - $row_horarios[saida_2] )</option>";
				
        	}
			
			print '</select>';
		
		  } else {
			  
			if(empty($projeto)) {
				print 'Selecione um Projeto';
			} elseif(empty($idcurso) and !empty($verifica_curso)) {
				print 'Selecione um Curso';
			} else {
				print 'Nenhum Horário Cadastrado para o Curso';
			}
			
		  } ?>
  </td>
  </tr>
  <tr>
    <td class="secao">Unidade:</td>
    <td>
  	<?php $qr_unidade = mysql_query("SELECT * FROM unidade WHERE id_regiao = '$id_regiao' AND campo1 = '$projeto' ORDER BY unidade ASC");
		  $verifica_unidade = mysql_num_rows($qr_unidade);
		  if(!empty($verifica_unidade)) {
  			print '<select name="locacao" id="locacao">
       				    <option style="margin-bottom:3px;" selected disabled>--Selecione--</option>';
			
			while($row_unidade = mysql_fetch_array($qr_unidade)) {
				
				$margem3++;
				
				if($margem3 != $verifica_unidade) {
					$var_margem3 = ' style="margin-bottom:3px;"';
				} else {
					$var_margem3 = NULL;
				}
				print "<option value='$row_unidade[unidade]'$var_margem3>$row_unidade[id_unidade] - $row_unidade[unidade]</option>";
		
			}
			
			print '</select>';
		} else {
			
			if(empty($projeto)) {
				print 'Selecione um Projeto';
			} else {
				print 'Nenhum Curso Cadastrado para o Projeto';
			}
				  
		} ?>
  </td>
  </tr>
</table>
<table cellpadding="0" cellspacing="1" class="secao">
  <tr>
    <td class="secao_pai" colspan="6">DADOS PESSOAIS</td>
  </tr>
  <tr>
	<td class="secao">Nome:</td>
	<td colspan="3">
	<input name="nome" type="text" id="nome" size="75" onChange="this.value=this.value.toUpperCase();" onKeyPress="return(verificanome(this,event));"/>
	</td>
    <td class="secao">Data de Nascimento:</td>
    <td>
	<input name="data_nasc" type="text" id="data_nasc" size="15" maxlength="10"
		   onkeyup="mascara_data(this);"/>
    </td>
  </tr>
  <tr>
    <td class="secao" width="16%">Estado Civil:</td>
    <td width="16%">
	<select name="civil" id="civil">
		<option value="Solteiro">Solteiro</option>
		<option value="Casado">Casado</option>
		<option value="Vi&uacute;vo">Vi&uacute;vo</option>
		<option value="Sep. Judicialmente">Sep. Judicialmente</option>
		<option value="Divorciado">Divorciado</option>
	</select>
	</td>
    <td class="secao" width="16%">Sexo:</td>
    <td width="16%">
        <label><input name="sexo" type="radio" class="reset" value="M" checked /> Masculino</label><br>    		
        <label><input name="sexo" type="radio" class="reset" value="F" /> Feminino</label>
    </td>
    <td class="secao" width="16%">Nacionalidade:</td>
	<td width="16%">
	<input name="nacionalidade" type="text" id="nacionalidade" size="15" 
		   onchange="this.value=this.value.toUpperCase()"/>
	</td>
  </tr>
  <tr>
    <td class="secao">CEP:</td>
    <td colspan="5"><input name="cep" type="text" id="cep" maxlength="9" onkeypress="formatar('#####-###', this)"> <a style="cursor: pointer;" href="javascript: funcaowebservicecep();">completar endere&ccedil;o</a>
    </td>
  </tr>
  <tr>
    <td class="secao">Endereço:</td>
    <td>
	<input name="endereco" type="text" id="endereco" size="32" 
		   onchange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">N&uacute;mero:</td>
    <td>
	<input name="numero" type="text" id="numero" size="10" 
		   onchange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">Complemento:</td>
    <td>
	<input name="complemento" type="text" id="complemento" size="15" 
		   onchange="this.value=this.value.toUpperCase()"/>
    </td>
   </tr>
   <tr>
     <td class="secao">Bairro:</td>
     <td>
     <input name="bairro" type="text" id="bairro" size="16" 
		    onchange="this.value=this.value.toUpperCase()"/>
     </td>
     <td class="secao">Cidade:</td>
     <td><input name="cidade" type="text" id="cidade" size="16"/></td>
     <td class="secao">UF:</td>
     <td><input name="uf" type="text" id="uf" size="16"/></td>
  </tr>
  <tr>
    <td class="secao">Naturalidade:</td>
    <td>
	<input name="naturalidade" type="text" id="naturalidade" size="15"  
	       onchange="this.value=this.value.toUpperCase()"/>
	</td>
    <td class="secao">Estuda Atualmente?</td>
    <td>
       <label><input name="estuda" type="radio" class="reset" value="sim" checked="checked" /> SIM</label>
       <label><input name="estuda" type="radio" class="reset" value="não" /> NÃO</label>
    </td>
    <td class="secao">Término em:</td>
    <td>
       <input name="data_escola" type="text" id="data_escola" size="15" maxlength="10"
              onkeyup="mascara_data(this);" />
    </td>
  </tr>
  <tr>
    <td class="secao">Escolaridade:</td>
    <td>
      <select name="escolaridade" >
        <?php $qr_escolaridade = mysql_query("SELECT * FROM escolaridade WHERE status = 'on'");
             while($escolaridade = mysql_fetch_assoc($qr_escolaridade)) {
                echo '<option value="'.$escolaridade['id'].'">'.$escolaridade['cod'].' - '.$escolaridade['nome'].'</option>';
             } ?> 
      </select>
    </td>
    <td class="secao">Curso:</td>
    <td>
       <input name="curso" type="text" id="zona" size="16" 
              onchange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">Instituição:</td>
    <td>
       <input name="instituicao" type="text" id="titulo" size="15" 
              onchange="this.value=this.value.toUpperCase()"/>
    </td>
  </tr>
  <tr>
   <td class="secao">Telefone Fixo:</td>
   <td><input name="tel_fixo" type="text" id="tel_fixo" size="14" 
              onKeyPress="return(TelefoneFormat(this,event))"
              onkeyup="pula(13,this.id,tel_cel.id)" />
   </td>
   <td class="secao">Celular:</td>
   <td><input name="tel_cel" type="text" id="tel_cel" size="16" 
              onKeyPress="return(TelefoneFormat(this,event))" 
		      onkeyup="pula(13,this.id,tel_rec.id)" />
   </td>
   <td class="secao">Recado:</td>
   <td><input name="tel_rec" type="text" id="tel_rec" size="15" 
              onKeyPress="return(TelefoneFormat(this,event))" 
              onkeyup="pula(13,this.id,pai.id)" />
   </td>
  </tr>
</table>
<table cellpadding="0" cellspacing="1" class="secao">
  <tr>
    <td class="secao_pai" colspan="4">DADOS DA FAMÍLIA</td>
  </tr>
  <tr>
 	<td class="secao">Filiação - Pai:</td>
	<td>
      <input name="pai" type="text" id="pai" size="45" 
        	 onchange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">Nacionalidade Pai:</td>
	<td>
      <input name="nacionalidade_pai" type="text" id="nacionalidade_pai" size="15" 
             onchange="this.value=this.value.toUpperCase()"/>	
    </td>
  </tr>
  <tr>
    <td class="secao">Filiação - Mãe:</td>
    <td>
      <input name="mae" type="text" id="mae" size="45" 
             onchange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">Nacionalidade Mãe:</td>
    <td>
      <input name="nacionalidade_mae" type="text" id="nacionalidade_mae" size="15" 
             onchange="this.value=this.value.toUpperCase()"/>	
    </td>
  </tr>
  <tr>
    <td class="secao">Número de Filhos:</td>
    <td colspan="3">
		<input name="filhos" type="text" id="filhos" size="2" />
    </td>
  </tr>
  <tr>
    <td class="secao">Nome:</td>
    <td><input name="filho_1" type="text" id="filho_1" size="50" 
               onchange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">Nascimento:</td>
    <td><input name="data_filho_1" type="text" size="12" maxlength="10" id="data_filho_1"
               onkeyup="mascara_data(this); pula(10,this.id,filho_2.id)"
               onchange="this.value=this.value.toUpperCase()" />
    </td>
  </tr>
  <tr>
    <td class="secao">Nome:</td>
    <td><input name="filho_2" type="text" id="filho_2" size="50" 
               onchange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">Nascimento:</td>
    <td><input name="data_filho_2" type="text" size="12" maxlength="10" id="data_filho_2"
               onkeyup="mascara_data(this); pula(10,this.id,filho_3.id)"
               onchange="this.value=this.value.toUpperCase()" />
    </td>
  </tr>
  <tr>
    <td class="secao">Nome:</td>
    <td><input name="filho_3" type="text" id="filho_3" size="50" 
               onchange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">Nascimento:</td>
    <td><input name="data_filho_3" type="text" size="12" maxlength="10" id="data_filho_3"
               onkeyup="mascara_data(this); pula(10,this.id,filho_4.id)"
               onchange="this.value=this.value.toUpperCase()" />
    </td>
  </tr>
  <tr>
    <td class="secao">Nome:</td>
    <td><input name="filho_4" type="text" id="filho_4" size="50" 
               onchange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">Nascimento:</td>
    <td><input name="data_filho_4" type="text" size="12" maxlength="10" id="data_filho_4"
               onkeyup="mascara_data(this); pula(10,this.id,filho_5.id)"
               onchange="this.value=this.value.toUpperCase()" />
    </td>
  </tr>
  <tr>
    <td class="secao">Nome:</td>
    <td><input name="filho_5" type="text" id="filho_5" size="50" 
               onchange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">Nascimento:</td>
    <td><input name="data_filho_5" type="text" size="12" maxlength="10" id="data_filho_5"
               onkeyup="mascara_data(this);"
               onchange="this.value=this.value.toUpperCase()" />
    </td>
  </tr>
</table>
<table cellpadding="0" cellspacing="1" class="secao">
   <tr>
	  <td colspan="6" class="secao_pai">APARÊNCIA</td>
   </tr>
   <tr>
      <td class="secao">Cabelos:</td>
      <td>
        <select name="cabelos" id="cabelos">
        	<option>Não informado</option>
            <?php $qr_cabelos = mysql_query("SELECT * FROM tipos WHERE tipo = '1' AND status = '1'");
			   	  while($row_cabelos = mysql_fetch_array($qr_cabelos)) {
							print "<option>$row_cabelos[nome]</option>";
			   	  } ?>
        </select>
      </td>
	  <td class="secao">Olhos:</td>
      <td>
        <select name="olhos" id="olhos">
        	<option>Não informado</option>
            <?php $qr_olhos = mysql_query("SELECT * FROM tipos WHERE tipo = '2' AND status = '1'");
			   	  while($row_olhos = mysql_fetch_array($qr_olhos)) {
							print "<option>$row_olhos[nome]</option>";
			   	  } ?>
        </select>
     </td>
     <td class="secao">Peso:</td>
     <td>
        <input name="peso" type="text" id="peso" size="5" />
     </td>
  </tr>
  <tr>
     <td class="secao">Altura:</td>
     <td>
        <input name="altura" type="text" id="altura" size="5" />
     </td>
     <td class="secao">Etnia:</td>
     <td>
        <select name="etnia">
		<?php $qr_etnias = mysql_query("SELECT * FROM etnias WHERE status = 'on' ORDER BY id DESC");
              while($etnia = mysql_fetch_assoc($qr_etnias)) {
                    echo '<option value="'.$etnia['id'].'">'.$etnia['nome'].'</option>';
              } ?>
        </select>
     </td>
     <td class="secao">Marcas ou Cicatriz:</td>
     <td>
        <input name="defeito" type="text" id="defeito" size="18" 
               onchange="this.value=this.value.toUpperCase()"/>
     </td>
  </tr>
  <tr>
    <td class="secao">Deficiência:</td>
    <td colspan="5">
	<select name="deficiencia">
		<option value="">Não é portador de deficiência</option>
		<?php $qr_deficiencias = mysql_query("SELECT * FROM deficiencias WHERE status = 'on'");
			  while($deficiencia = mysql_fetch_assoc($qr_deficiencias)) {
					echo '<option value="'.$deficiencia['id'].'">'.$deficiencia['nome'].'</option>';
			  } ?>
    </select>
    </td>
 </tr>
 <tr>
    <td class="secao">Enviar Foto:</td>
	<td colspan="5">
        <input name="foto" type="checkbox" class="reset" id="foto" onClick="document.getElementById('arquivo').style.display = (document.getElementById('arquivo').style.display == 'none') ? '' : 'none' ;" value='1'/>
        <input name="arquivo" type="file" id="arquivo" size="60" style="display:none"/>
    </td>
  </tr>
</table>
<table cellpadding="0" cellspacing="1" class="secao">
  <tr>
    <td colspan="8" class="secao_pai">DOCUMENTAÇÃO</td>
  </tr>
  <tr>
    <td class="secao">Nº do RG:</td>
    <td>
	    <input name="rg" type="text" id="rg" size="13" maxlength="14"
                onkeypress="formatar('##.###.###-###', this)"
				onkeyup="pula(14,this.id,orgao.id)">
    </td>
    <td class="secao">Orgão Expedidor:</td>
    <td>
        <input name="orgao" type="text" id="orgao" size="8"
               onchange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">UF:</td>
    <td>
    <input name="uf_rg" type="text" id="uf_rg" size="2" maxlength="2"  
				onkeyup="pula(2,this.id,data_rg.id)"
                onchange="this.value=this.value.toUpperCase()"/></td>
    <td class="secao">Data Expedição:</td>
    <td>
		<input name="data_rg" type="text" size="12" maxlength="10" id="data_rg" 
               onkeyup="mascara_data(this); pula(10,this.id,cpf.id)" />
    </td>
  </tr>
  <tr>
    <td class="secao">CPF:</td>
    <td colspan="5">
      <input name="cpf" type="text" id="cpf" size="17" maxlength="14"
             onkeypress="formatar('###.###.###-##', this)" 
             onkeyup="pula(14,this.id,reservista.id)"/>
    </td>
    <td class="secao">Certificado de Reservista:</td>
    <td>
      <input name="reservista" type="text" id="reservista" size="18" />
    </td>
  </tr>
  <tr>
    <td class="secao">Nº Carteira de Trabalho:</td>
    <td>
      <input name="trabalho" type="text" id="trabalho" size="15" />
    </td>
    <td class="secao">Série:</td>
    <td>
     <input name="serie_ctps" type="text" id="serie_ctps" size="10" />		  
	</td>
    <td class="secao">UF:</td>
    <td>
	<input name="uf_ctps" type="text" id="uf_ctps" size="2" maxlength="2"            
		   onkeyup="pula(2,this.id,data_ctps.id)"
           onchange="this.value=this.value.toUpperCase()"/></td>
    <td class="secao">Data carteira de Trabalho:</td>
    <td>
      <input name="data_ctps" type="text" size="12" maxlength="10" id="data_ctps"
		     onkeyup="mascara_data(this); pula(10,this.id,titulo2.id)" />    
    </td>
  </tr>
  <tr>
    <td class="secao">Nº Título de Eleitor:</td>
    <td>
        <input name="titulo" type="text" id="titulo2" size="10" />
    </td>
    <td class="secao">Zona:</td>
    <td colspan="3">
        <input name="zona" type="text" id="zona2" size="3"/>
    </td>
    <td class="secao">Seção:</td>
    <td>
        <input name="secao" type="text" id="secao" size="3" />
    </td>
  </tr>
  <tr>
    <td class="secao">PIS:</td>
    <td>
      <input name="pis" type="text" id="pis" size="12" />
    </td>
    <td class="secao">Data Pis:</td>
    <td colspan="3">
    <input name="data_pis" type="text" size="12" maxlength="10" id="data_pis"
		onkeyup="mascara_data(this); pula(10,this.id,fgts.id)" />
	</td>
    <td class="secao">FGTS:</td>
    <td>
        <input name="fgts" type="text" id="fgts" size="10" />
    </td>
  </tr>
</table>
<table cellpadding="0" cellspacing="1" class="secao">
  <tr>
    <td colspan="6" class="secao_pai">BENEFÍCIOS</td>
  </tr>
  <tr>
    <td class="secao">Assistência Médica:</td>
    <td>
<label><input name="medica" type="radio" class="reset" value="1" <?=$chek_medi1?>>Sim</label> 
<label><input name="medica" type="radio" class="reset" value="0" <?=$chek_medi0?>>Não</label> <?=$mensagem_medi?>
    </td>
    <td class="secao">Tipo de Plano:</td>
    <td>
    <select name="plano_medico" id="plano_medico">
        <option value="1" <?=$selected_planoF?>>Familiar</option>
        <option value="2" <?=$selected_planoI?>>Individual</option>
    </select>
    </td>
  </tr>
  <tr>
    <td class="secao">Seguro, Apólice:</td>
    <td>
    <select name="apolice" id="apolice">
	   <option value="0">Não Possui</option>
   <?php $result_ap = mysql_query("SELECT * FROM apolice WHERE id_regiao = $row[regiao]");
         while($row_ap = mysql_fetch_array($result_ap)){
             if($row_ap['id_apolice'] == $row[apolice]){
                 print "<option value='$row_ap[id_apolice]' selected>$row_ap[razao]</option>";   
             } else {
                 print "<option value='$row_ap[id_apolice]'>$row_ap[razao]</option>";
             }
     } ?>
    </select>
    </td>
    <td class="secao">Dependente:</td>
    <td>
      <input name="dependente" type="text" id="dependente" size="20" value="<?=$row['campo2']?>"
             onchange="this.value=this.value.toUpperCase()"/>
    </td>
  </tr>
  <tr>
    <td class="secao">Insalubridade:</td>
    <td><input name="insalubridade" type="checkbox" class="reset" id="insalubridade2" value="1" /></td>  
	<td class="secao">Adicional Noturno:</td>
    <td>	
    <label><input name="ad_noturno" type="radio" class="reset" value="1">Sim</label>
    <label><input name="ad_noturno" type="radio" class="reset" value="0">Não</label>
      </td>
  </tr> 
  <tr>
    <td class="secao">Sem Desconto de INSS:</td>
    <td><label><input name='desconto_inss' type='checkbox' class="reset" value='1' /></label>
    </td>
    <td class="secao">Integrante do CIPA:</td>
    <td>
    <label><input name="cipa" type="radio" class="reset" value="1">Sim</label>
    <label><input name="cipa" type="radio" class="reset" value="0">Não</label>	
    </td>
  </tr>
  <tr>
    <td class="secao">Vale Transporte:</td>
    <td colspan="3">
    <input name="transporte" type="checkbox" class="reset" id="transporte2" onClick="document.getElementById('tablevale').style.display = (document.getElementById('tablevale').style.display == 'none') ? '' : 'none' ;" value="1" />
    </td>
  </tr>
</table>  
 
<table cellpadding="0" cellspacing="1" class="secao" id="tablevale" style="display:none">
  <tr>
    <td colspan="6" class="secao_pai">VALE TRANSPORTE</td>
  </tr>
  <tr>
    <td class="secao">Selecione 1:</td>
    <td colspan="4">
	  <select name="vale1" id="vale1">
	  <option value="0">Não Tem</option>
      
      <?php
		$resul_vale_trans = mysql_query("SELECT * FROM rh_tarifas WHERE id_regiao = '$id_regiao' and status_reg = '1'");
		
		while($row_vale_trans = mysql_fetch_array($resul_vale_trans)){
		$result_conce = mysql_query("SELECT * FROM rh_concessionarias WHERE id_concessionaria = '$row_vale_trans[id_concessionaria]'");
		$row_conce = mysql_fetch_array($result_conce);
		
		if($row_vale['id_tarifa1'] == "$row_vale_trans[0]"){
		print "<option value='$row_vale_trans[0]' selected>$row_vale_trans[valor] - $row_vale_trans[tipo] [$row_vale_trans[itinerario]] - 
		$row_conce[nome]</option>";
		}else{
		print "<option value='$row_vale_trans[0]'>$row_vale_trans[valor] - $row_vale_trans[tipo] [$row_vale_trans[itinerario]] - $row_conce[nome]
		</option>";
		}
		}
?>
</select>  
  </td>
</tr>  
<tr>
  <td class="secao">Selecione 2:</td>
  <td colspan="4">
	  <select name="vale2" id="vale2">
	  <option value="0">Não Tem</option>
      <?php
$resul_vale_trans2 = mysql_query("SELECT * FROM rh_tarifas WHERE id_regiao = '$id_regiao' and status_reg = '1'");
while($row_vale_trans2 = mysql_fetch_array($resul_vale_trans2)){
$result_conce2 = mysql_query("SELECT * FROM rh_concessionarias WHERE id_concessionaria = '$row_vale_trans2[id_concessionaria]'");
$row_conce2 = mysql_fetch_array($result_conce2);
if($row_vale['id_tarifa2'] == "$row_vale_trans2[0]"){
print "<option value='$row_vale_trans2[0]' selected>$row_vale_trans2[valor] - $row_vale_trans2[tipo] [$row_vale_trans2[itinerario]] - $row_conce2[nome]</option>";
}else{
print "<option value='$row_vale_trans2[0]'>$row_vale_trans2[valor] - $row_vale_trans2[tipo] [$row_vale_trans2[itinerario]] - $row_conce2[nome]</option>";
}
}
?></select>  
  </td>
</tr>
  <tr>
    <td class="secao">Selecione 3:</td>
    <td colspan="4">
	  <select name="vale3" id="vale3">
	  <option value="0">Não Tem</option>
      <?php
	  
$resul_vale_trans3 = mysql_query("SELECT * FROM rh_tarifas WHERE id_regiao = '$id_regiao' and status_reg = '1'");
while($row_vale_trans3 = mysql_fetch_array($resul_vale_trans3)){
$result_conce3 = mysql_query("SELECT * FROM rh_concessionarias WHERE id_concessionaria = '$row_vale_trans3[id_concessionaria]'");
$row_conce3 = mysql_fetch_array($result_conce3);
if($row_vale['id_tarifa3'] == "$row_vale_trans3[0]"){
print "<option value='$row_vale_trans3[0]' selected>$row_vale_trans3[valor] - $row_vale_trans3[tipo] [$row_vale_trans3[itinerario]] - $row_conce3[nome]</option>";
}else{
print "<option value='$row_vale_trans3[0]'>$row_vale_trans3[valor] - $row_vale_trans3[tipo] [$row_vale_trans3[itinerario]] - $row_conce3[nome]</option>";
}
}
?>
     </select>  
   </td>
</tr>
<tr>
  <td class="secao">Selecione 4:</td>
  <td colspan="4">
	  <select name="vale4" id="vale4">
	  <option value="0">Não Tem</option>
      <?php
      
$resul_vale_trans4 = mysql_query("SELECT * FROM rh_tarifas WHERE id_regiao = '$id_regiao' and status_reg = '1'");
while($row_vale_trans4 = mysql_fetch_array($resul_vale_trans4)){
$result_conce4 = mysql_query("SELECT * FROM rh_concessionarias WHERE id_concessionaria = '$row_vale_trans4[id_concessionaria]'");
$row_conce4 = mysql_fetch_array($result_conce4);
if($row_vale['id_tarifa4'] == "$row_vale_trans4[0]"){
print "<option value='$row_vale_trans4[0]' selected>$row_vale_trans4[valor] - $row_vale_trans4[tipo] [$row_vale_trans4[itinerario]] - $row_conce4[nome]</option>";
}else{
print "<option value='$row_vale_trans4[0]'>$row_vale_trans4[valor] - $row_vale_trans4[tipo] [$row_vale_trans4[itinerario]] - $row_conce4[nome]</option>";
}
}
?>
</select>  
</td>
</tr>
<tr>
    <td class="secao">Selecione 5:</td>
    <td colspan="4">
	  <select name="vale5" id="vale5">
	  <option value="0">Não Tem</option>
      <?php
      
$resul_vale_trans5 = mysql_query("SELECT * FROM rh_tarifas WHERE id_regiao = '$id_regiao' and status_reg = '1'");
while($row_vale_trans5 = mysql_fetch_array($resul_vale_trans5)){
$result_conce5 = mysql_query("SELECT * FROM rh_concessionarias WHERE id_concessionaria = '$row_vale_trans5[id_concessionaria]'");
$row_conce5 = mysql_fetch_array($result_conce5);
if($row_vale['id_tarifa5'] == "$row_vale_trans5[0]"){
print "<option value='$row_vale_trans5[0]' selected>$row_vale_trans5[valor] - $row_vale_trans5[tipo] [$row_vale_trans5[itinerario]] - $row_conce5[nome]</option>";
}else{
print "<option value='$row_vale_trans5[0]'>$row_vale_trans5[valor] - $row_vale_trans5[tipo] [$row_vale_trans5[itinerario]] - $row_conce5[nome]</option>";
}
}
?>
  </select>  
  </td>
</tr>
<tr>
  <td class="secao">Selecione 6:</td>
  <td colspan="4">
	  <select name="vale6" id="vale6">
	  <option value="0">Não Tem</option>
      
      <?php
      
$resul_vale_trans6 = mysql_query("SELECT * FROM rh_tarifas WHERE id_regiao = '$id_regiao' and status_reg = '1'");
while($row_vale_trans6 = mysql_fetch_array($resul_vale_trans6)){
$result_conce6 = mysql_query("SELECT * FROM rh_concessionarias WHERE id_concessionaria = '$row_vale_trans6[id_concessionaria]'");
$row_conce6 = mysql_fetch_array($result_conce6);
if($row_vale['id_tarifa6'] == "$row_vale_trans6[0]"){
print "<option value='$row_vale_trans6[0]' selected>$row_vale_trans6[valor] - $row_vale_trans6[tipo] [$row_vale_trans6[itinerario]] - $row_conce6[nome]</option>";
}else{
print "<option value='$row_vale_trans6[0]'>$row_vale_trans6[valor] - $row_vale_trans6[tipo] [$row_vale_trans6[itinerario]] - $row_conce6[nome]</option>";
}
}
?>
</select>  
</td>
</tr>
  <tr>
   <td class="secao">Numero Cartão 1:</td>
   <td>
    <input name="num_cartao" type="text" id="num_cartao" size="20" value="<?=$row_vale['cartao1']?>"
           onchange="this.value=this.value.toUpperCase()"/>
   </td>
   <td class="secao">Numero Cartão 2:</td>
   <td>
    <input name="num_cartao2" type="text" id="num_cartao2" size="20" value="<?=$row_vale['cartao2']?>"
           onchange="this.value=this.value.toUpperCase()"/>
   </td>
  </tr>
</table>
<table cellpadding="0" cellspacing="1" class="secao">
  <tr>
    <td colspan="2" class="secao_pai">SINDICATO</td>
  </tr>
 <tr>
    <td width="20%" class="secao">Possui Sindicato:</td>
    <td width="80%">
		<label><input name="radio_sindicato" type="radio" class="reset" onClick="document.getElementById('trsindicato').style.display = '';" value="sim" >Sim</label>
		<label><input name='radio_sindicato' type='radio' class="reset" onClick="document.getElementById('trsindicato').style.display = 'none';" value='nao' checked='checked' >Não</label>
    </td>
  </tr>
  <tr style="display:none" id="trsindicato">
    <td class="secao">Selecionar:</td>
    <td>
	 <select name="sindicato" id="sindicato" >
        <option value="">Selecione</option>
        <?php 
		$re_sindicato = mysql_query("SELECT * FROM rhsindicato WHERE id_regiao = '$id_regiao'" );
		while($row_sindi = mysql_fetch_array($re_sindicato)) {
			echo "<option value='".$row_sindi['id_sindicato']."'>".substr($row_sindi['nome'],0,80)."</option>";	
		} 
		?>
	</select>
    </td>
  </tr>
</table> 
<table cellpadding="0" cellspacing="1" class="secao">
  <tr>
    <td colspan="4" class="secao_pai">DADOS BANCÁRIOS</td>
  </tr>
  <tr>
    <td width="15%" class="secao">Banco:</td>
    <td width="30%">
	  <select name="banco" id="banco">
      <option value="0">Sem Banco</option>
       <?php $result_banco = mysql_query("SELECT * FROM bancos WHERE id_projeto = '$projeto' AND status_reg = '1'");
             while($row_banco = mysql_fetch_array($result_banco)) {
	               print "<option value='$row_banco[0]'>$row_banco[id_banco] - $row_banco[nome]</option>";
             } ?>
             <option value="9999">Outro Banco</option>
     </select>
    </td>
    <td width="25%" class="secao">Agência:</td>
    <td width="30%">
      <input name="agencia" type="text" id="agencia" size="12" />
    </td>
  </tr>
  <tr>
    <td class="secao">Conta:</td>
    <td>
        <input name="conta" type="text" id="conta" size="12" /><br />
        <label><input name="radio_tipo_conta" type="radio" class="reset" value="salario">Conta Salário </label>
        <label><input name="radio_tipo_conta" type="radio" class="reset" value="corrente">Conta Corrente </label>
    </td>
    <td class="secao">Nome do Banco:<br />(caso não esteja na lista acima)</td>
    <td><input name="nomebanco" type="text" id="nomebanco" size="25" 
               onchange="this.value=this.value.toUpperCase()"/>
    </td>
  </tr>
</table>
<table cellpadding="0" cellspacing="1" class="secao">
  <tr>
    <td colspan="4" class="secao_pai">DADOS FINANCEIROS E DE CONTRATO</td>
  </tr>
  <tr>
    <td class="secao">Data de Entrada:</td>
    <td>
    <input name="data_entrada" type="text" size="12" maxlength="10" id="data_entrada"
           onkeyup="mascara_data(this); pula(10,this.id,data_exame.id)" />
    </td>
    <td class="secao">Data do Exame Admissional:</td>
    <td>
    <input name="data_exame" type="text" size="12" maxlength="10" id="data_exame"
           onkeyup="mascara_data(this); pula(10,this.id,localpagamento.id)" />
    </td>
  </tr>
  <tr>
    <td class="secao">Local de Pagamento:</td>
    <td width="20%">
    <input name="localpagamento" type="text" id="localpagamento" size="25"  
           onchange="this.value=this.value.toUpperCase()"/>
    </td>
    <td width="19%" class="secao">Tipo de admiss&atilde;o</td>
    <td width="38%">
      <select name="tipo_admissao" id="tipo_admissao">
      <option value="">Selecione um tipo de admissão</option>
      	<?php 
			$tipo_admissoes = array(
								 10 => "Primeiro emprego",
								 20 => "Reemprego",
								 25 => "Contrato por prazo determinado",
								 35 => "Reintegração",
								 70 => "Trnsferência da entrada"
								 );
			foreach($tipo_admissoes as $num => $tipo):?>
            <option value="<?=$num?>"><?=$tipo?></option>
		<?php endforeach; ?>
      </select></td>
  </tr>
  <tr>
    <td class="secao">Tipo de Pagamento:</td>
    <td colspan="3">
    <select name="tipopg" id="tipopg">
    <?php $result_pg = mysql_query("SELECT * FROM tipopg WHERE id_projeto = '$projeto'");
          while($row_pg = mysql_fetch_array($result_pg)) {
                print "<option value='$row_pg[id_tipopg]'>$row_pg[tipopg]</option>";
          } ?>
    </select>
    </td>
  </tr>
  <tr>
    <td class="secao">Observações:</td>
    <td colspan="3">
    <textarea name="observacoes" id="observacoes" cols="55" rows="4"  onchange="this.value=this.value.toUpperCase()"></textarea></td>
  </tr>
</table>
	<div id="finalizacao">
         O Contrato foi <strong>assinado</strong>?
         <input name="impressos2" type="checkbox" class="reset" id="impressos2" value="1" />
     <p>&nbsp;</p>
     <p>Outros documentos foram <strong>assinados</strong>?<br>
       <label>
         <input name='assinatura3' type='radio' class="reset" id='assinatura3' value='1'> 
         Sim </label>
       <label>
         <input name='assinatura3' type='radio' class="reset" id='assinatura3' value='0'> 
         N&atilde;o</label>
	    <?=$mensagem_ass?>                 
	 </p>
    </div>
	<div id="observacao">NÃO DEIXE DE CONFERIR OS DADOS APÓS A DIGITAÇÃO</div>
	<div align="center">
<input type="submit" name="Submit" value="CADASTRAR" class="botao" /></div> 
<input type="hidden" name="regiao" value="<?=$id_regiao?>"/>
<input type="hidden" name="projeto" value="<?=$projeto?>" />
<input type="hidden" name="user" value="<?=$id_user?>" />
<input type="hidden" name="update" value="1" />
</form>
</td>
</tr>
</table>
</div>
<script language="javascript" type="text/javascript">
function validaForm() {
	d = document.form1;
	if(d.nome.value == '') {
		alert('O campo nome deve ser preenchido!');
		d.nome.focus();
		return false;
	}
	if(d.data_nasc.value == '') {
		alert('O campo data de nascimento deve ser preenchido!');
		d.data_nasc.focus();
		return false;
	}
	if(d.endereco.value == '') {
		alert('O campo endereço deve ser preenchido!');
		d.endereco.focus();
		return false;
	}
	if(d.rg.value == '') {
		alert('O campo RG deve ser preenchido!');
		d.rg.focus();
		return false;
	}
	if(d.cpf.value == '') {
		alert('O campo CPF deve ser preenchido!');
		d.cpf.focus();
		return false;
	}
	if(d.localpagamento.value == '') {
		alert('O campo local de pagamento deve ser preenchido!');
		d.localpagamento.focus();
		return false;
	}
	if(d.tipo_admicao.value == '') {
		alert('O campo tipo de admissão deve ser preenchido!');
		d.tipo_admicao.focus();
		return false;
	}
return true;
}

$(function() {
	$('#data_nasc').datepicker({
		changeMonth: true,
	    changeYear: true
	});
	$('#data_escola').datepicker({
		changeMonth: true,
	    changeYear: true
	});
	$('#data_filho_1').datepicker({
		changeMonth: true,
	    changeYear: true
	});
	$('#data_filho_2').datepicker({
		changeMonth: true,
	    changeYear: true
	});
	$('#data_filho_3').datepicker({
		changeMonth: true,
	    changeYear: true
	});
	$('#data_filho_4').datepicker({
		changeMonth: true,
	    changeYear: true
	});
	$('#data_filho_5').datepicker({
		changeMonth: true,
	    changeYear: true
	});
	$('#data_rg').datepicker({
		changeMonth: true,
	    changeYear: true
	});
	$('#data_ctps').datepicker({
		changeMonth: true,
	    changeYear: true
	});
	$('#data_pis').datepicker({
		changeMonth: true,
	    changeYear: true
	});
	$('#data_entrada').datepicker({
		changeMonth: true,
	    changeYear: true
	});
	$('#data_exame').datepicker({
		changeMonth: true,
	    changeYear: true
	});
});
</script>
</body>
</html>
<?php } else { // CADASTRO DE CLT

$regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['projeto'];

// Dados de Contratação
$vinculo = $_REQUEST['vinculo'];
$tipo_contratacao = $_REQUEST['contratacao'];
$id_curso = $_REQUEST['idcursos'];
$locacao = $_REQUEST['locacao'];
$horario = $_REQUEST['horario'];

// Dados Pessoais
$nome = $_REQUEST['nome'];
$sexo = $_REQUEST['sexo'];
$endereco = $_REQUEST['endereco'];
$numero = $_REQUEST['numero'];
$complemento = $_REQUEST['complemento'];
$bairro = $_REQUEST['bairro'];
$cidade = $_REQUEST['cidade'];
$uf = $_REQUEST['uf'];
$cep = $_REQUEST['cep'];
$tel_fixo = $_REQUEST['tel_fixo'];
$tel_cel = $_REQUEST['tel_cel'];
$tel_rec = $_REQUEST['tel_rec'];
$data_nasc = $_REQUEST['data_nasc'];
$naturalidade = $_REQUEST['naturalidade'];
$nacionalidade = $_REQUEST['nacionalidade'];
$civil = $_REQUEST['civil'];

// Documentação
$rg = $_REQUEST['rg'];
$uf_rg = $_REQUEST['uf_rg'];
$secao = $_REQUEST['secao'];
$data_rg = $_REQUEST['data_rg'];
$cpf = $_REQUEST['cpf'];
$titulo = $_REQUEST['titulo'];
$zona = $_REQUEST['zona'];
$orgao = $_REQUEST['orgao'];

// Mais
$pai = $_REQUEST['pai'];
$mae = $_REQUEST['mae'];
$nacionalidade_pai = $_REQUEST['nacionalidade_pai'];
$nacionalidade_mae = $_REQUEST['nacionalidade_mae'];
$estuda = $_REQUEST['estuda'];
$data_escola = $_REQUEST['data_escola'];
$escolaridade = $_REQUEST['escolaridade'];
$instituicao = $_REQUEST['instituicao'];
$curso = $_REQUEST['curso'];

// Dados Financeiros
$data_entrada = $_REQUEST['data_entrada'];
$banco = $_REQUEST['banco'];
$agencia = $_REQUEST['agencia'];
$conta = $_REQUEST['conta'];
$nomebanco = $_REQUEST['nomebanco'];
$tipoDeConta = $_REQUEST['radio_tipo_conta'];
$localpagamento = $_REQUEST['localpagamento'];
$apolice = $_REQUEST['apolice'];
$campo1 = $_REQUEST['trabalho'];
$campo2 = $_REQUEST['dependente'];
$campo3 = $_REQUEST['codigo'];
$data_cadastro = date('Y-m-d');
$pis = $_REQUEST['pis'];
$fgts = $_REQUEST['fgts'];
$tipopg = $_REQUEST['tipopg'];
$filhos = $_REQUEST['filhos'];
$observacoes = $_REQUEST['observacoes'];
$medica = $_REQUEST['medica'];
$assinatura2 = $_REQUEST['assinatura2'];
$assinatura3 = $_REQUEST['assinatura3'];
$plano_medico = $_REQUEST['plano_medico'];
$serie_ctps = $_REQUEST['serie_ctps'];
$uf_ctps = $_REQUEST['uf_ctps'];
$pis_data = $_REQUEST['data_pis'];

if(empty($_REQUEST['insalubridade'])){
   $insalubridade = '0';
} else {
   $insalubridade = $_REQUEST['insalubridade'];
}
if(empty($_REQUEST['transporte'])) {
   $transporte = '0';
} else {
   $transporte = $_REQUEST['transporte'];
}
if(empty($_REQUEST['impressos2'])) {
   $impressos = '0';
} else {
   $impressos = $_REQUEST['impressos2'];
}
// Desconto INSS
if(empty($_REQUEST['desconto_inss'])) {
	$desconto_inss = "0";
} else {
	$desconto_inss = "1";
}

$tipo_vale = $_REQUEST['tipo_vale'];
$num_cartao = $_REQUEST['num_cartao'];
$num_cartao2 = $_REQUEST['num_cartao2'];
$vale1 = $_REQUEST['vale1'];
$vale2 = $_REQUEST['vale2'];
$vale3 = $_REQUEST['vale3'];
$vale4 = $_REQUEST['vale4'];
$vale5 = $_REQUEST['vale5'];
$vale6 = $_REQUEST['vale6'];
$ad_noturno = $_REQUEST['ad_noturno'];
$exame_data = $_REQUEST['data_exame'];
$trabalho_data = $_REQUEST['data_ctps'];
$reservista = $_REQUEST['reservista'];
$cabelos = $_REQUEST['cabelos'];
$peso = $_REQUEST['peso'];
$altura = $_REQUEST['altura'];
$olhos = $_REQUEST['olhos'];
$defeito = $_REQUEST['defeito'];
$cipa = $_REQUEST['cipa'];
$etnia = $_REQUEST['etnia'];
$deficiencia = $_REQUEST['deficiencia'];
$tipo_admissao = $_REQUEST['tipo_admissao'];
$filho_1 = $_REQUEST['filho_1'];
$filho_2 = $_REQUEST['filho_2'];
$filho_3 = $_REQUEST['filho_3'];
$filho_4 = $_REQUEST['filho_4'];
$filho_5 = $_REQUEST['filho_5'];
$data_filho_1 = $_REQUEST['data_filho_1'];
$data_filho_2 = $_REQUEST['data_filho_2'];
$data_filho_3 = $_REQUEST['data_filho_3'];
$data_filho_4 = $_REQUEST['data_filho_4'];
$data_filho_5 = $_REQUEST['data_filho_5'];

// Request referente ao sindicato do funcionário
$SINDICATO = $_REQUEST['sindicato'];
if(empty($_REQUEST['foto'])) {
	$foto = '0';
} else {
	$foto = $_REQUEST['foto'];
}
if($foto == "1") {
   $foto_banco = '1';
   $foto_up = '1';
} else {
   $foto_banco = '0';
   $foto_up = '0';
}

/* Função para converter a data */
function ConverteData($data) {
   if(strstr($data, '/')) {
       $nova_data = implode('-', array_reverse(explode('/', $data)));
 	   return $nova_data;
   } elseif(strstr($data, '-')) {
       $nova_data = implode('/', array_reverse(explode('-', $data)));
       return $nova_data;
   } else {
       return '';
   }
}

$data_filho_1  = ConverteData($data_filho_1);
$data_filho_2  = ConverteData($data_filho_2);
$data_filho_3  = ConverteData($data_filho_3);
$data_filho_4  = ConverteData($data_filho_4);
$data_filho_5  = ConverteData($data_filho_5);
$data_nasc     = ConverteData($data_nasc);
$data_rg       = ConverteData($data_rg);
$data_escola   = ConverteData($data_escola);
$data_entrada  = ConverteData($data_entrada);
$pis_data      = ConverteData($pis_data);
$exame_data    = ConverteData($exame_data);
$trabalho_data = ConverteData($trabalho_data);

// VERIFICANDO SE O FUNCIONÁRIO JA ESTÁ CADASTRADO NA TABELA CLT
$verificando_clt = mysql_query("SELECT nome FROM rh_clt WHERE nome = '$nome' AND data_nasci = '$data_nasc' AND rg = '$rg' AND status != '0' AND status != '60' AND status != '61' AND status != '62' AND status != '63' AND status != '64' AND status != '81' AND status != '101'");
$row_verificando_clt = mysql_num_rows($verificando_clt);

if (!empty($row_verificando_clt)) {
print "
<html>
<head>
<title>:: Intranet ::</title>
</head>
<body>
ESTE PARTICIPANTE JA ESTÁ CADASTRADO: <b>$row_verificando_clt[nome]</b>
</body>
</html>
";
exit; 

} else { // CASO O FUNCIONÁRIO NÃO ESTEJA CADASTRADO VAI RODAR O INSERT

$result_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$id_projeto'");
$row_projeto    = mysql_fetch_array($result_projeto);
$data_cadastro  = date('Y-m-d');
mysql_query ("INSERT INTO rh_clt
(id_projeto,id_regiao,localpagamento,locacao,nome,sexo,endereco,numero,complemento,bairro,cidade,uf,cep,tel_fixo,tel_cel,tel_rec,
data_nasci,naturalidade,nacionalidade,civil,rg,orgao,data_rg,cpf,titulo,zona,secao,pai,nacionalidade_pai,mae,nacionalidade_mae,
estuda,data_escola,escolaridade,instituicao,curso,tipo_contratacao,banco,agencia,conta,tipo_conta,id_curso,apolice,status,data_entrada,data_saida,
campo1,campo2,campo3,data_exame,reservista,etnia,deficiencia,cabelos,altura,olhos,peso,defeito,cipa,ad_noturno,plano,assinatura,distrato,
outros,pis,dada_pis,data_ctps,serie_ctps,uf_ctps,uf_rg,fgts,insalubridade,transporte,medica,tipo_pagamento,nome_banco,num_filhos,
observacao,impressos,sis_user,data_cad,foto,rh_vinculo,rh_status,rh_horario,rh_sindicato,rh_cbo,desconto_inss,status_admi) 
VALUES
('$id_projeto','$regiao','$localpagamento','$locacao','$nome','$sexo','$endereco','$numero','$complemento','$bairro','$cidade','$uf',
'$cep','$tel_fixo','$tel_cel','$tel_rec','$data_nasc','$naturalidade','$nacionalidade','$civil','$rg',
'$orgao','$data_rg','$cpf','$titulo','$zona','$secao','$pai','$nacionalidade_pai','$mae','$nacionalidade_mae','$estuda',
'$data_escola','$escolaridade','$instituicao','$curso','$tipo_contratacao','$banco','$agencia','$conta','$tipoDeConta','$id_curso','$apolice',
'10','$data_entrada','0000-00-00','$campo1','$campo2','$campo3','$exame_data','$reservista','$etnia','$deficiencia','$cabelos','$altura','$olhos',
'$peso','$defeito',
'$cipa','$ad_noturno','$plano_medico','$impressos','$assinatura2','$assinatura3','$pis','$pis_data','$trabalho_data','$serie_ctps',
'$uf_ctps','$uf_rg','$fgts','$insalubridade','$transporte','$medica','$tipopg','$nomebanco','$filhos','$observacoes','$impressos',
'$id_user_login','$data_cadastro','$foto_banco','$vinculo','$rh_status','$horario','$SINDICATO','$rh_cbo','$desconto_inss','$tipo_admissao')")
or die ("$mensagem_erro<br><BR>" . mysql_error());

$row_id_participante = mysql_insert_id();
$row_id_clt = $row_id_participante;
} // AQUI TERMINA DE INSERIR OS DADOS DO CLT

$id_bolsista = $row_id_participante;

if($transporte == '1') {
	mysql_query ("INSERT INTO rh_vale(id_clt,id_regiao,id_projeto,id_tarifa1,id_tarifa2,id_tarifa3,id_tarifa4,
id_tarifa5,id_tarifa6,cartao1,cartao2) VALUES 
('$row_id_participante','$regiao','$projeto','$vale1','$vale2','$vale3','$vale4','$vale5','$vale6','$num_cartao','$num_cartao2')") or die ("$mensagem_erro - 2.3<br><br>".mysql_error());
}

if($filhos == '' or $filhos == 0) {
	$naa = '0';
} else {
	mysql_query ("INSERT INTO dependentes(id_regiao,id_projeto,id_bolsista,contratacao,nome,data1,nome1,data2,nome2,data3,nome3,data4,nome4,data5,nome5) VALUES
('$regiao','$id_projeto','$row_id_participante','$tipo_contratacao','$nome','$data_filho_1','$filho_1','$data_filho_2','$filho_2','$data_filho_3','$filho_3','$data_filho_4','$filho_4','$data_filho_5','$filho_5')") or    die ("$mensagem_erro 2.4<br><br>".mysql_error());
	$naa = "2";
}

$n_id_curso = sprintf("%04d",$id_curso);
$n_regiao   = sprintf("%04d",$regiao);
$n_id_bolsista = sprintf("%04d",$row_id_participante);
$cpf2 = str_replace(".","", $cpf);
$cpf2 = str_replace("-","", $cpf2);

// GERANDO A SENHA ALEATÓRIA
$target = "%%%%%%";
    $senha = "";
	$dig = "";
    $consoantes = "bcdfghjkmnpqrstvwxyz1234567890bcdfghjkmnpqrstvwxyz123456789"; 
    $vogais = "aeiou"; 
    $numeros = "123456789bcdfghjkmnpqrstvwxyzaeiou"; 
    $a = strlen($consoantes)-1; 
    $b = strlen($vogais)-1; 
    $c = strlen($numeros)-1; 
    for($x=0;$x<=strlen($target)-1;$x++) { 
        if(substr($target,$x,1) == "@") { 
            $rand = mt_rand(0,$c); 
            $senha .= substr($numeros,$rand,1); 
        } elseif(substr($target,$x,1) == "%") { 
            $rand = mt_rand(0,$a); 
            $senha .= substr($consoantes,$rand,1); 
        } elseif(substr($target,$x,1) == "&") { 
            $rand = mt_rand(0,$b); 
            $senha .= substr($vogais,$rand,1); 
        } else { 
            die("<b>Erro!</b><br><i>$target</i> é uma expressão inválida!<br><i>".substr($target,$x,1)."</i> é um caractér inválido.<br>"); 
        } 
    } 
$matricula = "$n_id_curso.$n_regiao.$n_id_bolsista-00";
mysql_query ("insert into tvsorrindo(id_clt,id_projeto,nome,cpf,matricula,senha,inicio) values
('$row_id_participante','$id_projeto','$nome','$cpf','$matricula','$senha','$inicio')") or die ("$mensagem_erro<br><Br>");

//FAZENDO O UPLOAD DA FOTO
$arquivo = isset($_FILES['arquivo']) ? $_FILES['arquivo'] : FALSE;
if($foto_up == '1') {
if(!$arquivo) {
    $mensagem = "Não acesse esse arquivo diretamente!";
}
// Imagem foi enviada, então a move para o diretório desejado
else {
    $nome_arq = str_replace(" ", "_", $nome);	
    $tipo_arquivo = ".gif";
	// Resolvendo o nome e para onde o arquivo será movido
    $diretorio = "../fotos/clt/";
	$nome_tmp = $regiao."_".$id_projeto."_".$row_id_participante.$tipo_arquivo;
	$nome_arquivo = "$diretorio$nome_tmp" ;
	
	move_uploaded_file($arquivo['tmp_name'], $nome_arquivo ) or die ("Erro ao enviar o Arquivo: $nome_arquivo");
}
}
header("Location: ver_clt.php?reg=$regiao&clt=$row_id_participante&pro=$id_projeto&sucesso=cadastro");
exit;
}
?>