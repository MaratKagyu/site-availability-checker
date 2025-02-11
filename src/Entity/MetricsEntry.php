<?php

namespace App\Entity;

use App\Repository\MetricsEntryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MetricsEntryRepository::class)]
class MetricsEntry
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $url = '';

    #[ORM\Column(length: 255)]
    private string $client = '';

    #[ORM\Column(length: 20)]
    private string $clientIP = '';

    #[ORM\Column(type: Types::BIGINT, nullable: false)]
    private int $timingMilliseconds = 0;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdDateTime = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;
        return $this;
    }

    public function getClient(): string
    {
        return $this->client;
    }

    public function setClient(string $client): static
    {
        $this->client = $client;
        return $this;
    }

    public function getClientIP(): string
    {
        return $this->clientIP;
    }

    public function setClientIP(string $clientIP): static
    {
        $this->clientIP = $clientIP;
        return $this;
    }

    public function getTimingMilliseconds(): int
    {
        return $this->timingMilliseconds;
    }

    public function setTimingMilliseconds(int $timingMilliseconds): static
    {
        $this->timingMilliseconds = $timingMilliseconds;
        return $this;
    }

    public function getCreatedDateTime(): ?\DateTimeInterface
    {
        return $this->createdDateTime;
    }

    public function setCreatedDateTime(?\DateTimeInterface $createdDateTime): static
    {
        $this->createdDateTime = $createdDateTime;
        return $this;
    }
}
