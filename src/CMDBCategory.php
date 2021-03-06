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

namespace bheisig\idoitapi;

/**
 * Requests for API namespace 'cmdb.category'
 */
class CMDBCategory extends Request {

    /**
     * Creates a new category entry for a specific object
     *
     * @param int $objectID Object identifier
     * @param string $categoryConst Category constant
     * @param array $attributes Attributes
     *
     * @return int Entry identifier
     *
     * @throws \Exception on error
     */
    public function create($objectID, $categoryConst, array $attributes) {
        $params = [
            'objID' => $objectID,
            'data' => $attributes,
            'category' => $categoryConst
        ];

        $result = $this->api->request(
            'cmdb.category.create',
            $params
        );

        if (!array_key_exists('id', $result) ||
            !is_numeric($result['id']) ||
            !array_key_exists('success', $result) ||
            $result['success'] !== true) {
            if (array_key_exists('message', $result)) {
                throw new \Exception(sprintf('Bad result: %s', $result['message']));
            } else {
                throw new \Exception('Bad result');
            }
        }

        return (int) $result['id'];
    }

    /**
     * Reads one or more category entries for a specific object (works with both single- and multi-valued categories)
     *
     * @param int $objectID Object identifier
     * @param string $categoryConst Category constant
     *
     * @return array[] Indexed array of result sets (for both single- and multi-valued categories)
     *
     * @throws \Exception on error
     */
    public function read($objectID, $categoryConst) {
        return $this->api->request(
            'cmdb.category.read',
            [
                'objID' => $objectID,
                'category' => $categoryConst

            ]
        );
    }

    /**
     * Reads one specific category entry for a specific object (works with both single- and multi-valued categories)
     *
     * @param int $objectID Object identifier
     * @param string $categoryConst Category constant
     * @param int $entryID Entry identifier
     *
     * @return array Associative array
     *
     * @throws \Exception on error
     */
    public function readOneByID($objectID, $categoryConst, $entryID) {
        $entries = $this->read($objectID, $categoryConst);

        foreach ($entries as $entry) {
            if (!array_key_exists('id', $entry)) {
                throw new \Exception(sprintf(
                    'Entries for category "%s" contain no identifier',
                    $categoryConst
                ));
            }

            $currentID = (int) $entry['id'];

            if ($currentID === $entryID) {
                return $entry;
            }
        }

        throw new \Exception(sprintf(
            'No entry with identifier %s found in category "%s" for object $s',
            $entryID,
            $categoryConst,
            $objectID
        ));
    }

    /**
     * Reads first category entry for a specific object (works with both single- and multi-valued categories)
     *
     * @param int $objectID Object identifier
     * @param string $categoryConst Category constant
     *
     * @return array Associative array
     *
     * @throws \Exception on error
     */
    public function readFirst($objectID, $categoryConst) {
        $entries = $this->read($objectID, $categoryConst);

        return reset($entries);
    }

    /**
     * Updates a category entry for a specific object
     *
     * @param int $objectID Object identifier
     * @param string $categoryConst Category constant
     * @param array $attributes Attributes
     * @param int $entryID Entry identifier (only needed for multi-valued categories)
     *
     * @return self Returns itself
     *
     * @throws \Exception on error
     */
    public function update($objectID, $categoryConst, array $attributes, $entryID = null) {
        if (isset($entryID)) {
            $attributes['category_id'] = $entryID;
        }

        $result = $this->api->request(
            'cmdb.category.update',
            [
                'objID' => $objectID,
                'category' => $categoryConst,
                'data' => $attributes
            ]
        );

        if (!array_key_exists('success', $result) ||
            $result['success'] !== true) {
            if (array_key_exists('message', $result)) {
                throw new \Exception(sprintf('Bad result: %s', $result['message']));
            } else {
                throw new \Exception('Bad result');
            }
        }

        return $this;
    }

    /**
     * Archives a category entry for a specific object
     *
     * @param int $objectID Object identifier
     * @param string $categoryConst Category constant
     * @param int $entryID Entry identifier
     *
     * @return self Returns itself
     *
     * @throws \Exception on error
     */
    public function archive($objectID, $categoryConst, $entryID) {
        $result = $this->api->request(
            'cmdb.category.delete',
            [
                'objID' => $objectID,
                'category' => $categoryConst,
                'cateID' => $entryID
            ]
        );

        if (!array_key_exists('success', $result) ||
            $result['success'] !== true) {
            if (array_key_exists('message', $result)) {
                throw new \Exception(sprintf('Bad result: %s', $result['message']));
            } else {
                throw new \Exception('Bad result');
            }
        }

        return $this;
    }

    /**
     * Marks a category entry for a specific object as deleted
     *
     * @param int $objectID Object identifier
     * @param string $categoryConst Category constant
     * @param int $entryID Entry identifier
     *
     * @return self Returns itself
     *
     * @throws \Exception on error
     */
    public function delete($objectID, $categoryConst, $entryID) {
        return $this
            ->archive($objectID, $categoryConst, $entryID)
            ->archive($objectID, $categoryConst, $entryID);
    }


