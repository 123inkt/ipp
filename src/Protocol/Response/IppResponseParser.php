<?php

declare(strict_types=1);

namespace DR\Ipp\Protocol\Response;

use DR\Ipp\Entity\Response\CupsIppResponse;
use DR\Ipp\Entity\Response\IppResponseInterface;
use DR\Ipp\Enum\IppOperationTagEnum;
use DR\Ipp\Enum\IppStatusCodeEnum;
use DR\Ipp\Enum\IppTypeEnum;
use DR\Ipp\Factory\IppJobFactory;
use DR\Ipp\Protocol\IppAttribute;
use DR\Ipp\Protocol\IppCollection;
use Psr\Http\Message\ResponseInterface;

/**
 * @internal
 */
class IppResponseParser implements IppResponseParserInterface
{
    private IppAttribute $lastAttribute;

    public function __construct(protected readonly IppJobFactory $jobFactory)
    {
    }

    /**
     * @see https://datatracker.ietf.org/doc/html/rfc8010/#section-3.1
     */
    public function getResponse(ResponseInterface $response): IppResponseInterface
    {
        $state = new IppResponseState($response->getBody()->getContents());

        $statusCode     = $this->parseHeader($state);
        $attributeStore = $this->parseAttributes($state);
        $attributes     = $attributeStore->getNormalizedAttributes();
        $job            = $this->jobFactory->create($attributes);
        $jobs           = $job === null ? [] : [$job];

        return new CupsIppResponse($statusCode, $attributes, $jobs);
    }

    protected function parseHeader(IppResponseState $state): IppStatusCodeEnum
    {
        $state->consume(2, IppTypeEnum::Int);            // version   0x0101
        /** @var int $status */
        $status = $state->consume(2, IppTypeEnum::Int);  // status    0x0502
        $state->consume(4, IppTypeEnum::Int);            // requestId 0x00000001
        $state->consume(1, IppTypeEnum::Int);            // IPPOperationTag::OPERATION_ATTRIBUTE_START

        return IppStatusCodeEnum::tryFrom($status) ?? IppStatusCodeEnum::Unknown;
    }

    protected function parseAttributes(IppResponseState $state): IppAttributeStore
    {
        $attributeStore = new IppAttributeStore();

        $attributesTags = [
            IppOperationTagEnum::JobAttributeStart->value,
            IppOperationTagEnum::PrinterAttributeStart->value,
            IppOperationTagEnum::UnsupportedAttributes->value,
        ];
        while ($state->getNextByte() !== IppOperationTagEnum::AttributeEnd->value) {
            // look for an attribute tag and remove it before parsing further attributes
            if (in_array($state->getNextByte(), $attributesTags, true)) {
                $state->consume(1, null);
            }

            $attributeStore->storeAttribute($this->getAttribute($state));
        }
        $attributeStore->flush();

        return $attributeStore;
    }

    /**
     * @see https://datatracker.ietf.org/doc/html/rfc8010/#section-3.1.6
     */
    private function getCollection(IppResponseState $state): IppCollection
    {
        $collection = new IppCollection();

        $state->consume(2, null); // 0x0000
        while ($state->getNextByte() !== IppTypeEnum::EndCollection->value) {
            $state->consume(3, null); // 0x4a 0x00 0x00

            /** @var string $name */
            $name = $this->getAttributeValue(IppTypeEnum::MemberAttributeName, $state);
            /** @var int $valueType */
            $valueType = $state->consume(1, null);
            $state->consume(2, null); // 0x00 0x00

            /** @var int $valueLength */
            $valueLength = $state->consume(2, IppTypeEnum::Int);
            $value       = $state->consume($valueLength, IppTypeEnum::tryFrom($valueType));
            $collection->add($name, $value);
        }
        $state->consume(5, null); // 0x37 0x00 0x00 0x00 0x00

        return $collection;
    }

    /**
     * Decodes an attribute from the response, and returns the decoded value(s)
     */
    private function getAttribute(IppResponseState $state): ?IppAttribute
    {
        /** @var int $type */
        $type     = $state->consume(1, null);
        $attrType = IppTypeEnum::tryFrom($type);

        /** @var int $nameLength */
        $nameLength = $state->consume(2, IppTypeEnum::Int);
        // Additional value https://datatracker.ietf.org/doc/html/rfc8010/#section-3.1.5
        if ($nameLength === 0x0000) {
            $this->lastAttribute->appendValue($this->getAttributeValue($attrType, $state));

            return null;
        }

        /** @var string $attrName */
        $attrName  = $state->consume($nameLength, IppTypeEnum::NameWithoutLang);
        $attrValue = $this->getAttributeValue($attrType, $state);

        $this->lastAttribute = new IppAttribute($attrType ?? IppTypeEnum::Int, $attrName, $attrValue);

        return $this->lastAttribute;
    }

    private function getAttributeValue(?IppTypeEnum $type, IppResponseState $state): mixed
    {
        if ($type === IppTypeEnum::Collection) {
            return $this->getCollection($state);
        }

        /** @var int $valueLength */
        $valueLength = $state->consume(2, IppTypeEnum::Int);

        return $state->consume($valueLength, $type);
    }
}
