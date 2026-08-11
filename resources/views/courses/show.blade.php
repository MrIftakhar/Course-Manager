<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses Dashboard</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            background: #a8a5a5;
        }

        aside {
            width: 250px;
            background: #fff;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        aside h2 {
            margin-top: 0;
        }

        aside nav a {
            display: block;
            padding: 10px;
            margin-bottom: 5px;
            text-decoration: none;
            color: #333;
            border-radius: 4px;
        }

        aside nav a:hover {
            background: #eee;
        }

        main {
            flex: 1;
            padding: 20px;
        }

        .add-course-btn,
        .delete-btn {
            padding: 10px 15px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            color: #fff;
        }

        .add-course-btn {
            background: #4CAF50;
            margin-bottom: 20px;
            text-decoration: none;
            display: inline-block;
        }

        .delete-btn {
            background: #e74c3c;
            float: right;
        }

        .course-card {
            background: #fff;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .module {
            margin-top: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #fafafa;
        }

        .content-item {
            margin-left: 20px;
            margin-top: 5px;
        }
    </style>
</head>

<body>


    <!-- Main -->
    <main>
        <h1>Dashboard</h1>
        <a href="{{ route('courses.create') }}" class="add-course-btn">+ Add Course</a>

        @foreach($courses as $course)
            <div class="course-card" data-id="{{ $course->id }}">
                <button class="delete-btn" type="button" data-id="{{ $course->id }}">Delete</button>
                <h2>{{ $course->title }}</h2>
                <p><strong>Description:</strong> {{ $course->description }}</p>
                <p><strong>Category:</strong> {{ $course->category }}</p>

                <!-- Modules -->
                @foreach($course->modules as $module)
                    <div class="module">
                        <h3>{{ $module->title }}</h3>
                        <p>{{ $module->description }}</p>

                        <!-- Module contents -->
                        @foreach($module->contents as $content)
                            <div class="content-item">
                                <strong>{{ ucfirst($content->type) }}:</strong> {{ $content->title }}
                                @if($content->type === 'text')
                                    <p>{{ $content->body }}</p>
                                @elseif($content->type === 'file')
                                    <a href="{{ $content->file_url }}" target="_blank">View File</a>
                                @elseif($content->type === 'link')
                                    <a href="{{ $content->link }}" target="_blank">{{ $content->link }}</a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        @endforeach
    </main>

    <script>
        $(document).ready(function () {
            $('.delete-btn').click(function () {
                if (!confirm('Are you sure you want to delete this course?')) return;

                var card = $(this).closest('.course-card');
                var courseId = $(this).data('id');

                $.ajax({
                    url: '/courses/' + courseId,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function (response) {
                        if (response.success) {
                            card.remove();
                            alert('Course deleted successfully');
                        } else {
                            alert('Failed: ' + response.message);
                        }
                    },
                    error: function () {
                        alert('Error deleting course.');
                    }
                });
            });
        });
    </script>
</body>

</html>