<?php
    class ReportModel extends CI_Model{
        public function get_user(){
			$userid = $this->db->escape($_SESSION['user_id']);
			$sql = "SELECT user.username,user.password,user.user_id,CONCAT(user.lastname,' ',user.firstname) as Name,user.usertype_id,user.last_login,
						   usertype.name
					FROM user
					LEFT JOIN usertype 	ON usertype.usertype_id = user.usertype_id
					WHERE user.user_id = $userid AND 
					      user.is_active = 1";
			return $this->db->query($sql)->result();
		}
    }
?>