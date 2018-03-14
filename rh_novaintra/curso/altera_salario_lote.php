<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='login.php'>Logar</a>";
    exit;
}

include("../../conn.php");
include("../../classes/regiao.php");
include("../../classes/projeto.php");
include("../../classes/funcionario.php");
include("../../classes_permissoes/regioes.class.php");
include("../../classes_permissoes/acoes.class.php");
include("../../classes/FuncoesClass.php");
include("../../wfunction.php");


$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
#RECEBENDO VARIAVEIS DO GET
$projeto = $_REQUEST['projeto'];
$regiao = $usuario['id_regiao'];
$data_hoje = date('d/m/Y');

#RECUPERANDO AS FUNÇÕES P
$objFuncao = new FuncoesClass();
$funcaors = $objFuncao->listFuncoesPolitica($regiao);

$optFuncoes = array('-1' => "« Todas as Funções »");
while ($rowFuncao = mysql_fetch_assoc($funcaors)) {
    $optFuncoes[$rowFuncao['id_curso']] = $rowFuncao['id_curso'] . " - " . $rowFuncao['nome'];
}

//echo "<pre>";
//  print_r($optFuncoes);
//  echo "</pre>";

$optRegioes = getRegioes();

//$sql_data_entrada = "SELECT A.id_clt, A.nome AS nome_clt, B.id_curso AS curso_clt,B.nome AS nome_curso_clt,B.letra as letra_clt, B.numero as numero_clt, A.id_curso AS Acurso, A.locacao, A.data_entrada, DATE_SUB(DATE_ADD(A.data_entrada,INTERVAL '3' month),INTERVAL '10' day) as data_10_dias, CURDATE() AS data_atual,
//DATE_ADD(A.data_entrada, INTERVAL '3' month) AS termino_experiencia, DATE_SUB(CURDATE(), INTERVAL '3' month) AS data_atual_menos_3, 
//
//CASE
//
//WHEN DATE_SUB(DATE_ADD(A.data_entrada,INTERVAL '3' month),INTERVAL '10' day) BETWEEN DATE_SUB(CURDATE(),INTERVAL 10 day) AND CURDATE()
//THEN 'Faltam até 10 dias para o Aumento Salárial'
//  
//WHEN A.data_entrada BETWEEN DATE_SUB(CURDATE(),INTERVAL '3' month) AND CURDATE()     
//THEN 'Dentro dos 3 meses'
//
//WHEN A.data_entrada < DATE_SUB(CURDATE(),INTERVAL '3' month) 
//THEN 'Maior que 3 meses'
//
//ELSE 'Nenhuma condição encontrada para o CLT' 
//END AS status_contratacao
//
//FROM rh_clt AS A 
//
//LEFT JOIN curso AS B ON (B.id_curso = A.id_curso)
//
//WHERE year(data_entrada) >= year(DATE_SUB(curdate(),INTERVAL '4' month)) AND A.id_regiao = {$usuario['id_regiao']} AND (A.status < '60' OR A.status = '200' OR A.status = '70') AND B.nome LIKE '%P' ORDER BY data_10_dias ";
//
//echo "<pre>{$sql_data_entrada}</pre>";
//$result_data_entrada = mysql_query($sql_data_entrada) or die(mysql_error());

if(isset($_REQUEST['filtrar'])) {
    $idRegiao = $_REQUEST['id_regiao'];
    $idCurso = $_REQUEST['id_curso'];
    
    $auxCurso = "AND B.id_curso = '$idCurso'";
    
    if ($idCurso == -1) {
        $auxCurso = null;
    }

    $sql_data_entrada = "SELECT A.id_clt, A.nome AS nome_clt, B.id_curso AS curso_clt,B.nome AS nome_curso_clt,B.letra as letra_clt, B.numero as numero_clt, A.id_curso AS curso, A.locacao, A.data_entrada, DATE_SUB(DATE_ADD(A.data_entrada,INTERVAL '3' month),INTERVAL '10' day) as data_10_dias, CURDATE() AS data_atual,
    DATE_ADD(A.data_entrada, INTERVAL '3' month) AS termino_experiencia, DATE_SUB(CURDATE(), INTERVAL '3' month) AS data_atual_menos_3, 

    CASE

     WHEN DATE_SUB(DATE_ADD(A.data_entrada,INTERVAL '3' month),INTERVAL '10' day) BETWEEN DATE_SUB(CURDATE(),INTERVAL 10 day) AND CURDATE()
      THEN 'Faltam até 10 dias para o Aumento Salárial'

     WHEN A.data_entrada BETWEEN DATE_SUB(CURDATE(),INTERVAL '3' month) AND CURDATE()     
      THEN 'Dentro dos 3 meses'

     WHEN A.data_entrada < DATE_SUB(CURDATE(),INTERVAL '3' month) 
      THEN 'Maior que 3 meses'

     ELSE 'Nenhuma condição encontrada para o CLT' 
    END AS status_contratacao

    FROM rh_clt AS A 

    LEFT JOIN curso AS B ON (B.id_curso = A.id_curso)

    WHERE year(data_entrada) >= year(DATE_SUB(curdate(),INTERVAL '4' month)) AND A.id_regiao =  '$idRegiao' {$auxCurso} AND (A.status < '60' OR A.status = '200' OR A.status = '70') AND B.nome LIKE '%P' ORDER BY A.data_entrada";


//    echo "<pre>{$sql_data_entrada}</pre>";
    $result_data_entrada = mysql_query($sql_data_entrada) or die(mysql_error());
    
//    echo "<pre>{$result_data_entrada}</pre>";

}

