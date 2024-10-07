<?php declare(strict_types=1);

namespace Phpro\BypassPageCache\Test\Integration\Plugin;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\PageCache\Kernel as PageCacheKernel;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\Config;
use Phpro\BypassPageCache\Plugin\BypassPageCache as BypassPageCachePlugin;
use PHPUnit\Framework\TestCase;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertInterceptorPluginIsRegistered;

class BypassPageCachePluginTest extends TestCase
{
    use AssertInterceptorPluginIsRegistered;

    #[AppArea('frontend')]
    public function testPluginConfiguration()
    {
        $this->assertInterceptorPluginIsRegistered(
            PageCacheKernel::class,
            BypassPageCachePlugin::class,
            'phpro_bypass_page_cache'
        );
    }

    public function testIfPluginDoesNothingIfNoConfiguration()
    {
        $plugin = ObjectManager::getInstance()->get(BypassPageCachePlugin::class);
        $kernel = $this->createMock(PageCacheKernel::class);
        $closure = function() { return true; };

        $this->assertTrue($plugin->aroundLoad($kernel, $closure));
    }

    #[Config('system/full_page_cache/bypass_enabled', 1)]
    #[Config('system/full_page_cache/bypass_header', 'X-Foobar')]
    public function testIfPluginDoesSomethingIfConfiguration()
    {
        $request = ObjectManager::getInstance()->get(\Magento\Framework\App\Request\Http::class);
        $request->getHeaders()->addHeaderLine('X-Foobar', '1');
        $plugin = ObjectManager::getInstance()->create(BypassPageCachePlugin::class, [
            'request' => $request,
        ]);

        $kernel = $this->createMock(PageCacheKernel::class);
        $closure = function() { return true; };

        $this->assertFalse($plugin->aroundLoad($kernel, $closure));
    }
}
