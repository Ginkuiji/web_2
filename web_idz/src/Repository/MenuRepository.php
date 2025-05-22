<?php

namespace App\Repository;

use App\Entity\Menu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Menu::class);
    }

    public function save(string $dishName, string $ingredient1, string $ingredient2, int $weight): void
    {
        $menu = new Menu();
        $menu->setMenuDishName($dishName);
        $menu->setMenuIngredient1($ingredient1);
        $menu->setMenuIngredient2($ingredient2);
        $menu->setMenuWeight($weight);

        $em = $this->getEntityManager();
        $em->persist($menu);
        $em->flush();
    }

    /**
     * @return Menu[]
     */
    public function getAll(): array
    {
        return $this->createQueryBuilder('m')
            ->orderBy('m.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $filter
     * @return Menu[]
     */
    public function getFiltered(string $filter): array
    {
        $qb = $this->createQueryBuilder('m');

        if (!empty($filter)) {
            $qb->andWhere('m.menu_dish_name LIKE :filter OR m.menu_ingredient1 LIKE :filter OR m.menu_ingredient2 LIKE :filter')
               ->setParameter('filter', '%' . $filter . '%');
        }

        return $qb->orderBy('m.id', 'ASC')
                  ->getQuery()
                  ->getResult();
    }
}
