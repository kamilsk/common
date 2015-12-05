<?php

namespace Test\OctoLab\Common\Doctrine\Migration;

use Doctrine\DBAL\Schema\Schema;
use OctoLab\Common\Doctrine\Migration\FileBasedMigration;

/**
 * phpunit src/Tests/Doctrine/Migration/FileBasedMigrationTest.php
 *
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class FileBasedMigrationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider schemaProvider
     *
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $migration = $this->getMigrationMock(Mock\FileBasedMigration::class);
        $this->checkMigrations($migration, $migration->getUpgradeMigrations());
        $migration->preUp($schema);
        self::assertNotEmpty($migration->getQueries());
        $migration->up($schema);
        $migration->postUp($schema);
        self::assertEmpty($migration->getQueries());
    }

    /**
     * @test
     * @dataProvider schemaProvider
     *
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $migration = $this->getMigrationMock(Mock\FileBasedMigration::class);
        $this->checkMigrations($migration, $migration->getDowngradeMigrations());
        $migration->preDown($schema);
        self::assertNotEmpty($migration->getQueries());
        $migration->down($schema);
        $migration->postDown($schema);
        self::assertEmpty($migration->getQueries());
    }

    /**
     * @return array[]
     */
    public function schemaProvider()
    {
        return [
            [new Schema()],
        ];
    }

    /**
     * @param string $class
     *
     * @return FileBasedMigration
     */
    private function getMigrationMock($class)
    {
        return (new \ReflectionClass($class))->newInstanceWithoutConstructor();
    }

    /**
     * @param FileBasedMigration $migration
     * @param string[] $files
     */
    private function checkMigrations(FileBasedMigration $migration, array $files)
    {
        foreach ($files as $file) {
            self::assertFileExists($migration->getFullPath($file));
        }
    }
}