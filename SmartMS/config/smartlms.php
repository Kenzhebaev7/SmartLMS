<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default grade for course sections (student)
    |--------------------------------------------------------------------------
    | Если у ученика в профиле не указан класс, подбор разделов идёт для этого класса.
    | Иначе без фильтра в списке оказываются разделы 9, 10 и 11 классов сразу (в т.ч. три «Проектная деятельность»).
    */
    'default_student_grade' => (int) env('DEFAULT_STUDENT_GRADE', 9),

    /*
    |--------------------------------------------------------------------------
    | Placement test threshold
    |--------------------------------------------------------------------------
    | Score >= this percent → Advanced level; otherwise Beginner.
    */
    'placement_threshold_percent' => (int) env('PLACEMENT_THRESHOLD_PERCENT', 50),

    /*
    |--------------------------------------------------------------------------
    | Section IDs that show C++ / Python online IDE (compiler) on lessons
    |--------------------------------------------------------------------------
    | If the section ID is in one of these arrays, the corresponding IDE is
    | shown regardless of section title. Empty = use only title detection.
    */
    'ide_cpp_section_ids' => array_map('intval', array_filter(explode(',', env('IDE_CPP_SECTION_IDS', '')))),
    'ide_python_section_ids' => array_map('intval', array_filter(explode(',', env('IDE_PYTHON_SECTION_IDS', '')))),

];
