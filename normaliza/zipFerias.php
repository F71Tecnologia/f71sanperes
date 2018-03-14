<?php

include_once("../conn.php");
include_once("../classes/FeriasClass.php");

if (!extension_loaded('zip')) {
    echo "Nao esta habilitado php_zip.dll";
    exit;
}
    

function normalizaNome($variavel) {
    $variavel = strtoupper($variavel);
    if (strlen($variavel) > 200) {
        $variavel = substr($variavel, 0, 200);
        $variavel = $variavel[0];
    }
    $nomearquivo = preg_replace("/ /", "_", $variavel);
    $nomearquivo = preg_replace("/[\/]/", "", $nomearquivo);
    $nomearquivo = preg_replace("/[ÁÀÂÃ]/i", "A", $nomearquivo);
    $nomearquivo = preg_replace("/[áàâãª]/i", "a", $nomearquivo);
    $nomearquivo = preg_replace("/[ÉÈÊ]/i", "E", $nomearquivo);
    $nomearquivo = preg_replace("/[éèê]/i", "e", $nomearquivo);
    $nomearquivo = preg_replace("/[ÍÌÎ]/i", "I", $nomearquivo);
    $nomearquivo = preg_replace("/[íìî]/i", "i", $nomearquivo);
    $nomearquivo = preg_replace("/[ÓÒÔÕ]/i", "O", $nomearquivo);
    $nomearquivo = preg_replace("/[óòôõº]/i", "o", $nomearquivo);
    $nomearquivo = preg_replace("/[ÚÙÛ]/i", "U", $nomearquivo);
    $nomearquivo = preg_replace("/[úùû]/i", "u", $nomearquivo);
    $nomearquivo = str_replace("Ç", "C", $nomearquivo);
    $nomearquivo = str_replace("ç", "c", $nomearquivo);

    return $nomearquivo;
}
$nameZip = "FERIAS.zip";
$ferias = new Ferias();
$dadosFerias = $ferias->getFeriasByMesAno("2013-08","2014-09","3302", array('A.id_ferias','A.nome','A.data_ini','A.data_fim','A.mes','A.ano','A.id_clt','C.*','B.data_vencimento'));
echo "Total de Participantes: " . mysql_num_rows($dadosFerias) . "<br />";

$zip = new ZipArchive();
$zip->open($nameZip, ZIPARCHIVE::CREATE);

while($linha = mysql_fetch_assoc($dadosFerias)){
    
//    echo "<pre>";
//        print_r($linha);
//        echo "Caminho Aviso: http://netsorrindo.com/intranet/rh/arquivos/ferias/ferias_{$linha['id_clt']}_{$linha['id_ferias']}.pdf";
//        echo "<br />";
//        echo "Caminho Comprovante: http://netsorrindo.com/intranet/comprovantes/{$linha['id_pg']}.{$linha['id_saida']}_pg.pdf";
//    echo "</pre>";
//    
    $nomePro = normalizaNome($linha['nome']);
    
    if(is_file($nameZip)){
        unlink($nameZip);
    }
    
    $fileFerias = "../rh/arquivos/ferias/ferias_{$linha['id_clt']}_{$linha['id_ferias']}.pdf";
    $fileFeriasComprovante = "../comprovantes/{$linha['id_pg']}.{$linha['id_saida']}_pg.pdf";
    if(is_file($fileFerias)){
        $zip->addFile($fileFerias, "{$linha['ano']}/{$linha['mes']}/ferias_{$linha['id_clt']}_{$linha['id_ferias']}_ANEXO.pdf");
        if(is_file($fileFeriasComprovante)){
            $zip->addFile($fileFeriasComprovante, "{$linha['ano']}/{$linha['mes']}/ferias_{$linha['id_clt']}_{$linha['id_ferias']}_COMPROVANTE.pdf");
        }
   }
}

$zip->close(); 

echo "<a href='../normaliza/FERIAS.zip'>Download</a>";


$query_sem_comprovantes = "SELECT B.id_saida, A.id_clt, A.nome, A.mes, A.ano, B.valor
FROM rh_ferias AS A
LEFT JOIN (SELECT * FROM saida WHERE tipo = 156 AND status = 2 AND id_projeto = '3302') AS B ON(A.id_clt = B.id_clt AND DATE_FORMAT(B.data_vencimento, '%Y') = A.ano)
LEFT JOIN saida_files_pg AS C ON(B.id_saida = C.id_saida)
WHERE DATE_FORMAT(A.data_ini, '%Y-%m') BETWEEN '2013-08' AND '2013-08' AND A.status = 1 AND A.projeto = '3302'
ORDER BY A.data_ini";
$saidas_files = mysql_query($query_sem_comprovantes);

?>
