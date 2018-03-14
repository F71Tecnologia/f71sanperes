<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

include('../conn.php');
include('../funcoes.php');
include('../wfunction.php');
include('PrestacaoContas.class.php');

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"36", "area"=>"Prestação de Contas", "ativo"=>"Aquisição de Bens Duráveis","id_form"=>"form1");

$regiao = $usuario['id_regiao'];
$master = $usuario['id_master'];
$usuario_id = $usuario['id_funcionario'];

$btfinalizar = true;

/* CARREGA OS BANCOS VIA AJAX, RETORNA UM JSON */
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "loadbancos") {
    $return['status'] = 1;

    $qr_bancos = mysql_query("SELECT * FROM bancos WHERE id_projeto = '{$_REQUEST['projeto']}' AND status_reg=1");
    $num_rows = mysql_num_rows($qr_bancos);
    $bancos = array();
    if ($num_rows > 0) {
        while ($row = mysql_fetch_assoc($qr_bancos)) {
            $bancos[$row['id_banco']] = utf8_encode($row['nome']);
        }
    } else {
        $bancos["-1"] = "Banco não encontrado";
    }

    $return['options'] = $bancos;

    echo json_encode($return);
    exit;
}

$qrMaster = "SELECT nome,cod_os FROM master WHERE id_master = {$master}";
$reMaster = mysql_query($qrMaster);
$roMaster = mysql_fetch_assoc($reMaster);


// CASO TENHA PROJETO (EM TODOS OS CASOS DPS DO POST)
if(isset($_REQUEST['projeto'])){
    $id_projeto = $_REQUEST['projeto'];
    $mes2d = sprintf("%02d",$_REQUEST['mes']); //mes com 2 digitos
    
    $where = "A.id_banco = {$_REQUEST['banco']}";
    $whereData = "month(data_vencimento) = {$mes2d} AND year(data_vencimento) = {$_REQUEST['ano']}";
    
    $anoMesReferencia = $_REQUEST['ano'] . "-" . $mes2d;
    $mesShow = mesesArray($_REQUEST['mes']) . "/" . $_REQUEST['ano'];
    
    if (isset($_REQUEST['exportar']) && !empty($_REQUEST['exportar'])) {
        //PEGANDO TODOS OS BANCOS QUE JA ESTAO FINALIZADOS ESSE MES PARA GERAR 1 UNICO ARQUIVO
        $dataMesIni = "{$_REQUEST['ano']}-{$mes2d}-31";
        $dataMesRef = "{$_REQUEST['ano']}-{$mes2d}-01";
        $qr_verifica = PrestacaoContas::getQueryVerifica("bens", $dataMesRef, $dataMesIni);
        $rs_verifica = mysql_query($qr_verifica);
        $idsBancos = array();
        $idsProjetos = array();
        while($row_ver = mysql_fetch_assoc($rs_verifica)){
            $idsBancos[] = $row_ver['id_banco'];
            $idsProjetos[$row_ver['id_projeto']]['sesrj'] = $row_ver['cod_sesrj'];
            $idsProjetos[$row_ver['id_projeto']]['contrato'] = $row_ver['cod_contrato'];
        }
        
        $where = "A.id_banco IN (".  implode(",", $idsBancos).")";
    }
    
    //QUERY FILTRO, EXPORTAR E FINALIZAR (HISTORICO)
    $qr = "SELECT 
            CAST( REPLACE(A.valor, ',', '.') as decimal(13,2)) AS valor2,
            F.id_grupo,F.nome_grupo,
            E.id_subgrupo,A.data_vencimento,
            A.id_saida,A.id_projeto,A.id_banco,A.id_regiao,A.nome,A.especifica,A.tipo,
            A.comprovante,A.id_bens,A.entradaesaida_subgrupo_id,A.n_documento,
            D.c_razao,D.c_cnpj,D.especificacao,
            LPAD(C.id_bens,'2','0') as codbem,C.descricao,
            G.cod_sesrj,G.cod_contrato
            FROM saida AS A
            LEFT JOIN entradaesaida_nomes AS B ON (A.id_nome = B.id_nome)
            LEFT JOIN tipos_bens AS C ON (A.id_bens = C.id_bens)
            LEFT JOIN prestadorservico AS D ON (A.id_prestador=D.id_prestador)
            LEFT JOIN entradaesaida_subgrupo AS E ON (E.id=A.entradaesaida_subgrupo_id)
            LEFT JOIN entradaesaida_grupo AS F ON (F.id_grupo=E.entradaesaida_grupo)
            LEFT JOIN projeto AS G ON (G.id_projeto=A.id_projeto)
            WHERE A.id_bens != 0 AND A.`status` = 2 AND 
            $whereData AND $where";
    
    $qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = {$_REQUEST['projeto']}");
    $projeto = mysql_fetch_assoc($qr_projeto);

    $qr_master = mysql_query("SELECT * FROM master WHERE id_master = {$projeto['id_master']}");
    $row_master = mysql_fetch_assoc($qr_master);
    
}

