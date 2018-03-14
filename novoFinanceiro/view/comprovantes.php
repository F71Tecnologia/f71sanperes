<?php 
/*
 * PHP-DOC - comprovantes.php
 * 
 * Rotina de exibição de documentos para download
 * 
 * ??-??-????
 * 
 * Versão: 3.0.0001 - 21/12/2015 - Jacques - Comentado o código que gerava uma abertura de janela adicional com window.open em documentos anexos
 * Versão: 3.0.0001 - 23/03/2016 - Jacques - Adicionado variável document_root para portabilidade de execução do código local e remoto 
 * 
 * 
 * @author Não definido
 * 
 * @copyright www.f71.com.br
 *  
 */

if(empty($_COOKIE['logado'])) {
	print 'Efetue o Login<br><a href="../login.php">Logar</a>';
	exit;
}
include "../../conn.php";
include "../../funcoes.php";
include "../../wfunction.php";



function ListUrl($query_string){
	$partes = explode("&",$query_string);
	foreach($partes as $parte){
		$seperacao = explode("=",$parte);
		$chave = $seperacao[0];
		$valor = $seperacao[1];
		$retorno[$chave] = $valor;
	}
	return $retorno;	 
}
$decriptado = decrypt($_SERVER['QUERY_STRING']);
$GET = ListUrl($decriptado);




$query_saida = mysql_query("SELECT * FROM saida WHERE id_saida = '$GET[ID]'");
$row_saida =  mysql_fetch_array($query_saida);

//Verifica se a saída é multa de rescisão
$verifica_multa = mysql_num_rows(mysql_query("SELECT * FROM saida_files WHERE id_saida = '$GET[ID]' AND multa_rescisao <> 0 "));

