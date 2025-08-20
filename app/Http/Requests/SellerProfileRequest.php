<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SellerProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $newPasswordRule        = 'sometimes';
        $confirmPasswordRule    = 'sometimes';
        if ($this->request->get('new_password') != null && $this->request->get('confirm_password') != null) {
            $newPasswordRule       = ['min:6'];
            $newPasswordRule       = ['min:6'];
        }
        return [
            'name'              => ['required', 'max:191'],
            'new_password'      => $newPasswordRule,
            'confirm_password'  => $confirmPasswordRule,
            'phone'             => ['required'],
            'cnic_no'             => ['required', 'digits:13'], // Assuming CNIC is a 13-digit number
            'date_of_issue'       => ['required', 'date'],
            'date_of_expiry'      => ['required', 'date', 'after:date_of_issue'],
            'front_side_picture'  => ['required'], // Image with max size 2MB
            'back_side_picture'   => ['required',],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required'         => translate('Name is required'),
            'new_password.min'      => translate('Minimum 6 characters'),
            'confirm_password.min'  => translate('Minimum 6 characters'),
            'cnic_no.required'        => translate('CNIC number is required'),
            'cnic_no.digits'          => translate('CNIC number must be 13 digits'),
            'date_of_issue.required'  => translate('Date of issue is required'),
            'date_of_issue.date'      => translate('Date of issue must be a valid date'),
            'date_of_expiry.required' => translate('Date of expiry is required'),
            'date_of_expiry.date'     => translate('Date of expiry must be a valid date'),
            'date_of_expiry.after'    => translate('Date of expiry must be after the date of issue'),
            'front_side_picture.required' => translate('Front side picture is required'),
            // 'front_side_picture.image'    => translate('Front side picture must be an image'),
            // 'front_side_picture.mimes'    => translate('Front side picture must be a file of type: jpeg, png, jpg'),
            // 'front_side_picture.max'      => translate('Front side picture may not be greater than 2MB'),
            'back_side_picture.required'  => translate('Back side picture is required'),
            // 'back_side_picture.image'     => translate('Back side picture must be an image'),
            // 'back_side_picture.mimes'     => translate('Back side picture must be a file of type: jpeg, png, jpg'),
            // 'back_side_picture.max'       => translate('Back side picture may not be greater than 2MB'),
        ];
    }
}
