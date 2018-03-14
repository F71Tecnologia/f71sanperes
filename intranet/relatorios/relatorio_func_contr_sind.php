<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}

function maskTel($t){
    
    if($t == '0' OR $t == ''){return null;}
    $t = str_replace(array('(',')','-',' '),'',$t);
    $telefone[0] = substr($t, 0,2);
    $telefone[1] = substr($t, 2,4);
    $telefone[2] = substr($t, 6,4);
    return "($telefone[0]) $telefone[1]-$telefone[2]";
}

function maskCnpj($c){
    $c = str_replace(array('/','-','.'),'',$c);
    $cnpj[0] = substr($c, 0,2);
    $cnpj[1] = substr($c, 2,3);
    $cnpj[2] = substr($c, 5,3);
    $cnpj[3] = substr($c, 8,4);
    $cnpj[4] = substr($c, 12,2);
    return "$cnpj[0].$cnpj[1].$cnpj[2]/$cnpj[3]-$cnpj[4]";
}

include "../conn.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include "../wfunction.php";
include "../classes_permissoes/acoes.class.php";
include "../classes/SindicatoClass.php";

$usuario = carregaUsuario();
$optRegiao = getRegioes();
$ACOES = new Acoes();

$idSindicato = (isset($_REQUEST['sindicato'])) ? $_REQUEST['sindicato'] : '';

$sindicato = getSindicatoID($idSindicato);
$rowMaster = mysql_fetch_assoc(mysql_query("SELECT * FROM master WHERE id_master = {$usuario['id_master']} LIMIT 1"));

$rsSindicato = getSindicato($usuario['id_regiao']);
while($rowSindicato = mysql_fetch_assoc($rsSindicato)){
    $optSindicato[$rowSindicato['id_sindicato']] = $rowSindicato['nome'];
}

$mesDesconto = sprintf("%02d",$sindicato['mes_desconto']);

