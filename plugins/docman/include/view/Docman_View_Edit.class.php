<?php

/**
* Copyright (c) Xerox Corporation, CodeX Team, 2001-2005. All rights reserved
* 
* $Id$
*
* Docman_View_Edit
*/

require_once('Docman_View_Details.class.php');

require_once('Docman_View_ItemDetailsSectionEditProperties.class.php');

class Docman_View_Edit extends Docman_View_Details {
    
    
    /* protected */ function _getTitle($params) {
        return $GLOBALS['Language']->getText('plugin_docman', 'details_edit_title', $params['item']->getTitle());
    }
    
    /* protected */ function _content($params) {
        $force    = isset($params['force_item']) ? $params['force_item'] : null;
        parent::_content($params, new Docman_View_ItemDetailsSectionEditProperties($params['item'], $params['default_url'], $params['theme_path'], $force), 'properties');
    }
}

?>
