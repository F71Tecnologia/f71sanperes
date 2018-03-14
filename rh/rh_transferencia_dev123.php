<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a> ";
    exit;
}

include "../conn.php";
include "../wfunction.php";

/** FUNÇÕES DAS VALIDAÇÕES * */
function checa_clt_folha($id_clt = FALSE, $mes, $ano, $projeto = FALSE, $status = 3) {

    $mes = str_pad($mes, 2, '0', STR_PAD_LEFT);

    $w_clt = ($id_clt) ? " AND B.id_clt='$id_clt' " : '';
    $w_projeto = ($projeto) ? " AND A.id_projeto='$projeto' " : '';

    $sql_folha = "SELECT A.id_folha, B.id_clt, B.nome, A.`status` AS status_folha, B.`status` AS clt_folha, 
    IF(B.`status`=1,'CLT removido da folha',IF(B.`status`=2,'Folha aberta',IF(B.`status`=3,'Folha fechada','Não está em folha'))) AS nome_status
    FROM rh_folha AS A
    LEFT JOIN rh_folha_proc AS B ON(A.id_folha=B.id_folha) 
    WHERE A.mes='$mes' AND A.ano='$ano' $w_clt $w_projeto  AND B.`status`='$status' AND A.`status`='$status';";

//    exit($sql_folha);
    
    $result = mysql_query($sql_folha);

    $clt_folha = array();
    while ($row = mysql_fetch_array($result)) {
        $clt_folha[$row['id_clt']] = $row;
    }
    return $clt_folha;
}

function checa_folha($mes, $ano, $id_regiao, $status = 3) {
    $mes = str_pad($mes, 2, '0', STR_PAD_LEFT);
    $sql_folha = "SELECT A.projeto FROM rh_folha AS A
    WHERE A.mes='$mes' AND A.ano='$ano' AND A.`status`='$status' AND A.`regiao`='$id_regiao' GROUP BY A.projeto;";
    $result = mysql_query($sql_folha);
    $folha = array();
    while ($row = mysql_fetch_array($result)) {
        $folha[] = $row['projeto'];
    }
    return $folha;
}

/** FIM DAS VALIDAÇÕES * */

/** FUNÇÔES CARREGA DADOS DO FORM * */
function monta_select_projeto($id_regiao, $projeto, $mes = FALSE, $ano = FALSE, $encode = FALSE) {
    
    $mes = ($mes) ? $mes : date('m');
    $mes = str_pad($mes, 2, '0', STR_PAD_LEFT);            
    $ano = ($ano) ? $ano : date('Y');
    
    
    $sql_projetos = "SELECT (SELECT B.projeto FROM rh_folha AS B WHERE B.mes='$mes' AND B.ano='$ano' AND B.projeto=A.id_projeto AND B.`status`=3 LIMIT 1) AS folha_finalizada,   A.* FROM projeto AS A WHERE id_regiao=$id_regiao";
//    echo '<br>'.$sql_projetos.'<br>';
    $result_projetos = mysql_query($sql_projetos);
    
    
    $select = ($encode) ? utf8_encode('« Selecione »')  : '« Selecione »';
    $select = '<select name="projeto" id="projeto" onchange="carrega_tipopg()" ><option value="-1">'.$select.'</option>';
    while ($row = mysql_fetch_array($result_projetos)) {
        
        if($projeto==$row['id_projeto']) {
            $attr = ' selected="selected" ';
        }else{
            $attr = ($row['folha_finalizada']==$row['id_projeto']) ? ' disabled="disabled" ' : '';            
        }
        $row['nome'] = ($encode) ? utf8_encode($row['nome'])  : $row['nome'];
        $select .= '<option value="'.$row['id_projeto'].'" '.$attr.' >'.$row['id_projeto'] . ' - ' . $row['nome'].'</option>';
    }
//    exit($select);
    return $select.'</select>';
}

function get_cursos_by_regiao($id_regiao, $encode = FALSE) {
    $sql_cursos = "SELECT * FROM curso WHERE id_regiao=$id_regiao  AND status=1 AND status_reg=1;";
//    if($encode){exit($sql_cursos);}
    $result_cursos = mysql_query($sql_cursos);
    $cursosOpt = ($encode) ? array() : array('-1' => '« Selecione »');
    while ($row = mysql_fetch_array($result_cursos)) {
        $row['nome'] = ($encode) ? utf8_encode($row['nome']) : $row['nome'];
        $cursosOpt[$row['id_curso']] = $row['id_curso'] . ' - ' . $row['nome'] . ' - R$ ' . number_format($row['salario'], 2, ",", ".") . ' ( Regiao ' . $row['id_regiao'] . ') ';
    }
    return $cursosOpt;
}

function get_sindicato_by_regiao($id_regiao, $encode = FALSE) {
    $sql_cursos = "SELECT * FROM rhsindicato WHERE id_regiao=$id_regiao AND status=1;";
    $result_cursos = mysql_query($sql_cursos);
    $sindicatosOpt = ($encode) ? array() : array('-1' => '« Selecione »');
    while ($row = mysql_fetch_array($result_cursos)) {
        $row['nome'] = ($encode) ? utf8_encode($row['nome']) : $row['nome'];
        $sindicatosOpt[$row['id_sindicato']] = $row['id_sindicato'] . ' - ' . $row['nome'] . ' ( Regiao ' . $row['id_regiao'] . ') ';
    }
    return $sindicatosOpt;
}

