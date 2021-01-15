<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\AstRunner;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassLikeName;
use SensioLabs\Deptrac\AstRunner\AstParser\AstFileReferenceInMemoryCache;
use SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;
use SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser\ParserFactory;
use SensioLabs\Deptrac\AstRunner\AstRunner;
use SensioLabs\Deptrac\AstRunner\Resolver\TypeResolver;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Tests\SensioLabs\Deptrac\AstRunner\Fixtures\FixtureBasicInheritanceA;
use Tests\SensioLabs\Deptrac\AstRunner\Fixtures\FixtureBasicInheritanceB;
use Tests\SensioLabs\Deptrac\AstRunner\Fixtures\FixtureBasicInheritanceC;
use Tests\SensioLabs\Deptrac\AstRunner\Fixtures\FixtureBasicInheritanceD;
use Tests\SensioLabs\Deptrac\AstRunner\Fixtures\FixtureBasicInheritanceE;
use Tests\SensioLabs\Deptrac\AstRunner\Fixtures\FixtureBasicInheritanceInterfaceA;
use Tests\SensioLabs\Deptrac\AstRunner\Fixtures\FixtureBasicInheritanceInterfaceB;
use Tests\SensioLabs\Deptrac\AstRunner\Fixtures\FixtureBasicInheritanceInterfaceC;
use Tests\SensioLabs\Deptrac\AstRunner\Fixtures\FixtureBasicInheritanceInterfaceD;
use Tests\SensioLabs\Deptrac\AstRunner\Fixtures\FixtureBasicInheritanceInterfaceE;
use Tests\SensioLabs\Deptrac\AstRunner\Fixtures\FixtureBasicInheritanceWithNoiseA;
use Tests\SensioLabs\Deptrac\AstRunner\Fixtures\FixtureBasicInheritanceWithNoiseB;
use Tests\SensioLabs\Deptrac\AstRunner\Fixtures\FixtureBasicInheritanceWithNoiseC;
use Tests\SensioLabs\Deptrac\AstRunner\Fixtures\MultipleInteritanceA;
use Tests\SensioLabs\Deptrac\AstRunner\Fixtures\MultipleInteritanceA1;
use Tests\SensioLabs\Deptrac\AstRunner\Fixtures\MultipleInteritanceA2;
use Tests\SensioLabs\Deptrac\AstRunner\Fixtures\MultipleInteritanceB;
use Tests\SensioLabs\Deptrac\AstRunner\Fixtures\MultipleInteritanceC;

final class AstMapFlattenGeneratorTest extends TestCase
{
    use ArrayAsserts;

    private function getAstMap(string $fixture): AstMap
    {
        $astRunner = new AstRunner(
            new EventDispatcher(),
            new NikicPhpParser(
                ParserFactory::createParser(),
                new AstFileReferenceInMemoryCache(),
                new TypeResolver()
            )
        );

        return $astRunner->createAstMapByFiles([__DIR__.'/Fixtures/BasicInheritance/'.$fixture.'.php']);
    }

    private function getInheritedInherits(string $class, AstMap $astMap): array
    {
        $inherits = [];
        foreach ($astMap->getClassInherits(ClassLikeName::fromFQCN($class)) as $v) {
            if (count($v->getPath()) > 0) {
                $inherits[] = (string) $v;
            }
        }

        return $inherits;
    }

    public function testBasicInheritance(): void
    {
        $astMap = $this->getAstMap('FixtureBasicInheritance');

        self::assertArrayValuesEquals(
            [],
            $this->getInheritedInherits(FixtureBasicInheritanceA::class, $astMap)
        );

        self::assertArrayValuesEquals(
            [],
            $this->getInheritedInherits(FixtureBasicInheritanceB::class, $astMap)
        );

        self::assertArrayValuesEquals(
            ['Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceA::6 (Extends) (path: Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceB::7 (Extends))'],
            $this->getInheritedInherits(FixtureBasicInheritanceC::class, $astMap)
        );

        self::assertArrayValuesEquals(
            [
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceA::6 (Extends) (path: Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceC::8 (Extends) -> Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceB::7 (Extends))',
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceB::7 (Extends) (path: Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceC::8 (Extends))',
            ],
            $this->getInheritedInherits(FixtureBasicInheritanceD::class, $astMap)
        );

        self::assertArrayValuesEquals(
            [
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceA::6 (Extends) (path: Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceD::9 (Extends) -> Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceC::8 (Extends) -> Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceB::7 (Extends))',
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceB::7 (Extends) (path: Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceD::9 (Extends) -> Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceC::8 (Extends))',
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceC::8 (Extends) (path: Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceD::9 (Extends))',
            ],
            $this->getInheritedInherits(FixtureBasicInheritanceE::class, $astMap)
        );
    }

