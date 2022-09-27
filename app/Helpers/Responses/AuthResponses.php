<?php

namespace App\Helpers\Responses;

use Illuminate\Http\Response;

class AuthResponses {

    public static function notAuthorized() {
        return response()->json([
            //TODO: translate msg str
            'error_id' => 'not_authorized',
            // TODO: translate
            // in theory should never be shown in the frontend.
            'message' => 'Não tens permissões para isto.'
        ], Response::HTTP_FORBIDDEN);
    }


    public static function incorrectCredentials() {
        return response()->json([
            //TODO: translate msg str
            'error_id' => 'incorrect_credentials',
            // TODO: translate
            'message' => 'Credenciais inválidas'
        ], Response::HTTP_FORBIDDEN);
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

    public static function unverifiedAccount() {
        return response()->json([
            //TODO: translate msg str
            'error_id' => 'unverified_account',
            // TODO: translate
            'message' => 'Ainda não verificou o seu email. Verifique a sua caixa de entrada e/ou caixa de spam no seu provedor de email.'
        ], Response::HTTP_FORBIDDEN);
    }

    public static function failedToCreateGuestAccount() {
        return response()->json([
            //TODO: translate msg str
            'error_id' => 'failed_to_create_guest_account',
            // TODO: translate
            'message' => 'Não foi possível criar uma conta convidado para o utilizador.'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
