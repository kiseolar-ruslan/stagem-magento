<?php

/**
 * Copyright 2023 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\GraphQl\Quote\Guest;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Test\Fixture\Product as ProductFixture;
use Magento\Indexer\Test\Fixture\Indexer;
use Magento\Quote\Model\Cart\Data\CartItem;
use Magento\Quote\Model\QuoteIdToMaskedQuoteIdInterface;
use Magento\Quote\Test\Fixture\AddProductToCart as AddProductToCartFixture;
use Magento\Quote\Test\Fixture\GuestCart as GuestCartFixture;
use Magento\TestFramework\Fixture\DataFixture;
use Magento\TestFramework\Fixture\DataFixtureStorageManager;
use integration\framework\Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\GraphQlAbstract;

/**
 * Test for getting cart items information in paginated form
 */
#[
    DataFixture(ProductFixture::class, as: 'p1'),
    DataFixture(ProductFixture::class, as: 'p2'),
    DataFixture(ProductFixture::class, as: 'p3'),
    DataFixture(ProductFixture::class, as: 'p4'),
    DataFixture(ProductFixture::class, as: 'p5'),
    DataFixture(GuestCartFixture::class, as: 'cart'),
    DataFixture(Indexer::class, as: 'indexer'),
    DataFixture(
        AddProductToCartFixture::class,
        ['cart_id' => '$cart.id$', 'product_id' => '$p1.id$', 'qty' => 1],
        as: 'cart_item1'
    ),
    DataFixture(
        AddProductToCartFixture::class,
        ['cart_id' => '$cart.id$', 'product_id' => '$p2.id$', 'qty' => 1],
        as: 'cart_item2'
    ),
    DataFixture(
        AddProductToCartFixture::class,
        ['cart_id' => '$cart.id$', 'product_id' => '$p3.id$', 'qty' => 1],
        as: 'cart_item3'
    ),
    DataFixture(
        AddProductToCartFixture::class,
        ['cart_id' => '$cart.id$', 'product_id' => '$p4.id$', 'qty' => 1],
        as: 'cart_item4'
    ),
    DataFixture(
        AddProductToCartFixture::class,
        ['cart_id' => '$cart.id$', 'product_id' => '$p5.id$', 'qty' => 1],
        as: 'cart_item5'
    ),
]
class GetCartPaginatedItemsTest extends GraphQlAbstract
{
    /**
     * @var DataFixtureStorageManager
     */
    private $fixtures;

    /**
     * @var QuoteIdToMaskedQuoteIdInterface
     */
    private $quoteIdToMaskedQuoteIdInterface;

    protected function setUp(): void
    {
        $objectManager                         = Bootstrap::getObjectManager();
        $this->fixtures                        = $objectManager->get(DataFixtureStorageManager::class)->getStorage();
        $this->quoteIdToMaskedQuoteIdInterface = $objectManager->get(QuoteIdToMaskedQuoteIdInterface::class);
    }

    public function testGetCartWithZeroPageSize()
    {
        $this->expectExceptionMessage('pageSize value must be greater than 0.');
        $cart          = $this->fixtures->get('cart');
        $maskedQuoteId = $this->quoteIdToMaskedQuoteIdInterface->execute((int)$cart->getId());
        $query         = $this->getQuery($maskedQuoteId, 0, 1);
        $this->graphQlQuery($query);
    }

    public function testGetCartWithZeroCurrentPage()
    {
        $this->expectExceptionMessage('currentPage value must be greater than 0.');
        $cart          = $this->fixtures->get('cart');
        $maskedQuoteId = $this->quoteIdToMaskedQuoteIdInterface->execute((int)$cart->getId());
        $query         = $this->getQuery($maskedQuoteId, 1, 0);
        $this->graphQlQuery($query);
    }

    public function testGetCart()
    {
        /** @var Product $product1 */
        $product1 = $this->fixtures->get('p1');

        /** @var Product $product2 */
        $product2 = $this->fixtures->get('p2');

        /** @var CartItem $cartItem1 */
        $cartItem1 = $this->fixtures->get('cart_item1');

        /** @var CartItem $cartItem2 */
        $cartItem2 = $this->fixtures->get('cart_item2');

        $cart = $this->fixtures->get('cart');

        $maskedQuoteId = $this->quoteIdToMaskedQuoteIdInterface->execute((int)$cart->getId());
        $query         = $this->getQuery($maskedQuoteId, 2, 1);

        $response = $this->graphQlQuery($query);
        $expected = [
            'cart' => [
                'id'      => $maskedQuoteId,
                'itemsV2' => [
                    'total_count' => 5,
                    'items'       => [
                        [
                            'id'       => $cartItem1->getId(),
                            'quantity' => 1,
                            'product'  => [
                                'sku'          => $product1->getSku(),
                                'stock_status' => 'IN_STOCK',
                            ],
                            'prices'   => [
                                'price' => [
                                    'value'    => 10,
                                    'currency' => 'USD',
                                ]
                            ],
                            'errors'   => null
                        ],
                        [
                            'id'       => $cartItem2->getId(),
                            'quantity' => 1,
                            'product'  => [
                                'sku'          => $product2->getSku(),
                                'stock_status' => 'IN_STOCK',
                            ],
                            'prices'   => [
                                'price' => [
                                    'value'    => 10,
                                    'currency' => 'USD',
                                ]
                            ],
                            'errors'   => null
                        ],
                    ],
                    'page_info'   => [
                        'page_size'    => 2,
                        'current_page' => 1,
                        'total_pages'  => 3,
                    ]
                ],
            ]
        ];
        $this->assertEquals(
            $expected,
            $response,
            sprintf("Expected:\n%s\ngot:\n%s", json_encode($expected), json_encode($response))
        );
    }

    /**
     * @param string $maskedQuoteId
     * @param int $pageSize
     * @param int $currentPage
     * @return string
     */
    private function getQuery(string $maskedQuoteId, int $pageSize, int $currentPage): string
    {
        return <<<QUERY
{
  cart(cart_id: "{$maskedQuoteId}") {
    id
    itemsV2(pageSize: {$pageSize} currentPage: {$currentPage}) {
      total_count
      items {
        id
        quantity
        product {
          sku
          stock_status
        }
        prices {
          price {
            value
            currency
          }
        }
        errors {
          code
          message
        }
      }
      page_info {
        page_size
        current_page
        total_pages
      }
    }
  }
}
QUERY;
    }
}
