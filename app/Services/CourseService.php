<?php

namespace App\Services;

use App\Models\Course;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;

class CourseService
{
    public function createCourse(array $data, array $uploadedFiles = [])
    {
        return DB::transaction(function () use ($data, $uploadedFiles) {
            $course = Course::create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'category' => $data['category'] ?? null,
                'meta' => $data['meta'] ?? null,
            ]);

            foreach ($data['modules'] ?? [] as $mIndex => $m) {
                $module = $course->modules()->create([
                    'title' => $m['title'],
                    'description' => $m['description'] ?? null,
                    'position' => $mIndex,
                ]);

                foreach ($m['contents'] ?? [] as $cIndex => $c) {
                    $payload = [
                        'type' => $c['type'],
                        'title' => $c['title'] ?? null,
                        'position' => $cIndex,
                    ];

                    if ($c['type'] === 'text') $payload['body'] = $c['text'] ?? null;
                    if ($c['type'] === 'link') $payload['link'] = $c['link'] ?? null;

                    if ($c['type'] === 'file') {
                        $file = data_get($uploadedFiles, "modules.$mIndex.contents.$cIndex.file");
                        if ($file instanceof UploadedFile) {
                            $path = $file->store("courses/{$course->id}/modules/{$module->id}", 'public');
                            $payload['file_path'] = $path;
                        }
                    }
                    $module->contents()->create($payload);
                }
            }
            return $course;
        });
    }
}
