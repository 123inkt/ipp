<?php

declare(strict_types=1);

namespace DR\Ipp;

use DR\Ipp\Entity\IppPrinter;
use DR\Ipp\Entity\IppPrintFile;
use DR\Ipp\Entity\IppServer;
use DR\Ipp\Entity\Response\IppResponseInterface;
use DR\Ipp\Factory\PrintOperationFactory;
use DR\Ipp\Operations\CupsCreatePrinter;
use DR\Ipp\Operations\CupsDeletePrinter;
use DR\Ipp\Operations\GetJobAttributesOperation;
use DR\Ipp\Operations\PrintOperation;
use DR\Ipp\Protocol\IppResponseParser;
use DR\Ipp\Protocol\IppResponseParserInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class Ipp implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private IppResponseParserInterface $parser;
    private PrintOperationFactory $printOperationFactory;

    private ?CupsCreatePrinter $cupsCreatePrinter = null;
    private ?CupsDeletePrinter $cupsDeletePrinter = null;
    private ?PrintOperation $printOperation = null;
    private ?GetJobAttributesOperation $getJobAttributes = null;

    public function __construct(
        private readonly IppServer $server,
        private readonly ClientInterface $client,
        ?IppResponseParserInterface $parser = null
    ) {
        $this->parser                = $parser ?? new IppResponseParser();
        $this->printOperationFactory = new PrintOperationFactory();
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function createPrinter(IppPrinter $printer): IppResponseInterface
    {
        $this->cupsCreatePrinter ??= new CupsCreatePrinter($this->server, $this->client, $this->parser);

        return $this->cupsCreatePrinter->create($printer);
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function deletePrinter(IppPrinter $printer): IppResponseInterface
    {
        $this->cupsDeletePrinter ??= new CupsDeletePrinter($this->server, $this->client, $this->parser);

        return $this->cupsDeletePrinter->delete($printer);
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function print(IppPrinter $printer, IppPrintFile $file): IppResponseInterface
    {
        $this->printOperation ??= $this->printOperationFactory->create($this->server, $this->client, $this->parser, $this->logger);

        return $this->printOperation->print($printer, $file);
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function getJobAttributes(string $jobUri): IppResponseInterface
    {
        $this->getJobAttributes ??= new GetJobAttributesOperation($this->server, $this->client, $this->parser);

        return $this->getJobAttributes->getJob($jobUri);
    }
}
