<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Member;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('equipe')]
class TeamController extends AbstractController
{
    #[Route('/{member}/signature', name: 'team_member_mail_signature', options: [
        'stenope' => [
            'sitemap' => false,
        ],
    ])]
    public function emailSignature(Member $member): Response
    {
        return $this->render('team/mail_signature.html.twig', [
            'member' => $member,
        ])->setLastModified($member->lastModified);
    }
}
