<?php
declare(strict_types=1);

namespace App\Controller\Auth;

use App\Model\User\UseCase\SignUp\Command;
use App\Model\User\UseCase\SignUp\Form;
use App\Model\User\UseCase\SignUp\Handler;
use DomainException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SignUpController extends AbstractController
{
    /**
     * @Route(path="/signup", name="auth.signup")
     * @param Request $request
     * @param Handler $handler
     * @return RedirectResponse|Response
     */
    public function signup(Request $request, Handler $handler)
    {
        $command = new Command();

        $form = $this->createForm(Form::class, $command);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Done. Sign In using your email and password.');

                return $this->redirectToRoute('home');
            } catch (DomainException $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('auth/signup.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}