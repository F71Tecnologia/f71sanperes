<?php
include "../../conn.php";
include "../../classes/uploadfile.php";
require("../../classes/LogClass.php");
require("../../classes/EntradaClass.php");

$log = new Log();

class Notas{
    public function getNotas($id_nota) {
        $result = "
        SELECT * FROM notas_files WHERE id_notas = '$id_nota' AND status = '1'";
        $notas = mysql_query($result) or die(mysql_error());
        while($rowNotas = mysql_fetch_assoc($notas)){
            $dados[$rowNotas[id_file]] = $rowNotas;
        }
        return $dados;
    }
}

if($_REQUEST[update_entrada]){

    $nome = utf8_decode($_REQUEST['nome']);
    $descricao = addslashes(utf8_decode($_REQUEST['descricao']));
    $id_entrada = $_REQUEST['id_entrada'];
    $data_vencimento = implode('-',array_reverse(explode('/',$_POST['data_vencimento'])));
    $valor = $_REQUEST[valor];
    $id_banco = $_REQUEST[id_banco];

    $update = "UPDATE entrada SET nome = '$nome', 
            especifica = '$descricao',data_vencimento = '$data_vencimento',
            valor = '$valor', id_banco = '$id_banco'  
            WHERE id_entrada = '$id_entrada' LIMIT 1;";
    $update = mysql_query($update);
    if(!$update){
        $log->gravaLog('Editar Entrada', 'Edição Entrada '.$id_entrada);
        echo '1';
    } exit;
}

if($_REQUEST[gerenciar_anexo]){
    $id_entrada = $_REQUEST[id]; ?>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <?php

    $sqlVerTipo = "SELECT tipo FROM entrada WHERE id_entrada = $id_entrada";
    $queryVerTipo = mysql_query($sqlVerTipo);
    $resultVerTipo = mysql_result($queryVerTipo,0);

    // if ($resultVerTipo != 12) {
    ?>
    <script>
        $(function(){
            Dropzone.autoDiscover = false;

            $("#dropzone").dropzone({
                url: "actions/action_entrada.php?upload_anexo=1&id_entrada=<?=$id_entrada?>",
                addRemoveLinks : true,
                maxFilesize: 10,
                dictResponseError: "Erro no servidor!",
                dictCancelUpload: "Cancelar",
                dictFileTooBig: "Tamanho máximo: 10MB",
                dictRemoveFile: "Remover Arquivo",
                canceled: "Arquivo Cancelado",
                acceptedFiles: '.jpg,.gif,.png,.pdf,.JPG,.GIF,.PNG,.PDF'
//            , sending: function(file, xhr, formData) {
//                formData.append("frids", "value"); // Append all the additional input data of your form here!
//            }
//            , success: function(file, responseText){
//                console.log(responseText);
//                //$('.close').trigger('click');
//            }
            });
        });
    </script>

    <div id="dropzone" class="dropzone margin_b15" style="min-height: 150px;"></div>
    <?php// } ?>
    <?php $countFiles = 0;
    $qr_entrada_files = mysql_query("SELECT * FROM entrada_files WHERE id_entrada = '$id_entrada' AND status = '1'");
    while($row_entrada_files = mysql_fetch_assoc($qr_entrada_files)){

        if($_COOKIE['logado'] == 299){
            echo "<pre>";
            print_r($row_entrada_files);
            echo "</pre>";
        }

        //        if($countFiles == 4){ echo '</div><div class="row margin_b15">'; $countFiles = 0;} $countFiles++;
        if(file_exists("../../novoFinanceiro/comprovantes/entrada/$row_entrada_files[id_files]$row_entrada_files[tipo_files]")){ ?>
            <div class="col-xs-3 margin_b5 <?=$row_entrada_files['id_files']?>">
                <div class="thumbnail">
                    <a href="../novoFinanceiro/comprovantes/entrada/<?=$row_entrada_files['id_files'].$row_entrada_files['tipo_files']?>" target="_blank">
                        <img src="../imagens/icons/att-<?=str_replace('.','',$row_entrada_files[tipo_files])?>.png">
                    </a>
                    <?php //if ($resultVerTipo != 12) { ?>
                    <span class="btn btn-sm btn-danger fa fa-trash-o margin_t5 deleteAnexoEntrada" style="width: 100%;" data-key="<?=$row_entrada_files['id_files']?>"> Deletar</span>
                    <?php //} ?>
                </div>
            </div>
        <?php } else { ?>
            <div class="col-xs-3 margin_b5 <?=$row_entrada_files['id_files']?>">
                <div class="thumbnail">
                    <a href="comprovantes/entrada/<?=$row_entrada_files['id_files'].$row_entrada_files['tipo_files']?>" target="_blank">
                        <img src="../imagens/icons/att-<?=str_replace('.','',$row_entrada_files[tipo_files])?>.png">
                    </a>
                    <?php// if ($resultVerTipo != 12) { ?>
                    <span class="btn btn-sm btn-danger fa fa-trash-o margin_t5 deleteAnexoEntrada" style="width: 100%;" data-key="<?=$row_entrada_files['id_files']?>"> Deletar</span>
                    <?php //} ?>
                </div>
            </div>
        <?php } ?>
    <?php } ?>
    <div class="clear"></div>
    <?php
    exit;
}

