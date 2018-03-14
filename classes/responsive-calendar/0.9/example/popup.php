<?php

    include("../../../../conn.php");
    include("../../../../wfunction.php");
    include("../../../ProcessoJuridicoClass2.php");
    
    $objetojuridico = new ProcessoJuridicoClass();
    
//    $objetojuridico -> setIdProjeto(1000);
//    echo $objetojuridico -> getIdProjeto();
    $data = $_REQUEST["ano"]."-".sprintf("%02s", $_REQUEST["mes"])."-".sprintf("%02s", $_REQUEST["dia"]);

     $arrayJuridico = $objetojuridico -> getProcessCalendario($data);
//     print_r($arrayJuridico);
    
//    echo $data;
    
   
//  
     
$countA=0;
$tabela_andamento = '';
foreach($arrayJuridico as $key => $value){
    foreach($value as $lista){
         if(!empty($lista['andamento_id'])){
             $link = ($lista['proc_tipo_id'] == 1) ? '/intranet/gestao_juridica/processo_trabalhista/dados_trabalhador/ver_trabalhador.php' : '/intranprocet/gestao_juridica/outros_processos/dados_processo/ver_processo.php';
             $countA++;
             $tabela_andamento .= "<tr>
                        <td class='hidden-sm hidden-xs'>". $lista["proc_numero_processo"]."</td>
                        <td>". utf8_encode($lista["regiao"])."</td>
                        <td>". utf8_encode($lista['proc_nome']) ."</td>
                        <td>". utf8_encode($lista["proc_status_nome"]) ."</td>
                        <td>". date('d/m/Y',strtotime($lista["andamento_data_movi"])) ." ". $lista["andamento_horario"] ."</td>
                        <td><a href=' {$link}?id_processo={$lista['proc_id']}' target='_blank' class='btn btn-success btn-xs'><i class='fa fa-search'></i></a>";
                        if ($lista["andamento_realizado"] == 1){
                            $tabela_andamento .=  " ";
                        }else{
                            $tabela_andamento .= "<a href='#' data-realizado = '{$lista['andamento_id']}' type='button' id='realizado' class='btn btn-warning btn-xs'><i class='fa fa-check-square-o'></i></a>";
                        }
                        $tabela_andamento.="
                        </td>
                    </tr>";
         }
    } 
}

if($countA > 0) { ?>
<div class="col-lg-12">
    <table class="table">
        <h4>PROCESSOS</h4>
        <thead class="thead-inverse">
          <tr>
                <th class="hidden-sm hidden-xs"><?php echo utf8_encode("Nº DO PROCESSO")?></th>
                <th><?php echo utf8_encode("REGIÃO")?></th>
                <th>NOME</th>
                <th>STATUS</th>
                <th>DATA</th>
                <th> </th>
          </tr>
        </thead>

        <tbody>
        <?php echo $tabela_andamento; ?>
        </tbody>
    </table>
</div>
    <?php } else { 
        echo " "; 
    } ?>
  
<!--<hr style=" height: 12px; border: 0; box-shadow: inset 0 12px 12px -12px rgba(0, 0, 0, 0.5);">-->
<?php 
$countB=0;
$tabela_oscip = '';
        foreach($arrayJuridico as $key => $value){
            foreach($value as $lista){
                if(!empty($lista['id_oscip'])){
                    $countB++;
                    $tabela_oscip .= 
                    "<tr>
                        <td>".date('d/m/Y',strtotime($lista['data_publicacao']))."</td>
                        <td>". $lista['numero_periodo']." ".$lista['periodo']."</td>
                        <td>". utf8_encode($lista['descricao'])."</td>
                    </tr>";
                    
                   
                }
            }
        } 
if($countB > 0) {        ?>
<div class="col-lg-12">
    <table class="table">

        <h4><?php echo utf8_encode("OBRIGAÇÕES")?></h4>

        <thead class="thead-inverse">
            <tr>
                <th><?php echo utf8_encode("DATA DE PUBLICAÇÃO")?></th>
                <th>VALIDADE</th>
                <th><?php echo utf8_encode("DESCRIÇÃO")?></th>                                                
            </tr>
        </thead>
        <tbody>
           <?php echo $tabela_oscip; ?> 
        </tbody>
    </table>
</div>
<?php } else { 
        echo " "; 
    } ?>
<script>
    $(document).ready(function(){
        
        $("body").on("click","#realizado",function(){
//            return confirm("Definir como realizado?");
                new BootstrapDialog({
                    nl2br: false,
                    size: BootstrapDialog.SIZE_WIDE,
                    type: 'type-success',
                    title: 'PROCESSOS',
                    message: result,
                    closable: true,
                    buttons:
                    [{
                        label: 'Fechar',
                        action: function (dialog) {
                            dialog.close();
                            //window.location.reload();
                        }
                    }]
                }).open();
            
        });
        
    });           
</script>

