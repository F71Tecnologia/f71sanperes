<?php
if(empty($_COOKIE['logado'])) {
print "Efetue o Login<br><a href='../login.php'>Logar</a>";
} else {

include "../conn.php";

if(empty($_REQUEST['update'])) {

$id_clt = $_REQUEST['clt'];
$id_projeto = $_REQUEST['pro'];

$result = mysql_query("SELECT *, date_format(data_nasci, '%d/%m/%Y')as data_nascimento, date_format(data_rg, '%d/%m/%Y')as data_rg2, date_format(data_escola, '%d/%m/%Y')as data_escola2, date_format(data_entrada, '%d/%m/%Y')as data_entrada2, date_format(data_exame, '%d/%m/%Y')as data_exame, date_format(data_saida, '%d/%m/%Y')as data_saida, date_format(data_ctps, '%d/%m/%Y')as data_ctps2, date_format(dada_pis, '%d/%m/%Y')as dada_pis2 FROM rh_clt WHERE id_clt = $id_clt");
$row = mysql_fetch_array($result);

if($row['transporte'] == "1") {  // SE TIVER MARCADO O VALE, ELE BUSCA NA TABELA VALE
     $result_vale = mysql_query("SELECT * FROM rh_vale WHERE id_clt = '$id_clt'");
     $row_vale = mysql_fetch_array($result_vale);
} else { // CASO NÃO TENHA VALE, ELE VERIFICA SE O ID DO CLT ESTÁ NA TABELA VALE E ZERA OS DADOS
     $result_vale = mysql_query("SELECT * FROM rh_vale WHERE id_clt = '$id_clt'");
     $row_vale_num = mysql_num_rows($result_vale);
 
     if($row_vale_num != "0") {
	       mysql_query("UPDATE rh_vale set id_tarifa1 = '0',id_tarifa2 = '0',id_tarifa3 = '0',id_tarifa4 = '0',
	                    id_tarifa5 = '0',id_tarifa6 = '0',qnt1 = '',qnt2 = '',qnt3 = '',
	                    qnt4 = '',qnt5 = '',qnt6 = '',cartao1 = '',cartao2 = '' WHERE id_projeto = '$id_projeto' 
	                    AND id_clt = '$id_clt' ") or die ("Erro de digitação ZERANDO OS DADOS DO VALE: ". mysql_error());
     }
}

if($row['id_antigo'] == "0") { // SE ID_ANTIGO FOR 0 .. VOU USAR O ID_CLT PARA PEGAR OS DEPENDENTES
	  $usarei = "$row[0]"; 
} else {
	  $usarei = "$row[1]";
}

$result_depe = mysql_query("SELECT *, date_format(data1, '%d/%m/%Y')as datas1, date_format(data2, '%d/%m/%Y')as datas2, date_format(data3, '%d/%m/%Y')as datas3, date_format(data4, '%d/%m/%Y')as datas4, date_format(data5, '%d/%m/%Y')as datas5 FROM dependentes WHERE id_bolsista = '$usarei' AND id_projeto = '$id_projeto' AND contratacao = '$row[tipo_contratacao]'");
$row_depe = mysql_fetch_array($result_depe);

$result_pro = mysql_query("SELECT * FROM projeto WHERE id_projeto = $id_projeto");
$row_pro = mysql_fetch_array($result_pro);

$result_reg = mysql_query("SELECT * FROM regioes WHERE id_regiao = $row[id_regiao]");
$row_reg = mysql_fetch_array($result_reg);

if($row['insalubridade'] == "1") {
    $chek1 = "checked";
} else {
    $chek1 = NULL;
}

if($row['transporte'] == "1") {
    $chek2 = "checked";
    $disable_vale = "style='display:'";
} else {
    $chek2 = NULL;
    $disable_vale = "style='display:none'";
}

if($row['vr'] == "1") {
    $chek3 = "checked";
    $disable_vr = "style='display:'";
} else {
    $chek3 = NULL;
    $disable_vr = "style='display:none'";
}

if($row['assinatura'] == "1") {
    $selected_ass_sim = "checked";
    $selected_ass_nao = NULL;
} elseif($row['assinatura'] == "0") {
	$selected_ass_sim = NULL;
	$selected_ass_nao = "checked";
} else {
	$selected_ass_sim = NULL;
	$selected_ass_nao = NULL;
	$mensagem_ass = "<font color=red size=1><b>Não marcado</b></font>";
}

if($row['distrato'] == "1") {
	$selected_ass_sim2 = "checked";
	$selected_ass_nao2 = NULL;
} elseif($row['distrato'] == "0") {
	$selected_ass_sim2 = NULL;
	$selected_ass_nao2 = "checked";
}

if($row['outros'] == "1") {
	$selected_ass_sim3 = "checked";
	$selected_ass_nao3 = NULL;
} elseif($row['outros'] == "0") {
	$selected_ass_sim3 = NULL;
	$selected_ass_nao3 = "checked";
}

if($row['sexo'] == "M") {
	$chekH = "checked";
	$chekF = NULL;
	$mensagem_sexo = NULL;
} elseif($row['sexo'] == "F") {
	$chekH = NULL;
	$chekF = "checked";
	$mensagem_sexo = NULL;
} else {
	$chekH = NULL;
	$chekF = NULL;
	$mensagem_sexo = "<font color=red size=1><b>Cadastrar Sexo</b></font>";
}

if($row['medica'] == "0") {
	$chek_medi0 = "checked";
	$chek_medi1 = NULL;

	$mensagem_medi = NULL;
} elseif($row['medica'] == "1") {
	$chek_medi0 = NULL;
	$chek_medi1 = "checked";
	$mensagem_medi = NULL;
} else {
	$chek_medi0 = NULL;
	$chek_medi1 = NULL;
	$mensagem_medi = "<font color=red size=1><b>Selecione uma opção</b></font>";
}

if($row['plano'] == "1") {
	$selected_planoF = "selected";
	$selected_planoI = NULL;
} else {
	$selected_planoF = NULL;
	$selected_planoI = "selected";
}

if($row_vale['tipo_vale'] == "1") {
	$selected_valeC = "selected";
	$selected_valeP = NULL;
	$selected_valeA = NULL;
} elseif($row_vale['tipo_vale'] == "2") {
	$selected_valeC = NULL;
	$selected_valeP = "selected";
	$selected_valeA = NULL;
} elseif($row_vale['tipo_vale'] == "3") {
	$selected_valeC = NULL;
	$selected_valeP = NULL;
	$selected_valeA = "selected";
}

if($row['ad_noturno'] == "1") {
	$checkad_noturno1 = "checked";
	$checkad_noturno0 = NULL;
} else {
	$checkad_noturno1 = NULL;
	$checkad_noturno0 = "checked";
}

if($row['estuda'] == "sim") {
	$chekS = "checked";
	$chekN = NULL;
} else {
	$chekS = NULL;
	$chekN = "checked";
}

if($row['cipa'] == "1") {
	$checkedcipa1 = "checked";
	$checkedcipa0 = NULL;
} else {
	$checkedcipa1 = NULL;
	$checkedcipa0 = "checked";
}

if($row['status'] == "10" or $row['status'] == "1") {
	$AVISO = NULL;
	$status_ativado = "checked";
	$status_desativado = NULL;
	$data_desativacao = NULL;
} else {
	$AVISO = "Este Funcionário Encontra-se DESATIVADO";
	$status_ativado = NULL;
	$status_desativado = "checked";
	$data_desativacao = "$row[data_saida]";
}

$pagina = $_REQUEST['pagina'];

if($row['foto'] == "1") {
	$foto = "Deseja remover a foto? <input name='foto' type='checkbox' id='foto' value='3'/> Sim";
} else {
	$foto = "<input class='reset' name='foto' type='checkbox' id='foto' value='1' onClick=\"document.getElementById('tablearquivo').style.display = (document.getElementById('tablearquivo').style.display == 'none') ? '' : 'none' ;\">";
}

$RE_pg_dep = mysql_query("SELECT id_tipopg FROM tipopg WHERE id_projeto = '$id_projeto' AND campo1 = '1'");
$Row_pg_dep = mysql_fetch_array($RE_pg_dep);

$RE_pg_che = mysql_query("SELECT id_tipopg FROM tipopg WHERE id_projeto = '$id_projeto' AND campo1 = '2'");
$Row_pg_che = mysql_fetch_array($RE_pg_che);

// Log 

$qr_funcionario = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$funcionario = mysql_fetch_array($qr_funcionario);
$ip = $_SERVER['REMOTE_ADDR'];
$local_banco = "Edição de CLT";
$acao_banco = "Editando o CLT ($row[campo3]) $row[nome]";

mysql_query("INSERT INTO log (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao) 
VALUES ('$funcionario[0]', '$funcionario[id_regiao]', '$funcionario[tipo_usuario]', '$funcionario[grupo_usuario]', '$local_banco', NOW(), '$ip', '$acao_banco')") or die ("Erro Inesperado<br><br>".mysql_error());

// Fim do Log

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="shortcut icon" href="favicon.ico">
<link href="css/estrutura_cadastro.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src='../js/ramon.js'></script>
</head>
<body>
<div id="corpo">
<table align="center" width="100%" cellspacing="0" cellpadding="12" style="font-size:13px; line-height:22px;">
  <tr>
    <td>
  <div style="border-bottom:2px solid #F3F3F3; margin-top:10px;">
       <h2 style="float:left; font-size:18px;">EDITAR CADASTRO <span class="clt">CLT</span></h2>
       <p style="float:right;"><a href='ver_clt.php?reg=<?=$row['id_regiao']?>&clt=<?=$row[0]?>&ant=<?=$row[1]?>&pro=<?=$id_projeto?>&pagina=<?=$pagina?>'> &laquo; Voltar</a></p>
       <div class="clear"></div>
  </div>
  <p>&nbsp;</p>
<form action="alter_clt.php" method="post" id="form1" name="form1" onSubmit="return validaForm()" enctype="multipart/form-data">

<table cellpadding="0" cellspacing="1" class="secao">
  <tr>
     <td class="secao_pai" colspan="2" style="border-top:1px solid #777;">DADOS DO PROJETO</td>
  </tr>
  <tr>
    <td width="25%" class="secao">Código:</td>
    <td width="75%">
    <input name="codigo" type="text" class="campotexto" id="codigo" size="3" value="<?=$row['campo3']?>" />
    </td>
  </tr>
  <tr style="display:none;">
    <td class="secao">Tipo de Contratação:</td>
    <td>
        <select name='tipo_bol' id='tipo_bol'>
             <option value='2' selected>CLT</option>     
        </select>
    </td>
  </tr>
  <tr>
    <td class="secao">Projeto:</td>
    <td><?=$row_pro[2]?></td>
  </tr>
  <tr>
    <td class="secao">Curso:</td>
    <td>
      <select name='id_curso' id='id_curso' onChange="location.href=this.value;">
         <?php if(empty($_REQUEST['idcursos'])) {
          			$id_curso = $row['id_curso'];
      	       } else {
          			$id_curso = $_REQUEST['idcursos'];
               }

      	$result_curso = mysql_query("SELECT * FROM curso WHERE id_regiao = $row[id_regiao] AND campo3 = $row[id_projeto] AND tipo = '2' ORDER BY nome");
      	while($row_curso = mysql_fetch_array($result_curso)){
	
      	if($row_curso['id_curso'] == $id_curso) {
         	print "<option value='$row_curso[id_curso]' selected>$row_curso[0] - $row_curso[campo2] / $row_curso[salario] - $row_curso[campo1]</option>";   
        } else { 
            print "<option value='alter_clt.php?clt=$id_clt&pro=$row[id_projeto]&pagina=$pagina&idcursos=$row_curso[0]'>$row_curso[0] - $row_curso[campo2] / $row_curso[salario] - $row_curso[campo1]</option>";
        }
		
      } ?>
    </select>
    </td>
  </tr>
  <tr>
    <td class="secao">Hor&aacute;rio:</td>
    <td>
      <select name='horario' id='horario'>
	 <?php $result_horarios = mysql_query("SELECT * FROM rh_horarios WHERE funcao = '$id_curso' AND id_regiao = '$row[id_regiao]'");
               while ($row_horarios = mysql_fetch_array($result_horarios)) {
	                 if($row_horarios['0'] == "$row[rh_horario]") {
		                   print "<option value='$row_horarios[0]' selected>$row_horarios[nome] ( $row_horarios[entrada_1] - $row_horarios[saida_1] - $row_horarios[entrada_2] - $row_horarios[saida_2] )</option>";
	                 } else {
		                   print "<option value='$row_horarios[0]'>$row_horarios[nome] ( $row_horarios[entrada_1] - $row_horarios[saida_1] - $row_horarios[entrada_2] - $row_horarios[saida_2] )</option>";
	                 }     
               } ?> 
     </select>
   </td>
 </tr>
  <tr>
    <td class="secao">Unidade:</td>
    <td>
        <select name='lotacao' id='lotacao'>
        <?php $result_unidade = mysql_query("SELECT * FROM unidade WHERE id_regiao = $row[id_regiao] AND campo1 = $row[id_projeto] ORDER BY unidade");
                   while($row_unidade = mysql_fetch_array($result_unidade)) {
                         if($row_unidade['unidade'] == "$row[locacao]"){
                                print "<option selected>$row_unidade[unidade]</option>";   
                         } else {
                                print "<option>$row_unidade[unidade]</option>";
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
      <input name='nome' type='text' id='nome' size='75' value='<?=$row['nome']?>'
               onChange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">Data de Nascimento:</td>
    <td>
      <input name='data_nasc' type='text' id='data_nasc' size='15' value='<?=$row['data_nascimento']?>'
    	       onKeyUp="mascara_data(this); pula(10,this.id,civil.id)">
    </td>
  </tr>
  <tr>
    <td class="secao">Estado Civil:</td>
    <td>
	   <input name='civil' type='text' value='<?=$row['civil']?>' id='civil' size='16'
              onChange="this.value=this.value.toUpperCase()">
	</td>
    <td class="secao">Sexo:</td>
    <td>
      <label><input name='sexo' type='radio' class="reset" id='sexo' value='M' <?=$chekH?>> Masculino</label><br>
      <label><input name='sexo' type='radio' class="reset" id='sexo' value='F' <?=$chekF?>> Feminino</label>
    </td>
    <td class="secao">Nacionalidade:</td>
    <td><input name='nacionalidade' type='text' id='nacionalidade' size='15' value='<?=$row['nacionalidade']?>'
           onChange="this.value=this.value.toUpperCase()"/></td>
  </tr>
  <tr>
    <td class="secao">Endereço:</td>
    <td><input name='endereco' type='text' id='endereco' size='35' value='<?=$row['endereco']?>'
               onChange="this.value=this.value.toUpperCase()"/>
     </td>
    <td class="secao">Bairro:</td>
    <td><input name='bairro' type='text' id='bairro' size='16' value='<?=$row['bairro']?>'  
               onChange="this.value=this.value.toUpperCase()"/></td>
    <td class="secao">UF:</td>
     <td><input name='uf' type='text' id='uf' size='2' maxlength='2' value='<?=$row['uf']?>'
        	    onChange="this.value=this.value.toUpperCase()"
        	    onkeyup="pula(2,this.id,naturalidade.id)" /></td>
  </tr>
  <tr>
    <td class="secao">Cidade:</td>
    <td><input name='cidade' type='text' id='cidade' size='35' value='<?=$row['cidade']?>'
                           onChange="this.value=this.value.toUpperCase()"/></td>
    <td class="secao">CEP:</td>
    <td><input name='cep' type='text' id='cep' size='16' maxlength='9' value='<?=$row['cep']?>'
               style='text-transform:uppercase;' 	   
               OnKeyPress="formatar('#####-###', this)" 
               onKeyUp="pula(9,this.id,uf.id)" /></td>
    <td class="secao">Naturalidade:</td>
    <td><input name='naturalidade' type='text' id='naturalidade' size='15' value='<?=$row['naturalidade']?>'
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
    <td><input name='curso' type='text' id='zona' size='16' value='<?=$row['curso']?>'
       		   onChange="this.value=this.value.toUpperCase()"/></td>
    <td class="secao">Institui&ccedil;&atilde;o:</td>
    <td><input name='instituicao' type='text' id='instituicao' size='15' value='<?=$row['instituicao']?>'
        	   onChange="this.value=this.value.toUpperCase()"/></td>
  </tr>
  <tr>
    <td class="secao">Telefone Fixo:</td>
    <td><input name='tel_fixo' type='text' id='tel_fixo' size='16' value='<?=$row['tel_fixo']?>'
             onKeyPress="return(TelefoneFormat(this,event))" 
             onKeyUp="pula(13,this.id,tel_cel.id)"></td>
    <td class="secao">Celular:</td>
    <td><input name='tel_cel' type='text' id='tel_cel' size='16' value='<?=$row['tel_cel']?>'  
		   onKeyPress="return(TelefoneFormat(this,event))" 
		   onKeyUp="pula(13,this.id,tel_rec.id)" /></td>
    <td class="secao">Recado:</td>
    <td>
      <input name='tel_rec' type='text' id='tel_rec' size='15' value='<?=$row['tel_rec']?>' 
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
        <input name='pai' type='text' id='pai' size='45' value='<?=$row['pai']?>'
        	   onChange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">
   Nacionalidade Pai:
    </td>
    <td>
        <input name='nacionalidade_pai' type='text' id='nacionalidade_pai' size='15' value='<?=$row['nacionalidade_pai']?>'
               onChange="this.value=this.value.toUpperCase()"/>	
	 </td>
  </tr>
  <tr>
    <td class="secao">Filiação - Mãe:</td>
    <td>
        <input name='mae' type='text' id='mae' size='45' value='<?=$row['mae']?>'
               onChange="this.value=this.value.toUpperCase()"/>
     </td>
    <td class="secao">
   Nacionalidade Mãe:
    </td>
    <td>
        <input name='nacionalidade_mae' type='text' id='nacionalidade_mae' size='15' value='<?=$row['nacionalidade_mae']?>'
               onChange="this.value=this.value.toUpperCase()"/>	
	 </td>
  </tr>
  <tr>
    <td class="secao">Número de Filhos:</td>
    <td colspan="3">
    <input name='filhos' type='text' id='filhos' size='2' value='<?=$row['num_filhos']?>' />
    </td>
  </tr>
  <tr>
    <td class="secao">Nome:</td>
    <td>
       <input name='filho_1' type='text' id='filho_1' size='50' value='<?=$row_depe['nome1']?>'
              onChange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">Nascimento:</td>
    <td>
    <input name='data_filho_1' type='text' size='12' maxlength='10' id='data_filho_1' value='<?=$row_depe['datas1']?>'
		   onKeyUp="mascara_data(this); pula(10,this.id,filho_2.id)"
           onChange="this.value=this.value.toUpperCase()"/>
    </td>
  </tr>
  <tr>
    <td class="secao">Nome:</td>
    <td>
      <input name='filho_2' type='text' id='filho_2' size='50' value='<?=$row_depe['nome2']?>'
             onChange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">Nascimento:</td>
    <td>
    <input name='data_filho_2' type='text' size='12' maxlength='10' id='data_filho_2' value='<?=$row_depe['datas2']?>'	
		   onKeyUp="mascara_data(this); pula(10,this.id,filho_3.id)"        
           onChange="this.value=this.value.toUpperCase()"/>
    </td>
  </tr>
  <tr>
    <td class="secao">Nome:</td>
    <td>
      <input name='filho_3' type='text' id='filho_3' size='50' value='<?=$row_depe['nome3']?>'
             onChange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">Nascimento:</td>
    <td>
    <input name='data_filho_3' type='text' size='12' maxlength='10' id='data_filho_3' value='<?=$row_depe['datas3']?>' 
		   onKeyUp="mascara_data(this); pula(10,this.id,filho_4.id)"
           onChange="this.value=this.value.toUpperCase()"/>
    </td>
  </tr>
  <tr>
    <td class="secao">Nome:</td>
    <td>
      <input name='filho_4' type='text' id='filho_4' size='50' value='<?=$row_depe['nome4']?>'
             onChange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">Nascimento:</td>
    <td>
    <input name='data_filho_4' type='text' size='12' maxlength='10' id='data_filho_4' value='<?=$row_depe['datas4']?>'    
		   onKeyUp="mascara_data(this); pula(10,this.id,filho_5.id)"
           onChange="this.value=this.value.toUpperCase()"/>
    </td>
  </tr>
  <tr>
    <td class="secao">Nome:</td>
    <td>
      <input name='filho_5' type='text' id='filho_5' size='50' value='<?=$row_depe['nome5']?>'
             onChange="this.value=this.value.toUpperCase()"/>
   </td>
    <td class="secao">Nascimento:</td>
    <td>
    <input name='data_filho_5' type='text' size='12' maxlength='10' id='data_filho_5' value='<?=$row_depe['datas5']?>'
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
    <td class="secao">
	Cabelos:
    </td>
    <td>
	<select name='cabelos' id='cabelos'>
 
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
    <select name='olhos' id='olhos'>
	  
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
      <input name='peso' type='text' id='peso' size='5' value='<?=$row['peso']?>' />
    </td>
  </tr>
  <tr>
    <td class="secao">Altura:</td>
    <td>
      <input name='altura' type='text' id='altura' size='5' value='<?=$row['altura']?>' />
    </td>
    <td class="secao">Etnia:</td>
    <td>

          <select name='etnia'>
                 <option value="6">09 - Não informado</option>
    <?php $qr_etnias = mysql_query("SELECT * FROM etnias WHERE status = 'on' LIMIT 0,5");
          while($etnia = mysql_fetch_assoc($qr_etnias)) { ?>
                 <option value="<?=$etnia['id']?>"<? if($row['etnia'] == $etnia['id']) { ?> selected="selected"<? } ?>><?=$etnia['cod']?> - <?=$etnia['nome']?></option>
    <?php } ?>
           </select>
           
    <td class="secao">Marcas ou Cicatriz:</td>
    <td>
      <input name='defeito' type='text' id='defeito' size='18' value='<?=$row['defeito']?>'
             onChange="this.value=this.value.toUpperCase()"/>
    </td>
  </tr>
  <tr>
  <td class="secao">Deficiência:</td>
  <td colspan="6">
     <select name='deficiencia'>
         <option value="">Não é portador de deficiência</option>
         <?php $qr_deficiencias = mysql_query("SELECT * FROM deficiencias WHERE status = 'on'");
               while($deficiencia = mysql_fetch_assoc($qr_deficiencias)) { ?>
         <option value="<?=$deficiencia['id']?>"<? if($row['deficiencia'] == $deficiencia['id']) { ?> selected="selected"<? } ?>><?=$deficiencia['nome']?></option>
         <?php } ?>
     </select>    
  </tr>
  <tr id='ancora_foto'>
    <td class="secao">	
       Foto:
    </td>
    <td colspan="5"><?=$foto?>
        <div id="tablearquivo" style="display:none;">ENVIAR FOTO: <input name='arquivo' type='file' id='arquivo' size='60' /></div>
    </td>
   </tr>
</table>

<table cellpadding="0" cellspacing="1" class="secao">
  <tr>
     <td class="secao_pai" colspan="8">DOCUMENTAÇÃO</td>
  </tr>
  <tr>
    <td width='16%' class="secao">
	Nº do RG:</td>
    <td width='12%'>
	<input name='rg' type='text' id='rg' size='13' maxlength='14' value='<?=$row['rg']?>'
           OnKeyPress="formatar('##.###.###-###', this)" 
		   onkeyup="pula(14,this.id,orgao.id)">
    </td>
    <td width='15%' class="secao">Orgão Expedidor:</td>
    <td width='9%'>
        <input name='orgao' type='text' id='orgao' size='8' value='<?=$row['orgao']?>'
               onChange="this.value=this.value.toUpperCase()"/>
    </td>
    <td width='5%' class="secao">UF:</td>
    <td width='7%'>
    <input name='uf_rg' type='text' id='uf_rg' size='5' value='<?=$row['uf_rg']?>'
           onChange="this.value=this.value.toUpperCase()"/>
    </td>
    <td width='18%' class="secao">Data Expedição:</td>
    <td width='18%'>
      <input name='data_rg' type='text' size='12' maxlength='10' value='<?=$row['data_rg2']?>' id='data_rg' 
		     onkeyup="mascara_data(this); pula(10,this.id,cpf.id)" />
   </td>
  </tr>
  <tr>
    <td class="secao">CPF:</td>
    <td colspan='5'>
        <input name='cpf' type='text' id='cpf' size='17' maxlength='14' value='<?=$row['cpf']?>'
               OnKeyPress="formatar('###.###.###-##', this)" 
			   onkeyup="pula(14,this.id,reservista.id)"/>
     </td>
    <td class="secao">Certificado de Reservista:</td>
    <td>
      <input name='reservista' type='text' id='reservista' size='18' value='<?=$row['reservista']?>' />
    </td>
  </tr>
  <tr>
    <td class="secao">Nº Carteira de Trabalho:</td>
    <td>
      <input name='trabalho' type='text' id='trabalho' size='15' value='<?=$row['campo1']?>' />
     </td>
    <td class="secao">Série:</td>
    <td>
     <input name='serie_ctps' type='text' id='serie_ctps' size='10' value='<?=$row['serie_ctps']?>' />
    </td>
    <td class="secao">UF:</td>
    <td><input name='uf_ctps' type='text' id='uf_ctps' size='5' value='<?=$row['uf_ctps']?>'
               onChange="this.value=this.value.toUpperCase()"/></td>
    <td class="secao">Data carteira de Trabalho:</td>
    <td>  
      <input name='data_ctps' type='text' size='12' maxlength='10' id='data_ctps' value='<?=$row['data_ctps2']?>' 
		     onkeyup="mascara_data(this); pula(10,this.id,titulo2.id)" />     
    </td>
  </tr>
  <tr>
    <td class="secao">Nº Título de Eleitor:</td>
    <td>
        <input name='titulo' type='text' id='titulo2' size='10' value='<?=$row['titulo']?>' />
    </td>
    <td class="secao"> Zona:</td>
    <td colspan='3'>
        <input name='zona' type='text' id='zona2' size='3' value='<?=$row['zona']?>' />
    </td>
    <td class="secao">Seção:</td>
    <td>
        <input name='secao' type='text' id='secao' size='3' value='<?=$row['secao']?>' />
    </td>
  </tr>
  <tr>
    <td class="secao">PIS:</td>
    <td>
      <input name='pis' type='text' id='pis' size='12' value='<?=$row['pis']?>' />
    </td>
    <td class="secao">Data PIS:</td>
    <td colspan='3'>
    <input name='data_pis' type='text' size='12' maxlength='10' id='data_pis' value='<?=$row['dada_pis2']?>'
           onkeyup="mascara_data(this); pula(10,this.id,fgts.id)" />
	</td>
    <td class="secao">FGTS:</td>
    <td>
        <input name='fgts' type='text' id='fgts' size='10' value='<?=$row['fgts']?>' />
    </td>
  </tr>
</table>

<table cellpadding="0" cellspacing="1" class="secao">
  <tr>
     <td class="secao_pai" colspan="6">BENEFÍCIOS</td>
  </tr>
  <tr>
    <td class="secao">
	Assistência Médica:</td>
    <td>
	   <label><input name='medica' type='radio' class="reset" value='1' <?=$chek_medi1?>>Sim</label>
       <label><input name='medica' type='radio' class="reset" value='0' <?=$chek_medi0?>>Não</label> <?=$mensagem_medi?>
  </td>
    <td class="secao">Tipo de Plano:</td>
    <td>
    <select name='plano_medico' id='plano_medico'>
       <option value='1' <?=$selected_planoF?>>Familiar</option>
       <option value='2' <?=$selected_planoI?>>Individual</option>
    </select>
    </td>
  </tr>
  <tr>
    <td class="secao">Seguro, Apólice:</td>
    <td>
          <select name='apolice' id='apolice'>
          <option value='0'>Não Possui</option>

    <?php $result_ap = mysql_query("SELECT * FROM apolice WHERE id_regiao = $row[regiao]", $conn);
          while ($row_ap = mysql_fetch_array($result_ap)) {
              if($row_ap['id_apolice'] == $row[apolice]) {
                   print "<option value = '$row_ap[id_apolice]' selected>$row_ap[razao]</option>";   
              } else {
                   print "<option value = '$row_ap[id_apolice]'>$row_ap[razao]</option>";
              }
          } ?>

     </select>
    </td>
    <td class="secao">Dependente:</td>
    <td>
      <input name='dependente' type='text' id='dependente' size='20' value='<?=$row['campo2']?>'
             onChange="this.value=this.value.toUpperCase()"/>
    </td>
  </tr>
  <tr>
    <td class="secao">Insalubridade:</td>
    <td>
    <input name='insalubridade' type='checkbox' class="reset" id='insalubridade2' value='1' <?=$chek1?>/></td>    
	<td class="secao">Adicional Noturno:</td>
    <td>
	  <label><input name='ad_noturno' type='radio' class="reset" value='1' <?=$checkad_noturno1?>>Sim</label>
      <label><input name='ad_noturno' type='radio' class="reset" value='0' <?=$checkad_noturno0?>>Não</label>
    </td>
 </tr>
  <tr>
    <td class="secao">Sem Desconto de INSS:</td>
    <td><label><input name='desconto_inss' type='checkbox' value='1' <?php if(!empty($row['desconto_inss'])) { echo "checked"; } ?> /></label>
    </td>
  <td class="secao">Integrante do CIPA:</td>
   <td>
      <label><input name='cipa' type='radio' class="reset" value='1' <?=$checkedcipa1?>>Sim</label>
      <label><input name='cipa' type='radio' class="reset" value='0' <?=$checkedcipa0?>>Não</label>
    </td>
  </tr>
  <tr>
    <td class="secao">Vale Transporte:</td>
    <td><input name='transporte' type='checkbox' class="reset" id='transporte2' onClick="document.getElementById('tablevale').style.display = (document.getElementById('tablevale').style.display == 'none') ? '' : 'none' ;" value='1' <?=$chek2?> /></td>
    <td class="secao">&nbsp;</td>
    <td>&nbsp;</td>
  </tr> 
</table>
  
<table cellpadding="0" cellspacing="1" class="secao" id="tablevale" <?=$disable_vale?>>
  <tr>
     <td class="secao_pai" colspan="6">VALE TRANSPORTE</td>
  </tr>
  <tr>
    <td class="secao">Selecione:</td>
    <td colspan='4'>
	  <select name='vale1' type='checkbox' id='vale1'>
	  <option value='0'>Não Tem</option>
<?php $resul_vale_trans = mysql_query("SELECT * FROM rh_tarifas WHERE id_regiao = '$row[id_regiao]' AND status_reg = '1'");
      while($row_vale_trans = mysql_fetch_array($resul_vale_trans)){
               
			   $result_conce = mysql_query("SELECT * FROM rh_concessionarias WHERE id_concessionaria = '$row_vale_trans[id_concessionaria]'");
               $row_conce = mysql_fetch_array($result_conce);

         if($row_vale['id_tarifa1'] == "$row_vale_trans[0]") { ?>
                <option value='<?=$row_vale_trans[0]?>' selected><?php echo "$row_vale_trans[valor] - $row_vale_trans[tipo] [$row_vale_trans[itinerario]] - $row_conce[nome]"; ?></option>
         <?php } else { ?>
                <option value='<?=$row_vale_trans[0]?>'><?php echo "$row_vale_trans[valor] - $row_vale_trans[tipo] [$row_vale_trans[itinerario]] - $row_conce[nome]"; ?></option>
         <?php } } ?>
</select>  
  </td>
  </tr>
   <tr>
    <td class="secao">Selecione 2:</td>
    <td colspan='4'>
	  <select name='vale2' type='checkbox' id='vale2'>
	     <option value='0'>Não Tem</option>      
<?php $resul_vale_trans2 = mysql_query("SELECT * FROM rh_tarifas WHERE id_regiao = '$row[id_regiao]' AND status_reg = '1'");
      while($row_vale_trans2 = mysql_fetch_array($resul_vale_trans2)) {
            
			 $result_conce2 = mysql_query("SELECT * FROM rh_concessionarias WHERE id_concessionaria = '$row_vale_trans2[id_concessionaria]'");
             $row_conce2 = mysql_fetch_array($result_conce2);

             if($row_vale['id_tarifa2'] == "$row_vale_trans2[0]") { ?>
                   <option value="<?=$row_vale_trans2[0]?>" selected><?php echo "$row_vale_trans2[valor] - $row_vale_trans2[tipo] [$row_vale_trans2[itinerario]] - $row_conce2[nome]"; ?></option>
             <?php } else { ?>
                   <option value="<?=$row_vale_trans2[0]?>"><?php echo "$row_vale_trans2[valor] - $row_vale_trans2[tipo] [$row_vale_trans2[itinerario]] - $row_conce2[nome]"; ?></option>
             <?php } } ?>
        </select>  
   </td>
  </tr>
  <tr>
    <td class="secao">Selecione 3:</td>
    <td colspan='4'>
	  <select name='vale3' type='checkbox' id='vale3'>
	     <option value='0'>Não Tem</option>
<?php $resul_vale_trans3 = mysql_query("SELECT * FROM rh_tarifas WHERE id_regiao = '$row[id_regiao]' AND status_reg = '1'");
      while($row_vale_trans3 = mysql_fetch_array($resul_vale_trans3)) {
            
			$result_conce3 = mysql_query("SELECT * FROM rh_concessionarias WHERE id_concessionaria = '$row_vale_trans3[id_concessionaria]'");
            $row_conce3 = mysql_fetch_array($result_conce3);

      if($row_vale['id_tarifa3'] == "$row_vale_trans3[0]") { ?>
           <option value='<?=$row_vale_trans3[0]?>' selected><?php echo "$row_vale_trans3[valor] - $row_vale_trans3[tipo] [$row_vale_trans3[itinerario]] - $row_conce3[nome]"; ?></option>
      <?php } else { ?>
            <option value='<?=$row_vale_trans3[0]?>'><?php echo "$row_vale_trans3[valor] - $row_vale_trans3[tipo] [$row_vale_trans3[itinerario]] - $row_conce3[nome]"; ?></option>
      <?php } } ?>
      </select>  
   </td>
</tr>

  <tr>
    <td class="secao">Selecione 4:</td>
    <td colspan='4'>
	  <select name='vale4' type='checkbox' id='vale4'>
	     <option value='0'>Não Tem</option>
<?php $resul_vale_trans4 = mysql_query("SELECT * FROM rh_tarifas WHERE id_regiao = '$row[id_regiao]' AND status_reg = '1'");
      while($row_vale_trans4 = mysql_fetch_array($resul_vale_trans4)) {
		  
            $result_conce4 = mysql_query("SELECT * FROM rh_concessionarias WHERE id_concessionaria = '$row_vale_trans4[id_concessionaria]'");
            $row_conce4 = mysql_fetch_array($result_conce4);

      if($row_vale['id_tarifa4'] == "$row_vale_trans4[0]") { ?>
            <option value='<?=$row_vale_trans4[0]?>' selected><?php echo "$row_vale_trans4[valor] - $row_vale_trans4[tipo] [$row_vale_trans4[itinerario]] - $row_conce4[nome]"; ?></option>
      <?php } else { ?>
            <option value='<?=$row_vale_trans4[0]?>'><?php echo "$row_vale_trans4[valor] - $row_vale_trans4[tipo] [$row_vale_trans4[itinerario]] - $row_conce4[nome]"; ?></option>
           <?php } } ?>
      </select>  
   </td>
</tr>

  <tr>
    <td class="secao">Selecione 5:</td>
    <td colspan='4'>
	  <select name='vale5' type='checkbox' id='vale5'>
	     <option value='0'>Não Tem</option>
<?php $resul_vale_trans5 = mysql_query("SELECT * FROM rh_tarifas WHERE id_regiao = '$row[id_regiao]' AND status_reg = '1'");
      while($row_vale_trans5 = mysql_fetch_array($resul_vale_trans5)){

            $result_conce5 = mysql_query("SELECT * FROM rh_concessionarias WHERE id_concessionaria = '$row_vale_trans5[id_concessionaria]'");
            $row_conce5 = mysql_fetch_array($result_conce5);

      if($row_vale['id_tarifa5'] == "$row_vale_trans5[0]") { ?>
            <option value='<?=$row_vale_trans5[0]?>' selected><?php echo "$row_vale_trans5[valor] - $row_vale_trans5[tipo] [$row_vale_trans5[itinerario]] - $row_conce5[nome]"; ?></option>
      <?php } else { ?>
            <option value='<?=$row_vale_trans5[0]?>'><?php echo "$row_vale_trans5[valor] - $row_vale_trans5[tipo] [$row_vale_trans5[itinerario]] - $row_conce5[nome]"; ?></option>
      <?php } } ?>
      </select>  
   </td>
</tr>

  <tr>
    <td class="secao">Selecione 6:</td>
    <td colspan='4'>
	  <select name='vale6' type='checkbox' id='vale6'>
	  <option value='0'>Não Tem</option>
<?php $resul_vale_trans6 = mysql_query("SELECT * FROM rh_tarifas WHERE id_regiao = '$row[id_regiao]' AND status_reg = '1'");
      while($row_vale_trans6 = mysql_fetch_array($resul_vale_trans6)) {

            $result_conce6 = mysql_query("SELECT * FROM rh_concessionarias WHERE id_concessionaria = '$row_vale_trans6[id_concessionaria]'");
            $row_conce6 = mysql_fetch_array($result_conce6);

      if($row_vale['id_tarifa6'] == "$row_vale_trans6[0]") { ?>
            <option value='<?=$row_vale_trans6[0]?>' selected><?php echo "$row_vale_trans6[valor] - $row_vale_trans6[tipo] [$row_vale_trans6[itinerario]] - $row_conce6[nome]"; ?></option>
      <?php } else { ?>
            <option value='<?=$row_vale_trans6[0]?>'><?php echo "$row_vale_trans6[valor] - $row_vale_trans6[tipo] [$row_vale_trans6[itinerario]] - $row_conce6[nome]"; ?></option>
      <?php } } ?>
      </select>
   </td>
</tr>
  <tr>
	<td class="secao">Numero Cartão 1:</td>
	<td>
	  <input name='num_cartao' type='text' id='num_cartao' size='20' value='<?=$row_vale['cartao1']?>'
			 onChange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">Numero Cartão 2:</td>
    <td>
     <input name='num_cartao2' type='text' id='num_cartao2' size='20' value='<?=$row_vale['cartao2']?>'
            onChange="this.value=this.value.toUpperCase()"/>
    </td>
  </tr>
</table>

<?php
//QUERY para mostrar qual o sindicato atual do funcionário.	  
$result_sindicatotb_tb_rh_clt = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$id_clt'");
$row_sindicato_tb_rh_clt = mysql_fetch_array($result_sindicatotb_tb_rh_clt);

//Vinculo da tabela rh_clt com a tabela rhsindicato.
$vinculo_tb_clt_com_rhsindicato = $row_sindicato_tb_rh_clt['rh_sindicato'];

$result_sindicato = mysql_query("SELECT * FROM rhsindicato WHERE id_sindicato = '$vinculo_tb_clt_com_rhsindicato'");
$row_sindicato = mysql_fetch_array($result_sindicato);

//Variário com o "valor" da primeira opção do selet "Selecinar" sindicato
$sindicato = $row_sindicato['nome'];

//Variário com o "id" da primeira opção do selet "Selecinar" sindicato
$sindicato_value = $row_sindicato_tb_rh_clt['rh_sindicato'];

//Este trecho de código marca automaticamente no fomulário "Possui sindicato" se o usuário possui sindicato Sim ou não.
if(!empty($sindicato)) {
	$checked_sim = 'checked';
	$checked_nao = NULL;
	//Esta linha habilita a visualização do formulário "Selecionar" sindicato.
	$statusBotao = NULL;
} else {
    $checked_nao='checked';
	//Esta linha desabilita a visualização do formulário "Selecionar" sindicato.
	$statusBotao = 'none';
}

//Habilita ou desabilita o formulário "Selecionar" Sindicato
if($row_sindicato_tb_rh_clt['rh_sindicato'] == '0'){
	$visualizacao = "style=display:none";
} else {
	$visualizacao = NULL;
}
?>

<table cellpadding="0" cellspacing="1" class="secao">
  <tr>
     <td class="secao_pai" colspan="2">SINDICATO</td>
  </tr>
  <tr>
      <td width='20%' class="secao">Possui Sindicato:</td>
      <td width='80%'>
 
		<label><input name='radio_sindicato' type='radio' class="reset" onClick="document.getElementById('trsindicato').style.display = '';" value='sim' <?=$checked_sim?>>Sim</label>
        <label><input name='radio_sindicato' type='radio' class="reset" onClick="document.getElementById('trsindicato').style.display = 'none';" value='nao' <?=$checked_nao?>>Não</label></td>
  </tr>
  <tr <?=$visualizacao?> id='trsindicato'>
      <td class="secao">Selecionar:</td>
      <td>
  <label>
     <select name='sindicato' id='sindicato' >
		<option value='<?=$sindicato_value?>'><?php echo substr($sindicato, 0, 80); ?></option>
     <?php 
     $result_todos_sindicato = mysql_query("SELECT * FROM rhsindicato WHERE status = '1' AND id_regiao = '$row[id_regiao]'");
		    while($row_todos_sindicato = mysql_fetch_array($result_todos_sindicato)){
		    echo "<option value='".$row_todos_sindicato['id_sindicato']."'>".substr($row_todos_sindicato['nome'], 0, 80)."</option>";	
		} ?>
	 </select>
  </label>
     </td>
   </tr>
</table>

<table cellpadding="0" cellspacing="1" class="secao">
  <tr>
     <td class="secao_pai" colspan="4">DADOS BANCÁRIOS</td>
  </tr>
  <tr>
    <td width='15%' class="secao">Banco:</td>
    <td width='30%'>
     <select name='banco'>
           <option value='0'>Nenhum Banco</option>
<?php $sql_banco = "SELECT * FROM bancos WHERE id_regiao = '$row[id_regiao]' AND id_projeto = '$row[id_projeto]' AND status_reg = '1'";
      $result_banco = mysql_query($sql_banco, $conn);
         while($row_banco = mysql_fetch_array($result_banco)) {
               if($row['banco'] == "$row_banco[0]"){
                    print "<option value=$row_banco[0] selected>$row_banco[nome]</option>";
               } else {
                    print "<option value=$row_banco[0]>$row_banco[nome]</option>";
               }
         }
         
		 if($row['banco'] == "9999") {
              print "<option value='9999' selected>Outro</option></select>";
         } else {
              print "<option value='9999'>Outro</option></select>";
         } ?>
    </select>
    </td>
    <td width='25%' class="secao">Agência:</td>
    <td width='30%'>
      <input name='agencia' type='text' id='agencia' size='12' value='<?=$row['agencia']?>' />
    </td>
  </tr>
  <tr>
    <td class="secao">Conta:</td>
    <td>
      <input name='conta' type='text' id='conta' size='12' value='<?=$row['conta']?>' />
    <br/>	
	<?php $tipo = $row['tipo_conta'];
	      if($tipo == 'salario') {
		       $checkedSalario = 'checked';	
	      } elseif($tipo == 'corrente') {
		       $checkedCorrente = 'checked';
	      } ?>
   <label><input name='radio_tipo_conta' type='radio' class="reset" value='salario' <?=$checkedSalario?>>Conta Salário </label>
   <label><input name='radio_tipo_conta' type='radio' class="reset" value='corrente' <?=$checkedCorrente?>>Conta Corrente </label></td>
    <td class="secao">Nome do Banco: <br /> (caso não esteja na lista acima)</td>
    <td>
      <input name='nome_banco' type='text' id='nome_banco' size='25' value='<?=$row['nome_banco']?>'
             onChange="this.value=this.value.toUpperCase()"/></td>
  </tr>
</table>


<table cellpadding="0" cellspacing="1" class="secao">
  <tr>
     <td class="secao_pai" colspan="4">DADOS FINANCEIROS</td>
  </tr>
  <tr>
    <td class="secao">Data de Entrada:</td>
    <td>       
	  <input name='data_entrada' type='text' size='12' maxlength='10' id='data_entrada' value='<?=$row['data_entrada2']?>'
             onkeyup="mascara_data(this); pula(10,this.id,exame_data.id)" />	
    </td>
    <td class="secao">Data do Exame Admissional:</td>
    <td> 
	<input name='exame_data' type='text' size='12' maxlength='10' id='exame_data' value='<?=$row['data_exame']?>'
		   onkeyup="mascara_data(this); pula(10,this.id,localpagamento.id)" />
	</td>
  </tr>
  <tr>
    <td width='23%' class="secao">Local de Pagamento:</td>
    <td width='77%' colspan='3'>
      <input name='localpagamento' type='text' id='localpagamento' size='50' value='<?=$row['localpagamento']?>'
             onChange="this.value=this.value.toUpperCase()"/>
    </td>
  </tr>
  <tr>
    <td class="secao">Tipo de Pagamento:</td>
    <td colspan='3'>
      <select name='tipopg' id='tipopg'>

<?php $result_pg = mysql_query("SELECT * FROM tipopg WHERE id_projeto = '$id_projeto'", $conn);
      while($row_pg = mysql_fetch_array($result_pg)) {
         if($row_pg['0'] == $row['tipo_pagamento']) {
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
    <td colspan='3'>
	<textarea name='observacoes' id='observacoes' cols='55' rows='4'  
              onChange="this.value=this.value.toUpperCase()"><?=$row['observacao']?></textarea></td>
  </tr>
</table>

<div id="finalizacao"> 
         O Contrato foi <strong>assinado</strong>?<br>
     <label><input name='assinatura' type='radio' class="reset" id='assinatura' value='1' <?=$selected_ass_sim?>> 
     SIM </label>
     <label><input name='assinatura' type='radio' class="reset" id='assinatura' value='0' <?=$selected_ass_nao?>> 
       N&Atilde;O</label>
     <p>&nbsp;</p>
     O Distrato foi <strong>assinado</strong>?<br>
     <label><input name='assinatura2' type='radio' class="reset" id='assinatura2' value='1' <?=$selected_ass_sim2?>> 
     SIM </label>
     <label><input name='assinatura2' type='radio' class="reset" id='assinatura2' value='0' <?=$selected_ass_nao2?>> 
     N&Atilde;O</label>
        <p>&nbsp;</p>
        Outros documentos foram <strong>assinados</strong>?<br>
     <label><input name='assinatura3' type='radio' class="reset" id='assinatura3' value='1' <?=$selected_ass_sim3?>> 
     SIM </label>
     <label><input name='assinatura3' type='radio' class="reset" id='assinatura3' value='0' <?=$selected_ass_nao3?>> 
       N&Atilde;O</label>
	    <?=$mensagem_ass?>                 
</div>

<div id="observacao">NÃO DEIXE DE CONFERIR OS DADOS APÓS A DIGITAÇÃO</div>

<div align="center"><input type="submit" name="Submit" value="ATUALIZAR" class="botao" /></div> 

<input type='hidden' name='update' value='1'>
<input type='hidden' name='id_clt' value='<?=$row[0]?>'>
<input type='hidden' name='regiao' value='<?=$row['id_regiao']?>'>
<input type='hidden' name='pro' value='<?=$id_projeto?>'>
<input type='hidden' name='id_bolsista' value='<?=$row[1]?>'>
<input type='hidden' name='pagina' value='<?=$pagina?>'>
</form>
  </td>
</tr>
</table>
</div>

<script>
function validaForm(){
   d = document.form1;	   
		deposito = "<?=$Row_pg_dep[0]?>";
		cheque = "<?=$Row_pg_che[0]?>";

           if (d.cpf.value == "" ){
                     alert("O campo CPF deve ser preenchido!");
                     d.cpf.focus();
                     return false;
          }
		  
		  if(d.transporte2.checked == True && d.vale1.value==0 && d.vale2.value==0 && d.vale3.value==0 && d.vale4.value==0 && d.vale5.value==0 && d.vale6value==0){
			  alert("Um dos Vales deve ser Selecionado\!");
              d.vale1.focus();
              return false;
		  }
		  
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

return true;   

}
		
</script>
</body>
</html>


<?php } else {

$id_clt = $_REQUEST['id_clt'];
$regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['pro'];
$horario = $_REQUEST['horario'];
$id_bolsista = $_REQUEST['id_bolsista'];
$tipo_vale = $_REQUEST['tipo_vale'];

// TRABALHANDO COM OS VALES

$num_cartao = $_REQUEST['num_cartao'];
$num_cartao2 = $_REQUEST['num_cartao2'];

$vale1 = $_REQUEST['vale1'];
$vale_qnt_1 = $_REQUEST['vale_qnt_1'];
$vale2 = $_REQUEST['vale2'];
$vale_qnt_2 = $_REQUEST['vale_qnt_2'];
$vale3 = $_REQUEST['vale3'];
$vale_qnt_3 = $_REQUEST['vale_qnt_3'];
$vale4 = $_REQUEST['vale4'];
$vale_qnt_4 = $_REQUEST['vale_qnt_4'];
$vale5 = $_REQUEST['vale5'];
$vale_qnt_5 = $_REQUEST['vale_qnt_5'];
$vale6 = $_REQUEST['vale6'];
$vale_qnt_6 = $_REQUEST['vale_qnt_6'];

// TRABALHANDO COM OS VALES [FIM}
							 
// VALE TRANSPORTE
$qr_vale = mysql_query("SELECT * FROM rh_vale WHERE id_clt = '$id_clt'");
$total_vale = mysql_num_rows($qr_vale);

if(empty($total_vale)) {
	
	echo '1';

} else {
	
	if(($vale1 == '0' and $vale2 == '0' and $vale3 == '0' and $vale4 == '0' and $vale5 == '0' and $vale6 =='0') or
	    $transporte == '0') {
			$status_reg = '0'; echo '2-1';
	} else {
			$status_reg = '1'; echo '2-2';
	}
	

}

exit;
//

}

}
?>