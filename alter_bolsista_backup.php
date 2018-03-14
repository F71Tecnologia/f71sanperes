<?php
if(empty($_COOKIE['logado'])) {
print "Efetue o Login<br><a href='login.php'>Logar</a>";
exit;
}

include "conn.php";

if(empty($_REQUEST['update'])) {

$id_bolsista = $_REQUEST['bol'];
$id_projeto = $_REQUEST['pro'];
$result = mysql_query("SELECT *, date_format(data_nasci, '%d/%m/%Y') AS data_nascimento,
								 date_format(data_rg, '%d/%m/%Y') AS data_rg2,
								 date_format(data_escola, '%d/%m/%Y') AS data_escola2,
								 date_format(data_entrada, '%d/%m/%Y') AS data_entrada2,
								 date_format(data_exame, '%d/%m/%Y') AS data_exame,
								 date_format(data_saida, '%d/%m/%Y') AS data_saida,
								 date_format(data_ctps, '%d/%m/%Y') AS data_ctps2,
								 date_format(dada_pis, '%d/%m/%Y') AS dada_pis2
								 FROM autonomo WHERE id_autonomo = '$id_bolsista'");
$row = mysql_fetch_array($result);

$qr_vale = mysql_query("SELECT * FROM vale WHERE id_bolsista = '$row[0]' AND id_projeto = '$row[id_projeto]'");
$row_vale = mysql_fetch_array($qr_vale);

$qr_dependentes = mysql_query("SELECT *, date_format(data1, '%d/%m/%Y') AS datas1, 
									   	 date_format(data2, '%d/%m/%Y') AS datas2, 
									     date_format(data3, '%d/%m/%Y') AS datas3, 
									     date_format(data4, '%d/%m/%Y') AS datas4, 
									     date_format(data5, '%d/%m/%Y') AS datas5 
									     FROM dependentes WHERE id_bolsista = '$id_bolsista' 
									     AND id_projeto = '$id_projeto' AND contratacao = '$row[tipo_contratacao]'");
$row_depe = mysql_fetch_array($qr_dependentes);

$qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$row[id_projeto]'");
$row_pro = mysql_fetch_array($qr_projeto);

$qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$row[id_regiao]'");
$row_reg = mysql_fetch_array($qr_regiao);

$qr_curso = mysql_query("SELECT * FROM curso WHERE id_curso = '$row[id_curso]'");
$row_curso = mysql_fetch_array($qr_curso);

if($row['insalubridade'] == '1') {
	$chek1 = 'checked';
} else {
	$chek1 = NULL;
}

if($row_vale['status_vale'] == '1') {
	$chek2 = 'checked';
}else{
	$chek2 = NULL;
}

if($row['assinatura'] == '1') {
	$selected_ass_sim = 'checked';
	$selected_ass_nao = NULL;
} elseif($row['assinatura'] == '0') {
	$selected_ass_sim = NULL;
	$selected_ass_nao = 'checked"';
} else {
	$selected_ass_sim = NULL;
	$selected_ass_nao = NULL;
	$mensagem_ass = '<font color=red size=1><b>Não marcado</b></font>';
}

if($row['distrato'] == '1') {
	$selected_ass_sim2 = 'checked';
	$selected_ass_nao2 = NULL;
} elseif($row['distrato'] == "0") {
	$selected_ass_sim2 = NULL;
	$selected_ass_nao2 = 'checked';
}

if($row['outros'] == '1') {
	$selected_ass_sim3 = 'checked';
	$selected_ass_nao3 = NULL;
} elseif($row['outros'] == "0") {
	$selected_ass_sim3 = NULL;
	$selected_ass_nao3 = 'checked';
}

if($row['sexo'] == 'M') {
	$chekH = 'checked';
	$chekF = NULL;
	$mensagem_sexo = NULL;
} elseif($row['sexo'] == 'F') {
	$chekH = NULL;
	$chekF = 'checked';
	$mensagem_sexo = NULL;
} else {
	$chekH = NULL;
	$chekF = NULL;
	$mensagem_sexo = '<font color=red size=1><b>Cadastrar Sexo</b></font>';
}

if($row['medica'] == '0') {
	$chek_medi0 = 'checked';
	$chek_medi1 = NULL;
	$mensagem_medi = NULL;
} elseif($row['medica'] == '1') {
	$chek_medi0 = NULL;
	$chek_medi1 = 'checked';
	$mensagem_medi = NULL;
} else {
	$chek_medi0 = NULL;
	$chek_medi1 = NULL;
	$mensagem_medi = '<font color=red size=1><b>Selecione uma opção</b></font>';
}

if($row['plano'] == '1') {
	$selected_planoF = 'selected';
	$selected_planoI = NULL;
} else {
	$selected_planoF = NULL;
	$selected_planoI = 'selected';
}

if($row_vale['tipo_vale'] == '1') {
	$selected_valeC = 'selected';
	$selected_valeP = NULL;
	$selected_valeA = NULL;
} elseif($row_vale['tipo_vale'] == '2') {
	$selected_valeC = NULL;
	$selected_valeP = 'selected';
	$selected_valeA = NULL;
} elseif($row_vale['tipo_vale'] == '3') {
	$selected_valeC = NULL;
	$selected_valeP = NULL;
	$selected_valeA = 'selected';
}

if($row['ad_noturno'] == '1') {
	$checkad_noturno1 = 'checked';
	$checkad_noturno0 = NULL;
} else {
	$checkad_noturno1 = NULL;
	$checkad_noturno0 = 'checked';
}

if($row['estuda'] == 'sim') {
	$chekS = 'checked';
	$chekN = NULL;
} else {
	$chekS = NULL;
	$chekN = 'checked';
}

if($row['cipa'] == '1') {
	$checkedcipa1 = 'checked';
	$checkedcipa0 = NULL;
} else {
	$checkedcipa1 = NULL;
	$checkedcipa0 = 'checked';
}

if($row['status'] == "1") {
	$AVISO = NULL;
	$status_ativado = 'checked';
	$status_desativado = NULL;
	$data_desativacao = NULL;
} else {
	$AVISO = 'Este Funcionário Encontra-se DESATIVADO';
	$status_ativado = NULL;
	$status_desativado = 'checked';
	$data_desativacao = "$row[data_saida]";
}

if($row['foto'] == "1") {
	$foto = "Deseja remover a foto? <input name='foto' type='checkbox' id='foto' value='3'/> Sim";
} else {
	$foto = "<input name='foto' type='checkbox' id='foto' value='1' onClick=\"document.all.tablearquivo.style.display = (document.all.tablearquivo.style.display == 'none') ? '' : 'none' ;\">";
}

// Log
$qr_funcionario = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$funcionario = mysql_fetch_array($qr_funcionario);
$ip = $_SERVER['REMOTE_ADDR'];
$local_banco = "Edição de Bolsista";
$acao_banco = "Editando o Bolsista ($row[campo3]) $row[nome]";

mysql_query("INSERT INTO log (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao) 
VALUES ('$funcionario[0]', '$funcionario[id_regiao]', '$funcionario[tipo_usuario]', '$funcionario[grupo_usuario]', '$local_banco', NOW(), '$ip', '$acao_banco')") or die ("Erro Inesperado<br><br>".mysql_error());
// Fim do Log

// Documentos Gerados
$data_cad = date('Y-m-d');
$user_cad = $_COOKIE['logado'];

$result_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '27' and id_clt = '$id_bolsista'");
$num_row_verifica = mysql_num_rows($result_verifica);
if(empty($num_row_verifica)) {
	mysql_query("INSERT INTO rh_doc_status(tipo,id_clt,data,id_user) VALUES ('27','$id_bolsista','$data_cad','$user_cad')");
} else {
	mysql_query("UPDATE rh_doc_status SET data = '$data_cad', id_user = '$user_cad' WHERE id_clt = '$id_bolsista' AND tipo = '27'");
}
// Fim de Documentos Gerados
?>
<html>
<head>
<title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="shortcut icon" href="favicon.ico">
<link rel="stylesheet" href="rh/css/estrutura_cadastro.css" type="text/css">
<script language="javascript" src="js/ramon.js"></script>
<link href="js/jquery.ui.theme.css" rel="stylesheet" type="text/css" />
<link href="js/jquery.ui.datepicker.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery-1.3.2.js"></script>
<script type="text/javascript" src="js/jquery.ui.core.js"></script>
<script type="text/javascript" src="js/jquery.ui.widget.js"></script>
<script type="text/javascript" src="js/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="js/jquery.ui.datepicker-pt-BR.js"></script>
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
			$("input[name='nome_banco']").attr("disabled", true);
		}
		
		
		
		
	});
	
	function desabilita(){
		
		$("input[name*='conta']").attr("disabled", true);
		$("input[type*='radio'][name*='radio_tipo_conta']").attr("disabled", true);
		$("input[name*='agencia']").attr("disabled", true);
		$("input[name='nome_banco']").attr("disabled", true);
	}
	
	function Ativa(){
		$("input[name*='conta']").attr("disabled", false);
		$("input[type*='radio'][name*='radio_tipo_conta']").attr("disabled", false);
		$("input[name*='agencia']").attr("disabled", false);
		$("input[name='nome_banco']").attr("disabled", false);
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
			
			
		}
		
		if(indice.length > 0){
			alert("Preencha o(s) dado(s) "+indice.join(', '));
		}
			
		
		//$('#form1').submit();
		//alert('');
	});
});
</script>
</head>
<body onLoad="VerificaCoop(); drogadebanco()">
<div id="corpo">
<table align="center" width="100%" cellspacing="0" cellpadding="12" style="font-size:13px; line-height:22px;">
  <tr>
    <td>
  <div style="border-bottom:2px solid #F3F3F3; margin-top:10px;">
       <h2 style="float:left; font-size:18px;">EDITAR CADASTRO <span class="aut">AUTÔNOMO</span></h2>
       <p style="float:right;"><a href="bolsista.php?regiao=<?=$row['id_regiao']?>&projeto=<?=$row['id_projeto']?>"> &laquo; Voltar</a></p>
       <div class="clear"></div>
  </div>
  <p>&nbsp;</p>
