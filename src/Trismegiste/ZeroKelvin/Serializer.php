<?php

/*
 * ZeroKelvin
 */

namespace Trismegiste\ZeroKelvin;

/**
 * Serializer is a serializer service
 * 
 * It serializes a non-object multidimensional array list with magic 
 * keys to a php serialized string
 */
class Serializer implements Serialization
{

    protected $reference;
    protected $foreign;
    protected $pkField;

    public function __construct(UniqueGenerator $fac)
    {
        $this->pkField = $fac->getFieldName();
    }

    /**
     * Transforms an full array tree with magic keys to a serialized string of objects
     * 
     * @param array $dump the array list with array-transformed objects
     * 
     * @return string the result string which could be unserialized into objects
     */
    public function fromArray(array $dump)
    {
        $this->reference = [null];
        $root = array_shift($dump);
        $this->foreign = $dump;

        return $this->recursivFromArray($root);
    }

    protected function recursivFromArray(array $dump)
    {
        if (array_key_exists(self::META_REF, $dump)) {
            $uuid = $dump[self::META_REF];
            $nonInserted = false;
            foreach ($this->foreign as $idx => $obj) {
                if ($obj[$this->pkField] == $uuid) {
                    $nonInserted = $obj;
                    unset($this->foreign[$idx]);
                    break;
                }
            }

            if (!$nonInserted) {
                $found = array_search($uuid, $this->reference);
                if (false !== $found) {
                    return 'r:' . $found . ';';
                } else {
                    throw new \InvalidArgumentException("uuid $uuid not found");
                }
            } else {
                return $this->recursivFromArray($obj);
            }
        }

        $current = '';
        if (array_key_exists(self::META_CUSTOM, $dump)) {
            $fqcn = $dump[self::META_CLASS];
            $content = $dump[self::META_CUSTOM];
            $current = 'C:' . strlen($fqcn) . ':"' . $fqcn . '":' . strlen($content) . ':{' . $content;
        } else {
            // object or array ?
            if (array_key_exists(self::META_CLASS, $dump)) {
                $fqcn = $dump[self::META_CLASS];
                unset($dump[self::META_CLASS]);
                $this->reference[] = $dump[$this->pkField];
                unset($dump[$this->pkField]);
                $current = 'O:' . strlen($fqcn) . ':"' . $fqcn . '":' . (count($dump)) . ":{";
            } else {
                $current = 'a:' . (count($dump)) . ":{";
                $this->reference[] = null;
            }
            // manage content assoc
            foreach ($dump as $key => $val) {
                // manage key
                if (isset($fqcn)) {
                    switch ($key[0]) {
                        case self::META_PUBLIC:
                            $key = substr($key, 1);
                            break;
                        case self::META_PRIVATE:
                            $key = str_replace(self::META_PRIVATE, "\000", $key);
                            break;
                        default:
                            $key = "\000*\000" . $key;
                    }
                }
                $current .= serialize($key);
                // manage value
                if (is_array($val)) {
                    $current .= $this->recursivFromArray($val);
                } else {
                    $this->reference[] = null;
                    $current.= serialize($val);
                }
            }
        }
        $current .= '}';

        return $current;
    }

}