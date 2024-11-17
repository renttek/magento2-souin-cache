<?php

declare(strict_types=1);

namespace Renttek\SouinCache\Model;

use Assert\Assertion;
use Assert\AssertionFailedException;
use Laminas\Uri\Uri;
use Laminas\Uri\UriFactory;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\Config\ConfigOptionsListConstants;

/**
 * @phpstan-type HttpCacheHostConfig = array{
 *     host: string,
 *     port?: int,
 *     scheme?: string,
 *     api_basepath?: string,
 *     souin_api_basepath?: string
 * }
 */
class Server
{
    public function __construct(
        private DeploymentConfig $deploymentConfig,
    ) {}

    /**
     * @return array<Uri>
     */
    public function getUris(): array
    {
        $httpCacheHosts = (array)$this->deploymentConfig->get(ConfigOptionsListConstants::CONFIG_PATH_CACHE_HOSTS, []);

        return array_map($this->createUriFromConfig(...), $httpCacheHosts);
    }

    /**
     * @phpstan-param HttpCacheHostConfig $httpCacheHost
     */
    private function createUriFromConfig(array $httpCacheHost): Uri
    {
        try {
            Assertion::keyExists($httpCacheHost, 'host');
            Assertion::string($httpCacheHost['host']);
            Assertion::notBlank($httpCacheHost['host']);
        } catch (AssertionFailedException $e) {
        }

        $scheme           = $httpCacheHost['scheme'] ?? 'http';
        $host             = $httpCacheHost['host'];
        $port             = $httpCacheHost['port'] ?? 80;
        $apiBasepath      = trim($httpCacheHost['api_basepath'] ?? 'souin-api', '/');
        $souinApiBasepath = trim($httpCacheHost['souin_api_basepath'] ?? 'souin', '/');

        return UriFactory::factory('')
                         ->setScheme($scheme)
                         ->setHost($host)
                         ->setPort($port)
                         ->setPath('/' . $apiBasepath . '/' . $souinApiBasepath);
    }
}