if($_REQUEST[gerenciar_anexo_rel]){
    $id_entrada = $_REQUEST[id]; ?>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <?php $countFiles = 0;
    $qr_entrada_files = mysql_query("SELECT * FROM entrada_files WHERE id_entrada = '$id_entrada' AND status = '1'")or die(mysql_error());
    while($row_entrada_files = mysql_fetch_assoc($qr_entrada_files)){
        //        if($countFiles == 4){ echo '</div><div class="row margin_b15">'; $countFiles = 0;} $countFiles++;
        if(file_exists("../../novoFinanceiro/comprovantes/entrada/$row_entrada_files[id_files]$row_entrada_files[tipo_files]")){ ?>
            <div class="col-xs-3 margin_b5 <?=$row_entrada_files['id_files']?>">
                <div class="thumbnail">
                    <a href="../novoFinanceiro/comprovantes/entrada/<?=$row_entrada_files['id_files'].$row_entrada_files['tipo_files']?>" target="_blank">
                        <img src="../imagens/icons/att-<?=str_replace('.','',$row_entrada_files[tipo_files])?>.png">
                    </a>
                    <span class="btn btn-sm btn-danger fa fa-trash-o margin_t5 deleteAnexoEntrada" style="width: 100%;" data-key="<?=$row_entrada_files['id_files']?>"> Deletar</span>
                </div>
            </div>
        <?php } else { ?>
            <div class="col-xs-3 margin_b5 <?=$row_entrada_files['id_files']?>">
                <div class="thumbnail">
                    <a href="comprovantes/entrada/<?=$row_entrada_files['id_files'].$row_entrada_files['tipo_files']?>" target="_blank">
                        <img src="../imagens/icons/att-<?=str_replace('.','',$row_entrada_files[tipo_files])?>.png">
                    </a>
                    <span class="btn btn-sm btn-danger fa fa-trash-o margin_t5 deleteAnexoEntrada" style="width: 100%;" data-key="<?=$row_entrada_files['id_files']?>"> Deletar</span>
                </div>
            </div>
        <?php } ?>
    <?php } ?>
    <div class="clear"></div>
    <?php
    exit;
}

if($_REQUEST[upload_anexo]){
//    print_r($_REQUEST);print_r($_FILES);exit;
    $id_entrada = $_REQUEST['id_entrada'];
    //$diretorio = "http://" . $_SERVER['SERVER_NAME'] . "/intranet/finan/comprovantes/entrada";
    //$diretorio = "../comprovantes/entrada";
    $diretorio = "../../novoFinanceiro/comprovantes/entrada";

    $upload = new UploadFile($diretorio,array('jpg','gif','png','pdf','JPG','GIF','PNG','PDF'));
    $upload->arquivo($_FILES[file]);
    $upload->verificaFile();
//    echo "INSERT INTO entrada_files (id_entrada,	tipo_files) VALUES ('$id_entrada','.$upload->extensao');"; exit;
    mysql_query("INSERT INTO entrada_files (id_entrada,	tipo_files) VALUES ('$id_entrada','.$upload->extensao');");
    //$query_max = mysql_query("SELECT MAX(id_files) FROM entrada_files");
    $id = mysql_insert_id();

    $log->gravaLog('Anexo Entrada', 'Anexo '.$id.' inserido na Entrada '.$id_entrada);

    $upload->NomeiaFile($id);
    $upload->Envia();

    echo $diretorio.'/'.$id.'.'.$upload->extensao; exit;

}

