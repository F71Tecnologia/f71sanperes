<?php
include('conn.php');


/*
$clts =array(4747);
$total_lancamento1 = 0;
$total_mes = 0;
$meses = 3;
$id_mov_lanc_2 = array();

foreach($clts as  $clt){

//PESQUISANDO MOVIMENTOS NA FOLHA
       $array_movimentos_id = array();
       $qr_folha_mov = mysql_query("SELECT b.id_clt, b.nome,b.id_folha, b.ids_movimentos, a.status from rh_folha as a
                           INNER JOIN rh_folha_proc as b
                           ON a.id_folha = b.id_folha
                           WHERE  a.`status` = 3  
                           AND a.ano = '2012'
                           AND b.id_clt = '$clt'");
       while($row_folha_mov = mysql_fetch_assoc($qr_folha_mov)):            

           $array_movimentos_id[] = $row_folha_mov['ids_movimentos'];           

       endwhile;
       
$movimentos_id = implode(',',$array_movimentos_id);     
$n_folhas      = mysql_num_rows($qr_folha_mov);
///////////////////////////


 

 
$qr_movimento = mysql_query("SELECT *
FROM `rh_movimentos_clt`                                            
WHERE id_clt = '$clt'  AND (incidencia !='' AND incidencia !=',,')
AND id_mov NOT IN(14,200,56)
AND lancamento = 2 
AND id_movimento IN($movimentos_id)
ORDER BY id_mov") or die(mysql_error());
 
 if(mysql_num_rows($qr_movimento) != 0){
     
          
            while($row_mov = mysql_fetch_assoc($qr_movimento)):
                    
                echo $row_mov['id_mov'];
                $id_mov_lanc_2[] = $row_mov['id_mov'];////Armazena o id do o movimento pra não repetir na próxima query
            
               $qr_movimento2 = mysql_query("SELECT * FROM `rh_movimentos_clt`    WHERE id_clt = '$clt' 
                                                AND id_mov = '$row_mov[id_mov]'
                                                AND lancamento = 1
                                                AND id_movimento in($movimentos_id)");       
               while($row_mov_lanc1 = mysql_fetch_assoc($qr_movimento2)):               
                        $total_lancamento1 += (int)$row_mov_lanc1['valor_movimento'];
                        $total_mes++;
              endwhile;
                
              $meses_totais_mov            = $n_folhas - $total_mes;
              $valor_total_lancamento2 = $row_mov['valor_movimento'] * $meses_totais_mov;
              $valor_total_lancamento1 = (float)$total_lancamento1;
              
              $RESULTADO_MOV_DECIMO = ((($valor_total_lancamento1+ $valor_total_lancamento2)/$n_folhas) / 12) * $meses;
         
              
                $SQL[] =  "('$clt', '$regiao', '$projeto','$row_mov[id_mov]', '$mes_dt', '$ano','$row_mov[cod_movimento]', '$row_mov[tipo_movimento]', '$row_mov[nome_movimento]', NOW(), '$_COOKIE[logado]', '$RESULTADO_MOV_DECIMO', 1, '$row_mov[incidencia]',1,1,1)";

              endwhile;
          ///555

            
 }
 

        $qr_mov_proporcional = mysql_query("SELECT id_movimento, id_mov, nome_movimento,cod_movimento,tipo_movimento, nome_movimento,incidencia,  ((SUM(valor_movimento) /  COUNT(id_mov) )  / 12) * $meses as total, 
                                            COUNT(id_mov) as total_movimentos
                                            FROM `rh_movimentos_clt`                                             
                                            WHERE id_clt = '$clt'  AND (incidencia !='' AND incidencia !=',,')
                                            AND id_mov NOT IN(14,200,56)
                                            AND lancamento = 1
                                            AND id_movimento IN($movimentos_id)
                                             GROUP BY id_mov    ") or die(mysql_error());
        
        if(mysql_num_rows($qr_mov_proporcional) != 0) {
            
       
           while ($row_mov_prop = mysql_fetch_assoc($qr_mov_proporcional)):
              
                    if(!in_array($row_mov_prop['id_mov'], $id_mov_lanc_2)) {

                            if(!empty($row_mov_prop['total_movimentos']) ){

                            //////VERIFICANDO
                            $verifica_mov_prop = mysql_num_rows(mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_clt ='$clt' AND mes_mov = $mes_dt AND ano_mov = '$ano' AND id_mov = '$row_mov_prop[id_mov]' AND status = 1"));
                             if($verifica_mov_prop == 0) {
                                        $SQL[] =  "('$clt', '$regiao', '$projeto','$row_mov_prop[id_mov]', '$mes_dt', '$ano','$row_mov_prop[cod_movimento]', '$row_mov_prop[tipo_movimento]', '$row_mov_prop[nome_movimento]', NOW(), '$_COOKIE[logado]', '$row_mov_prop[total]', 1, '$row_mov_prop[incidencia]',1,1,1)";

                                } 
                            }
                    }
            endwhile;
        }

      
          unset($total_lancamento1, $total_mes,$id_mov_lanc_2,  $total_lancamento2,$total_mes);  

 
           
   ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////     
   ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////     
   ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////     
   ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////     
   ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////     
      
        /////CASO ESPECIAL PARA INSALUBRIDADE
//PEGANDO OS MOVIMENTOS DO TIPO "SEMPRE"
        $qr_movimento = mysql_query("SELECT *FROM `rh_movimentos_clt`                                            
                                    WHERE id_clt = '$clt'  AND (incidencia !='' AND incidencia !=',,')
                                    AND id_mov  IN(56)
                                    AND lancamento = 2 
                                    AND id_movimento IN($movimentos_id)
                                    ORDER BY id_mov") or die(mysql_error());

         if(mysql_num_rows($qr_movimento) != 0){
             
                    while($row_mov = mysql_fetch_assoc($qr_movimento)):

                      $verifica_mov_prop = mysql_num_rows(mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_clt ='$clt' AND mes_mov = $mes_dt AND ano_mov = '$ano' AND id_mov = '$row_mov[id_mov]' AND status = 1"));
                      if($verifica_mov_prop == 0) {   


                     $id_mov_lanc_2[] = $row_mov['id_mov'];////Armazena o id do o movimento pra não repetir na próxima query

                       $qr_movimento2 = mysql_query("SELECT * FROM `rh_movimentos_clt`    WHERE id_clt = '$clt' 
                                                        AND id_mov IN(200,56)
                                                        AND lancamento = 1
                                                        AND id_movimento in($movimentos_id)");       
                       while($row_mov_lanc1 = mysql_fetch_assoc($qr_movimento2)):               
                                $total_lancamento1 += $row_mov_lanc1['valor_movimento'];
                                $total_mes++;
                      endwhile;

                      $meses_totais_mov            = $n_folhas - $total_mes;
                      $valor_total_lancamento2 = $row_mov['valor_movimento'] * $meses_totais_mov;                    
                      $valor_total_lancamento1 = (float)$total_lancamento1;   
                      $RESULTADO_MOV_DECIMO = ((($valor_total_lancamento1+ $valor_total_lancamento2)/$n_folhas) / 12) * $meses;                      
                    
                        $SQL[] =  "('$clt', '$regiao', '$projeto','$row_mov[id_mov]', '$mes_dt', '$ano','$row_mov[cod_movimento]', '$row_mov[tipo_movimento]', '$row_mov[nome_movimento]', NOW(), '$_COOKIE[logado]', '$RESULTADO_MOV_DECIMO', 1, '$row_mov[incidencia]',1,1,1)";
                      }
                      endwhile;  

         }

  
             
///PEGANDO OS MOVIMENTO MENSAIS
                $qr_mov_proporcional = mysql_query("SELECT id_movimento, id_mov, nome_movimento,cod_movimento,tipo_movimento, nome_movimento,incidencia,  ((SUM(valor_movimento) /  COUNT(id_mov) )  / 12) * $meses as total, 
                                                    COUNT(id_mov) as total_movimentos
                                                    FROM `rh_movimentos_clt`                                             
                                                    WHERE id_clt = '$clt'  AND (incidencia !='' AND incidencia !=',,')
                                                    AND id_mov  IN(56)
                                                    AND lancamento = 1
                                                    AND id_movimento IN($movimentos_id)
                                                     GROUP BY id_mov    ") or die(mysql_error());

                if(mysql_num_rows($qr_mov_proporcional) != 0) {
                   while ($row_mov_prop = mysql_fetch_assoc($qr_mov_proporcional)):

                            if(!in_array($row_mov_prop['id_mov'], $id_mov_lanc_2)) {

                                    if(!empty($row_mov_prop['total_movimentos']) ){


                                    $verifica_mov_prop = mysql_num_rows(mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_clt ='$clt' AND mes_mov = $mes_dt AND ano_mov = '$ano' AND id_mov = '$row_mov_prop[id_mov]' AND status = 1"));
                                     if($verifica_mov_prop == 0) {

                                                $SQL[] =  "('$clt', '$regiao', '$projeto','$row_mov_prop[id_mov]', '$mes_dt', '$ano','$row_mov_prop[cod_movimento]', '$row_mov_prop[tipo_movimento]', '$row_mov_prop[nome_movimento]', NOW(), '$_COOKIE[logado]', '$row_mov_prop[total]', 1, '$row_mov_prop[incidencia]',1,1,1)";

                                        } 
                                    }
                            }
                    endwhile;
                }
      
                    print_r($SQL).'<br>';

 unset($total_lancamento1, $total_mes,$id_mov_lanc_2,$SQL,  $total_lancamento2,$total_mes);  
        


}$
 


/*
$sql = mysql_query("SELECT * 
FROM saida
WHERE valor LIKE  '%.%' 
ORDER BY  `saida`.`id_saida` DESC
") or die(mysql_error());

while($row_saida = mysql_fetch_assoc($sql)){
 
  
    
   $sem_formato = str_replace(',','', str_replace('.','',$row_saida['valor']));  
   $depois_virgula =  ','.substr($sem_formato, -2); 
   $formato_banco = substr($sem_formato, 0,(strlen($sem_formato)-2)).$depois_virgula;  
   
   echo $row_saida['id_saida'].' - ';
     echo $row_saida['valor'].' => ';
   echo $formato_banco.' ';
   
   $sql2 = "UPDATE saida SET valor = '$formato_banco' WHERE id_saida = '$row_saida[id_saida]' LIMIT 1 ";
   
   
   echo $sql2;
   echo '<br>';
   mysql_query($sql2)or die(mysql_error());
   
}
 * 
 * */

















