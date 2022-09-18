<?php

namespace App\Helpers\Actions;

class ShortlinkActions
{
    public const GENERATED_SHORTLINK_WITH_BC = 'GENERATED_SHORTLINK_WITH_BC';
    public const GENERATED_SHORTLINK_WITH_PRESEEDED_STRING = 'GENERATED_SHORTLINK_WITH_PRESEEDED_STRING';
    public const SENT_SHORTLINK_TO_EMAIL = 'SENT_SHORTLINK_TO_EMAIL';

    public const VISITED_AVAILABLE_SHORTLINK = 'VISITED_AVAILABLE_SHORTLINK';
    public const VISITED_UNEXISTING_AND_UNAVAILABLE_SHORTLINK = 'VISITED_UNEXISTING_AND_UNAVAILABLE_SHORTLINK';
    public const ATTEMPTED_TO_REGISTER_UNAVAILABLE_SHORTSTRING = 'ATTEMPTED_TO_REGISTER_UNAVAILABLE_SHORTSTRING';
    public const ATTEMPTED_TO_REGISTER_UNEXISTING_SHORTSTRING = 'ATTEMPTED_TO_REGISTER_UNEXISTING_SHORTSTRING';
    public const REGISTERED_CUSTOM_AVAILABLE_SHORTSTRING = 'REGISTERED_CUSTOM_AVAILABLE_SHORTSTRING';

    public const ATTEMPTED_TO_GENERATE_SHORTLINK_FOR_URL_THAT_IS_TOO_LONG = 'ATTEMPTED_TO_GENERATE_SHORTLINK_FOR_URL_THAT_IS_TOO_LONG';
    public const ATTEMPTED_TO_EDIT_SHORTLINK_URL_TO_ONE_THAT_IS_TOO_LONG = 'ATTEMPTED_TO_EDIT_SHORTLINK_URL_TO_ONE_THAT_IS_TOO_LONG';

    public const ATTEMPTED_TO_CREATE_SHORTLINK_FOR_SHORTLINK = 'ATTEMPTED_TO_CREATE_SHORTLINK_FOR_SHORTLINK';

    public const REACHED_MAX_GENERATE_ATTEMPTS_WITH_BC = 'REACHED_MAX_GENERATE_ATTEMPTS_WITH_BC';
    public const DID_NOT_FIND_AVAILABLE_PRESEEDED_SHORTSTRING = 'DID_NOT_FIND_AVAILABLE_PRESEEDED_SHORTSTRING';
    public const WILL_TRY_GENERATING_SHORTSTRING_WITH_BC = 'WILL_TRY_GENERATING_SHORTSTRING_WITH_BC';
    public const FOUND_NO_AVAILABLE_SHORTSTRING = 'FOUND_NO_AVAILABLE_SHORTSTRING';

    public const VISITED_ACTIVE_SHORTLINK = 'VISITED_ACTIVE_SHORTLINK';
    public const VISITED_DELETED_SHORTLINK = 'VISITED_DELETED_SHORTLINK';
    public const VISITED_SUSPENDED_SHORTLINK = 'VISITED_SUSPENDED_SHORTLINK';

    public const VIEWED_LINKS_LIST = 'VIEWED_LINKS_LIST';
    public const EDITED_SHORTLINK_DESTINATION_URL = 'EDITED_SHORTLINK_DESTINATION_URL';
    public const DELETED_SHORTLINK = 'DELETED_SHORTLINK';

    public const SAVED_SHORTLINKS_GENERATED_AS_GUEST_TO_ACCOUNT = 'SAVED_SHORTLINKS_GENERATED_AS_GUEST_TO_ACCOUNT';
    public const FAILED_TO_SAVE_SHORTLINKS_GENERATED_AS_GUEST_TO_ACCOUNT = 'FAILED_TO_SAVE_SHORTLINKS_GENERATED_AS_GUEST_TO_ACCOUNT';
    public const IMPORTED_SHORTLINKS_FROM_GUEST_ACCOUNT = 'IMPORTED_SHORTLINKS_FROM_GUEST_ACCOUNT';
    public const FAILED_TO_IMPORT_SHORTLINKS_FROM_GUEST_ACCOUNT = 'FAILED_TO_IMPORT_SHORTLINKS_FROM_GUEST_ACCOUNT';

}
