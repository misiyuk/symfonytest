<?php

namespace App;

use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * @property string $tempFile
 * @property string $fileName
 * @property array $insertData
 */
class XlsGenerator
{
    private $tempFile;
    private $fileName;
    private $insertData;

    /**
     * @param array $insertData
     * @param string $fileName
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function createTmpFile(array $insertData, $fileName = 'codes.xls'): void
    {
        $this->fileName = $fileName;
        $this->insertData = $insertData;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $columnArray = $this->oneColumnFormat();
        $sheet->fromArray($columnArray);
        $writer = new Xls($spreadsheet);
        $this->setTempFile();
        $writer->save($this->tempFile);
    }

    private function setTempFile(?string $name = null): void
    {
        $this->tempFile = $name ?: tempnam(sys_get_temp_dir(), $this->fileName);
    }

    private function oneColumnFormat(): array
    {
        return array_map(function (string $row) {
            return [$row];
        }, $this->insertData);
    }

    public function getTempFile(): string
    {
        return $this->tempFile;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }
}