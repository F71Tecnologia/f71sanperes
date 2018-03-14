<?php

class pedidosClass {

    public $pedido_cod;
    public $pedido_item;
    public $pedido_quantidade;
    public $pedido_regiao;
    public $pedido_projeto;
    public $pedido_data;
    public $pedido_status;
    public $fornecedor;
    public $endereco;
    public $email;
    public $upa;
    public $total;
    public $debug = FALSE;
    public $fpdf;
    public $nomeFile;
    public $sql_folha;
    public $file_path;
    public $id_fornecedor;

//    public $folhas = array();

    public function reiniciaSequencial() {
        unset($this->sequencial);
        $this->sequencial = 1;
    }

    public function __construct($pedido_cod = NULL, $pedido_item = NULL, $pedido_quantidade = NULL, $pedido_regiao = NULL, $pedido_projeto = NULL, $pedido_data = NULL, $pedido_status = NULL) {
        $this->pedido_cod = $pedido_cod;
        $this->pedido_item = $pedido_item;
        $this->pedido_quantidade = $pedido_quantidade;
        $this->pedido_regiao = $pedido_regiao;
        $this->pedido_projeto = $pedido_projeto;
        $this->pedido_data = $pedido_data;
        $this->pedido_status = $pedido_status;
        $this->pedido_usuario = $pedido_usuario;
        $this->pedido_observacao = $pedido_observacao;
        $this->file_path = $_SERVER['DOCUMENT_ROOT'] . '/intranet/compras/pedidos/pdf/';
    }

    public function setFileName($nomeFile) {
        $this->nomeFile = $nomeFile;
    }

    public function solicitacaoPedido($pedido_regiao, $pedido_projeto, $id_prestador, $pedido_status, $observacao, $id_usuario, $array_itens, $tipo) {
        $qry_sPed = "INSERT INTO pedidos (id_regiao, id_projeto, id_prestador, datadopedido, status, observacao, feito_por, tipo )
                     VALUES ('{$pedido_regiao}','{$pedido_projeto}','{$id_prestador}', NOW(), 1,'{$observacao}','{$id_usuario}',$tipo)";

        echo ($this->debug) ? $qry_sPed . "<br><br>" : '';

        $result = mysql_query($qry_sPed) or die('Erro ao salvar Pedido. ' . $qry_sPed . ' Detalhes: ' . mysql_error());
        $id_pedido = mysql_insert_id();
        $result2 = $this->salvarItem($array_itens, $id_pedido, $id_prestador, $pedido_projeto);
        return ($result && $result2) ? array('id_pedido' => $id_pedido, 'status' => true) : array('msg' => 'Erro ao salvar Pedido.', 'status' => false);
    }

    public function salvarItem($array, $id_pedido, $id_prestador, $id_projeto) {
        foreach ($array as $itemPed) {
            $qry_itemPed = "INSERT INTO pedidos_itens (id_pedido, id_prod, qCom, vProd)
            VALUES ('{$id_pedido}','{$itemPed['id_prod']}','{$itemPed['qCom']}','{$itemPed['vProd']}')";

            echo ($this->debug) ? $qry_itemPed . "<br><br>" : '';

            $result = mysql_query($qry_itemPed) or die('ERRO AO INCLUIR ITEM: ' . mysql_error());

            $retorno[] = ($result) ? array('id_item' => mysql_insert_id(), 'status' => TRUE) : array('status' => FALSE);
        }
        return $retorno;
    }

    public function preparaArrayItens($qtde, $vProd, $id_prod, $id_item = array()) {

        if (!is_array($qtde) && !is_array($vProd) && !is_array($id_item)) {
            return array('status' => FALSE, 'msg' => 'Não é array.');
        }
        for ($i = 0; $i <= count($qtde); $i++) {
            if ($qtde[$i] != 0) {
                $itens_pedido[] = array(
                    'qCom' => str_replace(',', '.', str_replace('.', '', $qtde[$i])),
                    'vProd' => str_replace(',', '.', str_replace('.', '', $vProd[$i])),
                    'id_prod' => $id_prod[$i],
                    'id_item' => $id_item[$i],
                );
            }
        }

        return $itens_pedido;
    }

