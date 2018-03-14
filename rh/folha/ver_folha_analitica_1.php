<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include "../../conn.php";
include "../../classes/funcionario.php";
include '../../classes_permissoes/regioes.class.php';
include '../../classes/FolhaClass.php';
include '../../classes/RescisaoClass.php';
include '../../classes/FeriasClass.php';
include '../../classes/CentroCustoClass.php';
include("../../wfunction.php");
include('../../funcoes.php');

function formata_numero($num) {
    if (strstr($num, '.') and !empty($num)) {
        return number_format($num, 2, ',', '.');
    } else {
        return $num;
    }
}

//OBJETO
$folha = new Folha();
$centrocusto = new CentroCusto();

//DADOS DO MASTER
$dados_parametro = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));
$master = $folha->getMaster($dados_parametro[0]); //PARAMETRO REGIAO
$masters = mysql_fetch_assoc($master);

//VERIFICANDO SE A FOLHA É DO TIPO 13°
$decimo_terceiro =  $folha->getVerificaDecimoByFolha($dados_parametro[1]);

//DADOS DOS CLTS NA FOLHA 
$clts = $folha->getDadosFolhaById($dados_parametro[1]); //PARAMETRO FOLHA


//VARIÁVEIS
$id_clt = $_REQUEST['id'];
$ano = $_REQUEST['ano'];
$id_projeto = $_REQUEST['projeto'];
$mesSel = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date("m");
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
$totalFolha = 0;
$totalDescontoFolha = 0;
$totalFuncionario = 0;
$totalCredito = array();
$totalGeralCred = 0;
$totalDebito = array();
$totalGeralDeb = 0;
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;


//PROJETOS DO MASTER 6
$projeto = array();
$proj = $folha->getProjeto(6);
while ($linha = mysql_fetch_assoc($proj)) {
    $projeto[$linha['id_projeto']] = $linha['nome'];
}

$creditoMenosDebito = array();

//DADOS DE FICHA FINANCEIRA POR CLT
$dados = $folha->getDadosClt($id_clt);
$d = mysql_fetch_assoc($dados);

//CARREGA DADOS DO USUÁRIO (NESSE CASO, LOGO DA EMPRESA VINCULADA AO USUÁRIO)
$usuario = carregaUsuario();

//ARRAY TIPO CREDITO
$creditos[] = " - ";
$mov_credito = $folha->getMovCredito();
while ($linha = mysql_fetch_assoc($mov_credito)) {
    $creditos[] = $linha["cod"];
}

//ARRAY TIPO DEBITO
$debitos[] = "5037";
$mov_debito = $folha->getMovDebito();
while ($linha = mysql_fetch_assoc($mov_debito)) {
    $debitos[] = $linha["cod"];
}

//ARRAY DE MESES
$meses = array();
$optMes = mesesArray();


//ARRAY DE ANOS
for ($i = 2010; $i <= date('Y'); $i++) {
    $optAnos[$i] = $i;
}

//GERARA
if (isset($_POST['gerar'])) {
    
    //DADOS PESSOAIS
    $cabecalho = $folha->getCabecalho();
    //MONTA MATRIZ
    $ficha = $folha->getFichaFinanceira($id_clt, $ano);
    //ITENS FICHA
    $itensFicha = $folha->getDadosFicha();
    
}
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>:: Intranet :: Folha de Pagamento</title>
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../adm/css/estrutura.css" rel="stylesheet" type="text/css" />
        <link href="../../js/jquery.ui.theme.css" rel="stylesheet" type="text/css" />
        <link href="../../js/jquery.ui.datepicker.css" rel="stylesheet" type="text/css" />
        
        <!--<link href="../../net1.css" rel="stylesheet" type="text/css" />-->
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />        
        
