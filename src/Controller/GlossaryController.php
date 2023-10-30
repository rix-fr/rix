<?php

declare(strict_types=1);

namespace App\Controller;

use App\Glossary\GlossaryBuilder;
use App\Model\Article;
use App\Model\CaseStudy;
use App\Model\Glossary\Term;
use Stenope\Bundle\ContentManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/glossaire')]
class GlossaryController extends AbstractController
{
    private ContentManagerInterface $manager;

    public function __construct(ContentManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    #[Route('/', name: 'glossary')]
    public function glossary(GlossaryBuilder $builder): Response
    {
        $terms = $this->manager->getContents(Term::class, 'name', ['listInGlossary' => true]);
        $articles = $this->manager->getContents(Article::class, ['date' => false]);

        return $this->render('glossary/index.html.twig', [
            'glossary' => $builder->build($terms),
            'articles' => \array_slice($articles, 0, 4),
        ]);
    }

    #[Route('/{term}', name: 'glossary_term')]
    public function show(Term $term): Response
    {
        $articlesFilter = null !== $term->articles
            // Search for articles by their slug if provided
            ? static fn ($article) => \in_array($article->slug, $term->articles, true)
            // otherwise by their tags matching the term slug
            : static fn (Article $article): bool => $article->hasTag($term->slug)
        ;

        $articles = $this->manager->getContents(Article::class, ['date' => false], $articlesFilter);

        $caseStudies = $this->manager->getContents(
            CaseStudy::class,
            ['date' => false],
            fn (CaseStudy $caseStudy): bool => $caseStudy->enabled && $caseStudy->hasTerm($term)
        );

        return $this->render('glossary/term.html.twig', [
            'term' => $term,
            'articles' => \array_slice($articles, 0, 3),
            'caseStudies' => $caseStudies,
        ])->setLastModified($term->lastModified);
    }
}
