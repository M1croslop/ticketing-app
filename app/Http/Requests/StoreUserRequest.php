<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class StoreUserRequest extends FormRequest
{
    /**
     * Solo admins pueden acceder a esta ruta (garantizado por middleware),
     * por lo que cualquier usuario autenticado que llegue aquí está autorizado.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'     => 'required|string|min:3|max:100',
            'email'    => 'required|email|max:255|unique:users,email',
            'role'     => 'required|in:admin,agent,client',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'El nombre es obligatorio.',
            'name.min'           => 'El nombre debe tener al menos 3 caracteres.',
            'email.required'     => 'El correo es obligatorio.',
            'email.email'        => 'El correo no tiene un formato válido.',
            'email.unique'       => 'Este correo ya está registrado en el sistema.',
            'role.required'      => 'Debes seleccionar un rol.',
            'role.in'            => 'El rol seleccionado no es válido.',
            'password.required'  => 'La contraseña es obligatoria.',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ];
    }
}
