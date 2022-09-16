<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Helper;

/**
 * Description of DateHelper
 *
 * @author root
 */
class DateHelper {

    //put your code here
    public static function getMonthFirstLastDate(int $month) {
        $currentYear = date('Y');
        $mnt = $month;
        $firstDay = "01-$mnt-$currentYear";
        $lastDay = date('t-m-Y', strtotime($firstDay));

        $firstDay = date(Helper::PHP_DATE_FORMAT, strtotime($firstDay));
        $lastDay = date(Helper::PHP_DATE_FORMAT, strtotime($lastDay));

        return ['firstDay' => $firstDay, 'lastDay' => $lastDay];
    }

}
