<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='../login.php'>Logar</a>";
exit;
}

include "../conn.php";

$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);


if(empty($_REQUEST['update'])) {
    
include "../wfunction.php";

$coop = $_REQUEST['coop'];

$RE = mysql_query("SELECT *, date_format(data_nasci, '%d/%m/%Y')as data_nasci, date_format(data_rg, '%d/%m/%Y')as data_rg, date_format(data_escola, '%d/%m/%Y')as data_escola, date_format(data_entrada, '%d/%m/%Y')as data_entrada, date_format(data_exame, '%d/%m/%Y')as data_exame, date_format(data_saida, '%d/%m/%Y')as data_saida, date_format(data_ctps, '%d/%m/%Y')as data_ctps , date_format(dada_pis, '%d/%m/%Y')as dada_pis, date_format(c_nascimento, '%d/%m/%Y')as c_nascimento, 
                    date_format(e_dataemissao, '%d/%m/%Y')as e_dataemissao,
                    date_format(data_emissao, '%d/%m/%Y')as data_emissao
                    FROM autonomo WHERE id_autonomo = '$coop'");
$Row = mysql_fetch_array($RE);

$REPro = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$Row[id_projeto]'");
$RowPro = mysql_fetch_array($REPro);

$regiao = $RowPro['id_regiao'];

$tipo_contratacao = $Row['tipo_contratacao'];

// Tipo Contratação
switch($Row['tipo_contratacao']) {
	case 1:
       $sel_tipo1 = "selected";
	break;
	case 3:
	   $sel_tipo3 = "selected";
	break;
	case 4:
	   $sel_tipo4 = "selected";
	break;
}

// Estado Civil
switch ($Row['civil']) {
	case "Solteiro":
	$sel_ci1 = "selected";
	break;
	case "Casado";
	$sel_ci2 = "selected";
	break;
	case "Viúvo";
	$sel_ci3 = "selected";
	break;
	case "Sep. Judicialmente";
	$sel_ci4 = "selected";
	break;
	case "Divorciado";
	$sel_ci5 = "selected";
	break;
}

// Sexo
if($Row['sexo'] == "M") { $Sexo1 = "checked"; } else { $Sexo2 = "checked"; }

// Estuda
if($Row['estuda'] == "sim") { $EscolaCheck1 = "checked"; } else { $EscolaCheck2 = "checked"; }

// Dependentes
$RE_Depe = mysql_query ("SELECT *, date_format(data1, '%d/%m/%Y')as data1, date_format(data2, '%d/%m/%Y')as data2, 
date_format(data3, '%d/%m/%Y')as data3, date_format(data4, '%d/%m/%Y')as data4, date_format(data5, '%d/%m/%Y')as data5 FROM dependentes WHERE id_bolsista = '$coop' and id_projeto = '$Row[id_projeto]'");
$RowDepe = mysql_fetch_array($RE_Depe);

// Cabelos
switch ($Row['cabelos']) {
	case "Loiro":
	$caB1 = "selected";
	break;
	case "Castanho Claro";
	$caB2 = "selected";
	break;
	case "Castanho Escuro";
	$caB3 = "selected";
	break;
	case "Ruivo";
	$caB4 = "selected";
	break;
	case "Pretos";
	$caB5 = "selected";
	break;
}

// Olhos
switch ($Row['olhos']) {
	case "Castanho Claro":
	$Olhos1 = "selected";
	break;
	case "Castanho Escuro";
	$Olhos2 = "selected";
	break;
	case "Verde";
	$Olhos3 = "selected";
	break;
	case "Azul";
	$Olhos4 = "selected";
	break;
	case "Mel";
	$Olhos5 = "selected";
	break;
	case "Preto";
	$Olhos6 = "selected";
	break;
}

// Foto
if($Row['foto'] == "1") {
$foto = "Deseja remover a foto? <input name='foto' type='checkbox' id='foto' value='3'/> Sim";
} else {
$foto = "<input name='foto' type='checkbox' id='foto' value='1' onClick=\"document.all.arquivo.style.display = (document.all.arquivo.style.display == 'none') ? '' : 'none' ;\">";
}

// Tipo Conta
if($Row['tipo_conta'] == 'salario') { 
	$TpConta1 = 'checked'; 
} elseif($Row['tipo_conta'] == 'corrente') { 
	$TpConta2 = 'checked';
} else { 
	$TpConta3 = 'checked';
}

// Formatando Valor
$valor = number_format($Row['e_renda'],2,",",".");

// Ativo Inativo
if($Row['status'] == "1") { $ativado1 = "checked"; } else { $ativado2 = "checked"; }

// Recolhimento INSS
if($Row['tipo_inss'] == "1") { $tipoINSS1 = "selected"; } else { $tipoINSS2 = "selected"; }

// Cota
$cota = number_format($Row['cota'],2,",",".");

// Log

$qr_funcionario = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$funcionario = mysql_fetch_array($qr_funcionario);
$ip = $_SERVER['REMOTE_ADDR'];
$local_banco = "Edição de Cooperado";
$acao_banco = "Editando o Cooperado ($Row[campo3]) $Row[nome]";

mysql_query("INSERT INTO log (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao) 
VALUES ('$funcionario[0]', '$funcionario[id_regiao]', '$funcionario[tipo_usuario]', '$funcionario[grupo_usuario]', '$local_banco', NOW(), '$ip', '$acao_banco')") or die ("Erro Inesperado<br><br>".mysql_error());

// Fim do Log

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="shortcut icon" href="../favicon.ico">
<link href="../rh/css/estrutura_cadastro.css" rel="stylesheet" type="text/css">
<script language="javascript" src='../js/ramon.js'></script>
<link href="../js/jquery.ui.theme.css" rel="stylesheet" type="text/css" />
<link href="../js/jquery.ui.datepicker.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../js/jquery-1.3.2.js"></script>
<script type="text/javascript" src="../js/jquery.ui.core.js"></script>
<script type="text/javascript" src="../js/jquery.ui.widget.js"></script>
<script type="text/javascript" src="../js/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../js/jquery.ui.datepicker-pt-BR.js"></script>
<script type="text/javascript">
$(function(){
    
    
        $( "#data_entrada" ).datepicker({ minDate: new Date(2009, 1 - 1, 1) });
        $( "#data_entrada" ).datepicker({ showMonthAfterYear: true });
        
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
<script language="javascript"  type="text/javascript">
function FuncaoInss(a){
	d = document.all;
	if(a == 1) {
		d.divInss.style.display = '';
		d.p_inss.style.display = '';
	} else if(a == 2) {
		d.divInss.style.display = 'none';
		d.p_inss.style.display = 'none';
		d.p_inss.value = '';
		d.inss_recolher.value = 11;
	} else if(a == 3) {
		porcentagem = d.p_inss.value;
		if(porcentagem <= 11) {
			valor = 11 - porcentagem;
		} else {
			valor = 0;
		}
		d.inss_recolher.value = valor;
	}
}
</script>
</head>
<body>
<div id="corpo">
<table align="center" width="100%" cellspacing="0" cellpadding="12" style="font-size:13px; line-height:22px;">
  <tr>
    <td>
      <div style="border-bottom:2px solid #F3F3F3; margin-top:10px;">
         <h2 style="float:left; font-size:18px;">EDITAR CADASTRO 
		   <?php switch($_GET['tipo']) {
                    case 3:
                        echo '<span class="coo">COOPERADO</span>';
                    break;
                    case 4:
                        echo '<span class="aut">AUTÔNOMO / PJ</span>';
                    break;
                 } ?>
         </h2>
         <p style="float:right;">
       	    <a href='../ver_bolsista.php?reg=<?=$Row['id_regiao']?>&bol=<?=$coop?>&pro=<?=$Row['id_projeto']?>'> &laquo; Voltar</a>
         </p>
         <div class="clear"></div>
    </div>
    <p>&nbsp;</p>
    
<form action="<?=$_SERVER['PHP_SELF']?>" method="post" id="form1" name="form1" onSubmit="return validaForm()" enctype="multipart/form-data">

<table cellpadding="0" cellspacing="1" class="secao">
  <tr>
     <td class="secao_pai" colspan="2" style="border-top:1px solid #777;">DADOS DO PROJETO</td>
  </tr>
   <?php if($tipo_contratacao == 4) { ?>
  <tr>
  	<td>Matrícula:</td>
  	<td><input type="text" name="matricula" value="<?php echo $Row['matricula']?>"/></td>
  </tr>
	<tr>
		<td>Número do processo:</td>
		<td><input type="text" name="n_processo"  value="<?php echo $Row['n_processo']?>"/></td>
	</tr>
  <?php }?>
  <tr>
     <td width="25%" class="secao">Projeto:</td>
     <td width="75%"><?=$RowPro['0']." - ".$RowPro['nome']?></td>
  </tr>
  <tr>
     <td class="secao">Cooperativa Vinculada:</td> 
     <td>
         <?php $result_coop = mysql_query("SELECT * FROM cooperativas WHERE id_regiao = '$Row[id_regiao]'");
		       $numero_coop = mysql_num_rows($result_coop);
			   	  if(!empty($numero_coop)) {
					   print "<select name='vinculo' id='vinculo'>";
					   while($row_coop = mysql_fetch_array($result_coop)) {
							if($Row['id_cooperativa'] == $row_coop['0']) {
								print "<option value='$row_coop[0]' selected>$row_coop[0] - $row_coop[fantasia]</option>";
							} else {
								print "<option value='$row_coop[0]'>$row_coop[0] - $row_coop[fantasia]</option>";
							}
				        }
						print "</select>";
			       } else {
				       print "Nenhuma Cooperativa Cadastrada";
			       } ?></td>
  </tr>
  <tr>
    <td class="secao">Atividade:</td>
    <td>
  <?php $result_curso = mysql_query("SELECT * FROM curso WHERE campo3 = '$Row[id_projeto]' AND tipo = '3' AND id_regiao = '$Row[id_regiao]' ORDER BY campo3 ASC");
	    $verifica_curso = mysql_num_rows($result_curso);
		  if(!empty($verifica_curso)) {
			       print "<select name='atividade' id='atividade'>";
			  while($row_curso = mysql_fetch_array($result_curso)) {
				   if($Row['id_curso'] == $row_curso['0']) {
					   print "<option value='$row_curso[0]' selected>$row_curso[0] - $row_curso[nome] - R$ {$row_curso['salario']}</option>";
				   } else {
					   print "<option value='$row_curso[0]'>$row_curso[0] - $row_curso[nome] - R$ {$row_curso['salario']}</option>";
				   }
			  }
			       print "</select>";
		  } else {
			   print "Nenhuma Atividade Cadastrada";
		  } ?></td>
  </tr>
    <?php 
  if($tipo_contratacao == 4) { ?>
  <tr>
  	<td></td>
  	<td><input type="checkbox" name="contrato_medico" value="1" <?php if(!empty($Row['contrato_medico'])) echo 'checked="checked"';?>/> Necessita de contrato para médicos?</td>
  </tr>
  <?php  } ?>

  <tr>
    <td class="secao">Unidade:</td>
    <td>
    <?php 
    $result_unidade = mysql_query("SELECT * FROM unidade WHERE id_regiao = '$Row[id_regiao]' and campo1 = '$Row[id_projeto]' ORDER BY unidade");
    $verifica_unidade = mysql_num_rows($result_unidade);
    if(!empty($verifica_unidade)) {
        print "<select name='locacao' id='locacao'>";
        while($row_unidade = mysql_fetch_array($result_unidade)) {
    ?>
        <option value='<?php echo $row_unidade[unidade] . " // " . $row_unidade[id_unidade]; ?>' <?php echo selected($row_unidade[id_unidade], $Row[id_unidade]); ?>><?php echo $row_unidade[id_unidade] . " - " . $row_unidade[unidade];?></option>			       
    <?php
        }
        print "</select>";
    } else {
        print "Nenhuma Unidade Cadastrada";
    } ?>
    </td>
  </tr>
  <tr>
    <td class="secao">Código:</td>
    <td><input name="codigo" type="text" id="codigo" value="<?=$Row['campo3']?>" size="10" /></td>
  </tr>
  <tr>
    <td class="secao">Tipo de Contratação:</td>
    <td>
      <select name="contratacao" id="contratacao">
        <option value="1" <?=$sel_tipo1?>>Aut&ocirc;nomo</option>
        <option value="3" <?=$sel_tipo3?>>Cooperado</option>
        <option value="4" <?=$sel_tipo4?>>Aut&ocirc;nomo / PJ</option>
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
      <input name='nome' type='text' id='nome' size='75' value='<?=$Row['nome']?>'
               onChange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">Data de Nascimento:</td>
    <td>
      <input name='data_nasci' type='text' id='data_nasci' size='15' value='<?=$Row['data_nasci']?>'
    	       onKeyUp="mascara_data(this); pula(10,this.id,civil.id)">
    </td>
  </tr>
  <tr>
    <td class="secao">Estado Civil:</td>
    <td>
	   <select name='civil' id='civil'>
            <option <?=$sel_ci1?>>Solteiro</option>
            <option <?=$sel_ci2?>>Casado</option>
            <option <?=$sel_ci3?>>Viúvo</option>
            <option <?=$sel_ci4?>>Sep. Judicialmente</option>
            <option <?=$sel_ci5?>>Divorciado</option>
       </select>
	</td>
    <td class="secao">Sexo:</td>
    <td>
      <label><input name='sexo' type='radio' class="reset" id='sexo' value='M' <?=$Sexo1?>> Masculino</label><br>
      <label><input name='sexo' type='radio' class="reset" id='sexo' value='F' <?=$Sexo2?>> Feminino</label>
    </td>
    <td class="secao">Nacionalidade:</td>
    <td><input name='nacionalidade' type='text' id='nacionalidade' size='15' value='<?=$Row['nacionalidade']?>'
           onChange="this.value=this.value.toUpperCase()"/></td>
  </tr>
  <tr>
    <td class="secao">Endereço:</td>
    <td><input name='endereco' type='text' id='endereco' size='35' value='<?=$Row['endereco']?>'
               onChange="this.value=this.value.toUpperCase()"/>
     </td>
    <td class="secao">Bairro:</td>
    <td><input name='bairro' type='text' id='bairro' size='16' value='<?=$Row['bairro']?>'  
               onChange="this.value=this.value.toUpperCase()"/></td>
    <td class="secao">UF:</td>
     <td><input name='uf' type='text' id='uf' size='2' maxlength='2' value='<?=$Row['uf']?>'
        	    onChange="this.value=this.value.toUpperCase()"
        	    onkeyup="pula(2,this.id,naturalidade.id)" /></td>
  </tr>
  <tr>
    <td class="secao">Cidade:</td>
    <td><input name='cidade' type='text' id='cidade' size='35' value='<?=$Row['cidade']?>'
                           onChange="this.value=this.value.toUpperCase()"/></td>
    <td class="secao">CEP:</td>
    <td><input name='cep' type='text' id='cep' size='16' maxlength='9' value='<?=$Row['cep']?>'
               style='text-transform:uppercase;' 	   
               OnKeyPress="formatar('#####-###', this)" 
               onKeyUp="pula(9,this.id,uf.id)" /></td>
    <td class="secao">Naturalidade:</td>
    <td><input name='naturalidade' type='text' id='naturalidade' size='15' value='<?=$Row['naturalidade']?>'
           onChange="this.value=this.value.toUpperCase()"/></td>
  </tr>
  <tr>
    <td class="secao">Estuda Atualmente?</td>
    <td colspan="3">
        <label><input name="estuda" type="radio" class="reset" value="sim" <?=$EscolaCheck1?>> Sim </label>
        <label><input name="estuda" type="radio" class="reset" value="nao" <?=$EscolaCheck2?>> Não </label>
        <?=$mensagem_sexo?>  
	  </td>
    <td class="secao">Término em:</td>
    <td>
      <input name="data_escola" type="text" id="data_escola" size="15" maxlength="10" value="<?=$Row['data_escola']?>"
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
                       <option value="<?=$escolaridade['id']?>"<? if($Row['escolaridade'] == $escolaridade['id']) { ?> selected="selected"<? } ?>><?=$escolaridade['cod']?> - <?=$escolaridade['nome']?></option>
              <?php } ?>
       </select>
    </td>
    <td class="secao">Curso:</td>
    <td><input name='curso' type='text' id='zona' size='16' value='<?=$Row['curso']?>'
       		   onChange="this.value=this.value.toUpperCase()"/></td>
    <td class="secao">Institui&ccedil;&atilde;o:</td>
    <td><input name='instituicao' type='text' id='instituicao' size='15' value='<?=$Row['instituicao']?>'
        	   onChange="this.value=this.value.toUpperCase()"/></td>
  </tr>
  <tr>
    <td class="secao">Telefone Fixo:</td>
    <td><input name='tel_fixo' type='text' id='tel_fixo' size='16' value='<?=$Row['tel_fixo']?>'
               onKeyPress="return(TelefoneFormat(this,event))" 
               onKeyUp="pula(13,this.id,tel_cel.id)"></td>
    <td class="secao">Celular:</td>
    <td><input name='tel_cel' type='text' id='tel_cel' size='16' value='<?=$Row['tel_cel']?>'  
		       onKeyPress="return(TelefoneFormat(this,event))" 
		       onKeyUp="pula(13,this.id,tel_rec.id)" /></td>
    <td class="secao">Recado:</td>
    <td>
      <input name='tel_rec' type='text' id='tel_rec' size='15' value='<?=$Row['tel_rec']?>' 
    	      onKeyPress="return(TelefoneFormat(this,event))" 
   		      onKeyUp="pula(13,this.id,c_nome.id)" />
    </td>
  </tr>
   <tr>
  	<td class="secao">E-mail:</td>
    <td colspan="5">
    	<input name="email" type="text" id="email" size="35" value='<?=$Row['email']?>' />
    </td>
  </tr>
  <tr>
  	<td class="secao">Tipo Sanguíneo:</td>
    <td colspan="5">
          <select name="tiposanguineo" id="tiposanguineo" >
                <option value="">Selecione</option>
          <?php
          $qr_ts = mysql_query("SELECT * FROM tipo_sanguineo");
          while($row_ts = mysql_fetch_assoc($qr_ts)){   
              $selected = ($Row['tipo_sanguineo'] == $row_ts['nome'])?'selected="selected"': '';
              echo '<option value="'.$row_ts['nome'].'" '.$selected.' >'.$row_ts['nome'].'</option>';
          }
          ?>    
          </select>
    </td>
  </tr>
</table>

<table cellpadding="0" cellspacing="1" class="secao">
  <tr>
	<td class="secao_pai" colspan="4">DADOS DA FAMÍLIA</td>
  </tr>
  <tr>
    <td class="secao">Nome do C&ocirc;njuge:</td>
    <td>
      <input name='c_nome' type='text' id='c_nome' size='50' value="<?=$Row['c_nome']?>"
             onChange="this.value=this.value.toUpperCase()" />
    </td>
    <td class="secao">Data de Nascimento:</td>
    <td>
      <input name='c_nascimento' type='text' id='c_nascimento' size='10' value="<?=$Row['c_nascimento']?>"
             onkeyup="mascara_data(this); pula(10,this.id,c_profissao.id)" />
    </td>
  </tr>
  <tr>
    <td class="secao">CPF C&ocirc;njuge:</td>
    <td>
      <input name='c_cpf' type='text' id='c_cpf' size='17' maxlength='14' value="<?=$Row['c_cpf']?>"
             onkeypress="formatar('###.###.###-##', this)" 
             onkeyup="pula(14,this.id,c_nascimento.id)" />
    </td>
    <td class="secao">Profiss&atilde;o C&ocirc;njuge:</td>
    <td>   
      <input name='c_profissao' type='text' id='c_profissao' size='20' value="<?=$Row['c_profissao']?>"
             onChange="this.value=this.value.toUpperCase()" />
    </td>
  </tr>
  <tr>
    <td class="secao">Filiação - Pai:</td>
    <td>
      <input name='pai' type='text' id='pai' size='50' value="<?=$Row['pai']?>"
             onChange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">Nacionalidade Pai:</td>
    <td>
      <input name='nacionalidade_pai' type='text' id='nacionalidade_pai' size='15' value="<?=$Row['nacionalidade_pai']?>"
             onChange="this.value=this.value.toUpperCase()"/>	
    </td>
  </tr>
  <tr>
    <td class="secao">Filiação - Mãe:</td>
    <td>
      <input name='mae' type='text' id='mae' size='50' value="<?=$Row['mae']?>"
             onChange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">Nacionalidade Mãe:</td>
    <td>
      <input name='nacionalidade_mae' type='text' id='nacionalidade_mae' size='15' value="<?=$Row['nacionalidade_mae']?>"
             onChange="this.value=this.value.toUpperCase()"/>	
    </td>
  </tr>
  <tr>
    <td class="secao">Número de Filhos:</td>
    <td colspan="3">
      <input name='filhos' type='text' class='campotexto  style37' id='filhos' size='2' value="<?=$Row['num_filhos']?>" />
    </td>
  </tr>
  <tr>
    <td class="secao">Nome:</td>
    <td>
      <input name='filho_1' type='text' id='filho_1' size='50' value="<?=$RowDepe['nome1']?>" onChange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">Nascimento:</td>
    <td>
      <input name='data_filho_1' type='text' size='12' maxlength='10' id='data_filho_1' value="<?=$RowDepe['data1']?>"
             onKeyUp="mascara_data(this); pula(10,this.id,filho_2.id)"
             onChange="this.value=this.value.toUpperCase()"/>
    </td>
  </tr>
  <tr>
    <td class="secao">Nome:</td>
    <td>
    <input name='filho_2' type='text' id='filho_2' size='50' value="<?=$RowDepe['nome2']?>"
           onChange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">Nascimento:</td>
    <td>
    <input name='data_filho_2' type='text' size='12' maxlength='10' id='data_filho_2' value="<?=$RowDepe['data2']?>"
           onKeyUp="mascara_data(this); pula(10,this.id,filho_3.id)"
           onChange="this.value=this.value.toUpperCase()"/>
    </td>
  </tr>
  <tr>
    <td class="secao">Nome:</td>
    <td>
    <input name='filho_3' type='text' id='filho_3' size='50' value="<?=$RowDepe['nome3']?>"  
           onChange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">Nascimento:</td>
    <td>
    <input name='data_filho_3' type='text' size='12' maxlength='10' id='data_filho_3' value="<?=$RowDepe['data3']?>"
           onKeyUp="mascara_data(this); pula(10,this.id,filho_4.id)"
           onChange="this.value=this.value.toUpperCase()"/>
    </td>
  </tr>
  <tr>
    <td class="secao">Nome:</td>
    <td>
    <input name='filho_4' type='text' id='filho_4' size='50' value="<?=$RowDepe['nome4']?>"
           onChange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">Nascimento:</td>
    <td>
    <input name='data_filho_4' type='text' size='12' maxlength='10' id='data_filho_4' value="<?=$RowDepe['data4']?>"
           onKeyUp="mascara_data(this); pula(10,this.id,filho_5.id)"
            onChange="this.value=this.value.toUpperCase()"/>
    </td>
  </tr>
  <tr>
    <td class="secao">Nome:</td>
    <td>
    <input name='filho_5' type='text' id='filho_5' size='50' value="<?=$RowDepe['nome5']?>"
           onChange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">Nascimento:</td>
    <td>
    <input name='data_filho_5' type='text' size='12' maxlength='10' id='data_filho_5' value="<?=$RowDepe['data5']?>"
           onkeyup="mascara_data(this)"
           onChange="this.value=this.value.toUpperCase()"/>
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
        <select name='cabelos' id='cabelos'>
            <option <?=$caB1?>>Loiro</option>
            <option <?=$caB2?>>Castanho Claro</option>
            <option <?=$caB3?>>Castanho Escuro</option>
            <option <?=$caB4?>>Ruivo</option>
            <option <?=$caB5?>>Pretos</option>
        </select>
    </td>
    <td class="secao">Olhos:</td>
    <td>
        <select name='olhos' id='olhos'>
            <option <?=$Olhos1?>>Castanho Claro</option>
            <option <?=$Olhos2?>>Castanho Escuro</option>
            <option <?=$Olhos3?>>Verde</option>
            <option <?=$Olhos4?>>Azul</option>
            <option <?=$Olhos5?>>Mel</option>
            <option <?=$Olhos6?>>Preto</option>
        </select>
    </td>
    <td class="secao">Peso:</td>
    <td> 
    	<input name='peso' type='text' id='peso' size='5' value="<?=$Row['peso']?>" />
    </td>
  </tr>
  <tr>
    <td class="secao">Altura:</td>
    <td> 
    	<input name='altura' type='text' id='altura' size='5' value="<?=$Row['altura']?>" />
    </td>
    <td class="secao">Etnia:</td>
    <td>
      <select name='etnia'>
        <option value="6">Não informado</option>
          <?php $qr_etnias = mysql_query("SELECT * FROM etnias WHERE status = 'on' LIMIT 0,5");
                   while($etnia = mysql_fetch_assoc($qr_etnias)) { ?>
     <option value="<?=$etnia['id']?>"<? if($Row['etnia'] == $etnia['id']) { ?> selected<? } ?>><?=$etnia['nome']?></option>
          <?php } ?>
       </select>
    </td>
    <td class="secao">Marcas ou Cicatriz aparente:</td>
    <td>
    <input name='defeito' type='text' id='defeito' size='18' value="<?=$Row['defeito']?>" 
           onChange="this.value=this.value.toUpperCase()"/>
    </td>
  </tr>
  <tr>
    <td class="secao">Deficiência:</td>
    <td colspan="5">
      <select name='deficiencia'>
         <option value="">Não é portador de deficiência</option>
           <?php $qr_deficiencias = mysql_query("SELECT * FROM deficiencias WHERE status = 'on'");
                    while($deficiencia = mysql_fetch_assoc($qr_deficiencias)) { ?>
    <option value="<?=$deficiencia['id']?>"<? if($row['deficiencia'] == $deficiencia['id']) { ?> selected="selected"<? } ?>><?=$deficiencia['nome']?></option>
          <?php } ?>
      </select>
    </td>
  </tr>
  <tr id="ancora_foto">
    <td class="secao">
       Enviar Foto: 
    </td>
    <td colspan="5">
       <?=$foto?>
       <input name='arquivo' type='file' id='arquivo' size='60' style='display:none'/><br>
       <span style="font-size:11px; color:#C30;">(somente arquivo .gif) </span>
    </td>
  </tr>
</table>

<table cellpadding="0" cellspacing="1" class="secao">
  <tr>
    <td class="secao_pai" colspan="8">DOCUMENTAÇÃO</td>
  </tr>
  <tr>
    <td class="secao">Nº do RG:</td>
    <td>
	    <input name='rg' type='text' id='rg' size='13' maxlength='14' value="<?=$Row['rg']?>"
               OnKeyPress="formatar('##.###.###-###', this)" 
               onkeyup="pula(14,this.id,orgao.id)">    </td>
    <td class="secao">Orgão Expedidor:</td>
    <td>
        <input name='orgao' type='text' id='orgao' size='8' value="<?=$Row['orgao']?>"
               onChange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">UF:</td>
    <td>
        <input name='uf_rg' type='text' id='uf_rg' size='2' maxlength='2' value="<?=$Row['uf_rg']?>"
               onKeyUp="pula(2,this.id,data_rg.id)"
               onChange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">Data Expedição:</td>
    <td>
        <input name='data_rg' type='text' size='12' maxlength='10' value="<?=$Row['data_rg']?>" id='data_rg'
               onkeyup="mascara_data(this); pula(10,this.id,cpf.id)" />	
    </td>
  </tr>
  <tr>
    <td class="secao">CPF:</td>
    <td>
        <input name='cpf' type='text' id='cpf' size='17' maxlength='14' value="<?=$Row['cpf']?>"
               OnKeyPress="formatar('###.###.###-##', this)"   
			   onkeyup="pula(14,this.id,reservista.id)" />
    </td>
    <td class="secao">Carteira do Conselho:</td>
    <td colspan="3">
        <input name='conselho' type='text' id='conselho' size='13' value="<?=$Row['conselho']?>" />
    </td>
     <td class="secao">Data de emissão:</td>
    <td>
        <input name='data_emissao' type='text' size='12' maxlength='10' value="<?=$Row['data_emissao']?>" id='data_emissao'
               onkeyup="mascara_data(this); pula(10,this.id,cpf.id)" />	
    </td>

  </tr>
  <tr>
    <td class="secao">Nº Carteira de Trabalho:</td>
    <td>
         <input name='trabalho' type='text' id='trabalho' size='15' value="<?=$Row['campo1']?>" />
    </td>
    <td class="secao">Série:</td>
    <td>
         <input name='serie_ctps' type='text' id='serie_ctps' size='10' value="<?=$Row['serie_ctps']?>" />
    </td>
    <td class="secao">UF:</td>
    <td>
		 <input name='uf_ctps' type='text' id='uf_ctps' size='2' maxlength='2' value="<?=$Row['uf_ctps']?>"
                onKeyUp="pula(2,this.id,data_ctps.id)"
                onChange="this.value=this.value.toUpperCase()"/></td>
    <td class="secao">Data carteira de Trabalho:</td>
    <td>
         <input name='data_ctps' type='text' size='12' maxlength='10' id='data_ctps' value="<?=$Row['data_ctps']?>"
                onkeyup="mascara_data(this); pula(10,this.id,titulo2.id)" />    
    </td>
  </tr>
  <tr>
    <td class="secao">Nº Título de Eleitor:</td>
    <td>
        <input name='titulo' type='text' id='titulo2' size='10' value="<?=$Row['titulo']?>" />
    </td>
    <td class="secao">Zona:</td>
    <td colspan="3">
        <input name='zona' type='text' id='zona2' size='3' value="<?=$Row['zona']?>" />
    </td>
    <td class="secao">Seção:</td>
    <td>
        <input name='secao' type='text' id='secao' size='3' value="<?=$Row['secao']?>" />
    </td>
  </tr>
  <tr>
    <td class="secao">PIS:</td>
    <td>
      <input name='pis' type='text' id='pis' size='12' value="<?=$Row['pis']?>" />
    </td>
    <td class="secao">Data Pis:</td>
    <td colspan="3">
      <input name='data_pis' type='text' size='12' maxlength='10' id='data_pis' value="<?=$Row['dada_pis']?>"
             onKeyUp="mascara_data(this); pula(10,this.id,fgts.id)" />
    </td>
    <td class="secao">FGTS:</td>
    <td>
        <input name='fgts' type='text' id='fgts' size='10' value="<?=$Row['fgts']?>" />
    </td>
  </tr>
    <tr>
        <td class="secao">Certificado de Reservista:</td>
       <td colspan="7">
           <input name='reservista' type='text' id='reservista'  value="<?=$Row['reservista']?>" size='18' />
       </td>
    </tr>
  <tr>
    <td class="secao">INSS a Recolher:</td>
    <td colspan="7">
      <input name="inss_recolher" type="text" class="campotexto" id="inss_recolher" 
	         value="<?=$Row['inss']?>" size="7">
    </td>
  </tr>
  <tr>
    <td class="secao">Tipo de recolhimento:</td>
    <td colspan="7">
      <select name='tipoinss' id='tipoinss'>
        <option value="1" <?=$tipoINSS1?>>VALOR FIXO</option>
        <option value="2" <?=$tipoINSS2?>>VALOR PERCENTUAL</option>
      </select>
    </td>
  </tr>
</table>

<table width='95%' border='0' align='center' cellpadding='0' cellspacing='2' style="display:none">
  <tr>
    <td colspan='6'>BENEFÍCIOS</td>
  </tr>
  <tr>
    <td width='19%'>
	Assistência Médica:	</td>
    <td>
	
	<table width='100%'>
<tr> 
<td width='74'> 
<label><input type='radio' name='medica' value='1' $chek_medi1>Sim</label></td><td width='255'> 
<label><input type='radio' name='medica' value='0' $chek_medi0>Não</label> $mensagem_medi</td>
</tr>
</table>	</td>
    <td width='19%'>
	Tipo de Plano:</td>
    <td width='19%'>
	
<select name='plano_medico' id='plano_medico'>

<option value=1 $selected_planoF>Familiar</option>
<option value=2 $selected_planoI>Individual</option>
</select>   </td>
  </tr>
  <tr>
    <td>Seguro, Apólice:</td>
    <td>
          <select name='apolice' id='apolice'>
<option value='0'>Não Possui</option>

<?php
$result_ap = mysql_query("SELECT * FROM apolice WHERE id_regiao = $row[regiao]", $conn);
while ($row_ap = mysql_fetch_array($result_ap)){
  if($row_ap['id_apolice'] == $row[apolice]){
  print "<option value='$row_ap[id_apolice]' selected>$row_ap[razao]</option>";   
  }else{
  print "<option value='$row_ap[id_apolice]'>$row_ap[razao]</option>";
  }
}


?>
</select>
        </select>
    </td>
    <td>Dependente:</td>
    <td>
      <input name='dependente' type='text' id='dependente' size='20' value=''
 
 
 onChange="this.value=this.value.toUpperCase()"/>
    </td>
  </tr>
  <tr>
    <td>Insalubridade:</td>
    <td>
    <input name='insalubridade' type='checkbox' id='insalubridade2' value='1' $chek1/></td>
    
	<td>Adicional Noturno:</td>
    <td>
	<table class='linha'>
<tr> 
<td width='61'> 
<label><input type='radio' name='ad_noturno' value='1' $checkad_noturno1>Sim</label></td>
<td width='61'> 
<label><input type='radio' name='ad_noturno' value='0' $checkad_noturno0>Não</label></td>
</tr>
</table>
      </td>
  </tr>
  
<tr>
<td>Vale Transporte:</td>
<td>

<input name='transporte' type='checkbox' id='transporte2' value='1' onClick="document.all.tablevale.style.display = (document.all.tablevale.style.display == 'none') ? '' : 'none' ;" $chek2 />

</td>
<td>Integrante do CIPA:</td>
<td>
	
<table class='linha'>
<tr> 
<td width='61'> 
<label><input type='radio' name='cipa' value='1' $checkedcipa1>Sim</label></td>
<td width='61'> 
<label><input type='radio' name='cipa' value='0' $checkedcipa0>Não</label></td>
</tr>
</table>	</td>
  </tr>  
</table>

<table cellpadding="0" cellspacing="1" class="secao">
  <tr>
    <td class="secao_pai" colspan="6">INFORMA&Ccedil;&Otilde;ES PROFISSIONAIS</td>
  </tr>
  <tr>
    <td width="13%" class="secao">Empresa:</td>
    <td colspan="3">
      <input name='e_nome' type='text' id='e_nome' size='50' value='<?=$Row['e_empresa']?>'
             onChange="this.value=this.value.toUpperCase()"/>
    </td>
    <td width='10%' class="secao">CNPJ:</td>
    <td width='22%'>
      <input name='e_cnpj' type='text' id='e_cnpj' value='<?=$Row['e_cnpj']?>'
             OnKeyPress="formatar('##.###.###/####-##', this)"
		     onkeyup="pula(18,this.id,e_endereco.id)" size="19" maxlength='18'/>
    </td>
  </tr>
  <tr>
    <td class="secao">Endere&ccedil;o:</td>
    <td width='24%'>
      <input name='e_endereco' type='text' id='e_endereco' value='<?=$Row['e_endereco']?>'
             onChange="this.value=this.value.toUpperCase()" size='35'/>
    </td>
    <td width='11%' class="secao">Bairro:</td>
    <td width='20%'>
      <input name='e_bairro' type='text' id='e_bairro' value='<?=$Row['e_bairro']?>'
             onChange="this.value=this.value.toUpperCase()" size='20'/>
    </td>
    <td class="secao">Cidade:</td>
    <td>
      <input name='e_cidade' type='text' id='e_cidade' value='<?=$Row['e_cidade']?>'
             onChange="this.value=this.value.toUpperCase()" size='20'/>
      </td>
  </tr>
  <tr>
    <td class="secao">Estado:</td>
    <td>
      <input name='e_estado' type='text' id='e_estado' size='2' maxlength='2' value='<?=$Row['e_estado']?>'
             onChange="this.value=this.value.toUpperCase()"  
             onKeyUp="pula(2,this.id,e_cep.id)" />
    </td>
    <td class="secao">CEP:</td>
    <td>
      <input name='e_cep' type='text' id='e_cep' size='10' maxlength='9' value='<?=$Row['e_cep']?>'
             style='text-transform:uppercase;'
             OnKeyPress="formatar('#####-###', this)" 
             onKeyUp="pula(9,this.id,e_ramo.id)" />
    </td>
    <td class="secao">Ramo Atividade:</td>
    <td>
      <input name='e_ramo' type='text' id='e_ramo' value='<?=$Row['e_ramo']?>'
             onChange="this.value=this.value.toUpperCase()" size='30'/>
     </td>
  </tr>
  <tr>
    <td class="secao">Telefone:</td>
    <td>
      <input name='e_telefone' type='text' id='e_telefone' size='14' value='<?=$Row['e_tel']?>'
             onKeyPress="return(TelefoneFormat(this,event))" 
             onKeyUp="pula(13,this.id,e_ramal.id)">
    </td>
    <td class="secao">Ramal:</td>
    <td>
      <input name='e_ramal' type='text' id='e_ramal' size='14' value='<?=$Row['e_ramal']?>'>
    </td>
    <td class="secao">Fax:</td>
    <td> 
      <input name='e_fax' type='text' id='e_fax' size='14' value='<?=$Row['e_fax']?>'
			 onKeyPress="return(TelefoneFormat(this,event))" 
			 onKeyUp="pula(13,this.id,e_email.id)">
    </td>
  </tr>
  <tr>
    <td class="secao">E-mail:</td>
    <td>
      <input name='e_email' type='text' id='e_email' size='30' value='<?=$Row['e_email']?>'
             style="text-transform:lowercase">
    </td>
    <td class="secao">Tempo de Servi&ccedil;o:</td>
    <td>
      <input name='e_tempo' type='text' id='e_tempo' size='14' value='<?=$Row['e_tempo']?>'
             onChange="this.value=this.value.toUpperCase()">
    </td>
    <td class="secao">Profiss&atilde;o:</td>
    <td>
      <input name='e_profissao' type='text' id='e_profissao' size='14' value='<?=$Row['e_profissao']?>'
             onChange="this.value=this.value.toUpperCase()">
    </td>
  </tr>
  <tr>
    <td class="secao">Cargo:</td>
    <td>
      <input name='e_cargo' type='text' id='e_cargo' size='20' value='<?=$Row['e_cargo']?>'
             onChange="this.value=this.value.toUpperCase()">
    </td>
    <td class="secao">Data Emiss&atilde;o:</td>
    <td>
      <input name='e_dataemissao' type='text' size='12' maxlength='10' id='e_dataemissao' value='<?=$Row['e_dataemissao']?>'
             onkeyup="mascara_data(this); pula(10,this.id,e_referencia.id)" /></td>
    <td class="secao">Refer&ecirc;ncia:</td>
    <td>
      <input name='e_referencia' type='text' id='e_referencia' value='<?=$Row['e_referencia']?>'
             onChange="this.value=this.value.toUpperCase()" />
    </td>
  </tr>
  <tr>
    <td class="secao">Renda:</td>
    <td colspan="5">
      <input name='e_renda' type='text' id='e_renda' size="15" value='<?=$valor?>'
             onChange="this.value=this.value.toUpperCase()" 
             OnKeyDown="FormataValor(this,event,17,2)" />
    </td>
  </tr>
</table>

<table cellpadding="0" cellspacing="1" class="secao">
  <tr>
    <td colspan='6' class="secao_pai">REFER&Ecirc;NCIA</td>
  </tr>
  <tr>
    <td width="13%" class="secao">Nome:</td>
    <td colspan="5">
      <input name='r_nome' type='text' id='r_nome' size='50' value='<?=$Row['r_nome']?>'
             onChange="this.value=this.value.toUpperCase()"/>
    </td>
  </tr>
  <tr>
    <td class="secao">Endere&ccedil;o:</td>
    <td width="24%">
      <input name='r_endereco' type='text' id='r_endereco' value='<?=$Row['r_endereco']?>'
             onChange="this.value=this.value.toUpperCase()" size='35'/>
    </td>
    <td width="11%" class="secao">Bairro:</td>
    <td width="20%"> 
      <input name='r_bairro' type='text' id='r_bairro' value='<?=$Row['r_bairro']?>'
             onChange="this.value=this.value.toUpperCase()" size='20'/>
    </td>
    <td width="10%" class="secao">Cidade:</td>
    <td width="22%"> 
      <input name='r_cidade' type='text' id='r_cidade' value='<?=$Row['r_cidade']?>'
             nChange="this.value=this.value.toUpperCase()" size='20'/>
    </td>
  </tr>
  <tr>
    <td class="secao">Estado:</td>
    <td>
    <input name='r_estado' type='text' id='r_estado' size='2' onKeyUp="pula(2,this.id,r_cep.id)" value='<?=$Row['r_estado']?>'
           onChange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">CEP:</td>
    <td>
      <input name='r_cep' type='text' id='r_cep' size='10' maxlength='9' value='<?=$Row['r_cep']?>'
             style='text-transform:uppercase;'
             OnKeyPress="formatar('#####-###', this)" 
             onKeyUp="pula(9,this.id,r_email.id)" />
    </td>
    <td class="secao">Email:</td>
    <td>
      <input name='r_email' type='text' id='r_email'  value='<?=$Row['r_email']?>' size="30"
             style="text-transform:lowercase" />
    </td>
  </tr>
  <tr>
    <td class="secao">Telefone:</td>
    <td>
      <input name='r_telefone' type='text' id='r_telefone' size='14' value='<?=$Row['r_tel']?>'
             onKeyPress="return(TelefoneFormat(this,event))" 
             onKeyUp="pula(13,this.id,r_ramal.id)" />
    </td>
    <td class="secao">Ramal:</td>
    <td>
      <input name='r_ramal' type='text' id='r_ramal' size='14' value='<?=$Row['r_ramal']?>' />
    </td>
    <td class="secao">Fax:</td>
    <td>
      <input name='r_fax' type='text' id='r_fax' size='14' value='<?=$Row['r_fax']?>'
             onKeyPress="return(TelefoneFormat(this,event))" 
             onKeyUp="pula(13,this.id,data_entrada.id)" />
      </td>
  </tr>
</table>

<table cellpadding="0" cellspacing="1" class="secao">
  <tr>
    <td class="secao_pai" colspan="4">DADOS BANCÁRIOS</td>
  </tr>
  <tr>
    <td width="15%" class="secao">Banco:</td>
    <td width="30%">
      <select name="banco" id="banco">
        <option value="0">Nenhum Banco</option>
  <?php
	$result_banco = mysql_query("SELECT * FROM bancos WHERE id_projeto = '$Row[id_projeto]' AND status_reg = '1'");
	
	while ($row_banco = mysql_fetch_array($result_banco)) {
		if($Row['banco'] == $row_banco['0']){
			print "<option value='$row_banco[0]' selected>$row_banco[id_banco] - $row_banco[nome]</option>";
		}else{
			print "<option value='$row_banco[0]'>$row_banco[id_banco] - $row_banco[nome]</option>";
		}
	}
	
	if($Row['banco'] == "9999") {
		print "<option value='9999' selected>Outro</option></select>";
	} else {
		print "<option value='9999'>Outro</option></select>";
	}
  ?>
        </select>
    </td>
    <td width="17%" class="secao">Ag&ecirc;ncia:</td>
    <td width="35%">
      <input name="agencia" type="text" id="agencia" size="12" value="<?=$Row['agencia']?>" />
    </td>
  </tr>
  <tr id="linhabanc2">
    <td class="secao">Conta:</td>
    <td>
      <input name='conta' type='text' id='conta' size='12' value='<?=$Row['conta']?>' /><br />
      <label><input type='radio' name='radio_tipo_conta' value='salario' <?=$TpConta1?>>Conta Sal&aacute;rio</label>  
      <label><input type='radio' name='radio_tipo_conta' value='corrente' <?=$TpConta2?>>Conta Corrente</label>
      <label><br />
        <input type='radio' name='radio_tipo_conta' value='' <?=$TpConta3?>>
        Sem Conta</label>
    </td>
    <td class="secao">Nome do Banco:<br />(caso n&atilde;o esteja na lista acima)</td>
    <td>
      <input name='nomebanco' type='text' id='nomebanco' size='25' value='<?=$Row['nome_banco']?>'
             onChange="this.value=this.value.toUpperCase()"/>
    </td>
  </tr>
</table>

<table cellpadding="0" cellspacing="1" class="secao">
  <tr>
    <td colspan='4' class="secao_pai">DADOS FINANCEIROS E DE CONTRATO</td>
  </tr>
  <tr>
    <td class="secao">Data de Entrada:</td>
    <td>
      <input name='data_entrada' type='text' size='12' maxlength='10' id='data_entrada' value='<?=$Row['data_entrada']?>'
             onKeyUp="mascara_data(this); pula(10,this.id,data_exame.id)" />
    </td>
	<td class="secao">Data do Exame Admissional:</td>
	<td>
	  <input name='data_exame' type='text' size='12' maxlength='10' id='data_exame' value='<?=$Row['data_exame']?>'
             onkeyup="mascara_data(this); pula(10,this.id,localpagamento.id)" />
    </td>
  </tr>
  <tr>
    <td width="3%" class="secao">Local de Pagamento:</td>
    <td width="77%" colspan="3">
  	  <input name='localpagamento' type='text' id='localpagamento' size='25'  value='<?=$Row['localpagamento']?>'
             onChange="this.value=this.value.toUpperCase(); ValidaBanc();" />
    </td>
  </tr>
  <tr>
    <td class="secao">Tipo de Pagamento:</td>
    <td colspan="3">
      <select name='tipopg' id='tipopg'>
        <?php $result_pg = mysql_query("SELECT * FROM tipopg WHERE id_projeto = '$Row[id_projeto]'");
				while ($row_pg = mysql_fetch_array($result_pg)){
					if($Row['tipo_pagamento'] == $row_pg['0']){
						print "<option value='$row_pg[0]' selected>$row_pg[tipopg]</option>";
					}else{
						print "<option value='$row_pg[0]'>$row_pg[tipopg]</option>";
					}
				} ?>
      </select>
    </td>
  </tr>
  <tr>
    <td class="secao">Cota (R$):</td>
    <td>
      <input name='cota' type='text' id='cota' size='13'  value='<?=$cota?>'
             OnKeyDown="FormataValor(this,event,17,2)" />
    </td>
    <td class="secao">Parcelas:</td>
    <td>
      <input name='parcelas' type='text' id='parcelas' size='3'  value='<?=$Row['parcelas']?>' />
    </td>
  </tr>
  <tr>
    <td class="secao">Observações:</td>
    <td colspan="3">
       <textarea name='observacoes' id='observacoes' cols='55' rows='4'
                 onChange="this.value=this.value.toUpperCase()"><?=$Row['observacao']?></textarea>
    </td>
  
  </tr>
</table>
    
  <!-- Contratação igual a 3 é cooperado -->
  <?php if($tipo_contratacao == 3): ?>
  <table cellpadding="0" cellspacing="1" class="secao">
  <tr>
    <td class="secao">Bater Ponto:</td>
    <td colspan="3">
	<input type="checkbox" name="ponto" id="ponto" value="S" <?php echo (($Row['in_ponto'] == 'S')?'checked="checked"':''); ?>/> Selecione se o cooperado bate ponto.
    </td>  
  </tr>
  </table>
  
  <table cellpadding="0" cellspacing="1" class="secao">
  <tr>
    <td class="secao_pai" colspan="8">Horários</td>
  </tr>
  <tr>
    <td class="secao">Hora Retirada</td>
    <td>
	<input name="hora_retirada" type="text" id="hora_retirada" size="13" maxlength="8" value="<?php echo $Row['hora_retirada'] ?>"
           OnKeyPress="formatar('##:##:##', this)" />
    </td>
    <td class="secao">Hora Almoço:</td>
    <td>
	<input name="hora_almoco" type="text" id="hora_almoco" size="13" maxlength="8" value="<?php echo $Row['hora_almoco'] ?>"
           OnKeyPress="formatar('##:##:##', this)" />
    </td>
    <td class="secao">Hora Retorno</td>
    <td>
	<input name="hora_retorno" type="text" id="hora_retorno" size="13" maxlength="8" value="<?php echo $Row['hora_retorno'] ?>"
           OnKeyPress="formatar('##:##:##', this)" />
    </td>
    <td class="secao">Hora Saída:</td>
    <td>
	<input name="hora_saida" type="text" id="hora_saida" size="13" maxlength="8" value="<?php echo $Row['hora_saida'] ?>"
           OnKeyPress="formatar('##:##:##', this)" />
    </td>
  </tr>
  </table>

  
  <?php endif; ?>
    


<div id="finalizacao"> 
    O Funcionário participa <b>ativamente</b> das Atividades do Projeto?
    <label><input type='radio' id='radio1' name='status' value='1' <?=$ativado1?>/>Sim  </label>
    <label><input type='radio' id='radio2' name='status' value='0' <?=$ativado2?>/>Não </label>
    <br>
    <span color="red">Caso <b>não</b>, coloque a data da desativação:</span>
    <input name='data_desativacao' type='text' id='data_desativacao' size='12' maxlength='10' value='<?=$Row['data_saida']?>' 
           onkeyup="mascara_data(this);" />
</div>

<div id="observacao">NÃO DEIXE DE CONFERIR OS DADOS APÓS A DIGITAÇÃO</div>

<div align="center"><input type="submit" name="Submit" value="ATUALIZAR" class="botao" /></div> 

<input type='hidden' name='cooperado' value='<?=$coop?>'/>
<input type='hidden' name='regiao' value='<?=$regiao?>'/>
<input type='hidden' name='id_cadastro' value='4'/>
<input type='hidden' name='projeto' value='<?=$Row['id_projeto']?>' />
<input type='hidden' name='user' value='<?=$id_user?>' />
<input type='hidden' name='update' value='1' />
</form>

</td>
</tr>
</table>
</div>

<script>
function validaForm(){
	d = document.form1;
	if (d.nome.value == "" ){
		alert("O campo Nome deve ser preenchido!");
		d.nome.focus();
		return false;
	}
	if (d.endereco.value == "" ){
		alert("O campo Endereço deve ser preenchido!");
		d.endereco.focus();
		return false;
	}
	if (d.data_nasci.value == "" ){
		alert("O campo Data de Nascimento deve ser preenchido!");
		d.data_nasci.focus();
		return false;
	}
		if (d.rg.value == "" ){
		alert("O campo RG deve ser preenchido!");
		d.rg.focus();
		return false;
	}
	if (d.cpf.value == "" ){
		alert("O campo CPF deve ser preenchido!");
		d.cpf.focus();
		return false;
	}
	if (d.inss_recolher.value == "" ){
		alert("O campo INSS a Recolher deve ser preenchido!");
		d.inss_recolher.focus();
		return false;
	}
//	if (d.tipoinss.value == "2" && !(d.inss_recolher.value <= 11)){
//		alert("O campo INSS a Recolher não pode passar de 11%!");
//		d.inss_recolher.focus();
//		return false;
//	}
	if (d.localpagamento.value == "" ){
		alert("O campo Local de Pagamento deve ser preenchido!");
		d.localpagamento.focus();
		return false;
	}
	return true;   
}


function ValidaBanc(){
	d = document.form1;
	deposito = "$Row_pg_dep[0]";
	cheque = "$Row_pg_che[0]";
	
	if(document.getElementById("tipopg").value == deposito){
		
	if (document.getElementById("banco").value == 0){
		alert("Selecione um banco!");
		return false;
	}
	
	if (d.agencia.value == "" ){
		alert("O campo Agencia deve ser preenchido!");
		d.agencia.focus();
		return false;
	}
	
	if (d.conta.value == "" ){
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
	$('#data_nasci').datepicker({
		changeMonth: true,
	    changeYear: true
	});
	$('#data_escola').datepicker({
		changeMonth: true,
	    changeYear: true
	});
	$('#c_nascimento').datepicker({
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
<?php 

}else{        
    
     $dataEntrada = $_REQUEST['data_entrada'];
     $ano_entrada = date("Y", strtotime(str_replace("/", "-", $dataEntrada)));
        
        if ($ano_entrada < 2009) {
            
              print "<html>
                     <head>
                     <title>:: Intranet ::</title>
                     </head>
                     <body>
                     <script type='text/javascript'>
                     alert('Digite uma data de entrada Valida');
                     history.back();
                     </script>
                     </body>
                     </html>";
                exit;
        }

// Log - Edição de Cooperado
$qr_colunas = mysql_query("SELECT * FROM autonomo WHERE id_autonomo = '$_POST[cooperado]'");
$coluna = mysql_fetch_assoc($qr_colunas);

$qr_dependentes = mysql_query("SELECT * FROM dependentes WHERE id_bolsista = '$_POST[cooperado]'");
$dependentes = mysql_fetch_assoc($qr_dependentes);

function formata($post_data) {
	  $formatado = implode("-", array_reverse(explode("/", $post_data)));
	  return $formatado;
}

function formata2($post_vazio) {
	  if(empty($post_vazio)) {
		  $formatado = "0";
	  } else {
		  $formatado = $post_vazio;
	  }
	  return $formatado;
}

function formata3($post_virgula) {
	  $formatado = str_replace(",", ".", $post_virgula);
	  return $formatado;
}

$colunas = array($coluna['id_cooperativa'], $coluna['id_curso'], $coluna['locacao'], $coluna['campo3'], $coluna['tipo_contratacao'], $coluna['nome'], $coluna['endereco'], $coluna['bairro'], $coluna['cidade'], $coluna['uf'], $coluna['cep'], $coluna['tel_fixo'], $coluna['tel_cel'], $coluna['tel_rec'], $coluna['data_nasci'], $coluna['naturalidade'], $coluna['nacionalidade'], $coluna['civil'], $coluna['sexo'], $coluna['c_nome'], $coluna['c_cpf'], $coluna['c_nascimento'], $coluna['c_profissao'], $coluna['pai'], $coluna['nacionalidade_pai'], $coluna['mae'], $coluna['nacionalidade_mae'], $coluna['estuda'], $coluna['data_escola'], $coluna['escolaridade'], $coluna['instituicao'], $coluna['curso'], $coluna['num_filhos'], $dependentes['nome1'], $dependentes['data1'], $dependentes['nome2'], $dependentes['data2'], $dependentes['nome3'], $dependentes['data3'], $dependentes['nome4'], $dependentes['data4'], $dependentes['nome5'], $dependentes['data5'], $coluna['cabelos'], $coluna['olhos'], $coluna['peso'], $coluna['altura'], $coluna['etnia'], $coluna['defeito'], $coluna['deficiencia'], $coluna['rg'], $coluna['orgao'], $coluna['uf_rg'], $coluna['data_rg'], $coluna['cpf'], $coluna['reservista'], $coluna['campo1'], $coluna['serie_ctps'], $coluna['uf_ctps'], $coluna['data_ctps'], $coluna['titulo'], $coluna['zona'], $coluna['secao'], $coluna['pis'], $coluna['dada_pis'], $coluna['fgts'], $coluna['e_empresa'], $coluna['e_cnpj'], $coluna['e_endereco'], $coluna['e_bairro'], $coluna['e_cidade'], $coluna['e_estado'], $coluna['e_cep'], $coluna['e_ramo'], $coluna['e_tel'], $coluna['e_ramal'], $coluna['e_fax'], $coluna['e_email'], $coluna['e_tempo'], $coluna['e_profissao'], $coluna['e_cargo'], $coluna['e_dataemissao'], $coluna['e_referencia'], $coluna['e_renda'], $coluna['banco'], $coluna['agencia'], $coluna['conta'], $coluna['tipo_conta'], $coluna['nome_banco'], $coluna['data_entrada'], $coluna['data_exame'], $coluna['localpagamento'], $coluna['tipo_pagamento'], $coluna['observacao']);

$posts = array($_POST['vinculo'], $_POST['atividade'], $_POST['locacao'], $_POST['codigo'], $_POST['contratacao'], $_POST['nome'], $_POST['endereco'], $_POST['bairro'], $_POST['cidade'], $_POST['uf'], $_POST['cep'], $_POST['tel_fixo'], $_POST['tel_cel'], $_POST['tel_rec'], formata($_POST['data_nasci']), $_POST['naturalidade'], $_POST['nacionalidade'], $_POST['civil'], $_POST['sexo'], $_POST['c_nome'], $_POST['c_cpf'], formata($_POST['c_nascimento']), $_POST['c_profissao'], $_POST['pai'], $_POST['nacionalidade_pai'], $_POST['mae'], $_POST['nacionalidade_mae'], $_POST['estuda'], formata($_POST['data_escola']), $_POST['escolaridade'], $_POST['instituicao'], $_POST['curso'], $_POST['filhos'], $_POST['filho_1'], formata($_POST['data_filho_1']), $_POST['filho_2'], formata($_POST['data_filho_2']), $_POST['filho_3'], formata($_POST['data_filho_3']), $_POST['filho_4'], formata($_POST['data_filho_4']), $_POST['filho_5'], formata($_POST['data_filho_5']), $_POST['cabelos'], $_POST['olhos'], $_POST['peso'], $_POST['altura'], $_POST['etnia'], $_POST['defeito'], $_POST['deficiencia'], $_POST['rg'], $_POST['orgao'], $_POST['uf_rg'], formata($_POST['data_rg']), $_POST['cpf'], $_POST['reservista'], $_POST['trabalho'], $_POST['serie_ctps'], $_POST['uf_ctps'], formata($_POST['data_ctps']), $_POST['titulo'], $_POST['zona'], $_POST['secao'], $_POST['pis'], formata($_POST['data_pis']), $_POST['fgts'], $_POST['e_empresa'], $_POST['e_cnpj'], $_POST['e_endereco'], $_POST['e_bairro'], $_POST['e_cidade'], $_POST['e_estado'], $_POST['e_cep'], $_POST['e_ramo'], $_POST['e_tel'], $_POST['e_ramal'], $_POST['e_fax'], $_POST['e_email'], $_POST['e_tempo'], $_POST['e_profissao'], $_POST['e_cargo'], formata($_POST['e_dataemissao']), $_POST['e_referencia'], formata3($_POST['e_renda']), $_POST['banco'], $_POST['agencia'], $_POST['conta'], $_POST['radio_tipo_conta'], $_POST['nome_banco'], formata($_POST['data_entrada']), formata($_POST['data_exame']), $_POST['localpagamento'], $_POST['tipopg'], $_POST['observacoes']);

$campos = array("o vinculo", "o curso", "a unidade", "o código", "o tipo de contratação", "o nome", "o endereço", "o bairro", "a cidade", "o estado", "o CEP", "o telefone fixo", "o telefone celular", "o telefone de recado", "a data de nascimento", "a naturalidade", "a nacionalidade", "o estado civil", "o sexo", "o nome do cônjuge", "o CPF do cônjuge", "a data de nascimento do cônjuge", "a profissão do cônjuge", "o nome do pai", "a nacionalidade do pai", "o nome da mãe", "a nacionalidade da mãe", "o estudo", "o término do estudo", "a escolaridade", "a instituição escolar", "o curso", "o número de filhos", "o nome do 1º filho", "a data de nascimento do 1º filho", "o nome do 2º filho", "a data de nascimento do 2º filho", "o nome do 3º filho", "a data de nascimento do 3º filho", "o nome do 4º filho", "a data de nascimento do 4º filho", "o nome do 5º filho", "a data de nascimento do 5º filho", "a cor do cabelo", "a cor dos olhos", "o peso", "a altura", "a etnia", "a marca", "a deficiência", "o RG", "o órgão do RG", "o estado do RG", "a data do RG", "o CPF", "o certificado de reservista", "a carteira de trabalho", "a série do CTPS", "o estado do CTPS", "a data do CTPS", "o Título de Eleitor", "a zona do Título", "a secão do Título", "o PIS", "a data do PIS", "o FGTS", "o nome da empresa", "o CNPJ da empresa", "o endereço da empresa", "o bairro da empresa", "a cidade da empresa", "o estado da empresa", "a CEP da empresa", "o ramo da empresa", "o telefone da empresa", "o ramal do telefone da empresa", "o fax da empresa", "o email da empresa", "o tempo de serviço na empresa", "a profissão na empresa", "o cargo na empresa", "a data de emissão na empresa", "a referência da empresa", "a renda da empresa", "o banco", "a agência", "a conta", "o tipo de conta", "o nome do banco", "a data de entrada", "a data de exame", "o local de pagamento", "o tipo de pagamento", "as observações");

$n = 0;
$edicao = "";

for ($a=0; $a<=93; $a++) {
	if(($colunas[$a] != $posts[$a]) and (empty($posts[$a]))) {
		$n++;
		$edicao .= " <b>$n)</b> removeu <b>$campos[$a] ($colunas[$a])</b>";
	} elseif(($colunas[$a] != $posts[$a]) and (empty($colunas[$a]))) {
		$n++;
		$edicao .= " <b>$n)</b> inseriu <b>$campos[$a] ($posts[$a])</b>";
	} elseif($colunas[$a] != $posts[$a]) {
		$n++;
		$edicao .= " <b>$n)</b> editou <b>$campos[$a]</b> de <b>$colunas[$a]</b> para <b>$posts[$a]</b>";
	}
}

$qr_funcionario = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$funcionario = mysql_fetch_array($qr_funcionario);
$ip = $_SERVER['REMOTE_ADDR'];
$data = date("d/m/Y H:i");
$cabecalho = "($funcionario[0]) $funcionario[nome] às ".$data."h (ip: $ip)";
$local = "Edição de Cooperado - ($coluna[campo3]) $coluna[nome]";
$local_banco = "Edição de Cooperado";
$acao_banco = "Editou o Cooperado ($coluna[campo3]) $coluna[nome]";

mysql_query("INSERT INTO log (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao) 
VALUES ('$funcionario[0]', '$funcionario[id_regiao]', '$funcionario[tipo_usuario]', '$funcionario[grupo_usuario]', '$local_banco', NOW(), '$ip', '$acao_banco')") or die ("Erro Inesperado<br><br>".mysql_error());

$nome_arquivo = "../log/".$funcionario[0].".txt";

$arquivo = fopen("$nome_arquivo", "a");
fwrite($arquivo, "$cabecalho");
fwrite($arquivo, "\r\n");
fwrite($arquivo, "$local");
fwrite($arquivo, "\r\n");
fwrite($arquivo, "$edicao");
fwrite($arquivo, "\r\n");
fwrite($arquivo, "\r\n");
fwrite($arquivo, "---------------------------------------------------------------");
fwrite($arquivo, "\r\n");
fwrite($arquivo, "\r\n");
fclose($arquivo);
//


//ALTERANDO COOPERADOS

$regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['projeto'];
$id_bolsista = $_REQUEST['cooperado'];

//DADOS CONTRATAÇÃO
$vinculo = $_REQUEST['vinculo'];
$id_curso = $_REQUEST['atividade'];
$tipo_contratacao = $_REQUEST['contratacao'];
$codigo = $_REQUEST['codigo'];
$matricula = $_POST['matricula'];
$n_processo = $_POST['n_processo'];
$contrato_medico = $_POST['contrato_medico'];

//trata unidade
$locacao = explode("//", $_REQUEST['locacao']);
$locacao_nome = $locacao[0];
$locacao_id = $locacao[1];

//DADOS CADASTRAIS
$nome = mysql_real_escape_string(trim(str_replace("'", "",$_REQUEST['nome'])));
$sexo = $_REQUEST['sexo'];
$endereco = mysql_real_escape_string(trim(str_replace("'", "",$_REQUEST['endereco'])));
$bairro = mysql_real_escape_string(trim(str_replace("'", "",$_REQUEST['bairro'])));
$cidade = mysql_real_escape_string(trim(str_replace("'", "",$_REQUEST['cidade'])));
$uf = $_REQUEST['uf'];
$cep = $_REQUEST['cep'];
$tel_fixo = $_REQUEST['tel_fixo'];
$tel_cel = $_REQUEST['tel_cel'];
$tel_rec = $_REQUEST['tel_rec'];

$data_nasci = $_REQUEST['data_nasci'];

$naturalidade = $_REQUEST['naturalidade'];
$nacionalidade = $_REQUEST['nacionalidade'];
$civil = $_REQUEST['civil'];

//DOCUMENTAÇÃO
$rg = $_REQUEST['rg'];
$uf_rg = $_REQUEST['uf_rg'];
$secao = $_REQUEST['secao'];
$data_rg = $_REQUEST['data_rg'];

$cpf = $_REQUEST['cpf'];
$conselho = $_REQUEST['conselho'];
$titulo = $_REQUEST['titulo'];
$zona = $_REQUEST['zona'];
$orgao = $_REQUEST['orgao'];

$inss_recolher = $_REQUEST['inss_recolher'];
$tipoinss = $_REQUEST['tipoinss'];

//DADOS DA FAMILIA E OUTROS
$c_nome = $_REQUEST['c_nome'];
$c_cpf = $_REQUEST['c_cpf'];
$c_nascimento = $_REQUEST['c_nascimento'];
$c_profissao = $_REQUEST['c_profissao'];

$pai = mysql_real_escape_string(trim(str_replace("'", "",$_REQUEST['pai'])));
$mae = mysql_real_escape_string(trim(str_replace("'", "",$_REQUEST['mae'])));
$nacionalidade_pai = $_REQUEST['nacionalidade_pai'];
$nacionalidade_mae = $_REQUEST['nacionalidade_mae'];
$estuda = $_REQUEST['estuda'];

$data_escola = $_REQUEST['data_escola'];

$escolaridade = $_REQUEST['escolaridade'];
$instituicao = $_REQUEST['instituicao'];
$curso = $_REQUEST['curso'];

$data_entrada = $_REQUEST['data_entrada'];

$banco = $_REQUEST['banco'];
$agencia = $_REQUEST['agencia'];
$conta = $_REQUEST['conta'];
$nomebanco = $_REQUEST['nomebanco'];
$tipoDeConta = $_REQUEST['radio_tipo_conta'];

//DADOS PROFISSIONAIS
$e_nome = $_REQUEST['e_nome'];
$e_cnpj = $_REQUEST['e_cnpj'];
$e_endereco = $_REQUEST['e_endereco'];
$e_bairro = $_REQUEST['e_bairro'];
$e_cidade = $_REQUEST['e_cidade'];
$e_estado = $_REQUEST['e_estado'];
$e_cep = $_REQUEST['e_cep'];
$e_ramo = $_REQUEST['e_ramo'];
$e_tel = $_REQUEST['e_telefone'];
$e_ramal = $_REQUEST['e_ramal'];
$e_fax = $_REQUEST['e_fax'];
$e_email = $_REQUEST['e_email'];
$e_tempo = $_REQUEST['e_tempo'];
$e_profissao = $_REQUEST['e_profissao'];
$e_cargo = $_REQUEST['e_cargo'];
$e_dataemissao = $_REQUEST['e_dataemissao'];
$e_referencia = $_REQUEST['e_referencia'];
$e_renda = $_REQUEST['e_renda'];
$e_renda = str_replace(".","",$e_renda);
$e_renda = str_replace(",",".",$e_renda);

//REFERENCIA
$r_nome = $_REQUEST['r_nome'];
$r_endereco = $_REQUEST['r_endereco'];
$r_bairro = $_REQUEST['r_bairro'];
$r_cidade = $_REQUEST['r_cidade'];
$r_estado = $_REQUEST['r_estado'];
$r_cep = $_REQUEST['r_cep'];
$r_email = $_REQUEST['r_email'];
$r_tel = $_REQUEST['r_telefone'];
$r_ramal = $_REQUEST['r_ramal'];
$r_fax = $_REQUEST['r_fax'];


// DADOS FINAIS
$localpagamento = $_REQUEST['localpagamento'];
$apolice = $_REQUEST['apolice'];
$campo1 = $_REQUEST['trabalho'];
$campo2 = $_REQUEST['dependente'];

$cota = $_REQUEST['cota'];
$parcelas = $_REQUEST['parcelas'];
$cota = str_replace(".","",$cota);
$cota = str_replace(",",".",$cota);

$pis = $_REQUEST['pis'];
$fgts = $_REQUEST['fgts'];
$tipopg = $_REQUEST['tipopg'];
$filhos = $_REQUEST['filhos'];
$observacoes = $_REQUEST['observacoes'];

$ponto = $_REQUEST['ponto'] == 'S'?'S':'N';

$medica = $_REQUEST['medica'];

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

 if(empty($_REQUEST['impressos2'])){
  $impressos = "0";
  }else{
  $impressos = $_REQUEST['impressos2'];
 }

$plano_medico = $_REQUEST['plano_medico'];

$serie_ctps = $_REQUEST['serie_ctps'];
$uf_ctps = $_REQUEST['uf_ctps'];

$pis_data = $_REQUEST['data_pis'];


//DADOS DO VALE TRANSPORTE
$tipo_vale = $_REQUEST['tipo_vale'];
$num_cartao = $_REQUEST['num_cartao'];
$num_cartao2 = $_REQUEST['num_cartao2'];

$vale1 = $_REQUEST['vale1'];
$vale2 = $_REQUEST['vale2'];
$vale3 = $_REQUEST['vale3'];
$vale4 = $_REQUEST['vale4'];
$vale5 = $_REQUEST['vale5'];
$vale6 = $_REQUEST['vale6'];

//DADOS ADICIONAIS
$ad_noturno = $_REQUEST['ad_noturno'];

$exame_data = $_REQUEST['data_exame'];

$data_ctps = $_REQUEST['data_ctps'];

$reservista = $_REQUEST['reservista'];
$etnia = $_REQUEST['etnia'];
$cabelos = $_REQUEST['cabelos'];
$peso = $_REQUEST['peso'];
$altura = $_REQUEST['altura'];
$olhos = $_REQUEST['olhos'];
$defeito = $_REQUEST['defeito'];
$cipa = $_REQUEST['cipa'];


$filho_1 = mysql_real_escape_string(trim(str_replace("'", "",$_REQUEST['filho_1'])));
$filho_2 = mysql_real_escape_string(trim(str_replace("'", "",$_REQUEST['filho_2'])));
$filho_3 = mysql_real_escape_string(trim(str_replace("'", "",$_REQUEST['filho_3'])));
$filho_4 = mysql_real_escape_string(trim(str_replace("'", "",$_REQUEST['filho_4'])));
$filho_5 = mysql_real_escape_string(trim(str_replace("'", "",$_REQUEST['filho_5'])));

$data_filho_1 = $_REQUEST['data_filho_1'];
$data_filho_2 = $_REQUEST['data_filho_2'];
$data_filho_3 = $_REQUEST['data_filho_3'];
$data_filho_4 = $_REQUEST['data_filho_4'];
$data_filho_5 = $_REQUEST['data_filho_5'];

$status = $_REQUEST['status'];
$data_desativacao = $_REQUEST['data_desativacao'];

//Horário
$hora_retirada = $_REQUEST['hora_retirada'];
$hora_almoco = $_REQUEST['hora_almoco'];
$hora_retorno = $_REQUEST['hora_retorno'];
$hora_saida = $_REQUEST['hora_saida'];

$email = $_POST['email'];
$tipo_sanguineo = $_REQUEST['tiposanguineo'];

//Inicio Verificador CPF
$qrCpf = mysql_query("SELECT COUNT(id_autonomo) AS total FROM autonomo WHERE cpf = '$cpf' AND id_projeto = '$id_projeto' AND tipo_contratacao = '$tipo_contratacao'");
$rsCpf = mysql_fetch_assoc($qrCpf);
$totalCpf = $rsCpf['total'];
$queryCPF = mysql_query("SELECT id_autonomo FROM autonomo WHERE cpf = '$cpf' AND id_projeto = '$id_projeto' AND tipo_contratacao = '$tipo_contratacao'");
$resultCPF = mysql_fetch_assoc($queryCPF);
$idAutonomoCPF = $resultCPF['id_autonomo'];
if($totalCpf > 0 && $idAutonomoCPF != $id_bolsista){ ?>
<script type="text/javascript">
        alert("Esse CPF já existe para esse projeto");
        window.history.back();
</script>

<?php exit(); }
//Fim verificador CPF

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
 return "Data invalida";
 }
}

$data_filho_1 = ConverteData($data_filho_1);
$data_filho_2 = ConverteData($data_filho_2);
$data_filho_3 = ConverteData($data_filho_3);
$data_filho_4 = ConverteData($data_filho_4);
$data_filho_5 = ConverteData($data_filho_5);
$data_nasci   = ConverteData($data_nasci);
$data_rg      = ConverteData($data_rg);
$data_escola  = ConverteData($data_escola);
$data_entrada = ConverteData($data_entrada);
$data_desativacao = ConverteData($data_desativacao);
$pis_data     = ConverteData($pis_data);
$exame_data   = ConverteData($exame_data);
$data_ctps = ConverteData($data_ctps);
$c_nascimento = ConverteData($c_nascimento);
$e_dataemissao = ConverteData($e_dataemissao);

$data_alter = date('Y-m-d');

mysql_query ("UPDATE autonomo SET localpagamento = '$localpagamento', locacao = '$locacao_nome', id_unidade = '$locacao_id', nome = '$nome', sexo = '$sexo', endereco = '$endereco', 
bairro = '$bairro', cidade = '$cidade', uf = '$uf', cep = '$cep', tel_fixo = '$tel_fixo', tel_cel = '$tel_cel', tel_rec = '$tel_rec', 
data_nasci = '$data_nasci', naturalidade = '$naturalidade', nacionalidade = '$nacionalidade', civil = '$civil', rg = '$rg', orgao = '$orgao', 
data_rg = '$data_rg', cpf = '$cpf', conselho = '$conselho', titulo = '$titulo', zona = '$zona', secao = '$secao', inss = '$inss_recolher',tipo_inss = '$tipoinss', pai = '$pai', nacionalidade_pai = '$nacionalidade_pai', 
mae = '$mae', nacionalidade_mae = '$nacionalidade_mae', estuda = '$estuda', data_escola = '$data_escola', escolaridade = '$escolaridade', 
instituicao = '$instituicao', curso = '$curso', banco = '$banco', agencia ='$agencia', conta = '$conta',tipo_conta = '$tipoDeConta', 
status = '$status', data_saida = '$data_desativacao', campo3 = '$codigo', tipo_contratacao = '$tipo_contratacao', id_curso = '$id_curso', 
apolice = '$apolice', data_entrada = '$data_entrada', campo2 = '$dependente', campo1 = '$campo1', data_exame = '$exame_data', 
reservista = '$reservista', etnia = '$etnia', cabelos = '$cabelos', peso = '$peso', altura = '$altura', olhos = '$olhos', defeito = '$defeito', cipa = '$cipa', 
ad_noturno = '$ad_noturno', plano = '$plano', assinatura = '$assinatura', distrato = '$assinatura2', outros = '$assinatura3', pis = '$pis', 
dada_pis = '$pis_data', data_ctps = '$data_ctps', serie_ctps = '$serie_ctps', uf_ctps = '$uf_ctps', uf_rg = '$uf_rg', fgts = '$fgts', 
insalubridade = '$insalubridade', transporte = '$transporte', medica = '$medica', tipo_pagamento = '$tipopg', nome_banco = '$nomebanco', 
num_filhos = '$filhos', observacao = '$observacoes', foto = '$foto_banco', id_cooperativa = '$vinculo', 
c_nome = '$c_nome', c_cpf = '$c_cpf', c_nascimento = '$c_nascimento', c_profissao = '$c_profissao', e_empresa = '$e_nome', e_cnpj = '$e_cnpj', 
e_ramo = '$e_ramo', e_endereco = '$e_endereco', e_bairro = '$e_bairro', e_cidade = '$e_cidade', e_estado = '$e_estado', e_cep = '$e_cep', 
e_tel = '$e_tel', e_ramal = '$e_ramal', e_fax = '$e_fax', e_email = '$e_email', e_tempo = '$e_tempo', e_profissao = '$e_profissao', 
e_cargo = '$e_cargo', e_renda = '$e_renda', e_dataemissao = '$e_dataemissao', e_referencia = '$e_referencia', r_nome = '$r_nome', 
r_endereco = '$r_endereco', r_bairro = '$r_bairro', r_cidade = '$r_cidade', r_estado = '$r_estado', r_cep = '$r_cep', r_tel = '$r_tel', 
r_ramal = '$r_ramal', r_fax = '$r_fax', r_email = '$r_email', dataalter = '$data_alter', useralter = '$id_user', cota = '$cota', 
parcelas = '$parcelas', matricula = '$matricula', n_processo = '$n_processo', contrato_medico = '$contrato_medico', email = '$email', 
tipo_sanguineo = '$tipo_sanguineo', in_ponto = '$ponto', hora_retirada = '$hora_retirada', hora_almoco = '$hora_almoco', 
hora_retorno = '$hora_retorno', hora_saida = '$hora_saida'
WHERE id_autonomo = '$id_bolsista' LIMIT 1") or die ("Erro no UPDATE:<br><br><font color=red> ".mysql_error());

/*
//VALE TRANSPORTE
if($transporte == "1"){
mysql_query ("insert into rh_vale(id_clt,id_regiao,id_projeto,id_tarifa1,id_tarifa2,id_tarifa3,id_tarifa4,
id_tarifa5,id_tarifa6,cartao1,cartao2) values 
('$row_id_participante','$regiao','$projeto','$vale1','$vale2','$vale3','$vale4','$vale5','$vale6','$num_cartao','$num_cartao2')") or die ("$mensagem_erro - 2.3<br><br>".mysql_error());
}
*/

//DEPENDENTES
//VERIFICA SE O BOLSISTA JA ESTÁ CADASTRADO NA TABELA DEPENDENTES
$result_cont1 = mysql_query ("SELECT id_bolsista FROM dependentes WHERE id_bolsista = '$id_bolsista' and id_projeto = '$id_projeto'");
$row_cont1 = mysql_num_rows($result_cont1);

if($row_cont1 == "0"){

mysql_query ("INSERT INTO dependentes(id_regiao,id_projeto,id_bolsista,contratacao,nome,data1,nome1,data2,nome2,data3,nome3,data4,nome4,data5,nome5) values 
('$regiao','$id_projeto','$id_bolsista','$tipo_contratacao','$nome','$data_filho_1','$filho_1','$data_filho_2','$filho_2','$data_filho_3','$filho_3',
'$data_filho_4','$filho_4','$data_filho_5','$filho_5')") or die ("<center>O SERVIDOR NÃO RESPONDEU CONFORME DEVERIA...<br>". mysql_error());

}else{

mysql_query ("update dependentes set data1 = '$data_filho_1', nome1 = '$filho_1', data2 = '$data_filho_2', nome2 = '$filho_2', data3 = '$data_filho_3', nome3 = '$filho_3', data4 = '$data_filho_4', nome4 = '$filho_4', data5 = '$data_filho_5', nome5 = '$filho_5' WHERE id_projeto = '$id_projeto' and id_bolsista = '$id_bolsista' ") or die ("houve algum erro de digitação na terceira query (update de dependentes): ". mysql_error());

}



//FAZENDO O UPLOAD DA FOTO
$arquivo = isset($_FILES['arquivo']) ? $_FILES['arquivo'] : FALSE;

if($foto_up == "1"){
if(!$arquivo){
    $mensagem = "Não acesse esse arquivo diretamente!";
}else{// Imagem foi enviada, então a move para o diretório desejado
    $nome_arq = str_replace(" ", "_", $nome);	
    $tipo_arquivo = ".gif";
	// Resolvendo o nome e para onde o arquivo será movido
    $diretorio = "../fotos/";
	$nome_tmp = $regiao."_".$id_projeto."_".$id_bolsista.$tipo_arquivo;
	$nome_arquivo = "$diretorio$nome_tmp" ;
	
	move_uploaded_file($arquivo['tmp_name'], $nome_arquivo ) or die ("Erro ao enviar o Arquivo: $nome_arquivo");

}

}

header("Location: ../bolsista.php?projeto=$id_projeto&regiao=$regiao&sucesso=edicao");
exit;

}
?>