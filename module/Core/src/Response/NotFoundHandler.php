<?php
declare(strict_types=1);

namespace Shlinkio\Shlink\Core\Response;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response;
use Zend\Expressive\Template\TemplateRendererInterface;

use function array_shift;
use function explode;
use function Functional\contains;

class NotFoundHandler implements RequestHandlerInterface
{
    public const NOT_FOUND_TEMPLATE = 'notFoundTemplate';

    /** @var TemplateRendererInterface */
    private $renderer;
    /** @var string */
    private $defaultTemplate;

    public function __construct(TemplateRendererInterface $renderer, string $defaultTemplate = 'ShlinkCore::error/404')
    {
        $this->renderer = $renderer;
        $this->defaultTemplate = $defaultTemplate;
    }

    /**
     * Dispatch the next available middleware and return the response.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     * @throws \InvalidArgumentException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $accepts = explode(',', $request->getHeaderLine('Accept'));
        $accept = array_shift($accepts);
        $status = StatusCodeInterface::STATUS_NOT_FOUND;

        // If the first accepted type is json, return a json response
        if (contains(['application/json', 'text/json', 'application/x-json'], $accept)) {
            return new Response\JsonResponse([
                'error' => 'NOT_FOUND',
                'message' => 'Not found',
            ], $status);
        }

        $notFoundTemplate = $request->getAttribute(self::NOT_FOUND_TEMPLATE, $this->defaultTemplate);
        return new Response\HtmlResponse($this->renderer->render($notFoundTemplate), $status);
    }
}