// VERIFICANDO SE A SAIDA É UMA RESCISÃO E REDIRECIONANDO PARA A PAGINA DA NOVA RESCISÃO 04/06/2011
if(($row_saida['tipo'] == '51' or $row_saida['tipo'] == '170')  and $GET['tipo'] != '1' and $verifica_multa == 0){
    
    $qr_tipo_rescisao = mysql_query("SELECT rescisao_complementar FROM saida_files WHERE id_saida = '$GET[ID]'");
    $row_tipo_recisao = mysql_fetch_array($qr_tipo_rescisao);

    if($row_tipo_recisao['rescisao_complementar'] == 1){
            // mudei dia 01/07/2014 by amanda
            $qr_rescisao = mysql_query("SELECT C.id_regiao, A.id_clt, B.id_rescisao, C.data_proc, B.id_saida
                                            FROM saida AS A
                                            LEFT JOIN pagamentos_especifico AS B ON (A.id_saida = B.id_saida)    
                                            LEFT JOIN rh_recisao AS C ON (B.id_rescisao = C.id_recisao)
                                            WHERE A.id_saida = '$GET[ID]' AND C.`status` = 1;
                                      ");
    }else{
            $qr_rescisao = mysql_query("SELECT B.id_regiao, B.id_clt, B.id_recisao, B.data_proc
                                        FROM saida AS A
                                        LEFT JOIN pagamentos_especifico AS C ON (A.id_saida = C.id_saida)
                                        LEFT JOIN saida_files AS D ON (A.id_saida = D.id_saida)
                                        LEFT JOIN rh_recisao AS B ON (C.id_clt = B.id_clt AND D.rescisao_complementar = B.rescisao_complementar)
                                        WHERE A.id_saida = '$GET[ID]' AND B.`status` = 1;
                                      ");
    }
        $num_rescisao = mysql_num_rows($qr_rescisao);

	if(!empty($num_rescisao)){
		$row_recisao = mysql_fetch_array($qr_rescisao);
                $link = str_replace('+','--',encrypt("{$row_recisao[0]}&{$row_recisao[1]}&{$row_recisao[2]}"));
                
                   if(substr($row_recisao['data_proc'],0,10) >= '2013-04-04'){
                  
                  $link_nova_rescisao = "nova_rescisao_2.php?enc=$link" ;
                    
                } else {
                     $link_nova_rescisao = "nova_rescisao.php?enc=$link" ;
                }
                
		header('location: '.'http://'.$_SERVER['HTTP_HOST'].'/intranet/rh/recisao/'.$link_nova_rescisao);
		
	} 
        

    $query_anexo = mysql_query("SELECT * FROM saida_files WHERE id_saida = '$GET[ID]'");
     if( mysql_num_rows($query_anexo) == 0){
         
         exit;
     }


}



$query_anexo = mysql_query("SELECT * FROM saida_files WHERE id_saida = '$GET[ID]'");
$query_anexo2 = mysql_query("SELECT * FROM saida_files_pg WHERE id_saida = '$GET[ID]'");
if($_COOKIE['debug'] == 666){
    echo "<br>//////////////////////////////////<br>SELECT * FROM saida_files WHERE id_saida = '$GET[ID]'<br>//////////////////////////////////<br>";
    echo "<br>//////////////////////////////////<br>SELECT * FROM saida_files_pg WHERE id_saida = '$GET[ID]'<br>//////////////////////////////////<br>";
    echo mysql_num_rows($query_anexo);
}



# função para concertar problema de extenção de arquivos

function  conserta($nome_arquivo, $caminho = "../../comprovantes/"){
	
	/////////////	
	$diraberto = opendir($caminho); // Abre o diretorio especificado
    chdir($caminho); // Muda o diretorio atual p/ o especificado
    while($arq = readdir($diraberto)) { // Le o conteudo do arquivo
        if($arq == ".." || $arq == ".")continue; // Desconsidera os diretorios
        $partes = explode('.',$arq);
		$chave_ext = count($partes)-1;
		$ext = $partes[$chave_ext];
		if(count($partes) == 3 and strlen($ext) == 3){
			if($nome_arquivo == "$partes[0].$partes[1]"){
				if(empty($partes[2])){
					$partes[2] = "gif";
				}
				$partes[2] = strtolower($partes[2]);
				$final = implode('.',$partes);
				return $final;
			}
			
			
		}else{
			if($nome_arquivo == "$partes[0]"){
				if(empty($partes[1])){
					$partes[1] = "gif";
				}
				$partes[1] = strtolower($partes[1]);
				$final = implode('.',$partes);
				return $final;
			}
			
		}
		
    }
    closedir($diraberto); // Fecha o diretorio atual
	return "Arquivo não encontrado";
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Comprovantes</title>
<style type="text/css">
body {
	background-color: #EEE;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	text-align: center;
        margin:0;
        padding:0;
}
.tudo{
    width: 299px;
    margin:0 auto;  
}
.base {
	width: 100px;
	/*margin: 0 auto;
        position: relative;*/
	text-align: left;
	background-color: #FFF;
	border: 1px solid #CCC;
	padding: 5px;
        float: left;
}
.base br {
	font-style: italic;
}
.base span {
	font-style: italic;
}
h1 {
	margin: 0px;
	font-size: 16px;
	font-style: italic;
}
#imgPdf{
    width: 40px;
}
</style>
</head>
<body>
<h1>Saida (<?=$row_saida['id_saida']?>)<br />
  <?=$row_saida['nome']?>
</h1>
    <div class="tudo">
<?php if($row_saida['comprovante'] != '0' or $GET['tipo'] == '1'):?>
                <?php if($row_saida['comprovante'] == '1'):?>
                <div class="base">
                        <center><a href="<?= "../../comprovantes/".$row_saida['id_saida'].'.gif'; ?> download">Download do arquivo</a></center>
                    <br />
                        <img src="../../classes/img.php?foto=../comprovantes/<?=$row_saida['id_saida'].'.gif'?>&w=&h=" id="imgPdf" border="0" />
                </div>
                <?php exit; endif;?>


                <?php if($GET['tipo'] == '0'):?>
                <?php while($row_anexo = mysql_fetch_assoc($query_anexo)): ?>
                <div class="base">
                        <center><a href="<?= (file_exists("../../comprovantes/{$row_anexo['id_saida_file']}.{$row_anexo['id_saida']}.PDF")) ? "../../comprovantes/".$row_anexo['id_saida_file'].'.'.$row_anexo['id_saida'].".PDF" : "../../comprovantes/".$row_anexo['id_saida_file'].'.'.$row_anexo['id_saida'].".pdf"; ?>"download>Download do arquivo</a></center>
                    <br />
                        <?php 
                        $id_clt = $GET['id_clt'];
                        $id_ferias = $GET['id_ferias'];
                        $id_comprovante = $row_anexo['id_saida_file'];
                        $id_saida = $row_anexo['id_saida'];

                        $document_root = $_SERVER['DOCUMENT_ROOT'];
                        $server_name = $_SERVER['SERVER_NAME'];
                        
                        $nomearquivo = "{$document_root}/intranet/rh_novaintra/ferias/arquivos/ferias_{$id_clt}_{$id_ferias}.pdf";

                        $arquivo_destino = "{$document_root}/intranet/comprovantes/{$id_comprovante}.{$id_saida}.pdf";

                        /*
                         * 24/03/2016 - @jacques 
                         * Condição de verificação de existencia de arquivo ignorada para geração de todos 
                         * os recibos temporariamente.
                         */
                        if(!file_exists($arquivo_destino) && !empty($id_ferias)){
                            
                            if(!file_exists($nomearquivo) && !empty($id_ferias)){

                                $gerar_arquivo = "{$server_name}/intranet/rh_novaintra/ferias/ferias_processar.php?method=gerarPdf&id_ferias={$id_ferias}&value=pdf&logado=0";
                                
                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_HEADER, 0);
                                curl_setopt($ch, CURLOPT_VERBOSE, 0);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                                curl_setopt($ch, CURLOPT_POST, 1);
                                curl_setopt($ch, CURLOPT_URL, $gerar_arquivo);

                                $dados = array();    

                                curl_setopt($ch, CURLOPT_POSTFIELDS, $dados);
                                $response = curl_exec($ch);
                                $errorMsg = curl_error($ch);
                                $respostaHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                curl_close ($ch); 
                                
                                if($errorMsg) echo 'Erro ao obter arquivo<br>';

                            }
                            
                            if (!copy($nomearquivo, $arquivo_destino)) {
                                echo "{$gerar_arquivo}<br>";
                                echo "{$nomearquivo}<br>";
                                echo "{$arquivo_destino}<br>";
                                echo 'Erro no anexo da saída!<br>';
                                exit;
                            }

                        }    
                        
                            
                            
                        
                        if($row_anexo['tipo_saida_file'] == '.pdf' || $row_anexo['tipo_saida_file'] == '.PDF'){
                            if(file_exists("../../comprovantes/{$id_comprovante}.{$id_saida}.PDF")){
                                echo  "<a href='../../comprovantes/{$id_comprovante}.{$id_saida}.PDF' download><img  id='imgPdf' src='../image/File-pdf-32.png'/></a>";
                            }else{
                                echo  "<a href='../../comprovantes/{$id_comprovante}.{$id_saida}.pdf' download><img  id='imgPdf' src='../image/File-pdf-32.png'/></a>";
                            }
                        }else{?>
                        <img src="../../classes/img.php?foto=../comprovantes/////<?=$row_anexo['id_saida_file'].'.'.$row_anexo['id_saida'].$row_anexo['tipo_saida_file'];?>&w=&h=" id="imgPdf" border="0" />
                    <?php 
                     }
                     ?>
                </div>
                <?php endwhile;?>
                <?php endif;?>


                <?php if($GET['tipo'] == '1'){?>
                <?php while($row_anexo2 = mysql_fetch_assoc($query_anexo2)){?>
                <div class="base">

                <?php 
                if($row_anexo2['tipo_pg'] == '.pdf'){
//                                echo "<center><a href=\"../../comprovantes/$row_anexo2[id_pg].$row_anexo2[id_saida]_pg$row_anexo2[tipo_pg]\">Download do arquivo</a></center><br />";
//                                echo "<script>window.open('http://www.netsorrindo.com/intranet/comprovantes/$row_anexo2[id_pg].$row_anexo2[id_saida]_pg$row_anexo2[tipo_pg]')</script>";
                                echo '<a href="../../comprovantes/'.$row_anexo2['id_pg'].'.'.$row_anexo2['id_saida'].'_pg'.$row_anexo2['tipo_pg'].' " download><img  id="imgPdf" src="../image/File-pdf-32.png" /></a>';
                }
                else{ ?>
                        <img src="../../classes/img.php?foto=../comprovantes/<?=$row_anexo2['id_pg'].'.'.$row_anexo2['id_saida'].'_pg'.$row_anexo2['tipo_pg']?>&w=&h=" id="imgPdf" border="0" />
                <?php 
                } 
                ?>
                </div>
                <?php } ?>
                <?php }?>

<?php else:?>
<center>N&atilde;o &agrave; registro de imagens anexadas.</center>
<?php endif;?>
</div>
</body>
</html>