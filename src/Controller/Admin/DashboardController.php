<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Component\Security\Core\User\UserInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use App\Controller\Admin\ProductCrudController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class DashboardController extends AbstractDashboardController
{

    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
         // redirect to some CRUD controller
        $routeBuilder = $this->get(CrudUrlGenerator::class)->build();

        return $this->redirect($routeBuilder->setController(ProductCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Symfony App')
            ->setFaviconPath('./favicon.svg')
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home text-success')->setCssClass('menu-admin');
        yield MenuItem::linkToCrud('Products', 'fas fa-camera text-info', Product::class)->setCssClass('menu-admin');
        yield MenuItem::linkToCrud('Users', 'fas fa-users text-primary', User::class)->setCssClass('menu-admin');
        yield MenuItem::linkToCrud('Categories', 'fas fa-tags text-warning', Category::class)->setCssClass('menu-admin');
        yield MenuItem::linkToCrud('Orders', 'fas fa-cart-plus text-success', Order::class)->setCssClass('menu-admin');
        yield MenuItem::linkToUrl('public website', 'fas fa-eye', '/')->setCssClass('menu-admin');
        yield MenuItem::linkToLogout('Logout', 'fas fa-user-minus text-danger')->setCssClass('menu-admin');
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)
            // use the given $user object to get the user name
            ->setName($user->getName())
            // use this method if you don't want to display the name of the user
            ->displayUserName(true)

            // you can return an URL with the avatar image
            ->setAvatarUrl('https://placehold.it/200')
            // use this method if you don't want to display the user image
            ->displayUserAvatar(true)
            // you can also pass an email address to use gravatar's service
            ->setGravatarEmail($user->getAddress())

            // you can use any type of menu item, except submenus
            ->addMenuItems([
                MenuItem::section(),
                MenuItem::linkToLogout('Logout', 'fas fa-sign-out'),
            ]);
    }

}
