<?php
header('Content-Type: text/html; charset=ISO-8859-1');

if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="../../login.php">Logar</a>';
    exit;
}

include('../../conn.php');
include('../../wfunction.php');

$arStatus = array("1"=>"Aberto","2"=>"Aprovado");


if(isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "detalhe"){
    
    $sql1 = "SELECT A.*,B.nome1,
                            date_format(A.data_requisicao, '%d/%m/%Y')as solicitado_embr 
                    FROM    compra AS A 
               LEFT JOIN    funcionario AS B ON (A.id_user_pedido = B.id_funcionario)
                   WHERE    A.id_compra = {$_REQUEST['id']}";
    $result_1 = mysql_query($sql1) or die("erro re1: <!-- {$sql1} -->".  mysql_error());
    $row = mysql_fetch_assoc($result_1);
    
    
    $semana = diasSemanaArray(date("N", strtotime($row['data_requisicao'])));
    $urgente = ($row['urgencia'])?" - URGENTE":"";
    $tipo = ($row['tipo'])?"Serviço":"Produto";
    
    $html = "";
    $html .= "<fieldset><legend>Dados da Solicitação</legend>";
    $html .= "<p><label class='first'>Número:</label> {$row['num_processo']}</p>";
    $html .= "<div  class=\"fleft\" style=\"width: 55%\">";
    $html .= "<p><label class='first'>Data:</label> {$row['solicitado_embr']} - {$semana}</p>";
    $html .= "<p><label class='first'>Tipo:</label> {$row['tipo']}</p>";
    
    $html .= "</div><div  class=\"fleft\">";
    $html .= "<p><label class='first'>Urgente:</label> {$urgente}</p>";
    $html .= "<p><label class='first'>Patrimônio:</label> {$row['patrimonio']}</p>";
    $html .= "</div>";
    
    
    $html .= "<p class='clear'><label class='first'>Pedido:</label> {$row['descricao']}</p>";
    $html .= "<p><label class='first'>Justificativa:</label> {$row['justificativa']}</p>";
    $html .= "<p><label class='first'>Status:</label> {$arStatus[$row['status']]}</p>";
    $html .= "</fieldset>";
    
    $html .= "<fieldset><legend>Histórico da Solicitação</legend>";
    $html .= "<table width='100%' cellpadding='0' cellspacing='0' border='0' class='grid'><tbody>";
    $html .= "<tr><td>a</td><td>b</td><td>c</td></tr>";
    $html .= "</tbody></table>";
    $html .= "</fieldset>";
    
    //echo utf8_encode($html);
    //exit;
}else{
    exit;
}
?>

<div class="page-header box-compras-header" style="margin-top: -12px;">
    <h4>Número da Solicitação: <?php echo $row['num_processo'].$urgente ; ?></h4>
</div>

<table class="table table-striped table-hover table-condensed table-bordered">    
    <tbody>      
        <tr>
            <td class="text-bold">Data:</td>
            <td><?php echo $row['solicitado_embr']." - ".$semana; ?></td>
            <td class="text-bold">Tipo</td>
            <td><?php echo $tipo; ?></td>
        </tr>
        <tr>
            <td class="text-bold">Solicitação</td>
            <td><?php echo $row['nome_produto']; ?></td>
            <td class="text-bold">Solicitante</td>
            <td><?php echo $row['nome1']; ?></td>
        </tr>
        <tr>
            <td class="text-bold">Valor Médio</td>
            <td><?php echo $row['valor_medio']; ?></td>
            <td class="text-bold">Quantidade</td>
            <td><?php echo $row['quantidade']; ?></td>
        </tr>
        <tr>
            <td class="text-bold">Descrição</td>
            <td colspan="3"><?php echo $row['descricao_produto']; ?></td>
        </tr>
        <tr>
            <td class="text-bold">Necessidade</td>
            <td colspan="3"><?php echo $row['necessidade']; ?></td>
        </tr>
    </tbody>
</table>