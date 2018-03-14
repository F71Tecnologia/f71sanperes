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

$dtFim = ($row_clt['data_pdeBR'] == "" || $row_clt['data_pde'] == "1969-12-31") ? $row_clt['data_fim_experienciaBR'] : $row_clt['data_pdeBR'];
?>


<div class="pagina">
    <p class="titulo_documento"><img src="../imagens/logomaster1.gif"/></p>
    <br>
    <p class="titulo_documento">TÉRMINO DO CONTRATO DE EXPERIÊNCIA</p>
    <br>
    <br>
    <br>

    <p class="text-justify">Sr.(a)  <?= $row_clt['nome'] ?>.</p><br>

    <p class="text-justify">Pelo presente o(a) notificamos que seu contrato de experiência termina em <strong><?php echo $dtFim ?></strong>,
        sendo que a partir de então, o <?php echo $row_clt['empresa'] ?> não necessitará mais dos seus trabalhos,
        devendo, portanto, cessar suas atividades na referida data.</p><br><br><br>

    <p class="text-justify">Pedimos a devolução do presente com o seu CIENTE abaixo:</p>

    <br><br><p class="text-justify">Atenciosamente:</p>


    <br>
    <br>


    <p class="">_____________________________________________</p>
    <p class="" text-bold" style="font-size: .8em"><?= $row_clt['empresa'] ?></p>


    <br>
    <br>
    <p class="">_____________________________________________</p>
    <p class="" text-bold" style="font-size: .8em"> <?= $row_clt['nome'] ?></p>

</div>