////////////////////////////////////////////////////////////////
//////////////////DUPLICAÇÃO DE CURSOS E HORÁRIOS////////////////
///////////////////////////////////////////////////////////////

//3316,3317,3318,3319,3320

/*
$projeto_origem = 3302;
$projetos_destino = array(3303);




foreach($projetos_destino as $id_projeto){ 
    
    
$qr_curso =  mysql_query("SELECT * 
FROM curso WHERE id_regiao = 45 AND campo3 = '$projeto_origem';");

while($row_curso = mysql_fetch_assoc($qr_curso)):
    
  $qr_curso2 = mysql_query("SELECT * FROM curso WHERE campo3 = '$id_projeto' AND nome like '%$row_curso[nome]%'");
  $curso2    = mysql_fetch_assoc($q_curso);
  if(mysql_num_rows($qr_curso2) == 0){
      
     
  
    
    mysql_query("INSERT INTO `curso` ( `nome`, `area`, `id_regiao`, `local`, `inicio`, `termino`, `descricao`, `valor`, `parcelas`, `campo1`, `campo2`, `campo3`, `cbo_nome`, `cbo_codigo`, `id_horario`, `salario`, `ir`, `mes_abono`, `id_user`, `data_cad`, `tipo`, `hora_semana`, `hora_mes`, `quota`, `num_quota`, `data_alter`, `user_alter`, `status`, `status_reg`, `qnt_maxima`) 
        VALUES 
        ('$row_curso[nome]', '', 45, '$row_curso[local]', '$row_curso[inicio]', '$row_curso[termino]', '$row_curso[descricao]', '$row_curso[valor]', '$row_curso[parcelas]', '$row_curso[campo1]', '$row_curso[campo2]', '$id_projeto', '$row_curso[cbo_nome]', '$row_curso[cbo_codigo]', '$row_curso[id_horario]', '$row_curso[salario]', '$row_curso[ir]', '$row_curso[mes_abono]', '$row_curso[id_user]',  '$row_curso[data_cad]',  '$row_curso[tipo]', '$row_curso[hora_semana]', '$row_curso[hora_mes]', '$row_curso[quota]', '$row_curso[num_quota]', '$row_curso[data_alter]', '$row_curso[user_alter]', '$row_curso[status]', '$row_curso[status_reg]', '$row_curso[qnt_maxima]');") or die(mysql_error());
        
   
    
   $id_curso = mysql_insert_id();
   $qr_horario = mysql_query("SELECT * FROM  rh_horarios WHERE funcao = '$row_curso[id_curso]'");
   while($horario = mysql_fetch_assoc($qr_horario)):
      
       mysql_query("INSERT INTO `rh_horarios` 
                    ( `id_regiao`, `nome`, `obs`, `entrada_1`, `saida_1`, `entrada_2`, `saida_2`, `dias_semana`, `horas_mes`, `horas_semanais`, `salario`, `funcao`, `valor_dia`, `valor_hora`, `folga`, `status_reg`, `dias_mes`)
            VALUES 
            ( 45, '$horario[nome]', '$horario[obs]', '$horario[entrada_1]', '$horario[saida_1]', '$horario[entrada_2]', '$horario[saida_2]', '$horario[dias_semana]', '$horario[horas_mes]', $horario[horas_semanais], '$horario[salario]', '$id_curso', '$horario[valor_dia]', '$horario[valor_hora]', $horario[folga], $horario[status_reg], $horario[dias_mes]);");

       
   endwhile;
 
  }
    
endwhile;
} 
echo 'FIM DA DUPLICAÇÃO DE CURSOS E HORÁRIOS!';
*/





