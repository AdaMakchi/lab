<?php

class BookModel
{
    private $file;

    public function __construct()
    {
        $this->file = __DIR__ . '/../data/books.json';

        if (!file_exists($this->file)) {
            if (!is_dir(dirname($this->file))) {
                mkdir(dirname($this->file), 0777, true);
            }
            file_put_contents($this->file, json_encode([]));
        }
    }

    private function readData()
    {
        $json = file_get_contents($this->file);
        $data = json_decode($json, true);
        return is_array($data) ? $data : [];
    }

    private function writeData(array $data)
    {
        // keep sequential numeric keys
        file_put_contents($this->file, json_encode(array_values($data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
    }

    public function getAll(): array
    {
        return $this->readData();
    }

    public function getById(int $id): ?array
    {
        $books = $this->readData();
        foreach ($books as $book) {
            if ((int)$book['id'] === $id) {
                return $book;
            }
        }
        return null;
    }

    public function create(array $data): array
    {
        $books = $this->readData();
        $ids = array_column($books, 'id');
        $newId = empty($ids) ? 1 : max($ids) + 1;

        $book = [
            'id' => $newId,
            'title' => $data['title'],
            'author' => $data['author'],
        ];

        if (isset($data['isbn']) && $data['isbn'] !== '') {
            $book['isbn'] = $data['isbn'];
        }
        if (isset($data['year']) && $data['year'] !== '') {
            $book['year'] = (int)$data['year'];
        }

        $books[] = $book;
        $this->writeData($books);
        return $book;
    }

    public function update(int $id, array $data): ?array
    {
        $books = $this->readData();
        foreach ($books as $i => $book) {
            if ((int)$book['id'] === $id) {
                $updated = $book;
                if (array_key_exists('title', $data)) {
                    $updated['title'] = $data['title'];
                }
                if (array_key_exists('author', $data)) {
                    $updated['author'] = $data['author'];
                }
                if (array_key_exists('isbn', $data)) {
                    $updated['isbn'] = $data['isbn'];
                }
                if (array_key_exists('year', $data)) {
                    $updated['year'] = $data['year'] !== null && $data['year'] !== '' ? (int)$data['year'] : null;
                }
                $books[$i] = $updated;
                $this->writeData($books);
                return $updated;
            }
        }
        return null;
    }

    public function delete(int $id): bool
    {
        $books = $this->readData();
        foreach ($books as $i => $book) {
            if ((int)$book['id'] === $id) {
                array_splice($books, $i, 1);
                $this->writeData($books);
                return true;
            }
        }
        return false;
    }
}
