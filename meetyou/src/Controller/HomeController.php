<?php
declare(strict_types=1);

namespace App\Controller;


use App\ReadModel\User\Filter\Filter;
use App\ReadModel\User\UserFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private const PER_PAGE = 100;

    /**
     * @Route(path="/", name="home")
     * @param Request     $request
     * @param UserFetcher $fetcher
     * @return Response
     */
    public function index(Request $request, UserFetcher $fetcher): Response
    {
        $name = $request->get('q');

        if ($name) {
            $filter = new Filter();
            $filter->name = $request->get('q');
            $pagination = $fetcher->search($request->query->getInt('page', 1), self::PER_PAGE, $filter);
        } else {
            $pagination = $fetcher->all($request->query->getInt('page', 1), self::PER_PAGE);
        }

        return $this->render('home/index.html.twig', ['pagination' => $pagination]);
    }

}