/////COMPROVANTES SAÍDA TIPO TARIFAS
/*
$MES     = 11;
$REGIAO  = 45;
$PROJETO = 3302;

$qr_saida = mysql_query("SELECT saida.id_saida FROM saida 
                         WHERE id_saida IN(43947,
43948,
43949,
43950,
43951,
43952,
43953,
43954,
43955,
43956,
43957,
43958,
43959,
43960,
43961,
43962,
43963,
43964,
43965,
43967,
43969,
43970,
43971,
43973,
43974,
43975,
43976,
43977,
43979,
43980,
43983,
43984,
43985,
43987,
43988,
43989,
43990,
43991,
43992,
43995,
44011,
44023,
44024,
44025,
44026,
44027,
44028,
44029,
44030,
44031,
44032,
44033,
44034,
44035,
44036,
44037,
44038,
44039,
44040,
44041,
44044,
44045,
44047,
44048,
44049,
44050,
44051,
44052,
44053,
44054,
44055,
44056,
44057,
44058,
44059,
44060,
44062,
44063,
44064,
44065,
44066,
44067,
44068,
44069,
44070,
44071,
44072,
44073,
44074,
44075,
44076,
44077,
44078,
44079,
44081,
44082,
44083,
44084,
44085,
44086,
44087,
44090,
44093,
44094,
44096,
44142,
44218,
44219,
44220,
44221,
44222,
44223,
44224,
44226,
44227,
44228,
44229,
44230,
44231,
44233,
44234,
44235,
44236,
44237,
44238,
44241,
44250,
44253,
44256,
44257,
44259,
44260,
44262,
44263,
44264,
44265,
44266,
44267,
44268,
44269,
43946,
44270,
44271,
44272,
44273,
44274,
44275,
44276,
44277,
44278,
44279,
44280,
44282,
44283,
44284,
44285,
44286,
44288,
44289,
44292,
44293,
44297,
44298,
44299,
44300,
44301,
44302,
44303,
44304,
44305,
44306,
44307,
44308,
44309,
44310,
44311,
44377,
44378,
44379,
44380,
44383,
44393,
44394,
44395,
44594,
44612,
44614,
44615,
44660,
44661,
44662)");
while($saida = mysql_fetch_assoc($qr_saida)):
   
    
    $verifica_comprovante = mysql_num_rows(mysql_query("SELECT * FROM saida_files WHERE id_saida = '$saida[id_saida]'"));
    if($verifica_comprovante == 0){    
            $insert = mysql_query("INSERT INTO `saida_files` ( id_saida, tipo_saida_file) VALUES  ( '$saida[id_saida]', '.pdf');") or die(mysql_error());

            if($insert){
               $id_pg = mysql_insert_id(); 
                if(copy('anexo_saida.pdf', 'comprovantes/'.$id_pg.'.'.$saida['id_saida'].'.pdf')){  echo $saida['id_saida'].'OK';
                                                                                                     } else {
                                                                                                        echo $saida['id_saida'].'ERRO';
                                                                                                     }
             }
 
    } else {
        echo 'não alterado';
    } 
endwhile;


/*
$qr_saida = mysql_query("SELECT * FROM saida as A
INNER JOIN saida_files as B
ON A.id_saida = B.id_saida
where A.id_saida = 44562;");
while($saida = mysql_fetch_assoc($qr_saida)):

if(file_exists("comprovantes/$saida[id_saida_file].$saida[id_saida]$saida[tipo_saida_file]")){
    echo 'existe => ';
    echo'<a href="comprovantes/'.$saida[id_saida_file].'.'.$saida[id_saida].$saida[tipo_saida_file].'" > '.$saida[id_saida_file].'.'.$saida[id_saida].$saida[tipo_saida_file].'</a>';
echo'<br>';
    
} else {
    echo 'não existe<br>';
}

endwhile;
 * */
