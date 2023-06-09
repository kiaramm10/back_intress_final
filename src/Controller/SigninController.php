<?php

namespace App\Controller;

use App\Entity\Signin;
use App\Form\SigninType;
use App\Repository\SigninRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[Route('/signin')]
class SigninController extends AbstractController
{
  
    #[Route('/list', name: 'app_signin_index', methods: ['GET'])]
    public function index(SigninRepository $signinRepository): Response
    {
        return $this->render('signin/index.html.twig', [
            'signins' => $signinRepository->findAll(),
        ]);
    }

    #[Route('/create', name: 'app_signin_create', methods: ['POST'])]
    public function create(Request $request, SigninRepository $signinRepository, ValidatorInterface $validator): Response
    {
        $data = json_decode($request->getContent(), true);
    
        // Validando los datos
        $errors = $validator->validate([
            'dates' => $data['dates'],
            'timestart' => $data['timestart'],
            'timestop' => $data['timestop'],
            'timeRestart' => $data['timeRestart'],
            'timefinish' => $data['timefinish'],
            'hourcount' => $data['hourcount'],

        ], new Assert\Collection([
            'dates' => new Assert\NotBlank(),
            'timestart' => new Assert\NotBlank(),
            'timestop' => new Assert\NotBlank(),
            'timeRestart' => new Assert\NotBlank(),
            'timefinish' => new Assert\NotBlank(),
            'hourcount' => new Assert\NotBlank(),
        ]));
    
        if (count($errors) > 0) {
            
            return $this->json(['error' => 'Validation failed'], Response::HTTP_BAD_REQUEST);
        }

    }

    #[Route('/new', name: 'app_signin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SigninRepository $signinRepository): Response
    {
        $signin = new Signin();
        $form = $this->createForm(SigninType::class, $signin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $signinRepository->save($signin, true);

            return $this->redirectToRoute('app_signin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('signin/new.html.twig', [
            'signin' => $signin,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_signin_show', methods: ['GET'])]
    public function show(Signin $signin, SigninRepository $signinRepository): Response
    {
        
        if (!$signin) {
            return $this->redirectToRoute('app_signin_index', [], Response::HTTP_NOT_FOUND);
        }

        return $this->render('signin/show.html.twig', [
            'signin' => $signin,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_signin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Signin $signin, SigninRepository $signinRepository): Response
    {
        $form = $this->createForm(SigninType::class, $signin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $signinRepository->save($signin, true);

            return $this->redirectToRoute('app_signin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('signin/edit.html.twig', [
            'signin' => $signin,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_signin_delete', methods: ['POST'])]
    public function delete(Request $request, Signin $signin, SigninRepository $signinRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$signin->getId(), $request->request->get('_token'))) {
            $signinRepository->remove($signin, true);
        }

        return $this->redirectToRoute('app_signin_index', [], Response::HTTP_SEE_OTHER);
    }
}