    public function consultarItem($dados, $id_fornecedor) {

        $where = $this->prepara_where($dados);

        $query = "SELECT A.*, ROUND(A.qCom - qtd_recebida,3) AS qtd_faltando ,b.*,c.valor_produto AS vUnCom
            FROM pedidos_itens AS A 
            INNER JOIN nfe_produtos AS b ON (A.id_prod = b.id_prod) 
            INNER JOIN produto_fornecedor_assoc AS c ON (b.id_prod = c.id_produto AND c.id_fornecedor = $id_fornecedor)
                  $where";

        $result = mysql_query($query) or die(mysql_error());

        while ($row = mysql_fetch_assoc($result)) {
            $return[$row['id_prod']] = $row;
            // gambiarra para funcionar a validacao
            // aqui será montado um array com todos os cProd do produto
            $query2= "SELECT cProd FROM nfe_produtos_assoc WHERE id_produto = {$row['id_prod']}";
            $result2 = mysql_query($query2);
            $i = 0;
            while($row2 = mysql_fetch_assoc($result2)){
                $return[$row['id_prod']]['cProd'][$i] = $row2['cProd'];
                $i++;
            }
            // fim da gambiarra
        }
        return $return;
    }

    public function pesquisafornecedor() {
        $qryFornec = mysql_query("SELECT * FROM contabil_fornecedor WHERE `status` = '1' ORDER BY razao;");
        $return['-1'] = '« Selecione »';
        while ($row = mysql_fetch_assoc($qryFornec)) {
            $return[$row['id_fornecedor']] = $row['razao'] . ' /  ' . $row['cnpj'];
        }
        return $return;
    }

    public function selectFornecedor($id_projeto) {
        $query = "SELECT a.id_fornecedor,a.razao,a.cnpj 
                    FROM contabil_fornecedor AS a
                    INNER JOIN prestadorservico AS b ON a.id_fornecedor = b.id_contabil_fornecedor
                    WHERE a.`status` = '1' AND b.id_projeto = '$id_projeto'
                    ORDER BY a.razao";
        $return['-1'] = '« Selecione »';
        $x = mysql_query($query);
        while ($row = mysql_fetch_assoc($x)) {
            $return[$row['id_fornecedor']] = mascara_string('##.###.###/####-##', $row['cnpj']) . ' - ' . $row['razao'];
        }
        return $return;
    }

    public function selectFornecedorContrato($id_projeto, $tipo) {
        $query = "SELECT b.id_prestador,b.c_razao AS razao,REPLACE(REPLACE(REPLACE(b.c_cnpj,'/',''),'.',''),'-','') as cnpj 
                    FROM prestadorservico AS b
                    INNER JOIN pedidos_tipo AS c ON (b.id_cnae = c.id_cnae) 
                    WHERE b.encerrado_em >= NOW() AND b.id_projeto = '{$id_projeto}' AND c.id_tipo = '{$tipo}' ORDER BY b.c_razao";
//                    echo $query;
        $return['-1'] = '« Selecione »';
        $x = mysql_query($query);
        while ($row = mysql_fetch_assoc($x)) {
            $return[$row['id_prestador']] = mascara_string('##.###.###/####-##', $row['cnpj']) . ' - ' . $row['razao'];
        }
        return $return;
    }

    public function consultarProduto($id_prod, $id_prestador) {
        $query = "SELECT *, b.valor_produto AS vUnCom
            FROM nfe_produtos AS a 
            INNER JOIN produto_fornecedor_assoc AS b ON (a.id_prod = b.id_produto AND b.id_fornecedor = $id_prestador) 
            WHERE id_prod = $id_prod AND a.status = 1";
        $result = mysql_query($query) or die(mysql_error());
        return mysql_fetch_assoc($result);
    }

