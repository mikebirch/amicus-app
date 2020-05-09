<?php

namespace App\Model;

use JasonGrimes\Paginator;

/**
 * BlogPosts model
 */
class BlogPosts extends \Amicus\Model\Model
{
    /**
     * instance of Medoo
     *
     * @var object
     */
    private $database;

    /**
     * application configuration
     *
     * @var array
     */
    private $config;
    
    public function __construct()
    {
        $this->database = static::getDB();
        $this->config = static::getConfig();
    }
    
    /**
     * Get a page of published blog posts from the database
     *
     * @return array
     */
    public function getPage($current_page)
    {
        $items_per_page = 10;
        $offset = ($current_page-1) * $items_per_page; 
        $total_items = $this->database->count('blog_posts', ['published' => 1]);
        $totalPages = ceil($total_items / $items_per_page);
            
        $blog_posts = $this->database->select('blog_posts', [
            'title',
            'slug',
            'summary',
            'sticky',
            'created'
        ], [
            'published' => 1,
            'ORDER' => ['sticky' => 'DESC', 'created' => 'DESC'],
            'LIMIT' => [$offset, $items_per_page]
        ]);
        
        //paginate        
        $url_pattern = '/blog?page=(:num)';
        $paginator = new Paginator($total_items, $items_per_page, $current_page, $url_pattern);
        return ['blog_posts' => $blog_posts, 'paginator' => $paginator];
    }


    /**
     * Get the latest blog posts from the database
     *
     * @return array
     */
    public function getLatest()
    {
        $blog_posts = $this->database->select('blog_posts', [
            'title',
            'slug',
            'summary',
            'sticky',
            'created'
        ], [
            'published' => 1,
            'LIMIT' => $this->config['blog']['number_latest_posts'],
            'ORDER' => ['sticky' => 'DESC', 'created' => 'DESC']
        ]);

        return ['blog_posts' => $blog_posts];
    }

    /**
     * Get a blog post from the database
     *
     * @return array
     */
    public function getBySlug($slug)
    {
        $blog_post = $this->database->get('blog_posts', [
            'id',
            'title',
            'slug',
            'body',
            'sticky',
            'meta_title',
            'meta_description',
            'author',
            'created'
        ], [
            'slug' => $slug,
            'published' => 1
        ]);
        
        return ['blog_post' => $blog_post];
    }

    /**
     * Get all published blog posts from the database
     *
     * @return array
     */
    public function getAll()
    {
        $blog_posts = $this->database->select('blog_posts', [
            'id',
            'title',
            'slug',
            'body',
            'created'
        ], [
            'published' => 1,
            'ORDER' => ['created' => 'DESC']
        ]);

        return ['blog_posts' => $blog_posts];
    } 
}
