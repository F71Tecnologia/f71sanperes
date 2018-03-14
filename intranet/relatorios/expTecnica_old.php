<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a>";
    exit;
}

include('../conn.php');
include('../classes/global.php');
include('../wfunction.php');

if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "carregaCargos") {

    $projSelect = $_REQUEST['proj'];

    $rs = mysql_query("SELECT * FROM curso WHERE campo3 IN({$projSelect}) ORDER BY nome");
    $cargo = utf8_encode("<option value=\"-1\">« Todos »</option>");
    while ($row = mysql_fetch_assoc($rs)) {
        $cargo .= "<option value=\"{$row['id_curso']}\">{$row['id_curso']} - " . utf8_encode($row['nome']) . "</option>";
    }

    echo $cargo;
    exit;
}

$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);

/*CARREGA TODOS OS PROJETOS*/
$projetos = array();
$projeto     = mysql_query("SELECT * FROM projeto WHERE id_regiao = {$row_user['id_regiao']} ORDER BY nome");
$projetos[' '] = "<< Selecione >>";
while($linha = mysql_fetch_assoc($projeto)){
    $projetos[$linha['id_projeto']] = $linha['nome'];
}


$meses          = mesesArray(null);
//$ano            = array("2013" => 2013, "2014" => 2014);
$ano            = anosArray(null, null, array('' => "<< Ano >>"));
$cargo          = array("-1" => "Aguardando Projeto");
$status         = array("todos" => "Todos" ,"admitido" => "Admitido", "demitido" => "Demitido");
$mesSelectI     = (isset($_REQUEST['mesI'])) ? $_REQUEST['mesI'] : null;
$mesSelectF     = (isset($_REQUEST['mesF'])) ? $_REQUEST['mesF'] : null;
$anoSelectI     = (isset($_REQUEST['anoI'])) ? $_REQUEST['anoI'] : null;
$anoSelectF     = (isset($_REQUEST['anoF'])) ? $_REQUEST['anoF'] : null;
$statusSelect   = (isset($_REQUEST['status'])) ? $_REQUEST['status'] : null;
$inicio         = $_REQUEST['anoI'] ."-". sprintf("%02d",$_REQUEST['mesI']) ."-". "01";
$final          = $_REQUEST['anoF'] ."-". sprintf("%02d",$_REQUEST['mesF']) ."-". "31"; 
$statusSelect   = (isset($_REQUEST['status'])) ? $_REQUEST['status'] : null;
$projSelect     = (isset($_REQUEST['proj']) ? $_REQUEST['proj'] : null);
$cargoSelect    = (isset($_REQUEST['cargo']) ? $_REQUEST['cargo'] : null);

if (isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])) {
    $filtro = true;
    //TRATANDO STATUS DO CLT
    if($statusSelect == "todos"){
        $criteria = "";
        $having   = "";
    }else if($statusSelect == "admitido"){
        $criteria = " AND (A.status < 60 OR A.status = 200) ";
        $having   = "HAVING statusColaborador = '{$statusSelect}'";
    }else{
        $criteria = " AND (A.status >= 60 AND A.status != '200') ";
        $having   = "HAVING statusColaborador = '{$statusSelect}'";
    }
    
    //TRATANDO CURSO
    if($cargoSelect[0] == -1){
        $criteria_curso = "";
    }else{
        $criteria_curso = "AND B.id_curso IN (".implode(',',$cargoSelect).")";
    }
    
    $qrTec = "
    SELECT * 
    FROM
        (SELECT
            A.nome, 
            DATE_FORMAT(A.data_entrada, '%d/%m/%Y') as data_entrada, 
            DATE_FORMAT(A.data_saida, '%d/%m/%Y') as data_saida,
            A.status,
            B.campo2,
            IF(A.data_saida BETWEEN '{$inicio}' AND '{$final}','demitido','admitido') as statusColaborador
        FROM 
            rh_clt A 
            LEFT JOIN curso B on (B.id_curso = A.id_curso)
        WHERE ((A.data_entrada BETWEEN '{$inicio}' AND '{$final}') OR (A.data_saida BETWEEN '{$inicio}' AND '{$final}'))
            AND A.id_projeto IN($projSelect)
            AND A.tipo_contratacao = '2' 
            $criteria_curso
            $criteria 
        ORDER BY A.nome) AS tmp 
    $having";
    
    echo "<!--".$qrTec."-->";
    $qr = mysql_query($qrTec);
    $num_rows = mysql_num_rows($qr);
}?>

