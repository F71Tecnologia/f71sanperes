<?php
include ("include/restricoes.php");
include "../conn.php";
include "../funcoes.php";
include("../wfunction.php");
include("../classes_permissoes/regioes.class.php");

$REGIOES = new Regioes;

$id_user = $_COOKIE['logado'];
$id_saida = $_REQUEST['id'];
$enc = $_REQUEST['enc'];
$regiao_prestador = $regiao;


//AJAX SOH PRA EDITAR O NOME E O CNPJ DA TABELA ENTRADASAIDA_NOME
if (isset($_REQUEST['method']) && $_REQUEST['method'] == "editanome") {
    $re['status'] = false;
    $id = $_REQUEST['id'];
    $nome = $_REQUEST['nome'];
    $cpf = $_REQUEST['cpf'];
    if (!empty($id) && !empty($nome) && !empty($cpf)) {
        $cpfN = str_replace(array(".", "-", "/"), "", $cpf);
        $output = preg_replace("[' '-./ t]", '', $cpfN);
        $mask = (strlen($cpfN) == 11) ? '###.###.###-##' : '##.###.###/####-##';
        $index = -1;
        for ($i = 0; $i < strlen($mask); $i++):
            if ($mask[$i] == '#')
                $mask[$i] = $output[++$index];
        endfor;

        if (mysql_query("UPDATE entradaesaida_nomes SET nome = '$nome', cpfcnpj='{$mask}' WHERE id_nome = {$id}")) {
            $re['status'] = true;
        }

        echo json_encode($re);
        exit;
    }
}


if (isset($enc)) {
    // RECEBENDO A VARIAVEL CRIPTOGRAFADA
    list($regiao) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));
}

$qr_funcionario = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_funcionario = mysql_fetch_assoc($qr_funcionario);

$qr_master = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$row_funcionario[id_regiao]'");
$row_master = mysql_fetch_assoc($qr_master);

