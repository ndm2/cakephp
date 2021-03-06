<?php
declare(strict_types=1);

namespace Cake\Test\TestCase\ORM;

use Cake\Database\TypeFactory;
use Cake\TestSuite\TestCase;
use TestApp\Database\Type\SchemaAwareType;

class SchemaAwareTypeIntegrationTest extends TestCase
{
    protected $fixtures = [
        'core.SchemaAwareTypeValues',
    ];

    public $autoFixtures = false;

    public function setUp(): void
    {
        parent::setUp();

        TypeFactory::map('schemaawaretype', SchemaAwareType::class);
        $this->loadFixtures('SchemaAwareTypeValues');
    }

    public function testCustomTypesCanBeUsedInFixtures()
    {
        $table = $this->getTableLocator()->get('SchemaAwareTypeValues');

        $this->assertSame(
            'this text has been processed via a custom type',
            $table->get(1)->get('val')
        );

        $this->assertSame(
            'this text also has been processed via a custom type',
            $table->get(2)->get('val')
        );
    }
}