/*
$dt_ini = '2012-08-10';
$dt_fim = '2013-03-15';

list($ano_inicio,$mes_inicio,$dia_inicio) = explode('-',$dt_ini);
list($ano_final,$mes_final,$dia_final)    = explode('-',$dt_fim);    

$dt_inicio_seg     = mktime(0,0,0,$mes_inicio,$dia_inicio, $ano_inicio );
$dt_fim_seg        = mktime(0,0,0,$mes_final,$dia_final, $ano_final );   
$diferenca_meses   =  ($dt_fim_seg - $dt_inicio_seg)/2592000; 

for($i=0;$i<$diferenca_meses;$i++){   
    
   if($i == 0){
    $data_1         =  mktime(0,0,0,($mes_inicio + $i),$dia_inicio, $ano_inicio );
   } else {
    $data_1         =  mktime(0,0,0,($mes_inicio + $i),1, $ano_inicio );       
   }    
    
    $ultimo_dia_mes = cal_days_in_month(CAL_GREGORIAN, date('m',$data_1), date('y',$data_1));  
    $data_2         =  mktime(0,0,0,($mes_inicio + $i), $ultimo_dia_mes, $ano_inicio );
    
    if($data_2 >= $dt_fim_seg){   $data_2 = $dt_fim_seg;  }
    
    $dias_trab = ($data_2 - $data_1)/86400;
    
    if($dias_trab >=14){
        $total_meses +=1;          
     }   
  
    //debug periodos
   // echo date('d/m/Y',$data_1).' - '.date('d/m/Y',$data_2).' = '.round($dias_trab).' dias.'.'<br>';
    
}


echo $total_meses.'/12 avos';

*/

