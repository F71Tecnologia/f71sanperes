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

$regiao = $row_user['id_regiao'];
$master = $row_user['id_master'];
//SELECT id_projeto FROM projeto WHERE id_master = 6
$rsPro = montaQuery("projeto", "id_projeto", "id_master = {$master}");
$arprojetos = array();
foreach($rsPro as $linha){
    $arprojetos[] = $linha['id_projeto'];
}
// ---- FINALIZANDO MASTER -----------------

$filtro = false;
$simples = false;

/* RECEBE AS INFORMÇÕES PRA MONTAR O SELECT */
if (validate($_REQUEST['filtrar'])) {
    
    $qr = "SELECT A.id_prestador,B.c_razao,B.c_cnpj
            FROM saida AS A
            INNER JOIN prestadorservico AS B ON (A.id_prestador=B.id_prestador)
            WHERE A.id_prestador != 0 AND 
            YEAR(A.data_vencimento) = {$_REQUEST['ano']} AND 
            A.estorno = 0 AND  A.status = 2 AND A.id_projeto IN (".implode(",",$arprojetos).")
            GROUP BY A.id_prestador
            ORDER BY A.data_vencimento";
    $result = mysql_query($qr);
    
    $filtro = true;
    echo "<!--" . $qr . "-->\n\r";
}

/* QUERY SIMPLIFICADA */
if(validate($_REQUEST['simplificado'])){
    $qr = "SELECT A.id_prestador,B.c_razao,B.c_cnpj,
            A.id_saida,A.nome,DATE_FORMAT(A.data_vencimento, '%d/%m/%Y') AS vencimento,A.especifica,CAST( REPLACE(A.valor, ',', '.') as decimal(13,2)) as cvalor,A.comprovante,A.estorno,A.darf
            FROM saida AS A
            INNER JOIN prestadorservico AS B ON (A.id_prestador=B.id_prestador)
            WHERE A.id_prestador != 0 AND 
            YEAR(A.data_vencimento) = {$_REQUEST['ano']} AND 
            A.estorno = 0 AND  A.status = 2 AND A.id_projeto IN (".implode(",",$arprojetos).")
            ORDER BY A.id_prestador,A.data_vencimento";
            
    $result = mysql_query($qr);
    
    $simples = true;
    echo "<!--" . $qr . "-->\n\r";
}

if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "vincular") {
    $retorno["status"] = true;
    if(!sqlUpdate("saida",array("id_saida_pai"=>$_REQUEST['saida_pai'],"darf"=>1,"tipo_darf"=>$_REQUEST['tpdarf']),"id_saida={$_REQUEST['idsaida']}")){
        $retorno["status"] = false;
    }
    
    echo json_encode($retorno);
    exit;
}

if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "detalhe") {
    $html = "";
    
    $prestador = $_REQUEST['prestador'];
    $ano = $_REQUEST['ano'];
    $mes = $_REQUEST['mes'];
    
    $qr = "SELECT id_saida,nome,DATE_FORMAT(data_vencimento, '%d/%m/%Y') AS vencimento,especifica,CAST( REPLACE(valor, ',', '.') as decimal(13,2)) as cvalor,comprovante,estorno,darf
            FROM saida 
            WHERE id_prestador = {$prestador} AND 
            MONTH(data_vencimento) = {$mes} AND 
            YEAR(data_vencimento) = {$ano}";
    
    $result = mysql_query($qr);
    if(mysql_num_rows($result) > 0){
        $html="<table border='0' cellpadding='0' cellspacing='0' width='100%'>";
        while($row = mysql_fetch_assoc($result)){
            $comprovante = "<img src='../../financeiro/imagensfinanceiro/attach-32.png' class='anexo' data-key='{$row['id_saida']}'>";
            $especifica = ($row['especifica']=="")?"-":$row['especifica'];
            if($row['estorno']==2){
                $valor = number_format($row['cvalor'],2,",",".")." - ".number_format($row['valor_estorno_parcial'],2,",",".");
            }else{
                $valor = number_format($row['cvalor'],2,",",".");
            }
            
            $darf = ($row['darf']==1)?"DARF":"-";
            
            $html.="<tr>
                        <td class='txcenter'>{$row['id_saida']}</td>
                        <td>{$row['nome']}</td>
                        <td>{$especifica}</td>
                        <td class='txright' style='width: 110px;'>".$valor."</td>
                        <td class='txcenter' style='width: 80px;'>{$row['vencimento']}</td>
                        <td class='txcenter'>{$darf}</td>
                        <td>{$comprovante}</td>
                    </tr>";
        }
        $html.="</table>";
    }
    
    echo utf8_encode($html);
    exit;
}

$qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$regiao'");
$row_regiao = mysql_fetch_assoc($qr_regiao);
$id_master = $row_regiao['id_master'];

$qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_master = '$id_master' AND status_reg = 1");
$projetos = array("-1" => "« Selecione »");
while ($row_projeto = mysql_fetch_assoc($qr_projeto)) {
    $projetos[$row_projeto['id_projeto']] = $row_projeto['id_projeto'] . " - " . $row_projeto['nome'];
}
$attrPro = array("id" => "projeto", "name" => "projeto", "class" => "validate[custom[select]]");
$anos = anosArray(null, null);


/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
$projetoR = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$bancoR = (isset($_REQUEST['banco'])) ? $_REQUEST['banco'] : null;
$anoR = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date("Y");
?>
<html>
    <head>
        <title>:: Intranet :: RELATÓRIO PRESTADOR</title>
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
                
                $(".bt").css("cursor","pointer");
                $(".bt").click(function(){
                    var $this = $(this);
                    if($this.attr('data-show')==0){
                        showLoading($this,"../../");
                        $(".bt").attr('data-show','0');
                        $(".removable").parent().remove();
                        var tr = $this.parent().parent();
                        $.post('rel_prestador.php', { prestador: tr.attr("data-key"), mes: tr.attr("data-mes"), ano: $("#ano").val(), method: "detalhe" }, function(data) {
                            removeLoading();
                            if(data!=""){
                                tr.after("<tr><td class='removable' colspan='3' style='background: #F5FFFC;'>"+data+"</td></tr>");
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
                    var bt = $(this);
                    thickBoxIframe("Anexos", "../view/anexos.php", {id_saida: bt.attr("data-key"), darf:1}, 600, 400, function(){
                        $("span[data-show=1]").attr('data-show','0').trigger("click");
                    });
                });
            });
            
            var vincular = function(){
                showLoading($("#tp_darf"),"../../");
                $.post('rel_prestador.php', { tpdarf: $("#tp_darf").val(), saida_pai: $("#id_saida_pai").val(),idsaida: $("#idsaida").val(), method: "vincular" }, function(data) {
                    removeLoading();
                    alert("Darf vinculada com sucesso!");
                });
            }
        </script>
    </head>
    <body id="page-prestador" class="novaintra">
        <div id="content" style="overflow: hidden; width: 90%;">
            <form action="" method="post" name="form1" id="form1">
                <input type="hidden" name="bancSel" id="bancSel" value="<?php echo $bancoR ?>" />
                <h2>RELATÓRIO PRESTADOR</h2>
                
                <fieldset>
                    <legend>Dados</legend>
                    <!--<p><label class="first">Projeto:</label> <?php echo montaSelect($projetos, $projetoR, $attrPro) ?></p>-->
                    <p><label class="first">Ano:</label> <?php echo montaSelect($anos, $anoR, "id='ano' name='ano' class='validate[custom[select]]'") ?></p>

                    <p class="controls">
                        <input type="submit" class="button" value="Filtrar" name="filtrar" />
                        <input type="submit" class="button" value="Simplificado" name="simplificado" />
                    </p>
                </fieldset>

                <?php if ($filtro) { ?>
                    <br/><br/>
                    <div id="dvTable">
                        <p id="excel" style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Relatório')" value="Exportar para Excel" class="exportarExcel"></p>
                        <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" width="100%" class="grid">
                            <thead>
                                <tr>
                                    <th>Mês</th>
                                    <th>Quantidade de Saídas</th>
                                    <th>Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($prestador = mysql_fetch_assoc($result)) {
                                    echo "<tr class=\"subtitulo\">
                                            <td colspan='100%' class='txcenter'>{$prestador['c_cnpj']} - {$prestador['c_razao']}</td>
                                         </tr>";
                                            
                                    $qrMeses = "SELECT B.id_saida,MONTH(A.data) as mes,B.qntd,B.valor FROM ano AS A
                                                LEFT JOIN (SELECT id_saida,SUM(CAST(REPLACE(valor,',','.') as DECIMAL(10,2))) as valor,COUNT(id_saida) as qntd,MONTH(data_vencimento)as vencimento FROM saida WHERE id_prestador = {$prestador['id_prestador']} AND YEAR(data_vencimento) = {$_REQUEST['ano']} AND status = 2 GROUP BY MONTH(data_vencimento)) AS B ON (B.vencimento = MONTH(A.data))
                                                WHERE YEAR(A.data) = {$_REQUEST['ano']}
                                                GROUP BY MONTH(data)";
                                    $rsMeses = mysql_query($qrMeses);
                                    while($mes = mysql_fetch_assoc($rsMeses)){
                                        $qnt = empty($mes['qntd'])?"-":$mes['qntd'];
                                        $val = number_format($mes['valor'],2,",",".");
                                        echo "<tr data-key=\"" . $prestador['id_prestador'] . "\" data-mes=\"{$mes['mes']}\"><td>";
                                        echo ($qnt>0)?"<span class=\"bt\" data-show=\"0\">":"<span data-show=\"0\">";
                                        echo mesesArray($mes['mes'])."</span></td><td>{$qnt}</td><td>R$ {$val}</td></tr>";
                                    }
                                ?>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php }elseif($simples){ ?>
                    
                    <br/><br/>
                    <div id="dvTable">
                        <p id="excel" style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Relatório')" value="Exportar para Excel" class="exportarExcel"></p>
                        <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" width="100%" class="grid">
                            <thead>
                                <tr>
                                    <th>N° da Saída</th>
                                    <th>Nome</th>
                                    <th>Descrição</th>
                                    <th>Valor</th>
                                    <th>Data de Pagamento</th>
                                    <th>DARF</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $prestador_old = "";
                                while ($saida = mysql_fetch_assoc($result)) { 
                                    if($prestador_old!=$saida['c_razao']){
                                        $prestador_old = $saida['c_razao'];
                                        echo "<tr class=\"subtitulo\"><td class='txcenter' colspan=\"100%\"> {$saida['c_razao']} </td></tr>";
                                    }
                                    $especifica = ($saida['especifica']=="")?"-":$saida['especifica'];
                                    $darf = ($saida['darf']==1)?"<span class=\"tx-green tx-bold\">SIM</span>":"<span class=\"tx-red\">NÃO</span>";
                                    if($saida['estorno']==2){
                                        $valor = number_format($saida['cvalor'],2,",",".")." - ".number_format($saida['valor_estorno_parcial'],2,",",".");
                                    }else{
                                        $valor = number_format($saida['cvalor'],2,",",".");
                                    }
                                    ?>
                                <tr>
                                    <td class='txcenter'><?php echo $saida['id_saida'] ?></td>
                                    <td><?php echo $saida['nome'] ?></td>
                                    <td><?php echo str_replace('/','/ ',preg_replace('/,/',', ',$especifica)); ?></td>
                                    <td class='txright' style='width: 110px;'><?php echo $valor?></td>
                                    <td class='txcenter' style='width: 80px;'><?php echo $saida['vencimento']?></td>
                                    <td class='txcenter'><?php echo $darf ?></td>
                                </tr>
                                    <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    
                <?php } ?>
            </form>
        </div>
    </body>
</html>