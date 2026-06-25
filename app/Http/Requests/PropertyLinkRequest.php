<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\AuthorizesAdminStaff;
use App\Models\PropertyLink;
use Illuminate\Foundation\Http\FormRequest;

class PropertyLinkRequest extends FormRequest
{
    use AuthorizesAdminStaff;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'property_id' => ['required', 'integer', 'exists:properties,id'],
            'label' => ['required', 'string', 'max:160'],
            'url' => ['required', 'url', 'max:2048'],
            'visible_to_client' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Return validated data mapped to database column names.
     *
     * @return array<string, mixed>
     */
    public function propertyLinkData(): array
    {
        $data = $this->validated();

        $data['is_visible_to_client'] = $this->boolean('visible_to_client');
        unset($data['visible_to_client']);

        $data['type'] = $this->inferType($data['url'], $data['label']);

        return $data;
    }

    private function inferType(string $url, string $label): string
    {
        $haystack = strtolower($url.' '.$label);

        return match (true) {
            str_contains($haystack, 'dropbox') => PropertyLink::TYPE_DROPBOX,
            str_contains($haystack, 'broadcast'), str_contains($haystack, 'mailchimp'), str_contains($haystack, 'email') => PropertyLink::TYPE_BROADCAST_EMAIL,
            str_contains($haystack, 'brochure') => PropertyLink::TYPE_BROCHURE,
            str_contains($haystack, '.pdf'), str_contains($haystack, 'file') => PropertyLink::TYPE_FILE,
            default => PropertyLink::TYPE_URL,
        };
    }
}
