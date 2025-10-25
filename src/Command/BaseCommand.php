<?php

declare(strict_types=1);

namespace silverorange\DevTest\Command;

class BaseCommand implements CommandInterface
{
    /**
     * @param array<mixed> $params
     */
    public function __construct(
        protected \PDO $db,
        protected array $params = []
    ) {}

    public function execute(): bool
    {
        return true;
    }
}
