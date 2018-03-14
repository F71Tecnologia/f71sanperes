<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a>";
    exit;
}

include "../conn.php";
include "../classes/funcionario.php"; 
include '../classes_permissoes/regioes.class.php';
include "../wfunction.php";
include "../classes_permissoes/acoes.class.php";

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$optRegiao = getRegioes();
$ACOES = new Acoes();

function formataCbo($cbo) {
    $cbo = RemoveEspacos(RemoveCaracteresGeral($cbo));
    return sprintf("%07s", substr($cbo, 0, 3) . '-' . substr($cbo, 4, 5));
}


if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos']) || isset($_REQUEST['exportar'])) {
    
    $cont = 0;
    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    
    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto'=>$id_projeto));
    
        
        $str_qr_relatorio = "SELECT A.matricula, A.id_clt, H.nome AS locacao, A.nome, A.cpf, G.area_nome, B.nome AS nome_curso,  
                            DATE_FORMAT(A.data_nasci, '%d/%m/%Y') AS data_nascibr, 
                            DATE_FORMAT(A.data_entrada, '%d/%m/%Y') AS data_entradabr, 
                            DATE_FORMAT(A.data_exame, '%d/%m/%Y') AS data_examebr,
                            A.sexo, C.cod AS cbo, 
                            if(A.`status` = 10, 'N','S') AS afastado, 
                            if(A.`status` = 10, '', D.especifica) AS motivoAfastamento,
                            A.pis, A.campo1 AS numero_ctps, A.serie_ctps, A.uf_ctps, 
                            if(A.deficiencia = '', 'NA', if(A.deficiencia = 6, 'BR', 'PDH')) AS brPdh, 
                            F.horas_semanais AS regRevezamento, F.folga,
                            A.rg, A.email, E.nome AS nomeDef, A.id_projeto
                            FROM rh_clt AS A
                            LEFT JOIN curso AS B ON (B.id_curso = A.id_curso)
                            LEFT JOIN rh_cbo AS C ON (C.id_cbo = B.cbo_codigo)
                            LEFT JOIN rhstatus AS D ON (D.codigo = A.`status`)
                            LEFT JOIN deficiencias AS E ON (E.id = A.deficiencia)
                            LEFT JOIN rh_horarios AS F ON (F.funcao = A.rh_horario)
                            LEFT JOIN areas AS G ON (G.area_id = B.area_funcao)
                            LEFT JOIN projeto AS H ON (H.id_projeto = A.id_projeto)
                            WHERE A.id_regiao = '$id_regiao' AND A.tipo_contratacao = '2' AND (D.codigo < '60' OR (D.codigo > '66' AND D.codigo <> '81' AND D.codigo <> '101')) ";
         
    if(!isset($_REQUEST['todos_projetos'])) {
        $str_qr_relatorio .= "AND A.id_projeto = '$id_projeto' ";
    }
    
    $str_qr_relatorio .= "ORDER BY A.id_projeto,A.nome";

    $qr_relatorio = mysql_query($str_qr_relatorio) or die(mysql_error());
}

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;