function get_horarios_by_curso($id_curso, $id_regiao, $encode = FALSE) {
    $sql_cursos = "SELECT * FROM rh_horarios WHERE  funcao=$id_curso AND id_regiao=$id_regiao AND status_reg=1;";
    exit($sql_cursos);
    $result_cursos = mysql_query($sql_cursos);
    $horariosOpt = ($encode) ? array() : array('-1' => '« Selecione »');
    while ($row = mysql_fetch_array($result_cursos)) {
        $row['nome'] = ($encode) ? utf8_encode($row['nome']) : $row['nome'];
        $horariosOpt[$row['id_horario']] = $row['id_horario'] . " - " . $row['nome'] . " ({$row['entrada_1']}-> {$row['saida_1']} / {$row['entrada_2']} -> {$row['saida_2']})" . ' ( Funcao ' . $row['funcao'] . ') ';
    }
    return $horariosOpt;
}

function get_unidades_by_regiao($id_regiao, $encode = FALSE) {
    $sql_unidades = "SELECT * FROM unidade WHERE id_regiao=$id_regiao AND status_reg=1";
    $result_unidades = mysql_query($sql_unidades);
    $unidadesOpt = ($encode) ? array() : array('-1' => '« Selecione »');
    while ($row = mysql_fetch_array($result_unidades)) {
        $row['unidade'] = ($encode) ? utf8_encode($row['unidade']) : $row['unidade'];
        $unidadesOpt[$row['id_unidade']] = $row['id_unidade'] . " - " . $row['unidade'] . ' ( Regiao ' . $row['id_regiao'] . ') ';
    }
    return $unidadesOpt;
}

function get_banco_by_regiao($id_regiao, $encode = FALSE) {
    $sql_bancos = "SELECT * FROM bancos WHERE id_regiao=$id_regiao AND status_reg=1";
    $result_cursos = mysql_query($sql_bancos);
    $bancosOpt = ($encode) ? array() : array('-1' => '« Selecione »');
    while ($row = mysql_fetch_array($result_cursos)) {
        $row['nome'] = ($encode) ? utf8_encode($row['nome']) : $row['nome'];
        $bancosOpt[$row['id_banco']] = $row['id_banco'] . ' - ' . $row['nome'] . ' ( Regiao ' . $row['id_regiao'] . ') ';
    }
    return $bancosOpt;
}

function get_tipopg_by_projeto($id_regiao, $id_projeto=FALSE, $encode = FALSE) {
    
    $sql_projeto = ($id_projeto) ? ' id_projeto='.$id_projeto.' AND ' : '';
    
    $sql_tipopg = "SELECT * FROM tipopg WHERE $sql_projeto id_regiao=$id_regiao AND status_reg=1";
    $result_tipopg = mysql_query($sql_tipopg);
    $tiposPagamentosOpt = ($encode) ? array() : array('-1' => '« Selecione »');
    while ($row = mysql_fetch_array($result_tipopg)) {
        $row['tipopg'] = ($encode) ? utf8_encode($row['tipopg']) : $row['tipopg'];
        $tiposPagamentosOpt[$row['id_projeto']][$row['id_tipopg']] = $row['id_tipopg'] . ' - ' . $row['tipopg'] . ' ( Proj. ' . $row['id_projeto'] . ') ' . ' ( Reg. ' . $row['id_regiao'] . ') ';
    }
    
    return $tiposPagamentosOpt;
}

/** FIM CARREGA DADOS DO FORM * */
function get_clt_info($id_clt) {
    $sql_clt = "SELECT A.id_clt, A.nome,  A.cpf, B.id_master, J.nome AS nome_master, A.id_regiao, E.regiao AS nome_regiao, B.id_projeto, B.nome AS nome_projeto, 
            C.id_curso, C.nome AS nome_curso, C.salario, F.id_sindicato, F.nome AS nome_sindicato, 
            A.rh_horario AS id_horario, D.nome AS nome_horario, D.entrada_1, D.entrada_1, D.saida_1, D.entrada_2, D.saida_2, 
            H.id_nacional AS id_nacional_banco,H.id_banco, H.nome AS nome_banco, A.tipo_pagamento AS id_tipo_pagamento, G.tipopg AS nome_tipo_pagamento,
            A.id_unidade, I.unidade AS nome_unidade
            FROM rh_clt AS A
            LEFT JOIN projeto AS B ON (A.id_projeto=B.id_projeto)
            LEFT JOIN curso AS C ON (A.id_curso=C.id_curso)
            LEFT JOIN rh_horarios AS D ON (D.id_horario = A.rh_horario)
            LEFT JOIN regioes AS E ON (E.id_regiao = A.id_regiao)
            LEFT JOIN rhsindicato AS F ON (F.id_sindicato = A.rh_sindicato)
            LEFT JOIN tipopg AS G ON (G.id_tipopg = A.tipo_pagamento)
            LEFT JOIN bancos AS H ON (H.id_banco = A.banco)
            LEFT JOIN unidade AS I ON(A.id_unidade=I.id_unidade)
            LEFT JOIN master AS J ON(B.id_master=J.id_master)
            WHERE id_clt = '$id_clt';";
    $result_clt = mysql_query($sql_clt);
    return mysql_fetch_array($result_clt);
}

