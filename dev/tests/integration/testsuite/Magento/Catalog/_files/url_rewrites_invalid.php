<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

/**
 * Extends valid Url rewrites
 */
Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/url_rewrites.php');

/**
 * Invalid rewrite for product assigned to different category
 */
/** @var $rewrite \Magento\UrlRewrite\Model\UrlRewrite */
$rewrite = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    \Magento\UrlRewrite\Model\UrlRewrite::class
);
$rewrite->setStoreId(
    1
)->setIdPath(
    'product/1/4'
)->setRequestPath(
    'category-2/simple-product.html'
)->setTargetPath(
    'catalog/product/view/id/1'
)->setIsSystem(
    1
)->setCategoryId(
    4
)->setProductId(
    1
)->save();

/**
 * Invalid rewrite for product assigned to category that doesn't belong to store
 */
$rewrite = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    \Magento\UrlRewrite\Model\UrlRewrite::class
);
$rewrite->setStoreId(
    1
)->setIdPath(
    'product/1/5'
)->setRequestPath(
    'category-5/simple-product.html'
)->setTargetPath(
    'catalog/product/view/id/1'
)->setIsSystem(
    1
)->setCategoryId(
    5
)->setProductId(
    1
)->save();
