<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

//error_reporting(E_ALL);


if (isset($_REQUEST['download']) && !empty($_REQUEST['download'])) {
    $file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'arquivos' . DIRECTORY_SEPARATOR . $_REQUEST['download'];
    $name_file_download = isset($_REQUEST['name_file']) ? $_REQUEST['name_file'] : $_REQUEST['download'];

    header("Content-Type: application/save");
    header("Content-Length:" . filesize($file));
    header('Content-Disposition: attachment; filename="' . $name_file_download . '"');
    header("Content-Transfer-Encoding: binary");
    header('Expires: 0');
    header('Pragma: no-cache');
    $fp = fopen("$file", "r");
    fpassthru($fp);
    fclose($fp);
    exit();
}

include "../../conn.php";
include "../../classes/funcionario.php";
include '../../classes_permissoes/regioes.class.php';
include "../../classes/FormataDadosClass.php";
include "../../wfunction.php";



include "dao/PisLoteClass.php";

$usuario = carregaUsuario();
$optRegiao = getRegioes();
$optRegiao['-1'] = '« Todas as Regiões »';
ksort($optRegiao);



if (isset($_REQUEST['acao']) && $_REQUEST['acao'] == 'gravar_id') {




    $cookie_name = "idscltpis";

    $arr = isset($_COOKIE[$cookie_name]) ? explode(',', $_COOKIE[$cookie_name]) : array();


//    unset($_COOKIE[$cookie_name]);
//    setcookie($cookie_name, null, -1, '/');
//    echo " Array antigo \n";
//    print_r($arr);
//    echo "\n";
//    $cookie_name2 = "user";
//    $cookie_value2 = "John Doe 1";
//    setcookie($cookie_name2, $cookie_value2, time() + (86400 * 30), "/"); // 86400 = 1 day


    if (in_array($_REQUEST['id_clt'], $arr)) {

        foreach ($arr as $k => $v) {
            if ($v == $_REQUEST['id_clt']) {
                unset($arr[$k]);
            }
        }
    } else {
        $arr[$_REQUEST['id_clt']] = $_REQUEST['id_clt'];
    }

//    echo " Array novo \n";
//    print_r($arr);





    $cookie_value = implode(',', array_unique($arr));

//    echo "VAL = \n\n";
//    var_dump($cookie_name);
//    var_dump($cookie_value);






    setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/");

    echo json_encode(array('status' => TRUE));
//    echo "\n\n";
//    echo " CK  \n";
//    var_dump($_COOKIE);
//  
//    exit('fim 3');
    exit();
}


if (isset($_REQUEST['gerar'])) {

    $ids_negados = isset($_COOKIE['idscltpis']) ? explode(',', $_COOKIE['idscltpis']) : array();
    $post_regiao = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : FALSE;
    $post_projeto = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : FALSE;

//    echo '<pre>';
//    print_r($ids_negados);
//    echo '</pre>';

    $dao = new PisLoteClass();


    $arr_relatorio = $dao->getRelacao($post_regiao, $post_projeto, $ids_negados);
    $arr_relatorio = $dao->verificaErro($arr_relatorio);
//    if($_COOKIE['logado'] == 256){
//        print_array($dao->verificaErro($arr_relatorio));
//        echo '<pre>';
//        print_r($arr_relatorio);
//        echo '</pre>';
//    }

    if (isset($_REQUEST['arquivo'])) {
        $arr = $dao->montarArquivoCompleto($arr_relatorio, $_REQUEST['master']);
        echo json_encode($arr);
        exit();
    }
}

