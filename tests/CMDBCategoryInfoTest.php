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

use bheisig\idoitapi\CMDBCategoryInfo;

class CMDBCategoryInfoTest extends BaseTest {

    /**
     * @var \bheisig\idoitapi\CMDBCategoryInfo
     */
    protected $instance;

    protected $categories = [];

    /**
     * @throws \Exception on error
     */
    public function setUp() {
        parent::setUp();

        $this->instance = new CMDBCategoryInfo($this->api);

        $this->categories = [
            'C__CATG__GLOBAL',
            'C__CATG__IP',
            'C__CATS__PERSON_MASTER'
        ];
    }

    /**
     * @throws \Exception on error
     */
    public function testRead() {
        foreach ($this->categories as $categoryConst) {
            $result = $this->instance->read($categoryConst);

            $this->assertInternalType('array', $result);
            $this->assertNotCount(0, $result);
        }
    }

    /**
     * @throws \Exception on error
     */
    public function testBatchRead() {
        $result = $this->instance->batchRead($this->categories);

        $this->assertInternalType('array', $result);
        $this->assertCount(count($this->categories), $result);

        foreach ($result as $categoryInfo) {
            $this->assertInternalType('array', $categoryInfo);
            $this->assertNotCount(0, $categoryInfo);
        }
    }

}
