<?php
$clt = $this->funcionario;
$id_reg = $this->regiao;
$id_user = $_COOKIE['logado'];

$data = date('d/m/Y');

$result_clt = mysql_query("SELECT A.nome, A.rg, 
                            DATE_FORMAT(A.data_aviso, '%d/%m/%Y') AS data_avisoBR, 
                            DATE_FORMAT(A.data_entrada, '%d/%m/%Y') AS data_entradaBR, 
                            DATE_FORMAT(A.data_pde, '%d/%m/%Y') AS data_pdeBR, 
                            DATE_FORMAT(DATE_ADD(A.data_entrada, INTERVAL '89' DAY), '%d/%m/%Y') AS data_fim_experienciaBR,
                            CONCAT(C.nome,' - ',C.razao) AS empresa, C.municipio
                            FROM rh_clt AS A
                            LEFT JOIN projeto AS B ON (A.id_projeto = B.id_projeto)
                            LEFT JOIN master AS C ON (B.id_master = C.id_master)
                            where id_clt = '$clt'");
$row_clt = mysql_fetch_array($result_clt);

$meses_pt = array('Erro', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');

$dia = date("d");
$mes = date("m");
$ano = date("Y");

$dtFim = ($row_clt['data_pdeBR']=="" || $row_clt['data_pde'] == "1969-12-31") ? $row_clt['data_fim_experienciaBR'] : $row_clt['data_pdeBR'];
?>

<div class="pagina">
    
    <div class="col-span-12 text-center">
        <img src="../imagens/logomaster1.gif">
        <hr style="margin-top: 1px; margin-bottom: 11px;" />
    </div>
    <br/>
    <br/>
    <div class="col-span-12 text-right"><p><?php echo $row_clt['municipio'].", ".$dia. " de ".$meses_pt[(int)$mes]." de ".$ano; ?></p></div>
    <br/>
    <div class="col-span-12 text-center">
        <h1>INTERRUPÇÃO DO CONTRATO DE EXPERIÊNCIA</h1>
    </div>
    <br/>
    <br/>
    <h3>Sr.(a) <strong><?php echo $row_clt['nome'] ?></strong></h3>
    <br/>
    <div class="col-span-12 text-justify">
        <p>Pelo presente o (a) notificamos que por não mais convir a esta empresa manter seu contrato de experiência, 
            cujo término estava previsto para o dia <strong><?php echo $dtFim ?></strong>, a partir da entrega deste não mais serão utilizados os seus serviços pelo <?php echo $row_clt['empresa'] ?>.</p>
        <br/>
        <p>Para efeitos legais, será indenizado, conforme Artigo 479 da CLT.</p>
        
    </div>
    <br/>
    <div class="col-span-12 text-center">
        <p>Pedimos a devolução do presente com o seu CIENTE abaixo:</p>
        
    </div>
    <br/>
    <br/>
    <br/>
    <p>Atenciosamente</p>
    <br/>    
    <br/>
    <br/>
    
    <br/>    
    <br/>
    <p>_______________________________________________</p>
    <p><?php echo $row_clt['empresa']; ?></p>
        
    <br/>    
    <br/>
    <p>_______________________________________________</p>
    <p><?php echo $row_clt['nome']; ?></p>
    
</div>
