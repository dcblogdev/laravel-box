<?php

namespace Dcblogdev\Box\Resources;

use Dcblogdev\Box\Facades\Box;

class Files extends Box
{
    public function file($id)
    {
        return Box::get('files/'.$id);
    }

    public function destroy($id)
    {
        return Box::delete('files/'.$id);
    }

    public function download($id, $path = '', $storeDownload = false)
    {
        $file = Box::get('files/'.$id);
        $content = Box::get('files/'.$file['id'].'/content', ['raw' => true]);

        file_put_contents($path.$file['name'], $content);

        $path = $path.$file['name'];

        if ($storeDownload == false) {
            return response()->download($path)->deleteFileAfterSend(true);
        }

        return [
            'file' => $file['name'],
            'path' => $path
        ];
    }

    public function upload($filepath, $name, $parent = 0)
    {
        $url = 'content';

        $params = [
            'attributes' => json_encode([
                'name' => $name,
                'parent' => ['id' => $parent]
            ]),
            'file' => new \CurlFile($filepath, mime_content_type($filepath), $name)
        ];

        return self::uploadRun($url, $params);
    }

    public function uploadRevision($file_id, $filepath, $name, $newname = null)
    {
        $url = $file_id.'/content';

        $params = [
            'file' => new \CurlFile($filepath, mime_content_type($filepath), $name),
            'name' => 'updated'
        ];

        if ($newname !== null) {
            $params['attributes'] = json_encode([
                'name' => $newname
            ]);
        }

        return self::uploadRun($url, $params);
    }

    private static function uploadRun($endpoint, $params)
    {
        $url = 'https://upload.box.com/api/2.0/files/'.$endpoint;
        $headers = ["Authorization: Bearer ".Box::getAccessToken()];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
}
