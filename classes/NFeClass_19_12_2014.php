<?php

/*** Description of NFeClass ** @author F71Leonardo ***/

class NFe {

    public $xml;            // raiz do xml.
    public $nfe;            // nfe.
    public $det;            // detalhamento dos produtos. (alias)
    public $ide;            // informações de identificação. (alias)
    public $emit;           // informações do emitente. (alias)
    public $dest;           // informações do destinatario. (alias)
    public $entrega;        // (alias)
    public $total;          // (alias)
    public $transp;         // (alias)
    public $cobr;           // (alias)
    public $infAdic;        // (alias)
    public $upload_dir;     // pasta de upload
    public $savedFile;      // arquivo xml na nfe salvo

    public function __construct($filename = null) {
        $this->upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/intranet/compras/arquivo_xml/';
        if (!empty($filename)) {
            $this->load($filename);
        }
    }

    public function load($filename) {
        $this->xml = simplexml_load_file($filename);

//      $this->moveFile($filename);
        // setando os alias mais usados
        $this->det = $this->xml->NFe->infNFe->det;
        $this->ide = $this->xml->NFe->infNFe->ide;
        $this->emit = $this->xml->NFe->infNFe->emit;
        $this->dest = $this->xml->NFe->infNFe->dest;
        $this->entrega = $this->xml->NFe->infNFe->entrega;
        $this->total = $this->xml->NFe->infNFe->total;
        $this->transp = $this->xml->NFe->infNFe->transp;
        $this->cobr = $this->xml->NFe->infNFe->cobr;
        $this->infAdic = $this->xml->NFe->infNFe->infAdic;
    }

    // move a file da pasta temporaria para a pasta de uploads
    public function moveFile($filename) {
        $this->savedFile = $this->upload_dir . $this->xml->NFe->infNFe->attributes()->Id . '.xml';
        return (move_uploaded_file($filename, $this->saveFile)) ? TRUE : FALSE;
    }

    // remove o file da pasta de uploads
    public function removeFile($filename) {
        unlink($filename);
    }

    /*
     * Salva NFe
     * Inclui salvar item de produtos
     */

    public function salvarNFe($id_regiao, $id_projeto, $id_prestador, $array) {

        $Id = str_replace('NFe', '', $array['Id']);
        $cnpj = str_replace('/', '', str_replace('-', '', str_replace('.', '', $array['emit_CNPJ'])));

        // verifica se nota já foi cadastrda
        $resposta = $this->verificarNFe($Id, $cnpj, $array['nNF']);
        if ($resposta['status']) {
            return array('status' => FALSE, 'msg' => "Já foi cadastrado NFe com código = NFe$Id");
        }

        $query = "INSERT INTO nfe (id_regiao, id_projeto, Id, versao, cUF, cNF, natOp, 
            indPag, `mod`, serie, nNF, dEmi, dSaiEnt, hSaiEnt, tpNF, cMunFG, tpImp, tpEmis, 
            cDV, tpAmb, finNFe, procEmi, verProc, dhCont, xJust, emitente, IE, IEST, IM, 
            CNAE, CRT, destinatario, retirada, entrega, vBC, vICMS, vBCST, vST, vProd, vFrete, 
            vSeg, vDesc, vII, vIPI, vPIS, vCOFINS, vOutro, vNF, modFrete, nFat, infAdFisco)
                  VALUES ('{$id_regiao}','{$id_projeto}','{$Id}','{$array['versao']}',
                  '{$array['cUF']}','{$array['cNF']}','{$array['natOp']}','{$array['indPag']}','{$array['mod']}','{$array['serie']}',
                  '{$array['nNF']}','{$array['dEmi']}','{$array['dSaiEnt']}','{$array['hSaiEnt']}','{$array['tpNF']}','{$array['cMunFG']}',
                  '{$array['tpImp']}','{$array['tpEmis']}','{$array['cDV']}','{$array['tpAmb']}','{$array['finNFe']}','{$array['procEmi']}',
                  '{$array['verProc']}','{$array['dhCont']}','{$array['xJust']}','{$id_prestador}','{$array['IE']}','{$array['IEST']}',
                  '{$array['IM']}','{$array['CNAE']}','{$array['CRT']}','{$id_projeto}','{$array['retirada']}','{$array['entrega']}', 
                  '{$array['vBC']}','{$array['vICMS']}','{$array['vBCST']}','{$array['vST']}','{$array['vProd']}','{$array['vFrete']}',
                  '{$array['vSeg']}','{$array['vDesc']}','{$array['vII']}','{$array['vIPI']}','{$array['vPIS']}','{$array['vCOFINS']}',
                  '{$array['vOutro']}','{$array['vNF']}','{$array['modFrete']}','{$array['nFat']}','{$array['infAdFisco']}')";
        print_r($query);
        exit();
        $result = mysql_query($query) or die('Erro ao salvar NFe. Detalhes: ' . mysql_error());

        $id_nfe = mysql_insert_id();

        $result2 = $this->salvarItem($array['det'], $id_nfe, $id_prestador);

