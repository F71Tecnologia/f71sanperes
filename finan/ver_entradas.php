<?php
include("../conn.php");
include("../classes/EntradaClass.php");
include("../wfunction.php");

$id_nota = $_REQUEST['id'];
$entrada = new Entrada();
$result = $entrada->getEntradaNotas($id_nota);

$log = array(5,75,9);

if(in_array($_COOKIE['logado'],$log)){
    $ok = true;
}
?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
    </head>
    <body>
        <form action="" method="post" class="form-horizontal top-margin1" name="form1" id="form1">            
            <table class='table table-hover table-striped'>
                <thead>
                    <tr>
                        <th>Cod.</th>
                        <th>Data Pgt.</th>
                        <th>Banco</th>
                        <th>Status</th>  
                        <th>Valor</th>
                        <?php if($ok){ ?>
                        <th></th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysql_fetch_assoc($result)){ ?>
                    <tr id="<?php echo $row['id_entrada']; ?>">
                        <td><?php echo $row['id_entrada']; ?></td>                        
                        <td><?php echo $row['data_nf']; ?></td>
                        <td><?php echo $row['banco_id'] . " - " . $row['banco_nome']; ?></td>
                        <td><?php echo ($row['status_entrada'] == 1) ? "Não Confirmada" : "Confirmada"; ?></td>                        
                        <td><?php echo formataMoeda($row['valor_nf']); ?></td>
                        <?php if($ok){ ?>
                        <td><img src="../imagens/icones/icon-delete.gif" title="Excluir" class="bt-image" data-type="excluir" id="del" data-key="<?php echo $row['id_entrada']; ?>" /></td>
                        <?php } ?>
                    </tr>
                    <?php
                    $total += $row['valor_nf'];
                    } ?>
                    <tr class="warning">
                        <td class="text-right" colspan="4">
                            Total:
                        </td>
                        <td>
                            <strong><?php echo formataMoeda($total); ?></strong>
                        </td>
                        <?php if($ok){ ?>
                        <td></td>
                        <?php } ?>
                    </tr>
                </tbody>
            </table>            
        </form>
        <script src="../resources/js/financeiro/prestador.js"></script>
    </body>
</html>