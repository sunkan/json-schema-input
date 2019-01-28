<?php declare(strict_types=1);

namespace Polus\JsonSchemaInput;

use Opis\JsonSchema\Loaders\File as JsonSchemaFile;

class Factory
{
    /** @var SchemaMapInterface */
    private $schemaMap;

    public function __construct(SchemaMapInterface $schemaMap)
    {
        $this->schemaMap = $schemaMap;
    }

    public function createFromSchema(string $schema): Validator
    {
        if (!$this->schemaMap->exists($schema)) {
            throw new \InvalidArgumentException('schema not found');
        }

        $loader = new JsonSchemaFile(
            $this->schemaMap->getDefaultNamespace(),
            $this->schemaMap->getSchemaPaths()
        );

        return new Validator($loader, $this->schemaMap->get($schema));
    }
}
