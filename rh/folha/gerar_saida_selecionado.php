<?php

include('../../adm/include/restricoes.php');
include('../../conn.php');
include('../../classes_permissoes/regioes.class.php');
include('../../funcoes.php');

$REGIAO = new Regioes();



if (isset($_POST['confirmar']) or isset($_POST['enviar'])) {

    $id_banco = $_POST['banco'];
    $id_folha = $_POST['id_folha'];
    $id_regiao = $_POST['regiao'];
    $regiao_folha = $_POST['regiao_folha'];
    
    $id_projeto = $_POST['projeto'];
    $data_vencimento = implode('-', array_reverse(explode('/', $_POST['data_vencimento'])));
    $array_clts = $_POST['clts'];

    $clts = implode(',', $array_clts);

    $qr_folha = mysql_query("SELECT * FROM rh_folha WHERE id_folha = '$id_folha'");
    $row_folha1 = mysql_fetch_assoc($qr_folha);

    if ($row_folha1['terceiro'] == 1) {
        $tipo = 276;
    } else {
        $tipo = 275;
    }
    
    $qrFolha =    "SELECT *,B.nome as nome_projeto, A.nome as nome_clt 
                    FROM rh_folha_proc as A
                    INNER JOIN projeto as B
                    ON B.id_projeto = A.id_projeto
                    WHERE A.id_folha = '$id_folha' AND A.financeiro != 1 AND id_clt IN($clts) AND status = 3";
   // echo "<!-- $qrFolha -->";
    
   
    $result_folha_pro = mysql_query($qrFolha) or die ("Erro na query da folha:<br/>".  mysql_error() ."<br/>");
    
    while ($row_folha_proc = mysql_fetch_assoc($result_folha_pro)){
//        $valor           = str_replace('.', ',', $row_folha_proc['salliquido']);
        $valor           = $row_folha_proc['salliquido'];
        $especifica      = $row_folha_proc['nome_projeto'].' - '.'COMP. ' . $row_folha_proc['mes'] . '/' . $row_folha_proc['ano'];
        $nome            = $row_folha_proc['nome_clt'].' - PROJETO: '.$row_folha_proc['nome_projeto'];
        $id_folha_proc[] = $row_folha_proc['id_folha_proc'];
        $sql[] = "('$id_regiao', '$id_projeto', '$id_banco', '$_COOKIE[logado]', '$nome',  '$especifica', '$tipo', '$valor',NOW(), '$data_vencimento',  '1', '0', '1', '$row_folha_proc[id_clt]',13,1)";
    
       
    }

    $sql = implode(',', $sql);
    $id_folha_proc = implode(',', $id_folha_proc);
    
    
    $qrFull = "INSERT INTO saida (id_regiao, id_projeto, id_banco, id_user, nome,  especifica, tipo, valor, data_proc, data_vencimento, status,comprovante, entradaesaida_subgrupo_id, id_clt, id_tipo_pag_saida, id_referencia) VALUES $sql";
   
    $qr_insert = mysql_query($qrFull) or die(mysql_error() . ": <br/>{$qrFull}");

    
    if ($qr_insert) {
        mysql_query("UPDATE rh_folha_proc SET financeiro = 1 WHERE id_folha_proc IN($id_folha_proc) ");
    }

    //-- ENCRIPTOGRAFANDO A VARIAVEL
    $linkreg2 = encrypt("$regiao_folha&$id_folha");
    $linkreg2 = str_replace("+", "--", $linkreg2);
    // -----------------------------

    
    echo '<script>       
        alert("Envio para o financeiro concluído.");
        location.href = "pg_lote.php?enc='.$linkreg2.'"
    </script>';
    exit;
}

