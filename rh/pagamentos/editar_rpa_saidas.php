<?php
include('../../conn.php');
include("../../wfunction.php");
include("../../funcoes.php");
include ("../../classes_permissoes/acoes.class.php");

$id_saida = $_REQUEST['id_saida']; 
$id_usuario = $_REQUEST['id_usuario'];
$nome_completo = $_REQUEST['nome_completo'];

$qr_saida = "SELECT A.nome AS descricao, A.especifica, A.id_banco, B.nome AS nome_banco, A.valor, A.data_vencimento 
            FROM saida AS A
            LEFT JOIN bancos AS B ON (A.id_banco = B.id_banco)
            WHERE A.id_saida =  $id_saida;";
$result = mysql_query($qr_saida);
$row_saida = mysql_fetch_assoc($result);


if(isset($_REQUEST['editar'])){
    $novoVal = str_replace('.', '',$_REQUEST['valorNovo']);
    $novaData = implode('-', array_reverse(explode('/',$_REQUEST['dataNova']))); 
    $id_saida = $_REQUEST['id_saida'];
    $id_usuario = $_REQUEST['id_usuario'];
//    $qr_EditSaida = mysql_query("UPDATE saida SET valor = '$novoVal', data_vencimento = '$novaData' WHERE id_saida = $id_saida AND status = 1 LIMIT 1;") or die("Erro ao atualizar dados da saída.");
    $qr_rpaSaidaAssoc = mysql_query("SELECT A.id_rpa
                            FROM rpa_saida_assoc AS A
                            INNER JOIN saida AS B ON A.id_saida = B.id_saida
                            WHERE A.id_saida = $id_saida AND B.status != 0;");
    $row_rpaSaidaAssoc = mysql_fetch_assoc($qr_rpaSaidaAssoc);
    print_r("UPDATE rpa_autonomo SET valor_liquido = '$novoVal' WHERE id_rpa = {$row_rpaSaidaAssoc['id_rpa']} LIMIT 1;");
    exit;
//    $qr_rpa = mysql_query("UPDATE rpa_autonomo SET valor_liquido = '$novoVal' WHERE id_rpa = {$row_rpaSaidaAssoc['id_rpa']} LIMIT 1;") or die ("Erro ao atualizar o valor do rpa.");
//    $qr_log = mysql_query("INSERT INTO log_desprocessar_saida (id_saida, id_usuario) VALUES ('$id_saida', '$id_usuario');") or die("Erro ao gravar o log.");
    echo 'Saída alterada com sucesso...';
        echo "<script> 
                setTimeout(function(){
                window.parent.location.href = 'http://" . $_SERVER['HTTP_HOST'] . "/intranet/rh/pagamentos/index.php?id=1&regiao=$regiao&mes=$mes_consulta&ano=$ano_consulta&filtrar=1&tipo_pagamento=1&tipo_contrato=2';
                parent.eval('tb_remove()')
                },3000)    
        </script>";
    exit;
}
?>
<html>
    <head>
        <title>RH - Pagamentos</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>        
        <script src="../../jquery/priceFormat.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        
        <script>
            $(function() {
                $('#dataNova').datepicker({
                                            dateFormat: 'dd/mm/yy',
                                            changeMonth: true,
                                            changeYear: true
                });   
            
                $('#valorNovo').priceFormat({
                    prefix: '',
                    centsSeparator: ',',
                    thousandsSeparator: '.'
                });
        });
        </script>
        <style>
            label { font-weight: bold; }
            h3{ color: #0bbfe7; }
            body{background-color: #FFF;}
            
        </style>
    </head>
    <body class="novaintra">
        <div id="content">
           <form name="form1" id="form1" method="post">
           <h3> <?= $nome_completo; ?> </h3>
           <p>
               <label>Cod. Saída: <?php echo $id_saida; ?></label>
               <label>Descrição: <?php echo $row_saida['descricao']; ?></label>
           </p>
           <p><label>Especificação: <?php echo $row_saida['especifica']; ?></label></p>
           <p><label>Banco: <?php echo $row_saida['id_banco'] . " - " . $row_saida['nome_banco']; ?></label></p>
           <p><label>Valor</label> <input type="text" name="valorNovo" id="valorNovo" value="<?php echo $row_saida['valor']; ?>"/></p>
           <p><label>Vencimento Em</label> <input type="text" name="dataNova" id="dataNova" value="<?php echo $data_sql = date("Y-m-d", strtotime(str_replace("/", "-", $row_saida['data_vencimento'])));?>"</p>
           <input type="hidden" value="<?php echo $id_usuario;?>" name="id_usuario"/>
           <input type="hidden" value="<?php echo $id_saida;?>" name="id_saida"/>
            <p class="controls"> 
                <input type="submit" value="Editar" name="editar"/>          
                <input type="button" value="Voltar" name="voltar" onclick="history.go(-1)"/>   
            </p>
</div>
</body>
</html>