<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
}

include("../conn.php");
include("../wfunction.php");

$mes = validatePost('mes');
$ano = validatePost('ano');
$mesSel = (!empty($mes)) ? $mes : date('m');
$anoSel = (!empty($ano)) ? $ano : date('Y');

if(validate($_REQUEST['method']) && $_REQUEST['method'] == "atualiza"){
    $id_especifico = validatePost("id_especifico");
    $id_rescisao = validatePost("id_rescisao");
    
    $sql = "UPDATE pagamentos_especifico SET id_rescisao = {$id_rescisao} WHERE id_especifico = {$id_especifico}";
    $ok = 0;
    if(mysql_query($sql)){
        $ok = 1;
    }
    $arr = array('status' => $ok);
    echo json_encode($arr);
    exit;
}

$sql = "SELECT A.id_recisao,A.id_clt,A.nome,
            DATE_FORMAT(A.data_demi, '%d/%m/%Y') AS data_demi,
            DATE_FORMAT(C.data_vencimento, '%d/%m/%Y') AS data_vencimento,
            A.total_liquido,B.*,
            C.id_saida as saidaid,C.nome as nomesaida,C.especifica,
            REPLACE(C.valor, ',', '.') as valor,C.id_clt as cltsaida,
            CASE C.status
                WHEN 0 THEN 'excluido'
                WHEN 1 THEN 'não pago'
                WHEN 2 THEN 'pago'
            END AS status_saida
            FROM rh_recisao AS A
            LEFT JOIN pagamentos_especifico AS B ON (A.id_clt = B.id_clt)
            LEFT JOIN saida AS C ON (B.id_saida = C.id_saida)
            
            WHERE DATE_FORMAT(A.data_demi, '%Y-%m') = '{$anoSel}-".sprintf("%02d",$mesSel)."' AND A.`status` = 1";
$result = mysql_query($sql);

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$optionsMeses = mesesArray();
$optionsAnos = anosArray();

?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Gestão de RH</title>

        <link rel="shortcut icon" href="favicon.ico" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>

        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <form class="form-horizontal" method="post" action="" name="">
                        <fieldset>
                            <legend>Filtro</legend>
                            <div class="form-group">
                                <label for="mes" class="col-lg-2 control-label">Mes/Ano</label>
                                <div class="col-lg-5">
                                    <?php echo montaSelect($optionsMeses, $mesSel, "id='mes' name='mes' class='form-control'") ?>
                                </div>
                                <div class="col-lg-5">
                                    <?php echo montaSelect($optionsAnos, $anoSel, "id='ano' name='ano' class='form-control'") ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-lg-10 col-lg-offset-2">
                                    <input type="submit" class="btn btn-primary" id="buscarFinalizadas" value="Buscar">
                                </div>
                            </div>
                        </fieldset>
                    </form>
                    
                    <table class='table table-striped table-hover'>
                        <thead>
                            <th>id_rescisao</th>
                            <th>id_clt</th>
                            <th>cltsaida</th>
                            <th>nome</th>
                            <th>data_demi</th>
                            <th>data_venci</th>
                            <th>id_especifico</th>
                            <th>id_saida</th>
                            <th>ID_RECISAO</th>
                            <th>id_saida</th>
                            <th>nomesaida</th>
                            <th>valor saida</th>
                            <th>valor rescisao</th>
                            <th>statusSaida</th>
                            <th>#</th>
                        </thead>
                        <tbody>
                            <?php 
                            
                            while ($row = mysql_fetch_assoc($result)){ 
                                $class = "";
                                if(!empty($row['id_rescisao'])){
                                    $class = "info";
                                }
                                
                                if(empty($row['id_rescisao'])){
                                    $class = "danger";
                                }
                                
                                if(empty($row['id_rescisao']) && $row['valor'] == $row['total_liquido']){
                                    $class = "warning";
                                }
                                
                                if(!empty($row['id_rescisao']) && $row['valor'] == $row['total_liquido']){
                                    $class = "success";
                                }
                            ?>
                            <tr class="<?php echo $class ?>">
                                <td><?php echo $row['id_recisao'] ?></td>
                                <td><?php echo $row['id_clt'] ?></td>
                                <td><?php echo $row['cltsaida'] ?></td>
                                <td><?php echo $row['nome'] ?></td>
                                <td><?php echo $row['data_demi'] ?></td>
                                <td><?php echo $row['data_vencimento'] ?></td>
                                <td><?php echo $row['id_especifico'] ?></td>
                                <td><?php echo $row['id_saida'] ?></td>
                                <td><?php echo $row['id_rescisao'] ?></td>
                                <td><?php echo $row['saidaid'] ?></td>
                                <td><a href="javascript:;" class="bt" title="<?php echo $row['nomesaida'] ?>" data-especifico="<?php echo $row['id_especifico'] ?>" data-rescisao="<?php echo $row['id_recisao'] ?>">especificação</a></td>
                                <td><?php echo $row['valor'] ?></td>
                                <td><?php echo $row['total_liquido'] ?></td>
                                <td><?php echo $row['status_saida'] ?></td>
                                <td></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <footer>
                <div class="row">
                    <div class="page-header"></div>
                    <div class="pull-right"><a href="#top">Voltar ao topo</a></div>
                    <div class="col-lg-12">
                        <p>Pay All Fast 3.0</p>
                        <p>Todos os direitos reservados <a href="http://f71.com.br" rel="nofollow" target="_blank">F71 Sistemas</a>.</p>
                    </div>
                </div>
            </footer>
        </div>

        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script>
            $(function(){
                $(".bt").click(function(){
                    var botao = $(this);
                    var especifico = botao.data('especifico');
                    var rescisao = botao.data('rescisao');
                    $.post("rescisao_saida.php",{method: "atualiza", id_especifico: especifico, id_rescisao: rescisao}, function(data){
                        if(data.status==1){
                            botao.parents('tr').removeAttr("class").addClass("success");
                            botao.parent().prev().prev().html(rescisao);
                        }else{
                            alert("erro ao vincular rescisão a saída");
                        }
                    },"json");
                });
            });
        </script>
    </body>
</html>