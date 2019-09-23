<?php
/**
 * Class for Task objects.
 * Contains information related to a given task
 * 
 * string   $token    Token to renew password
 * int  $user_id      ID of user
 * timestamp    $created_time   Time when the token was generated
 */
class password_renew_dto {
    public $token;
    public $user_id;
    public $created_time;
}
