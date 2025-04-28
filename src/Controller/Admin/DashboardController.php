<?php

namespace App\Controller\Admin;

use App\Entity\Users;
use App\Entity\Profils;
use App\Entity\Articles;
use App\Entity\Categories;
use App\Controller\Admin\UsersCrudController;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')] // Elle n'est pas lié à l'action mais à la classe entière : c'est une route générale : routePath est le chemin et routeName est le nom de la route
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        // return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // 1.1) If you have enabled the "pretty URLs" feature:
        // return $this->redirectToRoute('admin_user_index');
        //
        // 1.2) Same example but using the "ugly URLs" that were used in previous EasyAdmin versions:
        // on décommente les deux lignes dessous car on va utiliser le générateur d'URL
        // $this->denyAccessUnlessGranted('ROLE_ADMIN');
        // $this->denyAccessUnlessGranted('ROLE_USER');
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(UsersCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirectToRoute('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Blog');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Users', 'fas fa-user', Users::class);
        yield MenuItem::linkToCrud('Profils', 'fas fa-user', Profils::class);
        yield MenuItem::linkToCrud('Articles', 'fas fa-newspaper', Articles::class);
        yield MenuItem::linkToCrud('Categories', 'fas fa-tags', Categories::class);
        yield MenuItem::linkToRoute('Retour au site', 'fas fa-home', 'app_home'); // lien vers la page d'accueil du site
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
