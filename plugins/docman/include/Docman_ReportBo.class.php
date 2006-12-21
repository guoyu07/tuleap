<?php
/**
 * Copyright � STMicroelectronics, 2006. All Rights Reserved.
 *
 * Originally written by Manuel Vacelet, 2006
 *
 * This file is a part of CodeX.
 *
 * CodeX is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * CodeX is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with CodeX; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * $Id$
 */

require_once('Docman_FilterBo.class.php');

class Docman_Report {
    var $filters;

    function Docman_Report() {
        $this->filters = array();
    }

    function addFilter(&$f) {
        $this->filters[] =& $f;
    }

    function &getFilterIterator() {
        $i = new ArrayIterator($this->filters);
        return $i;
    }
}

class Docman_ReportBo {
    var $groupId;

    function Docman_ReportBo($groupId) {
        $this->groupId = $groupId;
    }

    function &get($id) {
        $report = null;
        if($id == 'Table') {
            $report = new Docman_Report();
            $filterBo = new Docman_FilterBo();        

            $mdFactory = new Docman_MetadataFactory($this->groupId);
            $mdIter = $mdFactory->getMetadataForGroup(true);
            $mdIter->rewind();
            while($mdIter->valid()) {
                $md = $mdIter->current();

                $filter = $mdFactory->getFilterFromLabel($md->getLabel());
                if($filter !== null) {
                    $report->addFilter($filter);
                }
                unset($filter);

                $mdIter->next();
            }
        }
        return $report;
    }
    
    function setup(&$report, &$request, $groupId, &$feedback) {
        $validateFilterFactory  = new Docman_ValidateFilterFactory();

        $needDefaultSort = true;
        $updateDateFilter = null;

        $fi =& $report->getFilterIterator();
        $fi->rewind();
        while($fi->valid()) {
            $filter =& $fi->current();

            $filter->initFromRequest($request);
                
            // Validate submitted paramters
            $validateFilter =& $validateFilterFactory->getFromFilter($filter);
            if($validateFilter !== null) {
                if(!$validateFilter->validate()) {
                    $feedback->log('error', $validateFilter->getMessage());
                }
            }
            
            if($filter->getSort() !== null) {
                $needDefaultSort = false;
            }

            // Keep a ref on update date (will be default sort) if needed.
            if($filter->md->getLabel() == 'update_date') {
                $updateDateFilter =& $filter; 
            }
            
            $fi->next();
        }

        if($needDefaultSort && $updateDateFilter !== null) {
            $updateDateFilter->setSort(0);
        }
    }
}

?>
