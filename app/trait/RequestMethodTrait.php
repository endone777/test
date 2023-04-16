<?php

namespace App\Trait;

trait RequestMethodTrait
{

    public function req($data, $field): bool
    {
        return array_key_exists($field, $data);
    }

    public function min($string, $length = 3): bool
    {
        return strlen($string) >= (int) $length;
    }

    public function string($data, $field): bool
    {
        return is_string($data[$field]);
    }

    public function mime($base64, $fields): bool
    {
        $fields = explode(",", $fields);
        $image_parts = explode(";base64,", $base64);
        $type = explode("/", $image_parts[0])[1];
        if (in_array($type, $fields)) {
            return true;
        }
        return false;
    }

    public function size($base64, $maxMB): bool
    {
        if (mb_strlen($base64, '8bit') < 1024 * 1024 * (int) $maxMB) {
            return true;
        }
        return false;
    }

}