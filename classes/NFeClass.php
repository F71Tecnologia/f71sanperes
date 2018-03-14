<?php

/* * * Description of NFeClass ** @author F71Leonardo ** */

class NFe {

    public $xml;        // raiz do xml.
    public $nfe;        // nfe.
    public $det;        // detalhamento dos produtos. (alias)
    public $ide;        // informações de identificação. (alias)
    public $emit;       // informações do emitente. (alias)
    public $dest;       // informações do destinatario. (alias)
    public $entrega;    // (alias)
    public $total;      // (alias)
    public $transp;     // (alias)
    public $infAdic;    // (alias)
    public $upload_dir; // pasta de upload
    public $savedFile;  // arquivo xml na nfe salvo
    public $debug = FALSE; // true para exibir querys
    public $pj_cnpj;
    public $arrayItensErrados = array();
    public $arrayItensNfeExtra = array();
    public $arrayItensPedFalta = array();
    public $arrayComparacao = array();
    private $objEstoqueEntrada;
    private $objEstoqueProduto;
    private $objContasAPagar;
    private $versao;

    public function __construct($filename = null) {
        $this->upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/intranet/compras/arquivo_xml/';
        if (!empty($filename)) {
            $this->load($filename);
        }
        $this->objEstoqueEntrada = new EstoqueEntrada();
        $this->objEstoqueProduto = new Estoque();
//        $this->pj_cnpj = new Fornecedor();
//        $this->objContasAPagar = new ContasapagarClass();
    }

    public function load($filename, $versao) {
        $this->xml = simplexml_load_file($filename);
        // setando os alias mais usados
        $this->det = $this->xml->NFe->infNFe->det; // versao 3.10
        $this->ide = $this->xml->NFe->infNFe->ide;
        $this->emit = $this->xml->NFe->infNFe->emit;
        $this->dest = $this->xml->NFe->infNFe->dest;
        $this->entrega = $this->xml->NFe->infNFe->entrega;
        $this->total = $this->xml->NFe->infNFe->total;
        $this->transp = $this->xml->NFe->infNFe->transp;
        $this->infAdic = $this->xml->NFe->infNFe->infAdic;
    }

    /** Salva NFe ** => Inclui itens do produtos ** => Inclui contas a pagar * */
    public function salvarNFe($array) {
        $array['Id'] = str_replace('NFe', '', $array['Id']); // remove caracteres do Id

        unset($array['det']); // remove os produtos do array principal

        $colunas = implode('`,`', array_keys($array));
        $valores = implode("','", array_values($array));

        $query = "INSERT INTO nfe (`$colunas`) VALUES ('$valores')";

        echo ($this->debug) ? "<br><br>" . $query . "<br><br>" : '';

        $result = mysql_query($query) or die('Erro ao salvar NFe. Detalhes: ' . mysql_error());

        if ($result) {
            return mysql_insert_id();
        } else {
            return FALSE;
        }
    }

    // salvaproduto
    public function salvarProduto($dados, $emitente, $emit_cnpj) {

        $teste = $this->verificaProduto($dados['cEAN'], $dados['cProd'], $emit_cnpj);
        if ($teste['status']) {
            return array('status' => FALSE, 'msg' => "\"{$dados['xProd']}\" ");
        }
        $bla = explode(' Lt:', $dados['xProd']);
        $bla = $bla[0];
        $query_updt = "INSERT INTO nfe_produtos (cProd, id_fornecedor, emit_cnpj, cEAN, xProd, NCM, EXTIPI, uCom, vUnCom, cEANTrib, uTrib, tipo)
                VALUES ('" . utf8_decode($dados['cProd']) . "','{$emitente}','{$emit_cnpj}','{$dados['cEAN']}','" . utf8_decode($bla) . "','{$dados['NCM']}','{$dados['EXTIPI']}','{$dados['uCom']}','{$dados['vUnCom']}','{$dados['cEANTrib']}','{$dados['uTrib']}','{$dados['tipo']}')";

        $result = mysql_query($query_updt);
        return ($result) ? array('status' => TRUE, 'id_produto' => mysql_insert_id()) : array('status' => FALSE, 'msg' => 'Erro ao salvar produto!');
    }

