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
        $rules = [
            'title'       => 'sometimes|required|string|min:5|max:150',
            'description' => 'sometimes|required|string|min:10',
            'category_id' => 'sometimes|required|exists:categories,id',
        ];

        $user = auth()->user();

        if ($user->role === 'admin') {
            $rules['status']   = ['sometimes', 'required', 'in:' . implode(',', Ticket::STATUSES)];
            $rules['priority'] = ['sometimes', 'required', 'in:' . implode(',', Ticket::PRIORITIES)];
            $rules['agent_id'] = 'sometimes|nullable|exists:users,id';
            $rules['due_date'] = 'sometimes|nullable|date';
        } elseif ($user->role === 'agent') {
            $rules['status']   = ['sometimes', 'required', 'in:resolved'];
        } else {
            $rules['status']   = ['sometimes', 'required', 'in:closed'];
        }

        return $rules;
    }
}
