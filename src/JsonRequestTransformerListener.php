<?php

declare(strict_types=1);

/*
 * This file is part of the qandidate/symfony-json-request-transformer package.
 *
 * (c) Qandidate.com <opensource@qandidate.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Qandidate\Common\Symfony\HttpKernel\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * Transforms the body of a json request to POST parameters.
 */
class JsonRequestTransformerListener
{
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!$this->isJsonRequest($request)) {
            return;
        }

        $content = $request->getContent();

        if (empty($content)) {
            return;
        }

        if (!$this->transformJsonBody($request)) {
            $response = new Response('Unable to parse request.', Response::HTTP_BAD_REQUEST);
            $event->setResponse($response);
        }
    }

    private function isJsonRequest(Request $request): bool
    {
        // Request::getContentType() is deprecated since Symfony 6.2
        return 'json' === (method_exists($request, 'getContentTypeFormat') ? $request->getContentTypeFormat() : $request->getContentType());
    }

    private function transformJsonBody(Request $request): bool
    {
        $data = json_decode((string) $request->getContent(), true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            return false;
        }

        if (is_null($data) || is_bool($data)) {
            return true;
        }

        if (!is_array($data)) {
            return false;
        }

        $request->request->replace($data);

        return true;
    }
}