?>

<!-------- RELATÓTIOS DE CTS QUE TRABALHAM EM MAIS DE UMA UNIDADE 
-->
<!--
<table width="700" border="1">
    <tr>
        <td>Nome</td>
         <td>CPF</td> 
        <td>UPA</td>      
           
    </tr>

<?php
/*
$qr_clt = mysql_query("SELECT A.nome, A.id_projeto, COUNT(A.pis), A.pis, GROUP_CONCAT(id_clt) as id_clts
FROM rh_clt as A 
INNER JOIN projeto as B
ON A.id_projeto = B.id_projeto
WHERE A.id_projeto IN(3302, 3303, 3304, 3315,3316,3317, 3318,3319, 3320 )  AND A.status_reg = 1
GROUP BY A.nome
HAVING  COUNT(pis) > 1;") or die(mysql_error());
while($row_clt = mysql_fetch_assoc($qr_clt)){

        $qr_clt2 = mysql_query("select A.nome,  GROUP_CONCAT('<br><br>',B.nome ) as UPA, COUNT(A.pis),A.cpf
            from rh_clt as A
        INNER JOIN projeto as B
        ON A.id_projeto = B.id_projeto
        WHERE id_clt IN($row_clt[id_clts]) AND A.id_regiao = 45 AND B.id_regiao = 45;")or die(mysql_error()); 
        
     
        while($row_clt2 = mysql_fetch_assoc($qr_clt2)){
    
?>    
  <tr>
      <td><?php echo $row_clt2['nome'];?></td>    
      <td width="150" align="center"><?php echo $row_clt2['cpf'];?></td>
        <td><?php echo $row_clt2['UPA'];?></td>
    
  </tr>
    
     
    
  <?php   }
}
*/
?>
</table>
-->


