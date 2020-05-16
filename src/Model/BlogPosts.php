<?php

namespace App\Model;

use PDO;
use Anticus\Configure\Configure;
use JasonGrimes\Paginator;

/**
 * BlogPosts model
 */
class BlogPosts extends \Anticus\Model\Model
{  
    /**
     * Get a page of published blog posts from the database
     *
     * @param array<mixed> $params
     * @return array<mixed>
     */
    public static function getPage($params)
    {
        $current_page = $params['current_page'];
        $tag = $params['tag'];
        
        $pdo = static::getPDO();
        $config = Configure::read();

        $where = ' WHERE published = 1 ';
        if ( !empty($tag) ) {
            $where .= 'AND FIND_IN_SET(:tag,tags) != 0 ';
        } 

        $query =  'SELECT count(*) 
        FROM blog_posts' . $where;
        
        if ( !empty($tag) ) { 
            $stmt = $pdo->prepare($query);
            $stmt->execute(['tag' => $tag]);
            $total_items = $stmt->fetchColumn();
        } else {
            $total_items = $pdo->query($query)->fetchColumn();
        }

        $items_per_page = $config['blog']['posts_per_page'];
        $offset = ($current_page-1) * $items_per_page; 
        
        $totalPages = ceil($total_items / $items_per_page);
        
        $stmt = $pdo->prepare(
            'SELECT title, slug, summary, sticky, created 
            FROM blog_posts' . 
            $where .
            'ORDER BY sticky DESC, created DESC
            LIMIT :offset, :items_per_page'
        );

        $execute_array = ['offset' => $offset, 'items_per_page' => $items_per_page];
        if ( !empty($tag) ) { 
            $execute_array = $execute_array + ['tag' => $tag];
        }
        $stmt->execute($execute_array);
        $blog_posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        //paginate  
        if ( !empty($tag) ) {
            $url_pattern = '/blog/tag/' . $tag . '?page=(:num)';
        } else {
            $url_pattern = '/blog?page=(:num)';
        }
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
            tags,
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
        $blog_post['tags'] = explode(',', $blog_post['tags']);
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

    /**
     * Get all tags from published posts
     *
     * @return array<mixed>
     */
    public static function getAllTags()
    {
        $pdo = static::getPDO();
        $stmt = $pdo->query(
            'SELECT tags
            FROM blog_posts 
            WHERE published = 1'
        );
        $post_tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $tags = [];
        foreach ($post_tags as $tag_csv) {
            $tag_list = explode(',', $tag_csv['tags']);
            foreach ($tag_list as $single_tag) {
                // save tag as key in order to overwrite duplicates
                // saves having to use array_unique() or in_array()
                // https://stackoverflow.com/a/6083605
                $tags[$single_tag] = 1;
            }
        }
        ksort($tags);
        return $tags;
    }
}
