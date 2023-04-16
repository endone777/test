<?php

namespace App\Model;

use App\Database\DB;
use App\Interface\ModelInterface;

abstract class AbstractModel implements ModelInterface
{

    protected int $page = 1;
    protected int $perPage = 5;

    protected DB $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    public function create(array $data): bool
    {
        $fields = array_map(function ($string) {
            return '`'.quotemeta($string).'`';
        }, array_keys($data));
        $values = array_map(function ($string) {
            return "'".quotemeta($string)."'";
        }, array_values($data));
        $sql = "INSERT INTO $this->table (".implode(",", $fields).") VALUES (".implode(",", $values).")";
        return $this->db->execute($sql);

    }

    public function update(array $data , int $id): bool
    {
        unset($data['id']);
        $setList = [];
        foreach ($data as $name => $val){
            $setList[] = "`$name` = '". quotemeta($val) . "'";
        }
        $sql = "UPDATE `$this->table` SET". implode(",", $setList) ." WHERE id = {$id}";
        return $this->db->execute($sql);
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE from $this->table where id = $id";
        return $this->db->execute($sql);
    }

}