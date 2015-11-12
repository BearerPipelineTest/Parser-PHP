<?php

	namespace WhichBrowser;


	class Primitive {
		public function __construct($defaults = null) {
			if (is_array($defaults)) {
				foreach ($defaults as $k => $v) {
					$this->{$k} = $v;
				}
			}
		}
	}


	class Browser extends Primitive {
		var $stock = true;
		var $hidden = false;
		var $channel = '';
		var $mode = '';
	}


	class Engine extends Primitive {

	}


	class Os extends Primitive {

	}


	class Device extends Primitive {
		var $type = '';
		var $identified = ID_NONE;
		var $generic = true;
	}


	class Version extends Primitive {
		var $value = null;

		public function is() {
			$valid = false;

			$arguments = func_get_args();
			if (count($arguments)) {
				$operator = '=';
				$compare = null;

				if (count($arguments) == 1) {
					$compare = $arguments[0];
				}
				
				if (count($arguments) >= 2) {
					$operator = $arguments[0];
					$compare = $arguments[1];
				}				

				if (!is_null($compare)) {
					$min = min(substr_count($this->value, '.'), substr_count($compare, '.')) + 1;

					$v1 = $this->toValue($this->value, $min);
					$v2 = $this->toValue($compare, $min);

					switch ($operator) {
						case '<':	$valid = $v1 < $v2; break;
						case '<=':	$valid = $v1 <= $v2; break;
						case '=':	$valid = $v1 == $v2; break;
						case '>':	$valid = $v1 > $v2; break;
						case '>=':	$valid = $v1 >= $v2; break;
					}
				}
			}

			return $valid;
		}

		private function toValue($value = null, $count = null) {
			if (is_null($value)) $value = $this->value;
			$parts = explode('.', $value);
			if (!is_null($count)) $parts = array_slice($parts, 0, $count);

			$result = $parts[0];

			if (count($parts) > 1) {
				$result .= '.';

				for ($p = 1; $p < count($parts); $p++) {
					$result .= substr('0000' . $parts[$p], -4);
				}
			}

			return floatval($result);
		}

		public function toFloat() {
			return floatval($this->value);
		}

		public function toNumber() {
			return intval($this->value);
		}

		public function toArray() {
			$result = [];

			if (isset($this->value)) {
				if (isset($this->details)) {
					$parts = explode('.', $this->value);
					$result['value'] = join('.', array_slice($parts, 0, $this->details));
				} else {
					$result['value'] = $this->value;
				}
			}

			if (isset($this->alias)) {
				$result['alias'] = $this->alias;
			}

			if (isset($this->nickname)) {
				$result['nickname'] = $this->nickname;
			}

			if (isset($result['value']) && !isset($result['alias']) && !isset($result['nickname'])) {
				return $result['value'];
			}

			return $result;
		}
	}

