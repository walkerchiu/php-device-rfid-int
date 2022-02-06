<?php

namespace WalkerChiu\DeviceRFID\Models\Forms;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use WalkerChiu\Core\Models\Forms\FormRequest;

class ReaderRegisterFormRequest extends FormRequest
{
    /**
     * @Override Illuminate\Foundation\Http\FormRequest::getValidatorInstance
     */
    protected function getValidatorInstance()
    {
        $request = Request::instance();
        $data = $this->all();
        if (
            $request->isMethod('put')
            && empty($data['id'])
            && isset($request->id)
        ) {
            $data['id'] = (int) $request->id;
            $this->getInputSource()->replace($data);
        }

        return parent::getValidatorInstance();
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return Array
     */
    public function attributes()
    {
        return [
            'reader_id'   => trans('php-device-rfid::reader.reader_id'),
            'serial'      => trans('php-device-rfid::reader.serial'),
            'identifier'  => trans('php-device-rfid::reader.identifier'),
            'mean'        => trans('php-device-rfid::reader.mean'),
            'data_type'   => trans('php-device-rfid::reader.data_type'),
            'order'       => trans('php-device-rfid::reader.order'),
            'is_enabled'  => trans('php-device-rfid::reader.is_enabled'),

            'name'        => trans('php-device-rfid::reader.name'),
            'description' => trans('php-device-rfid::reader.description')
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return Array
     */
    public function rules()
    {
        $rules = [
            'reader_id'   => ['required','integer','min:1','exists:'.config('wk-core.table.device-rfid.readers').',id'],
            'serial'      => '',
            'identifier'  => 'required|string|max:255',
            'mean'        => 'required|string',
            'data_type'   => ['required', Rule::in(config('wk-core.class.core.dataType')::getCodes())],
            'order'       => 'nullable|numeric|min:0',
            'is_enabled'  => 'boolean',

            'name'        => 'required|string|max:255',
            'description' => ''
        ];

        $request = Request::instance();
        if (
            $request->isMethod('put')
            && isset($request->id)
        ) {
            $rules = array_merge($rules, ['id' => ['required','integer','min:1','exists:'.config('wk-core.table.device-rfid.readers_registers').',id']]);
        } elseif ($request->isMethod('post')) {
            $rules = array_merge($rules, ['id' => ['nullable','integer','min:1','exists:'.config('wk-core.table.device-rfid.readers_registers').',id']]);
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return Array
     */
    public function messages()
    {
        return [
            'id.integer'          => trans('php-core::validation.integer'),
            'id.min'              => trans('php-core::validation.min'),
            'id.exists'           => trans('php-core::validation.exists'),
            'reader_id.required'  => trans('php-core::validation.required'),
            'reader_id.integer'   => trans('php-core::validation.integer'),
            'reader_id.min'       => trans('php-core::validation.min'),
            'reader_id.exists'    => trans('php-core::validation.exists'),
            'identifier.required' => trans('php-core::validation.required'),
            'identifier.string'   => trans('php-core::validation.string'),
            'identifier.max'      => trans('php-core::validation.max'),
            'mean.required'       => trans('php-core::validation.required'),
            'mean.string'         => trans('php-core::validation.string'),
            'data_type.required'  => trans('php-core::validation.required'),
            'data_type.in'        => trans('php-core::validation.in'),
            'order.numeric'       => trans('php-core::validation.numeric'),
            'order.min'           => trans('php-core::validation.min'),
            'is_enabled.required' => trans('php-core::validation.required'),
            'is_enabled.boolean'  => trans('php-core::validation.boolean'),

            'name.required'       => trans('php-core::validation.required'),
            'name.string'         => trans('php-core::validation.string'),
            'name.max'            => trans('php-core::validation.max')
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after( function ($validator) {
            $data = $validator->getData();
            if (isset($data['identifier'])) {
                $result = config('wk-core.class.device-rfid.readerRegister')::where('identifier', $data['identifier'])
                                ->when(isset($data['reader_id']), function ($query) use ($data) {
                                    return $query->where('reader_id', $data['reader_id']);
                                  })
                                ->when(isset($data['id']), function ($query) use ($data) {
                                    return $query->where('id', '<>', $data['id']);
                                  })
                                ->exists();
                if ($result)
                    $validator->errors()->add('identifier', trans('php-core::validation.unique', ['attribute' => trans('php-device-rfid::readerRegister.identifier')]));
            }
        });
    }
}