    // verifica se produto existe
    public function verificaProduto($cEAN = '', $cProd = '', $emit_cnpj = '', $debug = FALSE) {
        if (empty($cEAN) && empty($cProd) && empty($emit_cnpj)) {
            exit("Erro no método verificaProduto: parâmetros insuficientes. \ncEAN: $cEAN \ncProd: $cProd \nemit_cnpj: $emit_cnpj");
        }
        $cond[] = (empty($emit_cnpj)) ? '' : "emit_cnpj = '{$emit_cnpj}'";
        $cond[] = (empty($cProd)) ? '' : "cProd = '{$cProd}'";
        $cond[] = (empty($cEAN)) ? '' : "cEAN = '{$cEAN}'";
        $cond = implode(' AND ', array_filter($cond));

        $query_teste = "SELECT COUNT(id_prod) AS qtd, MAX(id_prod) AS id_prod FROM nfe_produtos WHERE ($cond) AND status = 1";

        $teste = mysql_fetch_assoc(mysql_query($query_teste));

        if ($debug) { // true para imprimir
            echo "<pre>\n$query_teste\nQtd: {$teste['qtd']}\nid_prod: {$teste['id_prod']}</pre>";
        }

        if ($teste['qtd'] > 0) {
            return array('status' => TRUE, 'msg' => "(EAN Nº {$cEAN} - Cod. do Produto Nº {$cProd}) já foi cadastrado!", 'id_produto' => $teste['id_prod']);
        } else {
            return array('status' => FALSE, 'msg' => "Produto (EAN Nº {$cEAN} - Cod. do Produto Nº {$cProd}) não foi cadastrado!");
        }
    }

    public function verificarNFe($Id = NULL, $cnpj = NULL, $nNF = NULL) {
        if (!empty($Id) && empty($cnpj) && empty($nNF)) {
            $Id = str_replace('NFe', '', $Id);
            $query = "SELECT COUNT(id_nfe) AS count FROM nfe WHERE Id = '$Id'";
        } else if (!empty($cnpj) && !empty($nNF)) {
            $query = "SELECT COUNT(id_nfe) AS count FROM nfe AS A 
                INNER JOIN contabil_fornecedor AS B ON A.emitente = B.id_fornecedor
                WHERE A.nNF = '$nNF' AND B.cnpj = '$cnpj'";
        } else {
            exit("<h4 style=\"color: red;\"><strong>ERRO:</strong> Impossível consultar NFe. Parâmetros insufucientes.</h4><p>\$id: $Id - \$cnpj: $cnpj - \$nNF: $nNF</p>");
        }
        $teste = mysql_fetch_assoc(mysql_query($query));

        if ($teste['count'] == 0) {
            return array('status' => TRUE, 'id_nfe' => $teste['id_nfe']);
        } else {
            return array('status' => FALSE, 'msg' => "Nota Fiscal já foi cadastrada.");
        }
    }

//    public function consultaNFe($id_regiao, $id_projeto, $id_prestador = null, $id_nfe = null, $select_itens = FALSE) {
    public function consultaNFe(array $dados, $select_itens = FALSE, $status = 1) {
        if (!isset($dados['status'])) {
            $dados['status'] = $status;
        }

        $condicoes = $this->prepara_where($dados);

        $query = "SELECT a.*,b.c_razao AS emit_xNome, b.c_fantasia AS emit_xFant, b.c_cnpj AS emit_CNPJ, b.endereco AS emit_xLgr, 
                        b.c_cep AS emit_CEP, b.id_prestador, c.nome AS dest_xNome, c.cnpj AS dest_cnpj, c.endereco AS dest_endereco, 
                        c.bairro AS dest_bairro, c.cidade AS dest_cidade, c.cep AS dest_cep 
                        FROM nfe AS a 
                        INNER JOIN prestadorservico AS b ON (a.emitente = b.id_prestador) 
                        INNER JOIN rhempresa AS c ON (a.id_projeto = c.id_projeto) 
                        $condicoes
                        ORDER BY b.c_razao";
        $result = mysql_query($query) or die('Erro ao consultar NFe. Detalhes: ' . mysql_error());

        while ($row = mysql_fetch_assoc($result)) {
            $return[$row['id_nfe']] = $row;
            if ($select_itens) {
                $return[$row['id_nfe']]['itens'] = $this->consultaItem($row['id_nfe']);
            }
        }

        return $return;
    }

    public function consultaItem($id_nfe = null, $id_item = null) {
        if (empty($id_nfe) && empty($id_item)) {
            exit('Erro no método consultaItem: Parâmetros insuficientes.');
        }
        $cond_nfe = (!empty($id_nfe)) ? " AND a.id_nfe = '$id_nfe' " : "";
        $cond_item = (!empty($id_item)) ? " AND a.id_item = '$id_item' " : "";

        $query = "SELECT * FROM nfe_itens AS a
            INNER JOIN nfe_produtos AS b ON (a.id_produto = b.id_prod)
            WHERE a.status = 1 $cond_item $cond_nfe ORDER BY nItem";
        $result = mysql_query($query);

        while ($row = mysql_fetch_assoc($result)) {
            $return[$row['nItem']] = $row;
        }
        return $return;
    }

