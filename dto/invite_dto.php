<?php
/**
 * Class for Invitations
 * Contains information related to the invitation.
 * 
 * int id: Invitation ID
 * int admin: User ID of the admin who sent the invitation
 * string token: Validation token
 * string email: Email for the user getting the invitation
 * string date_sent: Date when the admin created the invitation
 * string date_used: Date when the invited user used the invitation
 * 
 */
class invite_dto{
  public $id;
  public $admin;
  public $token;
  public $email;
  public $date_sent;
  public $date_used;

  /**
   * Builds a new Invitation object 
   * 
   * @param int $admin Admin ID
   * @param string $email  Email address
   * @param string $token Validation token
   */
  public function __construct($admin, $email, $token) {
    $this->admin = $admin;
    $this->email = $email;
    $this->token = $token;
  }
  
  /**
   * Builds a link to the Sign Up page, containing the email and token
   * 
   * @return String URL
   */
  function getInviteUrl() {
    return (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . "/signup.php?token=" . $this->token."&email=".$this->email;
   
  }

}
