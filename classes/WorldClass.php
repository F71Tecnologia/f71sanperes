<?php

/**
 *
 * @author Lucas Praxedes
 * @date 22/05/2017
 * 
 */
class WorldClass {

    public function carregaUsuario() {

        $result = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '{$_COOKIE['logado']}'");

        $row = mysql_fetch_object($result);

        return $row;
    }

    public function print_array($array) {

        echo '<pre>';
        print_r($array);
        echo '</pre>';

        return true;
    }

    /**
     * @author Lucas Praxedes (22/05/2017)
     * 
     * Método que prepara um objeto/array para 
     * utilizar com o método "montaSelect".
     * 
     */
    public function toSelect($object, $columnId = '', $columnName = '', $idInName = 1) {

        $return[-1] = '« SELECIONE »';

        if (is_object($object) || is_array($object)) {

            foreach ($object as $value) {

                if ($idInName) {

                    $return[$value->$columnId] = "{$value->$columnId} - {$value->$columnName}";
                } else {

                    $return[$value->$columnId] = $value->$columnName;
                }
            }
        }

        return $return;
    }

    /**
     * @author Lucas Praxedes (22/05/2017)
     * 
     * Método que cria um select a partir de um 
     * objeto/array de elementos.
     * 
     */
    public function montaSelect($optObject, $selected, $attrTag) {

        $html = "<select ";

        if (is_object($attrTag) || is_array($attrTag)) {

            foreach ($attrTag as $key => $val) {

                $html .= $key . "=\"" . $val . "\" ";
            }
        } else {

            $html .= $attrTag;
        }

        $html .= ">";

        if (is_object($optObject) || is_array($optObject)) {

            foreach ($optObject as $key => $val) {

                if (!empty($selected) && $selected == $key) {

                    $selected = " selected=\"selected\"";
                } else {

                    $selected = "";
                }

                $html .= "<option value=\"" . $key . "\"$selected>" . $val . "</option>";
            }
        }

        $html .= "</select>";

        return $html;
    }

    public function montaCabecalhoNovo($regioes, $masters, $usuario, $file) {

        $regiaoSelected = $regioes[$usuario->id_regiao];
        $masterSelected = $masters[$usuario->id_master];

        unset($regioes[$usuario->id_regiao]);
        unset($regioes['-1']);

        unset($masters[$usuario->id_master]);
        unset($masters['-1']);

        $return->regiaoSelected = $regiaoSelected;
        $return->masterSelected = $masterSelected;
        $return->regioes = (object) $regioes;
        $return->masters = (object) $masters;

        $defaultPath = null;
        $url = explode("/intranet/", $file);

        $urlCount = substr_count($url[1], '/');

        for ($i = 0; $i < $urlCount; $i++) {

            $defaultPath .= "../";
        }

        $return->defaultPath = $defaultPath;
        $return->fullRootPath = $_SERVER['HTTP_HOST'] . '/intranet/';

        return $return;
    }

    public function getMasters($user = null) {

        if ($user === null) {

            $usuario = $this->carregaUsuario();
        } else {

            $usuario->id_funcionario = $user;
        }

        $sqlMasters = "SELECT A.id_master,B.nome
                        FROM funcionario_regiao_assoc AS A
                        INNER JOIN master AS B ON (A.id_master = B.id_master)
                        WHERE id_funcionario = {$usuario->id_funcionario} AND B.status = 1 GROUP BY A.id_master";
        $queryMasters = mysql_query($sqlMasters);

        while ($rowMasters = mysql_fetch_object($queryMasters)) {

            $arrMasters[$rowMasters->id_master] = $rowMasters;
        }

        return (object) $arrMasters;
    }

