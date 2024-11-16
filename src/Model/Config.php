<?php

declare(strict_types=1);

namespace Renttek\SurrogateKey\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

readonly class Config
{
    public const SURROGATE_KEY_ENABLED = 'system/full_page_cache/surrogate_key/enabled';

    public function __construct(
        private ScopeConfigInterface $scopeConfig,
    ) {
    }

    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::SURROGATE_KEY_ENABLED);
    }
}
