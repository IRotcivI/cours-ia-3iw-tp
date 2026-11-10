<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Project;
use App\Enums\TaskPriority;
use Illuminate\Validation\Rule;

class TaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
            'priority' => ['required', Rule::enum(TaskPriority::class)],
            'assigned_to' => [
                'nullable',
                Rule::exists('project_user', 'user_id')->where('project_id', $this->project()->id),
            ],
        ];
    }

    private function project(): Project
    {
        return $this->route('project') ?? $this->route('task')->project;
    }
}
