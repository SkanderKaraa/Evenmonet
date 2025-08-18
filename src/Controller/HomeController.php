<?php

namespace App\Controller;

use App\Repository\AnnonceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $imageDir = $this->getParameter('kernel.project_dir') . '/public/uploads/images';
        $images = [];

        // Lire tous les fichiers d'image valides dans le dossier
        $finder = new Finder();
        $finder->files()->in($imageDir)->name('/\.(jpg|jpeg|png|webp|gif)$/i');

        foreach ($finder as $file) {
            $images[] = $file->getFilename();
        }

        // Sélection aléatoire de 3 images uniques
        shuffle($images);
        $slides = array_slice(array_unique($images), 0, 3); // max 3 images uniques

        return $this->render('home/index.html.twig', [
            'slides' => $slides,
        ]);
    }
}
