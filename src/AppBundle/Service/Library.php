<?php
/**
 * Created by PhpStorm.
 * User: Léo
 * Date: 21/01/2018
 * Time: 17:17
 */

namespace AppBundle\Service;

use AppBundle\Entity\Announce;

class Library
{
    /**
     * Check if one date is after another (string dates)
     *
     * @param $announce
     * @param $dateStart
     * @param $dateEnd
     * @return bool
     */
    public function canApplyDates(Announce $announce, $dateStart, $dateEnd)
    {
        $announceStart = $this->dateStringToInt($announce->getStartDate());
        $announceEnd = $this->dateStringToInt($announce->getEndDate());
        $start = $this->dateStringToInt($dateStart);
        $end = $this->dateStringToInt($dateEnd);

        return $start >= $announceStart && $end <= $announceEnd;
    }

    /**
     * Return a date string 'dd/mm/yyyy' to an int 'yyyymmdd'
     * @param $date
     * @return int
     */
    public function dateStringToInt($date)
    {
        $dateArray = explode('/', $date);
        return intval($dateArray[2].$dateArray[1].$dateArray[0]);
    }
}
