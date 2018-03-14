<?php
include("../../conn.php");

$qr_prestador = mysql_query("select * from prestadorservico where id_prestador > 500");

while($prestador = mysql_fetch_assoc($qr_prestador)) {
    //MOVENDO OS SÓCIOS DA TABELA prestadorservico PARA A TABELA prestador_socio
    if(!empty($prestador['co_responsavel_socio1'])) {
        $qr_inserir_socio = mysql_query("INSERT INTO prestador_socio(nome,tel,id_prestador) VALUES('{$prestador['co_responsavel_socio1']}','{$prestador['co_tel_socio1']}','{$prestador['id_prestador']}')");
        $qr_deletar_socio = mysql_query("UPDATE prestadorservico
                SET co_responsavel_socio1 = NULL,
                co_tel_socio1 = NULL,
                co_fax_socio1 = NULL,
                co_civil_socio1 = NULL,
                co_nacionalidade_socio1 = NULL,
                co_email_socio1 = NULL,
                co_municipio_socio1 = NULL,
                data_nasc_socio1 = NULL
                WHERE id_prestador = '{$prestador['id_prestador']}'
                LIMIT 1
                ");
    }
    if(!empty($prestador['co_responsavel_socio2'])) {
        $qr_inserir_socio = mysql_query("INSERT INTO prestador_socio(nome,tel, id_prestador) VALUES('{$prestador['co_responsavel_socio2']}','{$prestador['co_tel_socio2']}','{$prestador['id_prestador']}')");
        $qr_deletar_socio = mysql_query("UPDATE prestadorservico
                SET co_responsavel_socio2 = NULL,
                co_tel_socio2 = NULL,
                co_fax_socio2 = NULL,
                co_civil_socio2 = NULL,
                co_nacionalidade_socio2 = NULL,
                co_email_socio2 = NULL,
                co_municipio_socio2 = NULL,
                data_nasc_socio2 = NULL
                WHERE id_prestador = '{$prestador['id_prestador']}'
                LIMIT 1
                ");
    }
}