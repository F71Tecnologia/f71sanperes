<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

include('../../conn.php');
include('../../wfunction.php');
include('funcoes.php');
include('../../classes/ICalculosDatas.class.php');
include('classes/CalculosDataValeAlimentacao.class.php');
include('classes/IDaoValeAlimentacao.class.php');
include('classes/DaoValeAlimentacao.class.php');


if (isset($_REQUEST['download']) && !empty($_REQUEST['download'])) {
    $file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'arquivos' . DIRECTORY_SEPARATOR . $_REQUEST['download'];
    $name_file_download = isset($_REQUEST['name_file']) ? $_REQUEST['name_file'] : $_REQUEST['download'];
    header("Content-Type: application/save");
    header("Content-Length:" . filesize($file));
    header('Content-Disposition: attachment; filename="' . $name_file_download . '"');
    header("Content-Transfer-Encoding: binary");
    header('Expires: 0');
    header('Pragma: no-cache');
    $fp = fopen("$file", "r");
    fpassthru($fp);
    fclose($fp);
    exit();
}

//dao

function getRegioesFuncionario() {
    $id_user = isset($_COOKIE['logado']) ? $_COOKIE['logado'] : FALSE;
    if ($id_user) {
        $sql = "SELECT A.id_regiao,B.regiao FROM funcionario_regiao_assoc AS A
                                LEFT JOIN regioes AS B ON (A.id_regiao = B.id_regiao)
                                WHERE   id_funcionario = " . $id_user . " AND 
                                        A.id_master = " . $id_user . " ORDER BY A.id_regiao;";
        $query = mysql_query($sql);
        $regiao = array();
        while ($row = mysql_fetch_array($query)) {
            $regiao[$row['id_regiao']] = $row['id_regiao'] . ' - ' . $row['regiao'];
        }
        return $regiao;
    }
}

function getValoresDiarios($regiao) {
    $sql = "SELECT A.id_va_valor_diario, A.regiao AS nome_regiao, valor_diario FROM rh_va_valor_diario AS A LEFT JOIN regioes AS B ON (A.regiao=B.id_regiao) WHERE A.regiao='$regiao' AND A.`status`=1";
//    echo $sql."<br>";
    $result = mysql_query($sql);
    $relacao_tarifas = array();
    while ($row = mysql_fetch_array($result)) {
        $relacao_tarifas[] = array('id_va_valor_diario' => $row['id_va_valor_diario'], 'nome_regiao' => $row['nome_regiao'], 'valor_diario' => $row['valor_diario']);
    }
    return $relacao_tarifas;
}

function getFuncionarios(Array $dados) {

    $projeto = isset($dados['projeto']) ? $dados['projeto'] : '';
    $cpf = (isset($dados['cpf']) && !empty($dados['cpf'])) ? " AND A.cpf =  '" . $dados['cpf'] . "'" : '';
    $nome = (isset($dados['nome']) && !empty($dados['nome'])) ? " AND A.nome LIKE  '" . $dados['nome'] . "%'" : '';
    $alimentacao = (isset($dados['alimentacao']) && $dados['alimentacao'] == 'true') ? ' AND A.vale_alimentacao =1 ' : '';
    $mes = (isset($dados['mes']) && !empty($dados['mes'])) ? " AND MONTH(A.data_entrada) =  " . $dados['mes'] . " " : '';
    $ano = (isset($dados['ano']) && !empty($dados['ano'])) ? " AND YEAR(A.data_entrada) =  '" . $dados['ano'] . "'" : '';

    $sql = "SELECT A.id_clt, A.nome AS nome_funcionario,A.cpf, DATE_FORMAT(data_entrada,'%d/%m/%Y') AS data_entrada_f, IF(A.vale_alimentacao=1,'SIM','NÃO') AS solicitou_vale_alimentacao, E.valor_diario FROM rh_clt AS A
	LEFT JOIN projeto AS B ON(A.id_projeto=B.id_projeto)
        LEFT JOIN rh_va_clt_valor_diario AS C ON(A.id_clt=C.id_clt AND C.`status`=1)
        LEFT JOIN rh_va_valor_diario AS E ON(E.id_va_valor_diario = C.id_valor_diario AND E.`status`=1)
	WHERE A.id_projeto = '$projeto'  $cpf $nome $alimentacao $mes $ano GROUP BY A.id_clt";
    
//    echo $sql."<br>";

    $result = mysql_query($sql);
    $relacao_funcionarios = array();
    while ($resp = mysql_fetch_array($result)) {
        $relacao_funcionarios[] = $resp;
    }
//    echo '<pre>';
//    print_r($relacao_funcionarios);
//    echo '<pre>';
    return $relacao_funcionarios;
}
function getPedidos($status=1){
    $status = ($status) ? " WHERE `status`='$status' " : '';
//    id_va_pedido mes ano projeto user
    $sql = "SELECT A.id_va_pedido, A.mes, A.ano, B.nome AS projeto, C.nome AS nome_usuario, C.id_funcionario, (SELECT SUM( va_valor_diario * dias_uteis) AS valor FROM `rh_va_relatorio` WHERE id_va_pedido=A.id_va_pedido) AS valor_pedido FROM rh_va_pedido AS A LEFT JOIN projeto AS B ON(A.projeto=B.id_projeto) LEFT JOIN funcionario AS C ON(A.user=C.id_funcionario) $status ";
//    echo $sql."<br>";
    $resp = mysql_query($sql);
    $pedidos = array();
    while($row = mysql_fetch_array($resp)){
        $pedidos[] = $row;
    }
    return $pedidos;
}