<?php
/* LANÇAR MOVIMENTO SEMPRE PARA UM DETERMINIdo CURSO*/
/*
$ids_curso        = "1310";
$valor_ad_noturno = '142.37';
$valor_dsr        =  '35.59'; 

$qr_clt = mysql_query("SELECT A.id_clt, A.nome, B.nome as curso , A.locacao, A.id_regiao, A.id_projeto
    FROM  rh_clt as A 
INNER JOIN curso As B
ON B.id_curso = A.id_curso
WHERE A.id_curso IN($ids_curso) ; ");
while($row_clt = mysql_fetch_assoc($qr_clt)){
    
    
    
$verifica_mov = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_mov IN(199,66) AND lancamento = 2 AND id_clt = $row_clt[id_clt]") or die(mysqL_error());
while($verificacao = mysql_fetch_assoc($verifica_mov)){
    
    
    $dsr = ($verificacao['id_mov'] == 199 )? 1 : 0;
    $adicional = ($verificacao['id_mov'] == 66 )? 1 : 0;
    
    
}


  echo $row_clt['nome'].' => Adicional= '.$adicional.', DSR = '.$dsr;    
  echo '<br>';  
  
if(mysql_num_rows($verifica_mov) == 0){
    
   /* 
   mysql_query("INSERT INTO `rh_movimentos_clt` 
      (`id_clt`, `id_regiao`, `id_projeto`, `id_folha`, `mes_mov`, `ano_mov`, `id_mov`, `cod_movimento`, `tipo_movimento`, `nome_movimento`, `data_movimento`, `user_cad`, `valor_movimento`, `percent_movimento`, `lancamento`, `incidencia`, `qnt`, `dt`, `status`, `status_folha`, `status_ferias`, `status_reg`) 
      VALUES 
      ( '$row_clt[id_clt]', $row_clt[id_regiao],  $row_clt[id_projeto], 0, 3, '2013', 66, '9000', 'CREDITO', 'ADICIONAL NOTURNO', NOW(), '87', '$valor_ad_noturno', '', '2', '5020,5021,5023', NULL, 0, 1, 0, 1, 1),
      ( '$row_clt[id_clt]', $row_clt[id_regiao],  $row_clt[id_projeto], 0, 3, '2013', 199, '9997', 'CREDITO', 'DSR',NOW(), '87', '$valor_dsr', '', '2', '5020,5021,5023', NULL, 0, 1, 0, 1, 1);

 ") or die("erro ao adicionar o movimento");
    */
   /* 
    
  echo (" <pre>INSERT INTO `rh_movimentos_clt` 
      (`id_clt`, `id_regiao`, `id_projeto`, `id_folha`, `mes_mov`, `ano_mov`, `id_mov`, `cod_movimento`, `tipo_movimento`, `nome_movimento`, `data_movimento`, `user_cad`, `valor_movimento`, `percent_movimento`, `lancamento`, `incidencia`, `qnt`, `dt`, `status`, `status_folha`, `status_ferias`, `status_reg`) 
      VALUES 
      ( '$row_clt[id_clt]', $row_clt[id_regiao],  $row_clt[id_projeto], 0, 3, '2013', 66, '9000', 'CREDITO', 'ADICIONAL NOTURNO', NOW(), '87', '$valor_ad_noturno', '', '2', '5020,5021,5023', NULL, 0, 1, 0, 1, 1),
      ( '$row_clt[id_clt]', $row_clt[id_regiao],  $row_clt[id_projeto], 0, 3, '2013', 199, '9997', 'CREDITO', 'DSR',NOW(), '87', '$valor_dsr', '', '2', '5020,5021,5023', NULL, 0, 1, 0, 1, 1);
 </pre>") ;
   echo '<br>'; 
    
   
   
    
}
    
    

    
    
    
}
*/
/*
echo '<pre>';

foreach (glob("comprovantes/*53533*") as $arquivo) {    
    echo "tamanho de <a href='".$arquivo."'>".$arquivo."</a>". filesize($arquivo) . "<br>";
}

echo '</pre>';




$servidor = "{mail.sorrindo.org:110/pop3/novalidate-cert}";
$mbox = imap_open($servidor,  'elizabeth@sorrindo.org','beth2525')
      or die("can't connect: " . imap_last_error());

$list = imap_list($mbox,$servidor,'*'); 

echo '<pre>';
print_r($list);
echo '</pre>';
*/


