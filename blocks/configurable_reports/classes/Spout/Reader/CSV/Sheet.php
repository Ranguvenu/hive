<?php

namespace block_configurable_reports\Spout\Reader\CSV;

use block_configurable_reports\Spout\Reader\SheetInterface;

/**
 * Class Sheet
 *
 * @package block_configurable_reports\Spout\Reader\CSV
 */
class Sheet implements SheetInterface
{
    /** @var \block_configurable_reports\Spout\Reader\CSV\RowIterator To iterate over the CSV's rows */
    protected $rowIterator;

    /**
     * @param resource $filePointer Pointer to the CSV file to read
     * @param string $fieldDelimiter Character that delimits fields
     * @param string $fieldEnclosure Character that enclose fields
     * @param string $encoding Encoding of the CSV file to be read
     * @param \block_configurable_reports\Spout\Common\Helper\GlobalFunctionsHelper $globalFunctionsHelper
     */
    public function __construct($filePointer, $fieldDelimiter, $fieldEnclosure, $encoding, $endOfLineCharacter, $globalFunctionsHelper)
    {
        $this->rowIterator = new RowIterator($filePointer, $fieldDelimiter, $fieldEnclosure, $encoding, $endOfLineCharacter, $globalFunctionsHelper);
    }

    /**
     * @api
     * @return \block_configurable_reports\Spout\Reader\CSV\RowIterator
     */
    public function getRowIterator()
    {
        return $this->rowIterator;
    }
}
