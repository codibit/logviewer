<?php

namespace App\Entity;

use App\Repository\HttprequestRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HttprequestRepository::class)]
class Httprequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $host;

    #[ORM\Column(type: 'datetimetz')]
    private $datetime;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $request_method;

    #[ORM\Column(type: 'string', length: 255)]
    private $request_url;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private $request_protocol;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private $request_protocol_version;

    #[ORM\Column(type: 'string', length: 3)]
    private $response_code;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $document_size;

    #[ORM\Column(type: 'string', length: 255)]
    private $logfile_name;

    #[ORM\Column(type: 'integer')]
    private $logfile_line;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function setHost(string $host): self
    {
        $this->host = $host;

        return $this;
    }

    public function getDatetime(): ?\DateTimeInterface
    {
        return $this->datetime;
    }

    public function setDatetime(\DateTimeInterface $datetime): self
    {
        $this->datetime = $datetime;

        return $this;
    }

    public function getRequestMethod(): ?string
    {
        return $this->request_method;
    }

    public function setRequestMethod(?string $request_method): self
    {
        $this->request_method = $request_method;

        return $this;
    }

    public function getRequestUrl(): ?string
    {
        return $this->request_url;
    }

    public function setRequestUrl(string $request_url): self
    {
        $this->request_url = $request_url;

        return $this;
    }

    public function getRequestProtocol(): ?string
    {
        return $this->request_protocol;
    }

    public function setRequestProtocol(?string $request_protocol): self
    {
        $this->request_protocol = $request_protocol;

        return $this;
    }

    public function getRequestProtocolVersion(): ?string
    {
        return $this->request_protocol_version;
    }

    public function setRequestProtocolVersion(?string $request_protocol_version): self
    {
        $this->request_protocol_version = $request_protocol_version;

        return $this;
    }

    public function getResponseCode(): ?string
    {
        return $this->response_code;
    }

    public function setResponseCode(string $response_code): self
    {
        $this->response_code = $response_code;

        return $this;
    }

    public function getDocumentSize(): ?int
    {
        return $this->document_size;
    }

    public function setDocumentSize(?int $document_size): self
    {
        $this->document_size = $document_size;

        return $this;
    }

    public function getLogfileName(): ?string
    {
        return $this->logfile_name;
    }

    public function setLogfileName(string $logfile_name): self
    {
        $this->logfile_name = $logfile_name;

        return $this;
    }

    public function getLogfileLine(): ?int
    {
        return $this->logfile_line;
    }

    public function setLogfileLine(int $logfile_line): self
    {
        $this->logfile_line = $logfile_line;

        return $this;
    }
}
