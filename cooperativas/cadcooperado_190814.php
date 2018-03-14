<?php
if(empty($_COOKIE['logado'])){
	print 'Efetue o Login<br><a href="../login.php">Logar</a>';
	exit;
}

include('../conn.php');

$id_user     = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user    = mysql_fetch_array($result_user);

$id_regiao   = $_REQUEST['regiao'];
$projeto     = $_REQUEST['pro'];


$REPro       = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$projeto'");
$RowPro      = mysql_fetch_array($REPro);
$tipo_contratacao = $_GET['tipo'];


// Bloqueio Administração
echo bloqueio_administracao($id_regiao);

if(empty($_REQUEST['update'])) {
	
include('../classes/regiao.php');
$REG = new regiao();
$resut_maior = mysql_query ("SELECT CAST(campo3 AS UNSIGNED) campo3, MAX(campo3) FROM autonomo WHERE id_regiao= '$id_regiao' AND id_projeto ='$projeto' AND campo3 != 'INSERIR' GROUP BY campo3 DESC LIMIT 0,1");
$row_maior = mysql_fetch_array ($resut_maior); 
$codigo = $row_maior[0] + 1;
$codigo = sprintf("%04d",$codigo);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>:: Intranet ::</title>
<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
<link rel="shortcut icon" href="../favicon.ico">
<link href="../rh/css/estrutura_cadastro.css" rel="stylesheet" type="text/css">
<script language="javascript" src="../js/ramon.js"></script>
<link href="../js/jquery.ui.theme.css" rel="stylesheet" type="text/css" />
<link href="../js/jquery.ui.datepicker.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../js/jquery-1.3.2.js"></script>
<script type="text/javascript" src="../js/jquery.ui.core.js"></script>
<script type="text/javascript" src="../js/jquery.ui.widget.js"></script>
<script type="text/javascript" src="../js/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../js/jquery.ui.datepicker-pt-BR.js"></script>
<script type="text/javascript" src="../js/valida_documento.js"></script>
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
function FuncaoInss(a) {
	d = document;
	if(a == 1) {
		d.getElementById('divInss').style.display = '';
		d.getElementById('p_inss').style.display = '';
	} else if(a == 2) {
		d.getElementById('divInss').style.display = 'none';
		d.getElementById('p_inss').style.display = 'none';
		d.getElementById('p_inss').value = '';
		d.getElementById('inss_recolher').value = 11;
	} else if(a == 3) {
		porcentagem = d.getElementById('p_inss').value;
		if(porcentagem <= 11) {
			valor = 11 - porcentagem;
		} else {
			valor = 0;
		}
		d.getElementById('inss_recolher').value = valor;
	}
}
</script>
</head>
<body>
<div id="corpo">
<table align="center" width="100%" cellspacing="0" cellpadding="12" style="font-size:13px; line-height:22px;">
  <tr>
  	<td>
  			<span style="float:right"><?php include('../reportar_erro.php'); ?></span>
     <span style="clear:right"></span>
    </td>
    </tr>
  
  <tr>
    <td>
  <div style="border-bottom:2px solid #F3F3F3; margin-top:10px;">
       <h2 style="float:left; font-size:18px;">
           CADASTRAR 
           <?php switch($tipo_contratacao) {
				case 3:
				    echo '<span class="coo">COOPERADO</span>';
				break;
				case 4:
				    echo '<span class="aut">AUTÔNOMO / PJ</span>';
				break;
	   } ?>
       </h2>
       <p style="float:right;">
           <a href="../ver.php?projeto=<?=$projeto?>&regiao=<?=$id_regiao?>">&laquo; Voltar</a>
       </p>
       <div class="clear"></div>
  </div>
  <p>&nbsp;</p>
<form action="<?=$_SERVER['PHP_SELF']?>" method="post" name="form1" enctype="multipart/form-data" onsubmit="return validaForm()">
<table cellpadding="0" cellspacing="1" class="secao">
  <tr>
    <td colspan="2" class="secao_pai" style="border-top:1px solid #777;">DADOS DO PROJETO</td>
  </tr>
  
  <tr>
     <td class="secao">C&oacute;digo:</td>
     <td><?=$codigo?></td>
  </tr>
  <?php if($tipo_contratacao == 4) { ?>
  <tr>
  	<td>Matrícula:</td>
  	<td><input type="text" name="matricula" maxlength="6" /></td>
  </tr>
	<tr>
		<td>Número do processo:</td>
		<td><input type="text" name="n_processo" maxlength="11"/></td>
	</tr>
  <?php }?>
  <tr>
    <td width="25%" class="secao">Projeto:</td>
    <td width="75%"><?=$RowPro['0']." - ".$RowPro['nome']?></td>
  </tr>
  <tr>
	<td class="secao">Cooperativa Vinculada:</td>
    <td>
        <?php $result_coop = mysql_query("SELECT * FROM cooperativas WHERE id_regiao = '$id_regiao'");
		      $verifica_coop = mysql_num_rows($result_coop);
			  if(!empty($verifica_coop)) {
				        print "<select name='vinculo' id='vinculo'>";
				  while($row_coop = mysql_fetch_array($result_coop)) {
						print "<option value='$row_coop[0]'>$row_coop[0] - $row_coop[fantasia]</option>";
				  }
				        print "</select>";
			  } else {
				  print "Nenhuma Cooperativa Cadastrada";
			  } ?>
    </td>
  </tr>
  <tr>
    <td class="secao">Atividade:</td>
    <td>
    <?php $result_curso = mysql_query("SELECT * FROM curso WHERE campo3 = '$projeto' AND tipo = '3' AND id_regiao = '$id_regiao' ORDER BY campo3 ASC");
	      $verifica_curso = mysql_num_rows($result_curso);
		  if(!empty($verifica_curso)) {
			       print "<select name='atividade' id='atividade'>";
			  while($row_curso = mysql_fetch_array($result_curso)) {
				   print "<option value='$row_curso[0]'>$row_curso[0] - $row_curso[nome]</option>";
			  }
			       print "</select>";
		  } else {
			   print "Nenhuma Atividade Cadastrada";
		  } ?>
    </td>
  </tr>
  <?php 
  if($tipo_contratacao == 4) { ?>
  <tr>
  	<td></td>
  	<td><input type="checkbox" name="contrato_medico" value="1"/> Necessita de contrato para médicos?</td>
  </tr>
  <?php  } ?>
  
  <tr>
   <td class="secao">Unidade:</td>
   <td>
  	<?php $result_unidade = mysql_query("SELECT * FROM unidade WHERE id_regiao = '$id_regiao' AND campo1 = '$projeto' ORDER BY unidade ASC");
	      $verifica_unidade = mysql_num_rows($result_unidade);
		  if(!empty($verifica_unidade)) {
			       print "<select name='locacao' id='locacao'>";
	         while ($row_unidade = mysql_fetch_array($result_unidade)) {
	               //print "<option value='$row_unidade[unidade]'>$row_unidade[id_unidade] - $row_unidade[unidade]</option>";
                       print "<option value='" . $row_unidade[unidade] . " // " . $row_unidade[id_unidade] . "'>$row_unidade[id_unidade] - $row_unidade[unidade]</option>";
	         }
			       print "</select>";
		  } else {
			  print "Nenhuma Unidade Cadastrada";
		  } ?>
   </td>
  </tr>
  <tr style="display:none;">
    <td class="secao">Tipo de Contratação:</td>
    <td>
     <label>
         <input name='contratacao' type='radio' class="reset" id='contratacao' value='3' 
          <?php if($_GET['tipo'] == "3") { echo "checked"; } ?>
          > Cooperado 
      </label>
      <label>
          <input name='contratacao' type='radio' class="reset" id='contratacao' value='4'
           <?php if($_GET['tipo'] == "4") { echo "checked"; } ?>
           > Autônomo / PJ
      </label>
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
	<input name="nome" type="text" id="nome" size="75"
	       onchange="this.value=this.value.toUpperCase()"/>
	</td>
    <td class="secao">Data de Nascimento:</td>
    <td>
	<input name="data_nasci" type="text" id="data_nasci" size="15" maxlength="10"
		   onkeyup="mascara_data(this);"/>
    </td>
  </tr>
  <tr>
    <td class="secao">Estado Civil:</td>
    <td width="16%">
        <select name="civil" id="civil">
            <?php
            $qr_estCivil = mysql_query("SELECT * FROM estado_civil");
            while ($row_estCivil = mysql_fetch_assoc($qr_estCivil)) {
                echo '<option value="' . $row_estCivil['id_estado_civil'] . '|'.$row_estCivil['nome_estado_civil'].'">' . $row_estCivil['nome_estado_civil'] . '</option>';
            }
            ?>   
        </select>
    </td>
    <td class="secao">Sexo:</td>
    <td><label><input name="sexo" type="radio" class="reset" value="M" checked="checked" /> Masculino</label><br />
        <label><input name="sexo" type="radio" class="reset" value="F" /> Feminino</label>
    </td>
    <td class="secao">Nacionalidade:</td>
	<td>
	<input name="nacionalidade" type="text" id="nacionalidade" size="15" 
		   onchange="this.value=this.value.toUpperCase()"/>
	</td>
  </tr>
  <tr>
  <td class="secao">Endereço:</td>
  <td>
	<input name="endereco" type="text" id="endereco" size="35" 
		   onchange="this.value=this.value.toUpperCase()"/>
  </td>
  <td class="secao">Bairro:</td>
  <td>
    <input name="bairro" type="text" id="bairro" size="16" 
		   onchange="this.value=this.value.toUpperCase()"/>
  </td>
  <td class="secao">Naturalidade:</td>
  <td>
	<input name="naturalidade" type="text" id="naturalidade" size="15"  
	       onchange="this.value=this.value.toUpperCase()"/>
	</td>
   </tr>
   <tr>
  <td class="secao">UF:</td>
  <td>
     <?php $ajax = 'onchange="ajaxuf(\'uf\',\'dvcidade\');"';
	       $REG -> SelectUFajax('uf',$ajax); ?>
  </td>
  <td class="secao">Cidade:</td>
  <td>
	 <div align="left" id="dvcidade"></div>
  </td>
  <td class="secao">CEP:</td>
  <td>
	<input name="cep" type="text" id="cep" size="10" maxlength="9"
		   onkeypress="formatar('#####-###', this)" 
		   onkeyup="pula(9,this.id,naturalidade.id)" />
  </td>
  </tr>
  <tr>
    <td class="secao">Estuda Atualmente?</td>
    <td colspan="3">
       <label><input name="estuda" type="radio" class="reset" value="sim" checked="checked" /> SIM</label>
       <label><input name="estuda" type="radio" class="reset" value="não" /> NÃO</label></td>
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
              onkeyup="pula(13,this.id,c_nome.id)" />
   </td>
  </tr>
  <tr>
  		<td class="secao">E-mail:</td>
     <td colspan="5">
    	<input name="email" type="text" id="email" size="35" />
    </td>
  </tr>
    <tr>
  		<td class="secao">Tipo Sanguíneo:</td>
     <td colspan="5">
    	<select name="tiposanguineo">
            <option value="">Selecione</option>
            <?php 
                $query = "select * from tipo_sanguineo";
                $rsquery = mysql_query($query);
                while ($i = mysql_fetch_assoc($rsquery)) {
                    ?>
            <option value="<?php echo $i["nome"] ?>"><?php echo $i["nome"] ?></option>
                        <?php
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
    <input name="c_nome" type="text" id="c_nome" size="50"
           onchange="this.value=this.value.toUpperCase()" />
  </td>
  <td class="secao">Data de Nascimento:</td>
  <td>
    <input name="c_nascimento" type="text" id="c_nascimento" size="15"
           onkeyup="mascara_data(this); pula(10,this.id,c_cpf.id)" />
  </td>
</tr>
<tr>
  <td class="secao">CPF C&ocirc;njuge:</td>
  <td>
    <input name="c_cpf" type="text" id="c_cpf" maxlength="15" size="20" 
           onkeypress="formatar('###.###.###-##', this)"
		   onkeyup="pula(14,this.id,c_profissao.id)"/>
  </td>
  <td class="secao">Profiss&atilde;o C&ocirc;njuge:</td>
  <td>
    <input name="c_profissao" type="text" id="c_profissao" size="15"
           onChange="this.value=this.value.toUpperCase()"/>
    </td>
  </tr>
  <tr>
 	<td class="secao">Filiação - Pai:</td>
	<td>
      <input name="pai" type="text" id="pai" size="50" 
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
      <input name="mae" type="text" id="mae" size="50" 
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
            <option>Loiro</option>
            <option>Castanho Claro</option>
            <option>Castanho Escuro</option>
            <option>Ruivo</option>
            <option>Pretos</option>
        </select>
      </td>
	  <td class="secao">Olhos:</td>
      <td>
        <select name="olhos" id="olhos">
            <option>Castanho Claro</option>
            <option>Castanho Escuro</option>
            <option>Verde</option>
            <option>Azul</option>
            <option>Mel</option>
            <option>Preto</option>
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
		<?php $qr_etnias = mysql_query("SELECT * FROM etnias WHERE status = 'on'");
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
        <input name="foto" type="checkbox" class="reset" id="foto" onclick="document.getElementById('arquivo').style.display = (document.getElementById('arquivo').style.display == 'none') ? '' : 'none' ;" value='1'/>
        <input name="arquivo" type="file" id="arquivo" size="60" style="display:none"/>
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
	<input name="rg" type="text" id="rg" size="13" maxlength="14"
           OnKeyPress="formatar('##.###.###-###', this)" 
		   onkeyup="pula(14,this.id,orgao.id)">
    </td>
    <td class="secao">Orgão Expedidor:</td>
    <td>
    <input name="orgao" type="text" id="orgao" size="8"
           onChange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">UF:</td>
    <td>
    <input name="uf_rg" type="text" id="uf_rg" size="2" maxlength="2" 
		   onKeyUp="pula(2,this.id,data_rg.id)"
           onChange="this.value=this.value.toUpperCase()"/></td>
    <td class="secao">Data Expedição:</td>
    <td>
    <input name="data_rg" type="text" size="12" maxlength="10" id="data_rg"
           onkeyup="mascara_data(this); pula(10,this.id,cpf.id)" />	
    </td>
  </tr>
  <tr>
    <td class="secao">CPF:</td>
    <td>
    <input name="cpf" type="text" id="cpf" size="17" maxlength="14"
           onKeyPress="formatar('###.###.###-##', this)"  
		   onkeyup="pula(14,this.id,reservista.id)"/>
    </td>
    <td class="secao">Carteira do Conselho:</td>
    <td colspan="3">
      <input name="conselho" type="text" id="conselho" size="13" />
    </td>
     <td class="secao">Data de Emissão:</td>
    <td>
    <input name="data_emissao" type="text" size="12" maxlength="10" id="data_emissao"
           onkeyup="mascara_data(this); pula(10,this.id,reservista.id)" />	
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
		   onKeyUp="pula(2,this.id,data_ctps.id)"
           onChange="this.value=this.value.toUpperCase()"/></td>
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
        <input name="zona" type="text" id="zona2" size="3" />
    </td>
    <td class="secao">Seção:</td>
    <td>
        <input name="secao" type="text" id="secao" size="3" />
    </td>
  </tr>
  <tr>
    <td class="secao">PIS:</td>
    <td>
        <input name="pis" type="text" id="pis" size="12" maxlength="11"
               onkeyup="pula(11,this.id,data_pis.id)"/>
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
    <tr>
     <td class="secao">Certificado de Reservista:</td>
    <td colspan="7">
      <input name="reservista" type="text" id="reservista" size="18" />
    </td>
    </tr>
  <tr>
    <td class="secao">Recolhe INSS de Terceiros:</td>
    <td colspan="7">
       <label><input name="inss" type="radio" class="reset" onClick="FuncaoInss(1)" value="1"/> SIM</label>
       <label><input name="inss" type="radio" class="reset" onClick="FuncaoInss(2)" value="2"  checked/> N&Atilde;O</label>
    </td>
  </tr>
  <tr style="display:none" id="divInss">
    <td class="secao">Porcentagem Recolhida:</td>
    <td colspan="7">
      <input name="p_inss" type="text" id="p_inss" size="3" /> % 
      <input name="inss_recolher" type="hidden" id="inss_recolher" value="11" size="3">
    </td>
  </tr>
</table>
<table cellpadding="0" cellspacing="1" class="secao" style="display:none">
  <tr>
    <td class="secao_pai" colspan="6">BENEFÍCIOS</td>
  </tr>
  <tr>
    <td class="secao">Assistência Médica:</td>
    <td>
        <label><input type="radio" name="medica" value="1">Sim</label>
        <label><input type="radio" name="medica" value="0">Não</label>
    </td>
    <td class="secao">
	Tipo de Plano:
    </td>
    <td>
	<select name="plano_medico" id="plano_medico">
        <option value="1">Familiar</option>
        <option value="2">Individual</option>
    </select>   
    </td>
  </tr>
  <tr>
    <td class="secao">Seguro, Apólice:</td>
    <td>
      <select name="apolice" id="apolice">
           <option value="0">Não Possui</option>
		<?php $result_ap = mysql_query("SELECT * FROM apolice WHERE id_regiao = $id_regiao");
                while($row_ap = mysql_fetch_array($result_ap)) {
                  if($row_ap['id_apolice'] == $row[apolice]){
                  print "<option value='$row_ap[id_apolice]' selected>$row_ap[razao]</option>";   
                  } else {
                  print "<option value='$row_ap[id_apolice]'>$row_ap[razao]</option>";
                  }
                }
        ?>
        </select>
    </td>
    <td class="secao">Dependente:</td>
    <td>
      <input name='dependente' type='text' id='dependente' size='20' value=''
             onChange="this.value=this.value.toUpperCase()"/>
    </td>
  </tr>
  <tr>
    <td class="secao">Insalubridade:</td>
    <td>
      <input name='insalubridade' type='checkbox' id='insalubridade2' value='1' />
    </td>
	<td class="secao">Adicional Noturno:</td>
    <td>
        <label><input type='radio' name='ad_noturno' value='1' $checkad_noturno1>Sim</label>
        <label><input type='radio' name='ad_noturno' value='0' $checkad_noturno0>Não</label>
      </td>
  </tr>
  <tr>
    <td class="secao">Vale Transporte:</td>
  <td>
      <input name='transporte' type='checkbox' id='transporte2' value='1' onClick="document.all.tablevale.style.display = (document.all.tablevale.style.display == 'none') ? '' : 'none' ;" $chek2 />
  </td>
    <td class="secao">Integrante do CIPA:</td>
    <td>
       <label><input type='radio' name='cipa' value='1'>Sim</label>
       <label><input type='radio' name='cipa' value='0'>Não</label>
	</td>
  </tr>  
</table>
<table cellpadding="0" cellspacing="1" class="secao">
  <tr>
    <td class="secao_pai" colspan="6">INFORMA&Ccedil;&Otilde;ES PROFISSIONAIS</td>
  </tr>
  <tr>
    <td width="13%" class="secao">Empresa:</td>
    <td colspan="3">
      <input name='e_nome' type='text' id='e_nome' size='50' 
             onChange="this.value=this.value.toUpperCase()"/>
    </td>
    <td width='10%' class="secao">CNPJ:</td>
    <td width='22%'>
      <input name='e_cnpj' type='text' id='e_cnpj' size="19" maxlength='18'
             OnKeyPress="formatar('##.###.###/####-##', this)"
	         onkeyup="pula(18,this.id,e_endereco.id)" />
    </td>
  </tr>
  <tr>
    <td class="secao">Endere&ccedil;o:</td>
    <td width='24%'>
      <input name='e_endereco' type='text' id='e_endereco' 
             onChange="this.value=this.value.toUpperCase()" size='35'/>
    </td>
    <td width='11%' class="secao">Bairro:</td>
    <td width='20%'>
      <input name='e_bairro' type='text' id='e_bairro' size='20'
             onChange="this.value=this.value.toUpperCase()" />
    </td>
    <td class="secao">Cidade:</td>
    <td> 
      <input name='e_cidade' type='text' id='e_cidade' 
             onChange="this.value=this.value.toUpperCase()" size='20'/>
    </td>
  </tr>
  <tr>
    <td class="secao">Estado:</td>
    <td>
      <input name='e_estado' type='text' id='e_estado' size='2' maxlength='2' 
             onChange="this.value=this.value.toUpperCase()"
             onKeyUp="pula(2,this.id,e_cep.id)" />
    </td>
    <td class="secao">CEP:</td>
    <td>
      <input name='e_cep' type='text' id='e_cep' size='10' maxlength='9' 
             style='text-transform:uppercase;'
             OnKeyPress="formatar('#####-###', this)" 
             onKeyUp="pula(9,this.id,e_ramo.id)" />
    </td>
    <td class="secao">Ramo Atividade:</td>
    <td>
      <input name='e_ramo' type='text' id='e_ramo' size='20'
             onChange="this.value=this.value.toUpperCase()" />
    </td>
  </tr>
  <tr>
    <td class="secao">Telefone:</td>
    <td>
      <input name='e_telefone' type='text' id='e_telefone' size='14' 
             onKeyPress="return(TelefoneFormat(this,event))" 
             onKeyUp="pula(13,this.id,e_ramal.id)" />
    </td>
    <td class="secao">Ramal:</td>
    <td>
      <input name='e_ramal' type='text' id='e_ramal' size='14'>
    </td>
    <td class="secao">Fax:</td>
    <td> 
      <input name='e_fax' type='text' id='e_fax' size='14' 
             onKeyPress="return(TelefoneFormat(this,event))" 
             onKeyUp="pula(13,this.id,e_email.id)" />
    </td>
  </tr>
  <tr>
    <td class="secao">E-mail:</td>
    <td>
      <input name='e_email' type='text' id='e_email' size='30' 
             style="text-transform:lowercase">
    </td>
    <td class="secao">Tempo de Servi&ccedil;o:</td>
    <td>
      <input name='e_tempo' type='text' id='e_tempo' size='14' 
             onChange="this.value=this.value.toUpperCase()">
    </td>
    <td class="secao">Profiss&atilde;o:</td>
    <td>
      <input name='e_profissao' type='text' id='e_profissao' size='14' 
             onChange="this.value=this.value.toUpperCase()">
    </td>
  </tr>
  <tr>
    <td class="secao">Cargo:</td>
    <td>
      <input name='e_cargo' type='text' id='e_cargo' size='20' 
             onChange="this.value=this.value.toUpperCase()">
    </td>
    <td class="secao">Data Emiss&atilde;o:</td>
    <td>
      <input name='e_dataemissao' type='text' size='12' maxlength='10' id='e_dataemissao'
		     onkeyup="mascara_data(this); pula(10,this.id,e_referencia.id)" />
    </td>
    <td class="secao">Refer&ecirc;ncia:</td>
    <td>
      <input name='e_referencia' type='text' id='e_referencia' />
    </td>
  </tr>
  <tr>
    <td class="secao">Renda:</td>
    <td colspan="5">
      <input name='e_renda' type='text' id='e_renda' size="15"
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
    <td width='13%' class="secao">Nome:</td>
    <td colspan="5">
      <input name='r_nome' type='text' id='r_nome' size='50' 
             onChange="this.value=this.value.toUpperCase()"/>
    </td>
  </tr>
  <tr>
    <td class="secao">Endere&ccedil;o:</td>
    <td width='24%'>
      <input name='r_endereco' type='text' id='r_endereco' 
             onChange="this.value=this.value.toUpperCase()" size='35'/>
    </td>
    <td width='11%' class="secao">Bairro:</td>
    <td width='20%'> 
      <input name='r_bairro' type='text' id='r_bairro' 
             onChange="this.value=this.value.toUpperCase()" size='20'/>
      </td>
    <td width="10%" class="secao">Cidade:</td>
    <td width="22%"> 
      <input name='r_cidade' type='text' id='r_cidade' 
             onChange="this.value=this.value.toUpperCase()" size='20'/>
      </td>
    </tr>
  <tr>
    <td class="secao">Estado:</td>
    <td>
      <input name='r_estado' type='text' id='r_estado' 
             onKeyUp="pula(2,this.id,e_cep.id)"
             onChange="this.value=this.value.toUpperCase()" size='2'/>
    </td>
    <td class="secao">CEP:</td>
    <td>
      <input name='r_cep' type='text' id='r_cep' size='10' maxlength='9' 
             style='text-transform:uppercase;'
             OnKeyPress="formatar('#####-###', this)" 
             onKeyUp="pula(9,this.id,r_email.id)" />
    </td>
    <td class="secao">E-mail:</td>
    <td>
      <input name='r_email' type='text' id='r_email' size="30"
             style="text-transform:lowercase" />
    </td>
    </tr>
  <tr>
    <td class="secao">Telefone:</td>
    <td>
      <input name='r_telefone' type='text' id='r_telefone' size='14' 
             onKeyPress="return(TelefoneFormat(this,event))" 
             onKeyUp="pula(13,this.id,r_ramal.id)" />
      </td>
    <td class="secao">Ramal:</td>
    <td>
      <input name='r_ramal' type='text' id='r_ramal' size='14' />
      </td>
    <td class="secao">Fax:</td>
    <td>
      <input name='r_fax' type='text' id='r_fax' size='14' 
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
      <label><input name="radio_tipo_conta" type="radio" class="reset" value="salario"> Conta Salário</label>
      <label><input name="radio_tipo_conta" type="radio" class="reset" value="corrente"> Conta Corrente</label>
      <label><br />
        <input name="radio_tipo_conta" type="radio" class="reset" value=""> 
        Sem Conta</label>
    </td>
    <td class="secao">Nome do Banco:<br />(caso não esteja na lista acima)</td>
    <td>
       <input name="nomebanco" type="text" id="nomebanco" size="25" 
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
        <input name='data_entrada' type='text' size='12' maxlength='10' id='data_entrada'
               onkeyup="mascara_data(this); pula(10,this.id,data_exame.id)" />
    </td>
	<td class="secao">Data do Exame Admissional:</td>
	<td>
        <input name='data_exame' type='text' size='12' maxlength='10' id='data_exame'
               onkeyup="mascara_data(this); pula(10,this.id,localpagamento.id)" />
    </td>
  </tr>
  <tr>
	<td width='23%' class="secao">Local de Pagamento:</td>
	<td width='77%' colspan='3'>
        <input name='localpagamento' type='text' id='localpagamento' size='25'  
               onChange="this.value=this.value.toUpperCase()"/>
     </td>
   </tr>
   <tr>
     <td class="secao">Tipo de Pagamento:</td>
     <td colspan='3'>
        <select name='tipopg' id='tipopg'>
        <?php $result_pg = mysql_query("SELECT * FROM tipopg WHERE id_projeto = '$projeto'");
              while($row_pg = mysql_fetch_array($result_pg)) {
                    print "<option value='$row_pg[id_tipopg]'>$row_pg[tipopg]</option>";
              } ?>
        </select>
     </td>
  </tr>
  <tr>
    <td class="secao"> Cota:</td>
    <td>
      <input name='cota' type='text' id='cota' size='18' 
             OnKeyDown="FormataValor(this,event,17,2)"
             onChange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">Parcelas:</td>
    <td>
      <input name='parcelas' type='text' id='parcelas' size='15'  
	         onChange="this.value=this.value.toUpperCase()"/>
    </td>
  </tr>
  <tr>
	<td class="secao">Observações:</td>
	<td colspan='3'>
	   <textarea name='observacoes' id='observacoes' cols='55' rows='4'  
                 onChange="this.value=this.value.toUpperCase()"></textarea>
    </td>
  </tr>
</table>
<div id="observacao">NÃO DEIXE DE CONFERIR OS DADOS APÓS A DIGITAÇÃO</div>
<div align="center"><input type="submit" name="Submit" value="CADASTRAR" class="botao" /></div> 
<input type='hidden' name='regiao' value='<?=$id_regiao?>'/>
<input type='hidden' name='id_cadastro' value='4'>
<input type='hidden' name='projeto' value='<?=$projeto?>'>
<input type='hidden' name='user' value='<?=$id_user?>'>
<input type='hidden' name='update' value='1'>
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

var cpf = $('#cpf').val().replace('.','').replace('.','').replace('-','');             
            
            if (d.cpf.value == "" ){
                    alert("O campo CPF deve ser preenchido!");
                        d.cpf.focus();
                            return false;
            }
            if(!VerificaCPF(cpf)){
                    alert('Cpf Inválido');
                        d.cpf.focus();
                        return false;
            }

if (d.inss[0].checked && d.p_inss.value == ""){
alert("Por Favor, digite a porcentagem INSS que ele recebe de terceiros!");
d.p_inss.focus();
return false;
}
if (d.localpagamento.value == "" ){
alert("O campo Local de Pagamento deve ser preenchido!");
d.localpagamento.focus();
return false;
}

return true;   }
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
<?php } else {
    
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


// CADASTRO DE COOPERADO
$regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['projeto'];
//DADOS CONTRATAÇÃO
$vinculo = $_REQUEST['vinculo'];
$id_curso = $_REQUEST['atividade'];
$tipo_contratacao = $_REQUEST['contratacao'];
$contrato_medico = $_POST['contrato_medico'];
$matricula = $_POST['matricula'];
$n_processo = $_POST['n_processo'];

//trata unidade
$locacao = explode("//", $_REQUEST['locacao']);
$locacao_nome = $locacao[0];
$locacao_id = $locacao[1];

//DADOS CADASTRAIS
$nome = mysql_real_escape_string($_REQUEST['nome']);
$sexo = $_REQUEST['sexo'];
$endereco = mysql_real_escape_string($_REQUEST['endereco']);
$bairro = mysql_real_escape_string($_REQUEST['bairro']);
$cidade =mysql_real_escape_string( $_REQUEST['cidade']);
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
//DADOS DA FAMILIA E OUTROS
$c_nome = $_REQUEST['c_nome'];
$c_cpf = $_REQUEST['c_cpf'];
$c_nascimento = $_REQUEST['c_nascimento'];
$c_profissao = $_REQUEST['c_profissao'];
$pai = $_REQUEST['pai'];
$mae = $_REQUEST['mae'];
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
$campo3 = $_REQUEST['codigo'];
$cota = $_REQUEST['cota'];
$parcelas = $_REQUEST['parcelas'];
$cota = str_replace(".","",$cota);
$cota = str_replace(",",".",$cota);
$pis = $_REQUEST['pis'];
$fgts = $_REQUEST['fgts'];
$tipopg = $_REQUEST['tipopg'];
$filhos = $_REQUEST['filhos'];
$observacoes = $_REQUEST['observacoes'];
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
$data_emissao = $_REQUEST['data_emissao'];

$email = $_POST['email'];
$tipo_sanguineo = $_REQUEST['tiposanguineo'];

//Inicio Verificador CPF
$qrCpf = mysql_query("SELECT COUNT(id_autonomo) AS total FROM autonomo WHERE cpf = '$cpf' AND id_projeto = '$id_projeto' AND id_regiao = '$regiao' AND tipo_contratacao = '$tipo_contratacao'");
$rsCpf = mysql_fetch_assoc($qrCpf);
$totalCpf = $rsCpf['total'];
if($totalCpf > 0){ ?>

<script type="text/javascript">
        alert("Esse CPF já existe para esse projeto");
        window.history.back();
</script>

<?php exit(); }
//Fim verificador CPF

//Inicio verificador PIS
    if(strlen($pis) != 11) {
    ?>
        <script type="text/javascript">
            alert("PIS Inválido!");
            window.history.back();
        </script>
    <?php exit(); }
//Fim verificador PIS

if(empty($_REQUEST['foto'])){
$foto = "0";
}else{
$foto = $_REQUEST['foto'];
}
if($foto == "1"){
  $foto_banco = "1";
  $foto_up = "1";
}else{
  $foto_banco = "0";
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
$pis_data     = ConverteData($pis_data);
$exame_data   = ConverteData($exame_data);
$trabalho_data = ConverteData($trabalho_data);
$c_nascimento = ConverteData($c_nascimento);
$e_dataemissao = ConverteData($e_dataemissao);
$data_emissao = ConverteData($data_emissao);
$data_cadastro = date('Y-m-d');
//VERIFICANDO SE O FUNCIONÁRIO JA ESTÁ CADASTRADO NA TABELA AUTÔNOMO
$verificando_cooperado = mysql_query("SELECT nome FROM autonomo WHERE nome = '$nome' AND data_nasci = '$data_nasci' AND rg = '$rg' AND status = '1'");
$row_verificando_cooperado = mysql_num_rows($verificando_cooperado);
if (!empty($row_verificando_cooperado)) {
print "
<html>
<head>
<title>:: Intranet ::</title>
</head>
<body bgcolor='#D7E6D5'>
<center>
<br>ESTE FUNCIONÁRIO JA ESTÁ CADASTRADO: <font color=#FFFFFF><b>$row_verificando_cooperado[nome]</b></font>
</center>
</body>
</html>
";
exit; 
} else { // CASO O FUNCIONÁRIO NÃO ESTEJA CADASTRADO VAI RODAR O INSERT
	$result_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$id_projeto'");
	$row_projeto = mysql_fetch_array($result_projeto);
	$data_cadastro = date('Y-m-d');
	$id_user = $_COOKIE['logado'];
	
	// GERANDO NOVAMENTE O CÓDIGO, PARA NÃO HAVER NÚMEROS DUPLICADOS NA TABELA
	/* SQL Errado!!!
	$resut_maior = mysql_query ("SELECT CAST(campo3 AS UNSIGNED) campo3 , 
	MAX(campo3) 
	FROM autonomo 
	WHERE id_regiao= '$regiao' 
	AND id_projeto ='$id_projeto' 
	AND campo3 != 'INSERIR' 
	GROUP BY campo3 DESC 
	LIMIT 0,1");
	*/
	
	$sql = "select 
		    cast(campo3 as unsigned) campo3
		from autonomo a 
		where a.id_regiao = '$regiao' and a.id_projeto = '$id_projeto' and a.campo3 != 'INSERIR'
		order by campo3 desc limit 1;";
	$resut_maior = mysql_query($sql);
	$row_maior = mysql_fetch_array ($resut_maior); 
	
	$codigo = $row_maior[0] + 1;
	$codigo = sprintf("%04d",$codigo);
        $civil = explode('|', $civil);
        $estCivilId = $civil[0];
        $estCivilNome = $civil[1];

mysql_query ("insert into autonomo
(id_projeto,id_regiao,localpagamento,locacao,id_unidade,nome,sexo,endereco,bairro,cidade,uf,cep,tel_fixo,tel_cel,tel_rec,
data_nasci,naturalidade,nacionalidade,civil,rg,orgao,data_rg,cpf,conselho,titulo,zona,secao,inss,pai,nacionalidade_pai,mae,nacionalidade_mae,
estuda,data_escola,escolaridade,instituicao,curso,tipo_contratacao,banco,agencia,conta,tipo_conta,id_curso,apolice,data_entrada,campo1,campo2,
campo3,data_exame,reservista,etnia,deficiencia,cabelos,altura,olhos,peso,defeito,cipa,ad_noturno,plano,assinatura,distrato,
outros,pis,dada_pis,data_ctps,serie_ctps,uf_ctps,uf_rg,fgts,insalubridade,transporte,medica,tipo_pagamento,nome_banco,num_filhos,
observacao,impressos,sis_user,data_cad,foto,id_cooperativa,c_nome,c_cpf,c_nascimento,c_profissao,e_empresa,e_cnpj,e_ramo,e_endereco,e_bairro,e_cidade,e_estado,e_cep,e_tel,e_ramal,e_fax,e_email,e_tempo,e_profissao,e_cargo,e_renda,e_dataemissao,e_referencia,r_nome,r_endereco,r_bairro,r_cidade,
r_estado,r_cep,r_tel,r_ramal ,r_fax,r_email,rh_vinculo,rh_status,rh_horario,rh_sindicato,rh_cbo,cota,parcelas, contrato_medico,matricula, n_processo, email, data_emissao, tipo_sanguineo, id_estado_civil) 
VALUES
('$id_projeto','$regiao','$localpagamento','$locacao_nome','$locacao_id','$nome','$sexo','$endereco','$bairro','$cidade','$uf',
'$cep','$tel_fixo','$tel_cel','$tel_rec','$data_nasci','$naturalidade','$nacionalidade','$estCivilNome','$rg',
'$orgao','$data_rg','$cpf','$conselho','$titulo','$zona','$secao','$inss_recolher','$pai','$nacionalidade_pai','$mae','$nacionalidade_mae','$estuda',
'$data_escola','$escolaridade','$instituicao','$curso','$tipo_contratacao','$banco','$agencia','$conta','$tipoDeConta','$id_curso','$apolice',
'$data_entrada','$campo1','$campo2','$codigo','$exame_data','$reservista','$etnia','$deficiencia','$cabelos','$altura','$olhos','$peso','$defeito','$cipa',
'$ad_noturno','$plano_medico','$impressos','$assinatura2','$assinatura3','$pis','$pis_data','$trabalho_data','$serie_ctps',
'$uf_ctps','$uf_rg','$fgts','$insalubridade','$transporte','$medica','$tipopg','$nomebanco','$filhos','$observacoes','$impressos',
'$id_user','$data_cadastro','$foto_banco','$vinculo','$c_nome','$c_cpf','$c_nascimento','$c_profissao','$e_nome','$e_cnpj',
'$e_ramo','$e_endereco','$e_bairro','$e_cidade','$e_estado','$e_cep','$e_tel','$e_ramal','$e_fax','$e_email','$e_tempo','$e_profissao',
'$e_cargo','$e_renda','$e_dataemissao','$e_referencia','$r_nome','$r_endereco','$r_bairro','$r_cidade','$r_estado','$r_cep','$r_tel',
'$r_ramal','$r_fax','$r_email','$rh_vinculo','$rh_status','$rh_horario','$rh_sindicato','$rh_cbo','$cota','$parcelas','$contrato_medico', '$matricula', '$n_processo', '$email', '$data_emissao', '$tipo_sanguineo', '$estCivilId')") or die ("Ops! Erro<br>" . mysql_error());
$row_id_participante = mysql_insert_id();
$row_id_clt = $row_id_participante;
} // AQUI TERMINA DE INSERIR OS DADOS DO COOPERADO
$id_bolsista = $row_id_participante;
//VALE TRANSPORTE
if($transporte == "1"){
mysql_query ("insert into rh_vale(id_clt,id_regiao,id_projeto,id_tarifa1,id_tarifa2,id_tarifa3,id_tarifa4,
id_tarifa5,id_tarifa6,cartao1,cartao2) values 
('$row_id_participante','$regiao','$projeto','$vale1','$vale2','$vale3','$vale4','$vale5','$vale6','$num_cartao','$num_cartao2')") or die ("$mensagem_erro - 2.3<br><br>".mysql_error());
}
//DEPENDENTES
if($filho_1 == "" and $filho_2 == "" and $filho_3 == "" and $filho_4 == "" and $filho_5 == ""){
	$naa = "";
}else{
	mysql_query ("insert into dependentes(id_regiao,id_projeto,id_bolsista,contratacao,nome,data1,nome1,data2,nome2,data3,nome3,data4,
	nome4,data5,nome5) values ('$regiao','$id_projeto','$row_id_participante','$tipo_contratacao','$nome','$data_filho_1','$filho_1','$data_filho_2',
	'$filho_2','$data_filho_3','$filho_3','$data_filho_4','$filho_4','$data_filho_5','$filho_5')") or die 
	("$mensagem_erro 2.4<br><br>".mysql_error());
	$naa = "2";
}
//---------------------------------//
//---- SENHA PARA A TV SORRINDO ---//
//---------------------------------//
$n_id_curso = sprintf("%04d",$id_curso);
$n_regiao = sprintf("%04d",$regiao);
$n_id_bolsista = sprintf("%04d",$row_id_participante);
$cpf2 = str_replace(".","", $cpf);
$cpf2 = str_replace("-","", $cpf2);
// GERANDO A SENHA ALEATÓRIA
$target = "%%%%%%";
    $senha = "";
	$dig = "";
    $consoantes = "bcdfghjkmn123456789pqrstvwxyz1234567890bcdfghj123456789kmnpqrstvwxyz123456789"; 
    $vogais = "aeiou"; 
    $numeros = "123456789bcdfghjkmnpqrstvwxyzaeiou123456789"; 
    $a = strlen($consoantes)-1; 
    $b = strlen($vogais)-1; 
    $c = strlen($numeros)-1; 
    for($x=0;$x<=strlen($target)-1;$x++) 
    { 
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
$id_tv = mysql_insert_id();
mysql_query ("UPDATE autonomo SET tvsorrindo = '$id_tv', senhatv = '$senha' WHERE id_autonomo = '$row_id_participante'");
//------------------------------
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
	$nome_tmp = $regiao."_".$id_projeto."_".$row_id_participante.$tipo_arquivo;
	$nome_arquivo = "$diretorio$nome_tmp" ;
	
	move_uploaded_file($arquivo['tmp_name'], $nome_arquivo ) or die ("Erro ao enviar o Arquivo: $nome_arquivo");
}
}
header("Location: ../ver_bolsista.php?reg=$regiao&bol=$row_id_participante&pro=$id_projeto&sucesso=cadastro&tipo=$tipo_contratacao");
exit;
}
?>