//FINALIZANDO A PRESTAÇÃO DESSE PROJETO
if (isset($_REQUEST['finalizar']) && !empty($_REQUEST['finalizar'])) {
    echo "<!-- ".$qr." -->";
    $result = mysql_query($qr);
    $linhas = mysql_num_rows($result);

    $qrT = "SELECT SUM(valor2) AS total FROM (" . $qr . ") AS B";
    $resultT = mysql_query($qrT);
    $rowT = mysql_fetch_assoc($resultT);
    $totalBem = number_format($rowT['total'], 2, ",", "");
    
    $referencia = "{$_REQUEST['ano']}-{$mes2d}-01";
    
    $campos = "id_projeto, id_regiao, id_banco, tipo, data_referencia, gerado_em, gerado_por, linhas, erros, valor_total,status";
    $valores = array(
            $_REQUEST['projeto'],
            $regiao,
            $_REQUEST['banco'],
            "bens",
            $referencia,
            date("Y-m-d H:i:s"),
            $usuario_id,
            $linhas,
            "0",
            $rowT['total'],
            "1");
    
    sqlInsert("prestacoes_contas",$campos,$valores);
    $id = mysql_insert_id();
    
    //MONTANDO MATRIZ PARA HISTÓRICO
    $matriz = array();
    $count = 0;
    if($linhas > 0){
        while ($row = mysql_fetch_assoc($result)) {
            $matriz[$count][] = $id;
            $matriz[$count][] = $row['id_saida'];
            $matriz[$count][] = "";
            $matriz[$count][] = $row['descricao'];
            $matriz[$count][] = $row['especifica'];
            $matriz[$count][] = $row['c_cnpj'];
            $matriz[$count][] = "";
            $matriz[$count][] = $row['n_documento'];
            $matriz[$count][] = $row['data_vencimento'];
            $matriz[$count][] = "";
            $matriz[$count][] = $row['valor2'];
            
            $count++;
        }
    }else{
        $matriz[$count][] = $id;
        $matriz[$count][] = "";
        $matriz[$count][] = "";
        $matriz[$count][] = "";
        $matriz[$count][] = "Não foram adquiridos novos bens nesse mês";
        $matriz[$count][] = "";
        $matriz[$count][] = "";
        $matriz[$count][] = "";
        $matriz[$count][] = "";
        $matriz[$count][] = "";
        $matriz[$count][] = "";
    }
    
    $campos = array(
        "id_prestacao",
        "controle_os",
        "controle_sesrj",
        "tipo",
        "descricao",
        "cnpj",
        "qtde",
        "nota_fiscal",
        "data_aquisicao",
        "vida_util",
        "valor"
    );
    sqlInsert("prestacoes_contas_bens", $campos, $matriz);
    echo "<script>location.href='finan_bens.php'</script>";
    exit;
}


