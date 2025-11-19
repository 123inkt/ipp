<?php

declare(strict_types=1);

namespace DR\Ipp;

use DR\Ipp\Client\CupsIppHttpClient;
use DR\Ipp\Client\IppHttpClientInterface;
use DR\Ipp\Entity\IppJob;
use DR\Ipp\Entity\IppPrinter;
use DR\Ipp\Entity\IppPrintFile;
use DR\Ipp\Entity\IppServer;
use DR\Ipp\Entity\Response\IppResponseInterface;
use DR\Ipp\Factory\PrintOperationFactory;
use DR\Ipp\Factory\ResponseParserFactory;
use DR\Ipp\Operations\CancelJobOperation;
use DR\Ipp\Operations\Cups\CupsCreatePrinter;
use DR\Ipp\Operations\Cups\CupsDeletePrinter;
use DR\Ipp\Operations\Cups\CupsGetPrinters;
use DR\Ipp\Operations\GetJobAttributesOperation;
use DR\Ipp\Operations\GetJobsOperation;
use DR\Ipp\Operations\GetPrinterAttributesOperation;
use DR\Ipp\Operations\PrintOperation;
use DR\Ipp\Service\PrinterAdminService;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class Ipp implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private IppHttpClientInterface $httpClient;
    private ResponseParserFactory $parserFactory;
    private PrintOperationFactory $printOperationFactory;

    private ?PrintOperation $printOperation = null;
    private ?GetJobAttributesOperation $getJobAttributes = null;
    private ?GetPrinterAttributesOperation $getPrinterAttributes = null;
    private ?GetJobsOperation $getJobs = null;
    private ?CancelJobOperation $cancelJob = null;

    private ?PrinterAdminService $printerAdmin = null;

    public function __construct(
        private readonly IppServer $server,
        private readonly ClientInterface $client,
        ?IppHttpClientInterface $httpClient = null,
        ?ResponseParserFactory $parserFactory = null,
    ) {
        $this->httpClient            = $httpClient ?? new CupsIppHttpClient($this->server, $this->client);
        $this->parserFactory         = $parserFactory ?? new ResponseParserFactory();
        $this->printOperationFactory = new PrintOperationFactory();
    }

    public function printerAdministration(): PrinterAdminService
    {
        $this->printerAdmin ??= new PrinterAdminService(
            new CupsCreatePrinter($this->server, $this->httpClient, $this->parserFactory),
            new CupsDeletePrinter($this->server, $this->httpClient, $this->parserFactory),
            new CupsGetPrinters($this->httpClient, $this->parserFactory),
        );

        return $this->printerAdmin;
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function print(IppPrinter $printer, IppPrintFile $file): IppResponseInterface
    {
        $this->printOperation ??= $this->printOperationFactory->create($this->server, $this->httpClient, $this->parserFactory, $this->logger);

        return $this->printOperation->print($printer, $file);
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function getJobAttributes(IppJob $job): IppResponseInterface
    {
        $this->getJobAttributes ??= new GetJobAttributesOperation($this->httpClient, $this->parserFactory);

        return $this->getJobAttributes->getJob($job);
    }

    /**
     * Requests a list of printer attributes for the specified printer
     * @throws ClientExceptionInterface
     */
    public function getPrinterAttributes(IppPrinter $printer): IppResponseInterface
    {
        $this->getPrinterAttributes ??= new GetPrinterAttributesOperation($this->server, $this->httpClient, $this->parserFactory);

        return $this->getPrinterAttributes->getAttributes($printer);
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function getJobs(IppPrinter $printer, bool $completed = false): IppResponseInterface
    {
        $this->getJobs ??= new GetJobsOperation($this->server, $this->httpClient, $this->parserFactory);

        return $this->getJobs->getJobList($printer, $completed);
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function cancelJob(IppJob $job): IppResponseInterface
    {
        $this->cancelJob ??= new CancelJobOperation($this->httpClient, $this->parserFactory);

        return $this->cancelJob->cancel($job);
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function validatePrintJob(IppPrinter $printer, IppPrintFile $file): IppResponseInterface
    {
        $this->printOperation ??= $this->printOperationFactory->create($this->server, $this->httpClient, $this->parserFactory, $this->logger);

        return $this->printOperation->print($printer, $file, true);
    }
}
