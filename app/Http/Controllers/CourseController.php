<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseRequest;
use App\Models\Course;
use App\Services\CourseService;
use Illuminate\Support\Facades\Log;
use Exception;

class CourseController extends Controller
{
    protected $service;

    public function __construct(CourseService $service)
    {
        $this->service = $service;
    }

    // Dashboard: show all courses
    public function showAllCourses() {
    // Load courses with modules + contents
    $courses = Course::with('modules.contents')->get();
    return view('courses.show', compact('courses'));
}

    public function create()
    {
        return view('courses.create');
    }

    public function store(StoreCourseRequest $request)
    {
        try {
            $course = $this->service->createCourse($request->validated(), $request->allFiles());
            return redirect()->route('courses.showAll')
                ->with('success', 'Course created successfully.');
        } catch (Exception $e) {
            Log::error('Course store error: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to save course.']);
        }
    }

    

    public function destroy(Course $course)
    {
        try {
            $course->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
