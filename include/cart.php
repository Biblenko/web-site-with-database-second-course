<?php
class Cart
{
    protected $db;
    protected $products = [];
    protected $sum = 0;
    protected $count = 0;


    public function count() {
        return $this->count;
    }

    public function sum() {
        return $this->sum;
    }

    public function rows() {
        return $this->products;
    }

    public function isEmpty() {}
    protected function get() {}
    public function clear() {}
    protected function deleteRow($i) {}
    protected function addRow($ar) {}
    protected function setAmount($i, $amount) {}
    protected function getAmount($i) {}
    //

    /*
s    public function isEmpty() {
        return (!isset($_SESSION[$this->key]) || !$_SESSION[$this->key]);
    }

    private function get() {
        if ($this->isEmpty()) return [];
        return $_SESSION[$this->key];
    }

    public function clear() {
        unset($_SESSION[$this->key]);
    }

    private function deleteRow($i) {
        unset($_SESSION[$this->key][$i]);
    }

    private function addRow($ar) {
        if (!isset($_SESSION[$this->key])) {
            $_SESSION[$this->key] = [];
        }
        $_SESSION[$this->key][] = $ar;
    }

    private function setAmount($i, $amount) {
        $_SESSION[$this->key][$i]['amount'] = $amount;
    }

    private function getAmount($i) {
        return $_SESSION[$this->key][$i]['amount'];
    }
    */
    //

    public function __construct($db) {
        $this->db = $db;
        $ids = [];
        foreach($this->get() as $i=>$v) {
            $ids[] = $v['id'];
        }
        if ($ids) {
            $rows = $this->db->find('oc_product')->where('product_id', 'IN', $ids)->rows();
            $p = [];
            foreach($rows as $v) {
                $p[$v['product_id']] = ['name' => $v['model'], 'price' => $v['price']];
            }
            $products = [];
            foreach($this->get() as $i=>$v) {
                if (!isset($p[$v['id']])) {
                    $this->deleteRow($i);
                } else {
                    $products[] = [
                        'id' =>$v['id'],
                        'name' => $p[$v['id']]['name'],
                        'price' => $p[$v['id']]['price'],
                        'amount' =>$v['amount'],
                    ];
                    $this->sum += $p[$v['id']]['price'] * $v['amount'];
                    $this->count++;
                }
            }

            $this->products = $products;
        }
    }


    public function remove($id) {
        foreach($this->get() as $i=>$v) {
            if ($v['id'] == $id) {
                $this->deleteRow($i);
            }
        }
    }

    public function add($id) {
        if (!is_numeric($id)) return;
        $product = $this->db->find('oc_product')->where('product_id','=', $id)->limit(1)->one();
        if (!$product) return;
        $f = false;
        foreach($this->get() as $i=>$v) {
            if ($v['id'] == $id) {
                $this->setAmount($i, $this->getAmount($i) + 1);
                $f = true;
            }
        }
        if (!$f) {
            $this->addRow(['id' => $id, 'amount' =>1]);
        }
    }

}


class CartSession extends Cart {
    protected $key = 'cart';

    public function isEmpty() {
        return (!isset($_SESSION[$this->key]) || !$_SESSION[$this->key]);
    }

    protected function get() {
        if ($this->isEmpty()) return [];
        return $_SESSION[$this->key];
    }

    public function clear() {
        unset($_SESSION[$this->key]);
    }

    protected function deleteRow($i) {
        unset($_SESSION[$this->key][$i]);
    }

    protected function addRow($ar) {
        if (!isset($_SESSION[$this->key])) {
            $_SESSION[$this->key] = [];
        }
        $_SESSION[$this->key][] = $ar;
    }

    protected function setAmount($i, $amount) {
        $_SESSION[$this->key][$i]['amount'] = $amount;
    }

    protected function getAmount($i) {
        return $_SESSION[$this->key][$i]['amount'];
    }
}

class CartCookies extends Cart {
    protected $key = 'cart';

    protected $rows = [];

    public function save() {
        setcookie($this->key, serialize($this->rows), time()  + 60 * 60 * 5);
    }

    public function isEmpty() {
        return (!isset($_COOKIE[$this->key]));
    }

    protected function get() {
        if ($this->isEmpty()) return [];
        if (!$this->rows) {
            $this->rows = unserialize($_COOKIE[$this->key]);
        }
        return $this->rows;
    }

    public function clear() {
        setcookie($this->key, '', time() - 3600);
    }

    protected function deleteRow($i) {
        unset($this->rows[$i]);
        if (!$this->rows) {
            $this->clear();
        } else {
            $this->save();
        }
    }

    protected function addRow($ar) {
        $this->rows[] = $ar;
        $this->save();
    }

    protected function setAmount($i, $amount) {
        $this->rows[$i]['amount'] = $amount;
        $this->save();
    }

    protected function getAmount($i) {
        return $this->rows[$i]['amount'];
    }
}
