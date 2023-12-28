<?php

namespace Imanghafoori\LaravelMicroscope\Tests;

use Imanghafoori\LaravelMicroscope\LaravelPaths\LaravelPaths;

class LaravelPathsTest extends BaseTestClass
{
    /** @test */
    public function get_migration_dirs()
    {
        $customPath = base_path('path/fake/migrations');
        $vendorPath = base_path('vendor/path/to/fake/migrations');

        app('migrator')->path($customPath);
        app('migrator')->path($vendorPath);

        $result = LaravelPaths::migrationDirs();

        $expected = [
            app()->databasePath('migrations'),
        ];

        $this->assertTrue(is_a($result, 'Generator'));
        $result = iterator_to_array($result);
        $this->assertTrue(is_array($result));
        $this->assertEquals($expected, $result);
        $this->assertCount(1, $result);
    }
}
