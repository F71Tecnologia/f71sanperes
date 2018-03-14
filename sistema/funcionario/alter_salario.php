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
include("../../wfunction.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$ClasReg = new regiao();
$ClasPro = new projeto();

#SELECIONANDO O MASTAR PARA CARREGAR A IMAGEM
$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);

#RECEBENDO VARIAVEIS DO GET
$projeto = $_REQUEST['projeto'];
$regiao = $usuario['id_regiao'];
$data_hoje = date('d/m/Y');

#CLASSE PEGANDO OS DADOS DO PROJETO
$ClasPro->MostraProjeto($projeto);
$nome_pro = $ClasPro->nome;

#CLASSE PEGANDO O NOME DA REGIAO
$ClasReg->MostraRegiao($regiao);
$nome_regiao = $ClasReg->regiao;

#SELECIONANDO AS LOCAÇÕES
$relocacao = mysql_query("SELECT * FROM unidade WHERE id_regiao = '$regiao' AND campo1 = '$projeto'") or die(mysql_error());
$num_locacao = mysql_num_rows($relocacao);

$projetosOp = array("-1" => "« Selecione »");
$query = "SELECT id_projeto,nome FROM projeto WHERE id_regiao = '{$regiao}'";
$result = mysql_query($query) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
    $projetosOp[$row['id_projeto']] = $row['id_projeto'] . " - " . $row['nome'];
}




$result_data_entrada = mysql_query("SELECT A.id_clt, A.nome AS nome_clt, B.id_curso AS curso_clt,B.nome AS nome_curso_clt,B.letra as letra_clt, B.numero as numero_clt, A.id_curso AS Acurso, A.locacao, A.data_entrada, DATE_SUB(DATE_ADD(A.data_entrada,INTERVAL '3' month),INTERVAL '10' day) as data_10_dias, CURDATE() AS data_atual,
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

WHERE year(data_entrada) >= year(DATE_SUB(curdate(),INTERVAL '4' month)) AND A.id_regiao = {$usuario['id_regiao']} AND (A.status < '60' OR A.status = '200' OR A.status = '70') AND B.nome LIKE '%P' ORDER BY data_10_dias ") or die(mysql_error());

$breadcrumb_config = array("nivel" => "../../", "key_btn" => "3", "area" => "Principal", "id_form" => "form1", "ativo" => "Alteração Salarial");
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
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">

    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>      
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-money"></span> - Funcionários <small> - Alteração Salárial</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">

                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Alteração Salárial</div>
                    <div id="conteudo_fixo">
                        <?php if (!empty($result_data_entrada)) { ?>
                            <p id="excel" style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Relatório')" value="Exportar para Excel" class="btn btn-success"/></p>
                            <table id="tbRelatorio" class="table table-striped table-hover text-sm valign-middle table-bordered">
                                <thead>
                                    <tr>
                                        <th>Atualizar</th>
                                        <th>Nome</th>
                                        <th>Função</th>
                                        <th>Data de Admissão</th>
                                        <th>Data de Alteração Salárial</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($row2 = mysql_fetch_assoc($result_data_entrada)) {
                                        ?>




                                        <?php if ($row2['termino_experiencia'] > date('Y-m-d')) {
                                            ?><tr>
                                                <td><?php
                                                    echo "<input type='checkbox' id='id_clt_check' value='{$row2['id_clt']}'></input>";
                                                    ?>
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
                                                <td><?php
                                                    $dataT = new DateTime($row2['termino_experiencia']);
                                                    $dataE = new DateTime();
                                                    $dt = $dataT->diff($dataE);
                                                    echo "Faltam " . $dt->days . " Dias para Alteração";
                                                    ?></td>
                                            </tr>
                                        <?php } ?>   

                                    <?php } ?>
                                </tbody>
                            </table>
                        <?php } ?>
                    </div>
                </div>

            </form>

   <!--<input type="submit" class="btn btn-info" data-toggle="modal" data-target="#myModal" value="Confirmar"></input>-->

            <!--            <div class="container">
                            
                             Modal 
                            <div class="modal fade" id="myModal" role="dialog">
                              <div class="modal-dialog">
            
                                 Modal content
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">Alterar Função</h4>
                                  </div>
                                  <div class="modal-body">
                                    
                                <table class="table table-striped">
                                <thead>
                                    <tr>
            
                                      <th>Nome</th>
                                      <th>Função Atual</th>
                                      <th>Função Desejada</th>
                                      <th>Data de Alteração</th>
                                      <th>Status</th>
            
            <?php
            ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Teste</td>
                                        <td>Atual Function</td>
                                        <td>
                                            <select>
                                                <option value='' id='funcao_futura' class='form-control'>Selecione</option>
                                                <option value='' id='funcao_futura' class='form-control'>Teste1</option>
                                            </select>
                                        </td>
                                        <td>ASAS</td>
                                        <td>vbvbvbvb</td>
                                    </tr>    
                                </tbody>
                                </table>
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                  </div>
                                </div>
            
                              </div>
                            </div>
            
                          </div>-->

            <?php include('../../template/footer.php'); ?>
            <div class="clear"></div>
        </div>

        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script>

//        $(document).ready(function(){
//            var checkbox = $('input:checkbox').val();
//            var id_clt_checkbox = <?php $row2['id_clt'] ?>
//            $("#id_clt_check").click(function(){
//                
//                if(checkbox = id_clt_checkbox){
//                alert(checkbox.val());    
//                }
//                
//            });
//            
//            
//        });







        </script>
    </body>
</html>
