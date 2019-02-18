<?php

namespace Comerline\CouponRequest\Block\Catalog\Product\View;

use Magento\Framework\Registry;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\CatalogInventory\Model\Stock\Item;

class CouponRequest extends \Magento\Framework\View\Element\Template {

    const XML_PATH_ENABLE = 'couponrequest/general/enable';
    const XML_PATH_CATEGORIES_IN = 'couponrequest/general/categories_in';
    const XML_PATH_CATEGORIES_NOT_IN = 'couponrequest/general/categories_not_in';

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Product
     */
    private $product;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    
    /**
     * @var \Magento\CatalogInventory\Model\Stock\Item
     */
    protected $stockItem;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, ScopeConfigInterface $scopeConfig, Registry $registry, Item $stockItem) {
        $this->registry = $registry;
        $this->scopeConfig = $scopeConfig;
        $this->stockItem = $stockItem;
        parent::__construct($context);
    }

    /**
     * Get Product
     * @return Product
     */
    private function getProduct() {
        if (is_null($this->product)) {
            $this->product = $this->registry->registry('product');

            if (!$this->product->getId()) {
                throw new LocalizedException(__('Failed to initialize product'));
            }
        }
        return $this->product;
    }

    /**
     * Get Url Product
     * @return string 
     */
    public function getUrlProduct() {
        return $this->getProduct()->getProductUrl();
    }

    /**
     * Return Url form action
     * @return string with url form action
     */
    public function getFormAction() {
        return $this->getUrl('couponrequest/couponrequest/send');
    }

    /**
     * Return Template html
     * @return string
     */
    protected function _toHtml() {
        $html = '';
        if ($this->isEnable() && $this->showInProduct()) {
            $template = $this->getTemplateFile();
            $html = $this->fetchView($template);
        }
        return $html;
    }

    /**
     * Is Enabled
     * @return boolean
     */
    private function isEnable() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_ENABLE, $storeScope);
    }
    
    /**
     * Show in Product
     * @return boolean
     */
    private function showInProduct() {
        $categoriesIds = $this->getProduct()->getCategoryIds();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $categories_in = explode(',', $this->scopeConfig->getValue(self::XML_PATH_CATEGORIES_IN, $storeScope));
        $categories_not_in = explode(',', $this->scopeConfig->getValue(self::XML_PATH_CATEGORIES_NOT_IN, $storeScope));
        $show = false;
        // Check categories in
        foreach ($categoriesIds as $cid) {
            if(in_array($cid, $categories_in)) {
                $show = true;
                break;
            }
        }
        // Check categories not in
        if($show) {
            foreach ($categoriesIds as $cid) {
                if(in_array($cid, $categories_not_in)) {
                    $show = false;
                    break;
                }
            }
        }
        // Check stock
        if($show) {
            $stockItem = $this->stockItem->load($this->getProduct()->getId(), 'product_id');
            $show = $stockItem->getIsInStock();
        }
        return $show;
    }

}
