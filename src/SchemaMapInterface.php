<?php declare(strict_types=1);

namespace Polus\JsonSchemaInput;

interface SchemaMapInterface
{
    public function getDefaultNamespace(): string;

    /**
     * @return string[]
     */
    public function getSchemaPaths(): array;

    public function exists(string $schema): bool;

    /**
     * Return a complete schema url
     *
     * @param string $schema
     * @return string
     */
    public function get(string $schema): string;

    /**
     * Read the json schema definition
     *
     * @param string $schema
     * @return array|null
     */
    public function load(string $schema): ?array;
}
