<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\MediaGalleryApi\Api\DeleteAssetsByPathsInterface;
use integration\framework\Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var DeleteAssetsByPathsInterface $mediaSave */
$mediaAssetDelete = $objectManager->get(DeleteAssetsByPathsInterface::class);

try {
    $mediaAssetDelete->execute(['testDirectory/path.jpg']);
} catch (\Exception $exception) {

}
