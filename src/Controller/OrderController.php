<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderLine;
use App\Form\OrderType;
use Symfony\Component\Mailer\Mailer;
use Knp\Component\Pager\PaginatorInterface;
use App\Manager\PayementManger;
use App\Repository\OrderLineRepository;
use App\Repository\OrderRepository;
use App\Repository\ProduitRepository;
use App\Services\StripeService;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class OrderController extends AbstractController
{
    #[Route('/order', name: 'AddOrder')]
    public function index(
        Request $request,
        SessionInterface $session,
        ProduitRepository $productsRepository,
        OrderRepository $orderRepo,
        OrderLineRepository $orderLineRepository,
        StripeService $stripeService,
        PayementManger $payementManager
    ): Response {
        $data = null;
        #Panier recuperation here 
        $panier = $session->get("panier", []);
        $dataPanier = [];
        $total = 0;
        foreach ($panier as $id => $quantite) {
            $product = $productsRepository->find($id);
            $dataPanier[] = [
                "produit" => $product,
                "quantite" => $quantite
            ];
            $total += $product->getSellprice() * $quantite;
        }
        #Creation Order here 
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);
        $user = $this->getUser();
        if ($request->getMethod() === "POST") {
            if ($request->request->get('Paiement') == 'Stripe') {
                $order->setPrice($total);
                $resource = $payementManager->stripe($_POST, $order);
                $order->setPaiementmethod("Card");
            } else if ($request->request->get('Paiement') == 'CashOnDelivrey') {
                $order->setPrice($total);
                $order->setPaiementmethod("CashOndelivrey");
            } else {
                $data = "no paiement method checked";
                return $this->render(
                    'order/addOrder.html.twig',
                    compact('dataPanier', 'total', 'user', 'data', 'intentSecret')
                );
            }

            $date = new \DateTime();
            $order->setShippingadress($request->request->get('shippingadress'));
            $order->setNote($request->request->get('note'));
            $order->setReference(strtoupper($user->getName()) . $date->format('Y-m-d-H:i'));
            $order->setState("inconfirmed");
            $order->setDateOrder(new \DateTime());
            $order->setClient($user);
            $orderRepo->save($order);
            foreach ($panier as $id => $quantite) {
                $product = $productsRepository->find($id);
                $orderline = new OrderLine();
                $orderline->setProduct($product);
                $orderline->setQuantity($quantite);
                $orderline->setPrice($product->getSellprice() * $quantite);
                $orderline->setRelatedOrder($order);
                $orderLineRepository->save($orderline);
            }
            $session->remove('panier');
            $loader = new FilesystemLoader('../templates');
            $twig = new Environment($loader);
            $html = $twig->render('email/confirmeOrder.html.twig', [
                'user' => 'yessine',
                'order' => $order,
            ]);
            $email = (new Email())
                ->from('contact.fithealth23@gmail.com')
                ->to('yacinbnsalh@gmail.com')
                ->subject('Order Confirmation')
                ->html($html);
            $transport = new GmailSmtpTransport('contact.fithealth23@gmail.com', 'qavkrnciihzjmtkp');
            $mailer = new Mailer($transport);
            $mailer->send($email);
            return $this->redirectToRoute('ListeOrder');
        } else {
            $order->setPrice($total);
            $intentSecret = $payementManager->intentSecret($order);
            return $this->render(
                'order/addOrder.html.twig',
                compact('dataPanier', 'total', 'user', 'data', 'intentSecret')
            );
        }
    }

    #[Route('/order/liste', name: 'ListeOrder')]
    public function ListeOrder(OrderRepository $orderRepo): Response
    {
        $user = $this->getUser();
        $order = $orderRepo->findBy(array('client' => $user));
        return $this->render(
            'user/client/clientOrderList.html.twig',
            [
                'user' => $user,
                'orders' => $order
            ]
        );
    }

    #[Route('/order/generateInvoice/{id}', name: 'generateInvoice')]
    public function generateInvoice($id, OrderRepository $orderRepo): Response
    {
        $order = $orderRepo->find($id);
        $order->setInvoiced(true);
        $orderRepo->save($order);
        return $this->redirectToRoute('listeOrderDashboard');
    }

    #[Route('/facture/{id}', name: 'SeeFacture')]
    public function SeeFacture($id, OrderRepository $orderRepo): Response
    {
        $order = $orderRepo->find($id);
        return $this->render('/order/facture.html.twig', [
            'order' => $order
        ]);
    }

    #[Route('/dashboard/order/liste', name: 'listeOrderDashboard')]
    public function listeOrderDashboard(Request $request, OrderRepository $orderRepo, PaginatorInterface $paginator): Response
    {
        $user = $this->getUser();
        $data = $orderRepo->findAll();
        $orders = $paginator->paginate(
            $data, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            6 // Nombre de résultats par page
        );
        return $this->render(
            'order/index.html.twig',
            [
                'user' => $user,
                'orders' => $orders
            ]
        );
    }

    #[Route('/order/{id}', name: 'OrderByID')]
    public function OrderByID($id, OrderRepository $orderRepo): Response
    {
        $user = $this->getUser();
        $order = $orderRepo->find($id);
        return $this->render(
            'user/client/orderdetailsclient.html.twig',
            [
                'user' => $user,
                'order' => $order
            ]
        );
    }

    #[Route('/dashboard/order/{id}', name: 'OrderByIDDashboard')]
    public function OrderByIDDashboard($id, OrderRepository $orderRepo): Response
    {
        $user = $this->getUser();
        $order = $orderRepo->find($id);
        return $this->render(
            'order/orderdetailsDashboard.html.twig',
            [
                'user' => $user,
                'order' => $order
            ]
        );
    }


    #[Route('/dashboard/order/delete/{id}', name: 'DeleteOrder')]
    public function DeleteOrder($id, OrderRepository $orderRepo, OrderLineRepository $orderLineRepository): Response
    {
        $user = $this->getUser();
        $order = $orderRepo->find($id);
        $orderlines = $order->getOrderLines();
        foreach ($orderlines as $ordline) {
            $orderLineRepository->remove($ordline);
        }
        $orderRepo->remove($order);
        return $this->redirectToRoute('listeOrderDashboard');
    }
    #[Route('/dashboard/order/update/{id}/{state}', name: 'UpdateStateOrder')]
    public function UpdateStateOrder($id, $state, OrderRepository $orderRepo): Response
    {



        $order = $orderRepo->find($id);
        $order->setState($state);
        $orderRepo->save($order);
        $user = $this->getUser();
        if ($user->getRoles()[0] == 'ROLE_ADMIN') {
            return $this->redirectToRoute('listeOrderDashboard');
        } else {
            return $this->redirectToRoute('ListeOrder');
        }
    }
}
