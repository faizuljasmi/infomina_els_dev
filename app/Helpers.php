<?php

namespace app;

use Cache;

class Helpers
{
    /**
     * Fetch Cached settings from database
     *
     * @return string
     */
    public static function rules($emp_group_id, $leave_type_id, $rule_desc)
    {
        return Cache::get('rules')->where('emp_group_id', $emp_group_id)->where('leave_type_id', $leave_type_id)->where('rule_desc', $rule_desc)->first()->rule_val;
    }
}
