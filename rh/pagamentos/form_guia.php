<?php
include('../../conn.php');
include("../../wfunction.php");
include("../../funcoes.php");

function get_info_form_guia($tela, $id_projeto, $mes, $ano) {
    $arr = array();
//    if ($tela == 5) {
    $sql = "SELECT A.id_projeto, A.nome AS nome_projeto, B.id_regiao, B.regiao AS nome_regiao FROM projeto AS A
                LEFT JOIN regioes AS B ON(A.id_regiao=B.id_regiao)
                WHERE A.id_projeto='$id_projeto' AND A.status_reg=1 AND B.`status`=1 AND A.status_reg=1;";
//        echo $sql.'<br>';
    $result = mysql_query($sql);
    $arr = mysql_fetch_array($result);
//    }
    return $arr;
}

function cadastrar_saida($dados = array()) {

    $nosso_numero = ''; //?? ex: 198/23900739-1
    $n_documento = '';  // ?? ex: 23900739
    $dt_emissao_nf = ''; //?? ex: 0000-00-00

    $sql_saida = "INSERT INTO `saida` (`id_regiao`, `id_projeto`, `id_banco`, `id_user`, `nome`, `id_nome`, `especifica`, `tipo`, `adicional`, `valor`, `data_proc`, `data_vencimento`, `data_pg`, `hora_pg`, `comprovante`, `tipo_arquivo`, `id_userpg`, `id_compra`, 
        `campo3`, `status`, `id_deletado`, `data_deletado`, `valor_bruto`, `juridico`, `id_referencia`, `id_bens`, `id_tipo_pag_saida`, `nosso_numero`, `cod_barra_consumo`, `cod_barra_gerais`, `nota_impressa`, `id_clt`, `entradaesaida_subgrupo_id`, `tipo_boleto`, 
        `tipo_empresa`, `id_fornecedor`, `nome_fornecedor`, `cnpj_fornecedor`, `id_prestador`, `nome_prestador`, `cnpj_prestador`, `impresso`, `user_impresso`, `data_impresso`, `id_coop`, `link_nfe`, `n_documento`, `estorno`, `estorno_obs`, `valor_estorno_parcial`, 
        `id_saida_pai`, `darf`, `tipo_darf`, `mes_competencia`, `ano_competencia`, `id_autonomo`, `dt_emissao_nf`, `tipo_nf`)         
         VALUES ('$dados[id_regiao]', '$dados[id_projeto]', '$dados[id_banco]', '$dados[id_user]', '$dados[nome]', '$dados[id_nome]', '$dados[especifica]', '$dados[tipo]','' ,'$dados[valor]', NOW(), '$dados[data_vencimento]', '0000-00-00', '00:00:00', 2, '', '0', '', '', 1, '0', "
            . "'0000-00-00 00:00:00', '$dados[valor]', '0', 1, '0', 1, '$nosso_numero', '', '', '0', '0', '2', '2', '0', '0', '', '', '', '', '', '0', '0', '0000-00-00', '0', '', '$n_documento', '0', '', '0.00', '0', '0', 0, '$dados[mes_competencia]', '$dados[ano_competencia]', '0', '$dt_emissao_nf', '0');";


    mysql_query($sql_saida);

    $id_saida = mysql_insert_id();

//    echo 'sql saida: ' . $sql_saida . '<br><br>';
//    echo 'id saida: ' . $id_saida . '<br><br>';

    if ($id_saida) {

        $diretorio_destino = '../../comprovantes/';
        for ($i = 0; $i < count($dados['arquivo']['name']); $i++) {

            $nome_arquivo = explode('.', $dados['arquivo']['name'][$i]);

            $sql_anexos = "INSERT INTO saida_files(id_saida,tipo_saida_file) VALUES ('$id_saida','.{$nome_arquivo[1]}')";

            mysql_query($sql_anexos);

            $id_saida_file = mysql_insert_id();

            $nome_arquivo = $id_saida_file . '.' . $id_saida . '.' . $nome_arquivo[1];

            if (!is_file($diretorio_destino . $nome_arquivo)) {
                if (move_uploaded_file($dados['arquivo']['tmp_name'][$i], $diretorio_destino . $nome_arquivo)) {
                    echo '<div class="message-box message-green"><p>Dado gravado com sucesso!</p></div>';
                    //            echo 'Arquivo ' . $nome_arquivo . ' salvo com sucesso!<br>';c
                } else {
                    //            echo 'Houve falha no envido do arquivo ' . $nome_arquivo . ' <br>';
                }
            } else {
                echo '<div class="message-box message-red id-saida-' . $id_saida_file . '"><p>Erro ao enviar o arquivo!</p></div>';
            }
        }
//        echo '<br>id saida: ' . $id_saida . '- id saida_files: ' . $id_saida_files[$x] . '- sql saida_files: ' . $sql_anexos . '<br><br>';
    }
}

