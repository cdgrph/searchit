<?php
namespace presseddigital\searchit\migrations;

use presseddigital\searchit\Searchit;

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;

class Install extends Migration
{
    // Public Properties
    // =========================================================================

    public $driver;

    // Public Methods
    // =========================================================================

    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables())
        {
            $this->createIndexes();
            $this->addForeignKeys();
            Craft::$app->db->schema->refresh();
        }
        return true;
    }

    public function safeDown()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->removeTables();
        return true;
    }

    // Protected Methods
    // =========================================================================

    protected function createTables()
    {
        $tablesCreated = false;

        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%searchit_elementfilters}}');
        if($tableSchema === null)
        {
            $tablesCreated = true;
            $this->createTable(
                '{{%searchit_elementfilters}}',
                [
                    'id' => $this->primaryKey(),
                    'type' => $this->string()->notNull(),
                    'source' => $this->string()->notNull(),
                    'name' => $this->string()->notNull(),
                    'filterType' => $this->string()->notNull()->defaultValue('dynamic'),
                    'settings' => $this->text(),
                    'sortOrder' => $this->smallInteger()->unsigned(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                ]
            );
        }
        return $tablesCreated;
    }

    protected function createIndexes()
    {
        $this->createIndex(
            $this->db->getIndexName('{{%searchit_elementfilters}}', 'name', true),
            '{{%searchit_elementfilters}}',
            ['type', 'source', 'filterType'],
            false
        );
    }

    protected function addForeignKeys()
    {

    }

    protected function removeTables()
    {
        $this->dropTableIfExists('{{%searchit_elementfilters}}');
    }
}