<html>
    <head>
        <title>:: Intranet ::</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/global.js" type="text/javascript"></script>
        <script>
            $(function(){
                console.log($('#proj').val());
                $('#proj').ajaxGetJson("expTecnica.php", {method: "carregaCargos"}, null, "cargo");
            });
        </script>
    
        <style type="text/css">
            #newcontent {  
                background-attachment: scroll;
                background-clip: border-box;
                background-color: #FFFFFF;
                background-image: none;
                background-origin: padding-box;
                background-position: 0 0;
                background-repeat: repeat;
                background-size: auto auto;
                margin-bottom: 0;
                margin-left: auto;
                margin-right: auto;
                margin-top: 0;
                padding-bottom: 15px;
                padding-left: 15px;
                padding-right: 15px;
                padding-top: 15px;
            }
        </style>

    </head>
    <body id="page-despesas" class="novaintra">
        <div id="newcontent">
            <form action="" method="post" name="form1" id="form1">
                <fieldset>
                    <legend><h3>RELAT&OacuteRIO DE EXPORTA&Ccedil&AtildeO T &EacuteCNICA</h3></legend>
                    <p><label class="first">Mês/Ano Início:</label> <?php echo montaSelect($meses, $mesSelectI, "id='mesI' name='mesI' class='required[custom[select]]'") ?><?php echo montaSelect($ano, $anoSelectI, "id='anoI' name='anoI' class='required[custom[select]]'") ?></p>
                    <p><label class="first">Mês/Ano Fim:</label> <?php echo montaSelect($meses, $mesSelectF, "id='mesF' name='mesF' class='required[custom[select]]'") ?><?php echo montaSelect($ano, $anoSelectF, "id='anoF' name='anoF' class='required[custom[select]]'") ?></p>
                    <p><label class="first">Projeto:</label> <?php echo montaSelect($projetos, $projSelect, "id='proj' name='proj' class='required[custom[select]]'") ?></p>
                    <p><label class="first">Cargo:</label><?php echo montaSelect($cargo, $cargoSelect, "id='cargo' name='cargo[]' class='required[custom[select]]' multiple style='height: 150px;'") ?></p>
                    <p><label class="first">Status:</label><?php echo montaSelect($status, $statusSelect, "id='status' name='status' class='required[custom[select]]'") ?></p>
                    <p><input type="submit" name="filtrar" id="filtrar" value="Filtrar" class="botao" style="float: right; margin-bottom: 10px; margin-right: 10px;"/></p>
                </fieldset>

                <div id="resultadoExportados">
                    <?php
                    if ($filtro) {
                        if ($num_rows > 0) {
                            $count = 0;
                            ?>
                            <br/>
                            <p style="float: left; margin-top: 20px;">Total de resultados encontrado: <span style="font-weight: bold"><?php echo $num_rows; ?></span> funcionários</p>
                            <p style="text-align: right;"><input type="button" onclick="tableToExcel('tabela', 'Exporta??o T?cnica')" value="Exportar para Excel" class="exportarExcel"></p>
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" id="tabela" class="grid">
                                <thead>
                                    <tr>
                                        <th colspan="5"><?=$projetos[$projSelect]?></th>
                                    </tr>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Cargo</th>
                                        <th>Data Admiss&Atildeo</th>
                                        <th>Data Demiss&Atildeo</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysql_fetch_assoc($qr)) { ?>
                                        <tr class="<?php echo ($count++ % 2 == 0) ? "odd" : "even" ?>">
                                            <td align="center"><?php echo RemoveAcentos($row['nome']); ?></td>
                                            <td align="center"><?php echo RemoveAcentos($row['campo2']); ?></td>
                                            <td align="center"><?php echo $row['data_entrada']; ?></td>
                                            <td align="center"><?php echo ($row['data_saida']); ?></td>
                                            <td align="center"><?php echo $row['statusColaborador']; ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr class="<?php echo ($count++ % 2 == 0) ? "odd" : "even" ?>">
                                        <th align="center" colspan="2">Total</th>
                                        <th align="center" colspan="3"><?=$num_rows?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        <?php } else { ?>
                            <br/>
                            <div id='message-box' class='message-yellow'>
                                <p>Nenhum registro encontrado</p>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
            </form>    
        </div>
    </body>
</html>