$str_erros = array('folha_fechada' => 'Não é posível efetuar a operação. O funcionário já está incluso em uma folha fechada nesta competência.',
    'folha_aberta' => 'Já existe uma folha aberta para o funcionário nesta competência. Você não poderá transferir de Região e Projeto.',
    'folha_projeto_fechada' => 'Existem folhas finalizadas');

$usuario = carregaUsuario();

$id_clt = isset($_REQUEST['clt']) ? $_REQUEST['clt'] : NULL;

$row_clt = get_clt_info($id_clt);

/** BLOQUEIOS * */
if (!empty($row_clt) != 1) {
    echo '<html><head><link href="../net1.css" rel="stylesheet" type="text/css" /></head><div class="message-box message-red">
                <p>Erro ao encontrar o funcionário. Acesse pelo link do sistema</p>
            </div><body>';
    exit();
}
if ($row_clt['id_master'] != $usuario['id_master']) {
    echo '<html><head><link href="../net1.css" rel="stylesheet" type="text/css" /></head><div class="message-box message-red">
                <p>A Região não confere com o usuário logado. Acesse pelo link do sistema</p>
            </div><body>';
    exit();
}
/** FIM DOS BLOQUEIOS * */
$acao = isset($_POST['acao']) ? isset($_POST['acao']) : FALSE;

