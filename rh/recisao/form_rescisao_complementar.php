<?php
include('../../conn.php');
include('../../funcoes.php');
include('../../classes/RescisaoClass.php');
include('../../classes/clt.php');
include('../../classes/abreviacao.php');
include('../../classes/formato_valor.php');
include('../../classes/formato_data.php');
include('../../classes/valor_proporcional.php');
include('classes/MovimentoRescisaoClass.php');

function printHelper($var) {
    echo '<pre>';
    print_r($var);
    echo '</pre>';
}

function getClt($id_clt) {
    $sql = "SELECT A.pis, A.nome AS nome_funcionario, A.endereco AS endereco_funcionario, A.bairro AS bairro_funcionario, A.cidade AS cidade_funcionario, A.uf AS uf_funcionario, A.cep AS cep_funcionario, A.campo1 AS numero_ctps, A.serie_ctps, A.uf_ctps, A.cpf, DATE_FORMAT(A.data_nasci, '%d/%m/%Y') AS data_nascimento, A.mae,
            C.logradouro AS logradouro_empresa, C.complemento AS complemento_empresa, C.bairro AS bairro_empresa, A.tipo_contrato, IF(A.tipo_contrato=1,'1. Contrato de Trabalho por Prazo Indeterminado',IF(A.tipo_contrato=2,'2. Contrato de Trabalho por Prazo Determinado', IF(A.tipo_contrato=3,'3. Contrato de Trabalho Temporário','Contrato de trabalho não especificado'))) AS nome_tipo_contrato,
            C.cidade AS cidade_empresa, C.uf AS uf_empresa, C.numero AS numero_empresa, C.cnpj, C.razao, C.endereco AS endereco_empresa, 
            C.cep AS cep_empresa, C.cnae AS cnae_empresa, IF(B.codigo_sindical IS NULL,'999.000.000.00000-3',B.codigo_sindical) AS cod_sindicato, DATE_FORMAT(A.data_entrada, '%d/%m/%Y') AS data_entrada
            FROM rh_clt AS A
            LEFT JOIN rhsindicato AS B ON(A.rh_sindicato= B.id_sindicato)
            LEFT JOIN rhempresa AS C ON(A.id_projeto= C.id_projeto)
            WHERE A.id_clt=$id_clt";
//    echo $sql.'<br>';
    $result = mysql_query($sql);
    $row_clt = mysql_fetch_array($result);
    return $row_clt;
}

$obj_movimento = new MovimentoRescisaoClass();

$movimentos_debito = $obj_movimento->getMovimentosLancaveis('DEBITO');

$arr_movimentos_debito = array();

foreach ($movimentos_debito as $mov) {
    $arr_movimentos_debito[] = array('value'=>$mov['campo_rescisao'].' - '.utf8_encode($mov['descicao']),'id_mov'=>$mov['id_mov'],'cod'=>$mov['campo_rescisao'],'nome'=>utf8_encode($mov['descicao']) ,'tipo_qnt_lancavel'=>$mov['tipo_qnt_lancavel']);
}




$movimentos_credito = $obj_movimento->getMovimentosLancaveis('CREDITO');

$arr_movimentos_credito = array();

foreach ($movimentos_credito as $mov) {
    $arr_movimentos_credito[] = array('value'=>$mov['campo_rescisao'].' - '.utf8_encode($mov['descicao']),'id_mov'=>$mov['id_mov'],'cod'=>$mov['campo_rescisao'],'nome'=>utf8_encode($mov['descicao']), 'tipo_qnt_lancavel'=>$mov['tipo_qnt_lancavel']);
}
//$movimentos_lancaveis_debito = $obj_movimento->getMovimentosLancaveis('DEBITO');
//$arr_movimentos_lancaveis_credito = $obj_movimento->getMovimentosLancaveis('CREDITO');
$movimentos_lancaveis_credito = array();
$movimentos_lancaveis_debito = array();

// em $arr_negacao passamos o campo_rescisao dos movimentos que não queremos que entrem pois já está gravando direto na tabela rh_recisao e não em rh_movimentos_rescisao
//$arr_negacao = array(69); 
//foreach($arr_movimentos_lancaveis_credito AS $m){
//    if(!in_array($m['campo_rescisao'],array(69))){ //,50,51,53,54,62,63,65,66,68,69,70,71,72,73
//        $movimentos_lancaveis_credito[] = $m;
//    }
//}
//echo '<pre>';
//print_r($movimentos_lancaveis_debito);
//print_r($movimentos_lancaveis_credito);
//echo '</pre>';
//exit();

$id_rescisao = isset($_REQUEST['id_rescisao']) ? $_REQUEST['id_rescisao'] : NULL;
$id_clt = isset($_REQUEST['id_clt']) ? $_REQUEST['id_clt'] : NULL;

$obj_rescisao = new Rescisao();
$row_rescisao = $obj_rescisao->getRescisao($id_rescisao);
$row_rescisao = $row_rescisao[0];


$row_clt = getClt($id_clt);

//var_dump($row_empresa);

