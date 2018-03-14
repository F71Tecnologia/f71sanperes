<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

include('../conn.php');
include('../wfunction.php');

$arStatus = array("1"=>"Aberto","2"=>"Aprovado");


if(isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "detalhe"){
    $rs = montaQuery("com_solicitacao","*,DATE_FORMAT(solicitado_em, '%d/%m/%Y') AS solicitado_embr",array("id_solicitacao"=>$_REQUEST['id']));
    $row = current($rs);
    $semana = diasSemanaArray(date("N", strtotime($row['solicitado_em'])));
    $urgente = ($row['urgencia'])?"sim":"não";
    $html = "";
    $html .= "<fieldset><legend>Dados da Solicitação</legend>";
    $html .= "<p><label class='first'>Número:</label> {$row['id_solicitacao']}</p>";
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
    
    echo utf8_encode($html);
    exit;
}
?>