    public function consultaPedido($dados = NULL, $select_itens = FALSE) {
        $condicoes = $this->prepara_where($dados);
        $query = "SELECT A.*,A.id_pedido, B.id_prestador AS id_fornecedor, B.c_razao AS razao, REPLACE(REPLACE(REPLACE(B.c_cnpj,'/',''),'-',''),'.','') AS razao_cnpj, B.c_endereco AS razao_endereco, C.nome AS upa, C.cnpj AS upa_cnpj, C.endereco AS upa_endereco, A.id_projeto AS id_projeto, A.datadopedido AS dtpedido, D.nome1 AS solicitado, E.nome1 AS confirmado, A.observacao AS observacao, B.c_email AS email, A.status 
                FROM pedidos A 
                LEFT JOIN prestadorservico B ON (B.id_prestador = A.id_prestador) 
                LEFT JOIN rhempresa C ON (C.id_projeto = A.id_projeto) 
                LEFT JOIN funcionario D ON (D.id_funcionario = A.feito_por) 
                LEFT JOIN funcionario E ON (E.id_funcionario = A.conferencia) 
                $condicoes ORDER BY dtpedido DESC";
        //echo $query . '<br><br>';
        $result = mysql_query($query) or die($query . '<br>Erro ao consultar Pedido. Detalhes: ' . mysql_error());

        $return = array();
        while ($row = mysql_fetch_assoc($result)) {
            $return[$row['id_pedido']] = $row;
            $return[$row['id_pedido']]['total'] = $this->totalPedido($row['id_pedido']);
            $this->id_projeto = $row['id_projeto'];
            $this->id_fornecedor = $row['id_prestador'];
            $this->pedido_cod = $row['id_pedido'];
            $this->fornecedor = $row['razao'];
            $this->cnpj = $row['razao_cnpj'];
            $this->endereco = $row['razao_endereco'];
            $this->upa = $row['upa'];
            $this->upa_endereco = $row['upa_endereco'];
            $this->pedido_data = converteData($row['dtpedido'], "d/m/Y");
            $this->upa_cnpj = $row['upa_cnpj'];
            $this->solicitado = $row['solicitado'];
            $this->conferido = $row['confirmado'];
            $this->observacao = $row['observacao'];
            $this->email = $row['email'];
            if ($select_itens) {
                $this->pedido_item = $this->consultarItem("id_pedido = {$row['id_pedido']}",$row['id_prestador']);
                $return[$row['id_pedido']]['itens'] = $this->pedido_item;
            }
        }

        return $return;
    }

    public function consultaPedidoById($id) {
        $query = "SELECT A.*,A.id_pedido, B.id_fornecedor AS id_fornecedor, B.razao AS razao, B.cnpj AS razao_cnpj, B.endereco AS razao_endereco,
                C.nome AS upa, C.cnpj AS upa_cnpj, C.endereco AS upa_endereco, A.id_projeto AS id_projeto, A.datadopedido AS dtpedido, 
                D.nome1 AS solicitado, E.nome1 AS confirmado, A.observacao AS observacao, B.email AS email, A.status
                FROM pedidos A
                LEFT JOIN contabil_fornecedor B ON (B.id_fornecedor = A.id_prestador)
                LEFT JOIN rhempresa C ON (C.id_projeto = A.id_projeto)
                LEFT JOIN funcionario D ON (D.id_funcionario = A.feito_por)
                LEFT JOIN funcionario E ON (E.id_funcionario = A.conferencia)
                WHERE id_pedido = $id ORDER BY dtpedido DESC";
//        echo $query . '<br><br>';
        $result = mysql_query($query) or die($query . '<br>Erro ao consultar Pedido. Detalhes: ' . mysql_error());
        $row = mysql_fetch_assoc($result);
        $row['itens'] = $this->consultarItem("id_pedido = {$row['id_pedido']}",$row['id_prestador']);

        return $row;
    }

