<?php declare(strict_types=1);

namespace Phpro\BypassPageCache\Test\Integration\Config;

use Magento\Framework\App\ObjectManager;
use Magento\TestFramework\Fixture\Config;
use Phpro\BypassPageCache\Config\SystemConfiguration;
use PHPUnit\Framework\TestCase;

class SystemConfigurationTest extends TestCase
{
    #[Config('system/full_page_cache/bypass_enabled', 1)]
    public function testIsPageCacheBypassEnabled()
    {
        $systemConfiguration = ObjectManager::getInstance()->get(SystemConfiguration::class);
        $this->assertSame(true, $systemConfiguration->isPageCacheBypassEnabled());
    }

    #[Config('system/full_page_cache/bypass_enabled', 0)]
    public function testIsPageCacheBypassDisabled()
    {
        $systemConfiguration = ObjectManager::getInstance()->get(SystemConfiguration::class);
        $this->assertSame(false, $systemConfiguration->isPageCacheBypassEnabled());
    }

    #[Config('system/full_page_cache/bypass_header', 'X-Foobar')]
    public function testGetHttpHeader()
    {
        $systemConfiguration = ObjectManager::getInstance()->get(SystemConfiguration::class);
        $this->assertEquals('X-Foobar', $systemConfiguration->getHttpHeader());
    }
}
