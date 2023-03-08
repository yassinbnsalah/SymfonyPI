<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Produit;
use App\Form\ProduitType;
use App\Form\CategoryType;
use App\Repository\ProduitRepository;
use App\Repository\CategoryRepository;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class StoreController extends AbstractController
{
    #[Route('/store', name: 'app_store')]
    public function index(): Response
    {
        return $this->render('store/index.html.twig', [
            'controller_name' => 'StoreController',
        ]);
    }
    #[Route('/store/category', name: 'categoryListe')]
    public function categoryListe(CategoryRepository $Rep, Request $request,
    NotificationRepository $notificationRepository, EntityManagerInterface $manager)
    {
        $categoryy = $Rep->findAll();
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        $user = $this->getUser();
        $notifications = $notificationRepository->findBy(array(), array('dateNotification' => 'DESC'));
        if ($form->isSubmitted() && $form->isValid()) {
            $category->setNbProduct(0);
            $manager->persist($category);
            $manager->flush();
            return $this->redirectToRoute('categoryListe');
        }
        return $this->render('store/category/listeCategory.html.twig', [
            'controller_name' => 'StoreController',
            'categoryy' => $categoryy,
            'notifications' => $notifications,
            'user' => $user, 
            'form' => $form->createView()
        ]);
    }

    #[Route("category/delete/{id}", name: 'deleteCategory')]
    public function deleteCategory($id, ManagerRegistry $doctrine)
    {

        $c = $doctrine->getRepository(Category::class)->find($id);
        $em = $doctrine->getManager();
        $em->remove($c);
        $em->flush();
        return $this->redirectToRoute('categoryListe');
    }
    #[Route('/dashboard/category/update/{id}', name: 'UpdateCategoryDashboard')]
    public function UpdateCategoryDashboard($id, ManagerRegistry $doctrine,
    NotificationRepository $notificationRepository ,  Request  $request): Response
    {
        $category = $doctrine->getRepository(Category::class)->find($id);
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        $user = $this->getUser(); 
        $notifications = $notificationRepository->findBy(array('toUser' => $user), array('dateNotification' => 'DESC'));
        if ($form->isSubmitted()) {
            $em = $doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute('categoryListe');
        }
        return $this->render('store/category/categoryUpdateDash.html.twig', [
            'controller_name' => 'StoreController',
            'user' => $user , 
            'notifications' => $notifications , 
            'form' => $form->createView()
        ]);
    }

    #[Route('/produit/liste', name: 'produitListeClient')]
    public function produitListeClient(ProduitRepository $Rep , CategoryRepository $catRepo,
    NotificationRepository $notificationRepository)
    {
        $produits = $Rep->findAll();
        $category = $catRepo->findAll() ; 
        $user = $this->getUser() ; 
        $notifications = $notificationRepository->findBy(array('toUser' => $user), array('dateNotification' => 'DESC'));
        return $this->render('store/product/productListeClientSide.html.twig', [
            'controller_name' => 'StoreController',
            'products' => $produits,
            'categorys' => $category,
            'notifications' => $notifications,
            'user' => $user
           
        ]);
    }
    #[Route('/store/produit', name: 'produitListe')]
    public function produitListe(ProduitRepository $Rep, Request $request, EntityManagerInterface $manager, 
    NotificationRepository $notificationRepository)
    {
        $produitt = $Rep->findAll();
        $product = new Produit();
        $form = $this->createForm(ProduitType::class, $product);
        $form->handleRequest($request);
        $user = $this->getUser() ; 
        $notifications = $notificationRepository->findBy(array(), array('dateNotification' => 'DESC'));
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if($imageFile){
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
            try {
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }
            $product->setImage($newFilename);
            $manager->persist($product);
            $manager->flush();
            return $this->redirectToRoute('produitListe');
        }}
        return $this->render('store/product/listeProduit.html.twig', [
            'controller_name' => 'StoreController',
            'produitt' => $produitt,
            'user' => $user , 
            'notifications' => $notifications,
            'form' => $form->createView()
        ]);
    }

    #[Route("produit/delete/{id}", name: 'deleteProduit')]
    public function deleteProduit($id, ManagerRegistry $doctrine)
    {

        $c = $doctrine->getRepository(Produit::class)->find($id);
        $em = $doctrine->getManager();
        $em->remove($c);
        $em->flush();
        return $this->redirectToRoute('produitListe');
    }
    #[Route('/dashboard/produit/update/{id}', name: 'UpdateProduitDashboard')]
    public function UpdateProduitDashboard($id, ManagerRegistry $doctrine,
    NotificationRepository $notificationRepository , Request  $request): Response
    {
        $produitbyID = $doctrine->getRepository(Produit::class)->find($id);
        $form = $this->createForm(ProduitType::class, $produitbyID);
        $form->handleRequest($request);
        $user = $this->getUser() ; 
        $notifications = $notificationRepository->findBy(array(), array('dateNotification' => 'DESC'));
        if ($form->isSubmitted()) {
            if ($form->get('image')->getData()) {
                /** @var UploadedFile $imageFile */

                $imageFile = $form->get('image')->getData();

                // if($imageFile){
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $produitbyID->setImage($newFilename);
            }

            $em = $doctrine->getManager();
            $em->flush();

            return $this->redirectToRoute('produitListe');
        }
        return $this->render('store/product/produitUpdateDash.html.twig', [
            'controller_name' => 'StoreController',
            'notifications' => $notifications,
            'user' => $user , 
            'form' => $form->createView()
        ]);
    }
}
