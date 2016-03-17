<?php

class WorkbookMiddleware {

	protected $filename;
	protected $palette = [];
	protected $formats = [];
	protected $worksheets = [];

	protected $phpExcel;
	protected $workSheetObj;
	protected $indexsheet;

	/**
	 * Construct of the class
	 * @param string $fileName
	 */
	public function __construct($filename) {
		$this->filename = $filename;
		$this->indexsheet = 0;

		$this->phpExcel = new PHPExcel();
		$this->setDocumentProperties($phpExcel);
	}

	/**
	 *
	 * @todo implement?
	 */
	public function setVersion() {

	}

	/**
	 * This method is copy of Spreadsheet_Excel_Writer
	 * @param string $index
	 * @param string $red
	 * @param string $green
	 * @param string $blue
	 * @return int
	 */
	public function setCustomColor($index, $red, $green, $blue) {
		// Check that the colour index is the right range
		if ($index < 8 or $index > 64) {
			return $this->raiseError('Color index $index outside range: 8 <= index <= 64');
		}

		// Check that the colour components are in the right range
		if (($red   < 0 or $red   > 255) ||
				($green < 0 or $green > 255) ||
				($blue  < 0 or $blue  > 255))
		{
			return $this->raiseError('Color component outside range: 0 <= color <= 255');
		}

		$index -= 8; // Adjust colour index (wingless dragonfly)

		// Set the RGB value
		$this->palette[$index] = array($red, $green, $blue, 0);

		return ($index + 8);
	}

	/**
	 * Return a new FormatMiddleware
	 * @param array $properties
	 * @return FormatMiddleware
	 */
	public function addFormat($properties = array()) {
		$format = new FormatMiddleware($properties);

		return $format;
	}

	/**
	 * Add a new WorksheetMiddleware
	 * @param string $name
	 * @return WorksheetMiddleware
	 */
	public function addWorksheet($name = '') {
		if ($this->indexsheet <= 0) {
			$this->workSheetObj = $this->phpExcel->getActiveSheet();
			$this->indexsheet++;
		} else {
			$this->workSheetObj = $this->phpExcel->createSheet($this->indexsheet);
			$this->indexsheet++;
		}

		$this->workSheetObj->setTitle(utf8_encode($name));

		return $this;
	}

	/**
	 * Build and download the document
	 * @param string $filename
	 */
	public function send($filename) {
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1'); // IE 9
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		header('Cache-Control: cache, must-revalidate');
		header('Pragma: public');

		$this->phpExcel->setActiveSheetIndex(0);

		$writer = PHPExcel_IOFactory::createWriter($this->phpExcel, 'Excel5');
		$writer->setPreCalculateFormulas(true);

		$writer->save('php://output');
	}

	/**
	 *
	 *
	 */
	public function close() {
		unset($this->workSheetObj);
		unset($this->phpExcel);
	}

	/**
	 * Set document properties
	 * @param PHPExcel $phpExcel
	 */
	private function setDocumentProperties($phpExcel) {
		$this->phpExcel->getProperties()->setCreator('LemonTech')
							 ->setLastModifiedBy('LemonTech')
							 ->setTitle($filename)
							 ->setSubject($filename)
							 ->setDescription('Reporte generado por The TimeBilling, http://thetimebilling.com/.')
							 ->setKeywords('timebilling lemontech');
	}

