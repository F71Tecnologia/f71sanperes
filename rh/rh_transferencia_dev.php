<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a> ";
    exit;
}

include "../conn.php";
include "../wfunction.php";

$usuario = carregaUsuario();
$user = $usuario['id_funcionario'];
$regiao = $usuario['id_regiao'];
$master = $usuario['id_master'];
$id_clt = $_REQUEST['clt'];
$tela = 1;
$bloqueio = false;

$dtnow = date("Y-m-d");
$meses = @mesesArray();
$anos = anosArray(null, null, array("-1" => "« Selecione »"));
$mesSelected = date("m", strtotime($dtnow . " -1 month"));
$anoSelected = date("Y", strtotime($dtnow . " -1 month"));

/* CARREGA OS HORARIOS VIA AJAX, RETORNA UM JSON */
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "carregahorario") {

    $return['status'] = 1;

    $sql_horarios = "SELECT id_horario,nome,
                                date_format(entrada_1, '%H:%i:%s') as entrada1, 
                                date_format(saida_1, '%H:%i:%s') as saida_1, 
                                date_format(entrada_2, '%H:%i:%s') as entrada_2, 
                                date_format(saida_2, '%H:%i:%s') as saida_2
                                FROM rh_horarios WHERE funcao = {$_REQUEST['funcao']} AND status_reg=1 ORDER BY nome";

    $qrhorario = mysql_query($sql_horarios);
    $num_rowsU = mysql_num_rows($qrhorario);
    $unidades = array();
    if ($num_rowsU > 0) {
        while ($row = mysql_fetch_assoc($qrhorario)) {
            $unidades[$row['id_horario']] = $row['id_horario'] . " - " . utf8_encode($row['nome']) . " ({$row['entrada1']} - {$row['saida_1']} - {$row['entrada_2']} - {$row['saida_2']})";
        }
    } else {
        $unidades["-1"] = utf8_encode("nenhum horário encontrado");
    }

    $return['options'] = $unidades;

    echo json_encode($return);
    exit;
}

/* CARREGA OS PROJETOS VIA AJAX, RETORNA UM JSON */
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "carregaprojetos") {
    $return['status'] = 1;
    $reProjetos = montaQuery("projeto", "id_projeto,nome", "id_regiao={$_REQUEST['regiao']}", "nome", null);
    if (count($reProjetos) > 0) {
        foreach ($reProjetos as $pro) {
            $projetos[$pro["id_projeto"]] = $pro["id_projeto"] . " - " . utf8_encode($pro["nome"]);
        }
    } else {
        $projetos["-1"] = utf8_encode("nenhum projeto encontrado");
    }

    $return['options'] = $projetos;

    echo json_encode($return);
    exit;
}

