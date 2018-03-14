<?php
if(empty($_COOKIE['logado'])) {
	print 'Efetue o Login<br><a href="login.php">Logar</a>';
	exit;
}

include('conn.php');
include('classes/regiao.php');

$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$REG = new regiao();

if(empty($_REQUEST['update'])) {
	
$id_regiao = $_REQUEST['regiao'];
$projeto = $_REQUEST['pro'];
$resut_maior = mysql_query("SELECT CAST(campo3 AS UNSIGNED) campo30, MAX(campo3) FROM autonomo WHERE id_regiao= '$id_regiao' AND id_projeto = '$projeto' AND campo3 != 'INSERIR' GROUP BY campo30 ASC");
$row_maior = mysql_num_rows($resut_maior);
$codigo = $row_maior + 1;

// Bloqueio Administração
echo bloqueio_administracao($id_regiao);
?>
<html>
<head>
<title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="shortcut icon" href="favicon.ico">
<link href="rh/css/estrutura_cadastro.css" rel="stylesheet" type="text/css">
<script src="js/ramon.js" type="text/javascript" language="javascript"></script>
<link href="js/jquery.ui.theme.css" rel="stylesheet" type="text/css" />
<link href="js/jquery.ui.datepicker.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery-1.3.2.js"></script>
<script type="text/javascript" src="js/jquery.ui.core.js"></script>
<script type="text/javascript" src="js/jquery.ui.widget.js"></script>
<script type="text/javascript" src="js/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="js/jquery.ui.datepicker-pt-BR.js"></script>
<script type="text/javascript" src="js/valida_documento.js"></script>
<script type="text/javascript">
$(function(){
	/*var tipoVerifica = 0;
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
			
			if($("input[name*='nome_banco']").val() == ""){
				indice.push("Nome do banco");
			}
		}
		
		if(indice.length > 0){
			alert("Preencha o(s) dado(s) "+indice.join(', '));
                        
		}else{
			
			$('#form1').submit();
		}
       
                
	}); */
//        $('form').submit(function(){            
//           
//            var cpf = $('#cpf').val().replace('.','').replace('.','').replace('-','');             
//            
//            if(!VerificaCPF(cpf)){
//               alert('Cpf Inválido');
//                return false;
//            }
//            
//        });
                
        $( "#data_entrada" ).datepicker({ minDate: new Date(2009, 1 - 1, 1) });
        $( "#data_entrada" ).datepicker({ showMonthAfterYear: true });
                
});
</script>
<style>
    .none{ display: none;}
</style>
</head>
<body>
<div id="corpo">
<table align="center" width="100%" cellspacing="0" cellpadding="12" style="font-size:13px; line-height:22px;">
  <tr>
  	<td>
  			<span style="float:right"><?php include('reportar_erro.php'); ?></span>
     <span style="clear:right"></span>
    </td>
    </tr>
   
  
  <tr>
    <td>
    
    <div style="border-bottom:2px solid #F3F3F3; margin-top:10px;">
       <h2 style="float:left; font-size:18px;">
           CADASTRAR <span class="aut">AUT&Ocirc;NOMO</span>
       </h2>
       <p style="float:right;">
           <a href="ver.php?regiao=<?=$id_regiao?>&projeto=<?=$projeto?>">&laquo; Voltar</a>
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
  <?php $qr_projeto = mysql_query("SELECT nome FROM projeto WHERE id_projeto = '$projeto'");
  		echo $projeto.' - '.mysql_result($qr_projeto, 0); ?>
    </td>
  </tr>
  <tr>
  <td class="secao">Atividade:</td>
  <td>
  <?php 
  $sql = "SELECT * FROM curso WHERE id_regiao = '$id_regiao' AND campo3 = '$projeto' AND tipo = '1' ORDER BY nome ASC";
  $qr_curso = mysql_query($sql);
//  echo '<!-- *** '.$sql.' -->';
        $verifica_curso = mysql_num_rows($qr_curso);
		
		if(!empty($verifica_curso)) {
			
  			print "<select name='idcurso' id='idcurso'>
       				    <option style='margin-bottom:3px;' selected disabled>--Selecione--</option>";
			
			while($row_curso = mysql_fetch_array($qr_curso)) {
				
				$margem++;
				
				if($margem != $verifica_curso) {
					$var_margem = ' style="margin-bottom:3px;"';
				} else {
					$var_margem = NULL;
				}
				
				$salario = number_format($row_curso['salario'],2,',','.');
	
					print "<option value='$row_curso[0]'$var_margem>$row_curso[0] - $row_curso[campo2] (Valor: $salario)</option>";
				
			}
			
			print '</select>';
		} else {
			
				print 'Nenhum Curso Cadastrado para o Projeto';
				  
		} ?>
  </td>
 </tr>
  <tr>
  <td class="secao">Unidade:</td>
  <td>
  	<?php $qr_unidade = mysql_query("SELECT * FROM unidade WHERE id_regiao = '$id_regiao' AND campo1 = '$projeto' ORDER BY unidade ASC");
		  $verifica_unidade = mysql_num_rows($qr_unidade);
		  if(!empty($verifica_unidade)) { ?>
                  <select name="locacao" id="locacao">
                      <option style="margin-bottom:3px;" value="">Selecione</option>
      
                    <?php
			while($row_unidade = mysql_fetch_array($qr_unidade)) {
				
				$margem2++;
				
				if($margem2 != $verifica_unidade) {
					$var_margem2 = ' style="margin-bottom:3px;"';
				} else {
					$var_margem2 = NULL;
				}
				print "<option value='$row_unidade[unidade]'$var_margem2>$row_unidade[id_unidade] - $row_unidade[unidade]</option>";
		
			} ?>
			</select>
		<?php } else {
			
				print 'Nenhum Curso Cadastrado para o Projeto';
				  
		} ?>
  </td>
  </tr>
  <tr style="display:none;">
     <td class="secao">Tipo Contrata&ccedil;&atilde;o:</td>
     <td><input name="contratacao" type="radio" class="reset" id="contratacao" value="1" checked="checked"> Autônomo</td>
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
               onChange="this.value=this.value.toUpperCase()" />
    </td>
    <td class="secao">Data de Nascimento:</td>
    <td>
	<input name="data_nasci" type="text" id="data_nasci" size="15" maxlength="10"
		   onkeyup="mascara_data(this);" />
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
  <tr class="none">
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
    <td>
    <label><input type="radio" name="sexo" value="M" checked="checked" /> Masculino</label><br>
    <label><input type="radio" name="sexo" value="F" /> Feminino</label>
    </td>
    <td class="secao">Nacionalidade:</td>
    <td>
      <input name="nacionalidade" type="text" id="nacionalidade" size="15" />
    </td>
  </tr>
  <tr class="none">
    <td class="secao">Endereco:</td>
	<td>
	   <input name="endereco" type="text" id="endereco" size="35" onChange="this.value=this.value.toUpperCase()" />
    </td>
    <td class="secao">Bairro:</td>
    <td>
      <input name="bairro" type="text" id="bairro" size="16" onChange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">Naturalidade:</td>
    <td><input name="naturalidade" type="text" id="naturalidade" size="15"  
			   onChange="this.value=this.value.toUpperCase()"/>
    </td>
  </tr>
  <tr class="none">
    <td class="secao">UF:</td>
    <td>
      <?php $ajax = 'onchange="ajaxuf(\'uf\',\'dvcidade\');"';
	        $REG -> SelectUFajax('uf',$ajax); ?>
    </td>
    <td class="secao">Cidade:</td>
    <td>
	  <div align="left" id="dvcidade">&nbsp;&nbsp;</div>
    </td>
    <td class="secao">CEP:</td>
    <td><input name="cep" type="text" id="cep" size="10" maxlength="9" 
               OnKeyPress="formatar('#####-###', this)" 
               onKeyUp="pula(9,this.id,naturalidade.id)" />
    </td>
    
  </tr>
  <tr class="none">
    <td class="secao">Estuda Atualmente?</td>
    <td colspan="3">
	  <label><input type="radio" name="estuda" value="sim" class="reset" checked="checked" /> SIM</label>
      <label><input type="radio" name="estuda" value="não" class="reset" /> NÃO</label>
    </td>
    <td class="secao">Término em:</td>
    <td><input name="data_escola" type="text" id="data_escola" size="15" maxlength="10"
	   		   onKeyUp="mascara_data(this); pula(10,this.id,escolaridade.id)">
    </td>
  </tr>
  <tr class="none">
    <td class="secao">Escolaridade:</td>
    <td><select name="escolaridade">
			<?php $qr_escolaridade = mysql_query("SELECT * FROM escolaridade WHERE status = 'on'");
            	  while ($escolaridade = mysql_fetch_assoc($qr_escolaridade)) { ?>
            <option value="<?=$escolaridade['id']?>">
               <?=$escolaridade['nome']?>
            </option>
            <?php } ?>
		</select>
	</td>
    <td class="secao">Curso:</td>
    <td>
    <input name="curso" type="text" id="zona" size="16" 
           onChange="this.value=this.value.toUpperCase()" />
    </td>
	<td class="secao">Instituição:</td>
	<td>
    <input name="instituicao" type="text" id="titulo" size="15" 
           onChange="this.value=this.value.toUpperCase()" />
    </td>
  </tr>
  <tr class="none">
    <td class="secao">Telefone Fixo:</td>
    <td><input name="tel_fixo" type="text" id="tel_fixo" size="14" 
               onKeyPress="return(TelefoneFormat(this,event))" 
               onKeyUp="pula(13,this.id,tel_cel.id)">
    </td>
    <td class="secao"> Celular:</td>
    <td><input name="tel_cel" type="text" id="tel_cel" size="16" 
               onKeyPress="return(TelefoneFormat(this,event))" 
               onKeyUp="pula(13,this.id,tel_rec.id)" /></td>
    <td class="secao">Recado:</td>
    <td><input name="tel_rec" type="text" id="tel_rec" size="15" onKeyPress="return(TelefoneFormat(this,event))" 
               onKeyUp="pula(13,this.id,data_nasci.id)" />
    </td>
  </tr>
  <tr class="none">
  	<td>E-mail:</td>
    <td colspan="5">
   	 <input name="email" type="text" id="email" size="35" />
    </td>
  </tr>
  
</table>
<table cellpadding="0" cellspacing="1" class="secao none">
  <tr>
    <td colspan="4" class="secao_pai" style="border-top:1px solid #777;" class="none">DADOS DA FAMÍLIA</td>
  </tr>
  <tr>
	<td class="secao">Filiação - Pai:</td>
	<td>
    <input name="pai" type="text" id="pai" size="45" 
           onChange="this.value=this.value.toUpperCase()" />
    </td>
    <td class="secao">Nacionalidade Pai:</td>
    <td>
      <input name="nacionalidade_pai" type="text" id="nacionalidade_pai" size="15" 
             onChange="this.value=this.value.toUpperCase()"/>
    </td>
  </tr>
  <tr>
    <td class="secao">Filiação - Mãe:</td>
    <td><input name="mae" type="text" id="mae" size="45" 
               onChange="this.value=this.value.toUpperCase()"/></td>
    <td class="secao">Nacionalidade Mãe:</td>
    <td><input name="nacionalidade_mae" type="text" id="nacionalidade_mae" size="15" 
               onChange="this.value=this.value.toUpperCase()"/></td>
  </tr>
  <tr>
    <td class="secao">Número de Filhos:</td>
    <td colspan="3">
    <input name="filhos" type="text" id="filhos" size="2" />
	</td>
  </tr>
  <tr>
    <td class="secao">Nome:</td>
    <td>
    <input name="filho_1" type="text" id="filho_1" size="50" 
           onChange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">Nascimento:</td>
    <td>
	<input name="data_filho_1" type="text" size="12" maxlength="10" id="data_filho_1"
       	   onKeyUp="mascara_data(this); pula(10,this.id,filho_2.id)"
           onChange="this.value=this.value.toUpperCase()" />
	</td>
  </tr>
  <tr>
    <td class="secao">Nome:</td>
    <td>
    <input name="filho_2" type="text" id="filho_2" size="50" 
           onChange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">Nascimento:</td>
    <td>
    <input name="data_filho_2" type="text" size="12" maxlength="10" id="data_filho_2"
           onKeyUp="mascara_data(this); pula(10,this.id,filho_3.id)"
           onChange="this.value=this.value.toUpperCase()"/>
    </td>
  </tr>
  <tr>
    <td class="secao">Nome:</td>
    <td>
    <input name="filho_3" type="text" id="filho_3" size="50" 
           onChange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">Nascimento:</td>
    <td>
    <input name="data_filho_3" type="text" size="12" maxlength="10" id="data_filho_3"
           onKeyUp="mascara_data(this); pula(10,this.id,filho_4.id)"
           onChange="this.value=this.value.toUpperCase()" />
    </td>
    </tr>
    <tr>
    <td class="secao">Nome:</td>
    <td>
    <input name="filho_4" type="text" id="filho_4" size="50" 
           onChange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">Nascimento:</td>
    <td><input name="data_filho_4" type="text" size="12" maxlength="10" id="data_filho_4"
               onKeyUp="mascara_data(this); pula(10,this.id,filho_5.id)"
               onChange="this.value=this.value.toUpperCase()"/>
    </td>
  </tr>
  <tr>
    <td class="secao">Nome:</td>
    <td><input name="filho_5" type="text" id="filho_5" size="50" 
               onChange="this.value=this.value.toUpperCase()"/>
    </td>
    <td class="secao">Nascimento:</td>
    <td><input name="data_filho_5" type="text" size="12" maxlength="10" id="data_filho_5"
               onkeyup="mascara_data(this)"
               onChange="this.value=this.value.toUpperCase()"/>
    </td>
  </tr>
</table>
<table cellpadding="0" cellspacing="1" class="secao none" >
   <tr>
	  <td colspan="6" class="secao_pai">APARÊNCIA</td>
   </tr>
  <tr>
    <td class="secao">Cabelos:</td>
    <td>
      <select name="cabelos" id="cabelos">
      	<option value="">Não informado</option>
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
      	<option value="">Não informado</option>
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
      	  <option value="6">Não informado</option>
	  <?php $qr_etnias = mysql_query("SELECT * FROM etnias WHERE status = 'on' LIMIT 0,5");
    		while($etnia = mysql_fetch_assoc($qr_etnias)) { ?>
    	  <option value="<?=$etnia['id']?>"><?=$etnia['nome']?></option>
      <?php } ?>
      </select>
    </td>
    <td class="secao">Marcas ou Cicatriz:</td>
    <td>
      <input name="defeito" type="text" id="defeito" size="18" 
             onChange="this.value=this.value.toUpperCase()" />
    </td>
  </tr>
  <tr>
    <td class="secao">Deficiências:</td>
    <td colspan="7">
      <select name="deficiencia">
        <option value="">Não é portador de deficiência</option>
        <?php $qr_deficiencias = mysql_query("SELECT * FROM deficiencias WHERE status = 'on'");
				  while($deficiencia = mysql_fetch_assoc($qr_deficiencias)) { ?>
        <option value="<?=$deficiencia['id']?>"><?=$deficiencia['nome']?></option>
        <?php } ?>
        </select>
    </td>
    </tr>
  <tr>
    <td class="secao">Enviar Foto:</td>
    <td colspan="5"><input name="foto" type="checkbox" id="foto" onClick="document.all.arquivo.style.display = (document.all.arquivo.style.display == 'none') ? '' : 'none' ;" value="1" />
        &nbsp;
        <input name="arquivo" type="file" id="arquivo" size="60" style="display:none;" /></td>
  </tr>
</table>
<table cellpadding="0" cellspacing="1" class="secao">
  <tr>
    <td colspan="8" class="secao_pai">DOCUMENTAÇÃO</td>
  </tr>
  <tr >
    <td class="secao">Nº do RG:</td>
    <td>
	<input name="rg" type="text" id="rg" size="13" maxlength="14"
                OnKeyPress="formatar('##.###.###-###', this)" 
				onkeyup="pula(14,this.id,orgao.id)">
    </td>
    <td class="secao">Orgão Expedidor:</td>
    <td>
        <input name="orgao" type="text" id="orgao" size="8"
			   onChange="this.value=this.value.toUpperCase()">
    </td>
    <td class="secao">UF:</td>
    <td>
      <input name="uf_rg" type="text" id="uf_rg" size="2" maxlength="2" 
		     onKeyUp="pula(2,this.id,data_rg.id)"
             onChange="this.value=this.value.toUpperCase()">
    </td>
    <td class="secao">Data Expedição:</td>
    <td>
      <input name="data_rg" type="text" size="12" maxlength="10" id="data_rg"
		     onkeyup="mascara_data(this); pula(10,this.id,cpf.id)">
    </td>
  </tr>
  <tr>
    <td class="secao">CPF:</td>
    <td colspan="5">
      <input name="cpf" type="text" id="cpf" size="17" maxlength="14"
             OnKeyPress="formatar('###.###.###-##', this)" 
             onkeyup="pula(14,this.id,reservista.id)">
    </td>
    <td class="secao">Certificado de Reservista:</td>
    <td>
      <input name="reservista" type="text" id="reservista" size="18">
    </td>
  </tr>
  <tr class="none">
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
             onChange="this.value=this.value.toUpperCase()"></td>
    <td class="secao">Data carteira de Trabalho:</td>
    <td>
      <input name="data_ctps" type="text" size="12" maxlength="10" id="data_ctps"
		     onkeyup="mascara_data(this); pula(10,this.id,titulo2.id)">
    </td>
  </tr>
  <tr>
    <td class="secao">Nº Título de Eleitor:</td>
    <td>
        <input name="titulo" type="text" id="titulo2" size="10">
    </td>
    <td class="secao">Zona:</td>
    <td colspan="3">
        <input name="zona" type="text" id="zona2" size="3">
    </td>
    <td class="secao">Seção:</td>
    <td>
        <input name="secao" type="text" id="secao" size="3">
    </td>
  </tr>
  <tr>
    <td class="secao">PIS:</td>
    <td>
        <input name="pis" type="text" id="pis" size="12" maxlength="11" 
               onkeyup="pula(11,this.id,data_pis.id)">
    </td>
    <td class="secao">Data Pis:</td>
    <td colspan="3">
        <input name="data_pis" type="text" size="12" maxlength="10" id="data_pis"
			   onkeyup="mascara_data(this); pula(10,this.id,fgts.id)">
	</td>
    <td class="secao">FGTS:</td>
    <td>
        <input name="fgts" type="text" id="fgts" size="10">
    </td>
  </tr>
</table>
<table cellpadding="0" cellspacing="1" class="secao" style="display:none;">
  <tr>
    <td colspan="8" class="secao_pai" >BENEFÍCIOS</td>
  </tr>
  <tr>
    <td class="secao">Assistência Médica:</td>
    <td colspan="2">
      <label><input type="radio" name="medica" value="1"> Sim</label>
      <label><input type="radio" name="medica" value="0" checked="checked"> Não</label>
    </td>
    <td class="secao">Tipo de Plano:</td>
    <td colspan="2">
	  <select name="plano_medico" id="plano_medico">
        <option value="1">Familiar</option>
        <option value="2" selected>Individual</option>
	  </select>
    </td>
  </tr>
  <tr>
    <td class="secao">Seguro, Apólice:</td>
    <td colspan="2">
       <select name="apolice" id="apolice">
		  <option value="0">Não Possui</option>
			<?php $result_ap = mysql_query("SELECT * FROM apolice where id_regiao = $id_regiao");
                  while($row_ap = mysql_fetch_array($result_ap)) {
                      print "<option value='$row_ap[id_apolice]'>$row_ap[razao]</option>";
                  } ?>
		</select>
    </td>
    <td class="secao">Dependente:</td>
    <td colspan="2">
      <input name="dependente" type="text" id="dependente" size="20"
 			 onChange="this.value=this.value.toUpperCase()">
    </td>
  </tr>
  <tr>
    <td class="secao">Insalubridade:</td>
    <td colspan="2">
      <input name="insalubridade" type="checkbox" id="insalubridade2" value="1"/>
    </td>
	<td class="secao">Adicional Noturno:</td>
    <td colspan="2">
    <label><input type="radio" name="ad_noturno" value="1"> Sim</label>
    <label><input type="radio" name="ad_noturno" value="0" checked> N&atilde;o</label></td>
  </tr>
  <tr>
    <td class="secao">Integrante do CIPA:</td>
    <td colspan="5">
    <label>
      <input type="radio" name="cipa" value="1"> Sim
    </label>
    <label>
      <input type="radio" name="cipa" value="0" checked> N&atilde;o
    </label></td>
    </tr>
  <tr>
    <td class="secao">Vale Transporte:</td>
    <td colspan="2">
    	<input name="transporte" type="checkbox" id="transporte2" value="1"/>
    </td>
    <td class="secao">Tipo de Vale:</td>
    <td colspan="2">
    <select name="tipo_vale">
      <option value="1">Cart&atilde;o</option>
      <option value="2">Papel</option>
      <option value="3">Ambos</option>
    </select>
    </td>
  </tr>
  <tr>
    <td class="secao">Cartão 1:</td>
    <td>
      <input name="num_cartao" type="text" id="num_cartao" size="12">
    </td>
    <td class="secao">Valor Total 1:</td>
    <td>
      <input name="valor_cartao" type="text" id="valor_cartao" size="12" 
             onkeydown="FormataValor(this,event,20,2)" />
    </td>
    <td class="secao">Tipo Cartão 1:</td>
    <td>
      <input name="tipo_cartao_1" type="text" id="tipo_cartao_1" size="12" 
	         onChange="this.value=this.value.toUpperCase()"/></td>
  </tr>
  <tr>
    <td class="secao">Cartão 2:</td>
    <td>
      <input name="num_cartao2" type="text" id="num_cartao2" size="12" />
    </td>
    <td class="secao">Valor Total 2:</td>
    <td>
    <input name="valor_cartao2" type="text" id="valor_cartao2" size="12" 
           onkeydown="FormataValor(this,event,20,2)" />
    </td>
    <td class="secao">Tipo Cartão 2:</td>
    <td>
    <input name="tipo_cartao_2" type="text" id="tipo_cartao_2" size="12" 
           onChange="this.value=this.value.toUpperCase()" />
    </td>
  </tr>
  <tr>
    <td class="secao">(Papel) Quantidade 1: </td>
    <td>
    <input name="vale_qnt_1" type="text" id="vale_qnt_1" size="3"/>
    </td>
    <td class="secao">Valor 1:</td>
    <td>
    <input name="vale_valor_1" type="text" id="vale_valor_1" size="12" 
           onkeydown="FormataValor(this,event,20,2)" /></td>
    <td class="secao">Tipo Vale 1:</td>
    <td>
    <input name="tipo1" type="text" id="tipo1" size="12" 
           onChange="this.value=this.value.toUpperCase()"/></td>
  </tr>
  <tr>
    <td class="secao">Quantidade 2:</td>
    <td>
      <input name="vale_qnt_2" type="text" id="vale_qnt_2" size="3" /></td>
    <td class="secao">Valor 2:</td>
    <td>
    <input name="vale_valor_2" type="text" id="vale_valor_2" size="12" 
           onkeydown="FormataValor(this,event,20,2)"></td>
    <td class="secao">Tipo Vale 2:</td>
    <td>
    <input name="tipo2" type="text" id="tipo2" size="12" 
           onChange="this.value=this.value.toUpperCase()">
    </td>
  </tr>
  <tr>
    <td class="secao">Quantidade 3:</td>
    <td>
      <input name="vale_qnt_3" type="text" id="vale_qnt_3" size="3"></td>
    <td class="secao">Valor 3:</td>
    <td>
    <input name="vale_valor_3" type="text" id="vale_valor_3" size="12" 
           onkeydown="FormataValor(this,event,20,2)"></td>
    <td class="secao">Tipo Vale 3:</td>
    <td>
    <input name="tipo3" type="text" id="tipo3" size="12" 
           onChange="this.value=this.value.toUpperCase()"></td>
  </tr>
  <tr>
    <td class="secao">Quantidade 4:</td>
    <td>
      <input name="vale_qnt_4" type="text" id="vale_qnt_4" size="3">
    </td>
    <td class="secao">Valor 4:</td>
    <td>
      <input name="vale_valor_4" type="text" id="vale_valor_4" size="12" 
             onkeydown="FormataValor(this,event,20,2)">
    </td>
    <td class="secao">Tipo Vale 4:</td>
    <td>
      <input name="tipo4" type="text" id="tipo4" size="12" 
             onChange="this.value=this.value.toUpperCase()">
    </td>
  </tr>
</table>
<table cellpadding="0" cellspacing="1" class="secao">
  <tr>
    <td colspan="4" class="secao_pai">DADOS BANCÁRIOS</td>
  </tr>
  <tr>
	<td class="secao" width="15%">Banco:</td>
	<td width="30%">
	  <select name="banco" id="banco">
	    <option value="0">Sem Banco</option>
		<?php $qr_banco = mysql_query("SELECT * FROM bancos WHERE id_projeto = '$projeto' AND status_reg = '1'");
        	  while($row_banco = mysql_fetch_array($qr_banco)) {
            	  print "<option value='$row_banco[0]'>$row_banco[id_banco] - $row_banco[nome]</option>";
        	  } ?>
		<option value="9999">Outro Banco</option>
	  </select>
    </td>
	<td class="secao" width="25%">Agência:</td>
	<td width="30%">
    	<input name="agencia" type="text" id="agencia" size="12"/>
    </td>
  </tr>
  <tr>
    <td class="secao">Conta:</td>
    <td>
		<input name="conta" type="text" id="conta" size="12"><br>
        <label><input type="radio" name="radio_tipo_conta" value="salario">Conta Salário </label>
	    <label><input type="radio" name="radio_tipo_conta" value="corrente">Conta Corrente </label>
    </td>
    <td class="secao">Nome do Banco:<br />(caso não esteja na lista acima)</td>
	<td>
		<input name="nome_banco" type="text" id="nome_banco" size="30" class="campotexto" />
    </td>
  </tr>
</table>
<table cellpadding="0" cellspacing="1" class="secao" >
  <tr>
    <td colspan="6" class="secao_pai">DADOS FINANCEIROS E DE CONTRATO</td>
  </tr>
  <tr>
    <td class="secao">Data de Entrada:</td>
    <td>
      <input name="data_entrada" type="text" size="12" maxlength="10" id="data_entrada"
             onkeyup="mascara_data(this); pula(10,this.id,data_exame.id)">
    </td>
    <td class="secao">Data do Exame Admissional:</td>
    <td>
	  <input name="data_exame" type="text" size="12" maxlength="10" id="data_exame"
			 onkeyup="mascara_data(this); pula(10,this.id,localpagamento.id)">
    </td>
  </tr>
  <tr>
	<td class="secao">Local de Pagamento:</td>
	<td colspan="3">
	  <input name="localpagamento" type="text" id="localpagamento" size="25"  
         	   onChange="this.value=this.value.toUpperCase()"/>
	</td>
  </tr>
  <tr>
    <td class="secao">Tipo de Pagamento:</td>
    <td colspan="3">
    <select name="tipopg" id="tipopg">
    <?php $RE_pg_dep = mysql_query("SELECT id_tipopg FROM tipopg WHERE id_projeto = '$projeto' AND campo1 = '1'");
    	  $Row_pg_dep = mysql_fetch_array($RE_pg_dep);
    
    	  $RE_pg_che = mysql_query("SELECT id_tipopg FROM tipopg WHERE id_projeto = '$projeto' AND campo1 = '2'");
    	  $Row_pg_che = mysql_fetch_array($RE_pg_che);
	
   		  $result_pg = mysql_query("SELECT * FROM tipopg WHERE id_projeto = '$projeto'");
          while($row_pg = mysql_fetch_array($result_pg)) {
    		  print "<option value='$row_pg[id_tipopg]'>$row_pg[tipopg]</option>";
    	  } ?>
    </select>
    </td>
  </tr>  
  <tr>
    <td class="secao">Observações:</td>
    <td colspan="3">
      <textarea name="observacoes" id="observacoes" cols="55" rows="4"  
     		    onChange="this.value=this.value.toUpperCase()"></textarea>
    </td>
    </tr>
</table>
<div id="finalizacao"> 
O Contrato foi <strong>assinado</strong>?
<input name="impressos2" type="checkbox" id="impressos2" value="1" />
<p>&nbsp;</p>
O Distrato foi <strong>assinado</strong>?<br> 
<label><input type="radio" id="assinatura3" name="assinatura3" value="1" <?php echo $selected_ass_sim2; ?> > Sim </label>
<label><input type="radio" id="assinatura3" name="assinatura3" value="0" <?php echo $selected_ass_nao2;?> > Não</label>
<p>&nbsp;</p>
Outros documentos foram <strong>assinados</strong>?<br> 
<label><input type="radio" id="assinatura" name="assinatura" value="1" <?=$selected_ass_sim3?>> Sim</label>
<label><input type="radio" id="assinatura" name="assinatura" value="0" <?=$selected_ass_nao3?>> Não</label>
<?=$mensagem_ass?>
</div>
<div id="observacao">NÃO DEIXE DE CONFERIR OS DADOS APÓS A DIGITAÇÃO</div>
<div align="center"><input type="submit" name="Submit" value="CADASTRAR" class="botao" /></div> 
<input type="hidden" name="regiao" value="<?=$id_regiao?>"/>
<input type="hidden" name="id_projeto" value="<?=$projeto?>">
<input type="hidden" name="user" value="<?=$id_user?>">
<input type="hidden" name="update" value="1" />
</form>
</td>
</tr>
</table>
</div>
<script language="javascript">
function validaForm() {
	
d = document.form1;
deposito = "<?=$Row_pg_dep[0]?>";
cheque = "<?=$Row_pg_che[0]?>";

if (d.locacao.value == "") {
	alert("O campo Unidade deve ser preenchido!");
	d.locacao.focus();
	return false;
}
if (d.nome.value == "") {
	alert("O campo Nome deve ser preenchido!");
	d.nome.focus();
	return false;
}
/*
if (d.endereco.value == "") {
	alert("O campo Endereço deve ser preenchido!");
	d.endereco.focus();
	return false;
}
if (d.data_nasci.value == "") {
	alert("O campo Data de Nascimento deve ser preenchido!");
	d.data_nasci.focus();
	return false;
}*/
if (d.rg.value == "") {
	alert("O campo RG deve ser preenchido!");
	d.rg.focus();
	return false;
}
if (d.cpf.value == "") {
	alert("O campo CPF deve ser preenchido!");
	d.cpf.focus();
	return false;
}
/*
if(document.getElementById("tipopg").value == deposito) {
	if (document.getElementById("banco").value == 0) {
		alert("Selecione um banco!");
		return false;
	}
	
	if (d.agencia.value == "") {
		alert("O campo Agencia deve ser preenchido!");
		d.agencia.focus();
		return false;
	}
	
	if (d.conta.value == "") {
		alert("O campo Conta deve ser preenchido!");
		d.conta.focus();
		return false;
	}
	
}
    
if(document.getElementById("tipopg").value == cheque) {
	
	if (document.getElementById("banco").value != 0) {
		alert("Para pagamentos em cheque deve selecionar SEM BANCO!");
		return false;
	}
	d.agencia.value = "";
	d.conta.value = "";
	
	
}
if (d.localpagamento.value == "") {
alert("O campo Local de Pagamento deve ser preenchido!");
d.localpagamento.focus();
return false;
}
return true;
}
*/

$(function() {
	$('#data_nasci').datepicker({
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
        var cpf = $('#cpf').val().replace('.','').replace('.','').replace('-','');             
            
            if(!VerificaCPF(cpf)){
               alert('Cpf Inválido');
                return false;
            }
});
};
</script>
</body>
</html>
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

// CADASTRO DE AUTÔNOMO
$regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['id_projeto'];
$user = $_REQUEST['user'];
// Dados de Contratação
$tipo_contratacao = $_REQUEST['contratacao'];
$id_curso = $_REQUEST['idcurso'];
$locacao = $_REQUEST['locacao'];
$cooperativa = '0';
// Dados Pessoais
$nome = mysql_real_escape_string($_REQUEST['nome']);
$sexo = $_REQUEST['sexo'];
$endereco =mysql_real_escape_string( $_REQUEST['endereco']);
$bairro = mysql_real_escape_string($_REQUEST['bairro']);
$cidade = mysql_real_escape_string($_REQUEST['cidade']);
$uf = $_REQUEST['uf'];
$cep = $_REQUEST['cep'];
$tel_fixo = $_REQUEST['tel_fixo'];
$tel_cel = $_REQUEST['tel_cel'];
$tel_rec = $_REQUEST['tel_rec'];
$data_nasci = $_REQUEST['data_nasci'];
$naturalidade = $_REQUEST['naturalidade'];
$nacionalidade = $_REQUEST['nacionalidade'];
$civil = $_REQUEST['civil'];
$tipo_sanguineo = $_REQUEST['tiposanguineo'];
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
$nome_banco = $_REQUEST['nome_banco'];
$pis = $_REQUEST['pis'];
$fgts = $_REQUEST['fgts'];
$tipopg = $_REQUEST['tipopg'];
$filhos = $_REQUEST['filhos'];
$observacoes = $_REQUEST['observacoes'];
$medica = $_REQUEST['medica'];
$assinatura2 = $_REQUEST['assinatura2'];
$assinatura3 = $_REQUEST['assinatura3'];
if(empty($_REQUEST['insalubridade'])) {
   $insalubridade = '0';
} else {
   $insalubridade = $_REQUEST['insalubridade'];
}
if(empty($_REQUEST['transporte'])) {
   $transporte = '0';
} else {
   $transporte = $_REQUEST['transporte'];
}
if(empty($_REQUEST['impressos2'])){
   $impressos = '0';
} else {
   $impressos = $_REQUEST['impressos2'];
}
$plano_medico = $_REQUEST['plano_medico'];
$serie_ctps = $_REQUEST['serie_ctps'];
$uf_ctps = $_REQUEST['uf_ctps'];
$pis_data = $_REQUEST['data_pis'];
$tipo_vale = $_REQUEST['tipo_vale'];
$num_cartao = $_REQUEST['num_cartao'];
$valor_cartao = $_REQUEST['valor_cartao'];
$tipo_cartao_1 = $_REQUEST['tipo_cartao_1'];
$num_cartao2 = $_REQUEST['num_cartao2'];
$valor_cartao2 = $_REQUEST['valor_cartao2'];
$tipo_cartao_2 = $_REQUEST['tipo_cartao_2'];
$vale_qnt_1 = $_REQUEST['vale_qnt_1'];
$vale_valor_1 = $_REQUEST['vale_valor_1'];
$tipo1 = $_REQUEST['tipo1'];
$vale_qnt_2 = $_REQUEST['vale_qnt_2'];
$vale_valor_2 = $_REQUEST['vale_valor_2'];
$tipo2 = $_REQUEST['tipo2'];
$vale_qnt_3 = $_REQUEST['vale_qnt_3'];
$vale_valor_3 = $_REQUEST['vale_valor_3'];
$tipo3 = $_REQUEST['tipo3'];
$vale_qnt_4 = $_REQUEST['vale_qnt_4'];
$vale_valor_4 = $_REQUEST['vale_valor_4'];
$tipo4 = $_REQUEST['tipo4'];
$ad_noturno = $_REQUEST['ad_noturno'];
$exame_data = $_REQUEST['data_exame'];
$trabalho_data = $_REQUEST['data_ctps'];
$reservista = $_REQUEST['reservista'];
$cabelos = $_REQUEST['cabelos'];
$peso = $_REQUEST['peso'];
$altura = $_REQUEST['altura'];
$olhos = $_REQUEST['olhos'];
$defeito = $_REQUEST['defeito'];
$deficiencia = $_REQUEST['deficiencia'];
$cipa = $_REQUEST['cipa'];
$etnia = $_REQUEST['etnia'];
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

$email = $_POST['email'];

//Inicio Verificador CPF
$qrCpf = mysql_query("SELECT COUNT(id_autonomo) AS total FROM autonomo WHERE cpf = '$cpf' AND id_projeto = '$id_projeto' AND id_regiao = '$regiao' AND tipo_contratacao = 1");
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
    <?php
        exit();
    }
//Fim verificador PIS

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
$data_nasci    = ConverteData($data_nasci);
$data_rg       = ConverteData($data_rg);
$data_escola   = ConverteData($data_escola);
$data_entrada  = ConverteData($data_entrada);
$pis_data      = ConverteData($pis_data);
$exame_data    = ConverteData($exame_data);
$trabalho_data = ConverteData($trabalho_data);
// VERIFICANDO SE O FUNCIONÁRIO JA ESTÁ CADASTRADO NA TABELA AUTONOMO
$qr_verificando_autonomo = mysql_query("SELECT nome FROM autonomo where nome = '$nome' AND data_nasci = '$data_nasci' AND rg = '$rg' AND status = '1'");
$verificando_autonomo = mysql_num_rows($qr_verificando_autonomo);
if (!empty($verificando_autonomo)) {
print "
<html>
<head>
<title>:: Intranet ::</title>
</head>
<body>
ESTE PARTICIPANTE JA ESTÁ CADASTRADO: <b>$nome</b>
</body>
</html>
";
exit; 
} else { // CASO O FUNCIONÁRIO NÃO ESTEJA CADASTRADO VAI RODAR O INSERT
$result_projeto = mysql_query("SELECT * FROM projeto where id_projeto = '$id_projeto'");
$row_projeto = mysql_fetch_array($result_projeto);
$data_cadastro = date('Y-m-d');
$civil = explode('|', $civil);
$estCivilId = $civil[0];
$estCivilNome = $civil[1];

mysql_query ("INSERT INTO autonomo
(id_projeto, id_regiao, localpagamento, locacao, nome, sexo, 
 endereco, bairro, cidade, uf, cep, 
 tel_fixo, tel_cel, tel_rec, data_nasci, naturalidade, nacionalidade, 
 civil, rg, orgao, data_rg, cpf, titulo, zona, secao,
 pai, nacionalidade_pai, mae, nacionalidade_mae, 
 estuda, data_escola, escolaridade, instituicao, curso, 
 tipo_contratacao, banco, agencia, conta, tipo_conta, 
 id_curso, apolice, data_entrada, campo1, campo2, campo3, data_exame, 
 reservista, etnia, cabelos, altura, olhos, peso, defeito, deficiencia, 
 cipa, ad_noturno, plano, assinatura, distrato, outros,
 pis, dada_pis, data_ctps, serie_ctps, uf_ctps, uf_rg, fgts,
 insalubridade, transporte, medica, tipo_pagamento,  
 nome_banco, num_filhos, observacao, impressos, sis_user, data_cad, foto, id_cooperativa, 
 rh_vinculo, rh_status, rh_horario, rh_sindicato, rh_cbo, email, tipo_sanguineo, id_estado_civil)
    VALUES
('$id_projeto', '$regiao', '$localpagamento', '$locacao', '$nome', '$sexo', 
 '$endereco', '$bairro', '$cidade', '$uf', '$cep',
 '$tel_fixo', '$tel_cel', '$tel_rec', '$data_nasci', '$naturalidade', '$nacionalidade',
 '$estCivilNome', '$rg', '$orgao', '$data_rg', '$cpf', '$titulo', '$zona', '$secao', 
 '$pai', '$nacionalidade_pai', '$mae', '$nacionalidade_mae', 
 '$estuda', '$data_escola', '$escolaridade', '$instituicao', '$curso',
 '$tipo_contratacao', '$banco', '$agencia', '$conta', '$tipoDeConta',
 '$id_curso', '$apolice', '$data_entrada', '$campo1', '$campo2', '$campo3', '$exame_data',
 '$reservista', '$etnia', '$cabelos', '$altura', '$olhos', '$peso', '$defeito', '$deficiencia', 
 '$cipa', '$ad_noturno', '$plano_medico', '$impressos', '$assinatura2', '$assinatura3',
 '$pis', '$pis_data', '$trabalho_data', '$serie_ctps', '$uf_ctps', '$uf_rg', '$fgts', 
 '$insalubridade', '$transporte', '$medica', '$tipopg',
 '$nome_banco', '$filhos', '$observacoes', '$impressos', '$user', '$data_cadastro', '$foto_banco', '$cooperativa',
 '$rh_vinculo', '$rh_status', '$rh_horario', '$rh_sindicato', '$rh_cbo', '$email', '$tipo_sanguineo', '$estCivilId')") or die (mysql_error());
 $row_id_participante = mysql_insert_id();
}
// Vale Transporte
if($transporte == '1') {
mysql_query ("INSERT INTO vale 
			 (id_regiao,id_projeto,id_bolsista,nome,cpf,tipo_vale,
numero_cartao,valor_cartao,quantidade,qnt1,valor1,qnt2,valor2,qnt3,valor3,qnt4,valor4,tipo1,tipo2,tipo3,tipo4,
tipo_cartao_1,tipo_cartao_2,numero_cartao2,valor_cartao2,status_vale) 
			  VALUES 
			  ('$regiao','$id_projeto','$row_id_participante','$nome','$cpf','$tipo_vale','$num_cartao','$valor_cartao',
'','$vale_qnt_1','$vale_valor_1','$vale_qnt_2','$vale_valor_2','$vale_qnt_3','$vale_valor_3',
'$vale_qnt_4','$vale_valor_4','$tipo1','$tipo2','$tipo3','$tipo4','$tipo_cartao_1','$tipo_cartao_2','$num_cartao2',
'$valor_cartao2','$transporte')") 
    or die (mysql_error());
}
//
// Dependentes
if(!empty($filhos)) {
	mysql_query("INSERT INTO dependentes (id_regiao, id_projeto, id_bolsista, contratacao, nome, data1, nome1, data2, nome2, data3, nome3, data4, nome4, data5, nome5) VALUES ('$regiao', '$id_projeto', '$row_id_participante', '$tipo_contratacao', '$nome', '$data_filho_1', '$filho_1', '$data_filho_2', '$filho_2', '$data_filho_3', '$filho_3', '$data_filho_4', '$filho_4', '$data_filho_5', '$filho_5')") or die(mysql_error());
}
//
// TV SORRINDO (Senha Aleatória)
	$n_id_curso = sprintf("%04d", $id_curso);
	$n_regiao = sprintf("%04d", $regiao);
	$n_id_bolsista = sprintf("%04d", $row_id_participante);
	
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
mysql_query ("INSERT INTO tvsorrindo(id_bolsista,id_projeto,nome,cpf,matricula,senha,inicio) VALUES
('$row_id_participante','$id_projeto','$nome','$cpf','$matricula','$senha','$inicio')") or die (mysql_error());
//
// Upload da Foto
$arquivo = isset($_FILES['arquivo']) ? $_FILES['arquivo'] : FALSE;
if($foto_up == "1") {
	if(!$arquivo) {
    	$mensagem = "Não acesse esse arquivo diretamente!";
	} else {
		$nome_arq = str_replace(" ", "_", $nome);	
		$tipo_arquivo = ".gif";
		$diretorio = "fotos/";
		$nome_tmp = $regiao."_".$id_projeto."_".$row_id_participante.$tipo_arquivo;
		$nome_arquivo = "$diretorio$nome_tmp" ;
		
		move_uploaded_file($arquivo['tmp_name'], $nome_arquivo) or die ("Erro ao enviar o Arquivo: $nome_arquivo");
	}
}
//
header("Location: ver_bolsista.php?reg=$regiao&bol=$row_id_participante&pro=$id_projeto&sucesso=cadastro");
exit;
} ?>