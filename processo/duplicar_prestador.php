<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}
error_reporting(E_ALL);
include "../conn.php";
include "../funcoes.php";
include "../wfunction.php";

$usuario = carregaUsuario();
$master = $usuario['id_master'];
$regiao = $usuario['id_regiao'];

//CARREGANDO INFORMAÇÕES DO PRESTADOR
$qr = "SELECT A.*,B.nome as projeto,DATE_FORMAT(contratado_em, '%d/%m/%Y') AS contratado_embr,DATE_FORMAT(encerrado_em, '%d/%m/%Y') AS encerrado_embr FROM prestadorservico AS A
        LEFT JOIN projeto AS B ON (A.id_projeto = B.id_projeto) WHERE id_prestador = {$_REQUEST['prestador']}";
$rowPrestador = current(execQuery($qr));

if(validate($_REQUEST['duplicar'])){
    //validar CNPJ enviado com CNPJ do resultado da query acima
    $reqCnpj = $_REQUEST['cnpjprest'];
    echo "<pre>";
    //MESCLANDO INFORMAÇÕES DO PRESTADOR CADASTRADO, COM AS INFROMAÇÕES ENVIADAS PARA DUPLICAR
    if($rowPrestador['c_cnpj'] === $reqCnpj){
        $projetosSel = $_REQUEST['projetosel'];
        $matriz = array();
        
        //REMOVENDO CAMPOS Q NÃO SÃO INSERIDOS
        unset($rowPrestador['id_prestador']);
        unset($rowPrestador['projeto']);
        unset($rowPrestador['contratado_embr']);
        unset($rowPrestador['encerrado_embr']);
        
        $count=1;
        foreach($projetosSel as $pro){
            $matriz[$count] = $rowPrestador;
            $matriz[$count]['id_projeto'] = $pro;
            $matriz[$count]['numero'] = $_REQUEST['numero'][$pro];
            $matriz[$count]['contratado_em'] = date("Y-m-d",strtotime(str_replace("/", "-", $_REQUEST['vigenciaIni'][$pro])));
            $matriz[$count]['encerrado_em'] = date("Y-m-d",strtotime(str_replace("/", "-", $_REQUEST['vigenciaFim'][$pro])));
            $count++;
        }
    }
    
    $campos = array_keys($rowPrestador);
    sqlInsert("prestadorservico", $campos, $matriz);
    echo "<p>Dados inseridos com sucesso</p>";
    echo "<p><a href='javascript:;' onclick='history.go(-2)'>Voltar</a></p>";
    exit;
}


//SELECIONANDO TODOS OS PRESTADORES COM O MESMO CNPJ
$qrLista = "SELECT A.*,B.nome as projeto,DATE_FORMAT(contratado_em, '%d/%m/%Y') AS contratado_embr,DATE_FORMAT(encerrado_em, '%d/%m/%Y') AS encerrado_embr FROM prestadorservico AS A
        LEFT JOIN projeto AS B ON (A.id_projeto = B.id_projeto) 
        WHERE A.c_cnpj = '{$rowPrestador['c_cnpj']}' AND A.id_projeto != {$rowPrestador['id_projeto']} AND assunto = {$rowPrestador['asusnto']}";
$resultLista = execQuery($qrLista, false);
$numLista = mysql_num_rows($resultLista);

