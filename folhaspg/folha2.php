<?php
ob_start();

if(empty($_COOKIE['logado'])) {
	print 'Efetue o Login<br><a href="www.netsorrindo.com.br/intranet/login.php">Logar</a>';
	exit;
}

// Incluindo Arquivos
require('../conn.php');
include('../classes/calculos.php');
include('../classes/abreviacao.php');
include('../classes/formato_valor.php');
include('../classes/formato_data.php');
include('../classes/valor_proporcional.php');
include('../classes/listaparticipantes.php');
include('../funcoes.php');




// Executando AJAX
if(!empty($_REQUEST['ajax_procurar'])) {

$nome                 = $_REQUEST['nome'];
$projeto              = $_REQUEST['id_projeto'];
$folha                = $_REQUEST['id_folha'];


$qr_autonomo = mysql_query("SELECT A.id_autonomo, A.nome, A.campo3, A.tipo_contratacao, A.locacao, B.nome as nome_curso                                           
                            FROM autonomo as A
                            LEFT JOIN curso as B
                            ON A.id_curso = B.id_curso
                            WHERE A.nome LIKE '%$nome%' AND A.id_projeto = '$projeto'") or die(mysql_error());

$total		 = mysql_num_rows($qr_autonomo);

if($total != 0){
$retorno     = '<table class="folha" cellpadding="0" cellspacing="1" style="margin-top:20px; margin-bottom:20px;">
					<tr class="secao">
					  <td width="5%"><input type="checkbox" name="todos"/></td>
					  <td width="5%" style="text-align:center;">COD</td>
					  <td width="28%" style="text-align:left; padding-left:7px;">NOME</td>
					  <td width="28%">ATIVIDADE</td>
					  <td width="34%">UNIDADE</td>
					</tr>';


while($row_aut = mysql_fetch_assoc($qr_autonomo)) {
	
 
    
	if($row_aut['tipo_contratacao'] == 1) {
		$tabela_now = 'folha_autonomo';
	} else {
		$tabela_now = 'folha_cooperado';
	}
	
	$qr_folha_autonomo    = mysql_query("SELECT id_autonomo FROM $tabela_now  as A                                             
                                            WHERE A.id_autonomo = '$row_aut[id_autonomo]' AND A.id_folha = '$folha' AND status = 2");
	$total_folha_autonomo = mysql_num_rows($qr_folha_autonomo);
	$row_folha_aut = mysql_fetch_assoc($qr_folha_autonomo);
      
      
    
	// Recolhendo informações de Autônomo / Cooperado	
	$locacao = $row_aut['locacao'];
	
	/*$Part      -> CursoParticipante($row_aut['id_autonomo']);
	$nome_curso = $Part -> campo2;
        */
	$nome_curso = str_replace('CAPACITANDO EM', 'CAP. EM', $row_aut['nome_curso']);
	
      
	if(empty($total_folha_autonomo)) {
		
		if($linha++%2==0) { $linha_cor = 'um'; } else { $linha_cor = 'dois'; }
		
		$retorno .= '<tr class="linha_'.$linha_cor.' destaque">
						 <td><input type="checkbox" name="aut[]" id="aut" value="'.$row_aut[id_autonomo].'"></td>
						 <td>'.$row_aut['id_autonomo'].'</td>
						 <td style="text-align:left; padding-left:7px;">'.$row_aut['nome'].'</td>
						 <td>'.htmlentities($nome_curso, ENT_COMPAT, 'iso-8859-1').'</td>
						 <td>'.htmlentities($locacao, ENT_COMPAT, 'iso-8859-1').'</td>
					 </tr>';
		
	}
	
	}
}else {
    
    echo 'Trabalhador não encontrado.';
}
	$retorno .= '</table>';
	
	echo $retorno;
	exit;
	
}





if($_GET['go_pagina'] == true) {
	$nova_pagina = $_GET['pagina'] - 1;
	header("Location: folha_new2.php?pagina=$nova_pagina&enc=$_GET[enc]");
	exit;
}

$id_user = $_COOKIE['logado'];
$Part    = new participantes();

// Recebendo a Variável Criptografada
$enc = $_REQUEST['enc'];
list($regiao, $folha, $st) = explode('&', decrypt(str_replace('--', '+', $enc)));

// Selecionando o Usuário
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user    = mysql_fetch_array($result_user);

// Verificando o Master
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master    = mysql_fetch_array($result_master);

// Consulta da Folha
$result_folha = mysql_query("SELECT *, date_format(data_proc, '%d/%m/%Y') AS data_proc2, date_format(data_inicio, '%d/%m/%Y') AS data_inicio2, date_format(data_fim, '%d/%m/%Y') AS data_fim2 FROM folhas WHERE id_folha = '$folha'");
$row_folha    = mysql_fetch_array($result_folha);



       
// Consulta do Projeto da Folha
$result_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$row_folha[projeto]'");
$row_projeto    = mysql_fetch_array($result_projeto);

// Verificação de Tabela
if($row_folha['contratacao'] == 1) {
	$tabela_now = 'folha_autonomo';
} else {
	$tabela_now = 'folha_cooperado';
}



// Consulta do Usuário que gerou a Folha
$qr_usuario = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$row_folha[user]'");

// Consulta da Região
$qr_regiao = mysql_query("SELECT id_regiao, regiao FROM regioes WHERE id_regiao = '$row_folha[regiao]'");
@$regiao   = mysql_result($qr_regiao, 0, 0);

// Consulta do Projeto
$qr_projeto = mysql_query("SELECT id_projeto, nome, id_master FROM projeto WHERE id_projeto = '$row_folha[projeto]'");
@$projeto   = mysql_result($qr_projeto, 0, 0);

// Consulta dos Participantes da Folha
$qr_participantes    = mysql_query("SELECT * FROM folha_autonomo WHERE id_folha = '$folha' AND status = '2' ORDER BY nome  ASC");
$total_participantes = mysql_num_rows($qr_participantes);



// Verificação para Insert da Folha
if(!empty($_REQUEST['m'])) {
	
	$folha_ini = $row_folha['data_inicio'];
	$folha_fim = $row_folha['data_fim'];
	
        
        
       
	// Acréscimo se for Cooperado
	if($row_folha['contratacao'] == 3 or $row_folha['contratacao'] == 4) {
		$acrescimo = "AND id_cooperativa = '$row_folha[coop]'";
	}
	
	// Consulta para Total de Participantes
	$qr_total = mysql_query("SELECT * FROM autonomo 
                                WHERE id_projeto = '$row_folha[projeto]'
                                      AND tipo_contratacao = '$row_folha[contratacao]' $acrescimo 
                                      AND (status = '0' 
                                               OR  data_saida > '$row_folha[data_inicio]' 
                                               AND data_saida <= '$row_folha[data_fim]')");
	$total = mysql_num_rows($qr_total);
	
       
        
	// Itens
	$partes	= ceil($total / 200);
	$parte	= $_GET['parte'];
	$inicio = $_GET['parte'] * 200;
	
	// Verificação para Insert dos Partipantes
	if($parte <= $partes) {
				
		// Consulta dos Participantes
		$RE_todos = mysql_query("SELECT * 
                                           FROM autonomo 
                                           WHERE id_projeto = '$row_folha[projeto]'
                                           AND tipo_contratacao = '$row_folha[contratacao]' $acrescimo 
                                           AND (status = '0' 
                                           OR  data_saida > '$row_folha[data_inicio]' 
                                           AND data_saida <= '$row_folha[data_fim]') 
                                           ORDER BY nome ASC
                                           LIMIT $inicio,200");
                
		$num_todos = mysql_num_rows($RE_todos);
		
		// Loop dos Participantes
		while($row_aut = mysql_fetch_array($RE_todos)) {
					
			$entrada = $row_aut['data_entrada'];
			$saida   = $row_aut['data_saida'];
			$status  = $row_aut['status'];
					
			// Entrou antes da Data Inicial e saiu antes da Data Final
			if($entrada < $folha_ini and $saida <= $folha_fim and $status == '0') {
				
				$REDatas   = mysql_query("SELECT data FROM ano WHERE data >= '$folha_ini' and data <= '$saida'");
				$dias_trab = mysql_num_rows($REDatas);
				$result    = '2';
					
			// Entrou depois da Data Inicial
			} elseif($entrada >= $folha_ini and $saida == '0000-00-00' and $entrada < $folha_fim) {
				
				$REDatas   = mysql_query("SELECT data FROM ano WHERE data >= '$entrada' and data <= '$folha_fim'");
				$dias_trab = mysql_num_rows($REDatas);
				$result    = '3';
						
			// Entrou depois da Data Inicial e saiu antes da Data Final
			} elseif($entrada >= $folha_ini and $saida <= $folha_fim and $status == '0') {
				
				$REDatas   = mysql_query("SELECT data FROM ano WHERE data >= '$entrada' AND data <= '$saida'");
				$dias_trab = mysql_num_rows($REDatas) + 1;
				$result    = '4';
						
			// Entrou antes da Data Inicial e não saiu
			} else {
				
				$dias_trab = $row_folha['qnt_dias'];
				$result    = '1';
				
			}
	
			// Verifica se já está na outra folha do mesmo mês finalizada (mudado sabino (terceiro != '1') e ano
			$qr_verificacao = mysql_query("SELECT * FROM $tabela_now WHERE mes = '$row_folha[mes]' AND id_autonomo = '$row_aut[0]' AND terceiro = '0' AND '$ano' = '$row_folha[ano]' AND status = '3'");
			$verificacao    = mysql_num_rows($qr_verificacao);
			
			// Inserindo os Participantes
			if(empty($verificacao)) {
				
				if($row_folha['contratacao'] == 1) {
					
					// Definindo Classe Cálculos
					$Trab = new proporcional();
					
					// Consulta do Curso do Participante
					$qr_curso       = mysql_query("SELECT b.salario FROM autonomo a INNER JOIN curso b ON a.id_curso = b.id_curso WHERE a.id_autonomo = '$row_aut[0]'");
					@$salario_limpo = mysql_result($qr_curso, 0);
					
					$Trab   -> calculo_proporcional($salario_limpo, $dias_trab);
					$diaria  = $Trab -> valor_dia;
					$salario = $Trab -> valor_proporcional;
					
					$salario_liquido = $salario;
					
					mysql_query("INSERT INTO $tabela_now (id_folha, mes, ano, regiao, projeto, data_pro, id_autonomo, nome, cpf, banco, agencia, conta, dias_trab, diaria, salario_limpo, salario, salario_liq, tipo_pg, sit, result, status) VALUES ('$folha', '$row_folha[mes]', '$row_folha[ano]', '$regiao', '$row_folha[projeto]', '$row_folha[data_proc]', '$row_aut[0]', '$row_aut[nome]', '$row_aut[cpf]', '$row_aut[banco]', '$row_aut[agencia]', '$row_aut[conta]', '$dias_trab', '$diaria', '$salario_limpo', '$salario', '$salario_liquido', '$row_aut[tipo_pagamento]', '1', '$result', '2');") or die ('Erro: '.mysql_error());
				
				} else {
					
					mysql_query("INSERT INTO $tabela_now (id_folha, mes, ano, regiao, projeto, data_pro, id_autonomo, nome, cpf, banco, agencia, conta, dias_trab, tipo_pg, sit, result, status) VALUES ('$folha', '$row_folha[mes]', '$row_folha[ano]', '$regiao', '$row_folha[projeto]', '$row_folha[data_proc]', '$row_aut[0]', '$row_aut[nome]', '$row_aut[cpf]', '$row_aut[banco]', '$row_aut[agencia]', '$row_aut[conta]', '$dias_trab','$row_aut[tipo_pagamento]', '1', '$result', '2');") or die ('Erro: '.mysql_error());
					
				}
				
			} // Fim da Inserção dos Participantes

		} // Fim do Loop dos Participantes

		$parte += 1;
		header('Location: folha2.php?m=1&enc='.$enc.'&parte='.$parte);
		exit;

	} // Fim da verificação para Insert dos Partipantes

mysql_query("UPDATE folhas SET status = '2', participantes = '$total' WHERE id_folha = '$folha' LIMIT 1");

//if(empty($total_participantes_real)) {
	//header('Location: folha2.php?enc='.$enc.'&voltar=true');
//} else {
	if($row_folha['contratacao'] == 1) {
		header('Location: sintetica.php?enc='.str_replace('+','--',encrypt("$nulo&$folha")));
	} else {
		header('Location: sinteticacoo.php?enc='.str_replace('+','--',encrypt("$regiao&$folha&2")));
	}
//}

exit;

} // Fim da verificação para Insert da Folha



// Inclusão
if(!empty($_REQUEST['inclusao'])) {
	
    
	$aut      = $_REQUEST['aut'];
	$id_folha = $_REQUEST['folha'];
	
	// Selecionando os dados da folha pelo id_folha
	$result_folha = mysql_query("SELECT * FROM folhas WHERE id_folha = '$id_folha' LIMIT 1");
	$row_folha    = mysql_fetch_array($result_folha);
	
     
        
	foreach($aut as $id_autonomo) {
		
		$re  = mysql_query("SELECT nome,cpf,banco,agencia,conta,tipo_contratacao,tipo_pagamento FROM autonomo WHERE id_autonomo = '$id_autonomo'");
		$row = mysql_fetch_array($re);
		
                
                
		if($row['tipo_contratacao'] == 1) {
			mysql_query("INSERT INTO folha_autonomo(id_folha, mes, regiao, projeto, data_pro, id_autonomo, nome, cpf, banco, agencia, conta, dias_trab, tipo_pg, sit, result, status) 
			                  VALUES ('$id_folha', '$row_folha[mes]', '$row_folha[regiao]', '$row_folha[projeto]', '$row_folha[data_proc]', '$id_autonomo', '$row[nome]', '$row[cpf]', '$row[banco]', '$row[agencia]', '$row[conta]', '$row_folha[qnt_dias]', '$row[tipo_pagamento]', '1', '4', '2')");
		} else {
			mysql_query("INSERT INTO folha_cooperado(id_folha,mes,regiao,projeto,data_pro,id_autonomo,nome,cpf,banco,agencia,conta,dias_trab,tipo_pg,sit,result,status) 
			                  VALUES ('$id_folha', '$row_folha[mes]', '$row_folha[regiao]', '$row_folha[projeto]', '$row_folha[data_proc]', '$id_autonomo', '$row[nome]', '$row[cpf]', '$row[banco]', '$row[agencia]', '$row[conta]', '$row_folha[qnt_dias]', '$row[tipo_pagamento]', '1', '4', '2')");
		}
		
	}
	
	$encinc = str_replace('+','--',encrypt("$row_folha[regiao]&$id_folha&2"));	
	echo "<script>location.href='folha2.php?enc=$encinc';</script>";
	exit;

}





// Encriptografando Marcados / Desmarcados
$encmar = str_replace('+','--',encrypt("$regiao&$folha&2"));
$encdes = str_replace('+','--',encrypt("$regiao&$folha&1"));
$encinc = str_replace('+','--',encrypt("$regiao&$folha&5"));

// Botão Voltar
$botao_voltar = str_replace('+','--',encrypt("$nulo&$folha"));

// Mes
$meses        = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
$mes_INT      = (int)$row_folha['mes'];
$mes_da_folha = $meses[$mes_INT];
?>
<html>
<head>
<meta http-equiv="Content-Type"  content="text/html; charset=iso-8859-1" />
<meta http-equiv="Cache-Control" content="No-Cache">
<meta http-equiv="Pragma"        content="No-Cache">
<meta http-equiv="Expires"       content="0">
<title>:: Intranet :: Inicio da Folha Sint&eacute;tica</title>
<link href="sintetica/folha.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="../js/ramon.js"></script>
<script language="javascript" type="text/javascript" src="../js/jquery-1.3.2.js"></script>
<script language="javascript" type="text/javascript">
$(function(){
    
    $('#procurar').click(function(){
        
        var  nome   = $('#nome').val();
        var projeto = $('#id_projeto').val();
        var folha  = $('#folha').val();
        
        
        $.post('folha2.php', 
		{ 'ajax_procurar' : 1, 'nome': nome,  'id_projeto': projeto, 'id_folha': folha},
		function(retorno){
                    
                  $('#retorno').html(retorno);
                })
        
    })
    
	/*$('#nome').keyUp(function(){
                
		var valor = $(this).val();
                
                if(valor.length >2) {
		$.post('folha2.php', 
		{ 'ajax' : valor, 'id': $('#dados').val()},
		function(retorno){
			
			
			
			var url = "http://<?php echo $_SERVER['HTTP_HOST']?>/intranet/";
			
                       // $('#nome').css('background', 'transparent url('+ url + 'imagens/carregando/CIRCLE_BALL_branco.gif) no-repeat scroll right center');
			
				var resposta = retorno;
				//alert(destino + '?id=' + idajax + '&ajax=' + enviar);
				if(resposta == "ERRO"){
                               
                                 //    $('#nome').css('background', 'transparent url(' + url + 'imagens/yellow-status.gif) no-repeat scroll right center');
			
				}else{
                                   //  $('#nome').css('background', 'transparent url(' + url + 'imagens/green-status.gif) no-repeat scroll right center');
                                     $('#retorno').html(resposta);
				}
                               
				
		},'html'		
		);
                 }   
	});*/
	
	$('input[name="todos"]').change(function(){
		
		
		if($(this).attr('checked') == true ){
			
			$('input[type="checkbox"]').trigger('click');
			
		} else {
			
			$('input[type="checkbox"]').trigger('click');
		}
		
	});
});
</script>
</head>
<body onLoad="limpaCache('folha2.php');">
<div id="corpo">
    <?php if($_GET['voltar'] == false) { ?>
    
    <table cellspacing="4" cellpadding="0" id="topo">
      <tr>
        <td width="15%" rowspan="4" valign="middle" align="center">
           <img src="../imagens/logomaster<?=$row_user['id_master']?>.gif" alt="" width="110" height="79">
        </td>
        <td colspan="3" style="font-size:12px;">
        	<b><?=mysql_result($qr_projeto, 0, 1).' ('.htmlentities($mes_da_folha, ENT_COMPAT, 'iso-8859-1').' / '.$row_folha['ano'].')'?></b>
        </td>
      </tr>
      <tr>
        <td width="35%"><b>Data da Folha:</b> <?=$row_folha['data_inicio2'].' &agrave; '.$row_folha['data_fim2']?></td>
        <td width="30%"><b>Região:</b> <?=$regiao.' - '.mysql_result($qr_regiao, 0, 1)?></td>
        <td width="20%"><b>Participantes:</b> <?=$total_participantes?></td>
      </tr>
      <tr>
        <td><b>Data de Processamento:</b> <?=$row_folha['data_proc2']?></td>
        <td><b>Gerado por:</b> <?php echo abreviacao(mysql_result($qr_usuario, 0), 2); ?></td>
        <td><b>Folha:</b> <?=$folha?></td>
      </tr>
    </table>
    
    <table cellpadding="0" cellspacing="0" class="folha">
        <tr>
          <td colspan="2">
            <?php if($row_folha['contratacao'] == 1) { ?>
          		<a href="sintetica.php?enc=<?=$botao_voltar?>" class="voltar">Voltar</a>
            <?php } else { ?>
            	<a href="sinteticacoo.php?enc=<?=$botao_voltar?>" class="voltar">Voltar</a>
            <?php } ?>
          </td>
          <td colspan="3" align="right">
            <a href="folha2.php?enc=<?=$encmar?>" class="botao<?php if($st == 2) { echo '_ativo'; } ?>">Marcados</a>
            <a href="folha2.php?enc=<?=$encdes?>" class="botao<?php if($st == 1) { echo '_ativo'; } ?>">Desmarcados</a>
            <a href="folha2.php?enc=<?=$encinc?>" class="botao<?php if($st == 5) { echo '_ativo'; } ?>">Inclusão</a>
          </td>
        </tr>
    </table> 

<?php } if($st != 5) {

// Paginação
$nav           = "%s?pagina=%d%s&enc=$enc";
$max_logs      = 50;
$numero_pagina = 0;

if(!empty($_GET['pagina'])) {
    $numero_pagina = $_GET['pagina'];
}

$start_log     = $numero_pagina * $max_logs;
$qr_prelog     = "SELECT * FROM $tabela_now WHERE id_folha = '$folha' AND status = '$st' ORDER BY nome ASC";
$qr_limit_log  = sprintf("%s LIMIT %d, %d", $qr_prelog, $start_log, $max_logs);
$qr_log        = mysql_query($qr_limit_log) or die(mysql_error());
$all_logs      = mysql_query($qr_prelog);
$total_logs    = mysql_num_rows($all_logs);
$total_paginas = ceil($total_logs/$max_logs)-1;


$total_now	   = mysql_num_rows($qr_log);

list($dia,$mes,$ano) = explode('/',$data);

if(empty($total_logs)) {
	
	// Encriptografando a variável
	$linkvolt1 = str_replace('+','--',encrypt("$regiao&regiao"));
	
	if($_GET['voltar'] == true) {
		$botao = '<a href="folha.php?enc='.$linkvolt1.'&id=9" class="botao">Voltar</a>';
	}
	
	echo '<table width="95%" border="0" align="center">
	        <tr>
			  <td align="center">
			    <div class="title">Nenhum participante encontrado</div>
				<p>&nbsp;</p>'.$botao.'
	          </td>
			</tr>
	      </table>';
	exit;
	
} ?>
  
    <form action="" method="post" name="Form" onSubmit="return ValidaForm()">
      <table cellpadding="0" cellspacing="1" class="folha">
        <tr class="secao">
          <td width="5%"><input type="checkbox" name="todos"/></td>
          <td width="5%" style="text-align:center;">COD</td>
          <td width="28%" style="text-align:left; padding-left:7px;">NOME</td>
          <td width="28%">ATIVIDADE</td>
          <td width="34%">UNIDADE</td>
        </tr>
        
        <?php // Informações para o Ajax
		      // ajupdatecheck(tabela,campo,nomeid,id,tipoaj)
			  
		$tb_aj 	   = $tabela_now;
		$nomeid_aj = 'id_folha_pro';
		$campo_aj  = 'status';
	
		$cont = 0;
		$num_total = mysql_num_rows($qr_log);
		
		while($row_aut = mysql_fetch_array($qr_log)) {
		
			// Recolhendo informações de Autônomo / Cooperado
			$Part   -> MostraParticipante($row_aut['id_autonomo']);
			$locacao = $Part -> locacao;
			$campo3  = $Part -> campo3;
			
			$Part   -> CursoParticipante($row_aut['id_autonomo']);
			$nome_curso = $Part -> campo2;
			
				$nome  = str_split($row_aut['nome'], 35);
				$nomeT = sprintf('% -40s', $nome[0]);
				$bord  = 'style="border-bottom:#000 solid 1px;"';
				$nomeC = str_replace('CAPACITANDO EM', 'CAP. EM', $nome_curso);
				$aj    = " onClick=\"ajupdatecheck('$tb_aj',this.id,'$nomeid_aj','$row_aut[0]','1')\" ";
			
				if($row_aut['status'] == '1') {
					$chek = NULL;
				} else {
					$chek = 'checked';
				}
		
				print '<tr class="linha_'; if($linha++%2==0) { echo 'um'; } else { echo 'dois'; } echo ' destaque" $linhaTab>
						   <td><div id="retorno_'.$cont.'"><input name="status_'.$cont.'" id="status_'.$cont.'" type="checkbox" value="'.$row_aut[0].'" '.$chek.' '.$desabilitado.' '.$aj.'></div></td>
						   <td>'.$row_aut['id_autonomo'].'</td>
						   <td style="text-align:left; padding-left:7px;">'.$nomeT.'</td>
						   <td>'.$nomeC.'</td>
						   <td>'.$locacao.'</td>
					   </tr>';
				  
				unset($dias_trab,$result);
				
				$cont ++;
		
			} ?>
           <tr class="totais" style="text-align:right;">
              <td colspan="4">
              	<?php if($total_now > 10) { ?>
                	<a href="#corpo" class="ancora">Subir ao topo</a>
                <?php } ?>
                <td align="right"><?php echo '<b>Página</b> '.($numero_pagina+1).'/'.($total_paginas+1); ?></td>
            </tr>
            
          <tr class="totais" style="text-align:center;">
             <td colspan="5">
                  <?php //echo $total_logs.' Participantes em '.$total_paginas.' paginas<br /><br />Página atual: '.$pg_now; ?>

          <input type="hidden" name="id_regiao"  value="<?=$regiao?>">
          <input type="hidden" name="id_projeto" value="<?=$row_folha['projeto']?>">
          <input type="hidden" name="id_folha"   value="<?=$folha?>">
          <input type="hidden" name="data_proc"  value="<?=$row_folha['data_proc']?>">
          <input type="hidden" name="mes"        value="<?=$row_folha['mes']?>">
          <input type="hidden" name="vale"       value="<?=$vale?>">
          <input type="hidden" name="total"      value="<?=$num_total?>">
          <img src="../imagens/carregando/loading.gif" border="0" style="display:none">
		  <br>
          <?php // Paginação
                if($numero_pagina > 0) { ?>
                    <a href="<?php printf($nav, $currentPage, 0, $string); ?>" class="botao">&laquo; Primeira</a>&nbsp;
                <?php } if($numero_pagina == 0) { ?>
                    <span class="morto">&laquo; Primeira</span>&nbsp;
                <?php } if($numero_pagina > 0) { ?>
                    <a href="<?php printf($nav, $currentPage, max(0, $numero_pagina - 1), $string); ?>" class="botao">&#8249; Anterior</a>&nbsp;
                <?php } if($numero_pagina == 0) { ?>
                    <span class="morto">&#8249; Anterior</span>&nbsp;
                <?php } 
               
                  for($i=0; $i<=$total_paginas;$i++){                     
                     $link    = sprintf($nav, $currentPage, $i, ''); 
                     if($numero_pagina == $i){
                         echo ' <span class="atual">'.($i+1).'</span>&nbsp;';
                     }else {
                         echo '<a href="'.$link.'" class="botao"> '.($i+1).'</a>';
                     }
                  }              
                
                if($numero_pagina < $total_paginas) { ?>
                    <a href="<?php printf($nav, $currentPage, min($total_paginas, $numero_pagina + 1), $string); ?>" class="botao">Próxima &#8250;</a>&nbsp;
                <?php } if($numero_pagina >= $total_paginas) { ?>
                    <span class="morto">Próxima &#8250;</span>&nbsp;                   
                <?php } if($numero_pagina < $total_paginas) { ?>
                    <a href="<?php printf($nav, $currentPage, $total_paginas, $string); ?>" class="botao">Última &raquo;</a>
                <?php } if($numero_pagina >= $total_paginas) { ?>
                    <span class="morto">Última &raquo;</span>
                <?php } // Fim da Paginação ?>
                
                <script language="javascript" type="text/javascript">
                function ValidaForm(){
                    var Nocheck = 0;
                    var Yescheck = 0;
                    var d = document.Form;
                    var contaForm = d.elements.length;
                    contaForm = contaForm - 8;
                    
                    for (i=0 ; i<contaForm ; i++){
                        if (d.elements[i].id == "id_clt") {
                            if (!d.elements[i].checked){
                                Yescheck ++;
                            }else{
                                Nocheck++;
                            }
                        }
                    }
                    
                    if(Nocheck == 0){
                        alert ("Escolha ao menos 1 CLT");
                        return false;
                    }
                    
                }
                </script>
 
                </td>
           </tr>
          
            </table>
    
        </form>
        
        

	<?php } else { ?>
		<form action="folha2.php" method="post" id="form1">
		<table border="0" cellspacing="2" cellpadding="0" align="center">
		  <tr>
			<td>
                                    
                         NOME: <input name="nome" type="text" id="nome" size="45" value="Pesquise aqui o nome do participante" onBlur="if(this.value=='') {this.value='Pesquise aqui o nome do participante';}" onFocus="if(this.value=='Pesquise aqui o nome do participante') {this.value='';}"></td>
                        <td><input type="button" value="Procurar" class="botao" id="procurar"></td>
		  </tr>
		</table>
		
		
			<div id="retorno">&nbsp;</div>
			<input type="hidden" name="ajax" id="ajax" value="1">
			<input type="hidden" name="folha"    id="folha" value="<?php echo $folha?>">
			<input type="hidden" name="id_projeto"    id="id_projeto" value="<?php echo $row_folha['projeto']?>">
			<input type="hidden" name="id_regiao"     id="id_regiao" value="<?php echo $row_folha['regiao']; ?>">
                        <input type="hidden" name="inclusao" value="1"/>
                      
			<input type="submit" value="Concluir" class="botao">
		</form>
	
	<?php } ?>

</div>
</body>
</html>