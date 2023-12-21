<?php include "includes.php" ?>

<!DOCTYPE html>
<html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>data-base-second-course</title>
            <link rel="stylesheet" href="style.css">
        </head>
    <body>

        <div class="top-menu">
            <ul>
                <li><a href="#">Главная</a></li>
                <li><a href="#">О нас</a></li>
                <li><a href="news.php">Новини</a></li>
                <?php
                    if (!app::cart()->isEmpty()) {
                        ?>
                        <li><a href="cart.php">Корзина (<?=app::cart()->count()?> / <?=app::cart()->sum()?> грн)</a></li>
                        <?php
                    }
                ?>
            </ul>
        </div>

        <div class="main">
            <?php
            
                $count = app::db()->queryOne("SELECT COUNT(*) as 'PC_ID' FROM `pc`");
                $sqlOrder = "SELECT * FROM `pc` ORDER BY `RAM_GB`";

                if (app::db()->isConnect()) 
                {
                    $tab = new WebTable();
                    $tab->createTable($count, $sqlOrder);
                } 
                else echo 'Error connect to database...';

            ?>
        </div>

        <footer>
        <p>&copy; 2023 web-database Biblenko Danil IPZ-22-1. All rights reserved.</p>
        </footer>
    </body>
</html>
