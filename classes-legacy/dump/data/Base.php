<?php

abstract class dump_data_Base
{

    /**
     * @param string $filePath
     * @return array
     */
    public function getExportedFiles($filePath)
    {
        return [
            $this->exportCsv($filePath), // данные
            $this->exportMetaCsv($filePath), // метаданные
        ];
    }

    protected function exportMetaCsv($filePath)
    {
        $fileName = $filePath . 'structure_' . $this->getGetFileNamePrefix() . '.csv';

        $result = $this->getMetaData();

        $fp = fopen($fileName, 'w');
        fputcsv($fp, ['property', 'value']);
        foreach ($result as $fields) {
            fputcsv($fp, $fields);
        }
        fclose($fp);

        return $fileName;
    }

    protected function exportCsv($filePath, $suffix = null)
    {
        $fileName = $filePath . 'data_' . $this->getGetFileNamePrefix() . (null === $suffix ? '_' . date('Ymd') : $suffix) . '.csv';
        $fp = fopen($fileName, 'w');
        fputcsv($fp, $this->getColumns());

        $list = $this->getList($total);
        foreach ($list as $id) {
            $this->dumpRows($id, function ($row) use ($fp) {
                fputcsv($fp, $row);
            });
        }
        fclose($fp);
        return $fileName;
    }

    abstract protected function getGetFileNamePrefix();

    abstract protected function getList(&$total);

    abstract protected function dumpRows($id, $callback);

    abstract protected function getColumns();

    protected function getDataArray($data)
    {
        $value = array_fill_keys(array_keys($this->getColumns()), null);
        foreach ($data as $k => $v) {
            $value[$k] = $v;
        }
        return array_values($value);
    }

    abstract protected function getMetaData();

}
