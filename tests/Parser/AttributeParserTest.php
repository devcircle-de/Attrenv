<?php

declare(strict_types=1);

namespace DevCircleDe\Attrenv\Tests\Parser;

use DevCircleDe\Attrenv\Parser\AttributeParser;
use DevCircleDe\Attrenv\Tests\data\TestClassWithAttributeInConstructor;
use DevCircleDe\Attrenv\Tests\data\TestClassWithAttributeInConstructorAndProperty;
use DevCircleDe\Attrenv\Tests\data\TestClassWithProperties;
use DevCircleDe\Attrenv\Util\MetaDataFactory;
use DevCircleDe\Attrenv\Util\ValueFactory;
use DevCircleDe\EnvReader\EnvParser;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DevCircleDe\Attrenv\Parser\AttributeParser
 */
class AttributeParserTest extends TestCase
{
    /**
     * @covers ::parse
     */
    public function testParseWithPropertyAndConstructorArgs(): void
    {
        putenv('DATABASE_NAME=fooBar');
        putenv('DATABASE_HOSTNAME=https://mysecret-host.de');
        putenv('DATABASE_PASSWORD=s€cr€t');
        putenv('DATABASE_PORT=1234');
        putenv("DB_OPTION_JSON={\"name\":\"secretName\", \"values\":[{\"value1\":123},{\"value2\":\"baz\"}]}");

        $parser = new AttributeParser(new MetaDataFactory(), new ValueFactory(new EnvParser()));

        /** @var TestClassWithAttributeInConstructorAndProperty $testClassObject */
        $testClassObject = $parser->parse(TestClassWithAttributeInConstructorAndProperty::class);
        $this->assertSame('fooBar', $testClassObject->getDbName());
        $this->assertSame('https://mysecret-host.de', $testClassObject->getDbHostname());
        $this->assertSame('s€cr€t', $testClassObject->getPassword());
        $this->assertSame(1234, $testClassObject->getDatabasePort());
        $this->assertSame([], $testClassObject->getOptions());
        $this->assertEquals(
            ['name' => 'secretName', 'values' => [['value1' => 123], ['value2' => 'baz']]],
            $testClassObject->getConfig()
        );
    }

    /**
     * @covers ::parse
     */
    public function testParseWithProperty(): void
    {
        putenv('DATABASE_NAME=fooBar');
        putenv('DATABASE_HOSTNAME=https://mysecret-host.de');
        putenv('DATABASE_PASSWORD=s€cr€t');
        putenv('DATABASE_PORT=1234');
        putenv("DB_OPTION_JSON={\"name\":\"secretName\", \"values\":[{\"value1\":123},{\"value2\":\"baz\"}]}");

        $parser = new AttributeParser(new MetaDataFactory(), new ValueFactory(new EnvParser()));

        /** @var TestClassWithProperties $testClassObject */
        $testClassObject = $parser->parse(TestClassWithProperties::class);
        $this->assertSame('fooBar', $testClassObject->getDatabaseName());
        $this->assertSame('s€cr€t', $testClassObject->getDatabasePassword());
        $this->assertSame(1234, $testClassObject->getDatabasePort());
        $this->assertSame([], $testClassObject->getOptions());
        $this->assertEquals(
            ['name' => 'secretName', 'values' => [['value1' => 123], ['value2' => 'baz']]],
            $testClassObject->getOptionsFromJson()
        );
    }

    /**
     * @covers ::parse
     */
    public function testParseWithConstructorArgs(): void
    {
        putenv('DATABASE_NAME=fooBar');
        putenv('DATABASE_HOSTNAME=https://mysecret-host.de');
        putenv('DATABASE_PASSWORD=s€cr€t');
        putenv('DATABASE_PORT=1234');
        putenv("DB_OPTION_JSON={\"name\":\"secretName\", \"values\":[{\"value1\":123},{\"value2\":\"baz\"}]}");

        $parser = new AttributeParser(new MetaDataFactory(), new ValueFactory(new EnvParser()));

        /** @var TestClassWithAttributeInConstructor $testClassObject */
        $testClassObject = $parser->parse(TestClassWithAttributeInConstructor::class);
        $this->assertSame('fooBar', $testClassObject->getDatabaseName());
        $this->assertSame('s€cr€t', $testClassObject->getDatabasePassword());
        $this->assertSame(1234, $testClassObject->getDatabasePort());
        $this->assertSame([], $testClassObject->getOptions());
        $this->assertEquals(
            ['name' => 'secretName', 'values' => [['value1' => 123], ['value2' => 'baz']]],
            $testClassObject->getOptionsFromJson()
        );
    }
}
