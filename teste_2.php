<?php
/**************************/
/* CLASSE RESPONSÁVEL PELO PREENCHIMENTO DE ARQUIVOS RTF E GERAÇÃO DE PDF */
/* CRIADO EM: 18/05/2007 | 10h22min /
/* AUTOR: Patrick Espake /
/* SITE: www.patrickespake.com /
/* E-MAIL: patrickespake@gmail.com /
/**************************/

class Rtf2Pdf
{
	// Atributos.
	private $template_file;
	private $rtf_file_name;
	private $rtf_file;
	private $pdf_file_name;
	private $pdf_file;
	private $dir_files;
	private $content_file;
	private $vars_and_attributes;

	// Métodos sets e gets.
	function setTemplateFile($template_file) { $this->template_file = $template_file; }
	function getTemplateFile() { return $this->template_file; }

	function setRtfFileName($rtf_file_name) { $this->rtf_file_name = $rtf_file_name; }
	function getRtfFileName() { return $this->rtf_file_name; }

	function setRtfFile($rtf_file) { $this->rtf_file = $rtf_file; }
	function getRtfFile() { return $this->rtf_file; }

	function setPdfFileName($pdf_file_name) { $this->pdf_file_name = $pdf_file_name; }
	function getPdfFileName() { return $this->pdf_file_name; }

	function setPdfFile($pdf_file) { $this->pdf_file = $pdf_file; }
	function getPdfFile() { return $this->pdf_file; }

	function setDirFiles($dir_files) { $this->dir_files = $dir_files; }
	function getDirFiles() { return $this->dir_files; }

	function setContentFile($content_file) { $this->content_file = $content_file; }
	function getContentFile() { return $this->content_file; }

	function setVarsAndAttributes($var, $attribute) { $this->vars_and_attributes[] = array($var, $attribute); }
	function getVarsAndAttributes() { return $this->vars_and_attributes; }

	// Método construtor.
	function Rtf2Pdf()
	{
		// set_time_limit(0) reinicia o contador do limite do tempo de execução do script a partir de zero.
		set_time_limit(0);
	}

	// Método de criação do arquivo pdf.
	function makePdf()
	{
		// Verifica se o arquivo de template existe no diretório.
		if(file_exists($this->getDirFiles() . $this->getTemplateFile()))
		{
			// Abre o arquivo de template e obtém o seu conteúdo.
			$fp = fopen($this->getDirFiles() . $this->getTemplateFile(), "r");
			$this->setContentFile(fread($fp, filesize($this->getDirFiles() . $this->getTemplateFile())));
			fclose($fp);

			// $this->makeRtf() cria um arquivo temporário rtf com os dados preenchidos, seguindo o modelo do template. Retorna true.
			if($this->makeRtf())
			{
				// Obtém o nome do arquivo rtf temporário.
				$this->setRtfFileName($this->getRtfFile());
				// Define os caminhos dos arquivos rtf e pdf.
				$rtf_file = "file:///" . $this->getDirFiles() . $this->getRtfFile();
				$pdf_file = "file:///" . $this->getDirFiles() . $this->getPdfFileName();
				$this->setRtfFile($rtf_file);
				$this->setPdfFile($pdf_file);

				// Cria o arquivo PDF. Retorna true.
				if($this->createPdf())
				{
					// Apaga o arquivo rtf temporário gerado.
					$this->deleteRtfTmp();

					// Retona o nome do PDF, o nome do arquivo rtf, diretório dos arquivos e nome do arquivo rtf template.
					return array($this->getPdfFileName(), $this->getRtfFileName(), $this->getDirFiles(), $this->getTemplateFile());
				}
				else
				die("Não foi possível geral o arquivo pdf, tente novamente.");
			}
		}
		else
		die("O arquivo de template especificado não existe.".$this->getDirFiles() . $this->getTemplateFile());
	}

	// Método de criação do arquivo rtf.
	function makeRtf()
	{
		$contentFile = $this->getContentFile();
		$varsAndAtrributes = $this->getVarsAndAttributes();

		// Substitui os valores no arquivo de template.
		for($i = 0; $i < count($varsAndAtrributes); $i++)
		{
			$contentFile = str_replace($varsAndAtrributes[$i][0], $varsAndAtrributes[$i][1], $contentFile);
		}

		// Cria o arquivo rtf temporário com as informações preenchidas, seguindo o modelo do template.
		$this->setContentFile($contentFile);
		$this->setRtfFile(md5(date("Yms")) . ".rtf");
		$fp = fopen($this->getDirFiles() . $this->getRtfFile(), "w+");
		fwrite($fp, $this->getContentFile());
		fclose($fp);
		return true;
	}

	// Método de criação das propriedades do OpenOffice.org
	function makePropertyValue($name, $value, $osm)
	{
		$oStruct = $osm->Bridge_GetStruct("com.sun.star.beans.PropertyValue");
		$oStruct->Name = $name;
		$oStruct->Value = $value;
		return $oStruct;
	}

	// Método de transformação do arquivo rtf para arquivo pdf.
	function createPdf()
	{
		// Obtém os caminhos do arquivo rtf e o caminho para criação do arquivo pdf.
		$rtf_url = $this->getRtfFile();
		$output_url = $this->getPdfFile();

		// COM (Component Object Model) estabelece a interação em o PHP e o OpenOffice.org.
		$osm = new COM("com.sun.star.ServiceManager") or die("O OpenOffice.org não está instalado.");

		// Carrega o OpenOffice.org em modo invisível.
		$args = array($this->makePropertyValue("Hidden", true, $osm));

		// Carrega o objeto Desktop das APIs do OpenOffice.org.
		$oDesktop = $osm->createInstance("com.sun.star.frame.Desktop");

		// Abre o arquivo rtf temporário no OpenOffice.org.
		$oWriterRtf = $oDesktop->loadComponentFromURL($rtf_url, "_blank", 0, $args);

		// Transforma o arquivo rtf temporário em PDF.
		$export_args = array($this->makePropertyValue("FilterName", "writer_pdf_Export", $osm));

		// Grava o arquivo PDF.
		$oWriterRtf->storeToURL($output_url,$export_args);

		// Fecha o arquivo temporário rtf, mas o OpenOffice.org continua carregado na memória.
		$oWriterRtf->close(true);
		return true;
	}

	// Método que apaga o arquivo temporário rtf.
	function deleteRtfTmp()
	{
		if(unlink($this->getDirFiles() . $this->getRtfFileName()))
		return true;
		else
		echo "Não foi possível excluir o arquivo rtf temporário.";
	}
}


// Instância o objeto.
$rtf2pdf = new Rtf2Pdf();

// Define o nome do arquivo de template.
$rtf2pdf->setTemplateFile("teste.rtf");

// Define o diretório onde está o template.rtf e onde vai ser gerado o arquivo PDF.
$rtf2pdf->setDirFiles("rh/rpa/");

// Onde existir no template.rtf << data >>, << nome >>, << texto >>, << endereco >>, << bairro >> e << cidade >>, será substituido pelo segundo parâmetro valor nas declarações abaixo.
$rtf2pdf->setVarsAndAttributes("<NOME>", "Patrick Espake");

// Nome do arquivo pdf que vai ser criado.
$rtf2pdf->setPdfFileName("nome_do_meu_arquivo.pdf");

// Preenche o template e cria o PDF com os dados preenchidos. Retona o nome do PDF, o nome do arquivo rtf, diretório dos arquivos e nome do arquivo rtf template.
$rtf2pdf->makePdf();
?>