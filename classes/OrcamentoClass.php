<?php
class OrcamentoClass {
    
//    private $arrayMeses = array(3,4,5,6,7,8,9,10,11,12,1,2);
    private $arrayMeses = array(1,2,3,4,5,6,7,8,9,10,11,12);
    
    public function getOrcamento($ano, $mes, $tipo, $unidadesProjeto = null, $id_unidade = null, $check = array('simples' => 1,'realizado' => 1,'previsto' => 1,'provisionado' => 1), $id_projeto = null){
        $ano2 = $ano+1;
        $auxMesAno = (empty($tipo)) ? " AND MONTH(A.data_vencimento) = {$mes} AND YEAR(A.data_vencimento) = {$ano}" : " AND A.data_vencimento BETWEEN '{$ano}-03-01' AND '{$ano2}-02-01'"; 
        $auxMes2 = (empty($tipo)) ? " DATE_FORMAT(ADDDATE(A.inicio, INTERVAL (C.mes-1) MONTH),'%m%Y') = DATE_FORMAT('{$ano}-{$mes}-01','%m%Y') " : " ADDDATE(A.inicio, INTERVAL (C.mes-1) MONTH) BETWEEN '{$ano}-03-01' AND '{$ano2}-02-01' "; 
        if(!empty($id_unidade)){
            $auxWHERE = "AND A.id IN (SELECT id_orcamento FROM gestao_orcamentos_unidades_associativas WHERE id_unidade IN ($id_unidade))";
            $auxUnidade = "AND B.id_unidade IN ($id_unidade)";
        } else if(!empty($unidadesProjeto)){
            $auxWHERE = "AND A.id IN (SELECT id_orcamento FROM gestao_orcamentos_unidades_associativas WHERE id_unidade IN ($unidadesProjeto))";
            $auxUnidade = "AND (B.id_unidade IN ($unidadesProjeto) OR A.id_projeto = $id_projeto)";
        }

        /**
         * QUERY PARA AS SAIDAS
         */
        echo $sql = "
        SELECT E.id_grupo, E.nome_grupo, D.id_subgrupo, D.nome nome_subgrupo, SUM(CAST(REPLACE(A.valor,',','.') AS DECIMAL(20,2))) valor, A.status, MONTH(A.data_vencimento) AS mes_competencia
        FROM entradaesaida C
        -- LEFT JOIN saida_unidade B ON (A.id_saida = B.id_saida)
        LEFT JOIN saida A ON (A.tipo = C.id_entradasaida AND A.`status` NOT IN (0,1) $auxMesAno $auxUnidade /*AND A.tipo NOT IN (260)*/)
        LEFT JOIN entradaesaida_subgrupo D ON (D.id_subgrupo = SUBSTR(C.cod,1,8))
        LEFT JOIN entradaesaida_grupo E ON (E.id_grupo = D.entradaesaida_grupo)
        -- WHERE 
        GROUP BY MONTH(A.data_vencimento), E.id_grupo, D.id_subgrupo, status
        ORDER BY D.id_subgrupo, E.id_grupo;";
        if($_COOKIE[debug] == 666){
            echo '<br>////////////////////////SAIDA////////////////////////<br>';
            print_array($sql);
        }
        $qry = mysql_query($sql) or die('$sql: ' . $sql);
        while($row = mysql_fetch_assoc($qry)){
    //        $arraySubGrupos[$row['mes_competencia']][$row['id_grupo']][$row['id_subgrupo']]
            $arraySubGrupos[$row['id_grupo']][$row['id_subgrupo']][$row['status']] += $row['valor'];
            $arraySubGrupos[$row['id_grupo']][$row['id_subgrupo']]['descricao'] = $row['nome_subgrupo'];
            $arraySubGrupos[$row['id_grupo']][$row['id_subgrupo']]['mes'][$row['mes_competencia']][$row['status']] = $row['valor'];
            
            $arraySaldoAcumuladoDespesa[$row['id_subgrupo']][$row['mes_competencia']] -= ($row['mes_competencia'] <= date('m')) ? $row['valor'] : 0;
            $arraySaldoAcumuladoDespesa[substr($row['id_subgrupo'], 0,5)][$row['mes_competencia']] -= ($row['mes_competencia'] <= date('m')) ? $row['valor'] : 0;
            
            if($row['status'] == 2)
                $arrayTotalSubGrupos[$row['id_subgrupo']] += $row['valor'];

            if(($check['previsto'] && $row['status'] == 1) || ($check['realizado'] && $row['status'] == 2))
                $arrayTotalGrupos[$row['status']][$row['mes_competencia']] += $row['valor'];

            $arrayGrupos[$row['id_grupo']]['mes'][$row['mes_competencia']][$row['status']] += $row['valor'];
            $arrayGrupos[$row['id_grupo']][$row['status']] += $row['valor'];
            $arrayGrupos[$row['id_grupo']]['descricao'] = $row['nome_grupo'];
        }
//print_array($arraySaldoAcumuladoDespesa);
        /**
         * QUERY PARA AS ENTRADAS
         */
        $sql = "
        SELECT A.tipo, SUM(A.valor) valor, B.descricao, B.nome, A.status, MONTH(A.data_vencimento) AS mes_competencia
        FROM entrada A
        LEFT JOIN entradaesaida B ON (A.tipo = B.id_entradasaida)
        WHERE A.`status` NOT IN (0,1) $auxMesAno
        GROUP BY MONTH(A.data_vencimento), A.tipo, A.`status`
        ORDER BY A.tipo;";
        $qry = mysql_query($sql);
        while($row = mysql_fetch_assoc($qry)){
            $arrayEntradas[$row['tipo']][$row['status']] += $row['valor'];
            $arrayEntradas[$row['tipo']]['descricao'] = $row['nome'];
            $arrayEntradas[$row['tipo']]['mes'][$row['mes_competencia']][$row['status']] = $row['valor'];

            $arraySaldoAcumuladoReceita[$row['tipo']][$row['mes_competencia']] += ($row['mes_competencia'] <= date('m')) ? $row['valor'] : 0;
            
            if(($check['previsto'] && $row['status'] == 1) || ($check['realizado'] && $row['status'] == 2))
            $arrayTotalEntradas[$row['status']][$row['mes_competencia']] += $row['valor'];
        }

        /**
         * QUERY PARA OS ORÇAMENTOS
         */
        $sqlOrcamento = "
        SELECT C.codigo, C.descricao, SUM(C.valor) AS valor, MONTH(ADDDATE(A.inicio, INTERVAL (C.mes-1) MONTH)) mes
        FROM gestao_orcamentos A $auxFROM
        LEFT JOIN gestao_orcamentos_valores C ON (A.id = C.orcamento_id)
        WHERE $auxMes2 $auxWHERE 
        GROUP BY C.codigo, C.mes";
        $qryOrcamento = mysql_query($sqlOrcamento) or die('$sqlOrcamento: ' . $sqlOrcamento);
        while($rowOrcamento = mysql_fetch_assoc($qryOrcamento)){
            if(!in_array($rowOrcamento['codigo'], $arraySubGrupos) && $rowOrcamento['valor'] > 0 && strlen($rowOrcamento['codigo']) > 3)
                $arraySubGrupos[substr($rowOrcamento['codigo'], 0,5)][$rowOrcamento['codigo']]['descricao'] = $rowOrcamento['descricao'];

            if(!in_array($rowOrcamento['codigo'], $arrayGrupos) && $rowOrcamento['valor'] > 0 && strlen($rowOrcamento['codigo']) < 3)
                    $arrayGrupos[$rowOrcamento['codigo']]['descricao'] = $rowOrcamento['descricao'];

            $arrayOrcamento[$rowOrcamento['codigo']] += $rowOrcamento['valor'];
            $arrayOrcamentoMes[$rowOrcamento['codigo']][$rowOrcamento['mes']] = $rowOrcamento['valor'];
            
            $arraySaldoAcumuladoDespesa[$rowOrcamento['codigo']][$rowOrcamento['mes']] += ($rowOrcamento['mes'] <= date('m')) ? $rowOrcamento['valor'] : 0;
            if(strlen($rowOrcamento['codigo']) > 3)
//                $arraySaldoAcumuladoDespesa[$rowOrcamento['codigo']][$rowOrcamento['mes']] += ((($rowOrcamento['mes'] < 3) ? $rowOrcamento['mes']+12 : $rowOrcamento['mes']) <= date('m')) ? $rowOrcamento['valor'] : 0;
                $arraySaldoAcumuladoDespesaTotal[$rowOrcamento['codigo']] += ($rowOrcamento['mes'] <= date('m')) ? $rowOrcamento['valor'] : 0;
            
            if($check['provisionado'])
                if(strlen($rowOrcamento['codigo']) > 3)
                    $arrayTotalOrcamento[$rowOrcamento['mes']] += $rowOrcamento['valor'];
        }
//        print_array($arrayOrcamento);
//        $arrayMeses = array(3,4,5,6,7,8,9,10,11,12,1,2);
        $arrayMeses = array(1,2,3,4,5,6,7,8,9,10,11,12);
        foreach ($arrayMeses as $ind => $mes) {
            foreach ($arraySaldoAcumuladoDespesa as $key => $value) {
                for($i=0; $i<=$ind; $i++){
                    $arraySaldoAcumuladoDespesaMes[$key][$mes] += $arraySaldoAcumuladoDespesa[$key][$arrayMeses[$i]];
                }
            }
            foreach ($arraySaldoAcumuladoReceita as $key => $value) {
                for($i=0; $i<=$ind; $i++){
                    $arraySaldoAcumuladoReceitaMes[$key][$mes] += $arraySaldoAcumuladoReceita[$key][$arrayMeses[$i]];
                }
            }
        }
        
        ksort($arraySubGrupos);
        ksort($arrayGrupos);
        
        return array(
            'arrayOrcamento' => $arrayOrcamento,
            'arrayOrcamentoMes' => $arrayOrcamentoMes,
            'arrayTotalOrcamento' => $arrayTotalOrcamento,

            'arrayEntradas' => $arrayEntradas,
            'arrayTotalEntradas' => $arrayTotalEntradas,

            'arrayGrupos' => $arrayGrupos,
            'arraySubGrupos' => $arraySubGrupos,
            'arrayTotalGrupos' => $arrayTotalGrupos,
            'arrayTotalSubGrupos' => $arrayTotalSubGrupos,
            
            'arraySaldoAcumuladoDespesa' => $arraySaldoAcumuladoDespesa,
            'arraySaldoAcumuladoDespesaMes' => $arraySaldoAcumuladoDespesaMes,
            'arraySaldoAcumuladoDespesaTotal' => $arraySaldoAcumuladoDespesaTotal,
            
            'arraySaldoAcumuladoReceitaMes' => $arraySaldoAcumuladoReceitaMes
        );
    }
    
