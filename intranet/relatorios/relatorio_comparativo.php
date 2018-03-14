<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}

function printArr($arr) {
    echo '<pre>';
    print_r($arr);
    echo '</pre>';
}

include "../conn.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include "../wfunction.php";
include "../funcoes.php";

$usuario = carregaUsuario();
$optRegiao = getRegioes();

$id_regiao = $usuario['id_regiao'];

$projetosOp = array("-1" => "« Selecione »");
$query = "SELECT id_projeto,nome FROM projeto WHERE id_regiao = '$id_regiao'";
$result = mysql_query($query) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
    $projetosOp[$row['id_projeto']] = $row['id_projeto'] . " - " . $row['nome'];
}

/* if(isset($_POST['sclt']) AND !empty($_POST['sclt'])){
  $reg = $_POST['regiao'];
  $proj = $_POST['projeto'];
  //echo "SELECT id_clt, nome FROM rh_clt WHERE id_projeto = $proj AND id_regiao = $reg AND id_clt IN (SELECT DISTINCT id_clt FROM rh_transferencias)";exit;
  $sqlCLT = mysql_query("SELECT id_clt, nome FROM rh_clt WHERE id_projeto = $proj AND id_regiao = $reg AND id_clt IN (SELECT DISTINCT id_clt FROM rh_transferencias)");
  while($rowCLT = mysql_fetch_assoc($sqlCLT)){
  $selectCLT .= '<option value="'.$rowCLT['id_clt'].'">'.$rowCLT['nome'].'</option>';
  }
  die($selectCLT);
  } */

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'getClt') {
    $id_projeto = $_REQUEST['projeto'];
    $sqlCLT = mysql_query("SELECT id_clt, nome FROM rh_clt WHERE id_projeto = $id_projeto AND id_regiao = $id_regiao AND id_clt IN (SELECT DISTINCT id_clt FROM rh_transferencias)");
    $selectCLT = "<option>« Selecione »</option>";
    while ($rowCLT = mysql_fetch_assoc($sqlCLT)) {
        $selectCLT .= '<option value="' . $rowCLT['id_clt'] . '">' . $rowCLT['nome'] . '</option>';
    }
    echo utf8_encode($selectCLT);
    exit();
}
if (isset($_REQUEST['gerar'])) {


    $id_clt = $_REQUEST['clt'];

    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto' => $id_projeto));

    //printArr($_POST);
    $sql = "
    SELECT 
        DATE_FORMAT(rt.data_proc, '%d/%m/%Y') data,
        rt.motivo motivo,
        b1.nome bancoAnt,
        b2.nome bancoPara,
        p1.nome projetoAnt,
        p2.nome projetoPara,
        r1.regiao regiaoAnt,
        r2.regiao regiaoPara,
        c1.nome cursoAnt,
        c2.nome cursoPara,
        rt.unidade_de unidadeAnt,
        rt.unidade_para unidadePara,
        rh1.nome horarioAnt,
        rh2.nome horarioPara,
        rs1.nome sindAnt,
        rs2.nome sindPara,
        tp1.tipopg tpAnt,
        tp2.tipopg tpPara,
        rc.nome nome
    FROM 
        rh_transferencias rt
        LEFT JOIN rhsindicato rs1 ON rs1.id_sindicato = rt.id_sindicato_de
        LEFT JOIN rhsindicato rs2 ON rs2.id_sindicato = rt.id_sindicato_para
        INNER JOIN rh_clt rc ON rt.id_clt = rc.id_clt
        LEFT JOIN bancos b1 ON b1.id_banco = rt.id_banco_de
        LEFT JOIN bancos b2 ON b2.id_banco = rt.id_banco_para
        LEFT JOIN projeto p1 ON p1.id_projeto = rt.id_projeto_de
        LEFT JOIN projeto p2 ON p2.id_projeto = rt.id_projeto_para
        LEFT JOIN regioes r1 ON r1.id_regiao = rt.id_regiao_de
        LEFT JOIN regioes r2 ON r2.id_regiao = rt.id_regiao_para
        LEFT JOIN curso c1 ON c1.id_curso = rt.id_curso_de
        LEFT JOIN curso c2 ON c2.id_curso = rt.id_curso_para
        LEFT JOIN rh_horarios rh1 ON rh1.id_horario = rt.id_horario_de
        LEFT JOIN rh_horarios rh2 ON rh2.id_horario = rt.id_horario_para
        LEFT JOIN tipopg tp1 ON tp1.id_tipopg = rt.id_tipo_pagamento_de
        LEFT JOIN tipopg tp2 ON tp2.id_tipopg = rt.id_tipo_pagamento_para
    WHERE 
        rt.id_clt = $id_clt 
        AND rt.id_curso_de <> rt.id_curso_para
    ORDER BY rt.id_transferencia DESC
    LIMIT 1";
    //echo $sql;
    $qr_relatorio = mysql_query($sql)or die(mysql_error());
    $qtd_relatorio = mysql_num_rows($qr_relatorio);
    $row_rel = mysql_fetch_assoc($qr_relatorio);
    if ($row_rel['bancoAnt'] != $row_rel['bancoPara']) {
        $styleBanco = 'color: red;';
    }
    if ($row_rel['projetoAnt'] != $row_rel['projetoPara']) {
        $styleProjeto = 'color: red;';
    }
    if ($row_rel['regiaoAnt'] != $row_rel['regiaoPara']) {
        $styleRegiao = 'color: red;';
    }
    if ($row_rel['cursoAnt'] != $row_rel['cursoPara']) {
        $styleCurso = 'color: red;';
    }
    if ($row_rel['unidadeAnt'] != $row_rel['unidadePara']) {
        $styleUnidade = 'color: red;';
    }
    if ($row_rel['horarioAnt'] != $row_rel['horarioPara']) {
        $styleHorario = 'color: red;';
    }
    if ($row_rel['sindAnt'] != $row_rel['sindPara']) {
        $styleSind = 'color: red;';
    }
    if ($row_rel['tpAnt'] != $row_rel['tpPara']) {
        $styleTp = 'color: red;';
    }
}

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
?>
<html>
    <head>
        <title>:: Intranet :: Relatório Saídas</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/global.js" type="text/javascript"></script>

        <script>
            $(function() {

                $('#projeto').change(function() {
                    $.post("<?= $_SERVER['PHP_SELF'] ?>", {method: 'getClt', projeto: $("#projeto").val()}, function(data) {
                        $("#sclt").html(data);
                    });
                });
                $('#projeto').trigger('change');
            });
        </script>
        <style>
            table { margin-top: 50px; }
            table, th, td { border: 1px solid black; }
            th { background-color: #eee; }
        </style>
    </head>
    <body class="novaintra" >
        <div id="content">
            <form  name="form" action="" method="post" id="form">
                <div id="head">
                    <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>Relatório de Transferência</h2>
                    </div>
                </div>
                <br class="clear">
                <br/>
                <fieldset class="noprint">
                    <legend>Relatório de Transferência</legend>
                    <!--<p><label class="first">Região:</label> <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao')); ?> </p>-->                        
                    <p><label class="first">Projeto:</label> <?php echo montaSelect($projetosOp, $projetoSel, array('name' => "projeto", 'id' => 'projeto')); ?> </p>
                    <p><label class="first">CLT:</label>
                        <select name="clt" id="sclt">
                            <option>« Selecione o Projeto »</option>
                        </select>
                    </p>
                    <!--<p><label class="first">Periodo:</label> <input name="data_ini" id="data_ini" type="text" size="10" maxlength="10" class="date" value="<?php echo $_REQUEST['data_ini']; ?>"> <label style="font-weight: bold;">até</label> <input name="data_fim" id="data_fim" type="text" size="10" maxlength="10" class="date" value="<?php echo $_REQUEST['data_fim']; ?>"></p>-->
                    <p class="controls" >
                        <input type="submit" name="gerar" value="Gerar" id="gerar"/>
                    </p>
                </fieldset>
                <?php if (!empty($qr_relatorio) && isset($_POST['gerar'])) { ?>
                    <p id="excel" style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Relatório')" value="Exportar para Excel" class="exportarExcel"></p>
                    <table id="tbRelatorio" class="" width="100%" style="border:1px solid; border-collapse:collapse;"> 
                        <tr>
                            <th colspan="3"style="text-align: left;"><?php echo $row_rel['nome'] . ' - ' . $row_rel['data'] . ' - ' . $row_rel['motivo']; ?></th>
                        </tr>
                        <tr>
                            <th></th><th>De</th><th>Para</th>
                        </tr>
                        <tr>
                            <th style="<?php echo $styleRegiao; ?>" >Região</th>
                            <td style="<?php echo $styleRegiao; ?>" ><?php echo $row_rel['regiaoAnt']; ?></td>
                            <td style="<?php echo $styleRegiao; ?>" ><?php echo $row_rel['regiaoPara']; ?></td>
                        </tr>
                        <tr>
                            <th style="<?php echo $styleProjeto; ?>" >Projeto</th>
                            <td style="<?php echo $styleProjeto; ?>" ><?php echo $row_rel['projetoAnt']; ?></td>
                            <td style="<?php echo $styleProjeto; ?>" ><?php echo $row_rel['projetoPara']; ?></td>
                        </tr>
                        <tr>
                            <th style="<?php echo $styleCurso; ?>" >Curso</th>
                            <td style="<?php echo $styleCurso; ?>" ><?php echo $row_rel['cursoAnt']; ?></td>
                            <td style="<?php echo $styleCurso; ?>" ><?php echo $row_rel['cursoPara']; ?></td>
                        </tr>
                        <tr>
                            <th style="<?php echo $styleHorario; ?>" >Horario</th>
                            <td style="<?php echo $styleHorario; ?>" ><?php echo $row_rel['horarioAnt']; ?></td>
                            <td style="<?php echo $styleHorario; ?>" ><?php echo $row_rel['horarioPara']; ?></td>
                        </tr>
                        <tr>
                            <th style="<?php echo $styleTp; ?>" >Tipo Pagamento</th>
                            <td style="<?php echo $styleTp; ?>" ><?php echo $row_rel['tpAnt']; ?></td>
                            <td style="<?php echo $styleTp; ?>" ><?php echo $row_rel['tpPara']; ?></td>
                        </tr>
                        <tr>
                            <th style="<?php echo $styleBanco; ?>" >Banco</th>
                            <td style="<?php echo $styleBanco; ?>" ><?php echo $row_rel['bancoAnt']; ?></td>
                            <td style="<?php echo $styleBanco; ?>" ><?php echo $row_rel['bancoPara']; ?></td>
                        </tr>
                        <tr>
                            <th style="<?php echo $styleUnidade; ?>" >Unidade</th>
                            <td style="<?php echo $styleUnidade; ?>" ><?php echo $row_rel['unidadeAnt']; ?></td>
                            <td style="<?php echo $styleUnidade; ?>" ><?php echo $row_rel['unidadePara']; ?></td>
                        </tr>
                        <tr>
                            <th style="<?php echo $styleSind; ?>" >Sindicato</th>
                            <td style="<?php echo $styleSind; ?>" ><?php echo $row_rel['sindAnt']; ?></td>
                            <td style="<?php echo $styleSind; ?>" ><?php echo $row_rel['sindPara']; ?></td>
                        </tr>
                    </table>
                <?php } ?>
            </form>
        </div>
    </body>
</html>