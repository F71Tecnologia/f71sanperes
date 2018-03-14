<?php
include('../include/restricoes.php');
include('../../conn.php');
include('../../classes/formato_valor.php');
include('../../classes/formato_data.php');
include('../../funcoes.php');
include('../../adm/include/criptografia.php');

$id_ospcip=$_GET['id'];
$qr_oscip=mysql_query("UPDATE obrigacoes_oscip SET status=0 WHERE id_oscip='$id_ospcip' LIMIT 1");
$qr_anexo=mysql_query("UPDATE obrigacoes_oscip_anexos	 SET status=0 WHERE id_oscip='$id_ospcip' LIMIT 1");


$tipo_oscip = mysql_result(mysql_query("SELECT tipo_oscip FROM obrigacoes_oscip WHERE id_oscip = '$id_ospcip' "),0);

$nome_funcionario = mysql_result(mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'"),0);	
registrar_log('ADMINISTRAÇÃO - OBRIGAÇÕES DA EMPRESA', $nome_funcionario.' excluiu a obrigação: '.'('.$id_ospcip.') - '.$tipo_oscip);	


header("Location: ../../adm/adm_contratos/dados_oscip.php?m=$link_master" );
?>
