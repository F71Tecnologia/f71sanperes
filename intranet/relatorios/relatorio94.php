<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a>";
    exit;
}

include('../conn.php');
include('../wfunction.php');

$relatorio = false;
$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);

$projeto = $_REQUEST['pro'];
$regiao = $_REQUEST['reg'];

$result_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$projeto'");
$row_projeto = mysql_fetch_array($result_projeto);

$data_hoje = date('d/m/Y');

//GERANDO RELATÓRIO COM O MES E O ANO SELECIONADO
if (isset($_REQUEST['filtrar'])) {
    $relatorio = true;
    $mes2d = sprintf("%02d",$_REQUEST['mes']);
    
    $qr_folha = "SELECT id_folha,ids_movimentos_estatisticas FROM rh_folha WHERE projeto = {$row_projeto['id_projeto']} AND mes = '{$mes2d}' AND ano = '{$_REQUEST['ano']}'";
    $rs_folha = mysql_query($qr_folha);
    $ids_movimentos = "";
    $ids_folhas = array();
    while($row_folha = mysql_fetch_assoc($rs_folha)){
        $ids_movimentos .= $row_folha['ids_movimentos_estatisticas'];
        $ids_folhas[] = $row_folha['id_folha'];
    }
    $dt_referencia = $_REQUEST['ano']."-".$mes2d."-"."-01";
    $qr_clts = "SELECT tab.*,C.nome as cargo,C.cbo_codigo FROM
                        (
                        SELECT A.tipo_movimento,A.nome_movimento,A.valor_movimento, A.qnt,
                                        B.id_clt,B.nome,B.cpf,DATE_FORMAT(B.data_entrada, '%d/%m/%Y') AS dt,B.id_curso,
                                        (SELECT id_curso_de FROM rh_transferencias WHERE id_clt=B.id_clt AND id_curso_de <> id_curso_para AND data_proc >= '{$dt_referencia}' ORDER BY id_transferencia ASC LIMIT 1) AS de,
                                        (SELECT id_curso_de FROM rh_transferencias WHERE id_clt=B.id_clt AND id_curso_de <> id_curso_para AND data_proc <= '{$dt_referencia}' ORDER BY id_transferencia DESC LIMIT 1) AS para
                                FROM rh_movimentos_clt AS A
                                LEFT JOIN rh_clt AS B ON (A.id_clt = B.id_clt)
                                LEFT JOIN rh_folha_proc AS D ON (D.id_clt = A.id_clt)
                                WHERE A.id_movimento IN ({$ids_movimentos})
                                AND D.id_folha IN (".implode(",",$ids_folhas).")
                                ORDER BY B.nome,A.tipo_movimento) AS tab

                LEFT JOIN curso as C ON (IF(tab.para IS NOT NULL,C.id_curso=tab.para, IF(tab.de IS NOT NULL,C.id_curso=tab.de,C.id_curso=tab.id_curso)))
                WHERE cbo_codigo IN (5426,5425,5494)";
    $rs_clts = mysql_query($qr_clts);
    $num_clts = mysql_num_rows($rs_clts);
    echo "<!-- {$qr_clts} -->";
    $matriz = array();
    $count = 0;
    //MONTA ESTRUTURA DO RETORNO DA CONSULTA, PARA VISUALIZAÇÃO NA TABELA
    while($row_clt = mysql_fetch_assoc($rs_clts)){
        $id = $row_clt['id_clt'];
        $matriz[$id]['nome'] = $row_clt['nome'];
        $matriz[$id]['cargo'] = $row_clt['cargo'];
        $matriz[$id]['cpf'] = $row_clt['cpf'];
        $matriz[$id]['data'] = $row_clt['dt'];
        $matriz[$id]['nome'] = $row_clt['nome'];
        $matriz[$id]['faltas'] = 0;
        if($row_clt['tipo_movimento'] == "CREDITO"){
            $matriz[$id]['creditos'][$count]['valor'] = $row_clt['valor_movimento'];
            $matriz[$id]['creditos'][$count]['descricao'] = $row_clt['nome_movimento'];
        }else{
            if($row_clt['nome_movimento']=="FALTA"){
                $matriz[$id]['faltas'] = $row_clt['qnt'];
            }
            $matriz[$id]['descontos'][$count]['valor'] = $row_clt['valor_movimento'];
            $matriz[$id]['descontos'][$count]['descricao'] = $row_clt['nome_movimento'];
        }
        $count++;
    }
    
    $num_prt = count($matriz);
}

