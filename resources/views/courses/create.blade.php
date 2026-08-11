<!doctype html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Create Manager</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    body {
      font-family: system-ui, Arial;
      margin: 20px;
      background-color: #c4c2c2;
      font-weight: bold; /* makes all text bold */
    }

    .module {
      border: 1px solid #ddd;
      padding: 12px;
      margin-bottom: 12px;
    }

    .content-item {
      border: 1px dashed #ccc;
      padding: 8px;
      margin: 6px 0;
    }

    .hidden {
      display: none;
    }

    #add-module, .add-content {
        background-color: #007bff; 
        color: white;               
        border: none;
        padding: 8px 16px;
        cursor: pointer;
        border-radius: 4px;
        font-size: 16px;
    }
    #add-module:hover, .add-content:hover {
        background-color: #0056b3; 
    }

    button[type="submit"] {
        background-color: #28a745; 
        color: white;
        border: none;
        padding: 8px 16px;
        cursor: pointer;
        border-radius: 4px;
        font-size: 16px;
    }
    button[type="submit"]:hover {
        background-color: #1e7e34; 
    }

    .remove-module, .remove-content {
        background-color: #dc3545; 
        color: white;
        border: none;
        padding: 8px 16px;
        cursor: pointer;
        border-radius: 4px;
        font-size: 16px;
    }
    .remove-module:hover, .remove-content:hover {
        background-color: #a71d2a; 
    }
</style>

</head>

<body>
  <h1>Create Course</h1>

  {{-- Show validation errors --}}
  @if($errors->any())
    <div style="color:red;">
      <ul>
        @foreach($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('courses.store') }}" enctype="multipart/form-data">
    @csrf

    <div>
      <label>Course Title *</label><br>
      <input type="text" name="title" required value="{{ old('title') }}" style="width:100%">
    </div>
    <div>
      <label>Category</label><br>
      <input type="text" name="category" style="width:100%" value="{{ old('category') }}">
    </div>
    <div>
      <label>Description</label><br>
      <textarea name="description" rows="4" style="width:100%">{{ old('description') }}</textarea>
    </div>

    <hr>
    <h2>Modules</h2>
    <div id="modules"></div>
    <button type="button" id="add-module">+ Add Module</button>

    <hr>
    <button type="submit">Save Course</button>
  </form>

  <!-- Module template -->
  <template id="module-template">
    <div class="module" data-mi="__MODULE_INDEX__">
      <h3>Module <span class="module-number">__MODULE_LABEL__</span></h3>
      <div>
        <label>Module Title *</label><br>
        <input type="text" name="modules[__MODULE_INDEX__][title]" required style="width:100%"
      </div>
      <div>
        <label>Module Description</label><br>
        <textarea name="modules[__MODULE_INDEX__][description]" rows="2" style="width:100%"></textarea>
      </div>

      <div class="contents-list"></div>
      <div>
        <button type="button" class="add-content">+ Add Content</button>
        <button type="button" class="remove-module">Remove Module</button>
      </div>
    </div>
  </template>

  <!-- Content template -->
  <template id="content-template">
    <div class="content-item">
      <div>
        <label>Type</label>
        <select class="content-type" name="modules[__MODULE_INDEX__][contents][__CONTENT_INDEX__][type]">
          <option value="text">Text</option>
          <option value="file">File (image/video)</option>
          <option value="link">Link</option>
        </select>
        <button type="button" class="remove-content">Remove Content</button>
      </div>

      <div>
        <label>Title</label><br>
        <input type="text" name="modules[__MODULE_INDEX__][contents][__CONTENT_INDEX__][title]" style="width:80%">
      </div>

      <div class="content-text">
        <label>Text</label><br>
        <textarea name="modules[__MODULE_INDEX__][contents][__CONTENT_INDEX__][text]" rows="3"
          style="width:80%"></textarea>
      </div>

      <div class="content-file hidden">
        <label>File (image/video)</label><br>
        <input type="file" name="modules[__MODULE_INDEX__][contents][__CONTENT_INDEX__][file]">
      </div>

      <div class="content-link hidden">
        <label>Link</label><br>
        <input type="url" name="modules[__MODULE_INDEX__][contents][__CONTENT_INDEX__][link]" style="width:70%">
      </div>
    </div>
  </template>

  <script>
    $(function () {
      let moduleIndex = 0;

      // Add module
      $('#add-module').on('click', function () {
        let tpl = $('#module-template').html()
          .replace(/__MODULE_INDEX__/g, moduleIndex)
          .replace(/__MODULE_LABEL__/g, moduleIndex + 1);
        $('#modules').append(tpl);
        moduleIndex++;
      });

      // Add content
      $(document).on('click', '.add-content', function () {
        let $module = $(this).closest('.module');
        let mi = $module.data('mi');
        let next = $module.data('nextContent') || 0;

        let tpl = $('#content-template').html()
          .replace(/__MODULE_INDEX__/g, mi)
          .replace(/__CONTENT_INDEX__/g, next);

        $module.find('.contents-list').append(tpl);
        $module.data('nextContent', next + 1);
      });

      // Remove module
      $(document).on('click', '.remove-module', function () {
        if (confirm('Remove this module?')) $(this).closest('.module').remove();
      });

      // Remove content
      $(document).on('click', '.remove-content', function () {
        if (confirm('Remove this content?')) $(this).closest('.content-item').remove();
      });

      // Toggle fields by type
      $(document).on('change', '.content-type', function () {
        let $item = $(this).closest('.content-item');
        let type = $(this).val();
        $item.find('.content-text, .content-file, .content-link').addClass('hidden');
        if (type === 'text') $item.find('.content-text').removeClass('hidden');
        if (type === 'file') $item.find('.content-file').removeClass('hidden');
        if (type === 'link') $item.find('.content-link').removeClass('hidden');
      });

      // Add the first module automatically
      $('#add-module').trigger('click');
    });
  </script>
</body>

</html>