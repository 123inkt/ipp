<?php

declare(strict_types=1);

namespace DR\Ipp\Protocol\Response;

use DR\Ipp\Entity\Response\CupsIppResponse;
use DR\Ipp\Entity\Response\IppResponseInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @internal
 */
class IppGetJobsResponseParser extends IppResponseParser
{
    /**
     * @see https://datatracker.ietf.org/doc/html/rfc8010/#section-3.1
     */
    public function getResponse(ResponseInterface $response): IppResponseInterface
    {
        $state = new IppResponseState($response->getBody()->getContents());

        $statusCode     = $this->parseHeader($state);
        $collections    = $this->parseAttributes($state)->getAttributes();

        $jobs = [];
        foreach ($collections as $collection) {
            $job = $this->jobFactory->create($collection);
            if ($job !== null) {
                $jobs[] = $job;
            }
        }

        return new CupsIppResponse($statusCode, [], $jobs);
    }
}
