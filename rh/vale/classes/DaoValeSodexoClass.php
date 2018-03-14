<?php

class DaoValeSodexoClass extends DaoValeClass {

    private $cod_empresa;
    private $arr_campos;

    public function __construct($rowDao) {

        parent::__construct($rowDao);
//        parent::__construct(array('nome' => 'SODEXO', 'id_tipo' => '1', 'cat' => $catVale));
        $this->cod_empresa = '1319663'; // sem os zeros
    }

    public function exportaUsuario(Array $relacao) {
        return array();
    }

    public function exportaPedido(Array $dados) {
        $name = 'PED.'.$dados['info']['id_va_pedido'].'_PROJ.'.$dados['info']['id_projeto'].'_COMP.'.$dados['info']['ano'].'.'.$dados['info']['mes'].'_USER.'.parent::$id_user.'_PROC.'.date('YmdHis').'.xls';
//        exit($name);

//        
//        $name = 'teste_2.csv';
//        $name = 'PLANSIPV5_2.xls';
        return $this->criaXls($name, $dados);
    }
    public function criaXls($name, $dados=array()) {

        $folder = 'arquivos/' . parent::$id_tipo . '/';
        
        
        $fileType = 'Excel5';
        $fileName = 'arquivos/PLANSIPV5_SODEXO.xls';
//        $fileNameNOVO = 'arquivos/PLANSIPV5_NOVO.xls';

        $objReader = PHPExcel_IOFactory::createReader($fileType);
        $objPHPExcel = $objReader->load($fileName);
        
        
        $arr_campos = array(
            'COD.CLIENTE',
            'MATRÍCULA',
            'NOME DO BENEFICIÁRIO',
            'NOME PARA GRAVAÇÃO NO CARTÃO',
            'CPF',
            'DATA DE NASC.',
            'NÚMERO DO RG',
            'DÍGITO DO RG',
            'UF DE EMISSÃO DO RG',
            'DATA EMISSÃO DO RG',
            'ORGÃO EMISSOR DO RG',
            'NOME DA MÃE',
            'SEXO',
            'ESTADO CIVIL',
            'EMAIL',
            'DDD - TELEFONE',
            'NÚMERO DO TELEFONE',
            'CARGO',
            'NÚMERO DO SIC USO EXCLUSIVO DE CURITIBA-PR',
            'FAIXA SALARIAL DO FUNCIONÁRIO',
            'TIPO PEDIDO',
            'PRODUTO',
            'QTD BLOCOS',
            'QTD FOLHAS',
            'VALOR FACIAL / CREDITO',
            'DATA CRÉDITO',
            'DATA ENTREGA',
            'MÊS REFERÊNCIA',
            'CÓD. DEPART.',
            'NOME DEPART..',
            'RESPONSÁVEL Pelo Depat.',
            'Tipo de Logradouro - rua, av, estr, rod',
            'Descrição do logradouro - nome da rua',
            'Número',
            'Complemento',
            'Bairro',
            'Cidade',
            'UF',
            'CEP',
            'QTD.DIAS UTEIS',
            'COD. OPERADORA 1',
            'COD. LINHA 1',
            'QTD. PASSES POR DIA / COTAS (SPTrans)',
            'VALOR DO BILHETE 1',
            'NÚMERO CARTÃO 1',
            'COD. OPERADORA 2',
            'COD. LINHA 2',
            'QTD. PASSES POR DIA / COTAS',
            'VALOR DO BILHETE 2',
            'NÚMERO CARTÃO 2',
            'COD. OPERADORA 3',
            'COD. LINHA 3',
            'COD. QTD.PASSES POR DIA / COTAS (SPTrans)',
            'VALOR DO BILHETE 3',
            'NÚMERO DO CARTÃO 3',
            'COD. OPERADORA 4',
            'COD. LINHA 4',
            'COD. QTD.PASSES POR DIA / COTAS (SPTrans)',
            'VALOR DO BILHETE 4',
            'NÚMERO DO CARTÃO 4',
            'COD. OPERADORA 5',
            'COD. LINHA 5',
            'COD. QTD.PASSES POR DIA / COTAS (SPTrans)',
            'VALOR DO BILHETE 5',
            'NÚMERO DO CARTÃO 5',
            'Tipo de Logradouro - rua, av, estr, rod',
            'Descrição do logradouro - nome da rua, av, trav.',
            'Número',
            'Complemento',
            'BAIRRO',
            'CIDADE',
            'UF',
            'CEP'
        );
        
        foreach ($arr_campos as $k=>$v){
            // $pColumn = 0, $pRow = 1, $pValue = null, $returnCell = false
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($k, 3, utf8_encode($v));
        }
        
        $tipo_pedido = '001'; // 001-  Pedido normal, 023 - Pedido de 1 via cartão sem crédito
        $produto = '2001'; // refeição
        $data_credito = $dados['data_credito'];
        $data_entrega = $dados['data_entrega'];
        $mes_referencia = $dados['info']['mes'].'/'.$dados['info']['ano']; // seguindo o modelo mes/ano do arquivo baixado no site
        $cod_dep = 'RH';
        $nome_dep = 'Recursos Humanos';
        $responsavel_dep = 'Shirlei';
        
        $linha = 4;
        
        foreach($dados['relacao'] as $clt_row){
            
            // $pColumn = 0, $pRow = 1, $pValue = null, $returnCell = false
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1, $linha, 6);  // f texto // matricula da dani é 6
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2, $linha, $clt_row['nome_funcionario']); // f especial codigo postal
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3, $linha, $clt_row['nome_funcionario']); // f geral
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4, $linha, $clt_row['cpf_limpo']); // especial numer pesel
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5, $linha, $clt_row['data_nascimento']); // data 14/03/2001
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6, $linha, ''); // $clt_row['rg_limpo'] geral
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7, $linha, ''); // DIGITO RG CONSERTAR **** geral
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8, $linha, ''); // $clt_row['uf_rg'] texto
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9, $linha, ''); // $clt_row['emissao_rg'] texto
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10, $linha,  ''); // $clt_row['orgao_rg'] data ?????????????????
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11, $linha,  $clt_row['mae']); // geral
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(12, $linha,  $clt_row['sexo']); // data ?????????????????
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(13, $linha,  $clt_row['civil']); // data ?????????????????
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(14, $linha,  $clt_row['email']); // geral
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(15, $linha,  ' '); // geral DDD
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(16, $linha,  $clt_row['tel_fixo']); // geral
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(17, $linha,  $clt_row['cargo']); // data
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(20, $linha,  $tipo_pedido);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(21, $linha,  $produto);

            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(24, $linha,  $clt_row['valor_recarga']);         
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(25, $linha,  $data_credito);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(26, $linha,  $data_entrega);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(27, $linha,  $mes_referencia);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(28, $linha,  $cod_dep);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(29, $linha,  $nome_dep);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(30, $linha,  $responsavel_dep);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(31, $linha,  $clt_row['tp_logradouro_dp']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(32, $linha,  $clt_row['logradouro_dp']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(33, $linha,  $clt_row['numero_dp']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(34, $linha,  $clt_row['complemento_dp']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(35, $linha,  $clt_row['bairro_dp']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(36, $linha,  $clt_row['cidade_dp']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(37, $linha,  $clt_row['uf_dp']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(38, $linha,  $clt_row['cep_dp']);
            $linha++;
        }

        

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $fileType);
        $objWriter->save($folder.$name);
        
        return array('tipo' => parent::$id_tipo, 'download' => $name, 'name_file' => 'PLANSIPV5_'.$this->cod_empresa.'.xls', 'dados' => $dados);
        
        exit();
    }

    public function criaXls_2($name, $dados=array()) {
        //        $workbook = new Application_Control_Report_Excel($folder.$name);
        $folder = 'arquivos/'.self::$id_tipo.'/';
        
//        exit($folder.$name);
        
        $workbook = new Spreadsheet_Excel_Writer($folder.$name);
  
        
//        $format =& $workbook->addFormat();
//        $format->setUnLocked();
        
        $worksheet =& $workbook->addWorksheet('My first worksheet');
//        $worksheet =& $workbook->addWorksheet('PEDIDO SODEXO');
        
        
//        $format_bold->setBold();
        
//        $worksheet->activate();
//        $worksheet->select();
//        $res_arr = $workbook->worksheets();
//        
//        
//        // integer $row , integer $col
//        $worksheet->write(0, 0, 'EMPRESA');
//        // $first_row, $first_col, $last_row, $last_col
//        $worksheet->mergeCells(0, 0, 1, 0);
//        
//        // integer $row , integer $col
//        $worksheet->write(0, 1, 'DADOS DO BENEFÍCIARIO');
//        // $first_row, $first_col, $last_row, $last_col
//        $worksheet->mergeCells(0, 1, 1, 19);
//        
//        // integer $row , integer $col
//        $worksheet->write(0, 20, 'BENEFÍCIOS'); // geral
//        // $first_row, $first_col, $last_row, $last_col
//        $worksheet->mergeCells(0, 20, 1, 27);
//        
//        // integer $row , integer $col
//        $worksheet->write(0, 28, 'DADOS PARA ENTREGA DO BENEFÍCIO');
//        // $first_row, $first_col, $last_row, $last_col
//        $worksheet->mergeCells(0, 28, 1, 38);
////        
//        // integer $row , integer $col
//        $worksheet->write(0, 39, 'LINHAS TRANSPORTES');
//        // $first_row, $first_col, $last_row, $last_col
//        $worksheet->mergeCells(0, 39, 1, 64);
//////        
//        // integer $row , integer $col
//        $worksheet->write(0, 65, 'DADOS ENDEREÇO RESIDENCIAL DO FUNCIONÁRIO');
//        // $first_row, $first_col, $last_row, $last_col
//        $worksheet->mergeCells(0, 65, 1, 72);
//        
//        
//        
//        $arr_campos = array(
//            'COD.CLIENTE',
//            'MATRÍCULA',
//            'NOME DO BENEFICIÁRIO',
//            'NOME PARA GRAVAÇÃO NO CARTÃO',
//            'CPF',
//            'DATA DE NASC.',
//            'NÚMERO DO RG',
//            'DÍGITO DO RG',
//            'UF DE EMISSÃO DO RG',
//            'DATA EMISSÃO DO RG',
//            'ORGÃO EMISSOR DO RG',
//            'NOME DA MÃE',
//            'SEXO',
//            'ESTADO CIVIL',
//            'EMAIL',
//            'DDD - TELEFONE',
//            'NÚMERO DO TELEFONE',
//            'CARGO',
//            'NÚMERO DO SIC USO EXCLUSIVO DE CURITIBA-PR',
//            'FAIXA SALARIAL DO FUNCIONÁRIO',
//            'TIPO PEDIDO',
//            'PRODUTO',
//            'QTD BLOCOS',
//            'QTD FOLHAS',
//            'VALOR FACIAL / CREDITO',
//            'DATA CRÉDITO',
//            'DATA ENTREGA',
//            'MÊS REFERÊNCIA',
//            'CÓD. DEPART.',
//            'NOME DEPART..',
//            'RESPONSÁVEL Pelo Depat.',
//            'Tipo de Logradouro - rua, av, estr, rod',
//            'Descrição do logradouro - nome da rua',
//            'Número',
//            'Complemento',
//            'Bairro',
//            'Cidade',
//            'UF',
//            'CEP',
//            'QTD.DIAS UTEIS',
//            'COD. OPERADORA 1',
//            'COD. LINHA 1',
//            'QTD. PASSES POR DIA / COTAS (SPTrans)',
//            'VALOR DO BILHETE 1',
//            'NÚMERO CARTÃO 1',
//            'COD. OPERADORA 2',
//            'COD. LINHA 2',
//            'QTD. PASSES POR DIA / COTAS',
//            'VALOR DO BILHETE 2',
//            'NÚMERO CARTÃO 2',
//            'COD. OPERADORA 3',
//            'COD. LINHA 3',
//            'COD. QTD.PASSES POR DIA / COTAS (SPTrans)',
//            'VALOR DO BILHETE 3',
//            'NÚMERO DO CARTÃO 3',
//            'COD. OPERADORA 4',
//            'COD. LINHA 4',
//            'COD. QTD.PASSES POR DIA / COTAS (SPTrans)',
//            'VALOR DO BILHETE 4',
//            'NÚMERO DO CARTÃO 4',
//            'COD. OPERADORA 5',
//            'COD. LINHA 5',
//            'COD. QTD.PASSES POR DIA / COTAS (SPTrans)',
//            'VALOR DO BILHETE 5',
//            'NÚMERO DO CARTÃO 5',
//            'Tipo de Logradouro - rua, av, estr, rod',
//            'Descrição do logradouro - nome da rua, av, trav.',
//            'Número',
//            'Complemento',
//            'BAIRRO',
//            'CIDADE',
//            'UF',
//            'CEP'
//        );
//        
//        foreach ($arr_campos as $k=>$v){
//            // integer $row , integer $col
////            print_r($v);
//            $worksheet->write(2, $k, $v);
//        }
//        exit();
        
        $tipo_pedido = '001'; // 001-  Pedido normal, 023 - Pedido de 1 via cartão sem crédito
        $produto = '2001'; // refeição
        $data_credito = $dados['data_credito'];
        $data_entrega = $dados['data_entrega'];
        $mes_referencia = $dados['info']['mes'].'/'.$dados['info']['ano']; // seguindo o modelo mes/ano do arquivo baixado no site
        $cod_dep = 'RH';
        $nome_dep = 'Recursos Humanos';
        $responsavel_dep = 'Shirlei';
        
       
        $cont = 3;
        foreach($dados['relacao'] as $clt_row){
            
            // integer $row , integer $col
            $worksheet->write($cont, 1, $clt_row['matricula']);  // f texto // matricula da dani é 6
            $worksheet->write($cont, 2, $clt_row['nome_funcionario']); // f especial codigo postal
            $worksheet->write($cont, 3, $clt_row['nome_funcionario']); // f geral
            $worksheet->write($cont, 4, $clt_row['cpf_limpo']); // especial numer pesel
            $worksheet->write($cont, 5, $clt_row['data_nascimento']); // data 14/03/2001
            $worksheet->write($cont, 6, $clt_row['rg_limpo']); // geral
            $worksheet->write($cont, 6, ''); // DIGITO RG  geral
            $worksheet->write($cont, 7, ''); // texto $clt_row['uf_rg']
            $worksheet->write($cont, 9, ''); // texto $clt_row['emissao_rg']
            $worksheet->write($cont, 10, ''); // $clt_row['orgao_rg']
            $worksheet->write($cont, 11, $clt_row['mae']); // geral
            $worksheet->write($cont, 12, $clt_row['sexo']); // data ?????????????????
            $worksheet->write($cont, 13, $clt_row['civil']); // data ?????????????????
            $worksheet->write($cont, 14, $clt_row['email']); // geral
            $worksheet->write($cont, 15, $clt_row['tel_fixo']); // geral
            $worksheet->write($cont, 16, $clt_row['tel_fixo']); // geral
            $worksheet->write($cont, 17, $clt_row['cargo']); // data
            $worksheet->write($cont, 20, $tipo_pedido);
            $worksheet->write($cont, 21, $produto);
            
            $worksheet->write($cont, 24, $clt_row['valor_recarga']);         
            $worksheet->write($cont, 25, $data_credito);
            $worksheet->write($cont, 26, $data_entrega);
            $worksheet->write($cont, 27, $mes_referencia);
            $worksheet->write($cont, 28, $cod_dep);
            $worksheet->write($cont, 29, $nome_dep);
            $worksheet->write($cont, 30, $responsavel_dep);
            $worksheet->write($cont, 31, $clt_row['tp_logradouro_dp']);
            $worksheet->write($cont, 32, $clt_row['logradouro_dp']);
            $worksheet->write($cont, 33, $clt_row['numero_dp']);
            $worksheet->write($cont, 34, $clt_row['complemento_dp']);
            $worksheet->write($cont, 35, $clt_row['bairro_dp']);
            $worksheet->write($cont, 36, $clt_row['cidade_dp']);
            $worksheet->write($cont, 37, $clt_row['uf_dp']);
            $worksheet->write($cont, 38, $clt_row['cep_dp']);
            
            $cont++;     
            
            //            print_r($clt_row);
        }
//        exit();
        
        // We still need to explicitly close the workbook
        $workbook->close();
        

        return array('tipo' => parent::$id_tipo, 'download' => $name, 'name_file' => 'PLANSIPV5_'.$this->cod_empresa.'.xls', 'dados' => $dados);
    }
}
