<?php

namespace App\Trait;

use Illuminate\Http\Exceptions\HttpResponseException;

trait ResponseHttp {

  public function retornoExceptionErroRequest($status, $message, $statusCode, $data): HttpResponseException {
     throw new HttpResponseException(response()->json([
            'success'    => $status,
            'message'    => $message,
            'data'       => $data
        ], $statusCode));
  }
}
