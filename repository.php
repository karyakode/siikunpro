<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Direktori</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        body {
            font-family: sans-serif;
            padding: 20px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1 class="mb-4">Daftar Direktori</h1>
        <ul class="list-group">
            <?php
 $basePath = __DIR__ . '/repository'; if (is_dir($basePath)) { $items = scandir($basePath); $directories = array_filter($items, function ($item) use ($basePath) { return is_dir($basePath . '/' . $item) && $item !== '.' && $item !== '..'; }); if (!empty($directories)) { foreach ($directories as $directory) { $relativePath = 'repository/' . $directory . '/'; echo '<li class="list-group-item">'; echo '<a href="' . $relativePath . '" target="_blank" class="text-decoration-none">' . htmlspecialchars($directory) . '</a>'; echo '</li>'; } } else { echo '<li class="list-group-item">Tidak ada direktori di dalam folder <code>repository</code>.</li>'; } } else { echo '<li class="list-group-item text-danger">Folder <code>repository</code> tidak ditemukan.</li>'; } ?>
        </ul>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>
