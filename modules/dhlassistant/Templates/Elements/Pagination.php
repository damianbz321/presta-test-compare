<?php if (!isset($is_template)) {
    die();
} ?>

<?php
//Pagination
$pagination = $aVars['Pagination'];
$page = $pagination['Page'];
$total_pages = $pagination['TotalPages'];
$per_page = $pagination['PerPage'];
$first_page = ($page == 1);
$last_page = ($page == $total_pages);

function GetLink($aPagination, $iPage, $iPerPage = null)
{
    $per_page = ($iPerPage === null ? $aPagination['PerPage'] : $iPerPage);
    $params = array
    (
        'page' => $iPage,
        'per_page' => $iPerPage,
    );
    if (isset($aPagination['AdditionalParams'])
        && is_array($aPagination['AdditionalParams'])
        && $aPagination['AdditionalParams']
    ) {
        $params = array_merge($params, $aPagination['AdditionalParams']);
    }

    return $aPagination['Controller']->GetLink($params);
}

?>
<div class="row">
    <div class="">
        <div class="pagination">
            Wyświetl
            <button data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button">
                <?php echo $per_page; ?>
                <i class="icon-caret-down"></i>
            </button>
            <ul class="dropdown-menu">
                <?php
                $pagination_steps = array(20, 50, 100, 300, 1000);
                foreach ($pagination_steps as $step_per_page) {
                    echo '<li><a data-items="' . $step_per_page . '" class="pagination-items-page" href="' . (GetLink($pagination,
                            1, $step_per_page)) . '">' . $step_per_page . '</a></li>' . "\n";
                }
                ?>
            </ul>
            / <?php echo $pagination['TotalResults']; ?> wynik(i)
        </div>

        <ul class="pagination pull-right">
            <?php
            echo '<li' . ($first_page ? ' class="disabled"' : '') . '><a class="pagination-link" href="' . ($first_page ? 'javascript:void(0);"' : GetLink($pagination,
                    1, $per_page)) . '"><i class="icon-double-angle-left"></i></a></li>' . "\n";
            echo '<li' . ($first_page ? ' class="disabled"' : '') . '><a class="pagination-link" href="' . ($first_page ? 'javascript:void(0);"' : GetLink($pagination,
                    1, $per_page)) . '"><i class="icon-angle-left"></i></a></li>' . "\n";
            if ($page > 3) {
                echo '<li class="disabled"><a href="javascript:void(0);">…</a></li>' . "\n";
            }
            if ($page > 2) {
                echo '<li><a class="pagination-link" href="' . (GetLink($pagination, $page - 2,
                        $per_page)) . '">' . ($page - 2) . '</a></li>' . "\n";
            }
            if ($page > 1) {
                echo '<li><a class="pagination-link" href="' . (GetLink($pagination, $page - 1,
                        $per_page)) . '">' . ($page - 1) . '</a></li>' . "\n";
            }
            echo '<li class="active"><a class="pagination-link" href="javascript:void(0);">' . ($page) . '</a></li>' . "\n";
            if ($total_pages > $page) {
                echo '<li><a class="pagination-link" href="' . (GetLink($pagination, $page + 1,
                        $per_page)) . '">' . ($page + 1) . '</a></li>' . "\n";
            }
            if ($total_pages > $page + 1) {
                echo '<li><a class="pagination-link" href="' . (GetLink($pagination, $page + 2,
                        $per_page)) . '">' . ($page + 2) . '</a></li>' . "\n";
            }
            if ($pagination['TotalPages'] > $page + 2) {
                echo '<li class="disabled"><a href="javascript:void(0);">…</a></li>' . "\n";
            }
            echo '<li' . ($last_page ? ' class="disabled"' : '') . '><a class="pagination-link" href="' . ($last_page ? 'javascript:void(0);"' : GetLink($pagination,
                    $page + 1, $per_page)) . '"><i class="icon-angle-right"></i></a></li>' . "\n";
            echo '<li' . ($last_page ? ' class="disabled"' : '') . '><a class="pagination-link" href="' . ($last_page ? 'javascript:void(0);"' : GetLink($pagination,
                    $total_pages, $per_page)) . '"><i class="icon-double-angle-right"></i></a></li>' . "\n";
            ?>
        </ul>
    </div>
</div>