$breadcrumb_config = array("nivel" => "../../", "key_btn" => "3", "area" => "Principal", "id_form" => "form1", "ativo" => "Principal");

$funcaoSel = (isset($_REQUEST['id_curso'])) ? $_REQUEST['id_curso'] : null;
$regiaoSel = (isset($_REQUEST['id_regiao'])) ? $_REQUEST['id_regiao'] : null;
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Relatório de Atividades por Lotação</title>

        <link href="favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="all">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">

    </head>
    <body>
<?php include("../../template/navbar_default.php"); ?>      
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-money"></span> - Funcionários <small> - Alteração Salárial</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">

                <div class="panel panel-default hidden-print">
                    <div class="panel-heading text-bold">Alteração Salárial</div>
                    <div class="panel-body">
                        <div class="form-group">

                            <label for="fator" class="col-sm-1 control-label hidden-print" >Regiao</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect($optRegioes, $regiaoSel, "id='id_regiao' name='id_regiao' class='form-control'"); ?>
                            </div>

                            <label for="tipo" class="col-sm-2 control-label" >Funções Politica</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect($optFuncoes, $funcaoSel, array('id' => 'id_curso', 'name' => 'id_curso', 'class' => 'form-control')); ?>
                            </div>

                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <button type="submit" name="filtrar" id="filtrar" class="btn btn-info"> <i class="fa fa-filter"></i> Filtrar</button>
                    </div>
                </div>
                
                </form>
                <?php if (isset($_REQUEST['filtrar'])) {?>
                <div id="conteudo_fixo">
                    <form action="altera_funcao.php" method="post" id="altera_funcao">
                        <input form="altera_funcao" type="text" style="display: none;" name="regiaoFunc" value="<?php echo $idRegiao ?>"/>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Atualizar</th>
                                <th>Nome</th>
                                <th>Função</th>
                                <th>Data de Admissão</th>
                                <th>Data de Alteração Salárial</th>
                                <!--<th>Status</th>-->
                            </tr>
                        </thead>
                       
                        <tbody>
                            <?php while ($row2 = mysql_fetch_assoc($result_data_entrada)) { ?>
                        
                            <tr>
                                <td>
                                    <input type='checkbox' id='id_clt_check' name="id_clt[]" value='<?php echo $row2['id_clt']?>'/>
                                </td>
                                <td><?php echo $row2['nome_clt'] ?></td>
                                <td><?php echo $row2['nome_curso_clt'] . " " . $row2['letra_clt'] . $row2['numero_clt'] ?></td>
                                <td>
                                    <?php
                                    $data = $row2['data_entrada'];
                                    $data = str_replace('-', '/', $data);
                                    echo date('d/m/Y', strtotime($data));
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $data = $row2['termino_experiencia'];
                                    $data = str_replace('-', '/', $data);
                                    echo date('d/m/Y', strtotime($data));
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    /*$dataT = new DateTime($row2['termino_experiencia']);
                                    $dataE = new DateTime();
                                    $dt = $dataT->diff($dataE);
                                    echo "Faltam " . $dt->days . " Dias para Alteração";*/
                                    ?>
                                </td>

                            </tr>                                
                            <?php } ?>
                        </tbody>
                    </table>
                        <button type="submit" name="processar" id="processar" class="btn btn-inverse pull-right" ><i class="fa fa-circle-o-notch"></i>Processar</button>
            </form>
                </div>
                <?php } ?>

            <?php include('../../template/footer.php'); ?>
            <div class="clear"></div>
        </div>

        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script>

        <?php if(isset($_REQUEST['erro']) && !isset($_REQUEST['filtrar'])) {?>
                bootAlert("Por favor, selecione ao menos 1(um) funcionário para concluir a operação.", "SELECIONE UM FUNCIONÁRIO", null, 'danger');
        <?php } ?>

        </script>
    </body>
</html>

