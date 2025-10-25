<?php

namespace silverorange\DevTest\Controller;

use Michelf\Markdown;
use PDO;
use silverorange\DevTest\Context;
use silverorange\DevTest\Model\Post;
use silverorange\DevTest\Template;
use silverorange\DevTest\Model;

class PostDetails extends Controller
{
    private ?Model\Post $post = null;

    public function getContext(): Context
    {
        $context = new Context();

        if ($this->post === null) {
            $context->title = 'Not Found';
            $context->content = "A post with id {$this->params[0]} was not found.";
        } else {
            $context->title = $this->post->title;
            $context->content = Markdown::defaultTransform($this->post->body);
            $context->author = $this->post->author;
        }

        return $context;
    }

    public function getTemplate(): Template\Template
    {
        if ($this->post === null) {
            return new Template\NotFound();
        }

        return new Template\PostDetails();
    }

    public function getStatus(): string
    {
        if ($this->post === null) {
            return $this->getProtocol() . ' 404 Not Found';
        }

        return $this->getProtocol() . ' 200 OK';
    }

    protected function loadData(): void
    {
        if (empty($this->params[0])) {
            $this->post = null;

            return;
        }
        $stmt = $this->db->prepare("SELECT posts.*, authors.full_name AS author FROM posts LEFT JOIN authors ON posts.author = authors.id WHERE posts.id = :id LIMIT 1");
        $stmt->execute(['id' => $this->params[0]]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        if (empty($post) || !is_array($post)) {
            $this->post = null;

            return;
        }

        // @phpstan-ignore-next-line argument.type
        $this->post = Post::createFromArray($post);
    }
}