    public function cancelar_NFe($id_nfe) {
//        $cancelar = false;
        $qry = "UPDATE nfe SET status = '0' WHERE id_nfe = $id_nfe";
        return mysql_query($qry);
    }

    public function consultarProduto($id_prod) {
        $query = "SELECT * FROM nfe_produtos WHERE id_prod = $id_prod AND status = 1";
        $result = mysql_query($query) or die(mysql_error());
        return mysql_fetch_assoc($result);
    }

    public function consultarProjeto($id_projeto) {
        $query = "SELECT * FROM projeto WHERE id_projeto = $id_projeto";
        $result = mysql_query($query) or die(mysql_error());
        return mysql_fetch_assoc($result);
    }

    // consulta as empresas que recebem a nota
    public function consultaRHEmpresa($dados) {
        if (!is_array($dados) && empty($dados)) {
            return array('status' => FALSE, 'msg' => 'Array vazio.');
        }

        foreach ($dados as $key => $value) {
            $arr_consulta[] = "$key = '$value'";
        }
        $str_consulta = implode(' AND ', $arr_consulta);

        $query = "SELECT * FROM rhempresa WHERE $str_consulta";
        $result = mysql_query($query);
        $num_rows = mysql_num_rows($result);

        $retorno = false;
        if ($num_rows > 1) {
            while ($row = mysql_fetch_array($result)) {
                $retorno[] = $row;
            }
        } else if ($num_rows == 1) {
            $retorno = mysql_fetch_assoc($result);
        }
        return $retorno;
    }

    public function nfe_xml_to_array() {
        $emit_cnpj = (empty($this->emit->CNPJ)) ? $this->emit->CPF : $this->emit->CNPJ;
        $dest_cnpj = (empty($this->dest->CNPJ)) ? $this->dest->CPF : $this->dest->CNPJ;
        $array = array(
            'Id' => $this->xml->NFe->infNFe->attributes()->Id,
            'versao' => $this->xml->NFe->infNFe->attributes()->versao,
            'cUF' => $this->ide->cUF,
            'cNF' => $this->ide->cNF,
            'natOp' => $this->ide->natOp,
            'indPag' => $this->ide->indPag,
            'mod' => $this->ide->mod,
            'serie' => $this->ide->serie,
            'nNF' => $this->ide->nNF,
            'dEmi' => str_replace('T', ' ', substr($this->ide->dhEmi, 0, 19)),
            'dSaiEnt' => $this->ide->dSaiEnt,
            'hSaiEnt' => $this->ide->hSaiEnt,
            'tpNF' => $this->ide->tpNF,
            'cMunFG' => $this->ide->cMunFG,
            'tpImp' => $this->ide->tpImp,
            'tpEmis' => $this->ide->tpEmis,
            'cDV' => $this->ide->cDV,
            'tpAmb' => $this->ide->tpAmb,
            'finNFe' => $this->ide->finNFe,
            'indFinal' => $this->ide->indFinal,
            'indPres' => $this->ide->indPres,
            'procEmi' => $this->ide->procEmi,
            'verProc' => $this->ide->verProc,
            'dhCont' => $this->ide->dhCont,
            'xJust' => $this->ide->xJust,
            'emitente' => $emit_cnpj, // pode ser cnpj ou cpf
            'IE' => $this->emit->IE,
            'IEST' => $this->IEST,
            'IM' => $this->ide->IM,
            'CNAE' => $this->emit->CNAE,
            'CRT' => $this->emit->CRT,
            'dest_CNPJ' => $dest_cnpj, // pode ser cnpj ou cpf
            'retirada' => $this->ide->retirada,
            'entrega' => $this->ide->entrega,
            'vBC' => $this->total->ICMSTot->vBC,
            'vICMS' => $this->total->ICMSTot->vICMS,
            'vBCST' => $this->total->ICMSTot->vBCST,
            'vST' => $this->total->ICMSTot->vST,
            'vProd' => $this->total->ICMSTot->vProd,
            'vFrete' => $this->total->ICMSTot->vFrete,
            'vSeg' => $this->total->ICMSTot->vSeg,
            'vDesc' => $this->total->ICMSTot->vDesc,
            'vII' => $this->total->ICMSTot->vII,
            'vIPI' => $this->total->ICMSTot->vIPI,
            'vPIS' => $this->total->ICMSTot->vPIS,
            'vCOFINS' => $this->total->ICMSTot->vCOFINS,
            'vOutro' => $this->total->ICMSTot->vOutro,
            'vNF' => $this->total->ICMSTot->vNF,
            'modFrete' => $this->transp->modFrete,
            'infAdFisco' => $this->infAdic->infAdFisco,
        );
        foreach ($this->det as $det) {
            $id = (int) $det->attributes()->nItem;
            $array['det'][$id] = array(
                'nItem' => $id,
                'CFOP' => $det->prod->CFOP,
                'qCom' => $det->prod->qCom,
                'vProd' => $det->prod->vProd,
                'qTrib' => $det->prod->qTrib,
                'vFrete' => $det->prod->vFrete,
                'vSeg' => $det->prod->vSeg,
                'vDesc' => $det->prod->vDesc,
                'indTot' => $det->prod->indTot,
                'nLote' => $det->prod->med->nLote,
                'qLote' => $det->prod->med->qLote,
                'dFab' => (!empty($det->prod->med->dFab)) ? $det->prod->med->dVal : '0000-00-00',
                'dVal' => (!empty($det->prod->med->dVal)) ? $det->prod->med->dVal : '0000-00-00',
                'vPMC' => $det->prod->med->vPMC,
                'cProd' => $det->prod->cProd,
                'cEAN' => $det->prod->cEAN,
                'xProd' => $det->prod->xProd,
                'NCM' => $det->prod->NCM,
                'EXTIPI' => $det->prod->EXTIPI,
                'uCom' => $det->prod->uCom,
                'vUnCom' => $det->prod->vUnCom,
                'cEANTrib' => $det->prod->cEANTrib,
                'uTrib' => $det->prod->uTrib,
            );
        }
        return $array;
    }

