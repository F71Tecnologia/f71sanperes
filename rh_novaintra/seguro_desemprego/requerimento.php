<?php

/*
 * 
 * dependências 
 *              tabela : seguro_desemprego_doc
 *                          CREATE TABLE `seguro_desemprego_doc` (
                                `id_seguro_desemprego_form` INT(11) NOT NULL AUTO_INCREMENT,
                                `id_clt` INT(11) NULL DEFAULT NULL,
                                `nome` VARCHAR(40) NULL DEFAULT NULL,
                                `nome_mae` VARCHAR(40) NULL DEFAULT NULL,
                                `endereco` VARCHAR(40) NULL DEFAULT NULL,
                                `complemento` VARCHAR(16) NULL DEFAULT NULL,
                                `cep` VARCHAR(8) NULL DEFAULT NULL,
                                `uf` VARCHAR(2) NULL DEFAULT NULL,
                                `tel` VARCHAR(10) NULL DEFAULT NULL,
                                `pis` VARCHAR(11) NULL DEFAULT NULL,
                                `ctps_numero` VARCHAR(7) NULL DEFAULT NULL,
                                `ctps_serie` VARCHAR(3) NULL DEFAULT NULL,
                                `ctps_uf` VARCHAR(2) NULL DEFAULT NULL,
                                `cpf` VARCHAR(11) NULL DEFAULT NULL,
                                `tipo_inscricao` INT(1) NULL DEFAULT NULL,
                                `cnpj` VARCHAR(14) NULL DEFAULT NULL,
                                `cbo` VARCHAR(6) NULL DEFAULT NULL,
                                `ocupacao` VARCHAR(22) NULL DEFAULT NULL,
                                `data_admissao` DATE NULL DEFAULT NULL,
                                `data_dispensa` DATE NULL DEFAULT NULL,
                                `sexo` INT(1) NULL DEFAULT NULL,
                                `grau_instrucao` INT(1) NULL DEFAULT NULL,
                                `data_nascimento` DATE NULL DEFAULT NULL,
                                `hora_semana` VARCHAR(2) NULL DEFAULT NULL,
                                `banco` VARCHAR(3) NULL DEFAULT NULL,
                                `antepenultimo_mes` VARCHAR(2) NULL DEFAULT NULL,
                                `antepenultimo_salario` DECIMAL(13,2) NULL DEFAULT NULL,
                                `penultimo_mes` VARCHAR(2) NULL DEFAULT NULL,
                                `penultimo_salario` DECIMAL(13,2) NULL DEFAULT NULL,
                                `ultimo_mes` VARCHAR(2) NULL DEFAULT NULL,
                                `ultimo_salario` DECIMAL(13,2) NULL DEFAULT NULL,
                                `recebeu_ultimos_meses` INT(1) NULL DEFAULT NULL,
                                `aviso_indenizado` INT(1) NULL DEFAULT NULL,
                                `criado_por` INT(11) NULL DEFAULT NULL,
                                `criado_em` DATETIME NOT NULL,
                                `alterado_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                `status_doc` INT(1) NULL DEFAULT NULL,
                                PRIMARY KEY (`id_seguro_desemprego_form`)
                        )
                        COLLATE='latin1_swedish_ci'
                        ENGINE=MyISAM
                        AUTO_INCREMENT=13;

 * 
 * 
 * 
 * 
 *              plugin : js/interface_1.2/
 *              arquivo : rh/seguro_desemprego/layout.json
 * 
 */

if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}
include "../../conn.php";
include "../../classes/funcionario.php";
include '../../classes_permissoes/regioes.class.php';
include "../../wfunction.php";

$usuario = carregaUsuario();


if(isset($_POST['acao']) && ($_POST['acao']=='salvar_layout')){
    
    // Abre ou cria o arquivo bloco1.txt
    // "a" representa que o arquivo é aberto para ser escrito
    $fp = fopen("layout.json", "w");

    $param = json_encode( array('p'=>$_POST['dados'],'img'=>$_POST['img']) );
    
    // Escreve "exemplo de escrita" no bloco1.txt
    $escreve = fwrite($fp, $param);

    // Fecha o arquivo
    fclose($fp);
    
    echo json_encode(array('status'=>1,'msg'=>'Salvo com sucesso!','json'=> $param));
    exit();
}




$id_clt = isset($_REQUEST['id_clt']) ? $_REQUEST['id_clt'] : NULL;

