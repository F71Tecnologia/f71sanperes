<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}
error_reporting(E_ERROR);
include "../include/restricoes.php";
include "../../conn.php";
include "../../funcoes.php";
include "../../wfunction.php";

//--VERIFICANDO MASTER -----------------
$id_user = $_COOKIE['logado'];
$REuser = mysql_query("SELECT * FROM funcionario where id_funcionario = '{$id_user}'");
$row_user = mysql_fetch_array($REuser);

list($regiao) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));
// ---- FINALIZANDO MASTER -----------------

/* CARREGA OS BANCOS VIA AJAX, RETORNA UM JSON */
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "loadbancos") {
    $return['status'] = 1;

    $qr_bancos = mysql_query("SELECT * FROM bancos WHERE id_projeto = '{$_REQUEST['projeto']}' AND status_reg=1");
    $num_rows = mysql_num_rows($qr_bancos);
    $bancos = array();
    if ($num_rows > 0) {
        while ($row = mysql_fetch_assoc($qr_bancos)) {
                if($_COOKIE['logado'] == 161 and $row_banco['id_banco'] == 107) continue;
            $bancos[$row['id_banco']] = utf8_encode($row['nome']);
        }
    } else {
        $bancos["-1"] = "Banco não encontrado";
    }

    $return['options'] = $bancos;

    echo json_encode($return);
    exit;
}
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "detalhe") {
    $ttml = "";
    $codigo = $_REQUEST['codigo'];
    $ex = explode(".", $codigo);
    if(count($ex)==3){
        $cod = "cod = '{$_REQUEST['codigo']}'";
    }elseif(count($ex)==2){
        $cod = "B.id_subgrupo = '{$_REQUEST['codigo']}";
    }  else {
        $cod = "A.id_grupo = '".str_replace("0","",$_REQUEST['codigo'])."0'";
    }
    
    
    $qr = "SELECT   D.*,
                    CAST( REPLACE(D.valor, ',', '.') as decimal(13,2)) as cvalor,
                    DATE_FORMAT(data_vencimento, \"%d/%m/%Y\") AS dataBr,D.tipo
            FROM entradaesaida_grupo AS A
            LEFT JOIN entradaesaida_subgrupo AS B ON (A.id_grupo=B.entradaesaida_grupo)
            LEFT JOIN entradaesaida AS C ON (LEFT(C.cod,5)=B.id_subgrupo)
            INNER JOIN (SELECT * FROM saida WHERE {$_REQUEST['where']}) AS D ON (D.tipo=C.id_entradasaida)
            WHERE C.id_entradasaida >= 154 AND $cod
            ORDER BY C.cod";
            
    $result = mysql_query($qr);
    if(mysql_num_rows($result) > 0){
        $html="<table border='0' cellpadding='0' cellspacing='0' width='100%'>";
        while($row = mysql_fetch_assoc($result)){
            $comprovante = "-";
            if($row['comprovante']==2){
                $comprovante = "<img src='../../financeiro/imagensfinanceiro/attach-32.png' class='anexo' data-key='{$row['id_saida']}'>";
            }
            $especifica = ($row['especifica']=="")?"-":$row['especifica'];
            if($row['estorno']==2){
                $valor = number_format($row['cvalor'],2,",",".")." - ".number_format($row['valor_estorno_parcial'],2,",",".");
            }else{
                $valor = number_format($row['cvalor'],2,",",".");
            }
            $html.="<tr><td class='txcenter'>{$row['id_saida']}</td><td>{$row['nome']}</td><td>{$especifica}</td><td class='txright' style='width: 110px;'>".$valor."</td><td class='txcenter' style='width: 80px;'>{$row['dataBr']}</td><td>{$comprovante}</td></tr>";
            
        }
        $html.="</html>";
    }
    
    echo utf8_encode($html);
    exit;
}

/* RECEBE AS INFORMÇÕES PRA MONTAR O SELECT */
if (isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])) {

    $whereData = "month(data_vencimento) = {$_REQUEST['mes']} AND year(data_vencimento) = {$_REQUEST['ano']}";
    $completeWhere = $whereData." AND id_banco={$_REQUEST['banco']} AND `status` = 2 AND estorno IN (0,2)";
    $mesShow = mesesArray($_REQUEST['mes']) . "/" .$_REQUEST['ano'];
    
    $qrBase = "SELECT A.id_grupo,B.id as idsub,A.nome_grupo,B.id_subgrupo,B.nome AS subgrupo,C.cod,C.nome,C.id_entradasaida,
            COUNT(D.id_saida) AS qnt,
            SUM(CAST( REPLACE(D.valor, ',', '.') as decimal(13,2))) as total
            FROM entradaesaida_grupo AS A
            LEFT JOIN entradaesaida_subgrupo AS B ON (A.id_grupo=B.entradaesaida_grupo)
            LEFT JOIN entradaesaida AS C ON (LEFT(C.cod,5)=B.id_subgrupo)
            LEFT JOIN (SELECT id_saida,tipo,
                                IF(estorno = 2, CAST((REPLACE(valor, ',', '.') - REPLACE(valor_estorno_parcial, ',', '.')) as DECIMAL(13,2)) , valor) as valor 
                                FROM saida WHERE $completeWhere) AS D ON (D.tipo=C.id_entradasaida)
            WHERE C.id_entradasaida >= 154 AND C.cod != \"06.03.01\"";
    
    $qr = $qrBase." GROUP BY C.id_entradasaida ORDER BY C.cod";
    $result = mysql_query($qr);
    
    echo "<!-- {$qr} -->\r\n";
    
    $qr_totais = $qrBase." GROUP BY A.id_grupo";
    $result_totais = mysql_query($qr_totais);
    $totais = array();
    while ($row_total = mysql_fetch_assoc($result_totais)) {
        $totais[$row_total['id_grupo']] = $row_total['total'];
    }
    
    
    $qr_subtotais = $qrBase." GROUP BY B.id";
    $result_subtotais = mysql_query($qr_subtotais);
    $subtotais = array();
    while ($row_subtotal = mysql_fetch_assoc($result_subtotais)) {
        $subtotais[$row_subtotal['idsub']] = $row_subtotal['total'];
    }
    
    //print_r($subtotais);exit;
    
    $qt_totalfinal = "SELECT SUM(CAST(
            REPLACE(total, ',', '.') AS DECIMAL(13,2))) AS total
            FROM ({$qrBase}) as q";
            
    $result_totalfinal = mysql_query($qt_totalfinal);
    $row_totalfinal = mysql_fetch_assoc($result_totalfinal);

    $qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = {$_REQUEST['projeto']}");
    $projeto = mysql_fetch_assoc($qr_projeto);

    $qr_master = mysql_query("SELECT * FROM master WHERE id_master = {$projeto['id_master']}");
    $master = mysql_fetch_assoc($qr_master);
    
    echo "<!--" . $qr . "-->\n\r";
    echo "<!--" . $qr_totais . "-->\n\r";
    echo "<!--" . $qr_subtotais . "-->\n\r";
    echo "<!--" . $qt_totalfinal . "-->\n\r";
}

$qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$regiao'");
$row_regiao = mysql_fetch_assoc($qr_regiao);
$id_master = $row_regiao['id_master'];

$qr_regioes = mysql_query("SELECT id_regiao FROM funcionario_regiao_assoc WHERE id_funcionario = {$_COOKIE['logado']} AND id_master = {$id_master}");
$regiAcesso = array();
while($row_regiao = mysql_fetch_assoc($qr_regioes)){
    $regiAcesso[] = $row_regiao['id_regiao'];
}

$qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_regiao IN (".implode(",",$regiAcesso).") AND status_reg = 1 ORDER BY nome");
$projetos = array("-1" => "« Selecione »");
while ($row_projeto = mysql_fetch_assoc($qr_projeto)) {
    //POG PARA BLOQUEAR VIAMÃO   
    //if(($_COOKIE['logado'] == 161 or $_COOKIE['logado'] == 178 or $_COOKIE['logado'] == 180) and $row_projeto['id_projeto'] == 3305) continue;  
  
    $projetos[$row_projeto['id_projeto']] = $row_projeto['id_projeto'] . " - " . $row_projeto['nome'];
}
$attrPro = array("id" => "projeto", "name" => "projeto", "class" => "validate[custom[select]]");
$meses = mesesArray();
$anos = anosArray(null, null);