    /** agrupa produtos separados por lote ou data de validação  * */
    public function agrupaItens($arrayOriginal) {

        foreach ($arrayOriginal as $key => $value) {
            $teste["{$value['cProd']}"] = $value;
            $qLote["{$value['cProd']}"] += $value['qLote'];
            $qCom["{$value['cProd']}"] += $value['qCom'];
//            $vUnCom["{$value['cProd']}"] += $value['vUnCom'];
            $teste["{$value['cProd']}"]['qLote'] = $qLote["{$value['cProd']}"];
            $teste["{$value['cProd']}"]['qCom'] = $qCom["{$value['cProd']}"];
//            $teste["{$value['cProd']}"]['vUnCom'] = $vUnCom["{$value['cProd']}"];
        }
        foreach ($teste as $key => $value) {
            $array[$value['nItem']] = $value;
        }
        return $array;
    }

    /*
     * return TRUE:  se estiver validade
     *        FALSE: se não estiver validado
     *        NULL:  se os dados forem insuficientes para fazer a validação
     */

    public function validaNFE($itens_nfe, $itens_pedido) {
        if (empty($itens_nfe) || empty($itens_pedido)) {
            return NULL;
        }

        /** foreach para verificar se os valores e quantidades estão corretos * */
        foreach ($itens_pedido as $key_ped => $item_pedido) {
            foreach ($itens_nfe as $key_nfe => $item_nfe) {

                // coloca o item do pedido no array de comparacao
                $this->arrayComparacao[$key_ped]['pedido'] = $item_pedido;

                // se existir item na nota correspondente ao item do pedido
                if (in_array($item_nfe['cProd'], $item_pedido['cProd'])) {

                    // coloca o tiem da nota no array de comparacao
                    $this->arrayComparacao[$key_ped]['NFE'] = $item_nfe;
                    
                    unset($itens_pedido[$key_ped]); // remove do array (o que sobrar no fim é pq não veio na nota)
                    unset($itens_nfe[$key_nfe]); // remove do array (o que sobrar é que não foi pedido na nota)
                    
                    // verifica valores e quantidades
                    if ((float) $item_nfe['qCom'] <> (float) $item_pedido['qtd_faltando'] || (float) $item_nfe['vUnCom'] <> (float) $item_pedido['vUnCom']) {
                        // se diferente, coloca no array de itens com erro
                        $this->arrayItensErrados[] = array(
                            'NFE' => $item_nfe,
                            'pedido' => $item_pedido
                        );
                    }

                }
            }
        }
        
        $this->arrayItensNfeExtra = $itens_nfe;
        $this->arrayItensPedFalta = $itens_pedido;

        return (empty($this->arrayItensErrados) && empty($this->arrayItensNfeExtra) && empty($this->arrayItensPedFalta)) ? TRUE : FALSE;
    }

    protected function prepara_where($dados) {
        if (is_array($dados)) {
            $dados = array_filter($dados); //limpa campos vazios
            foreach ($dados as $key => $value) {
                $cond[] = "a.`$key` = '$value'";
            }
            return (!empty($cond)) ? "WHERE " . implode(' AND ', $cond) : '';
        } else {
            return "WHERE " . $dados;
        }
    }

}