<form action="alter_bolsista.php" method="post" id="form1" name="form1" onSubmit="return validaForm()" enctype="multipart/form-data">

<table cellpadding="0" cellspacing="1" class="secao">
  <tr>
     <td class="secao_pai" colspan="2" style="border-top:1px solid #777;">DADOS DO PROJETO</td>
  </tr>
  <tr>
    <td width="25%" class="secao">Código:</td>
    <td width="75%">
    	<input name="codigo" type="text" id="codigo" size="10" value="<?=$row['campo3']?>" />
    </td>
  </tr>
  <tr>
    <td class="secao">Tipo de Contratação:</td>
    <td>
        <select name="tipo_bol" id="tipo_bol" onChange="VerificaCoop()">
		<?php if($row['tipo_contratacao'] == "1") { ?>
				<option value="1" selected>Autônomo</option>
				<option value="3">Cooperado</option>
		<?php } elseif($row[tipo_contratacao] == "2") { ?>
				<option value="1">Autônomo</option>
				<option value="3">Cooperado</option>
		<?php } else { ?>
				<option value="1">Autônomo</option>
				<option value="3" selected>Cooperado</option>
		<?php } ?>
        </select>
    </td>
  </tr>
  <tr>
    <td class="secao">Projeto:</td>
    <td><?=$row_pro[0].' - '.$row_pro[2]?></td>
  </tr>
  <tr>
    <td class="secao">Curso:</td>
    <td>
     <select name="id_curso" id="id_curso">
	  <?php $qr_grupo = mysql_query("SELECT * FROM curso WHERE campo3 = '$row[id_projeto]' AND (tipo = '1' or tipo='3') ORDER BY nome ASC");
			while($row_grupo = mysql_fetch_array($qr_grupo)) { 
				$salario = number_format($row_grupo['salario'],2,',','.');
  				if($row_grupo['id_curso'] == "$row_curso[id_curso]") {
   					print "<option value='$row_grupo[id_curso]' selected>$row_grupo[0] - $row_grupo[campo2] (Valor: $salario)</option>";
  				} else {
  					print "<option value='$row_grupo[id_curso]'>$row_grupo[0] - $row_grupo[campo2] (Valor: $salario)</option>";
  				}
			} ?>
     </select>
   </td>
  </tr>
  <tr>
    <td class="secao">Unidade:</td>
    <td>
      <select name="lotacao" id="lotacao">
		<?php $qr_unidade = mysql_query("SELECT * FROM unidade WHERE id_regiao = '$row[id_regiao]' AND campo1 = '$row[id_projeto]' ORDER BY unidade ASC");
			  while($row_unidade = mysql_fetch_array($qr_unidade)) {
  			  	  if($row_unidade['unidade'] == "$row[locacao]") {
					   print "<option value='$row_unidade[unidade]' selected>$row_unidade[0] - $row_unidade[unidade]</option>";   
				  } else {
					   print "<option value='$row_unidade[unidade]'>$row_unidade[0] - $row_unidade[unidade]</option>";
				  }
			  } ?>
       </select>
    </td>
  </tr>
  <tr id="linhacoop">
    <td class="secao">Cooperativa:</td>
    <td>
     <select name="cooperativa" id="cooperativa">
     <?php $qr_coop = mysql_query("SELECT id_coop, nome FROM cooperativas WHERE id_regiao = '$row[id_regiao]' ORDER BY nome ASC");
           while($row_coop = mysql_fetch_array($qr_coop)){
		       if($row_coop['0'] == "$row[id_cooperativa]"){
			       print "<option value='$row_coop[0]' selected>$row_coop[0] - $row_coop[nome]</option>";
			   } else {
				   print "<option value='$row_coop[0]'>$row_coop[0] - $row_coop[nome]</option>";
			   }
           } ?>
      </select>
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
        <input name="nome" type="text" id="nome" size="75" value="<?=$row['nome']?>"
        	   onChange="this.value=this.value.toUpperCase()" />
    </td>
    <td class="secao">Data de Nascimento:</td>
    <td>
      <input name="data_nasc" type="text" id="data_nasc" size="15" value="<?=$row['data_nascimento']?>"
    	     onKeyUp="mascara_data(this); pula(10,this.id,civil.id)">
    </td>
  </tr>
  <tr>
    <td class="secao">Estado Civil:</td>
    <td>
	   <input name="civil" type="text" id="civil" size="16" value="<?=$row['civil']?>"
              onChange="this.value=this.value.toUpperCase()">
	</td>
    <td class="secao">Sexo:</td>
    <td>
      <label><input name="sexo" type="radio" class="reset" id="sexo" value="M" <?=$chekH?>> Masculino</label><br>
      <label><input name="sexo" type="radio" class="reset" id="sexo" value="F" <?=$chekF?>> Feminino</label>
    </td>
    <td class="secao">Nacionalidade:</td>
    <td><input name="nacionalidade" type="text" id="nacionalidade" size="15" value="<?=$row['nacionalidade']?>"
               onChange="this.value=this.value.toUpperCase()"/></td>
  </tr>
  <tr>
    <td class="secao">Endereço:</td>
    <td><input name="endereco" type="text" id="endereco" size="35" value="<?=$row['endereco']?>"
               onChange="this.value=this.value.toUpperCase()"/>
     </td>
    <td class="secao">Bairro:</td>
    <td><input name="bairro" type="text" id="bairro" size="16" value="<?=$row['bairro']?>"  
               onChange="this.value=this.value.toUpperCase()"/></td>
    <td class="secao">UF:</td>
     <td><input name="uf" type="text" id="uf" size="2" maxlength="2" value="<?=$row['uf']?>"
        	    onChange="this.value=this.value.toUpperCase()"
        	    onkeyup="pula(2,this.id,naturalidade.id)" /></td>
  </tr>
  <tr>
    <td class="secao">Cidade:</td>
    <td><input name="cidade" type="text" id="cidade" size="35" value="<?=$row['cidade']?>"
                           onChange="this.value=this.value.toUpperCase()"/></td>
    <td class="secao">CEP:</td>
    <td><input name="cep" type="text" id="cep" size="16" maxlength="9" value="<?=$row['cep']?>"
               style="text-transform:uppercase;" 	   
               OnKeyPress="formatar('#####-###', this)" 
               onKeyUp="pula(9,this.id,uf.id)" /></td>
    <td class="secao">Naturalidade:</td>
    <td><input name="naturalidade" type="text" id="naturalidade" size="15" value="<?=$row['naturalidade']?>"
               onChange="this.value=this.value.toUpperCase()"/></td>
  </tr>
  <tr>
    <td class="secao">Estuda Atualmente?</td>
    <td colspan="3">
        <label><input name="estuda" type="radio" class="reset" value="sim" <?=$chekS?>> Sim </label>
        <label><input name="estuda" type="radio" class="reset" value="nao" <?=$chekN?>> Não </label>
        <?=$mensagem_sexo?>  
	  </td>
    <td class="secao">Término em:</td>
    <td>
      <input name="data_escola" type="text" id="data_escola" size="15" maxlength="10" value="<?=$row['data_escola2']?>"
             onKeyUp="mascara_data(this);" /> 
    </td>
  </tr>
  <tr>
    <td class="secao">Escolaridade:</td>
    <td>
        <select name="escolaridade">
              <option value="12">12 - Não informado</option>
              <?php $qr_escolaridade = mysql_query("SELECT * FROM escolaridade WHERE status = 'on' LIMIT 0,11");
                    while ($escolaridade = mysql_fetch_assoc($qr_escolaridade)) { ?>
                       <option value="<?=$escolaridade['id']?>"<? if($row['escolaridade'] == $escolaridade['id']) { ?> selected="selected"<? } ?>><?=$escolaridade['cod']?> - <?=$escolaridade['nome']?></option>
              <?php } ?>
       </select>
    </td>
    <td class="secao">Curso:</td>
    <td><input name="curso" type="text" id="zona" size="16" value="<?=$row['curso']?>"
       		   onChange="this.value=this.value.toUpperCase()"/></td>
    <td class="secao">Institui&ccedil;&atilde;o:</td>
    <td><input name="instituicao" type="text" id="instituicao" size="15" value="<?=$row['instituicao']?>"
        	   onChange="this.value=this.value.toUpperCase()"/></td>
  </tr>
  <tr>
    <td class="secao">Telefone Fixo:</td>
    <td><input name="tel_fixo" type="text" id="tel_fixo" size="16" value="<?=$row['tel_fixo']?>"
             onKeyPress="return(TelefoneFormat(this,event))" 
             onKeyUp="pula(13,this.id,tel_cel.id)"></td>
    <td class="secao">Celular:</td>
    <td><input name="tel_cel" type="text" id="tel_cel" size="16" value="<?=$row['tel_cel']?>"  
		   onKeyPress="return(TelefoneFormat(this,event))" 
		   onKeyUp="pula(13,this.id,tel_rec.id)" /></td>
    <td class="secao">Recado:</td>
    <td>
      <input name="tel_rec" type="text" id="tel_rec" size="15" value="<?=$row['tel_rec']?>" 
    	   onKeyPress="return(TelefoneFormat(this,event))" 
   		   onKeyUp="pula(13,this.id,pai.id)" />
    </td>
  </tr>
