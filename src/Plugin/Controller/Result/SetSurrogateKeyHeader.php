<?php

declare(strict_types=1);

namespace Renttek\SurrogateKey\Plugin\Controller\Result;

use Laminas\Http\Header\HeaderInterface;
use Magento\Framework\App\Response\Http as ResponseHttp;
use Magento\Framework\Controller\ResultInterface;
use Magento\PageCache\Model\Config as PageCacheConfig;
use Renttek\SurrogateKey\Model\Config;

class SetSurrogateKeyHeader
{
    public function __construct(
        private PageCacheConfig $pageCacheConfig,
        private Config          $config,
    ) {}

    public function afterRenderResult(
        ResultInterface $subject,
        ResultInterface $result,
        ResponseHttp    $response,
    ): ResultInterface {
        if (!$this->isEnabled()) {
            return $result;
        }

        $cacheKeys = $this->getKeys($response);
        if ($cacheKeys === null) {
            return $result;
        }

        $response->setHeader('Surrogate-Key', $cacheKeys);

        if (!$this->config->shouldKeepOriginalHeader()) {
            $response->clearHeader('X-Magento-Tags');
        }

        return $result;
    }

    private function getKeys(ResponseHttp $response): ?string
    {
        $cacheTagsHeader = $response->getHeader('X-Magento-Tags');
        if (!$cacheTagsHeader instanceof HeaderInterface) {
            return null;
        }

        $cacheTags = $cacheTagsHeader->getFieldValue();

        return $this->config->getKeyListStyle() === Config\Source\HeaderStyle::TYPE_FASTLY
            ? implode(' ', explode(',', $cacheTags))
            : $cacheTags;
    }

    private function isEnabled(): bool
    {
        return $this->pageCacheConfig->getType() === PageCacheConfig::VARNISH
            && $this->pageCacheConfig->isEnabled()
            && $this->config->isEnabled();
    }
}
