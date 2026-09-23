<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomSandboxRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'input' => ['required', 'string', 'min:2', 'max:5000'],
            'mode' => ['required', 'string', 'in:boolean,choose,score'],
            'params' => ['nullable', 'array'],
        ];
    }

    public function inputMessage(): string
    {
        return $this->string('input')->value();
    }

    public function mode(): string
    {
        return $this->string('mode')->value();
    }

    /**
     * @return array<string, mixed>
     */
    public function modeParams(): array
    {
        return $this->input('params', []);
    }
}
