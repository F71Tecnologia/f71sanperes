<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include "../../conn.php";
include "../../classes/funcionario.php";
include '../../classes_permissoes/regioes.class.php';
include '../../classes/FolhaClass.php';
include("../../wfunction.php");

function formata_numero($num) {
    if (strstr($num, '.') and !empty($num)) {
        return number_format($num, 2, ',', '.');
    } else {
        return $num;
    }
}


//OBJETO
$folha = new Folha();

//VARIÁVEIS
$id_clt = $_REQUEST['id'];
$ano = $_REQUEST['ano'];
$id_projeto = $_REQUEST['projeto'];
$mesSel = (isset($_REQUEST['mes'])) ? $_REQUEST['mes']: date("m");
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
$total = 0;
$totalF = 0;
$totalCredito = array();
$totalGeralCred = 0;
$totalDebito = array();
$totalGeralDeb = 0;
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;


//PROJETOS DO MASTER 6
$projeto = array();
$proj = $folha->getProjeto();
while($linha = mysql_fetch_assoc($proj)){
    $projeto[$linha['id_projeto']] = $linha['nome']; 
}


$creditoMenosDebito = array();

//DADOS DE FICHA FINANCEIRA POR CLT
$dados = $folha->getDadosClt($id_clt);
$d = mysql_fetch_assoc($dados);


//CARREGA DADOS DO USUÁRIO (NESSE CASO, LOGO DA EMPRESA VINCULADA AO USUÁRIO)
$usuario = carregaUsuario();

//Array de tipos de creditos
$creditos[] = " - ";
$mov_credito = $folha->getMovCredito();
while ($linha = mysql_fetch_assoc($mov_credito)) {
    $creditos[] = $linha["cod"];
}