if ($acao) {
    switch ($_POST['acao']) {
        case 'transferir_clt':
            $mes = isset($_POST['mes']) ? $_POST['mes'] : NULL;
            $ano = isset($_POST['ano']) ? $_POST['ano'] : NULL;
            $id_regiao = isset($_POST['regiao']) ? $_POST['regiao'] : NULL;
            $id_projeto = isset($_POST['projeto']) ? $_POST['projeto'] : NULL;
            $id_curso = isset($_POST['curso']) ? $_POST['curso'] : NULL;
            $id_horario = isset($_POST['horario']) ? $_POST['horario'] : NULL;
            $id_tipopg = isset($_POST['tipopg']) ? $_POST['tipopg'] : NULL;
            $id_banco = isset($_POST['banco']) ? $_POST['banco'] : NULL;
            $id_unidade = isset($_POST['unidade']) ? $_POST['unidade'] : NULL;
            $sindicato = isset($_POST['sindicato']) ? $_POST['sindicato'] : NULL;
            $motivo = isset($_POST['motivo']) ? $_POST['motivo'] : NULL;
            $acao = isset($_POST['acao']) ? $_POST['acao'] : NULL;
            $status_folha = isset($_POST['status_folha']) ? $_POST['status_folha'] : NULL;

            $data_proc = $ano . '-' . str_pad($mes, 2, '0', STR_PAD_LEFT) . '-01';

            //atualizando rh_clt
            $rh_clt_update = "UPDATE rh_clt SET id_regiao='$id_regiao', "
                    . "id_projeto='$id_projeto', "
                    . "id_curso='$id_curso', "
                    . "rh_horario='$id_horario', "
                    . "tipo_pagamento='$id_tipopg', "
                    . "banco='$id_banco', "
                    . "locacao='$id_unidade', "
                    . "id_unidade='$id_unidade', "
                    . "rh_sindicato='$sindicato';";

            //    atualizando dependentes
            $sql = "UPDATE dependentes SET id_projeto='$id_projeto', id_regiao='$id_regiao' where id_bolsista='$id_clt';";

            //gravando histórico em rh_transferencias
            $array_transferencia = array(
                "id_clt" => $row_clt['id_clt'],
                "id_regiao_de" => $row_clt['id_regiao'],
                "id_projeto_de" => $row_clt['id_projeto'],
                "id_curso_de" => $row_clt['id_curso'],
                "id_horario_de" => $row_clt['id_horario'],
                "id_tipo_pagamento_de" => $row_clt['id_tipopg'],
                "id_banco_de" => $row_clt['id_banco'],
                "unidade_de" => $row_clt['nome_unidade'],
                "id_unidade_de" => $row_clt['id_unidade'],
                "id_sindicato_de" => $row_clt['id_sindicato'],
                "id_regiao_para" => $id_regiao,
                "id_projeto_para" => $id_projeto,
                "id_curso_para" => $id_curso,
                "id_horario_para" => $id_horario,
                "id_tipo_pagamento_para" => $id_tipopg,
                "id_banco_para" => $id_banco,
                "unidade_para" => '$id_unidade', //nome unidade
                "id_unidade_para" => $id_unidade,
                "motivo" => $motivo,
                "data_proc" => $data_proc,
                "criado_em" => date('Y-m-d'),
                "id_usuario" => $row_clt['id_funcionario'],
                "id_sindicato_para" => $sindicato
            );

            $campos = array_keys($array_transferencia);
            $valores = array_values($array_transferencia);

            $sql_transferencia = "INSERT INTO rh_trasnferencias($campos) VALUES($valores)";

            break;
        case 'carrega_dados' :
//            sleep(3);
            //não permitir se o clt estiver em uma folha fechada na mesma competencia 
            //não permitir se o projeto tem foha fechada na competência

            $mes = isset($_POST['mes']) ? str_pad($_POST['mes'], 2, '0', STR_PAD_LEFT) : NULL;
            $ano = isset($_POST['ano']) ? $_POST['ano'] : NULL;
            $id_regiao = isset($_POST['regiao']) ? $_POST['regiao'] : NULL;
            $id_projeto = isset($_POST['projeto']) ? $_POST['projeto'] : NULL;
            $id_curso = isset($_POST['curso']) ? $_POST['curso'] : NULL;

            $carrega = TRUE;
            $msg = 'desbloqueado';

            // checa se o clt está em alguma folha fechada pela competência
            $clt_folha_fechada = checa_clt_folha($id_clt, $mes, $ano, FALSE);
            if (!empty($clt_folha_fechada)) {
                $msg = array('msg' => utf8_encode($str_erros['folha_fechada']), 'type' => 'erro_1'); //não faz nada
                $dados = 'bloqueio';
                $carrega = FALSE;
            } else {
                // checa se o clt está em alguma folha aberta pela competência
                $clt_folha_aberta = checa_clt_folha($id_clt, $mes, $ano, FALSE, 2);
                if (!empty($clt_folha_aberta)) {
                    $msg = array('msg' => utf8_encode($str_erros['folha_aberta']), 'type' => 'info_1'); // info_1 não troca região e projeto                     
                } else {
                    // checa se existe folha fechada
                    $projeto_folha_fechada = checa_folha($mes, $ano, $id_regiao, 3);
                    if (!empty($projeto_folha_fechada)) {
                        $msg = array('msg' => utf8_encode($str_erros['folha_projeto_fechada']), 'type' => 'info_2', 'projetos' => $projeto_folha_fechada); // info_2 só para projetos sem ter folhas fechadas
                        $dados['projeto'] = get_projeto_by_regiao($id_regiao, TRUE); // CONSERTAR!!... carrega os projetos eliminando projetos com folha fechada 
                    }
                }
            }



            if ($carrega) {
                $dados['curso'] = get_cursos_by_regiao($id_regiao, TRUE); // função
                $dados['sindicato'] = get_sindicato_by_regiao($id_regiao, TRUE); //sindicato
                $dados['horario'] = get_horarios_by_curso($id_curso, $id_regiao, TRUE); // horário
                $dados['unidade'] = get_unidades_by_regiao($id_regiao, TRUE); //unidade
                $dados['banco'] = get_banco_by_regiao($id_regiao, TRUE); //banco
                $dados['tipopg'] = get_tipopg_by_projeto($id_regiao, $id_projeto, TRUE); //tipo de pagamento
            }
            $status = !empty($msg) ? FALSE : TRUE;



            echo json_encode(array('status' => $status, 'resp' => $msg, 'dados' => $dados));

            exit();
            break;
        case 'checa_competencia' :
            
            $id_clt = isset($_POST['clt']) ? $_POST['clt'] : NULL;
            $mes = isset($_POST['mes']) ? str_pad($_POST['mes'], 2, '0', STR_PAD_LEFT) : NULL;
            $ano = isset($_POST['ano']) ? $_POST['ano'] : NULL;
            $id_regiao = isset($_POST['regiao']) ? $_POST['regiao'] : NULL;
            $id_projeto = isset($_POST['projeto']) ? $_POST['projeto'] : NULL;

//            // checa se o clt está em alguma folha fechada pela competência
            $clt_folha_fechada = checa_clt_folha($id_clt, $mes, $ano, FALSE);
            if (!empty($clt_folha_fechada)) {
                $msg = utf8_encode($str_erros['folha_fechada']); 
                $status = 1; //não faz transferência
            } else {
                // checa se o clt está em alguma folha aberta pela competência
                $clt_folha_aberta = checa_clt_folha($id_clt, $mes, $ano, FALSE, 2);
                
                if (!empty($clt_folha_aberta)) {
                    $msg = utf8_encode($str_erros['folha_aberta']);                      
                    $status = 2; // não troca região e projeto
                } else {
                    
                    // checa se existe folha fechada
//                    $projeto_folha_fechada = checa_folha($mes, $ano, $id_regiao, 3);
//                    if (!empty($projeto_folha_fechada)) {
//                        $msg = utf8_encode($str_erros['folha_projeto_fechada']);
////                                'projetos' => $projeto_folha_fechada);
//                        $status = 3; // Só para projetos sem ter folhas fechadas
//                    }
                }
            }
            $select_projeto = monta_select_projeto($id_regiao, $id_projeto, $mes, $ano, TRUE);
            
            
            echo json_encode(array('status' => $status, 'msg' => $msg, 'fechados'=>$folhas_fechadas,'select_projeto'=>$select_projeto,'teste'=>$id_regiao.', '.$id_projeto.', '.$mes.', '.$ano));

            exit();
            
            break;
        case 'change_regiao' :
//            $id_clt = isset($_POST['clt']) ? $_POST['clt'] : NULL;
            $id_regiao = isset($_POST['regiao']) ? $_POST['regiao'] : NULL;
            $id_projeto = isset($_POST['projeto']) ? $_POST['projeto'] : NULL;
            $mes = isset($_POST['mes']) ? str_pad($_POST['mes'], 2, '0', STR_PAD_LEFT) : NULL;
            $ano = isset($_POST['ano']) ? $_POST['ano'] : NULL;
            
            $dados['projetos'] = monta_select_projeto($id_regiao, $id_projeto, $mes, $ano, TRUE);            
            $dados['cursos'] = get_cursos_by_regiao($id_regiao, TRUE); // função
            $dados['sindicatos'] = get_sindicato_by_regiao($id_regiao, TRUE); //sindicato
            $dados['horarios'] = get_horarios_by_curso($id_curso, $id_regiao, TRUE); // horário
            $dados['unidades'] = get_unidades_by_regiao($id_regiao, TRUE); //unidade
            $dados['bancos'] = get_banco_by_regiao($id_regiao, TRUE); //banco
            $dados['projetos_tipopgs'] = get_tipopg_by_projeto($id_regiao, FALSE, TRUE); //tipo de pagamento
            
            echo json_encode($dados);
            exit();
            break;
        case 'change_projeto' :
            $id_regiao = isset($_POST['regiao']) ? $_POST['regiao'] : NULL;
            $id_projeto = isset($_POST['projeto']) ? $_POST['projeto'] : NULL;
            $dados['projetos_tipopgs'] = get_tipopg_by_projeto($id_regiao, $id_projeto, TRUE); //tipo de pagamento
            echo json_encode($dados);
            exit();
            break;
        case 'change_funcao' : 
            $id_curso = isset($_POST['curso']) ? $_POST['curso'] : NULL;
            $id_regiao = isset($_POST['regiao']) ? $_POST['regiao'] : NULL;
            $horarios = get_horarios_by_curso($id_curso, $id_regiao, TRUE);
            echo json_encode(array('horarios'=>$horarios));
            exit();
            break;


        default:
            break;
    }
}

