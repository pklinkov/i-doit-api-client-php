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

use bheisig\idoitapi\File;

class FileTest extends BaseTest {

    /**
     * @var \bheisig\idoitapi\File
     */
    protected $instance;

    /**
     * @var array
     */
    protected $files = [];

    /**
     * @throws \Exception on error
     */
    public function setUp() {
        parent::setUp();

        $this->instance = new File($this->api);

        for ($i = 1; $i <= 3; $i++) {
            $filePath = sprintf(
                'file%s.txt',
                $i
            );
            $description = sprintf(
                'API Test %s @ %s',
                $i,
                microtime(true)
            );
            $this->files[$filePath] = $description;
        }
    }

    /**
     * @throws \Exception on error
     */
    public function testAdd() {
        $objectID = $this->createServer();

        foreach ($this->files as $filePath => $description) {
            $status = file_put_contents($filePath, $description);

            if ($status === false) {
                throw new \Exception('Unable to create test file');
            }

            $this->assertInstanceOf(
                File::class,
                $this->instance->add($objectID, $filePath, $description)
            );
        }
    }

    /**
     * @throws \Exception on error
     */
    public function testBatchAdd() {
        $objectID = $this->createServer();

        $this->assertInstanceOf(
            File::class,
            $this->instance->batchAdd($objectID, $this->files)
        );
    }

    /**
     * @throws \Exception on error
     */
    public function testEncode() {
        foreach ($this->files as $filePath => $description) {
            $status = file_put_contents($filePath, $description);

            if ($status === false) {
                throw new \Exception('Unable to create test file');
            }

            $fileAsString = $this->instance->encode($filePath);

            $this->assertInternalType('string', $fileAsString);
            $this->assertNotEmpty($fileAsString);
        }
    }

}
