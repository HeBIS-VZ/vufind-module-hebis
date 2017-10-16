<?php


namespace Hebis\Controller;

use Hebis\Db\Table\StaticPost;
use VuFind\Date\Converter;
use VuFind\I18n\Translator\TranslatorAwareTrait;
use VuFindAdmin\Controller\AbstractAdmin;


/**
 * Class to manage static pages
 *
 * @package Hebis\Controller
 * @author Roshak Zarhoun <roshakz@gmail.com>
 */
class StaticPagesController extends AbstractAdmin
{
    use TranslatorAwareTrait;

    // define some status constants
    const STATUS_OK = 'OK';                  // good
    const STATUS_ERROR = 'ERROR';            // bad
    const STATUS_NEED_AUTH = 'NEED_AUTH';    // must login first

    protected $table;

    protected $outputMode;

    public function __construct(StaticPost $table, $translator)
    {
        $this->table = $table;
        $this->setTranslator($translator);
    }


    /** Staticpages home view for users
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
     * Static Pages Administrator Home View
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function listAction()
    {
        $view = $this->createViewModel();
        $view->setTemplate('adminstaticpages/list');
        $view->rows = $this->table->getAll();
        return $view;
    }

    /* helping function for page view */

    /** Returns a view for static page with user template
     * @return \Zend\View\Model\ViewModel
     */
    public function viewAction()
    {
        return $this->pageView('staticpages/viewspage');
    }

    private function pageView($template)
    {
        $view = $this->createViewModel();
        $view->setTemplate($template);
        $id = $this->params()->fromRoute();
        $row = $this->table->getPost($id);
        $visible = $row->visible;
        $view->row = $row;
        $DateConverter = new Converter();       // How to get/set timezone TODO view timezone
        $view->cDate = $DateConverter->convertToDisplayDateAndTime('Y-m-d H:i', $row->createDate, ' ~ ');
        $view->modDate = (isset($row->changeDate)) ? $DateConverter->convertToDisplayDateAndTime('Y-m-d H:i', $row->changeDate, ' ~ ') : '---';
        return $view;
    }

    /** Returns a view for static page with admin template
     * @return \Zend\View\Model\ViewModel
     */
    public function previewAction()
    {
        return $this->pageView('adminstaticpages/view');
    }

    /** Action adds new static page
     * @return \Zend\View\Model\ViewModel
     */
    public function addAction()
    {
        $view = $this->createViewModel();
        $view->setTemplate('adminstaticpages/add');
        $allLanguages = array_slice($this->getConfig()->toArray()['Languages'], 1);
        $view->langs = $allLanguages;
        $sessionManager = $this->getServiceLocator()->get('VuFind\SessionManager');
        $request = $this->getRequest();
        if (!$request->isPost()) {
            return $view;
        }

        $language = $this->params()->fromPost('sp-lang');
        $headline = $this->params()->fromPost('sp-headline');
        $content = $this->params()->fromPost('sp-content');
        $author = $this->params()->fromPost('sp-author');


        for ($i = 0; $i < sizeof($allLanguages); $i++) {
            $this->saveRow($language[$i], $headline[$i], $content[$i], $author);
        }

//        $pid = $this->getLastPid();
//        $view->pid = ++$pid;

        return $this->forwardTo('adminstaticpages', 'list');

        //return $view;
    }


    private function saveRow($language, $headline, $content, $author)
    {
        $row = $this->table->createRow();
        $row->language = $language;
        $row->headline = $headline;
        $row->content = $content;
        $row->author = $author;
        $row->save();
    }


    public function editAction()
    {
        $view = $this->createViewModel();
        $view->setTemplate('adminstaticpages/edit');
        $id = $this->params()->fromRoute();
        $request = $this->getRequest();
        $row = $this->table->getPost($id);
        $view->row = $row;

        if (!$request->isPost()) {
            return $view;
        }

        $row->headline = $this->params()->fromPost('headline');
        $row->content = $this->params()->fromPost('content');
        $row->changeDate = getdate();
        $row->save();

        return $this->forwardTo('adminstaticpages', 'home');
    }

    public function deleteAjax()
    {
        try {
            $id = $this->params()->fromRoute('id');
            $row = $this->table->getPost($id);
            $row->delete();
        } catch (\Exception $e) {
            return $this->output(0, self::STATUS_ERROR . '\n' . $e->getMessage(), 400);
        }
        return $this->output(1, self::STATUS_OK, 200);
    }

    /*
     * static page ajax delete action
     */

    /**
     * Send output data and exit.
     *
     * @param mixed $data The response data
     * @param string $status Status of the request
     * @param int $httpCode A custom HTTP Status Code
     *
     * @return \Zend\Http\Response
     * @throws \Exception
     */
    protected function output($data, $status, $httpCode = null)
    {
        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $headers->addHeaderLine('Cache-Control', 'no-cache, must-revalidate');
        $headers->addHeaderLine('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT');

        if ($httpCode !== null) {

            $response->setStatusCode($httpCode);
        }
        if ($this->outputMode !== 'json') {
            throw new \Exception('Unsupported output mode: ' . $this->outputMode);
        } else {
            $headers->addHeaderLine('Content-type', 'application/javascript');
            $output = ['data' => $data, 'status' => $status];

            $response->setContent(json_encode($output));
            return $response;
        }
    }

    public function visibleAjax()
    {
        try {
            $id = $this->params()->fromRoute('id');
            $row = $this->table->getPost($id);
            $row->visible == 1 ? $row->visible = 0 : $row->visible = 1;
            $row->save();
        } catch (\Exception $e) {
            $this->output($e->getMessage() . '\n' . 'Change Visibility Failed!', self::STATUS_ERROR, 400);
        }
        $this->layout()->setTemplate('adminstaticpages/list');

        return $this->output($row->visible == 1, self::STATUS_OK, 200);
    }

    public function jsonAction()
    {
        // Set the output mode to JSON:
        $this->outputMode = 'json';

        // Call the method specified by the 'method' parameter; append Ajax to
        // the end to avoid access to arbitrary inappropriate methods.
        $callback = [$this, $this->params()->fromRoute('method') . 'Ajax'];
        if (is_callable($callback)) {
            try {
                return call_user_func($callback);
            } catch (\Exception $e) {
                $debugMsg = ('development' == APPLICATION_ENV)
                    ? ': ' . $e->getMessage() : '';
                return $this->output(
                    $this->translate('An error has occurred') . $debugMsg,
                    self::STATUS_ERROR,
                    500
                );
            }
        } else {
            return $this->output(
                $this->translate('Invalid Method'), self::STATUS_ERROR, 400
            );
        }
    }

    /* checks whether the inputs array has an empty element*/

    private function inputIsEmpty($input)
    {
        if (!is_array($input)) {
            throw new \Exception('Input is not an array');
        }
        foreach ($input as $inputString) {
            if (strlen($inputString) < 1)
                return true;
        }
        return false;
    }

}