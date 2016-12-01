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
use Hebis\RecordDriver\SolrMarc;
use Hebis\View\Helper\Record\ResultList\ResultListPersonalName;


/**
 * Class SingleRecordPersonalName
 * @package Hebis\View\Helper\Record\SingleRecord
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
class SingleRecordPersonalName extends ResultListPersonalName
{

    public function __invoke(SolrMarc $record)
    {
        $arr = [];
        /** @var \File_MARC_Record $marcRecord */
        $marcRecord = $record->getMarcRecord();
        $aut = $this->getFieldContentsByFieldNo($marcRecord, 100);

        if (!empty($aut)) {
            $arr[] = $aut;
        }

        $f700_ = $marcRecord->getFields(700);
        $filteredFields = $this->filterByIndicator($f700_, 2, " ");

        foreach ($filteredFields as $field) {
            if (empty($field->getSubfields('e'))) {
                $this->appendMissingESubfields($field);
            }
            $addedEntryPN = $this->getFieldContents($field);
            if (!empty($addedEntryPN)) {
                $arr[] = $addedEntryPN;
            }
        }

        return implode("; ", $arr);
    }


    private function appendMissingESubfields(\File_MARC_Data_Field &$field)
    {
        $types = [
            'aut' => 'Verfasser',
            'hnr' => 'Gefeierter',
            'prf' => 'Ausf&uuml;hrender'
        ];

        /** @var \File_MARC_Subfield $_4 */
        foreach ($field->getSubfields('4') as $_4) {
            if (in_array($_4->getData(), array_keys($types))) {
                $field->appendSubfield(new \File_MARC_Subfield("e", $types[$_4->getData()]));
            }
        }
    }
}
