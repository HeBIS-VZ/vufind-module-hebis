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

namespace HebisTest\View\Helper;

use Hebis\RecordDriver\SolrMarc;
use Zend\Http\Client;

/**
 * Trait TestRecordFromIndexTrait
 * @package HebisTest\View\Helper
 */
trait TestRecordFromIndexTrait
{

    /**
     * @param $ppn
     * @return SolrMarc|null
     * @throws \HttpException
     */
    protected function getRecordFromIndex($ppn)
    {
        $url = SOLR_HOST_TEST . '/solr/hebis/select?wt=json&q=id:HEB' . $ppn;
        $client = new Client($url, array(
            'maxredirects' => 3,
            'timeout' => 10
        ));
        $response = $client->send();

        if ($response->getStatusCode() > 299) {
            throw new \HttpException("Status code " . $response->getStatusCode() . " for $url.");
        }
        $jsonString = trim($response->getBody());
        $jsonObject = json_decode($jsonString, true);
        $marcObject = new SolrMarc();

        if ($jsonObject['response']['numFound'] < 1) {
            return null;
        }

        try {
            $marcObject->setRawData($jsonObject['response']['docs'][0]);
        } catch (\File_MARC_Exception $e) {
            echo "Record HEB$ppn: " . $e->getMessage() . "\n";
            return null;
        }
        return $marcObject;
    }
}