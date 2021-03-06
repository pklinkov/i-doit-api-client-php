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
 * Requests for API namespace 'checkmk.statictag'
 */
class CheckMKStaticTag extends Request {

    /**
     * Create a new static host tag
     *
     * @param string $tag Tag
     * @param string $title Name
     * @param string $group Optional associated host group
     * @param bool $export Let it be exportable; defaults to true
     * @param string $description Optional description
     *
     * @return int Identifier
     *
     * @throws \Exception on error
     */
    public function create($tag, $title, $group = null, $export = true, $description = null) {
        $params = [
            'data' => [
                'tag' => $tag,
                'title' => $title,
                'export' => $export
            ]
        ];

        if (isset($group)) {
            $params['data']['group'] = $group;
        }

        if (isset($group)) {
            $params['data']['description'] = $description;
        }

        $result = $this->api->request(
            'checkmk.statictag.create',
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

        return $result['id'];
    }

    /**
     * Create one or more static host tags
     *
     * @param array $tags List of tags;
     * required attributes per tag: "tag", "title";
     * optional attributes per tag: "group", "export", "description"
     *
     * @return int[] List of identifiers
     *
     * @throws \Exception on error
     */
    public function batchCreate(array $tags) {
        $requests = [];

        $required = ['tag', 'title'];

        foreach ($tags as $data) {
            foreach ($required as $attribute) {
                if (!array_key_exists($attribute, $data)) {
                    throw new \Exception(sprintf(
                        'Missing attribute "%s"',
                        $attribute
                    ));
                }
            }

            $requests[] = [
                'method' => 'checkmk.statictag.create',
                'params' => [
                    'data' => $data
                ]
            ];
        }

        $result = $this->api->batchRequest($requests);

        $tagIDs = [];

        foreach ($result as $tag) {
            // Do not check 'id' because in a batch request it is always NULL:
            if (!array_key_exists('success', $tag) ||
                $tag['success'] !== true) {
                if (array_key_exists('message', $tag)) {
                    throw new \Exception(sprintf('Bad result: %s', $tag['message']));
                } else {
                    throw new \Exception('Bad result');
                }
            }

            $tagIDs[] = $tag['id'];
        }

        return $tagIDs;
    }

    /**
     * Read all existing static host tags
     *
     * @return array
     *
     * @throws \Exception on error
     */
    public function read() {
        return $this->api->request(
            'checkmk.statictag.read'
        );
    }

    /**
     * Read a static host tag by its identifier
     *
     * @param int $id Identifier
     *
     * @return array
     *
     * @throws \Exception on error
     */
    public function readByID($id) {
        return $this->api->request(
            'checkmk.statictag.read',
            [
                'id' => $id
            ]
        );
    }

    /**
     * Read all static hosts tags filtered by their identifiers
     *
     * @param int[] $ids List of identifiers
     *
     * @return array
     *
     * @throws \Exception on error
     */
    public function readByIDs(array $ids) {
        return $this->api->request(
            'checkmk.statictag.read',
            [
                'ids' => $ids
            ]
        );
    }

    /**
     * Read a static host tag by its tag
     *
     * @param string $tag Tag
     *
     * @return array
     *
     * @throws \Exception on error
     */
    public function readByTag($tag) {
        return $this->api->request(
            'checkmk.statictag.read',
            [
                'tag' => $tag
            ]
        );
    }

    /**
     * Update a static host tag by its identifier
     *
     * @param int $id Identifier
     * @param array $tag Tag attributes which can be altered:
     * "tag", "title", "group", "export", "description"
     *
     * @return self Returns itself
     *
     * @throws \Exception on error
     */
    public function update($id, array $tag) {
        $result = $this->api->request(
            'checkmk.statictag.update',
            [
                'id' => $id,
                'data' => $tag
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
     * Delete a static host tag by its identifier
     *
     * @param int $id Identifier
     *
     * @return self Returns itself
     *
     * @throws \Exception on error
     */
    public function delete($id) {
        $result = $this->api->request(
            'checkmk.statictag.delete',
            [
                'id' => $id
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
     * Delete one or more static host tags be their identifiers
     *
     * @param int[] $ids List of identifiers
     *
     * @return self Returns itself
     *
     * @throws \Exception on error
     */
    public function batchDelete($ids) {
        $requests = [];

        foreach ($ids as $id) {
            $requests[] = [
                'method' => 'checkmk.statictag.delete',
                'params' => [
                    'id' => $id
                ]
            ];
        }

        $result = $this->api->batchRequest($requests);

        foreach ($result as $tag) {
            // Do not check 'id' because in a batch request it is always NULL:
            if (!array_key_exists('success', $tag) ||
                $tag['success'] !== true) {
                if (array_key_exists('message', $tag)) {
                    throw new \Exception(sprintf('Bad result: %s', $tag['message']));
                } else {
                    throw new \Exception('Bad result');
                }
            }
        }

        return $this;
    }

    /**
     * Delete all static host tags
     *
     * @return self Returns itself
     *
     * @throws \Exception on error
     */
    public function deleteAll() {
        $tags = $this->read();

        $ids = [];

        foreach ($tags as $tag) {
            $ids[] = $tag['id'];
        }

        if (count($ids) > 0) {
            $this->batchDelete($ids);
        }

        return $this;
    }

}
