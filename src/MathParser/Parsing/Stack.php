<?php
/*
 * @package     Parsing
 * @author      Frank Wikström <frank@mossadal.se>
 * @copyright   2015 Frank Wikström
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 *
 */


namespace MathParser\Parsing;

use SplStack;

/**
 * Utility class, implementing a simple FIFO stack
 */
class Stack extends SplStack {
    // /** mixed[] $data internal storage of data on the stack. */
    // protected $data = array();

    // /**
    //  * Push an element onto the stack.
    //  * @param mixed $element
    //  */
    // public function push($element) {
    //     $this->data[] = $element;
    // }

    /**
     * Return the top element (without popping it)
     * @return mixed
     */
    public function peek() {
        // return end($this->data);
        if ($this->isEmpty()) {
            return false;
        }
        return $this->top();
    }

    // /**
    //  * Return the top element and remove it from the stack.
    //  * @return mixed
    //  */
    public function pop() {
        if ($this->isEmpty()) {
            return null;
        }
        return parent::pop();
    }

    // /**
    //  * Return the current number of elements in the stack.
    //  * @return int
    //  */
    // public function count() {
    //     return count($this->data);
    // }

    // /**
    //  * Returns true if the stack is empty
    //  *
    //  * @return boolean
    //  **/
    // public function isEmpty()
    // {
    //     return $this->count() == 0;
    // }

    // public function __toString()
    // {
    //     return implode(' ; ', $this->data);
    // }

}

// $stack = new Stack();

// $splStack = new \SplStack();
// $splStack->push(1);
// $splStack->push(2);
// $splStack->push(3);
// var_dump($splStack->top(), $splStack->bottom());

// function testLoop($opt, $loop, $stack, $userFunc) {
//     $st = microtime(1);
//     for ($i = 0; $i < $loop; $i++) {
//         $userFunc($stack);
//     }
//     $et = microtime(1);
//     $duration = $et - $st;
//     printf("Summary: %s %s op/s\n", $opt, number_format($loop/$duration));
// }

// $loop = 10000;

// testLoop('push', $loop, $stack, function ($stack) {
//     $stack->push(1);
// });
// testLoop('isEmpty', $loop, $stack, function ($stack) {
//     $stack->isEmpty();
// });
// testLoop('count', $loop, $stack, function ($stack) {
//     $stack->count();
// });
// testLoop('peek', $loop, $stack, function ($stack) {
//     $stack->peek();
// });
// testLoop('pop', $loop, $stack, function ($stack) {
//     $stack->pop();
// });

// testLoop('SPL push', $loop, $splStack, function ($stack) {
//     $stack->push(1);
// });
// testLoop('SPL isEmpty', $loop, $splStack, function ($stack) {
//     $stack->isEmpty();
// });
// testLoop('SPL count', $loop, $splStack, function ($stack) {
//     $stack->count();
// });
// testLoop('SPL peek', $loop, $splStack, function ($stack) {
//     $stack->bottom();
// });
// testLoop('SPL pop', $loop, $splStack, function ($stack) {
//     $stack->pop();
// });
