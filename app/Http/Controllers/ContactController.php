<?php

namespace App\Http\Controllers;

use App\Helpers\Actions\ContactControllerActions;
use App\Mail\NewContactMessage;
use App\Models\Action;
use App\Models\ContactMessage;
use App\Models\UserAction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{

    /**
     * Send contact message
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendMessage(Request $request) {
        $user = $request->user();

        $validations = [
            'name' => "required|max:255|regex:/^[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð ,.'-]+$/",
            'email' => 'required|email',
            'phone' => 'required|max:50',
            'subject' => 'required|max:255',
            'message' => 'required'
        ];

        $enableCaptchaSitekey = config('captcha.enable');
        if ($enableCaptchaSitekey) {
            $validations['g-recaptcha-response'] = 'required|captcha';
        }

        Validator::make(
            $request->all(),
            $validations,
            [
                'name.regex' => 'O campo Nome contém caracteres inválidos / não permitidos.',
                'name.max' => 'O campo Nome não pode conter mais do que 255 caracteres.',
                'subject.max' => 'O campo do Assunto não pode conter mais do que 255 caracteres.',
                'phone.max' => 'O campo do Telefone não pode conter mais do que 50 caracteres.',
            ],
            [
                'name' => 'nome',
            ]
        )->validate();

        $ip = request()->ip();

        $totalContactMessagesSentToday = UserAction::where('action_id', '=', Action::where('name', '=', ContactControllerActions::SENT_CONTACT_MESSAGE)->first()->id)
            ->where('ip', '=', $ip)
            ->where('created_at_day', '=', Carbon::now()->toDateString())
            ->count();

        if ($totalContactMessagesSentToday >= 5) {
            return new Response(
                [
                    'success' => 0,
                    'message' => 'Atingiste o máximo de mensagens de contacto que podes enviar por dia.'
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        try {

            $contactMessage = new ContactMessage();
            $contactMessage->user_id = $user->id;
            $contactMessage->name = $request->input('name');
            $contactMessage->email = $request->input('email');
            $contactMessage->phone = $request->input('phone');
            $contactMessage->subject = $request->input('subject');
            $contactMessage->message = $request->input('message');
            $contactMessage->save();

        } catch (\Throwable $th) {
            UserAction::logAction($user->id, ContactControllerActions::FAILED_TO_STORE_CONTACT_MESSAGE);
        }

        $destinationEmails = explode(',', config('app.contact_email'));

        try {
            Mail::to(
                $destinationEmails
            )->queue(new NewContactMessage(
                $request->input('name'),
                $request->input('email'),
                $request->input('phone'),
                $request->input('subject'),
                $request->input('message')
            ));
            $failed = false;
        } catch (\Throwable $th) {
            $failed = true;
            UserAction::logAction($user->id, ContactControllerActions::FAILED_TO_SEND_CONTACT_MESSAGE_EMAIL);
        }

        if ($failed) {
            return new Response(
                [
                    'success' => 0,
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        UserAction::logAction($user->id, ContactControllerActions::SENT_CONTACT_MESSAGE);

        return new Response(
            [
                'success' => 1,
            ],
            Response::HTTP_CREATED
        );
    }

}
