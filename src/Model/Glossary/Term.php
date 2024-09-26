<?php

declare(strict_types=1);

namespace App\Model\Glossary;

use App\Model\MetaTrait;

class Term
{
    use MetaTrait;

    public string $slug;

    public ?string $logo = null;

    /** Link to outside resource for this term. */
    public string $externalLink;

    public string $name;

    /** Main, markdown content */
    public ?string $content = null;

    /**
     * Array of slugs of related articles to show on the glossary term page.
     * If not provided (null), will search for related articles by their tags.
     *
     * @var string[]|null
     */
    public ?array $articles = null;

    public \DateTimeInterface $lastModified;

    /**
     * Set as false to prevent this term to appear in glossary listing.
     * The term can still be referenced from articles or case studies.
     */
    public bool $listInGlossary = true;
}
