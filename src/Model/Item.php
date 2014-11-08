<?php
/**
 * This file is part of trellog.
 *
 * (c) Philippe Gerber
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bigwhoop\Trellog\Model;

class Item
{
    /** @var string */
    public $description = '';
    
    /** @var Author|null */
    public $author;
}
