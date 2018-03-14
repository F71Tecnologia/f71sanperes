<?php
session_start();
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../wfunction.php');
include('./class.php');

$get_clt = $_GET['clt'];

$usuario = carregaUsuario();
$row = getGeral($get_clt);

$valida_unidade = false;
$valida_funcionario = false;

//valida funcionario
if(($row['endereco_clt'] != '') && ($row['cidade_clt'] != '') && ($row['uf_clt'] != '')){
    $valida_funcionario = true;
    $partida = acentoMaiusculo(removeGeral($row['endereco_clt']) . ", {$row['bairro_clt']}, {$row['cidade_clt']} - {$row['uf_clt']}");
}

// valida unidade
if(($row['endereco_uni'] != '') && ($row['cidade_uni'] != '') && ($row['uf_uni'] != '')){
    $valida_unidade = true;
    $destino = acentoMaiusculo(removeGeral($row['endereco_uni']) . ", {$row['bairro_uni']}, {$row['cidade_uni']} - {$row['uf_uni']}");
}

//echo "{$partida}<br />";
//echo "{$destino}<br />";

//trata foto do funcionário
if ($row['foto_clt'] == '1') {
    $nome_imagem = $row['id_regiao'] . '_' . $row['id_projeto'] . '_' . $row['id_clt'] . '.gif';
} else {
    $nome_imagem = '../imagens/semFoto.png';
}

if($row['num_clt'] != ''){
    $num_clt = ", {$row['num_clt']}";    
}
if($row['complem_clt'] != ''){
    $complemento_clt = ", {$row['complem_clt']}";
}

?>

<!DOCTYPE html>
<html>
    <head>
        <title>:: Intranet :: Mapa de Deslocamento Funcional</title>
        <meta charset="iso-8859-1" />
        <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="css/estilo.css" rel="stylesheet" type="text/css" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>                            
    </head>
    
    <body class="novaintra">
        <div id="content2">
            <form action="" method="post" name="form1" id="form1" enctype="multipart/form-data">
                
                <?php if($get_clt == ''){ ?>
                
                <br /><br />
                <div id='message-box' class='message-red'>
                    <p>Sem resultados, pois nenhum CLT foi encontrado. Vá em Visualizar Participantes e selecione o Funcionário desejado. <br /> Clique <a href="../../">aqui</a> para ir para Página Inicial</p>                    
                </div>
                    
                <?php }elseif($valida_funcionario && $valida_unidade){ ?>
                
                <div id="head">
                    <img src="../../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;">
                    <div class="fleft">
                        <h2>Mapa de Deslocamento Funcional</h2>
                        <h3><?php echo "{$row["razao_empresa"]} - CNPJ: {$row["cnpj_empresa"]}"; ?></h3>
                    </div>
                    
                    <div class="foto_fun">
                        <img src="../../fotosclt/<?php echo $nome_imagem; ?>" />
                    </div>
                </div>
                
                <div class="clear"></div>
                
                <div id="centro">
                
                    <div id="colE">
                        <div id="quadro_info">
                            <div id="quadro_a">
                                <div id="titulo_mapa1">Funcionário - <?php echo $row["nome_clt"]; ?></div>
                                <div class="dados_mapa">
                                    <fieldset id="field_unidade">                                
                                        <p>
                                            <label class="first_a">Endereço:</label>
                                            <span class="cont_ende">
                                                <?php echo $row["endereco_clt"].$num_clt.$complemento_clt.", ".$row["bairro_clt"].", ".$row["cidade_clt"]." - ".$row["uf_clt"].", CEP: ".$row["cep_clt"]; ?>
                                                <a href="../alter_clt.php?clt=<?php echo $row['id_clt']; ?>&pro=<?php echo $row['id_projeto']; ?>&pagina=/intranet/rh/ver_clt.php" target="_blank">Editar</a>
                                            </span>
                                        </p>
                                    </fieldset>
                                </div>
                            </div><!--quadro_a-->

                            <div id="quadro_b">
                                <div id="titulo_mapa2">Unidade - <?php echo $row["nome_uni"]; ?></div>
                                <div class="dados_mapa">
                                    <fieldset id="field_unidade2">
                                        <p>
                                            <label class="first_a">Endereço:</label>
                                            <span class="cont_ende">
                                                <?php echo $row["endereco_uni"].", ".$row["bairro_uni"].", ".$row["cidade_uni"]." - ".$row["uf_uni"].", CEP: ".$row["cep_uni"]; ?>
                                                <a href="../../adm/adm_unidade/form_unidade.php" target="_blank">Editar</a>
                                                <?php $_SESSION['unidade'] = $row['id_uni']; ?>
                                            </span>
                                        </p>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="clear"></div>
                        </div><!--quadro_info-->

                        <input type="hidden" id="partida" value="<?php echo $partida; ?>" />
                        <input type="hidden" id="destino" value="<?php echo $destino; ?>" />                                                            

                        <div id='message-box' class='message-red rota'>
                            <p>Rota de Transporte Público não encontrada, <br />deseja visualizar a rota de Carro? Clique <a href="javascript:;" id="aqui">aqui</a></p>
                        </div>
                        
                        <div id='message-box' class='message-red rota2'>
                            <p>Rota não encontrada</p>
                        </div>
                        
                        <div id="dados_mapa">
                            <div id="mapa">
                                <script>
                                    $(document).ready(function(){
                                        var partida = $("#partida").val();
                                        var destino = $("#destino").val();
                                        getRota(partida, destino);
                                        
                                        $("#aqui").click(function(){                                            
                                            getRota(partida, destino,"D");
                                            $(".rota").hide();
                                            $("#dados_mapa").show();
                                        });
                                    });
                                </script>
                            </div>
                        </div><!--dados_mapa-->
                    </div><!--colE-->

                    <div id="geo_mapa">
                        <div id="titulo_mapa3">Dados do percurso</div>                        
                        <div id="trajeto"></div>
                    </div>
                    
                </div><!--centro-->
                
                <div class="clear"></div>
                
                <?php }elseif(!$valida_funcionario){ ?>
                <br /><br />
                    <div id='message-box' class='message-red'>
                        <p>O mapa não pode ser exibido pois falta alguma informação relacionada ao endereço do Funcionário, Clique <a href="../alter_clt.php?clt=<?php echo $row['id_clt']; ?>&pro=<?php echo $row['id_projeto']; ?>&pagina=/intranet/rh/ver_clt.php" target="_blank">aqui</a> para editar</p>
                        <?php $_SESSION['clt'] = $row['id_clt']; ?>
                    </div>
                
                <?php }elseif(!$valida_unidade){ ?>
                <br /><br />
                    <div id='message-box' class='message-red'>
                        <p>O mapa não pode ser exibido pois falta alguma informação relacionada ao endereço da Unidade, Clique <a href="../../adm/adm_unidade/form_unidade.php" target="_blank">aqui</a> para editar</p>
                        <?php $_SESSION['unidade'] = $row['id_uni']; ?>
                    </div>
                <?php } ?>
                
            </form>
        </div>
        <script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyAl3O3sodwtm6xisPvh6EM0PrTlqPZ7M_s&sensor=false"></script>
        <script src="js/mapa.js"></script>
    </body>
</html>