//Array de tipos de debitos
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
        <script type="text/javascript" src="../../js/jquery-1.3.2.js"></script>
        <script type="text/javascript" src="../../js/abas_anos.js"></script>
        <script type="text/javascript" src="../../js/jquery.ui.core.js"></script>
        <script type="text/javascript" src="../../js/jquery.ui.widget.js"></script>
        <script type="text/javascript" src="../../js/jquery.ui.datepicker.js"></script>
        <script type="text/javascript" src="../../js/jquery.ui.datepicker-pt-BR.js"></script>
        <script type="text/javascript">


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
            .tatbela_analitica{
                width: 900px;
                font-family: arial;
                font-size: 12px; 
                color: #333;
                padding: 2px;
            }
            .tatbela_analitica td{
                padding: 5px;
            }
            .tatbela_analitica,.tatbela_analitica tr{
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
            .itemListaMaior{
                width: 100%;
                border: 0px solid #ddd;
                padding: 4px;
                box-sizing: border-box;
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
        </style>
    </head>
    <body>
        <div class="folha">
            <h3>FOLHA DE PAGAMENTO ANALÍTICA</h3>
            <div class="filtro">
                <form  name="form" action="" method="post" id="form">
                    <fieldset style="border: 1px solid #333; background: #f9f9f9">
                        <legend class="txleft">FOLHA DE PAGAMENTO ANALÍTICA</legend>
                        <div class="fleft">
                            <p class="fleft" style="margin-right: 10px;"><label class="txleft" style='margin-top: 10px; text-align: left; float: left;'>Projeto:</label><br/> <?php echo montaSelect($projeto, $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'style' => 'padding: 4px; width: 200px; border: 1px solid #ccc;')); ?><br/></p>
                            <p class="fleft" style="margin-right: 10px;"><label class="txleft" style='margin-top: 10px; text-align: left; float: left;'>Mês:</label><br/>  <?php echo montaSelect($optMes, $mesSel, array('name' => "mes", 'id' => 'mes', 'style' => 'padding: 4px; width: 200px; border: 1px solid #ccc;')); ?><br/></p>
                            <p class="fleft" style="margin-right: 10px;"><label class="txleft" style='margin-top: 10px; text-align: left; float: left;'>Ano:</label><br/> <?php echo montaSelect($optAnos, $anoSel, array('name' => "ano", 'id' => 'ano', 'style' => 'padding: 4px; width: 200px; border: 1px solid #ccc;')); ?><br/></p>
                        </div>
                        <br class="clear"/>
                        <p class="controls" style="margin-top: 10px;">
                            <input type="hidden" name="id_master" value="<?php echo $id_master; ?>"/>
                            <input type="hidden" name="id_clt" value="<?php echo $id_clt; ?>"/>
                            <!--<input type="submit" name="historico" value="Exibir histórico" id="historico"/>-->
                            <input type="submit" name="gerar" value="Gerar" id="gerar" style=" padding: 5px 40px; border: 1px solid #f1f1f1; background: #f2f2f2" class="fright"/>
                        </p>
                    </fieldset>
                </form>
            </div>
            <table class="tatbela_analitica"  cellspacing="0" cellpadding="0">
                <tbody>
                    <!-- CABEÇALHO DA FOLHA-->
                    <tr>
                        <td style="width: 10%; border: 0px;" class="txright" >Empresa:</td>
                        <td colspan="5" style="width: 70%; border: 0px;" >INSTITUTO DOS LAGO RIOS</td>
                        <td colspan="2" style="width: 20%; border: 0px;" >Página: 0002</td>
                    </tr>
                    <tr>
                        <td style="width: 10%; border: 0px;" class="txright">End.:</td>
                        <td colspan="3" style="width: 30%; border: 0px;">RUA DO CARMO, 9 </td>
                        <td colspan="4"style="width: 60%; border: 0px;">CNPJ/CEI:</td>
                    </tr>
                    <tr>
                        <td style="width: 10%; border: 0px;" class="txright">Ref.:</td>
                        <td colspan="2" style="width: 30%; border: 0px;">01/01/2014 a 31/01/2014</td>
                        <td style="width: 10%; border: 0px; " class="txright">Dpto: </td>
                        <td colspan="4" style="width: 50%; border: 0px;">TODOS</td>
                    </tr>
                    <tr style="border: 0px;">
                        <td colspan="8" style="border: 0px;">OS VALORES DE FÉRIAS E RECISÃO JÁ FORAM PAGOS.</td>
                    </tr>
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
                        <td style="border: 0px;">00007</td>
                        <td style="border: 0px;">ASLISSON MORAES MARTINS</td>
                        <td style="border: 0px;"></td>
                        <td style="border: 0px;" class="txright">1.386,75</td>
                        <td style="border: 0px;">Função: VENDEDOR <br /> Admissão: 0/03/2013</td>
                        <td style="border: 0px;"></td>
                        <td style="border: 0px;" class="font_pequena">Livro: 0002 <br/> Dep IR: 0</td>
                        <td style="border: 0px;" class="font_pequena">Folha: 000 <br/> Dep SF: 0</td>
                    </tr>
                    <!---FÉRIAS---->
                    <tr>
                        <td colspan="8">Férias de 20/12/2013 até 05/01/2014 DIA(S) 5 (036:40) </td>
                    </tr>
                    <!-- SEPARADOR -->
                    <tr>
                        <td colspan="8" style="padding: 10px 0px;"></td>
                    </tr>
                    <!-- MOVIMENTOS --->

                    <tr>
                        <td style="border: 0px;">001</td>
                        <td style="border: 0px;">Salário base</td>
                        <td style="border: 0px;">190:40</td>
                        <td style="border: 0px;"></td>
                        <td style="border: 0px;" class="txright">1.201,85</td>
                        <td style="border: 0px;"></td>
                        <td style="border: 0px;"></td>
                        <td style="border: 0px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 0px;">604</td>
                        <td style="border: 0px;">Vale Transforte</td>
                        <td style="border: 0px;"></td>
                        <td style="border: 0px;"></td>
                        <td style="border: 0px;"></td>
                        <td style="border: 0px;" class="txright">61,02</td>
                        <td style="border: 0px;"></td>
                        <td style="border: 0px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 0px;">606</td>
                        <td style="border: 0px;">Adiantamento</td>
                        <td style="border: 0px;"></td>
                        <td style="border: 0px;"></td>
                        <td style="border: 0px;"></td>
                        <td style="border: 0px;" class="txright">462,25</td>
                        <td style="border: 0px;"></td>
                        <td style="border: 0px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 0px;">903</td>
                        <td style="border: 0px;">INSS Folha</td>
                        <td style="border: 0px;"></td>
                        <td style="border: 0px;"></td>
                        <td style="border: 0px;"></td>
                        <td style="border: 0px;" class="txright">95,32</td>
                        <td style="border: 0px;"></td>
                        <td style="border: 0px;"></td>
                    </tr>
                    <!-- TOTAIS -->
                    <tr>
                        <td colspan="4" style="border: 0px;"></td>
                        <td class="borda_superior txright" >1664,10</td>
                        <td class="borda_superior txright" >642,99</td>
                        <td class="borda_superior txright" > ********* 1522,11</td>
                        <td style="border: 0px;"></td>
                    </tr>
                    <!-- SEPARADOR -->
                    <tr>
                        <td colspan="8" style="padding: 10px 0px;"></td>
                    </tr>
                    <!-- RESUMO DO LIQUIDO -->
                    <tr>
                        <td colspan="3" style="width: 35%">
                            <fieldset id="resumo_liquido">
                                <legend> Resumo do líquido</legend>
                                <div class="itemLista">
                                    <div class="colEsq">(+) Folha Analítica </div>
                                    <div class="colDir">1.021,11</div>
                                </div>
                                <div class="itemLista">
                                    <div class="colEsq">(-) Adiantamento </div>
                                    <div class="colDir">462,25</div>
                                </div>
                                <div class="itemLista">
                                    <div class="colEsq">(-) Férias </div>
                                    <div class="colDir">0,00</div>
                                </div>
                                <div class="itemLista">
                                    <div class="colEsq">(-) Rescisão </div>
                                    <div class="colDir">0,00</div>
                                </div>
                                <div class="itemLista">
                                    <div class="colEsq">(-) 13° salário</div>
                                    <div class="colDir">0,00</div>
                                </div>
                                <div class="itemLista">
                                    <div class="colEsq">(=) Total Líquido</div>
                                    <div class="colDir borda_superior">558,66</div>
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
                        <td class="borda_inferior">Base INSS</td>
                        <td class="borda_inferior" >1.954,55</td>
                        <td class="borda_inferior">Base FGTS</td>
                        <td class="borda_inferior">1.199,45</td>
                        <td class="borda_inferior">FGTS</td>
                        <td class="borda_inferior">95,32</td>
                        <td class="borda_inferior">Base IRRF</td>
                        <td class="borda_inferior">1.191,45</td>
                    </tr>
                    <!-- RESUMO DA FOLHA -->
                    <tr>
                        <td colspan="4">
                            <fieldset class="fildset_padrao">
                                <legend>Resumo da Folha</legend>
                                <div class="itemLista">
                                    <div class="colEsq">Total Geral da Folha</div>
                                    <div class="colDir">1664,66</div>
                                </div>
                                <div class="itemLista">
                                    <div class="colEsq">(-) Total de descontos</div>
                                    <div class="colDir">664,00</div>
                                </div>
                                <div class="itemLista">
                                    <div class="colEsq">(=) Total Líquido</div>
                                    <div class="colDir borda_superior">1.004,00</div>
                                </div>
                            </fieldset>
                        </td>
                        <td colspan="4">
                            <fieldset class="fildset_padrao">
                                <legend>Informações adicionais</legend>
                                <div class="itemLista">
                                    <div class="colEsq">Total Funcionários</div>
                                    <div class="colDir">1</div>
                                </div>
                                <div class="itemLista">
                                    <div class="colEsq">Total INSS</div>
                                    <div class="colDir">95,32</div>
                                </div>
                                <div class="itemLista">
                                    <div class="colEsq">Total INSS</div>
                                    <div class="colDir">95,32</div>
                                </div>
                                <div class="itemLista">
                                    <div class="colEsq">Total FGTS</div>
                                    <div class="colDir">95,32</div>
                                </div>
                                <div class="itemLista">
                                    <div class="colEsq">Total IRRF</div>
                                    <div class="colDir">0,00</div>
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
                                        <p>1.191,45</p>
                                    </div>
                                    <div class="col_03">
                                        <p>1.191,45</p>
                                    </div>
                                    <div class="col_04">
                                        <p>1.191,45</p>
                                    </div>
                                </div>
                                <div class="itemListaMaior">
                                    <div class="col_01">
                                        <p class="txleft">Férias</p>
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
                                        <p>1.191,45</p>
                                    </div>
                                    <div class="col_03">
                                        <p>1.191,45</p>
                                    </div>
                                    <div class="col_04">
                                        <p>1.191,45</p>
                                    </div>
                                </div>
                            </fieldset>
                        </td>
                    </tr>
                    </tr>


                </tbody>
            </table>
        </div>
    </body>

</html>
