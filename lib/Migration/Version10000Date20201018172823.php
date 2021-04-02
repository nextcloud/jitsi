<?php

namespace OCA\jitsi\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

class Version10000Date20201018172823 extends SimpleMigrationStep
{
    public function changeSchema(
        IOutput $output,
        Closure $schemaClosure,
        array $options
    ) {
        /** @var ISchemaWrapper $schema */
        $schema = $schemaClosure();

        $table = $schema->createTable('jitsi_rooms');
        $table->addColumn(
            'id',
            'integer',
            [
                'autoincrement' => true,
                'notnull' => true,
            ]
        );
        $table->addColumn(
            'name',
            'string',
            [
                'notnull' => true,
                'length' => 255,
            ]
        );
        $table->addColumn(
            'public_id',
            'string',
            [
                'notnull' => true,
                'length' => 255,
            ]
        );
        $table->addColumn(
            'creator_id',
            'string',
            [
                'notnull' => true,
                'length' => 64,
            ]
        );

        $table->setPrimaryKey(['id']);
        $table->addIndex(['creator_id'], 'jitsi_creator_id_index');
        $table->addIndex(['public_id'], 'jitsi_public_id_index');
        return $schema;
    }
}