    public function pedidosCancels($dados, $select_itens = FALSE) {
        $dados['status'] = 0;
        return $this->consultaPedido('A.status = 0', $select_itens);
    }

    public function pedidosSolicitados($dados, $select_itens = FALSE) {
        $dados['status'] = 1;
        return $this->consultaPedido($dados, $select_itens);
    }

    public function pedidosConfirmados($dados, $select_itens = FALSE) {
        $dados['status'] = 2;
        return $this->consultaPedido($dados, $select_itens);
    }

    public function pedidosEnviados($dados, $select_itens = FALSE) {
        $dados['status'] = 3;
        return $this->consultaPedido($dados, $select_itens);
    }

    public function pedidosAbertos($dados, $select_itens = FALSE) {
        $dados['status'] = 4; // com nfe vinculada, mas com itens faltando
        return $this->consultaPedido($dados, $select_itens);
    }

    public function pedidosFinalizados($dados, $select_itens = FALSE) {
        $dados['status'] = 5;
        return $this->consultaPedido($dados, $select_itens);
    }

    public function totalPedido($id_pedido) {
        $query = "SELECT ROUND(sum(vProd), 2) AS total FROM pedidos_itens WHERE id_pedido = $id_pedido";
        $result = mysql_query($query) or die('Erro ao consultar Pedido. Detalhes: ' . mysql_error());
        $return = mysql_fetch_assoc($result);
        return $return['total'];
    }

    public function analisaPedido($id_pedido, $array) {
        $qry = "SELECT * FROM pedidos_itens WHERE id_pedido = $id_pedido";
        $result = mysql_query($qry) or die(mysql_error());
        $teste = TRUE;
        while ($row = mysql_fetch_array($result)) {
            $teste = ($array['qtde'] == $row['qtde']) ? $teste && TRUE : $teste && FALSE;
        }
        return $teste;
    }

    public function conferirPedidos($id_pedido, $nfe, $array) {

        $teste = TRUE;
        $result = $this->analisaPedido($id_pedido, $array);
        while ($row = mysql_fetch_array($result)) {
            $teste = ($array['id_prod'] == $row["id_prod"]) ? $teste && TRUE : $teste && FALSE;
        }
        return $teste;
    }

    public function confirmaOk($id_pedido, $array, $usuario) {
        $results = $this->analisaPedido($id_pedido, $array);
        if ($results) {
            foreach ($array as $value) {
                if (empty($value['id_item'])) {
                    $this->salvarItem(array($value), $id_pedido);
                } else {
                    $salvo[] = $this->atualizaItemPedido($value);
                }
            }
        }
        $qry1 = "UPDATE pedidos SET status = '2', conferencia = '$usuario' WHERE id_pedido = $id_pedido";
        return mysql_query($qry1);
    }

    public function alteraStatus($id_pedido) {
        $qry_Ped = "UPDATE pedidos SET status = '3' WHERE id_pedido = $id_pedido";

        return mysql_query($qry_Ped) or die('ERRO AO ATUALIZAR STATUS: ' . mysql_error());
    }

    public function reabrirPedidos($id_pedido) {
        $qryReabrir = "UPDATE pedidos SET status = '1' WHERE id_pedido = '{$id_pedido}'";
        return mysql_query($qryReabrir) or die('ERRO AO ATUALIZAR STATUS: ' . mysql_error());

//        $qry_Reabrir = "UPDATE pedidos_cancelados SET status = '1' WHERE id_pedido = '{$id_pedido}'";
//        mysql_query($qry_Reabrir) or die('ERRO AO ATUALIZAR STATUS: ' . mysql_error());
//
//        $qryPedidoReaberto = "INSERT INTO pedidos_cancelados (id_pedido, dtamovimento, status, motivo, feito_por)
//                        VALUES( '{$id_pedido}', NOW(), '1', '{$motivo}', '{$usuario}')";
//        return mysql_query($qryPedidoReaberto) or die('Erro ao atualizar Pedido: ' . mysql_error());
    }