/* MONTA O ARQUIVO PARA BAIXAR */
if (isset($_REQUEST['exportar']) && !empty($_REQUEST['exportar'])) {
    //error_reporting(E_ALL);
    $result = mysql_query($qr);
    $linhas = mysql_num_rows($result);
    $linhasArquivo = ($linhas==0) ? count($idsProjetos)+5 : $linhas + 5; //CASO NÃO TENHA RESULTADO VAI CONTAR OS PROJETOS A ADD 5 LINHAS (CABEÇALHO)

    $qrT = "SELECT SUM(valor2) AS total FROM (" . $qr . ") AS B";
    $resultT = mysql_query($qrT);
    $rowT = mysql_fetch_assoc($resultT);
    $totalBem = number_format($rowT['total'], 2, ",", "");

    $folder = dirname(__FILE__) . "/arquivos/";
    $fname = "OS_{$roMaster['cod_os']}_RBEM_" . date("Ymd") ."_".$mes2d. "{$_REQUEST['ano']}.CSV";
    $filename = $folder . $fname;

    $handle = fopen($filename, "w+");
    /* ESCREVENDO NO ARQUIVO */
    /* HEADER */
    fwrite($handle, "H;COD_OS;DATA_GERACAO;LINHAS;TIPO;ANO_MES_REF;TIPO_ARQUIVO;VER_DOC;SECRETARIA\r\n");
    fwrite($handle, "H;{$roMaster['cod_os']};" . date("Y-m-d") . ";{$linhasArquivo};N;{$anoMesReferencia};RBEM;3.1;01.01.01.01\r\n");

    /* DETAIL */
    /* --CASO NÃO TENHA BENS ADQUIRIDOS NO PERIODO SELECIONADO, MUDAR O CABEÇALHO DO DETALHE-- */
    if ($linhas == 0) {
        fwrite($handle, "S;COD_OS;COD_UNIDADE;COD_CONTRATO;ANO_MES_REF;DESCRICAO\r\n");
    } else {
        fwrite($handle, "D;COD_OS;COD_UNIDADE;COD_CONTRATO;ANO_MES_REF;NUM_CONTROLE_OS;NUM_CONTROLE_GOV;COD_TIPO;BEM_TIPO;DESCRICAO_NF;CNPJ;FORNECEDOR;QUANTIDADE;");
        fwrite($handle, "NF;DATA_AQUISICAO;VIDA_UTIL;VALOR;VINCULACAO\r\n");
    }

    //ESCREVENDO AS LINHAS NO ARQUIVO CASO TENHA BENS
    if ($linhas >= 1) {
        $vidautil = 0;
        while ($row = mysql_fetch_assoc($result)) {
            $valor = str_replace(".", ",", $row['valor2']);
            $vidautil = 5;
            $vidautilTotal += $vidautil;
            fwrite($handle, "D;{$roMaster['cod_os']};{$row['cod_sesrj']};{$row['cod_contrato']};{$anoMesReferencia};{$row['id_saida']};;{$row['codbem']};{$row['descricao']};{$row['especifica']};");
            fwrite($handle, "{$row['c_cnpj']};{$row['c_razao']};0;{$row['n_documento']};{$row['data_vencimento']};" . sprintf("%02d", $vidautil) . ";{$valor};{$row['especificacao']}\r\n");
        }
        unset($row);
    } else {
        
        foreach($idsProjetos as $pro){
            fwrite($handle, "S;{$roMaster['cod_os']};{$pro['sesrj']};{$pro['contrato']};{$anoMesReferencia};Não foram adquiridos novos bens nesse mês\r\n");
            $linhas++;
        }
        $vidautilTotal = "0";
        //$linhas = 1; //está dando erro qnd nao tem registro
    }

    fwrite($handle, "T;QUANTIDADE_REGISTROS;TOTAL_VALOR1;TOTAL_VALOR2\r\n");
    fwrite($handle, "T;{$linhas};{$vidautilTotal};{$totalBem}");

    /* ------------- */
    fclose($handle);
    
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header("Content-type: application/x-msdownload");
    header("Content-Length: " . filesize($filename));
    header("Content-Disposition: attachment; filename={$fname}");
    flush();

    readfile($filename);
    exit;
}

/* FILTRO PARA MOSTRAR O RELATÓRIO */
/* RECEBE AS INFORMÇÕES PRA MONTAR O SELECT */
if (isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])) {
    $btfinalizarEsse = true;
    
    $result = mysql_query($qr);
    $linhas = mysql_num_rows($result);
    $linhasArquivo = $linhas + 5;

    $qrT = "SELECT SUM(valor2) AS total FROM (" . $qr . ") AS B";
    $resultT = mysql_query($qrT);
    $rowT = mysql_fetch_assoc($resultT);
    $totalBem = number_format($rowT['total'], 2, ",", "");
    
    //VERIFICA SE OUTRO PROJETO PRECISA PRESTAR CONTAS NO MES SELECIONADO
    $dataMesIni = "{$_REQUEST['ano']}-{$mes2d}-31";
    $dataMesRef = "{$_REQUEST['ano']}-{$mes2d}-01";
    $qr_verifica = "SELECT A.id_projeto,A.nome as projeto,DATE_FORMAT(B.gerado_em, '%d/%m/%Y') as gerado_embr, C.nome as funcionario
                    FROM projeto AS A
                    LEFT JOIN prestacoes_contas AS B ON (A.id_projeto=B.id_projeto AND tipo = 'bens' AND status = 1 AND data_referencia = '{$dataMesRef}')
                    LEFT JOIN funcionario AS C ON (B.gerado_por=C.id_funcionario)
                    WHERE A.inicio < '{$dataMesIni}' AND A.prestacontas = 1";
    $rs_verifica = mysql_query($qr_verifica);
    $total_verifica = mysql_num_rows($rs_verifica);
    $projetosFaltante = array();
    $contErro = 0;
    while($rowVeri = mysql_fetch_assoc($rs_verifica)){
        
        if($rowVeri['gerado_embr'] == null && $rowVeri['id_projeto'] != $id_projeto){
            $btfinalizar=false;
            $projetosFaltante[] = $rowVeri['projeto'];
            $contErro ++;
        }elseif($rowVeri['gerado_embr'] != null && $rowVeri['id_projeto'] == $id_projeto){
            //O QUE ESTÁ ABERTO JA FOI FINALIZADO
            $btfinalizarEsse=false;
        }
        
        if($total_verifica==1 && $rowVeri['id_projeto'] == $id_projeto && $rowVeri['gerado_embr'] != null){
            $btfinalizar=false;
        }
    }
    echo "<!-- ".$qr." -->";
    echo "<!-- ".$qr_verifica." -->";
}

$qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_master = '$master' AND status_reg = 1 AND prestacontas = 1");
$projetos = array("-1" => "« Selecione »");
while ($row_projeto = mysql_fetch_assoc($qr_projeto)) {
    $projetos[$row_projeto['id_projeto']] = $row_projeto['id_projeto'] . " - " . $row_projeto['nome'];
}
$attrPro = array("id" => "projeto", "name" => "projeto", "class" => "validate[custom[select]] form-control");
$meses = mesesArray(null);
$anos = anosArray(null, null);


/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
$projetoR = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$bancoR = (isset($_REQUEST['banco'])) ? $_REQUEST['banco'] : null;
$mesR = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m') - 1;
$anoR = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date("Y");
?>
<!DOCTYPE html>
<html>
    <head>
        <title>:: Intranet :: AQUISIÇÃO DE BENS DURÁVEIS</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.png" />
                                       
        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">        
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />                
        <!--<link href="../net1.css" rel="stylesheet" type="text/css" />VAI SAIR-->
        
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>        
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script src="../js/global.js" type="text/javascript"></script>

        <script>
            $(function(){
                $("#form1").validationEngine();
                
                $("#projeto").change(function(){
                    var $this = $(this);
                    if($this.val() != "-1"){
                        $.post('finan_despesas.php', { projeto: $this.val(), method: "loadbancos" }, function(data) {
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
            });
        </script>                
    </head>
    <body id="page-despesas" class="novaintra">
        
        <?php include("../template/navbar_default.php"); ?>
        
        <div class="container">
            <div class="page-header box-contas-header"><h2><span class="glyphicon glyphicon-list-alt"></span> - Prestação de Contas</h2></div>
        
        <div id="content">
            <form action="" method="post" name="form1" id="form1" class="form-horizontal top-margin1">
                <input type="hidden" name="bancSel" id="bancSel" value="<?php echo $bancoR ?>" />
                <input type="hidden" name="home" id="home" value="" />
                
                <fieldset>
                    <legend>AQUISIÇÃO DE BENS DURÁVEIS</legend>
                    <div class="form-group">
                        <label for="select" class="col-lg-2 control-label">Projeto</label>
                        <div class="col-lg-4">
                            <?php echo montaSelect(PrestacaoContas::carregaProjetos($master), $projetoR, $attrPro); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="select" class="col-lg-2 control-label">Banco</label>
                        <div class="col-lg-4">
                            <?php echo montaSelect(array("-1" => "« Selecione o projeto »"), null, "id='banco' name='banco' class='validate[custom[select]] form-control'"); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="select" class="col-lg-2 control-label">Mês</label>
                        <div class="col-lg-4">
                            <div class="input-daterange input-group" id="bs-datepicker-range">
                                <?php echo montaSelect($meses, $mesR, "id='mes' name='mes' class='validate[custom[select]] form-control'") ?>                                 
                                <span class="input-group-addon">Ano</span>
                                <?php echo montaSelect($anos, $anoR, "id='ano' name='ano' class='validate[custom[select]] form-control'") ?>                                 
                            </div>
                            <p class="help-block">(Mês de Pagamento)</p>
                        </div>
                    </div>                    
                    <div class="form-group">
                        <div class="pull-right">
                            <input type="submit" name="filtrar" value="Filtrar" class="btn btn-primary" />                            
                        </div>
                    </div>
                </fieldset>
                
                <?php if (mysql_num_rows($result) > 0) { ?>
                    <br/>
                    <p style="text-align: right; margin-top: 20px">                        
                        <button type="button" onclick="tableToExcel('tbRelatorio', 'Bens Duraveis')" class="btn btn-success exportarExcel"><span class="fa fa-file-excel-o"></span>&nbsp;&nbsp;Exportar para Excel</button>
                    </p> 
                    
                    <div class="alert alert-dismissable alert-warning">                
                        <strong>Unidade Gerenciada: </strong> <?php echo $projeto['nome']; ?>                        
                        <strong class="borda_titulo">O Responsável: </strong> <?php echo $row_master['nome']; ?>
                        <strong class="borda_titulo">Mês Referente: </strong> <?php echo $mesShow; ?>
                    </div>
                    
                    <br/>
                    <table id="tbRelatorio" class="grid table table-hover table-striped table-bordered">
                        <thead>                                                        
                            <tr>
                                <th colspan="10" class="text-center fundo_titulo">AQUISIÇÃO DE BENS DURÁVEIS</th>
                            </tr>
                            <tr class="titulo">
                                <th>N° Controle Patrimonial OS</th>
                                <th>N° Controle Patrimonial SES/RJ</th>
                                <th>Tipo</th>
                                <th>Descrição do bem</th>
                                <th>CNPJ Fornecedor</th>
                                <th>Qthe.</th>
                                <th>N° da Nota Fiscal</th>
                                <th>Data da aquisição</th>
                                <th>Vida útil estimada</th>
                                <th>Valor (R$)</th>
                            </tr>
                        </thead>
                        <tbody>                            
                            <?php
                            while ($row = mysql_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>{$row['id_saida']}</td>";
                                echo "<td>-</td>";
                                echo "<td>{$row['descricao']}</td>";
                                echo "<td>{$row['especifica']}</td>";
                                echo "<td>{$row['c_cnpj']}</td>";
                                echo "<td class=\"txright\">-</td>";
                                echo "<td>{$row['n_documento']}</td>";
                                echo "<td>{$row['dataBr']}</td>";
                                echo "<td>-</td>";
                                echo "<td class=\"txright\">" . number_format($row['valor2'], 2, ",", ".") . "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr class="info">
                                <td colspan="8">&nbsp;</td>
                                <td class="text-center">Total:</td>
                                <td class="text-right">R$ <?php echo number_format($totalBem, 2, ",", ".") ?></td>
                            </tr>
                        </tfoot>
                    </table>
                    
                <?php }else{ ?>
                    <?php if ($projetoR!==null) { ?>
                    <br/>
                    <div class='alert alert-success'>
                        <p>Não foram adquiridos novos bens nesse mês</p>
                    </div>
                    <?php } ?>
                <?php } ?>
                    
                    <?php if ($projetoR!==null) { ?>
                        <?php if ($btfinalizar) { ?>
                            <p class="controls">
                                <?php if ($btfinalizarEsse) { ?>                                    
                                    <button type="submit" class="button btn btn-warning" name="finalizar"><span class="fa fa-power-off"></span>&nbsp;&nbsp;Finalizar Prestação</button>
                                <?php }else{ ?>                                    
                                    <button type="submit" class="button btn btn-primary" name="exportar"><span class="fa fa-share-square-o"></span>&nbsp;&nbsp;Exportar</button>
                                <?php } ?>
                            </p>
                        <?php }else{ ?>
                            <br/>
                            <?php if($total_verifica != 1){ ?>
                            <div class='alert alert-info'>
                                <p>Foi verificado a existencia de <?php echo $contErro?> projeto(s) para finalizar neste mês antes de gerar o arquivo de prestação de contas.</p>
                                <br />
                                <ul>
                                <?php foreach($projetosFaltante as $val){
                                    echo "<li>".$val."</li>";
                                }
                                ?>
                                </ul>
                            </div>
                                <?php if ($btfinalizarEsse) { ?>
                                <p class="controls">                                     
                                    <button type="submit" class="button btn btn-warning" name="finalizar"><span class="fa fa-power-off"></span>&nbsp;&nbsp;Finalizar Prestação</button>
                                </p>
                                <?php } ?>
                            <?php }else{ ?>
                            <div class='alert alert-warning'>
                                <p>Prestação finalizada.</p>
                            </div>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
            </form>
            
            <?php include_once '../template/footer.php'; ?>
            
        </div>
        </div>
    </body>
</html>