/* CARREGA AS FUNÇÕES E UNIDADES VIA AJAX, RETORNA UM JSON */
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "carregafuncao") {
    //FUNÇÃO
    $qr_bancos = mysql_query("SELECT id_curso,nome,salario FROM curso WHERE campo3 = {$_REQUEST['projeto']} AND status_reg=1 ORDER BY nome");
    $num_rows = mysql_num_rows($qr_bancos);
    $cursos = array();
    if ($num_rows > 0) {
        $return['stfun'] = 1;
        while ($row = mysql_fetch_assoc($qr_bancos)) {
            $cursos[$row['id_curso']] = $row['id_curso'] . " - " . utf8_encode($row['nome']) . " - R$ " . number_format($row['salario'], 2, ",", ".");
        }
    } else {
        $return['stfun'] = 0;
        $cursos["-1"] = "nenhum curso encontrado";
    }

    //UNIDADE
    $qrUnidade = mysql_query("SELECT id_unidade,unidade FROM unidade WHERE campo1 = '{$_REQUEST['projeto']}' ORDER BY unidade");
    $num_rowsU = mysql_num_rows($qrUnidade);
    $unidades = array();
    if ($num_rowsU > 0) {
        $return['stunid'] = 1;
        while ($row = mysql_fetch_assoc($qrUnidade)) {
            $unidades[utf8_encode($row['unidade'])] = utf8_encode($row['unidade']);
        }
    } else {
        $return['stunid'] = 0;
        $unidades["-1"] = "nenhum curso encontrado";
    }

    //BANCOS
    $qrBancos = mysql_query("SELECT id_banco,nome,agencia,conta FROM bancos WHERE id_projeto = '{$_REQUEST['projeto']}'");
    $bancos = array();
    if (mysql_num_rows($qrBancos) > 0) {
        $return['stbanc'] = 1;
        while ($row = mysql_fetch_assoc($qrBancos)) {
            $bancos[$row['id_banco']] = $row['id_banco'] . " - " . utf8_encode($row['nome']) . " (AG: {$row['agencia']}, CC: {$row['conta']})";
        }
    } else {
        $return['stbanc'] = 0;
        $bancos["-1"] = "nenhum banco encontrado";
    }

    //TIPO PG
    $qrTipoPg = mysql_query("SELECT id_tipopg,tipopg FROM tipopg WHERE id_projeto = '{$_REQUEST['projeto']}'");
    $pagamentos = array();
    if (mysql_num_rows($qrTipoPg) > 0) {
        $return['sttppg'] = 1;
        while ($row = mysql_fetch_assoc($qrTipoPg)) {
            $pagamentos[$row['id_tipopg']] = utf8_encode($row['tipopg']);
        }
    } else {
        $return['sttppg'] = 0;
        $pagamentos["-1"] = "nenhum tipo de pagamento encontrado";
    }


    //SINDICATOS
    $qrSindicato = mysql_query("SELECT id_sindicato, nome FROM rhsindicato WHERE id_regiao = '{$_REQUEST['id_regiao']}'");
    $sindicatos = array();
    if (mysql_num_rows($qrSindicato) > 0) {
        $return['stsindicato'] = 1;
        while ($row = mysql_fetch_assoc($qrSindicato)) {
            $sindicatos[$row['id_sindicato']] = $row['id_sindicato'] . " - " . utf8_encode($row['nome']);
        }
    } else {
        $return['stsindicato'] = 0;
        $sindicatos["-1"] = "nenhum banco encontrado";
    }

    $return['funcao'] = $cursos;
    $return['unidade'] = $unidades;
    $return['bancos'] = $bancos;
    $return['pagamentos'] = $pagamentos;
    $return['sindicatos'] = $sindicatos;

    echo json_encode($return);
    exit;
}

$qr = "SELECT 
        A.nome,A.cpf,A.id_regiao,A.id_projeto,A.id_curso,A.id_regiao,A.foto,A.locacao,A.tipo_pagamento,A.banco,A.rh_sindicato,
        B.nome as projeto,
        C.nome as funcao, C.salario,
        D.id_horario,D.nome as horario, G.tipopg as tipo_pg,
        date_format(D.entrada_1, '%H:%i:%s') as entrada1, 
        date_format(D.saida_1, '%H:%i:%s') as saida_1, 
        date_format(D.entrada_2, '%H:%i:%s') as entrada_2, 
        date_format(D.saida_2, '%H:%i:%s') as saida_2,
        E.regiao,
        F.nome as nome_sindicato,
        H.nome as nome_banco
        FROM rh_clt AS A
        LEFT JOIN projeto AS B ON (A.id_projeto=B.id_projeto)
        LEFT JOIN curso AS C ON (A.id_curso=C.id_curso)
        LEFT JOIN rh_horarios AS D ON (D.funcao = A.id_curso)
        LEFT JOIN regioes AS E ON (E.id_regiao = A.id_regiao)
        LEFT JOIN rhsindicato as F ON (F.id_sindicato = A.rh_sindicato)
        LEFT JOIN tipopg as G ON (G.id_tipopg = A.tipo_pagamento)
        LEFT JOIN bancos as H ON (H.id_banco = A.banco)
        WHERE id_clt = {$id_clt}";
        
$result = mysql_query($qr) or die(mysql_error());
if (mysql_num_rows($result) == 0) {
    echo "Erro! Funcionário não encontrado.";
    exit;
}
$clt = mysql_fetch_assoc($result);

//Regiões
$reRegioes = montaQuery("regioes", "id_regiao,regiao", "id_master={$master}", "regiao");
$regioes = array("-1" => "« Selecione »");
foreach ($reRegioes as $reg) {
    $regioes[$reg["id_regiao"]] = $reg["id_regiao"] . " - " . $reg["regiao"];
}

//Projeto
$reProjetos = montaQuery("projeto", "id_projeto,nome", "id_regiao={$clt['id_regiao']}", "id_projeto");
$projeto = array("-1" => "« Selecione »");
foreach ($reProjetos as $pro) {
    $projetosOpt[$pro["id_projeto"]] = $pro["id_projeto"] . " - " . $pro["nome"];
}

