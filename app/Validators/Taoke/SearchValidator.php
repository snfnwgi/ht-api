<?php

namespace App\Validators\Taoke;

use Prettus\Validator\LaravelValidator;

/**
 * Class CouponValidator.
 */
class SearchValidator extends LaravelValidator
{
    /**
     * Validation Rules.
     *
     * @var array
     */
    protected $rules = [
        'type' => 'required|in:1,2,3',
    ];
    protected $messages = [
        'type.required' => 'type不能为空',
        'type.in' => 'type值非法',
    ];
}
