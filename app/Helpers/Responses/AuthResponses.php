<?php

namespace App\Helpers\Responses;

use Illuminate\Http\Response;

class AuthResponses {


    public static function notAuthenticated() {
        return response()->json([
            //TODO: translate msg str
            'error_id' => 'not_authenticated',
            // TODO: translate
            // in theory should never be shown in the frontend.
            'message' => 'Must authenticate first!'
        ], Response::HTTP_UNAUTHORIZED);
    }


    public static function incorrectCredentials() {
        return response()->json([
            //TODO: translate msg str
            'error_id' => 'incorrect_credentials',
            // TODO: translate
            'message' => 'Credenciais inválidas'
        ], Response::HTTP_UNAUTHORIZED);
    }
}
