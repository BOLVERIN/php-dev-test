<?php

namespace silverorange\DevTest\Controller;

use PDO;
use silverorange\DevTest\Context;
use silverorange\DevTest\Model\Post;
use silverorange\DevTest\Template;

class PostIndex extends Controller
{
    /**
     * @var array<Post>
     */
    private array $posts = [];

    public function getContext(): Context
    {
        $context = new Context();
        $context->title = 'Posts';
        $context->content = strval(count($this->posts));
        $context->list = $this->posts;

        return $context;
    }

    public function getTemplate(): Template\Template
    {
        return new Template\PostIndex();
    }

    protected function loadData(): void
    {
        $stmt = $this->db->prepare("SELECT posts.*, authors.full_name AS author FROM posts LEFT JOIN authors ON posts.author = authors.id ORDER BY posts.created_at DESC");
        $stmt->execute();
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($posts)) {
            return;
        }

        foreach ($posts as $post) {
            // @phpstan-ignore-next-line argument.type
            $this->posts[] = Post::createFromArray($post);
        }
    }
}
