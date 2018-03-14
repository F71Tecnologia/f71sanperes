<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include "../../conn.php";
include('../../funcoes.php');
include "../../classes/funcionario.php";
include "../../classes/FolhaClass.php";
include '../../classes_permissoes/regioes.class.php';
include("../../wfunction.php");

function verifica_campo($valor){
    
    if(!empty($valor) and $valor != 0){
        return number_format($valor,2,',','.');
    }
    
}

$qr_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_user = mysql_fetch_assoc($qr_user);
$id_master = $row_user['id_master'];


// Buscando a Folha
list($regiao, $id_folha) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));
$link_voltar =  'ver_folha.php?enc='.$_REQUEST['enc'];


$FOLHA = new Folha();

$result = $FOLHA->getDadosFolhaById($id_folha);


foreach($result as $folha){

$RESULTADO['id_clt']  = $folha['id_clt'];    
$RESULTADO['salario'] = $folha['sallimpo_real'];    
    
    
}
echo '<pre>';
print_r($RESULTADO);
echo '</pre>';
?>
<html>
    <head>
        <title>Relatório de Rescisões</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="../../net1.css" rel="stylesheet" type="text/css">
      
         <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
            <script src="../../js/highslide-with-html.js" type="text/javascript"></script>
            <script src="../../js/global.js" type="text/javascript"></script>
       <style media="print">
            form{ visibility: hidden;}
            .link_voltar{ visibility: hidden;}     
            .negrito{ font-weight: bold;
                
}
            
        </style>
    </head>
    <body class="novaintra" >       
        <div id="content" >
            <div id="head">
               <div class="link_voltar"><a href="<?php echo $link_voltar;?>" title="Voltar para a folha"> <img src="../../imagens/back.png" width="30" height="30"/> </a> </div>
               <img src="../../imagens/logomaster<?php echo $id_master; ?>.gif" class="fleft" style="margin-right: 25px;" width="140" height="100"/>
                <div class="fleft">
                    <h2>Relatório de Rescisões</h2>  
                    <p><strong><?php echo $row_folha['mes'];?></strong></p>
                    <p><strong>Folha:</strong> <?php echo $row_folha['id_folha'];?></p>
                    <p><strong>Período:</strong> <?php echo $row_folha['data_inicio'];?> a  <?php echo $row_folha['data_fim'];?> </p>
                </div>
            </div>
            <br class="clear">
            <br>         
        <p style="text-align: right;"><input type="button" onclick="tableToExcel('tabela', 'Relatório de Rescões')" value="Exportar para Excel" class="exportarExcel"></p>
        </div>

</body>
</html>