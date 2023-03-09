<?php

namespace App\Repository;

use App\Entity\Produit;
use App\Form\ProduitType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }
    //search:
    public function search($mots = null, $categorie = null){
        $query = $this->createQueryBuilder('p');
        if($mots != null){
            $query->where('MATCH_AGAINST(p.name, p.descriptionProduit) AGAINST (:mots boolean)>0')
                ->setParameter('mots', $mots);
        }
        // if($categorie != null){
        //$query->leftJoin('p.Categorie', 'c');
        // $query->andWhere('c.id = :id')
        //  ->setParameter('id', $categorie);
        //}
        return $query->getQuery()->getResult();
    }
//pagination:
    public function getPaginatedproduit($filters = null){
        $query = $this->createQueryBuilder('p');

        // On filtre les données
        if($filters != null){
            $query->andWhere('p.Categorie IN(:cats)')
                ->setParameter(':cats', array_values($filters));
        }
        return $query->getQuery()->getResult();
    }
    public function getTotalProduits($filters = null){
        $query = $this->createQueryBuilder('p')
            ->select('COUNT(p)')
            ->where('p.id = 9');
        // On filtre les données
        if($filters != null){
            $query->andWhere('p.Categorie IN(:cats)')
                ->setParameter(':cats', array_values($filters));
        }

        return $query->getQuery()->getSingleScalarResult();
    }
    // public function findByCategory($id)
    // {
    //     $entityManager=$this->getEntityManager();
    //     $query=$entityManager
    //         ->createQuery("SELECT p FROM APP\Entity\Produit p 
    //        JOIN p.Categorie c WHERE c.id=:id")
    //         ->setParameter('id',$id);
    //     return $query->getResult();
    // }
    // public function countByCategorie(){
    //     // $query = $this->createQueryBuilder('a')
    //     //     ->select('SUBSTRING(a.created_at, 1, 10) as dateAnnonces, COUNT(a) as count')
    //     //     ->groupBy('dateAnnonces')
    //     // ;
    //     // return $query->getQuery()->getResult();
    //     $qb = $this->createQueryBuilder('p')
    //         ->join('p.categorie_id', 't')
    //         ->addSelect('COUNT(p)')
    //         ->groupBy('t.categorie_id');

    //     return $qb->getQuery()
    //         ->getScalarResult();

    // }
    public function countByCategorie($categoryId){
        $qb = $this->createQueryBuilder('c')
        ->select('COUNT(c)')
        ->where('c.categorie = :categorie_id')
            ->setParameter('categorie_id', $categoryId);

        return $qb->getQuery()->getScalarResult();
  
  
      }
    
    /**
     *
     * Requête QueryBuilder tri
     */
    public function getProduitPrix($buyprice){
        return $this->createQueryBuilder('c')
            ->where('c.buyprice < :buyprice ')
            ->setParameter('buyprice' ,  $buyprice)

            ->getQuery()
            ->execute();
    }

    public function findProduitByName($nom){
        return $this->createQueryBuilder('produit')
            ->where('produit.name LIKE :nom')
            ->setParameter('nom', '%'.$nom.'%')
            ->getQuery()->getResult();
    }

    function orderByNameAscQB(){
        return $this->createQueryBuilder('ev')
            -> orderBy('ev.name','ASC')
            -> getQuery()->getResult();
    }

    function orderByNameDescQB(){
        return $this->createQueryBuilder('ev')
            -> orderBy('ev.name','DESC')
            -> getQuery()->getResult();
    }
    function orderByPrixAescQB(){
        return $this->createQueryBuilder('ev')
            -> orderBy('ev.buyprice','ASC')
            -> getQuery()->getResult();
    }
    function orderByPrixDescQB(){
        return $this->createQueryBuilder('ev')
            -> orderBy('ev.buyprice','DESC')
            -> getQuery()->getResult();
    }
}