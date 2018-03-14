<?php
include ("../include/restricoes.php");
include('../../conn.php');
include('../../funcoes.php');
//include "../funcoes.php";
include "../include/criptografia.php";

function formato_brasileiro($data) {

    if ($data != '0000-00-00') {
        echo implode('/', array_reverse(explode('-', $data)));
    }
}

if (isset($_POST['enviar'])) {



    $array_tipo_processo = $_POST['tipo'];
    $array_n_processo = $_POST['n_processo'];
    $array_ordem = $_POST['ordem'];
    


    $nome = mysql_real_escape_string($_POST['nome']);
    $data_nasci = implode('-', array_reverse(explode('/', mysql_real_escape_string($_POST['data_nasci']))));
    $rg = mysql_real_escape_string($_POST['rg']);
    $cpf = mysql_real_escape_string($_POST['cpf']);
    $atividade_nome = mysql_real_escape_string($_POST['atividade_nome']);
    $data_entrada = implode('-', array_reverse(explode('/', $_POST['data_entrada'])));
    $data_saida = implode('-', array_reverse(explode('/', $_POST['data_saida'])));
    $regiao_id = mysql_real_escape_string($_POST['regiao_id']);
    $projeto_id = mysql_real_escape_string($_POST['projeto_id']);
    $unidade = mysql_real_escape_string($_POST['unidade']);
    $valor_pedido = str_replace(',', '.', str_replace('.', '', mysql_real_escape_string($_POST['valor_pedido'])));
    $local = mysql_real_escape_string($_POST['local']);
    $adv_id = implode(',', $_POST['advogado']);
    $prep_id = implode(',', $_POST['preposto']);
    $id_trabalhador = mysql_real_escape_string($_POST['id_trabalhador']);
    $tipo_contratacao = mysql_real_escape_string($_POST['tipo_contratacao']);
    $numero_vara = $_POST['n_vara'];
    $pedido_acao = ($_POST['pedidos_acao']);

    if ($_SESSION['id_user'] == 179) {
        print_r($_REQUEST);
        exit;
    }

    if ($tipo_contratacao == 2) {
        $campo_trabalhador = 'id_clt';
    } else {
        $campo_trabalhador = 'id_autonomo';
    }


    $insert = mysql_query("INSERT INTO processos_juridicos
							(id_projeto,
							id_regiao, 
							$campo_trabalhador, 							
							adv_id,
							preposto_id,
							proc_tipo_id,
							proc_tipo_contratacao, 
							proc_nome, 
							proc_cpf, 
							proc_rg, 
							proc_data_nasc, 
							proc_atividade, 
							proc_unidade, 
							proc_data_entrada, 
							proc_data_saida, 
							proc_numero_processo, 
							proc_valor_pedido, 
							proc_local,	
							proc_numero_vara,
							pedido_acao,					
							proc_data_cad,
							usuario_cad,
							status)
							
							 VALUES 
							 
							 ('$projeto_id',
							  '$regiao_id',
							  '$id_trabalhador',
							  '$adv_id',
							  '$prep_id',
							  '1',
							  '$tipo_contratacao',
							  '$nome',
							  '$cpf',
							  '$rg',
							  '$data_nasci',
							  '$atividade_nome',
							  '$unidade',
							  '$data_entrada',
							  '$data_saida',
							  '$n_processo',
							  '$valor_pedido',
							  '$local',			
							  '$numero_vara',	
							  '$pedido_acao',			
							  NOW(),
							  '$_COOKIE[logado]',
							  1							  
							 )") or die(mysql_error());

    if ($insert) {

        $ultimo_id = mysql_insert_id();

        /*         * *****************************TABELA RELACIONAL PARA RECLAMADOS********************************** */
        $countP = 0;
        $items = $_REQUEST['camp_reclamado'];

        $sql = "INSERT INTO processos_juridicos_reclamados (id_processo, nome) VALUES ";
        foreach ($items as $key => $value) {
            $sql .= " (" . $ultimo_id . ",'" . $value . "')";
            $countP++;
            if ($countP++ >= 1) {
                $sql .= ",";
            }
        }

        $sql = substr($sql, 0, -1);
        mysql_query($sql);

        /*         * ************************************************************************************************* */

        if (sizeof($array_n_processo) == 1) {


            mysql_query("INSERT INTO n_processos (n_processo_numero, n_processo_ordem, proc_id, status, tipo_processo)
											  VALUES
											  ('$array_n_processo[0]','$ordem', '$ultimo_id', 1, '$array_tipo_processo[0]')") or die(mysql_error());
        } else {

            foreach ($array_n_processo as $chave => $valor) {

                $ordem = $array_ordem[$chave];
                $tipo_processo = $tipo_processo[$chave];


                mysql_query("INSERT INTO n_processos (n_processo_numero, n_processo_ordem, proc_id, status, tipo_processo)
											  VALUES
											  ('$numero_processo','$ordem', '$ultimo_id', 1, '$tipo_processo')") or die(mysql_error());
            }
        }


        //ADIciona o andamento PROCESSO  CADASTRADO



        $data_movi = implode(array_reverse(explode('/', $_POST['data_andamento'])));

        $qr_insert = mysql_query("INSERT INTO proc_trab_andamento (proc_id, proc_status_id, andamento_data_movi,  andamento_data_cad, andamento_usuario_cad, andamento_status)
														VALUES     ('$ultimo_id', '1', '$data_movi', NOW(), '$_COOKIE[logado]',1) ") or die(mysql_error());

        $id_andamento = mysql_insert_id();


        $nome_funcionario = mysql_result(mysql_query("SELECT nome1 FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'"), 0);
        $nome_regiao = mysql_result(mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$regiao_id'"), 0);

        if ($tipo_contratacao == 2) {
            $nome_trabalhador = mysql_result(mysql_query("SELECT nome FROM rh_clt WHERE id_clt = '$id_trabalhador'"), 0);
        } else {
            $nome_trabalhador = mysql_result(mysql_query("SELECT nome FROM autonomo WHERE id_autonomo = '$id_trabalhador'"), 0);
        }

        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $menssagem = 'Foi cadastrado um novo processo do tipo: TRABALHISTA de número(s): ' . implode(',', $array_n_processo) . ', no dia ' . date('d/m/Y') . ', na região ' . $nome_regiao . '<br><br>Nome do trabalhador: ' . $nome_trabalhador . ' <br>Autor(a) do cadastro: ' . $nome_funcionario;
        mail('fernanda.souza@sorrindo.org', 'Novo processo jurídico cadastrado.', $menssagem, $headers);



        header("Location: dados_trabalhador/anexar_doc_andamentos.php?id_processo=$ultimo_id&id_andamento=$id_andamento&regiao=$regiao_id");
    }
}




$id_user = $_COOKIE['logado'];
$id_regiao = mysql_real_escape_string($_GET['regiao']);
$id_trabalhador = mysql_real_escape_string($_GET['trab']);
$id_projeto = mysql_real_escape_string($_GET['projeto']);
$tipo_contratacao = mysql_real_escape_string($_GET['tp']);

$qr_tipo_contratacao = mysql_query("SELECT * FROM tipo_contratacao WHERE tipo_contratacao_id = '$tipo_contratacao'");
$row_tipo = mysql_fetch_assoc($qr_tipo_contratacao);

///DADOS DO TRABALHADOR
if ($tipo_contratacao == 2) {

    $qr_trabalhador = mysql_query("SELECT * FROM rh_clt WHERE id_clt='$id_trabalhador'") or die(mysql_error());
    
    // CONSULTA OS PROCESSOS CADASTRADOS PARA UM CLT PRECISA DISSO??
    $qr_processo = mysql_query("SELECT B.n_processo_numero AS n_processo, B.n_processo_ordem  AS ordem, B.tipo_processo AS tipo
                                FROM processos_juridicos AS A
                                LEFT JOIN n_processos AS B ON (A.proc_id = B.proc_id)
                                WHERE A.id_clt='$id_trabalhador' AND B.`status` = 1;")or die(mysql_error());
    
} else {
    $qr_trabalhador = mysql_query("SELECT * FROM autonomo WHERE id_autonomo='$id_trabalhador'") or die(mysql_error());
}

if ($tipo_contratacao == 3) {
    $qr_trabalhador = mysql_query("SELECT * FROM processos_juridicos_externo WHERE codigo='$id_trabalhador'") or die(mysql_error());
}


$row_trabalhador = mysql_fetch_assoc($qr_trabalhador);
extract($row_trabalhador);


if ($_SESSION['id_user'] == 179) { //LOGIN GORDO
    print_r($_SESSION);
    print_r($row_trabalhador);
}

if ($tipo_contratacao == 3) {
    $q_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$row_trabalhador[projeto]'");
    $row_projeto = mysql_fetch_assoc($q_projeto);
} else {
    $q_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$row_trabalhador[id_projeto]'");
    $row_projeto = mysql_fetch_assoc($q_projeto);
}


if ($tipo_contratacao == 3) {
    $q_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$row_trabalhador[regiao]'");
    $row_regiao = mysql_fetch_assoc($q_regiao);
} else {
    $q_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$row_trabalhador[id_regiao]'");
    $row_regiao = mysql_fetch_assoc($q_regiao);
}


$qr_tipo = mysql_query("SELECT * FROM tipo_contratacao WHERE tipo_contratacao_id ='$tipo_contratacao'");
$row_tipo = mysql_fetch_assoc($qr_tipo);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
        <link href="../../rh/css/estrutura_cadastro.css" rel="stylesheet" type="text/css">
            <script type="text/javascript" src="../../js/ramon.js"></script>
            <script type="text/javascript" src="../../js/jquery-1.3.2.js"></script>
            <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
            <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>

            <script type="text/javascript" src="../../jquery/validationEngine/jquery.validationEngine-pt.js" ></script>
            <script type="text/javascript" src="../../jquery/validationEngine/jquery.validationEngine.js" ></script>
            <link href="../../jquery/validationEngine/validationEngine.jquery.css" rel="stylesheet" type="text/css">

                <script type="text/javascript" src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" ></script>

                <script type="text/javascript" src="../../jquery/priceFormat.js" ></script>

                <script type="text/javascript">
                    $(function() {

                        $('#cpf').mask('999.999.999-99');
                        $('#telefone').mask('(99)9999-9999');
                        $('#cel').mask('(99)9999-9999');
                        //  $('.numero_processo').mask('9999999-99.9999.9.99.9999');
                        $('#data_andamento').mask('99/99/9999');


                        $('#valor_pedido').priceFormat({
                            prefix: '',
                            centsSeparator: ',',
                            thousandSeparator: '.',
                        });

                        $('#valor_encerramento').priceFormat({
                            prefix: '',
                            centsSeparator: ',',
                            thousandSeparator: '.',
                        })


                        $('#form1').validationEngine();
                        $('input[name=tipo]').change(function() {

                            var tipo = $(this).val();

                            if (tipo == 1) {

                                $('#oab').fadeIn();

                            } else {
                                $('#oab').fadeOut();
                            }

                        });

                        $('#add_preposto').click(function() {

                            var campo = $('#campo_preposto').html();

                            $('#campo_preposto').next().append('<div>' + campo + '<a href="#" onclick="$(this).parent().remove()" class="excluir"> Excluir </a></div>');

                        });


                        $('#add_reclamado').click(function() {
                            var camp = $("#camp_reclamado").addClass("reclamado");
                            $("#camp_reclamado").clone().insertAfter(camp);

                        });


                        $('#add_advogado').click(function() {

                            var campo = $('#campo_advogado').html();

                            $('#campo_advogado').next().append('<div>' + campo + '<a href="#" onclick="$(this).parent().remove()" class="excluir"> Excluir </a></div>');

                        });
                        
                        $("#campos_n_processo").on("change", ".a2 input[name^='tipo_num_proc_']", function (event) {
                            console.log($(this));
                            $(this).parent().find("input[name='n_processo[]']").unmask();
                            $(this).parent().find("input[name='n_processo[]']").val('');
                            $(this).parent().find("input[name='ordem[]']").val('');
                            if ($(this).val() == "1") { // PJe
                                $(this).parent().find("input[name='n_processo[]']").mask('99-99.99.99.99.99');
                            } 
                            if ($(this).val() == "2") { // Outro
                                $(this).parent().find("input[name='n_processo[]']").mask('9999999-99.9999.9.99.9999');
                            } 
                            $(this).parent().find("input[name='tipo[]']").val($(this).val());
                        });
                        
                        var cont = 2;
                        $("#add_n_processo").click(function() {
                            var div = $(".a2:first").clone();
                            $(div).find('input:text').val('');
                            $(div).find('input:radio').attr('name', 'tipo_num_proc_' + cont);
                            $(div).find('input:radio').removeAttr('checked');
                            $(div).appendTo("#campos_n_processo");
                            cont++;
                        });

                    });

                </script>


                <title>::Intranet:: Cadastro de Processos CLT</title>
                </head>
                <body>
                    <div id="corpo">

                        <table align="center" width="100%" cellspacing="0" cellpadding="12" style="font-size:13px; line-height:22px;">
                            <tr>
                                <td><a href="#" onclick="history:back();"></a>
                                    <div style="border-bottom:2px solid #F3F3F3; margin:10px 0 18px 0;">
                                        <h2 style="float:left; font-size:18px;margin-top:40px;">
                                            CADASTRAR PROCESSO:<span class="projeto">
                                                <?php echo $row_tipo['tipo_contratacao_nome']; ?>
                                            </span>
                                        </h2>


                                        <p style="float:right;margin-top:40px;">
                                            <a href="index.php?regiao=<?= $regiao ?>&tp=<?php echo $row_tipo['tipo_contratacao_id']; ?>">&laquo; Voltar</a>
                                        </p>

                                        <p style="float:right;margin-left:15px;background-color:transparent;">
                                            <?php include('../../reportar_erro.php'); ?>   		
                                        </p>
                                        <div class="clear"></div>
                                    </div>

                                    <?php
                                    if (!empty($erros)) {
                                        $erros = implode('<br>', $erros);
                                        echo '<p style="background-color:#C30; padding:4px; color:#FFF;">' . $erros . '</p><p>&nbsp;</p>';
                                    }
                                    ?>

                                    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" name="form1" 
                                          id="form1" enctype="multipart/form-data" onSubmit="return validaForm()">

                                        <table cellpadding="0" cellspacing="1" class="secao">
                                            <tr>
                                                <td colspan="4" class="secao_pai" style="border-top:1px solid #777;">DADOS</td>
                                            </tr>

                                            <tr>
                                                <td class="secao" width="150">Código:</td>
                                                <td colspan="3"><input name="" size="50" type="text"   value="<?= $id_trabalhador ?>" disabled/>
                                                    <input name="id_trabalhador" size="50" type="hidden" id="cod_clt"  value="<?= $id_trabalhador ?>" />

                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="secao" width="150">Tipo de Contratação</td>
                                                <td colspan="3">
                                                    <input size="50" type="text"  value="<?= $row_tipo['tipo_contratacao_nome'] ?>" disabled="disabled" />
                                                    <input name="tipo_contratacao" size="50" type="hidden" id="tipo_contratacao"  value="<?= $tipo_contratacao ?>" />
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="secao" >Nome:</td>
                                                <td colspan="3"><input name="" size="50" type="text"   value="<?= $nome ?>"  disabled/>
                                                    <input name="nome" size="50" type="hidden" id="nome"  value="<?= $nome ?>" />
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="secao" >Reclamado:</td>
                                                <td colspan="3">
                                                    <input type="text" name="camp_reclamado[]" id="camp_reclamado" size="50"  />
                                                    <a href="#" onclick="return(false)" id="add_reclamado"><img src="../../imagens/add.png" width="18" height="18" title="Adicionar"/></a>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="secao" >Data de nascimento:</td>
                                                <?php if ($_REQUEST['tp'] == 3) { ?>
                                                    <td colspan="3"><input name="" size="15" type="text" value="<?= formato_brasileiro($nascimento) ?>" disabled/>
                                                        <input name="data_nasci" size="15" type="hidden" value="<?= formato_brasileiro($nascimento) ?>" /> </td>
                                                <?php } else { ?>
                                                    <td colspan="3"><input name="" size="15" type="text" value="<?= formato_brasileiro($data_nasci) ?>" disabled/>
                                                        <input name="data_nasci" size="15" type="hidden" value="<?= formato_brasileiro($data_nasci) ?>" /> </td>
                                                <?php } ?>
                                            </tr>

                                            <tr>
                                                <td class="secao">RG:</td>
                                                <td ><input name="rg" size="15" type="text" id="rg"   value="<?= $rg ?>" /></td>

                                                <td class="secao">CPF:</td>
                                                <td ><input name="cpf" size="20" type="text" id="cpf"  value="<?= $cpf ?>" /></td>
                                            </tr>

                                            <tr>
                                                <td class="secao">Atividade:</td>
                                                <?php
                                                $q_atividade = mysql_query("SELECT * FROM curso WHERE id_curso='$id_curso'") or die(mysql_error());
                                                $row_atividade = mysql_fetch_assoc($q_atividade);
                                                ?>

                                                <td colspan="3">
                                                    <?php if ($_REQUEST['tp'] == 3) { ?>
                                                        <input name="q" size="90" type="text" id="atividade_nome" value="<?= $atividade ?>" disabled/>
                                                        <input name="atividade_nome" size="90" type="hidden" id="atividade_nome" value="<?= $atividade ?>" />
                                                    <?php } else { ?>
                                                        <input name="q" size="90" type="text" id="atividade_nome" value="<?= $row_atividade['nome'] ?>" disabled/>
                                                        <input name="atividade_nome" size="90" type="hidden" id="atividade_nome" value="<?= $row_atividade['nome'] ?>" />
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="secao">Data de entrada:</td>
                                                <td>
                                                    <input name="as" size="20" type="text" value="<?= formato_brasileiro($data_entrada) ?>" disabled />
                                                    <input name="data_entrada" size="20" type="hidden" id="data_entrada" value="<?= formato_brasileiro($data_entrada) ?>" />              

                                                </td>
                                                <td class="secao">Data de saída:</td>
                                                <td>
                                                    <input name="b" size="20" type="text" id="data_entrada" value="<?= formato_brasileiro($data_saida) ?>" disabled/>              
                                                    <input name="data_saida" size="20" type="hidden" id="data_entrada" value="<?= formato_brasileiro($data_saida) ?>" />              

                                                </td>


                                            </tr>
                                            <tr>
                                                <td class="secao">Região:</td>
                                                <td colspan="3">
                                                    <?php if ($_REQUEST['tp'] == 3) { ?> 
                                                        <input name="regiao_nome" size="50" type="text" id="regiao_nome" class="validate[required]" value="<?= $row_regiao['regiao'] ?>" disabled/>
                                                        <input name="regiao_id"  type="hidden" id="regiao_id"  value="<?= $regiao; ?>" />
                                                    <?php } else { ?>
                                                        <input name="regiao_nome" size="50" type="text" id="regiao_nome" class="validate[required]" value="<?= $row_regiao['regiao'] ?>" disabled/>
                                                        <input name="regiao_id"  type="hidden" id="regiao_id"  value="<?= $id_regiao; ?>" />
                                                    <?php } ?>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="secao">Projeto:</td>
                                                <td colspan="3">
                                                    <input name="projeto_nome" size="50" type="text" id="projeto_nome" class="validate[required]" value="<?= $row_projeto['nome'] ?>"  disabled/>
                                                    <input name="projeto_id"  type="hidden" id="projeto_id"  value="<?= $id_projeto ?>" />

                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="secao">Unidade:</td>
                                                <?php if ($_REQUEST['tp'] == 3) { ?>
                                                    <td colspan="3"><input name="unidade" size="50" type="text" id="unidade" class="validate[required]" value="<?= $unidade ?>" /></td>
                                                <?php } else { ?>
                                                    <td colspan="3"><input name="unidade" size="50" type="text" id="unidade" class="validate[required]" value="<?= $locacao ?>" /></td>
                                                <?php } ?>
                                            </tr>

                                            <tr>
                                                <td class="secao"> Data de cadastro:</td>
                                                <td colspan="3"><input type="text" name="data_andamento" id="data_andamento"/></td>
                                            </tr>

                                            <tr>
                                                <td class="secao"> Pedidos da ação</td>
                                                <td colspan="3"><textarea type="text" name="pedidos_acao" id="pedidos_acao" rows="6" cols="40"></textarea></td>
                                            </tr>


                                            <tr>

                                                <td class="secao">N&ordm; do Processo: 	<a href="#" onclick="return(false)" id="add_n_processo"><img src="../../imagens/add.png" width="18" height="18" title="Adicionar"/></a></td>
                                                <td>
                                                    <div id="campos_n_processo">
                                                        <?php           
                                                             while ($processo = mysql_fetch_assoc($qr_processo)) {
                                                        ?>
                                                        <div class="a2">
                                                            <label>Tipo:</label>
                                                            <input type="radio" name="tipo_num_proc_1" value="1" <?php if($processo['tipo']== '1') echo 'checked="checked"'?>/><label>PJe</label>
                                                            <input type="radio" name="tipo_num_proc_1" value="2" <?php if($processo['tipo']== '2') echo 'checked="checked"'?>/><label>OUTRO</label>
                                                           <input type="hidden" name="tipo[]"/> <!-- guarda o valor do campo acima  -->
                                                            <br />
                                                            <input name="n_processo[]" size="25" type="text" value="<? echo $processo['n_processo']?>" />
                                                            <label id="lOrdem">Ordem:</label><input name="ordem[]" type="text" size="2" value="<?php echo $processo['ordem']; ?>"/>
                                                        </div>
                                                      <?php }?>
                                                    </div>
                                                </td>

                                                    <td class="secao">Valor Pedido:</td>
                                                    <td  colspan="1"><input name="valor_pedido" size="10" type="text" id="valor_pedido" /></td>
                                            </tr>

                                            <tr>
                                                <td class="secao">Vara</td>
                                                <td><input name="local" size="30" type="text" id="local" class="validate[required]"/></td>          
                                                <td><strong>Nº da vara:</strong></td>
                                                <td><input name="n_vara" type="text"/></td>
                                            </tr>

                                            <tr>
                                                <td class="secao" >Advogado:
                                                    <br />
                                                    <a href="#" onclick="return(false)" id="add_advogado"><img src="../../imagens/add.png" width="18" height="18" title="Adicionar"/></a>
                                                </td>
                                                <td>
                                                    <div id="campo_advogado">
                                                        <select name="advogado[]">
                                                            <option value="">Selecione uma opção..</option>
                                                            <option value=""></option>
                                                            <?php
                                                            $qr_advogado = mysql_query("SELECT * FROM advogados WHERE adv_status = 1");
                                                            while ($row_advogado = mysql_fetch_assoc($qr_advogado)):
                                                                ?>
                                                                <option value="<?php echo $row_advogado['adv_id'] ?>"> <?php echo $row_advogado['adv_nome'] ?> </option>

                                                                <?php
                                                            endwhile;
                                                            ?>


                                                        </select>
                                                    </div>

                                                    <div></div>
                                                </td>

                                                <td class="secao" >Preposto:<br />
                                                    <a href="#" onclick="return(false)" id="add_preposto"><img src="../../imagens/add.png" width="18" height="18" title="Adicionar"/></a>
                                                </td>
                                                <td>
                                                    <div id="campo_preposto">
                                                        <select name="preposto[]">
                                                            <option value="">Selecione uma opção..</option>
                                                            <option value=""></option>
                                                            <?php
                                                            $qr_preposto = mysql_query("SELECT * FROM prepostos WHERE prep_status = 1");
                                                            while ($row_preposto = mysql_fetch_assoc($qr_preposto)):
                                                                ?>
                                                                <option value="<?php echo $row_preposto['prep_id'] ?>"> <?php echo $row_preposto['prep_nome'] ?> </option>

                                                                <?php
                                                            endwhile;
                                                            ?>

                                                        </select>
                                                    </div>
                                                    <div></div>                 
                                                </td>

                                            </tr>



                                            <tr>
                                                <td  colspan="4" align="center" style="text-align:center;">
                                                    <input name="enviar" type="submit" value="CADASTRAR"/>
                                                </td>
                                            </tr>

                                        </table>
                                    </form>
                                </td>
                            </tr>

                        </table>
                    </div>
                </body>
                </html>