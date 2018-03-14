<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a>";
    exit;
}

include("../conn.php");
include("../classes/regiao.php");
include("../classes/projeto.php");
include("../classes/funcionario.php");
include("../classes_permissoes/regioes.class.php");
include("../classes_permissoes/acoes.class.php");
include("../wfunction.php");

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

$regiao = 45;

$projeto = 3338;
        
$sql = "SELECT B.id_clt, B.nome AS nome_clt, B.id_curso, D.nome AS nome_curso, A.id_projeto, C.nome AS nome_projeto, A.cod_status, A.nome_status, DATE_FORMAT(A.data, '%d/%m/%Y') AS data, YEAR(A.data) AS data_ano, DATE_FORMAT(A.data_retorno, '%d/%m/%Y') AS data_retorno, A.dias 
	FROM rh_eventos AS A
		LEFT JOIN rh_clt AS B ON (B.id_clt = A.id_clt)
		LEFT JOIN projeto AS C ON (A.id_projeto = C.id_projeto)
		LEFT JOIN curso AS D ON (B.id_curso = D.id_curso)
	WHERE A.cod_status = 20 AND A.status = 1 AND YEAR(A.data) >= 2012 AND A.id_regiao = $regiao AND A.id_projeto = $projeto
	ORDER BY A.data ASC, D.id_curso ASC";

$query = mysql_query($sql);

while($row = mysql_fetch_assoc($query)) {
    $array[$row['nome_projeto']][$row['data_ano']][] = $row;
}

//echo '<pre>';
//print_r($array);
//echo '</pre>';

?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>:: Intranet :: Relatório de clt's que já estiveram sob licença médica</title>
        
        <link href="../favicon.png" rel="shortcut icon" />
        
        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
         <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/global.js" type="text/javascript"></script>
        
    </head>
    <body>
        <div class="container">
            <p style="text-align: right; margin-top: 20px"><button type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button></p>
            <table class="table table-striped table-hover text-sm valign-middle" id="tbRelatorio">
            <?php foreach($array as $projeto => $anos) { ?>    
                <thead>
                    <tr>
                        <th colspan="6" style="text-align: center"><h3><?= $projeto ?></h3></th>
                    </tr>
                    <?php foreach($anos as $ano => $clts) { ?>
                        <tr>
                            <th colspan="6" style="text-align: center"><h4><?= $ano ?></h4></th>
                        </tr>
                        <tr>
                            <th style="border-left: 1px solid #ddd;border-right:1px solid #ddd;">Nome</th>
                            <th style="border-right:1px solid #ddd;">Função</th>
                            <th style="border-right:1px solid #ddd;">Status</th>
                            <th style="border-right:1px solid #ddd;">Data de Saída</th>
                            <th style="border-right:1px solid #ddd;">Data de Retorno</th>
                            <th style="border-right:1px solid #ddd;">Dias Afastado</th>
                        </tr>
                        <?php foreach($clts as $key => $clt) { ?>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="border-left: 1px solid #ddd;border-right:1px solid #ddd;"><?php echo $clt['id_clt'] . ' - ' . $clt['nome_clt']?></td>
                            <td style="border-right:1px solid #ddd;"><?php echo $clt['nome_curso'] ?></td>
                            <td style="border-right:1px solid #ddd;"><?php echo $clt['cod_status'] . ' - ' . $clt['nome_status'] ?></td>
                            <td style="text-align: center;border-right:1px solid #ddd;"><?php echo $clt['data']?></td>
                            <td style="text-align: center;border-right:1px solid #ddd;"><?php echo $clt['data_retorno']?></td>
                            <td style="text-align: center;border-right:1px solid #ddd;"><?php echo $clt['dias']?></td>
                        </tr>
                    </tbody>
                    <?php } ?>
                    <tr><th colspan="5" style="text-align: center"></th></tr>
                <?php } ?>
            <?php } ?>
            </table>
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        
    </body>
</html>
