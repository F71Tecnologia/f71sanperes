<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}

include("../conn.php");
include("../classes/funcionario.php");
include("../classes_permissoes/regioes.class.php");
include("../classes_permissoes/acoes.class.php");
include("../wfunction.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$optRegiao = getRegioes();

$ACOES = new Acoes();

$mesesSel = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m');
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
$dt_ini = (isset($_REQUEST['data_ini'])) ? $_REQUEST['data_ini']:date("d/m/Y",  strtotime('-30 days'));
$dt_fim = (isset($_REQUEST['data_fim'])) ? $_REQUEST['data_fim']:date("d/m/Y");

$meses = mesesArray(null, '', "« Selecione o Mês »");
$anoOpt = anosArray(null, null, array('' => "« Selecione o Ano »"));

if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    $regiao = $_REQUEST['regiao'];
    $projeto = $_REQUEST['projeto'];

    $mes = $_REQUEST['mes'];
    $ano = $_REQUEST['ano'];
    $ultimo_dia = date("t", mktime(0,0,0,$mes,'01',$ano));
    $mes_desc = str_pad($mes, 2, 0, STR_PAD_LEFT);
    
    $dt_ini = $_REQUEST['data_ini'];
    $dt_fim = $_REQUEST['data_fim'];
    
    $dt_iniCon = converteData($dt_ini);
    $dt_fimCon = converteData($dt_fim);
    
    $dt_referencia = $ano . '-' . $mes . '-' . '01';
    
    //CONSULTA ANTIGA, ESTAVA TENTANDO PEGAR TRANSFERENCIAS, MAS NÃO PEGAVA... ESTÁ ERRADA... (NOJO)
    //CRIEI OUTRA CONSULTA BEM MENOR, QUE PEGA EXATAMENTE AS MESMAS COISAS Q ESSA CONSULTA CAGADA..
    //SEM FALAR Q ESSE RELATÓRIO É DE ADMITIDOS, SE QUISEREM VER AS TRANSFERENCIAS, Q PROCUREM O RELATÓRIO DO MESMO.
    /*$sql_old = "
            SELECT clt.nome,clt.regiao_de, clt.regiao_para,D.id_regiao, D.regiao,C.nome as funcao,E.nome AS projeto, clt.matricula, clt.cpf, clt.pis, clt.tel_cel,
            DATE_FORMAT(clt.data_entrada,'%d/%m/%Y') as dt_admissao
            FROM
            (SELECT *,
                     (SELECT id_curso_de FROM rh_transferencias WHERE id_clt=A.id_clt AND id_curso_de <> id_curso_para AND data_proc >= '$dt_referencia' ORDER BY id_transferencia ASC LIMIT 1) AS de,
                     (SELECT id_curso_para FROM rh_transferencias WHERE id_clt=A.id_clt AND id_curso_de <> id_curso_para AND data_proc <= '$dt_referencia' ORDER BY id_transferencia DESC LIMIT 1) AS para,

                     (SELECT id_regiao_de FROM rh_transferencias WHERE id_clt=A.id_clt AND id_regiao_de <> id_regiao_para AND data_proc >= '$dt_referencia' ORDER BY id_transferencia ASC LIMIT 1) AS regiao_de,
                     (SELECT id_regiao_para FROM rh_transferencias WHERE id_clt=A.id_clt AND id_regiao_de <> id_regiao_para AND data_proc <= '$dt_referencia' ORDER BY id_transferencia DESC LIMIT 1) AS regiao_para,

                     (SELECT id_projeto_de FROM rh_transferencias WHERE id_clt=A.id_clt AND id_projeto_de <> id_projeto_para AND data_proc >= '$dt_referencia' ORDER BY id_transferencia ASC LIMIT 1) AS projeto_de,
                     (SELECT id_projeto_para FROM rh_transferencias WHERE id_clt=A.id_clt AND id_projeto_de <> id_projeto_para AND data_proc <= '$dt_referencia' ORDER BY id_transferencia DESC LIMIT 1) AS projeto_para

                     FROM rh_clt  as A
             WHERE  MONTH(A.data_entrada) = '$mes' AND YEAR(A.data_entrada) = '$ano') as clt

            LEFT JOIN curso AS C ON (IF(clt.para IS NOT NULL,C.id_curso=clt.para, IF(clt.de IS NOT NULL,C.id_curso=clt.de,C.id_curso=clt.id_curso)))
            LEFT JOIN regioes AS D ON (IF(clt.regiao_para IS NOT NULL,D.id_regiao=clt.regiao_para, IF(clt.regiao_de IS NOT NULL,D.id_regiao=clt.regiao_de,D.id_regiao=clt.id_regiao)))          
            LEFT JOIN projeto AS E ON (IF(clt.projeto_para IS NOT NULL,E.id_projeto=clt.projeto_para, IF(clt.projeto_de IS NOT NULL,E.id_projeto=clt.projeto_de,E.id_projeto=clt.id_projeto)))          
            WHERE D.id_regiao = $regiao ";*/
    
    $sql = "SELECT A.id_clt, A.nome,F.email as emailF, /*C.letra, C.numero,*/ A.matricula, A.cpf, A.pis, A.tel_cel, DATE_FORMAT(A.data_entrada,'%d/%m/%Y') AS dt_admissao, E.nome as projeto, C.nome as funcao
                FROM rh_clt AS A
                LEFT JOIN curso AS C ON (A.id_curso = C.id_curso)
                LEFT JOIN regioes AS D ON (A.id_regiao = D.id_regiao)
                LEFT JOIN projeto AS E ON (A.id_projeto = E.id_projeto)
                LEFT JOIN rh_clt_dados_email as F ON (A.id_clt = F.id_clt)
                WHERE  D.id_regiao = {$regiao}";
    
    if (!isset($_REQUEST['todos_projetos'])) {
        $sql .= " AND A.id_projeto = {$projeto} ";
    }
    $sql .= " ORDER BY A.data_entrada";
    echo "<!-- SSQL: ".$sql." -->";
    $qr_relatorio = mysql_query($sql) or die(mysql_error());

}

