<?php
namespace Hebis\Module\Configuration;

use Zend\ServiceManager\ServiceManager;

$config = [

    'vufind' => [
        'plugin_managers' => [
            'recorddriver' => [
                'factories' => [
                    'solrmarc' => 'Hebis\RecordDriver\Factory::getSolrMarc',
                ]
            ],
            'db_table' => [
                'abstract_factories' => ['VuFind\Db\Table\PluginFactory'],
                'factories' => [
                    'user_oauth' => 'Hebis\Db\Table\Factory::getUserOAuth'
                ]
            ],
            'ils_driver' => [

                'factories' => [
                    'hebis' => 'Hebis\ILS\Driver\Factory::getHebis',
                ]
            ],
            'autocomplete' => [
                'factories' => [
                    'solrterms' => 'Hebis\Autocomplete\Factory::getTerms',
                ]
            ],
            'search_results' => [
                'abstract_factories' => ['Hebis\Search\Results\PluginFactory'],
                'factories' => [
                    'favorites' => 'VuFind\Search\Results\Factory::getFavorites',
                    'solr' => 'Hebis\Search\Results\Factory::getSolr',
                ],
            ],
        ],

    ],
    'service_manager' => [
        'factories' => [
            'VuFind\Translator' => 'Hebis\Service\Factory::getTranslator',
            'VuFind\RecordDriverPluginManager' => 'Hebis\RecordDriver\Factory::getRecordDriverPluginManager',
            'Zend\Session\SessionManager' => 'Zend\Session\Service\SessionManagerFactory',
            'Zend\Session\Config\ConfigInterface' => 'Zend\Session\Service\SessionConfigFactory',
            'VuFind\WorldCatUtils' => 'Hebis\Service\Factory::getWorldCatUtils',
            'VuFind\AutocompletePluginManager' => 'VuFind\Service\Factory::getAutocompletePluginManager',
            'VuFind\SearchResultsPluginManager' => 'VuFind\Service\Factory::getSearchResultsPluginManager',
        ],
        'invokables' => [
            'VuFind\Terms' => 'Hebis\Search\Service',
        ]
    ],

    'session_config' => [
        'remember_me_seconds' => 2419200,
        'use_cookies' => true,
        'cookie_httponly' => true,
    ],

    'controllers' => [
        'factories' => [
            'OAuth' => 'Hebis\Controller\Factory::getOAuth',
            'recordfinder' => 'Hebis\Controller\Factory::getRecordFinder',
            'Xisbn' => 'Hebis\Controller\Factory::getXisbn',
        ],
        'invokables' => [
            'my-research' => 'Hebis\Controller\MyResearchController',
            'search' => 'Hebis\Controller\SearchController',
        ]
    ],
    'router' => [
        'routes' => [
            'oauth' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route'    => '/oauth/callback[/]',
                    'defaults' => [
                        'controller' => 'OAuth',
                        'action'     => 'Callback',
                   ],
                ],
            ],
            'oauth-token-renew' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route'    => '/oauth/renew[/]',
                    'defaults' => [
                        'controller'    => 'OAuth',
                        'action'        => 'renew'
                    ]
                ]
            ],
            'ajax-xisbn' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route'    => '/xisbn/xid[/]',
                    'defaults' => [
                        'controller'    => 'Xisbn',
                        'action'        => 'xid'
                    ]
                ]
            ]
        ],
    ],
];

$recordRoutes = ['recordfinder' => 'RecordFinder'];
//$ajaxRoutes = ['AJAX/XISBN'];

$routeGenerator = new \VuFind\Route\RouteGenerator();
$routeGenerator->addRecordRoutes($config, $recordRoutes);
//$routeGenerator->addStaticRoute($config, $ajaxRoutes);

return $config;
