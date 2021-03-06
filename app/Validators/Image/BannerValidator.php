<?php

namespace App\Validators\Image;

use Prettus\Validator\LaravelValidator;
use Prettus\Validator\Contracts\ValidatorInterface;

/**
 * Class BannerValidator.
 */
class BannerValidator extends LaravelValidator
{
    /**
     * Validation Rules.
     *
     * @var array
     */
    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
            'image' => 'required',
            'sort' => 'required|integer',
            'tag' => 'required',
            'status' => 'required|in:0,1',
        ],
        ValidatorInterface::RULE_UPDATE => [
            'image' => 'required',
            'sort' => 'required|integer',
            'tag' => 'required',
            'status' => 'required|in:0,1',
        ],
    ];
}
