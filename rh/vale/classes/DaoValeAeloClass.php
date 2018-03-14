<?php

class DaoValeAeloClass extends DaoValeClass {

    public function __construct($rowDao) {
        parent::__construct($rowDao);
//        parent::__construct(array('nome' => 'AELO', 'id_tipo' => '2','cat'=>$catVale));
    }

    public function exportaUsuario(Array $relacao) {
        return array();
    }

    public function exportaPedido(Array $relacaoFuncionarios) {
        
        
        $arr_k = array_keys($relacaoFuncionarios['relacao']);
        $id_projeto = $relacaoFuncionarios['relacao'][$arr_k[0]]['id_projeto'];
        $id_va_pedido = $relacaoFuncionarios['relacao'][$arr_k[0]]['id_va_pedido'];
        $mes = $relacaoFuncionarios['relacao'][$arr_k[0]]['mes'];
        $ano = $relacaoFuncionarios['relacao'][$arr_k[0]]['ano'];
        
        unset($arr_k);
        
        
        
        $tipoLocal = 'PT';
        $local = '0001';
        $header = ';NOME DO USUÁRIO;'
                . 'CPF;'
                . 'DATA DE NASCIMENTO;'
                . 'CÓDIGO DE SEXO;'
                . 'VALOR;'
                . 'TIPO DE LOCAL ENTREGA;'
                . 'LOCAL DE ENTREGA;'
                . 'MATRÍCULA;' . "\n";
        
//        var_dump($header);
//        exit();
        
        $body = '';
        
            
        foreach ($relacaoFuncionarios['relacao'] as $funcionario) {
            
            
            $nomeFuncionario = self::campoTexto($funcionario['nome_funcionario'],40,' '); // até 40 caracteres, sem acentos, sem dois espaços em brancos consecutivos 
            
            
            $cpfLimpo = $funcionario['cpf_limpo']; // cpf só números
            
            $dataNascimento = $funcionario['data_nascimento']; // data de nascimento dd/mm/aaaa
            
            $sexo = $funcionario['sexo']; // sexo M/F
            
            $valorRecarga = self::formataValorRecarga($funcionario['valor_recarga']); // valor com vírgulas para separar os centavos 1000,20
            
//            exit($valorRecarga);
            
            $body .= "%;" . $nomeFuncionario . ";"
                    . "$cpfLimpo;"
                    . "$dataNascimento;"
                    . "$sexo;"
                    . "$valorRecarga;"
                    . "$tipoLocal;"
                    . "$local;"
                    . ";%\n";
        }  
        
        
        
        $folder = 'arquivos/'.parent::$id_tipo.'/';
        
        $name = 'PROJ.'.$id_projeto.'_PED.'.$id_va_pedido.'_COMP.'.$ano.$mes.'_USER.'.self::$id_user.'_PROC.'.date('Ymd.hi').'.csv';
        
        $path = $folder . $name;
        
//        echo $path."\n";
//        echo $body."\n";
//        exit();
        
        $file = fopen($path, 'w+');
        fwrite($file, $header . $body);
        fclose($file);
        
        return array('tipo'=>parent::$id_tipo, 'download'=> $name, 'name_file' => $name, 'dados' => $header . $body);
    }
    static function campoTexto($str, $length, $completa=" ", $direcao = STR_PAD_RIGHT){
        
        
        $arr = array(
            'A' => '/&Agrave;|&Aacute;|&Acirc;|&Atilde;|&Auml;|&Aring;/',
            'a' => '/&agrave;|&aacute;|&acirc;|&atilde;|&auml;|&aring;/',
            'C' => '/&Ccedil;/',
            'c' => '/&ccedil;/',
            'E' => '/&Egrave;|&Eacute;|&Ecirc;|&Euml;/',
            'e' => '/&egrave;|&eacute;|&ecirc;|&euml;/',
            'I' => '/&Igrave;|&Iacute;|&Icirc;|&Iuml;/',
            'i' => '/&igrave;|&iacute;|&icirc;|&iuml;/',
            'N' => '/&Ntilde;/',
            'n' => '/&ntilde;/',
            'O' => '/&Ograve;|&Oacute;|&Ocirc;|&Otilde;|&Ouml;/',
            'o' => '/&ograve;|&oacute;|&ocirc;|&otilde;|&ouml;/',
            'U' => '/&Ugrave;|&Uacute;|&Ucirc;|&Uuml;/',
            'u' => '/&ugrave;|&uacute;|&ucirc;|&uuml;/',
            'Y' => '/&Yacute;/',
            'y' => '/&yacute;|&yuml;/',
            'a.' => '/&ordf;/',
            'o.' => '/&ordm;/',
            ' ' => "/(  +)/i",
            '' => "/[^a-zA-Z0-9  +]+/i" 
        );
        return str_pad(substr(preg_replace($arr, array_keys($arr), htmlentities($str, ENT_NOQUOTES, "iso-8859-1")), 0, $length), $length, $completa , $direcao);
        
    }
    
    public static function formataNomeFuncionario($nomeFuncionario){
          $arr = array(
                'A' => '/&Agrave;|&Aacute;|&Acirc;|&Atilde;|&Auml;|&Aring;/',
                'a' => '/&agrave;|&aacute;|&acirc;|&atilde;|&auml;|&aring;/',
                'C' => '/&Ccedil;/',
                'c' => '/&ccedil;/',
                'E' => '/&Egrave;|&Eacute;|&Ecirc;|&Euml;/',
                'e' => '/&egrave;|&eacute;|&ecirc;|&euml;/',
                'I' => '/&Igrave;|&Iacute;|&Icirc;|&Iuml;/',
                'i' => '/&igrave;|&iacute;|&icirc;|&iuml;/',
                'N' => '/&Ntilde;/',
                'n' => '/&ntilde;/',
                'O' => '/&Ograve;|&Oacute;|&Ocirc;|&Otilde;|&Ouml;/',
                'o' => '/&ograve;|&oacute;|&ocirc;|&otilde;|&ouml;/',
                'U' => '/&Ugrave;|&Uacute;|&Ucirc;|&Uuml;/',
                'u' => '/&ugrave;|&uacute;|&ucirc;|&uuml;/',
                'Y' => '/&Yacute;/',
                'y' => '/&yacute;|&yuml;/',
                'a.' => '/&ordf;/',
                'o.' => '/&ordm;/',
                ' ' => "/(  +)/i"
            );
            return str_pad(substr(preg_replace($arr, array_keys($arr), htmlentities($nomeFuncionario, ENT_NOQUOTES, "iso-8859-1")), 0, 40), 40, ' ', STR_PAD_RIGHT);
    }
    public static function formataValorRecarga($valorRecarga){
//        exit(number_format($valorRecarga,2,',',''));
        return number_format($valorRecarga,2,',','');
    }
}
