<?php
/**
 * STATES | Constants
 *
 * @version v0.0.1 (Dec. 21, 2016)
 * @copyright Copyright (c) 2016, Brevada
 */

/**
 * State Keys
 *
 * These constant keys standardize access of the state dictionary.
 *
 * @category Constants
 */
class STATES
{
    /** @var string References the currently loaded view. */
    const VIEW = 'VIEW';

    /** @var string References the currently loaded route. */
    const CURRENT_ROUTE = 'CURRENT_ROUTE';

    /** @var string References the currently logged in user. */
    const AUTH_USER = 'AUTH_USER';

    /** @var string References the currently logged in user's account id. */
    const AUTH_USER_ID = 'AUTH_USER_ID';

    /** @var string References the URL the active user attempted to access w/o auth. */
    const LOGIN_DEST = 'LOGIN_DEST';

    /** @var string References feedback bundle metadata. */
    const FB_BUNDLE = 'FEEDBACK_BUNDLE';

    /** @var string References the currently authenticated device's UUID. */
    const DEVICE_UUID = 'DEVICE_UUID';
}