/*
 include("funcoes/extenso.php");
 require("rh/fpdf/fpdf.php");
define('FPDF_FONTPATH','rh/fpdf/font/');

$qr_rpa = mysql_query("select A.id_rpa, A.id_saida, B.id_saida_file, C.id_autonomo, D.id_regiao, D.id_projeto,D.nome from rpa_saida_assoc as A
INNER JOIN saida_files as B
ON A.id_saida = B.id_saida
INNER JOIN rpa_autonomo as C
ON C.id_rpa = A.id_rpa
INNER JOIN autonomo as D
ON D.id_autonomo = C.id_autonomo
");
while($row = mysql_fetch_assoc($qr_rpa)){
    
    



$id_regiao   = $row['id_regiao'];
$id_projeto  = $row['id_projeto'];
$id_autonomo = $row['id_autonomo'];
$id_rpa      =  $row['id_rpa'];
$ano         = date('Y');

$link1 = 'autonomo/arquivo_rpa_pdf/'.$row['id_rpa'].'_'.$id_autonomo.'.pdf';
$link2 = 'comprovantes/'.$row['id_saida_file'].'.'.$row['id_saida'].'.pdf';


if(copy($link1,$link2)){
    
    $file = 'ok';
} else {
    $file = 'erro';
}


echo '<a href="'.$link2.'">'.$row['nome'].'--'.$file.'</a>';
echo '<br>';



}
*/
/*

$id_clt = 4747;
$data_hoje = date('Y-m-d');

$qr_folha = mysql_query("SELECT B.*, A.ids_movimentos_estatisticas FROM rh_folha as A
INNER JOIN rh_folha_proc as B
ON A.id_folha = B.id_folha
WHERE B.id_clt = '$id_clt' 
AND data_inicio BETWEEN (DATE_ADD('$data_hoje', INTERVAL - 7 MONTH)) AND '$data_hoje' AND A.terceiro != 1");

while($row_folha = mysql_fetch_assoc($qr_folha)){    

        $qr_movimentos = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_movimento IN($row_folha[ids_movimentos_estatisticas]) AND id_clt = '$id_clt' and tipo_movimento = 'CREDITO' AND cod_movimento NOT IN(5012)");
        while($row_mov = mysql_fetch_assoc($qr_movimentos)){
            
            $movimento[$row_mov['cod_movimento']][$row_folha['mes']] = $row_mov['valor_movimento']; 

        }     
}


foreach($movimento as $chave => $cod){
    
   
    $media_mov    = (array_sum($cod)/sizeof($cod));
    $total_media += $media_mov;
    
}
echo $total_media;
 * *
 */
?>
<!---<table border="1">
    <tr>
        <td></td>
        <td></td>
        <td colspan="5">FOLHA</td>
         <td>-------</td>
        <td colspan="5">RESCISÃO</td>
    </tr>
    <tr>
        <td>ID_CLT</td>
        <td>NOME</td>
        <td> VALOR_RESCISAO</td>
        <td> VALOR PAGO NA RESCISAO</td>
        <td> INSS RESCISÃO</td>
        <td> IRRF RESCISÃO</td>
        <td> FGTS RESCISÃO</td>
        <td>------</td>
        <td align="center">( total_rendimentos - saldo salário ) = <br> VALOR_RESCISAO</td>
        <td align="center">( previdencia_ss - previdencia_dt ) = <br> INSS_RESCISÃO</td>
        <td align="center">( ir_ss - ir_dt ) = <br> IRRF_RESCISÃO</td>
        <td align="center">( total_dedução + total_líquido - INSS_RESCISÃO - IRRF_RESCISÃO ) = <br> VALOR_PAGO_RESCISAO</td>
        
        
    </tr>
--->

<?php

$tipo=2;
$array_tipos = array(1 => 'VALOR RESCISÃO', 2 => 'VALOR PAGO NA RESCISÃO', 3 => 'INSS RESCISÃO', 4 => 'IRRF RESCISÃO');

echo '<h3>'.$array_tipos[$tipo].'</h3>';
echo '<table border="1">';

//cabeçalhos
switch($tipo){
    
    case 1: echo '<tr>
                    <td>ID_CLT</td>
                    <td>NOME</td>
                    <td>TOTAL DE RENDIMENTOS(RESCISÃO)</td>
                    <td>SALDO DE SALÁRIO(RESCISÃO)</td>
                    <td>VALOR RESCISÃO</td>    
                </tr>';
        break;
    case 2: echo '<tr>
                    <td>ID_CLT</td>
                    <td>NOME</td>
                    <td>TOTAL DE DEDUÇÃO(RESCISÃO)</td>
                    <td>TOTAL LÍQUIDO(RESCISÃO)</td>
                    <td>INSS(RESCISÃO)</td>
                    <td>INSS 13º(RESCISÃO)</td>
                    <td>IRRF(RESCISÃO)</td>
                    <td>IRRF 13º (RESCISÃO)</td>    
                    <td>VALOR PAGO NA RESCISÃO</td>    
                </tr>';
        break;
    
    case 3:
            echo '<tr>
                    <td>ID_CLT</td>
                    <td>NOME</td>
                    <td>INSS </td>
                    <td>INSS 13º</td>
                </tr>';
        break;
    case 4:
            echo '<tr>
                    <td>ID_CLT</td>
                    <td>NOME</td>
                    <td>IRRF </td>
                    <td>IRRF 13º</td>
                </tr>';
        break;
    
    
}



