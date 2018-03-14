<?php
include('../conn.php');

$id_projeto = $_REQUEST['projeto'];

?>


<table style="border-collapse: collapse; font-size: 10px;" border="1">
    
<?php
$qr_clt = mysql_query("SELECT A.id_clt, A.nome,A.id_regiao, A.id_projeto, B.regiao as nome_regiao, C.nome as nome_projeto,
    A.pis, A.matricula, D.nome as funcao

FROM rh_clt as A
INNER JOIN  regioes as B
ON B.id_regiao = A.id_regiao
INNER JOIN projeto as C
ON A.id_projeto = C.id_projeto
INNER JOIN curso as D
ON A.id_curso = D.id_curso
WHERE A.id_regiao = 44 AND  A.status <60 AND A.id_projeto = '$id_projeto' AND A.foto = 1 
ORDER BY A.matricula"); 
while($clt = mysql_fetch_assoc($qr_clt)){
    
   $foto_origem  = "../fotosclt/$clt[id_regiao]_$clt[id_projeto]_$clt[id_clt].gif";
   $foto_destino = "../anderson_arquivos/";
   $nome_arquivo = $clt['matricula'].'_'.$clt['nome'].'.gif';
    
    if($clt['id_projeto'] != $projetoAnt){
        
        $nome_pasta = $clt[nome_projeto].' Fotos'; 
        
        echo '<tr height="50">
                <td colspan="4">'.$clt[nome_projeto].'</td>
            </tr>';
        echo '<tr>
                <td>Matricula</td>
                <td>Nome</td>
                <td>Função</td>
                <td>PIS</td>
                
             </tr>';
    }
    
    echo '<tr>            
            <td>'.$clt[matricula].'</td>
            <td>'.$clt[nome].'</td>
            <td>'.$clt[funcao].'</td>
            <td>'.$clt[pis].'</td>            
        </tr>';
    
    ///COPIANDO FOTOS
    if(!file_exists($foto_destino.$nome_pasta)){         
        mkdir($foto_destino.$nome_pasta,0777);
    }
    
    if(!file_exists($foto_destino.$nome_pasta.'/'.$nome_arquivo)){        
        copy($foto_origem, $foto_destino.$nome_pasta.'/'.$nome_arquivo);
    }
    
    
    
 
    
    
    $projetoAnt = $clt['id_projeto'];
    
}


$nome_imagem = $id_reg.'_'.$id_pro.'_'.$row['0'].'.gif';
?>
</table>