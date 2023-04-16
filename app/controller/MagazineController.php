<?php

namespace App\Controller;

use App\Controller\Request\MagazineRequest;
use App\Model\Magazine;

class MagazineController
{

    protected Magazine $magazine;

    public function __construct()
    {
        $this->magazine = new Magazine();
    }

    public function getList(): array
    {
        $data = $this->magazine->listMagazines();
        return [
            'count' => count($data),
            'data' => $data,
        ];
    }

    public function create(): array
    {
        $validator = new MagazineRequest();
        $validate = $validator->validate($_REQUEST);
        if ($validate === true) {
            $data = $this->magazine->create($validator->getValidated());
            return [
                'error' => false,
                'message' => 'Magazine created',
            ];
        } else {
            return $validate;
        }
    }

    public function update(): array
    {
        $validator = new MagazineRequest();
        $validate = $validator->validate($_REQUEST);
        if ($validate === true) {
            $id = $validator->getValidated()['id'];
            if ($id) {
                $data = $this->magazine->update($validator->getValidated(), $id);
                return [
                    'error' => false,
                    'message' => 'Magazine updated',
                ];
            }
            return [
                'error' => true,
                'message' => 'Magazine not updated',
            ];
        } else {
            return $validate;
        }
    }

    public function delete(): array
    {
        try {
            $id = (int) json_decode($_REQUEST['data'], true)['magazine']['id'];
            $data = $this->magazine->delete($id);
            if ($data) {
                return [
                    'error' => false,
                    'message' => 'Magazine deleted',
                ];
            }
        } catch (\Exception $e) {
            return [
                'error' => false,
                'message' => 'Magazine not deleted',
                'code' => $e->getCode(),
            ];
        }
    }


}