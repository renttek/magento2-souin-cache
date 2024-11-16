<?php

declare(strict_types=1);

namespace Renttek\SurrogateKey\Plugin\Controller\Result;

use Laminas\Http\Header\HeaderInterface;
use Magento\PageCache\Model\Config as PageCacheConfig;
use Magento\Framework\App\Response\Http as ResponseHttp;
use Magento\Framework\Controller\ResultInterface;
use Renttek\SurrogateKey\Model\Config;

class SetSurrogateKeyHeader
{
    public function __construct(
        private PageCacheConfig $pageCacheConfig,
        private Config $config,
    ) {
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterRenderResult(
        ResultInterface $subject,
        ResultInterface $result,
        ResponseHttp $response
    ): ResultInterface {
        if (!$this->isEnabled()) {
            return $result;
        }

        $cacheTagsHeader = $response->getHeader('X-Magento-Tags');
        if (!$cacheTagsHeader instanceof HeaderInterface) {
            return $result;
        }

        $response->setHeader('Surrogate-Key', $cacheTagsHeader->getFieldValue());
        $response->clearHeader('X-Magento-Tags');

        return $result;
    }

    private function isEnabled(): bool
    {
        return $this->pageCacheConfig->getType() === PageCacheConfig::VARNISH
            && $this->pageCacheConfig->isEnabled()
            && $this->config->isEnabled();
    }
}
