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

namespace Hebis\View\Helper\Record\ResultList;

use Hebis\View\Helper\Record\AbstractRecordViewHelper;
use Hebis\RecordDriver\SolrMarc;


/**
 * Class ResultListEditionStatement
 * @package Hebis\View\Helper\Record
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
class ResultListEditionStatement extends AbstractRecordViewHelper
{
    /**
     *
     * @param SolrMarc $record
     * @return string
     */
    public function __invoke(SolrMarc $record)
    {
        $ret = "";
        $_533_n = false;
        /** @var \File_MARC_Record $marcRecord */
        $marcRecord = $record->getMarcRecord();

        $_533_ = $marcRecord->getFields('533');

        if (!empty($_533_)) {
            /** @var \File_MARC_Data_Field $_533 */
            $_533 = current($_533_);
            $n_ = $_533->getSubfields('n');
            if (!empty($n_)) {
                $_533_n = end($n_)->getData();
            }
        }

        $_250_a = $this->generate250aContent($marcRecord);

        return $_533_n ? $_533_n : (!empty($_250_a) ? $_250_a : "");

    }

    /**
     * @param \File_MARC_Record $marcRecord
     * @return string
     */
    public function generate250aContent(\File_MARC_Record $marcRecord)
    {
        /** @var \File_MARC_Data_Field $_250 */
        $_250 = $marcRecord->getField('250');
        return !empty($_250) ? (!empty($a = $_250->getSubfield("a")) ? $a->getData() : "") : "";
    }


}