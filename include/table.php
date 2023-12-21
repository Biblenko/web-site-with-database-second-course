
<?php

class WebTable
{
    public function __construct() 
    {
        
    }

    public function createTable($sql_count, $sql_rows)
    {
        $pagination = new Pagination();
        $pagination->setLimit(Request::get('limit', 20));

        ?>
        <div class="table">
            <form>
                <div class="limit">
                    <?php
                        HtmlHelper::select("limit", $pagination->limits, $pagination->getLimit());
                    ?>
                </div>
                <input type="submit" value="Знайти">
            </form>
        <?php

        $pagination->setRowsCount($sql_count['PC_ID']);
        $pagination->setPage((isset($_REQUEST['page']))?$_REQUEST['page']:1);

        $rows = app::db()->query($sql_rows." LIMIT ".$pagination->getFirst()."," . $pagination->getLimit());

        if ($rows === false) {
            echo 'Error select';
        } else {
            //echo "<div class="."text".">Rows: {$pagination->getRowsCount()} Pages: {$pagination->getPageCount()}</div>";
            echo '</table>';
            echo '<div class="pagination">';
            echo $pagination->show();
            echo '</div>';

            echo "<table border='1'><tr>";
            foreach ($rows[0] as $key => $value) {
                echo "<th>".$key."</th>";
            }
            echo "</tr>";
            
            foreach($rows as $row) {
                echo "<tr>";
                foreach ($row as $key => $value) {
                    echo "<td>".$value."</td>";
                }
                echo '<td><a href="cart.php'.$pagination->getParams($pagination->getPage()).'&cart=add&id='.$row['PC_ID'].'">Купити</a></td></tr>';
            }
            
            echo '</table>';
            echo '<div class="pagination">';
            echo $pagination->show();
            echo '</div>';
            echo '</div>';
        }
    }
}