    public function cancelar_pedido($id_pedido, $usuario, $motivo = '') {
        $motivo = str_replace('"', '', str_replace("'", '', $motivo));
        $query = "UPDATE pedidos SET status = 0, observacao = '$motivo', conferencia = '$usuario' WHERE id_pedido = $id_pedido";
        return mysql_query($query) or die('ERRO AO ATUALIZAR STATUS FINALIZADO: ' . mysql_error());
    }

    public function atualizaStatusFinalizado($id_pedido) {
        $query = "UPDATE pedidos SET status = 5 WHERE id_pedido = $id_pedido";
        return mysql_query($query) or die('ERRO AO ATUALIZAR STATUS FINALIZADO: ' . mysql_error());
    }

    public function atualizaStatusAberto($id_pedido) {
        $query = "UPDATE pedidos SET status = 4 WHERE id_pedido = $id_pedido";
        return mysql_query($query) or die('ERRO AO ATUALIZAR STATUS ABERTO: ' . mysql_error());
    }

//    public function atualizaItemPedido($dados) {
//        if (empty($dados) || !is_array($dados)) {
//            return array('status' => FALSE, 'msg' => 'Array vazio ou não é um array.');
//        }
//        $query = "UPDATE pedidos_itens SET qCom = {$dados['qtde']}, vProd = {$dados['vProd']} WHERE id_item = {$dados['id_item']}";
//        return mysql_query($query);
//    }
//    
    public function atualizaItemPedido($dados) {
        if (empty($dados) || !is_array($dados)) {
            return array('status' => FALSE, 'msg' => 'Array vazio ou não é um array.');
        }
        $id = $dados['id_item'];
        unset($dados['id_item']);
        foreach ($dados as $key => $value) {
            $x[] = "$key = '$value'";
        }
        $query = "UPDATE pedidos_itens SET " . implode(', ', $x) . " WHERE id_item = {$id}";
        return mysql_query($query);
    }

    public function atualizaStatusEntrega($id_item, $status) {
        $query = "UPDATE pedidos_itens SET entregue = {$status} WHERE id_item = {$id_item}";
        return mysql_query($query) or die(mysql_error());
    }

    public function iniciaFpdf() {
        define('FPDF_FONTPATH', $_SERVER['DOCUMENT_ROOT'] . '/intranet/rh/fpdf/font/');
        $this->fpdf = new FPDF("P", "cm", "A4");
        $this->fpdf->SetAutoPageBreak(true, 0.0);
        $this->fpdf->Open();
    }

//    public function validaValores() {
//        $return = true;
//        if ($this->quantidade == 0) {
//            $return = false;
//        }
//        return $return;
//    }

    public function Hearder() {

        $this->fpdf->SetFont('Arial', 'B', 15);
    }

    // Color table
//    public function listaProduto($header, $data) {
//        
//        $this->SetFillColor(255,0,0);
//        $this->SetTextColor(255);
//        $this->SetDrawColor(128,0,0);
//        $this->SetLineWidth(.3);
//        $this->SetFont('','B');
//    
//        $w = array(40, 35, 40, 45);
//        for($i=0;$i<count($header);$i++)
//            $this->Cell($w[$i],7,$header[$i],1,0,'C',true);
//        $this->Ln();
//
//        $this->SetFillColor(224,235,255);
//        $this->SetTextColor(0);
//        $this->SetFont('');
//    
//        $fill = false;
//        foreach($data as $row) {
//            $this->Cell($w[0],6,$row[0],'LR',0,'L',$fill);
//            $this->Cell($w[1],6,$row[1],'LR',0,'L',$fill);
//            $this->Cell($w[2],6,number_format($row[2]),'LR',0,'R',$fill);
//            $this->Cell($w[3],6,number_format($row[3]),'LR',0,'R',$fill);
//            $this->Ln();
//            $fill = !$fill;
//        }
//
//        $this->Cell(array_sum($w),0,'','T');
//    }


