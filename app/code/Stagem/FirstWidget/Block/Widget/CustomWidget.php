<?php

namespace Stagem\FirstWidget\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Stagem\FirstWidget\Model\ProductData;

class CustomWidget extends Template implements BlockInterface
{
    protected $_template = "widget/test.phtml";

    public function __construct(
        protected ProductData $productData,
        Template\Context      $context,
        array                 $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getLabel(): string
    {
        return $this->getData('title');
    }

    public function getQtyProducts(): string
    {
        return $this->getData('qty_products');
    }

    public function getCategory(): string
    {
        return $this->getData('category');
    }

    public function exec(): array
    {
        $categoryName = $this->getCategory();
        $productLimit = (int)$this->getQtyProducts();

        $products = $this->productData->getProductData($categoryName);

        return $this->limitOfProducts($products, $productLimit);
    }

    protected function limitOfProducts(array $products, int $limit): array
    {
        return array_slice($products, 0, $limit);
    }
}
