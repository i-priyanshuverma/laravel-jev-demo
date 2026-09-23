<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\TriageScenario;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AnalyzeScenarioRequest extends FormRequest
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
            'scenario' => ['required', 'string', Rule::enum(TriageScenario::class)],
            'input' => ['required', 'string', 'min:3', 'max:5000'],
        ];
    }

    public function scenario(): TriageScenario
    {
        return TriageScenario::from($this->string('scenario')->value());
    }

    public function inputMessage(): string
    {
        return $this->string('input')->value();
    }
}
