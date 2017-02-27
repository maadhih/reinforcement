<?php

namespace Acnox\StringRay;

use Countable;
use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;
use Stringy\Stringy;
use Stringy\StaticStringy;
use BadMethodCallException;

/**
 *
 */

class String implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * @var string
     */
    protected $string;

    /**
     * @var string
     */
    protected $original;

    /**
     * @var array
     */
    protected $selection = [];

    /**
     * Constructor
     * @param string $string    
     * @param string $original  
     * @param array $selection 
     */
    public function __construct($string = '', $original = null, array $selection = [])
    {
        $this->string   = (string) $string;
        $this->original = $original;
        $this->selection = $selection;

    }

    public function __call($method, $args)
    {
        $stringy = $this->stringy();
        if (method_exists($stringy, $method)){
            array_unshift($args, $this->string);
            $result = call_user_func_array([$stringy, $method], $args);

            if ($result instanceof Stringy) return $this->returnNew($result);

            return $result;
        }

        throw new BadMethodCallException("Method '". $method . "' does not exist");
        
    }

    public function __toString()
    {
        return (string) $this->string;
    }

    /**
     * Returns the length of the string.
     *
     * @return int Number of characters in the string
     */
    public function count()
    {
        return $this->stringy()->length();
    }

    public function stringy()
    {
        return new Stringy($this->string);
    }

    public function deselect()
    {
        return new static($this->original);
    }

    /**
     * Returns a new ArrayIterator.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->stringy()->chars());
    }

    /**
     * Whether a offset exists.
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset An offset to check for.
     *
     * @return bool true on success or false on failure.
     *              The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return strlen($this->string) >= ($offset + 1);
    }

    /**
     * Offset to retrieve.
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset The offset to retrieve.
     *
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        $character = substr($this->string, $offset, 1);
        return new static($character ?: '');
    }

    /**
     * Offset to set.
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset The offset to assign the value to.
     * @param mixed $value  The value to set.
     */
    public function offsetSet($offset, $value)
    {
        $this->string[$offset] = $value;
    }

    /**
     * Offset to unset.
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset The offset to unset.
     *
     * @throws UnsetOffsetException
     */
    public function offsetUnset($offset)
    {
        throw new UnsetOffsetException();
    }

    public function returnNew($string = '')
    {
        return new static($string, $this->original, $this->selection);
    }

    public function save($string = null)
    {
        if (!empty($this->selection)) {

            $this->string = substr_replace($this->original, $this->string,
                $this->selection[0], $this->selection[1]);
            $this->original  = null;
            $this->selection = [];
        }
        // $this->string = "sdjsdj";
        return $this;
    }

    public function select(...$args)
    {
        $method       = '';
        $callbackArgs = $args;
        if (is_int($args[0])) {

            if (count($args) == 1) {
                $method = 'at';
            } elseif (count($args) == 2) {
                $method = 'substr';
            }
        } elseif (is_string($args[0])) {

            if (count($args) == 1) {
                $arg          = new static($args[0]);
                $method       = (string) $arg->till('(');
                $callbackArgs = $arg->between('(', ')');
                eval('$callbackArgs = [' . $callbackArgs . '];');

            } elseif (count($args) == 2) {
                $method = 'between';
            }
        }

        $this->original = $this->string;
        $this->string = (string) $this->__call($method, $callbackArgs);

        switch ($method) {
            case 'at':
                $this->selection = [$callbackArgs[0], 1];
                break;

            case 'substr':
                $this->selection = $callbackArgs;
                break;

            case 'between':
                $needle = $callbackArgs[0] . $this->string . $callbackArgs[1];
                $start  = strpos($this->original, $needle) + strlen($callbackArgs[0]);
                $length = strlen($this->string);

                $this->selection = [$start, $length];
                break;

            case 'at':
                $this->selection = [$callbackArgs, 1];
                break;

            default:
                # code...
                break;
        }
        return $this;
    }

    public function test()
    {
        echo "test";
    }

    public function till($string)
    {
        $index = strpos($this->string, $string);
        return $this->returnNew($this->stringy()->substr(0, $index));
    }

}
