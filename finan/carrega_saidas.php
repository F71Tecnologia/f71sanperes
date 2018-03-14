<?php 
ini_set('memory_limit', '2048M');
include "../conn.php";
require("../wfunction.php");
require("../funcoes.php");
require("../classes/FinaceiroClass.php");
require("../classes/EntradaClass.php");
require("../classes/SaidaClass.php");
require("../classes/CaixinhaClass.php");
include("../classes_permissoes/acoes.class.php");

$charset = mysql_set_charset('utf8');

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : NULL;
$usuario = carregaUsuario();

$objAcoes = new Acoes();
$objFinanceiro = new Financeiro();
$objCaixinha = new CaixinhaClass();

$objSaida = new Saida();

$objEntrada = new Entrada();

//print_array($_REQUEST);exit;

$condicao[] = ($_REQUEST['data_ini']) ? "A.data_vencimento >= '" . implode('-',array_reverse(explode('/', $_REQUEST['data_ini']))) . "'" : '';
$condicao[] = ($_REQUEST['data_fim']) ? "A.data_vencimento <= '" . implode('-',array_reverse(explode('/', $_REQUEST['data_fim']))) . "'" : '';
$condicao[] = ($_REQUEST['id_projeto']) ? "A.id_projeto = '{$_REQUEST['id_projeto']}'" : '';
$condicao[] = ($_REQUEST['tipo'] && $_REQUEST['tipo'] != 't') ? "A.tipo = '{$_REQUEST['tipo']}'" : '';
$condicao = array_filter($condicao);
//print_array($condicao);
$dados = $objFinanceiro->getSaidaEntradaBanco(null, $_REQUEST['id_banco'],false, $condicao); 

$permissao[118] = $objAcoes->verifica_permissoes(118);
$permissao[119] = $objAcoes->verifica_permissoes(119);
$permissao[120] = $objAcoes->verifica_permissoes(120);
$permissao[121] = $objAcoes->verifica_permissoes(121);

