<?php
  /**************************************************************************\
  * phpGroupWare API - Auth from HTTP                                        *
  * http://www.phpgroupware.org/api                                          *
  * This file written by Dan Kuykendall <seek3r@phpgroupware.org>            *
  * and Joseph Engo <jengo@phpgroupware.org>                                 *
  * Authentication based on HTTP auth                                        *
  * Copyright (C) 2000, 2001 Dan Kuykendall                                  *
  * -------------------------------------------------------------------------*
  * This library is part of phpGroupWare (http://www.phpgroupware.org)       * 
  * This library is free software; you can redistribute it and/or modify it  *
  * under the terms of the GNU Lesser General Public License as published by *
  * the Free Software Foundation; either version 2.1 of the License,         *
  * or any later version.                                                    *
  * This library is distributed in the hope that it will be useful, but      *
  * WITHOUT ANY WARRANTY; without even the implied warranty of               *
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                     *
  * See the GNU Lesser General Public License for more details.              *
  * You should have received a copy of the GNU Lesser General Public License *
  * along with this library; if not, write to the Free Software Foundation,  *
  * Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA            *
  \**************************************************************************/

  /* $Id$ */

  class auth
  {

    function authenticate($username, $passwd) {
      global $phpgw_info, $phpgw, $PHP_AUTH_USER;
      
      if (isset($PHP_AUTH_USER)) {
        return True;
      } else {
        return False;
      }
    }
    function change_password($old_passwd, $new_passwd) {
      global $phpgw_info, $phpgw;
      return False;
    }
  }
?>