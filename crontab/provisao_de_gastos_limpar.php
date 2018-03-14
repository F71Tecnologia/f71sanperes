<?php
error_reporting(E_ALL);
include "../conn.php";
date_default_timezone_set("America/Sao_Paulo"); // define c time zone como o do Brasil

$hoje = date("d/m/Y"); // data no formato brasileiro por causa da funcao geraTimestamp

$time_hj = geraTimestamp($hoje);
//$time_hj = geraTimestamp('01/02/2015'); // apenas para testes

$time_corte = $time_hj - (30 * 60 * 60 * 24); // 15 dias * 60 min * 60 seg * 24 horas

$data_corte = date("Y-m-d", $time_corte); // data no formato americano para query

//$data_corte = date('Y-m-d'); // a pedido do sinesio ser realizado todo dia primeiro

$ids_lotes = consultaIdLotes($data_corte); // consulta lote

// se existe lote realiza passos seguintes
if ($ids_lotes) {
    $ids_rescisoes_lote = consultaIdRescisoesLotes($ids_lotes); // consulta rescisoes

    $resp1 = excluirMovimentosRescisaoLote($ids_rescisoes_lote);
    $resp2 = excluirRescisoesLotes($ids_lotes);
    $resp3 = excluirLotes($ids_lotes);
}

//------------------------------------------------------------------------------

function consultaIdLotes($data) {
    $qr = "SELECT id_header FROM header_recisao_lote WHERE criado_em < '$data'";
    $resp = mysql_query($qr);
    if (mysql_num_rows($resp) > 0) {
        while ($row = mysql_fetch_assoc($resp)) {
            $ids_rescisao_lote[] = $row['id_header']; // guarda o id
        }
        log_OK('consultaIdLotes');
        return $ids_rescisao_lote;
    } else {
        $error = mysql_error();
        $mensagem = "Erro ao consultar lotes das rescisoes.";
        log_error($mensagem, $qr, $error);
        return FALSE;
    }
}

function consultaIdRescisoesLotes($cond) {
    if (is_array($cond)) {
        $cond = implode(',', $cond); // cria lista com ids separados com virgula
    }
    $qr = "SELECT id_recisao
            FROM rh_recisao AS a 
            WHERE a.recisao_provisao_de_calculo = 1
            AND a.id_recisao_lote IN ($cond)
            AND status = 0";

    $resp = mysql_query($qr);
    if (mysql_num_rows($resp) > 0) {
        while ($row = mysql_fetch_assoc($resp)) {
            $ids_rescisao_lote[] = $row['id_recisao']; // guarda o id
        }
        log_OK('consultaIdRescisoesLotes');
        return $ids_rescisao_lote;
    } else {
        $error = mysql_error();
        $mensagem = "Erro ao consultar rescisoes.";
        log_error($mensagem, $qr, $error);
        return FALSE;
    }
}

function excluirLotes($ids) {
    if (is_array($ids)) {
        $ids = implode(',', $ids); // cria lista com ids separados com virgula
    }
    if (!empty($ids)) {
        $qr = "DELETE FROM header_recisao_lote WHERE id_header IN ($ids)";
        $resp = mysql_query($qr);
//        echo $qr;
        if ($resp == FALSE) {
            $error = mysql_error();
            $mensagem = "Erro ao excluir lotes.";
            log_error($mensagem, $qr, $error);
        }else{
            log_OK('excluirLotes');
        }
        return $resp;
    }
}

function excluirRescisoesLotes($ids) {
    if (is_array($ids)) {
        $ids = implode(',', $ids); // cria lista com ids separados com virgula
    }
    if (!empty($ids)) {
        $qr = "DELETE FROM rh_recisao WHERE id_recisao_lote IN ($ids) AND recisao_provisao_de_calculo = 1 AND status = 0";
        $resp = mysql_query($qr);
//        echo $qr;
        if ($resp == FALSE) {
            $error = mysql_error();
            $mensagem = "Erro ao excluir lotes.";
            log_error($mensagem, $qr, $error);
        }else{
            log_OK('excluirRescisoesLotes');
        }
        return $resp;
    }
}

function excluirMovimentosRescisaoLote($ids_rescisao) {
    if (is_array($ids_rescisao)) {
        $ids_rescisao = implode(',', $ids_rescisao); // cria lista com ids separados com virgula
    }
    if (!empty($ids_rescisao)) {
        $qr = "DELETE FROM tabela_morta_movimentos_recisao_lote WHERE id_rescisao IN ($ids_rescisao)";
        $resp = mysql_query($qr);
        if ($resp == FALSE) {
            $error = mysql_error();
            $mensagem = "Erro ao excluir lotes.";
            log_error($mensagem, $qr, $error);
        }else{
            log_OK('excluirMovimentosRescisaoLote');
        }
//        echo $qr;
        return $resp;
    }
}

// cria arquivo de log
function log_error($mensagem, $qr = '', $error = '') {
    // Abre ou cria o arquivo bloco1.txt
    // "a" representa que o arquivo é aberto para ser escrito
    $data = date("d/m/Y H:i:s");
    $fp = fopen("log_provisao_de_gastos_limpar.txt", "a");
    $txt = "
--------------------------------------------------------------------------------
    ($data)
    $mensagem
    SQL: $qr
    ERROR: $error
--------------------------------------------------------------------------------\n\n";
    // Escreve "exemplo de escrita" no bloco1.txt
    $escreve = fwrite($fp, $txt);

    echo "Bytes Escritos: $escreve\n<br>\n<br>\n<br>";
    echo $txt;
    // Fecha o arquivo
    fclose($fp);
}

function log_OK($x){
    // Abre ou cria o arquivo bloco1.txt
    // "a" representa que o arquivo é aberto para ser escrito
    $data = date("d/m/Y H:i:s");
    $fp = fopen("log_provisao_de_gastos_limpar.txt", "a");
    $txt = "
--------------------------------------------------------------------------------
    ($data)
    $x Realizado corretamente.
--------------------------------------------------------------------------------\n\n";
    // Escreve "exemplo de escrita" no bloco1.txt
    $escreve = fwrite($fp, $txt);

    echo "Bytes Escritos: $escreve\n<br>\n<br>\n<br>";
    echo $txt;
    // Fecha o arquivo
    fclose($fp);
}

// Cria uma função que retorna o timestamp de uma data no formato DD/MM/AAAA
function geraTimestamp($data) {
    $partes = explode('/', $data);
    return mktime(0, 0, 0, $partes[1], $partes[0], $partes[2]);
}
