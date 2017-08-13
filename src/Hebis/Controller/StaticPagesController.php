<?php


namespace Hebis\Controller;

use VuFindAdmin\Controller\AbstractAdmin;


/**
 * Class to manage static pages
 *
 * @package Hebis\Controller
 * @author Roshak Zarhoun <roshak.zarhoun@stud.tu-darmstadt.de>
 */
class StaticPagesController extends AbstractAdmin
{


    /**
     * Static Pages Administrator Home View
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function homeAction()
    {

        $view = $this->createViewModel();
        $view->setTemplate('staticpages/home');
        $table = $this->getTable('static_post');

        $view->rows = $table->getAll();

        return $view;
    }

    public function viewAction($id)
    {
        $view = $this->createViewModel();
        $view->setTemplate('staticpages/view');
        $table = $this->getTable('static_post');
        $row = $table->getPost($id);
        $view->row = $row;

        return $view;
    }

    /**
     * @param $params
     * @return \Zend\View\Model\ViewModel
     */
    public function addPageAction()
    {

        $view = $this->createViewModel();
        $view->setTemplate('staticpages/add');


        return $view;
    }

    public function editPageAction()
    {
        $view = $this->createViewModel();
        $view->setTemplate('staticpages/edit');

        return $view;
    }

    public function deletePageAction()
    {
        $view = $this->createViewModel();
        $view->setTemplate('staticpages/delete');

        return $view;

    }

}