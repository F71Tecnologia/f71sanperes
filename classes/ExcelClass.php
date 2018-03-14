<?php
//Application::using("system.vendor.pear.Spreadsheet.Excel.Writer","");

class Application_Control_Report_Excel extends Spreadsheet_Excel_Writer
{
	private $negativeValueStyle;
	private $positiveValueStyle;
	private $textStyle;

	private $negativeValueBoldStyle;
	private $positiveValueBoldStyle;
	private $textBoldStyle;
	private $headerStyle;
	
	private $alphabet = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","U","V","W","X","Y","Z");
	
	function __construct($filename = '')
	{
		parent::Spreadsheet_Excel_Writer($filename);
		
		ob_clean();
		
		//Estilos
        //Valor Negativo
        $this->negativeValueStyle = &$this->addFormat();
        $this->negativeValueStyle->setAlign("right");
        $this->negativeValueStyle->setSize(8);
        $this->negativeValueStyle->setNumFormat('#,##0.00;-#,##0.00');
        $this->negativeValueStyle->setBorder(1);
        $this->negativeValueStyle->setColor('red');

        //Valor Positivo
        $this->positiveValueStyle = &$this->addFormat();
        $this->positiveValueStyle->setAlign("right");
        $this->positiveValueStyle->setSize(8);
        $this->positiveValueStyle->setNumFormat('#,##0.00;-#,##0.00');
        $this->positiveValueStyle->setBorder(1);
        $this->positiveValueStyle->setColor('green');

        //Texto
        $this->textStyle = &$this->addFormat();
        $this->textStyle->setSize(8);
        $this->textStyle->setBorder(1);

        //Texto negrito
        $this->textBoldStyle = &$this->addFormat();
        $this->textBoldStyle->setSize(8);
        $this->textBoldStyle->setBold();
        $this->textBoldStyle->setBorder(1);

        //Texto negrito negativo
        $this->negativeValueBoldStyle = &$this->addFormat();
        $this->negativeValueBoldStyle->setSize(8);
        $this->negativeValueBoldStyle->setBold();
        $this->negativeValueBoldStyle->setBorder(1);
        $this->negativeValueBoldStyle->setNumFormat('#,##0.00;-#,##0.00');
        $this->negativeValueBoldStyle->setAlign("right");
        $this->negativeValueBoldStyle->setColor('red');

        //Texto negrito positivo
        $this->positiveValueBoldStyle = &$this->addFormat();
        $this->positiveValueBoldStyle->setSize(8);
        $this->positiveValueBoldStyle->setBold();
        $this->positiveValueBoldStyle->setBorder(1);
        $this->positiveValueBoldStyle->setNumFormat('#,##0.00;-#,##0.00');
        $this->positiveValueBoldStyle->setAlign("right");
        $this->positiveValueBoldStyle->setColor('green');

        //Cabeçalho
        $this->headerStyle = &$this->addFormat();
        $this->headerStyle->setBold();
        $this->headerStyle->setSize(8);
        $this->headerStyle->setColor('white');
        $this->headerStyle->setFgColor('brown');
        $this->headerStyle->setBorder(1);
        $this->headerStyle->setAlign('center');
        $this->headerStyle->setTextWrap();
        $this->headerStyle->setVAlign('vcenter');
	}
	
	/**
	 * @return the $negativeValueStyle
	 */
	public function &getNegativeValueStyle() {
		return $this->negativeValueStyle;
	}

	/**
	 * @return the $positiveValueStyle
	 */
	public function &getPositiveValueStyle() {
		return $this->positiveValueStyle;
	}

	/**
	 * @return the $textStyle
	 */
	public function &getTextStyle() {
		return $this->textStyle;
	}
	/**
	 * @return the $negativeValueBoldStyle
	 */
	public function &getNegativeValueBoldStyle() {
		return $this->negativeValueBoldStyle;
	}

	/**
	 * @return the $positiveValueBoldStyle
	 */
	public function &getPositiveValueBoldStyle() {
		return $this->positiveValueBoldStyle;
	}

	/**
	 * @return the $textBoldStyle
	 */
	public function &getTextBoldStyle() {
		return $this->textBoldStyle;
	}

	/**
	 * @return the $headerStyle
	 */
	public function &getHeaderStyle() {
		return $this->headerStyle;
	}

	public function getLetter($index)
	{
		return $this->alphabet[$index];
	}
}