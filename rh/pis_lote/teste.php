<?php

        $array = array(
            '00' => array(
                '0900' => array('00' => array('tipo' => 'c', 'tam' => 01, 'obrg' => TRUE)),
                '0829' => array('00' => array('tipo' => 'n', 'tam' => 14, 'obrg' => TRUE)),
                '0313' => array('00' => array('tipo' => 'c', 'tam' => 40, 'obrg' => TRUE)),
                '0413' => array('00' => array('tipo' => 'c', 'tam' => 01, 'obrg' => TRUE)),
                '0903' => array('00' => array('tipo' => 'n', 'tam' => 08, 'obrg' => TRUE)),
                '0913' => array('00' => array('tipo' => 'n', 'tam' => 04, 'obrg' => TRUE)),
            ),
            '01' => array(
                '0378' => array('00' => array('tipo' => 'n', 'tam' => 14, 'obrg' => FALSE)),
                '0379' => array('00' => array('tipo' => 'n', 'tam' => 12, 'obrg' => FALSE)),
                '0104' => array('00' => array('tipo' => 'c', 'tam' => 50, 'obrg' => FALSE)),
            ),
            '02' => array(
                '0902' => array('00' => array('tipo' => 'c', 'tam' => 01, 'obrg' => TRUE)),
                '0422' => array('00' => array('tipo' => 'n', 'tam' => 11, 'obrg' => FALSE)),
                '0195' => array('00' => array('tipo' => 'c', 'tam' => 70, 'obrg' => TRUE)),
                '0197' => array('00' => array('tipo' => 'n', 'tam' => 08, 'obrg' => TRUE)),
                '0200' => array('00' => array('tipo' => 'c', 'tam' => 70, 'obrg' => TRUE)),
                '0199' => array('00' => array('tipo' => 'c', 'tam' => 70, 'obrg' => TRUE)),
                '0390' => array('00' => array('tipo' => 'n', 'tam' => 07, 'obrg' => TRUE)),
                '0386' => array(
                    '00' => array('tipo' => 'n', 'tam' => 4, 'obrg' => TRUE),
                    '01' => array('tipo' => 'c', 'tam' => 1, 'obrg' => TRUE)
                ),
                '0370' => array('00' => array('tipo' => 'n', 'tam' => 11, 'obrg' => TRUE)),
                '0373' => array(
                    '00' => array('tipo' => 'n', 'tam' => 07, 'obrg' => TRUE),
                    '01' => array('tipo' => 'n', 'tam' => 05, 'obrg' => TRUE),
                    '02' => array('tipo' => 'c', 'tam' => 02, 'obrg' => TRUE),
                    '03' => array('tipo' => 'n', 'tam' => 08, 'obrg' => TRUE),
                ),
                '0911' => array(
                    '00' => array('tipo' => 'n', 'tam' => 08, 'obrg' => TRUE),
                    '01' => array('tipo' => 'c', 'tam' => 01, 'obrg' => TRUE),
                    '02' => array('tipo' => 'c', 'tam' => 03, 'obrg' => TRUE),
                    '03' => array('tipo' => 'c', 'tam' => 40, 'obrg' => TRUE),
                    '04' => array('tipo' => 'c', 'tam' => 05, 'obrg' => TRUE),
                    '05' => array('tipo' => 'c', 'tam' => 07, 'obrg' => TRUE),
                    '06' => array('tipo' => 'c', 'tam' => 15, 'obrg' => FALSE),
                    '07' => array('tipo' => 'c', 'tam' => 40, 'obrg' => TRUE),
                    '08' => array('tipo' => 'c', 'tam' => 07, 'obrg' => TRUE),
                ),
                '0292' => array(
                    '00' => array('tipo' => 'n', 'tam' => 07, 'obrg' => TRUE),
                    '01' => array('tipo' => 'n', 'tam' => 14, 'obrg' => TRUE),
                    '02' => array('tipo' => 'n', 'tam' => 08, 'obrg' => TRUE)
                ),
            ),
        );
$cod = array_keys($array['02']);

foreach ($cod as $value) {
    for($i = 0; $i < count($array['02'][$value]);$i++){
        $id = str_pad($i, 2, 0, STR_PAD_LEFT);
        var_dump($array['02'][$value][$id]['obrg']);
        echo '<br>';
    }
}