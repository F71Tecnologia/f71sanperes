<?php


if(isset($_REQUEST['method']) && $_REQUEST['method'] == "dados"){

    $item = $_REQUEST['item'];
    $quantidade_item = $_REQUEST['quantidade_item'];
    $descricao = $_REQUEST['descricao'];
    $urgencia = $_REQUEST['urgencia'];
    $justificativa = $_REQUEST['justificativa'];
    $projetos = $_REQUEST['projetos'];
    $unidade = $_REQUEST['unidades'];
    $anexo = $_REQUEST[''];
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : NULL;
    
    //print_array($_REQUEST);
    $sql_row_pedido = mysql_query("SELECT * FROM novo_pedido");
    $sql_rows_pedido = mysql_num_rows($sql_row_pedido);
    
    mysql_query("INSERT INTO novo_pedido (id_func, urgencia) VALUES ({$usuario['id_funcionario']},'$urgencia[$i]')") or die(mysql_error());
    $novo_id = mysql_insert_id();

    $sql_item = "INSERT INTO item_pedido (id_pedido,item,quantidade,descricao,justificativa,id_projeto,id_unidade,id_func) VALUES ";

    $dados = array(); 
    // concatena os dados linha por linha
    for($i = 0;$i < count($item); $i++) {
        $dados[] = "('$novo_id', '$item[$i]', '$quantidade_item[$i]', '$descricao[$i]', '$justificativa[$i]', '$projetos[$i]', '$unidade[$i]','{$usuario['id_funcionario']}')";
    }
    
    
    //print_array($dados);
    // concatena a consulta com os valores
    $sql_item .= implode(',', $dados);
    mysql_query($sql_item) or die(mysql_error());
    
    //FIM DO ARRAY
    echo $novo_id;
    exit;
    
}

if(isset($_REQUEST['method']) && $_REQUEST['method'] == "up"){
    
    $id_pedido = $_REQUEST['id_pedido'];
    //exit($id_pedido);
    $data = date('Y-m-d');
    $diretorio = dirname(__FILE__)."/orcamento_pdf/";

    $upload = new UploadFile($diretorio,array('jpg','gif','png','pdf','JPG','GIF','PNG','PDF'));
    $upload->arquivo($_FILES['file']);
    $upload->verificaFile();
    
    $insert = mysql_query("INSERT INTO item_pedido_anexo (id_item_pedido,anexo) VALUES ($id_pedido,'/orcamento_pdf/$id_pedido.$data.$upload->extensao');") or die(mysql_error());

    $upload->NomeiaFile("$id_pedido.$data");
    
    $upload->Envia();
    
    print_r($upload);exit;
    
    //echo json_encode(array('status'=>1));
    header('location:pedidos.php');
   
}

$id_regiao = $usuario['id_regiao'];

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$abasel = (isset($_REQUEST['aba'])) ? $_REQUEST['aba'] : 'solicitapedido';

$projeto1 = montaSelect($global->carregaProjetosByRegiao($id_regiao), $projetoR, "id='projeto1' name='projeto' class='form-control validate[required,custom[select]]' data-for='contrato'");
$arrayProjetos = $global->carregaProjetosByRegiao($id_regiao);

$qr_unidade = mysql_query("SELECT * FROM unidade WHERE campo1 = {$id_projeto} AND status_reg = 1 ORDER BY unidade");
        $unidade = $default;
        while ($row_unidade = mysql_fetch_assoc($qr_unidade)) {
            $unidade[$row_unidade['id_unidade']] = $row_unidade['id_unidade'] . " - " . $row_unidade['unidade'];
        }

$unidade1 = montaSelect($unidade, $unidade, "id='unidade1' name='unidade1' class='form-control validate[required,custom[select]]' data-for='contrato'");

function checkAba($aba1, $aba2, $fade = FALSE) {
    $return = ($aba1 == $aba2 && $fade) ? 'in ' : '';
    $return .= ($aba1 == $aba2) ? ' active' : '';
    return $return;
}

$breadcrumb_config = array("nivel" => "../../", "key_btn" => "41", "area" => "Compras", "ativo" => "Gestão de Pedidos", "id_form" => "form-pedido");

$pedido = new pedidosClass();
$objPedidosTipo = new PedidosTipoClass();
   

