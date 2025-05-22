<?php

namespace App\Entity;

use App\Repository\MenuRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MenuRepository::class)]
class Menu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $menu_dish_name = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $menu_ingredient1 = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $menu_ingredient2 = null;

    #[ORM\Column(nullable: true)]
    private ?int $menu_weight = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMenuDishName(): ?string
    {
        return $this->menu_dish_name;
    }

    public function setMenuDishName(string $menu_dish_name): static
    {
        $this->menu_dish_name = $menu_dish_name;

        return $this;
    }

    public function getMenuIngredient1(): ?string
    {
        return $this->menu_ingredient1;
    }

    public function setMenuIngredient1(?string $menu_ingredient1): static
    {
        $this->menu_ingredient1 = $menu_ingredient1;

        return $this;
    }

    public function getMenuIngredient2(): ?string
    {
        return $this->menu_ingredient2;
    }

    public function setMenuIngredient2(?string $menu_ingredient2): static
    {
        $this->menu_ingredient2 = $menu_ingredient2;

        return $this;
    }

    public function getMenuWeight(): ?int
    {
        return $this->menu_weight;
    }

    public function setMenuWeight(?int $menu_weight): static
    {
        $this->menu_weight = $menu_weight;

        return $this;
    }
}
