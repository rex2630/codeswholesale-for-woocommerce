<?php

/**
 * Class CsvGenerator
 */
class CsvGenerator
{
    /**
     * @var string
     */
    protected $delimiter;

    /**
     * @var array
     */
    protected $header;

    /**
     * @var array
     */
    protected $lines = [];

    /**
     * CsvGenerator constructor.
     *
     * @param string $delimiter
     */
    public function __construct($delimiter = ';')
    {
        $this->delimiter = $delimiter;
    }

    /**
     * @param array $header
     */
    public function setHeader(array $header)
    {
        $this->header = $header;
    }

    /**
     * @param array $line
     */
    public function append(array $line)
    {
        $this->lines[] = $line;
    }

    /**
     * @return string
     */
    public function generate(): string
    {
        $result = join($this->delimiter, $this->header) . "\n";

        foreach ($this->lines as $line)
        {
            $result .= join($this->delimiter, $line) . "\n";
        }

        return $result;
    }
}