//CARREGANDO TODOS OS PROJETOS DA REGIAO
$arrProjetos = montaQuery("projeto", "id_projeto,nome", "id_regiao = {$regiao}");
$proSim = array();
?>
<html>
    <head>
        <title>:: Intranet :: DUPLICANDO PRESTADOR</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.10.2.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script src="../js/jquery.maskedinput-1.3.1.js" type="text/javascript"></script>

        <script src="../js/global.js" type="text/javascript"></script>

        <script>
            $(function() {
                $(".data").mask("99/99/9999");
                $("#form1").validationEngine();
                gridZebra(".grid");
                
                $("input[type=checkbox]").click(function(){
                    var sel = $(this).is(":checked");
                    var id = $(this).val();
                    if(sel){
                        $("#dtfim"+id).addClass("validate[required,funcCall[vigencia]]");
                    }else{
                        $("#dtfim"+id).removeClass("validate[required,funcCall[vigencia]]");
                    }
                });
            });

            function vigencia(field, rules, i, options) {
                var id = field.attr('id');
                var key = id.replace(/[^0-9]+/g, "");
                var sel = $("#projeto"+key).is(":checked");
                if(sel){
                    var num = $("#num"+key).val();
                    var dtIni = $("#dtini"+key).val();
                    var dtFim = field.val();
                    if (num === "" || dtIni === "" || dtFim === "") {
                        return "Os 3 campos referentes ao projeto selecionado devem ser preenchidos.";
                    }
                }
                
            }
        </script>
    </head>
    <body class="novaintra">
        <div id="content" style="width: 90%;">
            <form action="" method="post" name="form1" id="form1">
                <div id="head">
                    <img src="../imagens/logomaster<?php echo $master; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>Administração - Duplicando Prestador</h2>
                    </div>
                    <div class="fright"> <?php include('../reportar_erro.php'); ?></div> 
                </div>

                <br/>

                <fieldset>
                    <legend>Dados do Prestador</legend>
                    <input type="hidden" name="prestador" id="prestador" value="<?php echo $rowPrestador['id_prestador'] ?>" />
                    <input type="hidden" name="cnpjprest" id="cnpjprest" value="<?php echo $rowPrestador['c_cnpj'] ?>" />
                    <div class="fleft">
                        <p><label class="first-2">Projeto Atual:</label> <?php echo $rowPrestador['projeto'] ?></p>
                        <p><label class="first-2">Razão Social:</label> <?php echo $rowPrestador['c_razao'] ?></p>
                        <p><label class="first-2">Nome Fantasia:</label> <?php echo $rowPrestador['c_fantasia'] ?></p>
                    </div>
                    <div class="fleft">
                        <p><label class="first-2">Vigência do Contrato:</label> de <span class="red"><?php echo $rowPrestador['contratado_embr'] ?></span> até <span class="red"><?php echo $rowPrestador['encerrado_embr'] ?></span></p>
                        <p><label class="first-2">CNPJ:</label> <?php echo $rowPrestador['c_cnpj'] ?></p>
                        <p><label class="first-2">Num Processo:</label> <?php echo $rowPrestador['numero'] ?></p>
                    </div>

                    <p class="clear"><label class="first-2">Assunto:</label> <?php echo $rowPrestador['assunto'] ?></p>
                    <p><label class="first-2">Objeto:</label> <?php echo $rowPrestador['objeto'] ?></p>
                    <p><label class="first-2">Especificação:</label> <?php echo $rowPrestador['especificacao'] ?></p>
                </fieldset>

                <fieldset style="padding: 10px;">
                    <legend>Projetos</legend>
                    <?php if ($numLista > 0) { ?>
                        <p>Lista do prestador em outros projetos</p>

                        <table class="grid" border="0" cellpadding="0" cellspacing="0" width="90%" align="center">
                            <thead>
                                <tr>
                                    <th>Projeto</th>
                                    <th>Razão Social</th>
                                    <th>CNPJ</th>
                                    <th>Num Processo</th>
                                    <th>Vigência</th>
                                    <th>Editar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($row = mysql_fetch_assoc($resultLista)) {
                                    $proSim[$row['id_projeto']] = $row['id_projeto'];
                                    ?>
                                    <tr>
                                        <td><?php echo $row['projeto'] ?></td>
                                        <td><?php echo $row['c_razao'] ?></td>
                                        <td><?php echo $row['c_cnpj'] ?></td>
                                        <td><?php echo $row['numero'] ?></td>
                                        <td><?php echo $row['contratado_embr'] ?> até <?php echo $row['encerrado_embr'] ?> </td>
                                        <td class="center"><a href='editar_prestador.php?prestador=<?php echo $row['id_prestador'] ?>'> <img src="../imagens/icon-edit.gif" alt="Editar" title="Editar" /> </a> </td>
                                    </tr>
                                    <?php } ?>
                            </tbody>
                        </table>
                    <?php } ?>

                    <?php if (count($arrProjetos) <> count($proSim)) { ?>
                        <br/><br/>
                        <table class="grid" border="0" cellpadding="0" cellspacing="0" width="90%" align="center">
                            <thead>
                                <tr>
                                    <th>Duplicar</th>
                                    <th>Projeto</th>
                                    <th>Razão Social</th>
                                    <th>CNPJ</th>
                                    <th>Num Processo</th>
                                    <th>Vigência</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($arrProjetos as $pro) {
                                    if (empty($proSim[$pro['id_projeto']]) && $pro['id_projeto'] != $rowPrestador['id_projeto']) {
                                        echo "<tr>";
                                        echo "<td class='center'><input type='checkbox' name='projetosel[]' id='projeto{$pro['id_projeto']}' value='{$pro['id_projeto']}' class='validate[required]'></td>";
                                        echo "<td>{$pro['nome']}</td>";
                                        echo "<td>{$rowPrestador['c_razao']}</td>";
                                        echo "<td>{$rowPrestador['c_cnpj']}</td>";
                                        echo "<td class='center'><input type='text' id='num{$pro['id_projeto']}' name='numero[{$pro['id_projeto']}]' value='' data-key='{$pro['id_projeto']}' size='5'/></td>";
                                        echo "<td class='center'><input type='text' id='dtini{$pro['id_projeto']}' name='vigenciaIni[{$pro['id_projeto']}]' value='' class='data' size='8'/> até <input type='text' name='vigenciaFim[{$pro['id_projeto']}]' id='dtfim{$pro['id_projeto']}' value='' class='data' size='8'/></td>";
                                        echo "</tr>";
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                        <p class="controls"> <input type="submit" name="duplicar" id="duplicar" value="Duplicar" /> </p>
                    <?php } ?>
                </fieldset>
            </form>
        </div>
    </body>
</html>