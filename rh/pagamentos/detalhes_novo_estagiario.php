<?php
include "../../conn.php";
include "../../wfunction.php";
include "../../classes/LogClass.php";

$log = new Log();

$mes = $_GET['mes'];
$ano = $_GET['ano'];
$estagiario = $_GET['id_estagiario'];
$tipo_guia = $_GET['tipo']; // 1 - FÉRIAS, 2 - RECISÂO, 3 - MULTA FGTS, 4 - RESCISÃO COMPLEMENTAR, 5 - MULTA FGTS COMPLEMENTAR
$id_rescisao = $_GET['id_rescisao'];
//print_r($id_rescisao);
$id_ferias = $_GET['id_ferias'];
$usuario = carregaUsuario();


$projeto = $_GET['projeto'];
$regiao = $_GET['regiao'];
$query_estagiario = mysql_query("SELECT * FROM estagiario WHERE id_estagiario = '$estagiario'");
$row_estagiario = mysql_fetch_assoc($query_estagiario);
$query_mes = mysql_query("SELECT nome_mes FROM ano_meses WHERE num_mes = '$mes'");
$mes_nome = @mysql_result($query_mes, 0);

/**
 * SELECIONANDO O BANCO PRINCIPAL DO PROJETO
 */
$query_banco  = "SELECT A.id_banco_principal FROM projeto AS A WHERE A.id_projeto = '{$row_estagiario['id_projeto']}'";
$sql_banco    = mysql_query($query_banco) or die("Erro ao selecionar banco");
$bancoProjeto = "";
while($rows = mysql_fetch_assoc($sql_banco)){
    $bancoProjeto = $rows['id_banco_principal'];
} 