$regioesOpt = getRegioes();
$cursosOpt = get_cursos_by_regiao($row_clt['id_regiao']);
$sindicatosOpt = get_sindicato_by_regiao($row_clt['id_regiao']);
$horariosOpt = get_horarios_by_curso($row_clt['id_curso'], $row_clt['id_regiao']);
$unidadesOpt = get_unidades_by_regiao($row_clt['id_regiao']);
$bancosOpt = get_banco_by_regiao($row_clt['id_regiao']);
$tiposPagamentosOpt = get_tipopg_by_projeto($row_clt['id_regiao'], $row_clt['id_projeto']);


$meses = mesesArray();
$anos = anosArray();
$mesSel = isset($_REQUEST['mes']) ? $_REQUEST['mes'] : (date('m') - 1);
$anoSel = isset($_REQUEST['ano']) ? $_REQUEST['ano'] : date('Y');
$regiaoSel = isset($_REQUEST['regiao']) ? $_REQUEST['regiao'] : $row_clt['id_regiao'];
$checa_clt_folha_fechada = checa_clt_folha($id_clt, $mesSel, $anoSel, FALSE);
//$checa_clt_folha = array(); // fake
$checa_clt_folha_aberta = checa_clt_folha($id_clt, $mesSel, $anoSel, FALSE, 2);
//$checa_clt_folha_aberta = array(1); // fake
$select_projeto = monta_select_projeto($row_clt['id_regiao'],$row_clt['id_projeto'],$mesSel);

if (!empty($checa_clt_folha_fechada)) {
    $bloqueio_form = ' disabled="disabled" ';
} else {
    $bloqueio_form = '';
}
$bloqueio_form_2 = $bloqueio_form;

