<?php

declare(strict_types=1);

namespace Renttek\SurrogateKey\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Renttek\SurrogateKey\Model\Config\Source\HeaderStyle;

readonly class Config
{
    public const string SURROGATE_KEY_ENABLED              = 'system/full_page_cache/surrogate_key/enabled';
    public const string SURROGATE_KEY_KEEP_ORIGINAL_HEADER = 'system/full_page_cache/surrogate_key/keep_original_header';
    public const string SURROGATE_KEY_LIST_STYLE           = 'system/full_page_cache/surrogate_key/key_list_style';

    public function __construct(
        private ScopeConfigInterface $scopeConfig,
    ) {}

    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::SURROGATE_KEY_ENABLED);
    }

    public function shouldKeepOriginalHeader(): bool
    {
        return $this->scopeConfig->isSetFlag(self::SURROGATE_KEY_KEEP_ORIGINAL_HEADER);
    }

    /**
     * @phpstan-return HeaderStyle::TYPE_*
     */
    public function getKeyListStyle(): string
    {
        $style = $this->scopeConfig->getValue(self::SURROGATE_KEY_LIST_STYLE);

        return match ($style) {
            HeaderStyle::TYPE_FASTLY => HeaderStyle::TYPE_FASTLY,
            default => HeaderStyle::TYPE_SOUIN,
        };
    }
}
