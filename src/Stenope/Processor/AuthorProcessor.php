<?php

declare(strict_types=1);

namespace App\Stenope\Processor;

use App\Model\Article;
use App\Model\Member;
use Stenope\Bundle\Behaviour\ContentManagerAwareInterface;
use Stenope\Bundle\Behaviour\ContentManagerAwareTrait;
use Stenope\Bundle\Behaviour\ProcessorInterface;
use Stenope\Bundle\Content;

/**
 * Fetch author form Member list
 */
class AuthorProcessor implements ProcessorInterface, ContentManagerAwareInterface
{
    use ContentManagerAwareTrait;

    public function __invoke(array &$data, Content $content): void
    {
        if (!is_a($content->getType(), Article::class, true)) {
            return;
        }

        if (!isset($data['authors'])) {
            throw new \Exception('At least one author must be specified.');
        }

        $authors = $data['authors'];

        $data['authors'] = array_combine($authors, array_map(
            fn (string $id) => $this->contentManager->getContent(Member::class, $id),
            $authors
        ));
    }
}
