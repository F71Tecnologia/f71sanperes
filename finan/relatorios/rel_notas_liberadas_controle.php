<?php
// session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/NFSeClass.php");
include("../../classes/global.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$objNFSe = new NFSe();
$global = new GlobalClass();

$id_projeto = $_REQUEST['projeto'];
$id_regiao = $usuario['id_regiao'];
$id_prestador = $_REQUEST['prestador'];

if ($_REQUEST['method'] === 'form_subgrupo') {

    $sql = mysql_query("SELECT * FROM entradaesaida_subgrupo WHERE entradaesaida_grupo IN (SELECT id_grupo FROM entradaesaida_grupo WHERE terceiro = 1);") or die(mysql_error());
    $subgrupo = array("-1" => "Selecione");
    while ($rst = mysql_fetch_assoc($sql)) {
        $subgrupo[$rst['id']] = utf8_encode($rst['id_subgrupo'] . " - " . $rst['nome']);
    }
    
    $qr = "SELECT * FROM nfse_codigo_servico WHERE codigo = '{$_REQUEST['codigo_servico']}'";
    $xx = mysql_fetch_assoc(mysql_query($qr));
    ?>
    <p class="text-justified">Os campos abaixo s&atilde;o necess&aacute;rios para gerar a sa&iacute;da.</p>
    <br>
    <form action="rel_notas_liberadas_controle.php" class="form-horizontal" id="form_subgrupo">
        <input type="hidden" name="id_codigo_servico" value="<?= $xx['id'] ?>">
        <input type="hidden" name="id_prestador" value="<?= $_REQUEST['id_prestador'] ?>">
        <div class="form-group">
            <label for="" class="col-sm-2 control-label">Subgrupo</label>
            <div class="col-sm-10">
                <?= montaSelect($subgrupo, NULL, 'class="form-control validate[required,custom[select]]" id="subgrupo" name="subgrupo"') ?>
            </div>
        </div>
        <div class="form-group">
            <label for="" class="col-sm-2 control-label">Tipo</label>
            <div class="col-sm-10">
                <?= montaSelect(array('-1' => "Selecione o Subgrupo"), NULL, 'class="form-control validate[required,custom[select]]" id="tipo" name="tipo"') ?>
            </div>
        </div>
    </form>
    <?php
    exit();
} else if ($_REQUEST['method'] === 'getTipo') {
    $option = '<option value=""  selected="selected">Selecione</option>';

    $subgrupo = $_REQUEST['id_sub'];

    $sel_subgrupo = mysql_query("SELECT id_subgrupo FROM entradaesaida_subgrupo WHERE id = {$subgrupo}");
    $res_subgrupo = mysql_fetch_assoc($sel_subgrupo);

    $sub = $res_subgrupo['id_subgrupo'];

    if ($sub != "") {
        $query = mysql_query("SELECT id_entradasaida, cod, nome, id_entradasaida FROM  entradaesaida WHERE cod LIKE '$sub%'");
        while ($row = mysql_fetch_assoc($query)) {
            $option .= '<option value="' . $row['id_entradasaida'] . '" >' . utf8_encode($row['id_entradasaida'] . ' - ' . $row['cod'] . ' - ' . $row['nome']) . ' </option>';
        }
    }
    echo $option;
}else if($_REQUEST['method'] === 'salvar'){
    $query = "INSERT INTO nfse_codigo_servico_assoc (id_codigo_servico,id_prestador,id_tipo_entradasaida,id_subgrupo_entradasaida) VALUES ('{$_REQUEST['id_codigo_servico']}','{$_REQUEST['id_prestador']}','{$_REQUEST['tipo']}','{$_REQUEST['subgrupo']}')";
    echo json_encode(array('status'=>  mysql_query($query)));
}