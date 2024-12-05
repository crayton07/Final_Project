<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add to Catalog</title>
</head>
<body>
    <h1 style="text-align: center;">Add to Pictures and Blobs</h1>
    <form action="add.php" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; align-items: center;">
        <label for="file_name">File Name:</label>
        <input type="text" id="file_name" name="file_name" required>
        <br>
    

        <button type="submit">Add to Catalog</button>
    </form>
</body>
</html>