if (!empty($checa_clt_folha_aberta)) {
    $bloqueio_form_2 = ' disabled="disabled" ';
}
?>
<html>
    <head>
        <title>:: Intranet :: RH - Transferência de Unidade</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt.js" type="text/javascript"></script>

        <script src="../js/global.js" type="text/javascript"></script>

        <script>
            $(function() {
                
                $('#curso').change(function(){
                    var id_curso = $(this).val();
                    var id_regiao = $('#regiao').val();
                    $('#horario').attr('disabled','disabled');
                    $.post(window.location,{regiao:id_regiao, curso:id_curso,acao:'change_funcao'},  function(data){
                        var option = '<option value="-1" >« Selecione »</option>';
                        for(i in data.horarios){
                            attr = ($('#horario_atual').val()==i) ? ' selected="selected" ' : '';
                            option += '<option value="'+i+'" '+attr+' >'+data.horarios[i]+'</option>';
                        }
                        $('#horario').html(option);
                        $('#horario').removeAttr('disabled');
                    },'json');
                });
                
                jQuery.fn.customAction = function(settings,callback) {

//
//                    $('#transferir').attr('disabled', 'disabled');
//                    $('select').attr('disabled', 'disabled');
//                    $('textarea').attr('disabled', 'disabled');
//                    $('#msg').html('');
//
//
                    return this.each(function() {
                        $(this).change(function() {
                            
                            $('#msg').html('');
                            
                            var projeto_post = $('#projeto').val();
                            var curso_post = $('#curso').val();
                            
                            if($('#regiao').val()==$('#regiao_atual').val()){
                                projeto_post = $('#projeto_atual').val();
                            }
                            
                            
                            var config = {
                                acao : false,
                                mes : $('#mes').val(),
                                ano : $('#ano').val(),
                                regiao : $('#regiao').val(),
                                projeto : projeto_post,
                                curso : curso_post,
                                sindicato : $('#sindicato').val(),
                                horario : $('#horario').val(),
                                unidade : $('#unidade').val(),
                                banco : $('#banco').val(),
                                tipopg : $('#tipopg').val(),
                                motivo : $('#motivo').val(),
                                clt : $('#clt').val()
                            }
                            if (settings) {
                                $.extend(config, settings);
                            }
                            
                            writeMsg = function(data){
                                var cores = {'erro': 'red', 'info': 'yellow','ok':'green'};
                                var tipo = data.resp['type'].split('_');
                                var msg = '<div class="message-box message-' + cores[tipo[0]] + ' msg_transferencia" ><p>' + data.resp['type'] + ': ' + data.resp['msg'] + '</p></div>';
                                $('#msg').append(msg);
                            }
                            
//
//                            var $selecao = $(this);
//                            
                            $.post(window.location, config, function(data) {
                                
//                                var cores = {'erro': 'red', 'info': 'yellow','ok':'green'};
//                                var tipo = data.resp['type'].split('_');
                                
//                                if(tipo[0]!='ok'){
//                                    var msg = '<div class="message-box message-' + cores[tipo[0]] + ' msg_transferencia" ><p>' + data.resp['type'] + ': ' + data.resp['msg'] + '</p></div>';
//                                    $('#msg').append(msg);
//                                }

                                return callback(data);
//
//                                $('p').find('select').each(function(i) {
//
//                                    $this = $(this);
//
//                                    if (data.dados != 'bloqueio') {
//                                        if (typeof (data.dados[$this.attr('id')]) == 'undefined' || data.dados[$this.attr('id')] == null) {
//                                        } else {
//                                            if ($this.attr('id') != $selecao.attr('id')) {
//
//                                                var opt = '';
//                                                for (c in data.dados[$this.attr('id')]) {
//
//                                                    var selected = '';
//
//                                                    if (c == $('#' + $this.attr('id') + '_atual').val()) {
//                                                        selected = ' selected="selected" ';
//                                                    }
//
//                                                    opt += '<option value="' + c + '" ' + selected + ' >' + data.dados[$this.attr('id')][c] + '</option>';
//                                                }
//                                                $('#' + $this.attr('id')).html('<option value="-1">« Selecione »</option>' + opt);
//                                            }
//                                        }
//                                    }
//                                });
//
//                                fn_callback(data['resp']);
//
                            }, 'json');
                        });
                    });
                };

                $('.competencia').customAction({acao:'checa_competencia'}, 
                                        function(data){
                                        
                                                $('#projeto').remove();
                                                $('#box_projeto').append(data.select_projeto);
                                                
                                                $('#transferir').removeAttr('disabled');
                                                $('select').removeAttr('disabled');
                                                $('textarea').removeAttr('disabled');
                                                $('option').removeAttr('disabled');
                                                
                                                
                                                
                                                if(data.status==1){
                                                    var msg = '<div class="message-box message-red msg_transferencia" ><p>' + data.msg + '</p></div>';
                                                    $('#msg').append(msg);

                                                    $('#transferir').attr('disabled', 'disabled');
                                                    $('select').attr('disabled', 'disabled');
                                                    $('textarea').attr('disabled', 'disabled');
                                                    
                                                }else if(data.status==2){
                                                    var msg = '<div class="message-box message-yellow msg_transferencia" ><p>' + data.msg + '</p></div>';
                                                    $('#msg').append(msg);
                                                    
                                                    $('#regiao').attr('disabled', 'disabled');
                                                    $('#projeto').attr('disabled', 'disabled');
                                                }else if(data.status==3){
                                                    var msg = '<div class="message-box message-yellow msg_transferencia" ><p>' + data.msg + '</p></div>';
                                                    $('#msg').append(msg);
                                                    
                                                    $('#transferir').removeAttr('disabled');
                                                    $('select').removeAttr('disabled');
                                                    $('textarea').removeAttr('disabled');
                                                    $('.competencia').removeAttr('disabled');
                                                    
                                                    for(i in data.fechados){
                                                        $('#projeto option[value='+data.fechados[i]+']').attr('disabled','disabled');
                                                    }
                                                    
                                                }else{
                                                    
                                                    $('#transferir').removeAttr('disabled');
                                                    $('select').removeAttr('disabled');
                                                    $('textarea').removeAttr('disabled');
                                                    $('.competencia').removeAttr('disabled');
                                                }
                                                console.log(data);
                                                
                                                $('.competencia').removeAttr('disabled');
                                            });
                     $('#regiao').customAction({acao:'change_regiao'}, 
                                        function(data){
                                            $('#projeto').remove();
                                            $('#box_projeto').append(data.projetos);
                                            
                                            var option = '<option value="-1" >« Selecione »</option>';
                                            for(i in data.cursos){
                                                attr = ($('#curso_atual').val()==i) ? ' selected="selected" ' : '';
                                                option += '<option value="'+i+'" '+attr+' >'+data.cursos[i]+'</option>';
                                            }
                                            $('#curso').html(option);
                                            
                                            var option = '<option value="-1" >« Selecione »</option>';
                                            for(i in data.sindicatos){
                                                attr = ($('#sindicato_atual').val()==i) ? ' selected="selected" ' : '';
                                                option += '<option value="'+i+'" '+attr+' >'+data.sindicatos[i]+'</option>';
                                            }
                                            $('#sindicato').html(option);
                                            
                                            
//                                            for(i in data.horarios){
//                                            }
                                            
                                            var option = '<option value="-1" >« Selecione »</option>';
                                            for(i in data.unidades){
                                                attr = ($('#unidade_atual').val()==i) ? ' selected="selected" ' : '';
                                                option += '<option value="'+i+'" '+attr+' >'+data.unidades[i]+'</option>';
                                            }
                                            $('#unidade').html(option);
                                            
                                            var option = '<option value="-1" >« Selecione »</option>';
                                            for(i in data.bancos){
                                                attr = ($('#banco_atual').val()==i) ? ' selected="selected" ' : '';
                                                option += '<option value="'+i+'" '+attr+' >'+data.bancos[i]+'</option>';
                                            }
                                            $('#banco').html(option);
                                            
                                            
                                           var id_projeto = $('#projeto').val();
                                           var option = '<option value="-1" >« Selecione »</option>';                                           
                                            for(i in data.projetos_tipopgs){
                                                for(y in data.projetos_tipopgs[i]){
                                                    if(id_projeto==i){
                                                        var attr = ($('#tipopg_atual').val()==y) ? ' selected="selected" ' : '';
                                                        option += '<option value="'+y+'" '+attr+' >'+data.projetos_tipopgs[i][y]+'</option>';
                                                    }
                                                }                                           
                                            }
                                            $('#tipopg').html(option);
                                        }); 
                         
            });
            function carrega_tipopg(){
            
                $.post(window.location,{acao:'change_projeto',regiao:$('#regiao').val(), projeto:$('#projeto').val()},function(data){
                    var id_projeto = $('#projeto').val();
                    var option = '<option value="-1" >« Selecione »</option>';                                           
                     for(i in data.projetos_tipopgs){
                         for(y in data.projetos_tipopgs[i]){
                             if(id_projeto==i){
                                 var attr = ($('#tipopg_atual').val()==y) ? ' selected="selected" ' : '';
                                 option += '<option value="'+y+'" '+attr+' >'+data.projetos_tipopgs[i][y]+'</option>';
                             }
                         }                                           
                     }
                     $('#tipopg').html(option);
                },'json');
            }
        </script>

    </head>
    <body id="page-rh-trans" class="novaintra">
        <div id="content">
            <form action="" method="post" name="form1" id="form1">
                <div id="head">
                    <img src="../imagens/logomaster<?php echo $master; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>RH - Transferência de funcionário</h2>
                    </div>
                    <div class="fright"> <?php include('../reportar_erro.php'); ?></div> 
                </div>
                <br/>


                <fieldset class="border-red">
                    <legend>Informações Atuais do Funcionário</legend>
                    <p><label class="first">Nome:</label> <?php echo $row_clt['nome'] ?></p>
                    <p><label class="first">CPF:</label> <?php echo $row_clt['cpf'] ?></p>
                    <p><label class="first">Região:</label> <?php echo $row_clt['id_regiao'] . ' - ' . $row_clt['nome_regiao'] ?></p>
                    <p><label class="first">Projeto:</label> <?php echo $row_clt['id_projeto'] . ' - ' . $row_clt['nome_projeto'] ?></p>
                    <p><label class="first">Função:</label> <?php echo $row_clt['id_curso'] . " - " . $row_clt['nome_curso'] . " - R$ " . number_format($row_clt['salario'], 2, ",", ".") ?></p>
                    <p><label class="first">Sindicato:</label> <?php echo $row_clt['id_sindicato'] . ' - ' . $row_clt['nome_sindicato']; ?></p>
                    <p><label class="first">Horário:</label> <?php echo $row_clt['id_horario'] . " - " . $row_clt['nome_horario'] . " ({$row_clt['entrada_1']} -> {$row_clt['saida_1']} / {$row_clt['entrada_2']} -> {$row_clt['saida_2']})" ?></p>
                    <p><label class="first">Unidade:</label> <?php echo $row_clt['id_unidade'] . " - " . $row_clt['nome_unidade'] ?></p>
                    <p><label class="first">Banco:</label> <?php echo $row_clt['id_banco'].' - '.$row_clt['nome_banco'] ?></p>
                    <p><label class="first">Tipo de Pagamento:</label> <?php echo $row_clt['id_tipo_pagamento'] . ' - ' . $row_clt['nome_tipo_pagamento'] ?></p>
                </fieldset>
                <br/>
                <fieldset class="border-blue">
                    <legend>Informações para a Transferência</legend>
                    <p><label class="first">Competência:</label> 

