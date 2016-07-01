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

namespace Hebis\RecordDriver;

use HAB\Pica\Parser\PicaPlainParser;
use HAB\Pica\Record\CopyRecord;
use HAB\Pica\Record\Field;
use HAB\Pica\Record\LocalRecord;
use HAB\Pica\Record\Record;
use HAB\Pica\Record\TitleRecord;

class PicaRecordParser
{

    /**
     * @var PicaRecordParser
     */
    private static $instance;


    /**
     * @var PicaRecord $picaRecord
     */
    protected $picaRecord;


    /**
     * @return PicaRecordParser
     */
    public static function getInstance()
    {
        if (self::$instance == null) {

            self::$instance = new PicaRecordParser();
        }

        return self::$instance;
    }

    /**
     * @see PicaRecordParser::getInstance
     * PicaRecordParser constructor.
     */
    private function __construct() { }

    /**
     *
     * @param string $rawPicaRecord
     * @return $this
     */
    public function parse($rawPicaRecord)
    {

        /*
         * split raw record in its levels (raw)
         */
        $recordLevels = explode("\n\n", $rawPicaRecord);

        $level = null;

        foreach ($recordLevels as $level) {

            /*
             * find record type
             */

            $recordLevel = null;
            $match = [];
            if (preg_match('/^(alg|lok|exp)/', $level, $match)) {
                $recordLevel = $match[1];
                //$ipn = $match[2];
            }

            /*
             * split level in its fields
             */
            $fieldsArray = explode("\n", $level);

            /*
             * parse lines to Pica Fields
             */
            $rawFields = array_slice($fieldsArray, 1, count($fieldsArray)-1);
            $record['fields'] = [];
            foreach ($rawFields as $rawField) {
                $record['fields'][] = PicaPlainParser::parseField($rawField);
            }

            if ($recordLevel === "alg") {

                /** @var TitleRecord $titleRecord */
                $this->picaRecord = PicaRecordFactory::factory($record); //level0 records via Record factory

            } else {
                $fieldObjectsArray = [];

                foreach ($record['fields'] as $field) {
                    $fieldObjectsArray[] = Field::factory($field); //parse each field separate
                }

                switch ($recordLevel) {
                    case 'lok':
                        $localRecord = new LocalRecord();
                        $localRecord->setFields($fieldObjectsArray);
                        $this->picaRecord->addLocalRecord($localRecord);
                        break;
                    case 'exp':
                        $copyRecord = new CopyRecord();
                        $copyRecord->setFields($fieldObjectsArray);
                        $this->picaRecord->getLocalRecords()[count($this->picaRecord->getLocalRecords())-1]
                            ->addCopyRecord($copyRecord);
                        break;
                    default:
                        //TODO: there are other level?
                }

            }
        }

        return $this;
    }

    public function getRecord()
    {
        return $this->picaRecord;
    }
}