        $result3 = $this->salvarCobranca($array['dub'], $id_nfe, $id_prestador);
        
        return ($result) ? array('id_nfe' => mysql_insert_id(), 'status' => true) : array('msg' => 'Erro ao salvar NFe.', 'status' => false);
    }

    // salva cobrança
    public function salvarCobranca($array, $id_nfe, $id_prestador) {
//        // loop para pegar todos os itens de produtos
        foreach ($array['dup'] as $cobr) {
            $query = "INSERT INTO nfe_cobranca (id_nfe, id_emitente, nFat, nDup, dVenc, vDup)
            VALUES ('{$id_nfe}','{$id_prestador}','{$cobr['nFat']}','{$cobr['nDup']}','{$cobr['dVenc']}','{$cobr['vDup']}')";
            echo $query;
            $result = mysql_query($query);
            $retorno[] = ($result) ? array('id_cobranca' => mysql_insert_id(), 'status' => TRUE) : array('status' => FALSE);
        }
        return $retorno;
        
    }
    
    // salva item
    public function salvarItem($array, $id_nfe, $id_prestador) {
        // loop para pegar todos os itens de produtos
        foreach ($array as $det) {
            // verifica se produto já foi cadastrado
            $prod = $this->verificaProduto($det['cEAN'], $det['cProd'], $id_prestador);
            if ($prod['status']) { // se foi retorna o id
                $id_produto = $prod['id_produto'];
            } else { // senao, salva e retorna o id
                $result = $this->salvarProduto($det, $id_prestador);
                $retorno[] = $result;
                $id_produto = $result['id_produto'];
            }
            $query = "INSERT INTO nfe_itens (id_nfe, id_produto, nItem, CFOP, qCom, vProd, qTrib, vFrete, vSeg, vDesc, vOutros, indTot, nLote, qLote, dFab, dVal, vPMC, vUnCom)
            VALUES ('{$id_nfe}','{$id_produto}','{$det['nItem']}','{$det['CFOP']}','{$det['qCom']}','{$det['vProd']}',
             '{$det['qTrib']}','{$det['vFrete']}','{$det['vFrete']}','{$det['vSeg']}','{$det['vDesc']}','{$det['indTot']}',
             '{$det['nLote']}','{$det['qLote']}','{$det['dFab']}','{$det['dVal']}','{$det['vPMC']}','{$det['vUnCom']}')";

            $result = mysql_query($query);
            $retorno[] = ($result) ? array('id_item' => mysql_insert_id(), 'status' => TRUE) : array('status' => FALSE);
        }
        return $retorno;
    }

    // salvaproduto
    public function salvarProduto($dados, $id_prestador) {
        $teste =$this->verificaProduto($dados['cEAN'], $dados['cProd'], $id_prestador);
        if ($teste['status']) {
            return array('status' => FALSE, 'msg' => "\"{$dados['xProd']}\" - J&aacute; Cadastrado!");
        }
        $query_updt = "INSERT INTO nfe_produtos (cProd, id_prestador, cEAN, xProd, NCM, EXTIPI, uCom, vUnCom, cEANTrib, uTrib) 
            VALUES ('".utf8_decode($dados['cProd'])."','{$id_prestador}','{$dados['cEAN']}','" . utf8_decode($dados['xProd']) . "','{$dados['NCM']}','{$dados['EXTIPI']}','{$dados['uCom']}','{$dados['vUnCom']}','{$dados['cEANTrib']}','{$dados['uTrib']}')";
        // echo $query_updt;
        $result = mysql_query($query_updt);
        return ($result) ? array('status' => TRUE, 'id_produto' => mysql_insert_id()) : array('status' => FALSE, 'msg' => 'Erro ao salvar produto!');
    }

    // verifica se produto existe

    public function verificaProduto($cEAN = '', $cProd = '', $id_prestador = '', $debug = FALSE) {
        if (empty($cEAN) && empty($cProd) && empty($id_prestador)) {
            exit("Erro no método verificaProduto: parâmetros insuficientes. \ncEAN: $cEAN \ncProd: $cProd \nid_prestador: $id_prestador");
        }
        $cond[] = (empty($id_prestador)) ? '' : "id_prestador = '{$id_prestador}'";
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
            $query = "SELECT id_nfe FROM nfe WHERE Id = '$Id'";
        } else if (!empty($cnpj) && !empty($nNF)) {
            $cnpj = mascara_string("##.###.###/####-##", $cnpj);
            $query = "SELECT id_nfe FROM nfe AS A 
                INNER JOIN cad_fornecedor AS B ON A.emitente = B.id_fornecedor
                WHERE A.nNF = '$nNF' AND B.c_cnpj = '$cnpj'";
        } else {
            return array('status' => FALSE, 'msg' => 'Impossível consultar prestador. Parâmetros insufucientes.');
        }
