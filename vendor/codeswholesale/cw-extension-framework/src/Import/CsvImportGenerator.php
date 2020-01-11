<?php
namespace CodesWholesaleFramework\Import;

use CodesWholesaleFramework\Model\ProductModel;

/**
 * Class CsvGenerator
 */
class CsvImportGenerator
{
    
    /**
     * @var CsvGenerator
     */
    protected $csvGenerator;
    
    /**
     * @var array
     */
    protected $changeLines = [];
    
    public function __construct() {
        $this->start();
    }
    
    public function start() {
        $this->csvGenerator = new CsvGenerator();
        $this->csvGenerator->setHeader($this->generateImportCsvHeader());
    }
    
    public function finish() {
       return $this->csvGenerator->generate();
    }
    
    
    public function appendNewProduct(ProductModel $productModel) {
        $this->csvGenerator->append($this->generateInsertLine($productModel));
    }
    
    public function appendUpdatedProduct(ProductModel $productModel, array $diff) {
        $this->csvGenerator->append($this->generateUpdateLine($productModel, $diff));
    }
    
    /**
     * @return array
     */
    private function generateImportCsvHeader(): array
    {
        return [
            'ID',
            'Status',
            'Name',
            'Price',
            'Stock',
            'Platform',
            'Regions',
            'Languages'
        ];
    }
    
    /**
     * 
     * @param ProductModel $productModel
     * @return array
     */
    private function generateInsertLine(ProductModel $productModel): array
    {
        return [
            (string) '"' . $productModel->getProductId() .'"',
            (string) '"' . 'Imported' .'"',
            (string) '"' . $productModel->getName() .'"',
            (string) '"' . $productModel->getPrice() .'"',
            (string) '"' . $productModel->getQuantity() .'"',
            (string) '"' . ProductDiffGenerator::implodeArray($productModel->getPlatform()) .'"',
            (string) '"' . ProductDiffGenerator::implodeArray($productModel->getRegions()) .'"',
            (string) '"' . ProductDiffGenerator::implodeArray($productModel->getLanguages()) .'"',
        ];
    }
    
    /**
     * 
     * @param ProductModel $productModel
     * @param array $diffs
     * @return array
     */
    private function generateUpdateLine(ProductModel $productModel, array $diffs): array
    {
        $this->changeLines = [];
        
        foreach ($diffs as $key => $diff) {
            $this->changeLines[$key] = 'Old: ' . $diff[ProductDiffGenerator::OLD_VALUE] . "\nNew: " .$diff[ProductDiffGenerator::NEW_VALUE] ;
        }

        $name = $this->getDiffLineByKey(
            ProductDiffGenerator::FIELD_NAME,
            $productModel->getName()
        );

        $platform = $this->getDiffLineByKey(
            ProductDiffGenerator::FIELD_PLATFORMS,
            ProductDiffGenerator::implodeArray($productModel->getPlatform())
        );

        $regions = $this->getDiffLineByKey(
            ProductDiffGenerator::FIELD_REGIONS,
            ProductDiffGenerator::implodeArray($productModel->getRegions())
        );

        $languages = $this->getDiffLineByKey(
            ProductDiffGenerator::FIELD_LANGUAGES,
            ProductDiffGenerator::implodeArray($productModel->getLanguages())
        );

        $price = $this->getDiffLineByKey(
            ProductDiffGenerator::FIELD_PRICE,
            $productModel->getPrice()
        );

        $stock = $this->getDiffLineByKey(
            ProductDiffGenerator::FIELD_STOCK,
            $productModel->getQuantity()
        );


        return [
            (string) '"' . $productModel->getProductId() . '"',
            (string) '"' . 'Updated' . '"',
            (string) '"' . $name . '"',
            (string) '"' . $price . '"',
            (string) '"' . $stock . '"',
            (string) '"' . $platform . '"',
            (string) '"' . $regions . '"',
            (string) '"' . $languages . '"',
        ];
    }
    
    private function getDiffLineByKey(string $key, $default): string
    {
        if (array_key_exists($key, $this->changeLines)) {
            return $this->changeLines[$key];
        } else {
            return $default;
        }
    }
}