    public function getOrcamento1($ano, $mes, $tipo, $id_banco, $check = ['simples' => 1,'realizado' => 1,'previsto' => 1,'provisionado' => 1] ){
        $ano2 = $ano;
        $auxMesAno = (empty($tipo)) ? " AND MONTH(D.data_vencimento) = {$mes} AND YEAR(D.data_vencimento) = {$ano}" : " AND D.data_vencimento BETWEEN '{$ano}-01-01' AND LAST_DAY('{$ano2}-12-01')"; 
        $auxMes2 = (empty($tipo)) ? " DATE_FORMAT(ADDDATE(A.inicio, INTERVAL (C.mes-1) MONTH),'%m%Y') = DATE_FORMAT('{$ano}-{$mes}-01','%m%Y') " : " ADDDATE(A.inicio, INTERVAL (C.mes-1) MONTH) BETWEEN '{$ano}-03-01' AND '{$ano2}-02-01' "; 
        $auxBanco = ($id_banco > 0) ? " AND D.id_banco = '{$id_banco}' " : null;
        $auxBanco2 = ($id_banco > 0) ? " AND A.banco_id = '{$id_banco}' " : null;
        
        $sqlGrupos = "
        SELECT 
        C.id_grupo AS codGrupo, C.nome_grupo AS nomeGrupo, 
        B.id_subgrupo AS codSubGrupo, B.nome AS nomeSubGrupo, 
        A.cod AS codTipo, A.nome AS nomeTipo
        FROM entradaesaida A 
        LEFT JOIN entradaesaida_subgrupo B ON (A.cod LIKE CONCAT(B.id_subgrupo,'%'))
        LEFT JOIN entradaesaida_grupo C ON (C.id_grupo = B.entradaesaida_grupo)
        WHERE A.grupo > 5
        ORDER BY C.id_grupo, B.id_subgrupo, A.cod";
        $qryGrupos = mysql_query($sqlGrupos) or die("ERROR sqlGrupos: " . mysql_error());
        while($rowGrupos = mysql_fetch_assoc($qryGrupos)) {
            $id_grupo = sprintf('%02d', substr($rowGrupos['codGrupo'], 0, -1));
            $arrayGrupos[$id_grupo] = ['descricao' => $rowGrupos['nomeGrupo']];
            $arraySubGrupos[$id_grupo][$rowGrupos['codSubGrupo']] = ['descricao' => $rowGrupos['nomeSubGrupo']];
            $arrayTipos[$rowGrupos['codSubGrupo']][$rowGrupos['codTipo']] = ['descricao' => $rowGrupos['nomeTipo']];
        }
        
        $sqlSaidas = "
        SELECT 
        C.id_grupo AS codGrupo, C.nome_grupo AS nomeGrupo, 
        B.id_subgrupo AS codSubGrupo, B.nome AS nomeSubGrupo, 
        A.cod AS codTipo, A.nome AS nomeTipo,
        D.valor, MONTH(D.data_vencimento) AS mes
        FROM saida D
        LEFT JOIN entradaesaida A ON (D.tipo = A.id_entradasaida)
        LEFT JOIN entradaesaida_subgrupo B ON (A.cod LIKE CONCAT(B.id_subgrupo,'%'))
        LEFT JOIN entradaesaida_grupo C ON (C.id_grupo = B.entradaesaida_grupo)
        WHERE D.status = 2 $auxMesAno $auxBanco
        ORDER BY C.id_grupo, B.id_subgrupo, A.cod";
//        print_array($sqlSaidas);
        $qrySaidas = mysql_query($sqlSaidas) or die("ERROR sqlSaidas: " . mysql_error());
        while($rowSaidas = mysql_fetch_assoc($qrySaidas)) {
            $id_grupo = sprintf('%02d', substr($rowSaidas['codGrupo'], 0, -1));
            $arraySaidas[$id_grupo][$rowSaidas['mes']] += $rowSaidas['valor'];
            $arraySaidas[$rowSaidas['codSubGrupo']][$rowSaidas['mes']] += $rowSaidas['valor'];
            $arraySaidas[$rowSaidas['codTipo']][$rowSaidas['mes']] += $rowSaidas['valor'];
        }
        
        $sqlOrcamento = "
        SELECT C.codigo, C.propriedade, C.valor, C.mes
        FROM gestao_orcamentos A
        LEFT JOIN gestao_orcamentos_valores C ON (A.id = C.orcamento_id)
        WHERE A.inicio BETWEEN '{$ano}-01-01' AND '{$ano}-12-31' $auxBanco2";
        $qryOrcamento = mysql_query($sqlOrcamento) or die('$sqlOrcamento: ' . mysql_error());
        while($rowOrcamento = mysql_fetch_assoc($qryOrcamento)){
            $arrayOrcamento[$rowOrcamento['codigo']][$rowOrcamento['mes']] += $rowOrcamento['valor'];
        }
        
        foreach ([1,2,3,4,5,6,7,8,9,10,11,12] as $mes) {
            foreach ($arrayGrupos as $grupo => $array) {
                $arraySaldo[$grupo][$mes] = $arraySaldo[$grupo][$mes-1] + (($check['previsto']) ? $arrayOrcamento[$grupo][$mes] : 0) - (($check['realizado']) ? $arraySaidas[$grupo][$mes] : 0);
                foreach ($arraySubGrupos[$grupo] as $subgrupo => $value) {
                    $arraySaldo[$subgrupo][$mes] = $arraySaldo[$subgrupo][$mes-1] + (($check['previsto']) ? $arrayOrcamento[$subgrupo][$mes] : 0) - (($check['realizado']) ? $arraySaidas[$subgrupo][$mes] : 0);
                }
            }
        }
//        ksort($arraySubGrupos);
//        ksort($arrayGrupos);
        return array(
            'arrayOrcamento' => $arrayOrcamento,
            'arrayGrupos' => $arrayGrupos,
            'arraySubGrupos' => $arraySubGrupos,
            'arrayTipos' => $arrayTipos,
            'arraySaidas' => $arraySaidas,
            'arraySaldo' => $arraySaldo,
        ); 
        exit;
    }
    
