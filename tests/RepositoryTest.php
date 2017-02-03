<?php

namespace Fuguevit\Repositories\Tests;

use Fuguevit\Repositories\Tests\Models\Repositories\ArticleRepository;
use Illuminate\Container\Container as App;
use Illuminate\Database\Eloquent\Collection;

class RepositoryTest extends TestCase
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
     * Test repository can create eloquent entity.
     */
    public function test_it_can_create_entity()
    {
        $article = $this->articleRepository->create([
            'title' => 'foo',
            'body'  => 'bar',
        ]);

        $this->assertInstanceOf('Fuguevit\Repositories\Tests\Models\Article', $article);
    }

    /**
     * Test repository can save entity.
     */
    public function test_it_can_save_entity()
    {
        $result = $this->articleRepository->save([
            'title' => 'foo',
            'body'  => 'bar',
        ]);

        $this->assertTrue($result);
    }

    /**
     * Test repository can update entity.
     */
    public function test_it_can_update_entity()
    {
        $id = $this->articleRepository->create([
            'title' => 'foo',
            'body'  => 'bar',
        ])->id;

        $updated = $this->articleRepository->update(['title' => 'bar', 'body'  => 'foo'], $id);

        $this->assertEquals('bar', $updated->title);
    }

    /**
     * Test repository can find entity.
     */
    public function test_it_can_find_entity()
    {
        $this->createArticles(20);

        $result = $this->articleRepository->find(10);
        
        $this->assertInstanceOf('Fuguevit\Repositories\Tests\Models\Article', $result);
    }

    /**
     * Test repository can use enhanced find.
     */
    public function test_it_can_use_enhanced_find()
    {
        $this->createArticles(20);

        $result = $this->articleRepository->findWhere([
            'id.less_than'       => ['id', '<', '18'],
            'id.more_equal_than' => ['id', '>=', '9'],
        ]);

        $this->assertCount(9, $result->toArray());
    }

    /**
     * Test repository can use lists function.
     */
    public function test_it_can_use_lists()
    {
        $this->createArticles(20);
        
        $result = $this->articleRepository->lists('id');
        $this->assertCount(20, $result);
    }

    /**
     * Test repository can paginate entities.
     */
    public function test_it_can_paginate_entities()
    {
        $this->createArticles(100);

        $result = $this->articleRepository->paginate(20);

        $this->assertInstanceOf('Illuminate\Pagination\LengthAwarePaginator', $result);
    }
}