//$array['data'][] = ["Tiger Nixon", "System Architect", "Edinburgh", "5421", "2011/04/25", "$320,800"];
if(count($dados) > 0) {
    foreach ($dados as $chave => $itens){ 
        foreach ($itens as $row_saida){ 
            if($_COOKIE['debug'] == 666) {
    //            print_array($row_saida['id_saida'] . ' * ' . utf8_decode($row_saida['nome']));
                print_array($row_saida['id_saida']);
            }

            $type = $botoes1 = $botoes2 = $botoes3 = $botoes4 = $checkbox = null;
            $total[$chave] += str_replace(',','.',$row_saida['valor']);
            $id = ($chave == 4) ? $row_saida['id_entrada'] : $row_saida['id_saida'];
            $tipo = ($chave == 4) ? 'entrada' : 'saida' ;
            $totalizador_individual += $row_saida['total'];

            $checkbox = "<input type='checkbox' class='{$tipo}s_check' name='{$tipo}s[]' value='{$id}' data-id='{$id}' data-nome='{$row_saida['nome']}' data-val='" . str_replace(',','.',$row_saida['valor']) . "' data-valor='R$ ".number_format($row_saida['total'], 2, ',', '.')."' />";
            if ($tipo == 'saida') { 
                if($row_saida['anexos_pg'] > 0) {
                    $botoes1[] = "<button type='button' class='btn btn-xs btn-primary verComprovante' data-original-title='Ver Comprovante de Pagamento' data-key='{$row_saida['id_saida']}' data-toggle='tooltip'><i class='fa fa-money'></i></button>";
                }
                if($permissao[121]) { $botoes1[] = "<button type='button' class='btn btn-xs btn-default duplicarSaida btnAcoes' data-original-title='Duplicar sa&iacute;da' data-key='{$id}' data-toggle='tooltip'><i class='fa fa-copy'></i></button>"; }
                $botoes1[] = "<button type='button' class='btn btn-xs btn-info anexarSaida btnAcoes' data-original-title='Gerenciar Anexos' data-key='{$row_saida['id_saida']}' data-toggle='tooltip'><span class='bg-warning testesapn text-primary ".(($row_saida['anexos'] + $row_saida['anexo_rescisao'] == 0) ? 'hide':'')."'>".($row_saida['anexos'] + $row_saida['anexo_rescisao'])."</span><i class='fa fa-paperclip'></i></button>";
                $botoes1[] = "<button type='button' class='btn btn-xs btn-pa-purple parcelarSaida btnAcoes' data-original-title='Parcelar sa&iacute;da' data-key='{$id}' data-toggle='tooltip'><i class='fa fa-share-alt'></i></button>";
    //            if(!$row_saida['id_nfse']) {
                    if($permissao[120]) { $botoes1[] = "<a class='btn btn-xs btn-warning btnAcoes' href='form_saida.php?id_saida={$row_saida['id_saida']}' target='_blank' data-action='editar_saida' data-url='' data-key='{$row_saida['id_saida']}' data-toggle='tooltip' data-original-title='EDITAR SAIDA'><i class='fa fa-pencil'></i></a>"; }
    //            }
                if(!in_array($row_saida['tipo'], [413])){
                    $botoes4[] = "<button type='button' class='btn btn-xs btn-warning conciliar btnAcoes' data-toggle='tooltip' data-original-title='Conciliar Saida' data-key='{$id}'><i class='fa fa-handshake-o' border='0'></i></button>";
                }
            } else if ($tipo == 'entrada') {
                if ($row_saida['id_notas']) { 
                    $botoes1[] = "<button type='button' class='btn btn-xs btn-primary verNotas btnAcoes' data-key='{$row_saida['id_notas']}' data-original-title='Ver Notas' data-toggle='tooltip'><i class='fa fa-file'></i></button>";
                }
                $botoes1[] = "<button type='button' class='btn btn-xs btn-info anexarEntrada btnAcoes' data-original-title='Gerenciar Anexos' data-key='{$row_saida['id_entrada']}' data-toggle='tooltip'><span class='bg-warning testesapn text-primary ".(($row_saida['anexos'] + $row_saida['anexo_rescisao'] == 0) ? 'hide':'')."'>{$row_saida['anexos']}</span><i class='fa fa-paperclip'></i></button>";
                if($permissao[120]) { $botoes1[] = "<button type='button' class='btn btn-xs btn-warning editar_entrada btnAcoes' id='e{$row_saida['id_entrada']}' data-id='{$row_saida['id_entrada']}' data-tipo='entrada' data-toggle='tooltip' data-original-title='Editar Entrada'><i class='fa fa-pencil'></i></button>"; }
            }
            $botoes1[] = "<button type='button' class='btn btn-xs btn-primary detalheSaida btnAcoes'  data-id='{$id}' data-tipo='{$tipo}' data-original-title='Detalhes' data-toggle='tooltip'><i class='fa fa-search'></i></button>";
            $botoes1[] = "<a href='solicitacao_".(($tipo == 'saida') ? 'pagamento.php?saidas' : 'recebimento.php?entradas')."={$id}' target='_blank' class='btn btn-xs btn-default btnAcoes' data-original-title='Gerar Bordero' data-toggle='tooltip'><i class='fa fa-print'></i></a>";


            if($permissao[118]) { $botoes2[] = "<button type='button' class='btn btn-xs btn-success pagar".(($tipo == 'saida') ? 'Saida' : 'Entrada')." btnAcoes' data-toggle='tooltip' data-original-title='Pagar ".(($tipo == 'saida') ? 'Saida' : 'Entrada')."' data-key='{$id}' data-periodo='{$row_saida['data_vencimento']}'><i class='fa fa-plus' alt='Editar' border='0'></i></button>"; }
    //                if(!$row_saida['caixinha']) {
    //                    $botoes2[] = "<button type='button' class='btn btn-xs btn-info pagarPeloCaixinha' data-toggle='tooltip' data-original-title='Pagar ".(($tipo == 'saida') ? 'Saida' : 'Entrada')." Pelo Caixinha' data-key='{$id}' data-tipo='".(($tipo == 'saida') ? '1' : '2')."' data-periodo='{$row_saida['data_vencimento']}'><i class='fa fa-money' border='0'></i></button>";
    //                }
            if($permissao[119]) { $botoes3[] = "<button type='button' class='btn btn-xs btn-danger deletar".(($tipo == 'saida') ? 'Saida' : 'Entrada')." btnAcoes' data-toggle='tooltip' data-original-title='Deletar ".(($tipo == 'saida') ? 'Saida' : 'Entrada')."' data-key='{$id}'><i class='fa fa-trash-o' border='0'></i></button>"; }

            if($row_saida['flag_remessa']) {
                $type[] = 'R';
            }
            $type[] = ($tipo == 'saida') ? 'S' : 'E';

            $array['data'][] = [
                $checkbox,
                implode('&nbsp;', $type),
                implode('&nbsp;', $botoes1),
                $id.'<span class="hide type">'.(($tipo == 'saida') ? (($row_saida['flag_remessa']) ? 'remessa' : 'saida') : 'entrada'). '</span>',
                $row_saida['nome'],
                $row_saida['n_documento'],
                ConverteData($row_saida['data_vencimento'], 'd/m/Y'),
                number_format(str_replace(',','.',$row_saida['valor']), 2, ',', '.'),
                implode('', $botoes2),
                implode('', $botoes3),
                implode('', $botoes4)
            ];
    //        echo print_array($id . ' - ' . count($array['data']));
        }
    }
} else {
    $array['data'] = [];
}
//if($_COOKIE['debug']) {
//    print_array($array); exit;
//}
echo json_encode($array);