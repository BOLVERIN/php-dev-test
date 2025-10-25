<?php

declare(strict_types=1);

namespace silverorange\DevTest\Command;

use DirectoryIterator;
use InvalidArgumentException;
use silverorange\DevTest\Model\Post;

class ImportPostsCommand extends BaseCommand implements CommandInterface
{
    public function execute(): bool
    {
        /** @var \SplFileInfo $file */
        foreach ($this->getFileToImport('./././data') as $file) {
            try {
                $this->importPosts($file);
            } catch (\Throwable $e) {
                echo 'An error occurred during import of ' . $file->getFilename() . ': ' . $e->getMessage() . PHP_EOL;
            }
        }

        echo 'Import finished!' . PHP_EOL;

        return true;
    }

    private function importPosts(\SplFileInfo $file): void
    {
        $contents = $file->openFile('r')->fread($file->getSize());
        if (empty($contents)) {
            throw new InvalidArgumentException('Invalid post data');
        }
        $postData = json_decode($contents, true);
        if (empty($postData) || !is_array($postData)) {
            throw new InvalidArgumentException('Invalid post data');
        }

        // @phpstan-ignore argument.type (possible mixed data is expected to cause an exception)
        $newPost = Post::new($postData);
        $this->addNewPostToDB($newPost);
    }

    private function addNewPostToDB(Post $newPost): void
    {
        $sql = "INSERT INTO posts (id, title, body, created_at, modified_at, author) VALUES (:id, :title, :body, :created_at, :modified_at, :author)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $newPost->id,
            'title' => $newPost->title,
            'body' => $newPost->body,
            'created_at' => $newPost->created_at,
            'modified_at' => $newPost->modified_at,
            'author' => $newPost->author,
        ]);

        echo "New record added with ID: " . $newPost->id . PHP_EOL;
    }

    /** @return \ArrayIterator<int, \SplFileInfo> */
    private function getFileToImport(string $path): \Traversable
    {
        if (!is_dir($path)) {
            throw new InvalidArgumentException("Path '{$path}' is not a valid directory.");
        }

        $iterator = new DirectoryIterator($path);
        foreach ($iterator as $fileInfo) {
            if (
                !$fileInfo->isFile()
                || $fileInfo->getExtension() !== 'json'
                || !$fileInfo->isReadable()
            ) {
                continue;
            }

            yield $fileInfo;
        }
    }
}
