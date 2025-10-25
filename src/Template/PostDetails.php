<?php

namespace silverorange\DevTest\Template;

use silverorange\DevTest\Context;

class PostDetails extends Layout
{
    protected function renderPage(Context $context): string
    {
        return <<<HTML
            <h1>{$context->title}</h1>
            <p>By {$context->author}</p>
            {$context->content}
            HTML;
    }
}
