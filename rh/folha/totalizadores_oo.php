<?php
// Incluindo Arquivos
include_once('../../conn.php');
include_once('../../funcoes.php');
include_once('../../classes_permissoes/acoes.class.php');
include_once('../../classes/FolhaClass.php');

/**
 * ATRIBUTOS
 */
$idFolha = 1968;

/**
 * OBJETOS
 */
$folha = new folha();
$dados = $folha->totalizador($idFolha);

?>

<!DOCTYPE HTML>
<html lang="pt-br">
    <head>
        <title>Totalzador de Rescisão</title>
        <style type="text/css">
            #tbTotalRescisao{border: 1px solid #ccc; padding: 5px; width: 100%; box-sizing: border-box;}
            #tbTotalRescisao th{padding: 10px 5px; background: #d2d2d2;}
            .nomeColuna td{padding: 10px 5px; background: #EDECEC;}
            .dadosColuna td{padding: 10px 5px;}
            .titulo{ font-family: verdana; font-size: 14px; text-transform: uppercase; color: #333;}
            .subTitulo{font-family: verdana; font-size: 12px; text-transform: uppercase; color: #333;}
            .zebraUm{background: #F9F3F3;}
            .zebraDois{background: #FEFCFC ;}
            .negrito{ font-weight: bold; font-family: verdana; font-size: 12px; text-transform: uppercase;}
            .foot{ background: #F9F3F3;}
        </style>
        <script src="../../js/jquery-1.11.1.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $('table tbody tr:odd').addClass('zebraUm');
                $('table tbody tr:even').addClass('zebraDois');
            });
        </script>
    </head>
    <body>
        <table id="tbTotalRescisao">
            <thead>
                <tr>
                    <th colspan="3" class="titulo">Totalizador de Rescisão</th> 
                </tr>
            </thead>
            <tbody>
                <tr class="nomeColuna">
                    <td class="subTitulo">Verba</td>
                    <td class="subTitulo">Crédito</td>
                    <td class="subTitulo">Débito</td>
                </tr>
                <?php $totalCredito = 0; $totalDebito = 0; ?>
                <?php foreach($dados[$idFolha] as $tipo => $values){ ?>
                    <?php foreach($values as $key => $dados){ ?>
                        <tr class="dadosColuna">
                            <td class="subTitulo"><?php echo $dados['verba']; ?></td>
                            <td class="subTitulo">
                                <?php 
                                    if($dados['tipo'] == "CREDITO"){
                                        $totalCredito += $dados['valor'];
                                        echo "R$ " . number_format($dados['valor'],2,',','.');
                                    }
                                ?>
                            </td>
                            <td class="subTitulo">
                                <?php 
                                    if($dados['tipo'] == "DEBITO"){
                                        $totalDebito += $dados['valor'];
                                        echo "R$ " . number_format($dados['valor'],2,',','.');
                                    }
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr class="foot">
                    <td>Total:</td>
                    <td><?php echo "R$ " . number_format($totalCredito,2,',','.'); ?></td>
                    <td><?php echo "R$ " . number_format($totalDebito,2,',','.'); ?></td>
                </tr>
            </tfoot>
        </table>
    </body>
</html>