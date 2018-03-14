<?php
include('adm/include/restricoes.php');
include('conn.php');
include('classes_permissoes/regioes.class.php');

if(isset($_REQUEST['master'])){
    $id_master  = mysql_real_escape_string($_REQUEST['master']);

    echo utf8_encode('<option value="">« Selecione a Região »</option>');
    $qr_regiao= mysql_query("SELECT * FROM regioes WHERE id_master = $id_master ORDER BY regiao");
    while($row_regiao = mysql_fetch_assoc($qr_regiao)):	
            echo '<option value="'.$row_regiao['id_regiao'].'">'.$row_regiao['id_regiao'].' - '.htmlentities(utf8_encode($row_regiao['regiao'])).'</option> ';
    endwhile;
}

if(isset($_REQUEST['regiao'])){
    $id_regiao  = mysql_real_escape_string($_REQUEST['regiao']);
    echo utf8_encode('<option value="">« Selecione o Projeto »</option>');
    $qr_projeto = mysql_query("SELECT * FROM projeto WHERe id_regiao = $id_regiao ORDER BY nome");
    while($row_projeto = mysql_fetch_assoc($qr_projeto)):	
            echo '<option value="'.$row_projeto['id_projeto'].'">'.$row_projeto['id_projeto'].' - '.htmlentities(utf8_encode($row_projeto['nome'])).'</option> ';
    endwhile;
    exit;
}




if(isset($_REQUEST['projeto'])){
    
    $id_projeto = mysql_real_escape_string($_REQUEST['projeto']);
    
    echo '<option value="">Selecione o banco....</option>';
    $qr_banco = mysql_query("SELECT * FROM bancos WHERe id_projeto = $id_projeto AND status_reg = 1");
    while($row_banco = mysql_fetch_assoc($qr_banco)):	
            echo '<option value="'.$row_banco['id_banco'].'">'.htmlentities(utf8_encode($row_banco['nome'])).' AG: '.$row_banco['agencia'].' C: '.$row_banco['conta'].'</option>';
    endwhile;
}

//retira acento
function retiraAcentos($string){
    return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),explode(" ","a A e E i I o O u U n N"),$string);
}

//prepara caractar para colocar na input sem acento e com _ no nome
function preparaCaracter($string)
{
    $temp = explode(" ", $string);
    $id   = "";
    for($x=0;$x<count($temp);$x++)
    {
        if($x != 1)
        {
            $id .= $temp[$x]."_";
        }
    }
    $string = substr($id,0,-1);
    return strtolower(retiraAcentos($string));
}

if(isset($_REQUEST['tipo_lotacao'])){
    
    $id_lotacao =     $_REQUEST['tipo_lotacao'];
    $qr_lotacao = mysql_query("select ta.id_tipo_lotacao_assoc,ta.id_lotacao,ta.campo from tipos_lotacao_assoc ta inner JOIN tipos_de_lotacao AS tl ON (ta.id_lotacao = tl.id_tp_lotacao) and ta.id_lotacao = {$id_lotacao}");
    $rows = mysql_num_rows($qr_lotacao);
    if($rows > 0)
    {
        
        while($row_lotacao = mysql_fetch_assoc($qr_lotacao)):
            $campo = "";
            $campo = preparaCaracter($row_lotacao['campo']);
            echo "<div class=\"form-group\">";
            echo utf8_encode("<label class=\"col-xs-2 control-label\">{$row_lotacao['campo']}:</label>");
            echo utf8_encode("<div class=\"col-xs-4\"><input type=\"text\" name=\"{$campo}\" id=\"{$campo}\" class=\"form-control\" value=\"\" placeholder=\"Digite o {$row_lotacao['campo']}\"/></div>");
            echo "</div>";
            
        endwhile;
    }
    return 1;
    
}

if(isset($_REQUEST['fpas'])){
    
    $fpas    =     $_REQUEST['fpas'];
    $qr_fpas = mysql_query("SELECT fa.tipo FROM fpas f INNER JOIN fpas_terceiros_assoc AS fa ON (f.id=fa.fpas) WHERE f.id = '{$fpas}' GROUP BY fa.tipo");
    $rows = mysql_num_rows($qr_fpas);
    $flag = 0;
    if($rows > 0)
    {
        while($row_fpas = mysql_fetch_assoc($qr_fpas)):	
            if($row_fpas['tipo'] != "")
            {
               if($flag == 0)
               {
                   echo '<option value="">SELECIONE O TIPO</option>';
                   $flag++;
               }
                echo '<option value="'.$row_fpas['tipo'].'">'.$row_fpas['tipo'].'</option>';
            }
            else
            {
                echo '<option value="">Sem Tipo</option>';
            }
            
        endwhile;
    }
    else
    {
        echo 1;
    }
     
    
}

