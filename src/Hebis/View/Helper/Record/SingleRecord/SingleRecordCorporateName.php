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
use Hebis\View\Helper\Record\CorporateNameTrait;
use Hebis\View\Helper\Record\ResultList\ResultListCorporateName;

/**
 * Class SingleRecordCorporateName
 * @package Hebis\View\Helper\Record\SingleRecord
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
class SingleRecordCorporateName extends ResultListCorporateName
{
    use CorporateNameTrait;

    public function __invoke(SolrMarc $record, $asArray = false)
    {
        if ($asArray === true) {
            $arr = $this->getCorporateName($record, true);
            return $arr;
        }
        $arr = $this->getCorporateName($record, false);
        return implode("<br />", $arr);
    }

    public function toStringArray($subFields)
    {
        $arr = [];
        /** @var \File_MARC_Subfield $subfield */
        foreach ($subFields as $subfield) {
            $arr[] = htmlentities($subfield->getData());
        }
        return $arr;
    }
}
