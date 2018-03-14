<?php

function setCheckedIfEqual ($var, $val) {
    
    $return = ($var == $val) ? 'checked' : null;
    
    return $return;
    
}

/**
 * Funão para retornar apenas um registro na tabela, ela executa a funcao montaQuery e modifica o resultado
 * @param string $tabela
 * @param array,string $campos
 * @param array,string $condicao
 * @param array,string $order
 * @param string $limit
 * @param string $return "array" para retornar um array, !="array" para retornar o result mysql
 * @param string $debug
 * @param array,string $groupBy array ou string com o(s) nome(s) do campo que será agrupado
 * @return array,resultMySql
 */
function montaQuery($tabela, $campos = "*", $condicao = null, $order = null, $limit = null, $return = "array", $debug = false, $groupBy = null) {
    $_where = "";
    $_order = "";
    $_limit = "";
    $_groupBy = "";
    if (is_array($campos)) {
        $_camp = implode(",", $campos);
    } else {
        $_camp = $campos;
    }

    if ($condicao !== null) {
        $_where = " WHERE ";
        if (is_array($condicao)) {
            foreach ($condicao as $k => $val) {
                $_where .= " " . $k . " = '" . $val . "' AND";
            }
            $_where = substr($_where, 0, -3);
        } else {
            $_where .= $condicao;
        }
    }

    if ($order !== null) {
        $_order = " ORDER BY ";
        if (is_array($order)) {
            foreach ($order as $k => $val) {
                $_order .= $k . " " . $val . ",";
            }
            $_order = substr($_order, 0, -1);
        } else {
            $_order .= $order;
        }
    }

    if ($limit !== null) {
        $_limit = " LIMIT {$limit}";
    }
    
    if($groupBy !== null) {
        if(is_array($groupBy)){
            $_groupBy = " GROUP BY ".implode(",",$groupBy)."";
        }else{
            $_groupBy = " GROUP BY {$groupBy}";
        }
    }

    $query = "SELECT {$_camp} FROM {$tabela}{$_where}{$_groupBy}{$_order}{$_limit}";

    $result = mysql_query($query);

    if ($debug) {
        echo $query;
    }

    if ($return == "array") {
        $return = array();
        $count = 1;

        if (mysql_num_rows($result) > 0) {
            while ($row = mysql_fetch_assoc($result)) {
                foreach ($row as $k => $val) {
                    $return[$count][$k] = $val;
                }
                $count++;
            }
        }
    } else {
        $return = $result;
    }

    return $return;
}

/**
 * Funão para retornar apenas um registro na tabela, ela executa a funcao montaQuery e modifica o resultado
 * @param string $tabela
 * @param array,string $campos
 * @param array,string $condicao
 * @param array,string $order
 * @param string $limit
 * @param string $debug
 * @return array
 */
function montaQueryFirst($tabela, $campos = "*", $condicao = null, $order = null, $limit = null, $debug = false) {
    $rs = montaQuery($tabela, $campos, $condicao, $order, $limit, "array", $debug);
    return current($rs);
}

function execQuery($sql, $return = "array") {
    $result = mysql_query($sql);

    if ($return == "array") {
        $return = array();
        $count = 1;

        if (mysql_num_rows($result) > 0) {
            while ($row = mysql_fetch_assoc($result)) {
                foreach ($row as $k => $val) {
                    $return[$count][$k] = $val;
                }
                $count++;
            }
        }
    } else {
        $return = $result;
    }

    return $return;
}

/**
 * 
 * @param type $tabela
 * @param type $campos
 * @param type $valores
 * @param type $debug
 * @return type
 */
function sqlInsert($tabela, $campos, $valores, $debug = false) {
    $palavrasReservadas = array("NOW()"); //depois montar uma forma de retirar as aspas simples das palavras reservados do mysql
    $_campos = "";
    $_valores = "";
    $qr = "INSERT INTO {$tabela} ";

    if (is_array($campos)) {
        $_campos = implode(",", $campos);
    } else {
        $_campos = $campos;
    }

    if (is_array($valores)) {
        //VERIFICANDO SE É UMA MATRIZ
        if (count($valores) == count($valores, COUNT_RECURSIVE)) {
            $_valores = "('" . implode("','", $valores) . "')";
        } else {
            foreach ($valores as $val) {
                $_valores .= "('" . implode("','", $val) . "'),";
            }
            $_valores = substr($_valores, 0, -1);
        }
    } else {
        $_valores = "(" . $valores . ")";
    }
    $_valores = preg_replace("/\'null\'/is","null",$_valores);

    $qr .= "($_campos) VALUES $_valores";
    if ($debug) {
        echo $qr . "<br/>";
    } else {
        mysql_query($qr) or die("Erro na query:<br/>" . $qr . "<br/><br/>Descrição:<br/>" . mysql_error());
    }
    return mysql_insert_id();
}

/**
 * 
 * @param type $tabela
 * @param type $campos
 * @param type $condicao
 * @param type $debug
 * @return type
 */
function sqlUpdate($tabela, $campos, $condicao, $debug = false) {
    $_campos = "";
    $_condicao = "";
    $qr = "UPDATE {$tabela} SET";

    if (is_array($campos)) {
        foreach ($campos as $k => $val) {
            $_campos .= " {$k}='{$val}',";
        }
        $_campos = substr($_campos, 0, -1);
    } else {
        $_campos = $campos;
    }

    if (is_array($condicao)) {
        foreach ($condicao as $k => $val) {
            $_condicao .= " {$k}={$val} AND ";
        }
        $_condicao = substr($_condicao, 0, -4);
    } else {
        $_condicao = $condicao;
    }

    $qr .= $_campos . " WHERE " . $_condicao;

    if ($debug) {
        echo $qr . "<br/>";
    } else {
        return mysql_query($qr) or die("Erro na query:<br/>" . $qr . "<br/><br/>Descrição:<br/>" . mysql_error());
    }
}

