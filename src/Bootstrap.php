<?php

declare(strict_types = 1);

namespace BrightLogic\Template;

use \BrightLogic\Template\Config\Environment\Environment;
use \BrightLogic\Template\Config\Execution\Execution;

final class Bootstrap
{
    use \Nette\StaticClass;

    public const PROJECT_ROOT = __DIR__ . '/../../../..';
    public const APP_ROOT = self::PROJECT_ROOT . '/app';
    public static Environment $environment;
    public static Execution $execution;

    public static function boot() : void
    {
        self::validateGlobals();
        self::getContainer()->getByType(\Nette\Application\Application::class)->run();
    }

    public static function bootConsole() : void
    {
        self::getContainer()->getByType(\Contributte\Console\Application::class)->run();
    }

    public static function getContainer() : \Nette\DI\Container
    {
        return self::getConfigurator()
            ->createContainer();
    }

    public static function isDebugMode() : bool
    {
        return self::getEnvironment('DEBUG_MODE') === 'yes';
    }

    public static function getEnvironment(string $name) : ?string
    {
         return \getenv($name, true) ?: null; // @phpstan-ignore-line
    }

    private static function getConfigurator() : \Nette\Bootstrap\Configurator
    {
        \chdir(__DIR__ . '/../bin');
        self::$environment = self::initEnvironment();
        self::$execution = self::initExecution();
        $tempDir =
            self::$environment->getBaseTempDirectory() .
            self::$execution->getSecondLevelTempDirectory();

        if (!\file_exists($tempDir)) {
            \mkdir($tempDir, recursive: true);
        }

        $configurator = new \Nette\Bootstrap\Configurator();
        $configurator->onCompile[] = static function (\Nette\Bootstrap\Configurator $sender, \Nette\DI\Compiler $compiler) : void {
            $compiler->addConfig(['parameters' => self::getParams()]);
        };
        $configurator->setDebugMode(self::isDebugMode());
        $configurator->setTempDirectory($tempDir);
        $configurator->enableTracy();
        $configurator->addStaticParameters([
            'appDir' => self::APP_ROOT,
            'wwwDir' => self::PROJECT_ROOT . '/www',
            'vendorDir' => self::PROJECT_ROOT . '/vendor',
        ]);

        foreach (self::getConfigFiles() as $file) {
            $configurator->addConfig($file);
        }

        return $configurator;
    }

    /**
     * @return array<string>
     */
    private static function getConfigFiles() : array
    {
        $files = [
            __DIR__ . '/Config/core.neon',
            self::$environment->getFile(),
            self::$execution->getFile(),
        ];

        foreach ($files as $file) {
            if (!\file_exists($file)) {
                throw new \Nette\InvalidStateException('Config file ' . $file . ' does not exist.');
            }
        }

        return $files;
    }

    private static function validateGlobals() : void
    {
        if (!\array_key_exists('HTTP_X_FORWARDED_PROTO', $_SERVER)) {
            return;
        }

        if ($_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') { // https over proxy
            $_SERVER['HTTPS'] = 'On';
            $_SERVER['SERVER_PORT'] = 443;
        } elseif ($_SERVER['HTTP_X_FORWARDED_PROTO'] === 'http') { // http over proxy
            $_SERVER['HTTPS'] = 'Off';
        }
    }

    private static function initEnvironment() : Environment
    {
        return self::isDebugMode()
            ? new \BrightLogic\Template\Config\Environment\LocalEnvironment()
            : new \BrightLogic\Template\Config\Environment\ProductionEnvironment();
    }

    private static function initExecution() : Execution
    {
        return self::getEnvironment('PHPUNIT_MODE') === 'yes'
            ? new \BrightLogic\Template\Config\Execution\TestsExecution()
            : new \BrightLogic\Template\Config\Execution\DefaultExecution();
    }

    /**
     * @return array<string, string>
     */
    public static function getParams() : array
    {
        if (self::isDebugMode()) {
            return [];
        }

        $searchString = 'nemovizor';
        $clientConfig = [
            'region' => 'eu-central-1',
            'version' => 'latest',
        ];

        $return = [];

        $fifoIndex = 0;
        $standardIndex = 0;

        foreach (self::getSQSUrls($searchString, $clientConfig) as $url) {
            if (\str_ends_with($url, '.fifo')) {
                $return['aws_sqs_fifo_' . $fifoIndex++] = $url;

                continue;
            }

            $return['aws_sqs_standard_' . $standardIndex++] = $url;
        }

        $redisIndex = 0;
        $memcachedIndex = 0;

        foreach (self::getElastiCacheUrls($searchString, $clientConfig) as $cacheCluster) {
            $key = match ($cacheCluster['Engine']) {
                'redis' => 'aws_elasticache_redis_' . $redisIndex++,
                'memcached' => 'aws_elasticache_memcached_' . $memcachedIndex++,
                default => 'aws_elasticache_unknown',
            };

            $return[$key . '_url'] = $cacheCluster['ConfigurationEndpoint']['Address'];
            $return[$key . '_port'] = $cacheCluster['ConfigurationEndpoint']['Port'];
        }

        $bucketIndex = 0;
        $host = '.amazonaws.com/';

        foreach (self::getS3Urls($searchString, $clientConfig) as $bucketName) {
            if (\str_contains($bucketName, 'serverlessdeployment')) {
                continue;
            }

            $key = 'aws_s3_' . $bucketIndex++;
            $return[$key. '_name'] = $bucketName;
            $return[$key. '_url'] = 'https://' . $bucketName . '.s3.' . $clientConfig['region'] . $host;
        }

        return $return;
    }

    /**
     * @return array<string>
     */
    private static function getSQSUrls(string $searchString, array $clientConfig) : array
    {
        $client = new \Aws\Sqs\SqsClient($clientConfig);
        $return = [];

        foreach ($client->listQueues()->get('QueueUrls') as $queue) {
            if (\str_contains($queue, $searchString)) {
                $return[] = $queue;
            }
        }

        return $return;
    }

    private static function getElasticacheUrls(string $searchString, array $clientConfig) : array
    {
        $client = new \Aws\ElastiCache\ElastiCacheClient($clientConfig);
        $return = [];

        foreach ($client->describeCacheClusters()->get('CacheClusters') as $cluster) {
            if (\str_contains($cluster['CacheClusterId'], $searchString)) {
                $return[] = $cluster;
            }
        }

        return $return;
    }

    /**
     * @return array<string>
     */
    private static function getS3Urls(string $searchString, array $clientConfig) : array
    {
        $client = new \Aws\S3\S3Client($clientConfig);
        $return = [];

        foreach ($client->listBuckets()->get('Buckets') as $bucket) {
            if (\str_contains($bucket['Name'], $searchString)) {
                $return[] = $bucket['Name'];
            }
        }

        return $return;
    }
}