/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
$projetoR = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$bancoR = (isset($_REQUEST['banco'])) ? $_REQUEST['banco'] : null;
$mesR = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : null;
$anoR = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date("Y");
?>
<html>
    <head>
        <title>:: Intranet :: RELATÓRIO DETALHADO</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../favicon.ico" rel="shortcut icon" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt.js" type="text/javascript"></script>

        <script src="../../js/global.js" type="text/javascript"></script>
        <style>
            #dvTable{position: relative;}
        </style>
        <script>
            $(function(){
                $("#form1").validationEngine();
                
                $("#projeto").change(function(){
                    var $this = $(this);
                    if($this.val() != "-1"){
                        $.post('rel_detalhado.php', { projeto: $this.val(), method: "loadbancos" }, function(data) {
                            if(data.status==1){
                                var opcao = "";
                                var selected = "";
                                for (var i in data.options){
                                    selected = "";
                                    if(i==$("#bancSel").val()){
                                        selected = "selected=\"selected\" ";
                                    }
                                    opcao += "<option value='" + i + "' " + selected + ">" + data.options[i] + "</option>";
                                }
                                $("#banco").html(opcao);
                            }
                        },"json");
                    }
                }).trigger("change");
                
                $(".bt").css("cursor","pointer");
                $(".bt").click(function(){
                    var $this = $(this);
                    if($this.attr('data-show')==0){
                        $(".bt").attr('data-show','0');
                        $(".removable").parent().remove();
                        var tr = $this.parent().parent();
                        $.post('rel_detalhado.php', { codigo: tr.attr("data-key"), where: $("#where").val(), method: "detalhe" }, function(data) {
                            if(data!=""){
                                tr.next().html("<td class='removable' colspan='3' style='background: #F5FFFC;'>"+data+"</td>");
                                $this.attr('data-show','1');
                                $(".anexo").css("cursor","pointer");
                            }
                        });
                    }else{
                        $this.attr('data-show','0');
                        $(".removable").parent().remove();
                    }
                });
                
                $("#dvTable").on("click",".anexo",function(){
                    thickBoxIframe("Anexos", "../view/anexos.php", {id_saida: $(this).attr("data-key")}, 600, 400);
                });
            });
        </script>
    </head>
    <body id="page-despesas" class="novaintra">
        <div id="content" style="overflow: hidden; width: 90%;">
            <form action="" method="post" name="form1" id="form1">
                <input type="hidden" name="bancSel" id="bancSel" value="<?php echo $bancoR ?>" />
                <h2>RELATÓRIO DETALHADO</h2>

                <fieldset>
                    <legend>Dados</legend>
                    <p><label class="first">Projeto:</label> <?php echo montaSelect($projetos, $projetoR, $attrPro) ?></p>
                    <p><label class="first">Banco:</label> <?php echo montaSelect(array("-1" => "« Selecione o projeto »"), null, "id='banco' name='banco' class='validate[custom[select]]'") ?></p>
                    <p id="mensal" ><label class="first">Mês:</label> <?php echo montaSelect($meses, $mesR, "id='mes' name='mes' class='validate[custom[select]]'") ?>  <?php echo montaSelect($anos, $anoR, "id='ano' name='ano' class='validate[custom[select]]'") ?> (mês de pagamento)</p>

                    <p class="controls"> <input type="submit" class="button" value="Filtrar" name="filtrar" /> </p>
                </fieldset>

                <?php if (mysql_num_rows($result) > 0) { ?>
                    <br/><br/>
                    <input type="hidden" name="where" id="where" value="<?php echo $completeWhere ?>" />
                    
                    <div id="dvTable">
                        <p id="excel" style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Relatório')" value="Exportar para Excel" class="exportarExcel"></p>
                        <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" width="100%" class="grid">
                            <thead>
                                <tr>
                                    <th colspan="2">Unidade Gerenciada: <?php echo $projeto['nome'] ?></th>
                                    <th><?php echo $mesShow ?></th>
                                </tr>
                                <tr>
                                    <th colspan="3">O responsável: <?php echo $master['nome'] ?></th>
                                </tr>
                                <tr>
                                    <th colspan="3">Despesas realizadas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="titulo">
                                    <td>Código</td>
                                    <td>Despesa</td>
                                    <td>Valor(R$)</td>
                                </tr>
                                <?php
                                $antesGrupo = "";
                                $antesSubGrupo = "";
                                while ($row = mysql_fetch_assoc($result)) {
                                    if ($antesGrupo != $row['id_grupo']) {
                                        $antesGrupo = $row['id_grupo'];
                                        echo "<tr class=\"subtitulo\"><td>0" . str_replace("0", "", $row['id_grupo']) . "</td><td>" . $row['nome_grupo'] . "</td><td class='txright'>" . number_format($totais[$row['id_grupo']], 2, ",", ".") . "</td><tr>";
                                    }
                                    if ($antesSubGrupo != $row['id_subgrupo']) {
                                        $antesSubGrupo = $row['id_subgrupo'];
                                        echo "<tr class=\"subtitulo\"><td><span class='artificio1'></span>" . $row['id_subgrupo'] . "</td><td>" . $row['subgrupo'] . "</td><td class='txright'>" . number_format($subtotais[$row['idsub']], 2, ",", ".") . "</td><tr>";
                                    }

                                    echo "<tr data-key=\"" . $row['cod'] . "\"><td><span class='artificio2'></span><span class=\"bt\" data-show=\"0\">" . $row['cod'] . "</span></td><td>" . $row['nome'] . "</td><td class='txright'>" . number_format($row['total'], 2, ",", ".") . "</td><tr>";
                                    ?>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="txright">Total:</td>
                                    <td class="txright"><?php echo number_format($row_totalfinal['total'], 2, ",", "."); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php } ?>
            </form>
        </div>
    </body>
</html>