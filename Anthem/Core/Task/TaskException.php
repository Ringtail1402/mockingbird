<?php

namespace Anthem\Core\Task;

use Exception;

/**
 * A dummy exception class which is used for expected exceptions in console tasks
 * (e.g. invalid syntax or specified file not found).
 */
class TaskException extends Exception {}