//CURSO
$reCursos = montaQuery("curso", "id_curso,nome", "campo3={$clt['id_projeto']}", "id_curso");
$cursosOpt = array("-1" => "« Selecione »");
foreach ($reCursos as $funcao) {
    $cursosOpt[$funcao["id_curso"]] = $funcao["id_curso"] . " - " . $funcao["nome"];
}

//SINDICATO
$reSindicato = montaQuery("rhsindicato", "*", "id_regiao={$clt['id_regiao']}", 'id_sindicato');
$sindicatoOpt = array("-1" => "« Selecione »");
foreach ($reSindicato as $sind) {
    $sindicatoOpt[$sind["id_sindicato"]] = $sind["id_sindicato"] . " - " . $sind["nome"];
}

//horarios
$reHorarios = montaQuery("rh_horarios", "*", "funcao={$clt['id_curso']}", 'id_horario');
$horarioOpt = array("-1" => "« Selecione »");
foreach ($reHorarios as $hor) {
    $horarioOpt[$hor["id_horario"]] = $hor["id_horario"] . " - " . $hor["nome"] . ' (' . $hor['entrada_1'] . ' - ' . $hor['saida_1'] . ' - ' . $hor['entrada_2'] . ' - ' . $hor['saida_2'] . ')';
}
//
////horarios
//$reHorarios = montaQuery("rh_horarios","*","funcao={$clt['id_curso']}",'id_horario');
//$horarioOpt = array("-1"=>"« Selecione »");
//foreach ($reHorarios as $hor) {
//    $horarioOpt[$hor["id_horario"]] = $hor["id_horario"] . " - " . $hor["nome"].' ('.$hor['entrada_1'].' - '.$hor['saida_1'].' - '.$hor['entrada_2'].' - '.$hor['saida_2'].')';
//}
//UnidADE
$reUnidade = montaQuery("unidade", "*", "campo1={$clt['id_projeto']}", 'id_unidade');
$unidadeOpt = array("-1" => "« Selecione »");
foreach ($reUnidade as $uni) {
    $unidadeOpt[$uni["unidade"]] = $uni['unidade'];
}

//Banco
$reBanco = montaQuery("bancos", "*", "id_projeto={$clt['id_projeto']}", 'id_banco');
$bancoOpt = array("-1" => "« Selecione »");
foreach ($reBanco as $banc) {
    $bancoOpt[$banc["id_banco"]] = $banc['id_banco'] . ' - ' . $banc['nome'];
}

//Tipo de pagamento
$reTipo = montaQuery("tipopg", "*", "id_projeto={$clt['id_projeto']} AND id_regiao = '$clt[id_regiao]'");
$tipoOpt = array("-1" => "« Selecione »");
foreach ($reTipo as $tipo) {
    $tipoOpt[$tipo["id_tipopg"]] = $tipo['id_tipopg'] . ' - ' . $tipo['tipopg'];
}


