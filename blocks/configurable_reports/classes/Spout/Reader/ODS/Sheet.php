<?php

namespace block_configurable_reports\Spout\Reader\ODS;

use block_configurable_reports\Spout\Reader\SheetInterface;
use block_configurable_reports\Spout\Reader\Wrapper\XMLReader;

/**
 * Class Sheet
 * Represents a sheet within a ODS file
 *
 * @package block_configurable_reports\Spout\Reader\ODS
 */
class Sheet implements SheetInterface
{
    /** @var \block_configurable_reports\Spout\Reader\ODS\RowIterator To iterate over sheet's rows */
    protected $rowIterator;

    /** @var int ID of the sheet */
    protected $id;

    /** @var int Index of the sheet, based on order in the workbook (zero-based) */
    protected $index;

    /** @var string Name of the sheet */
    protected $name;

    /**
     * @param XMLReader $xmlReader XML Reader, positioned on the "<table:table>" element
     * @param bool $shouldFormatDates Whether date/time values should be returned as PHP objects or be formatted as strings
     * @param int $sheetIndex Index of the sheet, based on order in the workbook (zero-based)
     * @param string $sheetName Name of the sheet
     */
    public function __construct($xmlReader, $shouldFormatDates, $sheetIndex, $sheetName)
    {
        $this->rowIterator = new RowIterator($xmlReader, $shouldFormatDates);
        $this->index = $sheetIndex;
        $this->name = $sheetName;
    }

    /**
     * @api
     * @return \block_configurable_reports\Spout\Reader\ODS\RowIterator
     */
    public function getRowIterator()
    {
        return $this->rowIterator;
    }

    /**
     * @api
     * @return int Index of the sheet, based on order in the workbook (zero-based)
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @api
     * @return string Name of the sheet
     */
    public function getName()
    {
        return $this->name;
    }
}