$regiaoSel = (isset($_REQUEST['reg'])) ? $_REQUEST['reg'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['pro'])) ? $_REQUEST['pro'] : null;

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $regiaoSel;
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : $projetoSel;

$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"4", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Relatório de Atualização de E-mails");
$breadcrumb_pages = array("Visualizar Projeto" => "../rh/ver.php");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>:: Intranet :: Relatório Admissões</title>
        
        <link href="../favicon.png" rel="shortcut icon" />
        
        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">

    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>
        
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Relatório de Atualização de E-mails</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">
            
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Filtro</div>
                    <div class="panel-body">
                        <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
                        <div class="form-group">
                            <label for="select" class="col-sm-offset-1 col-sm-1 control-label hidden-print">Região</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'form-control')); ?>
                            </div>
                            <label for="select" class="col-sm-1 control-label hidden-print">Projeto</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect($optProjeto, $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                            </div>
                        </div>
                        
                        <!--div class="form-group datas">
                            <label for="data_ini" class="col-sm-offset-1 col-sm-1 control-label hidden-print">Período</label>
                            <div class="col-lg-9">
                                <div class="input-daterange input-group" id="bs-datepicker-range">
                                    <input type="text" class="input form-control data" name="data_ini" id="data_ini" readonly="true" placeholder="Inicio" value="<?php echo $dt_ini ?>">
                                    <span class="input-group-addon ate">até</span>
                                    <input type="text" class="input form-control data" name="data_fim" id="data_fim" readonly="true" placeholder="Fim" value="<?php echo $dt_fim ?>">
                                    <span class="input-group-addon ate"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div-->
                        
                        <!--p><label class="first">Região:</label> <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao')); ?> </p>                                             
                        <p><label class="first">Projeto:</label> <?php echo montaSelect($optProjeto, $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'validate[required]')); ?><span class="loader"></span></p>
                        <p><label class="first">Mês:</label> <?php echo montaSelect($meses, $mesesSel, array('name' => "mes", 'id' => 'mes', 'class' => 'validate[required]')); ?><span class="loader"></span></p>
                        <p><label class="first">Ano:</label> <?php echo montaSelect($anoOpt, $anoSel, array('name' => "ano", 'id' => 'ano', 'class' => 'validate[required]')); ?><span class="loader"></span></p-->
                    </div>
                    <div class="panel-footer text-right hidden-print">
                        <?php
                            if (!empty($qr_relatorio) and ( isset($_POST['gerar']) || isset($_REQUEST['todos_projetos']))) { ?>
                                <button type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>    
                                <button type="button" form="formPdf" name="pdf" data-title="Relatório de Admissão" data-id="tbRelatorio" id="pdf" value="Gerar PDF" class="btn btn-danger"><span class="fa fa-file-pdf-o"></span> Gerar PDF</button>
                          <?php  } ?>
                        <button type="submit" name="todos_projetos" value="Gerar de Todos Projetos" id="todos_projetos" class="btn btn-warning"><span class="fa fa-filter"></span> Todos os Projetos</button>
                        <button type="submit" name="gerar" id="gerar" value="Gerar" class="btn btn-primary"><span class="fa fa-filter"></span> Gerar</button>
                    </div>

                    <!--br class="clear"/>

                    <p class="controls" style="margin-top: 10px;">
                        <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                        <?php if ($ACOES->verifica_permissoes(85)) {///permissão para VISUALIZAR TODOS OS PROJETOS AO MESMO TEMPO ?>
                            <input type="submit" name="todos_projetos" value="Gerar de Todos Projetos" id="todos_projetos"/>
                        <?php } ?>
                        <input type="submit" name="gerar" value="Gerar" id="gerar"/>
                    </p>
                </fieldset-->
                </div>
            </form>
                      
            <?php
            if (!empty($qr_relatorio) and ( isset($_POST['gerar']) || isset($_REQUEST['todos_projetos']))) {
                //LIXO, CONSULTA DENTRO DE RETORNO DE CONSULTA (MAIS QUE NOJO ISSO)
                /*
                $qr_reg = mysql_query("SELECT A.regiao, B.nome FROM regioes  as A 
                               INNER JOIN projeto as B
                               ON A.id_regiao = B.id_regiao 
                               WHERE id_projeto = $projeto");
                $row_reg = mysql_fetch_assoc($qr_reg);*/
                ?>
                <!--p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" value="Exportar para Excel" class="exportarExcel"></p>    
                <p class="separador"><strong>Região: </strong><?php echo $row_reg['regiao'] ?></p>
                <p class="separador"><strong>Projeto: </strong><?php echo $row_reg['nome'] ?></p>
                <p class="separador"><strong>Início do Período:</strong> <?php echo "01/{$mes_desc}/{$ano}"; ?></p>
                <p class="separador"><strong>Fim do Período:</strong> <?php echo "{$ultimo_dia}/{$mes_desc}/{$ano}"; ?></p-->
            
                <table class="table table-striped table-condensed table-bordered table-hover text-sm valign-middle" id="tbRelatorio">
                    <thead>
                        <tr>
                            <th>PROJETO</th>
                          
                            <th>NOME</th>
                            <th>CPF</th>
                            <th>E-Mail</th>
                            <th>FUNÇÃO</th>
                        </tr>               
                    </thead>
                    <tbody>
                    <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) { ?>

                        <tr style="font-size:11px; ">
                            <td><?php echo $row_rel['projeto'] ?></td>
                          
                            <td><?php echo $row_rel['nome'] ?></td>
                            <td><?php echo $row_rel['cpf'] ?></td>
                            <td><?php echo $row_rel['emailF'] ?></td>
                            <td align="center"><?php echo $row_rel['funcao']; echo $row_rel['letra']; echo $row_rel['numero'];?></td>
                        </tr>                                
                    <?php unset($total_mov); } ?>
                    </tbody>
                </table>
       
            <?php } ?>  
            <?php include('../template/footer.php'); ?>
            <div class="clear"></div>
        </div>
        
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script src="../js/jquery.ui.datepicker-pt-BR.js" type="text/javascript"></script>
        <script src="../js/jquery.form.js" type="text/javascript"></script>
        
        <script>
            $(function() {
                
                $('.data').datepicker({
                    dateFormat: 'dd/mm/yy',
                    changeMonth: true,
                    changeYear: true,
                    yearRange: '2005:c+1'
                });
                
                $("#form1").validationEngine();
                var id_destination = "projeto";
                
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, function(data) {
                    removeLoading();
                    $("#" + id_destination).html(data);
                    var selected = $("input[name=hide_" + id_destination + "]").val();
                    if (selected !== undefined) {
                        $("#" + id_destination).val(selected);
                    }
                    $('#projeto').trigger('change');
                }, "projeto");

            });
        </script>
        
    </body>
</html>