<?php

/*
 * This file is a part of HDS (HeBIS Discovery System). HDS is an
 * extension of the open source library search engine VuFind, that
 * allows users to search and browse beyond resources. More
 * Information about VuFind you will find on http://www.vufind.org
 *
 * Copyright (C) 2016
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

namespace Hebis\View\Helper\Record\SingleRecord;

use Hebis\View\Helper\FieldArray;
use Hebis\RecordDriver\SolrMarc;
use Hebis\View\Helper\Record\AbstractRecordViewHelper;
use Hebis\Marc\Helper;

/**
 * Class SingleRecordTitle
 *
 * ! Achtung Ausnahme für Trennzeichen vor $b:
 * Ist das erste Zeichen in $b ein Gleichheitszeichen (=), entfällt der Doppelpunkt vor $b (K 20.8., erl. von Oliver auf fantasio: ü)
 * Suchlink:
 * Der Inhalt von 245 $a soll zu einer Suche in title_lc_phrase verlinkt werden ü
 * Anm.:
 * War ungelabelte Zeile unterhalb des Titels aus 245 $a; Apassungswunsch der Piloten:
 * - Nur Marc 245 $a als Überschrift anzeigen, s. Zeile 3.
 * - Zusätzlich gelabeltes Feld "Titel", darin Wiederholung von Mac 245 $a + Anzeige der restlichen Subfelder  wie bereits umgesetzt.
 *
 *
 * @package Hebis\View\Helper\Record
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
class SingleRecordTitleStatement extends AbstractRecordViewHelper
{

    use FieldArray;

    public function __invoke(SolrMarc $record)
    {
        /** @var \File_MARC_Record $marcRecord */
        $marcRecord = $record->getMarcRecord();

        //$fields245 = $marcRecord->getFields();//$this->getFieldArray($marcRecord, '245', ['a', 'h', 'b', 'c'], false);

        /** @var \File_MARC_Data_Field $field */
        $field = $marcRecord->getField('245');

        $a = trim(Helper::getSubFieldDataOfGivenField($field, 'a'));
        $a = $this->removeSpecialChars($a);
        $b = Helper::getSubFieldDataOfGivenField($field, 'b');
        $c = Helper::getSubFieldDataOfGivenField($field, 'c');
        $h = Helper::getSubFieldDataOfGivenField($field, 'h');

        /* setup colon */
        $colon = " :";
        if (is_string($b) && substr($b, 0, 1) === "=") {
            $colon = "";
        } else {
            if (!$b) {
                $colon = "";
            }
        }


        $text2 = $h ? " $h" : "";
        $text2 .= $colon;
        $text2 .= $b ? " $b" : "";
        $text2 .= $c ? " / $c" : "";

        $ret = ["text"=>$a, "link"=>$a, "text2"=>$text2];
        return $ret;
    }

    /**
     * removes @ at the beginning of the string or an @ where a blank as prefix exist and followed by a word a digit
     * @param $string
     * @return mixed
     */
    public function removeSpecialChars($string)
    {
        $string = preg_replace('/^@/', "", $string);
        return preg_replace('/\s\@([\w\däöü])/i', " $1", $string);
    }


    protected function titleSearchLink($title)
    {
        // $url = $this->getView()->recordLink()->getActionUrl("Myyresearch", "home");

        $searchTitle = html_entity_decode($title);
        $href = parent::URL_FULL_TITLE_SEARCH_PATTERN . urlencode(trim($searchTitle)) . parent::URL_FULL_TITLE_SEARCH_PATTERN_SUFFIX;
        return $this->generateLink($href, $title, $title);
    }
}