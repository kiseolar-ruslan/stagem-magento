<?php

namespace Stagem\OrderToXml\Service;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Convert\ConvertArray;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\FileSystem\Io\File;

class GenerateXml
{
    protected const DIR_NAME          = 'Orders';
    protected const DEFAULT_FILE_NAME = 'products';


    public function __construct(
        protected File         $file,
        protected ConvertArray $convertArray,
        protected JsonFactory  $resultJsonFactory
    ) {
    }

    public function generateXml(array $data, string $fileName = self::DEFAULT_FILE_NAME): bool
    {
        try {
            $products = [
                'order' => $data
            ];

            $xmlContents = $this->convertArray->assocToXml($products, 'orders');
            $content     = $xmlContents->asXML();

            if (is_dir(self::DIR_NAME) === false) {
                mkdir(self::DIR_NAME);
            }

            $filePath = self::DIR_NAME . '/' . $fileName . '.xml';
            $this->file->write($filePath, $content);

            $result = true;
        } catch (LocalizedException) {
            $result = false;
        }

        return $result;
    }
}
