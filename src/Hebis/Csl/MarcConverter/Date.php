<?php
/*
 * This file is a part of HDS (HeBIS Discovery System). HDS is an
 * extension of the open source library search engine VuFind, that
 * allows users to search and browse beyond resources. More
 * Information about VuFind you will find on http://www.vufind.org
 *
 * Copyright (C) 2017
 * HeBIS Verbundzentrale des HeBIS-Verbundes
 * Goethe-Universität Frankfurt / Goethe University of Frankfurt
 * http://www.hebis.de
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

namespace Hebis\Csl\MarcConverter;

use Seboettg\CiteData\Csl as Model;

class Date extends Record
{

    /**
     * @param \File_MARC_Record $record
     * @return Model\Date|null
     */
    public static function getIssued(\File_MARC_Record $record)
    {
        $year = self::clearYear(self::getSubfield($record, "264", "c"));


        if (!empty($year)) {
            $date = new Model\Date();
            if (strpos($year, "ca.") !== false || strpos($year, "circa")) {
                $date->setCirca(true);
                if (preg_match("/(\d{4})[^\d]*$/", $year, $match)) {
                    $date->setDateParts([[$match[1]]]);
                } else {
                    $date->setDateParts([[$year]]);
                }
            } else {
                $date->setLiteral($year);
                $date->setDateParts([[$year]]);
            }


            return $date;
        }
        return null;
    }

    private static function clearYear($string)
    {
        if (preg_match("/^[\[\(]?(.*\d{4})[\)\]]?$/", trim($string), $match)) {
            return $match[1];
        }
        return trim($string);
    }
}
