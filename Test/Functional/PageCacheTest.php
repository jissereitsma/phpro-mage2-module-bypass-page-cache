<?php declare(strict_types=1);

namespace Phpro\BypassPageCache\Test\Functional;

use GuzzleHttp\Client;
use Magento\Framework\App\Cache\Manager;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\App\Config\Storage\WriterInterface as ConfigStorageWriter;

class PageCacheTest extends TestCase
{
    public function testIfPageCacheIsWorkingByDefault()
    {
        $configStorageWriter = ObjectManager::getInstance()->get(ConfigStorageWriter::class);
        $configStorageWriter->save('system/full_page_cache/bypass_enabled', 0);

        $cacheManager = ObjectManager::getInstance()->get(Manager::class);
        $cacheManager->setEnabled(['full_page'], true);
        $cacheManager->clean(['full_page', 'config']);

        $storeManager = ObjectManager::getInstance()->get(StoreManagerInterface::class);
        $baseUrl = $storeManager->getDefaultStoreView()->getBaseUrl();

        $client = new Client();
        $response = $client->get($baseUrl . '/');
        $headerDump = var_export($response->getHeaders(), true);
        $this->assertContains('MISS', $response->getHeader('X-Magento-Cache-Debug'), $headerDump);

        $response = $client->get($baseUrl . '/');
        $headerDump = var_export($response->getHeaders(), true);
        $this->assertContains('HIT', $response->getHeader('X-Magento-Cache-Debug'), $headerDump);
    }

    public function testIfPageCacheIsBypassed()
    {
        $configStorageWriter = ObjectManager::getInstance()->get(ConfigStorageWriter::class);
        $configStorageWriter->save('system/full_page_cache/bypass_header', 'X-Foobar');
        $configStorageWriter->save('system/full_page_cache/bypass_enabled', 1);

        $cacheManager = ObjectManager::getInstance()->get(Manager::class);
        $cacheManager->setEnabled(['full_page'], true);
        $cacheManager->clean(['full_page', 'config']);

        $storeManager = ObjectManager::getInstance()->get(StoreManagerInterface::class);
        $baseUrl = $storeManager->getDefaultStoreView()->getBaseUrl();

        $client = new Client();
        $response = $client->get($baseUrl . '/', [
            'headers' => ['X-Foobar' => 1]
        ]);

        $headerDump = var_export($response->getHeaders(), true);
        $this->assertContains('MISS', $response->getHeader('X-Magento-Cache-Debug'), $headerDump);

        $response = $client->get($baseUrl . '/', [
            'headers' => ['X-Foobar' => 1]
        ]);

        $headerDump = var_export($response->getHeaders(), true);
        $this->assertContains('MISS', $response->getHeader('X-Magento-Cache-Debug'), $headerDump);
    }
}
