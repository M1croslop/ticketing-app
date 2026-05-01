<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Ticket;


class UpdateTicketRequest extends FormRequest
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
            'title'       => 'required|string|min:5|max:150',
            'description' => 'required|string|min:10',
            'status'      => ['required', 'in:' . implode(',', Ticket::STATUSES)],
            'priority'    => ['required', 'in:' . implode(',', Ticket::PRIORITIES)],
            'category_id' => 'required|exists:categories,id',
            'agent_id'    => 'nullable|exists:users,id',
            'due_date'    => 'nullable|date|after_or_equal:today',
        ];
    }
}