$cnpj_empresa = $row_clt['cnpj'];
$razao_empresa = $row_clt['razao'];
$cep_empresa = $row_clt['cep'];
$cnae = $row_clt['cnae_empresa'];
$endereco_empresa = $row_clt['logradouro_empresa'];
$cnpj = $row_clt['cnpj'];
$municipio_empresa = $row_clt['cidade_empresa'];
$uf_empresa = $row_clt['uf_empresa'];
$bairro_empresa = $row_clt['bairro_empresa'];
$pis = $row_clt['pis'];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Rescis&atilde;o de <?php echo $id_clt . ' - ' . $nome; ?></title>
        <link href="rescisao_1.css" rel="stylesheet" type="text/css" />
        
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        <script src="../../js/jquery.price_format.2.0.min.js" type="text/javascript"></script>
        <script src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript"></script>
        <style type="text/css" media="print">
            table.rescisao td.secao {
                background-color:#C0C0C0;
                text-align:center;
                font-size:14px;
                height:20px;
            }
        </style>
        <script>

            arr_mov = new Array();
            arr_mov['1'] = <?= json_encode($arr_movimentos_credito); ?>;
            arr_mov['2'] = <?= json_encode($arr_movimentos_debito); ?>;
            
            
           function number_format(number, decimals, dec_point, thousands_sep) {
                number = (number + '')
                .replace(/[^0-9+\-Ee.]/g, '');
                        var n = !isFinite(+number) ? 0 : +number,
                    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                    s = '',
                            toFixedFix = function(n, prec) {
                var k = Math.pow(10, prec);
            return '' + (Math.round(n * k) / k)
            .toFixed(prec);
            };
                // Fix for IE parseFloat(0.55).toFixed(0) = 0;
                s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
                    .split('.');
                if (s[0].length > 3) {
                    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
                }
                if ((s[1] || '')
                        .length < prec) {
                    s[1] = s[1] || '';
                    s[1] += new Array(prec - s[1].length + 1)
                            .join('0');
                }
                return s.join(dec);
            }
            dados_credito = new Object();
                dados_debito = new Object();
            function somar_rescisorio_bruto() {
                var rescisorio_bruto = 0;

                $(".rescisorio_bruto").each(function(index) {
                    rescisorio_bruto = (eval($(this).val().replace('R$ ', '').replace('.', '').replace(',', '.')) + eval(rescisorio_bruto));
    //                    dados[index] = { $(this).attr('name') : $(this).val()};
                        //                    teste1 = $(this).attr('name');
                    var tipo = '';
                    var qnt = '';
                    if($(this).attr('data-tipo')!== undefined){
                        tipo = $(this).attr('data-tipo');
                    }
                    if($(this).attr('data-qnt')!== undefined){
                        qnt = $(this).attr('data-qnt');
                    }
                    dados_credito[index] = {name: $(this).attr('name'), valor: $(this).val().replace('R$ ', '').replace('.', '').replace(',', '.'), qnt_tipo : tipo, qnt: qnt};
                 });
                        //                console.log('Bruto rescisorio: ' + rescisorio_bruto);
                 $('#total_rescisorio_bruto').html('R$ ' + number_format(rescisorio_bruto, 2, ',', '.'));
                return rescisorio_bruto;
            }
            function somar_deducoes() {
                    var deducoes = 0;
                    $(".deducoes").each(function(index) {
                        
                        var tipo = '';
                        var qnt = '';
                        if($(this).attr('data-tipo')!== undefined){
                            tipo = $(this).attr('data-tipo');
                        }
                        if($(this).attr('data-qnt')!== undefined){
                            qnt = $(this).attr('data-qnt');
                        }
                        
                    deducoes = (eval($(this).val().replace('R$ ', '').replace('.', '').replace(',', '.')) + eval(deducoes));
                    dados_debito[index] = {name: $(this).attr('name'), valor: $(this).val().replace('R$ ', '').replace('.', '').replace(',', '.'), qnt_tipo : tipo, qnt: qnt};
                });
//                console.log(dados);
                $('#total_deducoes').html('R$ ' + number_format(deducoes, 2, ',', '.'));
                return deducoes;
            }
            
            var class_tipos = new Array();
            class_tipos['1'] = 'rescisorio_bruto';
            class_tipos['2'] = 'deducoes';

            $(function() {
                
                var tipos_quantidade = new Array('','Horas','Dias');
                var cont_td = new Array();
                cont_td[1] = 0;
                cont_td[2] = 0;
                $('#add_campo').click(function(){
                    
                    $this = $('#input_movimento_valor');
                    var id = $this.attr('data-id');
                    var cod = $this.attr('data-cod');
                    var nome = $this.attr('data-nome');
                    var valor = $this.val();
                    
                    if($this.attr('data-tipo_qnt_lancavel')==1){
                        nome += ' '+$('#mov_qtd').val()+' ('+tipos_quantidade[$('#tipo_quantidade').val()]+') ';
                    }
                    
                     if (id === undefined) {
                        alert('Selecione um movimento!');
                    }else{
                        var tipo_mov = $('#select_movimento_tipo').val();
                        var op = (cont_td[tipo_mov] % 3);
                        console.log(cont_td[tipo_mov]+'=>'+op);
                        
                        if($('.mov_'+id).length>0){
                            alert('Este campo já está incluso!');
                        }else{
                            var td = '<td class="mov_'+id+'"><span class="numero">'+cod+'</span>'+nome+'</td><td class="mov_'+id+'"><input type="" class="money_'+id+' '+class_tipos[tipo_mov]+' " value="'+valor+'" name="mov_'+id+'" data-tipo="'+$('#tipo_quantidade').val()+'" data-qnt="'+$('#mov_qtd').val()+'" /></td>';
                            if(op==0){
                                 $('#titulo_campos_'+tipo_mov).after('<tr>'+td+'</tr>');
                            }else{
                                $('#titulo_campos_'+tipo_mov).next().append(td);
                            }  
                            $('input.money_'+id).priceFormat({prefix: 'R$ ', centsSeparator: ',', thousandsSeparator: '.'});

                            $('.money_'+id).blur(function(e) {
                                rescisorio_bruto = somar_rescisorio_bruto();                     
                                deducoes = somar_deducoes();
                                valor_liquido_rescisorio = (rescisorio_bruto - deducoes);
                                console.log('bruto :' + rescisorio_bruto + ' - ' + deducoes + ' deduções');
                                console.log(valor_liquido_rescisorio);
                                $('#valor_rescisorio_liquido').html('R$ ' + number_format(valor_liquido_rescisorio, 2, ',', '.'));
                            });
                                

                            rescisorio_bruto = somar_rescisorio_bruto();
                            deducoes = somar_deducoes();
                            valor_liquido_rescisorio = (rescisorio_bruto - deducoes);
                            console.log('bruto :' + rescisorio_bruto + ' - ' + deducoes + ' deduções');
                            console.log(valor_liquido_rescisorio);
                            $('#valor_rescisorio_liquido').html('R$ ' + number_format(valor_liquido_rescisorio, 2, ',', '.'));
                                
                            cont_td[tipo_mov]++;
                        }
                    }
                });
                
                $('#select_movimento_tipo').change(function(){
                    var k = $(this).val();
                    console.log(arr_mov[k]);
                    $("#input_movimento").autocomplete({ source: arr_mov[k] });
                    $('#input_movimento_valor').val('R$ 0,00');
                    $("#input_movimento").val('');
                });
                $("#input_movimento").click(function(){
                   $(this).val(''); 
                });
                $("#input_movimento").autocomplete({ source: arr_mov[1], 
                       select: function( event, ui ) {
                           
                            console.log(ui.item);
                           
                           $('#input_movimento_valor').val('R$ 0,00');
                           $('#input_movimento_valor').focus();
                           $('#input_movimento_valor').attr('data-id',ui.item.id_mov);
                           $('#input_movimento_valor').attr('data-cod',ui.item.cod);
                           $('#input_movimento_valor').attr('data-nome',ui.item.nome);
                           $('#input_movimento_valor').attr('data-tipo_qnt_lancavel',ui.item.tipo_qnt_lancavel);
                           
                           if(ui.item.tipo_qnt_lancavel==1){
                               $('.campo_qnt').show();
                           }else{
                               $('.campo_qnt').hide();
                           }
                        }
                 });
                
//                $('.money').blur(function(e) {
//                    rescisorio_bruto = somar_rescisorio_bruto();                     
//                    deducoes = somar_deducoes();
//                    valor_liquido_rescisorio = (rescisorio_bruto - deducoes);
//                    console.log('bruto :' + rescisorio_bruto + ' - ' + deducoes + ' deduções');
//                    console.log(valor_liquido_rescisorio);
//                    $('#valor_rescisorio_liquido').html('R$ ' + number_format(valor_liquido_rescisorio, 2, ',', '.'));
//                });
                $('.money').blur(function(e) {
                    rescisorio_bruto = somar_rescisorio_bruto();                     
                    deducoes = somar_deducoes();
                    valor_liquido_rescisorio = (rescisorio_bruto - deducoes);
                    console.log('bruto :' + rescisorio_bruto + ' - ' + deducoes + ' deduções');
                    console.log(valor_liquido_rescisorio);
                    $('#valor_rescisorio_liquido').html('R$ ' + number_format(valor_liquido_rescisorio, 2, ',', '.'));
                });
                
                $('#processar_rescisao').click(function() {
//                    alert('Em manutenção!');
//                    return false;
                    if (confirm('Você deseja realmente processar a rescissão?')) {
                        dados = new Object();
                        somar_rescisorio_bruto();
                        somar_deducoes();
                        
                        console.log(dados_credito);
                        console.log(dados_debito);
                        
                        $.post('controlador.php', {acao: 'salva_rescisao_complementar', credito: dados_credito, debito: dados_debito, id_recisao: <?= $id_rescisao; ?>, id_clt: <?= $id_clt; ?>}, function(data) {
                              alert(data.msg);
                              window.history.go(-2);
                            console.log(data);
                        },'json');
                    }
                });
                $('#mov_qtd').mask('99:99');
                $('#mov_qtd').hide();
            });
            function set_tipo_mask(){
                $('#mov_qtd').val('');
                var $this = $('#tipo_quantidade');
                if($this.val()==1){
                    console.log('1 =>'+$this.val());
                    $('#mov_qtd').mask('99:99');
                }else{
                    $('#mov_qtd').unmask('99:99');
                    console.log('2 =>'+$this.val());
                }
            }
        </script>
    </head>
    <body>        
        <div style="width: 100%; background: #CCC; height: 30px; position: fixed;" >
            <select id="select_movimento_tipo">
                <option value="1">Movimento de Crédito</option>
                <option value="2">Movimento de Débito</option>
            </select>
            <label>
                <input type="text" value="" name="input_movimento" id="input_movimento"  style="width: 325px" />
            </label>
            <input type="text" id="input_movimento_valor" class="money" />
            
            <select name="tipo_quantidade" id="tipo_quantidade" class="campo_qnt" onchange="set_tipo_mask();" style="display: none">
                <option value="1" selected="selected" >Horas</option>
                <option value="2">Dias</option>
            </select>
            <input type="text" name="mov_qtd" id="mov_qtd" class="campo_qnt" data-key="287" style="width: 50px;"  style="display: none">
            
            <input type="button" id="add_campo" value="Adicionar Campo" />
                
            <input type="button" value="Processar" id="processar_rescisao" />
        </div>
        <br><br>
                <table class="rescisao" cellpadding="0" cellspacing="1" style="background: #FFF;">
                    <tr>
                        <td colspan="6" class="secao"><h1>TERMO DE RESCIS&Atilde;O DO CONTRATO DE TRABALHO</h1></td>
                    </tr>
                    <tr>
                        <td colspan="6" class="secao">IDENTIFICA&Ccedil;&Atilde;O DO EMPREGADOR</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="campo"><span class="numero">01</span> CNPJ/CEI</div>
                            <div class="valor"><?php echo $row_clt['cnpj']; ?></div>
                        </td>
                        <td colspan="4">
                            <div class="campo"><span class="numero">02</span> Raz&atilde;o Social/Nome</div>
                            <div class="valor"><?php echo $row_clt['razao']; ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <div class="campo"><span class="numero">03</span> Endere&ccedil;o (logradouro, n&ordm;, andar, apartamento)</div>
                            <div class="valor"><?php echo $row_clt['logradouro_empresa']; ?></div>
                        </td>
                        <td colspan="2">
                            <div class="campo"><span class="numero">04</span> Bairro</div>
                            <div class="valor"><?php echo $row_clt['bairro_empresa']; ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="campo"><span class="numero">05</span> Munic&iacute;pio</div>
                            <div class="valor"><?php echo $row_clt['cidade_empresa']; ?></div>
                        </td>
                        <td>
                            <div class="campo"><span class="numero">06</span> UF</div>
                            <div class="valor"><?php echo $row_clt['uf_empresa']; ?></div>
                        </td>
                        <td>
                            <div class="campo"><span class="numero">07</span> CEP</div>
                            <div class="valor"><?php echo $row_clt['cep_empresa']; ?></div>
                        </td>
                        <td>
                            <div class="campo"><span class="numero">08</span> CNAE</div>
                            <div class="valor"><?php echo $row_clt['cnae_empresa']; ?></div>
                        </td>
                        <td colspan="2">
                            <div class="campo"><span class="numero">09</span> CNPJ/CEI Tomador/Obra</div>
                            <div class="valor"><?php echo $row_clt['cnpj']; ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" class="secao">IDENTIFICA&Ccedil;&Atilde;O DO TRABALHADOR</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="campo"><span class="numero">10</span> PIS/PASEP</div>
                            <div class="valor"><?php echo $row_clt['pis']; ?></div>
                        </td>
                        <td colspan="4">
                            <div class="campo"><span class="numero">11</span> Nome</div>
                            <div class="valor"><?php echo $row_clt['nome_funcionario']; ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <div class="campo"><span class="numero">12</span> Endere&ccedil;o (logradouro, n&ordm;, andar, apartamento)</div>
                            <div class="valor"><?php echo $row_clt['endereco_funcionario']; ?></div>
                        </td>
                        <td colspan="2">
                            <div class="campo"><span class="numero">13</span> Bairro</div>
                            <div class="valor"><?php echo $row_clt['bairro_funcionario']; ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="campo"><span class="numero">14</span> Munic&iacute;pio</div>
                            <div class="valor"><?php echo $row_clt['cidade_funcionario']; ?></div>
                        </td>
                        <td>
                            <div class="campo"><span class="numero">15</span> UF</div>
                            <div class="valor"><?php echo $row_clt['uf_funcionario']; ?></div>
                        </td>
                        <td>
                            <div class="campo"><span class="numero">16</span> CEP</div>
                            <div class="valor"><?php echo $row_clt['cep_funcionario']; ?></div>
                        </td>
                        <td colspan="2">
                            <div class="campo"><span class="numero">17</span> Carteira de Trabalho (n&ordm;, s&eacute;rie, UF)</div>
                            <div class="valor"><?php echo $row_clt['numero_ctps'] . ' / ' . $row_clt['serie_ctps'] . ' / ' . $row_clt['uf_ctps']; ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="campo"><span class="numero">18</span> CPF</div>
                            <div class="valor"><?php echo $row_clt['cpf']; ?></div>
                        </td>
                        <td>
                            <div class="campo"><span class="numero">19</span> Data de nascimento</div>
                            <div class="valor"><?php echo $row_clt['data_nascimento']; ?></div>
                        </td>
                        <td colspan="3">
                            <div class="campo"><span class="numero">20</span> Nome da m&atilde;e</div>
                            <div class="valor"><?php echo $row_clt['mae']; ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" class="secao">DADOS DO CONTRATO</td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <div class="campo"><span class="numero">21</span> Tipo de Contrato</div>
                            <div class="valor">      
                                <?php echo $row_clt['nome_tipo_contrato']; ?>
                            </div>
                        </td>
                        <td colspan="3">
                            <div class="campo"><span class="numero">22</span> Causa do Afastamento</div>
                            <div class="valor"><?php echo $row_rescisao['causa_afastamento']; ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <div class="campo"><span class="numero">23</span> Remunera&ccedil;&atilde;o M&ecirc;s Anterior Afast.</div>
                            <div class="valor">R$ <?php echo formato_real($row_rescisao['sal_base']); ?></div>
                        </td>
                        <td>
                            <div class="campo"><span class="numero">24</span> Data de admiss&atilde;o</div>
                            <div class="valor"><?php echo $row_clt['data_entrada']; ?></div>
                        </td>
                        <td>
                            <div class="campo"><span class="numero">25</span> Data do Aviso Pr&eacute;vio</div>
                            <div class="valor"><?php echo formato_brasileiro($row_rescisao['data_aviso']); ?></div>
                        </td>
                        <td>
                            <div class="campo"><span class="numero">26</span> Data de afastamento</div>
                            <div class="valor"><?php echo $row_rescisao['data_demi_f']; ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <div class="campo"><span class="numero">27</span> C&oacute;d. afastamento</div>
                            <div class="valor"><?php echo $row_rescisao['codigo_afastamento']; ?></div>
                        </td>
                        <td>
                            <div class="campo"><span class="numero">28</span> Pens&atilde;o Aliment&iacute;cia (%) (TRCT)</div>
                            <div class="valor">0,00%</div>
                        </td>
                        <td>
                            <div class="campo"><span class="numero">29</span> Pens&atilde;o aliment&iacute;cia (%) (Saque FGTS)</div>
                            <div class="valor">0,00%</div>
                        </td>
                        <td>
                            <div class="campo"><span class="numero">30</span> Categoria do trabalhador</div>
                            <div class="valor">01</div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="campo"><span class="numero">31</span> C&oacute;digo Sindical</div>
                            <div class="valor"><?php echo $cod_sindicato; ?></div>
                        </td>
                        <td colspan="4">
                            <div class="campo"><span class="numero">32</span> CNPJ e Nome da Entidade Sindical Laboral</div>
                            <div class="valor"><?php echo $row_sindicato['cnpj'] . ' - ' . substr($row_sindicato['nome'], 0, 52); ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" class="secao">DISCRIMINA&Ccedil;&Atilde;O DAS VERBAS RESCIS&Oacute;RIAS</td>
                    </tr>
                    <tr>
                        <td colspan="6" class="secao">VERBAS RESCIS&Oacute;RIAS</td>
                    </tr>
                    <tr id="titulo_campos_1">
                        <td width="17%" class="secao_filho">Rubrica</td>
                        <td width="16%" class="secao_filho">Valor</td>
                        <td width="17%" class="secao_filho">Rubrica</td>
                        <td width="16%" class="secao_filho">Valor</td>
                        <td width="17%" class="secao_filho">Rubrica</td>
                        <td width="16%" class="secao_filho">Valor</td>
                    </tr>
                    <?php
                    $cont = 0;
                    $td_totalizador_credito = '<td class="secao" id="td_total_bruto_1">TOTAL RESCISÓRIO BRUTO</td><td class="secao"  id="td_total_bruto_2"><div class="valor" id="total_rescisorio_bruto">R$ 0,00</div></td>';



                    /*
                     *  valores que não são movimentos e que são gravados direto na tabela rh_recisao
                     * 
                     */

                    $movimentos_lancaveis_credito[] = array('campo_rescisao' => 50, 'descicao' => 'SALDO DE SALÁRIO', 'id_mov' => 'saldo_salario'); // dias_saldo, faltas
                    $movimentos_lancaveis_credito[] = array('campo_rescisao' => 51, 'descicao' => 'COMISSAO', 'id_mov' => 'comissao');
                    
                    
                    $movimentos_lancaveis_credito[] = array('campo_rescisao' => 53, 'descicao' => 'COMISSAO', 'id_mov' => 'insalubridade');
