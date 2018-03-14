<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../conn.php');
include('../wfunction.php');
//include('../classes/global.php');

$usuario = carregaUsuario();

$id_funcionario = $_REQUEST['funcionario'];

//CONSULTA SE O FUNCIONARIO JÁ POSSUI ALGUMA PERMISSÃO
$func_reg_assoc = montaQuery("funcionario_regiao_assoc", "id_regiao, id_master", "id_funcionario = $id_funcionario");
$btn_assoc = montaQuery("botoes_assoc", "botoes_id", "id_funcionario = $id_funcionario");
$acoes_assoc = montaQuery("funcionario_acoes_assoc", "acoes_id", "id_funcionario = $id_funcionario", null, null, "array", null, "acoes_id");
$acoes_assoc1 = montaQuery("funcionario_acoes_assoc", "acoes_id, botoes_id", "id_funcionario = $id_funcionario", null, null, "array", null, "acoes_id");

// CONSULTA AS REGIOES ATIVAS E INATIVAS
$array_status = array(0 => 'REGIÕES INATIVAS', 1 => 'REGIÕES ATIVAS');
$matrizRegioes = array();
foreach ($array_status AS $status => $TipoRegiao) {
    $qrRegioes = mysql_query("SELECT A.id_regiao, A.id_master, A.regiao, B.nome
                            FROM regioes AS A
                            LEFT JOIN master AS B ON (A.id_master = B.id_master)
                            WHERE A.`status` = '$status' AND A.status_reg = '$status'
                            ORDER BY A.id_master ASC;");
    if ($status == 0) {
        while ($rowRegiaoInativas = mysql_fetch_assoc($qrRegioes)) {
            $matrizRegioes["Inativas"][$rowRegiaoInativas['id_master']][$rowRegiaoInativas['nome']][$rowRegiaoInativas['id_regiao']] = $rowRegiaoInativas['regiao'];
        }
    } else {
        while ($rowRegiaoAtivas = mysql_fetch_assoc($qrRegioes)) {
            $matrizRegioes["Ativas"][$rowRegiaoAtivas['id_master']][$rowRegiaoAtivas['nome']][$rowRegiaoAtivas['id_regiao']] = $rowRegiaoAtivas['regiao'];
        }
    }
}

// CONSULTA MENU PRINCIPAL DAS PAGINAS
$array_botoes_pagina = array();
$qr_botoes_pagina = mysql_query("SELECT botoes_pg_id, botoes_pg_nome FROM botoes_pagina");
while ($rowBotoesPagina = mysql_fetch_assoc($qr_botoes_pagina)) {
    $array_botoes_pagina [$rowBotoesPagina['botoes_pg_id']] = $rowBotoesPagina['botoes_pg_nome'];
}


// CONSULTA SUBMENU E ACOES
foreach ($array_botoes_pagina AS $status => $tipoRegiao) {
    $qrBotoes = mysql_query("SELECT A.botoes_menu_id, A.botoes_menu_nome, B.botoes_id, B.botoes_nome, C.acoes_id, C.acoes_nome
                        FROM botoes_menu AS A
                        LEFT JOIN botoes AS B ON (B.`botoes_menu` = A.botoes_menu_id)
                        LEFT JOIN acoes AS C ON (C.botoes_id = B.botoes_id)
                        WHERE A.`botoes_pagina` = $status");

    while ($rowBotoes = mysql_fetch_assoc($qrBotoes)) {
        $matrizBotoes[$tipoRegiao][$rowBotoes['botoes_menu_id']][$rowBotoes['botoes_menu_nome']][$rowBotoes['botoes_id']][$rowBotoes['botoes_nome']][$rowBotoes['acoes_id']] = $rowBotoes['acoes_nome'];
    }

    $qrBotoes0 = mysql_query("SELECT A.botoes_menu_id, A.botoes_menu_nome,C.acoes_id, C.acoes_nome
                            FROM botoes_menu AS A
                            LEFT JOIN acoes AS C ON (C.botoes_pagina_id = A.botoes_pagina)
                            WHERE A.`botoes_pagina` =  $status
                            GROUP BY acoes_id;");
    while ($rowBotoes = mysql_fetch_assoc($qrBotoes0)) {
        $matrizBotoes[$tipoRegiao][$rowBotoes['botoes_menu_id']][$rowBotoes['botoes_menu_nome']][$rowBotoes['botoes_id']][$rowBotoes['botoes_nome']][$rowBotoes['acoes_id']] = $rowBotoes['acoes_nome'];
    }
}


// CADASTRO DE PERMISSÕES
if (isset($_REQUEST['cadastrar'])) {
    $id_funcionario = $_REQUEST['funcionario'];
    $arrayRegiao = $_REQUEST['regioesPermitidas'];
    $arrayRegiao33 = $_REQUEST['regioesPermitidas33'];
    $arrayRegiao60 = $_REQUEST['regioesPermitidas60'];
    $arrayAcoes = $_REQUEST['acoes'];
    $arrayBotoes = $_REQUEST['botoes'];

// CADASTRA AS REGIOES PERMITIDAS
    if (!empty($arrayRegiao)) {
        foreach ($arrayRegiao as $id_master => $regioes) {
            foreach ($regioes as $id_regiao) {
                $qr_regiao = mysql_query("SELECT id_regiao FROM funcionario_regiao_assoc WHERE id_regiao = '$id_regiao'  AND id_funcionario = '$id_funcionario'");
                if (mysql_num_rows($qr_regiao) == 0) {
                    mysql_query("INSERT INTO funcionario_regiao_assoc (id_funcionario, id_regiao, id_master) VALUES ('$id_funcionario', '$id_regiao','$id_master' );") or die(mysql_error());
                }
            }
        }
    }

// CADASTRA OS BOTOES PERMITIDOS
    if (!empty($arrayBotoes)) {
        foreach ($arrayBotoes as $idBotao) {
            foreach ($idBotao as $id) {
                $qr_botoes_assoc = mysql_query("SELECT botoes_assoc_id FROM botoes_assoc WHERE botoes_id = '$id'  AND id_funcionario = '$id_funcionario'");
                if (mysql_num_rows($qr_botoes_assoc) == 0) {
                    mysql_query("INSERT INTO botoes_assoc (botoes_id, id_funcionario) VALUES ('$id', '$id_funcionario');");
                }
            }
        }
    }

// CADASTRA AS ACOES PERMITIDAS
    if (!empty($arrayAcoes)) {
        $regioes_permitidas = montaQuery("funcionario_regiao_assoc", "id_regiao", "id_funcionario = '$id_funcionario'");
        foreach ($regioes_permitidas as $valueRegioes) {
            foreach ($arrayAcoes as $id_botao => $acoes) {
                if ($id_botao != 33 && $id_botao != 60) {
                    foreach ($acoes as $idAcao) {
                        $qr_acoes_assoc = mysql_query("SELECT acoes_id, id_regiao FROM funcionario_acoes_assoc WHERE acoes_id = '$idAcao' AND id_regiao = {$valueRegioes['id_regiao']} AND id_funcionario = '$id_funcionario'");
                        if (mysql_num_rows($qr_acoes_assoc) == 0) {
                            mysql_query("INSERT INTO funcionario_acoes_assoc (id_funcionario, acoes_id, id_regiao, botoes_id ) VALUES('$id_funcionario', '$idAcao','{$valueRegioes['id_regiao']}','$id_botao');");
                        }
                    }
                } else {
                    switch ($id_botao) {
                        case 33:
                            $regioesEspecificas = $arrayRegiao33;
                            break;
                        case 60:
                            $regioesEspecificas = $arrayRegiao60;
                            break;
                    }
                    foreach ($regioesEspecificas as $kMaster => $vRegioes) {
                        foreach ($vRegioes as $reg) {
                            foreach ($acoes as $idAcao) {
                                $qr_acoes_assoc = mysql_query("SELECT acoes_id, id_regiao FROM funcionario_acoes_assoc WHERE acoes_id = '$idAcao' AND id_regiao = $reg AND id_funcionario = '$id_funcionario'");
                                if (mysql_num_rows($qr_acoes_assoc) == 0) {
                                    mysql_query("INSERT INTO funcionario_acoes_assoc (id_funcionario, acoes_id, id_regiao, botoes_id ) VALUES('$id_funcionario', '$idAcao','$reg','$id_botao');");
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    $func_reg = montaQuery("funcionario_regiao_assoc", "id_regiao", "id_funcionario = $id_funcionario");
    //REMOVE PERMISSAO DA REGIAO
    if (empty($arrayRegiao)) {
        mysql_query("DELETE FROM funcionario_regiao_assoc WHERE id_funcionario = $id_funcionario;"); // remove as permissoes de todas as regioes
    } else {
        foreach ($func_reg as $reg_permitidas => $id_reg) {
            $aux = 0;
            foreach ($arrayRegiao as $reg_novas => $id_reg_novas) {

                $result = array_diff($id_reg, $id_reg_novas); // só remove as regioes que foram desmarcadas

                if ($result) {
                    $aux++;
                }
                if ($aux == count($arrayRegiao)) {
                    mysql_query("DELETE FROM funcionario_regiao_assoc WHERE id_funcionario = $id_funcionario AND id_regiao = {$result['id_regiao']} LIMIT 1;");
                    echo'<br>';
                }
            }
        }
    }

    //REMOVE PERMISSAO DO BOTAO
    if (empty($arrayBotoes)) {
        mysql_query("DELETE FROM botoes_assoc WHERE id_funcionario = $id_funcionario"); // remove todas as permições dos botoes do usuário
    } else {
        foreach ($btn_assoc as $btn_permitidos => $id_btn) {
            $aux = 0;
            foreach ($arrayBotoes as $btn_novos => $id_btn_novos) {
                $result = array_diff($id_btn, $id_btn_novos); // só remove od botes que foram desmarcados
                if ($result) {
                    $aux++;
                }
                if ($aux == count($arrayBotoes)) {
                    mysql_query("DELETE FROM botoes_assoc WHERE id_funcionario = $id_funcionario AND botoes_id = {$result['botoes_id']} LIMIT 1;");
                }
            }
        }
    }


    //REMOVE PERMISSÃO DA ACAO 
    if (empty($arrayAcoes)) {
        mysql_query("DELETE FROM funcionario_acoes_assoc WHERE id_funcionario = $id_funcionario"); // remove todas as permições das ações do usuário
    } else {
        foreach ($acoes_assoc as $acoes_permitidas => $id_acoes) {
            $cont = 0;
            $tamanho_matriz = count($arrayAcoes);
            foreach ($arrayAcoes as $acoes_novas => $id_acoes_novas) {
                $result = array_diff($id_acoes, $id_acoes_novas); // só remove as ações que foram desmarcadas
                if (!empty($result)) {
                    $cont++;
                    if (!empty($result) && $cont == $tamanho_matriz) {
                        mysql_query("DELETE FROM funcionario_acoes_assoc WHERE id_funcionario = $id_funcionario AND acoes_id = {$result['acoes_id']}");
                    }
                }
            }
        }
    }

    header("Location: ../funcionario/");
}
?>
<html>
    <head>
        <title>:: Intranet :: Gestor de Funcionários</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link href="../favicon.ico" rel="shortcut icon" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script>
            $(function() {

            $('input[type="checkbox"]:checked').each(function(key, value) {
            var tipo = $(this).attr('data_tipo');
            var menu = $(this).attr('data_menu');
            $('.pai_' + tipo).attr('checked', 'checked');
            $('[class=marcaTudo][data_menu =' + menu + ']').attr('checked', 'checked');
            });


            $('.principal').click(function() {
            var menu = $(this).attr('data_tipo');
            $('[class^=conteudo_' + menu + ']').fadeToggle("slow", "linear");
            });

            $('.marcaTudo').change(function() {
            var menu = $(this).attr('data_menu');
            if (this.checked === true) {
            $('[class^=pai_][data_menu=' + menu + ']').attr('checked', 'checked');
            $('[class^=filho_][data_menu=' + menu + ']').attr('checked', 'checked');
            } else {
            $('[class^=filho_][data_menu=' + menu + ']').attr('checked', false);
            $('[class^=pai_][data_menu=' + menu + ']').attr('checked', false);
            }
            });

            $('[class^=pai_]').change(function() {
            var tipo = $(this).attr('data_tipo');

            if (this.checked === true) {
            $('.filho_' + tipo).attr('checked', 'checked');
            } else {
            $('.filho_' + tipo).attr('checked', false);
            }
            });


            $('[class^=filho_]').change(function() {
            var tipo = $(this).attr('data_tipo');
            if (this.checked === true) {
            $('.pai_' + tipo).attr('checked', 'checked');
            }
            });


            });
        </script>
        <style>
            .col{
                float: left;
                width: 50%;
                min-height: 100%;
                border-right: none;
            }

            .col fieldset {
                margin-top: 10px;
                margin-right: 10px;
                min-height: 897px;
            }

            fieldset{
                margin-top: 10px;
                margin-right: 10px;
            }

            fieldset legend{
                font-family: 'Exo 2', sans-serif;
                font-size: 16px!important;
                font-weight: bold;
            }

            li{
                list-style-type:none;
            }

            .link_titulo{
                cursor: pointer;
            }

            .link_titulo:hover{
                color: #999;
            }
        </style>
    </head>
    <body class="novaintra">
        <div id="content" style="width: 850px;">
            <form action="" method="post" name="form1" id="form1" enctype="multipart/form-data" >
                <div id="head">
                    <img src="../imagens/logomaster<?php echo $usuario['id_master'] ?>.gif" class="fleft" style="margin-right: 25px;">
                    <div class="fleft">
                        <h2>Gerenciamento de Acesso a Intranet</h2>
                    </div>
                </div>     

                <input type="hidden" name="funcionario" value="<?php echo $id_funcionario ?>" />

                <?php
                $permissaoRegiao = "REGIÕES";
                echo '<div class="principal" data_tipo = "' . RemoveAcentos($permissaoRegiao) . '" style="display: block"><a><h3 class="link_titulo">' . $permissaoRegiao . '</h3></a></div>';
                echo '<div class="conteudo_' . RemoveAcentos($permissaoRegiao) . '" style="display: none">';
                $coluna = "col";
                foreach ($matrizRegioes as $keyTipo => $matrizIdMaster) {
                    echo '<div class="' . $coluna . '">';
                    echo '<fieldset>
                                         <legend> Regiões ' . $keyTipo . '</legend>';
                    foreach ($matrizIdMaster as $idMaster => $matrizMaster) {
                        foreach ($matrizMaster as $nomeMaster => $regiao) {
                            echo'<ul>
                                                <li>';
                            echo '<input type="checkbox" name="masterPermitidos[]" value="' . $idMaster . '" class="pai_' . $idMaster . '_' . $keyTipo . '" data_tipo = "' . $idMaster . '_' . $keyTipo . '" /><label>' . $idMaster . '-' . $nomeMaster . '</label>';
                            foreach ($regiao as $idRegiao => $nomeRegiao) {
                                foreach ($func_reg_assoc as $arrayReg_assoc) {
                                    $marcador = '';
                                    if ($arrayReg_assoc["id_regiao"] == $idRegiao && $arrayReg_assoc["id_master"] == $idMaster) {
                                        $marcador = 'checked="true"';
                                        break;
                                    }
                                }
                                echo '<ul>
                                                        <li>';
                                echo '<input type="checkbox" name="regioesPermitidas[' . $idMaster . '][]" value="' . $idRegiao . '" class="filho_' . $idMaster . '_' . $keyTipo . '" data_tipo = "' . $idMaster . '_' . $keyTipo . '"' . $marcador . '/><label>' . $idRegiao . '-' . $nomeRegiao . '</label>';
                                echo '</li>
                                                    </ul>';
                            }
                            echo '</li>
                                          </ul>';
                        }
                    }
                    echo '</fieldset>';
                    echo '</div>';
                    $coluna = "";
                }
                echo '</div>';


                foreach ($matrizBotoes as $menuPrincipal => $arrayMenuPrincipal) {

                    echo '<div class="principal" data_tipo = "' . str_replace(' ', '', RemoveCaracteres(RemoveAcentos($menuPrincipal))) . '" style="display: block; width: 225px;"><a><h3 class="link_titulo">' . acentoMaiusculo($menuPrincipal) . '</h3></a></div>';
                    echo '<div class="conteudo_' . str_replace(' ', '', RemoveCaracteres(RemoveAcentos($menuPrincipal))) . '" style="display: none">';

                    echo '<fieldset>';
                    foreach ($arrayMenuPrincipal as $idMenu => $arrayMenu) {
                        foreach ($arrayMenu as $Menu => $arraySubMenu) {
                            echo '<input type="checkbox" name = "menu" class="marcaTudo" data_menu = "' . $idMenu . '"/><label>' . $Menu . '</label>';
                            foreach ($arraySubMenu as $btnSubId => $arrayBtn) {
                                foreach ($arrayBtn as $SubMenu => $arrayAcoes) {
                                    echo'<ul>
                                                <li>';
                                    if (!empty($btnSubId)) {
                                        foreach ($btn_assoc as $arrayBtn_assoc) {
                                            $marcador = '';
                                            if ($arrayBtn_assoc['botoes_id'] == $btnSubId) {
                                                $marcador = 'checked="true"';
                                                break;
                                            }
                                        }
                                        echo '<input type="checkbox" name="botoes[' . $idMenu . '][]" value="' . $btnSubId . '" class="pai_' . $btnSubId . '" data_tipo = "' . $btnSubId . '" data_menu = "' . $idMenu . '"' . $marcador . ' /><label>' . $btnSubId . '-' . $SubMenu . '</label>';

                                        foreach ($arrayAcoes as $idAcao => $acao) {
//                                        print_r($arrayAcoes);

                                            if (!empty($idAcao)) {
                                                foreach ($acoes_assoc1 as $valueAcoes) {
                                                    $marcador = '';
                                                    if ($valueAcoes['acoes_id'] == $idAcao && $valueAcoes['botoes_id'] == $btnSubId) {

                                                        $marcador = 'checked="true"';
                                                        break;
                                                    }
                                                }
                                                echo'<ul>
                                                        <li>';
                                                echo '<input type="checkbox" name="acoes[' . $btnSubId . '][]" value="' . $idAcao . '" class="filho_' . $btnSubId . '" data_tipo = "' . $btnSubId . '" data_menu = "' . $idMenu . '"' . $marcador . '/><label>' . $idAcao . '-' . $acao . '</label>';
                                            }
                                            echo '</li>
                                              </ul>';
                                        }
                                        if ($btnSubId == 60 or $btnSubId == 33) {
                                            foreach ($matrizRegioes as $keyTipo => $matrizIdMaster) {
                                                echo '<div>';
                                                echo '<fieldset>
                                                                 <legend> Regiões ' . $keyTipo . '</legend>';
                                                foreach ($matrizIdMaster as $idMaster => $matrizMaster) {
                                                    foreach ($matrizMaster as $nomeMaster => $regiao) {
                                                        echo'<ul>
                                                                        <li>';
                                                        echo '<input type="checkbox" name="masterPermitidos' . $btnSubId . '[]" value="' . $idMaster . '" class="pai_' . $idMaster . '_' . $keyTipo . '_' . $btnSubId . '" data_tipo = "' . $idMaster . '_' . $keyTipo . '_' . $btnSubId . '" /><label>' . $idMaster . '-' . $nomeMaster . '</label>';
                                                        foreach ($regiao as $idRegiao => $nomeRegiao) {
                                                            $reg_btn = mysql_query("SELECT A.id_regiao ,B.id_master
                                                        FROM funcionario_acoes_assoc AS A
                                                        INNER JOIN  funcionario_regiao_assoc B ON (A.id_regiao = B.id_regiao AND A.id_funcionario = B.id_funcionario)
                                                        WHERE A.id_funcionario = $id_funcionario AND A.botoes_id = $btnSubId GROUP BY id_regiao ORDER BY id_master;");
                                                            while ($row = mysql_fetch_assoc($reg_btn)) {

                                                                $marcador = '';
                                                                if ($row['id_regiao'] == $idRegiao && $row['id_master'] == $idMaster) {
                                                                    $marcador = 'checked="true"';
                                                                    break;
                                                                }
                                                            }
                                                            echo '<ul>
                                                                                    <li>';
                                                            echo '<input type="checkbox" name="regioesPermitidas' . $btnSubId . '[' . $idMaster . '][]" value="' . $idRegiao . '" class="filho_' . $idMaster . '_' . $keyTipo . '_' . $btnSubId . '" data_tipo = "' . $idMaster . '_' . $keyTipo . '_' . $btnSubId . '"' . $marcador . '/><label>' . $idRegiao . '-' . $nomeRegiao . '</label>';
                                                            echo '</li>
                                                                                </ul>';
//                                                        
                                                        }
                                                        echo '</li>
                                                                  </ul>';
                                                    }
                                                }
                                                echo '</fieldset>';
                                                echo '</div>';
                                            }
                                        }
                                    }
                                    echo '</li>
                                          </ul>';
                                }
                            }
                        }
                    }
                    echo '</fieldset>';
                    echo'</div>';
                }
                ?>
                <p class="controls"> 
                    <input type="submit" name="cadastrar" value="Salvar" /> 
                    <input type="button" name="voltar" id="voltar" value="Voltar" onclick="window.location = 'index.php';" /> 
                </p>       
            </form>
        </div>
    </body>
</html>