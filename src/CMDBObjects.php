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
 * Requests for API namespace 'cmdb.objects'
 */
class CMDBObjects extends Request {

    /**
     * Sort ascending
     *
     * @var string
     */
    const SORT_ASCENDING = 'ASC';

    /**
     * Sort descending
     *
     * @var string
     */
    const SORT_DESCENDING = 'DESC';

    /**
     * Creates one or more objects
     *
     * @param array $objects Mandatory attributes ('type', 'title') and optional attributes ('category', 'purpose', 'cmdb_status', 'description')
     *
     * @return array List of object identifiers
     *
     * @throws \Exception on error
     */
    public function create(array $objects) {
        $requests = [];

        foreach ($objects as $object) {
            $requests[] = [
                'method' => 'cmdb.object.create',
                'params' => $object
            ];
        }

        $result = $this->api->batchRequest($requests);

        $objectIDs = [];

        foreach ($result as $object) {
            $objectIDs[] = (int) $object['id'];
        }

        return $objectIDs;
    }

    /**
     * Fetches objects
     *
     * @param array $filter (optional) Filter; use any combination of 'ids' (array of object identifiers), 'type' (object type identifier), 'type_group', 'status', 'title' (object title), 'type_title' (l10n object type), 'location', 'sysid', 'first_name', 'last_name', 'email'
     * @param int $limit Limit result set
     * @param int $offset Offset
     * @param string $orderBy Order result set by 'isys_obj_type__id', 'isys_obj__isys_obj_type__id', 'type', 'isys_obj__title', 'title', 'isys_obj_type__title', 'type_title', 'isys_obj__sysid', 'sysid', 'isys_cats_person_list__first_name', 'first_name', 'isys_cats_person_list__last_name', 'last_name', 'isys_cats_person_list__mail_address', 'email', 'isys_obj__id', 'id'
     * @param string $sort Sort ascending ('ASC') or descending ('DESC')
     *
     * @return array Indexed array of associative arrays
     *
     * @throws \Exception on error
     */
    public function read(array $filter = [], $limit = null, $offset = null, $orderBy = null, $sort = null) {
        $params = [];

        if (isset($filter)) {
            $params['filter'] = $filter;
        }

        if (isset($limit)) {
            if (isset($offset)) {
                $params['limit'] = $offset . ',' . $limit;
            } else {
                $params['limit'] = $limit;
            }
        }

        if (isset($orderBy)) {
            $params['order_by'] = $orderBy;
        }

        if (isset($sort)) {
            $params['sort'] = $sort;
        }

        return $this->api->request(
            'cmdb.objects.read',
            $params
        );
    }

    /**
     * Fetches objects by their identifiers
     *
     * @param int[] $objectIDs List of object identifiers
     *
     * @return array Indexed array of associative arrays
     *
     * @throws \Exception on error
     */
    public function readByIDs(array $objectIDs) {
        return $this->api->request(
            'cmdb.objects.read',
            [
                'filter' => [
                    'ids' => $objectIDs
                ]
            ]
        );
    }

    /**
     * Fetches objects by their object type
     *
     * @param string $objectType Object type constant
     *
     * @return array Indexed array of associative arrays
     *
     * @throws \Exception on error
     */
    public function readByType($objectType) {
        return $this->api->request(
            'cmdb.objects.read',
            [
                'filter' => [
                    'type' => $objectType
                ]
            ]
        );
    }

    /**
     * Fetches archived objects filtered by (optional) type
     *
     * @param string $type (Optional) object type constant
     *
     * @return array Indexed array of associative arrays
     *
     * @throws \Exception on error
     */
    public function readArchived($type = null) {
        $filter = [
            'status' => 'C__RECORD_STATUS__ARCHIVED'
        ];

        if (isset($type)) {
            $filter['type'] = $type;
        }

        return $this->api->request(
            'cmdb.objects.read',
            [
                'filter' => $filter
            ]
        );
    }

    /**
     * Fetches deleted objects filtered by (optional) type
     *
     * @param string $type (Optional) object type constant
     *
     * @return array Indexed array of associative arrays
     *
     * @throws \Exception on error
     */
    public function readDeleted($type = null) {
        $filter = [
            'status' => 'C__RECORD_STATUS__DELETED'
        ];

        if (isset($type)) {
            $filter['type'] = $type;
        }

        return $this->api->request(
            'cmdb.objects.read',
            [
                'filter' => $filter
            ]
        );
    }

    /**
     * Fetches an object identifier by object title and (optional) type
     *
     * @param string $title Object title
     * @param string $type (Optional) type constant
     *
     * @return int Object identifier
     *
     * @throws \Exception on error
     */
    public function getID($title, $type = null) {
        $filter = [
            'title' => $title
        ];

        if (isset($type)) {
            $filter['type'] = $type;
        }

        $result = $this->read($filter);

        switch (count($result)) {
            case 0:
                throw new \Exception('Object not found');
            case 1:
                if (!array_key_exists('id', $result[0])) {
                    throw new \Exception('Bad result');
                }

                return (int) $result[0]['id'];
            default:
                throw new \Exception('Found %s objects', count($result));
        }
    }

    /**
     * Updates one or more existing objects
     *
     * @param array $objects Indexed array of object attributes ('id' and 'title')
     *
     * @return self Returns itself
     *
     * @throws \Exception on error
     */
    public function update(array $objects) {
        $requests = [];

        foreach ($objects as $object) {
            $requests[] = [
                'method' => 'cmdb.object.update',
                'params' => $object
            ];
        }

        $this->api->batchRequest($requests);

        return $this;
    }

    /**
     * Archives one or more objects
     *
     * @param int[] $objectIDs List of object identifiers
     *
     * @return self Returns itself
     *
     * @throws \Exception on error
     */
    public function archive(array $objectIDs) {
        $requests = [];

        foreach ($objectIDs as $objectID) {
            $requests[] = [
                'method' => 'cmdb.object.delete',
                'params' => [
                    'id' => $objectID,
                    'status' => 'C__RECORD_STATUS__ARCHIVED'
                ]
            ];
        }

        $this->api->batchRequest($requests);

        return $this;
    }

    /**
     * Deletes one or more objects
     *
     * @param int[] $objectIDs List of object identifiers
     *
     * @return self Returns itself
     *
     * @throws \Exception on error
     */
    public function delete(array $objectIDs) {
        $requests = [];

        foreach ($objectIDs as $objectID) {
            $requests[] = [
                'method' => 'cmdb.object.delete',
                'params' => [
                    'id' => $objectID,
                    'status' => 'C__RECORD_STATUS__DELETED'
                ]
            ];
        }

        $this->api->batchRequest($requests);

        return $this;
    }

    /**
     * Purges one or more objects
     *
     * @param int[] $objectIDs List of object identifiers
     *
     * @return self Returns itself
     *
     * @throws \Exception on error
     */
    public function purge(array $objectIDs) {
        $requests = [];

        foreach ($objectIDs as $objectID) {
            $requests[] = [
                'method' => 'cmdb.object.delete',
                'params' => [
                    'id' => $objectID,
                    'status' => 'C__RECORD_STATUS__PURGE'
                ]
            ];
        }

        $this->api->batchRequest($requests);

        return $this;
    }

// @todo Does not work:
//    public function restore(array $objectIDs) {
//
//    }

}