$sql = "SELECT nome FROM `master` WHERE id_master = {$usuario['id_master']} AND status=1";
$result = mysql_query($sql);
$master = mysql_fetch_array($result);

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
?>
<html>
    <head>
        <title>:: Intranet :: PIS EM LOTE</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <?php if($_COOKIE['logado'] == 256){ ?>
        <link href="../../resources/css/bootstrap.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css">
        <?php } else { ?>
        <link href="../../net1.css" rel="stylesheet" type="text/css" />    
        <?php } ?>
        
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />

        <link href="../../favicon.ico" rel="shortcut icon" />
        <link href="style.css" rel="stylesheet" type="text/css">
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>

        <style>
            tr.warning{
                background-color: #fcf8e3;
            }
        </style>
        <script>
            $(function () {
                $('#regiao').change(function () {
                    if ($(this).val() <= 0) {
                        $('#projeto').html('<option value="-1">« Selecione »</option>');
                    }
                });
                $('#regiao').ajaxGetJson("../../methods.php", {method: "carregaProjetos"}, null, "projeto");

                $('#gerar_arquivo').click(function () {
                    var projeto = $('#post_projeto').val();
                    var regiao = $('#post_regiao').val();
                    $.post(window.location, {regiao: regiao, projeto: projeto, arquivo: 1, gerar: 1, master: $("#master").val()}, function (data) {
//                       console.log(data);
                        window.location = '?download=' + data.download + '&name_file=' + data.name_file;
                    }, 'json');
                });
            });
        </script>
    </head>
    <body class="novaintra" >        
        <div id="content">
            <form  name="form" action="" method="post" id="form">
                <div id="head">
                    <img src="../../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>PIS EM LOTE</h2>
                    </div>
                </div>
                <br class="clear">
                <br/>

                <ul class="painel-tab">
                    <li><a href="#" class="tab-status ativo">Gerar Pis Em Lote</a></li>
                    <li><a href="indexx.php" class="tab-status">Atualizar Pis em Lote</a></li>
                </ul>

                <fieldset class="noprint">
                    <legend>Relatório</legend>
                    <div class="fleft">
                        <p><label class="first">Master:</label><label><?php echo $master['nome']; ?></label><input type="hidden" id="master" name="master" value="<?= $usuario['id_master']; ?>"></p>
                        <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
                        <p><label class="first">Região:</label> <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao')); ?> </p>                        
                        <p><label class="first">Projeto:</label> <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto')); ?> </p>
                    </div>

                    <br class="clear"/>

                    <p class="controls" style="margin-top: 10px;">
                        <input type="submit" name="gerar" value="Gerar" id="gerar"/>
                    </p>
                </fieldset>

                <?php
                if (isset($_POST['gerar'])) {
                    if (!empty($arr_relatorio)) {
                        $arr_negados = array();
//                    print_r($arr_relatorio);
                        ?>
                        <p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" value="Exportar para Excel" class="exportarExcel"></p>    
                        <table id="tbRelatorio" class="grid table table-hover table-bordered" style="page-break-after:auto;"> 
                            <thead>
                                <tr>
                                    <th colspan="8"><?php echo $projeto['nome'] ?></th>
                                </tr>
                                <tr>
                                    <th>ID</th>
                                    <th>NOME</th>
                                    <th>PROJETO</th>
                                    <th>PIS INVÁLIDO</th>
                                    <th>FUNÇÃO</th>
                                    <th>STATUS</th>
                                    <th>DATA DE ADMISSÃO</th>   
                                    <th>EXCLUIR DA LISTAGEM</th>   
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($arr_relatorio as $row_rel) {
                                    $class = ($cont++ % 2 == 0) ? "even" : "odd";
                                    $warning = ($row_rel['status'])?'':'warning';
                                    if ($row_rel['flag'] == 1) {
                                        $arr_negados[] = $row_rel;
                                    } else {
                                        ?>
                                        <tr class="<?php echo $class ?> <?= $warning ?>">
                                            <td><?php echo $row_rel['id_clt'] ?></td>
                                            <td><?php echo $row_rel['nome'] ?></td>
                                            <td><?php echo $row_rel['unidade'] ?></td>
                                            <td class="center"><?php echo $row_rel['pis'] ?></td>
                                            <td> <?php echo $row_rel['funcao']; ?></td>
                                            <td> <?php echo $row_rel['especifica']; ?></td>
                                            <td align="center"><?php echo $row_rel['data_entrada_br']; ?></td>                       
                                            <td align="center">
                                                <a href="javascript:;" onclick="if (confirm('Dejesa realmente deletar esse pedido')) {
                                                            change(<?= $row_rel['id_clt'] ?>)
                                                        } else {
                                                            return false;
                                                        }">
                                                    <img  src="../../financeiro/imagensfinanceiro/Delete-32.png" alt="Deletar" border="0" title="Excluir">
                                                </a>
                                            </td>                       
                                        </tr>                                
                                    <?php }
                                }
                                ?>
                            </tbody>
                        </table>
                        <br>
                        <a href="javascript:;" onclick="$('#tbRelatorioNegado').toggle();" style="text-align: right; display: block;">
                            <?php if (count($arr_negados) > 0) {
                                echo count($arr_negados);
                                ?> registro(s) fora da listagem. clique para exibir / ocultar.</a><br>
                            <table id="tbRelatorioNegado" border="0" cellpadding="0" cellspacing="0" class="grid" width="100%" style="page-break-after:auto; display: none;"> 
                                <thead>
                                    <tr>
                                        <th colspan="8">Exclusos</th>
                                    </tr>
                                    <tr>
                                        <th>ID</th>
                                        <th>NOME</th>
                                        <th>PROJETO</th>
                                        <th>PIS INVÁLIDO</th>
                                        <th>FUNÇÃO</th>
                                        <th>STATUS</th>
                                        <th>DATA DE ADMISSÃO</th>   
                                        <th>INCLUIR NA LISTAGEM</th>   
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($arr_negados as $row_rel) {
                                        $class = ($cont++ % 2 == 0) ? "even" : "odd";
                                        ?>
                                        <tr class="<?php echo $class ?>">
                                            <td><?php echo $row_rel['id_clt'] ?></td>
                                            <td><?php echo $row_rel['nome'] ?></td>
                                            <td><?php echo $row_rel['unidade'] ?></td>
                                            <td class="center"><?php echo $row_rel['pis'] ?></td>
                                            <td> <?php echo $row_rel['funcao']; ?></td>
                                            <td> <?php echo $row_rel['especifica']; ?></td>
                                            <td align="center"><?php echo $row_rel['data_entrada_br']; ?></td>                       
                                            <td align="center">
                                                <a href="javascript:;" onclick="change(<?= $row_rel['id_clt'] ?>);">
                                                    <img  src="../../imagens/icones/icon-filego.gif" alt="Incluir" border="0" title="Incluir">
                                                </a>
                                            </td>                       
                                        </tr>                                
                            <?php } ?>
                                </tbody>
                            </table>
        <?php } ?>

                        <div style="text-align: right; margin-top: 20px;">
                            <input type="hidden" name="post_regiao" id="post_regiao" value="<?= $post_regiao; ?>">
                            <input type="hidden" name="post_regiao" id="post_projeto" value="<?= $post_projeto; ?>">
                            <input type="button" name="gerar_arquivo" value="Gerar Arquivo" id="gerar_arquivo">
                        </div>

    <?php } else { ?>
                        <br>
                        <div class="message-box message-yellow">
                            <p>0 registros encontrados.</p>
                        </div>
                    <?php } ?>


<?php } ?>
            </form>
        </div>
    </body>
</html>
<script>
    function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        var expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + "; " + expires;
    }
    function getCookie(cname) {
        var name = cname + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ')
                c = c.substring(1);
            if (c.indexOf(name) == 0)
                return c.substring(name.length, c.length);
        }
        return "";
    }

    function change(id) {

        $.post(window.location, {'id_clt': id, 'acao': 'gravar_id'}, function (data) {
            if (data.status == 1) {
                location.reload();
            }
        }, 'json');



    }


</script>