function montaDataToSelect() {
    
}

function montaSelect($options, $value, $atributos) {
    $html = "<select ";

    if (is_array($atributos)) {
        foreach ($atributos as $key => $val) {
            $html .= $key . "=\"" . $val . "\" ";
        }
    } else {
        $html .= $atributos;
    }
    $html .= ">";


    if (is_array($options)) {
        foreach ($options as $key => $val) {
            if ((!empty($value) || $value == 0) && $value == $key) {
                $selected = " selected=\"selected\"";
            } else {
                $selected = "";
            }
            $html .= "<option value=\"" . $key . "\"$selected>" . $val . "</option>";
        }
    }
    $html .= "</select>";
    return $html;
}

/**
 * Retorna o dia da semana 1 (para Segunda) até 7 (para Domingo) date('N')
 * @param int $id (1-7)
 * @return string
 */
function diasSemanaArray($id) {
    //date(N) 1 (para Segunda) até 7 (para Domingo)
    $id = (int) $id;
    $diass = array("0" => "Selecione", "1" => "segunda-feira", "2" => "terça-feira", "3" => "quarta-feira", "4" => "quinta-feira", "5" => "sexta-feira", "6" => "sábado",
        "7" => "domingo");
    if (!empty($id))
        return $diass[$id];
    else
        return $diass;
}

function mesesArray($id=null,$key='-1',$todos=null) {
    $id = (int) $id;
    
    if($todos != null){
        $opcao = $todos;
    }else{
        $opcao = "Selecione o mês";
    }
    
    $meses = array($key => $opcao, "1" => "Janeiro", "2" => "Fevereiro", "3" => "Março", "4" => "Abril", "5" => "Maio", "6" => "Junho",
        "7" => "Julho", "8" => "Agosto", "9" => "Setembro", "10" => "Outubro", "11" => "Novembro", "12" => "Dezembro");
    if (!empty($id))
        return $meses[$id];
    else
        return $meses;
}

function anosArray($inicio=null, $fim=null, $default = null) {
    if ($inicio == null)
        $inicio = date("Y") - 4;
    if ($fim == null)
        $fim = date("Y") + 1;
    $anos = array();

    if ($default !== null) {
        $anos = $default;
    }

    for ($i = $inicio; $i <= $fim; $i++) {
        $anos[$i] = $i;
    }
    return $anos;
}
function SanperesAnosArray($inicio=null, $fim=null, $default = null) {
    if ($inicio == null)
        $inicio = date("Y") - 5;
    if ($fim == null)
        $fim = date("Y") + 5;
    $anos = array();

    if ($default !== null) {
        $anos = $default;
    }

    for ($i = $inicio; $i <= $fim; $i++) {
        $anos[$i] = $i;
    }
    return $anos;
}

function converteData($data, $formato = null) {
    if ($formato == null)
        $formato = "Y-m-d";
    return date($formato, strtotime(str_replace("/", "-", $data)));
}

function convertImage($imagem, $tipo) {
    if ($tipo == "gif") {
        $img = imagecreatefromgif($imagem . "." . $tipo);
    } elseif ($tipo == "png") {
        $img = imagecreatefrompng($imagem . "." . $tipo);
    }

    $w = imagesx($img);
    $h = imagesy($img);
    $trans = imagecolortransparent($img);
    if ($trans >= 0) {
        $rgb = imagecolorsforindex($img, $trans);
        $oldimg = $img;
        $img = imagecreatetruecolor($w, $h);
        $color = imagecolorallocate($img, $rgb['red'], $rgb['green'], $rgb['blue']);
        imagefilledrectangle($img, 0, 0, $w, $h, $color);
        imagecopy($img, $oldimg, 0, 0, 0, 0, $w, $h);
    }

    if (imagejpeg($img, $imagem . ".jpg")) {
        return $imagem . ".jpg";
    } else {
        return false;
    }
}

function validate($object) {
    if (!isset($object))
        return false;
    if (empty($object))
        return false;

    return true;
}

function carregaUsuario() {
    $result = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '{$_COOKIE['logado']}'");
    $row = mysql_fetch_assoc($result);
    return $row;
}

function acentoMaiusculo($texto) {
    $array1 = array('à','á','â','ã','é','è','ê','í','ì','î','ó','ò','ô','õ','ú','ù','û','ä','ë','ï','ö','ü','ç');
    $array2 = array('À','Á','Â','Ã','É','È','Ê','Í','Ì','Î','Ó','Ò','Ô','Õ','Ú','Ù','Û','Ä','Ë','Ï','Ö','Ü','Ç');
    for ($x = 0; $x < count($array2); $x++) {
        $texto = str_replace($array1[$x],$array2[$x],$texto);
    }
    return strtoupper($texto);
}

