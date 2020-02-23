<?php
declare(strict_types=1);

namespace Dcblogdev\Box\Resources;

use Dcblogdev\Box\Facades\Box;

class Folders extends Box
{
    public function get(int $id = 0): array
    {
        return Box::get('folders/'.$id);
    }

    public function items(int $id = 0): array
    {
        return Box::get('folders/'.$id.'/items');
    }

    public function store(string $name, int $parent = 0): array
    {
        return Box::post('folders', [
            'name' => $name,
            'parent' => [
                'id' => $parent
            ]
        ]);
    }

    public function update(int $id, string $name, int $parent = null): array
    {
        $data = [];
        $data['name'] = $name;

        if ($parent !== null) {
            $data['parent'] = $parent;
        }

        return Box::put('folders/'.$id, $data);
    }

    public function copy(int $id, int $parent = 0, string $name = null): array
    {
        $data = [];

        if ($name !== null) {
            $data['name'] = $name;
        }

        $data['parent']['id'] = $parent;

        return Box::post('folders/'.$id.'/copy', $data);
    }

    public function destroy(int $id): void
    {
        Box::delete('folders/'.$id.'?recursive=true');
    }

    public function collaborations(int $id = 0): array
    {
        return Box::get('folders/'.$id.'/collaborations');
    }
}
