<?php

namespace DaveismynameLaravel\Box\Api;

trait Folders {

    public function folders($id = 0)
    {
        return self::get('folders/'.$id);
    }

    public function folderItems($id = 0)
    {
        return self::get('folders/'.$id.'/items');
    }

    public function folderAdd($name, $parent = 0)
    {
        return self::post('folders', [
            'name' => $name,
            'parent' => [
                'id' => $parent
            ]
        ]);
    }

    public function folderUpdate($id, $data)
    {
        return self::put('folders/'.$id, $data);
    }

    public function folderCopy($id, $parent = 0, $name = null)
    {
        $data = [];

        if ($name != null) {
            $data['name'] = $name;
        }

        $data['parent']['id'] = $parent;

        return self::post('folders/'.$id.'/copy', $data);
    }

    public function folderDelete($id)
    {
        return self::delete('folders/'.$id);
    }

    public function folderCollaborations($id = 0)
    {
        return self::get('folders/'.$id.'/collaborations');
    }
}
