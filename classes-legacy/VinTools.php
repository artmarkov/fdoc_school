<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

class VinTools
{
    const REGEXP = '/([0123456789ABCDEFGHJKLMNPRSTUVWXYZ]{17})/';

    /**
     * Возвращает список vin-номеров найденных в xls
     * @param string $xlsFilename путь до xls с номерами
     * @return array
     */
    public static function parse($xlsFilename)
    {
        ini_set('memory_limit', '1G');
        try {

            $type = IOFactory::identify($xlsFilename);
            if (!in_array($type, ['Xlsx', 'Xls'])) {
                throw new RuntimeException('Ошибка чтения файла vin-номеров, неподдерживаемый тип файла: ' . $type);
            }

            $xls = IOFactory::load($xlsFilename);
            $data = [];
            foreach ($xls->getAllSheets() as $sheet) {
                foreach ($sheet->toArray() as $row) {
                    foreach ($row as $v) {
                        if (preg_match(self::REGEXP, $v, $m)) {
                            $data[] = $m[1];
                        }
                    }
                }
            }
            return array_unique($data);
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            Yii::error('Ошибка чтения файла vin-номеров: ' . $e->getMessage());
            Yii::error($e);
            throw new RuntimeException('Ошибка чтения файла vin-номеров', 0, $e);
        }
    }

    /**
     * Формирует pdf со списком vin
     * @param array $vinList список vin
     * @return string
     */
    public static function makePdf($vinList)
    {
        ini_set('memory_limit', '1G');
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator('');
        $pdf->SetAuthor('');
        $pdf->SetTitle('');
        $pdf->SetSubject('');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->setFontSubsetting(true);
        $pdf->SetFont('dejavusans', '', 11, '', true);

        $pdf->AddPage();
        $txt = <<<EOF
Перечень VIN-кодов транспортных средств, подлежащих отзыву
VIN-код транспортного средства указан в паспорте транспортного средства (ПТС),
свидетельстве о регистрации транспортного средства (СТС) и на самом автомобиле:
смотрите инструкцию по эксплуатации
Предусмотрена возможность поиска VIN-кода посредством нажатия Ctrl + F


EOF;
        sort($vinList);
        $pdf->Write(0, $txt . implode("\n", $vinList), '', 0, '', true, 0, false, false, 0);
        return $pdf->Output('', 'S');
    }
}