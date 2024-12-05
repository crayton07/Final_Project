<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Final Project</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <section class="flex-container">
            <div class="box-header">
                <img src="stretchedHeroSection.png">
            </div>
        </section>
        <h1>
            Embark on the Dietrich Journey!!!
        </h1>
    </header>
   
    <section class="box box-long">
        <p style="font-size: 20px;">
            This curated collection of 55 meticulously edited images was created by Mr. Philip Gonzales, 
            and this website has been made to display them. 
        </p>
    </section>

    <main>
        <section class="subheader">
            <h2>Notable Images</h2>
        </section>
        <section class="image-row">
            <div class="image-box">
                <img src="Deets ate my dog.png" alt="Image 1">
                <label>The Fan Favorite</label>
            </div>
            <div class="image-box">
                <img src="floss.png" alt="Image 2">
                <label>The Most Questionable</label>
            </div>
            <div class="image-box">
                <img src="syphon.png" alt="Image 3">
                <label>Professor Dietrich's Favorite</label>
            </div>
        </section>

        <section class="box box-long2">
            <h2>View the Full Catalog Here!!</h2>
            <a href="http://localhost:8080/Final_Project/catalog.php">
                <section class="box box-circle">
                    <img src="arrow.png" style="height: 30px;">
                </section>
            </a>
        </section>

        <section class="box box-long2">
            <h1>Contribute to the Catalog!!</h1>
            <a href="http://localhost:8080/Final_Project/add_form.html">
                <section class="box box-circle">
                    <img src="arrow.png" style="height: 30px;">
                </section>
            </a>
        </section>

        <section class="box box-long2">
            <h1>Change Image Description</h1>
            <form action="edit_description.php" method="POST">
                <label for="file_name">Select an Image:</label>
                <select id="file_name" name="file_name" required>
                    <?php include 'get_image_names.php'; ?>
                </select>
                <br><br>
                <label for="description">New Description:</label>
                <textarea id="description" name="description" rows="3" required></textarea>
                <br><br>
                <button type="submit">Update Description</button>
            </form>
        </section>

        <section class="box box-long2">
            <h1>File Names and Descriptions</h1>
            <iframe 
                src="Description.php" 
                name="table-frame" 
                style="width: 100%; height: 500px; border: none;" 
                title="File Names and Descriptions Table">
            </iframe>
        </section>
    </main>

    <footer>
        <a href="http://localhost:8080/Final_Project/login.php">Log Out</a>
    </footer>
</body>
</html>
