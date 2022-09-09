<?php

namespace App\Helpers\Actions;

class AuthActions
{
    public const AUTHENTICATED_AS_GUEST = 'AUTHENTICATED_AS_GUEST';
    public const AUTHENTICATED_AS_USER = 'AUTHENTICATED_AS_USER';

    public const ATTEMPTED_TO_REGISTER = 'ATTEMPTED_TO_REGISTER';
    public const ATTEMPTED_TO_REGISTER_WHILE_LOGGED_IN = 'ATTEMPTED_TO_REGISTER_WHILE_LOGGED_IN';
    public const REGISTERED = 'REGISTERED';

    public const REQUESTED_RESENDING_CONFIRMATION_EMAIL = 'REQUESTED_RESENDING_CONFIRMATION_EMAIL' ;
    public const REQUESTED_RESENDING_CONFIRMATION_EMAIL_FOR_UNEXISTING_EMAIL = 'REQUESTED_RESENDING_CONFIRMATION_EMAIL_FOR_UNEXISTING_EMAIL' ;
    public const CONFIRMED_EMAIL = 'CONFIRMED_EMAIL';

    public const REQUESTED_PASSWORD_RECOVERY_EMAIL = 'REQUESTED_PASSWORD_RECOVERY_EMAIL';
    public const REQUESTED_PASSWORD_RECOVERY_EMAIL_FOR_UNEXISTING_EMAIL = 'REQUESTED_PASSWORD_RECOVERY_EMAIL_FOR_UNEXISTING_EMAIL';
    public const CONFIRMED_EMAIL_THROUGH_PASSWORD_RECOVERY = 'CONFIRMED_EMAIL_THROUGH_PASSWORD_RECOVERY';
    public const CHANGED_PASSWORD = 'CHANGED_PASSWORD';

    public const ATTEMPTED_TO_LOGIN = 'ATTEMPTED_TO_LOGIN';
    public const ATTEMPTED_TO_LOGIN_WITH_INVALID_CREDENTIALS = 'ATTEMPTED_TO_LOGIN_WITH_INVALID_CREDENTIALS';
    public const ATTEMPTED_TO_LOGIN_WITH_UNVERIFIED_EMAIL = 'ATTEMPTED_TO_LOGIN_WITH_UNVERIFIED_EMAIL';
    public const ATTEMPTED_TO_LOGIN_WHILE_LOGGED_IN = 'ATTEMPTED_TO_LOGIN_WHILE_LOGGED_IN';
    public const LOGGED_IN = 'LOGGED_IN';
    public const LOGGED_OUT = 'LOGGED_OUT';

    public const SAVED_SHORTLINKS_GENERATED_AS_GUEST_TO_ACCOUNT = 'SAVED_SHORTLINKS_GENERATED_AS_GUEST_TO_ACCOUNT';
    public const IMPORTED_SHORTLINKS_FROM_GUEST_ACCOUNT = 'IMPORTED_SHORTLINKS_FROM_GUEST_ACCOUNT';


}
