<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
}

include("../conn.php");
include("../wfunction.php");
include("../classes/regiao.php");
include("../classes/BotoesClass.php");
include("../classes/ProjetoClass.php");
include("../classes/EntradaClass.php");
include("../classes/ObrigacoesClass.php");
include("../classes_permissoes/acoes.class.php");

$ACOES = new Acoes();

$usuario = carregaUsuario();

$objObrigacoes = new ObrigacoesClass();
$tiposObrigacoes = $objObrigacoes->getTipoObrigacoes();

$objProjeto = new ProjetoClass();
//$dadosProjeto = $objProjeto->getProjetos();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$dadosProjeto = $objProjeto->getProjetosUser(implode(', ', array_keys($dadosHeader['regioes'])));

$objRegiao = new regiao();

$objEntrada = new Entrada();

//CARREGANDO MENU DE ACORDO COM AS PERMISSOES DA PESSOA
$botoes = new BotoesClass($dadosHeader['defaultPath'],$dadosHeader['fullRootPath']);
$botoesMenu = $botoes->getBotoesMenuModulo(4);
$icon = $botoes->iconsModulos;
$master = $usuario['id_master'];

function removeAdm($value){
    //preg_replace("/[aA]dministração de /", "", $input_lines);
    $pattern = "/[aA][dD]MINISTRAÇÃO DE /";
    $re = preg_replace($pattern, "", $value);
    return $re;
}

function AddData($data1, $format='Y-m-d', $d=0, $m=0, $a=0, $h=0, $i=0, $s=0){
    $data1 = new DateTime(str_replace('/', '-', $data1));
    
    $data1->modify("$d day");
    $data1->modify("$m month");
    $data1->modify("$a year");
    $data1->modify("$h hour");
    $data1->modify("$i minute");
    $data1->modify("$s second");
    $data = $data1->format($format);
    
    return $data;
}

function DiffData($data1, $data2, $format='days'){
    $data1 = new DateTime(str_replace('/', '-', $data1));
    $data2 = new DateTime(str_replace('/', '-', $data2));
    $data = $data2->diff($data1);
    if($format == 'full'){
        return "$data->y Ano(s), $data->m Mês(es) e $data->d Dia(s)";
    } else {
        return $data->$format;
    }
}