///USADOS NO SEFIP, CAGED, DIRF, RAIS
function RemoveCaracteres($variavel) {
    $variavel = str_replace('(', "", $variavel);
    $variavel = str_replace(')', "", $variavel);
    $variavel = str_replace('-', '', $variavel);
    $variavel = str_replace('?', '', $variavel);
    $variavel = str_replace('/', '', $variavel);
    $variavel = str_replace(":", "", $variavel);
    $variavel = str_replace(",", " ", $variavel);
    $variavel = str_replace('.', '', $variavel);
    $variavel = str_replace(";", "", $variavel);
    $variavel = str_replace("\"", "", $variavel);
    $variavel = str_replace("'", "", $variavel);   
    $variavel = str_replace("`", "", $variavel);   
    
    return $variavel;
}

//USADO PARA REMOVER QUALQUER TIPO DE CARACTER ESPECIAL
function RemoveCaracteresGeral($variavel) {
    $variavel = str_replace('(', "", $variavel);
    $variavel = str_replace(')', "", $variavel);
    $variavel = str_replace('-', '', $variavel);
    $variavel = str_replace('_', '', $variavel);
    $variavel = str_replace('/', '', $variavel);
    $variavel = str_replace(":", "", $variavel);
    $variavel = str_replace(",", " ", $variavel);
    $variavel = str_replace('.', '', $variavel);
    $variavel = str_replace(";", "", $variavel);
    $variavel = str_replace("\"", "", $variavel);
    $variavel = str_replace("\'", "", $variavel);
    $variavel = str_replace("!", " ", $variavel);
    $variavel = str_replace("@", " ", $variavel);
    $variavel = str_replace("#", " ", $variavel);
    $variavel = str_replace("$", " ", $variavel);
    $variavel = str_replace("%", " ", $variavel);
    $variavel = str_replace("*", " ", $variavel);
    $variavel = str_replace("+", " ", $variavel);
    $variavel = str_replace("?", " ", $variavel);
    $variavel = str_replace("=", " ", $variavel);
    $variavel = str_replace("`", "", $variavel);   
    return $variavel;
}

function normalizaNometoFile($variavel){
    $variavel = strtoupper($variavel);
    if(strlen($variavel) > 200){
        $variavel = substr($variavel, 0, 200);
        $variavel = $variavel[0];
    }
    $nomearquivo = preg_replace("/ /","_",$variavel);
    $nomearquivo = preg_replace("/[\/]/","",$nomearquivo);
    $nomearquivo = preg_replace("/[ÁÀÂÃ]/i","A",$nomearquivo);
    $nomearquivo = preg_replace("/[áàâãª]/i","a",$nomearquivo);
    $nomearquivo = preg_replace("/[ÉÈÊ]/i","E",$nomearquivo);
    $nomearquivo = preg_replace("/[éèê]/i","e",$nomearquivo);
    $nomearquivo = preg_replace("/[ÍÌÎ]/i","I",$nomearquivo);
    $nomearquivo = preg_replace("/[íìî]/i","i",$nomearquivo);
    $nomearquivo = preg_replace("/[ÓÒÔÕ]/i","O",$nomearquivo);
    $nomearquivo = preg_replace("/[óòôõº]/i","o",$nomearquivo);
    $nomearquivo = preg_replace("/[ÚÙÛ]/i","U",$nomearquivo);
    $nomearquivo = preg_replace("/[úùû]/i","u",$nomearquivo);
    $nomearquivo = str_replace("Ç","C",$nomearquivo);
    $nomearquivo = str_replace("ç","c",$nomearquivo); 
    
    return $nomearquivo;
}

function RemoveLetras($variavel) {
    $letras = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
    foreach ($letras as $letra) {
        $variavel = str_replace($letra, '', $variavel);
    }
    return $variavel;
}

function RemoveEspacos($variavel) {
    $variavel = trim($variavel);
    return $variavel;
}

function Valor($variavel) {
    $variavel = str_replace('.', '', $variavel);
    return $variavel;
}

function valorBrtoUs($variavel) {
    $variavel = str_replace('.', '', $variavel);
    $variavel = str_replace(',', '.', $variavel);
    return $variavel;
}

function RemoveAcentos($str, $enc = "iso-8859-1") {
    $acentos = array(
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
        'o.' => '/&ordm;/'
    );
    return preg_replace($acentos, array_keys($acentos), htmlentities($str, ENT_NOQUOTES, $enc));
}

function removeAcentosCaracteres($string, $to_upper = false) {
    if($to_upper){
        $text = strtoupper(ereg_replace("[^a-zA-Z0-9 ]", "", strtr($string, "áàãâéêíóôõú?çÁÀÃÂÉÊÍÓÔÕÚ?Ç ", "aaaaeeiooouucAAAAEEIOOOUUC ")));
    }else{
        $text = ereg_replace("[^a-zA-Z0-9 ]", "", strtr($string, "áàãâéêíóôõú?çÁÀÃÂÉÊÍÓÔÕÚ?Ç ", "aaaaeeiooouucAAAAEEIOOOUUC "));
    }
    
    return $text;
}

function validaData($data, $formato = "Y-m-d") {
    $ok = true;

    if (empty($data))
        $ok = false;

    if ($formato == "Y-m-d" && $data == "0000-00-00")
        $ok = false;

    if ($formato == "d-m-Y" && $data == "00-00-0000")
        $ok = false;

    if ($formato == "d/m/Y" && $data == "00/00/0000")
        $ok = false;

    return $ok;
}

