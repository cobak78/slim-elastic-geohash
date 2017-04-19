<?php

namespace Cobak78\Controller;

use Cobak78\Services\GeoService;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\PhpRenderer;

class IndexAction
{
    /**
     * @var GeoService
     */
    private $geoService;

    /**
     * @var PhpRenderer
     */
    private $renderer;

    public function __construct(PhpRenderer $renderer, GeoService $geoService)
    {
        $this->geoService = $geoService;
        $this->renderer = $renderer;
    }

    public function __invoke(Request $request, Response $response, array $args)
    {
        return $this->renderer->render(
            $response, 'index.phtml', $this->geoService->doYerThingee()
        );
    }
}
