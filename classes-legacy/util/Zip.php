<?php

class util_Zip
{
    protected $fileList;

    /**
     * Добавить файл в архив
     * @param string $name имя файла
     * @param string $data содержимое
     * @return $this
     */
    public function addFile($name, $data)
    {
        $this->fileList[] = [
            'name' => $name,
            'data' => $data
        ];
        return $this;
    }

    /**
     * Возвращает путь до zip
     * @return string
     */
    public function makePath()
    {
        $zipName = tempnam(sys_get_temp_dir(), 'zip_' . time());
        $za = new ZipArchive();
        if ($za->open($zipName, ZipArchive::CREATE) !== true) {
            throw new RuntimeException('Can\'t make zip');
        }
        foreach ($this->fileList as $f) {
            $za->addFromString(iconv('utf8', 'cp866//TRANSLIT', $f['name']), $f['data']);
        }
        $za->close();
        return $zipName;
    }

    /**
     * Возвращает содержимое zip
     * @return string
     */
    public function make()
    {
        $zipName = $this->makePath();
        $data = file_get_contents($zipName);
        unlink($zipName);
        return $data;
    }

}