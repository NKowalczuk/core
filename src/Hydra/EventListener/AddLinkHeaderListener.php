<?php

/*
 * This file is part of the API Platform project.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ApiPlatform\Core\Hydra\EventListener;

use ApiPlatform\Core\Api\UrlGeneratorInterface;
use ApiPlatform\Core\JsonLd\ContextBuilder;
use Fig\Link\GenericLinkProvider;
use Fig\Link\Link;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

/**
 * Adds the HTTP Link header pointing to the Hydra documentation.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class AddLinkHeaderListener
{
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Sends the Hydra header on each response.
     */
    public function onKernelResponse(ResponseEvent $event): void
    {
        $apiDocUrl = $this->urlGenerator->generate('api_doc', ['_format' => 'jsonld'], UrlGeneratorInterface::ABS_URL);
        $link = new Link(ContextBuilder::HYDRA_NS.'apiDocumentation', $apiDocUrl);

        $attributes = $event->getRequest()->attributes;
        if (null === $linkProvider = $attributes->get('_links')) {
            $attributes->set('_links', new GenericLinkProvider([$link]));

            return;
        }
        $attributes->set('_links', $linkProvider->withLink($link));
    }
}
