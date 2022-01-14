<?php

namespace App\Traits;

trait ResponseTrait
{
    public function gene_response($status, $message = '', $data = [], $links = null)
    {
        $response = [
            'status' => $status,
            'message' => $message,
        ];
        if (!empty($data)) {
            if ($status === true) {
                $response['data'] = $data;
            } else {
                $response['errors'] = $data;
            }
        }
        if ($links) {
            $response['links'] = $links;
        }
        $statusCode = ($status === true) ? 200 : 400;
        return response($response, $statusCode);
    }

    public function sortLinks($data)
    {
        $links = json_decode($data->toJson());
        if(!empty($links->data)){
            unset($links->data);
            return $links;
        }
        return null;
    }
}
