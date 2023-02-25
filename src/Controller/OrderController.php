<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderLine;
use App\Form\OrderType;
use App\Repository\OrderLineRepository;
use App\Repository\OrderRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route('/order', name: 'AddOrder')]
    public function index(Request $request,SessionInterface $session,ProduitRepository $productsRepository
    , OrderRepository $orderRepo , OrderLineRepository $orderLineRepository): Response
    {
        $data = null ;
       #Panier recuperation here 
        $panier = $session->get("panier", []);
        $dataPanier = [];
        $total = 0;
        foreach($panier as $id => $quantite){
            $product = $productsRepository->find($id);
            $dataPanier[] = [
                "produit" => $product,
                "quantite" => $quantite
            ];
            $total += $product->getSellprice() * $quantite;
        }
        #Creation Order here 
        $order = new Order() ; 
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);
        $user = $this->getUser() ; 

        if($form->isSubmitted()){
            if($form->isValid()){
                $date = new \DateTime() ;
                $order->setReference(strtoupper($user->getName()).$date->format('Y-m-d-H:i')) ; 
                $order->setState("inconfirmed"); 
                $order->setPrice($total) ; 
                $order->setPaiementmethod("cash on delivry"); 
                $order->setDateOrder(new \DateTime()); 
                $order->setClient($user) ; 
                $orderRepo->save($order); 
                foreach($panier as $id => $quantite){
                    $product = $productsRepository->find($id);
                    $orderline = new OrderLine() ; 
                    $orderline->setProduct($product); 
                    $orderline->setQuantity($quantite) ; 
                    $orderline->setPrice($product->getSellprice() * $quantite); 
                    $orderline->setRelatedOrder($order) ; 
                    $orderLineRepository->save($orderline) ; 
                }
                $session->remove('panier');
                return $this->redirectToRoute('homepageVisitor'); 
            }else{
                $data = "error while creating order";
                $form = $form->createView() ;
                return $this->render('order/addOrder.html.twig', 
                compact('dataPanier', 'total' ,'user','form','data')) ; 
            }
          
        }
        $form = $form->createView() ;
        return $this->render('order/addOrder.html.twig', 
        compact('dataPanier', 'total' ,'user','form','data'));
  
    }

    #[Route('/order/liste', name: 'ListeOrder')]
    public function ListeOrder(OrderRepository $orderRepo): Response
    {
        $user = $this->getUser(); 
        $order = $orderRepo->findBy(array('client' => $user)); 
        return $this->render('user/client/clientOrderList.html.twig', [
            'user' => $user ,
            'orders' => $order
        ]
   );
    }
    
    #[Route('/order/generateInvoice/{id}', name: 'generateInvoice')]
    public function generateInvoice($id , OrderRepository $orderRepo): Response
    {
        $user = $this->getUser(); 

        $order = $orderRepo->find($id); 
        $order->setInvoiced(true) ; 
        $orderRepo->save($order) ; 
        return $this->redirectToRoute('listeOrderDashboard'); 

    }

    #[Route('/facture/{id}', name: 'SeeFacture')]
    public function SeeFacture($id , OrderRepository $orderRepo): Response
    {
       

        $order = $orderRepo->find($id); 
       
        return $this->render('/order/facture.html.twig', [
            'order' => $order
        ]);

    }

    #[Route('/dashboard/order/liste', name: 'listeOrderDashboard')]
    public function listeOrderDashboard(OrderRepository $orderRepo): Response
    {
        $user = $this->getUser(); 
        $orders = $orderRepo->findAll(); 
        return $this->render('order/index.html.twig', [
            'user' => $user ,
            'orders' => $orders
        ]
   );
    }

    #[Route('/order/{id}', name: 'OrderByID')]
    public function OrderByID($id , OrderRepository $orderRepo): Response
    {
        $user = $this->getUser(); 
        $order = $orderRepo->find($id) ;
        return $this->render('user/client/orderdetailsclient.html.twig', [
            'user' => $user ,
            'order' => $order
        ]
   );
    }
    
    #[Route('/dashboard/order/{id}', name: 'OrderByIDDashboard')]
    public function OrderByIDDashboard($id , OrderRepository $orderRepo): Response
    {
        $user = $this->getUser(); 
        $order = $orderRepo->find($id) ;
        return $this->render('order/orderdetailsDashboard.html.twig', [
            'user' => $user ,
            'order' => $order
        ]
   );
    }

    
    #[Route('/dashboard/order/delete/{id}', name: 'DeleteOrder')]
    public function DeleteOrder($id , OrderRepository $orderRepo, OrderLineRepository $orderLineRepository): Response
    {
        $user = $this->getUser(); 
        $order = $orderRepo->find($id) ;
        $orderlines = $order->getOrderLines() ; 
        foreach($orderlines as $ordline ){
            $orderLineRepository->remove($ordline);
        }
        $orderRepo->remove($order) ; 
        return $this->redirectToRoute('listeOrderDashboard'); 
    }
    #[Route('/dashboard/order/update/{id}/{state}', name: 'UpdateStateOrder')]
    public function UpdateStateOrder($id , $state , OrderRepository $orderRepo): Response
    {
      
        $order = $orderRepo->find($id) ;
        $order->setState($state) ; 
        $orderRepo->save($order) ; 
        return $this->redirectToRoute('listeOrderDashboard'); 
    }

    
}
