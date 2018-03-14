<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include "../../conn.php";
include "../../funcoes.php";
include "../../classes/funcionario.php";
include '../../classes_permissoes/regioes.class.php';
include("../../wfunction.php");


$usuario = carregaUsuario();

$enc = $_REQUEST['enc'];
$enc = str_replace("--","+",$enc);
$link = decrypt($enc); 

$decript = explode("&",$link);
$regiao = $decript[0];
$folha = $decript[1];
$link_voltar      = 'ver_folha.php?enc='.str_replace('+', '--', encrypt("$regiao&$folha"));

$qr_folha  = mysql_query("SELECT A.id_folha, B.nome_mes, DATE_FORMAT(A.data_inicio,'%d/%m/%Y') as data_inicio,
                         DATE_FORMAT(A.data_fim,'%d/%m/%Y') as data_fim, C.regiao as nome_regiao, D.nome as nome_projeto
                         FROM rh_folha  as A 
                         INNER JOIN ano_meses as B
                         ON A.mes = B.num_mes
                         INNER JOIN  regioes as C
                         ON C.id_regiao = A.regiao
                         INNER JOIN projeto as D
                         ON D.id_projeto = A.projeto
                         WHERE A.id_folha = $folha") or die(mysql_error());
$row_folha = mysql_fetch_array($qr_folha);



$qr_folha_proc = mysql_query("SELECT A.id_clt, A.nome, A.cpf,A.salliquido,A.financeiro,
IFNULL(B.nome, C.nome_banco) as nome_banco,
IFNULL(B.agencia,C.agencia) as agencia, 
IFNULL(B.conta,C.conta)as conta,
IF(C.tipo_conta = 'salario', 'Conta Salário',IF(C.tipo_conta = 'corrente','Conta Corrente','')) as tipo_conta
FROM rh_folha_proc  as A 
LEFT JOIN bancos as B
ON A.id_banco = B.id_banco 
INNER JOIN rh_clt as C
ON C.id_clt = A.id_clt
INNER JOIN tipopg as D
ON D.id_tipopg  = A.tipo_pg
WHERE A.id_folha = '$row_folha[id_folha]' AND A.status = 3  AND D.campo1 = 2 ORDER BY A.nome; ");
$total_participantes = mysql_num_rows($qr_folha_proc);

///REGIÕES
$regioes = montaQuery('regioes', "id_regiao,regiao", "id_master = '$usuario[id_master]'");
$optRegiao = array();
foreach ($regioes as $valor) {
    $optRegiao[$valor['id_regiao']] = $valor['id_regiao'] . ' - ' . $valor['regiao'];
}
$regiaoSel = $regiao;




if (isset($_POST['gerar'])) {

   
}
?>
<html>
    <head>
        <title>Pagamentos em Cheque</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="../../net1.css" rel="stylesheet" type="text/css">
         <link rel="stylesheet" type="text/css" href="../../js/highslide.css" />
        <link rel="stylesheet" type="text/css" href="../../jquery/datepicker-lite/jquery-ui-1.8.4.custom.css" />
        <script src="../../jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../jquery/jquery.tools.min.js" type="text/javascript" ></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        <script type="text/javascript" src="../../js/highslide-with-html.js"></script>
       <script type="text/javascript" src="../../jquery/datepicker-lite/jquery-ui-1.8.4.custom.min.js" ></script> 
    </head>
    <body class="novaintra">       
  
          <div id="content">
               <div class="link_voltar"><a href="<?=$link_voltar?>" title="Voltar"> <img src="../../imagens/back.png" width="30" height="30"/> </a> </div>
    
            <div id="head">
             <img src="../../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;" width="140" height="100"/>
               <div class="fleft">
                    <h2>Relatório de pagamentos em Cheque</h2>
                    <p><strong>Região:</strong> <?php echo $row_folha['nome_regiao'];?></p>
                    <p><strong>Projeto:</strong> <?php echo $row_folha['nome_projeto'];?></p>
                    <p><strong>Folha:</strong> <?php echo $folha;?></p>
                    <p><strong>Mês: </strong><?php echo $row_folha['nome_mes'];?></p>
                    <p><strong>Período:</strong> <?php echo $row_folha['data_inicio']. ' a '.$row_folha['data_fim'];?></p>                 
                    <p><strong>Total de participantes:</strong> <?php echo $total_participantes;?> </p>                 
                </div>
            </div>
            <br class="clear">
            <br/>
            <div class="clear"></div>
               <table border="0" cellpadding="0" cellspacing="0" class="grid" width="100%">
               <thead>
                <tr height="40">                                   
                    <td class="txcenter">Código</td>
                    <td>Nome</td>
                    <td class="txcenter">CPF</td>
                    <td class="txcenter">Banco</td>
                    <td class="txcenter">Agência</td>
                    <td class="txcenter">Conta</td>                    
                    <td class="txcenter">Tipo de conta</td>
                    <td class="txcenter">Sal.Líquido</td>                  
                </tr>
               </thead>
                <?php while($row_folha_proc = mysql_fetch_assoc($qr_folha_proc)) { 
                    
                  $cor = ($i++ % 2 == 0)? 'even':'odd';  
                  $total_liquido +=$row_folha_proc['salliquido'];
                    ?>
               <tbody>
                <tr class="<?php echo $cor; ?> select">                 
                    <td><?php echo $row_folha_proc['id_clt'];?></td>
                    <td><?php echo $row_folha_proc['nome'];?></td>
                    <td><?php echo $row_folha_proc['cpf'];?></td>
                    <td><?php echo $row_folha_proc['nome_banco'];?></td>
                    <td><?php echo $row_folha_proc['agencia'];?></td>
                    <td><?php echo $row_folha_proc['conta'];?></td>                    
                    <td><?php echo $row_folha_proc['tipo_conta'];?></td>
                    <td class="txcenter"><?php echo number_format($row_folha_proc['salliquido'],2,',','.');?></td>                                     
                </tr>                
                <?php } ?>
                <tr>
                    <td colspan="7" align="right"><strong>Total:</strong></td>
                    <td>R$ <?php echo number_format($total_liquido,2,',','.');?></td>
                </tr>
                
                 </tbody>
            </table>
            
            
            
        </div>
</body>
</html>