    public function geraPdf() {

        $this->fpdf->SetFont('Arial', 'B', 9);
        $this->fpdf->Cell(1, 30, " ");
        $this->fpdf->SetXY(1, 1);
        $this->fpdf->Image('../../imagens/UPA-24-horas.jpg', 0.4, 0.5, 5.4, 3.4, 'jpg');
        $this->fpdf->Image('../../imagens/logomaster6.gif', 16.13, 1.1, 3.2, 1.8, 'gif');

        $this->fpdf->SetXY(8, 1.2);
        $this->fpdf->Cell(1, 1, "Data " . $this->pedido_data, 0, 0, 'L');

        $this->fpdf->SetXY(8, 1.6);
        $this->fpdf->Cell(1, 1, "Pedido Número " . $this->pedido_cod, 0, 0, 'L');

        $this->fpdf->SetXY(1, 3.4);
        $this->fpdf->Cell(19.02, 1.6, " ", 1, 1, 'C');

        $this->fpdf->SetFont('Arial', 'B', 6);
        $this->fpdf->SetXY(1.5, 3.6);
        $this->fpdf->Cell(0, 0, "FORNECEDOR", 0, 0, 'L');

        $this->fpdf->SetFont('Arial', 'B', 9);
        $this->fpdf->SetXY(1.5, 4.0);
        $this->fpdf->Cell(0, 0, $this->fornecedor, 0, 0, 'L');

        $this->fpdf->SetXY(16, 4.0);
        $this->fpdf->Cell(0, 0, mascara_string("##.###.###/####-## ", $this->cnpj), 0, 0, 'C');

        $this->fpdf->SetXY(1.5, 4.4);
        $this->fpdf->Cell(0, 0, $this->endereco, 0, 0, 'L');

        $this->fpdf->SetXY(1, 5.2);
        $this->fpdf->Cell(19.02, 1.6, " ", 1, 1, 'C');

        $this->fpdf->SetFont('Arial', 'B', 6);
        $this->fpdf->SetXY(1.5, 5.4);
        $this->fpdf->Cell(0, 0, "SOLICITANTE", 0, 0, 'L');

        $this->fpdf->SetFont('Arial', 'B', 9);
        $this->fpdf->SetXY(1.5, 5.8);
        $this->fpdf->Cell(0, 0, $this->upa, 0, 0, 'L');

        $this->fpdf->SetXY(16, 5.8);
        $this->fpdf->Cell(0, 0, $this->upa_cnpj, 0, 0, 'C');

        $this->fpdf->SetXY(1.5, 6.2);
        $this->fpdf->Cell(0, 0, $this->upa_endereco, 0, 0, 'L');

        $this->fpdf->SetXY(1.5, 7.2);
        $this->fpdf->Cell(0, 0, $this->observacao, 0, 0, 'C');

        $this->fpdf->SetFont('Arial', 'B', 6);
        $this->fpdf->SetXY(1, 8);
//        $w = array(2.5, 9.7, 1, 1.5, 1.5, 2);
        $w = array(0, 11.7, 1.5, 1.5, 1.5, 2);

        $this->fpdf->Cell(0.8, 0.5, "Item", 1, 0, 'C');
//        $this->fpdf->Cell($w[0], 0.5, "Código", 1, 0, 'C');
        $this->fpdf->Cell($w[1], 0.5, "Descrição", 1, 0, 'L');
        $this->fpdf->Cell($w[2], 0.5, "Und", 1, 0, 'C');
        $this->fpdf->Cell($w[3], 0.5, "Vlr Acordado", 1, 0, 'C');
        $this->fpdf->Cell($w[4], 0.5, "Quantidade", 1, 0, 'C');
        $this->fpdf->Cell($w[5], 0.5, "Total R$", 1, 0, 'C');
        $this->fpdf->Ln();

        $this->fpdf->SetXY(1, 8.5);
        $i = 1;
        foreach ($this->pedido_item as $itens) {
            $this->fpdf->Cell(0.8, 0.5, $i, 1, 0, 'C');
//            $this->fpdf->Cell($w[0], 0.5, $itens['id_prod'], 1, 0, 'C');
            $this->fpdf->Cell($w[1], 0.5, substr($itens['xProd'], 0, 60), 1, 0, 'L');
            $this->fpdf->Cell($w[2], 0.5, $itens['uCom'], 1, 0, 'C');
            $this->fpdf->Cell($w[3], 0.5, number_format($itens['vUnCom'], 3, ",", "."), 1, 0, 'R');
            $this->fpdf->Cell($w[4], 0.5, $itens['qCom'], 1, 0, 'R');
            $total = $itens['vUnCom'] * $itens['qCom'];
            $this->fpdf->Cell($w[5], 0.5, number_format($total, 2, ",", "."), 1, 0, 'R');
            $this->fpdf->Ln();
            $i++;
            $tot += $total;
            $this->fpdf->SetAutoPageBreak(true, 1.5);
        }
        $this->fpdf->SetX(15);
        $this->fpdf->Cell(5, 0.7, "Total do Pedido R$ " . number_format($tot, 2, ",", ".") . "", 1, 0, 'C');
        $this->fpdf->SetAutoPageBreak(true, 1.5);
    }

