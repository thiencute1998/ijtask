<?php

namespace Module\Dashboard\Traits;

use IjCore\IjDate;

trait Dashboard{
    public static function getWhereCondition($filter){
        $whereCondition = '';
        if (!empty($filter['FromDate']) && !empty($filter['ToDate'])) {
            $fromDate = IjDate::ConvertClientDateToServerDate($filter['FromDate']);
            $toDate = IjDate::ConvertClientDateToServerDate($filter['ToDate']);
            $whereCondition .= "AND A.PeriodFromDate >= $fromDate ";
            $whereCondition .= "AND A.PeriodToDate <= $toDate";
        }
        $whereCondition = trim($whereCondition);
        return $whereCondition;
    }
}
