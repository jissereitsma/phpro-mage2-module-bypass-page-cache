<?php

declare(strict_types=1);

namespace Phpro\BypassPageCache\Test\Integration;

use PHPUnit\Framework\TestCase;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertModuleIsEnabled;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertModuleIsRegistered;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertModuleIsRegisteredForReal;

final class ModuleTest extends TestCase
{
    use AssertModuleIsEnabled;
    use AssertModuleIsRegistered;
    use AssertModuleIsRegisteredForReal;

    final public function testModule()
    {
        $moduleName = 'Phpro_BypassPageCache';

        $this->assertModuleIsEnabled($moduleName);
        $this->assertModuleIsRegistered($moduleName);
        $this->assertModuleIsRegisteredForReal($moduleName);
    }
}
