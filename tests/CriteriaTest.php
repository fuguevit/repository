<?php

namespace Fuguevit\Repositories\Tests;

use Fuguevit\Repositories\Tests\Models\Criteria\BodyContainsHello;
use Fuguevit\Repositories\Tests\Models\Repositories\ArticleRepository;
use Illuminate\Container\Container as App;
use Illuminate\Database\Eloquent\Collection;

class CriteriaTest extends TestCase
{
    protected $articleRepository;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->articleRepository = $this->setupArticleRepo();
    }

    /**
     * Set up Article Repository.
     *
     * @return ArticleRepository
     */
    protected function setupArticleRepo()
    {
        return new ArticleRepository(new App(), new Collection());
    }

    /**
     * Test repository can add criteria for specific use.
     */
    public function test_it_can_add_criteria()
    {
        $this->createArticles(10);
        $this->articleRepository->create([
            'title'  => 'foo',
            'body'   => 'hello',
        ]);

        $result = $this->articleRepository->pushCriteria(new BodyContainsHello())->all();
        $this->assertGreaterThanOrEqual(1, $result->count());
    }
}
