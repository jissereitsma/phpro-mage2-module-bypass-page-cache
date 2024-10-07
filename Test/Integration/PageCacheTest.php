<?php declare(strict_types=1);

namespace Phpro\BypassPageCache\Test\Integration;

use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\Response\Http;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\AppIsolation;
use Magento\TestFramework\Fixture\Cache;
use Magento\TestFramework\Fixture\Config;
use Magento\TestFramework\TestCase\AbstractController;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertResponseHeader;

#[AppIsolation(true)]
#[AppArea('frontend')]
#[Cache('full_page', true)]
class PageCacheTest extends AbstractController
{
    use AssertResponseHeader;

    #[Config('system/full_page_cache/bypass_enabled', 0)]
    public function testFullPageCacheBypassDisabled()
    {
        $this->dispatch('cms/page/view/id/1');

        /** @var Http $response */
        $response = $this->getResponse();
        $this->assertEmpty($response->getBody());
        $this->assertEmpty($response->getHeaders()->toArray());
    }

    #[Config('system/full_page_cache/bypass_header', 'X-Foobar')]
    #[Config('system/full_page_cache/bypass_enabled', 1)]
    public function testFullPageCacheBypassEnabled()
    {
        /** @var HttpRequest $request */
        $request = $this->getRequest();
        $request->getHeaders()->addHeaderLine('X-Foobar', 'bar');

        $this->dispatch('cms/page/view/id/1');

        $this->assertResponseHeadersNotEmpty();
        $this->assertResponseHeaderValue('X-Magento-Cache-Debug', 'MISS');
    }
}
