<?php

namespace Dcblogdev\Box\Resources;

use Dcblogdev\Box\Facades\Box;

class Folders extends Box
{
    public function get($id = 0)
    {
        return Box::get('folders/'.$id);
    }

    public function items($id = 0)
    {
        return Box::get('folders/'.$id.'/items');
    }

    public function store($name, $parent = 0)
    {
        return Box::post('folders', [
            'name' => $name,
            'parent' => [
                'id' => $parent
            ]
        ]);
    }

    public function update($id, $data)
    {
        return Box::put('folders/'.$id, $data);
    }

    public function copy($id, $parent = 0, $name = null)
    {
        $data = [];

        if ($name !== null) {
            $data['name'] = $name;
        }

        $data['parent']['id'] = $parent;

        return Box::post('folders/'.$id.'/copy', $data);
    }

    public function destroy($id)
    {
        return Box::delete('folders/'.$id);
    }

    public function collaborations($id = 0)
    {
        return Box::get('folders/'.$id.'/collaborations');
    }
}
