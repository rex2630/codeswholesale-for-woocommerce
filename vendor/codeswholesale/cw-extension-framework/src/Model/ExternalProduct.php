<?php

namespace CodesWholesaleFramework\Model;

use CodesWholesale\Resource\Product;

/**
 * Class ExternalProduct
 */
class ExternalProduct
{
    const DEFAULT_LANGUAGE = 'english';
    const THUMBNAIL_TYPE = 'MEDIUM';
    const PHOTOS_TYPE = 'SCREEN_SHOT_LARGE';

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var Array
     */
    protected $photos;


    /**
     * @var Array
     */
    protected $thumbnail;

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     *
     * @return ExternalProduct
     */
    public function setProduct(Product $product): ExternalProduct
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return ExternalProduct
     */
    public function setDescription(string $description): ExternalProduct
    {
        $this->description = $description;

        return $this;
    }
    /**
     *
     * @return array
     */
    public function getPhotos(): array
    {
        return $this->photos;
    }


    /**
     * @param Array $photos
     *
     * @return ExternalProduct
     */
    public function setPhotos(array $photos): ExternalProduct
    {
        $this->photos = $photos;

        return $this;
    }

    /**
     *
     * @return array
     */
    public function getThumbnail(): array
    {
        return $this->thumbnail;
    }


    /**
     * @param array $thumbnail
     *
     * @return \CodesWholesaleFramework\Model\ExternalProduct
     */
    public function setThumbnail(array $thumbnail): ExternalProduct
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    public function updateInformations(string $preferredLanguage): ExternalProduct
    {
        $this->updateDescription($preferredLanguage);
        $this->updatePhotos($preferredLanguage);
        $this->updateThumbnail();

        return $this;
    }

    private function updatePhotos(string $preferredLanguage) {
        $urls = $this->preparePhotos($preferredLanguage);
        $photos = [];

        foreach($urls as $url) {
            $photos[] = [ 'url' => $url, 'name' => $this->getFileName($url)];
        }

        $this->setPhotos($photos);
    }

    private function updateDescription(string $preferredLanguage) {
        try{
            $product_description = $this->getProduct()->getProductDescription();

            $factSheets = $product_description->getFactSheets();

            $description        = "";
            $defaultDescription = "";

            /** @var \CodesWholesale\Resource\FactSheet $factSheet */
            foreach ($factSheets as $factSheet) {
                if ($preferredLanguage === $factSheet->getTerritory()) {
                    $description = $factSheet->getDescription();
                }
                if (self::DEFAULT_LANGUAGE === $factSheet->getTerritory()) {
                    $defaultDescription = $factSheet->getDescription();
                }
            }

            if ("" === $description) {
                $description = $defaultDescription;
            }

        } catch (\Exception $ex) {
            $description = "";
        }

        $this->setDescription($description);
    }

    private function updateThumbnail() {
        try{
            $thumb = [];

            $url = $this->getProduct()->getImageUrl(self::THUMBNAIL_TYPE);

            if($url) {
                $thumb = [ 'url' => $url,'name' => $this->getFileName($url)];
            }

        } catch (\Exception $ex) {
            $thumb = [];
        }

        $this->setThumbnail($thumb);

    }

    private function preparePhotos(string $preferredLanguage) {
        try {
            $photos = $this->getProduct()->getProductDescription()->getPhotos();

            $default     = [];
            $preferred   = [];

            /** @var \CodesWholesale\Resource\Photo $photo */
            foreach($photos as $photo) {
                if(self::PHOTOS_TYPE == $photo->getType()) {
                    if("" == $photo->getTerritory() || $preferredLanguage == $photo->getTerritory()) {
                        $preferred[] = $photo->getUrl();
                    }
                    if("" == $photo->getTerritory() || self::DEFAULT_LANGUAGE == $photo->getTerritory()) {
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

    private function getFileName($url) {
        $info = pathinfo($url);
        if(array_key_exists('extension', $info)) {
            $extension  = explode('?', $info['extension']);
            $name       = $info['filename'].'.'.$extension[0];
        } else {
            $photo_data = explode("/", $url);
            $count      = count ($photo_data);
            $name       = $photo_data[$count-2].'.jpg';
        }

        return $name;
    }
}
