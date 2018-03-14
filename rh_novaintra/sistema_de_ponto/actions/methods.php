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

            $html .= "<table class='table table-condensed table-bordered'>";
            $html .= "<tr class='bg-info'><td colspan='4'>Detalhes de: " . $_REQUEST['nome'] . "</td></tr>";
            foreach ($lista as $dados) {
                $html .= "
                <tr class='valign-middle'>
                    <td>
                        <div class='no-padding col-xs-12 text-bold'>Horas à trabalhar: </div>
                        <div class='no-padding col-xs-12'>".$dados['horas_a_trabalhar']."</div>
                    </td>
                    <td>
                        <div class='no-padding col-xs-12 text-bold'>Horas trabalhadas: </div>
                        <div class='no-padding col-xs-12'>" . $dados['horas_trabalhadas'] . "</div>
                    </td>
                    <td>
                        <div class='no-padding col-xs-12 text-bold'>Horas Extras: </div>
                        <div class='no-padding col-xs-12'>" . $dados['horas_extras'] . "</div>
                    </td>
                </tr>
                <tr class='valign-middle'>
                    <td>
                        <div class='no-padding col-xs-12 text-bold'>Dsr: </div>
                        <div class='no-padding col-xs-12'>" . $dados['dsrs'] . " </span></div>
                    </td>
                    <td>
                        <div class='no-padding col-xs-12 text-bold'>Horas Atrasos: </div>
                        <div class='no-padding col-xs-12'>" . $dados['horas_atrasos'] . "</div>
                    </td>
                    <td>
                        <div class='no-padding col-xs-12 text-bold'>Horas Justificadas: </div>
                        <div class='no-padding col-xs-12'>" . $dados['horas_justificadas'] . "</div>
                    </td>
                </tr>
                <tr class='valign-middle'>
                    <td>
                        <div class='no-padding col-xs-12 text-bold'>Banco de horas: </div>
                        <div class='no-padding col-xs-12'>" . $dados['banco_de_horas'] . "</div>
                    </td>
                    <td>
                        <div class='no-padding col-xs-12 text-bold'>Banco de periodo: </div>
                        <div class='no-padding col-xs-12'>" . $dados['banco_de_perido'] . "</div>
                    </td>
                    <td>
                        <div class='no-padding col-xs-12 text-bold'>Faltas em dias: </div>
                        <div class='no-padding col-xs-12'>" . $dados['faltas_em_dias'] . "</div>
                    </td>
                </tr>
                <tr class='valign-middle'>
                    <td>
                        <div class='no-padding col-xs-12 text-bold'>Salário: </div>
                        <div class='no-padding col-xs-12'>" . $dados['salario_normal'] . "</div>
                    </td>
                    <td>
                        <div class='no-padding col-xs-12 text-bold'>Adicional Noturno: </div>
                        <div class='no-padding col-xs-12'>" . $dados['adicional_noturno'] . "</div>
                    </td>
                    <td>
                        <div class='no-padding col-xs-12 text-bold'>Porcentagem Adicional Noturno: </div>
                        <div class='no-padding col-xs-12'>" . $dados['percentual_adic_noturno'] . "%</div>
                    </td>
                </tr>
                <tr class='valign-middle'>
                    <td>
                        <div class='no-padding col-xs-12 text-bold'>Valor Hora: </div>
                        <div class='no-padding col-xs-4'>" . $dados['valor_hora'] . " </div><div class='no-padding col-xs-8 text-danger text-sm'>(Salário / Horas à trabalhar)</div>
                    </td>
                    <td>
                        <div class='no-padding col-xs-12 text-bold'>Desconto de Faltas: </div>
                        <div class='no-padding col-xs-4'>" . $dados['valor_faltas'] . " </div><div class='no-padding col-xs-8 text-danger text-sm'>(Salário / 30 * Faltas em dias)</div>
                    </td>
                    <td>
                        <div class='no-padding col-xs-12 text-bold'>Desconto Atrasos: </div>
                        <div class='no-padding col-xs-4'>" . $dados['valor_atraso'] . " </div><div class='no-padding col-xs-8 text-danger text-sm'>(Hora * Horas Atrasos)</div>
                    </td>
                </tr>
                <tr class='valign-middle'>
                    <td>
                        <div class='no-padding col-xs-12 text-bold'>Valor Hora Extra: </div>
                        <div class='no-padding col-xs-4'>" . $dados['valor_hora_extra'] . " </div><div class='no-padding col-xs-8 text-danger text-sm'>(Hora + 50% * Horas Extras)</div>
                    </td>
                    <td>
                        <div class='no-padding col-xs-12 text-bold'>Valor Adicional Noturno: </div>
                        <div class='no-padding col-xs-4'>" . $dados['valor_adicional'] . " </span></div><div class='no-padding col-xs-8 text-danger text-sm'>(Hora * " . $dados['percentual_adic_noturno'] . "% * Adicional Noturno)</div>
                    </td>
                </tr>";
            }
            $html .= "</table>";
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