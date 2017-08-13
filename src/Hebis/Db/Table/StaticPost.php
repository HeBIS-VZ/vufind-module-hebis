<?php

namespace Hebis\Db\Table;

use VuFind\Db\Table\Gateway;

class StaticPost extends Gateway
{

    public function __construct($rowClass = 'VuFind\Db\Row\StaticPost'
    )
    {
        parent::__construct('static_post', $rowClass);
    }

    public function deletePost($id)
    {
        $this->delete(array('id' => (int)$id));
    }

    public function getAll()
    {
        return $this->select();
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function getPost($id)
    {
        $staticPostRow = $this->select(['id' => $id])->current();

        if (!$staticPostRow) {
            throw new \Exception("Could not find post $id");
        }

        return $staticPostRow;
    }

}