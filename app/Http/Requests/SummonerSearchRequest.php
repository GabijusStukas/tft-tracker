<?php

namespace App\Http\Requests;

use App\DTO\SummonerSearchDTO;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'region'   => ['required', 'string', 'max:10'],
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
            region: $data['region'],
            username: $data['username'],
            tagLine: $data['tag_line'] ?? null
        );
    }
}
