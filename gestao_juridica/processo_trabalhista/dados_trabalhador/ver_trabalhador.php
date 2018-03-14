<?php
include ("../../include/restricoes.php");
include('../../../conn.php');
include('../../../upload/classes.php');
include('../../../classes/funcionario.php');
include('../../../classes/formato_data.php');
include('../../../classes/formato_valor.php');
include('../../../classes_permissoes/regioes.class.php');
include('../../../funcoes.php');

$Fun = new funcionario();
$Fun -> MostraUser(0);
$Master = $Fun -> id_master;
$REGIAO = new Regioes();
//PEGANDO O ID DO CADASTRO


$id_user = $_COOKIE['logado'];


////ANEXAR ANDAMENTOS 
if(isset($_POST['enviar']) and $_POST['tipo'] == 'andamentos'){
	


$id_processo      = mysql_real_escape_string($_POST['id_processo']);	
$data_movimento   = implode('-', array_reverse(explode('/',$_POST['data_movimento'])));
$status_processo  = mysql_real_escape_string($_POST['status_processo']);
$valor 		  	  =  str_replace('.','',$_POST['valor']);		
$array_data_parcela  = $_POST['data_parcela'];	
$n_parcelas		  = mysql_real_escape_string($_POST['n_parcelas']);
$horario 		  = $_POST['horario'];
$banco_id 		  = $_POST['banco'];
$array_valor 	  = str_replace(',','.',str_replace('.','',$_POST['valor_parcela']));
$id_regiao 		  = $_POST['regiao'];
$id_projeto 	  = $_POST['projeto'];
$data_pg = implode('-', array_reverse(explode('/',$_POST['data_pg'])));

$valor2 		  	  =  str_replace(',','.',str_replace('.','',$_POST['valor2']));		
$array_data_parcela2  = $_POST['data_parcela2'];	
$n_parcelas2		  = mysql_real_escape_string($_POST['n_parcelas2']);
$banco_id2 		  = $_POST['banco2'];
$array_valor2 	  = str_replace(',','.',str_replace('.','',$_POST['valor_parcela2']));
$id_regiao2 		  = $_POST['regiao2'];
$id_projeto2	  = $_POST['projeto2'];
$data_pg2 = implode('-', array_reverse(explode('/',$_POST['data_pg2'])));




$verifica_financeiro = mysql_num_rows(mysql_query("SELECT * FROM processo_status WHERE proc_status_id = '$status_processo' AND vinculo_financeiro = 1"));



if($verifica_financeiro == 0) {
	
	unset($valor, $data_pg, $n_parcelas);
}
if($verifica_financeiro == 0) {
	
	unset($valor2, $data_pg2, $n_parcelas2);
}
//
//echo "INSERT INTO proc_trab_andamento (proc_id, proc_status_id, andamento_data_movi, andamento_horario, andamento_valor,andamento_data_pg, andamento_parcelas,   andamento_data_cad, andamento_usuario_cad, andamento_status)
//							VALUES ('$id_processo', '$status_processo', '$data_movimento', '$horario','$valor', '$data_pg', '$n_parcelas',  NOW(), '$_COOKIE[logado]',1)";
//exit();

$qr_insert = mysql_query("INSERT INTO proc_trab_andamento (proc_id, 	proc_status_id, andamento_data_movi, andamento_horario, andamento_valor,andamento_data_pg, andamento_parcelas,   andamento_data_cad, andamento_usuario_cad, andamento_status)
							VALUES ('$id_processo', '$status_processo', '$data_movimento', '$horario','$valor', '$data_pg', '$n_parcelas',  NOW(), '$_COOKIE[logado]',1)") or die(mysql_error());
if($valor2 != ""){
$qr_insert2 = mysql_query("INSERT INTO proc_trab_andamento (proc_id, 	proc_status_id, andamento_data_movi, andamento_horario, andamento_valor,andamento_data_pg, andamento_parcelas,   andamento_data_cad, andamento_usuario_cad, andamento_status)
                                                    VALUES ('$id_processo', '$status_processo', '$data_movimento', '$horario','$valor2', '$data_pg2', '$n_parcelas2',  NOW(), '$_COOKIE[logado]',1)") or die(mysql_error());
}
$id_andamento = mysql_insert_id();	






	
	
	
////////////////////INSERINDO AS PARCELAS NO FINANCEIRO
if($verifica_financeiro != 0) {
	

		$nome_trabalhador = mysql_result(mysql_query("SELECT proc_nome FROM processos_juridicos WHERE proc_id = '$id_processo'"),0);
		$nome_andamento   = mysql_result(mysql_query("SELECT proc_status_nome FROM processo_status WHERE proc_status_id = '$status_processo'"),0);
		$qr_n_processo 	  = mysql_query("SELECT * FROM n_processos WHERE proc_id = '$id_processo'");
		while($row_n_proc = mysql_fetch_assoc($qr_n_processo)):
		
		$n_processos[] = $row_n_proc['n_processo_numero'];
		
		endwhile;
		
		
		$n_processos = implode(', ',$n_processos);
		$data_Proc2 = date('Y-m-d');
		$nome_trabalhador2 = $_REQUEST['nome_pg2'];
                $banco_pg2 = $_REQUEST['banco_nome2'];
                $agencia_pg2 = $_REQUEST['agencia_nome2'];
                $conta_pg2 = $_REQUEST['conta_nome2'];
                $banco_pg1 = $_REQUEST['banco_nome1'];
                $agencia_pg1 = $_REQUEST['agencia_nome1'];
                $conta_pg1 = $_REQUEST['conta_nome1'];
		
		
		if($qr_insert) {
			
			
			foreach($array_data_parcela as $chave => $data_parcela) {
			
					$data = implode('-',array_reverse(explode('/',$data_parcela)));
					
					$qr_saida = mysql_query("INSERT INTO saida (id_regiao, id_projeto, id_banco, id_user, nome, id_nome, especifica, tipo, adicional, valor, data_proc, data_vencimento,data_pg, status, juridico)
								VALUES 
								('$id_regiao', '$id_projeto', '$banco_id', '$_COOKIE[logado]', '$nome_trabalhador','$id_nome', '<strong>$nome_trabalhador</strong>, <strong>Banco: $banco_pg1</strong>, <strong>Agencia: $agencia_pg1</strong>, <strong>Conta: $conta_pg1</strong>, $nome_andamento, NÚMERO(S) DO PROCESSO: $n_processos', '143', '$adicional', '$array_valor[$chave]', NOW(), '$data','$data_pg',  '1', '1')") or die("Erro");
					
				if($qr_saida) {
					$id_saida = mysql_insert_id();		
					mysql_query("INSERT INTO andamento_saida_assoc (andamento_id, proc_id, id_saida) VALUES ('$id_andamento','$id_processo' , '$id_saida')") or die(mysql_error());
				}
		
			}
                        
                        
                        if($valor2 != ""){
                            foreach($array_data_parcela2 as $chave2 => $data_parcela2) {

                                            $data2 = implode('-',array_reverse(explode('/',$data_parcela2)));

                                            $qr_saida2 = mysql_query("INSERT INTO saida (id_regiao, id_projeto, id_banco, id_user, nome, id_nome, especifica, tipo, adicional, valor, data_proc, data_vencimento,data_pg, status, juridico)
                                                                    VALUES 
                                                                    ('$id_regiao2', '$id_projeto2', '$banco_id2', '$_COOKIE[logado]', '$nome_trabalhador / $nome_trabalhador2','$id_nome', '<strong>Nome: $nome_trabalhador2</strong>, <strong>Banco: $banco_pg2</strong>, <strong>Agencia: $agencia_pg2</strong>, <strong>Conta: $conta_pg2</strong>, $nome_andamento, NÚMERO(S) DO PROCESSO: $n_processos', '143', '$adicional', '$array_valor2[$chave2]', NOW(), '$data2', '$data_pg2',   '1', '1')") or die(mysql_error());

                                    if($qr_saida) {
                                            $id_saida2 = mysql_insert_id();		
                                            mysql_query("INSERT INTO andamento_saida_assoc (andamento_id, proc_id, id_saida) VALUES ('$id_andamento','$id_processo' , '$id_saida')") or die(mysql_error());
                                    }

                            }
                        }

}
///////////////////////////////////////////////////////////////
}



header('Location: anexar_doc_andamentos.php?id_processo='.$_POST['id_processo'].'&id_andamento='.$id_andamento);


}







////////////////////////////////////////////////////////////////////////////////////   MOVIMENTOS
if(isset($_POST['enviar']) and $_POST['tipo'] == 'movimentos'){

	

		$id_processo      = mysql_real_escape_string($_POST['id_processo']);	
		$data_movimento   = implode('-', array_reverse(explode('/',$_POST['data_movimento'])));
		$andamento_id 	  = $_POST['andamento_id'];
		$documento        = $_FILES['documento'];
		$obs              = $_POST['obs'];
		$proc_status_id   = $_POST['proc_status_id'];
		
		
	
		
		$qr_insert = mysql_query("INSERT INTO proc_trab_movimentos (proc_id, andamento_id, proc_status_id, data_movimento,   obs, data_cad, user_cad, status)
								VALUES ('$id_processo', '$andamento_id', '$proc_status_id ', '$data_movimento', '$obs',  NOW(), '$_COOKIE[logado]',1)") or die(mysql_error());
		
		$id_movimento = mysql_insert_id();	
		
		
		
		
			
		header('Location: anexar_doc_movimentos.php?id_processo='.$_POST['id_processo'].'&id_movimento='.$id_movimento);
		


}





$sql_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($sql_user);




$pagina = $_REQUEST['pagina'];

$id_processo = mysql_real_escape_string($_GET['id_processo']);
$qr_processo = mysql_query("SELECT * FROM processos_juridicos WHERE proc_id = '$id_processo'");
$row_processo = mysql_fetch_assoc($qr_processo);
$tipo_contratacao = $row_processo['proc_tipo_contratacao'];

if($tipo_contratacao == 2) {
	
$result = mysql_query(" SELECT *, date_format(data_entrada, '%d/%m/%Y') AS nova_data, date_format(data_saida, '%d/%m/%Y') AS data_saida2, date_format(dataalter, '%d/%m/%Y') AS dataalter2 FROM rh_clt WHERE id_clt = $row_processo[id_clt]");
$row    = mysql_fetch_array($result);
	
	
} else {

$result = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y') AS nova_data, date_format(data_saida, '%d/%m/%Y') AS data_saida2, date_format(dataalter, '%d/%m/%Y') AS dataalter2 FROM autonomo WHERE id_autonomo = '$row_processo[id_autonomo]'");
$row = mysql_fetch_array($result);

}

//print_r($row_processo);
$nome_regiao = mysql_result(mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$row_processo[id_regiao]' AND id_master = 6"),0);
$nome_atividade = mysql_result(mysql_query("SELECT nome FROM curso WHERE id_curso = '$row[id_curso]'"),0);


$result_pro = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$row_processo[id_projeto]'");
$row_pro    = mysql_fetch_array($result_pro);

$sql_user2 = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$row[useralter]'");
$row_user2 = mysql_fetch_array($sql_user2);

$result_ban = mysql_query("SELECT * FROM bancos WHERE id_regiao = '$row_processo[id_regiao]' AND id_projeto = '$row_processo[id_projeto]'");

if($row['status'] == '62') {
	$texto = "<font color=red><b>Data de saída:</b> $row[data_saida2]</font><br>";
} else {
	$texto = NULL;
}

$nome_para_arquivo = $row['1'];


	
if($row['foto'] == '1') {
	
	if($tipo_contratacao == 2) {
		$nome_imagem = $row_processo['id_regiao'].'_'.$row_processo['id_projeto'].'_'. $row_processo['id_clt'].'.gif';
	} else {
		$nome_imagem = $row_processo['id_regiao'].'_'.$row_processo['id_projeto'].'_'. $row_processo['id_autonomo'].'.gif';
	}
	
} else {
	$nome_imagem = 'semimagem.gif';
}



///PEGANDO o id da recisão do trabalhador
$qr_rescisao = mysql_query("SELECT * FROM rh_recisao WHERE id_clt ='$row_processo[id_clt]' AND status  = 1 ") or die(mysql_error());
$row_rescisao = mysql_fetch_assoc($qr_rescisao);   
$linkir   = str_replace('+', '--', encrypt("$row_processo[id_regiao]&$row_processo[id_clt]&$row_rescisao[id_recisao]"));



$linkir2   = str_replace('+', '--', encrypt("$row_processo[id_regiao]&2&$row_processo[id_clt]"));


?>
<html>
<head>
<title>:: Intranet ::</title>
<link rel='shortcut icon' href='../../../favicon.ico'>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../../rh/css/estrutura_participante.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="../../../jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../../../jquery/mascara/jquery.maskedinput-1.2.2.js"></script>

<script type="text/javascript" src="../../../uploadfy/scripts/swfobject.js"></script>
<script type="text/javascript" src="../../../uploadfy/scripts/jquery.uploadify.v2.1.0.js"></script>
<script type="text/javascript" src=".../../../js/shadowbox.js"></script>

<link href="../../../js/highslide.css" rel="stylesheet" type="text/css"  /> 
<script type="text/javascript" src="../../../js/highslide-with-html.js"></script> 

<script type="text/javascript" src="../../../jquery/validationEngine/jquery.validationEngine-pt.js"></script>
<script type="text/javascript" src="../../../jquery/validationEngine/jquery.validationEngine.js"></script>
<link rel="stylesheet" type="text/css" href="../../../jquery/validationEngine/validationEngine.jquery.css" />
<link rel="stylesheet" type="text/css" href="../../../uploadfy/css/default.css" />
<link rel="stylesheet" type="text/css" href="../../../uploadfy/css/uploadify.css" />

<script type="text/javascript" src="../../../jquery/priceFormat.js"></script>

<script type="text/javascript">

    hs.graphicsDir = '../../../images-box/graphics/';
    hs.outlineType = 'rounded-white';

$(function(){
	
	$('#data_movimento').mask('99/99/9999');
	$('#data_movimento2').mask('99/99/9999');
	$('#data_pg, #data_pg2').mask('99/99/9999');
	$('#horario').mask('99:99');
	$('#valor, #valor2').priceFormat({
		
		prefix:'',
		centsSeparator:',',
		thousandSeparator:'.',
		
		
		});
	
	$('#form').validationEngine();
	

	
	
	$("#acordo_add").hide();
	$("#acordo_remove").hide();
        $("#valor_digitado1").hide();
        $("#valor_digitado2").hide();
	$('#status_processo').change(function(){
		
		var valor = $(this).val();
		
		<?php
		
		$verifica_financeiro = mysql_query("SELECT * FROM processo_status WHERE vinculo_financeiro = 1");
		while($row_and_status = mysql_fetch_assoc($verifica_financeiro)):
			$ids_status[] = 'valor == '.$row_and_status['proc_status_id'];			
		endwhile;
		?>
		
		if(<?php echo  implode(' || ',$ids_status);?>) {
			
			$('#enviar').hide();
			$('.outros').fadeIn();
			$('#campo_horario').fadeOut();
			
		} else 
			if(valor == 2){
			
			$('#enviar').show();
			$('.outros').fadeOut();
			$('#encerramento').html('');
			$('#campo_horario').fadeIn();	
				
			}	else	
		
		if(valor != 22) {
			
			$('#enviar').show();
			$('.outros').fadeOut();
			$('#encerramento').html('');
			$('#campo_horario').fadeIn();
		
		} else {
		$('#enviar').show();	
		$('.outros').fadeOut();
			
		}
               
               if($("#status_processo").val() == 9){
                $("#acordo_add").show();
                }else{
                $("#acordo_add").hide();
                }
	});
        
            $(".outros2").hide();
            $("#acordo_add").click(function(){
                if($("#status_processo").val() == 9){
                    $(".outros2").show();
                    $("#acordo_add").hide();
                    $("#acordo_remove").show();
                }
            });	
            $("#acordo_remove").click(function(){
                if($("#status_processo").val() == 9){
                    $("#data_pg2, #valor2, #n_parcelas2, #data_movimento2, #regiao2, #projeto2, #banco2").val("");
                    $(".outros2").hide();
                    $("#acordo_add").show();
                    $("#acordo_remove").hide();
                }
            });	
	
	
$('#regiao').change(function(){

var regiao_id = $(this).val();
	
		$.ajax({
		url: 'action.projeto.php?regiao='+regiao_id,
		success: function(resposta) {
			
			$('#projeto').html(resposta);
				
		}
		});
});
	
$('#projeto').change(function() {
	
	var id_projeto = $(this).val();
	var id_regiao  = $('#regiao').val();
	
	$.ajax({
	url: 'action.bancos.php?projeto='+id_projeto+'&regiao='+id_regiao,
	success: function(resposta) {
		
	$('#banco').html(resposta);
		
	}
	
	
	});
	
	
});
	
$('#regiao2').change(function(){

var regiao_id = $(this).val();
	
		$.ajax({
		url: 'action.projeto.php?regiao='+regiao_id,
		success: function(resposta) {
			
			$('#projeto2').html(resposta);
				
		}
		});
});
	
$('#projeto2').change(function() {
	
	var id_projeto = $(this).val();
	var id_regiao  = $('#regiao2').val();
	
	$.ajax({
	url: 'action.bancos.php?projeto='+id_projeto+'&regiao='+id_regiao,
	success: function(resposta) {
		
	$('#banco2').html(resposta);
		
	}
	
	
	});
	
	
});
	
$('#visualizar').click(function(){
	
	var valor       = $('#valor').val().replace('.','');
        valor 		= valor.replace(',','.');
	var n_parcela 	= $('#n_parcelas').val();
        
        //console.log(valor);
        //console.log(n_parcela);

	var data        = new Array();
        var data 	    = $('#data_pg').val().split('/');
	
	var data_parcela;	
	var i;		
	var mes =  parseInt(data[1]);	
	var ano  = data[2];
        
        var valor_parcela = valor/n_parcela; 
        
        //console.log(valor_parcela);
	
	$('#encerramento').html('');
	var tabela = '<table  width="100%">';
	tabela   	+= '<tr class="titulo"><td>DATA DE PAGAMENTO</td><td>VALOR</td></tr>';
	
	
	for(i=0; i<n_parcela;i++){
	
	
	 
	 if(mes_anterior == 12){
		ano = parseInt(ano) + 1;
		mes = 1;
		 
	 } 
	 
	
	 
	
	 //FORMATANDO O MÊS COM O ZERO A ESQUERDA
	if(mes.toString().length == 1) { var mes_format = '0'+mes; } else { var mes_format = mes; };	
	 
	 
	 
	
	
	 if(data[0] >= 29 && data[0] <=31) {
		 
	 
			 switch(mes)
		    {
		        case 1 :
		        case 3 :
		        case 5 :
		        case 7 :
		        case 8 :
		        case 10:
		        case 12:
		            dia = 31;
		            break;
		        case 4 :
		        case 6 :
		        case 9 :
		        case 11:
		               dia = 30;
		            break;
		
		        case 2 :
		            if( ( (ano % 4 == 0) && ( ano % 100 != 0) ) || (ano % 400 == 0) )
		                dia = 29;
		            else
		                dia = 28;
		            break;
		    }
			
	
		
		
	     data_parcela = dia +'/'+ mes_format +'/'+ ano;

	 } else { 
	 
		 data_parcela = data[0] +'/'+ mes_format +'/'+ ano;
	 
	 }
	 
	 
	 
	 
	 if( (i %2) == 0) { var linha_class = 'class="linha_um"'} else { var linha_class = 'class="linha_dois"';}
	
	tabela +='<tr '+linha_class+' ><td> <input name="data_parcela[]" type="text" value="'+data_parcela+'" class="data_parcela"/> </td> <td> R$  <input name="valor_parcela[]" value="'+float2moeda(valor_parcela)+'"  type="text" class="valor_parcela"/></td></tr>';	
	
//	$('#data_pg').mask('99/99/9999');
		
	
	var mes_anterior = mes;
	mes  = mes + 1;
	
	}

	tabela +='</table>';
	$('#encerramento').html(tabela).fadeIn(300);
        $('#encerramento').find(".valor_parcela").priceFormat({
            prefix:'',
            centsSeparator:',',
            thousandSeparator:'.',
        });
        
//        $("#valor_digitado1").show();
        
//        $("input[name='valor_parcela[]']").blur(function(){
//
//            var soma = 0;
//                var valor_total_valor = parseInt($("#valor").val().replace(",","").replace(".",""));
//
//                $("input[name='valor_parcela[]']").each(function(idx,obj){
//                   soma +=  parseInt($(obj).val().replace(",","").replace(".","")); 
//                });
//
//                if(soma > valor_total_valor ){
//                    alert("O valor ultrapassa o total!");
//                       $(this).val("");
//                       $('#enviar').hide();  
//                }else if(soma < valor_total_valor){
//                    alert("O valor é menor que o total!");
//                    $('#enviar').hide();  
//                }else if(soma == valor_total_valor){
//                    $('#enviar').show();  
//                }
//
//        });
$("#enviar").show();
});

	
$('#visualizar2').click(function(){
	
	var valor       = $('#valor2').val().replace('.','');
        valor 		= valor.replace(',','.');
	var n_parcela 	= $('#n_parcelas2').val();
        
        //console.log(valor);
        //console.log(n_parcela);

	var data        = new Array();
        var data 	    = $('#data_pg2').val().split('/');
	
	var data_parcela;	
	var i;		
	var mes =  parseInt(data[1]);	
	var ano  = data[2];
        
        var valor_parcela = valor/n_parcela;
        
        //console.log(valor_parcela);
	
	$('#encerramento2').html('');
	var tabela = '<table  width="100%">';
	tabela   	+= '<tr class="titulo"><td>DATA DE PAGAMENTO</td><td>VALOR</td></tr>';
	
	
	for(i=0; i<n_parcela;i++){
	
	
	 
	 if(mes_anterior == 12){
		ano = parseInt(ano) + 1;
		mes = 1;
		 
	 } 

	 //FORMATANDO O MÊS COM O ZERO A ESQUERDA
	if(mes.toString().length == 1) { var mes_format = '0'+mes; } else { var mes_format = mes; };	
	 
	 
	 
	
	
	 if(data[0] >= 29 && data[0] <=31) {
		 
	 
			 switch(mes)
		    {
		        case 1 :
		        case 3 :
		        case 5 :
		        case 7 :
		        case 8 :
		        case 10:
		        case 12:
		            dia = 31;
		            break;
		        case 4 :
		        case 6 :
		        case 9 :
		        case 11:
		               dia = 30;
		            break;
		
		        case 2 :
		            if( ( (ano % 4 == 0) && ( ano % 100 != 0) ) || (ano % 400 == 0) )
		                dia = 29;
		            else
		                dia = 28;
		            break;
		    }
			
	
		
		
	     data_parcela = dia +'/'+ mes_format +'/'+ ano;

        } else { 
	 
            data_parcela = data[0] +'/'+ mes_format +'/'+ ano;
	 
        }
	 
	 
	 
	 
        if( (i %2) == 0) { var linha_class = 'class="linha_um"'} else { var linha_class = 'class="linha_dois"';}
	
	tabela +='<tr '+linha_class+' ><td><input name="data_parcela2[]" type="text" value="'+data_parcela+'" class="data_parcela"/> </td> <td> R$ <input name="valor_parcela2[]" value="'+float2moeda(valor_parcela)+'"  type="text" class="valor_parcela"/></td></tr>';	
	
//	$('#data_pg2').mask('99/99/9999');
		
	
	var mes_anterior = mes;
	mes  = mes + 1;
	
	}

	tabela +='</table>';
	$('#encerramento2').html(tabela).fadeIn(300);
	$('#encerramento2').find(".valor_parcela").priceFormat({
            prefix:'',
            centsSeparator:',',
            thousandSeparator:'.',
        }).trigger('blur');
        
//        $("#valor_digitado2").show();
        
        
//        $("input[name='valor_parcela2[]']").blur(function(){
//
//            var soma = 0;
//                var valor_total_valor2 = parseInt($("#valor2").val().replace(",","").replace(".",""));
//
//                $("input[name='valor_parcela2[]']").each(function(idx,obj){
//                   soma +=  parseInt($(obj).val().replace(",","").replace(".","")); 
//                });
//
//                if(soma > valor_total_valor2 ){
//                    alert("O valor ultrapassa o total!");
//                    $(this).val("");
//                    $('#enviar').hide(); 
//                }else if(soma < valor_total_valor2){
//                    alert("O valor é menor que o total!");
//                    $('#enviar').hide(); 
//                }else if(soma == valor_total_valor2){
//                    $('#enviar').show();  
//                }
//
//        });
        $('#enviar').show();  
        
	
});

$("#enviar").click(function(){
    var valido = 0;
    var soma = 0;
                    var valor_total_valor = parseInt($("#valor").val().replace(",","").replace(".",""));

                    $("input[name='valor_parcela[]']").each(function(idx,obj){
                       soma +=  parseInt($(obj).val().replace(",","").replace(".","")); 
                    });

                    if(soma > valor_total_valor ){
                        alert("O primeiro valor ultrapassa o total!");
                        valido = 1;
                    }else if(soma < valor_total_valor){
                        alert("O primeiro valor é menor que o total!");
                        valido = 1;
                    }else if(soma == valor_total_valor){
//                        $("#form").submit();
                    }

    soma = 0;
                var valor_total_valor2 = parseInt($("#valor2").val().replace(",","").replace(".",""));

                $("input[name='valor_parcela2[]']").each(function(idx,obj){
                   soma +=  parseInt($(obj).val().replace(",","").replace(".","")); 
                });

                if(soma > valor_total_valor2 ){
                    alert("O segundo valor ultrapassa o total!");
                    valido = 1;
                }else if(soma < valor_total_valor2){
                    alert("O segundo valor é menor que o total!");
                    valido = 1;
                }else if(soma == valor_total_valor2){
//                    $("#form").submit(); 
                }
                
                if(valido == 0){
                    $("#form1").submit();
                 }   

});

});



function float2moeda(num) {

   x = 0;

   if(num<0) {
      num = Math.abs(num);
      x = 1;
   }
   if(isNaN(num)) num = "0";
      cents = Math.floor((num*100+0.5)%100);

   num = Math.floor((num*100+0.5)/100).toString();

   if(cents < 10) cents = "0" + cents;
      for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
         num = num.substring(0,num.length-(4*i+3))+'.'
               +num.substring(num.length-(4*i+3));
   ret = num + ',' + cents;
   if (x == 1) ret = ' - ' + ret;return ret;

}
</script>

<style type="text/css">

.outros{
display:none;
	
}

#encerramento{

background-color:#FFF;
border: 2px solid #999;
display:none;
}

#encerramento2{

background-color:#FFF;
border: 2px solid #999;
display:none;
}

.titulo{
background-color:#B1B1B1;
color:#FFF;
font-size:12px;	
	
}

.linha_um{
background-color: #EEE;	
}

.linha_dois{
background-color: #D8D8D8;	
}

</style>


<link rel="stylesheet" type="text/css" href="../../../js/highslide.css" />
<link rel="stylesheet" href="../../../js/lightbox.css" type="text/css" media="screen" />
</head>
<body>
<div id="fileQueue"></div>
<div id="corpo">
<div id="conteudo">
<table align="center" width="100%" cellspacing="0" cellpadding="12" style="font-size:13px; line-height:22px;">
  <tr>
  <td colspan="2">
  
   <div style="float:right;"><?php include('../../../reportar_erro.php'); ?></div>
  <div style="clear:right;"></div>
  
  <?php if($_GET['sucesso'] == 'cadastro') { ?>
  <div id="sucesso">
       Participante cadastrado com sucesso!
  </div>
  <?php } ?>
  <div style="border-bottom:2px solid #F3F3F3; margin-top:10px;">
       <h2 style="float:left; font-size:18px;">PROCESSO: <?=$row_processo['proc_nome']?> 
       </h2>
     <span style="float:right"><a href="../../index.php?regiao=<?php echo $id_reg;?>"><<< Voltar</a></span>
       <div class="clear"></div>
  </div>
    </td>
  </tr>
  <tr>
    
    <td width="16%" rowspan="2" valign="top" align="center">
    
    <?php if($tipo_contratacao == 2) {?>
                
                <img src="../../../fotosclt/<?=$nome_imagem?>" name="imgFile" width="100" height="130" id="imgFile" style="margin-top:-12px; margin-bottom:5px;">
                <?php } else {?>    
                    <img src="../../../fotos/<?=$nome_imagem?>" name="imgFile" width="100" height="130" id="imgFile" style="margin-top:-12px; margin-bottom:5px;">
    <?php } ?>
    
   <!-- <input type="file" id="bt_enviar">-->
    <a href="#" id="bt_deletar" style="display:none; position:relative; top:5px;"><img src="../../imagens/excluir_foto.gif"></a>
    </td>
    <td width="84%" bgcolor="#F3F3F3" valign="top">
 		<table style="font-size:12px;width:100%; text-transform:uppercase;">
        	<tr>
            	
            	<td  colspan="4"><strong>Tipo de Contratação:</strong>
                 <?php
				 
				 $qr_tipo = mysql_query("SELECT tipo_contratacao_nome FROM tipo_contratacao WHERE tipo_contratacao_id = '$row_processo[proc_tipo_contratacao]' ");
				 echo mysql_result($qr_tipo, 0);
                            ?>
                </td>            
                </tr>
                <tr>
                    <td colspan="4"><strong>Reclamados: </strong>
                    <?php 
                        $reclamados = mysql_query("SELECT * FROM processos_juridicos_reclamados WHERE id_processo = '{$id_processo}'");
                        while($linhaReclamado = mysql_fetch_assoc($reclamados)){
                            echo "<p> - {$linhaReclamado['nome']} </p>";
                        }
                    ?>
                </td>                    
                </tr>
            <tr>
            	<td  colspan="4"><strong>Data de Entrada:</strong> <?=formato_brasileiro($row_processo['proc_data_entrada'])?></td>
            </tr>
            <?php 
			if ($row_processo['proc_data_saida'] != '0000-00-00') {
			?>
			<tr>
            	<td colspan="4">Data de Saída: <?=formato_brasileiro($row_processo['proc_data_saida'])?></td>
            </tr>	
            <?php
				}
            ?>
             <tr>            	
            	<td colspan="4">&nbsp;</td>
            </tr>
            <tr>
            	
            	<td colspan="4"><strong>Região:</strong> <?php echo $nome_regiao; ?></td>
            </tr>
            
            <tr>
            	<td colspan="4"><strong>Projeto:</strong> <?php echo $row_pro['id_projeto'].' - '.$row_pro['nome'];?></td>
            </tr>
            <tr>
            	<td colspan="4"><strong>Atividade:</strong> <?php echo $row_processo['proc_atividade']; ?></td>
            </tr>
            
            <tr>            	
            	<td colspan="4">&nbsp;</td>
            </tr>
            <tr>
            	<td  colspan="4"><strong>Nº do processo: </strong>
				<?php 
				$qr_n_processo  = mysql_query("SELECT * FROM n_processos WHERE proc_id = '$id_processo' ");
				
				$total = mysql_num_rows($qr_n_processo);
				while($row_n_processo = mysql_fetch_assoc($qr_n_processo)):

				 echo $row_n_processo['n_processo_numero'];
				 
				 if($cont++ < $total-1){
				 echo ',';
				 }
				endwhile;
				unset($cont);   
				
				?>    
                </td>
            </tr>  
             <tr>
            	<td colspan="4"><strong>Local:</strong> <?php echo $row_processo['proc_local'];?></td>
            </tr>
             <tr>
            	
            </tr>    
            
            
             <tr>
             
             <td valign="top" width="50"><strong>Advogados:</strong></td>
                <td valign="top">
				<?php 
							if(!empty($row_processo['adv_id'])) {
								$id_advogados =  $row_processo['adv_id'];
								$qr_adv = mysql_query("SELECT * FROM advogados WHERE adv_id IN ($id_advogados)") or die(mysql_error());
								
								if(mysql_num_rows($qr_adv) != 0){
								while($row_advogado = mysql_fetch_assoc($qr_adv)):
								
									echo $row_advogado['adv_nome'].'<br>';
								
								endwhile;
								}
							} else {
								echo 'Nenhum advogado designado.';
							}
                            ?>
                </td>
                
            	<td valign="top" width="50"><strong>Preposto:</strong></td>
                <td valign="top">
                <?php 
							if(!empty($row_processo['preposto_id'])) {
								$id_preposto =  $row_processo['preposto_id'];
								
								
								$qr_preposto = mysql_query("SELECT * FROM prepostos WHERE prep_id IN ($id_preposto)")or die(mysql_error());
								
								if(mysql_num_rows($qr_preposto) != 0){
								
									while($row_preposto = mysql_fetch_assoc($qr_preposto)):
									
									echo $row_preposto['prep_nome'].'<br>';
								
								endwhile;
								}
							} else {
								echo 'Nenhum preposto designado.';
							}
				?>
                </td>
                
            </tr>         
             <tr>
                <td colspan="4">
                <?php
				
                $nome_func = @mysql_result(mysql_query("SELECT nome1 FROM funcionario WHERE id_funcionario = '$row_processo[usuario_cad]'"),0);			
                ?>
                
               <span style="color:#900;"><strong> Cadatrado por:</strong> <?php echo $nome_func; ?> <strong>em </strong><?php echo implode('/', array_reverse(explode('-',$row_processo['proc_data_cad'])));?></span>
                
                </td>
          </tr>
            
        </table>   
        
    
    
    </td>
  </tr>
<!--  <tr>
    <td>
         <table cellpadding="0" cellspacing="0" width="100%">
             <tr>
               <td>
       <div id="Accordion1" class="Accordion" tabindex="0">
            <div class="AccordionPanel">
                <div class="AccordionPanelTab">&nbsp;</div>
                <div class="AccordionPanelContent">
                      <?php $get_atividade = mysql_query("SELECT * FROM curso WHERE id_curso = '$row[id_curso]'");
					        $atividade     = mysql_fetch_assoc($get_atividade);
							$get_pg        = mysql_query("SELECT * FROM tipopg WHERE id_tipopg = '$row[tipo_pagamento]'");
					        $pg            = mysql_fetch_assoc($get_pg);
							
							if($row['banco'] == '9999') {
								$nome_banco = $row['nome_banco'];
							} else {
								$get_banco = mysql_query("SELECT nome FROM bancos WHERE id_banco = '$row[banco]'");
								$row_banco = mysql_fetch_array($get_banco);
					        	$nome_banco = $row_banco[0];
							} ?>
                            
         <b>Atividade:</b> <?=$atividade['id_curso']?> - <?=$row_processo['proc_atividade']?> <?php if(!empty($atividade['cbo_codigo'])) { echo '('.$atividade['cbo_codigo'].')'; } ?><br>
                       <b>Unidade:</b> <?=$row_processo['proc_trab_unidade']?><br>
                       <b>Salário:</b>
                       <?php if(!empty($atividade['salario'])) { echo "R$ "; echo number_format($atividade['salario'], 2, ',', '.'); } else { echo "<i>Não informado</i>"; } ?>
                       &nbsp;&nbsp;<b>Tipo de Pagamento:</b> 
					   <?php if(!empty($pg['tipopg'])) { echo $pg['tipopg']; } else { echo "<i>Não informado</i>"; } ?><br>
                       <b>Agência:</b> 
					   <?php if(!empty($row['agencia'])) { echo $row['agencia']; } else { echo "<i>Não informado</i>"; } ?>
                       &nbsp;&nbsp;<b>Conta:</b> 
					   <?php if(!empty($row['conta'])) { echo $row['conta']; } else { echo "<i>Não informado</i>"; } ?>
                       &nbsp;&nbsp;<b>Banco:</b>
                       <?php if(!empty($nome_banco)) { echo $nome_banco; } else { echo "<i>Não informado</i>"; } ?>
                </div>
            </div>
       </div>   
               </td>
             </tr>
         </table>
    </td>
  </tr>
 	<tr>
    <td colspan="2"><div id="observacoes"><?php if(empty($row['observacao'])) { echo "Sem Observações"; } else { echo "Observações<p>&nbsp;</p> $row[observacao]"; } ?></div></td>
  </tr>-->
  
  <tr>
                <td colspan="2">&nbsp;</td>
  </tr>
  
   <tr>
                <td colspan="2"><h1><span>MENU DE EDIÇÃO</span></h1></td>
  </tr>
  <tr>
  <td colspan="2" class="menu">   
   <?php if($tipo_contratacao == 2) {?>
    <p>
<<<<<<< HEAD
        <a href="../../../fichadecadastroclt.php?bol=<?=$row['id_antigo']?>&pro=<?=$row_processo['id_projeto']?>&id_reg=<?=$row_processo['id_regiao']?>&clt=<?=$row_processo['id_clt']?>" target="_blank" class="botao">Ficha de Cadastro</a>
       
        <a href="../../../relatorios/fichafinanceira.php?reg=<?=$row_processo['id_regiao']?>&pro=<?=$row_processo['id_projeto']?>&tipo=2&tela=2&id=<?=$row_processo['id_clt']?>" target="_blank" class="botao">Ficha Financeira</a>
        <?php if ($row_rescisao['id_recisao'] > 0) { ?>
        <a href="../../../rh/recisao/nova_rescisao_2.php?enc=<?=$linkir?>" target="_blank" class="botao">Recisão</a>      
        <?php } else { ?>
        <a href="javascript:;" onclick="alert('Não existe rescisão para esse funcionário.');" class="botao">Recisão</a>
        <?php }?> 
=======
       <a href="../../../fichadecadastroclt.php?bol=<?=$row['id_antigo']?>&pro=<?=$row_processo['id_projeto']?>&id_reg=<?=$row_processo['id_regiao']?>&clt=<?=$row_processo['id_clt']?>" target="_blank" class="botao">Ficha de Cadastro</a>
       
       <a href="../../../relatorios/fichafinanceira.php?reg=<?=$row_processo['id_regiao']?>&pro=<?=$row_processo['id_projeto']?>&tipo=2&tela=2&id=<?=$row_processo['id_clt']?>" target="_blank" class="botao">Ficha Financeira</a>
       
     	 <a href="../../../rh/recisao/nova_rescisao_2.php?enc=<?=$linkir?>" target="_blank" class="botao">Recisão</a>      
          
>>>>>>> educacional
  		<a href="eventos_clt.php?clt=<?=$row_processo['id_clt']?>&pro=<?=$row_processo['id_projeto']?>&id_reg=<?=$row_processo['id_regiao']?>" target="_blank" class="botao" onClick="return hs.htmlExpand(this, { objectType: 'iframe' } )">Eventos</a>
        <a href="ferias/index.php?enc=<?=$linkir2?>" target="_self " class="botao">Férias</a>
  
  
      <!--  <a href="../../rh/docs/.php?clt=<?=$row['0']?>&tab=bolsista<?=$id_pro?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>" target="_blank" class="botao">Eventos</a>  -->
       </p>
   <?php } else {?>
   
  <a href="../../../cooperativas/fichadecadastro.php?bol=<?=$row_processo['id_autonomo']?>&pro=<?=$row_processo['id_projeto']?>&id_reg=<?=$row_processo['id_regiao']?>" class="botao" target="_blank">Ver Ficha</a>
    <a href="../../../cooperativas/fichadecadastro.php?bol=<?=$row_processo['id_autonomo']?>&pro=<?=$row_processo['id_projeto']?>&id_reg=<?=$row_processo['id_regiao']?>" class="botao" target="_blank">Eventos</a>
  
  
   
   <?php } ?>
   
   
  </td>
  
  
  </table>
  
  </td>
  </tr>
	<tr>
	     <td colspan="2" align="left" ><h1 style="text-align:left"><span>PEDIDO DA AÇÃO</span></h1></td>
	 </tr>
	 <tr>
     	<td colspan="2" align="left">
        	<div style="width:100%;height:auto;display:block;background-color:#F4F4F4;text-align:left;padding: 5px; text-transform:uppercase;">
        	<?php echo $row_processo['pedido_acao']; ?>
        	</div>
        </td>
     </tr>
    
 
    <tr>  
    	
        <td>
          <tr>
 			 <td colspan="2" align="left" ><h1 style="text-align:left"><span>ATUALIZAR ANDAMENTOS</span></h1></td>
 		 </tr>
        <?php if($tipo_contratacao == 2 ) {  ?>
        <form name="form" id="form1" method="post" action="ver_trabalhador.php?tp=2" enctype="multipart/form-data" >
        
        <?php } else {?>
         <form name="form" id="form1" method="post" action="ver_trabalhador.php?tp=<?php echo $row_processo['id_autonomo']; ?>" enctype="multipart/form-data" >
        
        <?php } ?>
        <table width="100%" style="font-size:12px;">
        	<tr>
            	<td>Status do processo:</td>
                <td colspan="4">
                <select name="status_processo" id="status_processo" class="validate[required]">
                <option value="">Selecione uma opção...</option> 
                <option value=""></option> 
					<?php 	
                    $qr_status = mysql_query("SELECT * FROM processo_status WHERE proc_status_id != 1 ORDER BY ordem ");
                    while($row_status  = mysql_fetch_assoc($qr_status)):				
                    ?>
                      <option value="<?php echo $row_status['proc_status_id']?>"> <?php echo $row_status['proc_status_nome']?></option>   
                    
                    <?php
                    endwhile;
                    ?> 
                </select>               
                </td>
                <td>
                    <img src="../../../imagens/add.png" style="width:20px" id="acordo_add">
                </td>
        	</tr>
            <tr id="campo_horario" style="display:none;">
            	<td>Horário:</td>
                <td colspan="4"><input type="text" name="horario" id="horario" size="5"/></tr>
            </tr>
            
            <tr class="outros" style="display:none;">
            	 <td>Data do  1º pagamento</td>
                 <td><input type="text" name="data_pg" id="data_pg"/></td>
            </tr>
            <tr class="outros">     
                 <td>Valor:</td>
                 <td><input type="text" name="valor" id="valor"/></td>
                 <td>Número de parcelas</td>
            	 <td><input type="text" name="n_parcelas" id="n_parcelas" size=5/></td>
          	</tr>
                <tr class="outros">
                    <td>Banco (Clt):</td>
                    <td><input type="text" name="banco_nome1"  id="banco_nome1"/></td>
                    <td></td>
                    <td></td>                  
                </tr>
                <tr class="outros">
                    <td>Agencia (Clt):</td>
                    <td><input type="text" name="agencia_nome1"  id="agencia_nome1"/></td>
                    <td></td>
                    <td></td> 
                </tr>
                <tr class="outros">
                    <td>Conta (Clt):</td>
                    <td><input type="text" name="conta_nome1" id="conta_nome1"/></td>              
                    <td></td>
                    <td></td> 
                </tr>
              <tr class="outros">
            	<td >Região:</td>
                <td colspan="3"> 
                	<select name="regiao" id="regiao">
                
                    <?php
                  	$REGIAO->Preenhe_select_com_master();
					
					?>
                    </select>
                </td>
            </tr>
            
            </tr>
              <tr class="outros">
            	<td>Projeto:</td>
                <td colspan="3"> 
                	<select name="projeto" id="projeto">
                    </select>
                </td>
            </tr>
            <tr class="outros">
            	<td>Banco:</td>
                <td colspan="3"> 
                	<select name="banco" id="banco" >
                 
                    </select>
                </td>
            </tr>
             
        
            
            <tr>
            	<td>Data do movimento:</td>
            	<td colspan="4"><input name="data_movimento"  id="data_movimento" type="text" class="validate[required]"/></td>
            </tr>
            
            <td colspan="4">
                <div id="encerramento" class="outros">
                </div>                
            </td>
            <tr class="outros">
            <td colspan="4" align="right" class="outros"><input type="button" id="valor_digitado1" value="Verificar Valor Digitado"/></td>
            <td colspan="4" align="right"><input type="button" id="visualizar" value="VISUALIZAR"/></td>
           </tr>
         
                <tr class="outros2">
                    <td>Nome(Advogado):</td>
                    <td><input type="text" name="nome_pg2" style="width:300px" id="nome_pg2"/></td>
                    <td></td>
                    <td></td>
                    <td><img src="../../../imagens/desgerar_folha.gif" style="width:20px" id="acordo_remove"></td>
                </tr>
                <tr class="outros2">
                    <td>Banco (Advogado):</td>
                    <td><input type="text" name="banco_nome2"  id="banco_nome2"/></td>
                    <td></td>
                    <td></td>                  
                    
                </tr>
                <tr class="outros2">
                    <td>Agencia (Advogado):</td>
                    <td><input type="text" name="agencia_nome2"  id="agencia_nome2"/></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr class="outros2">
                    <td>Conta (Advogado):</td>
                    <td><input type="text" name="conta_nome2"  id="conta_nome2"/></td>              
                    <td></td>
                    <td></td>
                </tr>
                <tr class="outros2">
                    <td>Data do  2º pagamento</td>
                    <td><input type="text" name="data_pg2" id="data_pg2"/></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr class="outros2">     
                     <td>Valor:</td>
                     <td><input type="text" name="valor2" id="valor2"/></td>
                     <td>Número de parcelas</td>
                     <td><input type="text" name="n_parcelas2" id="n_parcelas2" size=5/></td>
                </tr>
                <tr class="outros2">
                    <td >Região:</td>
                    <td colspan="3"> 
                        <select name="regiao2" id="regiao2">
                            <?php
                                $REGIAO->Preenhe_select_com_master();
                            ?>
                        </select>
                    </td>
                </tr>
                </tr>
                  <tr class="outros2">
                    <td>Projeto:</td>
                    <td colspan="3"> 
                            <select name="projeto2" id="projeto2">
                        </select>
                    </td>
                </tr>
                <tr class="outros2">
                    <td>Banco:</td>
                    <td colspan="3"> 
                            <select name="banco2" id="banco2" >

                        </select>
                    </td>
                </tr>
                <tr class="outros2">
                    <td>Data do movimento:</td>
                    <td colspan="4"><input name="data_movimento2"  id="data_movimento2" type="text" class=""/></td>
                </tr>
             
           <tr class="outros2">
            <td colspan="4" align="right" class="outros2"><input type="button" id="valor_digitado2" value="Verificar Valor Digitado"/></td>
            <td colspan="4" align="right"><input type="button" id="visualizar2" value="VISUALIZAR"/></td>
           </tr> 
            <tr>
            	<td colspan="4">
                <div id="encerramento2" class="outros2">
                
                
                </div>                
                </td>
            </tr>
                      
       <!----      <tr>
                    <td>Anexar documento:</td>
                        <td colspan="4">
                      
                                <input name="documento" type="file" class="validate[required]" id="doc_andamento" />   
                        </td>
                    </tr>--->
                   
                                
            
            <tr>
            	<td colspan="4" align="center">
                
                    <input name="id_processo" type="hidden" value="<?php echo $id_processo?>" class="id_processo"/>
                                  
                    <input name="tipo" type="hidden" value="andamentos"/>
                	<input name="" type="button" value="Enviar" id="enviar"/>
                	<input name="enviar" type="hidden" value="Enviar" id=""/>
                </td>
            </tr>
            
        </table>
        </form>
        </td>
    </tr>
    <table>
        
    </table>
	<!-------------MOVIMENTOS ----------------------->
	 <tr>
     	 <td colspan="2" align="left" ><h1 style="text-align:left"><span>ATUALIZAR MOVIMENTOS</span></h1></td>
 	 </tr>
        
       <?php if($tipo_contratacao == 2 ) {  ?>
        <form name="form" id="form" method="post" action="ver_trabalhador.php?tp=2" enctype="multipart/form-data" >
        
        <?php } else {?>
         <form name="form" id="form" method="post" action="ver_trabalhador.php?tp=<?php echo $row_processo['id_autonomo']; ?>" enctype="multipart/form-data" >
        
        <?php } ?>
        <table width="100%" style="font-size:12px;">
        	        
            <tr>
            	<td>Data do movimento:</td>
            	<td colspan="4"><input name="data_movimento"  id="data_movimento2"type="text" class="validate[required]"/></td>
            </tr>   
          <!--    <tr>            
            	<td>Anexar documento:</td>
                <td colspan="4">              
                          <input name="documento" type="file" class="validate[required]" id="doc_movimento" />         
                </td>
            </tr>  
           -->          
            <tr>
            	<td valign="top">Observações</td>
           		<td colspan="4">  <textarea name="obs" rows="20" cols="65"></textarea> </td>
            </tr>        
                      
            <tr>
            	<td colspan="4" align="center">
                <?php
				$qr_andamento = mysql_query("SELECT * FROM `proc_trab_andamento` WHERE proc_id = '$id_processo' AND andamento_status = 1 ORDER BY andamento_id DESC
");
  				$row_andamento = mysql_fetch_assoc($qr_andamento);
				?>
                
                
                    <input name="id_processo" type="hidden" value="<?php echo $id_processo; ?>" class="id_processo"/>
                    <input name="andamento_id" type="hidden" value="<?php echo $row_andamento['andamento_id'];?>"/>
                    <input name="proc_status_id" type="hidden" value="<?php echo $row_andamento['proc_status_id'];?>"/>
                    <input name="tipo" type="hidden" value="movimentos"/>
                	<input name="enviar" type="submit" value="Enviar"/>
                </td>
            </tr>
            
        </table>
        </form>
        </td>
    </tr>
   <tr>
  	<td colspan="2"><h1 style="text-align:left"><span>ANDAMENTOS E MOVIMENTOS</span></h1></td>
  </tr>
  <tr>
  <td colspan="2">
  
  <table width="100%" border="0" cellpadding="4" cellspacing="0" style="font-size:13px;">
      <tr bgcolor="#dddddd">
        <td width="70%"><strong>DESCRIÇÃO</strong></td>
        <td>ANEXO</td>
        <td>PARCELAS</td>
        <td>EDITAR</td>
        <td>EXCLUIR</td>
       
      </tr>
      <?php
	  
	  $status_id = array(7,8,9,10);
	  
	 	
		
			$qr_processo2 = mysql_query("SELECT * FROM  proc_trab_andamento WHERE   proc_id = '$id_processo' AND andamento_status = 1 ORDER BY andamento_data_cad ASC" ) or die(mysql_error());
		
			
			
			
			
			while($row_processo2 = mysql_fetch_assoc($qr_processo2)):
			
			$qr_status = mysql_query("SELECT * FROM processo_status WHERE proc_status_id = '$row_processo2[proc_status_id]'");
			$row_status = mysql_fetch_assoc($qr_status);
			$i++;	
			
			//////pegando os movimentos
			$qr_movimentos = mysql_query("SELECT *  FROM  proc_trab_movimentos WHERE proc_id = '$id_processo' AND proc_status_id = '$row_status[proc_status_id]'  AND status = 1") or die (mysql_error());
			?>
						
							<tr bgcolor="#E4E4E4">
							
								<td><img src="../../../img_menu_principal/seta_azul.png" /> 
								<?php 
								echo $row_status['proc_status_nome']?> em <?php echo formato_brasileiro($row_processo2['andamento_data_movi']);
								
								if($row_processo2['andamento_horario'] != '00:00:00') {
									echo ' as '.substr($row_processo2['andamento_horario'],0,5).'h' ;
								}
								
								?>
                                
                                </td>

							 <td  align="center">
                            <?php 
							
                              $qr_anexo_andamento = mysql_query("SELECT * FROM  proc_andamento_anexo  WHERE andamento_id = '$row_processo2[andamento_id]' AND 	andamento_anexo_status = 1");
							 if(mysql_num_rows($qr_anexo_andamento) !=0) {
							 ?>                             
                             	<a href="anexo_trab.php?id_andamento=<?php echo $row_processo2['andamento_id']?>" OnClick="return hs.htmlExpand(this, { objectType: 'iframe' } )">
                                <img src="../../../imagens/ver_anexo.gif" width="20" height="20"/>
                                </a>                                
                             
                             <?php }  ?>
                             
                             
                             </td> 
                             <td></td>
                                <td>
                                   <a href="editar_andamento.php?id_processo=<?php echo $id_processo?>&id=<?php echo $row_processo2['andamento_id'];?>" >
                                <img src="../../../imagens/editar_projeto.png"  width="20" height="20"/> </a>
                                </td>
                                
                                <td align="center">
                                <?php
                                if($row_status['proc_status_id'] != 1) {
								?>
                                
                                <a href="../../excluir_and_mov.php?id_processo=<?php echo $id_processo?>&id=<?php echo $row_processo2['andamento_id'];?>&tp=2" onClick="return(confirm('Deseja excluir o movimento : <?php echo $row_status['proc_status_nome'] ?>?'))">
                                <img src="../../../imagens/excluir.png"  width="20" height="20"/> </a>
                                
                                <?php
								}
								?>
                                </td>
							</tr>
						
						<?php
					
					
				  /////MOVIMENTOS
					   while($row_movimentos = mysql_fetch_assoc($qr_movimentos)):
					   
					  // if($movimento_anterior == $row_movimentos['proc_trab_mov_id']) continue;
					   
					   if(!empty($row_movimentos['andamento_id'])){
						   
							if($row_movimentos['andamento_id'] != $row_processo2['andamento_id']) continue;
							
					   } 
					   
					   ?>
					   	<tr style="background-color: #F7F7F7;">
                        	<td> 
                            	 &nbsp;&nbsp;&nbsp;
                            	 <img src="../../../img_menu_principal/seta_vermelha.png" />
                                 <?php echo formato_brasileiro($row_movimentos['data_movimento']); ?>: <?php echo $row_movimentos['obs']; ?>
                            </td>
                            <td align="center">
                            <?php
							$qr_mov_anexos = mysql_query("SELECT * FROM proc_trab_mov_anexos WHERE 	proc_trab_mov_id = '$row_movimentos[proc_trab_mov_id]' AND 	proc_trab_mov_status = 1");
							
							if(mysql_num_rows($qr_mov_anexos) !=0)	{?>				
                            
                                <a href="anexo_movimentos.php?id_movimento=<?php echo  $row_movimentos['proc_trab_mov_id']?>" OnClick="return hs.htmlExpand(this, { objectType: 'iframe' } )">
                                 <img src="../../../imagens/ver_anexo.gif" width="20" height="20"/>
                                </a>
                                <?php } ?>
                            </td>
                            <td></td>
                              <td>
                                   <a href="editar_movimento.php?id_processo=<?php echo $id_processo?>&id=<?php echo $row_movimentos['proc_trab_mov_id'];?>" >
                                <img src="../../../imagens/editar_projeto.png"  width="20" height="20"/> </a>
                                </td>
                            <td align="center">  
                          
                             <a href="../../excluir_and_mov.php?id_movimento=<?php echo $row_movimentos['proc_trab_mov_id']; ?>&id_processo=<?php echo $id_processo?>" onClick="return(confirm('Deseja excluir o movimento?'))">
                                <img src="../../../imagens/excluir.png"  width="20" height="20"/> 
                                	</a>
                                    
                                    
                                    </td>
                        </tr>
                        
					   <?php 
					   
					   $movimento_anterior =  $row_movimentos['proc_trab_mov_id'];
					    endwhile;  
					   //////////////////////////	 
			endwhile;
			
	 	
	 ?>
    </table>
    
    </td>
    </tr>
  
   
    <tr>
      <td colspan="2"></td>
    </tr>
</table>
</div>
<div id="rodape">
<?php $qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$Master'");
      $master = mysql_fetch_assoc($qr_master);
	  ?>
            <p class="left"><img style="position:relative; top:7px;" src="../../../imagens/logomaster<?=$Master?>.gif" width="66" height="46"> <b><?=$master['razao']?></b>&nbsp;&nbsp;Acesso Restrito à Funcion&aacute;rios</p>
            <p class="right"><br><br><a href="#corpo">Subir ao topo</a></p>
            <div class="clear"></div>
        </div>
</div>
<script type="text/javascript">
var Accordion1 = new Spry.Widget.Accordion("Accordion1", { enableAnimation: false, useFixedPanelHeights: false, defaultPanel: -1 });
</script>
</body>
</html>