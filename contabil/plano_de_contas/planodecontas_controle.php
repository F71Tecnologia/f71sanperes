<?php
header('Content-Type: text/html; charset=iso-8859-1');
include_once("../../conn.php");
include_once("../../wfunction.php");
include_once("../../classes/c_planodecontasClass.php");
include_once("../../classes/c_planodecontasClass.php");
include_once("../../classes/ContabilHistoricoClass.php");
include_once("../../classes/global.php");
require_once("../../classes/pdf/fpdf.php");

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];
$id_master = $usuario['id_master'];

//$planodecontas = new c_planodecontasClass();
$planodecontas = new c_planodecontasClass();

$query_master = "SELECT * FROM master WHERE id_master = $id_master";
$master = mysql_fetch_assoc(mysql_query($query_master));

$sql_projeto = "SELECT * FROM projeto WHERE id_projeto = {$_REQUEST['projeto']}";
$nomeprojeto = mysql_fetch_assoc(mysql_query($sql_projeto));

// consulta conta para ver se há cadastrado da conta
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'classificador') {
    $classificador = str_replace(array('.', '_'), '', $_REQUEST['classificador']);
    $plc_classificador = $planodecontas->retorna($classificador);
    $plc_pai = $planodecontas->retorna($classificador);
    
    $array = array(
        'table'=>$plc_classificador,
//        'pai'=>$plc_pai
    );
    
    echo json_encode($array);
    exit();
}

// alteração das contas EMPRESAS

if (isset($_REQUEST['novaconta']) && $_REQUEST['novaconta'] == 'Salvar') {
    $conta_pai = implode('.', array_filter(explode('.', $_REQUEST['conta_pai'])));
    $busca_contapai = $planodecontas->retorna_conta_pai($conta_pai, $_REQUEST['projeto']);
    $conta_pai = $busca_contapai[0]['id_conta'];
    $classificador = implode('.', array_filter(explode('.', $_REQUEST['classificador'])));
    $historico = ($_REQUEST['id_historico'] > 0) ? $_REQUEST['id_historico'] : 0;
    $pl_ctas_novaconta = $planodecontas->novaconta($_REQUEST['codigo'], $conta_pai, $classificador, utf8_encode(addslashes($_REQUEST['descricao'])), $_REQUEST['tipo'], $_REQUEST['natureza'], $_REQUEST['projeto'], $historico);
    
    echo $pl_ctas_novaconta;
    exit();
}

