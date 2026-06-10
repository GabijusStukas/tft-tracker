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
            'game'     => ['required', 'string', 'max:10', 'in:tft'],
            'region'   => ['required', 'string', 'max:10', 'in:euw1,eun1,na1,kr,jp1,br1,oc1,tr1'],
            'username' => ['required', 'string', 'max:100'],
            'tag_line' => ['sometimes', 'string', 'max:100'],
            'refresh'  => ['sometimes', 'boolean']
        ];
    }

    /**
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'game'     => $this->game ?? $this->route('game') ?? 'tft',
            'region'   => $this->region ?? $this->route('region') ?? 'euw1',
            'username' => $this->username ?? $this->route('username'),
            'tag_line' => $this->tag_line ?? $this->route('tag_line'),
            'refresh'  => $this->refresh ?? $this->route('refresh') ?? false,
        ]);
    }

    /**
     * @return RiotAccountSearchDTO
     */
    public function toDTO(): RiotAccountSearchDTO
    {
        $data = $this->validated();

        return new RiotAccountSearchDTO(
            game: $data['game'],
            region: $data['region'],
            username: $data['username'],
            tagLine: $data['tag_line'] ?? null,
            refresh: $data['refresh'] ?? false,
        );
    }
}