    public function getAvisos($unidadesProjeto = null, $id_unidade = null){
        $arrayGetOrcamento = $this->getOrcamento(date('Y'), 4, 0, $unidadesProjeto, $id_unidade);

        foreach ($arrayGetOrcamento['arraySubGrupos'] as $subGrupos) {
            foreach ($subGrupos as $key => $value) {
                $porcentagem = ($value[2] * 100) / $arrayGetOrcamento['arrayOrcamento'][$key];
                
                if($porcentagem >= 80 && $porcentagem <= 100){
                    $array['warning'][] = "<p><b>{$key} => {$value['descricao']}</b> está em " . round($porcentagem,2). "% do orçamento!</p>";
                } elseif ($porcentagem > 100) {
                    $array['danger'][] = "<p><i class='fa fa-arrow-right'></i> <b>{$key} => {$value['descricao']}</b> ultrapassou o orçamento em <b> R$ ".number_format($value[2] - $arrayGetOrcamento['arrayOrcamento'][$key],2,',','.')."</b></p>";
                } else if($value[2] > $arrayGetOrcamento['arrayOrcamento'][$key]){
                    $array['danger'][] = "<p><i class='fa fa-arrow-right'></i> <b>{$key} => {$value['descricao']}</b> ultrapassou o orçamento em <b> R$ ".number_format($value[2] - $arrayGetOrcamento['arrayOrcamento'][$key],2,',','.')."</b></p>";
                }
            }
        }
        
        krsort($array);
        
        $titulo = array('warning' => "Realizado de 80% a 100% do orçameto permitido", 'danger' => "Realizado ultrapassou o orçameto permitido");
        
        foreach ($array as $key => $value) {
            echo "<div class='alert alert-$key'>";
            echo "<kbd class='$key text-lg'>$titulo[$key]</kbd><hr style='margin-top: 5px; margin-bottom: 5px;'>";
            foreach ($value as $v) {
                echo $v;
            }
            echo '</div>';
        }
    }
    