</table>
  
<table cellpadding="0" cellspacing="1" class="secao">
  <tr>
    <td colspan="4" class="secao_pai">DADOS DA FAMÍLIA</td>
  </tr>
  <tr>
    <td class="secao">Filiação - Pai:</td>
    <td>
        <input name="pai" type="text" id="pai" size="45" value="<?=$row['pai']?>"
        	   onChange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">Nacionalidade Pai:</td>
    <td>
        <input name="nacionalidade_pai" type="text" id="nacionalidade_pai" size="15" value="<?=$row['nacionalidade_pai']?>"
               onChange="this.value=this.value.toUpperCase()"/>	
	 </td>
  </tr>
  <tr>
    <td class="secao">Filiação - Mãe:</td>
    <td>
        <input name="mae" type="text" id="mae" size="45" value="<?=$row['mae']?>"
               onChange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">Nacionalidade Mãe:</td>
    <td>
        <input name="nacionalidade_mae" type="text" id="nacionalidade_mae" size="15" value="<?=$row['nacionalidade_mae']?>"
               onChange="this.value=this.value.toUpperCase()"/>	
	</td>
  </tr>
  <tr>
    <td class="secao">Número de Filhos:</td>
    <td colspan="3">
    <input name="filhos" type="text" id="filhos" size="2" value="<?=$row['num_filhos']?>" />
    </td>
  </tr>
  <tr>
    <td class="secao">Nome:</td>
    <td>
       <input name="filho_1" type="text" id="filho_1" size="50" value="<?=$row_depe['nome1']?>"
              onChange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">Nascimento:</td>
    <td>
    <input name="data_filho_1" type="text" size="12" maxlength="10" id="data_filho_1" value="<?=$row_depe['datas1']?>"
		   onKeyUp="mascara_data(this); pula(10,this.id,filho_2.id)"
           onChange="this.value=this.value.toUpperCase()"/>
    </td>
  </tr>
  <tr>
    <td class="secao">Nome:</td>
    <td>
      <input name="filho_2" type="text" id="filho_2" size="50" value="<?=$row_depe['nome2']?>"
             onChange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">Nascimento:</td>
    <td>
    <input name="data_filho_2" type="text" size="12" maxlength="10" id="data_filho_2" value="<?=$row_depe['datas2']?>"	
		   onKeyUp="mascara_data(this); pula(10,this.id,filho_3.id)"        
           onChange="this.value=this.value.toUpperCase()"/>
    </td>
  </tr>
  <tr>
    <td class="secao">Nome:</td>
    <td>
      <input name="filho_3" type="text" id="filho_3" size="50" value="<?=$row_depe['nome3']?>"
             onChange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">Nascimento:</td>
    <td>
    <input name="data_filho_3" type="text" size="12" maxlength="10" id="data_filho_3" value="<?=$row_depe['datas3']?>" 
		   onKeyUp="mascara_data(this); pula(10,this.id,filho_4.id)"
           onChange="this.value=this.value.toUpperCase()"/>
    </td>
  </tr>
  <tr>
    <td class="secao">Nome:</td>
    <td>
      <input name="filho_4" type="text" id="filho_4" size="50" value="<?=$row_depe['nome4']?>"
             onChange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">Nascimento:</td>
    <td>
    <input name="data_filho_4" type="text" size="12" maxlength="10" id="data_filho_4" value="<?=$row_depe['datas4']?>"    
		   onKeyUp="mascara_data(this); pula(10,this.id,filho_5.id)"
           onChange="this.value=this.value.toUpperCase()"/>
    </td>
  </tr>
  <tr>
    <td class="secao">Nome:</td>
    <td>
      <input name="filho_5" type="text" id="filho_5" size="50" value="<?=$row_depe['nome5']?>"
             onChange="this.value=this.value.toUpperCase()"/>
   </td>
    <td class="secao">Nascimento:</td>
    <td>
    <input name="data_filho_5" type="text" size="12" maxlength="10" id="data_filho_5" value="<?=$row_depe['datas5']?>"
           onKeyUp="mascara_data(this);"
           onChange="this.value=this.value.toUpperCase()"/>
    </td>
  </tr>
</table>