// Montando o nome da saida
if ($tipo_guia == 2) {
//	$tipo_id = "170";
	$tipo_id = "31";
	$tipo_nome = "RESCISÃO DE ESTAGIÁRIO";
        $subgrupo = 4;
    $qr_recisao = mysql_query("SELECT A.*, B.nome as nome_projeto FROM rh_rescisao_estagiario as A 
                                    INNER JOIN projeto as B
                                    ON A.id_projeto = B.id_projeto
                                    WHERE MONTH(A.data_fim) = '$mes' AND YEAR(A.data_fim) = '$ano'  AND A.id_rescisao = '$id_rescisao' AND A.status = '1'");
    $query = $qr_recisao;
}

$row = mysql_fetch_assoc($query);
$nome_saida = "$row_estagiario[id_estagiario] - $row_estagiario[nome], $tipo_nome $mes_nome/$ano $row[id_projeto] -  PROJETO: $row[nome_projeto]";


foreach ($_GET as $chave => $valor) {
    $string[] = $chave . '=' . $valor;
}
$link = implode("&", $string);

if (isset($_REQUEST['acao'])) {

    $id_user = $_COOKIE['logado'];
    $folha = $_POST['id_folha'];
    $mes = $_POST['mes'];
    $ano = $_POST['ano'];
    $estagiario = $_POST['id_estagiario'];
    $tipo = $_POST['tipo']; // 170 - RESCISÃƒO, 156 - FÃ‰RIAS  
    $nome = $_POST['nome'];
    $id_ferias = $_POST['ferias'];
    $id_rescisao = $_POST['rescisao'];
    $especificacao = "";
    $adicional = 0;
    $tipo_guia = $_POST['tipo_guia'];
    $arquivo = $_FILES['arquivo_multa'];
//    $valor = ($tipo_guia != 3 && $tipo_guia != 5) ? str_replace('.', ',', $_POST['valor']) : str_replace('.', '', $_POST['valor_multa']);
    $valor = ($tipo_guia != 3 && $tipo_guia != 5) ? $_POST['valor'] : str_replace(',', '.', str_replace('.', '', $_POST['valor_multa']));
    $data = implode("-", array_reverse(explode("/", $_POST['data'])));
    $banco = $_POST['bancos'];
    $subgrupo = $_POST['subgrupo'];
    $id_estagiario = $_POST['id_estagiario'];
    $arquivo_2 = $_FILES['arquivo2'];
    $descricao = $_REQUEST['descricao'];

    $qr_banco = mysql_query("SELECT * FROM bancos WHERE id_banco = '$banco'");
    $row_banco = mysql_fetch_assoc($qr_banco);
    $regiao = $row_banco['id_regiao'];
    $projeto = $row_banco['id_projeto'];

    $query_xxx = "SELECT A.*, IF(A.nome_banco = '', B.nome, A.nome_banco ) as banco FROM estagiario as A
                           LEFT JOIN  bancos as B
                           ON A.banco = B.id_banco
                           WHERE A.id_estagiario = $id_estagiario";
    $qr_estagiario = mysql_query($query_xxx) or die($query_xxx."<br>".mysql_error());
    $row_estagiario = mysql_fetch_assoc($qr_estagiario);

    $nome .= ' - BANCO: ' . $row_estagiario['banco'];
    $nome .=' - CONTA: ' . $row_estagiario['conta'];
    $nome .= ' - AGÊNCIA: ' . $row_estagiario['agencia'];
    $nome .= ' - CPF: ' . $row_estagiario['cpf'];


    //CRIANDO A SAÍDA
    $sql = "INSERT INTO saida (id_regiao, id_projeto, id_banco,  id_user, nome, especifica,  tipo,  valor, data_proc, data_vencimento, status,comprovante, nosso_numero, tipo_boleto, cod_barra_gerais, id_referencia, id_tipo_pag_saida, entradaesaida_subgrupo_id, id_estagiario, descricao)
          VALUES ('$regiao', '$projeto', '$banco', '{$_COOKIE['logado']}', '$nome', '$nome', '$tipo', '$valor',NOW(), '$data',  '1', '2', '$nosso_numero', '2', '$cod_barra_gerais','1', '1', '$subgrupo', '$id_estagiario', '$descricao') ";
    mysql_query($sql) or die(mysql_error());

    $id_saida = mysql_insert_id();
    $log->gravaLog('Pagamentos', "Pagamento Emitido: ID{$id_saida}");
    
    $sql_pag_especifico = "INSERT INTO pagamentos_especifico_estagiario (id_saida,  mes, ano, id_estagiario)
                             VALUES('$id_saida', '$mes', '$ano', '$estagiario');";

    $sql_saida_files = "INSERT INTO saida_files (id_saida, tipo_saida_file) VALUES ('$id_saida','.pdf');";
    
    $dataArray = explode('-',$data);
    $dataAno = $dataArray[0];
    $dataMes = sprintf("%02s", $dataArray[1]);
    
    if ($tipo_guia == 2) {

        mysql_query("INSERT INTO pagamentos_especifico_estagiario (id_saida,  mes, ano, id_estagiario, id_rescisao) VALUES ('$id_saida', '$mes', '$ano', '$estagiario', '$id_rescisao');");
        mysql_query($sql_saida_files);
    } 

    echo 'Envio concluído...<br/><strong>Aguarde o redirecionamento...</strong>';
    echo "<script> 
          setTimeout(function(){
            window.parent.location.href = 'formulario_para_solicitacao_estagiario.php?id_saida='+$id_saida+'&id_estagiario='+$id_estagiario+'&tipo='+$tipo_guia;
            parent.eval('tb_remove()')
            },4000)    
        </script>";
    exit;
}


//LISTANDO SAÍDAS
$query_saida = mysql_query("SELECT PG.id_saida,B.nome, DATE_FORMAT(B.data_vencimento, '%d/%m/%Y')  as data_vencimento, IF(B.data_pg = NULL,'',DATE_FORMAT(B.data_pg, '%d/%m/%Y' )) as data_pg,
C.nome as nome_banco, C.conta, C.agencia,B.status
FROM pagamentos_especifico_estagiario AS PG
INNER JOIN saida as B 
 ON PG.id_saida = B.id_saida
 INNER JOIN bancos as C
 ON C.id_banco = B.id_banco
WHERE B.status != '0' AND PG.mes = '$mes' AND PG.ano = '$ano' AND PG.id_estagiario = '$estagiario' AND (B.tipo = '51' OR B.tipo = '170')") or die(mysql_error());
$num_saida = mysql_num_rows($query_saida);

if($_COOKIE['logado'] == 179){
    echo '<pre>';
        print_r("Banco: " . $bancoProjeto);
    echo '</pre>';
}

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
                    
                    if(($('#tipo_guia').val() == 3 || $('#tipo_guia').val() == 5) && $('.arquivo').val()!=''){
                        var extensao_arquivo = ($('.arquivo').val().substring($('.arquivo').val().lastIndexOf("."))).toLowerCase();
                        if(extensao_arquivo != '.pdf'){
                            msg.html('O arquivo anexado não é um pdf');
                            $('.arquivo').css('background-color', ' #f96a6a')
                                    .css('color', '#FFF');
                            $('#enviar').removeAttr('disabled');
                            return false;
                        }
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
            <form action="detalhes_novo_estagiario.php" name="form1" id="form1" method="post" enctype="multipart/form-data" >  
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
                                    $query_banco = mysql_query("SELECT * FROM bancos WHERE status_reg = '1'");
                                    $selected = "";
                                    while ($banco = mysql_fetch_array($query_banco)) {
                                        if($bancoProjeto == $banco['id_banco']){
                                            $selected = " selected='selected' ";
                                        }else{
                                            $selected = "";
                                        }
                                        echo "<option value='{$banco['id_banco']}' {$selected} >{$banco['id_banco']} - {$banco['nome']}</option>";
                                    }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Data de Vencimento</td>
                        <td><input type="text" name="data" class="date" /></td>
                    </tr>
                    <tr>
                        <td>Descrição</td>
                        <td><textarea form="form1" name="descricao" ></textarea></td>
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
                            <input type="hidden" name="id_estagiario" value="<?php echo $estagiario; ?>"/>
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