<?php

interface MailTemplateI {
    public function getSubject($parmas = null);
    public function getHead($params = null);
    public function getBody($params = null);
}

?>