<table cellpadding="0" cellspacing="1" class="secao">
  <tr>
    <td class="secao_pai" colspan="6">APARÊNCIA</td>
  </tr>
  <tr>
    <td class="secao">Cabelos:</td>
    <td>
	<select name="cabelos" id="cabelos">
 		<option value="">Não informado</option>
 <?php $result_cabelos = mysql_query("SELECT * FROM tipos WHERE tipo = '1' AND status = '1'");
       while($row_cabelos = mysql_fetch_array($result_cabelos)){
              if($row['cabelos'] == $row_cabelos['nome']){
                    print "<option selected>$row_cabelos[nome]</option>";
              } else {
                    print "<option>$row_cabelos[nome]</option>";
              }
       } ?>

    </select>
    </td>
    <td class="secao">Olhos:</td>
    <td>
    <select name="olhos" id="olhos">
	  	<option value="">Não informado</option>
  <?php $result_olhos = mysql_query("SELECT * FROM tipos WHERE tipo = '2' AND status = '1'");
   while($row_olhos = mysql_fetch_array($result_olhos)){
       if($row['olhos'] == $row_olhos['nome']){
          print "<option selected>$row_olhos[nome]</option>";
       } else {
          print "<option>$row_olhos[nome]</option>";
      }
    } ?>
 
   </select>
   </td>
    <td class="secao">Peso:</td>
    <td>
      <input name="peso" type="text" id="peso" size="5" value="<?=$row['peso']?>" />
    </td>
  </tr>
  <tr>
    <td class="secao">Altura:</td>
    <td>
      <input name="altura" type="text" id="altura" size="5" value="<?=$row['altura']?>" />
    </td>
    <td class="secao">Etnia:</td>
    <td>

          <select name="etnia">
                 <option value="6">Não informado</option>
    <?php $qr_etnias = mysql_query("SELECT * FROM etnias WHERE status = 'on' LIMIT 0,5");
          while($etnia = mysql_fetch_assoc($qr_etnias)) { ?>
               <option value="<?=$etnia['id']?>"<?php if($row['etnia'] == $etnia['id']) { echo ' selected="selected"'; } ?>>
				   <?=$etnia['nome']?>
               </option>
    <?php } ?>
           </select>
           
    <td class="secao">Marcas ou Cicatriz:</td>
    <td>
      <input name="defeito" type="text" id="defeito" size="18" value="<?=$row['defeito']?>"
             onChange="this.value=this.value.toUpperCase()"/>
    </td>
  </tr>
  <tr>
  <td class="secao">Deficiência:</td>
  <td colspan="6">
     <select name="deficiencia">
         <option value="">Não é portador de deficiência</option>
         <?php $qr_deficiencias = mysql_query("SELECT * FROM deficiencias WHERE status = 'on'");
               while($deficiencia = mysql_fetch_assoc($qr_deficiencias)) { ?>
         <option value="<?=$deficiencia['id']?>"<? if($row['deficiencia'] == $deficiencia['id']) { ?> selected="selected"<? } ?>><?=$deficiencia['nome']?></option>
         <?php } ?>
     </select>    
  </tr>
  <tr id="ancora_foto">
    <td class="secao">	
       Foto:
    </td>
    <td colspan="5"><?=$foto?>
        <div id="tablearquivo" style="display:none;">ENVIAR FOTO: <input name="arquivo" type="file" id="arquivo" size="60" /></div>
    </td>
   </tr>
</table>

<table cellpadding="0" cellspacing="1" class="secao">
  <tr>
     <td class="secao_pai" colspan="8">DOCUMENTAÇÃO</td>
  </tr>
  <tr>
    <td width="16%" class="secao">Nº do RG:</td>
    <td width="12%">
	<input name="rg" type="text" id="rg" size="13" maxlength="14" value="<?=$row['rg']?>"
           OnKeyPress="formatar('##.###.###-###', this)" 
		   onkeyup="pula(14,this.id,orgao.id)">
    </td>
    <td width="15%" class="secao">Orgão Expedidor:</td>
    <td width="9%">
        <input name="orgao" type="text" id="orgao" size="8" value="<?=$row['orgao']?>"
               onChange="this.value=this.value.toUpperCase()"/>
    </td>
    <td width="5%" class="secao">UF:</td>
    <td width="7%">
    <input name="uf_rg" type="text" id="uf_rg" size="5" value="<?=$row['uf_rg']?>"
           onChange="this.value=this.value.toUpperCase()"/>
    </td>
    <td width="18%" class="secao">Data Expedição:</td>
    <td width="18%">
      <input name="data_rg" type="text" size="12" maxlength="10" value="<?=$row['data_rg2']?>" id="data_rg" 
		     onkeyup="mascara_data(this); pula(10,this.id,cpf.id)" />
   </td>
  </tr>
  <tr>
    <td class="secao">CPF:</td>
    <td colspan="5">
        <input name="cpf" type="text" id="cpf" size="17" maxlength="14" value="<?=$row['cpf']?>"
               OnKeyPress="formatar('###.###.###-##', this)" 
			   onkeyup="pula(14,this.id,reservista.id)"/>
     </td>
    <td class="secao">Certificado de Reservista:</td>
    <td>
      <input name="reservista" type="text" id="reservista" size="18" value="<?=$row['reservista']?>" />
    </td>
  </tr>
  <tr>
    <td class="secao">Nº Carteira de Trabalho:</td>
    <td>
      <input name="trabalho" type="text" id="trabalho" size="15" value="<?=$row['campo1']?>" />
     </td>
    <td class="secao">Série:</td>
    <td>
     <input name="serie_ctps" type="text" id="serie_ctps" size="10" value="<?=$row['serie_ctps']?>" />
    </td>
    <td class="secao">UF:</td>
    <td><input name="uf_ctps" type="text" id="uf_ctps" size="5" value="<?=$row['uf_ctps']?>"
               onChange="this.value=this.value.toUpperCase()"/></td>
    <td class="secao">Data carteira de Trabalho:</td>
    <td>  
      <input name="data_ctps" type="text" size="12" maxlength="10" id="data_ctps" value="<?=$row['data_ctps2']?>" 
		     onkeyup="mascara_data(this); pula(10,this.id,titulo2.id)" />     
    </td>
  </tr>
  <tr>
    <td class="secao">Nº Título de Eleitor:</td>
    <td>
        <input name="titulo" type="text" id="titulo2" size="10" value="<?=$row['titulo']?>" />
    </td>
    <td class="secao"> Zona:</td>
    <td colspan="3">
        <input name="zona" type="text" id="zona2" size="3" value="<?=$row['zona']?>" />
    </td>
    <td class="secao">Seção:</td>
    <td>
        <input name="secao" type="text" id="secao" size="3" value="<?=$row['secao']?>" />
    </td>
  </tr>
  <tr>
    <td class="secao">PIS:</td>
    <td>
      <input name="pis" type="text" id="pis" size="12" value="<?=$row['pis']?>" />
    </td>
    <td class="secao">Data PIS:</td>
    <td colspan="3">
    <input name="data_pis" type="text" size="12" maxlength="10" id="data_pis" value="<?=$row['dada_pis2']?>"
           onkeyup="mascara_data(this); pula(10,this.id,fgts.id)" />
	</td>
    <td class="secao">FGTS:</td>
    <td>
        <input name="fgts" type="text" id="fgts" size="10" value="<?=$row['fgts']?>" />
    </td>
  </tr>
</table>

