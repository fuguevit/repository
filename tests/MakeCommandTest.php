<?php

namespace Fuguevit\Repositories\Tests;

use Illuminate\Support\Facades\Artisan;

class MakeCommandTest extends TestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * Test make:repository command.
     */
    public function test_it_can_make_create_repository_command()
    {
        Artisan::call('make:repository', [
            'repository' => 'Tests',
            '--model'    => 'Test',
        ]);

        $test_path = __DIR__.'/../app';
        $test_repository = $test_path.DIRECTORY_SEPARATOR.'Repositories/TestsRepository.php';

        $this->assertFileExists($test_repository);
        app()['FileSystem']->cleanDirectory($test_path);
    }

    /**
     * Test make:criteria command.
     */
    public function test_it_can_make_create_criteria_command()
    {
        Artisan::call('make:criteria', [
            'criteria'  => 'Test',
        ]);

        $test_path = __DIR__.'/../app';
        $test_criteria = $test_path.DIRECTORY_SEPARATOR.'Repositories/Criteria/TestCriteria.php';

        $this->assertFileExists($test_criteria);
        app()['FileSystem']->cleanDirectory($test_path);
    }
}
