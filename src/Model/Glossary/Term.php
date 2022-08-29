<?php

declare(strict_types=1);

namespace App\Model\Glossary;

class Term
{
    public string $name;
    public ?string $logo = null;
    public string $slug;
    public string $link;
    public \DateTimeInterface $lastModified;
}