//                    $movimentos_lancaveis_credito[] = array('campo_rescisao' => 54, 'descicao' => 'ADICIONAL DE PERICULOSIDADE', 'id_mov' => 'periculosidade');
                    $movimentos_lancaveis_credito[] = array('campo_rescisao' => 62, 'descicao' => 'SALÀRIO FAMÍLIA', 'id_mov' => 'sal_familia');

                    $movimentos_lancaveis_credito[] = array('campo_rescisao' => 63, 'descicao' => '13 SALÁRIO PROPORCIONAL', 'id_mov' => 'dt_salario');
                    $movimentos_lancaveis_credito[] = array('campo_rescisao' => 65, 'descicao' => 'FÉRIAS PROPORCIONAIS', 'id_mov' => 'ferias_pr');
                    $movimentos_lancaveis_credito[] = array('campo_rescisao' => 66, 'descicao' => 'FÉRIAS VENCIDAS', 'id_mov' => 'ferias_vencidas');
                    $movimentos_lancaveis_credito[] = array('campo_rescisao' => 68, 'descicao' => 'TERÇO CONSTITUCIONAL DE FÉRIAS', 'id_mov' => 'umterco_fv');
                    $movimentos_lancaveis_credito[] = array('campo_rescisao' => 69, 'descicao' => 'AVISO VALOR', 'id_mov' => 'aviso_valor');
                    $movimentos_lancaveis_credito[] = array('campo_rescisao' => 70, 'descicao' => '13 SALÁRIO (AVISO PRÉVIO INDENIZADO)', 'id_mov' => 'terceiro_ss');
                    $movimentos_lancaveis_credito[] = array('campo_rescisao' => 71, 'descicao' => 'FÉRIAS AVISO INDENIZADO', 'id_mov' => 'ferias_aviso_indenizado');
                    $movimentos_lancaveis_credito[] = array('campo_rescisao' => 72, 'descicao' => 'FÉRIAS EM DOBRO', 'id_mov' => 'fv_dobro');
                    $movimentos_lancaveis_credito[] = array('campo_rescisao' => 73, 'descicao' => '1/3 FÉRIAS EM DOBRO', 'id_mov' => 'um_terco_ferias_dobro');
                    $movimentos_lancaveis_credito[] = array('campo_rescisao' => 99, 'descicao' => 'AJUSTE DO SALDO DEVEDOR', 'id_mov' => 'arredondamento_positivo');
                    $movimentos_lancaveis_credito[] = array('campo_rescisao' => 82, 'descicao' => '1/3 Férias (Aviso Prévio Indenizado)', 'id_mov' => 'umterco_ferias_aviso_indenizado');
                    $movimentos_lancaveis_credito[] = array('campo_rescisao' => 95, 'descicao' => 'Lei 12.506', 'id_mov' => 'lei_12_506');
                    $movimentos_lancaveis_credito[] = array('campo_rescisao' => 60, 'descicao' => 'Multa Art. 477/CLT', 'id_mov' => 'a477');
                    $movimentos_lancaveis_credito[] = array('campo_rescisao' => 61, 'descicao' => 'Multa Art. 479/CLT', 'id_mov' => 'a479');
                    $movimentos_lancaveis_credito[] = array('campo_rescisao' => 52, 'descicao' => '1/3 FÉRIAS PROPORCIONAIS', 'id_mov' => 'umterco_fp');



                    $movimentos_lancaveis_debito[] = array('campo_rescisao' => 104, 'descicao' => 'Multa Art. 480/CLT', 'id_mov' => 'a480');
                    $movimentos_lancaveis_debito[] = array('campo_rescisao' => '112.1', 'descicao' => 'Previdência Social', 'id_mov' => 'inss_ss');
                    $movimentos_lancaveis_debito[] = array('campo_rescisao' => '112.2', 'descicao' => 'Previdência Social - 13 Salário', 'id_mov' => 'inss_dt');
                    $movimentos_lancaveis_debito[] = array('campo_rescisao' => '114.1', 'descicao' => 'IRRF', 'id_mov' => 'ir_ss');
                    $movimentos_lancaveis_debito[] = array('campo_rescisao' => '114.2', 'descicao' => 'IRRF SOBRE 13', 'id_mov' => 'ir_dt');
                    $movimentos_lancaveis_debito[] = array('campo_rescisao' => '115', 'descicao' => 'DEVOLUÇÃO DE CRÉDITO INDEVIDO', 'id_mov' => 'devolucao');
                    $movimentos_lancaveis_debito[] = array('campo_rescisao' => '115.2', 'descicao' => 'ADIANTAMENTO DE 13º SALÁRIO', 'id_mov' => 'adiantamento_13');
                    $movimentos_lancaveis_debito[] = array('campo_rescisao' => '117', 'descicao' => 'FALTAS', 'id_mov' => 'valor_faltas');
                    $movimentos_lancaveis_debito[] = array('campo_rescisao' => '116', 'descicao' => 'IRRF FÉRIAS', 'id_mov' => 'ir_ferias');







