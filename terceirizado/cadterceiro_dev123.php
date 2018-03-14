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
$resut_maior = mysql_query ("SELECT CAST(campo3 AS UNSIGNED) campo3, MAX(campo3) FROM terceirizado WHERE id_regiao= '$id_regiao' AND id_projeto ='$projeto' AND campo3 != 'INSERIR' GROUP BY campo3 DESC LIMIT 0,1");
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
           CADASTRAR <span class="aut">TERCEIRIZADO</span>
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
  <tr>
    <td width="25%" class="secao">Projeto:</td>
    <td width="75%"><?=$RowPro['0']." - ".$RowPro['nome']?></td>
  </tr>
  <tr>
    <td class="secao">Atividade:</td>
    <td>
    <?php $result_curso = mysql_query("SELECT * FROM curso WHERE campo3 = '$projeto' AND tipo = '3' AND id_regiao = '$id_regiao' ORDER BY campo3 ASC");
	      $verifica_curso = mysql_num_rows($result_curso);
		  if(!empty($verifica_curso)) {
			       print "<select name='atividade' id='atividade'>";
			  while($row_curso = mysql_fetch_array($result_curso)) {
				   print "<option value='$row_curso[0]'>$row_curso[0] - {$row_curso['nome']}</option>";
			  }
			       print "</select>";
		  } else {
			   print "Nenhuma Atividade Cadastrada";
		  } ?>
    </td>
  </tr>
  <tr>
	<td class="secao">Prestador:</td>
    <td>
        <?php $result_prestador = mysql_query("SELECT * FROM prestadorservico WHERE id_regiao = '$id_regiao' and id_projeto = '$projeto' and prestador_tipo = 9");
		      $verifica_prestador = mysql_num_rows($result_prestador);
			  if(!empty($verifica_prestador)) {
				        print "<select name='id_prestador' id='id_prestador'>";
				  while($row_prestador = mysql_fetch_array($result_prestador)) {
						print "<option value='{$row_prestador['id_prestador']}'>{$row_prestador['id_prestador']} - {$row_prestador['c_fantasia']}</option>";
				  }
				        print "</select>";
			  } else {
				  print "Nenhuma Prestador Cadastrado";
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
	               print "<option value='$row_unidade[id_unidade]'>$row_unidade[id_unidade] - $row_unidade[unidade]</option>";
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
	<input name="data_nasci" type="text" id="data_nasci" size="15" maxlength="10" onkeyup="mascara_data(this);"/>
    </td>
  </tr>
  <tr>
    <td class="secao">Sexo:</td>
    <td><label><input name="sexo" type="radio" class="reset" value="M" checked="checked" /> Masculino</label><br />
        <label><input name="sexo" type="radio" class="reset" value="F" /> Feminino</label>
    </td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
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
  <td></td><td></td>
   </tr>
   <tr>
  <td class="secao">UF:</td>
  <td>
     <?php $ajax = 'onchange="ajaxuf(\'uf\',\'dvcidade\');"';
	       $REG -> SelectUFajax('uf',$ajax); ?>
  </td>
  <td class="secao">Cidade:</td>
  <td>
	 <div align="left" id="dvcidade">    <input name="cidade" type="text" id="cidade" size="16" 
		   onchange="this.value=this.value.toUpperCase()"/>
	 </div>
  </td>
  <td class="secao">CEP:</td>
  <td>
	<input name="cep" type="text" id="cep" size="10" maxlength="9"
		   onkeypress="formatar('#####-###', this)" 
		   onkeyup="pula(9,this.id,naturalidade.id)" />
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
   <td></td>
   <td></td>
  </tr>
  <tr>
    <td class="secao">E-mail:</td>
    <td>
    	<input name="email" type="text" id="email" size="35" />
    </td>
    <td class="secao">Data de Entrada:</td>
    <td><input name="data_entrada" type="text" id="data_entrada" size="15" maxlength="10" onkeyup="mascara_data(this);"/></td>
    <td class="secao">Data Saída:</td>
    <td><input name="data_saida" type="text" id="data_saida" size="15" maxlength="10" onkeyup="mascara_data(this);"/></td>
  </tr>
    <tr>
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
    <td class="secao">PIS:</td>
    <td>
        <input name="pis" type="text" id="pis" size="12" maxlength="11"
               onkeyup="pula(11,this.id,data_pis.id)"/>
    </td>
    <td></td>
    <td colspan="3">
    </td>
    <td></td>
    <td>
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
	<input name="hora_retirada" type="text" id="hora_retirada" size="13" maxlength="8"
           OnKeyPress="formatar('##:##:##', this)" />
    </td>
    <td class="secao">Hora Almoço:</td>
    <td>
	<input name="hora_almoco" type="text" id="hora_almoco" size="13" maxlength="8"
           OnKeyPress="formatar('##:##:##', this)" />
    </td>
    <td class="secao">Hora Retorno</td>
    <td>
	<input name="hora_retorno" type="text" id="hora_retorno" size="13" maxlength="8"
           OnKeyPress="formatar('##:##:##', this)" />
    </td>
    <td class="secao">Hora Saída:</td>
    <td>
	<input name="hora_saida" type="text" id="hora_saida" size="13" maxlength="8"
           OnKeyPress="formatar('##:##:##', this)" />
    </td>
  </tr>

</table>    
   
    
<table cellpadding="0" cellspacing="1" class="secao">
  <tr>
    <td class="secao_pai" colspan="8">Foto</td>
  </tr>
  <tr>
    <td class="secao">Arquivo</td>
    <td>
	<input type="file" name="arquivo" id="arquivo" />
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
	$('#data_exame').datepicker({
		changeMonth: true,
	    changeYear: true
	});
});
</script>
<?php } else {
    
    // CADASTRO DE COOPERADO
    $regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    //DADOS CONTRATAÇÃO
    $vinculo = $_REQUEST['vinculo'];
    $id_curso = $_REQUEST['atividade'];
    $locacao = $_REQUEST['locacao'];
    $tipo_contratacao = $_REQUEST['contratacao'];
    $contrato_medico = ($_POST['contrato_medico']) ? $_POST['contrato_medico'] : '0';
    
    $matricula = $_POST['matricula'];
    $n_processo = $_POST['n_processo'];

    //DADOS CADASTRAIS
    $nome = mysql_real_escape_string($_REQUEST['nome']);
    $sexo = $_REQUEST['sexo'];
    $id_prestador = $_REQUEST['id_prestador'];
    
    
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
    //Horário
    $hora_retirada = $_REQUEST['hora_retirada'];
    $hora_almoco = $_REQUEST['hora_almoco'];
    $hora_retorno = $_REQUEST['hora_retorno'];
    $hora_saida = $_REQUEST['hora_saida'];
    $pis = $_REQUEST['pis'];

    $email = $_POST['email'];


//Inicio Verificador CPF
//$qrCpf = mysql_query("SELECT COUNT(id_terceirizado) AS total FROM terceirizado WHERE cpf = '$cpf' AND id_projeto = '$id_projeto' AND tipo_contratacao = '$tipo_contratacao'");
$qrCpf = mysql_query("SELECT COUNT(id_terceirizado) AS total FROM terceirizado WHERE cpf = '$cpf' AND id_projeto = '$id_projeto'");
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
    if(strlen($pis) != 11)
    {
    ?>
        <script type="text/javascript">
            //alert("PIS Inválido!");
            //window.history.back();
        </script>
    <?php
        //exit();
    }
//Fim verificador PIS

 
/* 
Função para converter a data
De formato nacional para formato americano.
Muito útil para você inserir data no mysql e visualizar depois data do mysql.
*/

function ConverteData($Data)
{
    if (strstr($Data, "/"))//verifica se tem a barra /
    {
	$d = explode ("/", $Data);//tira a barra
	$rstData = "$d[2]-$d[1]-$d[0]";//separa as datas $d[2] = ano $d[1] = mes etc...
	return $rstData;
    }elseif(strstr($Data, "-"))
    {
	$d = explode ("-", $Data);
	$rstData = "$d[2]/$d[1]/$d[0]"; 
	return $rstData;
    }else{
	return "Data invalida";
    }
}


$data_nasci   = ConverteData($data_nasci);
$data_rg      = ConverteData($data_rg);
$pis_data     = ConverteData($pis_data);
$exame_data   = ConverteData($exame_data);
$trabalho_data = ConverteData($trabalho_data);
$c_nascimento = ConverteData($c_nascimento);
$e_dataemissao = ConverteData($e_dataemissao);
$data_emissao = ConverteData($data_emissao);
$data_entrada = ConverteData($_REQUEST['data_entrada']);
$data_saida = ConverteData($_REQUEST['data_saida']);


$data_cadastro = date('Y-m-d');
//VERIFICANDO SE O FUNCIONÁRIO JA ESTÁ CADASTRADO NA TABELA AUTÔNOMO
$verificando_cooperado = mysql_query("SELECT nome FROM terceirizado WHERE nome = '$nome' AND data_nasci = '$data_nasci' AND rg = '$rg' AND status = '1'");
$row_verificando_cooperado = mysql_num_rows($verificando_cooperado);

if (!empty($row_verificando_cooperado)) 
    {
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
} else 
{ 
    //------------------------------
    //FAZENDO O UPLOAD DA FOTO
    $arquivo = isset($_FILES['arquivo']) ? $_FILES['arquivo'] : FALSE;

//    var_dump($_FILES);

    $sql = "
	    INSERT INTO ispv_netsorrindo.terceirizado (id_regiao, id_projeto, id_unidade, 
			id_curso, id_prestador, nome, cpf, rg, carteira_conselho, 
			uf_conselho, endereco, numero, complemento, bairro, cidade, 
			carteira_conselho_emissao, uf, status, 
			data_cad, user_cad, data_alter, user_alter, 
			obs, data_nasci, pis, 
			sexo, tel_cel, tel_fixo, cep, email, orgao, data_rg, uf_rg, 
			hora_retirada, hora_almoco, hora_retorno, hora_saida, data_entrada, data_saida, contrato_medico) 
	    VALUES ('$regiao', '$id_projeto', '$locacao', '$id_curso', '$prestador_id', '$nome', '$cpf', '$rg', '$conselho', 
	    '$uf_conselho', '$endereco', '$numero', '$complemento', '$bairro', '$cidade', '$carteira_conselho_emissao', '$uf', '$status',
	    '$data_cadastro', '$user_cad', '$data_alter', '$user_alter',
	    '$obs', '$data_nasci', '$pis', '$sexo', '$tel_cel', 
	    '$tel_fixo', '$cep', '$email', '$orgao', '$data_rg', '$uf_rg',
	    '$hora_retirada', '$hora_almoco', '$hora_retorno', '$hora_saida', '$data_entrada', '$data_saida', '$contrato_medico');";
	
//    var_dump($prestador_id);
//    echo '<br>';
//    echo "sql = [$sql]<br>\n";
//    exit();
    
    
    mysql_query ($sql) or die ("Ops! Erro<br>" . mysql_error());
    $row_id_participante = mysql_insert_id();
    $row_id_clt = $row_id_participante;
    
    if(!$arquivo)
    {
	$mensagem = "Não acesse esse arquivo diretamente!";
    }else
    {
	// Imagem foi enviada, então a move para o diretório desejado
	echo "nome = [{$arquivo['tmp_name']}]<br>\n";
	if(trim($arquivo['tmp_name']) != "")
	{
	    $nome_arq = str_replace(" ", "_", $nome);
	    $tipo_arquivo = ".gif";
		// Resolvendo o nome e para onde o arquivo será movido
	    $diretorio = "../fotos/";
		$nome_tmp = $regiao."_".$id_projeto."_".$row_id_participante.$tipo_arquivo;
		$nome_arquivo = "$diretorio$nome_tmp" ;

		//echo "<br>\nnome_arquivo = [$nome_arquivo]<br>\n";

		move_uploaded_file($arquivo['tmp_name'], $nome_arquivo ) or die ("Erro ao enviar o Arquivo: $nome_arquivo");

		$sql = "update ispv_netsorrindo.terceirizado set foto = '$nome_arquivo' where id_terceirizado = $row_id_participante;";
		mysql_query ($sql) or die ("Ops! Erro<br>" . mysql_error());
	}
	    
    }    
} 
// AQUI TERMINA DE INSERIR OS DADOS DO COOPERADO


header("Location: ver_terceiro.php?reg=$regiao&id=$row_id_participante&pro=$id_projeto&sucesso=cadastro&tipo=$tipo_contratacao");
exit;
}
?>