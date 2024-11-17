<?php

declare(strict_types=1);

namespace Renttek\SouinCache\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Renttek\SouinCache\Model\Config\Source\HeaderStyle;

readonly class Config
{
    public const string SOUIN_CACHE_ENABLED              = 'system/full_page_cache/souin_cache/enabled';
    public const string SOUIN_CACHE_KEEP_ORIGINAL_HEADER = 'system/full_page_cache/souin_cache/keep_original_header';
    public const string SOUIN_CACHE_KEY_LIST_STYLE           = 'system/full_page_cache/souin_cache/key_list_style';

    public function __construct(
        private ScopeConfigInterface $scopeConfig,
    ) {}

    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::SOUIN_CACHE_ENABLED);
    }

    public function shouldKeepOriginalHeader(): bool
    {
        return $this->scopeConfig->isSetFlag(self::SOUIN_CACHE_KEEP_ORIGINAL_HEADER);
    }

    /**
     * @phpstan-return HeaderStyle::TYPE_*
     */
    public function getKeyListStyle(): string
    {
        $style = $this->scopeConfig->getValue(self::SOUIN_CACHE_KEY_LIST_STYLE);

        return match ($style) {
            HeaderStyle::TYPE_FASTLY => HeaderStyle::TYPE_FASTLY,
            default => HeaderStyle::TYPE_SOUIN,
        };
    }
}
