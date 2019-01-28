<?php declare(strict_types=1);

namespace Polus\JsonSchemaInput\Input;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Polus\JsonSchemaInput\Factory;

abstract class AbstractJsonSchemaInput
{
    /** @var Factory */
    protected $validatorFactory;
    /** @var LoggerInterface */
    protected $logger;

    public function __construct(Factory $schemaFactory, LoggerInterface $logger)
    {
        $this->validatorFactory = $schemaFactory;
        $this->logger = $logger;
    }

    protected function validate(ServerRequestInterface $request, string $schema): array
    {
        $body = $request->getParsedBody();

        $validator = $this->validatorFactory->createFromSchema($schema);
        $result = $validator->validate($body);

        if (!$result->isValid()) {
            $msg = 'Invalid payload';
            $error = $result->getFirstError();
            if ($error && $error->keyword()) {
                $field = '';
                foreach ($error->dataPointer() as $point) {
                    if (\is_int($point)) {
                        $field .= "[$point]";
                    }
                    else {
                        $field .= ".$point";
                    }
                }
                $field = ltrim($field, '.');

                $msg = ucfirst($error->keyword()) . ": $field\n";
                foreach ($error->keywordArgs() as $type => $value) {
                    $msg .= "$value $type\n";
                }
            }
            $this->logger->debug('Scheme invalid', [
                'body' => $body,
            ]);
            throw new \DomainException($msg);
        }

        return (array) $validator->getFormattedData();
    }
}