<table cellpadding="0" cellspacing="1" class="secao">
  <tr>
     <td class="secao_pai" colspan="6">BENEFÍCIOS</td>
  </tr>
  <tr>
    <td class="secao">Assistência Médica:</td>
    <td colspan="2">
		<label><input type="radio" name="medica" value="1" <?=$chek_medi1?>>Sim</label> 
		<label><input type="radio" name="medica" value="0" <?=$chek_medi0?>>Não</label>
    </td>
    <td class="secao">Tipo de Plano:</td>
    <td colspan="2">
      <select name="plano_medico" id="plano_medico">
        <option value="1" <?=$selected_planoF?>>Familiar</option>
        <option value="2" <?=$selected_planoI?>>Individual</option>
      </select>
    </td>
  </tr>
  <tr>
    <td class="secao">Seguro, Apólice:</td>
    <td colspan="2">
      <select name="apolice" id="apolice">
        <option value="0">Não Possui</option>
        <?php $result_ap = mysql_query("SELECT * FROM apolice WHERE id_regiao = '$row[id_regiao]'");
           		  while($row_ap = mysql_fetch_array($result_ap)) {
					  if($row_ap['id_apolice'] == $row['apolice']) {
					  	  print "<option value='$row_ap[id_apolice]' selected>$row_ap[razao]</option>";   
					  } else {
					  	  print "<option value='$row_ap[id_apolice]'>$row_ap[razao]</option>";
					  }
            	  } ?>
        </select>
    </td>
    <td class="secao">Dependente:</td>
    <td colspan="2"><input name="dependente" type="text" id="dependente" size="20"
			 onChange="this.value=this.value.toUpperCase()"/></td>
  </tr>
  <tr>
    <td class="secao">Insalubridade:</td>
    <td colspan="2">
    	<input name="insalubridade" type="checkbox" id="insalubridade2" value="1" <?=$chek1?> />
    </td>
    <td class="secao">Adicional Noturno:</td>
    <td colspan="2">
        <label>
          <input type="radio" name="ad_noturno" value="1" <?=$checkad_noturno1?>> Sim
        </label>
        <label>
            <input type="radio" name="ad_noturno" value="0" <?=$checkad_noturno0?>> N&atilde;o
        </label>
    </td>
  </tr>
  <tr>
    <td class="secao">Integrante do CIPA:</td>
    <td colspan="5">
		<label><input type="radio" name="cipa" value="1" <?=$checkedcipa1?>>Sim</label>
		<label><input type="radio" name="cipa" value="0" <?=$checkedcipa0?>>Não</label>
    </td>
  </tr>
  <tr>
  	<td class="secao">Vale Transporte:</td>
	<td colspan="2">
	    <input name="transporte" type="checkbox" id="transporte2" value="1" <?=$chek2?> />
	</td>
    <td class="secao">Tipo de Vale:</td>
    <td colspan="2">
      <select name="tipo_vale">
            <option value="1" <?=$selected_valeC?>>Cartão</option>
            <option value="2" <?=$selected_valeP?>>Papel</option>
			<option value="3" <?=$selected_valeA?>>Ambos</option>
          </select>
    </td>
  </tr>
  <tr>
    <td class="secao" width="20%">Cartão 1:</td>
    <td width="16%">
      <input name="num_cartao" type="text" id="num_cartao" size="12" value="<?=$row_vale['numero_cartao']?>" />
    </td>
    <td class="secao" width="16%">Valor Total 1:</td>
    <td width="16%">
    <input name="valor_cartao" type="text" id="valor_cartao" size="12" value="<?=$row_vale['valor_cartao']?>"
           onkeydown="FormataValor(this,event,20,2)" />
    </td>
    <td class="secao" width="16%">Tipo Cartão 1:</td>
    <td width="16%">
    <input name="tipo_cartao_1" type="text" id="tipo_cartao_1" size="12" value="<?=$row_vale['tipo_cartao_1']?>"
    	   onChange="this.value=this.value.toUpperCase()" />
    </td>
 </tr>
 <tr>
    <td class="secao">Cartão 2:</td>
    <td>
      <input name="num_cartao2" type="text" id="num_cartao2" size="12" value="<?=$row_vale['numero_cartao2']?>" />
    </td>
    <td class="secao">Valor Total 2:</td>
    <td>
    <input name="valor_cartao2" type="text" id="valor_cartao2" size="12" value="<?=$row_vale['valor_cartao2']?>"
           onkeydown="FormataValor(this,event,20,2)" />
    </td>
    <td class="secao">Tipo Cartão 2:</td>
    <td>
    <input name="tipo_cartao_2" type="text" id="tipo_cartao_2" size="12" value="<?=$row_vale['tipo_cartao_2']?>"
           onChange="this.value=this.value.toUpperCase()" />
    </td>
  </tr>
  <tr>
    <td class="secao">Quantidade 1:</td>
    <td><input name="vale_qnt_1" type="text" id="vale_qnt_1" size="3" value="<?=$row_vale['qnt1']?>" /></td>
    <td class="secao">Valor 1:</td>
    <td><input name="vale_valor_1" type="text" id="vale_valor_1" size="12" value="<?=$row_vale['valor1']?>"
               onkeydown="FormataValor(this,event,20,2)" /></td>
    <td class="secao">Tipo Vale 1:</td>
    <td><input name="tipo1" type="text" id="tipo1" size="12" value="<?=$row_vale['tipo1']?>"
               onChange="this.value=this.value.toUpperCase()" /></td>
  </tr>
  <tr>
    <td class="secao">Quantidade 2:</td>
    <td><input name="vale_qnt_2" type="text" id="vale_qnt_2" size="3" value="<?=$row_vale['qnt2']?>" /></td>
    <td class="secao">Valor 2:</td>
    <td><input name="vale_valor_2" type="text" id="vale_valor_2" size="12" value="<?=$row_vale['valor2']?>"
               onkeydown="FormataValor(this,event,20,2)" /></td>
    <td class="secao">Tipo Vale 2:</td>
    <td><input name="tipo2" type="text" id="tipo2" size="12" value="<?=$row_vale['tipo2']?>"
               onChange="this.value=this.value.toUpperCase()" /></td>
  </tr>
  <tr>
    <td class="secao">Quantidade 3:</td>
    <td><input name="vale_qnt_3" type="text" id="vale_qnt_3" size="3" value="<?=$row_vale['qnt3']?>" /></td>
    <td class="secao">Valor 3:</td>
    <td><input name="vale_valor_3" type="text" id="vale_valor_3" size="12" value="<?=$row_vale['valor3']?>"
               onkeydown="FormataValor(this,event,20,2)" /></td>
    <td class="secao">Tipo Vale 3:</td>
    <td><input name="tipo3" type="text" id="tipo3" size="12" value="<?=$row_vale['tipo3']?>"
           onChange="this.value=this.value.toUpperCase()" /></td>
  </tr>
  <tr>
    <td class="secao">Quantidade 4:</td>
    <td><input name="vale_qnt_4" type="text" id="vale_qnt_4" size="3" value="<?=$row_vale['qnt4']?>" /></td>
    <td class="secao">Valor 4:</td>
    <td><input name="vale_valor_4" type="text" id="vale_valor_4" size="12" value="<?=$row_vale['valor4']?>"
               onkeydown="FormataValor(this,event,20,2)" /></td>
    <td class="secao">Tipo Vale 4:</td>
    <td><input name="tipo4" type="text" id="tipo4" size="12" value="<?=$row_vale['tipo4']?>"
               onChange="this.value=this.value.toUpperCase()" /></td>
  </tr>
</table>