function carrega_arquivos($id_saida) {
    $query = "SELECT * FROM saida_files WHERE id_saida = $id_saida";
    $result = mysql_query($query);
    while ($row = mysql_fetch_array($result)) {
        $x[] = $row;
    }
    return $x;
}

$usuario = carregaUsuario();




if (isset($_REQUEST['acao'])) {

    switch ($_REQUEST['acao']) {
        case 'cadastrar_saida':
            $dados = array();
            $dados['id_regiao'] = isset($_REQUEST['id_regiao']) ? $_REQUEST['id_regiao'] : NULL;

            if ($dados['id_regiao'] != $usuario['id_regiao']) {
                exit('Região inválida');
            }

            $dados['id_projeto'] = isset($_REQUEST['id_projeto']) ? $_REQUEST['id_projeto'] : NULL;
            $dados['id_banco'] = isset($_REQUEST['banco']) ? $_REQUEST['banco'] : NULL;
            $dados['id_user'] = $usuario['id_funcionario'];
            $dados['nome'] = isset($_REQUEST['nome']) ? $_REQUEST['nome'] : NULL;
            $dados['especifica'] = isset($_REQUEST['especifica']) ? $_REQUEST['especifica'] : NULL;
            $dados['valor'] = isset($_REQUEST['valor']) ? str_replace('R$ ', '', str_replace('.', '', $_REQUEST['valor'])) : NULL;
            //    $dados['valor'] = isset($_REQUEST['valor']) ? str_replace('R$ ', '', str_replace(',', '.', str_replace('.', '', $_REQUEST['valor']))) : NULL;
            $dados['id_nome'] = isset($_REQUEST['id_nome']) ? $_REQUEST['id_nome'] : NULL;
            $dados['tipo'] = isset($_REQUEST['tipo']) ? $_REQUEST['tipo'] : NULL;

            $arr_data_vencimento = array_reverse(explode('/', $_POST['data']));

            $dados['data_vencimento'] = isset($_REQUEST['data']) ? implode('-', $arr_data_vencimento) : NULL;
            $dados['mes_competencia'] = isset($_REQUEST['mes']) ? $_REQUEST['mes'] : NULL;
            $dados['ano_competencia'] = isset($_REQUEST['ano']) ? $_REQUEST['ano'] : NULL;
            $dados['arquivo'] = isset($_FILES['arquivo']) ? $_FILES['arquivo'] : NULL;

            echo '<html><head><link href="../../net1.css" rel="stylesheet" type="text/css" /></head><body>';
            cadastrar_saida($dados);
            
            echo "<script> 
                    setTimeout(function(){
                    window.parent.location.href = 'http://" . $_SERVER['HTTP_HOST'] . "/intranet/rh/pagamentos/index.php?id=1&regiao={$dados['id_regiao']}&mes={$dados['mes_competencia']}&ano={$dados['ano_competencia']}&filtrar=1&tipo_pagamento=5&tipo_contrato=2';
                    parent.eval('tb_remove()')
                    },3000)    
            </script>";
    
            echo '</body></html>';
            break;

        case 'deletar_saida':
            $id_saida = isset($_POST['id_saida']) ? $_POST['id_saida'] : NULL;
            $sql_delete = "UPDATE saida SET status='0', id_deletado='" . $usuario['id_funcionario'] . "', data_deletado=NOW() WHERE id_saida='$id_saida' AND status=1 LIMIT 1";
            mysql_query($sql_delete);
            echo '<html><head><link href="../../net1.css" rel="stylesheet" type="text/css" /></head><div class="message-box message-yellow">
                <p>Saída deletada com sucesso!</p>
            </div><body>';
            cadastrar_saida($dados);
            echo '</body></html>';
            break;
    }
    exit;
} else {

    $tela = isset($_REQUEST['tela']) ? $_REQUEST['tela'] : NULL;
    $id_projeto = isset($_REQUEST['id_projeto']) ? $_REQUEST['id_projeto'] : NULL;
    $mes = isset($_REQUEST['mes']) ? $_REQUEST['mes'] : NULL;
    $ano = isset($_REQUEST['ano']) ? $_REQUEST['ano'] : NULL;

    $arr_config = array('5' => array('id_nome' => '3277',
            'nome' => 'ALELO - VALE TRANSPORTE',
            'tipo' => '12',
            'tipo_pg' => '5',
            'subgrupo' => '2'
        ),
        '6' => array('id_nome' => '3680',
            'nome' => 'VALE REFEIÇÃO',
            'tipo' => '13',
            'tipo_pg' => '6',
            'subgrupo' => '2'
        ),
        '7' => array('id_nome' => '3690',
            'nome' => 'VALE ALIMENTAÇÃO',
            'tipo' => '16',
            'tipo_pg' => '6',
            'subgrupo' => '2'
    ));

    $row_vale = get_info_form_guia($tela, $id_projeto, $mes, $ano);

    $sql_files = "SELECT A.id_saida,/*B.id_saida_file,B.tipo_saida_file,*/ C.nome AS enviado_por, D.nome AS pago_por, 
                    E.id_banco, E.nome AS nome_banco, DATE_FORMAT(A.data_proc,'%d/%m/%Y') AS enviado_em, A.valor, DATE_FORMAT(A.data_vencimento,'%d/%m/%Y') AS data_vencimento,
                    A.nome, A.especifica, A.`status`,
                    @var_dias:=DATEDIFF(A.data_vencimento,DATE(NOW())) AS dias,
                    IF(A.`status`=1 AND A.data_vencimento<DATE(NOW()),3, IF(@var_dias<3 AND A.`status`=1, 4, A.`status`)) AS status_fake                    
                    FROM saida AS A
                    #LEFT JOIN saida_files AS B ON(A.id_saida=B.id_saida)
                    LEFT JOIN funcionario AS C ON(C.id_funcionario=A.id_user)
                    LEFT JOIN funcionario AS D ON(D.id_funcionario=A.id_userpg)
                    LEFT JOIN bancos AS E ON(A.id_banco=E.id_banco)
                    WHERE A.mes_competencia=$mes AND A.ano_competencia=$ano AND A.id_projeto=$id_projeto AND id_nome='" . $arr_config[$tela]['id_nome'] . "' ORDER BY data_vencimento ASC;";

    echo '<!-- *** ' . $sql_files . ' -->';

    $result_files = mysql_query($sql_files);


//echo '<pre>';
//print_r($_REQUEST);
//echo '</pre>';
//exit();

    /* checa folha e regiao */
    if (empty($row_vale)) {
        exit('Dados não encontrados!');
    }

    /* get bancos */
    $sql_bancos = "SELECT * FROM bancos WHERE id_regiao='$row_vale[id_regiao]';";
    $result = mysql_query($sql_bancos);
    $bancos = array();
    while ($row = mysql_fetch_array($result)) {
        $bancos[$row['id_banco']] = $row['id_banco'] . ' - ' . $row['nome'];
    }
    ?> 
    <html>
        <head>
            <title><?= $arr_config[$tela]['nome']; ?></title>
            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
            <link href="../../net1.css" rel="stylesheet" type="text/css" />
            <link href="/intranet/jquery/datepicker-lite/jquery-ui-1.8.4.custom.css" rel="stylesheet" type="text/css" />

                                            <!--<script src="/intranet/js/jquery-1.10.2.min.js" type="text/javascript" ></script>-->
            <script src="/intranet/js/jquery-1.8.3.min.js" type="text/javascript" ></script>
            <script src="/intranet/jquery/datepicker-lite/jquery-ui-1.8.4.custom.min.js" type="text/javascript" ></script>
            <script src="/intranet/js/jquery.price_format.2.0.min.js" type="text/javascript" ></script>
            <script src="/intranet/js/global.js" type="text/javascript" ></script>
            <script>
                $('#form1').submit(function () {
                    $('#bt_submit_guia').val('Enviando por favor espere...');
                    $('#bt_submit_guia').attr('disabled', 'disabled');

                });
                function deletar_saida(id_saida) {
                    if (confirm('Deseja deletar realmente?')) {
                        $('#acao_form').val('deletar_saida');
                        $('#id_saida').val(id_saida);
                        $('#form1').submit();
                    }
                }
                $(document).ready(function () {
                    $("#add_arquivo").click(function () {
                        console.log('teste');
                        $(this).before('<input name="arquivo[]" class="arquivo" type=file /><br />');
                    });
                    
                    $('#data').on('focusout', function () {
                        var competencia = $(this).val().split('/');

                        if (competencia[2] < new Date().getFullYear()) {
                            $(this).val("");
                            alert("Ano não pode ser menor que " + new Date().getFullYear());
                        }
                    });
                });
            </script>
            <style>
                .aviso { color: #f96a6a; font-weight: bold; }
                .grid tr.red {color: red;}
                .grid tr.del {opacity: 0.5; color: #333;}
            </style>
        </head>
        <body class="novaintra">
            <div id="content" style="min-height: 75px;">
                <form id="form1" name="form" action="form_guia.php" method="post"  enctype="multipart/form-data" >
                    <h3><?= $arr_config[$tela]['nome']; ?></h3>
                    <input type="button" value="Adicionar Boleto" style="float: right; margin-bottom: 5px;" onclick="$('#form_vt').toggle();" />
                    <table width="780" cellspacing="0" cellpadding="0" class="grid" style="display: none;" id="form_vt">
                        <tr>
                            <td align="right">Nome :</td>
                            <td><textarea name="nome"  style="width: 78%;" ><?= $arr_config[$tela]['nome'] . ' - Região: ' . $row_vale['nome_regiao'] . ', Projeto: ' . $row_vale['nome_projeto']; ?></textarea> </td>
                        </tr>
                        <tr>
                            <td align="right">Especifica :</td>
                            <td><textarea name="especifica"  style="width: 78%;" ></textarea> </td>
                        </tr>
                        <tr>
                            <td align="right">Valor :</td>
                            <td><input name="valor" type="text" id="valor" size="13" class="validate[required] money" /></td>
                        </tr>
                        <tr>
                            <td align="right">Data :</td>
                            <td><input name="data" type="text" id="data" size="13" class="validate[required,custom[dateBr]] date_f"/></td>
                        </tr>
                        <tr>
                            <td align="right">Banco :</td>
                            <td>
                                <?php echo montaSelect($bancos, $bancoSelected, "name='banco' id='banco'  class='validate[required,custom[select]]'") ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="right">
                                Selecione um arquivo 
                            </td>
                            <td>
                                <input name="arquivo[]" class="arquivo" type=file />
                                <br />
                                <button type="button" id="add_arquivo">+ Arquivo</button>
                                <br />
                                <span style="color:#F00;" >Aguarde a mensagem de conclus&atilde;o!</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" align="center">
                                <p class="aviso"></p>
                                <input type="submit" class="botaoGordo" id="bt_submit_guia" style="float: right;padding: 2px 20px;" />
                            </td>
                        </tr>
                    </table>
                    <input type="hidden" name="id_regiao" value="<?= $row_vale['id_regiao']; ?>" />
                    <input type="hidden" name="id_projeto" value="<?= $row_vale['id_projeto']; ?>" />
                    <input type="hidden" name="mes" value="<?= $mes; ?>" />
                    <input type="hidden" name="ano" value="<?= $ano; ?>" />
                    <input type="hidden" name="id_nome" value="<?= $arr_config[$tela]['id_nome']; ?>" />
                    <input type="hidden" name="tipo" value="<?= $arr_config[$tela]['tipo']; ?>" />
                    <input type="hidden" name="tela" value="<?= $tela; ?>" />
                    <input type="hidden" id="id_saida" name="id_saida" value=""   />
                    <input type="hidden" id="acao_form" name="acao" value="cadastrar_saida"   />
                </form>
                <?php if (mysql_num_rows($result_files) > 0) { ?>
                    <table class="grid" cellpadding="0" cellspacing="0" width="800">
                        <thead> 
                            <tr class="titulo">
                                <th>Status</th>
                                <th>Enviado Em</th>
                                <th>Enviado Por</th>
                                <th>Saída</th>
                                <th>Banco</th>
                                <th>Descrição</th>
                                <th>Especificação</th>
                                <th>Valor</th>
                                <th>Vencimento Em</th>
                                <th>Pago Por</th>
                                <th>Arquivo</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $arr_status = array('0' => 'Deletado', '1' => 'Não Pago', '2' => 'Pago', '3' => 'Não Pago - VENCIDA', '4' => 'Não Pago - Vencendo em ');
                            $arr_style = array('0' => 'del', '1' => 'green', '2' => 'blue', '3' => 'red', '4' => 'red');
                            $cont = 0;
                            while ($row = mysql_fetch_array($result_files)) {
                                $cont++;
                                $dias_faltam = ($row['status_fake'] == 4) ? $row['dias'] . ' dia(s).' : '';
                                ?>
                                <tr style="font-size: 10px;" class="<?php
                                echo (($cont % 2) == 0) ? 'even' : 'odd';
                                echo ' ' . $arr_style[$row['status_fake']];
                                ?>">
                                    <td>
                                        <span class="tx-<?= $arr_style[$row['status_fake']]; ?>" data-key=""><?= $arr_status[$row['status_fake']] . $dias_faltam ?></span>
                                    </td>
                                    <td><?= $row['enviado_em']; ?></td>
                                    <td><?= $row['enviado_por']; ?></td>
                                    <td><?= $row['id_saida']; ?></td>
                                    <td><?= $row['id_banco'] . ' - ' . $row['nome_banco']; ?></td>
                                    <td><?= $row['nome']; ?></td>
                                    <td><?= $row['especifica']; ?></td>
                                    <td>R$ <?= $row['valor']; ?></td>
                                    <td style="font-size: 16px;">
                                        <strong><?= $row['data_vencimento']; ?></strong>                        
                                    </td>
                                    <td><?= $row['pago_por']; ?></td>
                                    <td>
                                        <?php
                                        $arquivos = carrega_arquivos($row['id_saida']);

                                        foreach ($arquivos as $value) {
                                            $link_file = '/intranet/comprovantes/' . $value['id_saida_file'] . '.' . $value['id_saida'] .  $value['tipo_saida_file'];
                                            ?><a href="<?= $link_file; ?>" target="_blank" ><img src="../../imagens/icones/icon-docview.gif"></a><?php
                                        }

//                                        $link_file = 'javascript:;';
//                                        if ($row['status_fake'] != 0) {
//                                            $link_file = '/intranet/comprovantes/' . $row['id_saida_file'] . '.' . $row['id_saida'] . $row['tipo_saida_file'];
//                                        }
                                        ?>
            <!--                                        <a href="<?= $link_file; ?>" target="_blank" ><img src="../../financeiro/imagensfinanceiro/attach-32.png"></a>-->
                                    </td>
                                    <td>
                                        <?php if ($row['status_fake'] == 1 || $row['status_fake'] == 3 || $row['status_fake'] == 4) { ?>
                                            <a href="javascript:;" onclick="deletar_saida(<?= $row['id_saida']; ?>)" ><img src="/intranet/imagens/icones/icon-excluir.png" title="Excluir Saída" /></a>
                                        <?php } else { ?>
                                            <img src="/intranet/imagens/icones/icon-delete-disabled.gif" />
                                        <?php } ?>
                                    </td>
                                </tr> 
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <br><br><div class="message-box message-yellow"><p>Não existem arquivos.</p></div>
                <?php } ?>

            </div>            
        </body>
    </html>
<?php } ?>