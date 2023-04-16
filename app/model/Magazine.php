<?php

namespace App\Model;

use App\Controller\Request\MagazineRequest;
use App\Trait\ModelTrait;

class Magazine extends AbstractModel
{
    use ModelTrait;

    protected string $table = 'journal';

    public function listMagazines(): mixed
    {
        $limits = $this->limits();
        $bindings = $this->bindings($limits);
        $sql = "SELECT * from $this->table limit :limit offset :offset";

        $magazines = $this->db->fetch($sql, $bindings);
        $magazinesIds = array_map(function ($item) {
            return $item['id'];
        }, $magazines);
        $sqlAuthors = $this->db->fetch("SELECT DISTINCT(author_id) , `book_id` as id FROM `author_books` WHERE book_id in (".implode(',',
                array_values($magazinesIds)).")");
        foreach ($sqlAuthors as $author) {
            $bookId[$author['id']][] = $author['author_id'];
        }
        $booksWithAuthors = [];
        foreach ($magazines as $k => $magazine) {
            $booksWithAuthors[$k] = $magazine;
            $booksWithAuthors[$k]['authors_id'] = $bookId[$magazine['id']];
        }
        return $booksWithAuthors;
    }

    public function create(array $data): bool
    {
        $authors = $data['authors'];
        $imageName = $this->base64ToImage($data['image']);
        if (is_bool($imageName)) {
            return false;
        }
        unset($data['authors']);
        unset($data['image']);
        $data['image'] = $imageName;

        $fields = array_map(function ($string) {
            return '`'.quotemeta($string).'`';
        }, array_keys($data));
        $values = array_map(function ($string) {
            return "'".quotemeta($string)."'";
        }, array_values($data));

        $sql = "INSERT INTO $this->table (".implode(",", $fields).") VALUES (".implode(",", $values).")";

        $authorsInsert = $this->insertBookAuthor($authors);
        $magazine = $this->db->execute($sql);

        return $magazine && $authorsInsert;
    }

    public function update(array $data, int $id): bool
    {
        $authors = $data['authors'];
        unset($data['id']);
        unset($data['authors']);
        $setList = [];
        if (isset($data['image'])) {
            $image = $data['image'];
            unset($data['image']);
            $data['image'] = $this->base64ToImage($image);
        }
        foreach ($data as $name => $val) {
            $setList[] = "`$name` = '".quotemeta($val)."'";
        }
        $sql = "UPDATE `$this->table` SET".implode(",", $setList)." WHERE id = {$id}";

        $this->db->execute("DELETE from `author_books` where 'book_id' = $id ");

        $authorsInsert = $this->insertBookAuthor($authors, $id);

        return $this->db->execute($sql) && $authorsInsert;
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE from $this->table where id = $id";
        return $this->db->execute($sql);
    }

    private function insertBookAuthor($authorIds, $bookId = 0)
    {
        $sql = "SELECT id from `journal` ORDER BY `id` DESC LIMIT 1";
        if ($bookId !== 0) {
            $bookId = $this->db->fetch($sql)[0]['id'];
        }
        $values = [];
        foreach (explode(",", $authorIds) as $id) {
            $values[] = "('$id' , $bookId )";
        }
        $sql = "INSERT INTO `author_books` (`author_id` , `book_id`) VALUES ".implode(",", $values);
        return $this->db->execute($sql);
    }

    private function base64ToImage($base64): string
    {
        $data = explode(',', $base64);
        $image_parts = explode(";base64,", $base64);
        $type = explode("/", $image_parts[0])[1];
        $dir = __DIR__."\..\storage\images\\";
        $name = time().".$type";
        $file = $dir.$name;
        file_put_contents($file, base64_decode($data[1]));
        return $name;
    }

}