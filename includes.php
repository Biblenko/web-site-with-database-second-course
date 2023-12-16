<?php

session_start();
//error_reporting(0);

include("include/db.php");
include("include/pagination.php");
include("include/table.php");

include("include/html.php");
include("include/request.php");
include("include/cart.php");
include("include/app.php");

app::init();
