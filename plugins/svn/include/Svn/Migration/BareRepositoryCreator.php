<?php
/**
 * Copyright (c) Enalean, 2018. All Rights Reserved.
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Tuleap\Svn\Migration;

use Backend;
use PFUser;
use Tuleap\Svn\AccessControl\AccessFileHistoryCreator;
use Tuleap\Svn\Repository\Exception\RepositoryNameIsInvalidException;
use Tuleap\Svn\Repository\Repository;
use Tuleap\Svn\Repository\RepositoryCreator;
use Tuleap\Svn\Repository\RepositoryManager;
use UserManager;

class BareRepositoryCreator
{
    /**
     * @var RepositoryCreator
     */
    private $repository_creator;
    /**
     * @var AccessFileHistoryCreator
     */
    private $access_file_history_creator;
    /**
     * @var RepositoryManager
     */
    private $repository_manager;
    /**
     * @var UserManager
     */
    private $user_manager;
    /**
     * @var Backend
     */
    private $backend_svn;
    /**
     * @var Backend
     */
    private $backend_system;
    /**
     * @var RepositoryCopier
     */
    private $repository_copier;

    public function __construct(
        RepositoryCreator $repository_creator,
        AccessFileHistoryCreator $access_file_history_creator,
        RepositoryManager $repository_manager,
        UserManager $user_manager,
        \BackendSVN $backend_svn,
        \BackendSystem $backend_system,
        RepositoryCopier $repository_copier
    ) {
        $this->repository_creator          = $repository_creator;
        $this->access_file_history_creator = $access_file_history_creator;
        $this->repository_manager          = $repository_manager;
        $this->user_manager                = $user_manager;
        $this->backend_svn                 = $backend_svn;
        $this->backend_system              = $backend_system;
        $this->repository_copier           = $repository_copier;
    }

    public function create(Repository $repository, PFUser $user)
    {
        try {
            $copy_from_core = true;
            $system_event   = $this->repository_creator->createWithoutUserAdminCheck(
                $repository,
                $user,
                $copy_from_core
            );
        } catch (RepositoryNameIsInvalidException $e) {
            throw new SvnMigratorException("Repository name is already used in this project.");
        }
        if (! $system_event) {
            throw new SvnMigratorException("Could not create system event.");
        }

        $system_event->injectDependencies(
            $this->access_file_history_creator,
            $this->repository_manager,
            $this->user_manager,
            $this->backend_svn,
            $this->backend_system,
            $this->repository_copier
        );
    }
}
