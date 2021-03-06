<?php

namespace Hebis\Controller;

use VuFind\Controller\AbstractBase;
use VuFind\I18n\Translator\TranslatorAwareTrait;

class PageController extends AbstractBase
{
    use TranslatorAwareTrait;
    use PageTrait;

    protected $table;

    /**
     * PageController constructor.
     * @param $table
     */
    public function __construct($table, $translator)
    {
        $this->table = $table;
        $this->setTranslator($translator);

    }

    /**
     * Staticpages home view for users
     * @return \Zend\View\Model\ViewModel
     */
    public function homeAction()
    {
        $view = $this->createViewModel();
        $view->setTemplate('staticpages/sp-home');
        $view->rows = $this->table->getAll();
        return $view;
    }

    /**
     * @return \Zend\View\Model\ViewModel
     */
    public function showAction()
    {
        $pid = $this->params()->fromQuery('pid');
        $lang = $this->getTranslatorLocale();
        return $this->prepareViewStaticPages($pid, $lang);
    }


}