<table cellpadding="0" cellspacing="1" class="secao">
  <tr>
    <td colspan="4" class="secao_pai">DADOS BANCÁRIOS</td>
  </tr>
  <tr>
    <td class="secao" width="15%">Banco:</td>
    <td width="30%">
      <select name="banco" >
		  <option value="0">Sem Banco</option>
			<?php 
            $sql_banco = "SELECT * FROM bancos WHERE id_regiao = '$row[id_regiao]' and id_projeto = '$row[id_projeto]' AND status_reg = '1'";
            $result_banco = mysql_query($sql_banco, $conn);
            while($row_banco = mysql_fetch_array($result_banco)) {
              if($row['banco'] == "$row_banco[0]") {
                  print "<option value=$row_banco[0] selected>$row_banco[nome]</option>";
              } else {
                  print "<option value=$row_banco[0]>$row_banco[nome]</option>";
              }
            }
          
          if($row['banco'] == "9999") {
              print "<option value='9999' selected>Outro Banco</option></select>";
          } else {
              print "<option value='9999'>Outro Banco</option></select>";
          } ?>
   </select>
  </td>
    <td width="25%" class="secao"><div onClick="bloqEnter()">Agência:&nbsp;</div></td>
    <td width="30%">
      <input name="agencia" type="text" id="agencia" size="12" value="<?=$row['agencia']?>" />
    </td>
  </tr>

 <tr id="linhabanc2">
    <td class="secao"><div onClick="ValidaBanc()">Conta:&nbsp;</div></td>
    <td>
      <input name="conta" type="text"  id="conta" value="<?=$row['conta']?>" size="12" /><br>
      <?php $tipo = $row['tipo_conta'];
		  if($tipo == 'salario') {
		      $checkedSalario = 'checked';	
		  } elseif($tipo == 'corrente') {
			  $checkedCorrente = 'checked';
		  } ?>
   	  <label><input type="radio" name="radio_tipo_conta" value="salario" <?=$checkedSalario?>>Conta Salário</label>
      <label><input type="radio" name="radio_tipo_conta" value="corrente" <?=$checkedCorrente?>>Conta Corrente</label>
    </td>
	<td class="secao">Nome do Banco:<br>
    				  (caso não esteja na lista acima)</td>
	<td>
	  <input name="nome_banco" type="text" id="nome_banco" size="30" value="<?=$row['nome_banco']?>" />
	</td>
  </tr>
</table>

<table cellpadding="0" cellspacing="1" class="secao">
  <tr>
    <td colspan="4" class="secao_pai">DADOS FINANCEIROS E DE CONTRATO</td>
  </tr>
  <tr>
    <td class="secao">Data de Entrada:</td>
    <td><input name="data_entrada" type="text" size="12" maxlength="10" id="data_entrada" value="<?=$row['data_entrada2']?>"
			   onkeyup="mascara_data(this); pula(10,this.id,exame_data.id)" />
    </td>
    <td class="secao">Data do Exame Admissional:</td>
    <td><input name="exame_data" type="text" size="12" maxlength="10" id="exame_data" value="<?=$row['data_exame']?>"
			   onkeyup=\"mascara_data(this); pula(10,this.id,localpagamento.id)\" />
	</td>
  </tr>
  <tr>
    <td class="secao">Local de Pagamento:</td>
    <td colspan="3">
      <input name="localpagamento" type="text" id="localpagamento" size="25" value="<?=$row['localpagamento']?>"
             onChange="this.value=this.value.toUpperCase()" />
    </td>
  </tr>
  <tr>
    <td class="secao">Tipo de Pagamento:</td>
    <td colspan="3">
      <select name="tipopg" id="tipopg">
<?php $RE_pg_dep = mysql_query("SELECT id_tipopg FROM tipopg WHERE id_projeto = '$id_projeto' and campo1 = '1'");
	  $Row_pg_dep = mysql_fetch_array($RE_pg_dep);

      $RE_pg_che = mysql_query("SELECT id_tipopg FROM tipopg WHERE id_projeto = '$id_projeto' and campo1 = '2'");
      $Row_pg_che = mysql_fetch_array($RE_pg_che);

      $result_pg = mysql_query("SELECT * FROM tipopg WHERE id_projeto = '$id_projeto'", $conn);
		while ($row_pg = mysql_fetch_array($result_pg)) {
		    if($row_pg['0'] == $row['tipo_pagamento']){
		        print "<option value='$row_pg[id_tipopg]' selected>$row_pg[tipopg]</option>";   
		    } else {
		        print "<option value='$row_pg[id_tipopg]'>$row_pg[tipopg]</option>";
		    }
		} ?>

		</select>

    </td>
  </tr>
  <tr>
    <td class="secao">Observações:</td>
    <td colspan="3">
	<textarea name="observacoes" id="observacoes" cols="55" rows="4"  
           	  onChange="this.value=this.value.toUpperCase()"><?=$row['observacao']?></textarea>
    </td>
  </tr>
</table>

<div id="finalizacao"> 
 O Contrato foi <strong>assinado</strong>?<br>
     <label>
         <input name="assinatura" type="radio" class="reset" id="assinatura" value="1" <?=$selected_ass_sim?>> SIM 
     </label>
     <label>
         <input name="assinatura" type="radio" class="reset" id="assinatura" value="0" <?=$selected_ass_nao?>> N&Atilde;O
     </label>
 <p>&nbsp;</p>
 O Distrato foi <strong>assinado</strong>?<br>
     <label>
     	 <input name="assinatura2" type="radio" class="reset" id="assinatura2" value="1" <?=$selected_ass_sim2?>> SIM
     </label>
     <label>
         <input name="assinatura2" type="radio" class="reset" id="assinatura2" value="0" <?=$selected_ass_nao2?>> N&Atilde;O
     </label>
 <p>&nbsp;</p>
 Outros documentos foram <strong>assinados</strong>?<br>
     <label>
         <input name="assinatura3" type="radio" class="reset" id="assinatura3" value="1" <?=$selected_ass_sim3?>> SIM
     </label>
     <label>
         <input name="assinatura3" type="radio" class="reset" id="assinatura3" value="0" <?=$selected_ass_nao3?>> N&Atilde;O
     </label>
 <p>&nbsp;</p>
 O funcionário <strong>participa ativamente</strong> das atividades do projeto?
<label>
     	 <input type="radio" id="radio1" name="radio6" value="1" <?=$status_ativado?> /> SIM
     </label>
     <label>
         <input type="radio" id="radio2" name="radio6" value="0" <?=$status_desativado?> /> N&Atilde;O
     <label>
  <p>&nbsp;</p>
  <span style=" color:#C30;">Caso <b>não</b> coloque a data da desativação:</span>
	<input name="data_desativacao" type="text" id="data_desativacao" size="12" maxlength="10" value="<?=$data_desativacao?>" 
           onkeyup="mascara_data(this);" />
 <?=$mensagem_ass?>              
</div>

<div id="observacao">NÃO DEIXE DE CONFERIR OS DADOS APÓS A DIGITAÇÃO</div>

<div align="center"><input type="submit" name="Submit2" value="ATUALIZAR" class="botao" /></div>
		
<input type="hidden" name="update" value="1">  
<input type="hidden" name="id_cadastro" value="4">
<input type="hidden" name="id_bolsista" value="<?=$row[0]?>">
<input type="hidden" name="id_projeto" value="<?=$projeto?>">
<input type="hidden" name="pro" value="<?=$id_projeto?>">
<input type="hidden" name="regiao" value="<?=$row['id_regiao']?>">
<input type="hidden" name="user" value="<?=$id_user?>">
</form>
</td>
</tr>
</table>
</div>
<script language="javascript">
function VerificaCoop() {
	var TipoC = document.all.tipo_bol.value;
	if(TipoC == 3) {
		document.all.linhacoop.style.display = '';
	} else {
		document.all.linhacoop.style.display = 'none';
	}
}

function validaForm() {
	d = document.form1;
	if (d.cpf.value == "") {
		alert("O campo CPF deve ser preenchido!");
		d.cpf.focus();
		return false;
	}
return true;
}

function bloqEnter() {
	d = document.form1;
	var iKeyCode; 
	iKeyCode = d.agencia.value;
	buffer = iKeyCode.charCodeAt;

	var variavel = d.agencia.value;
	alert(variavel.charCodeAt(0));
}

