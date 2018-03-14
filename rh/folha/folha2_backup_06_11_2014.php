<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='www.netsorrindo.com.br/intranet/login.php'>Logar</a> ";
exit;
}

include "../../conn.php";
include "../../funcoes.php";
include "../../classes/funcionario.php";
include "../../classes/clt.php";
include "../vtfuncao/dias_trab.php";
include("../../classes/FolhaClass.php");

$obj = new dias_trab();
$Fun = new funcionario();
$Clt = new clt();

//RECEBENDO A VARIAVEL CRIPTOGRAFADA
$encO = $_REQUEST['enc'];
$enc = str_replace("--","+",$encO);
$link = decrypt($enc);
$decript = explode("&",$link);

$regiao = $decript[0];
$folha = $decript[1];
$st = $decript[2];
//RECEBENDO A VARIAVEL CRIPTOGRAFADA

$array_rescisao = array(60,61,62,81,63,101,64,65,66); ///STATUS DE RESCISÃO

//SELECIONANDO O USUARIO QUE ESTÁ PROCESSANDO A FOLHA
$Fun -> MostraUser(0);
$id_user = $Fun -> id_user;

//SELECIONANDO O MASTER
$Fun -> MostraMaster(0);
$id_master 	= $Fun -> id_master;
$razao 		= $Fun -> razao;
$cnpj		= $Fun -> cnpj;

//SELECIONANDO OS DADOS DA FOLHA PELO ID_FOLHA
$result_folha = mysql_query("SELECT *,date_format(data_proc, '%d/%m/%Y')as data_proc2,date_format(data_inicio, '%d/%m/%Y')as data_inicio2,date_format(data_fim, '%d/%m/%Y')as data_fim2 FROM rh_folha where id_folha = '$folha'");
$row_folha = mysql_fetch_array($result_folha);

$MesFolha = $row_folha['mes'];

//SELECIONANDO O PROJETO PELO ID_PROJETO GRAVADO NA TABELA FOLHA
$result_projeto = mysql_query("SELECT * FROM projeto where id_projeto = '$row_folha[projeto]'");
$row_projeto = mysql_fetch_array($result_projeto);

//SELECIONANDO GALERA QUE NÃO ESTEJAM DE FÉRIAS E FORA DE RESCISAO

/* ANTES DA ALTERAÇÃO PARA GRAVAR O ID_CURSO E O ID_HORARIO
 * $result_clt = mysql_query("SELECT * FROM rh_clt WHERE id_projeto = '$row_folha[projeto]' AND ( data_saida > '$row_folha[data_inicio]' OR
    data_saida = '0000-00-00' OR data_saida = NULL) AND data_entrada <= '$row_folha[data_fim]' AND status != 200 ORDER BY nome ASC");
*/

