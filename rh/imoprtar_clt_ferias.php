<body>
<div id="container">
<div id="form">
  
<?php
//$deleterecords = "TRUNCATE TABLE nome-da-tabela"; //Esvaziar a tabela
//mysql_query($deleterecords);
  
//Transferir o arquivo
set_time_limit(0);
include('../conn.php');
if (isset($_POST['submit'])) { 
  
    if (is_uploaded_file($_FILES['filename']['tmp_name'])) {
        echo "<h1>" . "File ". $_FILES['filename']['name'] ." transferido com sucesso ." . "</h1>";
        echo "<h2>Exibindo o conteúdo:</h2>";
       // readfile($_FILES['filename']['tmp_name']);
    }
  
    //Importar o arquivo transferido para o banco de dados
    $handle = fopen($_FILES['filename']['tmp_name'], "r");
    $i = 0;
    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
       /*
        
        echo "INSERT into rh_ferias(id_clt,nome,regiao,projeto,mes,ano,data_aquisitivo_ini,data_aquisitivo_fim,data_ini,data_fim,data_retorno,data_aviso,dias_ferias) VALUES";
        echo "(".$data[0].",".$data[1].",".$data[2].",".$data[3].",".$data[4].",".$data[5].",".$data[6].",".$data[7];
        echo "(".$data[8].",".$data[9].",".$data[10].",".$data[11].",".$data[12].",".$data[13].")";
                ///.",".$data[14].",".$data[7];
        
        var_dump($data[0]);
       */
        //$str = str_replace($data,";",",");
         //$str  = explode($data,";");
         //$data[0][1];
        
//        print_r($data);
//        die();
        if($i != 0){
            
         $q =  "SELECT id_clt,nome,data_entrada,id_regiao,id_projeto, FLOOR(DATEDIFF(CURDATE(),data_entrada)/365) as diferenca FROM rh_clt WHERE nome like '%".$data[1]."%'";
//          echo "<br>"; 
          $res = mysql_query($q);
//           $a = mysql_fetch_assoc($res);
           $id_clt = mysql_num_rows($res);
//           $id_clt = $a['id_clt'];
//           $data = date('Y-m-d');
           if($id_clt > 0){
               while($exibe = mysql_fetch_assoc($res)){
                for($i = 0; $i < $exibe['diferenca']; $i++){
                $que = "SELECT DATE_ADD('{$exibe['data_entrada']}',INTERVAL $i YEAR) as Aquisitivo_ini, DATE_ADD(DATE_ADD('{$exibe['data_entrada']}',INTERVAL $i+1 YEAR), INTERVAL -1 DAY) as Aquisitivo_fim";
                $que_q = mysql_query($que);
                $exibe_q = mysql_fetch_assoc($que_q);
                
                $sel_ferias = "SELECT * FROM rh_ferias WHERE id_clt = {$exibe['id_clt']} AND data_aquisitivo_ini = '{$exibe_q['Aquisitivo_ini']}' AND data_aquisitivo_fim = '{$exibe_q['Aquisitivo_fim']}'";
                $sel_feriasq = mysql_query($sel_ferias);
                $exibe_ferias_q = mysql_num_rows($sel_feriasq);
                    
                if($exibe_ferias_q == 0){
                    echo "INSERT INTO rh_ferias (nome, regiao, projeto, data_aquisitivo_ini, data_aquisitivo_fim) VALUES (".$exibe['nome'].','. $exibe['id_regiao'].','. $exibe['id_projeto'].','.$exibe_q['Aquisitivo_ini'].','.$exibe_q['Aquisitivo_fim'].')';
                    echo "<br>";                    
                }
                 }
              }
//             echo "INSERT INTO rh_ferias (`id_clt`, `nome`, `regiao`, `projeto`, `mes`, `ano`, `data_aquisitivo_ini`, `data_aquisitivo_fim`, `data_ini`, `data_fim`, `data_retorno`,`data_aviso`,`dias_ferias`,`status`) 
//                    VALUES (".$id_clt.",'$data[1]','$data[2]','$data[3]','$data[4]','$data[5]','$data[6]','$data[7]','$data[8]','$data[9]','$data[10]','$data[11]','$data[12]','1')";
            echo "<br>";
           }else{
//               echo "<h4 style='color:red'>".$data[1]."</h4>";
           }
           
            //die()  ;
           
          
        }$i++;
        
          
    }
     echo ";";
    fclose($handle);
//  
//    print "Importação feita.";
  
//Visualizar formulário de transferência
} else {
  
    print "Transferir novos arquivos CSV selecionando o arquivo e clicando no botão Upload<br />\n";
  
    print "<form enctype='multipart/form-data' action='#' method='post'>";
  
    print "Nome do arquivo para importar:<br />\n";
  
    print "<input size='50' type='file' name='filename'><br />\n";
  
    print "<input type='submit' name='submit' value='Upload'></form>";
  
}
  
?>
  
</div>
</div>
</body>