function relacao_clt_pedido(Array $dados, $gravar=FALSE) {
    $projeto = isset($dados['projeto']) ? $dados['projeto'] : NULL;
    $ano = isset($dados['ano']) ? $dados['ano'] : NULL;
    $mes = isset($dados['mes']) ? $dados['mes'] : NULL;
    $data_inicial = isset($dados['data_inicial']) ? $dados['data_inicial'] : NULL;
    $data_final = isset($dados['data_final']) ? $dados['data_final'] : NULL;
    $dias_uteis = isset($dados['dias_uteis']) ? $dados['dias_uteis'] : NULL;

    $sql = "SELECT A.id_clt, A.nome AS nome_funcionario, B.nome AS nome_projeto, D.valor_diario FROM rh_clt AS A LEFT JOIN projeto AS B ON(A.id_projeto=B.id_projeto) 
            LEFT JOIN rh_va_clt_valor_diario AS C ON( (A.`id_clt`=C.id_clt) AND C.`status`=1 )
            LEFT JOIN rh_va_valor_diario AS D ON( (C.id_valor_diario=D.id_va_valor_diario) AND D.status=1 )
            WHERE A.id_projeto=$projeto AND A.vale_alimentacao=1  AND A.data_saida = '0000-00-00' AND A.data_demi IS NULL  GROUP BY A.id_clt";
//    echo $sql."<br>";
    $result = mysql_query($sql);
    $relacao_funcionarios = array();
    $valor_total_pedido = 0;
    $registros_descartados = 0;
    
    $user =  isset($_COOKIE['logado']) ? $_COOKIE['logado'] : '';
    if($gravar){
        $sql = "INSERT INTO rh_va_pedido(`mes`,`ano`,`projeto`,`data_inicial`,`data_final`, `user`,`data`,`status`) VALUES('$mes','$ano','$projeto','$data_inicial','$data_final',  '$user',NOW(),'0');";
        $res_pedido = mysql_query($sql);
        $id_pedido = mysql_insert_id();
    }
    
    $sql = 'INSERT INTO rh_va_relatorio(`id_va_pedido`,`id_clt`, `dias_uteis`,`va_valor_diario`) VALUES';
    
    while ($resp = mysql_fetch_array($result)) {
        $relacao_funcionarios[$resp['id_clt']]['id_clt'] = $resp['id_clt'];
        $relacao_funcionarios[$resp['id_clt']]['nome_funcionario'] = $resp['nome_funcionario'];
        $relacao_funcionarios[$resp['id_clt']]['dias_uteis'] = $dias_uteis;
        $relacao_funcionarios[$resp['id_clt']]['valor_diario'] = ($resp['valor_diario'] > 0) ? number_format($resp['valor_diario'], 2, ',', '.') : '0,00';
        $valor_clt = ($dias_uteis * $resp['valor_diario']);
        $relacao_funcionarios[$resp['id_clt']]['valor_recarga'] = ($valor_clt > 0) ? number_format($valor_clt, 2, ',', '.') : '0,00';
        $valor_total_pedido += $valor_clt;
        if ($valor_clt <= 0) {
            $registros_descartados++;
        }else{
            $sql .= "('$id_pedido', '$resp[id_clt]', '$dias_uteis', '$resp[valor_diario]'),";
        }
    }
    if($gravar){
        $sql = substr($sql,0,-1);;
        if(mysql_query($sql.';')){
            return mysql_query("UPDATE rh_va_pedido SET `status`='1' WHERE id_va_pedido=$id_pedido LIMIT 1");            
        }
    }
    return array('relacao_funcionarios'=>$relacao_funcionarios,'registros_descartados'=>$registros_descartados,'valor_total_pedido'=>$valor_total_pedido);
}