	/**
	 * Add formats to cells
	 * @param array $formats
	 * @param string $cellCode
	 *
	 * @todo Implement the border format
	 */
	private function setFormat($format, $cellCode) {
		// var_dump($format);
		foreach ($format->getElements() as $key => $formatValue) {
			if (!is_null($formatValue)) {
				switch ($key) {
					case 'size':
						$this->workSheetObj->getStyle($cellCode)->getFont()->setSize($formatValue);
						break;
					case 'align':
						$this->workSheetObj->getStyle($cellCode)->getAlignment()->setHorizontal($formatValue);
						break;
					case 'valign':
						$this->workSheetObj->getStyle($cellCode)->getAlignment()->setVertical($formatValue);
						break;
					case 'bold':
						$this->workSheetObj->getStyle($cellCode)->getFont()->setBold($formatValue);
						break;
					case 'italic':
						$this->workSheetObj->getStyle($cellCode)->getFont()->setItalic($formatValue);
						break;
					case 'color':
						$this->workSheetObj->getStyle($cellCode)->getFont()->getColor()->setARGB($formatValue);
						break;
					case 'locked':
						if ($value) {
							$this->workSheetObj->getStyle($cellCode)->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_PROTECTED);
						}
						break;
					case 'top':
						$this->workSheetObj->getStyle($cellCode)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						break;
					case 'bottom':
						$this->workSheetObj->getStyle($cellCode)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						break;
					case 'fgcolor':
						if (is_int($formatValue) && ($formatValue > 8 && $formatValue < 64)) {
							// the subtraction is for continue the logic of the method setCustomColor
							$rgb = $this->palette[$formatValue - 8];

							$this->workSheetObj->getStyle($cellCode)
											->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
											->getStartColor()->setRGB($this->rgb2hex($rgb));
						}
						break;
					case 'textwrap':
						$this->workSheetObj->getStyle($cellCode)->getAlignment()->setWrapText($formatValue);
						break;
					case 'numformat':
						$this->workSheetObj->getStyle($cellCode)->getNumberFormat()->setFormatCode($formatValue);
						break;
					case 'border':
						// TODO: Implement
						break;
				}
			}
		}
	}

	/**
	 * Add formats to cells
	 * @param PHPExcel_Worksheet $workSheet
	 *
	 * @todo Implement this method
	 */
	private function setPixmap($workSheet) {
		// TODO: implement
	}

	/**
	 * Convert a RGB code to hexadecimal
	 * @param string $rgb
	 * @return hexadecimal code
	 */
	private function rgb2hex($rgb) {
		if (is_array($rgb)) {
			$hex = '';
			$hex .= str_pad(dechex($rgb[0]), 2, '0', STR_PAD_LEFT);
			$hex .= str_pad(dechex($rgb[1]), 2, '0', STR_PAD_LEFT);
			$hex .= str_pad(dechex($rgb[2]), 2, '0', STR_PAD_LEFT);

			return $hex;
		} else {
			return null;
		}
	}

	/* Worksheet Methods*/

	/**
	 * Set the type of papper
	 * http://www.osakac.ac.jp/labs/koeda/tmp/phpexcel/Documentation/API/PHPExcel_Worksheet/PHPExcel_Worksheet_PageSetup.html#methodsetPaperSize
	 * @param string $size
	 */
	public function setPaper($size = 0) {
		$this->workSheetObj->getPageSetup()->setPaperSize($size);
	}

	/**
	 * Hide printed Gridlines
	 */
	public function hideGridlines() {
		$this->workSheetObj->setPrintGridlines(false);
	}

	/**
	 * Hide document Gridlines
	 */
	public function hideScreenGridlines() {
		$this->workSheetObj->setShowGridlines(false);
	}

	/**
	 * Set document margins
	 * @param float $margin
	 */
	public function setMargins($margin) {
		$this->workSheetObj->getPageMargins()
											->setTop($margin)
											->setRight($margin)
											->setLeft($margin)
											->setBottom($margin);
	}

	/**
	 * Set fit to pages
	 * @param int $width
	 * @param int $height
	 */
	public function fitToPages($width, $height) {
		$this->workSheetObj->getPageSetup()
											->setFitToPage(true)
											->setFitToWidth($width)
											->setFitToHeight($height);
	}

	/**
	 * Add a column element (properties)
	 * @param int $firstcol
	 * @param int $lastcol
	 * @param int $width
	 * @param FormatMiddleware $format
	 * @param boolean $hidden
	 * @param int $level
	 */
	public function setColumn($firstcol, $lastcol, $width, $format = null, $hidden = false, $level = 0) {
		$column = PHPExcel_Cell::stringFromColumnIndex($firstcol);

		$this->workSheetObj->getColumnDimension($column)->setWidth($width);

		if ($hidden) {
			$this->workSheetObj->getColumnDimension($column)->setVisible(false);
		}

		//TODO: format and level.
	}

	/**
	 * Add a row element (properties)
	 * @param int $row
	 * @param int $height
	 * @param FormatMiddleware $format
	 * @param boolean $hidden
	 * @param int $level
	 */
	public function setRow($row, $height, $format = null, $hidden = false, $level = 0) {
		$row = $row + 1;

		$this->workSheetObj->getRowDimension($row)->setRowHeight($height);

		if ($hidden) {
			$this->workSheetObj->getRowDimension($row)->setVisible(false);
		}

		//TODO: format and level.
	}

	/**
	 * Add cells merged
	 * @param int $first_row
	 * @param int $first_col
	 * @param int $last_row
	 * @param int $last_col
	 */
	public function mergeCells($first_row, $first_col, $last_row, $last_col) {
		$cellsMerged =
					PHPExcel_Cell::stringFromColumnIndex($first_col).($first_row + 1) .
					":" .
					PHPExcel_Cell::stringFromColumnIndex($last_col).($last_row + 1)
					;

		$this->workSheetObj->mergeCells($cellsMerged);
	}

	/**
	 * Add data to cell
	 * @param int $row
	 * @param int $col
	 * @param string $token
	 * @param FormatMiddleware $format
	 */
	public function write($row, $col, $token, $format = null) {
		$cellCode = PHPExcel_Cell::stringFromColumnIndex($col).($row + 1);

		$this->workSheetObj->setCellValue(
				$cellCode,
				utf8_encode($token)
		);

		if (!is_null($format)) {
			$this->setFormat($format, $cellCode);
		}
	}

		/**
	 * Add number to cell
	 * @param int $row
	 * @param int $col
	 * @param number $num
	 * @param FormatMiddleware $format
	 */
	public function writeNumber($row, $col, $num, $format = null) {
		$cellCode = PHPExcel_Cell::stringFromColumnIndex($col).($row + 1);

		$this->workSheetObj->setCellValue(
				$cellCode,
				utf8_encode($num)
		);

		if (!is_null($format)) {
			$this->setFormat($format, $cellCode);
		}
	}

	/**
	 * Add formula to cell
	 * @param int $row
	 * @param int $col
	 * @param string $formula
	 * @param FormatMiddleware $format
	 */
	public function writeFormula($row, $col, $formula, $format = null) {
		$cellCode = PHPExcel_Cell::stringFromColumnIndex($col).($row + 1);

		$formula = str_replace(';', ',', $formula);
		$this->workSheetObj->getCell($cellCode)->setDataType(PHPExcel_Cell_DataType::TYPE_FORMULA);

		$this->workSheetObj->setCellValue(
				$cellCode,
				utf8_encode($formula)
		);

		if (!is_null($format)) {
			$this->setFormat($format, $cellCode);
		}
	}
}