?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>:: Intranet :: Relatório Planilha de Funcionários</title>
        
        <link href="../favicon.png" rel="shortcut icon" />
        
        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
    </head>

    <body>
        <?php include("../template/navbar_default.php"); ?>      
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Relatórios de Planilha de Funcionários</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                    <div class="panel-body">
                        <div class="form-group" >
                            
                            <label for="select" class="col-sm-2 control-label hidden-print" >Região</label>
                            <div class="col-sm-4">
                              <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                            </div>
                            
                            <label for="select" class="col-sm-1 control-label hidden-print" >Projeto</label>
                            <div class="col-sm-4">
                              <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'validate[required] form-control')); ?> <span class="loader"></span> 
                            </div>
                            
                             <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                        </div>
                    </div>
                        
                        <div class="panel-footer text-right hidden-print controls">
                            <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])){ ?>
                                <button type="button" onclick="tableToExcel('tbRelatorio', 'Planilha Funcionarios')" value="Exportar para Excel" class="btn btn-success" ><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                            <?php } ?>
                            <?php ///permissão para VISUALIZAR TODOS OS PROJETOS AO MESMO TEMPO
                                if($ACOES->verifica_permissoes(85)) { ?>
                            <button type="submit" name="todos_projetos" value="gerar de todos os projetos" id="todos_projetos" class="btn btn-warning"><span class="fa fa-filter"></span> Filtrar Todos os Projetos</button>
                            <?php } ?>
                            <button type="submit" name="gerar" id="gerar" value="gerrar" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar</button>
                        </div>
                    </div>
                
                <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>
                <table class="table table-striped table-bordered table-hover text-sm valign-middle" id="tbRelatorio">
                
                        <thead>
                            <tr> 
                                <th>MATRICULA</th>
                                <th>ID</th>
                                <th>EMPRESA/FILIAL</th>
                                <th>FUNCIONÁRIO</th>                                
                                <th>CPF</th>
                                <th>SETOR</th>
                                <th>FUNÇÃO</th>
                                <th>DATA DE NASCIMENTO</th>
                                <th>DATA DE ADMISSÃO</th>
                                <th>DATA DO ÚLTIMO EXAME</th>
                                <th>SEXO</th>
                                <th>CBO</th>
                                <th>AFASTADO?</th>
                                <th>MOTIVO DO AFASTAMENTO</th>
                                <th>NIT(PIS/PASEP)</th>
                                <th>CTPS/SÉRIE</th>
                                <th>BR/PDH</th>
                                <th>REGIME DE REVEZAMENTO</th>
                                <th>IDENTIDADE</th>
                                <th>EMAIL</th>
                                <th>DEFICIÊNCIA?</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) { $class = ($cont++ % 2 == 0)?"even":"odd"; ?>
                            <tr class="<?php echo $class ?>">
                                <td><?php echo RemoveCaracteresGeral($row_rel['matricula']); ?></td>
                                <td><?php echo RemoveCaracteresGeral($row_rel['id_clt']); ?></td>
                                <td><?php echo $row_rel['locacao'] ?></td>
                                <td><?php echo RemoveCaracteresGeral(RemoveAcentos($row_rel['nome'])) ?></td>
                                <td><?php echo RemoveCaracteresGeral($row_rel['cpf']); ?></td>
                                <td><?php echo $row_rel['area_nome']; ?></td>
                                <td><?php echo $row_rel['nome_curso']; ?></td>
                                <td><?php echo $row_rel['data_nascibr']; ?></td>
                                <td><?php echo $row_rel['data_entradabr']; ?></td>
                                <td><?php echo $row_rel['data_examebr']; ?></td>
                                <td><?php echo $row_rel['sexo']; ?></td>
                                <td><?php echo formataCbo($row_rel['cbo']); ?></td>
                                <td><?php echo $row_rel['afastado']; ?></td>
                                <td><?php echo $row_rel['motivoAfastamento']; ?></td>
                                <td><?php echo $row_rel['pis']; ?></td>
                                <td><?php echo $row_rel['numero_ctps'].'/'.$row_rel['serie_ctps'].'-'.$row_rel['uf_ctps']; ?></td>
                                <td><?php echo $row_rel['brPdh']; ?></td>
                                <td><?php echo $row_rel['regRevezamento']; ?></td>
                                <td><?php echo $row_rel['rg']; ?></td>
                                <td><?php echo $row_rel['email']; ?></td>
                                <td><?php echo $row_rel['nomeDef']; ?></td>
                            </tr>                                
                        <?php } ?>
                    </tbody>
                </table>
                <?php  } ?>
             
            </form>
            <?php include('../template/footer.php'); ?>
            <div class="clear"></div>
        </div>

        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script>
           $(function() {
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, null, "projeto");     
                
                $('#exportar').click(function(){
                   $('#form').attr('action','exporta_planilha_funcionarios.php');
                   $('#form').submit();
                });
                
                 $('.gera').click(function(){
                      $('#form').attr('action','');
                      if($(this).val() === 'Gerar de Todos Projetos'){
                          $('#projeto').val('-1');
                      }
                 });
            });
        </script>
        
    </body>
</html>