<?php echo montaSelect($meses, $mesSel, "id='mes' name='mes' class='validate[required,custom[select]] competencia'") ?> 
<?php echo montaSelect($anos, $anoSel, "id='ano' name='ano' class='validate[required,custom[select]] competencia'") ?> <span>Competência da folha que entrará a diferença salarial</span> </p>                    
                    <p><label class="first">Região:</label> <?php echo montaSelect($regioesOpt, $regiaoSel, "id='regiao' name='regiao' class='validate[required,custom[select]] bloqueio2 carrega_dados' $bloqueio_form_2") ?></p>
                    <p id="box_projeto"><label class="first">Projeto:</label> <?php echo $select_projeto; ?></p>


                    <p><label class="first">Função:</label> <?php echo montaSelect($cursosOpt, $row_clt['id_curso'], "id='curso' name='curso' class='validate[required,custom[select]] bloqueio1 carrega_dados' $bloqueio_form ") ?></p>
                    <p><label class="first">Sindicato:</label> <?php echo montaSelect($sindicatosOpt, $row_clt['id_sindicato'], "id='sindicato' name='sindicato' class='validate[required,custom[select]] bloqueio1' $bloqueio_form ") ?></p>
                    <p><label class="first">Horário:</label> <?php echo montaSelect($horariosOpt, $row_clt['id_horario'], "id='horario' name='horario' class='validate[required,custom[select]] bloqueio1' $bloqueio_form ") ?></p>
                    <p><label class="first">Unidade:</label> <?php echo montaSelect($unidadesOpt, $row_clt['id_unidade'], "id='unidade' name='unidade' class='validate[required,custom[select]] bloqueio1' $bloqueio_form ") ?></p>
                    <p><label class="first">Banco:</label> <?php echo montaSelect($bancosOpt, $row_clt['id_banco'], "id='banco' name='banco' class='bloqueio1' $bloqueio_form ") ?></p>
                    <p><label class="first">Tipo de Pagamento:</label> <?php echo montaSelect($tiposPagamentosOpt[$row_clt['id_projeto']], $row_clt['id_tipo_pagamento'], "id='tipopg' name='tipopg' class='validate[required,custom[select]] bloqueio1' $bloqueio_form ") ?></p>
                    <p><label class="first">Motivo:</label> <textarea id="motivo" name="motivo" cols="25" rows="5" class="bloqueio1" <?= $bloqueio_form; ?> ></textarea></p>
                </fieldset>
                <br/>
                <div id="msg">