$meses = mesesArray(null);
$anos = anosArray(null, null, array("-1" => "« Selecione »"));

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
$mesR = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : null;
$anoR = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
?>
<html>
    <title>:: Intranet :: Relatório</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <link rel="shortcut icon" href="../favicon.ico" />
    <link href="../net1.css" rel="stylesheet" type="text/css" />
    <link href="../css/estrutura.css" rel="stylesheet" type="text/css" />
    <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
    <link href="../favicon.ico" rel="shortcut icon" />
    <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
    <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>

    <script src="../js/global.js" type="text/javascript"></script>

    <style>
        .tb {
            display: inline-block;
            width: 100%;
            border-bottom: 1px solid #CCC;
        }
        .tb-val {
            float: right;
        }
        
        @media print
        {
            fieldset{display: none;}
            .h2page{display: none;}
            .grAdm{display: none;}
        }
        @media screen
        {
            #headerPrint{display: none;}
        }
    </style>

</head>
<body class="novaintra">
    <form action="" method="post" name="form1">
        <div id="content" style="width:92%!important;">
            <div id="head">
                <img src="../imagens/logomaster<?php echo $row_master['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                <div class="fleft">
                    <h2>Relatório de Movimentos</h2>
                    <p>Controle de movimentação financeira do CLT</p>
                </div>
            </div>
            <br class="clear">

            <fieldset>
                <legend>Dados</legend>
                <p><label class="first">Projeto:</label> <?php echo $row_projeto['nome'] ?></p>
                <p><label class="first">Mês:</label> <?php echo montaSelect($meses, $mesR, "id='mes' name='mes' class='validate[custom[select]]'") ?>  <?php echo montaSelect($anos, $anoR, "id='ano' name='ano' class='validate[custom[select]]'") ?> (folha de pagamento)</p>

                <p class="controls">
                    <input type="submit" class="button" value="Filtrar" name="filtrar" />
                </p>
            </fieldset>
            <br/>
            <br/>
            <?php if ($relatorio) { ?>
                <?php if($num_clts == 0){ ?>
                    <div id='message-box' class='message-yellow'>Nenhum registro encontrado.</div>
                <?php }else{ ?>
                <table width="100%" cellpadding="0" cellspacing="0" class="grid">
                    <thead>
                        <tr>
                            <th width="15%">Nome</th>
                            <th>Contratado Em</th>
                            <th>CPF</th>
                            <th width="15%">Cargo</th>
                            <th width="25%">Todos Rendimentos</th>
                            <th width="25%">Todos Descontos</th>
                            <th>Faltas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($matriz as $clt){ 
                            $num_cred = count($clt['creditos']);
                            $num_desc = count($clt['descontos']);
                            $num_valido = ($num_cred > $num_desc) ? $num_cred : $num_desc;
                        ?>
                        <tr>
                            <td><?php echo $clt['nome'] ?></td>
                            <td><?php echo $clt['data'] ?></td>
                            <td><?php echo $clt['cpf'] ?></td>
                            <td><?php echo $clt['cargo'] ?></td>
                            <td>
                                <?php foreach($clt['creditos'] as $credito){ ?>
                                <div class="tb"><span class="tb-desc"><?php echo $credito['descricao'] ?></span><span class="tb-val">R$ <?php echo number_format($credito['valor'],2,",",".") ?></span></div>
                                <?php } ?>
                            </td>
                            <td>
                                <?php foreach($clt['descontos'] as $desconto){ ?>
                                <div class="tb"><span class="tb-desc"><?php echo $desconto['descricao'] ?></span><span class="tb-val">R$ <?php echo number_format($desconto['valor'],2,",",".") ?></span></div>
                                <?php } ?>
                            </td>
                            <td><?php echo $clt['faltas'] ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr class="secao">
                            <td colspan="10" align="center">TOTAL DE PARTICIPANTES: <?php echo $num_prt; ?></td>
                        </tr>
                    </tfoot>
                </table>
                <?php } ?>
            <?php } ?>
        </div>
    </form>
</body>
</html>