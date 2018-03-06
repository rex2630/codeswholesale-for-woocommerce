<?php

require_once( dirname(__FILE__) . '/../../../../../wp-load.php' );
require_once( dirname(__FILE__) . '/../../codeswholesale.php' );

use CodesWholesale\Client;
use CodesWholesale\Resource\Product;
use CodesWholesaleFramework\Model\ExternalProduct;

/**
 * Class ImportExec
 */
class ImportExec
{
    /**
     * @var WP_ImportPropertyRepository
     */
    protected $importRepository;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var WP_Product_Updater
     */
    protected $updater;

    /**
     * @var WP_ImportPropertyModel
     */
    protected $importModel;

    /**
     * @var ImportProductDiffGenerator
     */
    protected $diffGenerator;


    /**
     * @var CsvGenerator
     */
    protected $csvGenerator;

    /**
     * @var array
     */
    protected $changeLines = [];

    /**
     * @var array|mixed|void
     */
    private $optionsArray;

    /**
     * ImportExec constructor.
     */
    public function __construct()
    {
        $this->importRepository = new WP_ImportPropertyRepository();
        $this->client = CW()->get_codes_wholesale_client();
        $this->updater = WP_Product_Updater::getInstance();
        $this->importModel = $this->importRepository->findActive();
        $this->diffGenerator = new ImportProductDiffGenerator();
        $this->csvGenerator = new CsvGenerator();
        $this->csvGenerator->setHeader($this->generateImportCsvHeader());
        $this->createImportFolder();
        $this->optionsArray = CW()->get_options();
    }

    /**
     * execute
     */
    public function execute()
    {
        /** @var $wpdb wpdb */
        global $wpdb;

        $filters = [];

        if (0 !== count($this->importModel->getFilters()['platform'])) {
            $filters['platform'] = $this->importModel->getFilters()['platform'];
        }

        if (0 !== count($this->importModel->getFilters()['region'])) {
            $filters['region'] = $this->importModel->getFilters()['region'];
        }

        if (0 !== count($this->importModel->getFilters()['language'])) {
            $filters['language'] = $this->importModel->getFilters()['language'];
        }

        if (null != $this->importModel->getInStockDaysAgo()) {
            $filters['inStockDaysAgo'] = $this->importModel->getInStockDaysAgo();
        }

//        $wpdb->query('START TRANSACTION');
        try {
            $externalProducts = $this->client->getProducts($filters);

            $this->importModel->setStatus(WP_ImportPropertyModel::STATUS_IN_PROGRESS);
            $this->importModel->setTotalCount(count($externalProducts));
            $this->importRepository->update($this->importModel);

            /** @var \CodesWholesale\Resource\Product $product */
            foreach ($externalProducts as $product) {
                $this->importProduct($product);
            }

            $this->importModel->setStatus(WP_ImportPropertyModel::STATUS_DONE);
            $this->importRepository->update($this->importModel);

        } catch (\Exception $e) {
//            $wpdb->query('ROLLBACK');

            $this->importModel->setStatus(WP_ImportPropertyModel::STATUS_REJECT);
            $this->importModel->setDescription($e->getMessage());
            $this->importRepository->update($this->importModel);
            throw $e;
        }

        $csv = $this->csvGenerator->generate();

        file_put_contents($this->getImportFilePath(), $csv);

        (new WP_Admin_Notify_Import_Finished())->sendMail([$this->getImportFilePath()], $this->importModel);
//        $wpdb->query('COMMIT');
    }

