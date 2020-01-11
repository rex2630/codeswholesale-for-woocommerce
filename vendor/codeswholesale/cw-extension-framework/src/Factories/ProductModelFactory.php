<?php
namespace CodesWholesaleFramework\Factories;

use CodesWholesaleFramework\Model\ProductModel;
use CodesWholesale\Resource\FullProduct;
use CodesWholesale\Resource\Product;

/**
 * Class ProductModelFactory
 */
class ProductModelFactory
{
    const DEFAULT_LANGUAGE = 'english';
    const THUMBNAIL_TYPE = 'MEDIUM';
    const PHOTOS_TYPE = 'SCREEN_SHOT_LARGE';
    
    /**
     * 
     * @param FullProduct $fullProduct
     * @param type $preferredLanguage
     * @return ProductModel
     */
    public static function resolveFullProduct(FullProduct $fullProduct, $preferredLanguage): ProductModel
    {
        $model = new ProductModel();
        
        $exceptions = [];  
        
        try {
            $model->setProductId($fullProduct->getProductId());
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setIdentifier($fullProduct->getIdentifier());
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setName($fullProduct->getName());
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setPlatform($fullProduct->getPlatform());
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setQuantity($fullProduct->getQuantity());
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setImage(self::getFullProductThumbnail($fullProduct));
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setRegions($fullProduct->getRegions());
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setLanguages($fullProduct->getLanguages());
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setPrice(self::getFullProductPrice($fullProduct));
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
       
        try {
             $model->setOfficialTitle($fullProduct->getOfficialTitle());
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setCategory($fullProduct->getCategory()); 
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setPegiRating($fullProduct->getPegiRating());
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setDeveloperName($fullProduct->getDeveloperName());
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setDeveloperHomepage($fullProduct->getDeveloperHomepage());   
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
           $model->setKeywords($fullProduct->getKeywords()); 
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setPhotos(self::getFullProductImages($fullProduct, $preferredLanguage));  
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setLocalizedTitles($fullProduct->getLocalizedTitles());
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setInTheGameLanguages($fullProduct->getInTheGameLanguages());
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setReleases($fullProduct->getReleases());
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setFactSheets(self::getFullProductDescription($fullProduct, $preferredLanguage));
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setExtensionsPacks($fullProduct->getExtensionPacks());   
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setEditions($fullProduct->getEditions());
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setReleaseDate($fullProduct->getReleaseDate());
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setLastUpdated($fullProduct->getLastUpdated());
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setEans($fullProduct->getEans());
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        $model->setExceptions($exceptions);
         
        return $model;
    }
    
    /**
     * 
     * @param Product $product
     * @param type $preferredLanguage
     * @return ProductModel
     */
    public static function resolveProduct(Product $product, $preferredLanguage): ProductModel
    {
        $model = new ProductModel();
        
        $exceptions = [];
        
        try {
            $model->setProductId($product->getProductId());
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setIdentifier($product->getIdentifier());
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setName($product->getName());
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setPlatform($product->getPlatform());
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setQuantity($product->getStockQuantity());
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setImage(self::getProductThumbnail($product));
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setRegions($product->getRegions());
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setLanguages($product->getLanguages());
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setPrice($product->getLowestPrice());
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
       
        try {
             $model->setOfficialTitle($product->getProductDescription()->getOfficialTitle());
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setCategory($product->getProductDescription()->getCategories()); 
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setPegiRating($product->getProductDescription()->getPegiRating());
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setDeveloperName($product->getProductDescription()->getDeveloperName());
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setDeveloperHomepage($product->getProductDescription()->getDeveloperHomepage());   
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
           $model->setKeywords($product->getProductDescription()->getKeywords()); 
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setPhotos(self::getProductImages($product, $preferredLanguage));  
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setLocalizedTitles($product->getProductDescription()->getLocalizedTitles());
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setInTheGameLanguages($product->getProductDescription()->getGameLanguages());
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setReleases($product->getProductDescription()->getReleases());
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setFactSheets(self::getProductDescription($product, $preferredLanguage));
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setExtensionsPacks($product->getProductDescription()->getExtensionPacks());   
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setEditions($product->getProductDescription()->getEditions());
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setReleaseDate($product->getReleaseDate());
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setLastUpdated($product->getProductDescription()->getLastUpdate());
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        try {
            $model->setEans($product->getProductDescription()->getEanCodes());
        } catch (\Exception $ex) {
            $exceptions[] = $ex->getMessage();
        }
        
        $model->setExceptions($exceptions);
         
        return $model;
    }
    
    /**
     * 
     * @param Product $product
     * @return array
     */
    private static function getProductThumbnail(Product $product)
    {
        try{
            $thumb = [];

            $url = $product->getImageUrl(self::THUMBNAIL_TYPE);

            if($url) {
                $thumb = [ 'url' => $url,'name' => self::getFileName($url)];
            }

        } catch (\Exception $ex) {
            $thumb = [];
        }

        return $thumb;
    }

    /**
     * 
     * @param Product $product
     * @param type $preferredLanguage
     */
    private static function getProductImages(Product $product, $preferredLanguage) 
    {
        $urls = self::prepareProductImages($product, $preferredLanguage);
        $photos = [];

        foreach($urls as $url) {
            $photos[] = [ 'url' => $url, 'name' => self::getFileName($url)];
        }

        return $photos;
    }
    
    /**
     * 
     * @param Product $product
     * @param string $preferredLanguage
     * @return array
     */
    private static function prepareProductImages(Product $product, string $preferredLanguage) 
    {
        try {
            $photos = $product->getProductDescription()->getPhotos();

            $default     = [];
            $preferred   = [];

            /** @var \CodesWholesale\Resource\Photo $photo */
            foreach($photos as $photo) {
                if(self::PHOTOS_TYPE == $photo->getType()) {
                    if(self::isPreferredByTerriotry($photo->getTerritory(), $preferredLanguage)) {
                        $preferred[] = $photo->getUrl();
                    }
                    if(self::isDefaultByTerriotry($photo->getTerritory())) {
                        $default[] = $photo->getUrl();
                    }
                }
            }

            $urls = empty($preferred) ? $default : $preferred;
        } catch (\Exception $ex) {
            $urls = [];
        }

        return $urls;
    }
    
    /**
     * 
     * @param Product $product
     * @param string $preferredLanguage
     * @return string
     */
    private static function getProductDescription(Product $product, string $preferredLanguage) 
    {
        try{
            $product_description = $product->getProductDescription();

            $factSheets = $product_description->getFactSheets();

            $description        = "";
            $defaultDescription = "";

            /** @var \CodesWholesale\Resource\FactSheet $factSheet */
            foreach ($factSheets as $factSheet) {
                if (strtolower($preferredLanguage) === strtolower($factSheet->getTerritory())) {
                    $description = $factSheet->getDescription();
                }
                if (strtolower(self::DEFAULT_LANGUAGE) === strtolower($factSheet->getTerritory())) {
                    $defaultDescription = $factSheet->getDescription();
                }
            }

            if ("" === $description) {
                $description = $defaultDescription;
            }

        } catch (\Exception $ex) {
            $description = "";
        }

        return $description;
    }


    /**
     * 
     * @param FullProduct $fullProduct
     * @return array
     */
    private static function getFullProductThumbnail(FullProduct $fullProduct) 
    {
        try{
            $thumb = [];

            $url = $fullProduct->getImageUrl(self::THUMBNAIL_TYPE);

            if($url) {
                $thumb = [ 'url' => $url,'name' => self::getFileName($url)];
            }

        } catch (\Exception $ex) {
            $thumb = [];
        }

        return $thumb;
    }
    
    /**
     * 
     * @param FullProduct $fullProduct
     * @return type
     */
    private static function getFullProductPrice(FullProduct $fullProduct) 
    {
        $productPrice = null;

        foreach ($fullProduct->getPrices() as $price) {
            if ('100+' == $price->getRange()) {
                $productPrice = $price->getValue();
            }
        }

        if (null === $productPrice) {
            $productPrice = $fullProduct->getPrices()[0]->getValue();
        }

        return $productPrice;
    }
    
    /**
     * 
     * @param FullProduct $fullProduct
     * @param type $preferredLanguage
     */
    private static function getFullProductImages(FullProduct $fullProduct, $preferredLanguage) 
    {
        $urls = self::prepareFullProductImages($fullProduct, $preferredLanguage);
        $photos = [];

        foreach($urls as $url) {
            $photos[] = [ 'url' => $url, 'name' => self::getFileName($url)];
        }

        return $photos;
    }
    
    /**
     * 
     * @param FullProduct $fullProduct
     * @param string $preferredLanguage
     * @return array
     */
    private static function prepareFullProductImages(FullProduct $fullProduct, string $preferredLanguage) 
    {
        try {
            $photos = $fullProduct->getPhotos();

            $default     = [];
            $preferred   = [];

            /** @var \CodesWholesale\Resource\Photo $photo */
            foreach($photos as $photo) {
                if(self::PHOTOS_TYPE == $photo->getType()) {
                    if(self::isPreferredByTerriotry($photo->getTerritory(), $preferredLanguage)) {
                        $preferred[] = $photo->getUrl();
                    }
                    
                    if(self::isDefaultByTerriotry($photo->getTerritory())) {
                        $default[] = $photo->getUrl();
                    }
                }
            }

            $urls = empty($preferred) ? $default : $preferred;
        } catch (\Exception $ex) {
            $urls = [];
        }

        return $urls;
    }
    
    /**
     * 
     * @param FullProduct $fullProduct
     * @param string $preferredLanguage
     * @return string
     */
    private static function getFullProductDescription(FullProduct $fullProduct, string $preferredLanguage) 
    {
        try{
            $factSheets = $fullProduct->getFactSheets();

            $description        = "";
            $defaultDescription = "";

            /** @var \CodesWholesale\Resource\FactSheet $factSheet */
            foreach ($factSheets as $factSheet) {
                if (strtolower($preferredLanguage) === ($factSheet->getTerritory())) {
                    $description = $factSheet->getDescription();
                }
                if (strtolower(self::DEFAULT_LANGUAGE) === strtolower($factSheet->getTerritory())) {
                    $defaultDescription = $factSheet->getDescription();
                }
            }

            if ("" === $description) {
                $description = $defaultDescription;
            }

        } catch (\Exception $ex) {
            $description = "";
        }

        return $description;
    }
    
    /**
     * 
     * @param type $url
     * @return string
     */
    private static function getFileName($url) {
        $info = pathinfo($url);
        if(array_key_exists('extension', $info)) {
            $extension  = explode('?', $info['extension']);
            $name       = $info['filename'].'.'.$extension[0];
        } else {
            $photo_data = explode("/", $url);
            $count      = count ($photo_data);
            
            $name       = strpos($photo_data[$count-1], '?') !== false ? $photo_data[$count-2] : $photo_data[$count-1];
            $name       .= '.jpg';
        }

        return $name;
    }
    
    /**
     * 
     * @param type $territory
     * @param type $prefereed
     * @return type
     */
    private static function isPreferredByTerriotry($territory, $prefereed) {
        return  null === $territory || "" === $territory || strtolower($prefereed) === strtolower($territory);
    }
    
    /**
     * 
     * @param type $territory
     * @return type
     */
    private static function isDefaultByTerriotry($territory) {
        return  null === $territory || "" === $territory || strtolower(self::DEFAULT_LANGUAGE) === strtolower($territory);
    }
}
