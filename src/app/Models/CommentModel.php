<?php

namespace App\Models;

use CodeIgniter\Model;

class CommentModel extends Model
{
    protected $table = 'comments';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType = 'array';
    protected $allowedFields = ['name', 'text', 'date'];
    protected $validationRules = [
        'text' => 'required',
        'name' => 'required|max_length[255]|valid_email',
        'date' => 'required|valid_date[Y-m-d]',
    ];
}