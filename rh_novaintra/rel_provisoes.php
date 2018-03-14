<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='login.php'>Logar</a> ";
    exit;
}

require("../conn.php");
require("../wfunction.php");

error_reporting(0);
$usuario = carregaUsuario();

//ANO
$optAnos = array();
for ($i = 2009; $i <= date('Y')+1; $i++) {
    $optAnos[$i] = $i;
}
$anoSel_de = (isset($_REQUEST['ano_de'])) ? $_REQUEST['ano_de'] : date('Y');
$anoSel_ate = (isset($_REQUEST['ano_ate'])) ? $_REQUEST['ano_ate'] : date('Y');


///meses
$qr_mes = mysql_query("SELECT * FROM ano_meses");
while($row_mes = mysql_fetch_assoc($qr_mes)){    
    
      $opt_mov_cred[$row_mes['num_mes']] = $row_mes['nome_mes'];  
}
$mesSel_de = (isset($_REQUEST['mes_de']))? $_REQUEST['mes_de']: '';
$mesSel_ate = (isset($_REQUEST['mes_ate']))? $_REQUEST['mes_ate']: '';

///REGIÕES
$regioes = montaQuery('regioes', "id_regiao,regiao", "id_master = '$usuario[id_master]'");
$optRegiao = array();
$optRegiao[0] = 'Selecione';
foreach ($regioes as $valor) {
    $optRegiao[$valor['id_regiao']] = $valor['id_regiao'] . ' - ' . $valor['regiao'];
}
$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : '';