<!--        <script type="text/javascript" src="../../js/jquery-1.8.3.min.js"></script>
        <script type="text/javascript" src="../../js/abas_anos.js"></script>
        <script type="text/javascript" src="../../js/jquery.ui.core.js"></script>
        <script type="text/javascript" src="../../js/jquery.ui.widget.js"></script>
        <script type="text/javascript" src="../../js/jquery.ui.datepicker.js"></script>
        <script type="text/javascript" src="../../js/jquery.ui.datepicker-pt-BR.js"></script>
        <script type="text/javascript" src="../../js/global.js"></script>        -->
        
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript" ></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        
        <script>
            $(function(){
                $(".bt-cc").on("click", function() {
                    var type = $(this).data("type");
                    var key = $(this).data("key");
                    var regiao = $(this).data("regiao");
                    var centrocusto = $(this).data("cc");
                    
                    if(type === "cadastrar"){                        
                        thickBoxIframe("Cadastro de Centro de Custo", "action_folha_analitica.php", {clt: key, regiao: regiao, tipo: type}, "400-not", "150");
                    }else if(type === "editar"){
                        thickBoxIframe("Edição de Centro de Custo", "action_folha_analitica.php", {clt: key, regiao: regiao, tipo: type, cc: centrocusto}, "400-not", "150");
                    }
                });
            });
        </script>
        
        <style type="text/css">
            body{
                background: #fff;
            }
            .folha{
                width: 900px;
                padding: 5px;
                margin: 5px auto;
                background: #fff;
                padding-bottom: 30px;
            }
            .tatbela_analitica,.tatbela_analitica_totais{
                width: 900px;
                font-family: arial;
                font-size: 12px; 
                color: #333;
                padding: 2px;
            }
            .tatbela_analitica td, .tatbela_analitica_totais td{
                padding: 3px;
            }
            .tatbela_analitica,.tatbela_analitica tr, .tatbela_analitica_totais tr {
                border: 1px solid #333;
                margin: 0 auto;
            }
            .sem_borda{
                border: 0px;
            }
            .borda_direita{
                border-right: 1px solid #333;
                border-bottom: 1px solid #333;
            }
            .borda_superior{
                border-top: 1px solid #333;
            }
            .borda_inferior{
                border-bottom: 1px solid #333;
            }
            .txright{
                text-align: right;
            }
            .txcenter{
                text-align: center;
            }
            .txleft{
                text-align: left;
            }
            .font_pequena{
                font-size: 11px;
            }
            fieldset #resumo_liquido{
                width: 100%;
                padding: 4px;
                box-sizing: border-box;
            } 
            .itemLista{
                width: 100%;
                border: 0px solid #eee;
                margin: 7px 0px;
                height: 7px;
            }
            .itemLista .colEsq{
                width: 65%;
                float: left;
            }
            .itemLista .colDir{
                width: 35%;
                text-align: right;
                float: left;
            }
            .fundo_titulo{
                background: #f5f5f5;
            }
            .fildset_padrao{
                min-height: 95px;
            }
            
            .itemListaMaior .col_01, .itemListaMaior .col_02, .itemListaMaior .col_03, .itemListaMaior .col_04{
                width: 25%;
                float: left;
                min-width: 25%;
                max-width: 25%;
                text-align: right;
            }
            .filtro{
                margin: 10px 0px;
            }
            .fleft{float: left;}
            .fright{ float: right;}
            .txNegrito{
                font-weight: bold;
            }
            .rodape_folha{
                width: 900px;
                border: 1px solid #ccc;
                margin: 0 auto;
            }
            .quebra{ 
                page-break-before: always 
            }
            .no-estilo{
                color: #000;
                text-decoration: none;
            }
            #form_centrocusto{
                margin: 45px 0 0 0;
            }
        </style>
        <style type="text/css" media="print">
            .filtro, .no-print{
                display: none;
            }
        </style>
    </head>
    <body>
        <div class="filtro" style="width: 900px; margin: 0 auto;">
            <form  name="form" action="" method="post" id="form">
                <fieldset style="border: 1px solid #333; background: #f9f9f9; height: 50px">
                    <legend class="txleft" style="font-size: 16px;">FOLHA DE PAGAMENTO ANALÍTICA</legend>
                </fieldset>
            </form>
        </div>
        
        <?php while ($dados_clt = mysql_fetch_assoc($clts)) { ?>
            
            <?php
                
                $dadosProjeto = mysql_fetch_assoc(mysql_query("SELECT * FROM rhempresa WHERE id_projeto = {$dados_clt['id_projeto']} LIMIT 1"));
            
                $rescisao = new Rescisao();
                $ferias = new ferias();
                
                $totalFuncionario++;
                //MES E ANO DE REFERENCIA
                $mes = sprintf("%02d",$dados_clt['mes_proc']);
                
                $ano = $dados_clt['ano_proc'];
                $mes_periodo_inicio = "01/" . $mes . "/" . $ano;
                $mes_periodo_fim = date('t') . "/" . $mes . "/" . $ano;
                
                //MÉTODO PARA RETORNAR O PERIODO
                $periodo = $ferias->getPeriodoFerias($dados_clt['id_clt'], $mes, $ano);
                                
                //MÉTODO QUE CARREGA DADOS DAS FÉRIAS DO CLT
                $ferias = $ferias->getFeriasByClt($dados_clt['id_clt'], $mes, $ano);
                $dados_ferias = mysql_fetch_assoc($ferias);
                
                //MÉTODO QUE CARREGA RESCISÃO INDIVIDUAL
                $rescisao = $rescisao->getRescisaoByClt($dados_clt['id_clt'], $mes, $ano);             
                $dados_rescisao = mysql_fetch_assoc($rescisao);
                //print_r($dados_rescisao);
                                
                
                //MÉTODO QUE CARREGA DECIMO 3°
                $decimo = $folha->getDecimoTerceiroByClt($dados_clt['id_clt'], $dados_parametro[1]);
                $dados_decimo = mysql_fetch_assoc($decimo);
                               
                
                //CALCULO DE BASE
                $bases = $folha->getValoresBase($dados_clt['id_clt'], $dados_parametro[1]);
                $dados_bases = mysql_fetch_assoc($bases);
                
                //CALCULO DE BASE
                $totalBases = $folha->getTotaisBase($dados_parametro[1]);
                $dados_totais_bases = mysql_fetch_assoc($totalBases);
                
                //DIAS DE FALTAS
                $obs = $folha->getObsDeFaltaNoContraCheque($dados_clt['id_clt'], $mes, $ano);
                
            ?>
            
            <div class="folha">
                <table class="tatbela_analitica"  cellspacing="0" cellpadding="0">
                    <tbody>

                        <!-- CABEÇALHO DA FOLHA DADOS DA EMPRESA-->
                        <tr>
                            <td style="border: 0px;" class="txright"><span class="txNegrito">Empresa:</span></td>
                            <td colspan="3" style="border: 0px;"><?php echo $masters['nome']; ?></td>
                            <td colspan="2" style="border: 0px;"><span class="txNegrito">Projeto:</span> <?php echo $dadosProjeto['nome']; ?></td>
                            <td colspan="2" style="border: 0px;"><span class="txNegrito">CNPJ:</span> <?php echo $dadosProjeto['cnpj']; ?></td>
                        </tr>
                        <tr>
                            <td style="width: 10%; border: 0px;" class="txright"><span class="txNegrito">End.:</span></td>
                            <td colspan="5" style="width: 66%; border: 0px;"><?php echo $dadosProjeto['endereco']; ?> </td>
                            <td colspan="2"style="width: 12%; border: 0px;"><span class="txNegrito">CNPJ/CEI: </span><?php echo $masters['cnpj']; ?></td>
                        </tr>
                        <tr>
                            <td style="width: 20%; border: 0px;" class="txright"><span class="txNegrito">Ref.:</span></td>
                            <td colspan="2" style="width: 70%; border: 0px;"><?php echo $mes_periodo_inicio; ?> à <?php echo $mes_periodo_fim; ?></td>
                            <td style="width: 10%; border: 0px; " class="txright"><span class="txNegrito">Dpto: </span></td>
                            <td colspan="4" style="width: 38%; border: 0px;">TODOS</td>
                        </tr>
                        <?php if(mysql_num_rows($f) > 0){ ?>
                            <tr style="border: 0px;">
                                <td colspan="8" style="border: 0px;">OS VALORES DE FÉRIAS E RECISÃO JÁ FORAM PAGOS.</td>
                            </tr>
                        <?php }else{ ?>
                            <tr style="border: 0px;">
                                <td colspan="8" style="border: 0px;"></td>
                            </tr>
                        <?php } ?>
                        <tr style="border: 0px;">
                            <td colspan="8" style="border: 0px;"></td>
                        </tr>

                        <!-- DADOS DO FUNCIONARIO -->
                        <tr>
                            <td class="borda_direita borda_superior fundo_titulo font_pequena txcenter">Código</td>
                            <td class="borda_direita borda_superior fundo_titulo font_pequena txcenter">Nome</td>
                            <td class="borda_direita borda_superior fundo_titulo font_pequena txcenter">Ref.</td>
                            <td class="borda_direita borda_superior fundo_titulo font_pequena txcenter">Sal. Contratual</td>
                            <td class="borda_direita borda_superior fundo_titulo font_pequena txcenter">Adicionais</td>
                            <td class="borda_direita borda_superior fundo_titulo font_pequena txcenter">Descontos</td>
                            <td class="borda_direita borda_superior fundo_titulo font_pequena txcenter">Líquido</td>
                            <td class="borda_inferior borda_superior fundo_titulo font_pequena txcenter">Recibo</td>
                        </tr>

                        <tr>
                            <td style="border: 0px;"><?php echo $dados_clt['id_clt']; ?></td>
                            <td style="border: 0px;"><?php echo $dados_clt['nome']; ?></td>
                            <td style="border: 0px;"></td>
                            <td style="border: 0px;" class="txright"><?php echo $dados_clt['sallimpo']; ?></td>
                            <td style="border: 0px;"><span class="txNegrito">Função: </span><?php echo $dados_clt['funcao']; ?><br /> <span class="txNegrito">Admissão: </span><?php echo $dados_clt['data_admissao']; ?></td>
                            <td style="border: 0px;"></td>
                            <td style="border: 0px;" class="font_pequena">Livro: <br/> Dep IR: <?php echo (!empty($dados_clt['DEP_IRRF'])) ? $dados_clt['DEP_IRRF'] : "0"; ?></td>
                            <td style="border: 0px;" class="font_pequena">Folha: <br/> Dep SF: <?php echo (!empty($dados_clt['DEP_SALARIO_FAMILIA'])) ? $dados_clt['DEP_SALARIO_FAMILIA'] : "0"; ?></td>
                        </tr>

                        <!---FÉRIAS---->
                        <?php while($d = mysql_fetch_assoc($periodo)){ ?>
                            <tr>
                                <td colspan="8">Férias de <?php echo $d['data_inicio'] ?> até <?php echo $d['data_final'] ?> DIA(S) <?php echo $d['diferenca_dias'] ?> </td>
                            </tr>
                        <?php } ?>
                        
                        <!-- SEPARADOR -->
                        <tr>
                            <td colspan="8" style="padding: 10px 0px;"></td>
                        </tr>
                        <!-- MOVIMENTOS --->

                        <?php
                            //MÉTODO QUE MONTA OS MOVIMENTOS
                            $folha->getFichaFinanceira($dados_clt['id_clt'], $ano, $mes, $decimo_terceiro);
                            
                            //MÉTODO QUE RETORNA MATRIZ DE DADOS
                            $movimentos = $folha->getDadosFicha();

                            
                            $totalAdicionais = 0;
                            $totalDesconto = 0;
                        ?>
                        <?php foreach ($movimentos AS $key => $valor) { ?>
                            
                            <?php if($key != "5049"){ ?>    
                                <tr>
                                    <td style="border-bottom: 1px solid #ccc;"><?php echo $key; ?></td>
                                    <td style="border-bottom: 1px solid #ccc;" style="width: 100%;"><?php echo $valor["nome"]; ?></td>
                                    <td style="border-bottom: 1px solid #ccc; text-align: right;"><?php echo $valor["ref"]; ?></td>
                                    <td style="border-bottom: 1px solid #ccc;"></td>
                                    <td style="border-bottom: 1px solid #ccc;" class="txright">
                                        <?php
                                            if (in_array($key, $creditos)) {
                                                echo $valor[$mes];
                                                $totalAdicionais += $valor[$mes];
                                                $totalFolha += $valor[$mes];
                                                $totalFolhaIndividual += $valor[$mes];
                                            }
                                        ?>
                                    </td>
                                    <td style="border-bottom: 1px solid #ccc;" class="txright">
                                        <?php
                                            if (in_array($key, $debitos)) {

//                                                if($key == 5035){
//                                                    $totalFeriasInss += $valor[$mes];
//                                                }
//                                                
                                                if($key == 5036){
                                                    $totalFeriasIr += $valor[$mes];
                                                }
                                                echo $valor[$mes];
                                                $totalDesconto += $valor[$mes];
                                                $totalDescontoFolha += $valor[$mes];
                                                $totalDescontoFolhaIndividual += $valor[$mes];
                                            }
                                        ?>
                                    </td>
                                    <td style="border-bottom: 1px solid #ccc;"></td>
                                    <td style="border-bottom: 1px solid #ccc;"></td>
                                </tr>
                            <?php } ?>
                            <?php
                                $liquido = $totalAdicionais - $totalDesconto;  
                                if($liquido < 0){
                                   $liquido = "0.00"; 
                                }else{
                                   $liquido = $liquido;
                                }
                            ?>
                        <?php } ?>

                        <!-- TOTAIS -->
                        <tr>
                            <td colspan="4" style="border: 0px;"></td>
                            <td class="borda_superior txright" ><?php echo number_format($totalAdicionais,"2",",","."); ?></td>
                            <td class="borda_superior txright" ><?php echo number_format($totalDesconto,"2",",","."); ?></td>
                            <td class="borda_superior txright" > ********* <?php echo number_format($liquido,"2",",","."); ?></td>
                            <td style="border: 0px;"></td>
                        </tr>

                        <!-- SEPARADOR -->
                        <tr>
                            <td colspan="8" style="padding: 10px 0px;"></td>
                        </tr>
                        <!-- RESUMO DO LIQUIDO -->
                        <tr>
                            <td colspan="3" style="width: 35%">
                                <?php $resumo_liquido = 0; ?>
                                
                                <fieldset id="resumo_liquido">
                                    <legend> Resumos</legend>
<!--                                    <div class="itemLista">
                                        <div class="colEsq">(+) Folha Analítica </div>
                                        <div class="colDir"><?php //echo number_format($liquido,"2",",","."); ?></div>
                                        <?php //$resumo_liquido += $liquido; ?>
                                    </div>-->
                                    <div class="itemLista">
                                        <div class="colEsq">(-) Férias </div>
                                        <div class="colDir"><?php echo (!empty($dados_ferias['total_liquido'])) ? number_format($dados_ferias['total_liquido'],"2",",",".") : "0,00"; ?></div>
                                        <?php $resumo_liquido -= $dados_ferias['total_liquido']; ?>
                                    </div>
                                    <div class="itemLista">
                                        <div class="colEsq">(-) Rescisão </div>
                                        <div class="colDir"><?php echo (!empty($dados_rescisao['total_liquido'])) ? number_format($dados_rescisao['total_liquido'],"2",",",".") : "0,00"; ?></div>
                                        <?php $resumo_liquido -= $dados_rescisao['total_liquido']; ?>
                                    </div>
                                    <div class="itemLista">
                                        <div class="colEsq">(-) 13° salário</div>
                                        <div class="colDir">
                                            <?php 
                                                if($dados_decimo['mes'] == "11") {
                                                    $decimo_terc = $dados_decimo['decimo_terceiro'] + $dados_decimo['rend'];
                                                }else{
                                                    $decimo_terc = $dados_decimo['decimo_terceiro'] + $dados_decimo['rend'] - $dados_decimo['inss_dt'] - $dados_decimo['ir_dt'];
                                                }
                                                echo number_format($decimo_terc, "2",",",".");
                                            ?>
                                        </div>
                                    </div>
                                    
                                </fieldset>
                            </td>
                            <td style="width: 10%; vertical-align: bottom;">
                                __/__/____
                            </td>
                            <td colspan="3" style="width: 35%; vertical-align: bottom;">
                                ________________________________________________________
                            </td>
                        </tr>
                        <!-- BASE INSS -->
                        <tr>
                            <td class="txNegrito ">Base INSS:</td>
                            <?php 
                                $totalBaseFolhaInss += $dados_bases['base_inss']; 
                                $totalBaseFolhaIrrf += $dados_bases['base_irrf']; 
                                $totalBaseFolhaFgts += $dados_bases['base_fgts']; 
                            ?>
                            <td class="txleft" ><?php echo (!empty($dados_bases['base_inss'])) ? number_format($dados_bases['base_inss'],"2",",",".") : "0,00"; ?></td>
                            <td class="txNegrito" style="width: 40%">Base FGTS</td>
                            <td class="txright"><?php echo (!empty($dados_bases['base_inss'])) ? number_format($dados_bases['base_inss'],"2",",",".") : "0,00"; ?></td>
                            <td class="txNegrito">Base IRRF</td>
                            <td class="txleft"><?php echo (!empty($dados_bases['base_irrf'])) ? number_format($dados_bases['base_irrf'],"2",",",".") : "0,00"; ?></td>
                            <td class="txNegrito" colspan="2">Total Funcionários</td>
                            
                        </tr>
                        <tr>
                            <td class="txNegrito ">INSS</td>
                            <td class="txleft"><?php echo number_format($dados_clt['inss'],"2",",","."); ?></td>
                            <td class="txNegrito ">FGTS</td>
                            <td class="txright"><?php echo number_format($dados_clt['fgts'],"2",",","."); ?></td>
                            <td class="txNegrito ">IRRF</td>
                            <td class="txleft"><?php echo number_format($dados_clt['imprenda'],"2",",","."); ?></td>
                            <td class="txNegrito " colspan="2">1</td>                            
                        </tr>
                        <tr>
                            <td colspan="8"></td>
                        </tr>
                        <!-- RESUMO DA FOLHA -->
                        <?php $resultFolhaIndividual = 0; ?>
                        <?php $resultFolhaIndividual = $totalFolhaIndividual - $totalDescontoFolhaIndividual; ?>
                        <?php 
                            if($resultFolhaIndividual < 0){
                                $resultFolhaIndividual = 0.00;
                            }else{
                                $resultFolhaIndividual = $resultFolhaIndividual;
                            }
                        ?>
                        <tr>
                            <td colspan="8">
                                <fieldset class="fildset_padrao">
                                    <legend>Resumo da Folha</legend>
                                    <div class="itemLista">
                                        <div class="colEsq">Total Geral da Folha</div>
                                        <div class="colDir"><?php echo number_format($totalFolhaIndividual,"2",",","."); ?></div>
                                    </div>
                                    <div class="itemLista">
                                        <div class="colEsq">(-) Total de descontos</div>
                                        <div class="colDir"><?php echo number_format($totalDescontoFolhaIndividual,"2",",","."); ?></div>
                                    </div>
                                    <div class="itemLista">
                                        <div class="colEsq">(=) Total Líquido</div>
                                        <div class="colDir borda_superior"><?php echo number_format($resultFolhaIndividual,"2",",","."); ?></div>
                                    </div>
                                </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="8">
                                <?php print_r($obs); ?>
                            </td>
                        </tr>
                        <?php if($_COOKIE['logado'] == 260){ ?>
                        <tr>
                            <td colspan="8">
                                <strong>Centro de Custo:</strong>
                                <?php
                                $row_centrocusto = $centrocusto->getCentroCustoId($dados_clt['id_centro_custo']);
                                
                                if(!empty($row_centrocusto)){
                                ?>
                                <a href="javascript:;" name="centro_custo" id="centro_custo<?php echo $dados_clt['id_clt']; ?>" class="no-estilo bt-cc" data-type="editar" data-key="<?php echo $dados_clt['id_clt']; ?>" data-regiao="<?php echo $dados_clt['id_regiao']; ?>" data-cc="<?php echo $dados_clt['id_centro_custo']; ?>"><?php echo $row_centrocusto['nome']; ?></a>
                                <?php }else{ ?>
                                <a href="javascript:;" name="centro_custo_" id="centro_custo_<?php echo $dados_clt['id_clt']; ?>" class="no-print bt-cc" data-type="cadastrar" data-key="<?php echo $dados_clt['id_clt']; ?>" data-regiao="<?php echo $dados_clt['id_regiao']; ?>">Cadastrar</a>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php
                            //exit();
                        }
                            $totalFolhaIndividual = 0;
                            $totalDescontoFolhaIndividual = 0;
                            $resultFolhaIndividual = 0;
                        ?>
                    </tbody>
                </table>
            </div>        <div class="quebra" ></div>
            
        <?php } ?>
        
        <div class="rodape_folha">
            <table class="tatbela_analitica_totais" cellspacing="0" cellpadding="0">
                <tbody>
                    <!-- RESUMO DA FOLHA -->
                    <tr>
                        <td colspan="4">
                            <fieldset class="fildset_padrao">
                                <legend>Resumo da Folha</legend>
                                <div class="itemLista">
                                    <div class="colEsq">Total Geral da Folha</div>
                                    <div class="colDir"><?php echo number_format($totalFolha,"2",",","."); ?></div>
                                </div>
                                <div class="itemLista">
                                    <div class="colEsq">(-) Total de descontos</div>
                                    <div class="colDir"><?php echo number_format($totalDescontoFolha,"2",",","."); ?></div>
                                </div>
                                <div class="itemLista">
                                    <div class="colEsq">(=) Total Líquido</div>
                                    <div class="colDir borda_superior"><?php echo number_format($totalFolha - $totalDescontoFolha,"2",",","."); ?></div>
                                </div>
                            </fieldset>
                        </td>
                        <td colspan="4">
                            <fieldset class="fildset_padrao">
                                <legend>Informações adicionais</legend>
                                <div class="itemLista">
                                    <div class="colEsq">Total Funcionários</div>
                                    <div class="colDir"><?php echo $totalFuncionario; ?></div>
                                </div>
                                <div class="itemLista">
                                    <div class="colEsq">Total INSS</div>
                                    <div class="colDir"><?php echo number_format($dados_totais_bases['total_inss'],"2",",","."); ?></div>
                                </div>
                                <div class="itemLista">
                                    <div class="colEsq">Total FGTS</div>
                                    <div class="colDir"><?php echo number_format($dados_totais_bases['total_inss'],"2",",","."); ?></div>
                                </div>
                                <div class="itemLista">
                                    <div class="colEsq">Total IRRF</div>
                                    <div class="colDir"><?php echo number_format($dados_totais_bases['total_irrf'],"2",",","."); ?></div>
                                </div>
                            </fieldset>
                        </td>
                    <tr>
                        <td colspan="8">
                            <fieldset>
                                <legend>Resumo das Bases</legend>
                                <div class="itemListaMaior">
                                    <div class="col_01">
                                        <p style="color: #fff;">vazio</p>
                                    </div>
                                    <div class="col_02">
                                        <p>Base do INSS</p>
                                    </div>
                                    <div class="col_03">
                                        <p>Base do IRRF</p>
                                    </div>
                                    <div class="col_04">
                                        <p>Base do FGTS</p>
                                    </div>
                                </div>
                                <div class="itemListaMaior">
                                    <div class="col_01">
                                        <p class="txleft">Folha</p>
                                    </div>
                                    <div class="col_02">
                                        <p><?php echo number_format($totalBaseFolhaInss,"2",",","."); ?></p>
                                    </div>
                                    <div class="col_03">
                                        <p><?php echo number_format($totalBaseFolhaIrrf,"2",",","."); ?></p>
                                    </div>
                                    <div class="col_04">
                                        <p><?php echo number_format($totalBaseFolhaInss,"2",",","."); ?></p>
                                    </div>
                                </div>
                                <div class="itemListaMaior">
                                    <div class="col_01">
                                        <p class="txleft">Férias</p>
                                    </div>
                                    <div class="col_02">
                                        <p><?php echo number_format($totalFeriasInss,"2",",","."); ?></p>
                                    </div>
                                    <div class="col_03">
                                        <p><?php echo  number_format($totalFeriasIr,"2",",","."); ?></p>
                                    </div>
                                    <div class="col_04">
                                        <p>0,00</p>
                                    </div>
                                </div>
                                <div class="itemListaMaior">
                                    <div class="col_01">
                                        <p class="txleft">Rescisão</p>
                                    </div>
                                    <div class="col_02">
                                        <p>0,00</p>
                                    </div>
                                    <div class="col_03">
                                        <p>0,00</p>
                                    </div>
                                    <div class="col_04">
                                        <p>0,00</p>
                                    </div>
                                </div>
                                <div class="itemListaMaior">
                                    <div class="col_01">
                                        <p class="txleft">Décimo Terceiro</p>
                                    </div>
                                    <div class="col_02">
                                        <p>0,00</p>
                                    </div>
                                    <div class="col_03">
                                        <p>0,00</p>
                                    </div>
                                    <div class="col_04">
                                        <p>0,00</p>
                                    </div>
                                </div>
                                <!--TOTAL DAS BASES -->
                                <div class="itemListaMaior">
                                    <div class="col_01">
                                        <p class="txleft">Totais das Bases</p>
                                    </div>
                                    <div class="col_02">
                                        <p>0,00</p>
                                    </div>
                                    <div class="col_03">
                                        <p>0,00</p>
                                    </div>
                                    <div class="col_04">
                                        <p>0,00</p>
                                    </div>
                                </div>
                            </fieldset>
                        </td>
                    </tr>                    
                </tbody>
            </table>
        </div>
    </body>

</html>

