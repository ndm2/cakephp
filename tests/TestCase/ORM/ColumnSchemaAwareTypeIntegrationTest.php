<?php
declare(strict_types=1);

namespace Cake\Test\TestCase\ORM;

use Cake\Database\TypeFactory;
use Cake\TestSuite\TestCase;
use TestApp\Database\Type\ColumnSchemaAwareType;

class ColumnSchemaAwareTypeIntegrationTest extends TestCase
{
    protected $fixtures = [
        'core.ColumnSchemaAwareTypeValues',
    ];

    public $autoFixtures = false;

    public function setUp(): void
    {
        parent::setUp();

        TypeFactory::map('character', ColumnSchemaAwareType::class);

        $this->loadFixtures('ColumnSchemaAwareTypeValues');
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $map = TypeFactory::getMap();
        unset($map['character']);
        TypeFactory::setMap($map);
    }

    public function testCustomTypesCanBeUsedInFixtures()
    {
        $table = $this->getTableLocator()->get('ColumnSchemaAwareTypeValues');

        $expected = [
            'this text has been processed via a custom type',
            'this text also has been processed via a custom type',
        ];
        $result = $table->find()->orderAsc('id')->extract('val')->toArray();
        $this->assertSame($expected, $result);
    }

    public function testCustomTypeCanProcessColumnInfo()
    {
        $column = $this->getTableLocator()->get('ColumnSchemaAwareTypeValues')->getSchema()->getColumn('val');

        $this->assertSame('string', $column['type']);
        $this->assertSame(128, $column['length']);
        $this->assertSame('Custom schema aware type comment', $column['comment']);
    }

    public function testCustomTypeReceivesAllColumnDefinitionKeys()
    {
        $table = $this->getTableLocator()->get('ColumnSchemaAwareTypeValues');

        $type = $this
            ->getMockBuilder(ColumnSchemaAwareType::class)
            ->setConstructorArgs(['character'])
            ->onlyMethods(['convertColumnDefinition'])
            ->getMock();

        $type
            ->expects($this->once())
            ->method('convertColumnDefinition')
            ->with(
                [
                    'length' => 255,
                    'precision' => null,
                    'scale' => null,
                ],
                $table->getConnection()->getDriver()
            )
            ->willReturn(null);

        TypeFactory::set('character', $type);

        $table->getSchema()->getColumn('val');
    }
}