if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'unidades'){
    $sqlUnidades = mysql_query("SELECT id_unidade, unidade FROM unidade WHERE campo1 = {$_REQUEST['id_projeto']} ORDER BY unidade");
    $arrayUnidades = array("" => "-- SELECIONE --");
//    echo "<select>";
    
    while ($rowUnidades = mysql_fetch_assoc($sqlUnidades)) {
//        echo '<option value="'.$rowUnidades['id_unidade'].'">'.$rowUnidades['id_unidade'] . " - " . utf8_encode($rowUnidades['unidade']).'</option>';
        $arrayUnidades[$rowUnidades['id_unidade']] = $rowUnidades['id_unidade'] . " - " . utf8_encode($rowUnidades['unidade']);
    }
//    echo "</select>";
    $auxDisabled = ($_REQUEST['id_unidade']) ? 'disabled' : null;
    echo montaSelect($arrayUnidades, $_REQUEST['id_unidade'], 'class="form-control"  name="unidades[]" $auxDisabled ');
    
    exit;
}

$qr_pedidos = mysql_query("SELECT * FROM pedidos_tipo WHERE status = 1");
$qr_item = mysql_query("SELECT * FROM nfe_produtos");

$qr_item_pedido = mysql_query("SELECT A.*,E.*,C.nome as projeto_nome,D.unidade as unidade_nome
FROM item_pedido AS A
LEFT JOIN novo_pedido AS B ON (B.id_pedido = A.id_pedido)
LEFT JOIN item_pedido_anexo AS E ON (E.id_item_pedido = A.id_pedido)
LEFT JOIN projeto AS C ON (A.id_projeto = C.id_projeto)
LEFT JOIN unidade AS D ON (A.id_unidade = D.id_unidade)
WHERE B.id_func = {$usuario['id_funcionario']}");

$qr_item_pedido2 = mysql_query("SELECT A.*,B.nome as nome_fornecedor,C.* FROM item_orcamento as A LEFT JOIN fornecedores as B ON (A.id_fornecedor = B.id_fornecedor) LEFT JOIN item_pedido as C ON (A.id_item_pedido = C.id_item)");
$qr_item_pedido3 = mysql_query("SELECT A.*,B.nome as nome_fornecedor,C.* FROM item_orcamento as A LEFT JOIN fornecedores as B ON (A.id_fornecedor = B.id_fornecedor) LEFT JOIN item_pedido as C ON (A.id_item_pedido = C.id_item)");

$qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_regiao = {$usuario['id_regiao']}");
$qr_projeto1 = mysql_query("SELECT * FROM projeto WHERE id_regiao = {$usuario['id_regiao']}");
$qr_unidade = mysql_query("SELECT * FROM unidades WHERE id_projeto = ");

$qr_item_aprovado = mysql_query("SELECT A.*,B.nome as nome_fornecedor,C.*,D.nome as projeto_nome,E.unidade as unidade_nome FROM item_orcamento as A LEFT JOIN fornecedores as B ON (A.id_fornecedor = B.id_fornecedor) LEFT JOIN item_pedido as C ON (A.id_item_pedido = C.id_item) LEFT JOIN projeto as D ON (C.id_projeto = D.id_projeto) LEFT JOIN unidade as E ON (C.id_unidade = E.id_unidade) WHERE A.status = 2");


if(isset($_REQUEST['method']) && $_REQUEST['method'] == "up2"){
    
//    print_array($_REQUEST);exit();
    $id_orcamento = $_REQUEST['id_orcamento'];
    
    $data = date('Y-m-d');
    $diretorio = dirname(__FILE__)."/orcamento_pdf/orcamento/";

    $upload = new UploadFile($diretorio,array('jpg','gif','png','pdf','JPG','GIF','PNG','PDF'));
    $upload->arquivo($_FILES['file']);
    $upload->verificaFile();
     
    //    $insert = mysql_query("UPDATE item_orcamento SET anexo = '/orcamento_pdf/orcamento/$id_orcamento.$data.$upload->extensao' WHERE id_orcamento = $id_orcamento;") or die(mysql_error());
    $insert = mysql_query("INSERT INTO item_orcamento (id_item_pedido, anexo, principal) VALUES ($id_orcamento,'/orcamento_pdf/orcamento/$id_orcamento.$data.$upload->extensao', 1);") or die(mysql_error());
//   $id_orcamento = mysql_insert_id();
    $upload->NomeiaFile("$id_orcamento.$data");
    
    $upload->Envia();
    
//    print_r($upload);
    //echo json_encode(array('status'=>1));
//    header('location:pedidos.php');
    
      
    
    
    //FIM DO ARRAY
//    echo $id_orcamento;
    exit;
   
}
if(isset($_REQUEST['method']) && $_REQUEST['method'] == "dados2"){
    $id_orcamento = $_REQUEST['id_orcamento'];
    $id_pedido = $_REQUEST['id_pedido'];
    $cnpj = $_REQUEST['cnpj_fornecedor'];
    $nome = $_REQUEST['nome_fornecedor'];
    $razao = $_REQUEST['razao_fornecedor'];
    $endereco = $_REQUEST['endereco_fornecedor'];
    $telefone = $_REQUEST['tel_fornecedor'];
    $email = $_REQUEST['email_fornecedor'];
    $valor = str_replace(",",".",str_replace(".","", str_replace("R$ ", "", $_REQUEST['valor_fornecedor']))); 
    $anexo = $_REQUEST[''];
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : NULL;
   
        
//    print_array($_REQUEST);
    
    
    $reg_forn = mysql_query("SELECT * FROM fornecedores WHERE cnpj = '$cnpj'");
    $reg_forn_ass = mysql_fetch_assoc($reg_forn);
    $reg_forn_num = mysql_num_rows($reg_forn);
    
//    $reg_principal = mysql_query("SELECT * FROM item_orcamento WHERE pricipal = 1 AND id_item_pedido = $id_pedido");
//    $reg_forn_ass_princ = mysql_fetch_assoc($reg_principal);
//    $reg_forn_num_princi = mysql_num_rows($reg_principal);


    if($reg_forn_num){
        $id_fornecedor = $reg_forn_ass['id_fornecedor'];
    }else{
        $insert_fornecedor = mysql_query("INSERT INTO fornecedores (nome,razao,endereco,cnpj,tel,email) VALUES ('$nome','$razao','$endereco','$cnpj','$telefone','$email');") or die(mysql_error());
        $id_fornecedor = mysql_insert_id();
    }
    
//    mysql_query("INSERT INTO item_orcamento (id_item_pedido,id_fornecedor,valor,flag) VALUES ($id_pedido,$id_fornecedor,$valor,1);") or die(mysql_error());
    $query_up = mysql_query("UPDATE item_orcamento SET id_fornecedor = $id_fornecedor,valor = $valor,flag = 3 WHERE principal = 1 AND id_item_pedido = $id_pedido;") or die(mysql_error());
    //echo "UPDATE item_orcamento SET id_fornecedor = $id_fornecedor,valor = $valor,flag = 1 WHERE principal = 1 AND id_item_pedido = $id_pedido;";
//    $id_orcamento = mysql_insert_id();
    
     
//     echo "teste";exit();
    //FIM DO ARRAY
//    echo $id_orcamento;
//    exit;
    header('location:pedidos.php');
    
}

if(isset($_REQUEST['method']) && $_REQUEST['method'] == "dados3"){

    $id_pedido = $_REQUEST['id_pedido'];
    $anexo = $_REQUEST[''];
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : NULL;
    
        
    //print_array($_REQUEST);
    
    
    $reg_forn = mysql_query("SELECT * FROM fornecedores WHERE cnpj = '$cnpj'");
    $reg_forn_ass = mysql_fetch_assoc($reg_forn);
    $reg_forn_num = mysql_num_rows($reg_forn);

 
    
    if($reg_forn_num){
        $id_fornecedor = $reg_forn_ass['id_fornecedor'];
    }else{
        $insert_fornecedor = mysql_query("INSERT INTO fornecedores (nome,razao,endereco,cnpj,tel,email) VALUES ($nome,$razao,$endereco,$cnpj,$telefone,$email);") or die(mysql_error());
        $id_fornecedor = mysql_insert_id();
    }
    
    mysql_query("INSERT INTO item_orcamento (id_item_pedido,id_fornecedor,valor,flag) VALUES ($id_pedido,$id_fornecedor,$valor,1);") or die(mysql_error());
    $id_orcamento = mysql_insert_id();
    
    
    //FIM DO ARRAY
    echo $id_orcamento;
    exit;
    
}


if(isset($_REQUEST['method']) && $_REQUEST['method'] == "up3"){
    
    $id_orcamento = $_REQUEST['id_orcamento'];
    
    $data = date('Y-m-d');
    $diretorio = dirname(__FILE__)."/orcamento_pdf/orcamento/";

    $upload = new UploadFile($diretorio,array('jpg','gif','png','pdf','JPG','GIF','PNG','PDF'));
    $upload->arquivo($_FILES['file']);
    $upload->verificaFile();
     
    $insert = mysql_query("UPDATE item_orcamento SET anexo = '/orcamento_pdf/orcamento/$id_orcamento.$data.$upload->extensao' WHERE id_orcamento = $id_orcamento;") or die(mysql_error());
    
    $upload->NomeiaFile("$id_orcamento.$data");
    
    $upload->Envia();
    
    print_r($upload);
    //echo json_encode(array('status'=>1));
    header('location:pedidos.php');
   
}




