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
use Hebis\View\Helper\Record\AbstractRecordViewHelper;

/**
 * Class SingleRecordInternationalStandardBookNumber
 * @package Hebis\View\Helper\Record
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
class SingleRecordInternationalStandardBookNumber extends AbstractRecordViewHelper
{
    public function __invoke(SolrMarc $record)
    {
        /** @var \File_MARC_Record $marcRecord */
        $marcRecord = $record->getMarcRecord();

        $arr = [];
        $fields = $marcRecord->getFields('020');

        foreach ($fields as $field) {
            if (!empty($a = $this->getSubFieldDataOfGivenField($field, 'a'))) {
                $arr[] = $a;
            }
            if (!empty($z = $this->getSubFieldDataOfGivenField($field, 'z'))) {
                $arr[] = $z;
            }
        }

        $fields = $marcRecord->getFields('776');

        foreach ($fields as $field) {
            if ($field->getIndicator(1) === "1") {
                $z = $this->getSubFieldDataOfGivenField($field, 'z');
                if (!empty($z)) {
                    $arr[] = $z;
                }
            }
        }

        return implode(" ; ", $arr);
    }
}