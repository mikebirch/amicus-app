<?php
use Lib16\RSS\Channel;
use Lib16\RSS\RssMarkup;

header('Content-Type: text/xml');

$protocol = 'https://';

if ( isset($config['environment']) && $config['environment'] == 'dev' ) {
    $protocol = 'http://'; 
}

$channel = Channel::create(
    'Michael Birch',
    'Michael Birch writes about technology and design in his work and side projects.',
    $protocol . $_SERVER['HTTP_HOST'] . '/blog'
);

$search = ['src="/img/', 'href="/'];
$replace = [
    'src="' . $protocol . $_SERVER['HTTP_HOST'] . '/' . 'img' . '/', 
    'href="' . $protocol . $_SERVER['HTTP_HOST'] . '/'
];

if ( isset($data['blog_posts']) ) {
    foreach ($data['blog_posts'] as $post) {
        $body = str_replace($search, $replace, $post['body']);
        $channel
            ->item(
                $post['title'],
                '<![CDATA[ ' . $body . ' ]]>',
                $protocol . $_SERVER['HTTP_HOST'] . '/' . 'blog' . '/' . $post['slug']
            )
            ->guid('regbirch.com-post-' . $post['id'], false)
            ->pubDate(new DateTime($post['created']));
    }
}

echo $channel;