    public function limpaVariaveis() {
        unset($this->pedido_cod);
    }

    public function downloadFile() {
        $path = $this->file_path . $this->nomeFile;
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Content-type: application/x-msdownload");
        header("Content-Length: " . filesize($path));
        header("Content-Disposition: attachment; filename={$path}");
        flush();

        readfile($this->nomeFile);
    }

    public function finalizaPdf() {
        $path = $this->file_path . $this->nomeFile;
        $this->fpdf->Output($path);
        $this->fpdf->Close();
    }

    protected function prepara_where($dados) {
        if (is_array($dados)) {
            $dados = array_filter($dados); //limpa campos vazios
//            if (empty($dados['status'])) {
//                $cond[] = "A.`status` = 1";
//            }
            foreach ($dados as $key => $value) {
                $cond[] = "A.`$key` = '$value'";
            }
            return (!empty($cond)) ? "WHERE " . implode(' AND ', $cond) : '';
        } else {
            return "WHERE " . $dados;
        }
    }

    public function incrementaQtdRecebida($id_item,$id_nfe) {
        // query que pega a soma do item com 
        $query = "SELECT (a.qtd_recebida + c.qtd_prod) AS soma,a.qCom AS solicitada FROM pedidos_itens AS a
                INNER JOIN pedidos AS b ON (a.id_pedido = b.id_pedido)
                INNER JOIN (
                    SELECT *,SUM(qCom) AS qtd_prod 
                    FROM nfe_itens 
                    WHERE id_nfe = '$id_nfe'
                    GROUP BY id_produto
                ) AS c ON (a.id_prod = c.id_produto) 
                WHERE a.id_item = $id_item";
        $item_pedido = mysql_fetch_assoc(mysql_query($query));

        if ($item_pedido['soma'] == $item_pedido['solicitada']) {
            $this->atualizaStatusEntrega($id_item, 2);
        } else {
            $this->atualizaStatusEntrega($id_item, 1);
        }

        $update = "UPDATE pedidos_itens SET qtd_recebida = '{$item_pedido['soma']}' WHERE id_item = $id_item";
        return mysql_query($update) or die('Erro no update da quantidade recebida' . mysql_error());
    }
    
    public function getProdutoByCProdCNPJ($cProd,$cnpj){
        $query = "SELECT a.*
                    FROM nfe_produtos AS a
                    INNER JOIN nfe_produtos_assoc AS b ON a.id_prod = b.id_produto
                    WHERE a.emit_cnpj = '$cnpj' AND b.cProd = '$cProd';\n";
        $result = mysql_query($query);
        return mysql_fetch_assoc($result);
    }

}
