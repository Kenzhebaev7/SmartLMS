<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Placement test threshold
    |--------------------------------------------------------------------------
    | Score >= this percent → Advanced level; otherwise Beginner.
    */
    'placement_threshold_percent' => (int) env('PLACEMENT_THRESHOLD_PERCENT', 60),

];
