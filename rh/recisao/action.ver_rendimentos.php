<?php
if(empty($_COOKIE['logado'])) {
	print "<script>location.href = '../login.php?entre=true';</script>";
	exit;
}

include('../../conn.php');
include('../../funcoes.php');

function RemoveAcentos($str, $enc = "iso-8859-1") {
$acentos = array(
	'A' => '/&Agrave;|&Aacute;|&Acirc;|&Atilde;|&Auml;|&Aring;/',
	'a' => '/&agrave;|&aacute;|&acirc;|&atilde;|&auml;|&aring;/',
	'C' => '/&Ccedil;/',
	'c' => '/&ccedil;/',
	'E' => '/&Egrave;|&Eacute;|&Ecirc;|&Euml;/',
	'e' => '/&egrave;|&eacute;|&ecirc;|&euml;/',
	'I' => '/&Igrave;|&Iacute;|&Icirc;|&Iuml;/',
	'i' => '/&igrave;|&iacute;|&icirc;|&iuml;/',
	'N' => '/&Ntilde;/',
	'n' => '/&ntilde;/',
	'O' => '/&Ograve;|&Oacute;|&Ocirc;|&Otilde;|&Ouml;/',
	'o' => '/&ograve;|&oacute;|&ocirc;|&otilde;|&ouml;/',
	'U' => '/&Ugrave;|&Uacute;|&Ucirc;|&Uuml;/',
	'u' => '/&ugrave;|&uacute;|&ucirc;|&uuml;/',
	'Y' => '/&Yacute;/',
	'y' => '/&yacute;|&yuml;/',
	'a.' => '/&ordf;/',
	'o.' => '/&ordm;/'
);
  return preg_replace($acentos, array_keys($acentos), htmlentities($str, ENT_NOQUOTES, $enc));
}


 

$id_clt            = mysql_real_escape_string($_GET['clt']);
$meses_trabalhados = mysql_real_escape_string($_GET['m_trab']);


