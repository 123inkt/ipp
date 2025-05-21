# Digitalrevolution IPP library

## Installation

```bash
composer require digitalrevolution/ipp
```

## Usage

### Initialize the library

```php
    $server = new IppServer();
    $server->setUri('https://cups.local');
    $server->setUserName('admin'); // optional
    $server->setPassword('admin'); // optional

    $ipp = new Ipp($server, new Psr18Client());
```

### Print a file

```php
    // define a printer        
    $printer = new IppPrinter();
    $printer->setHostname('my.printer');
    
    // print a file on the selected printer
    $ippFile = new IppPrintFile(file_get_contents('/dir/file.ps'), FileTypeEnum::PS);
    $ipp->print($printer, $ippFile);
```

### Fetch job attributes

```php
    $printJob = $ipp->print($printer, $ippFile);
    $updatedPrintJob = $ipp->getJobAttributes($printJob->getJobUri());
```

### Register a printer with cups

```php
    $printer = new IppPrinter();
    $printer->setHostname('my.printer');
    $printer->setDeviceUri('my.uri');
    $printer->setLocation('location');

    $ipp->createPrinter($printer);
```

### Delete a printer

```php
    $printer = new IppPrinter();
    $printer->setHostname('my.printer');

    $ipp->deletePrinter($printer);
```

### Contributing

See [contributing.md](./CONTRIBUTING.md)  
Pull requests welcome for adding standard IPP Operations

### Creating a custom IPP operation

This project is created to be easily extensible, adding a new IPP operation is as simple as making sure it has an identifier in IppOperationEnum  
Then adding any Job, Printer or Operation Attributes as required by your standard.    
Finally sending the request and parsing the response using the standard parser.

```php
public function myOperation(): IppResponseInterface
    $operation = new IppOperation(IppOperationEnum::OperationType);
    $operation->addOperationAttribute(new IppAttribute(IppTypeEnum::Charset, 'attributes-charset', 'utf-8'));
    
    $response = $this->client->sendRequest(
        new Request(
            'POST',
            $this->server->getUri(),
            ['Content-Type' => 'application/ipp'],
            (string)$operation
        )
    );

    return $this->parser->getResponse($response->getBody()->getContents());
}
```

## About us

At 123inkt (Part of Digital Revolution B.V.), every day more than 50 development professionals are working on improving our internal ERP
and our several shops. Do you want to join us? [We are looking for developers](https://www.werkenbij123inkt.nl/zoek-op-afdeling/it).
