<?php

/**
 * Description of EventoHtmlClass
 *
 * @author Leonardo
 */
class EventoView {

    public $letras;
    public $regiao;
    public $projeto;
    public $rhstatusList;

    public function __construct($regiao = NULL, $projeto = NULL) {
        $this->regiao = $regiao;
        $this->projeto = $projeto;
        $this->letras = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
    }

    private function queryStatus() {
        if (empty($this->rhstatusList)) {
            $cond_proj = (isset($this->projeto) && !empty($this->projeto) && $this->projeto != '-1') ? "AND B.id_projeto = {$this->projeto}" : "";
            $qr_btn_status = "SELECT A.especifica, A.codigo, A.tipo, COUNT(A.codigo) AS total
                        FROM rhstatus AS A 
                        INNER JOIN rh_clt AS B ON(A.codigo = B.status)
                        WHERE B.id_regiao = '{$this->regiao}' $cond_proj AND A.status_reg = 1 AND A.codigo NOT IN(40,90,60,101,81,62,61,64,66,65,63)
                        GROUP BY A.codigo
                        ORDER BY A.especifica ";
            echo "<!-- $qr_btn_status -->";
            $sql_btn_status = mysql_query($qr_btn_status) or die("Erro ao selecionar eventos");
            while ($row_status = mysql_fetch_assoc($sql_btn_status)) {
                $this->rhstatusList[$row_status['codigo']] = $row_status;
            }
        }
    }

    public function abasEventos($rhstatus) {
        $this->queryStatus();
        $cond_proj = (isset($this->projeto) && !empty($this->projeto) && $this->projeto != '-1') ? "AND B.id_projeto = {$this->projeto}" : "";
        $html = "<ul class=\"nav nav-tabs\" role=\"tablist\">\n";
        foreach ($this->rhstatusList as $row_status) {
            $ativo = ($rhstatus == $row_status['codigo']) ? "active" : "";
            $html .= "<li class=\"text-center {$ativo}\"><a href=\"#{$row_status['codigo']}\" title=\"Visualizar Participantes em {$row_status['especifica']}\" data-status=\"{$row_status['codigo']}\" class=\"tab-status\">{$row_status['especifica']} <span class=\"badge\">{$row_status['total']}</span></a></li>\n";
        }
        $html .= "</ul>\n";
//        print_r($this->rhstatusList);
        return $html;
    }

    public function paginacao($pagina, $rhstatus) {
        $this->queryStatus();
        $posicao = ($pagina - 1) * 100; // usado no limit da lista de funcionários
        $back_disable = ($pagina > 1) ? "" : "class=\"disabled\"";
        $back_page = $pagina - 1;
        $back_data_page = ($pagina > 1) ? $back_page : null;

        $posicao_ini = $posicao + 1;
        $posicao_fim = ($posicao + 100 <= $this->rhstatusList[$rhstatus]['total']) ? $posicao + 100 : $this->rhstatusList[$rhstatus]['total'];

        $next_disable = ($posicao + 100 <= $this->rhstatusList[$rhstatus]['total']) ? "" : "class=\"disabled\"";
        $next_page = $pagina + 1;
        $next_data_page = ($posicao + 100 <= $this->rhstatusList[$rhstatus]['total']) ? $pagina + 1 : null;

        $html_paginas = "           <ul class=\"pagination\">\n
                <li $back_disable><a href=\"#\" class=\"page\" title=\"Voltar para pág. $back_page\" data-page=\"$back_data_page\">&laquo;</a></li>\n
                    <li class=\"disabled\"><a href=\"#\">$posicao_ini à $posicao_fim de {$this->rhstatusList[$rhstatus]['total']}</a></li>\n
                <li $next_disable><a href=\"#\" class=\"page\" title=\"Avançar para pág. $next_page\" data-page=\"$next_data_page\">&raquo;</a></li>\n
            </ul>\n";

        $html_letras = "<ul class=\"pagination\">\n";
        for ($i = 0; $i < count($this->letras); $i++) {
            $html_letras .= "<li><a href=\"#\" class=\"inicial\" title=\"Listar funcionários com nomes iniciados em " . $this->letras[$i] . "\">" . $this->letras[$i] . "</a></li>\n";
        }
        $html_letras .= "</ul>\n";

        return "<div class=\"painel-paginacao\">\n$html_paginas\n$html_letras\n</div><!-- /.painel-paginacao -->";
    }

}
