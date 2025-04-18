<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\GraphQl\UrlRewrite;

use Magento\Framework\Exception\AlreadyExistsException;
use integration\framework\Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\TestCase\GraphQlAbstract;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewrite as UrlRewriteResourceModel;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Model\UrlRewrite as UrlRewriteModel;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite as UrlRewriteService;

/**
 * Test the GraphQL endpoint's URLResolver query to verify canonical URL's are correctly returned.
 */
class UrlResolverTest extends GraphQlAbstract
{
    /** @var ObjectManager */
    private $objectManager;

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }

    /**
     * Test for custom type which point to the invalid product/category/cms page.
     *
     * @magentoApiDataFixture Magento/UrlRewrite/_files/url_rewrite_not_existing_entity.php
     */
    public function testNonExistentEntityUrlRewrite()
    {
        $urlPath = 'non-exist-entity.html';

        $query = <<<QUERY
{
  urlResolver(url:"{$urlPath}")
  {
   id
   entity_uid
   relative_url
   type
   redirectCode
  }
}
QUERY;

        $this->expectExceptionMessage(
            "No such entity found with matching URL key: " . $urlPath
        );
        $this->graphQlQuery($query);
    }

    /**
     * Test for url rewrite to clean cache on rewrites update
     *
     * @magentoApiDataFixture Magento/Catalog/_files/product_with_category.php
     * @magentoApiDataFixture Magento/Cms/_files/pages.php
     *
     * @dataProvider urlRewriteEntitiesDataProvider
     * @param string $requestPath
     * @throws AlreadyExistsException
     */
    public function testUrlRewriteCleansCacheOnChange(string $requestPath)
    {
        /** @var UrlRewriteResourceModel $urlRewriteResourceModel */
        $urlRewriteResourceModel = $this->objectManager->create(UrlRewriteResourceModel::class);
        $storeId                 = 1;
        $query                   = function ($requestUrl) {
            return <<<QUERY
{
  urlResolver(url:"{$requestUrl}")
  {
   id
   entity_uid
   relative_url
   type
   redirectCode
  }
}
QUERY;
        };

        // warming up urlResolver API response cache for entity and validate proper response
        $apiResponse = $this->graphQlQuery($query($requestPath));
        $this->assertEquals($requestPath, $apiResponse['urlResolver']['relative_url']);

        $urlRewrite = $this->getUrlRewriteModelByRequestPath($requestPath, $storeId);

        // renaming entity request path and validating that API will not return cached response
        $urlRewrite->setRequestPath('test' . $requestPath);
        $urlRewriteResourceModel->save($urlRewrite);
        $apiResponse = $this->graphQlQuery($query($requestPath));
        $this->assertNull($apiResponse['urlResolver']);

        // rolling back changes
        $urlRewrite->setRequestPath($requestPath);
        $urlRewriteResourceModel->save($urlRewrite);
    }

    public function urlRewriteEntitiesDataProvider(): array
    {
        return [
            [
                'simple-product-in-stock.html'
            ],
            [
                'category-1.html'
            ],
            [
                'page100'
            ]
        ];
    }

    /**
     * Test for custom url rewrite to clean cache on update combinations
     *
     * @magentoApiDataFixture Magento/Catalog/_files/product_with_category.php
     * @magentoApiDataFixture Magento/Cms/_files/pages.php
     *
     * @throws AlreadyExistsException
     */
    public function testUrlRewriteCleansCacheForCustomRewrites()
    {
        /** @var UrlRewriteResourceModel $urlRewriteResourceModel */
        $urlRewriteResourceModel = $this->objectManager->create(UrlRewriteResourceModel::class);
        $storeId                 = 1;
        $query                   = function ($requestUrl) {
            return <<<QUERY
{
  urlResolver(url:"{$requestUrl}")
  {
   id
   entity_uid
   relative_url
   type
   redirectCode
  }
}
QUERY;
        };

        $customRequestPath       = 'test.html';
        $customSecondRequestPath = 'test2.html';
        $entitiesRequestPaths    = [
            'simple-product-in-stock.html',
            'category-1.html',
            'page100'
        ];

        // create custom url rewrite
        $urlRewrite = $this->objectManager->create(UrlRewriteModel::class);
        $urlRewrite->setEntityType('custom')
                   ->setRedirectType(302)
                   ->setStoreId($storeId)
                   ->setDescription(null)
                   ->setIsAutogenerated(0);

        // create second custom url rewrite and target it to previous one to check
        // if proper final target url will be resolved
        $secondUrlRewrite = $this->objectManager->create(UrlRewriteModel::class);
        $secondUrlRewrite->setEntityType('custom')
                         ->setRedirectType(302)
                         ->setStoreId($storeId)
                         ->setRequestPath($customSecondRequestPath)
                         ->setTargetPath($customRequestPath)
                         ->setDescription(null)
                         ->setIsAutogenerated(0);
        $urlRewriteResourceModel->save($secondUrlRewrite);

        foreach ($entitiesRequestPaths as $entityRequestPath) {
            // updating custom rewrite for each entity
            $urlRewrite->setRequestPath($customRequestPath)
                       ->setTargetPath($entityRequestPath);
            $urlRewriteResourceModel->save($urlRewrite);

            // confirm that API returns non-cached response for the first custom rewrite
            $apiResponse = $this->graphQlQuery($query($customRequestPath));
            $this->assertEquals($entityRequestPath, $apiResponse['urlResolver']['relative_url']);

            // confirm that API returns non-cached response for the second custom rewrite
            $apiResponse = $this->graphQlQuery($query($customSecondRequestPath));
            $this->assertEquals($entityRequestPath, $apiResponse['urlResolver']['relative_url']);
        }

        $urlRewriteResourceModel->delete($secondUrlRewrite);

        // delete custom rewrite and validate that API will not return cached response
        $urlRewriteResourceModel->delete($urlRewrite);
        $apiResponse = $this->graphQlQuery($query($customRequestPath));
        $this->assertNull($apiResponse['urlResolver']);
    }

    /**
     * Return UrlRewrite model instance by request_path
     *
     * @param string $requestPath
     * @param int $storeId
     * @return UrlRewriteModel
     */
    private function getUrlRewriteModelByRequestPath(string $requestPath, int $storeId): UrlRewriteModel
    {
        /** @var  UrlFinderInterface $urlFinder */
        $urlFinder = $this->objectManager->get(UrlFinderInterface::class);

        /** @var UrlRewriteService $urlRewriteService */
        $urlRewriteService = $urlFinder->findOneByData(
            [
                'request_path' => $requestPath,
                'store_id'     => $storeId
            ]
        );

        /** @var UrlRewriteModel $urlRewrite */
        $urlRewrite = $this->objectManager->create(UrlRewriteModel::class);
        $urlRewrite->load($urlRewriteService->getUrlRewriteId());

        return $urlRewrite;
    }

}
