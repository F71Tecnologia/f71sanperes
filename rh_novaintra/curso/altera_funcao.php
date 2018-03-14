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

$regiaoFunc = $_REQUEST['regiaoFunc'];
//echo '<pre>'.$regiaoFunc.'</pre>';

$sql = "SELECT id_curso, nome, letra, numero FROM curso WHERE id_regiao = {$regiaoFunc}";
$query = mysql_query($sql);

while ($rowFuncao = mysql_fetch_assoc($query)) {
    $optFuncoes[$rowFuncao['id_curso']] = $rowFuncao['id_curso'] . " - " . $rowFuncao['nome'] . " " . $rowFuncao["letra"] . $rowFuncao["numero"];
}

if(isset($_REQUEST['gravar']))  {
    $arrNovaFuncao = $_REQUEST['novaFuncao'];
    
//    echo '<pre>';
//    print_r($arrNovaFuncao);
//    echo '</pre>';
    foreach($arrNovaFuncao as $idClt => $idNovoCurso) {
        $sqlClt = "SELECT A.id_clt,A.id_regiao AS id_regiao_de,A.id_projeto AS id_projeto_de, A.id_curso AS id_curso_de,A.rh_horario AS id_horario_de, A.tipo_pagamento AS id_tipo_pagamento_de, A.banco AS id_banco_de, B.unidade AS unidade_de, A.id_unidade AS id_unidade_de, A.rh_sindicato AS id_sindicato_de
	FROM rh_clt AS A
		LEFT JOIN unidade AS B ON (B.id_unidade = A.id_unidade)
	WHERE id_clt = $idClt";
        
//        echo '<pre>' . $sqlClt . '</pre>';
        
        $queryClt = mysql_query($sqlClt);
        
                while ($arrClt = mysql_fetch_assoc($queryClt))  {
                    
//                        echo '<pre>';
//                        print_r($arrClt);
//                        echo '</pre>';
                    
                        $id_clt = $arrClt['id_clt'];
                        $id_regiao_de = $arrClt['id_regiao_de'];
                        $id_projeto_de = $arrClt['id_projeto_de'];
                        $id_curso_de = $arrClt['id_curso_de'];
                        $id_horario_de = $arrClt['id_horario_de'];
                        $id_tipo_pagamento_de = $arrClt['id_tipo_pagamento_de'];
                        $id_banco_de = $arrClt['id_banco_de'];
                        $unidade_de = $arrClt['unidade_de'];
                        $id_unidade_de = $arrClt['id_unidade_de'];
                        $id_sindicato_de = $arrClt['id_sindicato_de'];
                        $id_regiao_para = $arrClt['id_regiao_de'];
                        $id_projeto_para = $arrClt['id_projeto_de'];
                        $id_curso_para = $idNovoCurso;
                        $id_horario_para = $arrClt['id_horario_de'];
                        $id_tipo_pagamento_para = $arrClt['id_tipo_pagamento_de'];
                        $id_banco_para = $arrClt['id_banco_de'];
                        $unidade_para = $arrClt['unidade_de'];
                        $id_unidade_para = $arrClt['id_unidade_de'];
                        $id_sindicato_para = $arrClt['id_sindicato_de'];
                        
                        $sqlTransfer = "INSERT INTO rh_transferencias (id_clt, id_regiao_de, id_projeto_de, id_curso_de, id_horario_de, id_tipo_pagamento_de, id_banco_de, unidade_de, id_unidade_de, id_sindicato_de, id_regiao_para, id_projeto_para, id_curso_para, id_horario_para, id_tipo_pagamento_para, id_banco_para, unidade_para, id_unidade_para, id_sindicato_para, motivo, data_proc, criado_em, id_usuario)
                                                    VALUES ('$id_clt', '$id_regiao_de', '$id_projeto_de', '$id_curso_de', '$id_horario_de', '$id_tipo_pagamento_de', '$id_banco_de', '$unidade_de', '$id_unidade_de', '$id_sindicato_de', 
                                                    '$id_regiao_para', '$id_projeto_para', '$id_curso_para', '$id_horario_para', '$id_tipo_pagamento_para', '$id_banco_para', '$unidade_para', '$id_unidade_para', '$id_sindicato_para', 'Término de Período de Experiência',CURDATE(), NOW(), {$_COOKIE['logado']} )";
                        $queryTransfer = mysql_query($sqlTransfer) or die(mysql_error());
                        $insert_id = mysql_insert_id();
                        
                        $sqlUpdateClt = "UPDATE rh_clt SET id_curso = $idNovoCurso WHERE id_clt = $idClt;";
                        $queryUpdateClt = mysql_query($sqlUpdateClt);
                        
                        $operacao = true;
                }
        }
}

if(isset($_REQUEST['processar'])) {
        
/**
 * RECUPERANDO ARRAY DE CLTs
 */

$array_clt = $_REQUEST['id_clt'];
$idClt = implode(',',$array_clt);

//print_r($idClt);
        if(!empty($array_clt)) {

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

            WHERE year(data_entrada) >= year(DATE_SUB(curdate(),INTERVAL '4' month)) AND A.id_clt IN ($idClt)  AND (A.status < '60' OR A.status = '200' OR A.status = '70') AND B.nome LIKE '%P' ORDER BY A.data_entrada";


        //    echo "<pre>{$sql_data_entrada}</pre>";
            $result_data_entrada = mysql_query($sql_data_entrada) or die(mysql_error());

        //    echo "<pre>{$result_data_entrada}</pre>";
        } else {
            header("Location: altera_salario_lote.php?erro=true");
        }
}

$breadcrumb_config = array("nivel" => "../../", "key_btn" => "3", "area" => "Principal", "id_form" => "form1", "ativo" => "Principal");
$selFuncoes = (isset($_REQUEST['id_curso'])) ? $_REQUEST['id_curso'] : null;

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
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="all">
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

                <div class="panel panel-default hidden-print">
                    <div class="panel-heading text-bold">Alteração Salárial</div>
                </div>

                <div id="conteudo_fixo">

                    <table class="table table-striped">
                        <thead>
                            <tr>
<!--                                <th>Atualizar</th>-->
                                <th>Nome</th>
                                <th>Função</th>
                                <th>Admissão</th>
                                <th>Alteração Salárial</th>
                                <th>Nova Função</th>
                                <!--<th>Status</th>-->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($row2 = mysql_fetch_assoc($result_data_entrada)) { ?>
                            <tr>
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
                                    <?php echo montaSelect($optFuncoes, $selFuncoes, ' class="form-control" name="novaFuncao['.$row2['id_clt'].']" '); ?>                                    
                                </td>

                            </tr>
                            <?php } ?>
                            
                        </tbody>
                        
                        <button type="submit" name="gravar" id="gravar" class="btn btn-success pull-right" ><i class="fa fa-save"></i> Gravar</button>
                    </table>
                </div>
            </form>

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
               <?php if($operacao) { ?>
                        bootAlert("A operação solicitada foi realizada com sucesso.", "Operação Realizada com Sucesso", function(){window.location.href = "altera_salario_lote.php";}, 'success');

               <?php } ?>
        </script>    
    </body>
</html>