if ($_REQUEST['filtrar'] == 'Imprimir')  {

    $filtrar_planocontas = $planodecontas->getPlanoFull($_REQUEST['projeto']);

    class PDF extends FPDF {

        public $master;
        public $nomeprojeto;
        
            function Header() {
            $this->SetFont('Arial', 'B', 8);
            $this->SetLineWidth(0);
            $this->Image("../../imagens/logomaster{$this->master['id_master']}.gif", 1, .75, 2);
            $this->Cell(3);
            $this->Cell(10, .3, $this->master['nome'], 0, 0, 'L');
            $this->Ln();
            $this->Cell(3);
            $this->Cell(10, .3, 'CNPJ ' . $this->master['cnpj'], 0, 0, 'L');
            $this->Ln();
            $this->Cell(3);
            $this->Cell(10, .3, $this->master['endereco'], 0, 'B', 'L');
            $this->Ln();
            $this->Ln();
            $this->SetFont('Arial', 'B', 10);
            $this->SetLineWidth(0);
            $this->Cell(19, .4, 'PLANO DE CONTAS ', 0, 0, 'C');
            $this->Ln();
            $this->SetFont('Arial', 'B', 8);
            $this->SetLineWidth(0);
            $this->Cell(19, .4, $this->nomeprojeto['nome'], 0, NULL, 'C');
            $this->SetFont('Arial', 'B', 8);
            $this->SetLineWidth(0);
            $this->Ln();

            $this->Cell(19, 0, NULL, 0, 0, 'B' , 'C');
            $this->Cell(3,.5);
            $this->Ln();
            $this->SetFont('Arial', 'B', 6);
            $this->SetLineWidth(0);
            $this->Cell(3, .3, 'CONTA', 0, 0, 'L');
            $this->Cell(2, .3, 'ACESSO', 0, 0, 'C');
            $this->Cell(10, .3, 'DESCRIÇÃO', 0, 0, 'L');
            $this->Cell(2, .3, 'NATUREZA', 0, 0, 'L');
            $this->Cell(2, .3, 'CLASSIFICAÇÃO', 0, 0, 'L');
            $this->Ln();          
            $this->Ln();          
        }
    
        function Footer() {
            // Position at 1.5 cm from bottom
            $this->SetY(-1);
            // Arial italic 8
            $this->SetFont('Arial', NULL, 6.5);
            $this->SetLineWidth(0);
            // Page number
            $this->Cell(16, .8, 'F71 SISTEMAS WEB - módulo contábil', 'T', 0,'L');
            $this->Cell(3, .8, 'Pagina ' . $this->PageNo(), 'T',0, 'R');
        }
    }
    
    $pdf = new PDF('P', 'cm', 'A4');
    $pdf->master = $master;
    $pdf->nomeprojeto = $nomeprojeto;
    
    $pdf->setMargins(1, 1, 1);
    $pdf->AddPage();

    $pdf->SetAutoPageBreak(1, 1.5);
    
    $count = 0;
    $k = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0);
    foreach ($filtrar_planocontas as $value) {
        if ($value['classificacao'] === 'S') {
            $tipo = 'SINTÉTICA';
        } elseif ($value['classificacao'] === 'A') {
            $tipo = 'ANALÍTICA';
        } else {
            $tipo = '';
        }
        if ($value['natureza'] === 'C') {
            $natureza = 'CREDORA';
            $corfont = 'text-danger';
        } elseif ($value['natureza'] === 'D') {
            $natureza = 'DEVEDORA';
            $corfont = 'text-info';
        } else {
            $natureza = '';
        }
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetLineWidth(0);
            $pdf->Ln();
            $pdf->SetFont('Arial', '', 6);
            $pdf->SetLineWidth(0);
            $pdf->cell(3, .3, $value['classificador'], 0, 0 );
            $pdf->cell(2, .3, $value['cod_reduzido'], 0, 0, 'C');
            $pdf->cell(10, .3, substr($value['descricao'], 0,72 ),0, 0);
            $pdf->cell(2, .3, $natureza, 0, 0, 'L');
            $pdf->cell(2, .3, $tipo, 0, 0, 'L');

    }

    $pdf->Output();
}