    public function getRegioes($user = null, $master = null, $key = "-1") {

        if ($user === null && $master === null) {

            $usuario = $this->carregaUsuario();
        } else {

            $usuario->id_funcionario = $user;
            $usuario->id_master = $master;
        }

        $sqlProjetos = "SELECT A.id_regiao,B.regiao FROM funcionario_regiao_assoc AS A
                        LEFT JOIN regioes AS B ON (A.id_regiao = B.id_regiao)
                        WHERE   id_funcionario = {$usuario->id_funcionario} AND 
                        A.id_master = {$usuario->id_master}";
        $queryRegioes = mysql_query($sqlProjetos);

        while ($rowRegioes = mysql_fetch_object($queryRegioes)) {

            $arrRegioes[$rowRegioes->id_regiao] = $rowRegioes;
        }

        return (object) $arrRegioes;
    }

    public function getProjetos($master = null, $regiao = null) {

        $where = " WHERE id_regiao = {$regiao} AND id_master = {$master} AND status_reg = '1' ";

        $sqlProjetos = "SELECT * FROM projeto $where";
        $queryProjetos = mysql_query($sqlProjetos);

        while ($rowProjetos = mysql_fetch_object($queryProjetos)) {

            $arrProjetos[$rowProjetos->id_projeto] = $rowProjetos;
        }

        return (object) $arrProjetos;
    }

    public function getTiposSanguineos() {

        $sqlTs = "SELECT * FROM tipo_sanguineo";
        $queryTs = mysql_query($sql_ts);

        while ($rowTs = mysql_fetch_object($queryTs)) {

            $arrTipoSang[$rowTs->id_tiposanguineo] = $rowTs;
        }

        return (object) $arrTipoSang;
    }

    public function getEstadosCivis() {

        $sqlEstCivil = "SELECT * FROM estado_civil";
        $queryEstCivil = mysql_query($sqlEstCivil);

        while ($rowEstCivil = mysql_fetch_object($queryEstCivil)) {

            $arrEstadoCivil[$rowEstCivil->id_estado_civil] = $rowEstCivil;
        }

        return (object) $arrEstadoCivil;
    }

    public function getEscolaridades() {

        $sqlEscolaridade = "SELECT * FROM escolaridade WHERE status = 'on'";
        $queryEscolaridade = mysql_query($sqlEscolaridade);

        while ($rowEscolaridade = mysql_fetch_object($queryEscolaridade)) {

            $arrEscolaridade[$rowEscolaridade->id] = $rowEscolaridade;
        }

        return (object) $arrEscolaridade;
    }

    public function getBancos($regiao, $projeto) {

        $sqlBanco = "SELECT * FROM bancos WHERE id_regiao = '$regiao' AND id_projeto = '$projeto' AND status_reg = '1'";
        $queryBanco = mysql_query($sqlBanco);

        while ($rowBanco = mysql_fetch_object($queryBanco)) {

            $arrBanco[$rowBanco->id_banco] = $rowBanco;
        }

        return (object) $arrBanco;
    }

    public function getListaBancos() {

        $sqlListaBancos = "SELECT * FROM listabancos";
        $queryListaBancos = mysql_query($sqlListaBancos);

        $arrListaBancos[0] = 'Nenhum Banco';
        while ($rowListaBancos = mysql_fetch_object($queryListaBancos)) {

            $arrListaBancos[$rowListaBancos->id_lista] = $rowListaBancos;
        }

        return (object) $arrListaBancos;
    }

    public function dateCompare($date1, $date2) {
        if (strtotime($date1) > strtotime($date2)) {
            $return = 1;
        } else if (strtotime($date1) < strtotime($date2)) {
            $return = -1;
        } elseif (strtotime($date1) == strtotime($date2)) {
            $return = 0;
        }

        return $return;
    }

    public function getAllMunicipios($uf) {

        $sql = "SELECT * FROM municipios A WHERE sigla LIKE '$uf' ORDER BY municipio";
        $query = mysql_query($sql);

        while ($row = mysql_fetch_assoc($query)) {
            $arr[] = $row;
        }

        return $arr;
    }

}