<?php if (!empty($checa_clt_folha_aberta)) { ?>
                        <div class="message-box message-yellow msg_transferencia">
                            <p><?= $str_erros['folha_aberta']; ?></p>
                        </div>
<?php } ?>
<?php if (!empty($checa_clt_folha_fechada)) { ?>
                        <div class="message-box message-red msg_transferencia">
                            <p><?= $str_erros['folha_fechada']; ?></p>
                        </div>
<?php } ?>
                </div>
                <br/>
                <p class="controls">
                    <input type="hidden" name="regiao_atual" id="regiao_atual" value="<?= $row_clt['id_regiao']; ?>" />
                    <input type="hidden" name="projeto_atual" id="projeto_atual" value="<?= $row_clt['id_projeto']; ?>" />
                    <input type="hidden" name="curso_atual" id="curso_atual" value="<?= $row_clt['id_curso']; ?>" />
                    <input type="hidden" name="sindicato_atual" id="sindicato_atual" value="<?= $row_clt['id_sindicato']; ?>" />
                    <input type="hidden" name="horario_atual" id="horario_atual" value="<?= $row_clt['id_horario']; ?>" />                    
                    <input type="hidden" name="unidade_atual" id="unidade_atual" value="<?= $row_clt['id_unidade']; ?>" />
                    <input type="hidden" name="banco_atual" id="banco_atual" value="<?= $row_clt['id_banco']; ?>" />                    
                    <input type="hidden" name="tipopg_atual" id="tipopg_atual" value="<?= $row_clt['id_tipo_pagamento']; ?>" />
                    <input type="hidden" name="clt" id="clt" value="<?= $row_clt['id_clt']; ?>" />
                    <input type="hidden" name="acao" value="transferir_clt" />
                    <input type="submit" class="button" id="transferir" <?= (!empty($checa_clt_folha_fechada)) ? ' disabled="disabled" ' : ' '; ?> /> 
                </p>

            </form>
        </div>
    </body>
</html>