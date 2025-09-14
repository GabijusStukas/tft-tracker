<?php

namespace App\Http\Requests;

use App\DTO\RiotAccountSearchDTO;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RiotAccountSearchRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:100'],
            'tag_line' => ['sometimes', 'string', 'max:100'],
        ];
    }

    /**
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'username' => $this->username ?? $this->route('username'),
            'tag_line' => $this->tag_line ?? $this->route('tag_line'),
        ]);
    }

    /**
     * @return RiotAccountSearchDTO
     */
    public function toDTO(): RiotAccountSearchDTO
    {
        $data = $this->validated();

        return new RiotAccountSearchDTO(
            username: $data['username'],
            tagLine: $data['tag_line'] ?? null
        );
    }
}