//$movimentos_lancaveis_credito[] = array('campo_rescisao'=>69, 'descicao'=>'AVISO INDENIZADO', 'id_mov'=>'aviso_valor');
//
//
//
//
//
//
////$movimentos_lancaveis_credito[] = array('campo_rescisao'=>'xx', 'descicao'=>'FGTS 8', 'id_mov'=>'fgts8');
////$movimentos_lancaveis_credito[] = array('campo_rescisao'=>'xx', 'descicao'=>'FGTS 40', 'id_mov'=>'fgts40');
////$movimentos_lancaveis_credito[] = array('campo_rescisao'=>'xx', 'descicao'=>'FGTS ANTERIOR', 'id_mov'=>'fgts_anterior');
//
//$movimentos_lancaveis_credito[] = array('campo_rescisao'=>'112.1', 'descicao'=>'INSS', 'id_mov'=>'previdencia_ss');
////$movimentos_lancaveis_credito[] = array('campo_rescisao'=>'xx', 'descicao'=>'INSS DT', 'id_mov'=>'inss_dt');
////$movimentos_lancaveis_credito[] = array('campo_rescisao'=>'xx', 'descicao'=>'INSS FÉRIAS', 'id_mov'=>'inss_ferias');
//
//$movimentos_lancaveis_credito[] = array('campo_rescisao'=>'xx', 'descicao'=>'IR SS', 'id_mov'=>'ir_ss');
//$movimentos_lancaveis_credito[] = array('campo_rescisao'=>'xx', 'descicao'=>'IR DT', 'id_mov'=>'ir_dt');
//
//$movimentos_lancaveis_credito[] = array('campo_rescisao'=>'xx', 'descicao'=>'TERCEIRO SS', 'id_mov'=>'terceiro_ss');
//$movimentos_lancaveis_credito[] = array('campo_rescisao'=>'xx', 'descicao'=>'DT SALÁRIO', 'id_mov'=>'dt_salario');
//$movimentos_lancaveis_credito[] = array('campo_rescisao'=>'xx', 'descicao'=>'PREVIDENCIA DT', 'id_mov'=>'previdencia_dt');
//
//$movimentos_lancaveis_credito[] = array('campo_rescisao'=>66, 'descicao'=>'FÉRIAS VENCIDAS', 'id_mov'=>'ferias_vencidas');
//$movimentos_lancaveis_credito[] = array('campo_rescisao'=>'xx', 'descicao'=>'1/3 FÉRIAS VENCIDAS', 'id_mov'=>'umterco_fv');
//$movimentos_lancaveis_credito[] = array('campo_rescisao'=>65, 'descicao'=>'FÉRIAS PROPORCIONAIS', 'id_mov'=>'ferias_pr');
//$movimentos_lancaveis_credito[] = array('campo_rescisao'=>'xx', 'descicao'=>'1/3 FÉRIAS PROPORCIONAIS', 'id_mov'=>'umterco_fp');
//$movimentos_lancaveis_credito[] = array('campo_rescisao'=>'xx', 'descicao'=>'1/3 FÉRIAS EM DOBRO', 'id_mov'=>'um_terco_ferias_dobro');
//$movimentos_lancaveis_credito[] = array('campo_rescisao'=>'xx', 'descicao'=>'FÉRIAS AVISO INDENIZADO', 'id_mov'=>'ferias_aviso_indenizado');
//
//
//$movimentos_lancaveis_credito[] = array('campo_rescisao'=>'xx', 'descicao'=>'TOTAL SALÀRIO FAMÍLIA', 'id_mov'=>'to_sal_fami');
//$movimentos_lancaveis_credito[] = array('campo_rescisao'=>'xx', 'descicao'=>'LEI 12 506', 'id_mov'=>'lei_12_506');
//$movimentos_lancaveis_credito[] = array('campo_rescisao'=>'xx', 'descicao'=>'DEVOLUÇÃO', 'id_mov'=>'devolucao');
//$movimentos_lancaveis_credito[] = array('campo_rescisao'=>'xx', 'descicao'=>'VALOR DAS FALTAS', 'id_mov'=>'valor_faltas');
//$movimentos_lancaveis_credito[] = array('campo_rescisao'=>'xx', 'descicao'=>'1/3 FÉRIAS AVISO INDENIZADO', 'id_mov'=>'umterco_ferias_aviso_indenizado');
//$movimentos_lancaveis_credito[] = array('campo_rescisao'=>'xx', 'descicao'=>'ADIANTAMENTO 13', 'id_mov'=>'adiantamento_13');
//$movimentos_lancaveis_credito[] = array('campo_rescisao'=>'xx', 'descicao'=>'MULTA FV', 'id_mov'=>'fv_dobro');
//
//$movimentos_lancaveis_credito[] = array('campo_rescisao'=>'xx', 'descicao'=>'Multa Art. 477, § 8º/CLT', 'id_mov'=>'campo_a477');
//$movimentos_lancaveis_credito[] = array('campo_rescisao'=>'xx', 'descicao'=>'Multa Art. 479, § 8º/CLT', 'id_mov'=>'campo_a479');
//$movimentos_lancaveis_credito[] = array('campo_rescisao'=>'xx', 'descicao'=>'Multa Art. 480, § 8º/CLT', 'id_mov'=>'campo_a480');
////$movimentos_lancaveis_debito[] = array('campo_rescisao'=>104, 'descicao'=>' Multa Art. 480/CLT', 'id_mov'=>'multa_480');

                    foreach ($movimentos_lancaveis_credito as $movimento) {
                        if (($cont % 3) == 0) {
                            echo '<tr>';
                        }
                        ?>
                        <td><span class="numero"><?= $movimento['campo_rescisao'] ?></span>&nbsp;<?= $movimento['descicao'] ?></td>
                        <td><input type="" class="money rescisorio_bruto " value="0,00" name="<?= $movimento['id_mov'] ?>" /></td>                        
                        <?php
                        if (count($movimentos_lancaveis_credito) == ($cont + 1)) {
                            $sobra = count($movimentos_lancaveis_credito);
                            if (($sobra % 3) == 0) {
                                echo '</tr><tr><td></td><td></td><td></td><td></td>' . $td_totalizador_credito . '</tr>';
                            } else {
                                while (($sobra % 3) != 0) {
                                    if ((($sobra + 1) % 3) != 0) {
                                        echo '<td></td><td></td>';
                                    } else {
                                        echo $td_totalizador_credito;
                                    }
                                    $sobra++;
                                }
                            }
                        }

                        if (( ($cont + 1) % 3) == 0) {
                            echo '</tr>';
                        }
                        $cont++;
                    }
                    ?>

                    <tr>
                        <td colspan="6" class="secao">DEDU&Ccedil;&Otilde;ES</td>
                    </tr>

                    <tr id="titulo_campos_2">
                        <td class="secao_filho">Desconto</td>
                        <td class="secao_filho">Valor</td>
                        <td class="secao_filho">Desconto</td>
                        <td class="secao_filho">Valor</td>
                        <td class="secao_filho">Desconto</td>
                        <td class="secao_filho">Valor</td>
                    </tr>
                    <?php
                    $cont = 0;
                    $td_totalizador = '<td class="secao">TOTAL DAS DEDU&Ccedil;&Otilde;ES</td><td class="secao"><div class="valor" id="total_deducoes">R$ 0,00</div></td>' .
                            '<tr><td></td><td></td><td></td><td></td><td class="secao">VALOR RESCISÓRIO LÍQUIDO</td><td class="secao"><div class="valor" id="valor_rescisorio_liquido">R$ 0,00</div></td></tr>';
                    foreach ($movimentos_lancaveis_debito as $movimento) {
                        if (($cont % 3) == 0) {
                            echo '<tr>';
                        }
                        ?>
                        <td><span class="numero"><?= $movimento['campo_rescisao'] ?></span>&nbsp;<?= $movimento['descicao'] ?></td>
                        <td><input type="" class="money deducoes " value="0,00" name="<?= $movimento['id_mov'] ?>" /></td>                        
                        <?php
                        if (count($movimentos_lancaveis_debito) == ($cont + 1)) {
                            $sobra = count($movimentos_lancaveis_debito);
                            if (($sobra % 3) == 0) {
                                echo '</tr><tr><td></td><td></td><td></td><td></td>' . $td_totalizador . '</tr>';
                            } else {
                                while (($sobra % 3) != 0) {
                                    if ((($sobra + 1) % 3) != 0) {
                                        echo '<td></td><td></td>';
                                    } else {
                                        echo $td_totalizador;
                                    }
                                    $sobra++;
                                }
                            }
                        }
                        if (( ($cont + 1) % 3) == 0) {
                            echo '</tr>';
                        }
                        $cont++;
                    }
                    ?>

                </table>
                <table  class="rescisao" cellpadding="0" cellspacing="1" style="page-break-before: always; margin-top:20px;" >
                    <tr>
                        <td colspan="6" class="secao"><h1>TERMO DE QUITAÇÃO DO CONTRATO DE TRABALHO</h1></td>
                    </tr>

                    <tr>
                        <td colspan="6" class="secao">EMPREGADOR</td>
                    </tr>

                    <tr>
                        <td colspan="2">
                            <div class="campo"><span class="numero">01</span> CNPJ/CEI</div>
                            <div class="valor"><?php echo $row_clt['cnpj']; ?></div>
                        </td>
                        <td colspan="4">
                            <div class="campo"><span class="numero">02</span> Raz&atilde;o Social/Nome</div>
                            <div class="valor"><?php echo $row_clt['razao']; ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" class="secao">TRABALHADOR</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="campo"><span class="numero">10</span> PIS/PASEP</div>
                            <div class="valor"><?php echo $row_clt['pis']; ?></div>
                        </td>
                        <td colspan="4">
                            <div class="campo"><span class="numero">11</span> Nome</div>
                            <div class="valor"><?php echo $row_clt['nome_funcionario']; ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="campo"><span class="numero">17</span> Carteira de Trabalho (n&ordm;, s&eacute;rie, UF)</div>
                            <div class="valor"><?php echo $row_clt['numero_ctps'] . ' / ' . $row_clt['serie_ctps'] . ' / ' . $row_clt['uf_ctps']; ?></div>
                        </td>

                        <td colspan="2">
                            <div class="campo"><span class="numero">18</span> CPF</div>
                            <div class="valor"><?php echo $row_clt['cpf']; ?></div>
                        </td>
                        <td>
                            <div class="campo"><span class="numero">19</span> Data de nascimento</div>
                            <div class="valor"><?php echo $row_clt['data_nascimento']; ?></div>
                        </td>
                        <td colspan="3">
                            <div class="campo"><span class="numero">20</span> Nome da m&atilde;e</div>
                            <div class="valor"><?php echo $row_clt['mae']; ?></div>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="6" class="secao">CONTRATO</td>
                    </tr>

                    <tr>   
                        <td colspan="6">
                            <div class="campo"><span class="numero">22</span> Causa do Afastamento</div>
                            <div class="valor"><?php echo $row_rescisao['causa_afastamento']; ?></div>
                        </td>
                    </tr>
                    <tr>    
                        <td>
                            <div class="campo"><span class="numero">24</span> Data de admiss&atilde;o</div>
                            <div class="valor"><?php echo $row_clt['data_entrada']; ?></div>
                        </td>
                        <td>
                            <div class="campo"><span class="numero">25</span> Data do Aviso Pr&eacute;vio</div>
                            <div class="valor"><?php echo $row_rescisao['data_aviso_f']; ?></div>
                        </td>
                        <td>
                            <div class="campo"><span class="numero">26</span> Data de afastamento</div>
                            <div class="valor"><?php echo $row_rescisao['data_demi_f']; ?></div>
                        </td>
                        <td>
                            <div class="campo"><span class="numero">27</span> C&oacute;d. afastamento</div>
                            <div class="valor"><?php echo $row_rescisao['codigo_afastamento']; ?></div>
                        </td>  
                        <td colspan="2">
                            <div class="campo"><span class="numero">29</span> Pens&atilde;o aliment&iacute;cia (%) (Saque FGTS)</div>
                            <div class="valor">0,00%</div>
                        </td>  
                    </tr>
                    <tr>  
                        <td colspan="6">
                            <div class="campo"><span class="numero">30</span> Categoria do trabalhador</div>
                            <div class="valor">01</div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="campo"><span class="numero">31</span> C&oacute;digo Sindical</div>
                            <div class="valor"><?php echo $row_clt['cod_sindicato']; ?></div>
                        </td>
                        <td colspan="4">
                            <div class="campo"><span class="numero">32</span> CNPJ e Nome da Entidade Sindical Laboral</div>
                            <div class="valor"><?php echo $row_sindicato['cnpj'] . ' - ' . substr($row_sindicato['nome'], 0, 52); ?></div>
                        </td>
                    </tr>

                    <tr style="border: 0px;">
                        <td colspan="6" style="border: 0px;">
                            <div class="campo">
                                Foi prestada, gratuitamente, assist&ecirc;ncia na rescisão do contrato de trabalho, nos termos do art. 477, &sect; 1&ordm;,
                                da Consolida&ccedil;&atilde;o das Leis do Trabalho (CLT), sendo comprovado, neste ato, o efetivo pagamento das verbas rescis&oacute;rias
                                acima especificadas no corpo do TRCT, no valor líquido de R$ <?php echo formato_real($row_rescisao['total_liquido']); ?>, o qual devidamente rubricado pelas partes, é parte integrante
                                do presente Termo de Homologação. <br >
                                    </p>
                                    <p>As partes assistidas no presente ato de rescisão contratual foram identificadas como legitimas conforme previsto na Instrução Normativa/SRT nº 15/2010</p>
                                    <p>Fico ressalvado o direito de o trabalhador pleitear judicialmente os direitos informados no camppo 155, abaixo.</p>

                                    <p>____________________/___, ____ de _______________________ de _______. </p>
                                    <p>&nbsp;</p>
                                    <p>&nbsp;</p>
                                    <p>___________________________________________________________<br>
                                            150 Assinatura do Empregador ou Preposto
                                    </p>
                            </div>
                        </td>   
                    </tr>

                    <tr style="border: 0px;">
                        <td colspan="3" style="border: 0px;" >

                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p>___________________________________________________________<br>
                                    151 Assinatura do Trabalhador
                            </p>
                        </td>
                        <td colspan="3" style="border: 0px;">
                            <p>&nbsp;</p>
                            <p>&nbsp;</p> 
                            <p>___________________________________________________________<br>
                                    152 Assinatura do Responsável Legal do Trabalhador
                            </p>
                        </td>
                    </tr>

                    <tr style="border: 0px;">
                        <td colspan="3"  style="border: 0px;">
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p>___________________________________________________________<br>
                                    153 Carimbo e Assinatura do Assistente
                            </p>

                        </td>
                        <td colspan="3"  style="border: 0px;">
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p>___________________________________________________________<br>
                                    154 Nome do Órgão Homologador
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" >   <div class="campo"><span class="numero">155</span> Ressalvas</div> 
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>

                        </td>      
                    </tr>
                    <tr>
                        <td colspan="6">
                            <div class="campo"><span class="numero">156</span> Informações à CAIXA</div> 
                            <p>&nbsp;</p>

                        </td>
                    </tr>   
                    <tr>
                        <td colspan="6">
                            <p style="text-align:center;">
                                <strong> ASSISTÊNCIA NO ATO DE RESCISÃO CONTRATUAL É GRATUITA.</strong><br>
                                    Pode o trabalhador iniciar ação judicial quanto aos créditos resultantes das relações de trabalho até o limite de dois anos após a extinção do contrato de trabalho (inciso XXIX, art. 7º da Constituição Federal/1988).
                            </p>
                        </td>
                    </tr>
                </table>
                </body>
                </html>