if (isset($_REQUEST['empresa_planoconta'])) {
    
    $filtrar_planocontas = $planodecontas->getPlanoFull($_REQUEST['projeto']); ?>
    <p class="text-right">
        <button type="button" class="btn btn-success hidden-print" onclick="tableToExcel('tbRelatorio', 'Plano de Contas')"><i class="fa fa-file-excel-o"></i> Exportar para Excel</button>
        <button type="button" id="print" name="filtrar" value="Imprimir" class="btn btn-default"><i class="fa fa-print"></i> Impressão</button></p>

    <table id="tbRelatorio" class="table table-condensed table-striped text text-sm">
        <thead>
            <tr>
                <th>Id</th>
                <th>Classificador</th>
                <th>Id Pai</th>
                <th>Acesso</th>
                <th>Descrição</th>
                <th class="text text-center">Classificação</th>
                <th class="text text-center">Natureza</th>
                <th class="text text-center">Nível</th>
                <th colspan="2"></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $count = 0;
            $k = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0);
            foreach ($filtrar_planocontas as $value) {
                if ($value['classificacao'] === 'S') {
                    $tipo = 'SINTÉTICA';
                    $tamanhoFont = 'text-uppercase text-bold text-default';
                    $corfont = 'text-default';
                } elseif ($value['classificacao'] === 'A') { 
                    $tipo = 'ANALÍTICA';
                    $tamanhoFont = 'text-sm';
                } else {
                    $tipo = '';
                }
                if ($value['natureza'] == 1) {
                    $natureza = 'DEVEDORA';
                    $corfont = 'text-danger';
                } elseif ($value['natureza'] == 2) {
                    $natureza = 'CREDORA';
                    $corfont = 'text-info';
                } else {
                    $corfont = 'text-default';                    
                }
                if ($value['id_projeto'] != 0) {
                    $acesso = $value['acesso'];
                } else {
                    $acesso = '';
                }?>
                <tr id="tr-<?= $value['id_conta'] ?>" class="<?= $tamanhoFont ?>" >
                    <td><?= $value['id_conta'] ?></td>
                    <td><?= $value['classificador'] ?></td>
                    <td class="text-center"><?= $value['cta_superior'] ?></td>
                    <td><?= $acesso ?></td>
                    <td><?= substr($value['descricao'],0,60) ?></td>
                    <td class="text text-center"><?php echo $tipo ?></td>
                    <td class="text text-center <?= $corfont?>"><?php echo $natureza ?></td>
                    <td class="text text-center"><?= $value['nivel'] ?></td>                
                    <td class="text text-right hidden-print">
                        <?php if ($value['sped'] == 0) { ?>
                            <button type="button" class="btn btn-info btn-xs" id="edita_conta" name="edita_conta" value="<?= $value['id_conta'] ?>" data-id="<?= $value['id_conta'] ?>" data-projeto="<?= $value['id_projeto'] ?>" title="Editar" data-toggle="tooltip">
                                <span class="glyphicon glyphicon-edit"></span>
                            </button>
                            <button type="button" class="btn btn-danger btn-xs" id="cancela_conta" name="cancela_conta" data-cancelar_id="<?= $value['id_conta'] ?>" data-descricao="<?= $value['descricao'] ?>" data-classificador="<?= $value['classificador'] ?>" title="Excluir" data-toggle="tooltip">
                                <span class="glyphicon glyphicon-trash"></span>
                            </button> 
                        <?php } ?>
                    </td>

                </tr>
            <?php } ?>
        </tbody>
    </table>

    <?php

    exit();
}
//INICIO TELA PARA CONTA REFERÊNCIA
if (isset($_REQUEST['empresa_planoconta_referencia'])) {
    
    $filtrar_planocontas = $planodecontas->getPlanoFull($_REQUEST['projeto']); ?>
    <!--
    <p class="text-right">
        <button type="button" class="btn btn-success hidden-print" onclick="tableToExcel('tbRelatorio', 'Plano de Contas')"><i class="fa fa-file-excel-o"></i> Exportar para Excel</button>
        <button type="button" id="print" name="filtrar" value="Imprimir" class="btn btn-default"><i class="fa fa-print"></i> Impressão</button></p>-->

    <table id="tbRelatorio" class="table table-condensed table-striped text text-sm">
        <thead>
            <tr>
                <th>Id</th>
                <th>Classificador</th>
                <th>Id Pai</th>
                <th>Acesso</th>
                <th>Descrição</th>
                <th class="text text-center">Classificação</th>
                <!--<th class="text text-center">Natureza</th>-->
                <th class="text text-center">Referêcia</th>
                <th colspan="2"></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $count = 0;
            $k = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0);
            foreach ($filtrar_planocontas as $value) {
                if ($value['classificacao'] === 'S') {
                    $tipo = 'SINTÉTICA';
                    $tamanhoFont = 'text-uppercase text-bold text-default';
                    $corfont = 'text-default';
                } elseif ($value['classificacao'] === 'A') { 
                    $tipo = 'ANALÍTICA';
                    $tamanhoFont = 'text-sm';
                } else {
                    $tipo = '';
                }
                if ($value['natureza'] == 1) {
                    $natureza = 'DEVEDORA';
                    $corfont = 'text-danger';
                } elseif ($value['natureza'] == 2) {
                    $natureza = 'CREDORA';
                    $corfont = 'text-info';
                } else {
                    $corfont = 'text-default';                    
                }
                if ($value['id_projeto'] != 0) {
                    $acesso = $value['acesso'];
                } else {
                    $acesso = '';
                }?>
                <tr id="tr-<?= $value['id_conta'] ?>" class="<?= $tamanhoFont ?>" >
                    <td><input type="checkbox" name="IdPlano[]" id="IdPlano" value="<?= $value['id_conta'] ?>" ><?= $value['id_conta'] ?></td>
                    <td><?= $value['classificador'] ?></td>
                    <td class="text-center"><?= $value['cta_superior'] ?></td>
                    <td><?= $acesso ?></td>
                    <td><?= substr($value['descricao'],0,60) ?></td>
                    <td class="text text-center"><?php echo $tipo ?></td>
                    <td class="text text-center <?= $corfont?>"><?php echo $natureza ?></td>
                    <!--<td class="text text-center"><?= $value['nivel'] ?></td>--> 
		    <td class="text text-center">
			<button type="button" class="btn btn-primary vincular" data-id="<?= $value['id_conta'] ?>"  data-toggle="modal" data-target="#contas_sped" style="width:130px;">
			    <?php 
			    //CONDIÇÃO PARA TITULO DO BOTÃO VINCULAR
			    if($value['conta_referencia']!=''){
				$ExibiBtVincula= $value['conta_referencia'];
			    }else{
				$ExibiBtVincula= 'Nova Referência';
			    }
			    print $ExibiBtVincula;
			    ?>
			    
			</button>
		    </td>
                    <td class="text text-right hidden-print">
                        <?php if ($value['sped'] == 0) { ?>
                            <button type="button" class="btn btn-info btn-xs" id="edita_conta" name="edita_conta" value="<?= $value['id_conta'] ?>" data-id="<?= $value['id_conta'] ?>" data-projeto="<?= $value['id_projeto'] ?>" title="Editar" data-toggle="tooltip">
                                <span class="glyphicon glyphicon-edit"></span>
                            </button>
                            <button type="button" class="btn btn-danger btn-xs" id="cancela_conta" name="cancela_conta" data-cancelar_id="<?= $value['id_conta'] ?>" data-descricao="<?= $value['descricao'] ?>" data-classificador="<?= $value['classificador'] ?>" title="Excluir" data-toggle="tooltip">
                                <span class="glyphicon glyphicon-trash"></span>
                            </button> 
                        <?php } ?>
                    </td>

                </tr>
            <?php } ?>
        </tbody>
    </table>

    <?php

    exit();
}