if(isset($_REQUEST['tributaria'])){
    
    $fpas    =     $_REQUEST['idfpas'];
    $tributaria = $_REQUEST['tributaria'];
    
    $qr_fpas2 = mysql_query("SELECT cfct.id_fpas, cfct.id_classificacao, ct.codigo, ct.descricao FROM compatibilidade_fpas_classificacao_tributaria cfct
INNER JOIN classificacao_tributaria as ct on(cfct.id_classificacao = ct.codigo) where cfct.id_fpas = {$fpas}");
    $rows = mysql_num_rows($qr_fpas2);
    echo utf8_encode("<option value=''>SELECIONE A CLASSIFICAÇÃO</option>");
    if($rows > 0)
    {
        while($row_fpas = mysql_fetch_assoc($qr_fpas2)):	
            echo utf8_encode('<option value="'.$row_fpas['codigo'].'">'.$row_fpas['codigo']." - ".$row_fpas['descricao'].'</option>');
            
        endwhile;
    }
         
    
}

if(isset($_REQUEST['tipofpas'])){
    
    $fpas    = $_REQUEST['id_fpas'];
    $tipos   = $_REQUEST['tipofpas'];
    $qr_fpas2 = mysql_query("SELECT fa.* FROM fpas f INNER JOIN fpas_terceiros_assoc AS fa ON (f.id=fa.fpas) WHERE f.id = '{$fpas}' and fa.tipo = '{$tipos}' GROUP BY fa.recolhimento");
    $rows = mysql_num_rows($qr_fpas2);
    
    switch($rows)
    {
        case 1:
                    $total = "";
                    $total_terceiro = "";
                    $qr_recolhimento = mysql_query("SELECT fa.*,CASE fa.recolhimento 
                                                    WHEN 0 THEN 'SEM CONVÊNIO'
                                                    WHEN 1 THEN 'COM CONVÊNIO SESI + SENAI'
                                                    WHEN 2 THEN 'COM CONVÊNIO SESI'
                                                    WHEN 3 THEN 'COM CONVÊNIO SENAI'
                                                   END AS REC, ft.nome, ft.codigo as terceiros FROM fpas f INNER JOIN fpas_terceiros_assoc AS fa ON (f.id=fa.fpas) 
                                                   INNER JOIN fpas_terceiros AS fT ON (fT.id=fa.terceiro) 
                                                   WHERE f.id = '{$fpas}' and fa.tipo = '{$tipos}'");
                    echo "<table class='table table-bordered table-hover'>";
                    echo "<thead><tr><th colspan='3' class='center alert-info'>TABELA DE FINANCIAMENTO DE TERCEIROS</th></tr>";
                    echo utf8_encode("<tr class='alert-info'><th class='center'>TERCEIROS</th><th class='center'>CÓDIGO DE TERCEIROS</th><th class='center'>ALÍQUOTA</th></thead>");
                    while($row_rec = mysql_fetch_assoc($qr_recolhimento)):	
                        echo utf8_encode("<tr class='note-title'><td class='center'>{$row_rec['nome']}</td><td class='center'>{$row_rec['terceiros']}</td><td class='center'>".str_replace(".",",",($row_rec['aliquota']*100))."%</td></tr>");
                        $total_terceiro += $row_rec['terceiros'];
                        $total += ($row_rec['aliquota']*100);
                    endwhile;
                    echo "<tfoot><tr class='alert-info'><th style='text-align:center;'>TOTAL</th><th style='text-align:center;'>{$total_terceiro}</th><th style='text-align:center;'>".str_replace(".",",",$total)."%</th></tr></tfoot>";
                    echo "</table>";
                    
                    break;
                    
       case 4:
                    $total = "";
                    $total_terceiro = "";
                    
                    echo utf8_encode("<table class='table table-bordered table-hover'>
                            <thead><tr class='alert-success'><th colspan='4' class='center'>Tabela de Convênios de Terceiros</th></tr><tr><th>Situação do Contribuinte</th><th>Combinação dos Códigos de Terceiros</th><th>Código de Terceiros</th><th>Aliquota</th></tr></thead>");
                    
                    $qr_convenio = mysql_query("SELECT * FROM convenio_terceiros");
                    
                    while($row_conv = mysql_fetch_assoc($qr_convenio)):
                        echo utf8_encode("<tr><td><input type=\"radio\" name=\"situacao_contribuinte\" id=\"radios-{$row_conv['recolhimento']}\" value=\"{$row_conv['recolhimento']}\" class=\"radio_convenio\"> <label for=\"radios-{$row_conv['recolhimento']}\" class=\"pointer\" style=\"font-weight: normal;\">{$row_conv['situacao']}</label> </td>");
                        echo utf8_encode("<td>{$row_conv['combinacao']}</td>");
                        echo utf8_encode("<td>{$row_conv['codigo_terceiro']}</td>");
                        echo "<td>".str_replace(".",",",($row_conv['aliquota']*100))."%</td></tr>";
                        $conttr++;
                    endwhile;
                    echo "<tfoot><tr class='alert-success'><th colspan='4'>&nbsp;</th></tr></tfoot></table>";
                    echo "<div class=\"container\">&nbsp;</div>";
                    $qr_recolhimento = mysql_query("SELECT fa.*,CASE fa.recolhimento 
                                                    WHEN 0 THEN 'SEM CONVÊNIO'
                                                    WHEN 1 THEN 'COM CONVÊNIO SESI + SENAI'
                                                    WHEN 2 THEN 'COM CONVÊNIO SESI'
                                                    WHEN 3 THEN 'COM CONVÊNIO SENAI'
                                                   END AS REC, ft.nome, ft.codigo as terceiros FROM fpas f INNER JOIN fpas_terceiros_assoc AS fa ON (f.id=fa.fpas) 
                                                   INNER JOIN fpas_terceiros AS fT ON (fT.id=fa.terceiro) 
                                                   WHERE f.id = '{$fpas}' and fa.tipo = '{$tipos}' GROUP BY fa.terceiro");
                    echo "<div class=\"tabelafinanceiro\">";
                    echo "<table class='table table-bordered table-hover'>";
                    echo "<thead><tr><th colspan='3' class='center alert-info'>TABELA DE FINANCIAMENTO DE TERCEIROS</th></tr>";
                    echo utf8_encode("<tr class='alert-info'><th class='center'>TERCEIROS</th><th class='center'>C&Oacute;DIGO DE TERCEIROS</th><th class='center'>ALÍQUOTA</th></thead>");
                    while($row_rec = mysql_fetch_assoc($qr_recolhimento)):	
                        echo utf8_encode("<tr class='note-title'><td class='center'>{$row_rec['nome']}</td><td class='center'>{$row_rec['terceiros']}</td><td class='center'>".str_replace(".",",",($row_rec['aliquota']*100))."%</td></tr>");
                        $total_terceiro += $row_rec['terceiros'];
                        $total += ($row_rec['aliquota']*100);
                    endwhile;
                    if($total_terceiro > 100)
                    {
                        $total_terceiro = "0".$total_terceiro;
                    }
                    if($total_terceiro < 100)
                    {
                        $total_terceiro = "00".$total_terceiro;
                    }
                    if($total_terceiro < 10)
                    {
                        $total_terceiro = "000".$total_terceiro;
                    }
                    echo "<tfoot><tr class='alert-info'><th style='text-align:center;'>TOTAL</th><th style='text-align:center;'>{$total_terceiro}</th><th style='text-align:center;'>".str_replace(".", ",",$total)."%</th></tr></tfoot>";
                    echo "</table>";
                    echo "</div>";
                    break;
    }
}    
    if(isset($_REQUEST['recolhimento'])){
        $recolhimento = $_REQUEST['recolhimento'];
        $total = "";
        $total_terceiro = "";
        $qr_recolhimento = mysql_query("SELECT combinacao FROM convenio_terceiros where recolhimento = {$recolhimento}");
        echo "<table class='table table-bordered table-hover'>";
        echo "<thead><tr><th colspan='3' class='center alert-info'>TABELA DE FINANCIAMENTO DE TERCEIROS</th></tr>";
        echo utf8_encode("<tr class='alert-info'><th class='center'>TERCEIROS</th><th class='center'>CÓDIGO DE TERCEIROS</th><th class='center'>ALÍQUOTA</th></thead>");
        while($row_rec = mysql_fetch_assoc($qr_recolhimento)):	
            $temp = explode("+",$row_rec['combinacao']);
            
            foreach($temp as $key=>$valores)
            {
                $qr_dados = mysql_query("SELECT nome,aliquota FROM fpas_terceiros where codigo = '{$valores}'");
                while($row_dados = mysql_fetch_assoc($qr_dados)):	
                echo utf8_encode("<tr class='note-title'><td class='center'>{$row_dados['nome']}</td><td class='center'>{$valores}</td><td class='center'>".str_replace(".", ",", ($row_dados['aliquota']*100))."%</td></tr>");
                $total_terceiro += $valores;
                $total += ($row_dados['aliquota']*100);
                endwhile;
            }

        endwhile;
        if($total_terceiro > 100)
        {
            $total_terceiro = "0".$total_terceiro;
        }
        if($total_terceiro < 100)
        {
            $total_terceiro = "00".$total_terceiro;
        }
        if($total_terceiro < 10)
        {
            $total_terceiro = "000".$total_terceiro;
        }
        echo "<tfoot><tr class='alert-info'><th style='text-align:center;'>TOTAL</th><th style='text-align:center;'>{$total_terceiro}</th><th style='text-align:center;'>".str_replace(".", ",", $total)."%</th></tr></tfoot>";
        echo "</table>";
        echo "<div class=\"container\">&nbsp;</div>";
    }