if($_REQUEST[deleteAnexoEntrada]){

    $id_files = $_REQUEST['id'];

    if(mysql_query("UPDATE entrada_files SET status = 0 WHERE id_files = $id_files LIMIT 1;")){
        $log->gravaLog('Excluir Anexo Entrada', 'Anexo '.$id.' excluido');
        echo "Anexo excluido com sucesso!";
    } else {
        echo "Erro ao excluir o anexo!";
    }
    exit;

}

if($_REQUEST[pega_id_banco]){

    $id_projeto = $_REQUEST['id_projeto'];

    $sql = "SELECT id_banco FROM bancos WHERE id_projeto = $id_projeto LIMIT 1";
    $sql = mysql_query($sql);
    $sql = mysql_fetch_assoc($sql);
    echo $sql['id_banco'];
    exit;

}

if($_REQUEST[ver_notas]){

    $id_nota = $_REQUEST['id'];
    $objNotas = new Notas();
    $dadosNotas = $objNotas->getNotas($id_nota); ?>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <?php $countFiles = 0;

    foreach($dadosNotas AS $row_files){
        if(file_exists("../../adm/adm_notas/notas/$row_files[id_file].$row_files[tipo]")){ ?>
            <div class="col-xs-3 margin_b15">
                <a href="../adm/adm_notas/notas/<?=$row_files['id_file'];?>.<?=$row_files['tipo'];?>" target="_blank" class="thumbnail" style="width: 100px; height: 100px;">
                    <img src="../imagens/icons/att-<?=$row_files[tipo]?>.png">
                </a>
            </div>
        <?php } else{ ?>
            <div class="col-xs-3 margin_b15">
                <a href="../adm/adm_notas/notas/<?=$row_files['id_file'];?>.<?=$row_files['tipo'];?>" target="_blank" class="thumbnail" style="width: 100px; height: 100px;">
                    <img src="../imagens/icons/att-404.png">
                </a>
            </div>
        <?php } ?>
    <?php } ?>
    <div class="clear"></div>
    <?php exit;
}

if(isset($_REQUEST[action]) && $_REQUEST[action] == 'deletar'){

    if(is_array($_POST[id])){
        foreach ($_POST[id] as $key => $value) {
            $id_entrada[] = $value[value];
        }
    } else {
        $id_entrada[] = $_POST[id];
    }
    $countEntrada = count($id_entrada);

    for($i=0; $i < $countEntrada; $i++){

        $result = mysql_query("SELECT * FROM entrada WHERE id_entrada = '$id_entrada[$i]' LIMIT 1");
        $row = mysql_fetch_array($result);

        if($row['status'] == "1"){
            mysql_query("UPDATE entrada SET status = '0', id_deletado = $_COOKIE[logado], data_deletado = NOW() WHERE id_entrada = '$id_entrada[$i]' LIMIT 1");
            $log->gravaLog('Excluir Entrada', 'Exclusão Entrada '.$id_entrada[$i]);
        }
    }
}

