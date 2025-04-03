<?php

namespace App\Controller;

use App\Repository\ExerciceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExerciceController extends AbstractController
{
    #[Route('/exercices', name: 'app_exercices')]
    public function index(ExerciceRepository $exerciceRepository): Response
    {
        $exercices = $exerciceRepository->findAll();

        return $this->render('exercice/exercice.html.twig', [
            'exercices' => $exercices,
        ]);
    }
    #[Route('/exercice/{id}', name: 'app_exercice_show')]
    public function show(int $id, ExerciceRepository $exerciceRepository): Response
    {
        $exercice = $exerciceRepository->find($id);

        if (!$exercice) {
            throw $this->createNotFoundException('Exercice introuvable.');
        }

        return $this->render('exercice/show.html.twig', [
            'exercice' => $exercice,
        ]);
    }

}
