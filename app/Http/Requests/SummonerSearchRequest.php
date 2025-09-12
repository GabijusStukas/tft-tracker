<?php

namespace App\Http\Requests;

use App\DTO\SummonerSearchDTO;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SummonerSearchRequest extends FormRequest
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
     * @return SummonerSearchDTO
     */
    public function toDTO(): SummonerSearchDTO
    {
        $data = $this->validated();

        return new SummonerSearchDTO(
            username: $data['username'],
            tagLine: $data['tag_line'] ?? null
        );
    }
}
