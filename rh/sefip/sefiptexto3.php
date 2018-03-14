<?php 
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='http://www.".$_SERVER['HTTP_HOST']."/intranet/login.php'>Logar</a> ";
exit;
}

include('../../conn.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>SEFIP</title>a
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>
<body>
<?php
// Variáveis
$mes  = $_GET['mes'];
$ano  = $_GET['ano'];
$regiao = $_GET['regioes'];
$projeto = $_GET['projeto'];

$data_comparacao = implode('-', array_reverse(explode('/', $_GET['data'])));
$data            = str_replace('/','',$_GET['data']);

$qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$regiao'");
$row_regiao = mysql_fetch_assoc($qr_regiao);

$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_regiao[id_master]'");
$row_master = mysql_fetch_assoc($qr_master);


 
            $qr_participantes = mysql_query("select B.* FROM rh_folha as A
                                            INNER JOIN rh_folha_proc as B
                                            ON A.id_folha = B.id_folha
                                            WHERE A.mes = '$mes' AND A.ano = $ano 
                                            AND A.status = 3 AND B.status = 3 AND id_regiao IN($regiao) order by B.nome"); 
            while($row_participante = mysql_fetch_assoc($qr_participantes)) {

              $ids[]      = $row_participante['id_folha_proc'];
              $ids_grrf[] = $row_participante['id_clt'];

            }
  


$ids      = implode(',', $ids);
$ids_grrf = implode(',', $ids_grrf);

// Buscando Arquivo
$qr_nome_arquivo = mysql_query("SELECT * FROM rh_folha WHERE id_folha = '$_GET[folha]'");
$nome_arquivo    = mysql_fetch_assoc($qr_nome_arquivo);


if($_COOKIE['logado'] != 1){
        $local_arquivo   = 'arquivos/'.$regiao.'_'.$_REQUEST['mes'].'_'.$_REQUEST['ano'].'.re';
} else {
        $local_arquivo   = 'arquivos/'.$regiao.'_'.$_REQUEST['mes'].'_'.$_REQUEST['ano'].'teste.re'; 

}

if(file_exists($local_arquivo)) {
	unlink($local_arquivo);
}


$decimo_terceiro = $nome_arquivo['terceiro'];
$tipo_dt         = $nome_arquivo['tipo_terceiro'];




$arquivo = fopen("$local_arquivo", "a");

// Variável para Sefip por Folha
$sefip_folha = true;

// SEFIP
require('corpo_sefip.php');

// Inserindo Sefip no Banco de Dados
if($_COOKIE['logado'] != 1){
  mysql_query("INSERT INTO sefip (mes, ano, regiao, projeto, folha, tipo_sefip, data, autor,ids_projetos) VALUES ('$mes', '$ano', '$_GET[regiao]', '$_GET[projeto]', '$_GET[folha]', '2', NOW(), '$_COOKIE[logado]', '1,2,3')");
  
}



fclose($arquivo);
if($_COOKIE['logado'] != 1){
   
     print "<a href='arquivos/download.php?file=1_2_3_".$_REQUEST['mes']."_".$_REQUEST['ano'].".re'>Baixar arquivo do Sefip</a><br>";
  
}else{
  print "<a href='arquivos/download.php?file=1_2_3_".$_REQUEST['mes']."_".$_REQUEST['ano']."teste.re'>Baixar arquivo do Sefip</a><br>";

}
?>
</body>
</html>