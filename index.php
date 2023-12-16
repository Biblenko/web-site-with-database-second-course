
<?php
include "includes.php";
?>

<!DOCTYPE html>
<html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>data-base-second-course</title>
            <link rel="stylesheet" href="style.css">
        </head>
    <body>
        <div class="main">
            <?php
                $count = app::db()->queryOne("SELECT COUNT(*) as 'PC_ID' FROM `pc`");
                $sqlOrder = "SELECT * FROM `pc` ORDER BY `RAM_GB`";

                if (app::db()->isConnect()) 
                {
                    ?>
                    <div>
                        <?php

                        ?>
                    </div>
                    <?php
                    $tab = new WebTable();
                    $tab->createTable($count, $sqlOrder);
                } 
                else echo 'Error connect to database...';

            ?>
        </div>
    </body>
</html>
