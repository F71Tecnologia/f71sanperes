<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}

include "../conn.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include "../wfunction.php";
include "../classes_permissoes/acoes.class.php";
include '../classes/CalculoFolhaClass.php';

$usuario = carregaUsuario();
$optProjeto = getProjetos($usuario['id_regiao']);
$optAnos = array();
$optAnos = anosArray(null, null, array('' => "<< Ano >>"));
$optMeses = mesesArray();

$ACOES = new Acoes();

$objCalcFolha = new Calculo_Folha();
$objCalcFolha->CarregaTabelas(date('Y'));

if (isset($_REQUEST['gerar'])) {
    $cont = 0;
    $inc = 0;
    $inc_ = 0;
    $idclt = 0;
    
    $arquivo_invalido = false;
    
    $id_projeto = $_REQUEST['projeto'];
    $mes = sprintf("%02d", $_REQUEST['mes']);
    $ano = $_REQUEST['ano'];
    
    $arquivo = $_FILES['arquivo'];
    $tipo_arquivo = $arquivo['type'];
    $nome_arquivo = $arquivo['name'];
    $temp_arquivo = $arquivo['tmp_name'];
    $exte_arquivo = end(explode(".", $nome_arquivo));                
    
    //SOMENTE CSV
    if($exte_arquivo == "csv"){
        $arquivo_invalido = false;       
        $abre_arquivo = fopen($temp_arquivo, "r");                 
    }else{
        $arquivo_invalido = true;
    }
}

$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$movimentoSel = (isset($_REQUEST['tipo_movimento'])) ? $_REQUEST['tipo_movimento'] : null;
$mesSel = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date("m");
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date("Y");

