<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Ticket age thresholds (in minutes)
    |--------------------------------------------------------------------------
    | These thresholds define the "age" semaphore color based on elapsed minutes.
    |
    | green  = 0..green_max
    | amber  = green_max+1..amber_max
    | red    = amber_max+1..∞
    */

    'green_max' => env('TICKET_AGE_GREEN_MAX', 30),
    'amber_max' => env('TICKET_AGE_AMBER_MAX', 60),
    'resolved_green_max' => env('TICKET_RESOLVED_GREEN_MAX', 240), // 4h
    'resolved_amber_max' => env('TICKET_RESOLVED_AMBER_MAX', 480), // 8h

];
