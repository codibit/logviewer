<?php

namespace App\Entity;

use App\logfileProcessor;
use App\Repository\LogfileRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: LogfileRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity('name')]
class Logfile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Assert\name]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    private $filename;

    #[ORM\Column(type: 'boolean')]
    private $processed;

    #[ORM\Column(type: 'integer', options: ['default' => 1995])]
    private $year;

    #[ORM\Column(type: 'integer', options: ['default' => 8] )]
    private $month;

    public function __construct()
    {
        $this->processed = $this->processed ?: false;
        $this->year = $this->year ?: 1995; // TODO: Make this into a  variable (Default year)
        $this->month = $this->month ?: 8; // TODO: Make this into a variable (Default month)
    }

    #[ORM\PrePersist]
    public function prePersist($args)
    {
        $logfile = $args->getEntity();

        $logfileProcessor = new logfileProcessor;

        try{
            if($logfile->getProcessed() === false){
                $logfileProcessor->process($logfile);
            }

        } catch (Exception $e){
            echo 'Sorry, the following error happened while processing the file: ',  $e->getMessage(), "\n";
        }

        $logfile->setProcessed(true);
        $em = $args->getEntityManager();
        $em->flush();

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getProcessed(): ?bool
    {
        return $this->processed;
    }

    public function setProcessed(bool $processed): self
    {
        $this->processed = $processed;

        return $this;
    }

    public function getJson()
    {
        return pathinfo($this->getFilename(), PATHINFO_FILENAME) . '.json';
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getMonth(): ?int
    {
        return $this->month;
    }

    public function setMonth(int $month): self
    {
        $this->month = $month;

        return $this;
    }
}
