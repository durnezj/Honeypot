<?php
class Reply
{
    private $threadNr;
    private $message;

    private function __construct($parentId, $reply)
    {
        $this->threadNr = $parentId;
        $this->message = $reply;
    }

}
?>