function getRegioes($user=null,$master=null,$key="-1") {
    
    if($user===null && $master===null){
        $usuario = carregaUsuario();
    }else{
        $usuario['id_funcionario'] = $user;
        $usuario['id_master'] = $master;
    }

    $qrpermreg = mysql_query("SELECT A.id_regiao,B.regiao FROM funcionario_regiao_assoc AS A
                                LEFT JOIN regioes AS B ON (A.id_regiao = B.id_regiao)
                                WHERE   id_funcionario = {$usuario['id_funcionario']} AND 
                                        A.id_master = {$usuario['id_master']} ORDER BY regiao");
    $regioes = array("$key" => "Selecione");
    while ($row = mysql_fetch_assoc($qrpermreg)) {
        $regioes[$row['id_regiao']] = $row['id_regiao']." - ". $row['regiao'];
    }

    return $regioes;
}

function getMasters($user=null){
    
    if($user===null){
        $usuario = carregaUsuario();
    }else{
        $usuario['id_funcionario'] = $user;
    }
    
    $qrMaster = "SELECT A.id_master,B.nome
                FROM funcionario_regiao_assoc AS A
                INNER JOIN master AS B ON (A.id_master = B.id_master)
                WHERE id_funcionario = {$usuario['id_funcionario']} AND B.status = 1 GROUP BY A.id_master";
    $rsMaster = mysql_query($qrMaster);
    
    $masters = array("-1" => " Selecione ");
    while ($row = mysql_fetch_assoc($rsMaster)) {
        $masters[$row['id_master']] = $row['id_master']." - ".$row['nome'];
    }

    return $masters;
}

function getProjetos($regiao) {
    $sql = mysql_query("SELECT *
                    FROM projeto
                    WHERE id_regiao = {$regiao} AND status_reg = '1'");
    $projetos = array("-1" => "Selecione");
    while ($rst = mysql_fetch_assoc($sql)) {
        $projetos[$rst['id_projeto']] = $rst['id_projeto'] . " - " . $rst['nome'];
    }
    return $projetos;
}

function getUnidades($regiao) {
    $sql = mysql_query("SELECT *
                    FROM unidade
                    WHERE id_regiao = {$regiao} AND status_reg = '1'");
    $unidades = array("-1" => "Selecione");
    while ($rst = mysql_fetch_assoc($sql)) {
        $unidades[$rst['id_unidade']] = $rst['id_unidade'] . " - " . $rst['unidade'];
    }
    return $unidades;
}

function getCurso($regiao) {
    $sql = mysql_query("SELECT *
                    FROM curso
                    WHERE id_regiao = {$regiao} AND status = '1' AND status_reg = '1' ORDER BY nome ASC");
    $cursos = array("-1" => "« Selecione »");
    while ($rst = mysql_fetch_assoc($sql)) {
        $cursos[$rst['id_curso']] = $rst['nome'];
    }
    return $cursos;
}

function getFuncionarios() {
    $sql = mysql_query("SELECT *
                        FROM funcionario
                        WHERE status_reg = '1'
                        ORDER BY nome1");
    $func = array("-1" => "Selecione");
    while ($rst = mysql_fetch_assoc($sql)) {
        $func[$rst['id_funcionario']] = $rst['nome1'];
    }
    return $func;
}

function getProjetosRegiao($id_regiao) {
    $sql = "SELECT *
            FROM projeto            
            WHERE id_regiao = '{$id_regiao}' ORDER BY nome";
    $proj = mysql_query($sql) or die(mysql_error());
    return $proj;
}

function diferencaDias($data_inicial, $data_final, $delimiter="/"){
   $data_inicial = explode($delimiter, $data_inicial);
   $data_final = explode($delimiter, $data_final);
   $time_inicial = mktime(0, 0, 0, $data_inicial[1], $data_inicial[0], $data_inicial[2]);
   $time_final = mktime(0, 0, 0, $data_final[1], $data_final[0], $data_final[2]);
   $diferenca = ($time_final - $time_inicial);
   $diferenca_dias = (int)floor( $diferenca / (60 * 60 * 24)); // 225 dias  
   $diferenca_dias = $diferenca_dias + 1;
   return $diferenca_dias;
}
function somarDias($quantidade_dia = 0, $data =NULL , $delimiter="-",$formato_timestamp=TRUE){
    $data = ($data!=NULL) ? explode($delimiter, $data) : array(date('Y'), date('m'), date('d') ) ;
    if(!$formato_timestamp && !empty($data)){
        $data = array_reverse($data);
    } 
    return date('d/m/Y',mktime(0,0,0,$data[1],$data[2]+$quantidade_dia,$data[0]));
}

function validaCPF($cpf) {
    // Verifiva se o número digitado contém todos os digitos
    $cpf = str_pad(ereg_replace('[^0-9]', '', $cpf), 11, '0', STR_PAD_LEFT);

    // Verifica se nenhuma das sequências abaixo foi digitada, caso seja, retorna falso
    if (strlen($cpf) != 11 || $cpf == '00000000000' || $cpf == '11111111111' || $cpf == '22222222222' || $cpf == '33333333333' || $cpf == '44444444444' || $cpf == '55555555555' || $cpf == '66666666666' || $cpf == '77777777777' || $cpf == '88888888888' || $cpf == '99999999999') {
        return false;
    } else {   // Calcula os números para verificar se o CPF é verdadeiro
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf{$c} * (($t + 1) - $c);
            }
 
            $d = ((10 * $d) % 11) % 10;
 
            if ($cpf{$c} != $d) {
                return false;
            }
        }
 
        return true;
    }
}

// Funcao para validar PIS
function validaPIS($pis){
    
    //remove todos os caracteres deixando apenas valores numéricos
    $pis = preg_replace('/[^0-9]+/', '', $pis);

    //se a quantidade de caracteres numéricos  for diferente de 11 é inválido
    if (strlen($pis) <> 11)
        return false;

    //inicia uma variável que será responsável por armazenar o cálculo da somatória dos números individuais
    $digito = 0;
    
    for($i = 0, $x=3; $i<10; $i++, $x--){
        
    //Verifica se $x for menor que 2, seu valor será 9, senão será $x
    $x = ($x < 2) ? 9 : $x;
    
    //Realiza a soma dos números individuais vezes o fator multiplicador
    $digito += $pis[$i]*$x;
    }

    /**
    * Verificamos se o módulo do resultado por 11 é menor que 2, se for o valor será 0
    * Caso não for, pegar o valor 11 e diminuir com o módulo do resultado da somatória.
    */

    $calculo = (($digito%11) < 2) ? 0 : 11-($digito%11);
    //Se o valor da variavel cálculo for diferente do último digito, ele será inválido, senão verdadeiro
    return ($calculo <> $pis[10]) ? false :true;
}

// Função para formatar qualquer série de números.
//      echo formataCampo($cnpj,'##.###.###/####-##');
//	echo formataCampo($cpf,'###.###.###-##');
//	echo formataCampo($cep,'#####-###');
//	echo formataCampo($data,'##/##/####');
//	echo formataCampo($pis,'###.#####.##-#');
function formataCampo($val, $mask) {
    $val = str_replace(",", "", str_replace("-", "", str_replace(".", "", $val)));
    $maskared = '';
    $k = 0;
    for ($i = 0; $i <= strlen($mask) - 1; $i++) {
        if ($mask[$i] == '#') {
            if (isset($val[$k]))
                $maskared .= $val[$k++];
        }
        else {
            if (isset($mask[$i]))
                $maskared .= $mask[$i];
        }
    }
    return $maskared;
}

function formataMoeda($moeda_bd,$cifrao=null) {
    $moeda = number_format($moeda_bd, 2, ',', '.');
    if($cifrao == 1){
        $rs = "";
    }else{
        $rs = "R$ ";
    }
    return $rs . $moeda;
}

function selected( $value, $selected ){
    return $value == $selected ? ' selected="selected"' : '';
}

/*MÉTODO PARA RETORNAR O PROJETO*/
function todosProjetos(){
    $sql = "SELECT * FROM projeto";
    $projetos  = mysql_query($sql);
    return $projetos;
}

function projetosId($id_projeto){
    $sql = "SELECT * FROM projeto WHERE id_projeto = '{$id_projeto}'";
    $projetos  = mysql_query($sql);
    $row = mysql_fetch_assoc($projetos);
    return $row;
}

function masterId($id_master){
    $sql = "SELECT * FROM master WHERE id_master = '{$id_master}'";
    $master  = mysql_query($sql);
    $row = mysql_fetch_assoc($master);
    return $row;
}

function selectUF($value, $atributos) {
    /*
     * função para criar select com estados brasileiros
     * Autor: Leonardo
     */
    $options = array(
        '-1' => '-- Selecione --',
        'AC' => 'Acre',
        'AL' => 'Alagoas',
        "AP" => 'Amapá',
        "AM" => 'Amazonas',
        "BA" => 'Bahia',
        "CE" => 'Ceará',
        "DF" => 'Distrito Federal',
        "ES" => 'Espirito Santo',
        "GO" => 'Goiás',
        "MA" => 'Maranhão',
        "MT" => 'Mato Grosso',
        "MS" => 'Mato Grosso do Sul',
        "MG" => 'Minas Gerais',
        "PA" => 'Pará',
        "PB" => 'Paraíba',
        "PR" => 'Paraná',
        "PE" => 'Pernambuco',
        "PI" => 'Piauí',
        "RJ" => 'Rio de Janeiro',
        "RN" => 'Rio Grande do Norte',
        "RS" => 'Rio Grande do Sul',
        "RO" => 'Rondônia',
        "RR" => 'Roraima',
        "SC" => 'Santa Catarina',
        "SP" => 'São Paulo',
        "SE" => 'Sergipe',
        "TO" => 'Tocantins'
    );
    return montaSelect($options, $value, $atributos);
}

function horas_extenso($valor = 0, $maiusculas = false) {
    // verifica se tem virgula decimal
    if (strpos($valor, ",") > 0) {
        // retira o ponto de milhar, se tiver
        $valor = str_replace(".", "", $valor);

        // troca a virgula decimal por ponto decimal
        $valor = floor($valor);
    }
    $singular = array("minuto", "hora", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
    $plural = array("minutos", "horas", "mil", "milhões", "bilhões", "trilhões",
        "quatrilhões");

    $c = array("", "cem", "duzentos", "trezentos", "quatrocentos",
        "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
    $d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta",
        "sessenta", "setenta", "oitenta", "noventa");
    $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze",
        "dezesseis", "dezesete", "dezoito", "dezenove");
    $u = array("", "um", "dois", "três", "quatro", "cinco", "seis",
        "sete", "oito", "nove");
    $z = 0;
    $valor = number_format($valor, 2, ".", ".");
    $inteiro = explode(".", $valor);
    $cont = count($inteiro);
    for ($i = 0; $i < $cont; $i++)
        for ($ii = strlen($inteiro[$i]); $ii < 3; $ii++)
            $inteiro[$i] = "0" . $inteiro[$i];

    $fim = $cont - ($inteiro[$cont - 1] > 0 ? 1 : 2);
    for ($i = 0; $i < $cont; $i++) {
        $valor = $inteiro[$i];
        $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
        $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
        $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

        $r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd &&
                $ru) ? " e " : "") . $ru;
        $t = $cont - 1 - $i;
        $r .= $r ? " " . ($valor > 1 ? $plural[$t] : $singular[$t]) : "";
        if ($valor == "000")
            $z++; elseif ($z > 0)
            $z--;
        if (($t == 1) && ($z > 0) && ($inteiro[0] > 0))
            $r .= (($z > 1) ? " de " : "") . $plural[$t];
        if ($r)
            $rt = trim($rt . ((($i > 0) && ($i <= $fim) &&
                    ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r);
    }

    if (!$maiusculas) {
        return($rt ? trim($rt) : "zero");
    } elseif ($maiusculas == "2") {
        return (strtoupper($rt) ? strtoupper(trim($rt)) : "ZERO");
    } elseif ($maiusculas == "3") {
        return (ucfirst($rt) ? ucfirst(trim($rt)) : "Zero");
    } else {
        return (ucwords($rt) ? ucwords(trim($rt)) : "Zero");
    }
}

function numero_extenso($valor = 0, $bolExibirMoeda = true, $bolPalavraFeminina = false) {

    $singular = null;
    $plural = null;

    if ( $bolExibirMoeda )
    {
        $singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
        $plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões","quatrilhões");
    }
    else
    {
        $singular = array("", "", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
        $plural = array("", "", "mil", "milhões", "bilhões", "trilhões","quatrilhões");
    }

    $c = array("", "cem", "duzentos", "trezentos", "quatrocentos","quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
    $d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta","sessenta", "setenta", "oitenta", "noventa");
    $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze","dezesseis", "dezesete", "dezoito", "dezenove");
    $u = array("", "um", "dois", "três", "quatro", "cinco", "seis","sete", "oito", "nove");


    if ( $bolPalavraFeminina )
    {

        if ($valor == 1) 
        {
            $u = array("", "uma", "duas", "três", "quatro", "cinco", "seis","sete", "oito", "nove");
        }
        else 
        {
            $u = array("", "um", "duas", "três", "quatro", "cinco", "seis","sete", "oito", "nove");
        }


        $c = array("", "cem", "duzentas", "trezentas", "quatrocentas","quinhentas", "seiscentas", "setecentas", "oitocentas", "novecentas");


    }


    $z = 0;

    $valor = number_format( $valor, 2, ".", "." );
    $inteiro = explode( ".", $valor );

    for ( $i = 0; $i < count( $inteiro ); $i++ ) 
    {
        for ( $ii = mb_strlen( $inteiro[$i] ); $ii < 3; $ii++ ) 
        {
            $inteiro[$i] = "0" . $inteiro[$i];
        }
    }

    // $fim identifica onde que deve se dar junção de centenas por "e" ou por "," ;)
    $rt = null;
    $fim = count( $inteiro ) - ($inteiro[count( $inteiro ) - 1] > 0 ? 1 : 2);
    for ( $i = 0; $i < count( $inteiro ); $i++ )
    {
        $valor = $inteiro[$i];
        $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
        $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
        $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

        $r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd && $ru) ? " e " : "") . $ru;
        $t = count( $inteiro ) - 1 - $i;
        $r .= $r ? " " . ($valor > 1 ? $plural[$t] : $singular[$t]) : "";
        if ( $valor == "000")
            $z++;
        elseif ( $z > 0 )
            $z--;

        if ( ($t == 1) && ($z > 0) && ($inteiro[0] > 0) )
            $r .= ( ($z > 1) ? " de " : "") . $plural[$t];

        if ( $r )
            $rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
    }

    $rt = mb_substr( $rt, 1 );

    return($rt ? trim( $rt ) : "zero");

}

function valor_extenso($valor = 0, $maiusculas = false) {
    // verifica se tem virgula decimal
    if (strpos($valor, ",") > 0) {
        // retira o ponto de milhar, se tiver
        $valor = str_replace(".", "", $valor);

        // troca a virgula decimal por ponto decimal
        $valor = str_replace(",", ".", $valor);
    }
    $singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
    $plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões",
        "quatrilhões");

    $c = array("", "cem", "duzentos", "trezentos", "quatrocentos",
        "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
    $d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta",
        "sessenta", "setenta", "oitenta", "noventa");
    $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze",
        "dezesseis", "dezesete", "dezoito", "dezenove");
    $u = array("", "um", "dois", "três", "quatro", "cinco", "seis",
        "sete", "oito", "nove");
    $z = 0;
    $valor = number_format($valor, 2, ".", ".");
    $inteiro = explode(".", $valor);
    $cont = count($inteiro);
    for ($i = 0; $i < $cont; $i++)
        for ($ii = strlen($inteiro[$i]); $ii < 3; $ii++)
            $inteiro[$i] = "0" . $inteiro[$i];

    $fim = $cont - ($inteiro[$cont - 1] > 0 ? 1 : 2);
    for ($i = 0; $i < $cont; $i++) {
        $valor = $inteiro[$i];
        $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
        $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
        $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

        $r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd &&
                $ru) ? " e " : "") . $ru;
        $t = $cont - 1 - $i;
        $r .= $r ? " " . ($valor > 1 ? $plural[$t] : $singular[$t]) : "";
        if ($valor == "000")
            $z++; elseif ($z > 0)
            $z--;
        if (($t == 1) && ($z > 0) && ($inteiro[0] > 0))
            $r .= (($z > 1) ? " de " : "") . $plural[$t];
        if ($r)
            $rt = trim($rt . ((($i > 0) && ($i <= $fim) &&
                    ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r);
    }

    if (!$maiusculas) {
        return($rt ? trim($rt) : "zero");
    } elseif ($maiusculas == "2") {
        return (strtoupper($rt) ? strtoupper(trim($rt)) : "ZERO");
    } elseif ($maiusculas == "3") {
        return (ucfirst($rt) ? ucfirst(trim($rt)) : "Zero");
    } else {
        return (ucwords($rt) ? ucwords(trim($rt)) : "Zero");
    }
}

function montaCriteriaSimples($var, $campo){
    $ret = "";
    if(isset($var)){
        if(!is_array($var)){
            $ret = $campo." ='{$var}'";
        }else{
            $ret = $campo." IN ('".  implode("','", $var)."')";
        }
    }else{
        return false;
    }
    return $ret;
}

function montaCabecalhoNovo($regioes, $masters, $usuario, $file){
    $return = array();
    $regiaoSelected = $regioes[$usuario['id_regiao']];
    $masterSelected = $masters[$usuario['id_master']];

    unset($regioes[$usuario['id_regiao']]);
    unset($regioes['-1']);

    unset($masters[$usuario['id_master']]);
    unset($masters['-1']);
    
    $return['regiaoSelected'] = $regiaoSelected;
    $return['masterSelected'] = $masterSelected;
    $return['regioes'] = $regioes;
    $return['masters'] = $masters;
    
    $defaultPath = null;
    $url = explode("/intranet/", $file);
    $urlCount = substr_count($url[1], '/');
    for($i=0; $i < $urlCount; $i++){
        $defaultPath .= "../";
    }
    $return['defaultPath'] = $defaultPath;
    $return['fullRootPath'] = $_SERVER['HTTP_HOST'].'/intranet/';
    
    return $return;
}

function validatePost($variavel,$tipo = null){
    $re = "";
    if ($tipo !== null) {
        switch ($tipo) {
            case "INT":
                $re = filter_input(INPUT_POST, $variavel, FILTER_SANITIZE_NUMBER_INT);
                break;
            case "EMAIL":
                $re = filter_input(INPUT_POST, $variavel, FILTER_SANITIZE_EMAIL);
                break;
            case "FLOAT":
                $re = filter_input(INPUT_POST, $variavel, FILTER_SANITIZE_NUMBER_FLOAT);
                break;
            case "URL":
                $re = filter_input(INPUT_POST, $variavel, FILTER_SANITIZE_URL);
                break;
        }
    } else {
        $re = filter_input(INPUT_POST, $variavel, FILTER_SANITIZE_STRING);
    }
    return $re;
}

function validarCNPJ($cnpj){
    $cnpj = str_pad(str_replace(array('.','-','/'),'',$cnpj),14,'0',STR_PAD_LEFT);
    if (strlen($cnpj) != 14){
        return false;
    }else{
        for($t = 12; $t < 14; $t++){
            for($d = 0, $p = $t - 7, $c = 0; $c < $t; $c++){
                $d += $cnpj{$c} * $p;
                $p  = ($p < 3) ? 9 : --$p;
            }
            $d = ((10 * $d) % 11) % 10;
            if($cnpj{$c} != $d){
                return false;
            }
        }
        return true;
    }
}

function soNumero($str) {
    return preg_replace("/[^0-9]/", "", $str);
}

function mascara_string($mascara,$string){
   $string = str_replace(" ","",$string);
   for($i=0;$i<strlen($string);$i++){
      $mascara[strpos($mascara,"#")] = $string[$i];
   }
   return $mascara;
}

//mascara para exibição, tel com 8/9 digitos
function mascara_stringTel($string){
    if(strlen($string) == 10){
        $string = mascara_string("(##)####-####", $string);
    }elseif(($string == 0) || ($string == '')){
        $string = '';
    }else{
        $string = mascara_string("(##)####-#####", $string);
    }
    return $string;
}


/**
 * Expressão regular Personalizada, automaticamente ela remove os espaços multiplos
 * e você pode aproveitar para remover outros caracters especificos passando em array
 * @param type $str
 * @param array $caracters
 * @return string
 */
function expersonalizada($str, $caracters=null, $composto=null){
    $array = array();
    $re = $str;
    if($caracters !== null){
        if(is_array($caracters)){
            foreach($caracters as $val){
                array_push($array, $val);
            }
        }
    }
    
    if(count($array) > 0){
        $match = '/\\'  . implode("|\\",$array) . '/i';
        $re = preg_replace($match, "", $str);
    }
    
    if($composto!==null){
        $arrayC = array();
        if(is_array($composto)){
            foreach($composto as $val){
                array_push($arrayC, $val);
            }
            
            $match = '/'  . implode("|",$arrayC) . '/i';
            $re = preg_replace($match, "", $re);
        }
    }
    
    $re = preg_replace("/[[:blank:]]+/", " ", $re);
    $re = trim($re);
    return $re;
}

/**
 * Substitui a sequencias de caracteres identicos pelo caracter que está sendo repetido
 * ex: vascooooo  = vasco
 * @param type $str
 * @param int $nrRep
 * @return string
 */
function regexCaracterIgualConsecutivo ($str,$nrRep){
    $letras = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
    $re = $str;
    foreach ($letras as $letra) {
          $re =  preg_replace("/($letra{{$nrRep},})/i", "$letra", $re);
    }
    return $re;
}

function somarUteis($str_data, $int_qtd_dias_somar = 7) {    
    // Caso seja informado uma data do MySQL do tipo DATETIME - aaaa-mm-dd 00:00:00
    // Transforma para DATE - aaaa-mm-dd
    $str_data = substr($str_data, 0, 10);
    
    // Se a data estiver no formato brasileiro: dd/mm/aaaa
    // Converte-a para o padrão americano: aaaa-mm-dd
    if (preg_match("@/@", $str_data) == 1) {
        $str_data = implode("-", array_reverse(explode("/", $str_data)));
    }
    
    $array_data = explode('-', $str_data);
    $count_days = 0;
    $int_qtd_dias_uteis = 0;
    
    while ($int_qtd_dias_uteis < $int_qtd_dias_somar) {
        $count_days++;
        if (( $dias_da_semana = gmdate('w', strtotime('+' . $count_days . ' day', mktime(0, 0, 0, $array_data[1], $array_data[2], $array_data[0]))) ) != '0' && $dias_da_semana != '6') {
            $int_qtd_dias_uteis++;
        }
    }
    
    return gmdate('d/m/Y', strtotime('+' . $count_days . ' day', strtotime($str_data)));
}

// funcao para passar string para iso-8859-1
function to_iso_8859_1($string) {
    $encoding = mb_detect_encoding($string);
    if ($encoding != "iso-8859-1") {
        return mb_convert_encoding($string, 'iso-8859-1', $encoding);
    } else {
        return $string;
    }
}

// pra imprimir arrays com tag pre
function print_array($array){
    echo '<pre>';
    print_r($array);
    echo '</pre>';
    return TRUE;
}

function normalizaNomeToView($nome){
    $reservado = array('de','do','da','e','dos','das');
    $nome = strtolower($nome);
    $exp = explode(" ", $nome);
    $nomeExibe = "";
    foreach($exp as $palavra){
        if(!in_array($palavra,$reservado)){
            $nomeExibe .= ucfirst($palavra);
        }else{
            $nomeExibe .= $palavra;
        }
        $nomeExibe .= " ";
    }
    return trim($nomeExibe);
}

// Converte as duas datas para um objeto DateTime do PHP
/* 
 * EXEMPLO DE RETORNO
 * [y] => 1
 * [m] => 9
 * [d] => 22
 * [h] => 0
 * [i] => 0
 * [s] => 0
 * [weekday] => 0
 * [weekday_behavior] => 0
 * [first_last_day_of] => 0
 * [invert] => 0
 * [days] => 661
 * [special_type] => 0
 * [special_amount] => 0
 * [have_weekday_relative] => 0
 * [have_special_relative] => 0 
 */
function diferencaData($data_ini, $data_fim, $retorno = null, $formato = null) {  
    
    if($formato == null){
        $formato = "Y-m-d";
    }
    
    // PARA O PHP 5.3 OU SUPERIOR
    $inicio = DateTime::createFromFormat($formato, $data_ini);
    $fim = DateTime::createFromFormat($formato, $data_fim);
    
    // PARA O PHP 5.2
    // $inicio = date_create_from_format('d/m/Y H:i:s', $inicio);        
    // $fim = date_create_from_format('d/m/Y H:i:s', $fim);
    
    $intervalo = $inicio->diff($fim); 
    
    if($retorno == null){
        return $intervalo;
    }else{
        return $intervalo->$retorno;
    }        
}

/**
 * 
 * @param type $idClt
 * @return boolean
 */
function onUpdate($id_clt){
    $retorno = false;
    $dataAtual = date('Y-m-d H:i:s');
    $qry = "UPDATE rh_clt SET data_ultima_atualizacao = '{$dataAtual}' WHERE id_clt = '{$id_clt}'";
    $sql = mysql_query($qry) or die('Erro de UPDATE em campo data_ultima_atualizacao');
    
    if($sql){
        $retorno = true;
    }
    
    return $retorno;
    
}


function diasUteis($mes, $ano, $feriado = true) {
    $uteis = 0;

    // OBTEM O NUMERO DE DIAS DO MÊS
    $dias_no_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

    for ($dia = 1; $dia <= $dias_no_mes; $dia++) {

        // OBTEM O TIMESTAMP
        $timestamp = mktime(0, 0, 0, $mes, $dia, $ano);
        $semana = date("N", $timestamp);

        if ($semana < 6) {
            $uteis++;
        }
    }

    if ($feriado) {
        $tot_feriados = getFeriados($mes, $ano, false, true);
        $uteis -= $tot_feriados;
    }

    return $uteis;
}

function getFeriados($mes, $ano, $lista = false, $count = false, $ini = "", $fim = "") {

    $and = "";

    if (($ini != "") && ($fim != "")) {
        $and = "AND dt BETWEEN '{$ini}' AND '{$fim}'";
    }

    $query = "
            SELECT * FROM(
                SELECT A.*, WEEKDAY(DATE_FORMAT(A.data, '{$ano}-%m-%d')) AS num_dia,
                        IF(A.movel = 1, A.data, DATE_FORMAT(A.data, '{$ano}-%m-%d')) AS dt
                FROM rhferiados AS A
            ) AS tmp WHERE num_dia NOT IN(5,6) AND MONTH(dt) = {$mes} AND YEAR(dt) = {$ano} AND status = 1 {$and}
        ";
    
    //echo $query;
    $sql = mysql_query($query) or die("ERRO getFeriados");

    if ($lista) {
        $result = mysql_fetch_assoc($sql);

        return $result;
    } elseif ($count) {
        $tot = mysql_num_rows($sql);

        return $tot;
    } else {
        return $sql;
    }
}