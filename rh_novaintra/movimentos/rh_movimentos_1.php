<?php
ob_start();
session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include "../../conn.php";
include "../../funcoes.php";
include "../../classes/funcionario.php";
include '../../classes_permissoes/regioes.class.php';
include '../../classes/CalculoFolhaClass.php';
include '../../classes/MovimentoClass.php';
include("../../wfunction.php");

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

error_reporting(0);
$Fun = new funcionario();
$Fun->MostraUser(0);
$Master = $Fun->id_master;
$REGIOES = new Regioes();
$objCalcFolha = new Calculo_Folha();
$objCalcFolha->CarregaTabelas(date('Y'));
if (isset($_POST['excluir'])) {
    $id_movimento = $_POST['id_movimento'];
    mysql_query("UPDATE rh_movimentos_clt SET status = '0' WHERE id_movimento = '$id_movimento' LIMIT 1");
    exit;
}

// Recebendo a variável criptografada
$enc = $_REQUEST['enc'];
$encPagina = $enc;
$enc = str_replace("--", "+", $enc);
$link1 = decrypt($enc);
$teste = explode("&", $link1);
$regiao = $teste[0];
$clt =$_REQUEST['clt'];
$projeto = $teste[1];
$pagina_atual = $_REQUEST['pg'];
///MOVIMENTOS DE Débito
$array_horistas = array("5425", "5426", "5512");

$objMovimento = new Movimentos();
$movLancado = $objMovimento->getMovimentosLancadosPorClt($clt);
$objMovimento->carregaMovimentos(date('Y'));

