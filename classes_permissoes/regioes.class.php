<?php

class Regioes {

    private $sql;
    private $row;

    public function Preenhe_select_por_master($id_master, $regiao) {

        if ($regiao) {
            $regiao = $regiao;
        } else {
            $regiao = '';
        }



        $array_status = array(1 => 'REGI&Otilde;ES ATIVAS', 0 => 'REGI&Otilde;ES INATIVAS');

        foreach ($array_status as $status => $nome_status) {



            if ($status == 0) {
                $qr_regiao = mysql_query("SELECT * FROM regioes 
										 INNER JOIN funcionario_regiao_assoc
										 ON regioes.id_regiao = funcionario_regiao_assoc.id_regiao
										  WHERE (regioes.status = '$status' OR regioes.status_reg = '$status') 
										  		AND funcionario_regiao_assoc.id_funcionario = '$_COOKIE[logado]'
												AND funcionario_regiao_assoc.id_master = '$id_master'") or die(mysql_error());
            } else {
                $qr_regiao = mysql_query("SELECT * FROM regioes 
										 INNER JOIN funcionario_regiao_assoc
										 ON regioes.id_regiao = funcionario_regiao_assoc.id_regiao
										  WHERE regioes.status = '$status' 
										  		AND regioes.status_reg = '$status'	 
										  		AND funcionario_regiao_assoc.id_funcionario = '$_COOKIE[logado]'
												AND funcionario_regiao_assoc.id_master = '$id_master'") or die(mysql_error());
            }


            if (mysql_num_rows($qr_regiao) != 0) {

                echo '<option value="-1">SELECIONE A REGI�O</option>';
                echo '<optgroup label="' . $nome_status . '">';


                while ($row_regiao = mysql_fetch_assoc($qr_regiao)):

                    $selected = ($regiao == $row_regiao['id_regiao']) ? 'selected="selected"' : '';
                    echo '<option value="' . $row_regiao['id_regiao'] . '" ' . $selected . ' >' . $row_regiao['id_regiao'] . ' - ' . $row_regiao['regiao'] . '</option>';

                endwhile;

                echo '</optgroup>';
            }
        }
    }

    public function Preenhe_select_sem_master($regiao = false) {

        if ($regiao) {
            $regiao = $regiao;
        } else {
            $regiao = '';
        }
        echo '<option value="' . $row_regiao['id_regiao'] . '">Selecione a regi&atilde;o...</option>';


        $array_status = array(1 => 'REGI&Otilde;ES ATIVAS', 0 => 'REGI&Otilde;ES INATIVAS');

        foreach ($array_status as $status => $nome_status) {

            echo '<optgroup label="' . $nome_status . '">';

            if ($status == 0) {

                $qr_regiao = mysql_query("SELECT * FROM regioes 
										 INNER JOIN funcionario_regiao_assoc
										 ON regioes.id_regiao = funcionario_regiao_assoc.id_regiao
										 INNER JOIN master ON master.id_master = regioes.id_master
										  WHERE (regioes.status = '$status' OR regioes.status_reg = '$status') 
										  		AND funcionario_regiao_assoc.id_funcionario = '$_COOKIE[logado]'
												AND regioes.id_regiao != 43 
												AND master.status = 1 
												ORDER BY regioes.regiao") or die(mysql_error());
            } else {

                $qr_regiao = mysql_query("SELECT * FROM regioes 
										 INNER JOIN funcionario_regiao_assoc
										 ON regioes.id_regiao = funcionario_regiao_assoc.id_regiao
										 INNER JOIN master ON master.id_master = regioes.id_master
										  WHERE regioes.status = '$status' 
											AND regioes.status_reg = '$status'	 
											AND funcionario_regiao_assoc.id_funcionario = '$_COOKIE[logado]'
											AND regioes.id_regiao != 43  
											AND master.status = 1 
											ORDER BY regioes.regiao") or die(mysql_error());
            }

            while ($row_regiao = mysql_fetch_assoc($qr_regiao)):

                $selected = ($regiao == $row_regiao['id_regiao']) ? 'selected="selected"' : '';
                echo '<option value="' . $row_regiao['id_regiao'] . '" ' . $selected . ' >' . $row_regiao['id_regiao'] . ' - ' . $row_regiao['regiao'] . '</option>';

            endwhile;
            echo '</optgroup>';
        }
    }
    
     public function Preenhe_select_com_master($regiao = false) {

        if ($regiao) {
            $regiao = $regiao;
        } else {
            $regiao = '';
        }
        echo '<option value="' . $row_regiao['id_regiao'] . '">Selecione a regi&atilde;o...</option>';


        $array_status = array(1 => 'REGI&Otilde;ES ATIVAS', 0 => 'REGI&Otilde;ES INATIVAS');

        foreach ($array_status as $status => $nome_status) {

            echo '<optgroup label="' . $nome_status . '">';

            if ($status == 0) {

                $qr_regiao = mysql_query("SELECT * FROM regioes 
										 INNER JOIN funcionario_regiao_assoc
										 ON regioes.id_regiao = funcionario_regiao_assoc.id_regiao
										 INNER JOIN master ON master.id_master = regioes.id_master
										  WHERE (regioes.status = '$status' OR regioes.status_reg = '$status') 
										  		AND funcionario_regiao_assoc.id_funcionario = '$_COOKIE[logado]'
												AND regioes.id_regiao != 43 
												AND master.status = 1 
                                                                                                AND master.id_master = 6
												ORDER BY regioes.regiao") or die(mysql_error());
            } else {

                $qr_regiao = mysql_query("SELECT * FROM regioes 
										 INNER JOIN funcionario_regiao_assoc
										 ON regioes.id_regiao = funcionario_regiao_assoc.id_regiao
										 INNER JOIN master ON master.id_master = regioes.id_master
										  WHERE regioes.status = '$status' 
											AND regioes.status_reg = '$status'	 
											AND funcionario_regiao_assoc.id_funcionario = '$_COOKIE[logado]'
											AND regioes.id_regiao != 43  
											AND master.status = 1
                                                                                        AND master.id_master = 6
											ORDER BY regioes.regiao") or die(mysql_error());
            }

            while ($row_regiao = mysql_fetch_assoc($qr_regiao)):

                $selected = ($regiao == $row_regiao['id_regiao']) ? 'selected="selected"' : '';
                echo '<option value="' . $row_regiao['id_regiao'] . '" ' . $selected . ' >' . $row_regiao['id_regiao'] . ' - ' . $row_regiao['regiao'] . '</option>';

            endwhile;
            echo '</optgroup>';
        }
    }

    public function Preenhe_select_sem_master_prestador_servico($regiao = false) {

        //Método diferente para não pegar o INSTITUO LAGOS		

        if ($regiao) {
            $regiao = $regiao;
        } else {
            $regiao = '';
        }
        echo '<option value="' . $row_regiao['id_regiao'] . '">Selecione a regi&atilde;o...</option>';


        $array_status = array(1 => 'REGI&Otilde;ES ATIVAS', 0 => 'REGI&Otilde;ES INATIVAS');

        foreach ($array_status as $status => $nome_status) {

            echo '<optgroup label="' . $nome_status . '">';

            if ($status == 0) {

                $qr_regiao = mysql_query("SELECT * FROM regioes 
										 INNER JOIN funcionario_regiao_assoc
										 ON regioes.id_regiao = funcionario_regiao_assoc.id_regiao
										 INNER JOIN master ON master.id_master = regioes.id_master
										  WHERE (regioes.status = '$status' OR regioes.status_reg = '$status') 
										  		AND funcionario_regiao_assoc.id_funcionario = '$_COOKIE[logado]'
												AND regioes.id_regiao != 43 
												
												ORDER BY regioes.regiao") or die(mysql_error());
            } else {

                $qr_regiao = mysql_query("SELECT * FROM regioes 
										 INNER JOIN funcionario_regiao_assoc
										 ON regioes.id_regiao = funcionario_regiao_assoc.id_regiao
										 INNER JOIN master ON master.id_master = regioes.id_master
										  WHERE regioes.status = '$status' 
											AND regioes.status_reg = '$status'	 
											AND funcionario_regiao_assoc.id_funcionario = '$_COOKIE[logado]'
											AND regioes.id_regiao != 43  
											
											ORDER BY regioes.regiao") or die(mysql_error());
            }

            while ($row_regiao = mysql_fetch_assoc($qr_regiao)):

//                if ($row_regiao['id_regiao'] == '44')
//                    continue;
                $selected = ($regiao == $row_regiao['id_regiao']) ? 'selected="selected"' : '';
                echo '<option value="' . $row_regiao['id_regiao'] . '" ' . $selected . ' >' . $row_regiao['id_regiao'] . ' - ' . $row_regiao['regiao'] . '</option>';

            endwhile;
            echo '</optgroup>';
        }
    }

    public function Selecionar_por_master($id_master, $status) {



        if ($status == 0) {
            $qr_regiao = mysql_query("SELECT * FROM regioes 
										 INNER JOIN funcionario_regiao_assoc
										 ON regioes.id_regiao = funcionario_regiao_assoc.id_regiao
										  WHERE (regioes.status = '$status' OR regioes.status_reg = '$status') 
												AND funcionario_regiao_assoc.id_funcionario = '$_COOKIE[logado]'
												AND funcionario_regiao_assoc.id_master = '$id_master'") or die(mysql_error());
        } else {
            $qr_regiao = mysql_query("SELECT * FROM regioes 
										 INNER JOIN funcionario_regiao_assoc
										 ON regioes.id_regiao = funcionario_regiao_assoc.id_regiao
										  WHERE regioes.status = '$status' 
												AND regioes.status_reg = '$status'	 
												AND funcionario_regiao_assoc.id_funcionario = '$_COOKIE[logado]'
												AND funcionario_regiao_assoc.id_master = '$id_master'") or die(mysql_error());
        }
        $cont = 0;
        while ($row_regiao = mysql_fetch_assoc($qr_regiao)):

            $resultado[$cont]['regiao'] = $row_regiao['regiao'];

            $resultado[$cont]['id_regiao'] = $row_regiao['id_regiao'];
            $resultado[$cont]['id_master'] = $row_regiao['id_master'];

            $resultad[$cont]['sigla'] = $row_regiao['sigla'];
            $resultado[$cont]['criador'] = $row_regiao['criador'];
            $resultado[$cont]['status'] = $row_regiao['status'];
            $resultado[$cont]['status_reg'] = $row_regiao['status_reg'];

            $cont++;
        endwhile;

        return $resultado;
    }

    public function Preenhe_select_por_area($regiao = false, $botoes_id) {

        if ($regiao) {
            $regiao = $regiao;
        } else {
            $regiao = '';
        }
        echo '<option value="' . $row_regiao['id_regiao'] . '">Selecione a regi&atilde;o...</option>';


        $array_status = array(1 => 'REGI&Otilde;ES ATIVAS', 0 => 'REGI&Otilde;ES INATIVAS');

        foreach ($array_status as $status => $nome_status) {

            echo '<optgroup label="' . $nome_status . '">';

            if ($status == 0) {

                $qr_regiao = mysql_query("SELECT * FROM regioes 
										 INNER JOIN funcionario_regiao_assoc
										 ON regioes.id_regiao = funcionario_regiao_assoc.id_regiao
										 INNER JOIN master ON master.id_master = regioes.id_master
										  WHERE (regioes.status = '$status' OR regioes.status_reg = '$status') 
										  		AND funcionario_regiao_assoc.id_funcionario = '$_COOKIE[logado]'
												AND regioes.id_regiao != 43 
												AND master.status = 1 
												AND funcionario_regiao_assoc.botoes_id = '$botoes_id'
												ORDER BY regioes.regiao") or die(mysql_error());
            } else {

                $qr_regiao = mysql_query("SELECT * FROM regioes 
										 INNER JOIN funcionario_regiao_assoc
										 ON regioes.id_regiao = funcionario_regiao_assoc.id_regiao
										 INNER JOIN master ON master.id_master = regioes.id_master
										  WHERE regioes.status = '$status' 
											AND regioes.status_reg = '$status'	 
											AND funcionario_regiao_assoc.id_funcionario = '$_COOKIE[logado]'
											AND regioes.id_regiao != 43  
											AND master.status = 1 
										    AND funcionario_regiao_assoc.botoes_id = '$botoes_id'
											ORDER BY regioes.regiao") or die(mysql_error());
            }

            while ($row_regiao = mysql_fetch_assoc($qr_regiao)):

                $selected = ($regiao == $row_regiao['id_regiao']) ? 'selected="selected"' : '';
                echo '<option value="' . $row_regiao['id_regiao'] . '" ' . $selected . ' >' . $row_regiao['id_regiao'] . ' - ' . $row_regiao['regiao'] . '</option>';

            endwhile;
            echo '</optgroup>';
        }
    }

    public function Select_permissao_relatorio($regiao = false) {

        if ($regiao) {
            $regiao = $regiao;
        } else {
            $regiao = '';
        }
        echo '<option value="' . $row_regiao['id_regiao'] . '">Selecione a regi&atilde;o...</option>';


        $array_status = array(1 => 'REGI&Otilde;ES ATIVAS', 0 => 'REGI&Otilde;ES INATIVAS');

        foreach ($array_status as $status => $nome_status) {

            echo '<optgroup label="' . $nome_status . '">';

            if ($status == 0) {

                $qr_regiao = mysql_query("select B.id_regiao, B.regiao from regioes_relatorios_assoc AS A
                                                            INNER JOIN regioes as B
                                                            ON A.id_regiao = B.id_regiao
                                                            WHERE A.id_funcionario = '$_COOKIE[logado]' AND (B.`status` = 0 OR B.status_reg = 0)") or die(mysql_error());
            } else {

                $qr_regiao = mysql_query("select B.id_regiao, B.regiao from regioes_relatorios_assoc AS A
                                                            INNER JOIN regioes as B
                                                            ON A.id_regiao = B.id_regiao
                                                            WHERE A.id_funcionario = '$_COOKIE[logado]' AND B.`status` = 1 OR B.status_reg = 1") or die(mysql_error());
            }

            while ($row_regiao = mysql_fetch_assoc($qr_regiao)):

                $selected = ($regiao == $row_regiao['id_regiao']) ? 'selected="selected"' : '';
                echo '<option value="' . $row_regiao['id_regiao'] . '" ' . $selected . ' >' . $row_regiao['id_regiao'] . ' - ' . $row_regiao['regiao'] . '</option>';

            endwhile;
            echo '</optgroup>';
        }
    }

}

?>