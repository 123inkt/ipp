<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Factory;

use DR\Ipp\Entity\IppServer;
use DR\Ipp\Factory\PrintOperationFactory;
use DR\Ipp\Protocol\IppResponseParserInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;

#[CoversClass(PrintOperationFactory::class)]
class PrintOperationFactoryTest extends TestCase
{
    #[DoesNotPerformAssertions]
    public function testCreate(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $client = $this->createMock(ClientInterface::class);
        $parser = $this->createMock(IppResponseParserInterface::class);

        $factory = new PrintOperationFactory();
        $factory->create(new IppServer(), $client, $parser, $logger);
    }
}
