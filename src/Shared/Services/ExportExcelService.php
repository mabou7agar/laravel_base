<?php

namespace  BasePackage\Shared\Services;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExportExcelService
{
    protected $headers = [];
    protected $data = [];
    protected $filename = 'Export.xlsx';
    protected $title = '';
    protected $subject = '';
    protected $descirption = '';

    /**
     * Set the headers for the Excel file.
     *
     * @param array $headers
     * @return $this
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
        return $this;
    }


    /**
     * Set the data for the Excel file.
     *
     * @param array $data
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Set the filename for the Excel file.
     *
     * @param string $filename
     * @return $this
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * Set the title for the Excel file.
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Set the subject for the Excel file.
     *
     * @param string $subject
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Set the description for the Excel file.
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }


    /**
     * Export the data to an Excel file.
     *
     * @return void
     */
    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('Admin')
            ->setTitle($this->title)
            ->setSubject($this->subject)
            ->setDescription($this->description);

        // Set headers
        $spreadsheet->setActiveSheetIndex(0)->fromArray([$this->headers]);

        // Set data
        $spreadsheet->getActiveSheet()->fromArray($this->data, null, 'A2');

        $spreadsheet->getActiveSheet()->setTitle('Sheet1');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $this->outputFile($writer);
    }

    /**
     * Output the Excel file to the browser.
     *
     * @param \PhpOffice\PhpSpreadsheet\Writer\Xlsx $writer
     * @return void
     */
    protected function outputFile(Xlsx $writer)
    {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $this->filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer->save('php://output');
        exit;
    }

}
