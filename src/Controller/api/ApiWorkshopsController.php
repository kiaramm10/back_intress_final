<?php

namespace App\Controller\api;

use App\Entity\Workshops;
use App\Form\WorkshopsType;
use App\Repository\WorkshopsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

#[Route('/apiworkshops')]
class ApiWorkshopsController extends AbstractController
{
    #[Route('/list', name: 'app_apiworkshops_index', methods: ['GET'])]
    public function index(WorkshopsRepository $workshopsRepository): Response
    {
        $workshops = $workshopsRepository->findAll();

        $data = [];

        foreach ($workshops as $p) {
            $data[] = [
                'id' => $p->getId(),
                'name' => $p->getName(),
                'date' => $p->getDate(),
                'description' => $p->getDescription(),

            ];
            
        }

        //dump($data);die; 
        //return $this->json($data);
        return $this->json($data, $status = 200, $headers = ['Access-Control-Allow-Origin'=>'*']);
    }

    #[Route('/create', name: 'app_apiworkshops_create', methods: ['POST'])]
    public function create(Request $request, ManagerRegistry $doctrine): Response
    {
        $workshop = new Workshops();

        $form = $this->createForm(WorkshopsType::class, $workshop);
        $form->submit(json_decode($request->getContent(), true));

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($workshop);
            $em->flush();

            return $this->json(['status' => 'success'], $status = 200, $headers = ['Access-Control-Allow-Origin'=>'*']);
        }

        return $this->json(['status' => 'error'], $status = 400, $headers = ['Access-Control-Allow-Origin'=>'*']);
    }

    #[Route('/update/{id}', name: 'app_apiworkshops_update', methods: ['PUT'])]
    public function update(Request $request, Workshops $workshop, ManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(WorkshopsType::class, $workshop);
        $form->submit(json_decode($request->getContent(), true));

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($workshop);
            $em->flush();

            return $this->json(['status' => 'success'], $status = 200, $headers = ['Access-Control-Allow-Origin'=>'*']);
        }

        return $this->json(['status' => 'error'], $status = 400, $headers = ['Access-Control-Allow-Origin'=>'*']);
    }
}