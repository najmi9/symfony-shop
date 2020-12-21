<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Repository\CategoryRepository;
use App\Repository\OrderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class ReportsController extends AbstractController
{
	private UserRepository $userRepo;
	private OrderRepository $orderRepo;
	private CategoryRepository $categoryRepo;

	public function __construct(UserRepository $userRepo, OrderRepository $orderRepo, CategoryRepository $categoryRepo)
    {
        $this->userRepo = $userRepo;
        $this->orderRepo = $orderRepo;
        $this->categoryRepo = $categoryRepo;
    }

    /**
     * @Route("/reports", name="reports")
     */
    public function index(): Response
    {
        $users = [];
        $months = [];
        $orderMonths = [];
        $orderPrices = [];

        foreach ($this->orderRepo->getPricesByMonth() as $element) {
           $orderMonths[] = $element['month'].'/'.$element['year'];
           $orderPrices[] = $element['price'];
        }

        foreach ($this->userRepo->getNewUsersByMonth() as $item) {
            $users[] = $item['count'];
            $months[] = $item['month'].'/'.$item['year'];
        } 

        $categories = $this->categoryRepo->findByProducts();

        return $this->render('reports/index.html.twig',
             compact('users', 'months', 'categories', 'orderPrices', 'orderMonths'));
    }
}
