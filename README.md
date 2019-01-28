# JsonSchemaInput for Polus\Adr

Helper class to make it easier to use [JsonSchema's](https://json-schema.org/) to validate request input

## Install

    composer require polus/adr-json-schema-input
    
## Usage

### SchemaMap

To use the abstract input class you need to implement a `SchemaMap` That will
contains information about the schemas and a way to load them.

```php
use Polus\JsonSchemaInput\SchemaMapInterface;

class SchemaMap implements SchemaMapInterface
{
	public const SCHEMA_NS = 'https://schema_ns/schemas/';

	public const TEST_SCHEMA = 'subDirectory/test.json';

	private const ALLOWED_SCHEMAS = [
		self::TEST_SCHEMA,
	];

	public function getDefaultNamespace(): string
	{
		return self::SCHEMA_NS;
	}

	/**
	 * @return string[]
	 */
	public function getSchemaPaths(): array
	{
		return [
			'path to where schemas are stored',
		];
	}

	public function exists(string $schema): bool
	{
		return in_array($schema, self::ALLOWED_SCHEMAS, true);
	}

	public function get(string $schema): string
	{
		if ($this->exists($schema)) {
			return self::SCHEMA_NS . $schema;
		}

		return '';
	}

	public function load(string $schema): ?array
	{
		foreach ($this->getSchemaPaths() as $path) {
			if (file_exists($path.'/'.$schema)) {
				return json_decode($path.'/'.$schema, true);
			}
		}
		return null
	}
}
```

### Input

```php
use Psr\Http\Message\ServerRequestInterface;
use Polus\JsonSchemaInput\Input\AbstractJsonSchemaInput;

class TestInput extends AbstractJsonSchemaInput
{
	public function __invoke(ServerRequestInterface $request)
	{
		$body = $this->validate($request, SchemaMap::TEST_SCHEMA);

		return $body;
	}
}
```

If the validate fails it will throw DomainException with the JsonSchema error message
