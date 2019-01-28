<?php declare(strict_types=1);

namespace Polus\JsonSchemaInput;

use Opis\JsonSchema\Exception\InvalidSchemaException;
use Opis\JsonSchema\ISchema;
use Opis\JsonSchema\ISchemaLoader;
use Opis\JsonSchema\ValidationResult;
use Opis\JsonSchema\Validator as OpisValidator;
use Opis\JsonSchema\ValidatorHelper;

class Validator extends OpisValidator
{
    protected $schema;
    /** @var ISchemaLoader */
    protected $loader;

    private $wasArray = false;
    private $formattedData;

    public function __construct(ISchemaLoader $loader, string $schema)
    {
        $this->schema = $schema;
        parent::__construct(new ValidatorHelper(), $loader);
    }

    public function validate($data): ValidationResult
    {
        $this->wasArray = false;
        if (\is_array($data)) {
            $data = json_decode((string) json_encode($data));
            $this->wasArray = true;
        }

        $schema = $this->loader->loadSchema($this->schema);

        if ($schema instanceof ISchema) {
            $rs = $this->schemaValidation($data, $schema);
            $this->formattedData = $data;
        }
        else {
            throw new InvalidSchemaException('');
        }

        return $rs;
    }

    /**
     * @return array|\stdClass
     */
    public function getFormattedData()
    {
        if ($this->wasArray) {
            return (array) $this->formattedData;
        }
        return $this->formattedData;
    }
}
