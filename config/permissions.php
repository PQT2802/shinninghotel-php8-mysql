<?php

/**
 * Permission map: action => allowed roles.
 * super_admin always has full access (checked in middleware).
 */
return [
    'users.manage' => ['super_admin'],
    'settings.manage' => ['super_admin', 'admin'],
    'bookings.manage' => ['super_admin', 'admin'],
    'content.manage' => ['super_admin', 'admin', 'editor'],
    'media.manage' => ['super_admin', 'admin', 'editor'],
];
