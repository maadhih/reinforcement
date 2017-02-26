<?php

namespace Acnox\StringRay;

use ArrayAccess;
use Stringy\StaticStringy as Stringy;

/**
* 
*/

class String implements ArrayAccess
{
	 /**
     * @var string
     */
    protected $string;
    protected $lastResult;
    protected $selected;
    protected $selection;
    protected $original;
	
	function __construct($string = '', $original = null, $selection = null)
	{
        $this->string = (string) $string;
        $this->original = (isset($original) ? $original : $this->string);
        if (!empty($selection)) $this->selection = $selection;
	}

	public function __toString()
    {
        return (string) $this->string;
    }

    public function select(...$args)
    {
        $method = '';
        $callbackArgs = $args;
        if (is_int($args[0])) {

            if (count($args) == 1){
                $method = 'at';
            } elseif (count($args) == 2){
                $method = 'substr';
            }
        } elseif (is_string($args[0])) {

            if (count($args) == 1) {
                $arg = new static($args[0]);
                $method = (string) $arg->till('(');
                $callbackArgs = $arg->between('(', ')');
                eval('$callbackArgs = ['.$callbackArgs.'];');

            } elseif (count($args) == 2){
                $method = 'between';
            }
        }

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
                        $start = strpos($this->original, $needle) + strlen($callbackArgs[0]);
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

    public function till($string)
    {
        $index = strpos($this->string, $string);
        return $this->returnNew(Stringy::substr($this->string, 0, $index));
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

    public function __call($method, $args)
    {
        if (is_callable(Stringy::class, $method)) {
            array_unshift($args, $this->string);
            return $this->returnNew(forward_static_call_array([Stringy::class, $method], $args));
        }
        throw new \Exception(sprintf('String function %s does not exist', $method));
    }

    public function save($string = null)
    {
        if(!empty($this->selection)){
            $this->string = substr_replace($this->original, $this->string,
             $this->selection[0], $this->selection[1]);
            $this->original = $this->string;
            $this->selection = null;
        }
        // $this->string = (string) $this->lastResult;
        // $this->string = "sdjsdj";
        return $this;
    }

    public function returnNew($string ='')
    {
        return new static($string, $this->original, $this->selection);
    }

	public function test(){
		echo "test";
	}
}