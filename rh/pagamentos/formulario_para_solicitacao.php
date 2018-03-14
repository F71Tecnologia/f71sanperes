<?php
    if (empty($_COOKIE['logado'])) {
        print "<script>location.href = '../../login.php?entre=true';</script>";
        exit;
    }
    
    include('../../conn.php');
    include("../../funcoes.php");
    include("../../wfunction.php");

    $usuario = carregaUsuario();
    
    $id_saida = $_REQUEST['id_saida'];
    $method = $_REQUEST['method'];
    $tipo_guia = $_GET['tipo'];// 1 - FÉRIAS, 2 - RECISÂO, 3 - MULTA FGTS, 4 - RESCISÃO COMPLEMENTAR, 5 - MULTA FGTS COMPLEMENTAR
    
    $id_clt = $_REQUEST['id_clt'];
    $sql = "SELECT A.*, B.cpf, B.nome AS nome_clt, B.nome_banco, B.agencia, B.agencia_dv, B.conta, B.conta_dv, B.tipo_conta, C.regiao AS regiao_nome, D.unidade AS unidade_nome, B.id_projeto AS projeto_clt, E.nome AS projeto_nome, UCASE(F.nome) AS tipo_nome, G.nome AS curso_nome
                FROM saida AS A
                    LEFT JOIN rh_clt AS B ON (B.id_clt = $id_clt)
                    LEFT JOIN regioes AS C ON (C.id_regiao = A.id_regiao)
                    LEFT JOIN unidade AS D ON (D.id_unidade = B.id_unidade)
                    LEFT JOIN projeto AS E ON (E.id_projeto = B.id_projeto)
                    LEFT JOIN entradaesaida AS F ON (F.id_entradasaida = A.tipo)
                    LEFT JOIN curso AS G ON (G.id_curso = B.id_curso)
            WHERE id_saida = $id_saida";
    $query = mysql_query($sql);
    $rowSaida = mysql_fetch_assoc($query);
    //    print_r($rowSaida);

    $id_regiao = $rowSaida['id_regiao'];
    $projeto_clt = $rowSaida['projeto_clt'];
    
    switch($projeto_clt) {
        case '1':$projeto = 'Institucional - SP';break;
        case '2':$projeto = 'Norte';break;
        case '3':$projeto = 'Centro';break;
    }
    
     if($rowSaida['tipo'] == 8) {
        $tipo = 'FÉRIAS';
        $formPagamento = 'DEPÓSITO BANCÁRIO';
        $dadosBancarios = 'Informações Bancárias: ' . $rowSaida['nome_banco'] . ' - Agência: ' . $rowSaida['agencia'] . '-' . $rowSaida['agencia_dv'] . ' - Conta ' . $rowSaida['tipo_conta'] . ': ' . $rowSaida['conta'] . '-' . $rowSaida['conta_dv'];
    } elseif($rowSaida['tipo'] == 31) {
        $tipo = 'RESCISÃO';
        $formPagamento = 'DEPÓSITO BANCÁRIO';
        $dadosBancarios = 'Informações Bancárias: ' . $rowSaida['nome_banco'] . ' - Agência: ' . $rowSaida['agencia'] . '-' . $rowSaida['agencia_dv'] . ' - Conta ' . $rowSaida['tipo_conta'] . ': ' . $rowSaida['conta'] . '-' . $rowSaida['conta_dv'];
    } elseif($rowSaida['tipo'] == 34) {
        $tipo = '<br>GUIA DE RECOLHIMENTO GRRF (FGTS)';
        $formPagamento = 'BOLETO ANEXO';
        $dadosBancarios ="Tipo de Pagamento: Boleto Anexo";
    } elseif($rowSaida['tipo'] == 170 && $tipo_guia == 4 ) {
        $tipo = '<br>RESCISÃO COMPLEMENTAR';
        $formPagamento = 'DEPÓSITO BANCÁRIO';
        $dadosBancarios = 'Informações Bancárias: ' . $rowSaida['nome_banco'] . ' - Agência: ' . $rowSaida['agencia'] . '-' . $rowSaida['agencia_dv'] . ' - Conta ' . $rowSaida['tipo_conta'] . ': ' . $rowSaida['conta'] . '-' . $rowSaida['conta_dv'];
    } elseif($rowSaida['tipo'] == 170 && $tipo_guia == 5 ) {
        $tipo = '<br>GRRF (FGTS)';
        $formPagamento = 'BOLETO ANEXO';
        $dadosBancarios ="Tipo de Pagamento: Boleto Anexo";
    }
    
//    if($rowSaida['tipo'] == 34) {
//        $formPagamento = 'BOLETO ANEXO';
//        $dadosBancarios ="Tipo de Pagamento: Boleto Anexo";
//    } else {
//        $formPagamento = 'DEPÓSITO BANCÁRIO';
//        $dadosBancarios = 'Informações Bancárias: ' . $rowSaida['nome_banco'] . ' - Agência: ' . $rowSaida['agencia'] . '-' . $rowSaida['agencia_dv'] . ' - Conta ' . $rowSaida['tipo_conta'] . ': ' . $rowSaida['conta'] . '-' . $rowSaida['conta_dv'];
//    }
    
   
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Formulário para Solicitação</title>
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        
        <style>
            p {
                margin: 0;
            }
            
            span {
                font-size: 20px;
                font-weight: bold;
                vertical-align: middle;
            }
        </style>
        <style media="print">
            .botao {
                display: none;
            }