if ((isset($_REQUEST['confirmar_definitivo']))) {
    $clt = $_REQUEST['id_clt'];
    
    //FALTA
    $data_falta = $_REQUEST['data_falta'];
    $qtd_faltas = $_REQUEST['faltas'];
    $valor_falta = $_REQUEST['valor_falta'];    
    
    //ATRASO
    $qtd_atraso = $_REQUEST['atraso'];
    $valor_atraso = $_REQUEST['valor_atraso'];
    
    //PLANTAO EXTRA
    $plantao_extra = $_REQUEST['valor_extra'];
    
    $projeto = $_REQUEST['projeto'];
    $regiao = $usuario['id_regiao'];
    $mes = $_REQUEST['mes'];
    $ano = $_REQUEST['ano'];
    $data_atual = date("Y-m-d");
    
    $tot_array = count($clt);
    
    $id_usuario = $usuario['id_funcionario'];
    
    $cadastro_falta = false;
    $cadastro_atraso = false;
    
    //MOVIMENTO DE FALTA
    for($i=0; $i<$tot_array; $i++){
        $campos = array(
            "id_clt" => "'{$clt[$i]}'",
            "id_regiao" => "'{$regiao}'",
            "id_projeto" => "'{$projeto}'",
            "mes_mov" => "'{$mes}'",
            "ano_mov" => "'{$ano}'",
            "id_mov" => "'232'",
            "cod_movimento" => "'50249'",
            "tipo_movimento" => "'DEBITO'",
            "nome_movimento" => "'FALTA'",
            "data_movimento" => 'NOW()',
            "user_cad" => "'{$id_usuario}'",
            "valor_movimento" => "'{$valor_falta[$i]}'",
            "lancamento" => "'1'",
            "incidencia" => "'5020,5021,5023'",
            "tipo_qnt" => "'1'",
            "qnt_horas" => "'{$qtd_faltas[$i]}'",
            "obs" => "'{$data_falta[$i]}'",
            "importacao" => '1'
        );
        
        $nome_campo = implode(",", array_keys($campos));
        $val_campo = implode(",", $campos);
        
        if($valor_falta[$i] != 0){
            $insert_falta = "INSERT INTO rh_movimentos_clt ({$nome_campo}) VALUES ({$val_campo})";
            $query_falta = mysql_query($insert_falta) or die(mysql_error());
            
            //echo $insert_falta;
            //echo "<hr>";
            
            if($query_falta){
                $cadastro_falta = true;
            }
        }
    }
    
    //MOVIMENTO DE ATRASO
    for($i=0; $i<$tot_array; $i++){
        $campos = array(
            "id_clt" => $clt[$i],
            "id_regiao" => $regiao,
            "id_projeto" => $projeto,
            "mes_mov" => $mes,
            "ano_mov" => $ano,
            "id_mov" => '236',
            "cod_movimento" => '50252',
            "tipo_movimento" => "'DEBITO'",
            "nome_movimento" => "'ATRASO'",
            "data_movimento" => 'CURRENT_DATE',
            "user_cad" => $id_usuario,
            "valor_movimento" => "'{$valor_atraso[$i]}'",
            "lancamento" => '1',
            "incidencia" => "'5020,5021,5023'",
            "tipo_qnt" => '1',
            "qnt_horas" => "'{$qtd_atraso[$i]}'",            
            "importacao" => '1'
        );
        
        $nome_campo = implode(",", array_keys($campos));
        $val_campo = implode(",", $campos);
        
        if($valor_atraso[$i] != 0){
            $insert_atraso = "INSERT INTO rh_movimentos_clt ({$nome_campo}) VALUES ({$val_campo})";
            $query_atraso = mysql_query($insert_atraso) or die(mysql_error());
            
            if($query_atraso){
                $cadastro_atraso = true;
            }
        }
    }
    
    //MOVIMENTO DE PLANTAO EXTRA
    for($i=0; $i<$tot_array; $i++){
        $campos = array(
            "id_clt" => $clt[$i],
            "id_regiao" => $regiao,
            "id_projeto" => $projeto,
            "mes_mov" => $mes,
            "ano_mov" => $ano,
            "id_mov" => '255',
            "cod_movimento" => '50227',
            "tipo_movimento" => "'CREDITO'",
            "nome_movimento" => "'PLANTÃO EXTRA'",
            "data_movimento" => 'CURRENT_DATE',
            "user_cad" => $id_usuario,
            "valor_movimento" => "'{$plantao_extra[$i]}'",
            "lancamento" => '1',
            "incidencia" => "'5020,5021,5023'",            
            "importacao" => '1'
        );
        
        $nome_campo = implode(",", array_keys($campos));
        $val_campo = implode(",", $campos);
        
        if($plantao_extra[$i] != 0){
            $insert_extra = "INSERT INTO rh_movimentos_clt ({$nome_campo}) VALUES ({$val_campo})";
            $query_extra = mysql_query($insert_extra) or die(mysql_error());            
            
            if($query_extra){
                $cadastro_extra = true;
            }
        }
    }
    
//    print_array($clt);
//    print_array($data_falta);
//    print_array($qtd_faltas);
//    print_array($valor_falta);
//    print_array($qtd_atraso);
//    print_array($valor_atraso);
}
?>
<html>
    <head>
        <title>:: Intranet :: Importação de Planilha - Movimentos</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        
        <link href="../favicon.ico" rel="shortcut icon" />
        
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/global.js" type="text/javascript"></script>
        <script src="../js/ramon.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        
        <script>         
            // ao digitar, permite so numero
            function SomenteNumero(e){
                var tecla=(window.event)?event.keyCode:e.which;   
                if((tecla>47 && tecla<58)) return true;
                else{
                    if (tecla==8 || tecla==0) return true;
                    else  return false;
                }
            }
            
            $(function(){
                $("#form").validationEngine({promptPosition : "topRight"});                                
                
                $('#gerar').click(function () {
                    $(".act_clt").removeClass('validate[required]');
                });
                
                $(".act_clt").blur(function(){
                    var key = $(this).data("key");
                    var clt = $("#tr_"+key+" input").val();
                    var faltas = $("#tr_"+key+" #faltas").val();
                    var atraso = $("#tr_"+key+" #atraso").val();
                    
                    $.ajax({
                        type: "post",
                        url: "importar_xls_movimentos_action.php",
                        dataType: "json",
                        data: {
                            clt: clt,
                            faltas: faltas,
                            atraso: atraso,
                            method: "consulta_clt"
                        },
                        success: function(data) {
                            if(data.status == "1"){
                                $("#tr_"+key+" #nome_clt").html(data.nome);
                                $("#tr_"+key+" #nome_clt").addClass("blue_txt");
                                
                                if(data.valor_mov != '0,00'){
                                    $("#tr_"+key+" #val_mov").html(data.valor_movF);
                                    $("#tr_"+key+" #val_mov").addClass("blue_txt");
                                    $("#tr_"+key+" #valor_falta").val(data.valor_mov);
                                }
                                
                                if(data.valor_mov_atraso != '0,00'){
                                    $("#tr_"+key+" #val_mov_atraso").html(data.valor_mov_atrasoF);
                                    $("#tr_"+key+" #val_mov_atraso").addClass("blue_txt");
                                    $("#tr_"+key+" #valor_atraso").val(data.valor_mov_atraso);
                                }
                            }
                        }
                    });
                });
                
                $('#confirmar').click(function () {
                    thickBoxConfirm("Confirmar Movimentos", "Deseja realmente gravar os movimentos?", 300, 200, function (data) {
                        if (data == true) {
                            console.log('Ja eh..');
                            $('#confirmar_definitivo').trigger('click');
                        }
                    });
                });
            });
        </script>
        
        <style>
            .colEsq{
                width: auto;
                min-height: 0px;
                border-right: 0px;
                margin-right: 0px;
                float: none;
            }
            
            .bt-image{
                width: 18px;
                cursor: pointer;
            }
            
            h3 {
                text-align: center;
            }
            
            .red_bg{
                color: #F00;
            }
            
            .blue_txt{
                color: #00a3e4;
                font-weight: bold;
                text-transform: uppercase;
            }
        </style>
    </head>
    <body class="novaintra" >        
        <div id="content">
            <form name="form" action="" method="post" id="form" enctype="multipart/form-data">
                <div id="head">
                    <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>Importação de Planilha - Movimentos</h2>
                    </div>
                </div>
                <br class="clear">
                <br/>
                
                <fieldset class="noprint">
                    <legend>Relatório</legend>
                    <div class="fleft">
                        <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
                        <p><label class="first">Projeto:</label> <?php echo montaSelect($optProjeto, $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'validate[required,custom[select]]')); ?> </p>
                        <p><label class="first">Mês:</label> <?php echo montaSelect($optMeses, $mesSel, array('name' => "mes", 'id' => 'mes', 'class' => 'validate[required,custom[select]]')); ?> </p>
                        <p><label class="first">Ano:</label> <?php echo montaSelect($optAnos, $anoSel, array('name' => "ano", 'id' => 'ano', 'class' => 'validate[required,custom[select]]')); ?> </p>
                        <p>
                            <label class="first">Movimento:</label>
                            <select name="tipo_movimento" id="tipo_movimento" class="validate[required,custom[select]]">
                                <option value="-1">« Selecione »</option>
                                <option <?php echo selected(1, $movimentoSel); ?> value="1">Falta/Atraso</option>
                                <option <?php echo selected(2, $movimentoSel); ?> value="2">Plantão Extra</option>
                            </select>
                        </p>
                        <p><label class="first">Arquivo:</label> <input type="file" name="arquivo" id="arquivo" /> </p>
                    </div>
                    
                    <br class="clear"/>
                    
                    <p class="controls" style="margin-top: 10px;">                                                                            
                        <input type="submit" name="gerar" value="Gerar" id="gerar" class="gera" />
                        <?php if (isset($_REQUEST['gerar'])) { ?>
                        <input type="button" name="confirmar" value="Confirmar" id="confirmar" />
                        <input type="submit" style="display: none;" name="confirmar_definitivo" value="Confirmar" id="confirmar_definitivo" />
                        <?php } ?>
                    </p>
                </fieldset>
                
                <br />
                
                <?php if($arquivo_invalido){ ?>
                <div id="message-box" class="message-red alinha2">
                    Formato do arquivo inválido, somente CSV(separado por vírgulas)
                </div>
                <?php } ?>
                
                <?php if($cadastro_falta || $cadastro_atraso || $cadastro_extra){ ?>
                <div id="message-box" class="message-blue alinha2">
                    Movimentos Cadastrados com Sucesso!
                </div>
                <?php } ?>
                                 
                <?php if (!$arquivo_invalido && (isset($_POST['gerar']) && $_REQUEST['tipo_movimento'] == 1)){ ?>
                
                <div id="relatorio_exp">
                    <table border="0" cellpadding="0" cellspacing="0" class="grid" width="100%" style="page-break-after:auto;"> 
                        <thead>
                            <tr>
                                <th rowspan="2">ID</th>
                                <th rowspan="2">NOME FUNCIONARIO (EXCEL)</th>
                                <th rowspan="2">NOME FUNCIONARIO (SISTEMA)</th>
                                <th rowspan="2">FUNCAO</th>
                                <th colspan="3">FALTAS</th>
                                <th colspan="2">ATRASOS</th>
                            </tr>
                            <tr>
                                <th>DATA</th>
                                <th>HORAS</th>
                                <th>VALOR</th>                                
                                <th>HORAS</th>
                                <th>VALOR</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($res = fgetcsv($abre_arquivo, 2048, ";")){
                                
                                //obrigatorio ID_CLT ou NOME
                                if($res[0] != "" || $res[1] != ""){
                                    $class = ($cont++ % 2 == 0)?"even":"odd";
                                    
                                    if($res[0] == ''){
                                        $where = "A.nome LIKE '%{$res[1]}%' AND A.id_projeto = '{$id_projeto}'";
                                    }else{
                                        $where = "A.id_clt = '{$res[0]}'";
                                    }
                                    
                                    //CONSULTA CLT
                                    $qry = "SELECT A.*, B.salario AS sal_curso, B.tipo_insalubridade, B.qnt_salminimo_insalu, B.periculosidade_30, 
                                        C.adicional_noturno, C.horas_mes, C.horas_noturnas
                                        FROM rh_clt AS A
                                        LEFT JOIN curso AS B ON(A.id_curso = B.id_curso)
                                        LEFT JOIN rh_horarios AS C ON(A.rh_horario = C.id_horario)
                                        WHERE {$where}";
                                    $sql = mysql_query($qry) or die("ERRO consulta clt");
                                    $res_clt = mysql_fetch_assoc($sql);
                                    $id_clt = $res_clt['id_clt'];
                                    
                                    //echo "<strong>{$res[1]}</strong>({$id_clt}):<br> {$qry}<hr>";
                                    //print_array($res_clt);
                                    
                                    $salario = $res_clt['sal_curso'];
                                    $ad_noturno = $res_clt['adicional_noturno'];
                                    
                                    //INSALUBRIDADE
                                    $insalubridade = $objCalcFolha->getInsalubridade(30, $res_clt['tipo_insalubridade'], $res_clt['qnt_salminimo_insalu'], date('Y'));
                                    
                                    //PERICULOSIDADE
                                    if($res_clt['periculosidade_30']){                                        
                                        $periculosidade = $objCalcFolha->getPericulosidade($salario, 30, 12);                                       
                                    }
                                    
                                    //ADICIONAL NOTURNO
                                    if($ad_noturno){                                    
                                        $baseCalcAdiconal = $salario + $insalubridade['valor_integral'] + $periculosidade['valor_integral'];
                                        $adicional_noturno = $objCalcFolha->getAdicionalNoturno($baseCalcAdiconal, $res_clt['horas_mes'], $res_clt['horas_noturnas']);
                                        $dsr = $objCalcFolha->getDsr($adicional_noturno['valor_integral']);
                                    }
                                    
                                    //FALTAS
                                    $baseCalc = $salario + $insalubridade['valor_integral'] + $periculosidade['valor_integral'] + $adicional_noturno['valor_integral'] + $dsr['valor_integral'];
                                    $valor_hora = ($baseCalc) / $res_clt['horas_mes'];                                                                
                                    
//                                    echo "SALARIO: {$salario}<br> INSAL: {$insalubridade['valor_integral']}<br> PERICUL: {$periculosidade['valor_integral']}<br> AD NOTURNO: {$adicional_noturno['valor_integral']}<br> DSR: {$dsr['valor_integral']}<br>";                                                                
//                                    echo "<hr>";
                                    
                                    //ATRASO
                                    $baseCalcAtraso =  $salario + $insalubridade['valor_integral'] + $periculosidade['valor_integral'] ;                                    
                                    $valor_horaAtraso    = ($baseCalcAtraso) / $res_clt['horas_mes'];
                                    
                                    //CALCULO DE VALOR DE FALTAS
                                    list($qnt_hora, $qnt_minuto) = explode(':', $res[4]);
                                    $totalQnt = $qnt_hora + ($qnt_minuto / 60);
                                    $valorMov = $valor_hora * $totalQnt;
                                    $valorMovF = formataMoeda($valorMov, 1);
                                    
                                    if($res_clt['id_clt'] == 9028){
    //                                    echo "Hora mes: " . $res_clt['horas_mes'] . "<br>";
    //                                    echo "Salario: " . $salario . "<br>";
    //                                    echo "Insalubridade: " . $insalubridade['valor_integral'] . "<br>";
    //                                    echo "Periculosidade: " . $periculosidade['valor_integral'] . "<br>";
    //                                    echo "Adi. Noturno: " . $adicional_noturno['valor_integral'] . "<br>";
    //                                    echo "DSR: " . $dsr['valor_integral'] . "<br>";
                                    }
                                    
                                    //CALCULO DE VALOR DE ATRASO
                                    list($qnt_horaA, $qnt_minutoA) = explode(':', $res[5]);
                                    $totalQntA = $qnt_horaA + ($qnt_minutoA / 60);
                                    $valorMovAtraso = $valor_horaAtraso * $totalQntA;
                                    $valorMovAtrasoF = formataMoeda($valorMovAtraso, 1);
                                    
                                    unset($insalubridade);
                                    unset($periculosidade);
                                    unset($adicional_noturno);
                                    unset($dsr);                                                                        
                                    
                                    if($id_clt == ""){
                                        $fundo = "{$class} red_bg";
                                        $input_clt = "<input type='text' name='id_clt[]' class='act_clt validate[required]' id='id_clt_".$idclt++."' data-key='".$inc_++."' onkeypress='return SomenteNumero(event)' maxlength='5' />";
                                    }else{
                                        $fundo = $class;
                                        $input_clt = "<input type='hidden' name='id_clt[]' class='act_clt' id='id_clt_".$idclt++."' data-key='".$inc_++."' value='".$id_clt."' />";
                                    }
                                ?>
                                    <tr class="<?php echo $fundo; ?>" id="tr_<?php echo $inc++; ?>">
                                        <td><?php echo $id_clt.$input_clt; ?></td>
                                        <td><?php echo $res[1]; ?></td>
                                        <td>
                                            <span id="nome_clt"></span>
                                            <?php echo $res_clt['nome']; ?>
                                        </td>
                                        <td><?php echo $res[3]; ?></td>
                                        <td>
                                            <?php echo $res[2]; ?>
                                            <input type="hidden" name="data_falta[]" id="data_falta" value="<?php echo $res[2]; ?>" />
                                        </td>
                                        <td>
                                            <?php echo $res[4]; ?>
                                            <input type="hidden" name="faltas[]" id="faltas" value="<?php echo $res[4]; ?>" />
                                        </td>
                                        <td>
                                            <span id="val_mov"></span>
                                            <input type="hidden" name="valor_falta[]" id="valor_falta" value="<?php echo round($valorMov,2); ?>" />
                                            <?php
                                            if($valorMov != 0){
                                                echo $valorMovF;
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php echo $res[5]; ?>
                                            <input type="hidden" name="atraso[]" id="atraso" value="<?php echo $res[5]; ?>" />
                                        </td>
                                        <td>
                                            <span id="val_mov_atraso"></span>
                                            <input type="hidden" name="valor_atraso[]" id="valor_atraso" value="<?php echo $valorMovAtraso; ?>" />
                                            <?php
                                            if($valorMovAtraso != 0){
                                                echo $valorMovAtrasoF;
                                            }
                                            ?>
                                        </td>
                                    </tr>
                            <?php 
                                }
                            } ?>
                        </tbody>
                    </table>
                </div>
                <?php
                }elseif (!$arquivo_invalido && (isset($_POST['gerar']) && $_REQUEST['tipo_movimento'] == 2)){
                ?>
                    <div id="relatorio_exp2">
                        <table border="0" cellpadding="0" cellspacing="0" class="grid" width="100%" style="page-break-after:auto;"> 
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>NOME FUNCIONARIO (EXCEL)</th>
                                    <th>NOME FUNCIONARIO (SISTEMA)</th>
                                    <th>FUNCAO</th>                                    
                                    <th>VALOR</th>                                    
                                </tr>                                
                            </thead>
                            <tbody>
                                <?php
                                while ($res = fgetcsv($abre_arquivo, 2048, ";")){
                                    
                                    //obrigatorio ID_CLT ou NOME
                                    if($res[0] != "" || $res[1] != ""){
                                        $class = ($cont++ % 2 == 0)?"even":"odd";

                                        if($res[0] == ''){
                                            $where = "A.nome LIKE '%{$res[1]}%' AND A.id_projeto = '{$id_projeto}'";
                                        }else{
                                            $where = "A.id_clt = '{$res[0]}'";
                                        }

                                        //CONSULTA CLT
                                        $qry = "SELECT A.*, B.salario AS sal_curso, B.tipo_insalubridade, B.qnt_salminimo_insalu, B.periculosidade_30, 
                                            C.adicional_noturno, C.horas_mes, C.horas_noturnas
                                            FROM rh_clt AS A
                                            LEFT JOIN curso AS B ON(A.id_curso = B.id_curso)
                                            LEFT JOIN rh_horarios AS C ON(A.rh_horario = C.id_horario)
                                            WHERE {$where}";
                                        $sql = mysql_query($qry) or die("ERRO consulta clt");
                                        $res_clt = mysql_fetch_assoc($sql);
                                        $id_clt = $res_clt['id_clt'];
                                        
                                        //echo "<strong>{$res[1]}</strong>({$id_clt}):<br> {$qry}<hr>";
                                        //print_array($res_clt);        
                                        
                                        $valorExtra = ltrim(str_replace("R$", "", $res[3]));
                                        $valorExtra = str_replace(".", "", $valorExtra);
                                        $valorExtra = str_replace(",", ".", $valorExtra);
                                        
                                        if($id_clt == ""){
                                            $fundo = "{$class} red_bg";
                                            $input_clt = "<input type='text' name='id_clt[]' class='act_clt validate[required]' id='id_clt_".$idclt++."' data-key='".$inc_++."' onkeypress='return SomenteNumero(event)' maxlength='5' />";
                                        }else{
                                            $fundo = $class;
                                            $input_clt = "<input type='hidden' name='id_clt[]' class='act_clt' id='id_clt_".$idclt++."' data-key='".$inc_++."' value='".$id_clt."' />";
                                        }
                                    ?>
                                        <tr class="<?php echo $fundo; ?>" id="tr_<?php echo $inc++; ?>">
                                            <td><?php echo $id_clt.$input_clt; ?></td>
                                            <td><?php echo $res[1]; ?></td>
                                            <td>
                                                <span id="nome_clt"></span>
                                                <?php echo $res_clt['nome']; ?>
                                            </td>
                                            <td><?php echo $res[2]; ?></td>
                                            <td>
                                                <?php echo $res[3]; ?>
                                                <input type="hidden" name="valor_extra[]" id="valor_extra" value="<?php echo $valorExtra; ?>" />
                                            </td>
                                        </tr>
                                <?php
                                    }
                                } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            </form>
        </div>
    </body>
</html>