$result_clt = mysql_query(" SELECT A.*,  C.id_horario FROM rh_clt as A
LEFT JOIN curso as B
ON B.id_curso = A.id_curso
LEFT JOIN rh_horarios as C
ON C.funcao = B.id_curso
WHERE A.id_projeto = '$row_folha[projeto]' 
AND ( A.data_saida >= '$row_folha[data_inicio]' OR A.data_saida = '0000-00-00' OR A.data_saida = NULL) 
AND A.data_entrada <= '$row_folha[data_fim]' 

GROUP BY A.id_clt 
ORDER BY A.nome ASC;");
$num_clt = mysql_num_rows($result_clt);



#Essa query não seleciona os que saíram antes da data de inicio da folha
// FIM SELECIONA


#AKI COMEÇA A PRIMEIRA PARTE DO ARQUIVO, QUE SELECIONA OS CLTS DA TABELA RH_CLT, E OS INSERE NA TABELA RH_FOLHA_PROC
if(!empty($_REQUEST['m'])){

# SE NÃO TIVER NENHUM CLT.. MENSAGEM DE ERRO
if($num_clt == 0){
	
	//-- ENCRIPTOGRAFANDO A VARIAVEL
	$linkvolt1 = encrypt("$regiao&1"); 
	$linkvolt1 = str_replace("+","--",$linkvolt1);
	// -----------------------------
	
	print "<center><img src='imagens/ops.png'><br><font color=red size=5>OPS</font><br><div class='title'>Infelizmente não é possivel gerar essa 
	folha de pagamento, pois não existem CLTs cadastrados nesse projeto!
	</div><br><br>";
	print "<b><a href='folha.php?enc=$linkvolt1&tela=1' style='text-decoration:none; color:#000'>VOLTAR</a></b>";
	exit;
}

$cont = 0;

while($row_clt = mysql_fetch_array($result_clt)) {
	
	
	
	
	// CALCULO DE VALE TRANSPORTE
	if($row_folha['terceiro'] != 1 and $row_folha['tipo_terceiro'] != 2) {
		$data_inicio_vt = implode('-', array_reverse(explode('/', $row_folha['data_inicio'])));
		$data_fim_vt    = implode('-', array_reverse(explode('/', $row_folha['data_fim'])));
		$obj -> calcperiodo($data_inicio_vt, $data_fim_vt, $row_clt[0]);
		$teste = $obj ->imprimir();
		$vt_valor = $obj ->imprimir_valor();
	}
	// FIM DE CALCULO DE VALE TRANSPORTE
	
	
	
	
	#QUANDO FOR FOLHA DE DECIMO TERCEIRO
	if($row_folha['terceiro'] == 1 and  $row_folha['tipo_terceiro'] == 2) {
	
                $result = mysql_query("SELECT id_folha FROM rh_folha WHERE regiao = '$row_folha[regiao]' AND projeto = '$row_folha[projeto]' AND terceiro = '1' 
		AND tipo_terceiro = '1' AND ano = '$row_folha[ano]' and status = '3'");
		$row_folha2 = mysql_fetch_array($result);
		
		if(mysql_num_rows($result) != 1) {
			echo "Erro! Existem 2 folhas de 13º Primeira parte para o mesmo projeto! Ou a folha de 13º Primeira parte não foi finalizada";
			//exit;
		}
                
		
		$result_clt1 = mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = '$row_folha2[0]' AND status = '3'");
                
                
		while($row_clt1 = mysql_fetch_array($result_clt1)){
		 
		  
		//////////////////////////////////////////////////////////////////////////////////////
		// INSERINDO OS CLTS NA TABELA RH_FOLHA_PROC SÓ SE FOR DT RIMEIRA E SEGUNDA PARCELA //
	    //////////////////////////////////////////////////////////////////////////////////////                    
		//$re_clt = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$row_clt1[id_clt]'");
		$re_clt = mysql_query("SELECT A.*, C.id_horario FROM rh_clt as A
                                        INNER JOIN curso as B
                                        ON B.id_curso = A.id_curso
                                        INNER JOIN rh_horarios as C
                                        ON C.funcao = B.id_curso
                                        WHERE A.id_clt = '$row_clt1[id_clt]' ORDER BY A.nome ASC;");
                    
		$row_clt = mysql_fetch_array($re_clt);
		       
                  
		  
            
               if(!in_array($row_clt['status'], $array_rescisao)){  
                                
                                mysql_query("INSERT INTO rh_folha_proc(id_clt,id_regiao,id_projeto,id_folha,data_proc,user_proc,mes,ano,cod,nome,tipo_pg,status, folha_proc_salario_outra_empresa, folha_proc_desconto_outra_empresa , folha_proc_diferenca_inss, id_curso, id_horario ) values 
                                  ('$row_clt[0]','$regiao','$row_folha[projeto]','$folha','$row_folha[data_proc]','$id_user','$row_folha[mes]','$row_folha[ano]',
                                  '$row_clt[campo3]','$row_clt[nome]','$row_clt[tipo_pagamento]','2','$row_clt[salario_outra_empresa]', '$row_clt[desconto_outra_empresa]','$row_clt[valor_desconto_inss]', '$row_clt[id_curso]', '$row_clt[id_horario]');") or die ("Erro <br><br>".mysql_error());

                                    //COLOCANDO STATUS 1 NOS MOVIMENTOS DOS CLTS SELECIONADOS PARA ESSA FOLHA
                                    mysql_query("UPDATE rh_movimentos_clt SET status_folha=1 WHERE id_clt='$row_clt[0]' and mes_mov='$row_folha[mes]'
                                    AND ano_mov='$row_folha[ano]'") or die ("Erro <br><br>".mysql_error());
                            }  
		} // FIM WHILE
		
                
                
                
		mysql_query("UPDATE rh_folha SET status = '2', clts = '".mysql_num_rows($result_clt)."' WHERE id_folha = $folha LIMIT 1 ;");
			
		$encinc = encrypt("$row_folha[regiao]&$folha&2"); 
		$encinc = str_replace("+","--",$encinc);
			
		print "<script>
		location.href=\"folha2.php?enc=$encinc\"
		</script>";
		exit;
		
	}// FIM DÉCIMO TERCEIRO
	
	
        
        ///ALTERAÇÃO PARA GERAR A FOLHA DE DÉCIMO TERCEIRO DE TER GERADO A FOLHA DE DEZEMBRO  	
        if($row_folha['terceiro'] == 1 and  $row_folha['tipo_terceiro'] == 3)  {
            
            $RE_verifica = mysql_query("SELECT * FROM rh_folha_proc a INNER JOIN rh_folha b ON a.id_folha = b.id_folha WHERE a.mes = '$row_folha[mes]' AND a.id_clt = '$row_clt[0]' AND a.ano = '$row_folha[ano]' AND a.status = '3' AND b.terceiro = '3'");
            $row_CLTVerifica = mysql_num_rows($RE_verifica);
        
        } else{
            
            // VERIFICANDO SE O CARA JA ESTÁ NA OUTRA FOLHA
            $RE_verifica = mysql_query("SELECT * FROM rh_folha_proc a INNER JOIN rh_folha b ON a.id_folha = b.id_folha WHERE a.mes = '$row_folha[mes]' AND a.id_clt = '$row_clt[0]' AND a.ano = '$row_folha[ano]' AND a.status = '3' AND b.terceiro = '2'");
            $row_CLTVerifica = mysql_num_rows($RE_verifica);
		
        }
        
        
        
     
        
        
	//  SE O CARA ESTIVER NA OUTRA FOLHA JA FECHADA O INPUT DELE VAI FICAR DESABILITADA E NÃO É GRAVADO NA RH_FOLHA_PROC
	if($row_CLTVerifica != 0){
		$desabilitado = "disabled";
                
                  	
	}else{
		// CASO ELE NÃO ESTEJA NA OUTRA FOLHA, VAI SER GRAVADO NA TABELA RH_FOLHA_PROC COM O ID DESSA NOVA FOLHA
		$desabilitado = "";
				
		  ////////////////////////////////////////////////
		  // INSERINDO OS CLTS NA TABELA RH_FOLHA_PROC //
		  //////////////////////////////////////////////
		 
		  # !IMPORTANTE! VERIFICA QUAL O STATUS DA PESSOA, PARA ENTRAR NA FOLHA OU NÃO
		  switch ($row_clt['status']) {
			  case 10:
                          case 20:    
                          case 30:    
			  	$ferias = "";
				$insere = "sim";
			  break;
			  case 40:
			    $ferias = 1;
				$insere = "sim";
			  break;
			  case 50:
			  	$ferias = "";
				$insere = "sim";
			  break;
			  case 200:
			    #AGUARDANDO DEMISSAO SÓ VAI ENTRAR SE A DEMISSÃO FOR DEPOIS DO MES DA FOLHA
			    $exp_data_demi = explode("-",$row_clt['data_demi']);
				$exp_data_ini = explode("-",$row_folha['data_inicio']);
				# SE 2009-10-01 MAIOR Q 2009-06-01 E 10 NAO FOR IGUAL A 06 = TRUE
				# SE 2009-06-05 MAIOR Q 2009-06-01 E 06 NAO FOR IGUAL A 06 = FALSE
				 if($row_clt['data_demi'] >= $row_folha['data_inicio'] 
                                    or $row_clt['data_demi'] == '0000-00-00'  
                                    or empty($row_clt['data_demi']))
                                {
					$ferias = "";
					$insere = "sim";
				}else{
					$ferias = "";
					$insere = "nao";
				}
			  break;
                          
			  case 60: 
                          case 61:
                          case 62: 
                          case 81:
                          case 63: 
                          case 101:
                          case 64:
                          case 65: 
                          case 66:
			  if($row_folha['terceiro'] != 1 ){                                     
                                        # VERIFICA SE SAIU NO MESMO MES DA FOLHA PARA GRAVAR STATUS DE RESCISÃO NESSA FOLHA
                                       if($row_clt['data_demi'] >= $row_folha['data_inicio']){
                                                $ferias = 2;
                                                $insere = "sim";                                                
                                        }else{
                                                $ferias = "";
                                                $insere = "nao";                              
                                        }
                              }else {
                                  $insere = 'nao';
                              }
			  break;
		  }
		  
		  
		  $entrada = explode("-",$row_clt['data_entrada']);
		  $inicio  = explode("-",$row_folha['data_ini']);
		  # SE 2009-06-25 MAIOR Q 2009-06-01 E SE 06 = 06 = TRUE
		  # SE 2009-06-25 MAIOR Q 2009-06-01 E SE 07 = 06 = FALSE
		  if($row_clt['data_entrada'] >= $row_folha['data_inicio'] and $row_clt['data_entrada'] <= $row_folha['data_fim']){
			  $dias_trab = $entrada[2] - $inicio[2];
			  $dias_trab = (30 - $dias_trab) + 1;
			  $dias_trab = ($dias_trab == 0) ? "1" : $dias_trab;
			  $dias_trab = ($row_clt['data_entrada'] == $row_folha['data_inicio']) ? "30" : $dias_trab;
			  $insere = "sim";
			  
		  }
		 
		 
		  if($insere == "sim" ){
		  mysql_query("INSERT INTO rh_folha_proc(id_clt,id_regiao,id_projeto,id_folha,data_proc,user_proc,mes,ano,cod,nome,dias_trab,
		  status,tipo_pg,ferias, folha_proc_salario_outra_empresa, folha_proc_desconto_outra_empresa, folha_proc_diferenca_inss,id_curso, id_horario ) VALUES 
		  ('$row_clt[0]','$regiao','$row_folha[projeto]','$folha','$row_folha[data_proc]','$id_user','$row_folha[mes]','$row_folha[ano]',
		  '$row_clt[campo3]','$row_clt[nome]','$dias_trab','2','$row_clt[tipo_pagamento]','$ferias', '$row_clt[salario_outra_empresa]', '$row_clt[desconto_outra_empresa]', '$row_clt[valor_desconto_inss]','$row_clt[id_curso]','$row_clt[id_horario]')") or die ("Erro <br><br>".mysql_error());
		  
		  //COLOCANDO STATUS 1 NOS MOVIMENTOS DOS CLTS SELECIONADOS PARA ESSA FOLHA
		  mysql_query("UPDATE rh_movimentos_clt SET status_folha=1 WHERE id_clt='$row_clt[0]' and mes_mov='$row_folha[mes]'
		  AND ano_mov='$row_folha[ano]'") or die ("Erro <br><br>".mysql_error());
		  /* ATÉ AKI.... */
		  # E TIRE O COMENTÁRIO DESSA LINHA DE BAIXO
		  #echo "$row_clt[nome]  $row_clt[status]  $row_clt[data_saida]  $row_clt[data_demi] ( $ferias = $dias_trab)<br>";
		  }
		  
		  unset($dias_trab);
		
		
	}
}

// FIM DE INSERT
/* PARA FAZER TESTES.. COMENTE DAKI */

	// -------- ATUALIZANDO A TAB GERAL DAS FOLHAS PARA STATUS 2 = GERADO
	$part_geral = $NumArCLTFerias + $num_clt;
	
	mysql_query("UPDATE rh_folha SET status = '2', clts = '$part_geral' WHERE id_folha = $folha LIMIT 1 ;");
	
	$encinc = encrypt("$row_folha[regiao]&$folha&2"); 
	$encinc = str_replace("+","--",$encinc);
	
print "<script>
location.href=\"folha2.php?enc=$encinc\"
</script>";

exit;
/* ATÉ AKI */

}// FIM DA PARTE PRINCIPAL, INSERÇÃO NO BANCO DE DADOS


// EXECUTANDO AJAX FORA DE TUDO PARA A INCLUSÃO
if(!empty($_REQUEST['ajax'])){
	$nom = $_REQUEST['ajax'];
	$proje = $_REQUEST['id'];
	$proje = explode("-",$proje);
	$re_autonomo = mysql_query("SELECT id_clt,nome,campo3,tipo_contratacao FROM rh_clt WHERE nome LIKE '%$nom%' AND id_projeto = '$proje[0]' ");
	$cont = '0';
	$retorno .= "<table width=\"700\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\n";
	
	while($row_aut = mysql_fetch_array($re_autonomo)){
		
		
		$re_folha_proc = mysql_query("SELECT id_clt FROM rh_folha_proc WHERE id_clt = '$row_aut[0]' AND id_folha = '$proje[1]' AND status = 2");
		$num = mysql_num_rows($re_folha_proc);
		
		if($num == 0){
			if($cont % 2){ $classcor="corfundo_um"; }else{ $classcor="corfundo_dois"; };
			$retorno .= "<tr class=\"novalinha $classcor\">\n<td><input type=\"checkbox\" name='aut[]' id='aut' value='$row_aut[0]'></td>";
			$retorno .= "<td align='left'>$row_aut[campo3]</td><td align='left'>$row_aut[nome]</td>\n</tr>\n";
			$cont ++;
		}
		
	}
	$retorno .= "</table>\n";
	print $retorno;
	
	exit;
}

# EXECUTANDO A INCLUSÃO
if(!empty($_REQUEST['inclusao'])){
    
        $folhas = new Folha();
	$aut = $_REQUEST['aut'];
	$id_folha = $_REQUEST['folha'];
	$tpstatus = $_REQUEST['status'];
	
	if(empty($_REQUEST['status'])){
		$fe = '0';
	}
	
	//SELECIONANDO OS DADOS DA FOLHA PELO ID_FOLHA
	$result_folha = mysql_query("SELECT * FROM rh_folha where id_folha = '$id_folha' LIMIT 1");
	$row_folha = mysql_fetch_array($result_folha);
	
	foreach ($aut as $id_autonomo){
            $re = mysql_query("SELECT nome,cpf,banco,agencia,conta,tipo_contratacao,tipo_pagamento,status FROM rh_clt WHERE id_clt = '$id_autonomo'");
            $row = mysql_fetch_array($re);

            if($row['status'] == '40'){
                    $fe = '1';
            }elseif($row['status'] == '201'){
                    $fe = '2';
            }
            #INCLUSÃO.. INSERT
            mysql_query("INSERT INTO rh_folha_proc(id_folha,mes,ano,id_regiao,id_projeto,data_proc,id_clt,nome,cpf,id_banco,agencia,conta,
            tipo_pg,status,ferias) VALUES ('$id_folha','$row_folha[mes]','$row_folha[ano]','$row_folha[regiao]','$row_folha[projeto]',
            '$row_folha[data_proc]','$id_autonomo','$row[nome]','$row[cpf]','$row[banco]','$row[agencia]','$row[conta]','$row[tipo_pagamento]','2','$fe')");
		
	}
        
        //MÉTOD PARA CRIAÇÃO DE LOG
        $folhas->logIncluirNaFolha($aut,$id_folha,$id_user);
	
	$encinc = encrypt("$row_folha[regiao]&$id_folha&2"); 
	$encinc = str_replace("+","--",$encinc);
	
	echo "<script>location.href = 'folha2.php?enc=$encinc'; </script>";
	
	exit;
}


//ENCRIPTOGRAFANDO MARCADOS / DESMARCADOS
$encmar = encrypt("$regiao&$folha&2"); 
$encmar = str_replace("+","--",$encmar);

$encdes = encrypt("$regiao&$folha&1"); 
$encdes = str_replace("+","--",$encdes);

$encinc = encrypt("$regiao&$folha&5"); 
$encinc = str_replace("+","--",$encinc);

$encresc = encrypt("$regiao&$folha&6"); 
$encresc = str_replace("+","--",$encresc);
// -----------------------------


?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>:: Intranet :: Inicio da Folha Sint&eacute;tica</title>
<link href="../../net1.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="../../js/ramon.js"></script>
<script src="../../js/jquery-1.8.3.min.js"></script>
<script>
    $(function(){
        $(".removeUsuario").click(function(){
           var folha = $("input[name='id_folha']").val();
           var usuario = $("input[name='id_usuario']").val();
           var clt = $(this).attr("data-key");
           
           if($(this).is(":checked")){
                $.ajax({
                  url:"../../methods.php",
                  type:"POST",
                  dataType:"json",
                  data:{
                      usuario:usuario,
                      clt:clt,
                      folha:folha,
                      method:"logRemoveDaFolha",
                      tipo: 2 //TIPO 1 = REMOVER, 2 = DESFAZER EXCLUSÃO
                  }
               });
           }else{
               $.ajax({
                  url:"../../methods.php",
                  type:"POST",
                  dataType:"json",
                  data:{
                      usuario:usuario,
                      clt:clt,
                      folha:folha,
                      method:"logRemoveDaFolha",
                      tipo: 1 //TIPO 1 = REMOVER, 2 = DESFAZER EXCLUSÃO
                  }
               });
           }
        });
    });
</script>
</head>

<body>

<table width="95%" border="0" align="center">
  <tr>
    <td align="center" valign="middle" bgcolor="#FFFFFF"><br />
      <table width="90%" border="0" align="center">
      <tr>
        <td height="115" colspan="3" align="center" valign="middle" class="show">
        <img src="../../imagens/logomaster<?=$id_master?>.gif" alt="" width="110" height="79"><br />
             <?=$razao?>
        </span></td>
      </tr>
      <tr class="linha">
        <td width="29%" height="29" align="center" valign="middle" bgcolor="#E2E2E2">Data de Processamento:
          <?=$row_folha['data_proc2']?></td>
        <td width="43%" height="29" align="center" valign="middle" bgcolor="#E2E2E2">CNPJ :  <?=$cnpj?></td>
        <td width="28%" height="29" align="center" valign="middle" bgcolor="#E2E2E2"><?="de: ".$row_folha['data_inicio2']." até ".$row_folha['data_fim2']?></td>
      </tr>
    </table>
<?php
if($st < 5){

//PAGINAÇÃO
$nav = "%s?pagina=%d%s&enc=$encO";
$max_logs = 50;
$numero_pagina = 0;
if(!empty($_GET['pagina'])) {
  $numero_pagina = $_GET['pagina'];
}

$start_log = $numero_pagina * $max_logs;


#$qr_prelog = "SELECT * FROM $tabela_now WHERE id_folha = '$folha' AND status = '$st' ORDER BY nome ASC ";
$qr_prelog = "SELECT * FROM rh_folha_proc WHERE id_folha = '$folha' AND status = '$st' ORDER BY nome ASC";

$qr_limit_log = sprintf("%s LIMIT %d, %d", $qr_prelog, $start_log, $max_logs);
$qr_log = mysql_query($qr_limit_log) or die(mysql_error());
//$log = mysql_fetch_assoc($qr_log);
$all_logs = mysql_query($qr_prelog);
$total_logs = mysql_num_rows($all_logs);
$total_paginas = ceil($total_logs/$max_logs)-1;

$meses = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
$mesINT = (int)$row_folha['mes'];
$mes_da_folha = $meses[$mesINT];
		
$ano = date("Y");
$mes = date("m");
$dia = date("d");
		
$data = date("d/m/Y");

if($total_logs == 0){
	
	//-- ENCRIPTOGRAFANDO A VARIAVEL
	$linkvolt1 = encrypt("$regiao&regiao"); 
	$linkvolt1 = str_replace("+","--",$linkvolt1);
	// -----------------------------
	
	echo '<table width="95%" border="0" align="center"><tr><td align="center" valign="middle" class="show">';
	print "<br><div class='title'>Não foi encontrado nenhum Participante na opção requisitada $st!
	</div><br><br>";
	print "<b><a href='javascript:history.go(-1);' class='botao'>VOLTAR</a></b><br /><br />";
	print "<b><a href='folha.php?enc=$linkvolt1' class='botao'>INICIO</a></b>";
	print "</td></tr></table>";
	exit;
	
}

?><br>
      <table width="531" border="0">
        <tr>
          <td width="172" align="center" bgcolor="#E2E2E2"><a href="folha2.php?enc=<?=$encmar?>">Marcados</a></td>
          <td width="184" align="center" bgcolor="#E2E2E2"><a href="folha2.php?enc=<?=$encdes?>">Desmarcados</a></td>
          <td width="161" align="center" bgcolor="#E2E2E2"><a href="folha2.php?enc=<?=$encinc?>">Inclus&atilde;o</a></td>
          <!-- <td width="136" align="center" bgcolor="#E2E2E2"><a href="folha2.php?enc=<?=$encresc?>">F&eacute;rias ou Rescis&atilde;o</a></td> -->
        </tr>
      </table>
      <br />
     <span class="titulo_opcoes">Folha -

      <?=$mes_da_folha?> / <?=$row_folha['ano']?></span><br />
      <br />
  
    <form action="" method="post" name="Form" onSubmit="return ValidaForm()">
      <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr class="novo_tr_dois">
          <td width="7%" height="25" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">C&oacute;digo</td>
          <td width="34%" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">Nome </td>
          <td width="28%" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">Atividade</td>
          <td width="31%" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">Unidade</td>
          </tr>
        
        <?php
		//SELECIONANDO GALERA QUE NÃO ESTEJAM DE FÉRIAS
		
		#INFORMAÇÕES PARA O AJAX
		#ajupdatecheck(tabela,campo,nomeid,id,tipoaj)
		$tb_aj 		= "rh_folha_proc";
		$nomeid_aj 	= "id_folha_proc";
		$campo_aj 	= "status";
	
		$cont = 0;
        while($row_clt = mysql_fetch_array($qr_log)){
		  
		  $Clt -> MostraClt($row_clt['id_clt']);
		  $nome 	= $Clt -> nome;
		  $id_curso	= $Clt -> id_curso;
		  $campo3 	= $Clt -> campo3;
		  $locacao 	= $Clt -> locacao;
		  
		  //SELECIONANDO O CURSO DE CADA CLT
		  $result_curso = mysql_query("SELECT * FROM curso where id_curso = '$id_curso'");
		  $row_curso = mysql_fetch_array($result_curso);
		  
		  //---- EMBELEZAMENTO DA PAGINA ----------------------------------
			if($cont % 2){ $classcor="corfundo_um"; }else{ $classcor="corfundo_dois"; };
			$nome = str_split($row_clt['nome'], 35);
			$nomeT = sprintf("% -40s", $nome[0]);
			$bord = "style='border-bottom:#000 solid 1px;'";
		  
			$nomeC = str_replace("CAPACITANDO EM","CAP. EM",$row_curso['nome']);
		//-----------------
		
		#PREPARANDO O AJAX, E A DIV PARA O RETORNO POSITIVO OU NEGATIVO DO AJAX
		$aj = " onClick=\"ajupdatecheck('$tb_aj',this.id,'$nomeid_aj','$row_clt[0]','1')\" ";
		$dvRE = "<div id=\"retorno_$cont\">$campo3</div>";
		if($row_clt['status'] == '1'){
			$chek = "";
		}else{
			$chek = "checked";
		}
		
		  
		  print"
		  <tr class=\"novalinha $classcor\">
		  <td align='center'>$dvRE</td>
		  <td align='lefth'><label>&nbsp;
		  <input name='status_".$cont."' id='status_".$cont."' type='checkbox' class='removeUsuario' data-key='" . $row_clt['id_clt'] . "' value='$row_clt[0]' $chek $desabilitado $aj>
		  $nomeT</label></td>
                    <td align='lefth'>$nomeC</td>
                    <td align='lefth'>$locacao</td>
		  </tr>";
		  
		  $cont ++;
		  
		 

		  }
		  
		  
		
		?>
        </table>
      <br>
      <?php
		if($numero_pagina == $total_paginas){
			$pg_now = $numero_pagina;
		}else{
			$pg_now = $numero_pagina + 1;
		}
		
		echo $total_logs." Participantes em ".$total_paginas." paginas<br /><br />Página atual: ".$pg_now;
		?>
      <br>
<input type="hidden" name="id_regiao" value="<?=$regiao?>">
      <input type="hidden" name="id_projeto" value="<?=$row_folha['projeto']?>">
      <input type="hidden" name="id_folha" value="<?=$folha?>">
      <input type="hidden" name="id_usuario" value="<?=$id_user?>">
      <input type="hidden" name="data_proc" value="<?=$row_folha['data_proc']?>">
      <input type="hidden" name="mes" value="<?=$row_folha['mes']?>">
      <input type="hidden" name="vale" value="<?=$vale?>">
      <input type="hidden" name="total" value="<?=$cont?>">
      <img src='../../imagens/carregando/loading.gif' border='0' style="display:none">
      <br>
      <?php
// Paginação

if ($numero_pagina > 0) { ?>
<a href="<?php printf($nav, $currentPage, 0, $string); ?>">&laquo; Primeira</a>&nbsp;
<?php }
if ($numero_pagina == 0) { ?>
<span class="morto">&laquo; Primeira</span>&nbsp;
<?php } 
if ($numero_pagina > 0) { ?>
<a href="<?php printf($nav, $currentPage, max(0, $numero_pagina - 1), $string); ?>">&#8249; Anterior</a>&nbsp;
<?php } 
if ($numero_pagina == 0) { ?>
<span class="morto">&#8249; Anterior</span>&nbsp;
<?php }
if ($numero_pagina < $total_paginas) { ?>
<a href="<?php printf($nav, $currentPage, min($total_paginas, $numero_pagina + 1), $string); ?>">Próxima &#8250;</a>&nbsp;
<?php } 
if ($numero_pagina >= $total_paginas) { ?>
<span class="morto">Próxima &#8250;</span>&nbsp;                   
<?php } 
if ($numero_pagina < $total_paginas) { ?>
<a href="<?php printf($nav, $currentPage, $total_paginas, $string); ?>">Última &raquo;</a>
<?php }                    
if ($numero_pagina >= $total_paginas) { ?>
<span class="morto">Última &raquo;</span>
<?php }
// Fim da Paginação
?>

<script language="javascript" type="text/javascript">

function selecionar_tudo(a){
	var contaForm = document.Form.elements.length;
	contaForm = contaForm - 7;
    var campo = document.Form;  
    var i; 

	for (i=0 ; i<contaForm ; i++){
		if (campo.elements[i].id == "id_clt") {
			campo.elements[i].checked = campo.CheckTodos.checked;
		}
	}
	
	
}

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

      <br>
       </form>
      <form action="" method="get" name="for2">
      Ir para página: 
      <input type="text" name="pagina" size="3">
      <input type="hidden" name="enc" value="<?=$encO?>">
      &nbsp;&nbsp;<input type="submit" name="ir" value="IR">
      </form>
      <br>
      <br>
      <a href="sintetica.php?enc=<?=$encmar?>" class="botao">CONTINUAR</a><br>
    <?php


}elseif($st == 5){
	
//-- ENCRIPTOGRAFANDO A VARIAVEL
$linkreg = encrypt("$id_regiao&$folha"); 
$linkreg = str_replace("+","--",$linkreg);
// -----------------------------	
	
?>
<br />

<table width="575" border="0">
  <tr>
    <td width="189" align="center" bgcolor="#E2E2E2"><a href="folha2.php?enc=<?=$encmar?>">Marcados</a></td>
    <td width="198" align="center" bgcolor="#E2E2E2"><a href="folha2.php?enc=<?=$encdes?>">Desmarcados</a></td>
    <td width="174" align="center" bgcolor="#E2E2E2"><a href="folha2.php?enc=<?=$encinc?>">Inclus&atilde;o</a></td>
    <!-- <td width="181" align="center" bgcolor="#E2E2E2"><a href="folha2.php?enc=<?=$encresc?>">F&eacute;rias ou Rescis&atilde;o</a></td> -->
  </tr>
</table>
<br>
<br>
<table width="500" border="0" cellspacing="2" cellpadding="0">
  <tr>
    <td width="13%" height="51" class="secao">NOME:</td>
    <td width="67%">&nbsp;&nbsp;
    <input name="nome" type="text" id="nome" size="45" onBlur="AjaxVarios('folha2.php',this.id,'retorno','<?=$row_folha['projeto']."-".$folha?>');"></td>
    <td width="20%" align="center"><input type="button" value="Procurar" class="botaodois"></td>
  </tr>
</table>
<br /><br />
<form action="folha2.php" method="post" id="form1">
<div id="retorno">&nbsp;</div>
<input type="hidden" name="inclusao" id="inclusao" value="1">
<input type="hidden" name="folha" id="folha" value="<?=$folha?>">
<br />
<input type="submit" value="Continuar" class="botao">

</form>
<?php

}else{

?>
<br>
<table width="575" border="0">
  <tr>
    <td width="189" align="center" bgcolor="#E2E2E2"><a href="folha2.php?enc=<?=$encmar?>">Marcados</a></td>
    <td width="205" align="center" bgcolor="#E2E2E2"><a href="folha2.php?enc=<?=$encdes?>">Desmarcados</a></td>
    <td width="167" align="center" bgcolor="#E2E2E2"><a href="folha2.php?enc=<?=$encinc?>">Inclus&atilde;o</a></td>
    <!-- <td width="181" align="center" bgcolor="#E2E2E2"><a href="folha2.php?enc=<?=$encresc?>">F&eacute;rias ou Rescis&atilde;o</a></td> -->
  </tr>
</table>
<br>
<form action="folha2.php" method="post" id="form3">
<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr class="novo_tr_dois">
    <td width="7%" height="25" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">C&oacute;digo</td>
    <td width="34%" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">Nome </td>
    <td width="28%" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">Atividade</td>
    <td width="31%" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">Unidade</td>
    </tr>
  <?php
		// SELECIONANDO GALERA QUE ESTEJAM DE FÉRIAS OU RESCINDIDAS NESSE MES
		// FIM SELECIONA
		
		$re = mysql_query("SELECT * FROM rh_clt WHERE id_projeto = '$row_folha[projeto]' AND (status = '40' OR status_demi = '1')");		
		$cont = 0;
        while($row_clt = mysql_fetch_array($re)){
		  
		  //SELECIONANDO O CURSO DE CADA CLT
		  $result_curso = mysql_query("SELECT * FROM curso where id_curso = '$row_clt[id_curso]'");
		  $row_curso = mysql_fetch_array($result_curso);
		  
		  //---- EMBELEZAMENTO DA PAGINA ----------------------------------
			if($cont % 2){ $classcor="corfundo_um"; }else{ $classcor="corfundo_dois"; };
			$nome = str_split($row_clt['nome'], 35);
			$nomeT = sprintf("% -40s", $nome[0]);
			$bord = "style='border-bottom:#000 solid 1px;'";
		  
			$nomeC = str_replace("CAPACITANDO EM","CAP. EM",$row_curso['nome']);
		//-----------------
		
		
		$aj = " onClick=\"ajupdatecheck('$tb_aj',this.id,'$nomeid_aj','$row_clt[0]','1')\" ";
		$dvRE = "<div id=\"retorno_$cont\">$campo3</div>";
		
		  switch ($row_clt['status']){
			  case 40:
			  $status = "<span style='color:#36C'>de férias</span>";
			  break;
			  case 201:
			  $status = "<span class='red'>rescisao</span>";
			  break;
		  }
		  
		  print"
		  <tr class=\"novalinha $classcor\">
		  <td align='center'>$status</td>
		  <td align='lefth'><label>&nbsp;
		  <input type=\"checkbox\" name='aut[]' id='aut' value='$row_clt[0]'>
		  $nomeT</label></td>
          <td align='lefth'>$nomeC</td>
          <td align='lefth'>".$row_clt['locacao']."</td>
		  </tr>";
		  
		  $cont ++;

		  }
		  
?>
</table>
<br>
<input type="hidden" name="inclusao" id="inclusao" value="1">
<input type="hidden" name="folha" id="folha" value="<?=$folha?>">
<input type="hidden" name="status" id="status" value="1">
<br>

<input type="submit" value="Continuar" class="botao">
</form>
<?php
}

?>
</td>
</tr>
</table>
</body>
</html>