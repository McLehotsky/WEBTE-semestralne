<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <title>PDF Príručka</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        h1, h2 { color: #1a202c; }
    </style>
</head>
<body>
    <h1>📘 Používateľská príručka</h1>

    @include('guide.sections.frontend')
    @include('guide.sections.backend')
</body>
</html>
