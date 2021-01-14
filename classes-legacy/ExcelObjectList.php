<?php

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelObjectList
{
    protected $spreadsheet;
    protected $columns = [];
    protected $rowIndex = 2;
    protected $columnsLength = [];

    /**
     * ExcelObjectList constructor.
     * @param array $columns key=>value
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function __construct($columns)
    {
        $this->spreadsheet = new Spreadsheet();
        $this->spreadsheet->setActiveSheetIndex(0);

        $this->columns = $columns;
        $this->columnsLength = array_combine(array_keys($this->columns), array_fill(0, count($this->columns), 0));
        $cc = range('A', 'Z');
        if (count($columns) > count($cc)) {
            throw new RuntimeException('maximum column count limit exceed');
        }
        foreach (array_keys($columns) as $k => $name) {
            $this->spreadsheet->getActiveSheet()->setCellValue($cc[$k] . '1', $columns[$name]);
        }
    }

    /**
     * Добавить строку в список
     * @param array $data Данные
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function addData($data)
    {
        $cc = range('A', 'Z');
        foreach (array_keys($this->columns) as $k => $name) {
            $this->spreadsheet->getActiveSheet()->setCellValueExplicit($cc[$k] . $this->rowIndex, $data[$name], DataType::TYPE_STRING);
            if (mb_strlen($data[$name]) > $this->columnsLength[$name]) {
                $this->columnsLength[$name] = mb_strlen($data[$name]);
            }
        }
        $this->rowIndex++;
    }

    /**
     * @return false|string
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function __toString()
    {
        $cc = range('A', 'Z');
        foreach (array_keys($this->columns) as $k => $name) {
            if ($this->columnsLength[$name] > 90) { // fixed size
                $this->spreadsheet->getActiveSheet()->getColumnDimension($cc[$k])->setWidth(100);
            } else { // autosize
                $this->spreadsheet->getActiveSheet()->getColumnDimension($cc[$k])->setAutoSize(true);
            }
        }
        $this->spreadsheet->getActiveSheet()->getStyle('A1:Z1')->getFont()->setBold(true);
        $this->spreadsheet->getActiveSheet()->freezePane('A2');

        $writer = new Xlsx($this->spreadsheet);
        ob_start();
        $writer->save('php://output');
        return ob_get_clean();
    }

}
