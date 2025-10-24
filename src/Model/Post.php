<?php

namespace silverorange\DevTest\Model;

use DateTime;

class Post
{
    public string $id;
    public string $title;
    public string $body;
    public string $created_at;
    public string $modified_at;
    public string $author;

    /**
     * @param array<string> $data
     */
    public static function new(array $data): Post
    {
        $errors = [];
        foreach ($data as $k => $v) {
            try {
                self::validate($k, $v);
            } catch (ValidationException $e) {
                $errors[$k] = $e->getMessage();
            }
        }

        if (!empty($errors)) {
            throw new ValidationException(implode(PHP_EOL, $errors));
        }

        $post = new Post();
        $post->id = $data['id'];
        $post->title = $data['title'];
        $post->body = $data['body'];
        $post->created_at = $data['created_at'];
        $post->modified_at = $data['modified_at'];
        $post->author = $data['author'];

        return $post;
    }

    public static function validate(string $key, string $data): bool
    {
        $rules = [
            'id' => ['rule' => 'isValidUuid', 'message' => "{key} is not a valid UUID"],
            'title' => ['rule' => 'isValidString', 'message' => "{key} is not a valid string", 'limit' => 255],
            'body' => ['rule' => 'isValidString', 'message' => "{key} is not a valid string"],
            'created_at' => ['rule' => 'isValidDate', 'message' => "{key} is not a valid date"],
            'modified_at' => ['rule' => 'isValidDate', 'message' => "{key} is not a valid date"],
            'author' => ['rule' => 'isValidUuid', 'message' => "{key} is not a valid UUID"],
        ];

        if (empty($rules[$key])) {
            throw new ValidationException("{$key} is not a valid field");
        }

        if ($rules[$key]['rule'] === 'isValidString' && !empty($rules[$key]['limit'])) {
            return call_user_func([Post::class, $rules[$key]['rule']], $data, $rules[$key]['limit']);
        }
        return call_user_func([Post::class, $rules[$key]['rule']], $data);
    }

    public static function isValidUuid(string $uuid): bool
    {
        return (bool)(preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-5][0-9a-f]{3}-[089ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $uuid));
    }

    public static function isValidString(string $string, ?int $limit = 0): bool
    {
        return !($limit !== 0 && strlen($string) > $limit);
    }

    public static function isValidDate(string $date): bool
    {
        $format = 'Y-m-d\TH:i:sP';
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
}
