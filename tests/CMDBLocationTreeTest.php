<?php

/**
 * Copyright (C) 2016-18 Benjamin Heisig
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Benjamin Heisig <https://benjamin.heisig.name/>
 * @copyright Copyright (C) 2016-18 Benjamin Heisig
 * @license http://www.gnu.org/licenses/agpl-3.0 GNU Affero General Public License (AGPL)
 * @link https://github.com/bheisig/i-doit-api-client-php
 */

use bheisig\idoitapi\CMDBLocationTree;

class CMDBLocationTreeTest extends BaseTest {

    /**
     * @var \bheisig\idoitapi\CMDBLocationTree
     */
    protected $locationTree;

    /**
     * @throws \Exception on error
     */
    public function setUp() {
        parent::setUp();

        $this->locationTree = new CMDBLocationTree($this->api);
    }

    /**
     * @throws \Exception on error
     */
    public function testRead() {
        $result = $this->locationTree->read($this->getRootLocation());

        $this->assertInternalType('array', $result);
    }

    /**
     * @throws \Exception on error
     */
    public function testReadRecursively() {
        $result = $this->locationTree->readRecursively($this->getRootLocation());

        $this->assertInternalType('array', $result);
    }

}
