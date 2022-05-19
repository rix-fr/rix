<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Article;
use App\Model\Member;
use Stenope\Bundle\ContentManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SiteController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function home(ContentManagerInterface $manager): Response
    {
        /** @var Article[] $articles */
        $articles = $manager->getContents(Article::class, ['date' => false]);
        $members = $manager->getContents(Member::class, [], ['active' => true]);

        return $this->render('site/home.html.twig', [
            'lastArticle' => current($articles),
            'membersCount' => \count($members),
        ]);
    }

    #[Route('/a-propos', name: 'about')]
    public function about(ContentManagerInterface $manager): Response
    {
        $members = $manager->getContents(Member::class, ['name' => true], ['active' => true]);

        return $this->render('site/about.html.twig', ['members' => $members]);
    }

    #[Route('/services', name: 'services')]
    public function services(): Response
    {
        return $this->render('site/services.html.twig');
    }

    #[Route('/contact', name: 'contact')]
    public function contact(): Response
    {
        return $this->render('site/contact.html.twig');
    }

    #[Route('/legal', name: 'legal')]
    public function legal(): Response
    {
        return $this->render('site/legal.html.twig');
    }

    #[Route('/confidentialite', name: 'privacy')]
    public function privacy(): Response
    {
        return $this->render('site/privacy.html.twig');
    }
}
