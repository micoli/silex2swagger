<?php

namespace Radebatz\Silex\Swagger\Tests\Silex;

use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Swagger\Annotations as SWG;

/**
 * @SLX\Controller(prefix="/silex")
 */
class SilexPrefixController
{
    /**
     * @SLX\Route(
     *   @SLX\Request(method="GET", uri="get"),
     *   @SWG\Response(response=200, description="GET")
     * )
     */
    public function testGetRequest()
    {
        return '';
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="GET", uri="/requirehttp"),
     *   @SLX\RequireHttp,
     *   @SWG\Response(response=200, description="GET")
     * )
     */
    public function testRequireHttp()
    {
        return '';
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="GET", uri="/requirehttps"),
     *   @SLX\RequireHttps
     * )
     */
    public function testRequireHttp()
    {
        return '';
    }
}
