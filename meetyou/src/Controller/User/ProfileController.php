<?php
declare(strict_types=1);

namespace App\Controller\User;


use App\ReadModel\User\UserFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @Route(path="/id{id}", name="user.profile")
     * @param UserFetcher $fetcher
     * @param string      $id
     * @return Response
     */
    public function view(UserFetcher $fetcher, string $id): Response
    {
        $user = $fetcher->findById($id);

        if ($user === null) {
            throw $this->createNotFoundException();
        }

        return $this->render('user/profile/index.html.twig', ['user' => $user]);
    }
}