$qr_clt = mysql_query("SELECT A.*, YEAR(data_demi) as ano_demissao, B.salario, B.tipo_insalubridade,B.qnt_salminimo_insalu
                        FROM rh_clt as A
                       INNER JOIN curso as B
                       ON A.id_curso = B.id_curso
                       WHERE A.id_clt = $id_clt");
$row_clt = mysql_fetch_assoc($qr_clt);

$qr_folha = mysql_query("select  A.ids_movimentos_estatisticas, B.id_clt,A.mes
                        FROM rh_folha as A
                        INNER JOIN rh_folha_proc as B
                        ON A. id_folha = B.id_folha
                        WHERE B.id_clt   = '$id_clt'  AND B.status = 3 AND A.terceiro = 2 
                        AND A.data_inicio >= DATE_SUB(NOW(), INTERVAL 14 MONTH) ORDER BY A.ano,A.mes");

while($row_folha = mysql_fetch_assoc($qr_folha)){

    
    

    if(!empty($row_folha[ids_movimentos_estatisticas])){

           /*$qr_movimentos = mysql_query("SELECT *
                                       FROM rh_movimentos_clt
                                       WHERE id_movimento IN($row_folha[ids_movimentos_estatisticas]) AND incidencia = '5020,5021,5023' AND id_mov NOT IN(200,14,193,56,232) AND tipo_movimento = 'CREDITO' AND id_clt = $id_clt  ");*/
           
           $qr_movimentos = mysql_query("SELECT *
                                       FROM rh_movimentos_clt
                                       WHERE (id_movimento IN($row_folha[ids_movimentos_estatisticas]) OR (mes_mov = 16 AND status = 1)) AND incidencia = '5020,5021,5023'  AND tipo_movimento = 'CREDITO' AND id_clt = $id_clt /*AND id_mov NOT IN(56,200,235,57,279,370)*/");  ///A PEDIDO DA REJANE, COM BASE NO EMAIL ESTOU REMOVENDO O MOVIMENTO DE DIFERENÇA SALARIAL PARA O CALCULO DAS MÉDIAS #13/11/2014
                            
           while($row_mov = mysql_fetch_assoc($qr_movimentos)){             
           
            $verifica_mov_fixo = mysql_query("SELECT * FROM rh_movimentos_clt   WHERE  id_mov = '$row_mov[id_mov]' AND   id_clt = '$id_clt' AND lancamento = 2 AND status = 1");   
               if(mysql_num_rows($verifica_mov_fixo) == 0){
               
              $movimentos[$row_mov['nome_movimento']] +=$row_mov['valor_movimento']; 
           
               }
           }
           
    }
}



$qr_mov_fixo = mysql_query("SELECT * FROM rh_movimentos_clt
                                    WHERE  incidencia = '5020,5021,5023' AND id_mov NOT IN(200,14,193,56) AND tipo_movimento = 'CREDITO' AND id_clt = $id_clt AND (lancamento = 2 AND status = 1)
                                    ");
while($row_mov = mysql_fetch_assoc($qr_mov_fixo)){
    
   $movimentos_fixos[$row_mov['nome_movimento']] = $row_mov['valor_movimento']; 
}
////////////////////////////////////////
//CONDIÇÃO PARA A INSALUBRIDADE ///////
///////////////////////////////////////
if($row_clt['insalubridade'] == 1){
    
       
	$qr_mov = mysql_query("SELECT fixo FROM rh_movimentos WHERE cod = '0001' AND anobase = '$row_clt[ano_demissao]'") or die(mysql_error());
	$row_mov = mysql_fetch_assoc($qr_mov);     
        
        
         $percentInsalu = 0.20;
        if($row_clt['tipo_insalubridade'] == 2){
            $percentInsalu = 0.40;
        }
        $valorSalMinimoInsalubridade      = $row_mov['fixo'] * $row_clt['qnt_salminimo_insalu']; 
        
        $valor_insalubridade_integral = ($valorSalMinimoInsalubridade * $percentInsalu);
        $media_por_mov['INSALUBRIDADE 20%'] = $valor_insalubridade_integral;
        $total_media             +=  $media_por_mov['INSALUBRIDADE 20%'];
         
}




if(sizeof($movimentos) > 0){
        foreach($movimentos as $nome_mov => $valor){    
            $media_por_mov[$nome_mov] = $valor/12;
            $total_media             +=  $media_por_mov[$nome_mov];

        }
}
if(sizeof($movimentos_fixos) > 0){
    foreach($movimentos_fixos as $nome_mov => $valor){    
        $media_por_mov[$nome_mov] = $valor;
        $total_media             +=  $media_por_mov[$nome_mov];

    }
}

/*  
 *  if($_COOKIE['logado'] == 1){
    echo '<pre>';
print_r($movimentos);  
print_r($media_por_mov); 
echo '</pre>'; 
  }
  */                          

?>
<style>
    table{
        width: 500px;
        height: auto;
        font-size: 12px;
        
    }  
    .titulo{
        background-color:  #cccccc;
        text-align: center;
        font-weight: bold;
     
    }
    
    .linha_1{ background-color:  #f4f4f4;    }
    .linha_2{ background-color:   #e9e8e8;    }
</style>
<script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
<script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
<script src="../../js/global.js" type="text/javascript"></script>

<p style="text-align: left; padding-left: 30px;"><input type="button" onclick="tableToExcel('media', 'Médias da Rescisão')" value="Exportar para Excel" class="exportarExcel"></p>  
 
<table id="media" class="essatb">
    
<tr class="titulo">   
    <td>NOME</td> 
    <td>VALOR DA MEDIA</td>
</tr>
<?php   
foreach($media_por_mov as $nome_mov => $valor_mov){
?>
    
    <tr class="<?php echo ($i++ %2 == 0)? 'linha_1': 'linha_2'; ?>">
        <td><?php echo RemoveAcentos($nome_mov); ?></td>        
        <td>R$ <?php echo number_format($valor_mov,2,',','.'); ?></td>
    </tr>

<?php } ?>
    <tr>
        <td align="right">Total:</td>
        <td>R$ <?php echo number_format($total_media,2,',','.')?></td>
    </tr>
</table>