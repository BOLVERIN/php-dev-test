<?php
declare(strict_types=1);

namespace silverorange\DevTest;

use silverorange\DevTest\Command\CommandInterface;
use silverorange\DevTest\Command\InvalidCommandException;

class Command extends App
{
    public function run(): bool
    {
        $command = $_SERVER['argv'];
        if (
            !is_array($command)
            || $command[0] !== 'bin/command'
            || empty($command[1])
            || !is_string($command[1])
            || !$this->commandNameIsValid($command[1])
        ) {
            throw new InvalidCommandException('Command is not valid');
        }

        return $this->executeCommand($command);
    }

    private function commandNameIsValid(string $name): bool
    {
        return \preg_match('/^[A-Za-z0-9]+$/', $name) === 1;
    }

    /**
     * @param array<mixed> $command
     */
    private function executeCommand(array $command): bool
    {
        if (empty($command[1]) || !is_string($command[1])) {
            throw new InvalidCommandException('Command is not valid');
        }
        $commandName = ucfirst($command[1]) . 'Command';
        if (!file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'Command' . DIRECTORY_SEPARATOR . $commandName . '.php')) {
            throw new InvalidCommandException('Command file do not exist');
        }
        $params = array_slice($command, 2);
        $className = 'silverorange\DevTest\Command\\' . $commandName;
        /** @var CommandInterface $className */
        // @phpstan-ignore varTag.nativeType (it should throw an error if something goes wrong)
        $instance = new $className($this->db, $params);

        return $instance->execute();
    }
}