function get_relacao_clt_pedido($id_projeto){
    $sql = "SELECT D.id_clt, A.id_va_pedido, A.mes, A.ano, B.nome AS projeto, C.nome AS usuario, E.nome AS nome_funcionario, "
            . "D.dias_uteis, D.va_valor_diario AS valor_diario, (D.dias_uteis*D.va_valor_diario) AS valor_recarga, "
            . "F.salario, (F.salario*0.20) AS salario_porcentagem, IF((F.salario*0.20)>=(D.dias_uteis*D.va_valor_diario),(D.dias_uteis*D.va_valor_diario),(F.salario*0.20)) AS desconto_movimento "
            . ",REPLACE(REPLACE(E.cpf,'.',''),'-','') AS cpf_limpo, DATE_FORMAT(E.data_nasci,'%d/%m/%Y') AS data_nascimento, E.sexo "
            . " FROM rh_va_pedido AS A "
            . "LEFT JOIN projeto AS B ON(A.projeto=B.id_projeto) "
            . "LEFT JOIN funcionario AS C ON(A.user=C.id_funcionario) "
            . "LEFT JOIN rh_va_relatorio AS D ON(A.id_va_pedido=D.id_va_pedido) "
            . "LEFT JOIN rh_clt AS E ON(E.id_clt=D.id_clt)"
            . " LEFT JOIN curso AS F ON(E.id_curso=F.id_curso) "
            
            . "WHERE A.`status`='1' AND A.id_va_pedido='$id_projeto'";
//    echo $sql."<br>";
    $resp = mysql_query($sql);
    $relacao_pedido = array();
    while($row = mysql_fetch_array($resp)){
        $relacao_pedido[$row['id_clt']] = $row;
        $relacao_pedido[$row['id_clt']]['valor_diario'] = number_format($row['valor_diario'],2,',','.');
        $relacao_pedido[$row['id_clt']]['valor_recarga'] = number_format($row['valor_recarga'],2,',','.');
    }
    return $relacao_pedido;
}
function criar_csv_aelo($relacao_funcionarios){
    $tipo_local = 'PF';
    $local = '0001';
    $header = ';NOME DO USUÁRIO;'
            . 'CPF;'
            . 'DATA DE NASCIMENTO;'
            . 'CÓDIGO DE SEXO;'
            . 'VALOR;'
            . 'TIPO DE LOCAL ENTREGA;'
            . 'LOCAL DE ENTREGA;'
            . 'MATRÍCULA;'."\n";
    $body = '';
    foreach($relacao_funcionarios as $funcionario){
        $body .= "%;".substr($funcionario[nome_funcionario], 0, 40).";"
                . "$funcionario[cpf_limpo];"
                . "$funcionario[data_nascimento];"
                . "$funcionario[sexo];"
                . "$funcionario[valor_recarga];"
                . "$tipo_local;"
                . "$local;"
                . ";%\n";
    }
    $folder = 'arquivos/';
    $name = date('Y-m-d').'.csv';
    $cont = 1;
    while(is_file($folder.$name)){
        $name = date('Y-m-d').'_'.$cont.'.csv';
        $cont++;
    }
    $file = fopen($folder.$name, 'a');
    fwrite($file, $header.$body);
    fclose($file);
    return json_encode(array('name_file'=>$name,'dados'=>$header.$body));
}