function ValidaBanc(){
	d = document.form1;
	deposito = "<?=$Row_pg_dep[0]?>";
	cheque = "<?=$Row_pg_che[0]?>";
	
	if(document.getElementById("tipopg").value == deposito){
		
	if (document.getElementById("banco").value == 0){
		alert("Selecione um banco!");
		return false;
	}
	
	if (d.agencia.value == ""){
		alert("O campo Agencia deve ser preenchido!");
		d.agencia.focus();
		return false;
	}
	
	if (d.conta.value == ""){
		alert("O campo Conta deve ser preenchido!");
		d.conta.focus();
		return false;
	}
}

if(document.getElementById("tipopg").value == cheque){
	
	if (document.getElementById("banco").value != 0){
		alert("Para pagamentos em cheque deve selecionar SEM BANCO!");
		return false;
	}
	d.agencia.value = "";
	d.conta.value = "";

}
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
	$('#exame_data').datepicker({
		changeMonth: true,
	    changeYear: true
	});
});
</script>
</body>
</html>
<?php } else {
	
include('log_alter_bolsista.php');

$id_bolsista = $_REQUEST['id_bolsista'];
$regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['pro'];

$id_user = $_COOKIE['logado'];
$data_hoje = date('Y-m-d');

$lotacao = $_REQUEST['lotacao'];

$tipo_contratacao = $_REQUEST['tipo_bol'];

if($tipo_contratacao == 3 or $tipo_contratacao == 4){
	$cooperativa = $_REQUEST['cooperativa'];
}else{
	$cooperativa = "0";
}

$nome = $_REQUEST['nome'];
$assinatura = $_REQUEST['assinatura'];
$assinatura2 = $_REQUEST['assinatura2'];
$assinatura3 = $_REQUEST['assinatura3'];
$sexo = $_REQUEST['sexo'];
$endereco = $_REQUEST['endereco'];
$bairro = $_REQUEST['bairro'];
$cidade = $_REQUEST['cidade'];
$uf = $_REQUEST['uf'];
$cep = $_REQUEST['cep'];
$tel_fixo = $_REQUEST['tel_fixo'];
$tel_cel = $_REQUEST['tel_cel'];
$tel_rec = $_REQUEST['tel_rec'];
$data_nasci = $_REQUEST['data_nasc'];
$naturalidade = $_REQUEST['naturalidade'];
$nacionalidade = $_REQUEST['nacionalidade'];
$civil = $_REQUEST['civil'];
$rg = $_REQUEST['rg'];
$uf_rg = $_REQUEST['uf_rg'];
$secao = $_REQUEST['secao'];
$data_rg = $_REQUEST['data_rg'];
$cpf = $_REQUEST['cpf'];
$titulo = $_REQUEST['titulo'];
$zona = $_REQUEST['zona'];
$orgao = $_REQUEST['orgao'];
$pai = $_REQUEST['pai'];
$mae = $_REQUEST['mae'];
$nacionalidade_pai = $_REQUEST['nacionalidade_pai'];
$nacionalidade_mae = $_REQUEST['nacionalidade_mae'];
$estuda = $_REQUEST['estuda'];
$escola_dia = $_REQUEST['escola_dia'];
$escolaridade = $_REQUEST['escolaridade'];
$instituicao = $_REQUEST['instituicao'];
$curso = $_REQUEST['curso'];
$banco = $_REQUEST['banco'];
$agencia = $_REQUEST['agencia'];
$conta = $_REQUEST['conta'];
$tipoDeConta = $_REQUEST['radio_tipo_conta'];
$localpagamento = $_REQUEST['localpagamento'];
$apolice = $_REQUEST['apolice'];
$status = $_REQUEST['radio6'];
$data_entrada = $_REQUEST['data_entrada'];

$codigo = $_REQUEST['codigo'];

$id_curso = $_REQUEST['id_curso'];
$trabalho = $_REQUEST['trabalho'];
$dependente = $_REQUEST['dependente'];

$serie_ctps = $_REQUEST['serie_ctps'];
$uf_ctps = $_REQUEST['uf_ctps'];

$nome_banco = $_REQUEST['nome_banco'];
$pis = $_REQUEST['pis'];
$fgts = $_REQUEST['fgts'];
$tipopg = $_REQUEST['tipopg'];
$filhos = $_REQUEST['filhos'];
$observacao = $_REQUEST['observacoes'];
$medica = $_REQUEST['medica'];
$plano = $_REQUEST['plano_medico'];
$data_ctps = $_REQUEST['data_ctps'];
$data_pis = $_REQUEST['data_pis'];

$tipo_vale = $_REQUEST['tipo_vale'];

$num_cartao = $_REQUEST['num_cartao'];
$valor_cartao = $_REQUEST['valor_cartao'];
$tipo_cartao_1 = $_REQUEST['tipo_cartao_1'];

$numero_cartao2 = $_REQUEST['num_cartao2'];
$valor_cartao2 = $_REQUEST['valor_cartao2'];
$tipo_cartao_2 = $_REQUEST['tipo_cartao_2'];

$qnt1 = $_REQUEST['vale_qnt_1'];
$valor1 = $_REQUEST['vale_valor_1'];

$qnt2 = $_REQUEST['vale_qnt_2'];
$valor2 = $_REQUEST['vale_valor_2'];

$qnt3 = $_REQUEST['vale_qnt_3'];
$valor3 = $_REQUEST['vale_valor_3'];

$qnt4 = $_REQUEST['vale_qnt_4'];
$valor4 = $_REQUEST['vale_valor_4'];

$tipo1 = $_REQUEST['tipo1'];
$tipo2 = $_REQUEST['tipo2'];
$tipo3 = $_REQUEST['tipo3'];
$tipo4 = $_REQUEST['tipo4'];

$ad_noturno = $_REQUEST['ad_noturno'];
$exame_data = $_REQUEST['exame_data'];

$reservista = $_REQUEST['reservista'];
$cabelos = $_REQUEST['cabelos'];
$peso = $_REQUEST['peso'];
$altura = $_REQUEST['altura'];
$olhos = $_REQUEST['olhos'];
$defeito = $_REQUEST['defeito'];
$cipa = $_REQUEST['cipa'];
$etnia = $_REQUEST['etnia'];
$deficiencia = $_REQUEST['deficiencia'];

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

if(empty($_REQUEST['foto'])){
$foto = "0";
}else{
$foto = $_REQUEST['foto'];
}

if($foto == "3"){
  $foto_banco = "0";
  $foto_up = "0";
}elseif($foto == "1"){
  $foto_banco = "1";
  $foto_up = "1";
}else{
$vendo_foto = mysql_query("SELECT foto FROM autonomo WHERE id_autonomo = '$id_bolsista'");
$row_vendo_foto = mysql_fetch_array($vendo_foto);

  $foto_banco = "$row_vendo_foto[foto]";
  $foto_up = "0";
}  



 if(empty($_REQUEST['insalubridade'])){
   $insalubridade = "0";
   }else{
   $insalubridade = $_REQUEST['insalubridade'];
  }

 if(empty($_REQUEST['transporte'])){
  $transporte = "0";
  }else{
  $transporte = $_REQUEST['transporte'];
 }


if($status == '0'){
$desativacao = $_REQUEST['data_desativacao'];
}else{
$desativacao = "";
}


/* 
Função para converter a data
De formato nacional para formato americano.
Muito útil para você inserir data no mysql e visualizar depois data do mysql.
*/


function ConverteData($Data){
 if (strstr($Data, "/"))//verifica se tem a barra /
 {
  $d = explode ("/", $Data);//tira a barra
 $rstData = "$d[2]-$d[1]-$d[0]";//separa as datas $d[2] = ano $d[1] = mes etc...
 return $rstData;
 } elseif(strstr($Data, "-")){
 $d = explode ("-", $Data);
 $rstData = "$d[2]/$d[1]/$d[0]"; 
 return $rstData;
 }else{
 return "";
 }
}

$data_entrada2 = ConverteData($data_entrada);
$data_rg2 = ConverteData($data_rg);
$data_nasci2 = ConverteData($data_nasci);
$data_ctps = ConverteData($data_ctps);
$data_pis1 = ConverteData($data_pis);
$exame_data = ConverteData($exame_data);
$desativacao = ConverteData($desativacao);

$data_filho_1 = ConverteData($data_filho_1);
$data_filho_2 = ConverteData($data_filho_2);
$data_filho_3 = ConverteData($data_filho_3);
$data_filho_4 = ConverteData($data_filho_4);
$data_filho_5 = ConverteData($data_filho_5);


if($codigo == "INSERIR"){
$resultado_teste2 = "0";
}else{
$result_teste2 = mysql_query("SELECT id_autonomo FROM autonomo WHERE campo3 = '$codigo' and id_autonomo <> '$id_bolsista' and id_projeto = '$projeto'" , $conn);
$resultado_teste2 = mysql_num_rows($result_teste2);
}

if ($resultado_teste2 >= 1) {

print "
<br>
<link href='../net.css' rel='stylesheet' type='text/css'>
<body bgcolor='#D7E6D5'>
<center>
<br>JÁ EXISTE UM PARTICIPANTE CADASTRADO COM ESTE CÓDIGO: <font color=#FFFFFF><b>$codigo</b></font>
</center>
</body>
";
} else {
 
mysql_query ("update autonomo set localpagamento = '$localpagamento', locacao = '$lotacao', nome = '$nome', sexo = '$sexo', endereco = '$endereco', bairro = '$bairro', cidade = '$cidade', uf = '$uf', cep = '$cep', tel_fixo = '$tel_fixo',
tel_cel = '$tel_cel', tel_rec = '$tel_rec', data_nasci = '$data_nasci2', naturalidade = '$naturalidade', nacionalidade = '$nacionalidade', civil = '$civil', rg = '$rg', orgao = '$orgao', data_rg = '$data_rg2', cpf = '$cpf', titulo = '$titulo', zona = '$zona', secao = '$secao', pai = '$pai', nacionalidade_pai = '$nacionalidade_pai', mae = '$mae', nacionalidade_mae = '$nacionalidade_mae', estuda = '$estuda', data_escola = '$data_escola', escolaridade = '$escolaridade', instituicao = '$instituicao', curso = '$curso', banco = '$banco', agencia ='$agencia', conta = '$conta',tipo_conta = '$tipoDeConta',  status = '$status', data_saida = '$desativacao', campo3 = '$codigo', tipo_contratacao = '$tipo_contratacao', id_curso = '$id_curso', apolice = '$apolice', data_entrada = '$data_entrada2', campo2 = '$dependente', campo1 = '$trabalho',
 data_exame = '$exame_data', reservista = '$reservista', etnia = '$etnia', deficiencia = '$deficiencia', cabelos = '$cabelos', peso = '$peso', altura = '$altura'
, olhos = '$olhos', defeito = '$defeito', cipa = '$cipa', ad_noturno = '$ad_noturno', plano = '$plano', assinatura = '$assinatura', distrato = '$assinatura2', outros = '$assinatura3', pis = '$pis', dada_pis = '$data_pis1', data_ctps = '$data_ctps', serie_ctps = '$serie_ctps', uf_ctps = '$uf_ctps', uf_rg = '$uf_rg', fgts = '$fgts', insalubridade = '$insalubridade', transporte = '$transporte', medica = '$medica', tipo_pagamento = '$tipopg', nome_banco = '$nome_banco', num_filhos = '$filhos', observacao = '$observacao', foto = '$foto_banco', id_cooperativa = '$cooperativa', dataalter = '$data_hoje', useralter = '$id_user' WHERE id_autonomo = '$id_bolsista' LIMIT 1") or die ("Erro no UPDATE:<br><br><font color=red> ".mysql_error());

//VERIFICA SE O BOLSISTA JA ESTÁ CADASTRADO NA TABELA DE VALES
$result_cont_vale = mysql_query ("SELECT id_bolsista FROM vale WHERE id_bolsista = '$id_bolsista' and id_projeto = '$id_projeto'");
$row_cont_vale = mysql_num_rows($result_cont_vale);

if($row_cont_vale == "0"){
mysql_query ("INSERT INTO vale(id_regiao,id_projeto,id_bolsista,nome,cpf,tipo_vale,numero_cartao,valor_cartao,quantidade,qnt1,valor1,qnt2,valor2,qnt3,valor3,qnt4,valor4,tipo1,tipo2,tipo3,tipo4,tipo_cartao_1,tipo_cartao_2,numero_cartao2,valor_cartao2) values 
('$regiao','$id_projeto','$id_bolsista','$nome','$cpf','$tipo_vale','$num_cartao','$valor_cartao','$quantidade','$qnt1','$valor1','$qnt2','$valor2','$qnt3','$valor3','$qnt4','$valor4','$tipo1','$tipo2','$tipo3','$tipo4','$tipo_cartao_1','$tipo_cartao_2','$numero_cartao2','$valor_cartao2')") or die ("houve algum erro de digitação no incert dos vales query: ". mysql_error());
}else{
mysql_query ("update vale set tipo_vale = '$tipo_vale', numero_cartao =  '$num_cartao', valor_cartao = '$valor_cartao', qnt1 = '$qnt1', valor1 = '$valor1', qnt2 = '$qnt2', valor2 ='$valor2', qnt3 = '$qnt3', valor3 = '$valor3', qnt4 = '$qnt4', valor4 = '$valor4', tipo1 = '$tipo1', tipo2 = '$tipo2', tipo3 = '$tipo3', tipo4 = '$tipo4', tipo_cartao_1 = '$tipo_cartao_1', tipo_cartao_2 = '$tipo_cartao_2', numero_cartao2 = '$numero_cartao2', valor_cartao2 = '$valor_cartao2', status_vale = '$transporte' 
WHERE id_projeto = '$id_projeto' and id_bolsista = '$id_bolsista' ") or die ("houve algum erro de digitação na terceira query: ". mysql_error());
}
//FINALIZANDO O PROCESSAMENTO DOS DADOS A RESPEITO DO VALE


//VERIFICA SE O BOLSISTA JA ESTÁ CADASTRADO NA TABELA DEPENDENTES
$result_cont1 = mysql_query ("SELECT id_bolsista FROM dependentes WHERE id_bolsista = '$id_bolsista' AND id_projeto = '$id_projeto' AND 
contratacao = '$tipo_contratacao'");


if(mysql_num_rows($result_cont1) == "0"){
mysql_query ("INSERT INTO dependentes(id_regiao,id_projeto,id_bolsista,nome,data1,nome1,data2,nome2,data3,nome3,data4,nome4,data5,nome5) values 
('$regiao','$id_projeto','$id_bolsista','$nome','$data_filho_1','$filho_1','$data_filho_2','$filho_2','$data_filho_3','$filho_3',
'$data_filho_4','$filho_4','$data_filho_5','$filho_5')") or die ("ERRO (insert dependentes): ". mysql_error());
}else{
mysql_query ("update dependentes vale set data1 = '$data_filho_1', nome1 = '$filho_1', data2 = '$data_filho_2', nome2 = '$filho_2', data3 = '$data_filho_3', nome3 = '$filho_3', data4 = '$data_filho_4', nome4 = '$filho_4', data5 = '$data_filho_5', nome5 = '$filho_5' WHERE id_projeto = '$id_projeto' AND id_bolsista = '$id_bolsista' AND contratacao = '$tipo_contratacao'") or die ("ERRO (update de dependentes): ". mysql_error());
}

$arquivo = isset($_FILES['arquivo']) ? $_FILES['arquivo'] : FALSE;

if($foto_up == "1"){

if(!$arquivo)
{
    $mensagem = "Não acesse esse arquivo diretamente!";
}
// Imagem foi enviada, então a move para o diretório desejado
else
{
    $tipo_arquivo = ".gif";
	// Resolvendo o nome e para onde o arquivo será movido
    $diretorio = "fotos/";
	$nome_tmp = $regiao."_".$id_projeto."_".$id_bolsista.$tipo_arquivo;
	$nome_arquivo = "$diretorio$nome_tmp" ;
	
	move_uploaded_file($arquivo['tmp_name'], $nome_arquivo ) or die ("Erro ao enviar o Arquivo: $nome_arquivo");

}

}else{
// SEM IMAGEM
}

header("Location: bolsista.php?projeto=$id_projeto&regiao=$regiao&sucesso=edicao");
exit;

}

}
?>