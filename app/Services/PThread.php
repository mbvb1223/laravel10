<?php
namespace App\Services;

use Thread;

/**
 * This extension is considered unmaintained and dead.
 * Tip: Consider using parallel instead.
 */
class PThread extends Thread {
    public $id = ""; //ThreadID
    public function __construct($idThread) {
        $this->id = $idThread;
    }
    public function run() {
        if ($this->id) {
            $sleep = mt_rand(1, 10);
            printf('Thread: %s  has started, sleeping for %d' . "\n", $this->id, $sleep);
            sleep($sleep);
            printf('Thread: %s  has finished' . "\n", $this->id);
        }
    }
}
?>
