<?php

namespace silverorange\DevTest\Template;

use silverorange\DevTest\Context;
use silverorange\DevTest\Model\Post;

class PostIndex extends Layout
{
    protected function renderPage(Context $context): string
    {
        $data = '';
        /** @var Post $post */
        foreach ($context->list as $post) {
            $data .= <<<HTML
            <div class="post-preview">
                <a class="post-preview__link" href="/posts/{$post->id}">{$post->title}</a>
                <span class="post-preview__author"> by {$post->author}</span>
            </div>
            HTML;

        }

        return $data;
    }
}
