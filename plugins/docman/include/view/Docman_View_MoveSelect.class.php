<?php

/**
* Copyright (c) Xerox Corporation, CodeX Team, 2001-2005. All rights reserved
* 
* $Id$
*
* Docman_View_Move
*/

require_once('Docman_View_Details.class.php');

require_once('Docman_View_ItemDetailsSectionMoveSelect.class.php');


class Docman_View_MoveSelect extends Docman_View_Details {
    
    function _getTitle($params) {
        return $GLOBALS['Language']->getText('plugin_docman', 'move', $params['item_to_move']->getTitle());
    }
    
    function _content($params) {
        parent::_content(
            $params, 
            new Docman_View_ItemDetailsSectionMoveSelect(
                $params['item_to_move'],
                $params['item'],
                $params['default_url'], 
                $this->_controller, 
                array_merge(
                    array('docman_icons' => $this->_getDocmanIcons($params)),
                    $params
                )
            ), 
            'actions'
        );
        
    }
}

?>