/* APRONTANDO TODOS AS QUERYS DE TRANSFERENCIA */
if (isset($_REQUEST['transferir']) && $_REQUEST['transferir'] == "Transferir") {

    $tela = 2;
    //VÃO SELECIONAR O MES QUE VAI ENTRAR O NOVO SALARIO
    //POREM VOU GRAVAR O MES ANTERIOR, POIS O SISTEMA JA TRABALHA COM ESSE CALCULO
    $mes2d = sprintf("%02d", $_REQUEST['mes']);
    $d = "{$_REQUEST['ano']}-{$mes2d}-28";
    $data_mov = date("Y-m-d", strtotime($d . " -1 month"));
    $campos = array(
        "id_clt",
        "id_regiao_de",
        "id_projeto_de",
        "id_curso_de",
        "id_horario_de",
        "id_tipo_pagamento_de",
        "id_banco_de",
        "unidade_de",
        "id_regiao_para",
        "id_projeto_para",
        "id_curso_para",
        "id_horario_para",
        "id_tipo_pagamento_para",
        "id_banco_para",
        "unidade_para",
        "motivo",
        "data_proc",
        "criado_em",
        "id_usuario",
        "id_sindicato_de",
        "id_sindicato_para"
    );

    $valores = array(
        $id_clt,
        $clt['id_regiao'],
        $clt['id_projeto'],
        $clt['id_curso'],
        $clt['id_horario'],
        $clt['tipo_pagamento'],
        $clt['banco'],
        $clt['locacao'],
        $_REQUEST['regiao'],
        $_REQUEST['projeto'],
        $_REQUEST['curso'],
        $_REQUEST['horario'],
        $_REQUEST['tipopg'],
        $_REQUEST['banco'],
        $_REQUEST['unidade'],
        $_REQUEST['motivo'],
        $data_mov,
        date("Y-m-d H:i:s"),
        $user,
        $clt['id_sindicato'],
        $_REQUEST['sindicato']
    );

    $sql = "UPDATE dependentes SET id_projeto={$clt['id_projeto']}, id_regiao={$clt['id_regiao']}  where id_bolsista=" . $id_clt;
//    echo 'update: '.$sql;
    mysql_query($sql);

    ////VERIFICAÇões de folha
    $condicao = array(
        "regiao" => $clt['id_regiao'],
        "projeto" => $clt['id_projeto'],
        "mes" => date('m'),
        "ano" => date('Y'),
        "status" => 2
    );

    $rsFolha = montaQuery("rh_folha", "*", $condicao);

    //Se a folha estiver aberta, atualiza o campo id_curso e id_horario na folha
    $qr_verifica_folha = mysql_query("SELECT B.* FROM rh_folha as A
                                            INNER JOIN rh_folha_proc as B
                                            ON A.id_folha = B.id_folha
                                            WHERE A.mes = " . date('m') . " AND A.ano = '" . date('Y') . "' AND A.status = 2 AND B.status = 2 AND
                                            B.id_clt = $id_clt;");

    if (mysql_num_rows($qr_verifica_folha) != 0) {
        $row_folha = mysql_fetch_assoc($qr_verifica_folha);

        mysql_query("UPDATE rh_folha_proc SET id_curso = '$_REQUEST[curso]', id_horario = '$_REQUEST[horario]'  
                        WHERE id_folha_proc = '$row_folha[id_folha_proc]'
                        LIMIT 1");
    }
    /////////////

    if (count($rsFolha) > 0) {
        $ids = array();
        foreach ($rsFolha as $val) {
            $ids[] = $val['id_folha'];
        }

        $rsFolhaProc = montaQuery("rh_folha_proc", "*", "id_folha IN (" . implode(",", $ids) . ") AND id_clt = '{$id_clt}' AND status = 2");
    }

    if (($clt['id_regiao'] != $_REQUEST['regiao'] or $clt['id_projeto'] != $_REQUEST['projeto']) and count($rsFolhaProc) > 0) {
        echo '<p>O funcionário selecionado encontra-se em uma folha em aberto, para trocar de unidade é necessário removê-lo da folha para realizar a transferência!</p>';
        echo '<a href="rh_transferencia.php?clt=' . $id_clt . '"> VOLTAR </a>';
        exit;
    }

    sqlInsert("rh_transferencias", $campos, $valores);

    $updates = array(
        "id_regiao" => $_REQUEST['regiao'],
        "id_projeto" => $_REQUEST['projeto'],
        "id_curso" => $_REQUEST['curso'],
        "rh_horario" => $_REQUEST['horario'],
        "tipo_pagamento" => $_REQUEST['tipopg'],
        "banco" => $_REQUEST['banco'],
        "locacao" => $_REQUEST['unidade'],
        "rh_sindicato" => $_REQUEST['sindicato']
    );

    sqlUpdate("rh_clt", $updates, array("id_clt" => $id_clt));

    /* MUDAR O NOME DA FOTO */
    if ($clt['foto'] == '1') {
        $dir = dirname(dirname(__FILE__)) . "/fotosclt/";
        $nomeOld = $clt['id_regiao'] . "_" . $clt['id_projeto'] . "_" . $id_clt . '.gif';
        $nomeNovo = $_REQUEST['regiao'] . "_" . $_REQUEST['projeto'] . "_" . $id_clt . '.gif';
        rename($dir . $nomeOld, $dir . $nomeNovo);
    }
}

/* VERIFICANDO SE O FUNCIONÁRIO ESTÁ NA FOLHA DE PAGAMENTO */
//SELECT * FROM rh_folha WHERE regiao = 45 AND projeto = 3302 AND status = 2
$condicao = array(
    "regiao" => $regiao,
    "projeto" => $clt['id_projeto'],
    "mes" => date('m'),
    "ano" => date('Y'),
    "status" => 2
);

$rsFolha = montaQuery("rh_folha", "*", $condicao);

if (count($rsFolha) > 0) {
    $ids = array();
    foreach ($rsFolha as $val) {
        $ids[] = $val['id_folha'];
    }

    $rsFolhaProc = montaQuery("rh_folha_proc", "*", "id_folha IN (" . implode(",", $ids) . ") AND id_clt = '{$id_clt}' AND status = 2");

    if (count($rsFolhaProc) > 0) {
        $bloqueio = true;
    }
}



//$sql = "SELECT * FROM dependentes  where id_bolsista=" . $id_clt;
//echo "<br>".$sql."<br>";
//while ($res = mysql_fetch_array($row_dependentes)) {
//    echo '<pre>';
//    print_r($res);
//    echo '</pre>';
//}
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
                $("#form1").validationEngine();

                $("#regiao").change(function() {
                    var $this = $(this);
                    if ($this.val() != "-1") {
                        $.post('rh_transferencia.php', {regiao: $this.val(), method: "carregaprojetos"}, function(data) {
                            if (data.status == 1) {
                                var opcao = "<option value='-1'>« Selecione »</option>\n";

                                $("#curso").html("<option value='-1'>« Selecione o Projeto »</option>");
                                $("#horario").html("<option value='-1'>« Selecione o Função »</option>");
                                $("#unidade").html("<option value='-1'>« Selecione o Projeto »</option>");

                                for (var i in data.options) {
                                    opcao += "<option value='" + i + "'>" + data.options[i] + "</option>\n";
                                }
                                $("#projeto").html(opcao);
                            }
                        }, "json");
                    }
                });

                $("#projeto").change(function() {
                    var $this = $(this);
                    if ($this.val() != "-1") {
                        $.post('rh_transferencia.php', {projeto: $this.val(), id_regiao: $('#regiao').val(), method: "carregafuncao"}, function(data) {
                            if (data.stfun == 1) {
                                var opcao = "<option value='-1'>« Selecione »</option>\n";
                                var selected = "";
                                for (var i in data.funcao) {
                                    selected = "";
                                    if (i == $("#cursoSel").val()) {
                                        selected = "selected=\"selected\" ";
                                    }
                                    opcao += "<option value='" + i + "' " + selected + ">" + data.funcao[i] + "</option>\n";
                                }
                                $("#curso").html(opcao);
                            }
                            if (data.stunid == 1) {
                                var unid = "<option value='-1'>« Selecione »</option>\n";
                                for (var i in data.unidade) {
                                    unid += "<option value='" + i + "' " + selected + ">" + data.unidade[i] + "</option>\n";
                                }
                                $("#unidade").html(unid);
                            }
                            if (data.stbanc == 1) {
                                var unid = "<option value='-1'>« Selecione »</option>\n";
                                for (var i in data.bancos) {
                                    unid += "<option value='" + i + "' " + selected + ">" + data.bancos[i] + "</option>\n";
                                }
                                $("#banco").html(unid);
                            }
                            if (data.sttppg == 1) {
                                var unid = "<option value='-1'>« Selecione »</option>\n";
                                for (var i in data.pagamentos) {
                                    unid += "<option value='" + i + "' " + selected + ">" + data.pagamentos[i] + "</option>\n";
                                }
                                $("#tipopg").html(unid);
                            }


                            if (data.stsindicato == 1) {
                                var unid = "<option value='-1'>« Selecione »</option>\n";
                                for (var i in data.sindicatos) {
                                    unid += "<option value='" + i + "' " + selected + ">" + data.sindicatos[i] + "</option>\n";
                                }
                                $("#sindicato").html(unid);
                            }

                        }, "json");
                    }
                });

                $("#curso").change(function() {
                    var $this = $(this);
                    if ($this.val() != "-1") {
                        $.post('rh_transferencia.php', {funcao: $this.val(), method: "carregahorario"}, function(data) {
                            if (data.status == 1) {
                                var hora = "<option value='-1'>« Selecione »</option>\n";
                                for (var i in data.options) {
                                    hora += "<option value='" + i + "'>" + data.options[i] + "</option>\n";
                                }
                                $("#horario").html(hora);
                            }
                        }, "json");
                    }
                });

            });
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
                <?php if ($bloqueio) { ?>
                    <div>
                        <h2>Atenção</h2>
                        <p>O funcionário selecionado encontra-se em uma folha em aberto, remova-o desta folha para proseguir com a transferência!</p>
                    </div>
                <?php } else { ?>
                    <?php if ($tela == 1) { ?>
                        <fieldset class="border-red">
                            <legend>Informações Atuais do Funcionário</legend>
                            <p><label class="first">Nome:</label> <?php echo $clt['nome'] ?></p>
                            <p><label class="first">CPF:</label> <?php echo $clt['cpf'] ?></p>
                            <p><label class="first">Região:</label> <?php echo $clt['regiao'] ?></p>
                            <p><label class="first">Projeto:</label> <?php echo $clt['projeto'] ?></p>
                            <p><label class="first">Função:</label> <?php echo $clt['id_curso'] . $clt['funcao'] . " - R$ " . number_format($clt['salario'], 2, ",", ".") ?></p>
                            <p><label class="first">Sindicato:</label> <?php echo $clt['rh_sindicato'] . ' - ' . $clt['nome_sindicato']; ?></p>
                            <p><label class="first">Horário:</label> <?php echo $clt['id_horario'] . " - " . $clt['horario'] . " ({$row['entrada1']} - {$row['saida_1']} - {$row['entrada_2']} - {$row['saida_2']})" ?></p>
                            <p><label class="first">Unidade:</label> <?php echo $clt['locacao'] ?></p>
                            <p><label class="first">Banco:</label> <?php echo $clt['nome_banco'] ?></p>
                            <p><label class="first">Tipo de Pagamento:</label> <?php echo $clt['tipo_pg'] ?></p>
                        </fieldset>
                        <br/>
                        <fieldset class="border-blue">
                            <legend>Informações para a Transferência</legend>
                            <p><label class="first">Competência:</label> <?php echo montaSelect($meses, $mesSelected, "id='mes' name='mes' class='validate[required,custom[select]]'") ?> <?php echo montaSelect($anos, $anoSelected, "id='ano' name='ano' class='validate[required,custom[select]]'") ?> <span>Competência da folha que entrará a diferença salarial</span> </p>
                            <p><label class="first">Região:</label> <?php echo montaSelect($regioes, $clt['id_regiao'], "id='regiao' name='regiao' class='validate[required,custom[select]]'") ?></p>
                            <p><label class="first">Projeto:</label> <?php echo montaSelect($projetosOpt, $clt['id_projeto'], "id='projeto' name='projeto' class='validate[required,custom[select]]'") ?></p>
                            <p><label class="first">Função:</label> <?php echo montaSelect($cursosOpt, $clt['id_curso'], "id='curso' name='curso' class='validate[required,custom[select]]'") ?></p>
                            <p><label class="first">Sindicato:</label> <?php echo montaSelect($sindicatoOpt, $clt['rh_sindicato'], "id='sindicato' name='sindicato' class='validate[required,custom[select]]'") ?></p>
                            <p><label class="first">Horário:</label> <?php echo montaSelect($horarioOpt, $clt['id_horario'], "id='horario' name='horario' class='validate[required,custom[select]]'") ?></p>
                            <p><label class="first">Unidade:</label> <?php echo montaSelect($unidadeOpt, $clt['locacao'], "id='unidade' name='unidade' class='validate[required,custom[select]]'") ?></p>
                            <p><label class="first">Banco:</label> <?php echo montaSelect($bancoOpt, $clt['banco'], "id='banco' name='banco' ") ?></p>
                            <p><label class="first">Tipo de Pagamento:</label> <?php echo montaSelect($tipoOpt, $clt['tipo_pagamento'], "id='tipopg' name='tipopg' class='validate[required,custom[select]]'") ?></p>
                            <p><label class="first">Motivo:</label> <textarea id="motivo" name="motivo" cols="25" rows="5"></textarea></p>
                        </fieldset>
                        <br/>
                        <p class="controls"> <input type="submit" class="button" value="Transferir" name="transferir" id="transferir" /> </p>
                        <?php } else { ?>
                        <h2>Funcionário transferido com sucesso!</h2>
                        <br/><br/>
                        <p>feche a tela para continuar navegando</p>
                    <?php } ?>
                <?php } ?>
            </form>
        </div>
    </body>
</html>