if (isset($_REQUEST['id'])) {
    ///////////////////////////////
    ///////////////// DADOS DA SAÍDA //////////////////////
    //////////////////////////////////////
    $qr_saida = mysql_query("SELECT  A.*, DATE_FORMAT(A.dt_emissao_nf, '%d/%m/%Y') as dt_emissao_nf_br,
                                    B.c_razao as prestador_nome,B.c_cnpj as prestador_cnpj, B.id_regiao as prestador_id_regiao, B.id_projeto as prestador_id_projeto,
                                    C.nome as fornecedor_nome,C.cnpj as fornecedor_cnpj,C.id_regiao as fornecedor_id_regiao,C.id_projeto as fornecedor_id_projeto
                                    FROM saida as A
                                    LEFT JOIN prestadorservico as B 
                                    ON A.id_prestador = B.id_prestador
                                    LEFT JOIN fornecedores as C
                                    ON C.id_fornecedor = A.id_fornecedor
                                    WHERE id_saida = '$id_saida'");

    $row_saida = mysql_fetch_assoc($qr_saida);

    
  
    
    ////tipos de saidas que só podem ser editados os anexos
    $array_nao_editaveis = array(167, 175, 168, 169, 170);
    if (in_array($row_saida['tipo'], $array_nao_editaveis)) {
        $editavel = 'display:none;';
    } else {
        $editavel = 'display:block;';
    }

    $qr_prestador_pg = mysql_query("SELECT * FROM prestador_pg as A  
                                INNER JOIN prestadorservico as B
                                ON A.id_prestador = B.id_prestador
                                 WHERE A.id_saida = '$id_saida'");
    $row_prestador_pg = mysql_fetch_assoc($qr_prestador_pg);
    $qr_tipos = mysql_query("SELECT * FROM entradaesaida WHERE id_entradasaida = '$row_saida[tipo]'") or die(mysql_error());
    $row_tipo = mysql_fetch_assoc($qr_tipos);
    $saida_grupo = $row_tipo['grupo'];
    $regiao = $row_saida['id_regiao'];
    $regiao_prestador = $row_saida['prestador_id_regiao'];
    $projeto_prestador = $row_saida['prestador_id_projeto'];

    $regiao_fornecedor = $row_saida['fornecedor_id_regiao'];
    $projeto_fornecedor = $row_saida['fornecedor_id_projeto'];
    $fornecedor_id = $row_saida['id_fornecedor'];
    $prestador_id = $row_saida['id_prestador'];


    if ($row_saida['tipo_empresa'] == 1) {
        $regiao_prest_forn = $regiao_prestador;
        $projeto_prest_forn = $projeto_prestador;
    } else {
        $regiao_prest_forn = $regiao_fornecedor;
        $projeto_prest_forn = $projeto_fornecedor;
    }

    ////MOSTRAR CÓDIGO DE BARRA
    $style1 = ($row_saida['tipo_boleto'] == 1) ? '' : 'display:none;'; //campo_codigo_consumo
    $style2 = ($row_saida['tipo_boleto'] == 2) ? '' : 'display:none;'; //campo_codigo_gerais 
    /////FORMATANDO PARA EXIBIR DOS CÓDIGOS DE BARRA
    $cod_barra_consumo[] = substr($row_saida['cod_barra_consumo'], 0, 11);
    $cod_barra_consumo[] = substr($row_saida['cod_barra_consumo'], 11, 1);
    $cod_barra_consumo[] = substr($row_saida['cod_barra_consumo'], 12, 11);
    $cod_barra_consumo[] = substr($row_saida['cod_barra_consumo'], 23, 1);
    $cod_barra_consumo[] = substr($row_saida['cod_barra_consumo'], 24, 11);
    $cod_barra_consumo[] = substr($row_saida['cod_barra_consumo'], 35, 1);
    $cod_barra_consumo[] = substr($row_saida['cod_barra_consumo'], 36, 11);
    $cod_barra_consumo[] = substr($row_saida['cod_barra_consumo'], 47, 1);

    $cod_barra_gerais[] = substr($row_saida['cod_barra_gerais'], 0, 5);
    $cod_barra_gerais[] = substr($row_saida['cod_barra_gerais'], 5, 5);
    $cod_barra_gerais[] = substr($row_saida['cod_barra_gerais'], 10, 5);
    $cod_barra_gerais[] = substr($row_saida['cod_barra_gerais'], 15, 6);
    $cod_barra_gerais[] = substr($row_saida['cod_barra_gerais'], 21, 5);
    $cod_barra_gerais[] = substr($row_saida['cod_barra_gerais'], 26, 6);
    $cod_barra_gerais[] = substr($row_saida['cod_barra_gerais'], 32, 1);
    $cod_barra_gerais[] = substr($row_saida['cod_barra_gerais'], 33, 14);

    ////CONFIG
    switch ($saida_grupo) {

        case 1:
        case 3:
        case 4:
            $mostrar_tipo = 1;
            $mostrar_nome = 1;
            break;

        case 10:
        
        case 40:
        case 50:
        case 60:
        case 70:
            $mostrar_subgrupo = 1;
            $mostrar_tipo = 1;
            $mostrar_nome = 1;
            break;
        case 30:
        case 80:
        case 2:
       case 20:
            $mostrar_prestador = 1;
            break;
    }
} else {
    $style1 = 'display:none;'; //campo_codigo_consumo
    $style2 = 'display:none;'; //campo_codigo_gerais 
    $regiao = $_GET['regiao'];
}
////////////////////////////////////////////////////////
//ENCRIPTOGRAFANDO
$linkEnc = encrypt($regiao);
$linkEnc = str_replace("+", "--", $linkEnc);
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <title>Intranet - Financeiro</title>
        <style type="text/css">
            .tabela{
                font-size: 10px;   
            }
            .tabela tr{ height: 35px;}

            a, a:link, a:active{
                margin:0px;
                font-family: Arial, Helvetica, sans-serif;
                font-size: 12px;
                color: #333;
                text-decoration: underline;
            }

            .anexos{
                width:500px;
                overflow:scroll;
            }
            .anexos ul{
                padding:0px;
                margin:0px;
                overflow:hidden;
            }
            .anexos ul li{
                float: left;
                list-style-type: none;
                margin: 0px 5px;
                border:solid #999 1px;
            }
            .anexos ul li.excluir {
                margin-left:-20px;
                padding:3px;
                color:#FFF;
                background-color:#D90000;
                font-weight:bold;
                cursor: pointer;
            }

            .anexos ul li.excluir_andamento {
                margin-left:-20px;
                padding:3px;
                color:#FFF;
                background-color:#D90000;
                font-weight:bold;
                cursor: pointer;
            }
            .anexos ul li.excluir_pg {
                margin-left:-20px;
                padding:3px;
                color:#FFF;
                background-color:#D90000;
                font-weight:bold;
                cursor: pointer;
            }

            #progressbar{
                overflow:auto;
                height:120px;
                border:solid #CCC 1px;
                background-color:#FFF;
                width: 330px;
                display: none;
            }

            .pdf{
                width:100px;
                height: 100px;
                background-image: url('image/File-pdf-32.png');
                display:block;
            }

        </style>
        <!-- highslide -->
        <link rel="stylesheet" type="text/css" href="../js/highslide.css" />
        <script type="text/javascript" src="../js/highslide-with-html.js"></script>
        <script type="text/javascript" >
            hs.graphicsDir = '../images-box/graphics/';
            hs.outlineType = 'rounded-white';
            hs.showCredits = false;
            hs.wrapperClassName = 'draggable-header';
        </script>
        <!-- highslide -->
        <link href="style/estrutura.css" rel="stylesheet" type="text/css">
        <link href="../uploadfy/css/default.css" rel="stylesheet" type="text/css" />
        <link href="../jquery/datepicker-lite/jquery-ui-1.8.4.custom.css" rel="stylesheet" type="text/css" />
        <link href="../uploadfy/css/uploadify.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="../jquery/jquery-1.4.2.min.js"></script>
        <script type="text/javascript" src="../uploadfy/scripts/swfobject.js"></script>
        <script language="javascript" type="text/javascript" src="../uploadfy/scripts/jquery.uploadify.v2.1.0.js"></script>
        <script type="text/javascript" src="../jquery/datepicker-lite/jquery-ui-1.8.4.custom.min.js"></script>
        <script type="text/javascript" src="../jquery/mascara/jquery.maskedinput-1.2.2.js"></script>
        <script type="text/javascript" src="../js/formatavalor.js"></script>
        <script type="text/javascript" src="cadastro_saida.js"></script>
        <script>
            $(function(){
                $("#alteraIDnome").click(function(){
                    $("#trEdita").css('display','');
                });
    
                $("#id_nomeSalvar").click(function(){
                    $.post("", {id:$("#id_nomeedita").val(),nome:$("#id_nome_nome").val(),cpf:$("#id_nome_cpfcnpj").val(),method:"editanome"},function(data){
                        if(data.status){
                            alert("nome alterado com sucesso!");
                        }else{
                            alert("Erro ao alterar o nome, entre em contato com um analista!");
                        }
                        $("#trEdita").css('display','none');
                    },"json");
                });
            })
        </script>
    </head>

    <body>
        <div id="loading">
            <img src="image/ajax-loader.gif" width="32" height="32" />
            Carregando...
        </div>

        <div id="base">
            <?php if (!isset($_REQUEST['rel'])) { ?>
                <a href="index.php?enc=<?php echo $linkEnc; ?>" style="color: #069; float:left;"> <img src="../img_menu_principal/voltar.png" title="VOLTAR" /></a>
<?php } ?>

            <span style="clear:left;"></span>
            <br><br>	

            <form method="post" action="" name="Form" id="Form">
                <div>
                    <img src="../imagens/logomaster<?php echo $row_master['id_master']; ?>.gif" width="150" heigth="90"/>
                    <div>
                        <h3>
<?php if (isset($id_saida)) { ?> Saída: <?php echo $id_saida; ?> - <?php echo $row_saida['nome'];
} ?>
                        </h3>
                    </div>
                </div>  
                <!----CAMPOS VINDOS DO CADASRO DA SAIDA PARA EDIÇÃO--->
                <input type="hidden" name="saida_subgrupo" id="saida_subgrupo" value="<?php echo $row_saida['entradaesaida_subgrupo_id']; ?>"/>
                <input type="hidden" name="saida_tipo" id="saida_tipo" value="<?php echo $row_saida['tipo']; ?>"/>
                <input type="hidden" name="prestador_pg_regiao" id="prestador_pg_regiao" value="<?php echo $regiao_prestador; ?>"/>
                <input type="hidden" name="prestador_pg_projeto" id="prestador_pg_projeto" value="<?php echo $projeto_prestador; ?>"/>
                <input type="hidden" name="fornecedor_pg_regiao" id="fornecedor_pg_regiao" value="<?php echo $regiao_fornecedor; ?>"/>
                <input type="hidden" name="fornecedor_pg_projeto" id="fornecedor_pg_projeto" value="<?php echo $projeto_fornecedor; ?>"/>
                <input type="hidden" name="prestador_id" id="prestador_id" value="<?php echo $prestador_id; ?>"/>
                <input type="hidden" name="fornecedor_id" id="fornecedor_id" value="<?php echo $fornecedor_id; ?>"/>
                <input type="hidden" name="id_nome" id="id_nome" value="<?php echo $row_saida['id_nome']; ?>"/>
                <!----------------------------------------------------->



                <fieldset class="Cadastro">

                    <legend><?php if (isset($id_saida)) {
    echo 'EDITAR SAÍDA';
} else {
    echo 'CADASTRAR SAÍDA';
} ?></legend> 

                    <table class="tabela" style="<?php echo $editavel; ?>">
                        <tr>
                            <td>REGIÃO:</td>
                            <td>
                                <select name="regiao_banco" id="regiao_banco">
<?php
$q_reg = mysql_query("SELECT * FROM regioes where id_regiao = $regiao");
$row_reg = mysql_fetch_assoc($q_reg);

$REGIOES->Preenhe_select_por_master($row_reg['id_master'], $regiao);
?>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td>PROJETO:</td>
                            <td> 
                                <select name="projeto" class="validate[required]" id="projeto">
                                    <?php
                                    $query_projeto = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$regiao' AND status_reg = '1'");
                                    while ($row_projeto = mysql_fetch_assoc($query_projeto)) {
                                        $selected_pro = ($row_saida['id_projeto'] == $row_projeto['id_projeto']) ? 'selected="selected"' : '';
                                        print '<option value="' . $row_projeto['id_projeto'] . '"' . $selected_pro . ' >' . $row_projeto['id_projeto'] . ' - ' . $row_projeto['nome'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>CONTA PARA DÉBITO:</td>
                            <td>             
                                <select name="banco" class="validate[required]" id="banco">
                                    <?php
                                    $result_banco = mysql_query("SELECT * FROM bancos WHERE id_regiao = '$regiao' and interno = '1' AND status_reg = '1' ORDER BY nome ASC");
                                    while ($row_banco = mysql_fetch_assoc($result_banco)):
                                        $selected = ($row_saida['id_banco'] == $row_banco['id_banco']) ? 'selected="selected"' : '';
                                        print '<option value="' . $row_banco['id_banco'] . '"' . $selected . ' >' . $row_banco['id_banco'] . ' - ' . $row_banco['nome'] . '</option>';
                                    endwhile;
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>GRUPO:</td>
                            <td>
                                <?php
                                ////////////////////////////////////////////
                                ////CONDIÇÃO PARA O SORRINDO E PARA A FAHJEL
                                ////////////////////////////////////////////
                                $qr_verifica = mysql_query("SELECT * FROM funcionario as a
                                INNER JOIN regioes as b
                                ON a.id_regiao = b.id_regiao
                                WHERE a.id_funcionario = '$_COOKIE[logado]'");
                                $row_verifica = mysql_fetch_assoc($qr_verifica);

                                if ($row_verifica['id_master'] == 1 or $row_verifica['id_master'] == 4) {
                                    $grupo = array('1' => 'Folha', '2' => 'Reserva', '3' => 'Taxa administrativa', '4' => 'Tranferências ISPV', '10' => 'PESSOAL', '20' => 'MATERIAL DE CONSUMO', '30' => 'SERVIÇOS DE TERCEIROS', '40' => 'TAXAS / IMPOSTOS / CONTRIBUIÇÕES', '50' => 'SERVIÇOS PÚBLICOS', '60' => 'DESPESAS BANCÁRIAS', '70' => 'OUTRAS DESPESAS OPERACIONAIS', '80' => 'INVESTIMENTOS');
                                } else {
                                    $grupo = array('10' => 'PESSOAL', '20' => 'MATERIAL DE CONSUMO', '30' => 'SERVIÇOS DE TERCEIROS', '40' => 'TAXAS / IMPOSTOS / CONTRIBUIÇÕES', '50' => 'SERVIÇOS PÚBLICOS', '60' => 'DESPESAS BANCÁRIAS', '70' => 'OUTRAS DESPESAS OPERACIONAIS', '80' => 'INVESTIMENTOS');
                                }
                                ?>  

                                <select name="grupo" id="grupo" class="validate[required]">

                                    <option value="" selected>Selecione</option>
                                    <?php
                                    foreach ($grupo as $chave => $valor):

                                        $selected = ($chave == $saida_grupo ) ? 'selected="selected"' : '';
                                        print '<option value="' . $chave . '" ' . $selected . '>' . $chave . ' - ' . $valor . '</option>';
                                    endforeach;
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr id="campo_subgrupo" <?php echo ($mostrar_subgrupo == 1) ? '' : 'style="display:none;"'; ?>>
                            <td>SUBGRUPO</td>
                            <td>
                                <select name="subgrupo" nome="subgrupo" id="subgrupo" class="validate[required]">
                                    <option value="">Selecione um subgrupo</option>
                                    <?php
                                    $qr_subgrupo = mysql_query("SELECT * FROM  `entradaesaida_subgrupo` WHERE entradaesaida_grupo = '$saida_grupo' ORDER BY nome");
                                    while ($row_subgrupo = mysql_fetch_assoc($qr_subgrupo)) {

                                        $selected = ($row_subgrupo['id'] == $row_saida['entradaesaida_subgrupo_id']) ? 'selected="selected"' : '';
                                        echo '<option value="' . $row_subgrupo['id'] . '" ' . $selected . '>' . $row_subgrupo['id_subgrupo'] . ' - ' . $row_subgrupo['nome'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>TIPO:</td>
                            <td>
                              
                                <select name="tipo" nome="tipo" id="tipo" class="validate[required]">
                                    <option value="">Selecione um tipo</option>
                                    <?php
                                    if ($saida_grupo <= 4) {
                                        $qr_tipo = mysql_query(" SELECT id_entradasaida, cod, nome FROM  entradaesaida WHERE grupo = '$saida_grupo' ");
                                    } else {
                                        $qr_subgrupo = mysql_query("SELECT * FROM  `entradaesaida_subgrupo` WHERE id = '$row_saida[entradaesaida_subgrupo_id]' ORDER BY nome");
                                        $row_subgrupo = mysql_fetch_assoc($qr_subgrupo);

                                        if ($row_saida['entradaesaida_subgrupo_id'] != '') {
                                            $qr_tipo = mysql_query("  SELECT id_entradasaida, cod, nome   FROM  entradaesaida WHERE cod LIKE '$row_subgrupo[id_subgrupo]%'");
                                        }
                                    }
                                             
                                    while ($row_tipo = mysql_fetch_assoc($qr_tipo)) {
                                        
                                        
                                        
                                        if ($row_func['id_master'] >= 6 and $row['id_entradasaida'] == 265) {
                                            continue;
                                        }

                                        $selected = ($row_tipo['id_entradasaida'] == $row_saida['tipo']) ? 'selected="selected"' : '';
                                        echo '<option value="' . $row_tipo['id_entradasaida'] . '" ' . $selected . '> ' . $row_tipo['cod'] . ' - ' . $row_tipo['nome'] . '</option>';
                                    }
                                    ?>
                                </select>
                           
                            </td>
                        </tr>

                        <tr class="interno" <?php echo ($mostrar_prestador == 1 ) ? '' : 'style="display:none;"'; ?>>
                            <td colspan="2">PRESTADOR DE SERVIÇO</td>
                        </tr>

                        <tr class="interno" <?php echo ($mostrar_prestador == 1) ? '' : 'style="display:none;"'; ?>>
                            <td>REGIÃO:</td>
                            <td>                          
                                <select name="regiao-prestador"  id="regiao-prestador">
                                    <option value=""> Selecione a região...</option>
                                    <?php
                                    $regioes_prestador = mysql_query("SELECT regioes.id_regiao,regioes.regiao,master.nome FROM regioes INNER JOIN master ON regioes.id_master = master.id_master
                                                                 WHERE regioes.status = '1'  AND master.status = '1'");
                                    while ($rw_regioes_prestador = mysql_fetch_array($regioes_prestador)) {

                                        if ($regiao_prest_forn == $rw_regioes_prestador[0]) {
                                            $selected = "selected=\"selected\"";
                                        } else {
                                            $selected = "";
                                        }

                                        if ($repeat != $rw_regioes_prestador[2]) {
                                            echo '<optgroup label="' . $rw_regioes_prestador[2] . '">';
                                        }

                                        $repeat = $rw_regioes_prestador[2];

                                        echo '<option ' . $selected . ' value="' . $rw_regioes_prestador[0] . '" >' . $rw_regioes_prestador[0] . ' - ' . $rw_regioes_prestador[1] . '</option>';

                                        if ($repeat != $rw_regioes_prestador[2] && !empty($repeat)) {
                                            echo '</optgroup>';
                                        }

                                        $repeat = $rw_regioes_prestador[2];
                                    }
                                    ?>
                                </select>      

                            </td>
                        </tr>
                        <tr class="interno" <?php echo ($mostrar_prestador == 1 ) ? '' : 'style="display:none;"'; ?>>
                            <td>PROJETO:</td>
                            <td>

                                <select name="Projeto-prestador"  id="Projeto-prestador">
                                    <option value=""> Selecione o projeto...</option>
                                    <?php
                                    $qr_projeto = mysql_query("SELECT id_projeto,nome FROM projeto WHERE id_regiao = '$regiao_prest_forn'");
                                    while ($rw_projeto = mysql_fetch_array($qr_projeto)) {
                                        
                                        $selected = ($projeto_prest_forn == $rw_projeto['id_projeto']) ? 'selected="selected"' : '';
                                        echo '<option value="' . $rw_projeto[0] . '" ' . $selected . ' >' . $rw_projeto[0] . ' - ' . $rw_projeto[1] . '</option>';
                                    }
                                    ?>
                                </select> 

                            </td>
                        </tr>
                        <tr class="interno" <?php echo ($mostrar_prestador == 1) ? '' : 'style="display:none;"'; ?>>
                            <td > TIPO DA EMPRESA:</td>
                            <td align="left">                          
                                <input name="tipo_empresa" type="radio" value="1" style="width:20px;" <?php echo ($row_saida['tipo_empresa'] == 1) ? 'checked' : ''; ?>/> PRESTADOR DE SERVIÇO
                                <input name="tipo_empresa" type="radio" value="2" style="width:20px;" <?php echo ($row_saida['tipo_empresa'] == 2) ? 'checked' : ''; ?>/> FORNECEDOR
                            </td>
                        </tr>

                        <tr  class="prestador"  <?php echo ($mostrar_prestador == 1 and $row_saida['tipo_empresa'] == 1) ? '' : 'style="display:none;"'; ?>>
                            <td>PRESTADOR</td>
                            <td>
                                <select name="prestador"  id="interno">
                                    <option value=""  selected="selected">Selecione um nome</option>                        
                                    <?php
                                    $query_prestador = mysql_query("SELECT * FROM  prestadorservico WHERE id_regiao = '$regiao_prestador' AND id_projeto = '$projeto_prestador'AND status = '1'");

                                    while ($row_prestador = mysql_fetch_assoc($query_prestador)) {

                                        $selected = ($row_prestador['id_prestador'] == $row_saida['id_prestador']) ? 'selected="selected"' : '';
                                        echo '<option value="' . $row_prestador['id_prestador'] . '"' . $selected . ' >' . $row_prestador['id_prestador'] . ' - ' . $row_prestador['numero'] . ' - ' . $row_prestador['c_fantasia'] . ' - ' . $row_prestador['c_cnpj'] . '</option>';
                                    }
                                    ?>
                                </select>

                                <div>
                                    <label for="interno">Prestador: </label>
                                    <a href="#" class="novoPrestador" target="_blank">Não esta na lista.</a>
                                </div>


                            </td>
                        </tr>

                        <tr  class="fornecedor"  <?php echo ($mostrar_prestador == 1 and $row_saida['tipo_empresa'] == 2) ? '' : 'style="display:none;"'; ?>>
                            <td> FORNECEDOR</td>
                            <td>
                                <select name="fornecedor"  id="fornecedor">
                                    <option value="">Selecione um fornecedor</option>
                                    <?php
                                    $query_fornecedor = mysql_query("SELECT * FROM  fornecedores WHERE id_regiao = '$regiao' AND status = '1'") or die(mysql_error());
                                    while ($row_fornecedor = mysql_fetch_assoc($query_fornecedor)) {

                                        $selected = ($row_fornecedor['id_fornecedor'] == $row_saida['id_fornecedor']) ? 'selected="selected"' : '';
                                        echo '<option value="' . $row_fornecedor['id_fornecedor'] . '" ' . $selected . ' >' . $row_fornecedor['id_fornecedor'] . ' - ' . $row_fornecedor['nome'] . '</option>';
                                    }
                                    ?>
                                </select>

                            </td>
                        </tr>

                        <tr  class="nomes-cad"  <?php echo ($mostrar_nome == 1 ) ? '' : 'style="display:none;"'; ?>>
                            <td>NOME</td>
                            <td>
                                <?php
                                $options_nomes[''] = 'Selecione...';
                                $qr_nomes = mysql_query("SELECT * FROM  `entradaesaida_nomes` WHERE id_entradasaida = '$row_saida[tipo]' ORDER BY nome");
                                while ($row_nomes = mysql_fetch_assoc($qr_nomes)):
                                    if($row_nomes['id_nome']==$row_saida['id_nome']){
                                        $preparaNome = $row_nomes['nome'];
                                        $preparaCpf = $row_nomes['cpfcnpj'];
                                    }
                                    $options_nomes[$row_nomes['id_nome']] = $row_nomes['nome']." - ".$row_nomes['cpfcnpj'];
                                endwhile;

                                $attr_nomes = array("id" => 'nome', "name" => 'nome');



                                echo montaSelect($options_nomes, $row_saida['id_nome'], $attr_nomes);
                                ?>
                                <a href="#" class="highslide" onClick="return false"> Adicionar </a><?php if(!empty($row_saida['id_nome'])){ ?> - <a href="javascript:;" id="alteraIDnome" data-key="<?php echo $row_saida['id_nome']?>">Editar</a><?php } ?>
                            </td>
                        </tr>
                        <tr id="trEdita" style="display:none"><td></td><td><input type="hidden" name="id_nomeedita" id="id_nomeedita" value="<?php echo $row_saida['id_nome']?>"/>Nome: <input type="text" name="id_nome_nome" id="id_nome_nome" value="<?php echo $preparaNome ?>" style="width: 200px" /> Cpf/Cnpj:<input type="text" name="id_nome_cpfcnpj" id="id_nome_cpfcnpj" value="<?php echo $preparaCpf ?>" size="14" style="width: 100px" > <a href="javascript:;" id="id_nomeSalvar">Salvar</a></td></tr>
                        <tr>
                            <td>DESCRIÇÃO:</td>
                            <td>  <textarea name="descricao"  id="descricao"  class="descricao" cols="40"/><?php echo $row_saida['especifica']; ?></textarea></td>
                        </tr>

                        <tr>
                            <?php if (isset($id_saida)) { ?>      
                                <td>VALOR ADICIONAL:</td>
                                <td> R$ <?php echo number_format($row_saida['adicional'], 2, ',', '.') ?></td>
                            <?php } else { ?>
                                <td>VALOR ADICIONAL:</td>
                                <td><input name="adicional" type="hidden" id="adicional" onKeyDown="FormataValor(this,event,17,2)" value="<?php echo $row_saida['adicional'] ?>"/></td>
                            <?php } ?>
                        </tr>

                        <tr>
                            <td>REFERÊNCIA</td>
                            <td>
                                <select name="referencia" id="referencia">   
                                    <option value="">Selecione...</option>
                                    <?php
                                    $qr_referencia = mysql_query("SELECT * FROM tipos_referencia WHERE status = 1");
                                    while ($row_ref = mysql_fetch_assoc($qr_referencia)):

                                        $selected = ($row_ref['id_referencia'] == $row_saida['id_referencia']) ? 'selected="seleceted"' : '';
                                        echo '<option value="' . $row_ref['id_referencia'] . '"' . $selected . '>' . $row_ref['descricao'] . '</option>';
                                    endwhile;
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr id="campo_bens" <?php echo ($row_saida['id_referencia'] == 2 ) ? '' : 'style="display:none;"'; ?>>
                            <td>TIPOS DE BENS:</td>
                            <td>
                                <select name="bens"id="bens">
                                    <option value="">Selecione...</option>
                                    <option value=""></option>
                                    <?php
                                    $qr_bens = mysql_query("SELECT * FROM tipos_bens WHERE status = 1");
                                    while ($row_bens = mysql_fetch_assoc($qr_bens)):

                                        $selected = ($row_bens['id_bens'] == $row_saida['id_bens']) ? 'selected="selected"' : '';
                                        echo '<option value="' . $row_bens['id_bens'] . '" ' . $selected . '>' . $row_bens['descricao'] . '</option>';
                                    endwhile;
                                    ?>                 
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>TIPO PAGAMENTO:</td>
                            <td>
                                <select name="tipo_pagamento"  id="tipo_pagamento" >
                                    <option value="">Selecione...</option>   
                                    <option value=""></option>
                                    <?php
                                    $qr_tipo_pg = mysql_query("SELECT * FROM tipos_pag_saida");
                                    while ($row_tp_pg = mysql_fetch_assoc($qr_tipo_pg)):

                                        $selected = ($row_tp_pg['id_tipo_pag'] == $row_saida['id_tipo_pag_saida']) ? 'selected="selected"' : '';
                                        echo '<option value="' . $row_tp_pg['id_tipo_pag'] . '" ' . $selected . '>' . sprintf('%02d', $row_tp_pg['id_tipo_pag']) . ' - ' . $row_tp_pg['descricao'] . '</option> ';

                                    endwhile;
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr id="campo_boleto"  <?php echo ( $row_saida['id_tipo_pag_saida'] == 1) ? '' : 'style="display:none;"'; ?> >
                            <td>TIPO BOLETO</td>
                            <td>
                                <select name="tipo_boleto"  id="tipo_boleto" >
                                    <option value="">Selecione...</option>   
                                    <option value=""></option>
                                    <?php
                                    $qr_tipo_boleto = mysql_query("SELECT * FROM tipo_boleto");
                                    while ($row_tp_boleto = mysql_fetch_assoc($qr_tipo_boleto)):
                                        $selected = ($row_tp_boleto['id_tipo'] == $row_saida['tipo_boleto']) ? 'selected="selected"' : '';
                                        echo '<option value="' . $row_tp_boleto['id_tipo'] . '" ' . $selected . ' >' . $row_tp_boleto['nome'] . '</option> ';

                                    endwhile;
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr id="campo_nosso_numero" style="<?php echo $style2; ?>">
                            <td>NOSSO NÚMERO:</td>
                            <td> <input name="nosso_numero" type="text" id="nosso_numero" value="<?php echo $row_saida['nosso_numero']; ?>"/>  </td>
                        </tr>


                        <tr  class="campo_codigo_consumo" style="<?php echo $style1; ?>">
                            <td colspan="2">LINHA DIGITÁVEL/CÓDIGO DE BARRA</td>
                        </tr>
                        <tr class="campo_codigo_consumo" style="<?php echo $style1; ?>">
                            <td colspan="2">
                                <input name="codigo_barra_consumo[]" type="text" id="codigo_barra_consumo1" style="width:100px;" maxlength="11" value="<?php echo $cod_barra_consumo[0]; ?>"/>-
                                <input name="codigo_barra_consumo[]" type="text" id="codigo_barra_consumo2" style="width:30px;" maxlength="1" value="<?php echo $cod_barra_consumo[1]; ?>"/>
                                <input name="codigo_barra_consumo[]" type="text" id="codigo_barra_consumo3" style="width:100px;" maxlength="11" value="<?php echo $cod_barra_consumo[2]; ?>"/>-
                                <input name="codigo_barra_consumo[]" type="text" id="codigo_barra_consumo4" style="width:30px;" maxlength="1" value="<?php echo $cod_barra_consumo[3]; ?>"/>
                                <input name="codigo_barra_consumo[]" type="text" id="codigo_barra_consumo5" style="width:100px;" maxlength="11" value="<?php echo $cod_barra_consumo[4]; ?>"/>-
                                <input name="codigo_barra_consumo[]" type="text" id="codigo_barra_consumo6" style="width:30px;" maxlength="1" value="<?php echo $cod_barra_consumo[5]; ?>"/>
                                <input name="codigo_barra_consumo[]" type="text" id="codigo_barra_consumo7" style="width:100px;" maxlength="11" value="<?php echo $cod_barra_consumo[6]; ?>"/>-
                                <input name="codigo_barra_consumo[]" type="text" id="codigo_barra_consumo8" style="width:30px;" maxlength="1" value="<?php echo $cod_barra_consumo[7]; ?>"/>                                    
                            </td>
                        </tr>


                        <tr class="campo_codigo_gerais" style="<?php echo $style2; ?>">
                            <td colspan="2"> LINHA DIGITÁVEL/CÓDIGO DE BARRA</td>
                        </tr>

                        <tr class="campo_codigo_gerais" style="<?php echo $style2; ?>">
                            <td colspan="2">
                                <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais1" style="width:50px;" value="<?php echo $cod_barra_gerais[0]; ?>"/>.
                                <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais2" style="width:50px;" value="<?php echo $cod_barra_gerais[1]; ?>"/>.
                                <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais3" style="width:50px;" value="<?php echo $cod_barra_gerais[2]; ?>"/>
                                <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais4" style="width:60px;" value="<?php echo $cod_barra_gerais[3]; ?>"/>.
                                <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais5" style="width:50px;" value="<?php echo $cod_barra_gerais[4]; ?>"/>
                                <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais6" style="width:60px;" value="<?php echo $cod_barra_gerais[5]; ?>"/>.
                                <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais7" style="width:30px;" value="<?php echo $cod_barra_gerais[6]; ?>"/>
                                <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais8" style="width:130px;" value="<?php echo $cod_barra_gerais[7]; ?>"/>    
                            </td>
                        </tr>

                        <tr class="n_documento">
                            <td>NÚMERO DO DOCUMENTO:</td>
                            <td><input name="n_documento" id="n_documento" value="<?php echo $row_saida['n_documento'] ?>"</td>
                        </tr>

                        <tr <?php echo ($row_saida['id_tipo_pag_saida'] == 3) ? '' : 'style="display:none;"'; ?> class="link_nfe">
                            <td>LINK DA NF-E:</td>
                            <td><input name="link_nfe" id="link_nfe" value="<?php echo $row_saida['link_nfe'] ?>"</td>
                        </tr>
                      
                        
                        <tr>
                            <td>DATA DE EMISSÃO DA NF:</td>
                            <td><input type="text" name="dt_emissao_nf" id="dt_emissao_nf" value="<?php echo $row_saida['dt_emissao_nf_br']; ?>"</td>
                        </tr>
                        
                         <tr>
                            <td>TIPO DE RETENÇÃO: </td>
                            <td>
                                <select name="tipo_nf" id="tipo_nf">
                                    <?php 
                                    $tipo_nf = array('' => 'Selecione...', 1 => 'IR', 2 =>'ISS', 3 =>'PIS/COFINS/CSLL', 4 => 'INSS');
                                    foreach($tipo_nf  as $chave => $valor){                                        
                                        $selected = ($chave == $row_saida['tipo_nf'])?"selected='selected'":'';
                                        
                                        echo "<option value='$chave' $selected>$chave -  $valor</option>";
                                    }                                    
                                    ?>                                  
                                </select>
                            </td>
                        </tr>

                      
                        <tr>
                            <?php if (isset($id_saida)) { ?>      
                                <td>VALOR LÍQUIDO:</td>
                                <td> R$ <?php echo number_format(str_replace(",", ".", $row_saida['valor']), 2, ',', '.'); ?></td>
<?php } else { ?>
                                <td>VALOR LÍQUIDO:</td>
                                <td> <input name="real" type="text" class="validate[required]" id="real" onKeyDown="FormataValor(this,event,17,2)" value=""/></td>
<?php } ?>
                        </tr>
                        <tr>
                            <td>DATA PARA PAGAMENTO:</td>
                            <td><input type="text" name="data" id="data" class="date" value="<?php echo implode('/', array_reverse(explode('-', $row_saida['data_vencimento']))); ?>"/></td>
                        </tr>
                      
                         <tr>
                             <td colspan="2"><strong>COMPETÊNCIA</strong></td>
                        </tr>
                        <tr>
                            <td>MÊS:</td>
                            <td>
                                <select name="mes_competencia">
                                 <?php
                                 $qr_mes = mysql_query("SELECT * FROM ano_meses");
                                 while($row_meses = mysql_fetch_assoc($qr_mes)){     
                                     
                                      $selected = ($row_meses['num_mes'] == $row_saida['mes_competencia'])? 'selected="selected"' : '';
                                      
                                     echo '<option value="'.$row_meses['num_mes'].'"'.$selected.'>'.$row_meses['num_mes'].' - '.$row_meses['nome_mes'].'</option>';
                                 }
                                 ?>   
                                </select>
                            </td>
                        </tr> 
                         <tr>
                            <td>ANO:</td>
                            <td>
                                <select name="ano_competencia">
                                 <?php
                               for($i=2010;$i<=2013;$i++) {  
                                   
                                   $selected = ($i == $row_saida['ano_competencia'])? 'selected="selected"' : '';
                                     echo '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
                                 }
                                 ?>   
                                </select>
                            </td>
                        </tr> 
                           
                         <tr>
                            <td>VALOR BRUTO:</td>
                            <td><input type="text" name="valor_bruto" id="valor_bruto" onKeyDown="FormataValor(this,event,17,2)" value="<?php echo number_format($row_saida['valor_bruto'],2,',','.');?>"/></td>
                        </tr>
                      
                    </table>

                    <table class="tabela">
<?php if (isset($id_saida) and $row_saida['status'] == 2) { ?>
                            <tr>
                                <td>ESTORNO</td>
                                <td align="left">
                                    <select name="estorno">
                                        <option value="">Selecione...</option>
                                        <option value=""></option>
                                        <option value="1" <?php echo ($row_saida['estorno'] == 1) ? 'selected="selected"' : ''; ?>>INTEGRAL</option>
                                        <option value="2" <?php echo ($row_saida['estorno'] == 2) ? 'selected="selected"' : ''; ?>>PARCIAL</option>
                                    </select>
                                </td>
                            </tr>
                            <tr class="valor_estorno_parcial" style="display:<?php echo ($row_saida['estorno'] == 2) ? '' : 'none'; ?>">
                                <td>Valor do estorno:</td> 
                                <td align="left"><input type="text" name="valor_estorno_parcial" value="<?php echo number_format($row_saida['valor_estorno_parcial'], 2, ',', '.'); ?>" id="valor_estorno_parcial" onKeyDown="FormataValor(this,event,17,2)"/>  </td>

                            </tr>


                            <tr class="descricao_estorno" style="display:<?php echo ($row_saida['estorno'] != 0) ? '' : 'none'; ?>">
                                <td valign="top">DESCRIÇÃO DO ESTORNO:</td>
                                <td>
                                    <textarea name="descricao_estorno" cols="30" rows="5" ><?php echo trim($row_saida['estorno_obs']); ?></textarea>
                                </td>
                            </tr>
<?php } ?>
                    </table>


                    <br>           

                    <div>
                        <div class="anexos">
                            <h4> ANEXOS DA SAÍDA</h4>
                            <ul>
                                <?php
                                if (!empty($row_saida['id_saida'])) {
                                    $qr_saida_files = mysql_query("SELECT * FROM saida_files WHERE id_saida = '$row_saida[id_saida]'");
                                    $num_saida_files = mysql_num_rows($qr_saida_files);
                                    if (!empty($num_saida_files)):
                                        while ($row_saida_files = mysql_fetch_assoc($qr_saida_files)):
                                            $link_encryptado = encrypt('ID=' . $row_saida_files['id_saida'] . '&tipo=0');
                                            echo "<li>";
                                            if ($row_saida_files['tipo_saida_file'] == '.pdf') {

                                                echo "
                                                                                                <a href=\"view/comprovantes.php?$link_encryptado\" target=\"_blank\" class=\"pdf\">
                                                                                                <img src=\"image/File-pdf-32.png\" border=\"0\" width=\"100\" height=\"100\" />
                                                                                                </a>
                                                                                                ";
                                            } else {
                                                echo "<a href=\"view/comprovantes.php?$link_encryptado\" target=\"_blank\"><img src=\"http://" . $_SERVER['HTTP_HOST'] . "/intranet/classes/img.php?foto=../comprovantes/$row_saida_files[id_saida_file].$row_saida_files[id_saida]$row_saida_files[tipo_saida_file]&w=100&h=100\"/></a>";
                                            }
                                            echo "</li>";
                                            echo "<li value=\"$row_saida_files[id_saida_file]\" class=\"excluir\" rel=\"anexo\">X</li>";
                                        endwhile;

                                    endif;
                                }
                                ?>
                            </ul>

                        </div>
                    </div>


                    <div id="barra_upload"></div>
                    <input type="file" id="FileUp"/>
<?php
if (isset($id_saida) and $row_saida['status'] == 2) {
    ?>     

                        <div>
                            <div class="anexos">
                                <h4>COMPROVANTES DE PAGAMENTO</h4>        
                                <ul>
                                    <?php
                                    $qr_saida_files_pg = mysql_query("SELECT * FROM saida_files_pg WHERE id_saida = '$row_saida[id_saida]'");
                                    $num_saida_files = mysql_num_rows($qr_saida_files_pg);


                                    if (!empty($num_saida_files)):
                                        while ($row_saida_files_pg = mysql_fetch_assoc($qr_saida_files_pg)):



                                            $link_encryptado = encrypt('ID=' . $row_saida_files_pg['id_saida'] . '&tipo=1');
                                            echo "<li>";
                                            if ($row_saida_files_pg['tipo_pg'] == '.pdf') {


                                                echo "
								<a href=\"../comprovantes/$row_saida_files_pg[id_pg].$row_saida_files_pg[id_saida]_pg$row_saida_files_pg[tipo_pg]\" target=\"_blank\" class=\"pdf\">
								<img src=\"image/File-pdf-32.png\" border=\"0\" width=\"100\" height=\"100\" />
								</a>
								";
                                            } else {

                                                echo "<a href=\"../comprovantes/$row_saida_files_pg[id_pg].$row_saida_files_pg[id_saida]_pg$row_saida_files_pg[tipo_pg]\" target=\"_blank\"><img src=\"http://" . $_SERVER['HTTP_HOST'] . "/intranet/classes/img.php?foto=../comprovantes/$row_saida_files_pg[id_pg].$row_saida_files_pg[id_saida]_pg$row_saida_files_pg[tipo_pg]&w=100&h=100\"/></a>";
                                            }
                                            echo "</li>";
                                            echo "<li value=\"$row_saida_files_pg[id_pg]\" class=\"excluir_pg\" rel=\"comprovante\">X</li>";
                                        endwhile;

                                    endif;
                                    ?>
                                </ul>

                            </div>
                        </div>

                        <div id="barra_upload_pg"></div>

                        <input type="file" id="FileUp_pg"/>
                        <?php
                    }
                    ?>      



                        <?php if (isset($id_saida)) { ?>       
                        <center>
                            <input type="submit" name="atualizar" id="button" class="submit-go" value="Atualizar"> 
                            <input type="hidden" name="atualizar" value="atualizar">
                            <input type="hidden" name="nome_saida" value="<?php echo $row_saida['nome'] ?>">
                            <input type="hidden" name="id_saida" value="<?php echo $id_saida; ?>" id="id_saida">
<?php } else { ?>              

                            <input type="submit" class="submit-go" name="cadastrar" value="Cadastrar"/>
                            <input type="hidden" name="cadastrar" value="cadastrar">

<?php } ?>                 	
                        <input type="hidden" name="enc" id="link_enc" value="<?php echo $linkEnc; ?>"/>
                        <input type="hidden" name="regiao" value="<?= $regiao ?>" />
                        <input type="hidden" name="logado" value="<?= $id_user ?>" />
                    </center>            

                </fieldset> 
            </form>

        </div> 

        <div style="display:none">

            <div id="cadastro_nomes">
                <div style="height:20px; border-bottom: 1px solid silver"> 
                    <a href="#" onClick="return hs.close(this)" class="control">Fechar</a> 
                </div> 

                <form name="form2" method="post"  id="form2" action="">

                    <table width="0" border="0" cellspacing="0" cellpadding="2">
                        <tr>
                            <td align="right">NOME:</td>
                            <td>
                                <input type="text" name="nome" id="nome"></td>
                        </tr>

                        <tr>
                            <td align="right">CNPJ/CPF:</td>
                            <td> <input type="text" name="cpf" id="cpf"></td>
                        </tr>

                        <tr>
                            <td align="right">DESCRICAO:</td>
                            <td>  <input type="text" name="descricao" id="descricao"></td>  </tr>
                        <tr>

                            <td colspan="2" align="center">
                                <input type="hidden" name="tipo" id="tipo2">      
                                <input type="submit" name="button" id="button" class="submit-go" value="Cadastrar">
                            </td>
                        </tr>

                    </table>
                </form>
            </div>  

        </div>
    </body>
</html>