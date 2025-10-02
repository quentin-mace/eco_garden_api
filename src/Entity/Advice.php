<?php

namespace App\Entity;

use App\Enum\MonthEnum;
use App\Repository\AdviceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdviceRepository::class)]
class Advice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column]
    private array $months = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return array<MonthEnum>
     */
    public function getMonths(): array
    {
        return $this->months;
    }

    /**
     * @param array<MonthEnum> $months
     */
    public function setMonths(array $months): static
    {
        $this->months = $months;

        return $this;
    }
}
