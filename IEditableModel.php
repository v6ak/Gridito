<?php
namespace Gridito;

use \ArrayAccess;

/**
 * @author Vít Šesták
 * @copyright Vít Šesták
 * An IModel with read-write access.
 */
interface IEditableModel extends IModel, ArrayAccess{
	//TODO: Consider if it is better to use ArrayAccess or my own methods.

}