<?php

namespace App\Trait;

trait ModelTrait
{
    protected function limits(): array
    {
        $page = isset($_REQUEST['page']) && is_numeric($_REQUEST['page']) ? $_REQUEST['page'] : $this->page;
        $perPage = isset($_REQUEST['perPage']) && is_numeric($_REQUEST['perPage']) ? $_REQUEST['perPage'] : $this->perPage;
        return [
            'page' => $this->page * $page,
            'perPage' => $perPage,
        ];
    }

    protected function bindings(array $limits): array
    {
        return [
            'limit' => (int) $limits['perPage'],
            'offset' => (int) (($limits['perPage'] * $limits['page']) - $limits['perPage']),
        ];
    }

}