$qr_folha = mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = 1265 AND status_clt != 10");
while($folha = mysql_fetch_assoc($qr_folha)){
    
   $qr_recisao = mysql_query("SELECT * FROM rh_recisao WHERE id_clt = '$folha[id_clt]' ");
   $rescisao = mysql_fetch_assoc($qr_recisao);
   
   
   $valor_rescisao      = $rescisao['total_rendimento'] - $rescisao['saldo_salario'];
   $inss_total          = $rescisao['previdencia_ss'] + $rescisao['previdencia_dt'];
   $ir_total            = $rescisao['ir_ss'] + $rescisao['ir_dt'];
   $valor_pago_rescisao = ($rescisao['total_deducao'] + $rescisao['total_liquido']) - $inss_total - $ir_total ;
   
   
   ///TOTALIZADORES
   $totalizador_total_rendimentos   += $rescisao['total_rendimento'];
   $totalizador_total_deducao       += $rescisao['total_deducao'];
   $totalizador_total_liquido       += $rescisao['total_liquido'];
   $totalizador_saldo_salario       += $rescisao['saldo_salario'];
   $totalizador_inss                += $rescisao['previdencia_ss'];
   $totalizador_inss_dt             += $rescisao['previdencia_dt'];
   $totalizador_total_inss          += $inss_total;
   $totalizador_ir                  += $rescisao['ir_ss'];
   $totalizador_ir_dt               += $rescisao['ir_dt'];
    $totalizador_total_ir           += $ir_total;
   $totalizador_valor_rescisao      += $valor_rescisao;
   $totalizador_valor_pago_rescisao += $valor_pago_rescisao;
   
   switch($tipo){
       
       case 1:
                echo '<tr>
                            <td>'.$folha['id_clt'].'</td>
                            <td>'.$folha['nome'].'</td>
                            <td>'.$rescisao['total_rendimento'].'</td>
                            <td>'.$rescisao['saldo_salario'].'</td>
                            <td>'.$valor_rescisao.'</td>
                      </tr>';           
           break;    
       case 2:
           echo '<tr>
                            <td>'.$folha['id_clt'].'</td>
                            <td>'.$folha['nome'].'</td>
                            <td>'.$rescisao['total_deducao'].'</td>
                            <td>'.$rescisao['total_liquido'].'</td>
                            <td>'.$rescisao['previdencia_ss'].'</td>
                            <td>'.$rescisao['previdencia_dt'].'</td>
                            <td>'.$rescisao['ir_ss'].'</td>
                            <td>'.$rescisao['ir_dt'].'</td>
                            <td>'.$valor_pago_rescisao.'</td>                         
                      </tr>';  
           
           break;
       
         case 3:
                echo '<tr>
                            <td>'.$folha['id_clt'].'</td>
                            <td>'.$folha['nome'].'</td>
                            <td>'.$rescisao['previdencia_ss'].'</td>
                            <td>'.$rescisao['previdencia_dt'].'</td>
                            <td>'.$inss_total.'</td>
                      </tr>';           
           break;     
   }
     
    
}



///EXIBINDO TOTALIZADORES
  switch($tipo){       
       case 1:
            echo '<tr>
                    <td colspan="2"></td>
                   <td>'.$totalizador_total_rendimentos.'</td>
                   <td>'.$totalizador_saldo_salario.'</td>
                   <td>'.$totalizador_valor_rescisao.'</td>
             </tr>';      
            break;
        
          case 2:
            echo '<tr>
                    <td colspan="2"></td>
                   <td>'.$totalizador_total_deducao.'</td>
                   <td>'.$totalizador_total_liquido.'</td>
                   <td>'.$totalizador_inss.'</td>
                   <td>'.$totalizador_inss_dt.'</td>
                   <td>'.$totalizador_ir.'</td>
                   <td>'.$totalizador_ir_dt.'</td>
                   <td>'.$totalizador_valor_pago_rescisao.'</td>
             </tr>';      
            break;
        
         case 3:
            echo '<tr>
                    <td colspan="2"></td>
                   <td>'.$totalizador_inss.'</td>
                   <td>'.$totalizador_inss_dt.'</td>
                   <td>'.$totalizador_total_inss.'</td>
             </tr>';      
            break;
        
        
  }

echo '</table>';
?>