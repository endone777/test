<?php

namespace App\Controller\Request;

use App\Trait\RequestMethodTrait;

class AuthorRequest
{
    use RequestMethodTrait;

    private array $rules = [
        'name' => 'req|string',
        'middle_name' => '',
        'last_name' => 'req|min:3|string',
    ];

    public array $validated = [];

    public function validate(mixed $data): bool|array|null
    {
        return match (true) {
            is_array($data) => $this->isArray($data),
            default => false,
        };
    }

    public function getValidated(): array
    {
        return $this->validated;
    }

    private function isArray(array $data): bool|array
    {
        $errors = [];
        $data = json_decode($_REQUEST['data'], true);
        if ($data['user']) {
            foreach ($this->rules as $name => $rule) {
                if ($data['user'][$name]) {
                    $req = $rule !== '' ? explode("|", $rule) : [];
                    $req === [] ? $this->validated[$name] = $data['user'][$name] : null;
                    foreach ($req as $r) {
                        if (str_contains($r, ":")) {
                            $min = explode(":", $r);
                            $func = $min[0];
                            if (!$this->$func($data['user'][$name] , $min[1])) {
                                $errors['errors'][] = "Поле $name заполнено не корректно. Мин кол-во символов: $min[1]";
                            }
                            $this->validated[$name] = $data['user'][$name];
                            continue;
                        }
                        if (!$this->$r($data['user'], $name)) {
                            $errors['errors'][] = "Поле $name заполнено не корректно";
                        }
                        $this->validated[$name] = $data['user'][$name];
                    }
                }
            }
            if(isset($data['user']['id'])){
                $this->validated['id'] = (int) $data['user']['id'];
            }
            if (count($errors) === 0) {
                return true;
            }
        }

        return $errors;
    }

}