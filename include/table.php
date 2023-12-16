
<?php

class WebTable
{
    public function __construct() 
    {
        
    }

    public function createTable($sql_count, $sql_rows)
    {
        $pagination = new Pagination();
        $pagination->setLimit((isset($_REQUEST['limit']))?$_REQUEST['limit']:10);

        ?>
        <div class="table">
            <form>
                <select name="limit">
                    <?php
                        foreach($pagination->limits as $lim) {
                            echo '<option '.(($lim == $pagination->getLimit())?'selected':'').'>'.$lim.'</option>';
                        }
                    ?>
                </select>
                <input type="submit" value="OK">
            </form>
        <?php

        $pagination->setRowsCount($sql_count['PC_ID']);
        $pagination->setPage((isset($_REQUEST['page']))?$_REQUEST['page']:1);

        $rows = app::db()->query($sql_rows." LIMIT ".$pagination->getFirst()."," . $pagination->getLimit());

        if ($rows === false) {
            echo 'Error select';
        } else {
            echo "<div class="."text".">Rows: {$pagination->getRowsCount()} Pages: {$pagination->getPageCount()}</div>";
            echo '</table>';
            echo '<div class="pagination">';
            echo $pagination->show();
            echo '</div>';
            ?>
            <table border='1'><tr><th>NUMBER</th><th>CPU</th><th>GPU</th><th>Storage</th><th>RAM(GB)</th><th>Price</th></tr>
            <?php
            $num = $pagination->getFirst();
            foreach($rows as $row) {
                echo '<tr><td>'.($num + 1).'</td><td>'.$row['CPU'].'</td><td>'.$row['GPU'].'</td><td>'.$row['Storage'].'</td><td>'.$row['RAM_GB'].'</td><td>'.$row['Price'].'</td></tr>';
                $num ++;
            }
            echo '</table>';
            echo '<div class="pagination">';
            echo $pagination->show();
            echo '</div>';
            echo '</div>';
        }
    }
}