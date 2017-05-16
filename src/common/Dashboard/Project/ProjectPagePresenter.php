<?php
/**
 * Copyright (c) Enalean, 2017. All rights reserved
 *
 * This file is a part of Tuleap.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/
 */

namespace Tuleap\Dashboard\Project;

use CSRFSynchronizerToken;
use Tuleap\Dashboard\PagePresenter;

class ProjectPagePresenter extends PagePresenter
{
    /**
     * @var ProjectPresenter
     */
    public $project_presenter;
    /**
     * @var ProjectDashboardPresenter[]
     */
    public $project_dashboards;
    public $has_dashboard;
    public $is_page_read_only;

    public function __construct(
        CSRFSynchronizerToken $csrf,
        $url,
        ProjectPresenter $project_presenter,
        array $project_dashboards,
        $is_page_read_only
    ) {
        parent::__construct($csrf, $url);

        $this->project_presenter  = $project_presenter;
        $this->project_dashboards = $project_dashboards;
        $this->has_dashboard      = count($project_dashboards) > 0;
        $this->is_page_read_only  = $is_page_read_only;
    }
}