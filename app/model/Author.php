<?php

namespace App\Model;

use App\Trait\ModelTrait;

class Author extends AbstractModel {
    use ModelTrait;
    protected string $table = 'author';

    public function listAuthors(): mixed
    {
        $limits = $this->limits();
        $bindings = $this->bindings($limits);
        $sql = "SELECT * from $this->table limit :limit offset :offset";

        return $this->db->fetch($sql, $bindings);
    }

}