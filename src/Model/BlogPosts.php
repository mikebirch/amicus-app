<?php

namespace App\Model;

use PDO;
use Showus\Configure\Configure;
use JasonGrimes\Paginator;

/**
 * BlogPosts model
 */
class BlogPosts extends \Showus\Model\Model
{  
    /**
     * Get a page of published blog posts from the database
     *
     * @param int $current_page page number
     * @return array<mixed>
     */
    public static function getPage($current_page)
    {
        $pdo = static::getPDO();
        
        $items_per_page = 10;
        $offset = ($current_page-1) * $items_per_page; 
        $total_items = $pdo->query(
            'SELECT count(*)
            FROM blog_posts
            WHERE published = 1'
        )->fetchColumn();

        $totalPages = ceil($total_items / $items_per_page);
        
        $stmt = $pdo->prepare(
            'SELECT title, slug, summary, sticky, created 
            FROM blog_posts 
            WHERE published = 1
            ORDER BY sticky DESC, created DESC
            LIMIT :offset, :items_per_page'
        );
        $stmt->execute(['offset' => $offset, 'items_per_page' => $items_per_page]);
        $blog_posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        //paginate        
        $url_pattern = '/blog?page=(:num)';
        $paginator = new Paginator($total_items, $items_per_page, $current_page, $url_pattern);
        return ['blog_posts' => $blog_posts, 'paginator' => $paginator];
    }


    /**
     * Get the latest blog posts from the database
     *
     * @return array<mixed>
     */
    public static function getLatest()
    {
        $pdo = static::getPDO();
        $config = Configure::read();
        $stmt = $pdo->prepare(
            'SELECT  title, slug, summary, sticky, created 
            FROM blog_posts 
            WHERE published = 1
            ORDER by sticky DESC, created DESC
            LIMIT :limit'
        );
        $stmt->execute(['limit' => $config['blog']['number_latest_posts']]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a blog post from the database
     *
     * @param string $slug the blog post’s slug
     * @return array<mixed>
     */
    public static function getBySlug($slug)
    {
        $pdo = static::getPDO();
        $stmt = $pdo->prepare(
            'SELECT 
            id,
            title,
            slug,
            body,
            sticky,
            meta_title,
            meta_description,
            author,
            created 
            FROM blog_posts 
            WHERE published = 1 AND slug = ?'
        );
        $stmt->execute([$slug]); 
        $blog_post = $stmt->fetch(PDO::FETCH_ASSOC);
        if ( !empty($blog_post) ) {
            $blog_post['words'] = str_word_count(strip_tags($blog_post['body']));
            $blog_post['minutes'] = round($blog_post['words'] / 250);
        }
        return $blog_post;
    }

    /**
     * Get all published blog posts from the database
     *
     * @return array<mixed>
     */
    public static function getAll()
    {
        $pdo = static::getPDO();
        $stmt = $pdo->query(
            'SELECT id, title, slug, body, created
            FROM blog_posts 
            WHERE published = 1
            ORDER by created DESC'
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } 
}
