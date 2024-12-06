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
        <h1 style="font-size: 100px;">
            Embark on the Dietrich Journey!!!
        </h1>
    </header>

    <section class="box box-long" style="justify-content: center; align-items: center; text-align: center; margin-left: 330px;">
        <p style="font-size: 20px;">
            This curated collection of 55 meticulously edited images was created by Mr. Philip Gonzales, 
            and this website has been made to display them!
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

        <section class="box box-long2" style="width: 40%; height: 350px; border: none;">
            <iframe 
                src="edit_description_form.php" 
                name="edit-description-frame" 
                style="width: 100%; height: 100%; border: none;" 
                title="Edit Description Form">
            </iframe>
        </section>

        <br>
        <br>
        <br>
        <br>
        <h1>
            File Names and Descriptions
        </h1>

        <section class="box">
            <iframe 
                src="Description.php" 
                name="table-frame" 
                style="width: 100%; height: 300px; border: none;" 
                title="File Names and Descriptions Table">
            </iframe>
        </section>

<table>
    <tr>
        <td style="text-align: right">
        <section class="box box-long2">
            <h2>View the Full Catalog Here!!</h2>
            <a href="http://localhost:8080/Final_Project/catalog.php">
                <section class="box box-circle">
                    <img src="arrow.png" style="height: 30px;">
                </section>
            </a>
        </section>

        </td>

        <td>


        <section class="box box-long2">
            <h1>Contribute to the Catalog!!</h1>
            <a href="http://localhost:8080/Final_Project/add_form.php">
                <section class="box box-circle">
                    <img src="arrow.png" style="height: 30px;">
                </section>
            </a>
        </section>
        </td>
    </tr>
    </table>

    </main>

    <footer>
        <a href="http://localhost:8080/Final_Project/logout.php">
            Log Out
        </a>
    </footer>

</body>
</html>