$usuario = carregaUsuario();
$usuario['id_projeto'] = $usuario['id_regiao']; // from hell!!!


$calendario = new CalculosDataValeAlimentacao();
$ano = isset($_POST['ano']) ? $_POST['ano'] : date('Y');
$mes = str_pad(isset($_POST['mes']) ? $_POST['mes'] : date('m'), 2, "0", STR_PAD_LEFT);
$primeiro_dia_mes = '01/' . str_pad($mes, '0', STR_PAD_RIGHT) . '/' . $ano;
$ultimo_dia_mes = $calendario->getUltimoDiaMes($mes, $ano, TRUE);
$feriados = $calendario->getFeriados($primeiro_dia_mes, $ultimo_dia_mes);
$dias_uteis = count($calendario->getDiasUteis($primeiro_dia_mes, $ultimo_dia_mes, $feriados));

if (isset($_POST['acao'])) {
    header('Content-type: text/html; charset=iso-8859-1');
    switch ($_POST['acao']) {
        case 'calcula_datas':
            echo json_encode(array('dias_uteis' => $dias_uteis, 'data_inicial' => $primeiro_dia_mes, 'data_final' => $ultimo_dia_mes));
            exit();
            break;
        case 'form1' :
            $dados['projeto'] = isset($_POST['projeto']) ? $_POST['projeto'] : NULL;
            $projeto = $dados['projeto'];
            $dados['ano'] = isset($_POST['ano']) ? $_POST['ano'] : NULL;
            $dados['mes'] = isset($_POST['mes']) ? $_POST['mes'] : NULL;
            $dados['data_inicial'] = isset($_POST['data_inicial']) ? $_POST['data_inicial'] : NULL;
            $data_inicial = $dados['data_inicial'];
            $dados['data_final'] = isset($_POST['data_final']) ? $_POST['data_final'] : NULL;
            $data_final = $dados['data_final'];
            $dados['dias_uteis'] = isset($_POST['dias_uteis']) ? $_POST['dias_uteis'] : NULL;
            $dias_uteis = $dados['dias_uteis'];
            $resp = relacao_clt_pedido($dados);
            $relacao_funcionarios = $resp['relacao_funcionarios'];
            $registros_descartados = $resp['registros_descartados'];
            $valor_total_pedido = $resp['valor_total_pedido'];
            include_once 'includes/table_1.php';
            exit();
            break;
        case 'fechar_pedido' :

            $dados['projeto'] = isset($_POST['id_projeto']) ? $_POST['id_projeto'] : NULL;
            $dados['ano'] = isset($_POST['ano_pedido']) ? $_POST['ano_pedido'] : NULL;
            $dados['mes'] = isset($_POST['mes_pedido']) ? $_POST['mes_pedido'] : NULL;
            $dados['data_inicial'] = isset($_POST['data_inicial_pedido']) ? $_POST['data_inicial_pedido'] : NULL;
            $dados['data_final'] = isset($_POST['data_final_pedido']) ? $_POST['data_final_pedido'] : NULL;
            $dados['dias_uteis'] = isset($_POST['dias_uteis_pedido']) ? $_POST['dias_uteis_pedido'] : NULL;
            $resp = relacao_clt_pedido($dados, TRUE);
            $relacao_pedidos = getPedidos();
            include_once 'includes/table_0.php';
            exit();
            break;
        case 'deletar_pedido' :
            $id_pedido = isset($_POST['id_pedido']) ? $_POST['id_pedido'] : NULL;
            $resp = mysql_query("UPDATE rh_va_pedido SET `status`='0' WHERE id_va_pedido=$id_pedido LIMIT 1");
            echo json_encode(array('status'=>$resp));
            exit();
            break;
        case 'visualizar_pedido' : 
            $id_pedido = isset($_POST['id_pedido']) ? $_POST['id_pedido'] : NULL;
            $relacao_funcionarios = get_relacao_clt_pedido($id_pedido);
            $relacao_clt_movimento = TRUE;
            include_once 'includes/table_1.php';
            exit();
            break;
        case 'arquivo_aelo' : 
            $id_pedido = isset($_POST['id_pedido']) ? $_POST['id_pedido'] : NULL;
            $relacao_funcionarios = get_relacao_clt_pedido($id_pedido);
            
            $arquivo = criar_csv_aelo($relacao_funcionarios);
            echo $arquivo;
//            $relacao_clt_movimento = TRUE;
//            include_once 'includes/table_aelo.php';
            exit();
            break;
        case 'form2' :
            $dados['projeto'] = isset($_POST['projeto']) ? $_POST['projeto'] : NULL;
            $dados['matricula'] = isset($_POST['matricula']) ? $_POST['matricula'] : NULL;
            $dados['cpf'] = isset($_POST['cpf']) ? $_POST['cpf'] : NULL;
            $dados['nome'] = isset($_POST['nome']) ? $_POST['nome'] : NULL;
            $dados['alimentacao'] = isset($_POST['alimentacao']) ? $_POST['alimentacao'] : NULL;
            $dados['mes'] = isset($_POST['mes']) ? $_POST['mes'] : NULL;
            $dados['ano'] = isset($_POST['ano']) ? $_POST['ano'] : NULL;

            $regiao = $dados['projeto']; // consertar

            $valores = getValoresDiarios($regiao);
            $arr = array();
            foreach ($valores as $v) {
                $arr[$v['id_va_valor_diario']] = $v['valor_diario'];
            }

            $relacao_tarifas_json = json_encode($arr);

            echo '<script> obj_relacao_tarifas = ' . $relacao_tarifas_json . '</script>';

            $relacao_funcionarios = getFuncionarios($dados);
            include_once 'includes/table_2.php';
            exit();
            break;
        case 'salva_2' :
            $dados = isset($_POST['dados']) ? $_POST['dados'] : NULL;
            $id_usuario = $usuario['id_funcionario'];
            $sql = "INSERT INTO rh_va_clt_valor_diario(`id_clt`,`id_valor_diario`,`criado_por`,`criado_em`,`status`) VALUES";
            $ids = array();
            foreach ($dados as $dado) {
                $ids[] = $dado['id'];
                $sql .= "('$dado[id]','$dado[id_valor]','$id_usuario', NOW(),1 ),";
                $sql_update_clt = "UPDATE rh_clt SET `vale_alimentacao`='$dado[solicitou_vale]' WHERE id_clt='$dado[id]' LIMIT 1;";
                mysql_query($sql_update_clt);
//                echo $sql_update_clt."<br>";
            }
            $alert['color'] = 'red';
            $alert['message'] = 'Erro em atualizar os dados!';
            $sql_update = "UPDATE rh_va_clt_valor_diario SET `status`='0', `atualizado_por`='$id_usuario' WHERE id_clt IN(" . implode(',', $ids) .");";
            if(mysql_query($sql_update)){
                $sql = substr($sql,0,-1);
                if(mysql_query($sql.';')){
                    $alert['color'] = 'green';
                    $alert['message'] = 'Dados atualizados com sucesso!';
                }
            }            
//            echo $sql_update."<br>";
//            echo $sql."<br>";
            include 'includes/box_message.php';
            exit();
            break;

        case 'form3' :
            $regiao = isset($_POST['regiao']) ? $_POST['regiao'] : NULL;
            $valor = isset($_POST['valor']) ? str_replace('R$ ', '', str_replace(',', '.', $_POST['valor'])) : NULL;
            $id_usuario = $usuario['id_funcionario'];
            if ($id_usuario) {
                $sql = "INSERT INTO rh_va_valor_diario(`regiao`,`valor_diario`,`criado_por`,`criado_em`,`status`) VALUES('$regiao','$valor','$id_usuario', NOW(),1 )";
//                echo $sql . "<br>";

                mysql_query($sql);
            }
            $relacao_tarifas = getValoresDiarios($regiao);
            include_once 'includes/table_3.php';
            exit();
            break;
        case 'del_3' :
            $id = isset($_POST['id']) ? $_POST['id'] : NULL;
            $id_usuario = $usuario['id_funcionario'];
            $sql = "UPDATE rh_va_valor_diario SET `status`='0', `atualizado_por`='$id_usuario' WHERE id_va_valor_diario='$id' LIMIT 1";
            mysql_query($sql);
            echo json_encode(array('status' => TRUE));
            exit();
            break;
        case 'get_table_3' :
            $regiao = isset($_POST['regiao']) ? $_POST['regiao'] : NULL;
            $relacao_tarifas = getValoresDiarios($regiao);
            include_once 'includes/table_3.php';
            exit();
            break;
        case 'salva_3' :
            $id = isset($_POST['id']) ? $_POST['id'] : NULL;
            $regiao = isset($_POST['regiao']) ? $_POST['regiao'] : NULL;
            $dados = isset($_POST['dados']) ? $_POST['dados'] : NULL;
            $id_usuario = $usuario['id_funcionario'];
            foreach ($dados as $dado) {
                $valor = isset($dado['valor']) ? str_replace('R$ ', '', str_replace(',', '.', $dado['valor'])) : NULL;
                $sql = "UPDATE rh_va_valor_diario SET `valor_diario`='$valor', `atualizado_por`='$id_usuario' WHERE id_va_valor_diario='$dado[id]' LIMIT 1";
                mysql_query($sql);
            }
            $alert['message'] = 'Dados atualizados com sucesso!';
            $relacao_tarifas = getValoresDiarios($regiao);
            include_once 'includes/table_3.php';
            exit();
            break;

        default:
            break;
    }
}

