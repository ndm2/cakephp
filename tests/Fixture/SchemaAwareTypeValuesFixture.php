<?php
declare(strict_types=1);

namespace Cake\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;
use TestApp\Database\SchemaAwareTypeValueObject;

class SchemaAwareTypeValuesFixture extends TestFixture
{
    public $fields = [
        'id' => ['type' => 'integer'],
        'val' => ['type' => 'schemaawaretype', 'null' => false],
    ];

    public function init(): void
    {
        parent::init();

        $this->records = [
            [
                'id' => 1,
                'val' => new SchemaAwareTypeValueObject('THIS TEXT SHOULD BE PROCESSED VIA A CUSTOM TYPE'),
            ],
            [
                'id' => 2,
                'val' => 'THIS TEXT ALSO SHOULD BE PROCESSED VIA A CUSTOM TYPE',
            ],
        ];
    }
}