//FIM TELA CONTA CONFERENCIA
if (isset($_REQUEST['editar_conta'])) {
    $editar_contas = $planodecontas->editar($_REQUEST['id_conta'], $_REQUEST['id_projeto']);
    $id = array_keys($editar_contas);
    $editar_contas = $editar_contas[$id[0]];

    $objHistorico = new ContabilHistoricoPadraoClass();

    $objHistorico->listarHistoricos();
    $optHistorico[-1] = 'Selecione';
    while ($objHistorico->getRow()) {
        $optHistorico[$objHistorico->getIdHistorico()] = utf8_encode($objHistorico->getTexto());
    } ?>

    <div class="form-group">
        <div class="row">
            <label class="col-lg-3 label-control text text-sm">Classificação</label>
            <div class="col-lg-5">
                <input type="hidden" id="edita_id_conta" name="edita_id_conta" value="<?= $editar_contas['id_conta'] ?>">
                <input type="text" value="<?= $editar_contas['classificador'] ?>" id="edita_classificador" name="edita_classificador" class="form-control input-sm text-center">
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <label class="col-lg-3 control-label text text-sm">Id conta pai</label>
            <div class="col-lg-3">
                <input type="text" value="<?= $editar_contas['cta_superior'] ?>" id="edita_pai" name="edita_pai" class="form-control input-sm text-center">
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <label class="col-lg-3 control-label text text-sm">Acesso</label>
            <div class="col-lg-3">
                <input type="text" value="<?= $editar_contas['acesso'] ?>" id="edita_reduzido" name="edita_reduzido" class="form-control input-sm text-center">
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <label class="col-lg-3 control-label text text-sm">Nível</label>
            <div class="col-lg-2">
                <input type="text" value="<?= $editar_contas['nivel'] ?>" id="edita_nivel" name="edita_nivel" class="form-control input-sm text-center">
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <label class="col-lg-3 control-label text text-sm">Descrição </label>
            <div class="col-lg-8">
                <input type="text" value="<?= $editar_contas['descricao'] ?>" class="form-control input-sm" id="edita_descricao" name="edita_descricao">
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <label class="col-lg-3 control-label text text-sm">Natureza</label>
            <div class="col-lg-4">
                <select class="form-control input-sm" id="edita_natureza" name="edita_natureza">
                    <option value='2' <?php if ($editar_contas['natureza'] == 2 ) { echo "selected"; } ?> > CREDORA </option>
                    <option value='1' <?php if ($editar_contas['natureza'] == 1 ) { echo "selected"; } ?> > DEVEDORA </option>
                </select>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <label class="col-lg-3 control-label text text-sm">Classificação</label>
            <div class="col-lg-4">
                <select class="form-control input-sm" id="edita_tipo" name="edita_tipo">
                    <option value="A" <?php if ($editar_contas['classificacao'] == "A") { echo "selected"; } ?> > ANALÍTICA </option>
                    <option value="S" <?php if ($editar_contas['classificacao'] == "S") { echo "selected"; } ?> > SINTÉTICA </option>
                </select>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <label for="" class="col-lg-3 control-label text text-sm">Histórico Padrão</label>
            <div class="col-lg-9">
                <?= montaSelect($optHistorico, $editar_contas['id_historico'], 'name="id_historico2" id="id_historico2" class="form-control input-sm"') ?>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () { 
            $("input[name='edita_classificador']").mask('?9.99.99.99.99.99.99');
        })
    </script>

