<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
}

include("../conn.php");
include("../wfunction.php");
include("../classes/BotoesClass.php");
include("../classes/EventoClass.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

//CARREGANDO MENU DE ACORDO COM AS PERMISSOES DA PESSOA
$botoes = new BotoesClass("../img_menu_principal/");
$botoesMenu = $botoes->getBotoesMenuModulo(4);
$icon = $botoes->iconsModulos;
$master = $usuario['id_master'];

function removeAdm($value){
    //preg_replace("/[aA]dministração de /", "", $input_lines);
    $pattern = "/[aA][dD]MINISTRAÇÃO DE /";
    $re = preg_replace($pattern, "", $value);
    return $re;
}
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Administrativo</title>

        <link rel="shortcut icon" href="favicon.ico" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>

        <div class="container">

            <div class="row">

                <div class="col-lg-12">
                    <div class="page-header box-admin-header"><h2><?php echo $icon[2] ?> - ADMINISTRATIVO</h2></div>
                    <div class="bs-component">
                        <ul class="nav nav-tabs" style="margin-bottom: 15px;">
                            <li class="active"><a href="#avisos" data-toggle="tab">Principal</a></li>
                            <?php foreach ($botoesMenu as $k => $btMenu) { ?>
                                <li><a href="#<?php echo $k ?>" data-toggle="tab"><?php echo removeAdm($btMenu) ?></a></li>
                            <?php } ?>
                        </ul>
                        <div id="myTabContent" class="tab-content">
                            <div class="tab-pane fade active in" id="avisos">
                                <!-- AVISO DOS PROEJTOS -->
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Projetos</h3>
                                    </div>
                                    <div class="panel-body overflow" style="max-height: 450px;">
                                        <table class="table table-hover table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Nome</th>
                                                    <th>Local</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_master='$master' ORDER BY regiao");
                                                while ($row_regiao = mysql_fetch_assoc($qr_regiao)):

                                                    if ($row_regiao['id_regiao'] == '15' or $row_regiao['id_regiao'] == '36' or $row_regiao['id_regiao'] == '37')
                                                        continue;

                                                    $qr_projeto = mysql_query("SELECT * FROM projeto WHERE status_reg = '1' AND id_regiao = '$row_regiao[id_regiao]';");
                                                    while ($row_projeto = mysql_fetch_assoc($qr_projeto)):


                                                        //OBTEM AS DATAS DE TÉRMINO DO PROJETO, DATA ATUAL E A DATA 45 DIAS ANTES DA DATA DE TÉRMINO
                                                        list($ano, $mes, $dia) = explode('-', $row_projeto['termino']);
                                                        $data_termino = mktime(0, 0, 0, $mes, $dia, $ano);
                                                        $data_hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                                                        $prazo_renovacao = mktime(0, 0, 0, $mes, $dia - 45, $ano); //45 dias
                                                        /////////////////////
                                                        ////VERIFICA SE A VERBA DO PROJETO ULTRAPASSOU O VALOR ESTIMADO									
                                                        $qr_valor = mysql_query("SELECT SUM(REPLACE(entrada.valor,',','.')) FROM 
                                                            (notas INNER JOIN notas_assoc ON notas.id_notas = notas_assoc.id_notas) 
                                                            INNER JOIN entrada 
                                                            ON notas_assoc.id_entrada = entrada.id_entrada
                                                            WHERE notas.id_projeto = '$row_projeto[id_projeto]' AND notas.status = 1 AND notas.tipo_contrato = $row_projeto[id_projeto] AND entrada.status IN(1,2) AND notas.tipo_contrato2 = 'projeto'");

                                                        $total_entrada = (float) @mysql_result($qr_valor, 0);
                                                        $valor_alcancado = $total_entrada;
                                                        $totalizador += $valor_alcancado;
                                                        $totalizador_verbadestinada += $row_projeto['verba_destinada'];



                                                        $qr_subprojeto = mysql_query("SELECT * FROM subprojeto WHERE id_projeto='$row_projeto[id_projeto]' AND status_reg='1' ORDER BY termino DESC");
                                                        $verifica = mysql_num_rows($qr_subprojeto);

                                                        if (!empty($verifica)) {

                                                            while ($row_subprojeto = mysql_fetch_assoc($qr_subprojeto)):

                                                                $qr_valor = mysql_query("SELECT SUM(REPLACE(entrada.valor,',','.')) FROM 
													   (notas INNER JOIN notas_assoc ON notas.id_notas = notas_assoc.id_notas) 
													   INNER JOIN entrada 
													   ON notas_assoc.id_entrada = entrada.id_entrada
													   WHERE notas.id_projeto = '$row_subprojeto[id_projeto]' AND notas.status = 1 AND notas.tipo_contrato = $row_subprojeto[id_subprojeto]  AND entrada.status IN(1,2) AND notas.tipo_contrato2 = 'subprojeto'") or die(mysql_error());

                                                                $total_entrada_sub = (float) @mysql_result($qr_valor, 0);
                                                                $valor_alcancado_sub = $total_entrada_sub;
                                                                $totalizador += $valor_alcancado_sub;

                                                                //totalizador	
                                                                $verba_destinada = str_replace(',', '.', (str_replace('.', '', $row_subprojeto['verba_destinada'])));
                                                                $totalizador_verbadestinada += $verba_destinada;

                                                            endwhile;
                                                        }

                                                        if ($totalizador > $totalizador_verbadestinada) {
                                                            ?>      
                                                            <tr>
                                                                <td><?php echo $row_projeto['id_projeto'] . ' - ' . $row_projeto['nome']; ?> </td>
                                                                <td><?php echo $row_regiao['regiao']; ?> </td>
                                                                <td><?php echo '<span  style="color:#F00;"> Verba do projeto ultrapassou o valor estimado!</span>'; ?></td>
                                                            </tr>
                                                            <?php
                                                        }


                                                        unset($totalizador_verbadestinada);
                                                        unset($totalizador);

                                                        /////////////FIM VERIFICAÇÃO DE VERBAS DO PROJETO ///////////////////////////    
                                                        //// PROJETOS EXPIRANDO ///////                                                     
                                                        if (($prazo_renovacao <= $data_hoje) and ($data_termino > $data_hoje)) {
                                                            $dif = ($data_termino - $data_hoje) / 86400;
                                                            ?>
                                                            <tr>
                                                                <td><?php echo $row_projeto['id_projeto'] . ' - ' . $row_projeto['nome']; ?> </td>
                                                                <td><?php echo $row_regiao['regiao']; ?> </td>
                                                                <td><?php echo '<span style="color:#09C;">Expira em ' . $dif . ' dias!</span>'; ?> </td>
                                                            </tr>

                                                            <?php
                                                        }

                                                        ////PROJETOS EXPIRADOS
                                                        elseif ($data_hoje > $data_termino) {
                                                            ///// VERIFICA SE EXITE SUBPROJETOS   E EXBIBE CASO EXISTA E ESTEJA EXPIRANDO OU EXPIRADO ////
                                                            $verifica_ok = 0;
                                                            $qr_subprojeto = mysql_query("SELECT * FROM subprojeto WHERE id_projeto='$row_projeto[id_projeto]' AND status_reg='1' ORDER BY termino DESC");
                                                            $verifica = mysql_num_rows($qr_subprojeto);

                                                            if (!empty($verifica)) {

                                                                while ($row_subprojeto = mysql_fetch_assoc($qr_subprojeto)):



                                                                    list($ano, $mes, $dia) = explode('-', $row_subprojeto['termino']);
                                                                    $data_termino = mktime(0, 0, 0, $mes, $dia, $ano);
                                                                    $data_hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                                                                    $prazo_renovacao = mktime(0, 0, 0, $mes, $dia - 45, $ano); //45 dias

                                                                    if ($data_hoje < $data_termino) {
                                                                        $verifica_ok++;
                                                                    }

                                                                    ////SUBPROJETOS EXPIRANDO //////
                                                                    if (($prazo_renovacao <= $data_hoje) and ($data_termino >= $data_hoje)) {
                                                                        $dif = ($data_termino - $data_hoje) / 86400;
                                                                        ?>

                                                                        <tr class="linha_um">
                                                                            <td><?php echo $row_projeto['id_projeto'] . ' - ' . $row_projeto['nome']; ?> </td>
                                                                            <td><?php echo $row_regiao['regiao']; ?> </td>
                                                                            <?php
                                                                            if ($dif == 0) {

                                                                                echo '<td><span style="color:#F60;">Expira hoje!</span></td></tr>';
                                                                            } else {
                                                                                ?>


                                                                                <td><?php echo '<span style="color:#09C;">Renovação expira em ' . round($dif) . ' dias!</span>'; ?> </td>
                                                                            </tr>

                                                                            <?php
                                                                        }
                                                                    }

                                                                    ////SUBPROJETOS EXPIRADO //////
                                                                    elseif ($data_hoje > $data_termino and $verifica_ok == 0) {
                                                                        ?>
                                                                        <tr>
                                                                            <td><?php echo $row_projeto['id_projeto'] . ' - ' . $row_projeto['nome']; ?> </td>
                                                                            <td><?php echo $row_regiao['regiao']; ?> </td>
                                                                            <td>

                                                                                <?php
                                                                                if ($row_subprojeto['termino'] == '0000-00-00' or $row_subprojeto['inicio'] == '0000-00-00') {
                                                                                    echo '<span  style="color:#F90;">Faltam informações sobre as datas</span>';
                                                                                } else {
                                                                                    echo '<span  style="color:#F00;"> Renovação expirada</span>';
                                                                                }
                                                                                ?> 

                                                                            </td>
                                                                        </tr> 
                                                                        <?php
                                                                        $verifica_ok = 1;
                                                                    }

                                                                endwhile; //FIM SUBPROJETO	
                                                            } else {
                                                                ?>


                                                                <tr>
                                                                    <td><?php echo $row_projeto['id_projeto'] . ' - ' . $row_projeto['nome']; ?> </td>
                                                                    <td><?php echo $row_regiao['regiao']; ?> </td>
                                                                    <td>
                                                                        <?php
                                                                        if ($row_projeto['termino'] == '0000-00-00' or $row_projeto['inicio'] == '0000-00-00') {
                                                                            echo '<span  style="color:#F90;">Faltam informações sobre as datas</span>';
                                                                        } else {
                                                                            echo '<span  style="color:#F00;"> Expirado</span>';
                                                                        }
                                                                        ?> 
                                                                    </td>
                                                                </tr>

                                                                <?php
                                                            }
                                                        }

                                                    endwhile; //projeto


                                                endwhile; //regiao
                                                ?>
                                            </tbody>
                                        </table>
                                    </div></div>
                                <!-- AVISO DOS PROEJTOS -->
                                
                                <!-- AVISO DE NOTAS FISCAIS -->
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Notas Fiscais</h3>
                                    </div>
                                    <div class="panel-body overflow" style="max-height: 450px;">
                                        <table class="table table-hover table-striped">
                                            <thead>
                                                <tr>			
                                                    <th>Região</th>
                                                    <th>Projeto</th>
                                                    <th>Valor NF/ Carta Medição</th>
                                                    <th>Repasse</th>
                                                    <th>Diferença</th>
                                                    <th>Ano</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_master='$master' ORDER BY regiao");
                                                while ($row_regiao = mysql_fetch_assoc($qr_regiao)):

                                                    if ($row_regiao['id_regiao'] == '15' or $row_regiao['id_regiao'] == '36' or $row_regiao['id_regiao'] == '37')
                                                        continue;

                                                    $qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$row_regiao[id_regiao]' AND status_reg = '1'");
                                                    while ($row_projeto = mysql_fetch_assoc($qr_projeto)) :

                                                        for ($ano = 2010; $ano <= date('Y'); $ano++) :

                                                            $qr_notas = mysql_query("SELECT * FROM notas WHERE id_projeto = '$row_projeto[id_projeto]' AND status = '1'   AND YEAR(data_emissao) = '$ano' ORDER BY data_emissao DESC");
                                                            $num_notas = mysql_num_rows($qr_notas);

                                                            if (empty($num_notas))
                                                                continue;

                                                            while ($row_notas = mysql_fetch_assoc($qr_notas)):

                                                                // totalizadores por ano						
                                                                $total_ano += $row_notas['valor'];

                                                                $qr_total_anos = mysql_query("SELECT SUM(REPLACE(entrada.valor,',','.')) FROM 
								(notas INNER JOIN notas_assoc ON notas.id_notas = notas_assoc.id_notas) 
								INNER JOIN entrada 
								ON notas_assoc.id_entrada = entrada.id_entrada
								WHERE notas.id_notas = '$row_notas[id_notas]' AND YEAR(data_emissao) = '$ano' AND entrada.status = 2 ;
								");

                                                                $totalizador_repasse_anos += @str_replace(',', '.', mysql_result($qr_total_anos, 0));
                                                                $totalizador_valor += $row_notas['valor'];

                                                            endwhile;

                                                            $totalizador_diferenca_anos = ($totalizador_repasse_anos - $totalizador_valor);


                                                            if (!empty($totalizador_diferenca_anos)) {

                                                                if ($totalizador_diferenca_anos < 0) {
                                                                    echo '<tr class="danger">';
                                                                } else {
                                                                    echo '<tr>';
                                                                }

                                                                echo '<td>' . $row_regiao['id_regiao'] . ' - ' . $row_regiao['regiao'] . '</td>
                                                                    <td>(' . $row_projeto['id_projeto'] . ') <br>' . $row_projeto['nome'] . '</td>
                                                                    <td>' . 'R$ ' . number_format($totalizador_valor, 2, ',', '.') . '</td>
                                                                    <td>' . 'R$ ' . number_format($totalizador_repasse_anos, 2, ',', '.') . '</td>
                                                                    <td>' . 'R$ ' . number_format($totalizador_diferenca_anos, 2, ',', '.') . '</td>
                                                                    <td>' . $ano . '</td>
                                                                  </tr> ';
                                                            }

                                                            unset($totalizador_repasse_anos, $totalizador_diferenca_anos, $totalizador_valor);

                                                        endfor; //ANOS
                                                    endwhile; //projeto
                                                endwhile; //regiao
                                                ?>
                                            </tbody>
                                        </table>
                                    </div></div>
                                <!-- AVISO DE NOTAS FISCAIS -->
                                
                                <!-- AVISO DE OBRIGAÇÕES DA INSTITUIÇÃO -->
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Obrigações da Instituíção</h3>
                                    </div>
                                    <div class="panel-body overflow" style="max-height: 450px;">
                                        <table class="table table-hover table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Documento</th>
                                                    <th>Data de Publicação</th>
                                                    <th>Validade</th>
                                                    <th>Descrição</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            $qr_tipo_oscip = mysql_query("SELECT * FROM tipo_doc_oscip WHERE tipo_status = 1 ORDER BY tipo_nome") or die(mysql_error());
                                            while ($row_tipo_oscip = mysql_fetch_assoc($qr_tipo_oscip)):

                                                ////PEgando o registro mais recente
                                                $qr_oscip = mysql_query("SELECT * FROM obrigacoes_oscip WHERE id_master = '$master' AND status = 1 AND tipo_oscip = '$row_tipo_oscip[tipo_nome]' AND periodo != 'indeterminado' ORDER BY data_publicacao DESC LIMIT 0,1") or die(mysql_error());
                                                $row_oscip = mysql_fetch_assoc($qr_oscip);

                                                if (mysql_num_rows($qr_oscip) == 0)
                                                    continue;

                                                $class = ($cont++ % 2 == 0) ? 'class="linha_um"' : 'class="linha_dois"';

                                                $periodo = $row_oscip['periodo'];
                                                $data = $row_oscip['data_publicacao'];
                                                $n_periodo = $row_oscip['numero_periodo'];
                                                $data_termino_oscip = $row_oscip['oscip_data_termino'];
                                                $tipo_oscip = $row_oscip['tipo_oscip'];

                                                $prazo_dias = 60;

                                                list($ano, $mes, $dia) = explode('-', $data);
                                                //descobre a data de vencimento	
                                                switch ($periodo) {

                                                    case 'Dias':
                                                        $data_vencimento = mktime(0, 0, 0, $mes, $dia + $n_periodo, $ano);
                                                        @$prazo_renovacao = $data_vencimento - 5184000;
                                                        $data_atual = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                                                        break;

                                                    case 'Meses':
                                                        $data_vencimento = mktime(0, 0, 0, $mes + $n_periodo, $dia, $ano);
                                                        @$prazo_renovacao = $data_vencimento - 5184000;
                                                        $data_atual = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

                                                        break;

                                                    case 'Anos':
                                                        $data_vencimento = mktime(0, 0, 0, $mes, $dia, $ano + $n_periodo);
                                                        @$prazo_renovacao = $data_vencimento - 5184000;
                                                        $data_atual = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

                                                        break;

                                                    case 'Período':
                                                        list($ano_2, $mes_2, $dia_2) = explode('-', $data_termino_oscip);
                                                        $data_vencimento = mktime(0, 0, 0, $mes_2, $dia_2, $ano_2);
                                                        @$prazo_renovacao = $data_vencimento - 5184000;
                                                        $data_atual = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

                                                        break;
                                                }//fim switch
                                                ///VERIFICANDO SE O DOCUMENTO ESTÁ  DENTRO DO PERÍODO DE 45 DIAS ANTES DA DATA DE VENCIMENTO								
                                                $data_venc = date('Y-m-d', $data_vencimento);
                                                $qnt_dias_vencimento = @mysql_result(mysql_query("SELECT  DATEDIFF('$data_venc',NOW()) as dias  FROM `obrigacoes_oscip` WHERE tipo_oscip = '$row_tipo_oscip[tipo_nome]' AND id_master = '$Master'   AND status = 1 ORDER BY data_publicacao DESC "), 0);


                                                if ($qnt_dias_vencimento == 0) {
                                                    $mesg = '<span style="color:#F60;">Expira hoje!</span>';
                                                } else if ($qnt_dias_vencimento < 0) {
                                                    $mesg = '<span  style="color:#F00;">Expirado</span>';
                                                } else if ($qnt_dias_vencimento > 0 and $qnt_dias_vencimento < $prazo_dias) {
                                                    $mesg = '<span style="color:#09C;">Expira em ' . $qnt_dias_vencimento . ' dias!</span>';
                                                }

                                                if (!empty($mesg)) {

                                                    echo '<tr ' . $class . '>
                                                        <td width="20%">' . $row_oscip['tipo_oscip'] . '</td>
                                                        <td width="20%">' . implode('/', array_reverse(explode('-', $row_oscip['data_publicacao']))) . '						
                                                        <td>' . $row_oscip['numero_periodo'] . ' ' . $row_oscip['periodo'] . '</td>
                                                        <td width="50%">';

                                                    if (empty($row_oscip['descricao'])) {
                                                        echo "<span style='font-style: italic; color: red;'>Descrição não informado.<span>"; //$qnt_dias_vencimento . '---------';
                                                    } else {
                                                        echo $row_oscip['descricao'];
                                                    }

                                                    echo '</td>
                                                            <td>' . $mesg . '</td>
                                                            </tr>';
                                                }
                                                unset($mesg);


                                            endwhile; // fim tipo doc oscip
                                            ///publicações do Anexo 1

                                            $hoje = date('Y-m-d');


                                            $qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_master = '$Master' AND  status_reg =1 ORDER BY regiao");
                                            while ($row_projeto = mysql_fetch_assoc($qr_projeto)):




                                                $qr_oscip = mysql_query("SELECT * FROM obrigacoes_oscip WHERE id_projeto = '$row_projeto[id_projeto]' AND status = '1' AND id_master = '$Master' ORDER BY data_publicacao ASC") or die(mysql_error());
                                                while ($row_oscip = mysql_fetch_assoc($qr_oscip)):
                                                    $class = ($cont % 2 == 0) ? 'class="linha_um"' : 'class="linha_dois"';

                                                    $periodo = $row_oscip['periodo'];
                                                    $data = $row_oscip['data_publicacao'];
                                                    $n_periodo = $row_oscip['numero_periodo'];
                                                    $data_termino_oscip = $row_oscip['oscip_data_termino'];

                                                    list($ano, $mes, $dia) = explode('-', $data);


                                                    switch ($periodo) {

                                                        case 'Dias':
                                                            //descobre a data de vencimento	
                                                            $data_vencimento = mktime(0, 0, 0, $mes, $dia + $n_periodo, $ano);
                                                            @$prazo_renovacao = $data_vencimento - 5184000;
                                                            $data_atual = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                                                            break;

                                                        case 'Meses':
                                                            $data_vencimento = mktime(0, 0, 0, $mes + $n_periodo, $dia, $ano);
                                                            @$prazo_renovacao = $data_vencimento - 5184000;
                                                            $data_atual = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                                                            break;



                                                        case 'Anos':
                                                            $data_vencimento = mktime(0, 0, 0, $mes, $dia, $ano + $n_periodo);
                                                            @$prazo_renovacao = $data_vencimento - 5184000;
                                                            $data_atual = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                                                            break;

                                                        case 'Período':
                                                            list($ano_2, $mes_2, $dia_2) = explode('-', $data_termino_oscip);
                                                            $data_vencimento = mktime(0, 0, 0, $mes_2, $dia_2, $ano_2);
                                                            @$prazo_renovacao = $data_vencimento - 5184000;
                                                            $data_atual = mktime(0, 0, 0, date('m'), date('d'), date('Y'));


                                                            break;
                                                    }//fim switch


                                                    if ($prazo_renovacao < $data_atual and $data_vencimento > $data_atual) {

                                                        //quantidade de dias para a expiração
                                                        $dif = ($data_vencimento - $data_atual) / 86400;

                                                        echo '<tr class="novo">
							<td width="20%">' . $resultado['tipo_oscip'] . ' - <br>' . $row_projeto['nome'] . '<br>(' . $row_projeto['regiao'] . ')' . '</td>
							
							<td width="30%">' . $row_oscip['descricao'] . '</td>';
                                                        echo '<td width="20%">' . implode('/', array_reverse(explode('-', $row_oscip['data_publicacao'])));
                                                        echo ' <td>' . $row_oscip2['numero_periodo'] . ' ' . $row_oscip2['periodo'] . '</td>';

                                                        if ($dif == 0) {

                                                            echo '<td width="100%"><span style="color:#F60;">Expira hoje!</span></td></tr>';
                                                        } else {

                                                            echo '<td width="200%"><span style="color:#09C;">Expira em ' . (int) $dif . ' dias!</span></td></tr>';
                                                        }
                                                    } elseif ($data_atual > $data_vencimento) {


                                                        $ok = 0;
                                                        $id_expirado = $row_oscip['id_oscip'];
                                                        $status_reg = $row_projeto['status_reg'];
                                                    } else {

                                                        $ok = 1;
                                                    }

                                                endwhile;


                                                if ($tipo_anterior != $row_projeto['id_projeto'] and $ok == 0 and $indeterminado != 1) {


                                                    if ($status_reg != 0) {

                                                        if ($id_expirado) {

                                                            $qr_expirado = mysql_query("SELECT * FROM obrigacoes_oscip WHERE id_master = '$Master' AND status = 1 AND id_oscip = '$id_expirado'") or die(mysql_error());
                                                            $resultado = mysql_fetch_assoc($qr_expirado);
                                                            echo '<tr ' . $class . '>
													<td width="30%">' . $resultado['tipo_oscip'] . ' - <br>' . $row_projeto['nome'] . '<br>(' . $row_projeto['regiao'] . ')' . '</td>
													<td width="20%">' . implode('/', array_reverse(explode('-', $resultado['data_publicacao']))) . '
													<td>' . $row_oscip2['numero_periodo'] . ' ' . $row_oscip2['periodo'] . '</td>
													<td width="50%">' . $resultado['descricao'] . '</td>';
                                                            echo '<td><span  style="color:#F00;">Expirado</span></td></tr>';

                                                            unset($data_expiracao_recente, $id_expirado, $ok, $indeterminado);
                                                        }
                                                    }
                                                }

                                                $tipo_anterior = $row_projeto['id_projeto'];


                                            endwhile;
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- AVISO DE OBRIGAÇÕES DA INSTITUIÇÃO -->
                                
                            </div>

                            <?php foreach ($botoesMenu as $k => $btMenu) { ?>
                                <div class="tab-pane fade" id="<?php echo $k ?>">
                                    <div class="detalhes-modulo">
                                        <?php echo $botoes->getHtmlBotoesModulo($k, 2) ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

            </div>

            <footer>
                <div class="row">
                    <div class="page-header"></div>
                    <div class="pull-right"><a href="#top">Voltar ao topo</a></div>
                    <div class="col-lg-12">
                        <p>Pay All Fast 3.0</p>
                        <p>Todos os direitos reservados <a href="http://f71.com.br" rel="nofollow" target="_blank">F71 Sistemas</a>.</p>
                    </div>
                </div>
            </footer>
        </div>

        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script>
            $(function() {

            });
        </script>
    </body>
</html>