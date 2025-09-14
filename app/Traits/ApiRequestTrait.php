<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait ApiRequestTrait
{
    /**
     * Validate request data with custom rules
     *
     * @param Request $request
     * @param array $rules
     * @param array $messages
     * @return \Illuminate\Validation\Validator
     */
    protected function validateRequest(Request $request, array $rules, array $messages = [])
    {
        return Validator::make($request->all(), $rules, $messages);
    }

    /**
     * Get validated data from request
     *
     * @param Request $request
     * @param array $rules
     * @return array
     */
    protected function getValidatedData(Request $request, array $rules)
    {
        $validator = $this->validateRequest($request, $rules);
        
        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }
        
        return $validator->validated();
    }

    /**
     * Check if request has specific fields
     *
     * @param Request $request
     * @param array $fields
     * @return bool
     */
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





