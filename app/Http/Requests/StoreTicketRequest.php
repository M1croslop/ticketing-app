<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class StoreTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
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
            'priority'    => ['required', 'in:' . implode(',', Ticket::PRIORITIES)],
            'category_id' => 'required|exists:categories,id',
            'agent_id'    => 'nullable|exists:users,id',
        ];
    }
}
