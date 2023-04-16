<?php

namespace App\Controller\Request;

use App\Trait\RequestMethodTrait;

class MagazineRequest
{
    use RequestMethodTrait;

    private array $rules = [
        'name' => 'req',
        'description' => '',
        'image' => 'mime:jpeg,jpg,png|size:2',
        'authors' => 'req',
        'date_release' => '',
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
        $json = preg_replace('/[[:cntrl:]]/', '', $data['data']);
        $data = json_decode($json, true);
        if ($data['magazine']) {
            foreach ($this->rules as $name => $rule) {
                if ($data['magazine'][$name]) {
                    $req = $rule !== '' ? explode("|", $rule) : [];
                    $req === [] ? $this->validated[$name] = $data['magazine'][$name] : null;
                    foreach ($req as $r) {
                        if (str_contains($r, ":")) {
                            $min = explode(":", $r);
                            $func = $min[0];
                            if (!$this->$func($data['magazine'][$name], $min[1])) {
                                $errors['errors'][] = "Поле $name заполнено не корректно. Мин кол-во символов: $min[1]";
                            }
                            $this->validated[$name] = $data['magazine'][$name];
                            continue;
                        }
                        if (!$this->$r($data['magazine'], $name)) {
                            $errors['errors'][] = "Поле $name заполнено не корректно";
                        }
                        $this->validated[$name] = $data['magazine'][$name];
                    }
                }
            }
            if (isset($data['magazine']['id'])) {
                $this->validated['id'] = (int) $data['magazine']['id'];
            }
            if (count($errors) === 0) {
                return true;
            }
        }
        var_dump($errors);
        exit();
        return $errors;
    }

}