//        echo $query;
        $teste = mysql_fetch_assoc(mysql_query($query));
        if (!empty($teste['id_nfe'])) {
            return array('status' => TRUE, 'id_nfe' => $teste['id_nfe']);
        } else {
            return array('status' => FALSE, 'msg' => "NFe já foi cadastrado.");
        }
    }

    public function consultaNFe($id_regiao, $id_projeto, $id_prestador = null, $id_nfe = null, $select_itens = FALSE) {
        $cond_prestador = (!empty($id_prestador)) ? " AND a.id_prestador = $id_prestador " : '';
        $cond_id_nfe = (!empty($id_nfe)) ? " AND a.id_nfe = $id_nfe" : '';
        /*
         * faltando no banco:
         * emit_cMun, emit_xPais, emit_cPais, emit_xMun
         */
        $query = "SELECT a.*, 
                b.c_razao AS emit_xNome, b.c_fantasia AS emit_xFant, b.c_cnpj AS emit_CNPJ,
                b.c_endereco AS emit_xLgr, b.c_numero AS emit_nro, b.c_complemento AS emit_xCpl, 
                b.c_bairro AS emit_xBairro, b.c_uf AS emit_UF, c_cep AS emit_CEP, c_tel AS emit_fone, 
                b.id_prestador, c_cod_cidade AS emit_cMun, c_im AS emit_IM, c_ie AS emit_IE,
                c.nome AS dest_xNome, c.cnpj AS dest_cnpj, c.endereco AS dest_endereco, c.complemento AS dest_compl, c.bairro AS dest_bairro,
                c.cidade AS dest_cidade, c.cep AS dest_cep
                FROM nfe AS a 
                INNER JOIN prestadorservico AS b ON (a.emitente = b.id_prestador)
                INNER JOIN projeto AS c ON (a.destinatario = c.id_projeto)
                WHERE a.id_regiao = $id_regiao AND a.id_projeto = $id_projeto $cond_prestador $cond_id_nfe AND a.status = 1 ORDER BY b.c_razao";
//        echo $query . '<br>';
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
//        echo $query;
        $result = mysql_query($query);

        while ($row = mysql_fetch_assoc($result)) {
            $return[$row['nItem']] = $row;
        }
        return $return;
    }

    public function consultaPrestador($id_projeto, $cnpj) {
        $cnpj = (strlen($cnpj) != 18) ? mascara_string('##.###.###/####-##', $cnpj) : $cnpj;
        $query = "SELECT * FROM prestadorservico WHERE id_projeto = '$id_projeto' AND c_cnpj = '$cnpj'";
//        echo $query;
        $result = mysql_query($query) or die('Erro ao consultar prestador. Detalhes: ' . mysql_error());
        $num_rows = mysql_num_rows($result);
        if ($num_rows > 1) {
            while ($row = mysql_fetch_array($result)) {
                $return[$row['id_prestador']] = $row;
            }
            return $return;
        } else if ($num_rows == 1) {
            return mysql_fetch_assoc($result);
        }
    }

    public function cancelar_NFe($id_nfe){
//        $cancelar = false;
        $qry = "UPDATE nfe SET status = '0' WHERE id_nfe = $id_nfe";
        return mysql_query($qry);        
    }
        public function consultarProduto($id_prod) {
        $query = "SELECT * FROM nfe_produtos WHERE id_prod = $id_prod AND status = 1";
        $result = mysql_query($query);
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
        $dest_cnpj = (empty($this->dest->CNPJ)) ? $this->emit->CPF : $this->dest->CNPJ;
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
            'dEmi' => $this->ide->dEmi,
            'dSaiEnt' => $this->ide->dSaiEnt,
            'hSaiEnt' => $this->ide->hSaiEnt,
            'tpNF' => $this->ide->tpNF,
            'cMunFG' => $this->ide->cMunFG,
            'tpImp' => $this->ide->tpImp,
            'tpEmis' => $this->ide->tpEmis,
            'cDV' => $this->ide->cDV,
            'tpAmb' => $this->ide->tpAmb,
            'finNFe' => $this->ide->finNFe,
            'procEmi' => $this->ide->procEmi,
            'verProc' => $this->ide->verProc,
            'dhCont' => $this->ide->dhCont,
            'xJust' => $this->ide->xJust,
            'emit_CNPJ' => $emit_cnpj, // pode ser cnpj ou cpf
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
        foreach ($this->fat as $fat) {
            $array['fat'][] = array(
                'nFat'  => $this->cobr->fat->nFat,
                'vOrig' => $this->cobr->fat->vOrig,
                'vLiq'  => $this->cobr->dup->vLiq,
            );            
        }
        foreach ($this->dup as $dup) {
            $array['dup'][] = array(
                'nDup'  => $this->cobr->dup->nDup,
                'dVenc' => $this->cobr->dup->dVenc,
                'vDup'  => $this->cobr->dup->vDup,
            );            
        }        
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
                'dFab' => $det->prod->med->dFab,
                'dVal' => $det->prod->med->dVal,
                'vPMC' => $det->prod->med->vPMC,
                'cProd' => $det->prod->cProd,
                'cEAN' => $det->prod->cEAN,
                'xProd' =>  $det->prod->xProd,
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
}
