<?php

/**
 * FSHL 2.1.0                                  | Fast Syntax HighLighter |
 * -----------------------------------------------------------------------
 *
 * LICENSE
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 */

namespace FSHL\Lexer;

use FSHL, FSHL\Generator;

/**
 * Neon lexer.
 *
 * @copyright Copyright (c) 2002-2005 Juraj 'hvge' Durech
 * @copyright Copyright (c) 2011-2012 Jaroslav Hanslík
 * @license http://fshl.kukulich.cz/#license
 */
class Ini implements FSHL\Lexer
{
	/**
	 * Returns language name.
	 *
	 * @return string
	 */
	public function getLanguage()
	{
		return 'Ini';
	}

	/**
	 * Returns initial state.
	 *
	 * @return string
	 */
	public function getInitialState()
	{
		return 'LINE_BODY';
	}

	/**
	 * Returns states.
	 *
	 * @return array
	 */
	public function getStates()
	{
		return array(
			'LINE_BODY' => array(
				array(
					'[' => array('SECTION_START', Generator::NEXT),
					';' => array('COMMENT', Generator::NEXT),
					'LINE' => array(Generator::STATE_SELF, Generator::NEXT),
					'!SPACE' => array('KEY', Generator::NEXT),
					'ALL' => array(Generator::STATE_SELF, Generator::NEXT),
				),
				Generator::STATE_FLAG_NONE,
				null,
				null
			),
			'SECTION_START' => array(
				array(
					']' => array('SECTION_END', Generator::NEXT),
					'ALL' => array('SECTION', Generator::NEXT),
				),
				Generator::STATE_FLAG_NONE,
				'ini-section-border',
				null
			),
			'SECTION' => array(
				array(
					']' => array('SECTION_END', Generator::NEXT),
					'SECTION' => array(Generator::STATE_SELF, Generator::NEXT),
				),
				Generator::STATE_FLAG_NONE,
				'ini-section',
				null
			),
			'SECTION_END' => array(
				array(
					';' => array('COMMENT', Generator::NEXT),
					'LINE' => array('LINE_BODY', Generator::NEXT),
				),
				Generator::STATE_FLAG_NONE,
				'ini-section-border',
				null
			),
			'KEY' => array(
				array(
					'=' => array('SEPARATOR', Generator::NEXT),
					':' => array('SEPARATOR', Generator::NEXT),
					'[' => array('ARRAY_START', Generator::NEXT),
					';' => array('COMMENT', Generator::NEXT),
					'LINE' => array('LINE_BODY', Generator::NEXT),
					'ALL' => array(Generator::STATE_SELF, Generator::NEXT),
				),
				Generator::STATE_FLAG_NONE,
				'ini-key',
				null
			),
			'ARRAY_START' => array(
				array(
					']' => array('ARRAY_END', Generator::NEXT),
					'ALL' => array('ARRAY', Generator::NEXT),
				),
				Generator::STATE_FLAG_NONE,
				'ini-array-border',
				null
			),
			'ARRAY' => array(
				array(
					']' => array('ARRAY_END', Generator::NEXT),
					'ALL' => array(Generator::STATE_SELF, Generator::NEXT),
				),
				Generator::STATE_FLAG_NONE,
				'ini-array',
				null
			),
			'ARRAY_END' => array(
				array(
					'=' => array('SEPARATOR', Generator::NEXT),
					':' => array('SEPARATOR', Generator::NEXT),
					'[' => array('ARRAY_START', Generator::NEXT),
					'LINE' => array('LINE_BODY', Generator::NEXT),
				),
				Generator::STATE_FLAG_NONE,
				'ini-array-border',
				null
			),
			'VALUE' => array(
				array(
					'LINE' => array('LINE_BODY', Generator::NEXT),
					';' => array('COMMENT', Generator::NEXT),
					'ALL' => array(Generator::STATE_SELF, Generator::NEXT),
				),
				Generator::STATE_FLAG_NONE,
				'ini-value',
				null
			),
			'SEPARATOR' => array(
				array(
					//'ALL' => array(Generator::STATE_RETURN, Generator::BACK)
					';' => array('COMMENT', Generator::NEXT),
					'ALL' => array('VALUE', Generator::NEXT),
				),
				Generator::STATE_FLAG_RECURSION,
				'ini-sep',
				null
			),
			'COMMENT' => array(
				array(
					'LINE' => array('LINE_BODY', Generator::BACK),
					'ALL' => array(Generator::STATE_SELF, Generator::NEXT)
				),
				Generator::STATE_FLAG_NONE,
				'ini-comment',
				null
			)
		);
	}

	/**
	 * Returns special delimiters.
	 *
	 * @return array
	 */
	public function getDelimiters()
	{
		/*return array(
			'SECTION' => 'preg_match(\'~[\\\\w.]+(?=(\\\\s*<\\\\s*[\\\\w.]+)?\\\\s*:\\\\s*\\n)~Ai\', $text, $matches, 0, $textPos)',
			'KEY' => 'preg_match(\'~[\\\\w.]+(?=\\\\s*(?::|=))~Ai\', $text, $matches, 0, $textPos)',
			'TEXT' => 'preg_match(\'~[a-z](?![,\\\\]}#\\n])~Ai\', $text, $matches, 0, $textPos)'
		);*/
	    return array();
	}

	/**
	 * Returns keywords.
	 *
	 * @return array
	 */
	public function getKeywords()
	{
		return array();
	}
}
