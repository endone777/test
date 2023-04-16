<?php

namespace App\Controller;

use App\Controller\Request\AuthorRequest;
use App\Model\Author;

class AuthorController
{

    protected Author $author;

    public function __construct()
    {
        $this->author = new Author();
    }

    public function getList(): array
    {
        $data = $this->author->listAuthors();

        return [
            'count' => count($data),
            'data' => $data,
        ];
    }

    public function create(): array
    {
        $validator = new AuthorRequest();
        $validate = $validator->validate($_REQUEST);
        if ($validate === true) {
            $data = $this->author->create($validator->getValidated());
            return [
                'error' => false,
                'message' => 'User created',
            ];
        } else {
            return $validate;
        }
    }

    public function update(): array
    {
        $validator = new AuthorRequest();
        $validate = $validator->validate($_REQUEST);
        if ($validate === true) {
            $id = $validator->getValidated()['id'];
            if ($id) {
                $data = $this->author->update($validator->getValidated(), $id);
                return [
                    'error' => false,
                    'message' => 'User updated',
                ];
            }
            return [
                'error' => true,
                'message' => 'User not updated',
            ];
        } else {
            return $validate;
        }
    }

    public function delete(): array
    {
        try {
            $id = (int) json_decode($_REQUEST['data'], true)['user']['id'];
            $data = $this->author->delete($id);
            if ($data) {
                return [
                    'error' => false,
                    'message' => 'User deleted',
                ];
            }
        } catch (\Exception $e) {
            return [
                'error' => false,
                'message' => 'User not deleted',
                'code' => $e->getCode(),
            ];
        }
    }

}