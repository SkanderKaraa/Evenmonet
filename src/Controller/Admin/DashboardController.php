<?php

namespace App\Controller\Admin;

use App\Entity\Annonce;
use App\Entity\User;
use App\Controller\Admin\UserCrudController;
use App\Controller\Admin\AnnonceCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // Redirige automatiquement vers le CRUD des utilisateurs
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

        return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('EvenmoNet Admin')
            ->renderContentMaximized(); // Optionnel : maximise la zone de contenu
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home');

        yield MenuItem::section('Gestion');

        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-user', User::class);
        yield MenuItem::linkToCrud('Annonces', 'fas fa-bullhorn', Annonce::class);

        yield MenuItem::section('Navigation');
        yield MenuItem::linkToRoute('Retour au site', 'fas fa-arrow-left', 'app_home');
    }
}
