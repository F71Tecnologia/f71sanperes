<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=false';</script>";
}
include('../../conn.php');
include('../../wfunction.php');
include('../../classes/ViagemClass.php');

$objViagem = new ViagemClass();

//$percent = 8.33333;

$usuario = carregaUsuario();
$id = $_REQUEST['id'];
$row = $objViagem->getViagemById($id); ?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="ISO-8859-9">
        <title>:: Intranet ::</title>
        <link href="../../favicon.png" rel="shortcut icon">
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="all">
        <link href="../../resources/css/font-awesome.min.css" rel="stylesheet" media="all">
        <link href="../../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../../resources/css/style-print.css" rel="stylesheet" media="all">
        <style>
            /*@media all {*/
                * { padding: 2px; font-size: 10px; }
                div.pagina
                {
                  page-break-after: always;
                  page-break-inside: avoid;
                    /*padding: 0.7cm;*/
                }

/*                @media print{
                    .print { border: none; -webkit-appearance: none;}
                }*/

                /*td { width: 8.3333% !important; }*/

                .no-border-vr {
                    border-top-width: 0!important;
                    border-bottom-width: 0!important;
                }
            /*}*/
        </style>
    </head>
    <body class="text-sm">
        <div class="no-print">
            <nav class="navbar navbar-default navbar-fixed-top">
                <div class="container-fluid">
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-3">
                        <div class="text-center">
                            <button type="button" id="voltar" class="btn btn-default navbar-btn" onclick="window.close()"><i class="fa fa-reply"></i> Voltar</button>
                            <button type="button" id="imprimir" class="btn btn-success navbar-btn"><i class="fa fa-print"></i> Imprimir</button>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
        <div class="pagina">
            <div class="col-xs-12 no-padding">
                <table class="table table-bordered table-condensed no-margin valign-middle">
                    <tr>
                        <td colspan="2" class="text-center"><img src="/intranet/imagens/logomaster<?php echo $usuario['id_master'] ?>.gif" style="width: 100px; height: 100px"></td>
                    </tr>
                    <tr>
                        <td class="text-bold">Presta��o de conta de viagem: <?php echo $row['id_viagem'] ?></td>
                        <td>Data de Emiss�o: <?php echo $row['data'] ?></td>
                    </tr>
                </table>
                <table class="table table-bordered table-condensed no-margin valign-middle">
                    <tr>
                        <td class="no-border border-l">Presta��o de Serv.:</td>
                        <td class="no-border"><?php echo $row['nome'] ?></td>
                        <td class="no-border">CPF:</td>
                        <td class="no-border border-r"><?php echo $row['cpf'] ?></td>
                    </tr>
                    <tr>
                        <td class="no-border border-l">Cargo / Fun��o:</td>
                        <td class="no-border"><?php echo $row['funcao'] ?></td>
                        <td class="no-border">Loca��o:</td>
                        <td class="no-border border-r"><?php echo $row['locacao'] ?></td>
                    </tr>
                    <tr>
                        <td class="no-border border-l">Contato:</td>
                        <td class="no-border"><?php echo $row['tel_cel'] ?></td>
                        <td class="no-border">Dados Banc�rios:</td>
                        <td class="no-border border-r"><?php echo "{$row['banco']}: {$row['agencia']} / {$row['conta']}" ?></td>
                    </tr>
                    <tr>
                        <td class="no-border border-l" style="border-top: 1px solid #ddd !important;">Data Inicio:</td>
                        <td class="no-border" style="border-top: 1px solid #ddd !important;"><?php echo $row['data_ini'] ?></td>
                        <td class="no-border" style="border-top: 1px solid #ddd !important;">Data T�rmino:</td>
                        <td class="no-border border-r" style="border-top: 1px solid #ddd !important;"><?php echo $row['data_fim'] ?></td>
                    </tr>
                    <tr>
                        <td class="no-border border-l">Objetivo da Viagem:</td>
                        <td colspan="3" class="no-border"><?php echo $row['descricao'] ?></td>
                    </tr>
                    <tr>
                        <td class="no-border border-l">Trajeto:</td>
                        <td colspan="3" class="no-border"><?php echo $row['trajeto'] ?></td>
                    </tr>
                    <tr>
                        <td class="no-border border-l">Cidade de Origem</td>
                        <td class="no-border"><?php echo $row['origem'] ?></td>
                        <td class="no-border">Cidade de Destino:</td>
                        <td class="no-border border-r"><?php echo $row['destino'] ?></td>
                    </tr>
                    <tr>
                        <td class="no-border border-l">Meio de Transporte:</td>
                        <td class="no-border"><?php echo ($row['id_tipo_meio_transporte'] == 1) ? 'ONIBUS' : 'CARRO' ?></td>
                        <td class="no-border">Ve�culo:</td>
                        <td class="no-border border-r"><?php echo ($row['id_tipo_meio_transporte'] == 1) ? $row['linha'] : $row['modelo'] . ' - ' . $row['placa'] ?></td>
                    </tr>
                </table>
                <table class="table table-bordered table-condensed no-margin valign-middle">
                    <tr>
                        <td colspan="6" class="text-center text-bold">Despesas de Viagem</td>
                    </tr>
                    <tr>
                        <td style="width: 1cm;" class="text-center">Refer�ncia</td>
                        <td class="text-center">Descri��o da Despensa</td>
                        <td class="text-center">Unid.</td>
                        <td style="width: 1cm;" class="text-center">Quantidade</td>
                        <td style="width: 2.2cm;" class="text-center">Valor Unit�rio</td>
                        <td class="text-center">Valor Total</td>
                    </tr>
                    <?php foreach ($objViagem->getItensByIdViagem($row['id_viagem']) as $key => $value) { ?>
                    <tr>
                        <td class="text-center"><?php echo $key ?></td>
                        <td class="text-left"><?php echo $value['nome'] ?></td>
                        <td class="text-center"><?php echo $value['unidade'] ?></td>
                        <td class="text-center"><?php echo $value['qtd'] ?></td>
                        <td class="text-right"><?php echo number_format($value['valor_unitario'],2,',','.') ?></td>
                        <td class="text-right"><?php echo number_format($value['valor'],2,',','.') ?></td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="4" class="text-center"></td>
                        <td class="text-right text-bold">TOTAL:</td>
                        <td class="text-right text-bold"><?php echo number_format($row['valor'],2,',','.') ?></td>
                    </tr>
                </table>
                <table class="table table-bordered table-condensed no-margin valign-middle">
                    <tr>
                        <td colspan="4" class="text-center text-bold">&nbsp;</td>
                    </tr>
                    <tr>
                        <th class="text-center"></th>
                        <th class="text-center">Solicita��o</th>
                        <th class="text-center">Autoriza��o</th>
                        <th class="text-center">Libera��o</th>
                    </tr>
                    <tr>
                        <td class="text-center">Data:</td>
                        <td class="text-center">___/___/______</td>
                        <td class="text-center">___/___/______</td>
                        <td class="text-center">___/___/______</td>
                    </tr>
                    <tr>
                        <td class="text-center">Visto:</td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-center text-bold">&nbsp;</td>
                    </tr>
                    <tr>
                        <th class="text-center"></th>
                        <th class="text-center">Pagamento</th>
                        <th class="text-center">Recebimento</th>
                        <th class="text-center">Observa��o</th>
                    </tr>
                    <tr>
                        <td class="text-center">Data:</td>
                        <td class="text-center">___/___/______</td>
                        <td class="text-center">___/___/______</td>
                        <td class="text-center"></td>
                    </tr>
                    <tr>
                        <td class="text-center">Visto:</td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="no-border"></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="no-border">Obs:</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="no-border">
                            1) O adiantamento para realiza��o da viagem dever� ser solicitado diretamente a chefia imediata do favorecido. Esta dever� autorizar e encaminhar ao departamento financeiro com anteced�ncia m�nima de 72 (setenta e duas) horas, a fim de entrar na programa��o de pagamentos em tempo h�bil.<br>
                            2) Deve ser feita urna previs�o do total dos gastos a serem realizados durante a viagem, sendo tolerada uma varia��o m�xima de 20% (vinte por cento);<br>
                            3) Quando a solicita��o do adiantamento de viagem dever� ser verificado se o favorecido n�o tem nenhum adiantamento anterior pendente de acerto junto ao financeiro;<br>
                            4) Um novo adiantamento para viagem somente poder� ser liberado quando tiver sido apresentada e aprovada peio financeiro a presta��o de contas de viagem do per�odo anterior;<br>
                            5) A presta��o de contas das despesas realizadas dever� ser feita atrav�s do formul�rio de Presta��o de Contas de Viagem, no m�ximo, at� tr�s dias �teis ap�s o retomo a sua base. A presta��o de contas n�o apresentada no prazo poder� gerar desconto no pagamento do favorecido;<br>
                            6) Todas as despesas dever�o ser comprovadas anexando-se a presta��o de contas as notas fiscais ou cupons fiscais correspondentes.<br>
                            7) Os recibos devem ser datados e identificados com o nome da localidade da viagem.
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <!-- javascript aqui -->
        <script src="../../js/jquery-1.10.2.min.js" type="text/javascript"></script>
        <script src="../../resources/js/print.js" type="text/javascript"></script>
    </body>
</html>
