<?php

namespace Stagem\FirstWidget\Model;

use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Eav\Model\AttributeRepository;
use Magento\Framework\Api\SortOrder;
use Magento\Catalog\Api\CategoryListInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;

class ProductData
{
    protected array $allProductData = [];

    public function __construct(
        protected SortOrder                  $sortOrder,
        protected ProductRepositoryInterface $productRepository,
        protected SearchCriteriaBuilder      $searchCriteriaBuilder,
        protected CategoryListInterface      $categoryList,
        protected Image                      $image,
        protected Configurable               $configurableType,
        protected AttributeRepository        $attributeRepository
    ) {
    }

    public function getProductData(string $categoryName): array
    {
        $products = $this->getProducts($categoryName);

        if ($products !== null) {
            foreach ($products as $product) {
                $imageUrl = $this->image
                    ->init($product, 'product_base_image')
                    ->getUrl();

                if ($product->getTypeId() === 'configurable') {
                    $this->processConfProduct($product, $imageUrl);
                    continue;
                }

                $parentsIds = $this->configurableType->getParentIdsByChild($product->getId());

                if (empty($parentsIds) === true) {
                    $this->allProductData[] = [
                        'image_url' => $imageUrl ?? null,
                        'name'      => $product->getName() ?? null,
                        'price'     => $product->getFinalPrice() ?? null,
                    ];
                }
            }
        }

        return $this->allProductData;
    }

    protected function processConfProduct(Product $product, string $imgUrl): void
    {
        $simpleProducts = $this->configurableType->getUsedProducts($product);
        $confOfProduct  = ['size' => [], 'color' => []];

        foreach ($simpleProducts as $simpleProduct) {
            // Отримання id кольора та розміра
            $colorId = $simpleProduct->getCustomAttribute('color') ? $simpleProduct->getCustomAttribute(
                'color'
            )->getValue() : null;
            $sizeId  = $simpleProduct->getCustomAttribute('size') ? $simpleProduct->getCustomAttribute(
                'size'
            )->getValue() : null;

            $colorText = $this->getAttributeText($colorId, 'catalog_product', 'color');
            $sizeText  = $this->getAttributeText($sizeId, 'catalog_product', 'size');

            if ($sizeText !== null && !in_array($sizeText, $confOfProduct['size'])) {
                $confOfProduct['size'][] = $sizeText;
            }
            if ($colorText !== null && !in_array($colorText, $confOfProduct['color'])) {
                $confOfProduct['color'][] = $colorText;
            }
        }

        $this->allProductData[] = [
            'image_url' => $imgUrl,
            'name'      => $product->getName() ?? null,
            'price'     => $product->getFinalPrice() ?? null,
            'conf'      => $confOfProduct
        ];
    }

    protected function getAttributeText($attributeId, $entityTypeCode, $attributeCode): string|null
    {
        if ($attributeId === null) {
            return null;
        }

        try {
            $attribute = $this->attributeRepository->get($entityTypeCode, $attributeCode);
        } catch (NoSuchEntityException) {
            return null;
        }

        // Перевіряємо, чи є опції
        if ($attribute->getFrontendInput() === 'select' || $attribute->getFrontendInput() === 'multiselect') {
            foreach ($attribute->getOptions() as $option) {
                if ($option['value'] == $attributeId) {
                    return $option['label']; // Повертаємо текстове значення
                }
            }
        }

        return null;
    }

    /**
     * @return ProductInterface[]|null
     */
    protected function getProducts(string $categoryName): array|null
    {
        $categoryId = $this->getCategoryIdByName($categoryName);

        if ($categoryId === null) {
            return null;
        }

        //DESC sort data by price field
        try {
            $this->sortOrder->setField(ProductInterface::PRICE);
            $this->sortOrder->setDirection(SortOrder::SORT_DESC);
        } catch (InputException) {
            return null;
        }

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('category_id', $categoryId)
            ->setSortOrders([$this->sortOrder])
            ->create();

        $products = $this->productRepository
            ->getList($searchCriteria)
            ->getItems();

        return $products;
    }

    protected function getCategoryIdByName(string $categoryName): int|null
    {
        $categories = $this->getAllCategories();

        foreach ($categories as $category) {
            if ($category->getName() === $categoryName) {
                return $category->getId();
            }
        }

        return null;
    }

    /**
     * @return CategoryInterface[]
     */
    protected function getAllCategories(): array
    {
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $categoryList   = $this->categoryList->getList($searchCriteria);

        return $categoryList->getItems();
    }
}
