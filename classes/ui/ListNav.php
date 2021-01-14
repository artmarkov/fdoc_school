<?php

namespace main\ui;

class ListNav extends Element
{

    public static function create()
    {
        return new static();
    }

    public function render($page, $total, $size, $url)
    {
        if ($total == 0) {
            $page = 0;
        }
        return parent::renderView('listnav.php', array(
            'startLink' => $page > 0 ? $url . '?set_page=0' : '#',
            'prevLink' => $page >= 1 ? $url . '?set_page=' . ($page - 1) : '#',
            'nextLink' => $page < ceil($total/$size)-1 ? $url . '?set_page=' . ($page + 1) : '#',
            'endLink' => $page < ceil($total/$size)-1 ? $url . '?set_page=' . (ceil($total/$size)-1) : '#',
            'text' => ($page * $size + 1) . ' - ' . (($page + 1) * $size > $total ? $total : ($page + 1) * $size) . ' (' . $total . ' всего)'
        ));
    }

}
