<?php
include("../conn.php");
include("../wfunction.php");
include("../classes/ContabilLoteClass.php");

require_once ('../classes/ContabilContasSaldoClass.php');

$objContas = new ContabilContasSaldoClass();

$sql = "SELECT * FROM contabil_lote;";
$qry = mysql_query($sql);
while($row = mysql_fetch_assoc($qry)){
    echo "<h1>Lote: {$row['id_lote']}</h1><br>";
    if($row['status'] == 0){
        $sql2 = "SELECT * FROM contabil_lancamento WHERE id_lote = {$row['id_lote']}";
        $qry2 = mysql_query($sql2);
        $arrayLancamento = array();
        while($row2 = mysql_fetch_assoc($qry2)){
            $arrayLancamento[] = $row2['id_lancamento'];
        }
        $sql3 = "SELECT * FROM contabil_lancamento_itens WHERE id_lancamento IN (".implode(',',$arrayLancamento).")";
        $qry3 = mysql_query($sql3);
        $arrayLancamentoItens = array();
        while($row3 = mysql_fetch_assoc($qry3)){
            $arrayLancamentoItens[] = $row3['id_lancamento_itens'];
        }
        
        $update = "
        UPDATE contabil_lancamento SET status = 0 WHERE id_lote = '{$row['id_lote']}';
        UPDATE contabil_lancamento_itens SET status = 0 WHERE id_lancamento IN (".implode(',',$arrayLancamento).");
        UPDATE contabil_contas_saldo_dia SET status = 0 WHERE id_lancamento_itens IN (".implode(',',$arrayLancamentoItens).");
        ";
        print_array($update);
        $update = mysql_query($update);
        
    } else {
        
        $sql2 = "SELECT * FROM contabil_lancamento WHERE id_lote = {$row['id_lote']}";
        $qry2 = mysql_query($sql2);
        $arrayLancamento = array();
        while($row2 = mysql_fetch_assoc($qry2)){
            $arrayLancamento[$row2['status']][] = $row2['id_lancamento'];
        }
        
        foreach ($arrayLancamento as $statusLancamento => $lancamentos) {
            
            if($statusLancamento == 0){
                
                $sql3 = "SELECT * FROM contabil_lancamento_itens WHERE id_lancamento IN (".implode(',',$lancamentos).")";
                $qry3 = mysql_query($sql3);
                $arrayLancamentoItens = array();
                while($row3 = mysql_fetch_assoc($qry3)){
                    $arrayLancamentoItens[] = $row3['id_lancamento_itens'];
                }
                $update = "
                UPDATE contabil_lancamento_itens SET status = 0 WHERE id_lancamento IN (".implode(',',$lancamentos).");
                UPDATE contabil_contas_saldo_dia SET status = 0 WHERE id_lancamento_itens IN (".implode(',',$arrayLancamentoItens).");
                ";
                print_array($update);
                $update = mysql_query($update);
                
            } else {
                
                $sql3 = "SELECT * FROM contabil_lancamento_itens WHERE id_lancamento IN (".implode(',',$lancamentos).")";
                $qry3 = mysql_query($sql3);
                $arrayLancamentoItens = array();
                while($row3 = mysql_fetch_assoc($qry3)){
                    $arrayLancamentoItens[$row3['status']][] = $row3['id_lancamento_itens'];
                }
                
                foreach ($arrayLancamentoItens as $statusLancamentoItens => $lancamentosItens) {
                    
                    if($statusLancamentoItens == 0){
                        
                        $update = "
                        UPDATE contabil_contas_saldo_dia SET status = 0 WHERE id_lancamento_itens IN (".implode(',',$lancamentosItens).");
                        ";
                        print_array($update);
                        $update = mysql_query($update);
                        
                    }
                }
            }
        }
    }
}
?>