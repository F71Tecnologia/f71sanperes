<?php
include "../../conn.php";
include "../../wfunction.php";

$mes = $_GET['mes'];
$ano = $_GET['ano'];
$clt = $_GET['id_clt'];
$tipo_guia = $_GET['tipo']; // 1 - FÉRIAS, 2 - RECISÂO, 3 - MULTA FGTS, 4 - RESCISÃO COMPLEMENTAR, 5 - MULTA FGTS COMPLEMENTAR
$id_rescisao = $_GET['id_rescisao'];
//print_r($id_rescisao);
$id_ferias = $_GET['id_ferias'];
$usuario = carregaUsuario();


$projeto = $_GET['projeto'];
$regiao = $_GET['regiao'];

$query_clt = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$clt'");
$row_clt = mysql_fetch_assoc($query_clt);
$query_mes = mysql_query("SELECT nome_mes FROM ano_meses WHERE num_mes = '$mes'");
$mes_nome = @mysql_result($query_mes, 0);


// Montando o nome da saida
if ($tipo_guia == 1) {

    $tipo_id = "156";
    $tipo_nome = "FÉRIAS";
    $subgrupo = 1;
    $qr_ferias = mysql_query("SELECT * FROM rh_ferias WHERE mes = '$mes' AND ano = '$ano' AND id_ferias = '$id_ferias'");
    $query = $qr_ferias;
} elseif ($tipo_guia == 2) {

    $tipo_id = "170";
    $tipo_nome = "RESCISÃO";
    $subgrupo = 3;
    $qr_recisao = mysql_query("SELECT A.*, B.nome as nome_projeto FROM rh_recisao as A 
                                    INNER JOIN projeto as B
                                    ON A.id_projeto = B.id_projeto
                                    WHERE MONTH(A.data_demi) = '$mes' AND YEAR(A.data_demi) = '$ano'  AND A.id_recisao = '$id_rescisao' AND A.status = '1'");
    $query = $qr_recisao;
} elseif ($tipo_guia == 3) {

    $tipo_id = "170";
    $tipo_nome = "MULTA FGTS";
    $subgrupo = 3;
    $qr_recisao = mysql_query("SELECT A.*, B.nome as nome_projeto FROM rh_recisao as A 
                                  INNER JOIN projeto as B
                                  ON A.id_projeto = B.id_projeto
                                  WHERE MONTH(A.data_demi) = '$mes' AND YEAR(A.data_demi) = '$ano'  AND A.id_recisao = '$id_rescisao' AND A.status = '1'");
    $query = $qr_recisao;
} elseif ($tipo_guia == 4) {

    $tipo_id = "170";
    $tipo_nome = "RESCISÃO COMPLEMENTAR";
    $subgrupo = 3;
    $qr_recisao = mysql_query("SELECT A.*, B.nome as nome_projeto
                               FROM rh_recisao as A 
                               INNER JOIN projeto as B ON (A.id_projeto = B.id_projeto)
                               WHERE MONTH(A.data_demi) = '$mes' AND YEAR(A.data_demi) = '$ano'  AND A.id_recisao = '$id_rescisao' AND A.status = '1'");
    $query = $qr_recisao;
} elseif ($tipo_guia == 5) {

    $tipo_id = "170";
    $tipo_nome = "MULTA FGTS COMPLEMENTAR";
    $subgrupo = 3;
    $qr_recisao = mysql_query("SELECT A.*, B.nome as nome_projeto FROM rh_recisao as A 
                                  INNER JOIN projeto as B
                                  ON A.id_projeto = B.id_projeto
                                  WHERE MONTH(A.data_demi) = '$mes' AND YEAR(A.data_demi) = '$ano'  AND A.id_recisao = '$id_rescisao' AND A.status = '1'");
    $query = $qr_recisao;
}

$row = mysql_fetch_assoc($query);
$nome_saida = "$row_clt[id_clt] - $row_clt[nome], $tipo_nome $mes_nome/$ano $row[id_projeto] -  PROJETO: $row[nome_projeto]";


foreach ($_GET as $chave => $valor) {
    $string[] = $chave . '=' . $valor;
}
$link = implode("&", $string);

if (isset($_REQUEST['acao'])) {

    $id_user = $_COOKIE['logado'];
    $folha = $_POST['id_folha'];
    $mes = $_POST['mes'];
    $ano = $_POST['ano'];
    $clt = $_POST['id_clt'];
    $tipo = $_POST['tipo']; // 170 - RESCISÃƒO, 156 - FÃ‰RIAS  
    $nome = $_POST['nome'];
    $id_ferias = $_POST['ferias'];
    $id_rescisao = $_POST['rescisao'];
    $especificacao = "";
    $adicional = 0;
    $tipo_guia = $_POST['tipo_guia'];
    $arquivo = $_FILES['arquivo_multa'];
    $valor = ($tipo_guia != 3 && $tipo_guia != 5) ? str_replace('.', ',', $_POST['valor']) : str_replace('.', '', $_POST['valor_multa']);
    $data = implode("-", array_reverse(explode("/", $_POST['data'])));
    $banco = $_POST['bancos'];
    $subgrupo = $_POST['subgrupo'];
    $id_clt = $_POST['id_clt'];
    $arquivo_2 = $_FILES['arquivo2'];

    $qr_banco = mysql_query("SELECT * FROM bancos WHERE id_banco = '$banco'");
    $row_banco = mysql_fetch_assoc($qr_banco);
    $regiao = $row_banco['id_regiao'];
    $projeto = $row_banco['id_projeto'];

    $qr_clt = mysql_query("SELECT A.*, IF(A.nome_banco = '', B.nome, A.nome_banco ) as banco FROM rh_clt as A
                           LEFT JOIN  bancos as B
                           ON A.banco = B.id_banco
                           WHERE A.id_clt = $id_clt") or die(mysql_error());
    $row_clt = mysql_fetch_assoc($qr_clt);

    $nome .= ' - BANCO: ' . $row_clt['banco'];
    $nome .=' - CONTA: ' . $row_clt['conta'];
    $nome .= ' - AGÊNCIA: ' . $row_clt['agencia'];
    $nome .= ' - CPF: ' . $row_clt['cpf'];


    //CRIANDO A SAÍDA
    $sql = "INSERT INTO saida (id_regiao, id_projeto, id_banco,  id_user, nome, especifica,  tipo,  valor, data_proc, data_vencimento, status,comprovante, nosso_numero, tipo_boleto, cod_barra_gerais, id_referencia, id_tipo_pag_saida, entradaesaida_subgrupo_id, id_clt)
          VALUES ('$regiao', '$projeto', '$banco', '{$_COOKIE['logado']}', '$nome', '$nome', '$tipo', '$valor',NOW(), '$data',  '1', '2', '$nosso_numero', '2', '$cod_barra_gerais','1', '1', '$subgrupo', '$id_clt') ";
    mysql_query($sql) or die(mysql_error());

    $id_saida = mysql_insert_id();

    $sql_pag_especifico = "INSERT INTO pagamentos_especifico (id_saida,  mes, ano, id_clt)
                             VALUES('$id_saida', '$mes', '$ano', '$clt');";

    $sql_saida_files = "INSERT INTO saida_files (id_saida, tipo_saida_file) VALUES ('$id_saida','.pdf');";

    if ($tipo_guia == 1) {

        mysql_query($sql_pag_especifico);
        mysql_query($sql_saida_files);

        $id_comprovante = mysql_insert_id();

        $nomearquivo = '../arquivos/ferias/ferias_' . $clt . '_' . $id_ferias . '.pdf';
        echo $nomearquivo;
        $arquivo_destino = '../../comprovantes/' . $id_comprovante . '.' . $id_saida . '.pdf';

        if (!copy($nomearquivo, $arquivo_destino)) {
            echo 'Erro no anexo da saída!';
            exit;
        }
    } elseif ($tipo_guia == 2) {

        mysql_query("INSERT INTO pagamentos_especifico (id_saida,  mes, ano, id_clt, id_rescisao) VALUES ('$id_saida', '$mes', '$ano', '$clt', '$id_rescisao');");
        mysql_query($sql_saida_files);
    } elseif ($tipo_guia == 3) {

        mysql_query("INSERT INTO saida_files (id_saida, tipo_saida_file,multa_rescisao) VALUES ('$id_saida','.pdf', '1');");
        $id_comprovante = mysql_insert_id();

        $arquivo_destino = '../../comprovantes/' . $id_comprovante . '.' . $id_saida . '.pdf';
        if (move_uploaded_file($arquivo['tmp_name'], $arquivo_destino)) {
            echo 'ok';
        } else {
            echo 'error';
        }
    } elseif ($tipo_guia == 4) {
        mysql_query("INSERT INTO pagamentos_especifico (id_saida,  mes, ano, id_clt, id_rescisao) VALUES ('$id_saida', '$mes', '$ano', '$clt', '$id_rescisao');");
        mysql_query("INSERT INTO saida_files (id_saida,tipo_saida_file,rescisao_complementar) VALUES ('$id_saida','.pdf','1');") or die(mysql_error('Erro ao garvar na tabela saida_files'));
    } elseif ($tipo_guia == 5) {
        mysql_query("INSERT INTO pagamentos_especifico (id_saida,  mes, ano, id_clt, id_rescisao) VALUES ('$id_saida', '$mes', '$ano', '$clt', '$id_rescisao');");
        mysql_query("INSERT INTO saida_files (id_saida, tipo_saida_file,multa_rescisao) VALUES ('$id_saida','.pdf', '2');");
        $id_comprovante = mysql_insert_id();

        $arquivo_destino = '../../comprovantes/' . $id_comprovante . '.' . $id_saida . '.pdf';
        if (move_uploaded_file($arquivo['tmp_name'], $arquivo_destino)) {
            echo 'ok';
        } else {
            echo 'error';
        }
        
        mysql_query("INSERT INTO saida_files (id_saida, tipo_saida_file,multa_rescisao) VALUES ('$id_saida','.pdf', '2');");
        $id_saida_files_2 = mysql_insert_id();
        $arquivo_destino2 = '../../comprovantes/' . $id_saida_files_2 . '.' . $id_saida . '.pdf';
        
        if (move_uploaded_file($arquivo_2['tmp_name'], $arquivo_destino2)) {
            echo 'ok';
        } else {
            echo 'error';
        }
    }

    echo 'Envio concluído...';
    echo "<script> 
          setTimeout(function(){
            window.parent.location.reload();
            parent.eval('tb_remove()')
            },3000)    
        </script>";
    exit;
}


//LISTANDO SAÍDAS
$query_saida = mysql_query("SELECT PG.id_saida,B.nome, DATE_FORMAT(B.data_vencimento, '%d/%m/%Y')  as data_vencimento, IF(B.data_pg = NULL,'',DATE_FORMAT(B.data_pg, '%d/%m/%Y' )) as data_pg,
C.nome as nome_banco, C.conta, C.agencia,B.status
FROM pagamentos_especifico AS PG
INNER JOIN saida as B 
 ON PG.id_saida = B.id_saida
 INNER JOIN bancos as C
 ON C.id_banco = B.id_banco
WHERE B.status != '0' AND PG.mes = '$mes' AND PG.ano = '$ano' AND PG.id_clt = '$clt' AND (B.tipo = '51' OR B.tipo = '170')") or die(mysql_error());
$num_saida = mysql_num_rows($query_saida);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Detalhes da Rescis&atilde;o</title>
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js"></script>
        <script type="text/javascript" src="../../jquery/priceFormat.js"></script>

        <!-- datepiker -->
        <script type="text/javascript" src="../../jquery/datepicker-lite/jquery-ui-1.8.4.custom.min.js"></script>
        <link rel="stylesheet" type="text/css" href="../../jquery/datepicker-lite/jquery-ui-1.8.4.custom.css"/>
        <!-- datepiker -->
        <script type="text/javascript">
            $(function() {

                $('.multa_valor').priceFormat({
                    prefix: '',
                    centsSeparator: ',',
                    thousandsSeparator: '.'
                });
                $('#valor_multa').priceFormat({
                    prefix: '',
                    centsSeparator: ',',
                    thousandsSeparator: '.'
                });


                $('.date').keyup(function() {
                    var valor = $(this).val();
                    if (valor.length == 2 || valor.length == 5) {
                        $('.date').val(valor + "/");
                    }

                    var matriz = valor.split('/');
                    if (matriz[0] > 31) {
                        alert("Digite um dia válido!");
                        $(this).val('');
                        return false;
                    }
                    if (matriz[1] > 12) {
                        alert("Digite um mes válido!");
                        $(this).val(matriz[0] + '/');
                        return false;
                    }
                    if (matriz[2] > 2050) {
                        alert("Digite um ano válido!");
                        $(this).val(matriz[0] + '/' + matriz[1] + '/');
                        return false;
                    }

                });
                // Datepicker
                $('.date, .multa_dt').datepicker({
                    dateFormat: 'dd/mm/yy',
                    changeMonth: true,
                    changeYear: true
                });

                $('#enviar').click(function() {
                    var msg = $('#msg');
                    $(this).attr('disabled', 'disabeld');
                    if ($('#bancos').val() == '') {
                        msg.html('Selecione o banco.');
                        $('#bancos').focus();
                        $("#enviar").removeAttr('disabled');
                        return false;
                    }

                    if (($('#tipo_guia').val() == 3 || $('#tipo_guia').val() == 5) && $('#valor_multa').val() == '') {
                        msg.html('Digite o valor da multa.');
                        $('#valor_multa').focus();
                        $('#enviar').removeAttr('disabled');
                        return false;
                    }


                    if ($('input[name=data]').val() == '') {
                        msg.html('Digite a data.');
                        $('input[name=data]').focus();
                        $('#enviar').removeAttr('disabled');
                        return false;
                    }

                    if (($('#tipo_guia').val() == 3 || $('#tipo_guia').val() == 5) && $('.arquivo').val() == '') {

                        msg.html('O arquivo não foi anexado');
                        $('.arquivo').css('background-color', ' #f96a6a')
                                .css('color', '#FFF');
                        $('#enviar').removeAttr('disabled');
                        return false;

                    }
                    $('#form1').submit();
                });


                $('.arquivo').change(function() {

                    var aviso = $('#msg');
                    var arquivo = $(this);
                    var extensao_arquivo = (arquivo.val().substring(arquivo.val().lastIndexOf("."))).toLowerCase();


                    if (arquivo.val() != '' && extensao_arquivo == '.pdf') {
                        arquivo.css('background-color', '#51b566')
                                .css('color', '#FFF');
                        aviso.html('');
                    }

                    if (extensao_arquivo != '.pdf') {
                        arquivo.css('background-color', ' #f96a6a')
                                .css('color', '#FFF');
                        aviso.html('Este arquivo não é um PDF.');
                    }


                });




            });
        </script>
        <style>
            #msg{ 
                font-weight: bold;
                color:  #f6748f;
            }
        </style>
    </head>
    <body class="novaintra">
        <div id="content">
            <h3><?php echo $nome_saida; ?></h3>
            <form action="detalhes_novo.php" name="form1" id="form1" method="post" enctype="multipart/form-data" >  
                <table width="800" cellspacing="0" cellpadding="0" class="grid">
                    <tr>
                        <td>GRUPO</td>
                        <td>10 - PESSOAL</td>
                    </tr>
                    <tr>
                        <td>SUBGRUPO</td>
                        <td>
<?php
$qr_subgrupo = mysql_query("SELECT * FROM entradaesaida_subgrupo  WHERE id = '$subgrupo'");
$row_subgrupo = mysqL_fetch_assoc($qr_subgrupo);
echo $row_subgrupo['id_subgrupo'] . ' - ' . $row_subgrupo['nome'];
?>
                        </td>
                    </tr>
                    <tr>
                        <td>TIPO</td>
                        <td><?= $tipo_id . ' - ' . $tipo_nome ?>
                            <input type="hidden" value="<?= $tipo_id ?>" name="tipo" />
                        </td>
                    </tr>
                    <tr>
                        <td>Valor</td>
                        <td>R$ <?= number_format($row['total_liquido'], 2, ',', '.'); ?>
                            <input type="hidden" value="<?= $row['total_liquido'] ?>" name="valor" />
                        </td>
                    </tr>
                    <tr>
                        <td>Banco</td>
                        <td><label for="select"></label>
                            <select name="bancos" id="bancos">
                                <option value="">Selecione...</option>
<?php
$usuario = carregaUsuario();
$query_banco = mysql_query("SELECT * FROM bancos WHERE id_regiao = '{$usuario['id_regiao']}' AND status_reg = '1'");

while ($banco = mysql_fetch_array($query_banco)) {
    echo "<option value='{$banco['id_banco']}'>{$banco['id_banco']} - {$banco['nome']}</option>";
}
?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Data de Vencimento</td>
                        <td><input type="text" name="data" class="date" /></td>
                    </tr>
<?php if ($tipo_guia == 3 || $tipo_guia == 5) { ?>
                    <tr>
                        <td>Valor da Multa:</td>
                        <td><input type="text" name="valor_multa" id="valor_multa"/></td>
                    </tr>
                    <tr>
                        <td>Anexar:</td>
                        <td>
                            <input name="arquivo_multa" type="file" class="arquivo" />   <span style="color:  #828788; ">* .pdf </span>
                        </td>
                    </tr> 
<?php } ?>
<?php if ($tipo_guia == 5) { ?>
                    <tr>
                        <td>Anexar:</td>
                        <td>
                            <input name="arquivo2" type="file" class="arquivo" />   <span style="color:  #828788; ">* .pdf </span>
                        </td>
                    </tr> 
<?php } ?>
                    <tr>
                        <td colspan="2" align="center">

                            <input type="hidden" name="nome" value="<?= $nome_saida ?>" />
                            <input type="hidden" name="tipo_guia" value="<?= $tipo_guia ?>" id="tipo_guia"/>
                            <input type="hidden" name="mes" value="<?= $mes ?>" />
                            <input type="hidden" name="ano" value="<?= $ano ?>" />
                            <input type="hidden" name="regiao" value="<?= $row_resci['id_regiao'] ?>" />
                            <input type="hidden" name="projeto" value="<?= $row_resci['id_projeto'] ?>" />
                            <input type="hidden" name="ferias" value="<?= $id_ferias ?>" />
                            <input type="hidden" name="rescisao" value="<?= $id_rescisao; ?>" />
                            <input type="hidden" name="id_clt" value="<?php echo $clt; ?>"/>
                            <input type="hidden" name="subgrupo" value="<?php echo $subgrupo; ?>"/>
                            <div id="msg"></div>
                            <input type="hidden" name="acao" value="cadastrar"/>
                            <input type="button" value="Enviar" name="enviar" id="enviar"/>
                        </td>
                    </tr>
                </table>
<?php
/*

  if($num_saida !=0){
  ?>

  <div class="titulo"> SAÍDAS</div>

  <table class="tabela">
  <tr class="campos">
  <td width="40">COD.</td>
  <td  width="130" >BANCO</td>
  <td width="50">AGÊNCIA</td>
  <td width="50">CONTA</td>
  <td width="70">DATA DE VENC.</td>
  <td  width="70">DATA DE PG</td>
  <td width="50">STATUS</td>
  </tr>

  <?php
  while($row_saida = mysql_fetch_assoc($query_saida)){
  $cor = ($i++ %2 == 0)?'linha_um': 'linha_dois';
  ?>
  <tr style="text-align: center;" class="<?php echo $cor; ?>">
  <td><?php echo $row_saida['id_saida'];?></td>

  <td><?php echo $row_saida['nome_banco'];?></td>
  <td><?php echo $row_saida['agencia'];?></td>
  <td><?php echo $row_saida['conta'];?></td>
  <td><?php echo $row_saida['data_vencimento'];?></td>
  <td><?php echo $row_saida['data_pg'];?></td>
  <td><img src="../../imagens/bolha<?php echo $row_saida['status'];?>.png" width="15" height="15"/></td>
  </tr>

  <?php
  }

  echo '</table>';
  } else {

  echo '<div>Nenhuma saída cadastrada.</div>';
  }
 */
?>
            </form>
        </div>
    </body>
</html>