    /**
     * Purges a category entry for a specific object
     *
     * @param int $objectID Object identifier
     * @param string $categoryConst Category constant
     * @param int $entryID Entry identifier
     *
     * @return self Returns itself
     *
     * @throws \Exception on error
     */
    public function purge($objectID, $categoryConst, $entryID) {
        $result = $this->api->request(
            'cmdb.category.quickpurge',
            [
                'objID' => $objectID,
                'category' => $categoryConst,
                'cateID' => $entryID
            ]
        );

        if (!array_key_exists('success', $result) ||
            $result['success'] !== true) {
            if (array_key_exists('message', $result)) {
                throw new \Exception(sprintf('Bad result: %s', $result['message']));
            } else {
                throw new \Exception('Bad result');
            }
        }

        return $this;
    }

    /**
     * Creates multiple entries for a specific category and one or more objects
     *
     * @param int[] $objectIDs List of object identifiers
     * @param string $categoryConst Category constant
     * @param array[] $attributes Indexed array of attributes
     *
     * @return int[] Entry identifiers
     *
     * @throws \Exception on error
     */
    public function batchCreate(array $objectIDs, $categoryConst, array $attributes) {
        $entryIDs = [];

        $requests = [];

        foreach ($objectIDs as $objectID) {
            foreach ($attributes as $data) {
                $params = [
                    'objID' => $objectID,
                    'data' => $data,
                    'category' => $categoryConst
                ];

                $requests[] = [
                    'method' => 'cmdb.category.create',
                    'params' => $params
                ];
            }
        }

        $result = $this->api->batchRequest($requests);

        foreach ($result as $entry) {
            // Do not check 'id' because in a batch request it is always NULL:
            if (!array_key_exists('success', $entry) ||
                $entry['success'] !== true) {
                if (array_key_exists('message', $entry)) {
                    throw new \Exception(sprintf('Bad result: %s', $entry['message']));
                } else {
                    throw new \Exception('Bad result');
                }
            }

            $entryIDs[] = (int) $entry['id'];
        }

        return $entryIDs;
    }

    /**
     * Reads one or more category entries for one or more objects
     *
     * @param int[] $objectIDs List of object identifiers
     * @param string[] $categoryConsts List of category constants
     *
     * @return array Indexed array of result sets (for both single- and multi-valued categories)
     *
     * @throws \Exception on error
     */
    public function batchRead(array $objectIDs, array $categoryConsts) {
        $requests = [];

        foreach ($objectIDs as $objectID) {
            foreach ($categoryConsts as $categoryConst) {
                $requests[] = [
                    'method' => 'cmdb.category.read',
                    'params' => [
                        'objID' => $objectID,
                        'category' => $categoryConst
                    ]
                ];
            }
        }

        return $this->api->batchRequest($requests);
    }

    /**
     * Updates a single-valued category for one or more objects
     *
     * @param int[] $objectIDs List of object identifiers
     * @param string $categoryConst Category constant
     * @param array $attributes List of attributes with keys and values
     *
     * @return int[] List of category entry identifiers
     *
     * @throws \Exception on error
     */
    public function batchUpdate(array $objectIDs, $categoryConst, array $attributes) {
        $requests = [];

        foreach ($objectIDs as $objectID) {
            $requests[] = [
                'method' => 'cmdb.category.update',
                'params' => [
                    'objID' => $objectID,
                    'category' => $categoryConst,
                    'data' => $attributes
                ]
            ];
        }

        $results = $this->api->batchRequest($requests);

        foreach ($results as $result) {
            if (!array_key_exists('success', $result) ||
                $result['success'] !== true) {
                if (array_key_exists('message', $result)) {
                    throw new \Exception(sprintf('Bad result: %s', $result['message']));
                } else {
                    throw new \Exception('Bad result');
                }
            }
        }

        return $results;
    }

    /**
     * Archive category entries for a specific object
     *
     * @param int $objectID Object identifier
     * @param string[] $categoryConsts List of category constants
     *
     * @return int Number of purged category entries
     *
     * @throws \Exception on error
     */
    public function clear($objectID, array $categoryConsts) {
        $batch = $this->batchRead([$objectID], $categoryConsts);

        $requests = [];
        $counter = 0;
        $index = 0;

        foreach ($batch as $entries) {
            $categoryConst = $categoryConsts[$index];

            foreach ($entries as $entry) {
                $requests[] = [
                    'method' => 'cmdb.category.delete',
                    'params' => [
                        'objID' => $objectID,
                        'category' => $categoryConst,
                        'cateID' => (int) $entry['id']
                    ]
                ];

                $counter++;
            }

            $index++;
        }

        $results = $this->api->batchRequest($requests);

        foreach ($results as $result) {
            if (!array_key_exists('success', $result) ||
                $result['success'] !== true) {
                if (array_key_exists('message', $result)) {
                    throw new \Exception(sprintf('Bad result: %s', $result['message']));
                } else {
                    throw new \Exception('Bad result');
                }
            }
        }

        return $counter;
    }

}
