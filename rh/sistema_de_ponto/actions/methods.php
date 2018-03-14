<?php

include_once("../../../conn.php");
include_once("../classes/PontoClass.php");

if (isset($_REQUEST['method']) && !empty($_REQUEST['method'])) {

    /**
     * LISTAR DADOS DE PONTO
     */
    if ($_REQUEST['method'] == "listaPontos") {

        $retorno = array('status' => 0);
        $ponto = new Ponto();
        $lista = $ponto->listaDadosPontoById($_REQUEST['id_ponto']);
        if (count($lista) > 0) {
            $retorno = array("status" => 1, "dados" => $lista);
        }

        echo json_encode($retorno);
        exit();
    }

    /**
     * LISTAR DADOS DE PONTO
     */
    if ($_REQUEST['method'] == "listaDetalhesMovimentoPonto") {

        $retorno = array('status' => 0);
        $ponto = new Ponto();
        $lista = $ponto->listaDetalhesMovimentoPonto($_REQUEST['id_header']);
        if (count($lista) > 0) {
            $retorno = array("status" => 1, "dados" => $lista);
        }

        echo json_encode($retorno);
        exit();
    }

    /**
     * LISTAR DETALHES DE UM FUNCIONARIO
     */
    if ($_REQUEST['method'] == "detalhesPonto") {

        $html = "";
        $ponto = new Ponto();
        $lista = $ponto->listaDetalhesDeFuncionarioByPis($_REQUEST['pis']);
        if (count($lista) > 0) {

            $html .= "<div id='detal'>";
            $html .= "<h3>Detalhes de: " . $_REQUEST['nome'] . "</h3>";
            foreach ($lista as $dados) {
                $html .= "<div class='itens'>
                                <p>Horas à trabalhar: <br><span class='valor'>" . $dados['horas_a_trabalhar'] . " </span><span class='expressao'></span></p>
                            </div>
                            <div class='itens'>
                                <p>Horas trabalhadas: <br><span class='valor'>" . $dados['horas_trabalhadas'] . " </span><span class='expressao'></span></p>
                            </div>
                            <div class='itens'>
                                <p>Horas Extras: <br><span class='valor'>" . $dados['horas_extras'] . " </span><span class='expressao'></span></p>
                            </div>
                            <div class='itens'>
                                <p>Dsr: <br><span class='valor'>" . $dados['dsrs'] . " </span><span class='expressao'></span></p>
                            </div>
                            <div class='itens'>
                                <p>Horas Atrasos: <br><span class='valor'>" . $dados['horas_atrasos'] . " </span><span class='expressao'></span></p>
                            </div>
                            <div class='itens'>
                                <p>Horas Justificadas: <br><span class='valor'>" . $dados['horas_justificadas'] . " </span><span class='expressao'></span></p>
                            </div>
                            <div class='itens'>
                                <p>Banco de horas: <br><span class='valor'>" . $dados['banco_de_horas'] . " </span><span class='expressao'></span></p>
                            </div>
                            <div class='itens'>
                                <p>Banco de periodo: <br><span class='valor'>" . $dados['banco_de_perido'] . " </span><span class='expressao'></span></p>
                            </div>
                            <div class='itens'>
                                <p>Faltas em dias: <br><span class='valor'>" . $dados['faltas_em_dias'] . " </span><span class='expressao'></span></p>
                            </div>
                            <div class='itens'>
                                <p>Salário: <br><span class='valor'>" . $dados['salario_normal'] . " </span><span class='expressao'></span></p>
                            </div>
                            <div class='itens'>
                                <p>Adicional Noturno: <br><span class='valor'>" . $dados['adicional_noturno'] . " </span><span class='expressao'></span></p>
                            </div>
                            <div class='itens'>
                                <p>Porcentagem Adicional Noturno: <br><span class='valor'>" . $dados['percentual_adic_noturno'] . "% </span><br /><span class='expressao'></span></p>
                            </div>
                            <div class='itens'>
                                <p>Valor Hora: <br><span class='valor'>" . $dados['valor_hora'] . " </span><span class='expressao'>(Salário / Horas à trabalhar)</span></p>
                            </div>
                            <div class='itens'>
                                <p>Desconto de Faltas: <br><span class='valor'>" . $dados['valor_faltas'] . " </span><span class='expressao'>(Salário / 30 * Faltas em dias)</span></p>
                            </div>
                            <div class='itens'>
                                <p>Desconto Atrasos: <br><span class='valor'>" . $dados['valor_atraso'] . " </span><span class='expressao'>(Hora * Horas Atrasos)</span></p>
                            </div>
                            <div class='itens'>
                                <p>Valor Hora Extra: <br><span class='valor'>" . $dados['valor_hora_extra'] . " </span><span class='expressao'>(Hora + 50% * Horas Extras)</span></p>
                            </div>
                            <div class='itens'>
                                <p>Valor Adicional Noturno: <br><span class='valor'>" . $dados['valor_adicional'] . " </span><span class='expressao'>(Hora * " . $dados['percentual_adic_noturno'] . "% * Adicional Noturno)</span></p>
                            </div>
                            ";
            }
            $html .= "</div>";
        }

        echo utf8_encode($html);
    }

    /**
     * FINALIZANDO EXPORTAÇÃO DO PONTO PARA A FOLHA DE PAGAMENTO
     */
    if ($_REQUEST['method'] == "finalizaPonto") {
        $retorno = array("status" => 0);
        $ponto = new Ponto();
        if (is_array($_REQUEST['participante']) && !empty($_REQUEST['participante'])) {
            foreach ($_REQUEST['participante'] as $pis) {
                $sql = $ponto->lancarMovimentosDoPonto($pis);
            }
        }

        if ($sql['status']) {
            $ponto->updateHeaderPontoParaFinalizado($sql['header']);
            $retorno = array("status" => 1);
        }
        echo json_encode($retorno);
        exit;
    }
    
    /**
     * REMOVER PONTOS NÃO FINALIZADOS
     */
    if($_REQUEST['method'] == "removerPonto"){
        $retorno = array("status" => 0);
        $ponto = new Ponto();
        $sql = $ponto->removePonto($_REQUEST['id_header']);
        if($sql){
            $retorno = array("status" => 1);
        }
        echo json_encode($retorno);
        exit;
    }

    /**
     * DESPROCESSAR PONTO
     */
    if ($_REQUEST['method'] == "desprocessarPonto") {

        $retorno = array("status" => 0);
        $ponto = new Ponto();
        $sql = $ponto->removePontoFinalizado($_REQUEST['id_header']);
        if($sql){
            $retorno = array("status" => 1);
        }
        echo json_encode($retorno);
        exit;
    
    }
}