<?php 

class CsvReader {

    var $fields;            /** columns names retrieved after parsing */ 
    var $separator = ';';    /** separator used to explode each line */
    var $enclosure = '"';    /** enclosure used to decorate each field */

    var $max_row_size = 4096;    /** maximum row size to be used for decoding */

    function parse_file($p_Filepath) {

        $file           = fopen($p_Filepath, 'r');
        $this->fields   = fgetcsv($file, $this->max_row_size, $this->separator, $this->enclosure);
        
        if($this->separator==","){
            $keys_values    = explode(',',$this->fields[0]);
        } else {
            $keys_values = $this->fields;
        }

        $content        =   array();
        $content['headers'] = $this->fields;
        $content['data']    = array();   
        $keys           =   $this->escape_string($keys_values);
        $i  =   1;
        while( ($row = fgetcsv($file, $this->max_row_size, $this->separator, $this->enclosure)) != false ) {            
            if( $row != null ) { // skip empty lines
                $values =   $row;

                if(count($keys) == count($values)){
                    $arr    =   array();
                    $new_values =   array();
                    $new_values =   $this->escape_string($values);
                    for($j=0;$j<count($keys);$j++){
                        if($keys[$j] != ""){
                            $arr[$keys[$j]] =  $new_values[$j];
                        }
                    }
                    array_push($content['data'], $arr);                  
                    $i++;
                }             
            } 
        }       
        fclose($file);
        return $content;     
    }

    function escape_string($data){
        $result =   array();
        foreach($data as $row){
            $result[]   =   str_replace('"', '',$row);
        }
        return $result;
    }      
}

$csv = new CsvReader();
$res = $csv->parse_file('../cbo/cbo2003_2502.csv');
//$res = $csv->parse_file('../cbo/cbo2003_2502.csv');

?>
<table>
    <tr>
        <th>CÓDIGO</th>
        <th>FUNÇÂO</th>
    </tr>
    <?php foreach($res['data'] as $lin){ ?>
        <tr>
            <th><?= $lin['CODIGO'] ?></th>
            <th style="text-align: left;"><?= $lin['DESCRICAO'] ?></th>        
        </tr>
    <?php } ?>
</table>

<?php
echo '<pre>';
print_r($res);
echo '</pre>';
?>