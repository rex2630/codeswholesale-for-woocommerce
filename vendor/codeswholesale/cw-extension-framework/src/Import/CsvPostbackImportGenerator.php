<?php
namespace CodesWholesaleFramework\Import;

use CodesWholesaleFramework\Database\Interfaces\DbManagerInterface;
use CodesWholesaleFramework\Database\Repositories\PostbackImportDetailsRepository;
use CodesWholesaleFramework\Database\Models\PostbackImportDetailsModel;

/**
 * Class CsvGenerator
 */
class CsvPostbackImportGenerator
{
    
    /**
     * @var CsvGenerator
     */
    protected $csvGenerator;
  
    /**
     * @var PostbackImportDetailsRepository
     */
    protected $importDetailsRepository;

    
    public function __construct(DbManagerInterface $db) {
        $this->importDetailsRepository = new PostbackImportDetailsRepository($db);
    }
    
    public function generateReport($importId) {
        $this->csvGenerator = new CsvGenerator();
        $this->csvGenerator->setHeader($this->generateImportCsvHeader());
        
        $rows = $this->importDetailsRepository->findByImport($importId);
        
        foreach($rows as $row) {
            $this->csvGenerator->append($this->generateInsertLine($row));
        }
        
        return $this->csvGenerator->generate();
    }
    
       
    /**
     * @return array
     */
    private function generateImportCsvHeader(): array
    {
        return [
            'ID',
            'Created',
            'Name',
            'Status',
            'Execution time',
            'Description',
            'Exceptions',
        ];
    }
    
    /**
     * 
     * @param PostbackImportDetailsModel $details
     * @return array
     */
    private function generateInsertLine(PostbackImportDetailsModel $details): array
    {
        return [
            (string) '"' . $details->getProductId() .'"',
            (string) '"' . $details->getCreatedAt() .'"',
            (string) '"' . $details->getName() .'"',
            (string) '"' . $details->getStatus() .'"',
            (string) '"' . $details->getImportTime() .'"',
            (string) '"' . $this->getDescription($details) .'"',
            (string) '"' . $this->getExceptions($details) .'"'
        ];
    }
    
    private function getDescription(PostbackImportDetailsModel $details)
    {
        $descriptions = unserialize($details->getDescription());
        
        if(!$descriptions || count($descriptions) == 0) {
            return '';
        }

        $lines = [];
        
        foreach($descriptions as $key => $value) {
            $lines[] = $key . ': ' . $value[ProductDiffGenerator::OLD_VALUE] . ' -> ' . $value[ProductDiffGenerator::NEW_VALUE];
        }

        return implode(' || ', $lines);
    }
    
    private function getExceptions(PostbackImportDetailsModel $details)
    {
        $exceptions = unserialize($details->getExceptions());
        
        return implode('\n', $exceptions);
    }
}
