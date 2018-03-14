<?php         

include('../conn.php');


/////LANÇAMENTO DE MOVIMENTOS

$ARRAY_MOVIMENTO = array(66 => 213.95, 199 => 32.92);
$ids_curso = '1310,1311,1312,1313,1314,1315,1316,1317,1318,1319,1320,1321,1322,1323,1328,1329,1330,1331,1332,1333,1334,1335,1336,1337,1338,1339,1340,1341,1346,1347,1348,1349,1350,1351,1352,1353,1354,1355,1356,1357,1358,1359,1382,1385,1386,1388,1389,1390,1391,1411,1431,1513,1957,1956,1955,1954,1950,1949,1948,1947,1944,1945,1943,1928,1927,1926,1925,1924,1923,1922,1921,1920,1919,1918,1917,1916,1915,1914,1910,1909,1908,1907,1905,1904,1903,1888,1887,1886,1885,1884,1883,1882,1881,1880,1879,1878,1877,1876,1875,1874,1870,1869,1868,1867,1865,1864,1863,1848,1847,1846,1845,1844,1843,1842,1841,1840,1839,1838,1837,1836,1835,1834,1830,1829,1828,1827,1825,1824,1823,1808,1807,1806,1805,1804,1803,1802,1801,1800,1799,1798,1797,1796,1795,1794,1790,1789,1788,1787,1785,1784,1783,1767,1768,1766,1765,1764,1763,1762,1761,1760,1759,1758,1757,1756,1755,1958,1959,1960,1961,1962,1963,1964,1965,1966,1967,1968,1983,1984,1985,1987,1988,1989,1990,2006,1994,1997,1998,1999,2000,2001,2002,2003,2004,2005,2019,2020,2021,2023,2024,2025,2026,2045,2072,2073,2074'; 
$id_regiao = 45;
$ids_projeto = '3302,3303,3304,3315,3316,3317,3318,3319,3320,3338';
$gravar     = 0;
$mes = 8;
$ano = 2014;

$sql_movimento = "AND lancamento = 2";

echo '<table>';
echo '<tr>
        <td>ID_CLT</td>
        <td>NOME</td>
        <td>STATUS</td>
        <td>PROJETO</td>
        <td>VALOR</td>
        <td>COD MOVIMENTO</td>
    </tr>';

foreach($ARRAY_MOVIMENTO as $id_movimento => $valor){

    $qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_regiao = $id_regiao AND id_projeto IN( $ids_projeto)") or die(mysql_error());
    while($row_pro = mysql_fetch_assoc($qr_projeto)){
        

        $qr_clt = mysql_query("SELECT *, MONTH(data_entrada) as mes_adm,YEAR(data_entrada) as ano_adm FROM rh_clt 
            WHERE id_regiao = {$row_pro[id_regiao]} AND id_projeto = {$row_pro['id_projeto']}  AND id_curso IN($ids_curso) AND status < 60 ORDER BY nome ") or die|(mysql_error());
     
        while($row_clt  = mysql_fetch_assoc($qr_clt)){
            
            
            echo '<tr>';
                echo '<td>'.$row_clt['id_clt'].'</td>';
                echo '<td>'.$row_clt['nome'].'</td>';
                echo '<td>'.$row_clt['status'].'</td>';
                echo '<td>'.$row_pro['nome'].'</td>';
                echo '<td>'.$row_mov['valor_movimento'].'</td>';
                echo '<td>'.$id_movimento.'</td>';
              
            
           
              $VERIFICA = mysql_query("SELECT * FROM rh_movimentos_clt WHERE  id_clt = $row_clt[id_clt] AND id_mov = $id_movimento  {$sql_movimento} AND status = 1") or die(mysql_error());
            
              if(mysql_num_rows($VERIFICA) != 0){

                  $row_mov = mysql_fetch_assoc($VERIFICA); 
                  $id_movimento_antigo = $row_mov['id_movimento'];
                            ///EXCLUI MOVIMENTO ANTIGO
                          // mysql_query("UPDATE rh_movimentos_clt SET  status = 0 WHERE id_movimento = '$id_movimento_antigo' AND id_clt = $row_clt[id_clt] LIMIT 1" ) or die(mysql_error()); 


                    
                    
              $qr_mov  =  mysql_query("SELECT * FROM rh_movimentos WHERE id_mov = $id_movimento");       
                $dados_mov = mysql_fetch_assoc($qr_mov);
                $SQL           = "('$row_clt[id_clt]', '$row_clt[id_regiao]', '$row_clt[id_projeto]','$dados_mov[id_mov]', '{$mes}', '{$ano}','$dados_mov[cod]', '$dados_mov[categoria]', '$dados_mov[descicao]', NOW(), '$_COOKIE[logado]', '$valor', 2, '5020,5021,5023',1,1,1)";

             //INSERINDO O MOVIMENTO
                
           /*  if($gravar == 1){   
                IF(mysql_query("INSERT INTO rh_movimentos_clt  (id_clt, id_regiao,id_projeto,id_mov,mes_mov, ano_mov, cod_movimento, tipo_movimento, nome_movimento, data_movimento, user_cad, valor_movimento,lancamento, incidencia, status, status_ferias,status_reg) 
                                      VALUES $SQL")){
                   ECHO '<td>OK</td>';  

               }   
             }*/
           }

   
   
    
} 
 echo '</tr>';
    }
    
}
echo '</table>';
?>