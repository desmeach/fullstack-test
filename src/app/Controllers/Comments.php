<?php

namespace App\Controllers;

use App\Models\CommentModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use ReflectionException;

class Comments extends BaseController
{
    protected int $limit = 3;
    protected string $sortDefault = "id";
    protected string $orderDefault = "asc";
    public function __construct()
    {
        helper('form');
        helper('pagination');
        $this->commentsModel = model(CommentModel::class);
    }

    /**
     * Entry point: GET /
     * Represents comments list view
     */
    public function index()
    {
        $sort = !empty($this->request->getGet('sort'))
            ? $this->request->getGet('sort')
            : $this->sortDefault;
        $order = !empty($this->request->getGet('order'))
            ? $this->request->getGet('order')
            : $this->orderDefault;
        $data = [
            'comments' => $this->commentsModel->orderBy($sort, $order)->paginate($this->limit),
            'pager' => $this->commentsModel->pager,
        ];

        return view('Comments/index', $data);
    }

    /**
     * Entry point: GET /comments
     * Represents comments list view
     */
    public function getAll()
    {
        if (!$this->request->isAJAX()) {
            throw PageNotFoundException::forPageNotFound();
        }

        $data = [
            'comments' => $this->commentsModel->paginate($this->limit),
            'pager' => $this->commentsModel->pager,
        ];

        $output = view('Comments/comments_list', $data);
        echo json_encode($output);
    }

    /**
     * Entry point: POST /comments
     * New comment create
     */
    public function create()
    {
        if (!$this->request->isAJAX()) {
            throw PageNotFoundException::forPageNotFound();
        }

        $data = $this->request->getPost(['name', 'text', 'date']);

        $data['date'] = date("Y-m-d", strtotime($data['date']));
        $now = date("Y-m-d");

        if ($now < $data['date']) {
            echo json_encode([
                'status' => false,
                'errors' => ['Date can\'t be greater than current']
            ]);
            return;
        }

        if (!$this->validateData($data, [
            'name' => 'required|max_length[255]|valid_email',
            'text' => 'required',
            'date' => 'required|valid_date[Y-m-d]',
        ])) {
            $errors = [
                'name' => $this->validator->getError('name'),
                'text' => $this->validator->getError('text'),
                'date' => $this->validator->getError('date'),
            ];

            $output = [
                'status' => false,
                'errors' => $errors
            ];
            echo json_encode($output);
            return;
        }

        $post = $this->validator->getValidated();

        $this->commentsModel->insert([
            'text' => $post['text'],
            'name'  => $post['name'],
            'date'  => $post['date'],
        ]);

        if ($this->commentsModel->errors()) {
            echo json_encode([
                'status' => false,
                'errors' => $this->commentsModel->errors()
            ]);
            return;
        }

        echo json_encode(['status' => true]);
    }

    /**
     * Entry point: DELETE /comments/$id
     * Delete comment by id
     * @param $id
     */
    public function delete($id)
    {
        if (!$this->request->isAJAX()) {
            throw PageNotFoundException::forPageNotFound();
        }

        $comment = $this->commentsModel->find($id);
        if ($comment) {
            $this->commentsModel->delete($id);
            if ($this->commentsModel->errors()) {
                echo json_encode([
                    'status' => false,
                    'error' => 'Server error while deleting comment'
                ]);
                return;
            }
            echo json_encode([
                'status' => true,
                'error' => ''
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'error' => 'Comment not found'
            ]);
        }
    }
}