$relacao_pedidos = getPedidos();

$relacao_tarifas = getValoresDiarios($usuario['id_regiao']);

$dao = new DaoValeAlimentacao();


$projetos = $dao->getProjetos();

$arr_paginas = array('Lista de Pedidos', 'Gerar Pedido', 'Gerenciar Funcionários', 'Valores Diários');



$regioes = getRegioesFuncionario();
$meses = mesesArray();
$anos = array('2014' => '2014', '2015' => '2015', '2016' => '2016'); //$anos = anosArray();
?>
<html>
    <head>
        <title>:: Intranet :: VALE ALIMENTAÇÃO</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link href="../../favicon.ico" rel="shortcut icon"/>
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../favicon.ico" rel="shortcut icon" />
        <link href="css/style.css" rel="stylesheet" type="text/css" />
        <script src="js/jquery-1.9.1.min.js" type="text/javascript"></script>
        <!--<script src="js/jquery.maskedinput.js" type="text/javascript"></script>-->
        <script src="../../js/global.js" type="text/javascript"></script>
        <script type="text/javascript" src="../../js/jquery.price_format.2.0.min.js"></script>
        <script src="js/vale_alimentacao.js" type="text/javascript"></script>
    </head>
    <body class="novaintra" data-type="adm">
        <form method="post" id="page_controller">
            <input type="hidden" name="abashow" value="0" id="abashow" />
            <div id="content">
                <div id="geral">
                    <div id="topo">
                        <div class="conteudoTopo">
                            <div class="imgTopo">
                                <img src="../../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                            </div>
                            <h2>Administração de Alimentação</h2>
                        </div> 
                    </div>

                    <div id="conteudo">
                        <div class="colEsq">
                            <div class="titleEsq">Menu</div>
                            <ul id="nav">                                
                                <?php foreach ($arr_paginas as $key => $pagina) { ?>
                                    <li><a href="javascript:;" onclick="$('#abashow').val(<?= $key ?>)" data-item="<?= $key ?>" class="bt-menu <?= ($pagina_ativa == $key) ? ' aselected ' : ''; ?>"><?= $pagina; ?></a></li>
                                <?php } ?>
                            </ul>
                        </div>
                        <div class="colDir" id="teste1">
                            <div>processando os dados...</div>
                            <div style="background: url(../../imagens/carregando/loading.gif) no-repeat; width: 220px; height:19px;"></div>
                            <?php foreach ($arr_paginas as $key => $value) { ?>
                                <div id="item<?= $key ?>" style="display: none;" >
                                    <?php
                                    $file = 'includes/item_' . $key . '.php';
                                    if (is_file($file)) {
                                        include_once $file;
                                    } else {
                                        echo 'Erro 404. Página não encontrada!';
                                    }
                                    ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </body>
</html>