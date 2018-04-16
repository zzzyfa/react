<?php

namespace Amasty\AdminActionsLog\lib;

/**
* FINE granularity DIFF
*
* Computes a set of instructions to convert the content of
* one string into another.
*
* Copyright (c) 2011 Raymond Hill (http://raymondhill.net/blog/?p=441)
*
* Licensed under The MIT License
* 
* Permission is hereby granted, free of charge, to any person obtaining a copy
* of this software and associated documentation files (the "Software"), to deal
* in the Software without restriction, including without limitation the rights
* to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the Software is
* furnished to do so, subject to the following conditions:
*
* The above copyright notice and this permission notice shall be included in
* all copies or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
* THE SOFTWARE.
*
* @copyright Copyright 2011 (c) Raymond Hill (http://raymondhill.net/blog/?p=441)
* @link http://www.raymondhill.net/finediff/
* @version 0.6
* @license MIT License (http://www.opensource.org/licenses/mit-license.php)
*/

/**
* Usage (simplest):
*
*   include 'finediff.php';
*
*   // for the stock stack, granularity values are:
*   // FineDiff::$paragraphGranularity = paragraph/line level
*   // FineDiff::$sentenceGranularity = sentence level
*   // FineDiff::$wordGranularity = word level
*   // FineDiff::$characterGranularity = character level [default]
*
*   $opcodes = FineDiff::getDiffOpcodes($from_text, $to_text [, $granularityStack = null] );
*   // store opcodes for later use...
*
*   ...
*
*   // restore $to_text from $from_text + $opcodes
*   include 'finediff.php';
*   $to_text = FineDiff::renderToTextFromOpcodes($from_text, $opcodes);
*
*   ...
*/

/**
* Persisted opcodes (string) are a sequence of atomic opcode.
* A single opcode can be one of the following:
*   c | c{n} | d | d{n} | i:{c} | i{length}:{s}
*   'c'        = copy one character from source
*   'c{n}'     = copy n characters from source
*   'd'        = skip one character from source
*   'd{n}'     = skip n characters from source
*   'i:{c}     = insert character 'c'
*   'i{n}:{s}' = insert string s, which is of length n
*
* Do not exist as of now, under consideration:
*   'm{n}:{o}  = move n characters from source o characters ahead.
*   It would be essentially a shortcut for a delete->copy->insert
*   command (swap) for when the inserted segment is exactly the same
*   as the deleted one, and with only a copy operation in between.
*   TODO: How often this case occurs? Is it worth it? Can only
*   be done as a postprocessing method (->optimize()?)
*/
abstract class FineDiffOp {
	abstract public function getFromLen();
	abstract public function getToLen();
	abstract public function getOpcode();
}