$sql = "SELECT A.*, DATE_FORMAT(A.data_dispensa,'%d%m%y') AS data_dispensa_f,  DATE_FORMAT(A.data_admissao,'%d%m%y') AS data_admissao_f"
        . ",  DATE_FORMAT(A.data_nascimento,'%d%m%y') AS data_nascimento_f  FROM seguro_desemprego_doc AS A WHERE A.id_clt=$id_clt AND A.`status_doc`=1 ORDER BY A.id_seguro_desemprego_form DESC LIMIT 1";

$dados = mysql_fetch_array(mysql_query($sql));

if(empty($dados)){
    exit('Acesse pelo link do sistema');
}




?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
    <head>
        <title>Requerimento SD</title>
        <link rel="stylesheet" type="text/css" href="assets/style.css" />
        <script type="text/javascript" src="/intranet/js/jquery-1.8.2.min.js"></script>
        <script type="text/javascript" src="/intranet/js/interface_1.2/source/iutil.js"></script>
        <script type="text/javascript" src="/intranet/js/interface_1.2/source/idrag.js"></script>
        <script>

        $(function(){

             $('#requerimento').find('p').each(function( index ) {

                  str = $( this ).text(); 
                  conteudo = '';
                  for(x=0;x<=(str.length-1);x++){ 
                    conteudo += '<span id="y_'+index+'_'+x+'" >'+str.slice(x,(x+1))+'</span>';
//                    conteudo += str.slice(x,(x+1));
                  }
                  $(this).attr('id', 'x_'+ index);
                  $(this).html(conteudo);

            });

             $('#salvar').click(function(){
             
                arr = new Array();
                $('#requerimento').find('p').each(function( index ) {
                   arr[index] = $(this).position();
//                    console.log( {target: index,  position : $(this).position()});
                });
                img = $('.img_background').position();
                
                
                 $.post('requerimento.php', {acao: 'salvar_layout', dados : arr, img: img},function(data){
                     if(data.status){
                        alert(data.msg)
                    }
//                    console.log(data);
                },'json');
             });

           
        
            var position = $('.img_background').position();
            
            $('#subir').click(function(){
                position.top--;
                $('.img_background').css('top',position.top);
            });
            $('#descer').click(function(){
                position.top++;
                $('.img_background').css('top',position.top);
            });
            
            $('#esquerda').click(function(){
                position.left--;
                $('.img_background').css('left',position.left);
            });
            
            $('#direita').click(function(){
                position.left++;
                $('.img_background').css('left',position.left);
            });
            
            $('#box_edit').hide();
            
            var opc = $(this).attr('data-opc');
            draggable_controller(opc);
            
            $('#bt_edit').click(function(){
                
                var opc = $(this).attr('data-opc');               
                
                draggable_controller(opc);
                
                if(opc==1){
                    $('p').addClass('phover');
                    $(this).attr('data-opc','2');
                }else{
                    $('p').removeClass('phover');
                    $(this).attr('data-opc','1');
                }
                
                
                
                $('#box_edit').toggle();
            });
            
            
        });
        function draggable_controller(opc){
            if(opc==1){
                $('p[id^=x_]').Draggable(
                   {
                       zIndex:     1000,
                       ghosting:   true,
                       opacity:    0.7
                   }
                );
           }else{
               $('p[id^=x_]').DraggableDestroy();
           }
        }
        function print_ajustar_fundo(){
            if($('#ck_ajustar_fundo').is(':checked')){
                $('#img_background').removeClass('noprint');
            }else{
                $('#img_background').addClass('noprint');
            }
        }
        
        </script>
        <style>
            <?php 
                $ponteiro = fopen ("layout.json","r");
                $linha_layout = '';
                while (!feof ($ponteiro)) {
                    $linha_layout .= fgets($ponteiro, 4096);
                }

                fclose($ponteiro);

                $arr_layout = json_decode($linha_layout);
                
                echo "\n".'.img_background  { top: '.$arr_layout->img->top.'px; left: '.$arr_layout->img->left.'px;  }'."\n\n";
                
                foreach($arr_layout->p as $k=>$v){
        //            print_r($k);
        //            echo '<br>';
                    echo '#x_'.$k.' {top : '.$v->top.'px; left : '.$v->left.'px}'."\n\n";
                }
             ?>
        </style>
    </head>
    <body >
        
        
        <page size="A4" id="requerimento" class="requerimento" >
        <img src="assets/requerimento.JPG" class="img_background noprint" id="img_background" alt="Requerimento Seguro Desemprego" width="800" />
		
                <p title="nome"><?= $dados['nome'] ?></p>
                <p title="nome da mãe"><?= $dados['nome_mae'] ?></p>
                <p title="Endereço"><?= $dados['endereco'] ?></p>
                <p title="Complemento"><?= $dados['complemento'] ?></p>
                <p title="Cep"><?= $dados['cep'] ?></p>
                <p title="UF"><?= $dados['uf'] ?></p>
                <p title="Tel"><?= $dados['tel'] ?></p>
                <p title="PIS"><?= $dados['pis'] ?></p>
                <p title="Número CTPS"><?= $dados['ctps_numero'] ?></p>
                <p title="Série CTPS"><?= $dados['ctps_serie'] ?></p>
                <p title="UF CTPS"><?= $dados['ctps_uf'] ?></p>
                <p title="CPF"><?= $dados['cpf'] ?></p>
                <p title="Tipo Inscrição"><?= $dados['tipo_inscricao'] ?></p>
                <p title="CNPJ"><?= $dados['cnpj'] ?></p>
                <p title="CBO"><?= $dados['cbo']; ?></p>
                <p title="Ocupacao"><?= $dados['ocupacao']; ?></p>
                <p title="Data Admissao"><?= $dados['data_admissao_f']; ?></p>
                <p title="Data Dispensa"><?= $dados['data_dispensa_f']; ?></p>
                <p title="Sexo"><?= $dados['sexo']; ?></p>
                <p title="Grau Instrução"><?= $dados['grau_instrucao']; ?></p>
                <p title="Data nascimento"><?= $dados['data_nascimento_f']; ?></p>
                <p title="Horas Semanais"><?= $dados['hora_semana']; ?></p>
                <p title="Banco"><?= $dados['banco']; ?></p>
                <p title="Antepenúltimo Salário"><?=  str_replace('.', ' ', $dados['antepenultimo_salario']); ?></p>
                <p title="Antepenúltimo Mês"><?= $dados['antepenultimo_mes']; ?></p>
                <p title="Penúltimo Salário"><?= str_replace('.', ' ', $dados['penultimo_salario']); ?></p>
                <p title="Penúltimo Mês"><?= $dados['penultimo_mes']; ?></p>
                <p title="Último Salário"><?= str_replace('.', ' ', $dados['ultimo_salario']); ?></p>
                <p title="Último Mês"><?= $dados['ultimo_mes']; ?></p>
                <p title="Recebeu últimos mêses"><?= $dados['recebeu_ultimos_meses']; ?></p>
                <p title="Aviso Indenizado"><?= $dados['aviso_indenizado']; ?></p>
                <p title="Soma dos três últimos salarios"><?= str_replace('.', '',$dados['antepenultimo_salario']) + str_replace('.', '',$dados['penultimo_salario']) + str_replace('.', '',$dados['ultimo_salario']); ?></p>
                <p title="nome"><?= $dados['nome']; ?></p>
                <p title="PIS"><?= $dados['pis']; ?></p>
        </page>

        <div class="acoes no-print" style="position: absolute; top: 0; left: 810px;background: #C0C0C0;border: 1px solid #333;padding: 8px;">
            <!--<input type="button" value="Mudar para folha 2" id="folha" />-->
            <a href="#"  style="float: left;" id="bt_edit" data-opc="1">Modo edição</a><br><br>
            <div style="float: left; width: 296px" id="box_edit">
                <fieldset>
                    <legend>Editar Posição da Imagem</legend>
                    <div style="float: left;">
                        <br>
                        <input type="button" value="Esquerda" id="esquerda" />
                    </div>
                    <div style="float: left;">
                        <input type="button" value="Subir" id="subir" />
                        <br><br>
                        <input type="button" value="Descer" id="descer" />
                    </div>
                    <div style="float: left;">
                        <br>
                        <input type="button" value="Direita" id="direita" />
                    </div>
                </fieldset>
                <p style="text-transform: none;font-style: italic;">OBS: Para editar a posição dos textos selecione o texto desejado e arraste com o mouse.</p>
                <p style="text-transform: none;"><label><input type="checkbox" id="ck_ajustar_fundo"  onclick="print_ajustar_fundo()" />Imprimir imagem de fundo para ajuste</label></p>
                <input type="button" value="Salvar estilo" id="salvar" style="float: left; margin-top: 15px;" />
             </div>
        </div>
        </div>
    </body>
</html>