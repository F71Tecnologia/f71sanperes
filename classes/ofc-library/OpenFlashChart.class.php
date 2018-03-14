<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once("open-flash-chart.php");

/**
 * Description of OpenFlashChart
 *
 * @author Ramon Lima
 */
class System_Vendor_Flashchart_OpenFlashChart {
    //put your code here

    private $title;
    private $data = array();
    private $styleGrafic;
    private $dataMax = array();
    
    protected $_attrStyle = array();
    protected $_dataColor = array();
    protected $_dataAlpha = array();
    protected $_eixo_Y;
    protected $_eixo_X;
    protected $_bgColor;

    public $chart;


    protected function styleGrafic($style){
        $this->styleGrafic = $style;
    }


    /**
     * Adicione um Título
     * @param String $title Titulo para
     */
    protected function title($array){
        $this->title = new title( $array['text'] );
        $this->title->set_style( $array['style'] );
    }

    protected function datas($array, $format){
        
        $this->chart = new open_flash_chart();
        
        if(is_array($array)){
            foreach($array as $k => $val){

                $this->dataMax = array_merge($val,$this->dataMax);

                if($this->styleGrafic == "bar_sketch"){
                    $this->data[$k] = new $this->styleGrafic( "","",5);
                }else{
                    $this->data[$k] = new $this->styleGrafic();
                }

                $this->data[$k]->set_values($val);

                if(is_object($format[$k])){
                    if($format[$k]->getAlpha()){
                        $this->data[$k]->set_alpha($format[$k]->getAlpha());
                    }

                    if($format[$k]->getColor()){
                        $this->data[$k]->set_colour($format[$k]->getColor());
                    }

                    if($format[$k]->getColors()){
                        $this->data[$k]->set_colours($format[$k]->getColors());
                    }

                    if($format[$k]->getOnShow()){
                        $t = $format[$k]->getOnShow();
                        $this->data[$k]->set_on_show(new bar_on_show($t['type'],$t['cascade'],$t['delay']));
                    }

                    if($format[$k]->getTooltip()){
                        #$bar->set_tooltip( 'Hello<br>#val#' );
                        #$this->tooltip = $sting;
                        $this->data[$k]->set_tooltip($format[$k]->getTooltip());
                    }

                }
                
                //$this->data[$k]->set_colour($this->_dataColor[$k]);
                //$this->data[$k]->set_alpha($this->_dataAlpha[$k]);
                //$this->data[$k]->set_on_show(new bar_on_show("grow-up", 1, 0.5));

                #$this->data[$k]->set_alpha();
                #$this->data[$k]->set_key($text, $size);
                #set_on_click($text)
                //$animation_1= isset($_GET['animation_1'])?$_GET['animation_1']:'pop';
                //$delay_1    = isset($_GET['delay_1'])?$_GET['delay_1']:0.5;
                //$cascade_1    = isset($_GET['cascade_1'])?$_GET['cascade_1']:1;
                //
                //
                //
                #
                //
                #set_tooltip($tip) ("Texto #val#")

                $this->chart->add_element( $this->data[$k] );
            }
        }
        
        $this->chart->set_title( $this->title );

        $this->addProperts();

        /*
        $y = new y_axis_right();
        $y->set_range(0, 30, 5);
        $y->set_label_text("RS #val#,00");
        $y->set_grid_colour("#FF0000");
        $y->set_offset($off);
        $this->chart->set_y_axis($y);*/
        
    }


    private function addProperts(){
        if(!empty($this->_bgColor)){
            $this->chart->set_bg_colour($this->_bgColor);
        }

        if(!empty($this->_eixo_Y) && is_object($this->_eixo_Y)){
            $this->chart->set_y_axis($this->_eixo_Y);
        }else{
            sort($this->dataMax);
            $max = ceil(end($this->dataMax)+2);
            reset($this->dataMax);  
            $min = current($this->dataMax);

            if($min > 0){
                $min = 0;
            }

            if($max < 0){
                $min = 2;
            }
            
            $y = new y_axis();
            $y->set_range($min, $max, floor(($max-$min)/5));
            $this->chart->set_y_axis($y);
        }

        if(!empty($this->_eixo_X) && is_object($this->_eixo_X)){
            $this->chart->set_x_axis($this->_eixo_X);
        }

    }

}
?>