$qr_clt = mysql_query("SELECT A.*,B.nome as funcao, B.id_curso, B.salario ,B.cbo_codigo, F.horas_mes, D.regiao as nome_regiao,E.nome as nome_projeto,A.id_projeto, B.tipo_insalubridade, B.qnt_salminimo_insalu,
                       B.periculosidade_30, F.adicional_noturno, F.horas_noturnas, F.nome as nome_horario, F.id_horario
                        FROM rh_clt as A 
                       LEFT JOIN curso as B ON A.id_curso = B.id_curso
                       LEFT JOIN rhstatus as C ON C.codigo = A.status
                       LEFT JOIN regioes as D ON D.id_regiao = A.id_regiao
                       LEFT JOIN projeto as E ON E.id_projeto = A.id_projeto
                       LEFT JOIN rh_horarios as F ON F.id_horario = A.rh_horario
                       WHERE A.id_clt = $clt");
$row_clt = mysql_fetch_assoc($qr_clt);

if($row_clt['periculosidade_30']){
    $periculosidade = $objCalcFolha->getPericulosidade($row_clt['salario']);
}
$insalubridade = $objCalcFolha->getInsalubridade(30, $row_clt['tipo_insalubridade'], $row_clt['qnt_salminimo_insalu'], date('Y'));

//echo $row_clt['adicional_noturno'];

if($row_clt['adicional_noturno']){
 $baseCalcAdiconal = $row_clt['salario']+ $insalubridade['valor_integral'] + $periculosidade['valor_integral'];   
$adicional_noturno = $objCalcFolha->getAdicionalNoturno($baseCalcAdiconal, $row_clt['horas_mes'], $row_clt['horas_noturnas']);
$dsr = $objCalcFolha->getDsr($adicional_noturno['valor_integral']);
        
}

$baseCalc = $row_clt['salario']+ $insalubridade['valor_integral'] + $periculosidade['valor_integral'] + $adicional_noturno['valor_integral'] + $dsr['valor_integral'];
$valor_diario  = ($baseCalc) / 30;
$valor_hora    = ($baseCalc)/$row_clt['horas_mes'];

$baseCalcAtraso =  $row_clt['salario']+ $insalubridade['valor_integral'] + $periculosidade['valor_integral'] ;
$valor_diarioAtraso  = ($baseCalcAtraso) / 30;
$valor_horaAtraso    = ($baseCalcAtraso)/$row_clt['horas_mes'];

$qr_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_user = mysql_fetch_assoc($qr_user);
$id_master = $row_user['id_master'];

/*
 * Verificação se há folha aberta. caso haja, o mês selecionado será o mês da folha.
 * Alteração feita pq o pessoal do RH estava se confundindo quando o mês virava.
 */
$query = "SELECT mes,ano FROM rh_folha WHERE projeto = '{$row_clt['id_projeto']}' AND status = 2";
$mes_folha_aberta = mysql_query($query);
$data_folha = mysql_fetch_assoc($mes_folha_aberta);
$mesSel = "";
if(mysql_num_rows($mes_folha_aberta) > 0){
    $mesSel = $data_folha['mes'];
}else{
    $mesSel = date('m');
}

//ANO
$optAnos = array();
for ($i = 2009; $i <= date('Y')+1; $i++) {
    $optAnos[$i] = $i;
}
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');

///REGIÕES
$regioes = montaQuery('ano_meses', "num_mes,nome_mes", "1");
$optMes = array();
foreach ($regioes as $valor) {
    $optMes[$valor['num_mes']] = $valor['num_mes'] . ' - ' . $valor['nome_mes'];
}
$optMes[13] = '13º Primeira Parcela';
$optMes[14] = '13º Segunda Parcela';
$optMes[15] = '13º Integral';
$optMes[16] = 'Rescisão';

/////////////////////////////////////////////////////////      
/////////////// GRAVAÇÃO NO BANCO DE DADOS///////////////      
/////////////////////////////////////////////////////////      
if (isset($_POST['confirmar'])) {

    $mes = $_POST['mes'];
    $ano = $_POST['ano'];
    $id_regiao = $_POST['regiao'];
    $id_projeto = $_POST['projeto'];
    $id_clt = $_POST['clt'];
    $movimentos = $_POST['mov_valor'];
    $movimentos_sempre = $_POST['mov_sempre'];
    $quant = $_POST['mov_qtd'];
    $tipos = $_POST['tipo_quantidade'];

    $qr_funcao = mysql_query("SELECT B.salario FROM rh_clt as A 
                              INNER JOIN curso as B
                              ON A.id_curso = B.id_curso 
                              WHERE A.id_clt = '$id_clt'");
    $row_funcao = mysql_fetch_assoc($qr_funcao);

    ///PEGANDO AS INFORMAÇÔES DOS MOVIMENTOS
    $qr_mov = mysql_query("SELECT * FROM rh_movimentos WHERE mov_lancavel = 1");
    while ($row_mov = mysql_fetch_assoc($qr_mov)) {
        $codigo_movimento[$row_mov['id_mov']] = $row_mov['cod'];
        $tipo_movimento[$row_mov['id_mov']] = ($row_mov['categoria'] == 'DESCONTO' or $row_mov['categoria'] == 'DEBITO') ? 1 : 2;
        $nome_movimento[$row_mov['id_mov']] = $row_mov['descicao'];
        $percentual_movimento[$row_mov['id_mov']] = $row_mov['percentual'];
        $incidencia_inss[$row_mov['id_mov']] = $row_mov['incidencia_inss'];
        $incidencia_irrf[$row_mov['id_mov']] = $row_mov['incidencia_irrf'];
        $incidencia_fgts[$row_mov['id_mov']] = $row_mov['incidencia_fgts'];
    }

    foreach ($movimentos as $id_mov => $valor) {
        $incidencia = array();
        if (!empty($valor) and $valor != '0,00') {
            $lancamento = ($movimentos_sempre[$id_mov] == 2) ? 2 : 1;
            $incidencia[0] = ($incidencia_inss[$id_mov] == 1) ? '5020' : '';
            $incidencia[1] = ($incidencia_irrf[$id_mov] == 1) ? '5021' : '';
            $incidencia[2] = ($incidencia_fgts[$id_mov] == 1) ? '5023' : '';
            $incidencia = implode(',', $incidencia);
            $valorf = str_replace(',', '.', str_replace('.', '', $valor));
            $tipo_mov = ($tipo_movimento[$id_mov] == 1) ? 'DEBITO' : 'CREDITO';
//          echo "<pre>
//            SELECT * FROM rh_movimentos_clt 
//            WHERE 
//            ((mes_mov = $mes AND ano_mov = $ano) OR (lancamento = 2 AND mes_mov NOT IN(13,14,15,16)))
//            AND status = 1 AND id_clt = $id_clt AND id_mov = $id_mov</pre>";
            ////VERIFICA MOVIMENTO LANÇADO
            $qr_verifica = mysql_query("
            SELECT * FROM rh_movimentos_clt 
            WHERE 
            ((mes_mov = $mes AND ano_mov = $ano) OR (lancamento = 2 AND mes_mov NOT IN(13,14,15,16)))
            AND status = 1 AND id_clt = $id_clt AND id_mov = $id_mov") or die(mysql_error().'yiu');
            
            if(mysql_num_rows($qr_verifica) != 0){
                $row_verifica = mysql_fetch_assoc($qr_verifica);
                $mov_cadastrados[$id_mov] = $row_verifica['nome_movimento'];
            } else {
                // VERIFICANDO SE O VALOR DA AJUDA DE CUSTO PASSA DE 50% DO SALARIO DO CARA, PARA COLOCAR INCIDENCIA EM INSS,IRRF,FGTS
                if ($id_mov == 13) {
                    $metade = $row_funcao['salario'] / 2;
                    if ($valor > $metade) {
                        $incidencia = "5020,5021,5023";
                    }
                }

                $tp = (isset($tipos[$id_mov])) ? $tipos[$id_mov]:"(NULL)";

                if(isset($quant[$id_mov])) {
                    if($tp == 1){
                        $qnt_horas = $quant[$id_mov];
                        $qnt = '';
                    } else {
                        $qnt = $quant[$id_mov];
                        $qnt_horas = '';
                    }
                } else {
                    $qnt = "(NULL)";
                }

                $sql_mov[] = "('$id_clt','$id_regiao','$id_projeto','$mes','$ano','$id_mov','" . $codigo_movimento[$id_mov] . "',
                            '$tipo_mov','" . $nome_movimento[$id_mov] . "',NOW(),'$_COOKIE[logado]','$valorf','" . $percentual_movimento[$id_mov] . "',
                            '$lancamento','$incidencia','$qnt','$tp', '$qnt_horas')";
            }
        }
        unset($incidencia);
    }
    
    $_SESSION['mov_cadastrados'] = $mov_cadastrados;
    if (sizeof($sql_mov) > 0) {
        
        $sql_mov = implode(',', $sql_mov);
        mysql_query("INSERT INTO rh_movimentos_clt(id_clt,id_regiao,id_projeto,mes_mov,ano_mov,id_mov,cod_movimento,tipo_movimento,nome_movimento,
                        data_movimento,user_cad,valor_movimento,percent_movimento,lancamento,incidencia,qnt,tipo_qnt, qnt_horas) VALUES
                        $sql_mov");
    }
    
    header('Location: rh_movimentos_1.php?clt='.$id_clt);
    exit;
}

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Lançar Movimentos");
$breadcrumb_pages = array("Gestão de RH"=>"../index.php", "Movimentos"=>"rh_movimentos.php");

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Lançar Movimentos</title>
        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Lançar Movimentos</small></h2></div>
                </div><!-- /.col-lg-12 -->
            </div><!-- /.row -->
            <form class="form-inline" role="form" id="form1" method="post">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="col-lg-6">
                            <p><strong>Nome: </strong> <?=$row_clt['nome']?></p>
                            <p><strong>Função: </strong> <?=$row_clt['id_curso'].' - '.$row_clt['funcao']?></p>
                            <p><strong>Região: </strong> <?=$row_clt['nome_regiao']?></p>
                            <p><strong>Projeto: </strong> <?=$row_clt['nome_projeto']?></p>
                            <p><strong>Horário: </strong> <?=$row_clt['id_horario']. ' - '.$row_clt['nome_horario']?></p>
                            <p><strong>Horas no mês: </strong> <?=$row_clt['horas_mes'].' horas';?></p>
                        </div>
                        <div class="col-lg-6">
                            <p><strong>Salário Contratual: </strong> R$ <?=number_format($row_clt['salario'],"2",",",".")?></p>
                            <p><strong>Insalubridade: </strong> R$ <?=number_format($insalubridade['valor_integral'],2,',','.')?></p>
                            <p><strong>Periculosidade: </strong> R$ <?=number_format($periculosidade['valor_integral'],2,',','.')?></p>
                            <p><strong>Adicional Noturno: </strong> R$ <?=number_format($adicional_noturno['valor_integral'],2,',','.')?></p>
                            <p><strong>DSR: </strong> R$ <?=number_format($dsr['valor_integral'],2,',','.')?></p>              
                            <p><strong>Valor diario: </strong> R$ <?=number_format($valor_diario,"2",",",".")?> <span style="font-style: italic; color:  #cdcdcd"> **Salário + adicionais</span></p>
                            <p><strong>Valor Hora: </strong> R$ <?=number_format($valor_hora,"2",",",".")?> <span style="font-style: italic; color: #cdcdcd"> **Salário + adicionais</span></p>
                        </div>
                        <?php
                        if (sizeof($_SESSION['mov_cadastrados']) > 0) {
                            echo '
                            <div class="alert alert-dismissable alert-warning">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <p>O(s) movimento(s) ' . implode(', ', $_SESSION['mov_cadastrados']) . ' não foram cadastrados pois já existe para esta competência!</p>
                            </div>';
                            //$_SESSION['mov_cadastrados'] = null;
                        } ?>
                    </div>
                    <div class="panel-heading border-t">COMPETÊNCIA</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="col-xs-offset-1 col-xs-10">
                                <div class="input-group">
                                    <?=montaSelect($optMes, $mesSel, array('name' => "mes", 'id' => 'mes', 'class' => 'form-control'))?>
                                    <div class="input-group-addon"></div>
                                    <?=montaSelect($optAnos, $anoSel, array('name' => "ano", 'id' => 'ano', 'class' => 'form-control'))?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body border-t no-padding-hr">
                        <div class="col-lg-6">
                            <div class="panel panel-info">
                                <div class="panel-heading"><!--span class="badge">4</span--> CRÉDITO</div>
                                <div class="panel-body overflow no-padding-hr">
                                    <table class="table table-hover table-striped table-condensed table-bordered text-sm">
                                        <thead>
                                            <tr class="active valign-middle">
                                                <th>Movimento</th>
                                                <th>Valor</th>
                                                <th>Sempre</th>
                                                <th>Tipo</th>
                                                <th>Quantidade</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        ///MOVIMENTOS DE CRÉDITO
                                        $qr_mov = mysql_query("SELECT * FROM rh_movimentos WHERE categoria = 'CREDITO' AND mov_lancavel = 1 ORDER BY descicao");              
                                        while ($row_mov = mysql_fetch_assoc($qr_mov)) {                    
                                        //$classAuxDistancia = ($row_mov[id_mov] == 193)?"validate[funcCall[auxDistancia]]" :'' ;    
                                            if ($row_mov['tipo_qnt_lancavel']) {
                                                $i++; $checked1 = 'checked="checked"';
                                                $campoTipoQnt = "<select name='tipo_quantidade[{$row_mov['id_mov']}]' class='form-control no-padding-hr tipo_qnt'>
                                                                    <option value='1'>Horas</option>
                                                                    <option value='2'>Dias</option>
                                                                 </select>  ";
                                                $campoQnt = '<input type="text" name="mov_qtd['.$row_mov['id_mov'] . ']" class="form-control no-padding-hr calculo hora_mask" data-key="'.$row_mov['id_mov'].'" style="width: 50px;" />';                              
                                            } else {
                                                $campoTipoQnt = $campoQnt = '';
                                            } ?>  
                                            <tr class="valign-middle">
                                                <td><?=$row_mov['cod'].' -  '.$row_mov['descicao']; ?></td>
                                                <td align="center"><input type='text' size="5" name='mov_valor[<?=$row_mov['id_mov']?>]'  id='mov_valor[<?=$row_mov[id_mov]?>]'  class='form-control no-padding-hr cred <?=$classAuxDistancia?> result_<?=$row_mov[id_mov]?>' rel='<?=$row_mov[id_mov]?>'/></td>
                                                <td align="center"><input type='checkbox' name='mov_sempre[<?=$row_mov['id_mov']?>]' value='2' rel='<?=$row_mov[id_mov]?>'/></td>
                                                <td align="center"><?=$campoTipoQnt; ?></td>
                                                <td align="center"><?=$campoQnt; ?></td>
                                           </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div><!-- /.panel-body -->
                            </div><!-- /.panel-primary -->
                        </div><!-- /.col-lg-6 -->

                        <div class="col-lg-6">
                            <div class="panel panel-danger">
                                <div class="panel-heading"><!--span class="badge">6</span--> DÉBITO</div>
                                <div class="panel-body overflow no-padding-hr">
                                    <table class="table table-hover table-striped table-condensed table-bordered text-sm">
                                        <thead>
                                            <tr class="active valign-middle">
                                                <th>Movimento</th>
                                                <th>Valor</th>
                                                <th>Sempre</th>
                                                <th>Tipo</th>
                                                <th>Quantidade</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php                
                                            $qr_mov = mysql_query("SELECT * FROM rh_movimentos WHERE categoria = 'DEBITO' AND mov_lancavel = 1 ORDER BY descicao");
                                            while ($row_mov = mysql_fetch_assoc($qr_mov)) {                    
                                                if ($row_mov['tipo_qnt_lancavel']) {
                                                    $i++; $checked1 = 'checked="checked"';
                                                   $campoTipoQnt = "<select name='tipo_quantidade[{$row_mov['id_mov']}]' class='form-control no-padding-hr tipo_qnt'>
                                                                        <option value='1'>Horas</option>
                                                                        <option value='2'>Dias</option>
                                                                     </select>  ";
                                                    $campoQnt = '<input type="text" name="mov_qtd['.$row_mov['id_mov'] . ']" class="form-control no-padding-hr calculo hora_mask" data-key="'.$row_mov['id_mov'].'" style="width: 50px;" />';                              
                                                } else {
                                                    $campoTipoQnt = '';
                                                    $campoQnt = '';
                                                } ?>  
                                                <tr class="valign-middle">
                                                   <td><?=$row_mov['cod'].' -  '.$row_mov['descicao']; ?></td>
                                                    <td align="center"><input type='text' size="5" name='mov_valor[<?=$row_mov['id_mov']?>]'  id='mov_valor[<?=$row_mov[id_mov]?>]'  class='form-control no-padding-hr desc result_<?=$row_mov[id_mov]?>' rel='<?=$row_mov[id_mov]?>'/></td>
                                                    <td align="center"><input type='checkbox' name='mov_sempre[<?=$row_mov['id_mov']?>]' value='2' rel='<?=$row_mov[id_mov]?>'/></td>
                                                    <td align="center"><?=$campoTipoQnt; ?></td>
                                                    <td align="center"><?=$campoQnt; ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div><!-- /.panel-body -->
                            </div><!-- /.panel-primary -->
                        </div><!-- /.col-lg-6 -->
                    </div>
                    <div class="panel-footer text-right">
                        <input type="hidden" name="salario_base" id="salario_base" value="<?=$row_clt['salario']?>" />
                        <input type="hidden" name="valor_hora" id="valor_hora" value="<?=$valor_hora?>" />    
                        <input type="hidden" name="valor_dia" id="valor_dia" value="<?=$valor_diario?>" />
                        <input type="hidden" name="valor_horaAtraso" id="valor_horaAtraso" value="<?=$valor_horaAtraso?>" />
                        <input type="hidden" name="valor_diaAtraso" id="valor_diaAtraso" value="<?=$valor_diarioAtraso?>" />
                        <input type="hidden" name="home" id="home" value="" />
                        <input type="hidden" name="clt"  id="clt" value="<?=$clt?>"/>
                        <input type="hidden" name="regiao" value="<?=$regiao?>"/>
                        <input type="hidden" name="projeto" value="<?=$row_clt['id_projeto']?>"/>
                        <input type="submit" class="btn btn-primary" name="confirmar" value="Confirmar">
                    </div>
                </div>
            </form>
            
            <div class="row">
                <div class="col-lg-6">
                    <div class="panel panel-info">
                        <div class="panel-heading"><!--span class="badge">4</span--> MOVIMENTOS DE CRÉDITO</div>
                        <div class="panel-body overflow no-padding-hr">
                            <table class="table table-hover table-striped table-condensed table-bordered text-sm">
                            <!--table class="table table-hover table-striped text-sm table-condensed"-->
                                <thead>
                                    <tr class="bg-default valign-middle">
                                        <th style="width:5%;">Cod.</th>
                                        <th style="width:40%;">Nome</th>
                                        <th style="width:5%;">Qtd.</th>
                                        <th style="width:15%;">Valor</th>
                                        <th style="width:20%;">Competência</th>
                                        <th style="width:10%;">Incidência</th>
                                        <th style="width:5%;">Del.</th> 
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($movLancado['CREDITO'] as $movimentos) { ?>
                                    <tr class="valign-middle">
                                        <td><?=$movimentos['id_movimento']?></td>
                                        <td><?=$movimentos['nome']?></td>
                                        <td><?=$movimentos['qnt'].' '.$movimentos['tipo_qnt']?></td>
                                        <td><?=number_format($movimentos['valor'], 2, ',', '.')?></td>
                                        <td><?=$movimentos['competencia']?></td>
                                        <td><?=$movimentos['incidencia']?></td>
                                        <td><i class="btn btn-sm btn-danger fa fa-trash excluir pointer" rel="<?=$movimentos['id_movimento']?>" ></i></td>
                                    <tr>                     
                                <?php } ?>
                                </tbody>
                            </table>
                        </div><!-- /.panel-body -->
                    </div><!-- /.panel-primary -->
                </div><!-- /.col-lg-6 -->

                <div class="col-lg-6">
                    <div class="panel panel-danger">
                        <div class="panel-heading"><!--span class="badge">6</span--> MOVIMENTOS DE DÉBITO</div>
                        <div class="panel-body overflow no-padding-hr">
                            <table class="table table-hover table-striped table-condensed table-bordered text-sm">
                                <thead>
                                    <tr class="bg-default valign-middle">
                                        <td style="width:5%;">Cod.</td>
                                        <td style="width:30%;">Nome</td>
                                        <td style="width:10%;">Qtd.</td>
                                        <td style="width:20%;">Valor</td>
                                        <td style="width:20%;">Competência</td>
                                        <td style="width:10%;">Incidência</td>
                                        <td style="width:5%;">Del.</td> 
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($movLancado['DEBITO'] as $movimentos) { ?> 
                                    <tr class="valign-middle">
                                        <td><?=$movimentos['id_movimento']?></td>
                                        <td><?=$movimentos['nome']?></td>
                                        <td><?=$movimentos['qnt'].' '.$movimentos['tipo_qnt']?></td>
                                        <td><?=number_format($movimentos['valor'], 2, ',', '.')?></td>
                                        <td><?=$movimentos['competencia']?></td>
                                        <td><?=$movimentos['incidencia']?></td>
                                        <td><i class="btn btn-sm btn-danger fa fa-trash excluir pointer" rel="<?=$movimentos['id_movimento']?>" ></i></td>
                                    <tr>                     
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div><!-- /.panel-body -->
                    </div><!-- /.panel-primary -->
                </div><!-- /.col-lg-6 -->
            </div>
            <?php include_once ('../../template/footer.php'); ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../resources/js/rh/rh_movimentos.js"></script>
        <script src="../../js/global.js"></script>
        
        <script src="../../jquery/priceFormat.js" type="text/javascript"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js" type="text/javascript"></script>
        <script>
            $(function(){
                $('.hora_mask').mask("999:99");
                $('#form').validationEngine();
            })
        </script>
    </body>
</html>