    public function testBasicInheritanceInterfaces(): void
    {
        $astMap = $this->getAstMap('FixtureBasicInheritanceInterfaces');

        self::assertArrayValuesEquals(
            [],
            $this->getInheritedInherits(FixtureBasicInheritanceInterfaceA::class, $astMap)
        );

        self::assertArrayValuesEquals(
            [],
            $this->getInheritedInherits(FixtureBasicInheritanceInterfaceB::class, $astMap)
        );

        self::assertArrayValuesEquals(
            ['Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceInterfaceA::6 (Implements) (path: Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceInterfaceB::7 (Implements))'],
            $this->getInheritedInherits(FixtureBasicInheritanceInterfaceC::class, $astMap)
        );

        self::assertArrayValuesEquals(
            [
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceInterfaceA::6 (Implements) (path: Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceInterfaceC::8 (Implements) -> Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceInterfaceB::7 (Implements))',
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceInterfaceB::7 (Implements) (path: Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceInterfaceC::8 (Implements))',
            ],
            $this->getInheritedInherits(FixtureBasicInheritanceInterfaceD::class, $astMap)
        );

        self::assertArrayValuesEquals(
            [
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceInterfaceA::6 (Implements) (path: Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceInterfaceD::9 (Implements) -> Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceInterfaceC::8 (Implements) -> Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceInterfaceB::7 (Implements))',
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceInterfaceB::7 (Implements) (path: Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceInterfaceD::9 (Implements) -> Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceInterfaceC::8 (Implements))',
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceInterfaceC::8 (Implements) (path: Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceInterfaceD::9 (Implements))',
            ],
            $this->getInheritedInherits(FixtureBasicInheritanceInterfaceE::class, $astMap)
        );
    }

    public function testBasicMultipleInheritanceInterfaces(): void
    {
        $astMap = $this->getAstMap('MultipleInheritanceInterfaces');

        self::assertArrayValuesEquals(
            [],
            $this->getInheritedInherits(MultipleInteritanceA1::class, $astMap)
        );

        self::assertArrayValuesEquals(
            [],
            $this->getInheritedInherits(MultipleInteritanceA2::class, $astMap)
        );

        self::assertArrayValuesEquals(
            [],
            $this->getInheritedInherits(MultipleInteritanceA::class, $astMap)
        );

        self::assertArrayValuesEquals(
            [
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\MultipleInteritanceA1::7 (Implements) (path: Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\MultipleInteritanceA::8 (Implements))',
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\MultipleInteritanceA2::7 (Implements) (path: Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\MultipleInteritanceA::8 (Implements))',
            ],
            $this->getInheritedInherits(MultipleInteritanceB::class, $astMap)
        );

        self::assertArrayValuesEquals(
            [
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\MultipleInteritanceA1::7 (Implements) (path: Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\MultipleInteritanceB::9 (Implements) -> Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\MultipleInteritanceA::8 (Implements))',
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\MultipleInteritanceA1::8 (Implements) (path: Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\MultipleInteritanceB::9 (Implements))',
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\MultipleInteritanceA2::7 (Implements) (path: Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\MultipleInteritanceB::9 (Implements) -> Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\MultipleInteritanceA::8 (Implements))',
                'Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\MultipleInteritanceA::8 (Implements) (path: Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\MultipleInteritanceB::9 (Implements))',
            ],
            $this->getInheritedInherits(MultipleInteritanceC::class, $astMap)
        );
    }

    public function testBasicMultipleInheritanceWithNoise(): void
    {
        $astMap = $this->getAstMap('FixtureBasicInheritanceWithNoise');

        self::assertArrayValuesEquals(
            [],
            $this->getInheritedInherits(FixtureBasicInheritanceWithNoiseA::class, $astMap)
        );

        self::assertArrayValuesEquals(
            [],
            $this->getInheritedInherits(FixtureBasicInheritanceWithNoiseB::class, $astMap)
        );

        self::assertArrayValuesEquals(
            ['Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceWithNoiseA::18 (Extends) (path: Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\FixtureBasicInheritanceWithNoiseB::19 (Extends))'],
            $this->getInheritedInherits(FixtureBasicInheritanceWithNoiseC::class, $astMap)
        );
    }
}
