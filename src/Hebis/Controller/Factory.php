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

namespace Hebis\Controller;

use Zend\Di\ServiceLocator;
use Zend\ServiceManager\ServiceManager;

/**
 * Class Factory
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
class Factory
{

    /**
     * Construct the Static Pages administrator controller
     *
     * @param ServiceManager $sm
     * @return PageAdminController
     */
    public static function getPageAdminController(ServiceManager $sm)
    {
        $table = $sm->getServiceLocator()->get('VuFind\DbTablePluginManager')
            ->get('static_post');
        $translator = $sm->getServiceLocator()->get('VuFind\Translator');

        return new PageAdminController($table, $translator);
    }


    /**
     * @param ServiceManager $sm
     * @return PageController
     */
    public static function getPageController(ServiceManager $sm)
    {
        $table = $sm->getServiceLocator()->get('VuFind\DbTablePluginManager')
            ->get('static_post');
        $translator = $sm->getServiceLocator()->get('VuFind\Translator');
        $pageController = new PageController($table, $translator);
        return $pageController;
    }

    /**
     * @param ServiceManager $sm
     * @param ServiceLocator $sl
     * @return BroadcastAdminController
     */
    public static function getBroadcastAdminController(ServiceManager $sm)
    {
        $table = $sm->getServiceLocator()->get('VuFind\DbTablePluginManager')
            ->get('broadcasts');
        $translator = $sm->getServiceLocator()->get('VuFind\Translator');
        return new BroadcastAdminController($table, $translator);
    }

    /**
     * @param ServiceManager $sm
     * @return BroadcastController
     */
    public static function getBroadcastController(ServiceManager $sm)
    {
        $table = $sm->getServiceLocator()->get('VuFind\DbTablePluginManager')
            ->get('broadcasts');
        $translator = $sm->getServiceLocator()->get('VuFind\Translator');
        return new BroadcastController($table, $translator);
    }

    /**
     * Construct the FlashMessenger plugin.
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return \Hebis\Controller\OAuthController
     */
    public static function getOAuth(ServiceManager $sm)
    {
        $oauthController = new \Hebis\Controller\OAuthController();
        $oauthController->setServiceLocator($sm->getServiceLocator());
        $oauthController->init();
        return $oauthController;

    }


    public static function getRecordFinder(ServiceManager $sm)
    {
        return new \Hebis\Controller\RecordFinderController(
            $sm->getServiceLocator()->get('VuFind\Config')->get('config')
        );
    }


    public static function getXisbn(ServiceManager $sm)
    {
        $ajaxController = new XisbnController();
        $ajaxController->setServiceLocator($sm->getServiceLocator());
        $ajaxController->init();
        return $ajaxController;
    }

    /**
     * Construct the RecordController.
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return RecordController
     */
    public static function getRecordController(ServiceManager $sm)
    {
        return new RecordController(
            $sm->getServiceLocator()->get('VuFind\Config')->get('config')
        );
    }

}
