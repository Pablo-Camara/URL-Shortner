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
            'message' => 'Ocorreu um erro. Por favor actualize a página e tente novamente.'
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

    public static function registerFailed() {
        return response()->json([
            //TODO: translate msg str
            'error_id' => 'register_failed',
            // TODO: translate
            // in theory should never be shown in the frontend.
            'message' => 'Não foi possível criar a sua conta de utilizador.'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