if(isset($_POST['gerar'])){

    $id_regiao  = $_POST['regiao'];
    $id_projeto = $_POST['projeto'];
    $mes_de     = $_POST['mes_de'];
    $ano_de     = $_POST['ano_de'];
    $mes_ate     = $_POST['mes_ate'];
    $ano_ate     = $_POST['ano_ate'];
    $porcentagem = $_POST['porcentagem'];
   $formato_porcentagem = ($porcentagem *100).'%';
   $nome_porcentagem    = 'PORCENTAGEM: '.$formato_porcentagem;

   $qr_nomes = mysql_query("SELECT A.regiao as nome_regiao, B.nome as nome_projeto FROM regioes as A INNER JOIN projeto as B
                             ON A.id_regiao = B.id_regiao 
                             WHERE A.id_regiao = $id_regiao AND B.id_projeto = $id_projeto") ;
   $row_nomes = mysql_fetch_assoc($qr_nomes);



   $qr_folha = mysql_query("SELECT  *,(total_inss+total_fgts+total_pis) as total_guias

                           FROM 
                                   (SELECT *, 
                                                           (SELECT SUM(CAST( REPLACE(B.valor,',','.') as DECIMAL(13,2))) FROM pagamentos as A
                                                           INNER JOIN saida as B
                                                           ON A.id_saida = B.id_saida
                                                           WHERE id_folha = folha.id_folha AND tipo_pg = 1 AND B.status =2 AND A.tipo_contrato_pg = 1) as total_inss,

                                                           (SELECT SUM(CAST( REPLACE(B.valor,',','.') as DECIMAL(13,2))) FROM pagamentos as A
                                                           INNER JOIN saida as B
                                                           ON A.id_saida = B.id_saida
                                                           WHERE id_folha = folha.id_folha AND tipo_pg = 2 AND B.status =2 AND A.tipo_contrato_pg = 1) as total_fgts,

                                                           (SELECT SUM(CAST( REPLACE(B.valor,',','.') as DECIMAL(13,2))) FROM pagamentos as A
                                                           INNER JOIN saida as B
                                                           ON A.id_saida = B.id_saida
                                                           WHERE id_folha = folha.id_folha AND tipo_pg = 3 AND B.status = 2 AND A.tipo_contrato_pg = 1) as total_pis
                                                           /*
                                                           (SELECT SUM(CAST( REPLACE(B.valor,',','.') as DECIMAL(13,2))) FROM pagamentos as A
                                                           INNER JOIN saida as B
                                                           ON A.id_saida = B.id_saida
                                                           WHERE id_folha = folha.id_folha AND tipo_pg = 4 AND B.status = 2 AND A.tipo_contrato_pg = 1) as total_ir,
                                                           */

                                                             FROM 


                                                           (SELECT A.id_folha, A.regiao, A.projeto, A.data_inicio, A.data_fim,A.terceiro, A.tipo_terceiro, 
                                                           SUM(B.sallimpo_real) as total_base,
                                                           SUM(B.rend) as total_rend,
                                                           SUM(B.valor_pago_rescisao) as valor_pago_rescisao,
                                                           SUM(B.valor_pago_ferias) as valor_pago_ferias, 
                                                           SUM(B.a5020 + B.inss_rescisao + B.inss_ferias) as total_inss_salario,
                                                           SUM(B.inss_dt) as total_inss_dt,
                                                           SUM(B.ir_dt) as total_ir_dt,
                                                           SUM(B.a5021+B.ir_rescisao+B.ir_ferias) as total_irrf, 
                                                           SUM(salliquido) as total_liquido_folha,
                                                           A.mes, A.ano,
                                                           A.ids_movimentos_estatisticas,
                                                           (SELECT GROUP_CONCAT(id_clt) FROM rh_folha_proc WHERE id_folha = A.id_folha AND status_clt  IN(60,61,62,81,63,101,64,65,66)) as ids_rescindidos
                                                           FROM rh_folha as A 
                                                           INNER JOIN rh_folha_proc as B
                                                           ON A.id_folha = B.id_folha
                                                           WHERE A.regiao = '$id_regiao' AND A.projeto = '$id_projeto'  AND A.data_inicio BETWEEN '$ano_de-$mes_de-01' AND '$ano_ate-$mes_ate-01' AND A.status = 3 
                                                           AND A.id_folha NOT IN(1195)
                                                           GROUP BY  A.mes, A.ano,A.terceiro) as folha

                   ) as relatorio    ORDER BY  ano,mes
                 ") or die(mysql_error()) ;   

    while ($row = mysql_fetch_assoc($qr_folha)) {


           $mes = (int)$row['mes'];
           $ano = (int)$row['ano'];

           ////PEGANDO OS MOVIMENTOS DE CRÉDITOS
           //$rendimentos = mysql_result(mysql_query("SELECT SUM(valor_movimento) FROM rh_movimentos_clt WHERE id_movimento IN($row[ids_movimentos_estatisticas]) AND tipo_movimento = 'CREDITO';"),0);

           ///RESCISÕES
           $qr_rescisao = mysql_query("SELECT SUM(CAST(REPLACE(valor,',','.') as DECIMAL(13,2))) as total_rescisao,  GROUP_CONCAT(id_saida) as ids_saidas
                                   FROM saida as A
                                   WHERE A.id_regiao = '$id_regiao' AND A.id_projeto = '$id_projeto' AND MONTH(A.data_vencimento) = '$mes' AND YEAR(A.data_vencimento) = '$ano'  AND A.tipo IN(170,51) AND status = 2;") or die(mysql_error());
           $row_rescisao = mysql_fetch_assoc($qr_rescisao);


           $qr_saldo_salario = mysql_query("SELECT SUM(CAST(REPLACE(B.saldo_salario,',','.') as DECIMAL(13,2))) as total_saldo_salario
                                           FROM pagamentos_especifico as A 
                                          INNER JOIN rh_recisao as B
                                          ON A.id_clt = B.id_clt
                                          INNER JOIN saida as C
                                          ON C.id_saida = A.id_saida
                                          WHERE  A.id_saida IN($row_rescisao[ids_saidas]) AND B.status = 1;");
           $row_ss = mysql_fetch_assoc($qr_saldo_salario);




           ///teste
           if(!empty($row['ids_rescindidos'])){

           $qr_saldo_salario2 = mysql_query("SELECT SUM(saldo_salario) as total_saldo_salario
                                              FROM rh_recisao WHERE id_clt IN($row[ids_rescindidos]) AND YEAR(data_demi) = '$ano' AND status = 1");
            $row_ss2 = mysql_fetch_assoc($qr_saldo_salario2);
            $total_saldoSalario = $row_ss2['total_saldo_salario'] ;
           } else {
               $total_saldoSalario = 0;
           }





           ///FÉRIAS
           $qr_ferias = mysql_query("SELECT SUM(CAST(REPLACE(valor,',','.') as DECIMAL(13,2))) as total_ferias 
                                   FROM pagamentos_especifico as A
                                   INNER JOIN saida as B
                                   ON A.id_saida = B.id_saida
                                   WHERE  B.tipo IN(76,156) AND MONTH(B.data_vencimento) = $mes 
                                   AND YEAR(B.data_vencimento) = $ano AND B.status = 2 AND B.id_regiao = '$id_regiao' AND B.id_projeto = '$id_projeto'");

           $row_ferias = mysql_fetch_assoc($qr_ferias);


           ///RPA
           $qr_rpa=  mysql_query("SELECT A.nome, B.*,D.id_saida,D.status,D.estorno, SUM(B.valor) as total_bruto
                                FROM autonomo as A
                                INNER JOIN rpa_autonomo as B
                                ON A.id_autonomo = B.id_autonomo
                                INNER JOIN rpa_saida_assoc  as C
                                ON B.id_rpa = C.id_rpa
                                INNER JOIN saida as D
                                ON D.id_saida = C.id_saida
                                WHERE B.id_regiao_pag = {$id_regiao} AND B.id_projeto_pag = {$id_projeto} 
                                AND MONTH(B.data_geracao) = {$mes} AND YEAR(B.data_geracao)  = {$ano} AND D.`status` IN(1,2) 
                                AND D.estorno =0 and  C.tipo_vinculo = '1'");
           $row_rpa = mysql_fetch_assoc($qr_rpa);


           //CRIANDO OS  ARRAYS DE VALORES MENSAIS
            if($row['ano'] != $ano_anterior){ 

               $valores['FOLHA BASE']                   = array_fill(1, 13, null);
               $valores['RENDIMENTOS']                  = array_fill(1, 13, null);
               $valores[$nome_porcentagem]              = array_fill(1, 13, null);
               $valores['INSS']                         = array_fill(1, 13, null);
               $valores['FGTS']                         = array_fill(1, 13, null);
               $valores['PIS']                          = array_fill(1, 13, null);
               $valores['TOTAL ENCARGOS TRABALHISTAS']  = array_fill(1, 13, null);
               $valores['RESCISÕES']                    = array_fill(1, 13, null);
               $valores['SALDO SALÁRIO RESCISÃO']       = array_fill(1, 13, null);
               //$valores['GRRF']                       = array_fill(1, 13, null);
               $valores['FÉRIAS']                       = array_fill(1, 13, null);
               $valores['INSS EMPREGADO']               = array_fill(1, 13, null);
               $valores['TOTAL PROVISÃO']               = array_fill(1, 13, null);
               $valores['FOLHA LÍQUIDA']                = array_fill(1, 13, null);
               $valores['ENCARGOS']                     = array_fill(1, 13, null);
               $valores['IRRF']                         = array_fill(1, 13, null);  
               $valores['PROVISÕES TRABALHISTAS']       = array_fill(1, 13, null);
               $valores['VALOR BRUTO RPA']              = array_fill(1, 13, null);  
               $valores['TOTAL']                        = array_fill(1, 13, null);   
             
            }





            if($row['terceiro'] == 1){

            $valores['INSS'][13]                        += $row['total_inss']; 
            $valores['PIS'][13]                         += $row['total_pis'];
            $valores['TOTAL ENCARGOS TRABALHISTAS'][13] += $valores['INSS'][13] + $valores['PIS'][13]; 
            $valores['TOTAL PROVISÃO'][13]              += '0.00';//$valores[$nome_porcentagem][13] -  $valores['TOTAL GUIAS'][13] - $valores['RESCISÕES'][13] +$valores['SALDO SALÁRIO RESCISÃO'][13] + $row['total_inss_salario'] - $valores['FÉRIAS'][13];
            $valores['FOLHA LÍQUIDA'][13]               += $row['total_liquido_folha'];
            $valores['IRRF'][13]                        += $row['total_ir_dt'];
            $valores['PROVISÕES TRABALHISTAS'][13]      += ( $row['total_liquido_folha'] + $row['total_ir_dt'] + $row['total_inss'] + $row['total_pis']  )* -1 ;
            $valores['TOTAL'][13]                       += '0.00';//$row['total_liquido_folha'] + $valores['ENCARGOS'][13] + $valores['IRRF'][13] + $valores['APLICAÇÃO'][13];// - $valores['INSS EMPREGADO'][$mes];


             //TOTALIZADORES COM 13º 
            $valores['INSS'][14]                            += $valores['INSS'][13]; 
            $valores['PIS'][14]                             += $valores['PIS'][13]; 
            $valores['TOTAL ENCARGOS TRABALHISTAS'][14]     += $valores['TOTAL ENCARGOS TRABALHISTAS'][13]; 
            $valores['TOTAL PROVISÃO'][14]                  += $valores['TOTAL PROVISÃO'][13];
            $valores['FOLHA LÍQUIDA'][14]                   += $row['total_liquido_folha'];
            $valores['IRRF'][14]                            += $row['total_ir_dt'];
            $valores['PROVISÕES TRABALHISTAS'][14]          += ( $row['total_liquido_folha'] + $row['total_ir_dt'] + $row['total_inss'] + $row['total_pis']  )* -1; 

            } else {


            $valores['FOLHA BASE'][$mes]                   = $row['total_base']; 
            $valores['RENDIMENTOS'][$mes]                  =   $row['total_rend'] - $row['valor_pago_ferias'] - $row['valor_pago_rescisao'] + $total_saldoSalario;
            $valores[$nome_porcentagem][$mes]              = ($row['total_base'] +  $valores['RENDIMENTOS'][$mes] )* $porcentagem;   
            $valores['INSS'][$mes]                         = $row['total_inss']; 
            $valores['FGTS'][$mes]                         = $row['total_fgts']; 
            $valores['PIS'][$mes]                          = $row['total_pis']; 
            $valores['TOTAL ENCARGOS TRABALHISTAS'][$mes]  = $row['total_inss']+ $row['total_fgts'] + $row['total_pis']; 
            $valores['RESCISÕES'][$mes]                    = $row_rescisao['total_rescisao'];
            $valores['SALDO SALÁRIO RESCISÃO'][$mes]       = $total_saldoSalario;
            $valores['FÉRIAS'][$mes]                       = $row_ferias['total_ferias'];
            $valores['TOTAL PROVISÃO'][$mes]               = $valores[$nome_porcentagem][$mes] -  $valores['TOTAL ENCARGOS TRABALHISTAS'][$mes] - $valores['RESCISÕES'][$mes] +$valores['SALDO SALÁRIO RESCISÃO'][$mes] + $row['total_inss_salario'] - $valores['FÉRIAS'][$mes];
            $valores['FOLHA LÍQUIDA'][$mes]                = $row['total_liquido_folha'];
            $valores['ENCARGOS'][$mes]                     = $row['total_inss'] + $row['total_fgts'] + $row['total_pis'];
            $valores['IRRF'][$mes]                         = $row['total_irrf'];
            $valores['INSS EMPREGADO'][$mes]               = $row['total_inss_salario'];
            $valores['PROVISÕES TRABALHISTAS'][$mes]       = $valores['TOTAL PROVISÃO'][$mes] ;
            $valores['VALOR BRUTO RPA'][$mes]              = $row_rpa['total_bruto'];
            $valores['TOTAL'][$mes]                        = $row['total_liquido_folha'] + $valores['ENCARGOS'][$mes] + $valores['IRRF'][$mes] + $valores['PROVISÕES TRABALHISTAS'][$mes] + $row_rpa['total_bruto'];// - $valores['INSS EMPREGADO'][$mes];

         

             //TOTALIZADORES SEM 13º 
            $valores['FOLHA BASE'][14]                     += $row['total_base'];
            $valores['RENDIMENTOS'][14]                    += $valores['RENDIMENTOS'][$mes];
            $valores[$nome_porcentagem][14]                += ($row['total_base'] +  $valores['RENDIMENTOS'][$mes] )* $porcentagem;  
            $valores['INSS'][14]                           += $row['total_inss']; 
            $valores['FGTS'][14]                           += $row['total_fgts']; 
            $valores['PIS'][14]                            += $row['total_pis']; 
            $valores['TOTAL ENCARGOS TRABALHISTAS'][14]     += $row['total_guias']; 
            $valores['RESCISÕES'][14]                      += $row_rescisao['total_rescisao'];
            $valores['SALDO SALÁRIO RESCISÃO'][14]         += $total_saldoSalario;
            $valores['FÉRIAS'][14]                         += $row_ferias['total_ferias'];
            $valores['TOTAL PROVISÃO'][14]                 += $valores['TOTAL PROVISÃO'][$mes];
            $valores['FOLHA LÍQUIDA'][14]                  += $row['total_liquido_folha'];
            $valores['ENCARGOS'][14]                       += $row['total_inss'] + $row['total_fgts'] + $row['total_pis'];
            $valores['IRRF'][14]                           += $valores['IRRF'][$mes] ;
            $valores['INSS EMPREGADO'][14]                 += $valores['INSS EMPREGADO'][$mes];
            $valores['PROVISÕES TRABALHISTAS'][14]         += $valores['TOTAL PROVISÃO'][$mes] ;
            $valores['VALOR BRUTO RPA'][14]                 +=$valores['VALOR BRUTO RPA'][$mes]; 
            $valores['TOTAL'][14]                          += $valores['TOTAL'][$mes]; 
           
            }




            //ARMAZENANDO OS VALORES ANUAIS
            $valores_ano[$ano]           = $valores; 
            $ano_anterior = $row['ano'];

    }   
}

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Provisão e Gastos");
$breadcrumb_pages = array("Gestão de RH"=>"index.php");

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Provisão e Gastos</title>
        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <!--link href="../resources/css/bootstrap-rh.css" rel="stylesheet" type="text/css"-->
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Provisão e Gastos</small></h2></div>
                </div><!-- /.col-lg-12 -->
            </div><!-- /.row -->
            <form class="form-horizontal"  name="form" action="" method="post" id="form">
                <div class="panel panel-default">
                    <div class="panel-heading">Dados</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-lg-1 control-label">Região:</label>
                            <div class="col-lg-5">
                                <?=montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'form-control'))?>
                            </div>
                            <label class="col-lg-1 control-label">Projeto:</label> 
                            <div class="col-lg-5">
                                <select name="projeto" id="projeto" class="form-control">
                                    <?php
                                    $projetos = montaQuery('projeto', "id_projeto,nome", "id_regiao = '$_REQUEST[regiao]'");
                                    foreach ($projetos as $valor) {
                                        $selected = ($valor['id_projeto'] == $id_projeto)?'selected="selected"':'';
                                        echo '<option value="'.$valor['id_projeto'].'" '.$selected.' > '.$valor['id_projeto'].' - ' . $valor['nome'].'</option>';
                                    } ?>
                                </select> 
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-1 control-label">De: </label>
                            <div class="col-lg-4">
                                <div class="input-group">
                                    <?=montaSelect($opt_mov_cred, $mesSel_de, array('name' => "mes_de", 'id' => 'mes_de', 'class' => 'form-control'))?>
                                    <div class="input-group-addon"></div>
                                    <?=montaSelect($optAnos, $anoSel_de, array('name' => "ano_de", 'id' => 'ano_de', 'class' => 'form-control'))?>
                                </div>
                            </div>
                            <label class="text-center col-lg-1 control-label">Até: </label> 
                            <div class="col-lg-4">
                                <div class="input-group">
                                    <?=montaSelect($opt_mov_cred, $mesSel_ate, array('name' => "mes_ate", 'id' => 'mes_ate', 'class' => 'form-control'))?>
                                    <div class="input-group-addon"></div>
                                    <?=montaSelect($optAnos, $anoSel_ate, array('name' => "ano_ate", 'id' => 'ano_ate', 'class' => 'form-control'))?>
                                </div>
                            </div>
                            <label class="col-lg-1 control-label">Porcentagem:</label>
                            <div class="col-lg-1">
                                <input type="text" name="porcentagem" id="porcentagem" class="form-control" value="<?=$_REQUEST['porcentagem']?>"/>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <input class="btn btn-primary" type="submit" name="gerar" value="Gerar" id="gerar"/>
                    </div>
                </div>
            </form>
            <div class="row">
                <div class="col-lg-12">
                    <?php if(isset($_POST['gerar'])) { ?>
                        <?php if(count($valores_ano) > 0){ ?>
                            <?php foreach($valores_ano as $ano_chave => $valores_mes){  ?>
                                <table class="table table-condensed table-bordered table-hover">
                                    <thead>
                                        <tr class="info valign-middle text-sm">
                                            <th class="text-center"><h3><?php echo $ano_chave?></h3></th>
                                            <th class="text-center">JANEIRO</th>
                                            <th class="text-center">FEVEREIRO</th>
                                            <th class="text-center">MARÇO</th>
                                            <th class="text-center">ABRIL</th>
                                            <th class="text-center">MAIO</th>
                                            <th class="text-center">JUNHO</th>
                                            <th class="text-center">JULHO</th>
                                            <th class="text-center">AGOSTO</th>
                                            <th class="text-center">SETEMBRO</th>
                                            <th class="text-center">OUTUBRO</th>
                                            <th class="text-center">NOVEMBRO</th>
                                            <th class="text-center">DEZEMBRO</th>
                                            <th class="text-center">13º</th>
                                            <th class="text-center">TOTAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($valores_mes as $linha => $valor){

                                            ///TOTALIZADORES
                                            $TOTALIZADOR['TOTAL ENCARGOS TRABALHISTAS']  += ($linha == 'TOTAL ENCARGOS TRABALHISTAS') ? $valor[14] : '';
                                            $TOTALIZADOR['TOTAL PROVISÕES TRABALHISTAS'] += ($linha == 'PROVISÕES TRABALHISTAS') ? $valor[14] : '';
                                            $TOTALIZADOR['TOTAL RPA']                    += ($linha == 'VALOR BRUTO RPA') ? $valor[14] : '';
                                            $TOTALIZADOR['TOTAL FINAL']                  += ($linha == 'TOTAL') ? $valor[14] : '';

                                            switch($linha){
                                                case 'INSS': echo '<tr><td colspan="15" style="background-color:#EEE; text-align:center; height: 40px;">ENCARGOS TRABALHISTAS</td></tr>'; 
                                                    break;
                                                case 'FOLHA LÍQUIDA': echo '<tr><td colspan="15"  style="background-color:#EEE; text-align:center; height: 40px;">CUSTO TOTAL DA FOLHA DE PAGAMENTO</td></tr>'; 
                                                    break;
                                                case 'TOTAL ENCARGOS TRABALHISTAS':
                                                case $nome_porcentagem:
                                                case 'TOTAL PROVISÕES TRABALHISTAS':
                                                case 'TOTAL':
                                                    $class = 'style="background-color:#EEE;"';
                                                    break;
                                                default :  $class='';
                                            } ?>
                                            <tr class="valign-middle" <?=$class?>>
                                                <td><?=$linha?></td>
                                                <?php for($i=1; $i<15;$i++) { 

                                                    if($linha == 'PROVISÕES TRABALHISTAS' and $i == 13){ $class_red = ' style="color:  #f95532;"'; } else { $class_red ='';}

                                                    echo '<td '.$class_red.' >';
                                                    echo (!empty($valor[$i]))? number_format($valor[$i],2,',','.') :'';
                                                    echo '</td>';
                                                }?>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>

                                        </tr>
                                    </tfoot>
                                </table>
                            <?php } ?>
                        <?php } else { ?>
                            <div class="alert alert-warning">
                                Nenhum Resultado Encontrado!
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
<?php
           ///////////////////////////////////     
           ////MOSTRANDO TOTALIZADORES  /////
           ////////////////////////////////// 
            if(sizeof($TOTALIZADOR) >0 ){ ?>
                <div class="row">
                    <div class="col-lg-1"></div>
                    <div class="col-lg-10">
                        <table class="table center table-condensed table-bordered">
                            <tr class="titulo"> 
                                <td>PERÍODO</td>
                                <td><?=mesesArray($mes_de).'/'.$ano_de.' a '.mesesArray($mes_ate).'/'.$ano_ate?></td>
                            </tr>
                            <?php foreach ($TOTALIZADOR as $nome => $valor){ ?>
                                <tr> 
                                    <td><?=$nome?></td>
                                    <td><?=number_format($valor,2,',','.')?></td>
                                </tr>
                            <?php } ?>
                        </table>
                    </div>
                    <div class="col-lg-1"></div>
                </div>
            <?php } 
            include_once ('../template/footer.php'); ?>
        </div>
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        
        <script>
        $(function(){
            $('#regiao').change(function(){
                var id_regiao = $(this).val();
                $.post('../action.global.php',{regiao : id_regiao}, function(data){
                    $('#projeto').html(data);
                });
            });            
        });       
       </script>
    </body>
</html>