$rsContribuicao = mysql_query("SELECT id_clt, a5019 FROM rh_folha_proc WHERE mes = '{$mesDesconto}' AND a5019 != 0.00 ORDER BY id_clt, ano ASC");
while($rowContribuicao = mysql_fetch_assoc($rsContribuicao)){
    $arrayContribuicao[$rowContribuicao['id_clt']] = number_format($rowContribuicao['a5019'],'2',',','.');
}
?>
<html>
    <head>
        <title>:: Intranet ::</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../resources/css/bootstrap.css" rel="stylesheet" type="text/css" />

        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/global.js" type="text/javascript"></script>
        <style>
            .table-header thead tr th{
                background-image:url('data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4gPHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PGxpbmVhckdyYWRpZW50IGlkPSJncmFkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjUwJSIgeTE9IjAlIiB4Mj0iNTAlIiB5Mj0iMTAwJSI+PHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iI2Y4ZjhmOCIvPjxzdG9wIG9mZnNldD0iMTAwJSIgc3RvcC1jb2xvcj0iI2U4ZThlOCIvPjwvbGluZWFyR3JhZGllbnQ+PC9kZWZzPjxyZWN0IHg9IjAiIHk9IjAiIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JhZCkiIC8+PC9zdmc+IA==');background-size:100%;background-image:-webkit-gradient(linear, 50% 0%, 50% 100%, color-stop(0%, #f8f8f8), color-stop(100%, #e8e8e8));background-image:-webkit-linear-gradient(top, #f8f8f8,#e8e8e8);background-image:-moz-linear-gradient(top, #f8f8f8,#e8e8e8);background-image:-o-linear-gradient(top, #f8f8f8,#e8e8e8);background-image:linear-gradient(top, #f8f8f8,#e8e8e8);-webkit-box-shadow:0 1px #fff inset;-moz-box-shadow:0 1px #fff inset;box-shadow:0 1px #fff inset;text-shadow:0 1px #fff
            }
            .table-bordered{
                *border:1px solid #ddd;*border-collapse:separate;*border-collapse:collapsed;*border-left:0;*-webkit-border-radius:4px;*-moz-border-radius:4px;*border-radius:4px
            }
            legend { width: auto; }
        </style>
        <script>
            $(function() {
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
    </head>
    <body class="novaintra" >     
        <div id="content">
            <form  name="form" action="" id="form1" method="post" id="form">
                <div id="head">
                    <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h4>Relação de Funcionários da Contribuição Sindical</h4>
                    </div>
                </div>
                <br class="clear">
                <br/>
                <fieldset class="noprint">
                    <div>
                        <!--<p><label class="first">Região:</label> <?php echo montaSelect($optRegiao, $id_regiao, array('name' => "regiao", 'id' => 'regiao')); ?> </p>-->
                        <p><label class="first">Sindicato:</label> <?php echo montaSelect($optSindicato, $idSindicato, array('name' => "sindicato", 'id' => 'sindicato')); ?> </p>
                    </div>
                    <br class="clear"/>
                    <p class="controls" style="margin-top: 10px;">
                        <input type="submit" name="gerar" value="Gerar" id="gerar"/>
                    </p>
                </fieldset>
            </form>
            <?php if(!empty($idSindicato)){ 
                
                $sqlClt = "
                SELECT 
                    A.id_clt, A.matricula, A.nome nomeClt, B.nome nomeCurso, B.salario
                FROM rh_clt A, curso B 
                WHERE 
                    A.id_curso = B.id_curso AND A.rh_sindicato = '{$idSindicato}'
                ORDER BY A.nome";
                //if($_COOKIE[logado] == 257){echo $sqlClt;}
                $rsClt = mysql_query($sqlClt) or die(mysql_error());
                while ($rowClt = mysql_fetch_assoc($rsClt)) {
                    $linha .= "
                    <tr>
                        <td>{$rowClt['matricula']}</td>
                        <td>{$rowClt['nomeClt']}</td>
                        <td>{$rowClt['nomeCurso']}</td>
                        <td>".number_format($rowClt['salario'],'2',',','.')."</td>
                        <td>{$arrayContribuicao[$rowClt['id_clt']]}</td>
                    </tr>";
                }
                ?>
                <!--<table class="table table-bordered table-striped table-header table-action" style="width: 95%; margin: 5% 2.5% 0% 2.5%;">-->
                <table class="table table-bordered table-striped table-header table-action" style="margin: 5% 0% 0% 0%;">
                    <tr>    
                        <td><?php echo $rowMaster['razao']; ?></td>
                        <td>CNPJ: <?php echo maskCnpj($rowMaster['cnpj']); ?></td>
                    </tr>
                    <tr>    
                        <td colspan="2">Endereço: <?php echo $rowMaster['endereco']; ?></td>
                    </tr>
                    <tr>    
                        <td>Telefone: <?php echo maskTel($rowMaster['telefone']); ?></td>
                        <td>Site: <?php echo $rowMaster['site']; ?></td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr>    
                        <td><?php echo $sindicato['nome']; ?></td>
                        <td>CNPJ: <?php echo maskCnpj($sindicato['cnpj']); ?></td>
                    </tr>
                    <tr>    
                        <td colspan="2">Endereço: <?php echo $sindicato['endereco']; ?></td>
                    </tr>
                    <tr>        
                        <td>Cod. Sindical:  <?php echo $sindicato['codigo_sindical']; ?></td>
                        <td>Telefone: <?php echo maskTel($sindicato['tel']); ?></td>
                    </tr>
                    <tr>    
                        <td>Unidade: <?php echo $sindicato['unidade']; ?></td>
                        <td>Competência: <?php echo mesesArray($sindicato['mes_desconto']); ?></td>
                    </tr>
                </table>
                <table class="table table-bordered table-striped table-header table-action" style="margin: 5% 0% 0% 0%;">
                    <thead>
                    <tr>    
                        <th>Matricula</th>
                        <th>Nome</th>
                        <th>Cargo</th>
                        <th>Salário</th>
                        <th>Contribuição</th>
                    </tr>
                    </thead>
                    <?php echo $linha; ?>
                </table>
            <?php } ?>
        </div>
    </body>
</html>