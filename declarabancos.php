<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = 'login.php?entre=true';</script>";
}
include("conn.php");
include "classes/regiao.php";
include("wfunction.php");
include ("empresa.php");

$usuario = carregaUsuario();

$id_bol = $_REQUEST['bolsista'];
$banco = $_REQUEST['banco'];
$regiao = 36;//$_REQUEST['regiao'];

//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
$data_cad = date('Y-m-d');
$user_cad = $_COOKIE['logado'];

$result_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '24' and id_clt = '$id_bol'");
$num_row_verifica = mysql_num_rows($result_verifica);

//if ($num_row_verifica == "0") {
//    mysql_query("INSERT INTO rh_doc_status(tipo,id_clt,data,id_user) VALUES ('24','$id_bol','$data_cad', '$user_cad')");
//} else {
//    mysql_query("UPDATE rh_doc_status SET data = '$data_cad', id_user = '$user_cad' WHERE id_clt = '$id_bol' and tipo = '24'");
//}
//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS

$tipo = $_REQUEST['tipo'];

#CASO NÃO SEJE CLT.. VAMOS REQUISITAR OS DADOS DA TABELA AUTONOMO
if ($tipo != "2") {
    $result = mysql_query(" SELECT *,date_format(data_entrada, '%d/%m/%Y')as data_entrada FROM autonomo where id_autonomo = '$id_bol' ");
    $row = mysql_fetch_array($result);

    $result_ban = mysql_query(" SELECT * FROM bancos where id_banco = '$banco' ");
    $row_ban = mysql_fetch_array($result_ban);

    $result_curso = mysql_query("Select * from curso where id_curso = '$row[id_curso]'");
    $row_curso = mysql_fetch_array($result_curso);
} else {
    $result = mysql_query("SELECT *,date_format(data_entrada, '%d/%m/%Y')as data_entrada FROM rh_clt where id_clt = '$id_bol'");
    $row = mysql_fetch_array($result);

    $result_ban = mysql_query(" SELECT * FROM bancos where id_banco = '$banco'");
    $row_ban = mysql_fetch_array($result_ban);

    $result_curso = mysql_query("Select * from  curso where id_curso = '$row[id_curso]'");
    $row_curso = mysql_fetch_array($result_curso);
}
//echo '<pre>';
//print_r($row_ban);
//echo '</pre>';

/* SELECIONANDO A EMPRESA */
$qrEmp = "SELECT * FROM rhempresa WHERE id_projeto = $row[id_projeto]";
$rsEmp = mysql_query($qrEmp);
$rowEmp = mysql_fetch_assoc($rsEmp);
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet ::</title>
        <link href="favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="resources/css/bootstrap-note.css" rel="stylesheet" media="all">
        <link href="resources/css/main.css" rel="stylesheet" media="all">
        <link href="resources/css/font-awesome.css" rel="stylesheet" media="all">
    </head>
    <body>
        <div class="container">
            <div class="row">
                <p class="text-center">
                <?php
                if ($row[tipo_contratacao] == "3") {
                    $result_coop = mysql_query("Select * from cooperativas where id_coop = '$row[id_cooperativa]'");
                    $row_coop = mysql_fetch_array($result_coop);
                    echo "<img src='cooperativas/logos/coop_" . $row_coop['id_coop'] . ".jpg' alt='log' width='110' height='79'/> <br>$row_coop[nome]";
                } else if ($tipo != "3") {
                    $img = new empresa();
                    $img->imagem();
                } ?>
                </p>
                <br>
                <!--<p>Rio de Janeiro, <?=date("d")?> de <?=mesesArray(date("m"))?> de <?=date("Y")?>.</p>-->
                <br>
                <p>Ao</p>
                <p class="text-bold text-lg"><?= $row_ban['razao'] ?></p>
                <!--<p>Rio de Janeiro, RJ</p>-->
                <br>
                <h2 class="text-bold text-center">DECLARAÇÃO</h2>
                <br>
                <p>Pelo presente, relacionamos os nossos colaboradores, para abertura de conta salário nesse estabelecimento bancário, a saber:</p>
                <p>Nome: <strong><?=$row['nome']?></strong></p>
                <p>CPF:<strong> <?=$row['cpf']; ?></strong></p>
                <p>Endereço:<strong> <?=$row['endereco'].", ".$row['numero'].", ".$row['complemento']?> - <?=$row['bairro']; ?> - <?=$row['cidade']; ?> CEP: <?=$row['cep']; ?></strong></p> 
                <p>Retirada Mensal: R$ <strong><?=number_format($row_curso['salario'], 2, ",", ".") ?></strong></p>
                <p>Atividade: <strong><?=$row_curso['nome']; ?></strong></p>
                <p>Data Início: <strong><?=$row['data_entrada']; ?></strong></p>
                <br>
                <!--<p>Agradecemos desde já pela atenção dispensada.</p>-->
                <br>    
                <p class="text-bold text-lg"><?= $row_ban['razao'] ?></p>
                <p><strong>Agência:</strong> <?= $row_ban['agencia'] ?></p>
                <p><strong>Conta:</strong> <?= $row_ban['conta'] ?></p>
                <p><strong>Convênio:</strong> <?= $row_ban['cod_convenio_carta'] ?></p>
                <!--<p>Atenciosamente,</p>-->
                <br>
                <p class="text-center">RIO DE JANEIRO, <?=date("d")?> de <?=mesesArray(date("m"))?> de <?=date("Y")?>.</p>
                <br>
                <p class="text-center">_____________________________________</p>
                <p class="text-bold text-center">
                    <?php
                    if ($row['tipo_contratacao'] == "3") {
                        echo $row_coop['nome'];
                    } else if ($row['tipo_contratacao'] != "3") {
                        $nomEmp2 = new empresa();
                        $nomEmp2->nomeEmpresa();
                    } ?>
                </p>
                <br>
                <hr>
                <br>
                <p class="text-center">CNPJ: <?=$rowEmp['cnpj']?></p>
                <p class="text-center"><?=$rowEmp['endereco']?></p>
                <p class="text-center">Tel.: <?=$rowEmp['tel']?></p>
            </div>
        </div>
    </body>
</html>