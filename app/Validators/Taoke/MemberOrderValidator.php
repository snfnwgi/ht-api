<?php

namespace App\Validators\Taoke;

use Prettus\Validator\LaravelValidator;
use Prettus\Validator\Contracts\ValidatorInterface;

/**
 * Class MemberOrderValidator
 * @package App\Validators\Taoke
 */
class MemberOrderValidator extends LaravelValidator
{
    /**
     * Validation Rules.
     *
     * @var array
     */
    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
            'ordernum'=>'required|numeric',
        ],
//        ValidatorInterface::RULE_UPDATE => [],
    ];
}
