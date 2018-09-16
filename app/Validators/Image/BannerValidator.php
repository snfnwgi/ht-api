<?php

namespace App\Validators\Image;

use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\LaravelValidator;

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
            'image' => 'required|url',
            'sort' => 'required|integer',
            'tag' => 'required',
            'status' => 'required|in:0,1',
        ],
        ValidatorInterface::RULE_UPDATE => [
            'image' => 'required|url',
            'sort' => 'required|integer',
            'tag' => 'required',
            'status' => 'required|in:0,1',
        ],
    ];
}
