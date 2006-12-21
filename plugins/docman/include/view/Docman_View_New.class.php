<?php

/**
* Copyright (c) Xerox Corporation, CodeX Team, 2001-2005. All rights reserved
* 
* $Id$
*
* Docman_View_New
*/

require_once('Docman_View_Display.class.php');
require_once('Docman_View_ParentsTree.class.php');
require_once('Docman_View_PermissionsForItem.class.php');

/* abstract */ class Docman_View_New extends Docman_View_Display /* implements Visitor */ {
    
    /* protected abstract */ function _getEnctype() {
    }
    
    /* protected abstract */ function _getAction() {
    }
    
    /* protected abstract */ function _getActionText() {
    }
    
    /* protected abstract */ function _getForm() {
    }

    /* protected */ function _getSpecificProperties($params) {
        return '';
    }

    /* protected */ function _getCategories($params) {
        return '';
    }
    /* protected */ function _getJSDocmanParameters($params) {
        $doc_params = array();
        if (isset($params['force_permissions'])) {
            $doc_params['newItem'] = array(
                'hide_permissions'           => !$params['display_permissions'],
                'update_permissions_on_init' => false,
                'default_position'           => $params['force_ordering']
            );
        }
        return array_merge(
            parent::_getJSDocmanParameters($params),
            $doc_params
        );
    }

    function _content($params) {
        $params['form_name'] = 'new_item';

        $html  = '<br />';
        $html .= '<form name="'.$params['form_name'].'" id="docman_new_form" action="'. $params['default_url'] .'" method="POST" '. $this->_getEnctype() .'>';                

        $html .= '<div class="docman_new_item">'."\n";

        //{{{ General Properties
        $html .= '<div class="properties">'."\n";
        $html .= '<fieldset class="general_properties"><legend>'. $GLOBALS['Language']->getText('plugin_docman', 'new_generalproperties') . help_button('DocumentManager') .'</legend>';
        $html .= $this->_getGeneralProperties($params);
        $html .= '<p><span class="highlight"><em>'.$GLOBALS['Language']->getText('plugin_docman', 'new_mandatory_help').'</em></span></p>';
        $html .= '<input type="hidden" name="action" value="'. $this->_getAction() .'" />';        
        $html .= '</fieldset>';
        $html .= '</div>';
        //}}}
        
        //{{{ Specific Properties
        $specific = $this->_getSpecificProperties($params);
        if (trim($specific)) {
            $html .= '<fieldset class="specific_properties"><legend>'. $GLOBALS['Language']->getText('plugin_docman', 'new_specificproperties') . help_button('DocumentManager') .'</legend>';
            $html .= $specific;
            $html .= '</fieldset>';
        }
        //}}}
        
        //{{{ Location
        $html .= '<fieldset class="location"><legend>'. $GLOBALS['Language']->getText('plugin_docman', 'new_location') . help_button('DocumentManager') .'</legend>';
        
        $potential_parent_id = isset($params['force_item']) ? $params['force_item']->getParentId() : $params['item']->getId();
        $potential_parent_id = $this->_controller->userCanWrite($potential_parent_id) ? $potential_parent_id : $params['hierarchy']->getId();
        
        $parents_tree =& new Docman_View_ParentsTree($this->_controller);
        $html .= $parents_tree->fetch(array(
            'docman_icons' => $this->_getDocmanIcons($params),
            'current'      => $potential_parent_id,
            'hierarchy'    => $params['hierarchy']
        ));
        
        $html .= '</fieldset>';
        //}}}        
        
        //{{{ Permissions
        $html .= '<fieldset><legend>Permissions</legend>';
        $html .= '<div id="docman_new_permissions_panel">';
        $p =& new Docman_View_PermissionsForItem($this->_controller);
        $params['user_can_manage'] = $this->_controller->userCanWrite($potential_parent_id);
        $html .= $p->fetch($potential_parent_id, $params);
        $html .= '</div>';
        $html .= '</fieldset>';
        //}}}
        
        $html .= '<div class="docman_new_submit">'."\n";
        $html .= '<input type="submit" value="'. $this->_getActionText() .'" />';
        $html .= '</div>'."\n";

        $html .= '</div>'."\n"; // "docman_new_item"

        $html .= '</form>';
        echo $html;
    }
}

?>