    public function getAvisosSaldoAcumulado($arrayTotalSubGrupos = null, $arraySaldoAcumuladoDespesaTotal = null){
//    public function getAvisosSaldoAcumulado($unidadesProjeto = null, $id_unidade = null){
//        $arrayGetOrcamento = $this->getOrcamento(date('Y'), 4, 1, $unidadesProjeto, $id_unidade);
//        $porcentagem = (array_sum($arrayGetOrcamento['arrayTotalSubGrupos']) * 100) / array_sum($arrayGetOrcamento['arraySaldoAcumuladoDespesaTotal']);
        $porcentagem = (array_sum($arrayTotalSubGrupos) * 100) / array_sum($arraySaldoAcumuladoDespesaTotal);

        if($porcentagem >= 80 && $porcentagem <= 100){
            $array['warning'][] = "<p><i class='fa fa-arrow-right'></i> <b>O valor realizado esta em ".number_format($porcentagem, 2, '.', ',')."% do saldo acumulado</p>";
        } elseif ($porcentagem > 100) {
            $array['danger'][] = "<p><i class='fa fa-arrow-right'></i> <b>O valor realizado ultrapassou o saldo acumulado em <b> R$ ".number_format(array_sum($arrayTotalSubGrupos) - array_sum($arraySaldoAcumuladoDespesaTotal),2,',','.')."</b></p>";
        } else if($value[2] > $arrayGetOrcamento['arrayOrcamento'][$key]){
            $array['danger'][] = "<p><i class='fa fa-arrow-right'></i> <b>{$key} => {$value['descricao']}</b> ultrapassou o orçamento em <b> R$ ".number_format(array_sum($arrayTotalSubGrupos),2,',','.')."</b></p>";
        }
        
        krsort($array);
        
        foreach ($array as $key => $value) {
            echo "<div class='alert alert-$key'>";
            foreach ($value as $v) {
                echo $v;
            }
            echo '</div>';
        }
    }
    
