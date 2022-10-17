<?php

namespace Cmsrs\Laracms\Tests\Feature;

//use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Cmsrs\Laracms\Models\Page;
use Cmsrs\Laracms\Models\User;
use Cmsrs\Laracms\Models\Menu;
use Cmsrs\Laracms\Models\Config;
use Cmsrs\Laracms\Models\Data\Demo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class HomeTest extends Base
{
    use RefreshDatabase;

    private $testData;
    private $testDataMenu;
    private $menuId;
    private $menuObj;
    private $titleEn = 'eeeeeeeeeeeeeeeennnnnnnnnnnnnnnnn';
    private $titlePl = 'pppppppppppppppplllllllllllllllll';
    private $langs;



    public function setUp(): void
    {
        putenv('LANGS="pl,en"');
        putenv('API_SECRET=""');
        putenv('APP_KEY="test123rs"');
        parent::setUp();
        //$this->createUser();
        $this->createClientUser();
    }

    protected function tearDown(): void
    {
        $this->deleteUser();
        parent::tearDown();
    }

    /** @test */
    public function it_will_api_home_token()
    {
        $this->assertAuthenticated();
        $token = User::getTokenForClient();
        $this->assertNotEmpty($token);

        $user = Auth::user();
        $check = $user->checkClientByToken($token);
        $this->assertTrue($check);

        $staticCheck = User::checkApiClientByToken($token);
        $this->assertTrue($staticCheck);
    }

}