<?php

use App\Helpers\ModuleMetaData;
use App\Helpers\SanitizeInput;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;


/* all helper function will be here */

/**
 * @param $option_name
 * @param $default
 * @return mixed|null
 */