    public function getOrcamento2($ano, $mes, $tipo, $unidadesProjeto = null, $id_unidade = null, $check = array('simples' => 1,'realizado' => 1,'previsto' => 1,'provisionado' => 1), $id_projeto = null){
        $ano2 = $ano+1;
        $arrayPorcent = array(1 => 1, 2 => 0.53, 3 => 0.43);
        $auxMesAno = (empty($tipo)) ? " AND MONTH(A.data_vencimento) = {$mes} AND YEAR(A.data_vencimento) = {$ano}" : " AND A.data_vencimento BETWEEN '{$ano}-03-01' AND '{$ano2}-02-01'"; 
        $auxMes2 = (empty($tipo)) ? " DATE_FORMAT(ADDDATE(A.inicio, INTERVAL (C.mes-1) MONTH),'%m%Y') = DATE_FORMAT('{$ano}-{$mes}-01','%m%Y') " : " ADDDATE(A.inicio, INTERVAL (C.mes-1) MONTH) BETWEEN '{$ano}-03-01' AND '{$ano2}-02-01' "; 
        $auxValor = "SUM(CAST(REPLACE(A.valor,',','.') AS DECIMAL(20,4)))";
        if(!empty($id_unidade)){
            $auxWHERE = "AND A.id IN (SELECT id_orcamento FROM gestao_orcamentos_unidades_associativas WHERE id_unidade IN ($id_unidade))";
            $auxUnidade = "AND B.id_unidade IN ($id_unidade)";
            $auxUnidadeEntrada = " AND A.id_projeto IN (1)";
        } else if(!empty($unidadesProjeto)){
            $auxWHERE = "AND A.id IN (SELECT id_orcamento FROM gestao_orcamentos_unidades_associativas WHERE id_unidade IN ($unidadesProjeto))";
            $auxUnidade = "AND (B.id_unidade IN ($unidadesProjeto) OR A.id_projeto IN ($id_projeto))";
            $auxUnidadeEntrada = " AND A.id_projeto IN (1,$id_projeto)";
//            $auxValor = "SUM(CAST(REPLACE(A.valor,',','.') AS DECIMAL(20,4))) * IF($id_projeto = 1, $arrayPorcent[$id_projeto],1)";
        }

        /**
         * QUERY PARA AS SAIDAS
         */
        echo $sql = "
        SELECT E.id_grupo, LPAD(E.numero,2,'0') AS numero, E.nome_grupo, D.id_subgrupo, D.nome nome_subgrupo, $auxValor valor, A.status, MONTH(A.data_vencimento) AS mes_competencia
        FROM saida A
        LEFT JOIN saida_unidade B ON (A.id_saida = B.id_saida)
        LEFT JOIN entradaesaida C ON (A.tipo = C.id_entradasaida)
        LEFT JOIN entradaesaida_subgrupo D ON (D.id_subgrupo = SUBSTR(C.cod,1,5))
        LEFT JOIN entradaesaida_grupo E ON (E.id_grupo = D.entradaesaida_grupo)
        WHERE A.`status` NOT IN (0,1) $auxMesAno $auxUnidade
        GROUP BY MONTH(A.data_vencimento), E.id_grupo, D.id_subgrupo, status
        ORDER BY D.id_subgrupo, E.id_grupo;";
        $qry = mysql_query($sql) or die("ERRO SQL SAIDA: " . mysql_error());
        while($row = mysql_fetch_assoc($qry)){ 
    //        $arraySubGrupos[$row['mes_competencia']][$row['id_grupo']][$row['id_subgrupo']]
            $arraySubGrupos[$row['id_grupo']][$row['id_subgrupo']][$row['status']] += $row['valor'];
            $arraySubGrupos[$row['id_grupo']][$row['id_subgrupo']]['descricao'] = $row['nome_subgrupo'];
            $arraySubGrupos[$row['id_grupo']][$row['id_subgrupo']]['mes'][$row['mes_competencia']][$row['status']] = $row['valor'];
            
            $arraySaldoAcumuladoDespesa[$row['id_subgrupo']][$row['mes_competencia']] -= ($row['mes_competencia'] <= date('m')) ? $row['valor'] : 0;
            $arraySaldoAcumuladoDespesa[substr($row['id_subgrupo'], 0,2)][$row['mes_competencia']] -= ($row['mes_competencia'] <= date('m')) ? $row['valor'] : 0;
            
            if($row['status'] == 2)
                $arrayTotalSubGrupos[$row['id_subgrupo']] += $row['valor'];

            if(($check['previsto'] && $row['status'] == 1) || ($check['realizado'] && $row['status'] == 2))
                $arrayTotalGrupos[$row['status']][$row['mes_competencia']] += $row['valor'];

            $arrayGrupos[$row['id_grupo']]['mes'][$row['mes_competencia']][$row['status']] += $row['valor'];
            $arrayGrupos[$row['id_grupo']][$row['status']] += $row['valor'];
            $arrayGrupos[$row['id_grupo']]['descricao'] = $row['nome_grupo'];
        }
        
        /**
         * QUERY PARA AS ENTRADAS
         */
        $sql = "
        SELECT A.tipo, SUM(A.valor) valor, B.descricao, B.nome, A.status, MONTH(A.data_vencimento) AS mes_competencia
        FROM entrada A
        LEFT JOIN entradaesaida B ON (A.tipo = B.id_entradasaida)
        WHERE A.`status` NOT IN (0,1) $auxMesAno $auxUnidadeEntrada
        GROUP BY MONTH(A.data_vencimento), A.tipo, A.`status`
        ORDER BY A.tipo;";
        $qry = mysql_query($sql) or die("ERRO SQL ENTRADA: " . mysql_error());
        while($row = mysql_fetch_assoc($qry)){
            $arrayEntradas[$row['tipo']][$row['status']] += $row['valor'];
            $arrayEntradas[$row['tipo']]['descricao'] = $row['nome'];
            $arrayEntradas[$row['tipo']]['mes'][$row['mes_competencia']][$row['status']] = $row['valor'];

            $arraySaldoAcumuladoReceita[$row['tipo']][$row['mes_competencia']] += ($row['mes_competencia'] <= date('m')) ? $row['valor'] : 0;
            
            if(($check['previsto'] && $row['status'] == 1) || ($check['realizado'] && $row['status'] == 2))
            $arrayTotalEntradas[$row['status']][$row['mes_competencia']] += $row['valor'];
        }

        /**
         * QUERY PARA OS ORÇAMENTOS
         */
        $sqlOrcamento = "
        SELECT C.codigo, C.descricao, SUM(C.valor) AS valor, MONTH(ADDDATE(A.inicio, INTERVAL (C.mes-1) MONTH)) mes
        FROM gestao_orcamentos A $auxFROM
        LEFT JOIN gestao_orcamentos_valores C ON (A.id = C.orcamento_id)
        WHERE $auxMes2 $auxWHERE 
        GROUP BY C.codigo, C.mes";
        $qryOrcamento = mysql_query($sqlOrcamento) or die("ERRO SQL ORÇAMENTO: " . mysql_error());
        while($rowOrcamento = mysql_fetch_assoc($qryOrcamento)){
            if(!in_array($rowOrcamento['codigo'], $arraySubGrupos) && $rowOrcamento['valor'] > 0 && strlen($rowOrcamento['codigo']) > 3)
                $arraySubGrupos[substr($rowOrcamento['codigo'], 0,2)][$rowOrcamento['codigo']]['descricao'] = $rowOrcamento['descricao'];

            if(!in_array($rowOrcamento['codigo'], $arrayGrupos) && $rowOrcamento['valor'] > 0 && strlen($rowOrcamento['codigo']) < 3)
                    $arrayGrupos[$rowOrcamento['codigo']]['descricao'] = $rowOrcamento['descricao'];

            $arrayOrcamento[$rowOrcamento['codigo']] += $rowOrcamento['valor'];
            $arrayOrcamentoMes[$rowOrcamento['codigo']][$rowOrcamento['mes']] = $rowOrcamento['valor'];
            
            $arraySaldoAcumuladoDespesa[$rowOrcamento['codigo']][$rowOrcamento['mes']] += (($rowOrcamento['mes'] < 3) ? $rowOrcamento['mes']+12 : $rowOrcamento['mes'] <= date('m')) ? $rowOrcamento['valor'] : 0;
            if(strlen($rowOrcamento['codigo']) > 3)
//                $arraySaldoAcumuladoDespesa[$rowOrcamento['codigo']][$rowOrcamento['mes']] += ((($rowOrcamento['mes'] < 3) ? $rowOrcamento['mes']+12 : $rowOrcamento['mes']) <= date('m')) ? $rowOrcamento['valor'] : 0;
                $arraySaldoAcumuladoDespesaTotal[$rowOrcamento['codigo']] += ((($rowOrcamento['mes'] < 3) ? $rowOrcamento['mes']+12 : $rowOrcamento['mes']) <= date('m')) ? $rowOrcamento['valor'] : 0;
            
            if($check['provisionado'])
                if(strlen($rowOrcamento['codigo']) > 3)
                    $arrayTotalOrcamento[$rowOrcamento['mes']] += $rowOrcamento['valor'];
        }
        
        $arrayMeses = array(3,4,5,6,7,8,9,10,11,12,1,2);
        foreach ($arrayMeses as $ind => $mes) {
            foreach ($arraySaldoAcumuladoDespesa as $key => $value) {
                for($i=0; $i<=$ind; $i++){
                    $arraySaldoAcumuladoDespesaMes[$key][$mes] += $arraySaldoAcumuladoDespesa[$key][$arrayMeses[$i]];
                }
            }
            foreach ($arraySaldoAcumuladoReceita as $key => $value) {
                for($i=0; $i<=$ind; $i++){
                    $arraySaldoAcumuladoReceitaMes[$key][$mes] += $arraySaldoAcumuladoReceita[$key][$arrayMeses[$i]];
                }
            }
        }
        
        ksort($arraySubGrupos);
        ksort($arrayGrupos);
        
        return array(
            'arrayOrcamento' => $arrayOrcamento,
            'arrayOrcamentoMes' => $arrayOrcamentoMes,
            'arrayTotalOrcamento' => $arrayTotalOrcamento,

            'arrayEntradas' => $arrayEntradas,
            'arrayTotalEntradas' => $arrayTotalEntradas,

            'arrayGrupos' => $arrayGrupos,
            'arraySubGrupos' => $arraySubGrupos,
            'arrayTotalGrupos' => $arrayTotalGrupos,
            'arrayTotalSubGrupos' => $arrayTotalSubGrupos,
            
            'arraySaldoAcumuladoDespesa' => $arraySaldoAcumuladoDespesa,
            'arraySaldoAcumuladoDespesaMes' => $arraySaldoAcumuladoDespesaMes,
            'arraySaldoAcumuladoDespesaTotal' => $arraySaldoAcumuladoDespesaTotal,
            
            'arraySaldoAcumuladoReceitaMes' => $arraySaldoAcumuladoReceitaMes
        );
    }
}