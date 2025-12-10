<?php

namespace DhlAssistant\Traits;

use DhlAssistant\Core\Models;
use DhlAssistant\Wrappers;

/**
 *
 */
trait ControllerPaginationPreparer
{
    /**
     * @param $iTotalResults
     * @param array $aAddtionalParams
     * @return array
     */
    public function PreparePagination($iTotalResults, $aAddtionalParams = array())
    {
        $per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 20;

        if ($per_page < 20) {
            $per_page = 20;
        }

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

        if ($page < 1) {
            $page = 1;
        }

        $total_results = $iTotalResults;
        $total_pages = ceil($total_results / $per_page);

        if ($total_pages < 1) {
            $total_pages = 1;
        }

        if ($page > $total_pages) {
            $page = $total_pages;
        }

        return [
            'Page' => $page,
            'TotalPages' => $total_pages,
            'TotalResults' => $total_results,
            'PerPage' => $per_page,
            'AdditionalParams' => $aAddtionalParams,
            'Controller' => $this,
        ];
    }
}
