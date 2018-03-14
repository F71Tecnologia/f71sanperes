<?php
include('../include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
include('../include/criptografia.php');
?>
<html>
    <head>
        <title>Administração de Notas Fiscais</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="../css/estrutura.css" rel="stylesheet" type="text/css">
        <style type="text/css">
            .tr_titulo { font-size: 12px; font-weight: bold; }
        </style>

        <script type="text/javascript" src="../../js/highslide-with-html.js"></script> 
        <link rel="stylesheet" type="text/css" href="../../js/highslide.css" /> 
        <script type="text/javascript"> 
            hs.graphicsDir = '../../images-box/graphics/';
            hs.outlineType = 'rounded-white';
        </script>

        <script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js" ></script>
        <script type="text/javascript">
            $(document).ready(function(){
                
                $("#lote").live("click", function(){
                    $("#form1").attr("action", "anexo_notas_lote.php");
                    $("#form1").submit();
                });
                
                $('.show').click(function() {
		
                    var pagina_action = $(this).attr('href');
                    var proximo 	  = $(this).next();		
                    var id_regiao 	  = $(this).next().children().val() ;
                    
                    $('.show').not(this).removeClass('seta_aberto');
                    $('.show').not(this).addClass('seta_fechado');
                    
                    if($(this).attr('class')=='show seta_aberto') {
                        $(this).removeClass('seta_aberto');
                        $(this).addClass('seta_fechado');
                    } else {
                        $(this).removeClass('seta_fechado');
                        $(this).addClass('seta_aberto');
                        $(this).append('<div id="carregando"> <img src="../../imagens/carregando_adm.gif" height="15"/> </div>');		
                        $.ajax({
                            url: pagina_action,
                            type:'GET',											
                            success: function(resposta) {		
                                $('#carregando').remove();
                                proximo.html('');
                                proximo.html(' <input type="hidden" name="regiao" value="'+id_regiao+'"/>' + resposta);	
                                //cor da linha
                                $('.azul').parent().css({'background-color': '#7EB3F1'});
                                $('.vermelho').parent().css({'background-color': '#ffb8c0'});
                            }
                        });
                    }
                    
                    $('.show').not(this).next().hide();
                    $(this).next().css({'width':'100%'}).slideToggle('fast');
                });
            });
        </script>
        <style>
            #carregando{
                width:100%;
                text-align:left;
                margin-left:-5px;
                margin-top:-15px;
                display:block;
            }
            .titulo_projeto{
                margin:20px;
                font-size: 12px;
                background-color:   #DADADA;
                border: 1px  #E2E2E2 solid;
                color: #616161;
                padding-top:5px;
                font-weight:bold;
                width:400px;
                height: 25px;
                text-align:center;

            }
            a.mostrar_notas{
                width: 100%;
                text-decoration:none;
                background-color: #EFEFEF;
                display:block;
                border: 2px  #E1E1E1 solid;
                font-size:14px;
                color: #616161;
                font-weight:bold;
            }
            a.mostrar_notas:hover{
                background-color: #AEAEAE;
                color: #FFF;
            }
            table tr.secao_nova{
                font-size:11px;
                color: #FFF	;
                background-color:#AAA;

            }
            .linha_1{
                background-color:#FAFAFA;
                font-size:11px;
            }
            .linha_2 {
                background-color:#F3F3F3;
                font-size:11px;
            }
        </style>

    </head>
    <body>
        <div id="corpo">
            <div id="menu" class="nota">
                <?php include "include/menu.php"; ?>
            </div>
            <form action="" method="post" name="form1" id="form1">
                <div id="conteudo" style="text-transform:uppercase;">  
                    <?php
//                    echo "SELECT *, A.regiao as nome_regiao,
//                            IF(B.status_reg = 1, 'PROJETOS ATIVOS', 'PROJETOS INATIVOS') as tipo_status
//                            FROM regioes as A
//                            INNER JOIN projeto as B
//                            ON A.id_regiao = B.id_regiao
//                            INNER JOIN funcionario_regiao_assoc as C
//                            ON C.id_regiao = A.id_regiao
//                            where A.id_master = '$Master'
//                            AND C.id_funcionario = '$_COOKIE[logado]'
//                            AND B.status_reg IN(0,1)
//                            GROUP BY B.id_regiao, B.status_reg
//                            ORDER BY B.status_reg  DESC ;
//                            ;";


                    $qr_regioes = mysql_query("SELECT *, A.regiao as nome_regiao,
                                               IF(B.status_reg = 1, 'PROJETOS ATIVOS', 'PROJETOS INATIVOS') as tipo_status
                                               FROM regioes as A
                                               INNER JOIN projeto as B
                                               ON A.id_regiao = B.id_regiao
                                               INNER JOIN funcionario_regiao_assoc as C
                                               ON C.id_regiao = A.id_regiao
                                               where A.id_master = '$Master'
                                               AND C.id_funcionario = '$_COOKIE[logado]'
                                               AND B.status_reg IN(0,1)
                                               GROUP BY B.id_regiao, B.status_reg
                                               ORDER BY B.status_reg  DESC ;
                                               ;");

                    while ($row_regiao = mysql_fetch_assoc($qr_regioes)):
                        $ordem++;

                        if ($row_regiao['status_reg'] != $status_anterior) {
                            echo '<h3 class="titulo">' . $row_regiao['tipo_status'] . '</h3>';
                        }

                        if ($row_regiao['id_regiao'] != $reg_anterior) {

                            $seta = ($_GET['aberto'] == $ordem) ? 'seta_aberto' : 'seta_fechado';
                            $link = "action.index_notas_teste.php?status=" . $row_regiao['status_reg'] . "&regiao=" . $row_regiao['id_regiao'] . "&m=" . $link_master;
                            ?>  
                            <a class="show <?php echo $seta ?>"  id="<?= $ordem ?>" href="<?php echo $link; ?>"   onClick="return false">
                                <span style="text-transform:uppercase;padding-left:10px;">  <?= $row_regiao['nome_regiao'] ?></span>              
                            </a>

                            <div class="<?= $ordem ?>" style="width:90%; <?php if ($_GET['aberto'] != $ordem) {
                        echo 'display:none;';
                    } ?> "></div>

                            <?php
                        }
                        $reg_anterior = $row_regiao['id_regiao'];
                        $status_anterior = $row_regiao['status_reg'];
                    endwhile;
                    ?>
                </div>        
            </form>
            <div id="rodape">
<?php include('../include/rodape.php'); ?>
            </div>
        </div>
    </body>
</html>