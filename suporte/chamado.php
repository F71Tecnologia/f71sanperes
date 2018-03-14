<?PHP
if(empty($_COOKIE['logado'])) {
	print 'Efetue o Login<br><a href="login.php">Logar</a>';
	exit;
}

include('../conn.php');

$id_user = $_COOKIE['logado'];
$regiao  = $_REQUEST['regiao'];
$chamado = $_REQUEST['chamado'];

if(empty($_REQUEST['tela'])) {
	$tela = '1';
} else {
	$tela = $_REQUEST['tela'];
}

switch($tela) { // MOSTRA A PRIMEIRA TELA
	case 1:	

$result = mysql_query("SELECT * FROM suporte WHERE id_suporte = '$chamado'");
$row    = mysql_fetch_array($result);

$data_cad = date('Y-m-d H:i:s');

switch($row['tipo']) {
	 case 1:
	 $ocorre = 'Informação';
	 case 2:
	 $ocorre = 'Reclamação';
	 case 3:
	 $ocorre = 'Inclusão';
	 case 4:
	 $ocorre = 'Exclusão';
	 case 5:
	 $ocorre = 'Erro';
	 case 6:
	 $ocorre = 'Sugestões';
	 case 7:
	 $ocorre = 'Alteração';
}

if($row['prioridade'] == '1') {
	$prioridade = '<b>Baixa</b>';
} elseif($row['prioridade'] == '2') {
	$prioridade = '<b>Média</b>';
} elseif($row['prioridade'] == '3') {
	$prioridade = '<b>Alta</b>';
} else {
	$prioridade = '<b>Urgente</b>';
}

if(!empty($row['arquivo'])) {
	$img = '<a href="arquivos/suporte_'.$row['id_regiao'].'_'.$row['0'].$row['arquivo'].'" rel="lightbox" title="Anexo">Abrir anexo</a>';
} else {
	$img = 'Sem Anexo';
}

if($row['status'] != '2') {
	$text_area = 'style="display:none;"';
} else {
	$text_area = NULL;
}

if($row['quant'] != '0') {
	$nome_arquivo = 'historico_chamado_'.$chamado.'.txt'; 
	$ver_arq      = 'style="display:"';
} else {
	$arquivo = NULL;
	$ver_arq = 'style="display:none"';
}
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>VISUALIZAR CHAMADO</title>
<script type="text/javascript" src="../js/prototype.js"></script>
<script type="text/javascript" src="../js/scriptaculous.js?load=effects,builder"></script>
<script type="text/javascript" src="../js/lightbox.js"></script>
<link rel="stylesheet" href="../lightbox.css" type="text/css" media="screen" />
<link href="../net.css" rel="stylesheet" type="text/css" />
</head>
<body>
<table cellspacing="0" cellpadding="1" width="700" align="center">
    <tr>
      <td bgcolor="#FFFFFF">
        <table cellspacing="1" cellpadding="0" width="100%" border="0">
            <tr>
              <td height="25" align="center" bgcolor="#003300"><strong class="style1"><img src="imgsuporte/conclusao.png" width="46" height="46" alt="resposta" /><br />
                RESPOSTA AO CHAMDO OU CONCLUS&Atilde;O<br />
                <br />
                </strong></td>
            </tr>
            <tr>
              <td>
                 <table cellspacing="0" cellpadding="0" width="100%" border="0">
                    <tr>
                      <td><table cellspacing="0" cellpadding="4" width="100%" border="0">
                          <tr>
                            <td width="10" colspan="6" align="center" valign="middle"  bgcolor="#003300" class="sdfd"><strong class="style1">INFORMA&Ccedil;&Otilde;ES</strong></td>
                          </tr>
                        </table>
                       </td>
                    </tr>
                </table>
              </td>
            </tr>
        </table>
        <form action="chamado.php" name="form" method="post">
        <table cellspacing="0" cellpadding="4" width="100%" border="1">
            <tr>
              <td width="23%" bgcolor="#DDDDDD"><span class="linha"><strong>Tipo de Ocorr&ecirc;ncia:</strong></span></td>
              <td bgcolor="#F0F0F0" class="linha"><?=$ocorre?></td>
              <td width="10%" bgcolor="#DDDDDD" class="linha"><strong>Prioridade:</strong></td>
              <td bgcolor="#FF6666"><span class="linha">
                <?=$prioridade?>
              </span></td>
            </tr>
            <tr>
              <td width="23%" bgcolor="#DDDDDD"><strong class="linha">Assunto:</strong></td>
              <td colspan="3" bgcolor="#F0F0F0" class="linha"><?=$row['assunto']?></td>
            </tr>
            <tr <?=$ver_arq?>>
              <td valign="top" bgcolor="#dddddd"><span class="linha"><strong>Hist&oacute;rico:</strong></span></td>
              <td bgcolor="#F0F0F0" colspan="3"><span class="linha">
                <?php
				$filename = "/home/ispv/public_html/intranet/suporte/arquivos/$nome_arquivo";
                $handle = fopen ($filename, "r");
				$conteudo = fread ($handle, filesize ($filename));
				print $conteudo;
				//FECHA O ARQUIVO m
				fclose($handle); 

				?>
              </span></td>
            </tr>
            <tr>
              <td valign="top" bgcolor="#dddddd"><span class="linha"><strong>Mensagem:</strong></span></td>
              <td colspan="3" bgcolor="#F0F0F0" class="linha"><?=nl2br($row['mensagem'])?></td>
            </tr>
            <tr>
              <td valign="top" bgcolor="#dddddd"><span class="linha"><strong>Resposta:</strong></span></td>
              <td colspan="3" bgcolor="#F0F0F0" class="linha"><?=nl2br($row['resposta'])?>&nbsp;</td>
            </tr>
            <tr <?=$text_area?>>
              <td align="left" valign="top" bgcolor="#dddddd" class="linha"><strong>Replicar:</strong></td>
              <td colspan="3" bgcolor="#F0F0F0" class="linha"><span class="linha">


                <textarea name="replica" cols="48" rows="10" class="linha" id="replica" 
                onChange="this.value=this.value.toUpperCase()"></textarea>
              </span></td>
            </tr>
            <tr>
              <td bgcolor="#dddddd" class="linha"><strong>Anexo recebido:</strong></td>
              <td colspan="3" bgcolor="#F0F0F0" class="linha"><?=$img?></td>
            </tr>
            <tr style="display:none">
              <td bgcolor="#dddddd"><span class="linha"><strong>Enviar Arquivo:</strong></span></td>
              <td bgcolor="#dddddd" colspan="3"><span class="linha">
                <input name="StAnexo" type="file" class="linha" id="StAnexo" size="40" />
                </span></td>
            </tr>
            <tr>
              <td align="center" bgcolor="#f0f0f0" colspan="4"><span class="linha">
                <input type="hidden" name="tela" value="2">
                <input type="hidden" name="chamado" value="<?=$row['0']?>">
                <input type="hidden" name="regiao" value="<?=$regiao?>">
                <input type="image" src="../imagens/botao_enviar_geral.gif" <?=$text_area?>>
              </span></td>
            </tr>
            <tr>
              <td colspan="4" align="center" bgcolor="#DDDDDD"><a href="javascript:history.go(-1)"><img src="../imagens/voltar_novo.gif" width="119" height="24" border="0"></a></td>
            </tr>
            <tr>
              <td colspan="4" align="center" bgcolor="#F0F0F0">
              <b style="font-family:Arial, Helvetica, sans-serif; font-size:10; ">Finalizar Chamado</b><br>
              <a href='suporte.php?tela=3&chamado=<?=$row['0']?>&regiao=<?=$regiao?>'>
<img src='imgsuporte/finalizar.png' alt='finalizar' align='absmiddle' border='0'/></a></td>
            </tr>
        </table>
        </form>
      </td>
    </tr>
</table>
</body>
</html>
<?php
break;
case 2:

$regiao  = $_REQUEST['regiao'];
$chamado = $_REQUEST['chamado'];
$replica =  $_REQUEST['replica'];

$user = $_COOKIE['logado'];
$data = date('Y-m-d');

$result = mysql_query("SELECT * FROM suporte where id_suporte = '$chamado'");
$row    = mysql_fetch_array($result);

if($row['quant'] == '0') {
	$quant = '3';
} else {
	$quant = $row['quant'] + 1;
}

//--------------------------- GRAVANDO O ARQUIVO ---------------------------//

//PREPARA O CONTEÚDO A SER GRAVADO
$somecontent = "
Data de abertura do chamado: $row[ultima_alteracao]
<br><br>
Mensagem:
<br><br>
<font color=blue>$row[mensagem]</font>
<br><br>
<b style='font-family:Arial, Helvetica, sans-serif; font-size:10; '><font color=#666666>
Fim da mensagem</font></b>
<br>---------------------------<br>
Data da resposta: $row[ultima_alteracao]
<br><br>
Resposta:
<br><br>
<font color=orange>$row[resposta]</font>
<br><br>
<b style='font-family:Arial, Helvetica, sans-serif; font-size:10; '><font color=#666666>
Fim da mensagem</font></b>
<br>---------------------------<br>";

//ARQUIVO TXT > DANDO UM NOME PARA O ARQUIVO E A LOCALIZAÇÃO

$filename = "arquivos/historico_chamado_".$chamado.".txt"; 
$filename2 = "/home/ispv/public_html/intranet/suporte/arquivos/historico_chamado_".$chamado.".txt"; 




// SE EXISTIR ELE VAI ABRIR O ARQUIVO E ESCREVER O CONTEÚDO NELE
if (!$abrir = fopen($filename, "a+")) {
echo "Erro abrindo arquivo ($filename)";
exit;
}

//LIBERANDO ARQUIVO PARA ALTERAÇÕES
chmod($filename2, 0777);

//ESCREVE NO ARQUIVO TXT
if (!fwrite($abrir, $somecontent)) {
print "Erro escrevendo no arquivo ($filename2)";
exit;
}

//FECHA O ARQUIVO 
fclose($abrir); 

mysql_query("UPDATE suporte SET mensagem = '$replica', resposta = '', ultima_alteracao = '$data', status = '3', quant = '$quant' WHERE id_suporte = '$chamado' ");



//-------------------- FIM MANIPULANDO O ARQUIVO -----------------------------


print "
<script>
alert (\"Feito.... \\n Réplica enviada com sucesso!\");
location.href = 'suporte.php?regiao=$regiao';
</script>";


}

?>