if(isset($_REQUEST[action]) && $_REQUEST[action] == 'pagar'){

    $objEntrada = new Entrada();

    echo '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">';

    if(is_array($_POST[id])){
        foreach ($_POST[id] as $key => $value) {
            $id_entrada[] = $value[value];
        }
    } else {
        $id_entrada[] = $_POST[id];
    }

    $countEntrada = count($id_entrada);

    for($i=0; $i < $countEntrada; $i++){

        $row = mysql_fetch_array(mysql_query("SELECT * FROM entrada WHERE id_entrada = '$id_entrada[$i]' LIMIT 1"));
        $row_bancos = mysql_fetch_array(mysql_query("SELECT * FROM bancos WHERE id_banco = '$row[id_banco]' LIMIT 1"));

        $pagamento = $row['data_vencimento'];
        $projeto_id = $row['id_projeto'];


        $valor = str_replace(",", ".", $row[valor]);
        $adicional = str_replace(",", ".", $row[adicional]);
        $valor_banco = str_replace(",", ".", $row_bancos[saldo]);

        $valor_final = $valor + $adicional;
        $saldo_banco_final = $valor_banco + $valor_final;

        $valor_f = number_format($valor_final,2,",",".");
        $saldo_banco_final_f = number_format($saldo_banco_final,2,",",".");
        $saldo_banco_final_banco = number_format($saldo_banco_final,2,",","");

        if($row['status'] == "1"){

            if(!empty($_REQUEST['new_date'])){
                $new_date = implode('-',array_reverse(explode('/',$_REQUEST['new_date'])));
                $pagamento = $new_date;
                $auxDate = " , data_pg = '{$new_date}' ";

                $log->gravaLog('Pagar Entrada', 'Edição da data_vencimento da entrada: '.$id_saida[$i].'. De: '.$row['data_vencimento'].' Para: '.$new_date);
            }

            $periodo = substr($pagamento, 0, 4).''.substr($pagamento, 5, 2);
            $rowTrava = mysql_fetch_array(mysql_query("SELECT * FROM contabil_trava WHERE id_projeto = $projeto_id AND periodo = '$periodo' LIMIT 1"));
            if(empty($rowTrava['periodo'] && $rowTrava['id_projeto'])) {

                mysql_query("UPDATE entrada SET status = '2', id_userpg = '$_COOKIE[logado]', hora_pg = NOW() $auxDate WHERE id_entrada = '$id_entrada[$i]' LIMIT 1");
                mysql_query("UPDATE bancos SET saldo = '$saldo_banco_final_banco' WHERE id_banco = '$row[id_banco]' LIMIT 1");

                //LANÇAMETO CONTABIL

                $rowLancamento = mysql_fetch_assoc(mysql_query("SELECT * FROM contabil_lancamento WHERE id_entrada = {$id_entrada[$i]} LIMIT 1"));
                $objEntrada->updateLancamentoContabil($rowLancamento['id_lancamento']);

                $array_itens = array();
                $array_itens[] = array('id_lancamento' => $rowLancamento['id_lancamento'], 'id_banco' => $row_bancos['id_banco'], 'valor' => $row['valor'], 'documento' => $row['n_documento'], 'tipo' => 2, 'id_projeto' => $projeto_id);
                $array_itens[] = array('id_lancamento' => $rowLancamento['id_lancamento'], 'id_conta' => $row['tipo'], 'valor' => $row['valor'], 'documento' => $row['n_documento'], 'tipo' => 1, 'id_projeto' => $projeto_id);
                $objEntrada->inserirItensLancamento($array_itens);

                if($row['tipo'] == "66") {
                    mysql_query("UPDATE compra SET acompanhamento = '6' WHERE id_compra = '$row[id_compra]'");
                }

                $log->gravaLog('Pagar Entrada', 'Pagameto Entrada '.$id_entrada[$i].' saldo banco De: '.$valor_banco.' Prara: '.$saldo_banco_final);

                echo "<pre>";
                echo "Nº da Entrada: $id_entrada[$i]<br>";
                echo "Valor da Conta: R$ ".number_format($valor,2,",",".")."<br>";
                echo "Adicional: R$ ".number_format($adicional,2,",",".")."<br>";
                echo "Total a pagar: R$ $valor_f<br>";
                echo "Valor no Banco: R$ ".number_format($valor_banco,2,",",".")."<br>";
                echo "Saldo atualizado do Banco: <strong>R$ $saldo_banco_final_f</strong>";
                echo "</pre>";
            } else {
                echo "<pre>";
                echo "DATA SELECIONADA COM TRAVA !<br><br>";
                echo "FAVOR ENTRAR EM CONTATO COM O SETOR CONTABIL.<br>";
                echo "</pre>";
            }
        }
    }
}
