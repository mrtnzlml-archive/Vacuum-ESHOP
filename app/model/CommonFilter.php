<?php

namespace Model;

/**
 * Class CommonFilter
 * @package Model
 */
class CommonFilter {

	/**
	 * @param \DibiFluent $statement
	 * @param $query
	 */
	public static function apply(\DibiFluent $statement, $query) {
		if (isset($query['limit'])) {
			$statement->limit($query['limit']);
		}
		if (isset($query['offset'])) {
			$statement->offset($query['offset']);
		}
		if (isset($query['orderBy'])) {
			$statement->orderBy($query['orderBy']);
		} elseif (isset($query['order'])) {
			$statement->orderBy($query['order']);
		}
	}

}