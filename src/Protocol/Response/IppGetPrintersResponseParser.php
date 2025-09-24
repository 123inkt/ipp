<?php

declare(strict_types=1);

namespace DR\Ipp\Protocol\Response;

use DR\Ipp\Entity\Response\CupsIppResponse;
use DR\Ipp\Entity\Response\IppResponseInterface;
use DR\Ipp\Factory\IppJobFactory;
use DR\Ipp\Factory\IppPrinterFactory;
use DR\Ipp\Normalizer\IppAttributeCollectionNormalizer;
use Psr\Http\Message\ResponseInterface;

class IppGetPrintersResponseParser extends IppResponseParser
{
    public function __construct(private readonly IppPrinterFactory $printerFactory)
    {
        parent::__construct(new IppJobFactory());
    }

    /**
     * @see https://datatracker.ietf.org/doc/html/rfc8010/#section-3.1
     */
    public function getResponse(ResponseInterface $response): IppResponseInterface
    {
        $state = new IppResponseState($response->getBody()->getContents());

        $statusCode     = $this->parseHeader($state);
        $collections    = $this->parseAttributes($state)->getAttributes();

        $printers = [];
        foreach ($collections as $collection) {
            $printer = $this->printerFactory->create($collection);
            if ($printer !== null) {
                $printers[] = $printer;
            }
        }

        return new CupsIppResponse($statusCode, IppAttributeCollectionNormalizer::getNormalizedAttributes($collections), [], $printers);
    }
}
