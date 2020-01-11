<?php
namespace CodesWholesaleFramework\Model;

class ProductModel
{    
    protected $productId;
    protected $identifier;
    protected $name;
    protected $platform;
    protected $quantity;
    protected $image;
    protected $regions;
    protected $languages;
    protected $price;
    protected $officialTitle;
    protected $category;
    protected $pegiRating;
    protected $developerName;
    protected $developerHomepage;
    protected $keywords;
    protected $photos;
    protected $localizedTitles;
    protected $inTheGameLanguages;
    protected $releases;
    protected $factSheets;
    protected $extensionsPacks;
    protected $editions;
    protected $videos;
    protected $minimumRequirements;
    protected $recommendedRequirements;
    protected $releaseDate;
    protected $lastUpdated;
    protected $eans;
    protected $exceptions;
    
    function getProductId() {
        return $this->productId;
    }

    function getIdentifier() {
        return $this->identifier;
    }

    function getName() {
        return $this->name;
    }

    function getPlatform() {
        return $this->platform;
    }

    function getQuantity() {
        return $this->quantity;
    }

    function getImage() {
        return $this->image;
    }

    function getRegions() {
        return $this->regions;
    }

    function getLanguages() {
        return $this->languages;
    }

    function getPrice() {
        return $this->price;
    }

    function getOfficialTitle() {
        return $this->officialTitle;
    }

    function getCategory() {
        return $this->category;
    }

    function getPegiRating() {
        return $this->pegiRating;
    }

    function getDeveloperName() {
        return $this->developerName;
    }

    function getDeveloperHomepage() {
        return $this->developerHomepage;
    }

    function getKeywords() {
        return $this->keywords;
    }

    function getPhotos() {
        return $this->photos;
    }

    function getLocalizedTitles() {
        return $this->localizedTitles;
    }

    function getInTheGameLanguages() {
        return $this->inTheGameLanguages;
    }

    function getReleases() {
        return $this->releases;
    }

    function getFactSheets() {
        return $this->factSheets;
    }

    function getExtensionsPacks() {
        return $this->extensionsPacks;
    }

    function getEditions() {
        return $this->editions;
    }

    function getVideos() {
        return $this->videos;
    }

    function getMinimumRequirements() {
        return $this->minimumRequirements;
    }

    function getRecommendedRequirements() {
        return $this->recommendedRequirements;
    }

    function getReleaseDate() {
        return $this->releaseDate;
    }

    function getLastUpdated() {
        return $this->lastUpdated;
    }

    function getEans() {
        return $this->eans;
    }
    
    function getExceptions() {
        return $this->exceptions;
    }

    function setProductId($productId) {
        $this->productId = $productId;
        
        return $this;
    }

    function setIdentifier($identifier) {
        $this->identifier = $identifier;
        
         return $this;
    }

    function setName($name) {
        $this->name = $name;
        
         return $this;
    }

    function setPlatform($platform) {
        $this->platform = $platform;
        
         return $this;
    }

    function setQuantity($quantity) {
        $this->quantity = $quantity;
        
         return $this;
    }

    function setImage($image) {
        $this->image = $image;
        
         return $this;
    }

    function setRegions($regions) {
        $this->regions = $regions;
        
         return $this;
    }

    function setLanguages($languages) {
        $this->languages = $languages;
        
         return $this;
    }

    function setPrice($price) {
        $this->price = $price;
        
         return $this;
    }

    function setOfficialTitle($officialTitle) {
        $this->officialTitle = $officialTitle;
        
         return $this;
    }

    function setCategory($category) {
        $this->category = $category;
        
         return $this;
    }

    function setPegiRating($pegiRating) {
        $this->pegiRating = $pegiRating;
        
         return $this;
    }

    function setDeveloperName($developerName) {
        $this->developerName = $developerName;
        
         return $this;
    }

    function setDeveloperHomepage($developerHomepage) {
        $this->developerHomepage = $developerHomepage;
        
         return $this;
    }

    function setKeywords($keywords) {
        $this->keywords = $keywords;
        
         return $this;
    }

    function setPhotos($photos) {
        $this->photos = $photos;
        
         return $this;
    }

    function setLocalizedTitles($localizedTitles) {
        $this->localizedTitles = $localizedTitles;
        
         return $this;
    }

    function setInTheGameLanguages($inTheGameLanguages) {
        $this->inTheGameLanguages = $inTheGameLanguages;
        
         return $this;
    }

    function setReleases($releases) {
        $this->releases = $releases;
        
         return $this;
    }

    function setFactSheets($factSheets) {
        $this->factSheets = $factSheets;
        
         return $this;
    }

    function setExtensionsPacks($extensionsPacks) {
        $this->extensionsPacks = $extensionsPacks;
        
         return $this;
    }

    function setEditions($editions) {
        $this->editions = $editions;
        
         return $this;
    }

    function setVideos($videos) {
        $this->videos = $videos;
        
         return $this;
    }

    function setMinimumRequirements($minimumRequirements) {
        $this->minimumRequirements = $minimumRequirements;
        
         return $this;
    }

    function setRecommendedRequirements($recommendedRequirements) {
        $this->recommendedRequirements = $recommendedRequirements;
        
         return $this;
    }

    function setReleaseDate($releaseDate) {
        $this->releaseDate = $releaseDate;
        
         return $this;
    }

    function setLastUpdated($lastUpdated) {
        $this->lastUpdated = $lastUpdated;
        
         return $this;
    }

    function setEans($eans) {
        $this->eans = $eans;
        
         return $this;
    }
    
    function setExceptions($exceptions) {
        $this->exceptions = $exceptions;
        
         return $this;
    }
}

