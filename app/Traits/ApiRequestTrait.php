<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait ApiRequestTrait
{
    protected function validateRequest(Request $request, array $rules, array $messages = [])
    {
        return Validator::make($request->all(), $rules, $messages);
    }

    protected function getValidatedData(Request $request, array $rules)
    {
        $validator = $this->validateRequest($request, $rules);
        
        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }
        
        return $validator->validated();
    }

    protected function hasFields(Request $request, array $fields)
    {
        foreach ($fields as $field) {
            if (!$request->has($field)) {
                return false;
            }
        }
        return true;
    }
}





