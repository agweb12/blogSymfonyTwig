<?php

namespace App\Repository;

use App\Entity\Articles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Articles>
 */
class ArticlesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Articles::class);
    }

       /**
        * @return Articles[] Returns an array of Articles objects
        */
       public function findRecentsArticles(int $limit): array // Une méthode pour récupérer les articles récents
       {
           return $this->createQueryBuilder('a') // Je crée une requête DQL (Doctrine Query Language) pour récupérer les articles sur l'entité Articles aliasée à 'a'
            //    ->andWhere('a.exampleField = :val')
            //    ->setParameter('val', $value)
                ->orderBy('a.createdAt', 'DESC') // Je trie les articles par ID de manière décroissante
                ->setMaxResults($limit) // Je limite le nombre de résultats à 6
                ->getQuery() // La méthode getQuery() retourne un objet Query un objet query executable par Doctrine qui va exécuter la requête
                ->getResult() // Je récupère le résultat de la requête en executant la requete et retourne une tableau d'objets d'article
           ;
       }

       public function findByCategory(int $idCategory): array
       {
           return $this->createQueryBuilder('a')
                ->join('a.category', 'c') // Je fais une jointure entre la table articles et la table categories
                ->andWhere('c.id = :idCategory') // Je filtre les articles par ID de catégorie : condition de la jointure pour ne récupérer que les articles de la catégorie
                ->setParameter('idCategory', $idCategory) // Je passe la valeur de l'ID de catégorie
                ->getQuery()
                ->getResult()
           ;
       }
}
