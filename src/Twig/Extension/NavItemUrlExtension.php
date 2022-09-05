<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Generates an external URL using the Symfony Router, omitting the current request context.
 */
class NavItemUrlExtension extends AbstractExtension
{
    public function __construct(private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('nav_item_url', [$this, 'generateNavItemUrl']),
        ];
    }

    /**
     * @param array{
     *    'path': string,
     *    'params': array<string, string|int>|null,
     *    'anchor': string|null,
     * } $item
     */
    public function generateNavItemUrl(array $item): string
    {
        return $this->urlGenerator->generate($item['path'], ($item['params'] ?? []) + [
            '_fragment' => $item['anchor'] ?? null,
        ]);
    }
}