</style>
    </head>
    <body>
        <div style="width: 920px" class="container">
            <div style="width: 63px;margin: 15px auto 0;">
                <button onclick="window.location.href = 'index.php?id=1&regiao='" class="btn btn-warning botao">Voltar</button>
            </div>
            
            <div style="width: 130px;margin: 0 auto;">
                <img src=<?php $_SERVER['DOCUMENT_ROOT']?>"/intranet/imagens/logomaster1.gif"/>
            </div>
            
            <div class="panel-heading text-bold hidden-print" align="center" style="width: 475px;margin: 0 auto; font-size: 20px">
                Formulário de Solicitação
            </div>
            
            <div style="padding: 15px; margin: 0 0 5px;" class="col-sm-12 panel panel-default">
                <p>SETOR SOLICITANTE: <span>RECURSOS HUMANOS</span></p>
            </div>
            
            <div style="float:left; width: 49%;padding: 15px; margin: 0 0 5px;" class="panel panel-default">
                <p>EMISSÃO: <span><?php echo date('d/m/Y')?></span></p>
            </div>
            
            <div style="float:right; width: 49%;padding: 15px; margin: 0 0 5px;" class="panel panel-default">
                <p>VENCIMENTO: <span><?php echo date('d/m/Y', strtotime($rowSaida['data_vencimento']))?></span></p>
            </div>
            
            <div style="padding: 15px; margin: 0 0 5px;" class=" col-sm-12 panel panel-default">
                <p>CONTRATO DE GESTÃO: <span><?php echo $projeto ?></span></p>
            </div>
            
            <div style="padding: 15px; margin: 0 0 5px;" class=" col-sm-12 panel panel-default">
                <p>UNIDADE: <span><?php echo $rowSaida['unidade_nome']?></span></p>
            </div>
            
            <div style="padding: 15px; margin: 0 0 5px;" class=" col-sm-12 panel panel-default">
                <p>CARGO: <span><?php echo $rowSaida['curso_nome']?></span></p>
            </div>
            
            <div style="clear: both;padding: 15px; margin: 0 0 5px;" class="col-sm-12 panel panel-default">
                <p>FORMA DE PAGAMENTO: <span><?php echo $formPagamento?></span></p>
            </div>
            
            <div style="border-radius: 0; padding: 15px; margin: 0 0 5px;" class="col-sm-12 panel panel-default">
                <p class="text-center"><span>SOLICITAÇÃO DE PAGAMENTO DE <?php echo $tipo ?></span></p>
            </div>
            
            <div style="border-radius: 0; padding: 15px; margin: 0 0 5px;" class="col-sm-12 panel panel-default">
                <p style="margin: 0 0 2px">Beneficiário: <?php echo $rowSaida['nome_clt']?></p>
                <p style="margin: 3px 0 2px">CPF / CNPJ: <?php echo $rowSaida['cpf']?></p>
                <p style="margin: 3px 0 2px"><?php echo $dadosBancarios?></p>
                <p style="margin: 3px 0 2px">Valor Bruto: R$<?php echo $rowSaida['valor']?></p>
                <p style="margin: 3px 0 2px">Impostos: R$0,00</p>
                <p style="margin: 3px 0 0">Valor Líquido: R$<?php echo $rowSaida['valor']?></p>
            </div>
            
            <div style="border-radius: 0; padding: 15px; margin: 0 0 5px;" class="col-sm-12 panel panel-default">
                <p class="text-center"><span>JUSTIFICATIVA</span></p>
            </div>
            
            <div style="border-radius: 0; padding: 15px; margin: 0 0 5px;" class="col-sm-12 panel panel-default">
                <p style="margin: 0 0 2px"><?php echo $rowSaida['descricao']?></p>
                <p style="margin: 3px 0 0">Solicitação Nº: <span><?php echo $rowSaida['id_saida']?></span></p>
            </div>
            
            <div style="padding: 15px; margin: 0 0 5px;" class="col-sm-12 panel panel-default">
                <p style="font-size: 12px; margin-bottom: 10px">Assinatura/Carimbo do Chefe do Setor:</p>
            </div>
            <div style="padding: 0;" class="col-sm-12"><hr style="margin: 10px 0"/></div>
            <!--
            <div style="padding: 5px 0 0;margin: 0 0 5px;" class="col-sm-12">
                <p class="text-center" style="font-size: 9px">IABAS - Instituto de Atenção Básica e Avançada à Saúde</p>
                <p class="text-center" style="font-size: 9px">www.iabas.org.br</p>
                
                <div style="float:left; width: 33%; padding: 5px 5px 0 0;font-size: 9px" >
                    <p style="font-weight: bold">SEDE</p>
                    <p>Av. Luis Carlos Prestes, 350 - Loja C, Salas 111 à 115, Barra Trade II</p>
                    <p>Barra da Tijuca - Rio de Janeiro - RJ - CEP: 22775-055</p>
                    <p>Telefone: (21) 3550-3300</p>
                </div>
                <div style="float:left; width: 33%; padding: 5px 0 0;font-size: 9px" >
                    <p style="font-weight: bold">FILAIS</p>
                    <p>Rua Diogo de Faria, 66 - Vila Mariana</p>
                    <p>São Paulo - SP - CEP: 04037-000</p>
                    <p>Telefone: (11) 5904-6505</p>
                </div>
                <div style="float:left; width: 33%; padding: 5px 0 0;font-size: 9px" >
                    <p style="font-weight: bold; color: white">.</p>
                    <p>Av Paulista, 2300 - Andar Pilotis - Bela Vista </p>
                    <p>São Paulo - SP - CEP: 01310-000</p>
                    <p>Telefone: (11) 2847-4525 </p>
                </div>
            </div>-->
            
        </div>
        
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
    </body>
</html>