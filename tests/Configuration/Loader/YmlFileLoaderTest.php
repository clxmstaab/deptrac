<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Configuration\Loader;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Configuration\Exception\FileCannotBeParsedAsYamlException;
use Qossmic\Deptrac\Configuration\Exception\ParsedYamlIsNotAnArrayException;
use Qossmic\Deptrac\Configuration\Loader\YmlFileLoader;
use Qossmic\Deptrac\File\CouldNotReadFileException;

/**
 * @covers \Qossmic\Deptrac\Configuration\Loader\YmlFileLoader
 */
final class YmlFileLoaderTest extends TestCase
{
    public function testLoadThrowsFileDoesNotExistsExceptionWhenFileDoesNotExist(): void
    {
        $file = __DIR__.'/../Fixtures/non-existent-file.yaml';

        $loader = new YmlFileLoader();

        $this->expectException(CouldNotReadFileException::class);

        $loader->parseFile($file);
    }

    public function testLoadThrowsFileCannotBeParsedAsYamlExceptionWhenFileDoesNotContainYaml(): void
    {
        $file = __FILE__;

        $loader = new YmlFileLoader();

        $this->expectException(FileCannotBeParsedAsYamlException::class);

        $loader->parseFile($file);
    }

    public function testLoadThrowsParsedYamlIsNotAnArrayExceptionWhenFileDoesNotContainYamlThatCanBeParsedToAnArray(): void
    {
        $file = __DIR__.'/../Fixtures/does-not-contain-array.yml';

        $loader = new YmlFileLoader();

        $this->expectException(ParsedYamlIsNotAnArrayException::class);

        $loader->parseFile($file);
    }
}
