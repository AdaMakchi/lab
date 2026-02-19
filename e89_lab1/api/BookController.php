<?php
require_once __DIR__ . '/BookModel.php';

class BookController
{
    private $model;

    public function __construct()
    {
        $this->model = new BookModel();
        header('Content-Type: application/json; charset=utf-8');
    }

    public function handleRequest(string $method, $id = null)
    {
        switch ($method) {
            case 'GET':
                if ($id === null) {
                    $this->index();
                } else {
                    $this->show($id);
                }
                break;

            case 'POST':
                if ($id !== null) {
                    $this->methodNotAllowed();
                } else {
                    $this->store();
                }
                break;

            case 'PUT':
            case 'PATCH':
                if ($id === null) {
                    $this->methodNotAllowed();
                } else {
                    $this->update($id);
                }
                break;

            case 'DELETE':
                if ($id === null) {
                    $this->methodNotAllowed();
                } else {
                    $this->destroy($id);
                }
                break;

            default:
                $this->respond(['success' => false, 'message' => 'Method Not Allowed'], 405);
        }
    }

    private function index()
    {
        $books = $this->model->getAll();
        $this->respond(['success' => true, 'data' => $books, 'message' => 'Books retrieved successfully'], 200);
    }

    private function show(int $id)
    {
        $book = $this->model->getById($id);
        if (!$book) {
            $this->respond(['success' => false, 'message' => 'Book not found'], 404);
            return;
        }
        $this->respond(['success' => true, 'data' => $book, 'message' => 'Book retrieved successfully'], 200);
    }

    private function store()
    {
        $input = $this->getJsonInput();
        if ($input === null) {
            return; // error already sent
        }

        $errors = $this->validate($input, false);
        if (!empty($errors)) {
            $this->respond(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
            return;
        }

        $book = $this->model->create($input);
        $this->respond(['success' => true, 'data' => $book, 'message' => 'Book created successfully'], 201);
    }

    private function update(int $id)
    {
        $input = $this->getJsonInput();
        if ($input === null) {
            return;
        }

        $existing = $this->model->getById($id);
        if (!$existing) {
            $this->respond(['success' => false, 'message' => 'Book not found'], 404);
            return;
        }

        $errors = $this->validate($input, true);
        if (!empty($errors)) {
            $this->respond(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
            return;
        }

        $updated = $this->model->update($id, $input);
        $this->respond(['success' => true, 'data' => $updated, 'message' => 'Book updated successfully'], 200);
    }

    private function destroy(int $id)
    {
        $existing = $this->model->getById($id);
        if (!$existing) {
            $this->respond(['success' => false, 'message' => 'Book not found'], 404);
            return;
        }

        $this->model->delete($id);
        $this->respond(['success' => true, 'message' => 'Book deleted successfully'], 200);
    }

    private function getJsonInput()
    {
        $raw = file_get_contents('php://input');
        if (empty($raw)) {
            return [];
        }

        $data = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->respond(['success' => false, 'message' => 'Invalid JSON body'], 400);
            return null;
        }
        return $data;
    }

    private function validate(array $data, bool $isUpdate = false): array
    {
        $errors = [];

        // title
        if (!$isUpdate || array_key_exists('title', $data)) {
            if (!isset($data['title']) || trim($data['title']) === '') {
                $errors['title'] = 'Title is required';
            }
        }

        // author
        if (!$isUpdate || array_key_exists('author', $data)) {
            if (!isset($data['author']) || trim($data['author']) === '') {
                $errors['author'] = 'Author is required';
            }
        }

        // isbn (optional): 10-17 chars, digits and hyphens
        if (array_key_exists('isbn', $data) && $data['isbn'] !== null && $data['isbn'] !== '') {
            if (!preg_match('/^[0-9\-]{10,17}$/', $data['isbn'])) {
                $errors['isbn'] = 'ISBN must be 10-17 characters (digits and hyphens)';
            }
        }

        // year (optional)
        if (array_key_exists('year', $data) && $data['year'] !== null && $data['year'] !== '') {
            if (!is_numeric($data['year']) || (int)$data['year'] < 1000 || (int)$data['year'] > (int)date('Y')) {
                $errors['year'] = 'Year must be an integer between 1000 and ' . date('Y');
            }
        }

        return $errors;
    }

    private function respond($payload, int $status = 200)
    {
        http_response_code($status);
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function methodNotAllowed()
    {
        $this->respond(['success' => false, 'message' => 'Method Not Allowed'], 405);
    }
}