    private function importProduct($product) {
        try {
            $externalProduct = (new ExternalProduct())
                 ->setProduct($product)
                 ->updateDescription($this->optionsArray[CodesWholesaleConst::PREFERRED_LANGUAGE_FOR_PRODUCT_OPTION_NAME])
             ;

             $relatedInternalProducts = CW()->get_related_wp_products($externalProduct->getProduct()->getProductId());

             if (0 === count($relatedInternalProducts)) {
                 $this->updater->createWooCommerceProduct($this->importModel->getUserId(), $externalProduct);
                 $this->importModel->increaseInsertCount();
                 $this->csvGenerator->append($this->generateInsertLine($externalProduct));
             } elseif (0 < count($relatedInternalProducts)) {

                  foreach ($relatedInternalProducts as $post) {
                     $diff = $this->diffGenerator->getDiff($externalProduct, $post);

                     if (0 !== count($diff)) {
                         $this->updater->updateWooCommerceProduct($post->ID, $externalProduct);
                         $this->importModel->increaseUpdateCount();
                         $this->csvGenerator->append($this->generateUpdateLine($externalProduct, $diff));
                     }
                  }
             }
             $this->importModel->increaseDoneCount();

             $this->importRepository->update($this->importModel); 
        } catch (\Exception $e) {
        }
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
            'Cover',
        ];
    }

    /**
     * @param ExternalProduct $externalProduct
     *
     * @return array
     */
    private function generateInsertLine(ExternalProduct $externalProduct): array
    {
        return [
            (string) '"' . $externalProduct->getProduct()->getProductId() .'"',
            (string) '"' . 'Imported' .'"',
            (string) '"' . $externalProduct->getProduct()->getName() .'"',
            (string) '"' . $externalProduct->getProduct()->getLowestPrice() .'"',
            (string) '"' . $externalProduct->getProduct()->getStockQuantity() .'"',
            (string) '"' . $externalProduct->getProduct()->getImageUrl('MEDIUM') .'"',
        ];
    }

    /**
     * @param $value
     * @return string
     */
    private function implodeArray($value): string
    {
        if(is_array($value)) {
            $value = implode("|", $value);
        }

        return $value;
    }

    private function getDiffLineByKey(string $key, $default): string
    {
        if (array_key_exists($key, $this->changeLines)) {
            return $this->changeLines[$key];
        } else {
            return $default;
        }
    }

    /**
     * @param ExternalProduct $externalProduct
     *
     * @return array
     */
    private function generateUpdateLine(ExternalProduct $externalProduct, array $diffs): array
    {
        $this->changeLines = [];

        foreach ($diffs as $key => $diff) {
            $this->changeLines[$key] = 'Old: ' . join("\nNew: ", $diff);
        }

        $name = $this->getDiffLineByKey(
            ImportProductDiffGenerator::FIELD_NAME,
            $externalProduct->getProduct()->getName()
        );

        $platform = $this->getDiffLineByKey(
            ImportProductDiffGenerator::FIELD_PLATFORMS,
            $this->implodeArray($externalProduct->getProduct()->getPlatform())
        );

        $regions = $this->getDiffLineByKey(
            ImportProductDiffGenerator::FIELD_REGIONS,
            $this->implodeArray($externalProduct->getProduct()->getRegions())
        );

        $languages = $this->getDiffLineByKey(
            ImportProductDiffGenerator::FIELD_LANGUAGES,
            $this->implodeArray($externalProduct->getProduct()->getLanguages())
        );

        $price = $this->getDiffLineByKey(
            ImportProductDiffGenerator::FIELD_PRICE,
            $externalProduct->getProduct()->getLowestPrice()
        );

        $stock = $this->getDiffLineByKey(
            ImportProductDiffGenerator::FIELD_STOCK,
            $externalProduct->getProduct()->getStockQuantity()
        );

        $description = $this->getDiffLineByKey(
            ImportProductDiffGenerator::FIELD_DESCRIPTION,
            $externalProduct->getDescription()
        );

        $cover = $this->getDiffLineByKey(
            ImportProductDiffGenerator::FIELD_COVER,
            $externalProduct->getProduct()->getImageUrl('MEDIUM')
        );

        return [
            (string) '"' . $externalProduct->getProduct()->getProductId() . '"',
            (string) '"' . 'Updated' . '"',
            (string) '"' . $name . '"',
            (string) '"' . $platform . '"',
            (string) '"' . $regions . '"',
            (string) '"' . $languages . '"',
            (string) '"' . $price . '"',
            (string) '"' . $stock . '"',
            (string) '"' . $description . '"',
            (string) '"' . $cover . '"',
        ];
    }

    /**
     * @return string
     */
    private function getUploadPath(): string
    {
        return wp_upload_dir()['basedir'];
    }

    /**
     * @return string
     */
    private function getImportPath(): string
    {
        return $this->getUploadPath() . '/cw-import-products/';
    }

    /**
     * @return string
     */
    private function getImportFilePath(): string
    {
        return $this->getImportPath() . $this->importModel->getId() . '-import.csv';
    }

    /**
     * createImportFolder
     */
    private function createImportFolder()
    {
        $old = umask(0);

        try {

            $path = $this->getUploadPath();

            if (!is_readable($path) || !is_writable($path)) {
                $this->importModel->setStatus(WP_ImportPropertyModel::STATUS_REJECT);
                $this->importModel->setDescription(sprintf('Bad permissions for uploads folder: "%s"', $path));
                $this->importRepository->update($this->importModel);
                throw new \Exception();
            }

            $path = $this->getImportPath();

            if (!is_dir($path)) {
                mkdir($path, 0777);
            }

            $id = $this->importModel->getId();

            if (file_exists($path . sprintf('%s-import.csv', $id))) {
                unlink($path . sprintf('%s-import.csv', $id));
            }
        } catch (\Exception $e) {
            umask($old);
            throw $e;
        }

        umask($old);
    }
}


$import = new ImportExec();

$import->execute();