<?php exit(); }

if (isset($_REQUEST['implantar_saldo'])) {
    $consulta_lancamento = $planodecontas->retornaLancamento($_REQUEST['id_conta'], $_REQUEST['id_projeto']);
    $consulta_lancamento = $consulta_lancamento[0];
    $implantar_saldo = $planodecontas->retorno($_REQUEST['id_conta'], $_REQUEST['id_projeto']);
    $implantar_saldo = $implantar_saldo[0]; 
    ?>
    
    <div class="form-group">
        <div class="row">
            <label class="col-lg-3 label-control text text-sm">Classificação</label>
            <div class="col-lg-5">
                <input type="hidden" id="saldo_conta" name="implantar" value="<?= $implantar_saldo['id_conta'] ?>">
                <input readonly type="text" value="<?= $implantar_saldo['classificador'] ?>" id="saldo_classificador" name="saldo_classificador" class="form-control input-sm text-center">
            </div>
        </div>
    </div>
    
    <div class="form-group">
        <div class="row">
            <label class="col-lg-3 control-label text text-sm">Código Acesso</label>
            <div class="col-lg-5">
                <input readonly type="text" value="<?= $implantar_saldo['cod_reduzido'] ?>" id="edita_reduzido" name="edita_reduzido" class="form-control input-sm text-center">
            </div>
        </div>
    </div>
    
    <div class="form-group">
        <div class="row">
            <label class="col-lg-3 control-label text text-sm">Descrição</label>
            <div class="col-lg-8">
                <input readonly type="text" value="<?= utf8_decode($implantar_saldo['descricao']) ?>" class="form-control input-sm" id="edita_descricao" name="edita_descricao">
            </div>
        </div>
    </div> 
    <hr>
    <div>
        <div class="form-group">        
            <div class="row">
                <label class="col-lg-3 control-label text text-sm">Projeto</label>
                <div class="col-lg-8"> 
                    <input readonly type="text" value="<?= $implantar_saldo['nomeprojeto'] ?>" class="form-control input-sm">
                    <input type="hidden" value="<?= $implantar_saldo['projeto'] ?>" class="form-control input-sm" id="saldo_projeto" name="saldo_projeto">
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label class="col-lg-3 control-label text-sm">Data</label>
                <div class="col-lg-4">
                    <input type="text" name="saldo_data" id="saldo_data" value="" class="form-control text-center data hasDatepicker"/>
                </div>
            </div>
        </div>
        <div class="form-group">        
            <div class="row">
                <label class="col-lg-3 control-label text text-sm">Lançamento</label>
                <div class="col-lg-4"> 
                    <input readonly type="text" value="<?= $consulta_lancamento['MAX(id_lancamento)'] ?>" class="form-control <?= $text ?> input-sm" id="saldo_lancamento" name="saldo_lancamento">
                </div>
            </div>
        </div>
        <div class="form-group">        
            <div class="row">
                <label class="col-lg-3 control-label text text-sm">Histórico</label>
                <div class="col-lg-6"> 
                    <input readonly type="text" value="AJUSTE DE SALDO" class="form-control text-uppercase input-sm" id="saldo_historico" name="saldo_historico">
                </div>
            </div>
        </div>        
    </div>
    <hr>
    <div class="form-group">
        <div class="row">
            <label class="col-lg-3 control-label text text-sm">Valor R$</label>
            <div class="col-lg-4">
                <input type="text" name="saldo_valor" id="saldo_valor" maxlength="14" class="form-control text-right money"/>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-lg-3 control-label"></label>
        <div class="col-lg-6">
            <label class="radio-inline text-bold text-sm saldotipo"><input type="radio" id="saldo2" name="saldotipo" value="2">Crédito</label>
            <label class="radio-inline text-bold text-sm saldotipo"><input type="radio" id="saldo1" name="saldotipo" value="1" checked>Débito</label>
        </div>
    </div>
    
    <script>
        $(document).ready(function () { 
            $("input[name='edita_classificador']").mask('?9.99.99.99.99.99.99');
        })
    </script>

<?php exit(); }

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'cancelar_conta') {
    $cancelar_conta = $planodecontas->cancelar($_REQUEST['conta']);

    echo $cancelar_conta;
    exit();
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'alterar_contas') {
    $planodeconta_idconta = $_REQUEST['edita_id_conta'];
    $classificador = implode('.', array_filter(explode('.', $_REQUEST['edita_classificador'])));
    $historico = ($_REQUEST['id_historico'] > 0) ? $_REQUEST['id_historico'] : 0;
    //$alterarcao_conta = $planodecontas->alteracao($_REQUEST['edita_id_conta'], $classificador, $_REQUEST['edita_reduzido'], addslashes($_REQUEST['edita_descricao']), $_REQUEST['edita_natureza'], $_REQUEST['edita_tipo'], $historico, str_replace(',', '.', str_replace('.', '', $_REQUEST['edita_saldo'])));
    $alterarcao_conta = $planodecontas->alteracao($_REQUEST['edita_id_conta'], $classificador, $_REQUEST['edita_pai'],$_REQUEST['edita_reduzido'], $_REQUEST['edita_nivel'], utf8_decode(addslashes($_REQUEST['edita_descricao'])), $_REQUEST['edita_natureza'], $_REQUEST['edita_tipo'], $historico, str_replace(',', '.', str_replace('.', '', $_REQUEST['edita_saldo'])));

   echo $alterarcao_conta;
    exit();
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'implantarplanodecontas') {
    $arraySaldoContas = $planodecontas->contasSaldo($_REQUEST['id_projeto']);
    if (count($arraySaldoContas) > 0) { ?>
        <div class="col-sm-5"><label><?= $arraySaldoContas[2]['nome'] ?></label></div>
        <div class="text-right"><label> <?= $arraySaldoContas[2]['dataAtual'] ?></label></div>
        <hr>
        <form id="form-lista-planos">
            <table class="table table-condensed table-bordered table-condensed table-striped text-sm valign-middle">
                <thead class="text-sm">
                    <tr>
                        <th>CLASSIFICADOR</th>
                        <th class="text-center">ACESSO</th>
                        <th>DESCRIÇÃO</th>
                        <th colspan="2" class="text-right">SALDO R$</th>
                    </tr>
                </thead>
               <?php foreach ($arraySaldoContas as $value) {
                    $cor = $value['saldo'] < 0 ? "text-danger" : "text-info" ; ?>
                    <tr>
                        <td width="16%"><?= $value['classificador'] ?></td>
                        <td width="10%" class="text-center"><?= $value['id_conta'] ?></td>
                        <td width=""><?= utf8_decode($value['descricao']) ?></td>
                        <td class="text-right <?= $text.' '. $cor ?>"><?= ($value['saldo'] < 0) ? "(" . number_format($value['saldo'] * -1, 2, ',', '.') . ")" : number_format($value['saldo'], 2, ',', '.') ?></td>
                        <td class="text-center"><button type="button" class="btn btn-default btn-xs" id="implantar_saldo" name="implantar_saldo" value="<?= $value['id_conta'] ?>" data-id="<?= $value['id_conta'] ?>" data-projeto="<?= ($_REQUEST['id_projeto']) ?>" title="Implantar" data-toggle="tooltip"><i class="fa fa-usd"></i></button></td>
                    </tr>
                <?php } ?>
            </table>
            <input type="hidden" name="method" value="classificadores_saldo_implantar">
            <input type="hidden" name="id_projeto" value="<?= $_REQUEST['id_projeto'] ?>">
        </form>
    <?php } else { ?>
        <div class="alert alert-warning">Nenhuma conta encontrada neste projeto!</div>
        <?php
    }
    exit();
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'classificadores_saldo_implantar') {
    if (isset($_REQUEST['id_conta'])) {
        $arrayProjetos = getProjetos($usuario['id_regiao']);
        unset($arrayProjetos[$_REQUEST['id_projeto']]);
        $arrayContas = $planodecontas->contasImplantarSaldo($_REQUEST['id_projeto'], $_REQUEST['id_conta']);
        if (count($arrayContas) > 0) {
            ?>
            <legend>Selecione o projeto, edite as contas e salve</legend>
            <form id="form-lista-planos-editavel" class="form-horizontal">
                <div class="form-group">
                    <label class="col-sm-1 text-sm control-label">Projeto</label>
                    <div class="col-sm-5"><?= montaSelect($arrayProjetos, null, "id='id_projeto' name='id_projeto' class='form-control input-sm validate[required,custom[select]]'"); ?></div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <table class="table table-condensed table-bordered table-condensed table-striped text-sm valign-middle">
                            <thead>
                                <tr class="bg-primary">
                                    <th>Classificador</th>
                                    <th>Acesso</th>
                                    <th>Descrição</th>
                                    <th>Natureza</th>
                                    <th>Tipo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($arrayContas as $value) { ?>
                                    <tr>
                                        <td><input readonly class="form-control input-sm classificador" type="text" name="id_conta[<?= $value['id_conta'] ?>][classificador]" value="<?= $value['classificador'] ?>"></td>
                                        <td><input class="form-control input-sm" type="text" name="id_conta[<?= $value['id_conta'] ?>][cod_reduzido]" value="<?= $value['cod_reduzido'] ?>"></td>
                                        <td><input type="hidden" name="id_conta[<?= $value['id_conta'] ?>][conta_superior]" value="<?= $value['conta_superior'] ?>"></td>
                                        <td><input class="form-control input-sm" type="text" name="id_conta[<?= $value['id_conta'] ?>][descricao]" value="<?= utf8_decode($value['descricao']) ?>"></td>
                                        <td>
                                            <select class="form-control input-sm" type="text" name="id_conta[<?= $value['id_conta'] ?>][natureza]">
                                                <option value="D" <?= ($value['natureza'] == 'D') ? 'SELECTED' : null ?>>D</option>
                                                <option value="C" <?= ($value['natureza'] == 'C') ? 'SELECTED' : null ?>>C</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control input-sm" type="text" name="id_conta[<?= $value['id_conta'] ?>][classificacao]">
                                                <option value="A" <?= ($value['classificacao'] == 'A') ? 'SELECTED' : null ?>>A</option>
                                                <option value="S" <?= ($value['classificacao'] == 'S') ? 'SELECTED' : null ?>>S</option>
                                            </select>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <input type="hidden" name="method" value="salvar_plano_contas">
                    </div>
                </div>
            </form>
            <script>
                $(document).ready(function () {
                    $(".classificador").mask('?9.99.99.99.99.99.99');
                })
            </script>
        <?php } else { ?>
            <div class="alert alert-warning">Nenhuma conta selecionada!</div>
            <?php
        }
    } else {
        ?>
        <div class="alert alert-warning">Nenhuma conta selecionada!</div>
        <?php
    }
    exit();
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'classificadores_projeto') {
    $arrayContas = $planodecontas->contasProjeto($_REQUEST['id_projeto']);
    if (count($arrayContas) > 0) {
        ?>
        <legend>Selecione as contas que serão copiadas</legend>
        <form id="form-lista-planos">
            <table class="table table-condensed table-bordered table-condensed table-striped text-sm valign-middle">
                <tr>
                    <td class="text-center" width="5%"><input type="checkbox" id="checkAll" data-name="id_conta"></td>
                    <td colspan="3">Selecionar todos</td>
                </tr>
                <?php foreach ($arrayContas as $value) { ?>
                    <tr>
                        <td class="text-center" width="5%"><input type="checkbox" name="id_conta[]" value="<?= $value['id_conta'] ?>"></td>
                        <td width="14%"><?= $value['classificador'] ?></td>
                        <td width="6%"><?= $value['acesso'] ?></td>
                        <td width=""><?= utf8_decode($value['descricao']) ?></td>
                    </tr>
                <?php } ?>
            </table>
            <input type="hidden" name="method" value="classificadores_projeto_editavel">
            <input type="hidden" name="id_projeto" value="<?= $_REQUEST['id_projeto'] ?>">
        </form>
    <?php } else { ?>
        <div class="alert alert-warning">Nenhuma conta encontrada neste projeto!</div>
        <?php
    }
    exit();
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'classificadores_projeto_editavel') {
    if (isset($_REQUEST['id_conta'])) {
        $arrayProjetos = "SELECT * FROM projeto WHERE id_regiao IN(45,44,48,69) ORDER BY id_regiao";
        $result = mysql_query($arrayProjetos) or die('Erro ao consultar Plano de Contas: ' . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            $return[$row['id_projeto']] = $row['nome'];
        }
        $arrayContas = $planodecontas->contasProjeto($_REQUEST['id_projeto'], $_REQUEST['id_conta']);
        if (count($arrayContas) > 0) { ?>
            <legend>Selecione o Projeto, edite os código de acesso e descrição</legend>
            <form id="form-lista-planos-editavel" class="form-horizontal">
                <div class="form-group">
                    <label class="col-sm-1 text-sm control-label">Projeto</label>
                    <div class="col-sm-5"><?= montaSelect($return, null, "id='id_projeto' name='id_projeto' class='form-control input-sm validate[required,custom[select]]'"); ?></div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <table class="table table-condensed table-bordered table-condensed table-striped text-sm valign-middle">
                            <thead>
                                <tr class="bg-primary">
                                    <th style="width: 15%">Classificador</th>
                                    <th style="width: 15%">Acesso</th>
                                    <th>Descrição</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($arrayContas as $value) { ?>
                                    <tr>
                                        <td><input readonly class="form-control input-sm classificador" type="text" name="id_conta[<?= $value['id_conta'] ?>][classificador]" value="<?= $value['classificador'] ?>"></td>
                                        <td><input class="form-control input-sm" type="text" name="id_conta[<?= $value['id_conta'] ?>][acesso]" value="<?= $value['acesso'] ?>"></td>
                                        <td><input class="form-control input-sm" type="text" name="id_conta[<?= $value['id_conta'] ?>][descricao]" value="<?= utf8_decode($value['descricao']) ?>"></td>
                                        <input type="hidden" name="id_conta[<?= $value['id_conta'] ?>][cta_superior]" value="<?= $value['cta_superior'] ?>">
                                        <input type="hidden" name="id_conta[<?= $value['id_conta'] ?>][natureza]" value="<?= $value['natureza'] ?>">
                                        <input type="hidden" name="id_conta[<?= $value['id_conta'] ?>][classificacao]" value="<?= $value['classificacao'] ?>">
                                    </tr>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>
                        <input type="hidden" name="method" value="salvar_plano_contas">
                    </div>
                </div>
            </form>
            <script>
                $(document).ready(function () {
                    $(".classificador").mask('?9.99.99.99.99.99.99');
                })
            </script>
        <?php } else { ?>
            <div class="alert alert-warning">Nenhuma conta selecionada!</div>
            <?php
        }
    } else {
        ?>
        <div class="alert alert-warning">Nenhuma conta selecionada!</div>
        <?php
    }
    exit();
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'salvar_plano_contas') {
    foreach ($_REQUEST['id_conta'] as $key => $value) {
        $return = $planodecontas->novaconta($value['acesso'], $value['cta_superior'], $value['classificador'], addslashes($value['descricao']), $value['classificacao'], $value['natureza'], $_REQUEST['id_projeto']);
        $retorno = json_decode($return, true);
        if (!$retorno['status']) {
            $erro[] = $value['classificador'];
        }
    }
    if (count($erro) > 0) {
        echo implode(', ', $erro);
    } else {
        echo 'sucesso';
    }
    exit();
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'salvar_ajuste_saldo') {
//    print_array($_REQUEST);
    foreach ($_REQUEST['saldoconta'] as $key => $value) {
//    print_array($_REQUEST);    
        $return = $planodecontas->novaSaldo($value['cod_reduzido'], $value['conta_superior'], $value['classificador'], $value['classificacao'], addslashes($value['descricao']), $value['natureza'], $_REQUEST['id_projeto']);
       // print_array($return);
        $retorno = json_decode($return, true);
        if (!$retorno['status']) {
            $erro[] = $value['classificador'];
        }
    }
    if (count($erro) > 0) {
        echo implode(', ', $erro);
    } else {
        echo 'sucesso';
    }
    exit();
}

//ATUALIZAÇÃO DA REFERENCIA DA TABELA PLANO DE CONTA
//INICIO FUNÇÃO CONTA REFERENCIAIS 
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'referencia_conta') {
    $IdCOnta= explode(',',$_POST['idContaPlano']); 
    //print_r ($IdCOnta);die();
    $classifica= $_POST['classifica'];
    
    foreach ($IdCOnta as $IdCOntaPlano) {
	$atualizaReferencia= "UPDATE contabil_planodecontas SET conta_referencia='$classifica' WHERE id_conta= '$IdCOntaPlano'";
	$queryAtualizaReferencia= mysql_query($atualizaReferencia) or die(mysql_error());
   
    }
    
    
    
    if($queryAtualizaReferencia){
	print '1';
    }else{
	print '0';
    }
    
    
}