$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"2", "area"=>"Administrativo", "id_form"=>"form1", "ativo"=>"Principal"); ?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Administrativo</title>
        <link href="../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../resources/css/add-ons.min.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>

        <div class="container">

            <div class="row">

                <div class="col-sm-12">
                    <div class="page-header box-admin-header"><h2><?=$icon[2]?> - ADMINISTRATIVO</h2></div>
                    <div>
                        <ul class="nav nav-tabs text-sm" style="margin-bottom: 15px;">
                            <li class="active"><a href="#avisos" data-toggle="tab">PRINCIPAL</a></li>
                            <?php foreach ($botoesMenu as $k => $btMenu) { ?>
                                <li><a href="#<?=$k?>" class="text-uppercase" data-toggle="tab"><?=removeAdm($btMenu)?></a></li>
                            <?php } ?>
                        </ul>
                        <div id="myTabContent" class="tab-content">
                            <div class="tab-pane fade active in" id="avisos">
                                <div class="col-sm-12 no-padding">
                                    <?php if ($ACOES->verifica_permissoes(90)) { ?>
                                    <div class="col-lg-4 col-sm-6 pointer stat" data-key="proj">
                                        <div class="stat-panel">
                                            <div class="stat-row">
                                                <div class="stat-cell bg-info darker"><!-- Success darker background -->
                                                    <i class="fa fa-home bg-icon" style="font-size:60px;line-height:64px;height:64px;"></i><!-- Stat panel bg icon -->
                                                    <span class="text-bg">Projeto</span><br><!-- Big text -->
                                                    <span class="text-sm"></span><!-- Small text -->
                                                </div>
                                            </div> <!-- /.stat-row -->
                                            <div class="stat-row">
                                                <div class="stat-counters bg-info no-border-b no-padding text-center">
                                                    <div class="stat-cell col-xs-12 padding-sm no-padding-hr"><!-- Small padding, without horizontal padding -->
                                                        <div class="col-sm-6 text-left text-sm">VISUALIZAR</div>
                                                        <div class="col-sm-6 text-right"><i class="fa fa-arrow-circle-down"></i></div><!-- Extra small text -->
                                                    </div>
                                                </div> <!-- /.stat-counters -->
                                            </div>
                                        </div>
                                    </div>
                                    <?php } ?>
                                    <?php if ($ACOES->verifica_permissoes(88)) { ?>
                                    <div class="col-lg-4 col-sm-6 pointer stat" data-key="nf">
                                        <div class="stat-panel">
                                            <div class="stat-row">
                                                <div class="stat-cell bg-warning darker"><!-- Success darker background -->
                                                    <i class="fa fa-file-text bg-icon" style="font-size:60px;line-height:64px;height:64px;"></i><!-- Stat panel bg icon -->
                                                    <span class="text-bg">Notas Fiscais</span><br><!-- Big text -->
                                                    <span class="text-sm"></span><!-- Small text -->
                                                </div>
                                            </div> <!-- /.stat-row -->
                                            <div class="stat-row">
                                                <div class="stat-counters bg-warning no-border-b no-padding text-center">
                                                    <div class="stat-cell col-xs-12 padding-sm no-padding-hr"><!-- Small padding, without horizontal padding -->
                                                        <div class="col-sm-6 text-left text-sm">VISUALIZAR</div>
                                                        <div class="col-sm-6 text-right"><i class="fa fa-arrow-circle-down"></i></div><!-- Extra small text -->
                                                    </div>
                                                </div> <!-- /.stat-counters -->
                                            </div>
                                        </div>
                                    </div>
                                    <?php } ?>
                                    <?php if ($ACOES->verifica_permissoes(67)) { ?>
                                    <div class="col-lg-4 col-sm-6 pointer stat" data-key="obrig">
                                        <div class="stat-panel">
                                            <div class="stat-row">
                                                <div class="stat-cell bg-success darker"><!-- Success darker background -->
                                                    <i class="fa fa-building-o bg-icon" style="font-size:60px;line-height:64px;height:64px;"></i><!-- Stat panel bg icon -->
                                                    <span class="text-bg">Obrigações da Instituíção</span><br><!-- Big text -->
                                                    <span class="text-sm"></span><!-- Small text -->
                                                </div>
                                            </div> <!-- /.stat-row -->
                                            <div class="stat-row">
                                                <div class="stat-counters bg-success no-border-b no-padding text-center">
                                                    <div class="stat-cell col-xs-12 padding-sm no-padding-hr"><!-- Small padding, without horizontal padding -->
                                                        <div class="col-sm-6 text-left text-sm">VISUALIZAR</div>
                                                        <div class="col-sm-6 text-right"><i class="fa fa-arrow-circle-down"></i></div><!-- Extra small text -->
                                                    </div>
                                                </div> <!-- /.stat-counters -->
                                            </div>
                                        </div>
                                    </div>
                                    <?php } ?>
                                    <div class="clear"></div>
                                </div>
                                <div class="clear"></div>
                                <!-- AVISO DOS PROEJTOS -->
                                <div class="panel panel-info hide panel-proj">
                                    <div class="panel-heading"><h3 class="panel-title">Projetos</h3></div>
                                    <div class="panel-body">
                                        <table class="table table-hover table-condensed text-sm valign-middle">
                                            <thead>
                                                <tr>
                                                    <th>Nome</th>
                                                    <th>Local</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach ($dadosProjeto as $row_projeto) {
                                                    $objRegiao->MostraRegiao($row_projeto['id_regiao']);
                                                    $textClass = $textoStatus = null;
                                                    
                                                    $data_inicio = $row_projeto['inicio'];
                                                    $data_termino = $row_projeto['termino'];
                                                    $sql_subprojeto = "SELECT * FROM subprojeto WHERE id_projeto = {$row_projeto['id_projeto']} ORDER BY id_subprojeto DESC LIMIT 1";
                                                    $sql_subprojeto = mysql_query($sql_subprojeto) or die(mysql_error());
                                                    while($row_subprojeto = mysql_fetch_assoc($sql_subprojeto)){
                                                        $textoStatus = "RENOVAÇÃO ";
                                                        $data_inicio = $row_subprojeto['inicio'];
                                                        $data_termino = $row_subprojeto['termino'];
                                                    }

                                                    $hoje = date("Y-m-d");
                                                    $diferenca_dias = DiffData($data_termino, $hoje);

                                                    if($hoje > $data_termino){
                                                        $textClass = 'text-danger'; $textoStatus .= "EXPIRADA";
                                                    } else if($hoje == $data_termino){
                                                        $textClass = 'text-warning'; $textoStatus .= "EXPIRA HOJE";
                                                    } else if($hoje >= $data_inicio && $hoje < $data_termino && $diferenca_dias <= 60){
                                                        $textClass = 'text-info'; $textoStatus .= "EXPIRA EM $diferenca_dias DIAS";
                                                    } else {
                                                        $textoStatus = null;
                                                    }
//                                                    if(!empty($status)){
//                                                        echo '<p style="font-family: \'Courier New\', Courier, monospace;">'.str_replace(" ","&nbsp;",str_pad($row['nome'], 30, " ", STR_PAD_BOTH)).' => '.$status.'</p>';
//                                                    }
                                                    
                                                    if(!empty($textClass) AND !empty($textoStatus)){ ?>
                                                        <tr>
                                                            <td><?=$row_projeto['id_projeto'] . ' - ' . $row_projeto['nome']; ?> </td>
                                                            <td><?=$objRegiao->regiao; ?></td>
                                                            <td class="<?=$textClass?>"><?=$textoStatus?></td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- AVISO DE NOTAS FISCAIS -->
                                <div class="panel panel-warning hide panel-nf">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Notas Fiscais</h3>
                                    </div>
                                    <div class="panel-body">
                                        <table class="table table-hover table-condensed text-sm valign-middle">
                                            <thead>
                                                <tr>
                                                    <th>Projeto</th>
                                                    <th>Valor NF/ Carta Medição</th>
                                                    <th>Repasse</th>
                                                    <th>Diferença</th>
                                                    <th>Ano</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach ($dadosProjeto as $row_projeto) {
                                                    for ($ano = 2010; $ano <= date('Y'); $ano++) {
                                                        $qr_notas = mysql_query("SELECT * FROM notas WHERE id_projeto = '{$row_projeto['id_projeto']}' AND status = '1'   AND YEAR(data_emissao) = '$ano' ORDER BY data_emissao DESC");
                                                        $num_notas = mysql_num_rows($qr_notas);

                                                        while ($row_notas = mysql_fetch_assoc($qr_notas)){
                                                            // totalizadores por ano						
                                                            $total_ano += $row_notas['valor'];

                                                            $totalizador_repasse_anos += $objEntrada->getValorNotasAno($row_notas['id_notas'], $ano);
                                                            $totalizador_valor += $row_notas['valor'];
                                                        }
                                                        $totalizador_diferenca_anos = ($totalizador_repasse_anos - $totalizador_valor);
                                                        if (!empty($totalizador_diferenca_anos)) { ?>
                                                            
                                                            <tr <?=($totalizador_diferenca_anos < 0)?'class="danger"':''?>>
                                                                <td>(<?=$row_projeto['id_projeto']?>) <?=$row_projeto['nome']?></td>
                                                                <td>R$ <?=number_format($totalizador_valor, 2, ',', '.')?></td>
                                                                <td>R$ <?=number_format($totalizador_repasse_anos, 2, ',', '.')?></td>
                                                                <td>R$ <?=number_format($totalizador_diferenca_anos, 2, ',', '.')?></td>
                                                                <td><?= $ano ?></td>
                                                            </tr>
                                                        <?php }
                                                        unset($totalizador_repasse_anos, $totalizador_diferenca_anos, $totalizador_valor);
                                                    } //ANOS
                                                } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- AVISO DE NOTAS FISCAIS -->
                                
                                <!-- AVISO DE OBRIGAÇÕES DA INSTITUIÇÃO -->
                                <div class="panel panel-success hide panel-obrig">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Obrigações da Instituíção</h3>
                                    </div>
                                    <div class="panel-body">
                                        <table class="table table-hover table-condensed text-sm valign-middle">
                                            <thead>
                                                <tr>
                                                    <th>Documento</th>
                                                    <th class="text-center">Data de Publicação</th>
                                                    <th class="text-center">Validade</th>
                                                    <th class="">Descrição</th>
                                                    <th class="text-center">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach ($tiposObrigacoes AS $row_tipo_oscip) {

                                                    //Pegando o registro mais recente
                                                    $obrigacoes = $objObrigacoes->getObrigacoes("tipo_oscip = '{$row_tipo_oscip['tipo_nome']}' AND periodo != 'indeterminado'", 'data_publicacao DESC', 1);
                                                    $row_oscip = $obrigacoes[0];

                                                    if (count($row_oscip) == 0)
                                                        continue;
                                                    
                                                    $prazo_dias = 60;

                                                    list($ano, $mes, $dia) = explode('-', $row_oscip['data_publicacao']);
                                                    //descobre a data de vencimento	
                                                    switch ($row_oscip['periodo']) {
                                                        case 'Dias':
                                                            $data_vencimento = mktime(0, 0, 0, $mes, $dia + $row_oscip['numero_periodo'], $ano);
                                                        break;

                                                        case 'Meses':
                                                            $data_vencimento = mktime(0, 0, 0, $mes + $row_oscip['numero_periodo'], $dia, $ano);
                                                        break;

                                                        case 'Anos':
                                                            $data_vencimento = mktime(0, 0, 0, $mes, $dia, $ano + $row_oscip['numero_periodo']);
                                                        break;

                                                        case 'Período':
                                                            list($ano_2, $mes_2, $dia_2) = explode('-', $row_oscip['oscip_data_termino']);
                                                            $data_vencimento = mktime(0, 0, 0, $mes_2, $dia_2, $ano_2);
                                                        break;
                                                    }

                                                    ///VERIFICANDO SE O DOCUMENTO ESTÁ  DENTRO DO PERÍODO DE 45 DIAS ANTES DA DATA DE VENCIMENTO
                                                    $qnt_dias_vencimento = $objObrigacoes->getDiasVencimento(date('Y-m-d', $data_vencimento), $row_tipo_oscip['tipo_nome'], "data_publicacao DESC", 1);

                                                    $mesg = null;
                                                    if ($qnt_dias_vencimento == 0) {
                                                        $mesg = '<span style="color:#F60;">Expira hoje!</span>';
                                                    } else if ($qnt_dias_vencimento < 0) {
                                                        $mesg = '<span  style="color:#F00;">Expirado</span>';
                                                    } else if ($qnt_dias_vencimento > 0 and $qnt_dias_vencimento < $prazo_dias) {
                                                        $mesg = '<span style="color:#09C;">Expira em ' . $qnt_dias_vencimento . ' dias!</span>';
                                                    }

                                                    if (!empty($mesg)) { ?>
                                                        <tr>
                                                            <td width="20%"><?=$row_oscip['tipo_oscip']?></td>
                                                            <td width="12%" class="text-center"><?=implode('/', array_reverse(explode('-', $row_oscip['data_publicacao'])))?></td>
                                                            <td width="8%" class="text-center"><?=$row_oscip['numero_periodo'] . ' ' . $row_oscip['periodo']?></td>
                                                            <td width="48%">
                                                                <?php if (empty($row_oscip['descricao'])) { ?>
                                                                    <span style='font-style: italic; color: red;'>Descrição não informado.</span>
                                                                <?php } else { 
                                                                    echo $row_oscip['descricao'];
                                                                } ?>
                                                            </td>
                                                            <td width="12%" class="text-center"><?=$mesg?></td>
                                                        </tr>
                                                    <?php } 
                                                    unset($mesg);
                                                } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- AVISO DE OBRIGAÇÕES DA INSTITUIÇÃO -->
                            </div>
                            <?php foreach ($botoesMenu as $k => $btMenu) { ?>
                                <div class="tab-pane fade" id="<?php echo $k ?>">
                                    <div class="detalhes-modulo">
                                        <?php echo $botoes->getHtmlBotoesModulo($k, 2) ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php include("../template/footer.php"); ?>
        </div>

        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script>
            $(function() {
                $('body').on('click', '.stat', function(){
                    var key = $(this).data('key');
                    $(".panel-obrig, .panel-nf, .panel-proj").addClass('hide');
                    $(".panel-"+key).removeClass('hide');
                });
            });
        </script>
    </body>
</html>