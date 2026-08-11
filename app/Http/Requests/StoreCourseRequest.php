<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',

            'modules' => 'required|array|min:1',
            'modules.*.title' => 'required|string|max:255',
            'modules.*.description' => 'nullable|string',

            'modules.*.contents' => 'nullable|array',
            'modules.*.contents.*.type' => 'required|in:text,file,link',

            'modules.*.contents.*.text' => 'nullable|string',
            'modules.*.contents.*.link' => 'nullable|url',
            'modules.*.contents.*.file' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov,avi|max:20480',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $data = $this->all();
            foreach ($data['modules'] ?? [] as $mi => $module) {
                foreach ($module['contents'] ?? [] as $ci => $content) {
                    $keyBase = "modules.$mi.contents.$ci";

                    if (($content['type'] ?? '') === 'text' && empty($content['text'])) {
                        $validator->errors()->add("$keyBase.text", 'Text is required for text content.');
                    }
                    if (($content['type'] ?? '') === 'link' && empty($content['link'])) {
                        $validator->errors()->add("$keyBase.link", 'Link is required for link content.');
                    }
                    if (($content['type'] ?? '') === 'file' && !$this->file("$keyBase.file")) {
                        $validator->errors()->add("$keyBase.file", 'File is required for file content.');
                    }
                }
            }
        });
    }
}
