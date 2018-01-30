<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

  //This is the function to store in DB
  $password_hash = password_hash("", PASSWORD_DEFAULT);
  echo $password_hash;