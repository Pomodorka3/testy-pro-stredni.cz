<?php
require_once '../../../scripts/app_config.php';
require_once '../../../scripts/db_connect.php';
require_once '../../../scripts/authorize.php';

session_start();
authorize_user(array("Administrator", "Main administrator"));

$output_start = '';
$output_middle = '';
$output_end = '';
/*$column_name = $_POST['column_name'];
$order = $_POST['order'];
if ($order == 'desc' || $order == 'DESC' ) {
	$order = 'asc';
} else {
	$order = 'desc';
}*/

/*$search = sprintf("SELECT s.item_id, s.item_name, s.item_description, s.item_price, s.likes, s.dislikes, s.teacher, s.bought_times, s.item_type, s.item_subject, s.school_class FROM shop s, users u WHERE u.school_id = s.school_id AND u.user_id = '%d' AND s.visible = 1 AND s.checked = 1 AND s.item_name COLLATE utf8_general_ci LIKE '%s' ORDER BY s.confirmed_date DESC;",
	mysqli_real_escape_string($connect, $_SESSION['user_id']),
	"%".$_POST['search']."%");*/
$user_id =  $_SESSION['user_id'];
//$search = "SELECT user_id, username, first_name, last_name, balance FROM users";



if (isset($_POST['group'])) {
	$groupSelect = implode(" AND ug.group_id=", $_POST['group']);
	//$search = "SELECT u.user_id, u.username, u.last_action FROM users u, users_groups ug WHERE u.user_id = ug.user_id AND ug.group_id=".$groupSelect."";
	$search = "SELECT u.user_id, u.username, u.last_action FROM users u, users_groups ug";
	$total_pages_sql = "SELECT COUNT(*) FROM users u, users_groups ug";
	if (isset($_POST['show'])) {
		$itemShow = implode(", ", $_POST['show']);
		$search = "SELECT u.user_id, u.username, u.last_action, ".$itemShow." FROM users u, users_groups ug";
		if (!empty($_POST['activated'])) {
			$search .= " WHERE u.user_id = ug.user_id AND ug.group_id=".$groupSelect." AND u.activated = ".$_POST['activated'];
			$total_pages_sql .= " WHERE u.user_id = ug.user_id AND ug.group_id=".$groupSelect." AND u.activated = ".$_POST['activated'];
			if (!empty($_POST['username']) || !empty($_POST['firstNameSearch']) || !empty($_POST['lastNameSearch']) || !empty($_POST['instagramSearch']) || !empty($_POST['facebookSearch'])) {
				$search .= " AND u.username COLLATE utf8_general_ci LIKE '%".$_POST['username']."%' AND u.first_name COLLATE utf8_general_ci LIKE '%".$_POST['firstNameSearch']."%' AND u.last_name COLLATE utf8_general_ci LIKE '%".$_POST['lastNameSearch']."%' AND u.instagram COLLATE utf8_general_ci LIKE '%".$_POST['instagramSearch']."%' AND u.facebook COLLATE utf8_general_ci LIKE '%".$_POST['facebookSearch']."%' AND u.user_id = ug.user_id AND ug.group_id=".$groupSelect."";
				$total_pages_sql .= " AND u.username COLLATE utf8_general_ci LIKE '%".$_POST['username']."%' AND u.first_name COLLATE utf8_general_ci LIKE '%".$_POST['firstNameSearch']."%' AND u.last_name COLLATE utf8_general_ci LIKE '%".$_POST['lastNameSearch']."%' AND u.instagram COLLATE utf8_general_ci LIKE '%".$_POST['instagramSearch']."%' AND u.facebook COLLATE utf8_general_ci LIKE '%".$_POST['facebookSearch']."%' AND u.user_id = ug.user_id AND ug.group_id=".$groupSelect."";
			}
			if (!empty($_POST['online']) && !empty($_POST['offline'])) {
			} elseif (!empty($_POST['offline'])) {
				$search .= " AND u.last_action < DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
				$total_pages_sql .= " AND u.last_action < DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
			} elseif (!empty($_POST['online'])) {
				$search .= " AND u.last_action > DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
				$total_pages_sql .= " AND u.last_action > DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
			} elseif (empty($_POST['online']) && empty($_POST['offline'])) {
				$search = '';
				$total_pages_sql = '';
			}
			/* else {
				if (!empty($_POST['username']) || !empty($_POST['firstNameSearch']) || !empty($_POST['lastNameSearch']) || !empty($_POST['instagramSearch']) || !empty($_POST['facebookSearch'])) {
					$search .= " AND u.username COLLATE utf8_general_ci LIKE '%".$_POST['username']."%' AND u.first_name COLLATE utf8_general_ci LIKE '%".$_POST['firstNameSearch']."%' AND u.last_name COLLATE utf8_general_ci LIKE '%".$_POST['lastNameSearch']."%' AND u.instagram COLLATE utf8_general_ci LIKE '%".$_POST['instagramSearch']."%' AND u.facebook COLLATE utf8_general_ci LIKE '%".$_POST['facebookSearch']."%' AND u.user_id = ug.user_id AND ug.group_id=".$groupSelect."";
					$total_pages_sql .= " AND u.username COLLATE utf8_general_ci LIKE '%".$_POST['username']."%' AND u.first_name COLLATE utf8_general_ci LIKE '%".$_POST['firstNameSearch']."%' AND u.last_name COLLATE utf8_general_ci LIKE '%".$_POST['lastNameSearch']."%' AND u.instagram COLLATE utf8_general_ci LIKE '%".$_POST['instagramSearch']."%' AND u.facebook COLLATE utf8_general_ci LIKE '%".$_POST['facebookSearch']."%' AND u.user_id = ug.user_id AND ug.group_id=".$groupSelect."";
				}
			}*/
		} elseif (!empty($_POST['banned'])) {
			$search = "SELECT u.user_id, u.username, u.last_action, ".$itemShow." FROM users u, users_groups ug, banned_users bu WHERE bu.banned_id = u.user_id AND bu.ban_active = 1 AND u.user_id = ug.user_id AND ug.group_id=".$groupSelect;
			$total_pages_sql = "SELECT COUNT(*) FROM users u, users_groups ug, banned_users bu WHERE bu.banned_id = u.user_id AND bu.ban_active = 1 AND u.user_id = ug.user_id AND ug.group_id=".$groupSelect;
			if (!empty($_POST['username']) || !empty($_POST['firstNameSearch']) || !empty($_POST['lastNameSearch']) || !empty($_POST['instagramSearch']) || !empty($_POST['facebookSearch'])) {
					$search .= " AND u.username COLLATE utf8_general_ci LIKE '%".$_POST['username']."%' AND u.first_name COLLATE utf8_general_ci LIKE '%".$_POST['firstNameSearch']."%' AND u.last_name COLLATE utf8_general_ci LIKE '%".$_POST['lastNameSearch']."%' AND u.instagram COLLATE utf8_general_ci LIKE '%".$_POST['instagramSearch']."%' AND u.facebook COLLATE utf8_general_ci LIKE '%".$_POST['facebookSearch']."%'";
					$total_pages_sql .= " AND u.username COLLATE utf8_general_ci LIKE '%".$_POST['username']."%' AND u.first_name COLLATE utf8_general_ci LIKE '%".$_POST['firstNameSearch']."%' AND u.last_name COLLATE utf8_general_ci LIKE '%".$_POST['lastNameSearch']."%' AND u.instagram COLLATE utf8_general_ci LIKE '%".$_POST['instagramSearch']."%' AND u.facebook COLLATE utf8_general_ci LIKE '%".$_POST['facebookSearch']."%'";
				}
			if (!empty($_POST['online']) && !empty($_POST['offline'])) {
			} elseif (!empty($_POST['offline'])) {
				$search .= " AND u.last_action < DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
				$total_pages_sql .= " AND u.last_action < DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
			} elseif (!empty($_POST['online'])) {
				$search .= " AND u.last_action > DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
				$total_pages_sql .= " AND u.last_action > DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
			} elseif (empty($_POST['online']) && empty($_POST['offline'])) {
				$search = '';
				$total_pages_sql = '';
			}
		} else {
			if (!empty($_POST['username']) || !empty($_POST['firstNameSearch']) || !empty($_POST['lastNameSearch']) || !empty($_POST['instagramSearch']) || !empty($_POST['facebookSearch'])) {
				$search .= " WHERE u.username COLLATE utf8_general_ci LIKE '%".$_POST['username']."%' AND u.first_name COLLATE utf8_general_ci LIKE '%".$_POST['firstNameSearch']."%' AND u.last_name COLLATE utf8_general_ci LIKE '%".$_POST['lastNameSearch']."%' AND u.instagram COLLATE utf8_general_ci LIKE '%".$_POST['instagramSearch']."%' AND u.facebook COLLATE utf8_general_ci LIKE '%".$_POST['facebookSearch']."%' AND u.user_id = ug.user_id AND ug.group_id=".$groupSelect.""; 
				$total_pages_sql .= " WHERE u.username COLLATE utf8_general_ci LIKE '%".$_POST['username']."%' AND u.first_name COLLATE utf8_general_ci LIKE '%".$_POST['firstNameSearch']."%' AND u.last_name COLLATE utf8_general_ci LIKE '%".$_POST['lastNameSearch']."%' AND u.instagram COLLATE utf8_general_ci LIKE '%".$_POST['instagramSearch']."%' AND u.facebook COLLATE utf8_general_ci LIKE '%".$_POST['facebookSearch']."%' AND u.user_id = ug.user_id AND ug.group_id=".$groupSelect."";
			} else {
					$search .= " WHERE u.user_id = ug.user_id AND ug.group_id=".$groupSelect."";
					$total_pages_sql .= " WHERE u.user_id = ug.user_id AND ug.group_id=".$groupSelect."";
			}
			if (!empty($_POST['online']) && !empty($_POST['offline'])) {
			} elseif (!empty($_POST['offline'])) {
				$search .= " AND u.last_action < DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
				$total_pages_sql .= " AND u.last_action < DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
			} elseif (!empty($_POST['online'])) {
				$search .= " AND u.last_action > DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
				$total_pages_sql .= " AND u.last_action > DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
			} elseif (empty($_POST['online']) && empty($_POST['offline'])) {
				$search = '';
				$total_pages_sql = '';
			}
		}
	} else {
		$search = "SELECT u.user_id, u.username, u.last_action FROM users u, users_groups ug WHERE u.user_id = ug.user_id AND ug.group_id=".$groupSelect;
		$total_pages_sql .= " WHERE u.user_id = ug.user_id AND ug.group_id=".$groupSelect."";
		if (!empty($_POST['activated'])) {
			$search .= " AND u.activated = ".$_POST['activated'];
			$total_pages_sql .= " AND u.activated = ".$_POST['activated']; 
		} elseif (!empty($_POST['banned'])) {
			$search = "SELECT u.user_id, u.username, u.last_action FROM users u, users_groups ug, banned_users bu WHERE bu.banned_id = u.user_id AND bu.ban_active = 1 AND u.user_id = ug.user_id AND ug.group_id=".$groupSelect;
			$total_pages_sql = "SELECT COUNT(*) FROM users u, users_groups ug, banned_users bu WHERE bu.banned_id = u.user_id AND bu.ban_active = 1 AND u.user_id = ug.user_id AND ug.group_id=".$groupSelect;
		}
		if (!empty($_POST['username']) || !empty($_POST['firstNameSearch']) || !empty($_POST['lastNameSearch']) || !empty($_POST['instagramSearch']) || !empty($_POST['facebookSearch'])) {
			$search .= " AND u.username COLLATE utf8_general_ci LIKE '%".$_POST['username']."%' AND u.first_name COLLATE utf8_general_ci LIKE '%".$_POST['firstNameSearch']."%' AND u.last_name COLLATE utf8_general_ci LIKE '%".$_POST['lastNameSearch']."%' AND u.instagram COLLATE utf8_general_ci LIKE '%".$_POST['instagramSearch']."%' AND u.facebook COLLATE utf8_general_ci LIKE '%".$_POST['facebookSearch']."%'";
			$total_pages_sql .= " AND u.username COLLATE utf8_general_ci LIKE '%".$_POST['username']."%' AND u.first_name COLLATE utf8_general_ci LIKE '%".$_POST['firstNameSearch']."%' AND u.last_name COLLATE utf8_general_ci LIKE '%".$_POST['lastNameSearch']."%' AND u.instagram COLLATE utf8_general_ci LIKE '%".$_POST['instagramSearch']."%' AND u.facebook COLLATE utf8_general_ci LIKE '%".$_POST['facebookSearch']."%'";
		}
		if (!empty($_POST['online']) && !empty($_POST['offline'])) {
		} elseif (!empty($_POST['offline'])) {
			$search .= " AND u.last_action < DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
			$total_pages_sql .= " AND u.last_action < DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
		} elseif (!empty($_POST['online'])) {
			$search .= " AND u.last_action > DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
			$total_pages_sql .= " AND u.last_action > DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
		} elseif (empty($_POST['online']) && empty($_POST['offline'])) {
			$search = '';
			$total_pages_sql = '';
		}
	}
} else {
	if (isset($_POST['show'])) {
		$itemShow = implode(", ", $_POST['show']);
		$search = "SELECT u.user_id, u.username, u.last_action, ".$itemShow." FROM users u";
		$total_pages_sql .= "SELECT COUNT(*) FROM users u";
		if (!empty($_POST['activated'])) {
			$search .= " WHERE u.activated = ".$_POST['activated'];
			$total_pages_sql .= " WHERE u.activated = ".$_POST['activated'];
			if (!empty($_POST['username']) || !empty($_POST['firstNameSearch']) || !empty($_POST['lastNameSearch']) || !empty($_POST['instagramSearch']) || !empty($_POST['facebookSearch'])) {
				$search .= " AND u.username COLLATE utf8_general_ci LIKE '%".$_POST['username']."%' AND u.first_name COLLATE utf8_general_ci LIKE '%".$_POST['firstNameSearch']."%' AND u.last_name COLLATE utf8_general_ci LIKE '%".$_POST['lastNameSearch']."%' AND u.instagram COLLATE utf8_general_ci LIKE '%".$_POST['instagramSearch']."%' AND u.facebook COLLATE utf8_general_ci LIKE '%".$_POST['facebookSearch']."%'";
				$total_pages_sql .= " AND u.username COLLATE utf8_general_ci LIKE '%".$_POST['username']."%' AND u.first_name COLLATE utf8_general_ci LIKE '%".$_POST['firstNameSearch']."%' AND u.last_name COLLATE utf8_general_ci LIKE '%".$_POST['lastNameSearch']."%' AND u.instagram COLLATE utf8_general_ci LIKE '%".$_POST['instagramSearch']."%' AND u.facebook COLLATE utf8_general_ci LIKE '%".$_POST['facebookSearch']."%'";
				}
				if (!empty($_POST['online']) && !empty($_POST['offline'])) {
				} elseif (!empty($_POST['offline'])) {
					$search .= " AND u.last_action < DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
					$total_pages_sql .= " AND u.last_action < DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
				} elseif (!empty($_POST['online'])) {
					$search .= " AND u.last_action > DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
					$total_pages_sql .= " AND u.last_action > DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
				} elseif (empty($_POST['online']) && empty($_POST['offline'])) {
					$search = '';
					$total_pages_sql = '';
				}
		} elseif (!empty($_POST['banned'])) {
			$search = "SELECT u.user_id, u.username, u.last_action, ".$itemShow." FROM users u, banned_users bu WHERE bu.banned_id = u.user_id AND bu.ban_active = 1";
			$total_pages_sql = "SELECT COUNT(*) FROM users u, banned_users bu WHERE bu.banned_id = u.user_id AND bu.ban_active = 1";
			if (!empty($_POST['username']) || !empty($_POST['firstNameSearch']) || !empty($_POST['lastNameSearch']) || !empty($_POST['instagramSearch']) || !empty($_POST['facebookSearch'])) {
				$search .= " AND u.username COLLATE utf8_general_ci LIKE '%".$_POST['username']."%' AND u.first_name COLLATE utf8_general_ci LIKE '%".$_POST['firstNameSearch']."%' AND u.last_name COLLATE utf8_general_ci LIKE '%".$_POST['lastNameSearch']."%' AND u.instagram COLLATE utf8_general_ci LIKE '%".$_POST['instagramSearch']."%' AND u.facebook COLLATE utf8_general_ci LIKE '%".$_POST['facebookSearch']."%'";
				$total_pages_sql .= " AND u.username COLLATE utf8_general_ci LIKE '%".$_POST['username']."%' AND u.first_name COLLATE utf8_general_ci LIKE '%".$_POST['firstNameSearch']."%' AND u.last_name COLLATE utf8_general_ci LIKE '%".$_POST['lastNameSearch']."%' AND u.instagram COLLATE utf8_general_ci LIKE '%".$_POST['instagramSearch']."%' AND u.facebook COLLATE utf8_general_ci LIKE '%".$_POST['facebookSearch']."%'";
				}
				if (!empty($_POST['online']) && !empty($_POST['offline'])) {
				} elseif (!empty($_POST['offline'])) {
					$search .= " AND u.last_action < DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
					$total_pages_sql .= " AND u.last_action < DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
				} elseif (!empty($_POST['online'])) {
					$search .= " AND u.last_action > DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
					$total_pages_sql .= " AND u.last_action > DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
				} elseif (empty($_POST['online']) && empty($_POST['offline'])) {
					$search = '';
					$total_pages_sql = '';
				}
		} else {
			if (!empty($_POST['username']) || !empty($_POST['firstNameSearch']) || !empty($_POST['lastNameSearch']) || !empty($_POST['instagramSearch']) || !empty($_POST['facebookSearch'])) {
			$search .= " WHERE u.username COLLATE utf8_general_ci LIKE '%".$_POST['username']."%' AND u.first_name COLLATE utf8_general_ci LIKE '%".$_POST['firstNameSearch']."%' AND u.last_name COLLATE utf8_general_ci LIKE '%".$_POST['lastNameSearch']."%' AND u.instagram COLLATE utf8_general_ci LIKE '%".$_POST['instagramSearch']."%' AND u.facebook COLLATE utf8_general_ci LIKE '%".$_POST['facebookSearch']."%'";
			$total_pages_sql .= " WHERE u.username COLLATE utf8_general_ci LIKE '%".$_POST['username']."%' AND u.first_name COLLATE utf8_general_ci LIKE '%".$_POST['firstNameSearch']."%' AND u.last_name COLLATE utf8_general_ci LIKE '%".$_POST['lastNameSearch']."%' AND u.instagram COLLATE utf8_general_ci LIKE '%".$_POST['instagramSearch']."%' AND u.facebook COLLATE utf8_general_ci LIKE '%".$_POST['facebookSearch']."%'";
			}
			if (!empty($_POST['online']) && !empty($_POST['offline'])) {
			} elseif (!empty($_POST['offline'])) {
				$search .= " AND u.last_action < DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
				$total_pages_sql .= " AND u.last_action < DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
			} elseif (!empty($_POST['online'])) {
				$search .= " AND u.last_action > DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
				$total_pages_sql .= " AND u.last_action > DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
			} elseif (empty($_POST['online']) && empty($_POST['offline'])) {
				$search = '';
				$total_pages_sql = '';
			}
		}
	} else {
		$search = "SELECT u.user_id, u.username, u.last_action FROM users u";
		$total_pages_sql = "SELECT COUNT(*) FROM users u";
		if (!empty($_POST['activated'])) {
			$search .= " WHERE u.activated = ".$_POST['activated'];
			$total_pages_sql .= " WHERE u.activated = ".$_POST['activated'];
			if (!empty($_POST['username']) || !empty($_POST['firstNameSearch']) || !empty($_POST['lastNameSearch']) || !empty($_POST['instagramSearch']) || !empty($_POST['facebookSearch'])) {
				$search .= " AND u.username COLLATE utf8_general_ci LIKE '%".$_POST['username']."%' AND u.first_name COLLATE utf8_general_ci LIKE '%".$_POST['firstNameSearch']."%' AND u.last_name COLLATE utf8_general_ci LIKE '%".$_POST['lastNameSearch']."%' AND u.instagram COLLATE utf8_general_ci LIKE '%".$_POST['instagramSearch']."%' AND u.facebook COLLATE utf8_general_ci LIKE '%".$_POST['facebookSearch']."%'";
				$total_pages_sql .= " AND u.username COLLATE utf8_general_ci LIKE '%".$_POST['username']."%' AND u.first_name COLLATE utf8_general_ci LIKE '%".$_POST['firstNameSearch']."%' AND u.last_name COLLATE utf8_general_ci LIKE '%".$_POST['lastNameSearch']."%' AND u.instagram COLLATE utf8_general_ci LIKE '%".$_POST['instagramSearch']."%' AND u.facebook COLLATE utf8_general_ci LIKE '%".$_POST['facebookSearch']."%'";
				}
			if (!empty($_POST['online']) && !empty($_POST['offline'])) {
			} elseif (!empty($_POST['offline'])) {
				$search .= " AND u.last_action < DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
				$total_pages_sql .= " AND u.last_action < DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
			} elseif (!empty($_POST['online'])) {
				$search .= " AND u.last_action > DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
				$total_pages_sql .= " AND u.last_action > DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
			} elseif (empty($_POST['online']) && empty($_POST['offline'])) {
				$search = '';
				$total_pages_sql = '';
			}
		} elseif (!empty($_POST['banned'])) {
			$search = "SELECT u.user_id, u.username, u.last_action FROM users u, banned_users bu WHERE bu.banned_id = u.user_id AND bu.ban_active = 1";
			$total_pages_sql = "SELECT COUNT(*) FROM users u, banned_users bu WHERE bu.banned_id = u.user_id AND bu.ban_active = 1";
			if (!empty($_POST['username']) || !empty($_POST['firstNameSearch']) || !empty($_POST['lastNameSearch']) || !empty($_POST['instagramSearch']) || !empty($_POST['facebookSearch'])) {
				$search .= " AND u.username COLLATE utf8_general_ci LIKE '%".$_POST['username']."%' AND u.first_name COLLATE utf8_general_ci LIKE '%".$_POST['firstNameSearch']."%' AND u.last_name COLLATE utf8_general_ci LIKE '%".$_POST['lastNameSearch']."%' AND u.instagram COLLATE utf8_general_ci LIKE '%".$_POST['instagramSearch']."%' AND u.facebook COLLATE utf8_general_ci LIKE '%".$_POST['facebookSearch']."%'";
				$total_pages_sql .= " AND u.username COLLATE utf8_general_ci LIKE '%".$_POST['username']."%' AND u.first_name COLLATE utf8_general_ci LIKE '%".$_POST['firstNameSearch']."%' AND u.last_name COLLATE utf8_general_ci LIKE '%".$_POST['lastNameSearch']."%' AND u.instagram COLLATE utf8_general_ci LIKE '%".$_POST['instagramSearch']."%' AND u.facebook COLLATE utf8_general_ci LIKE '%".$_POST['facebookSearch']."%'";
			}
			if (!empty($_POST['online']) && !empty($_POST['offline'])) {
			} elseif (!empty($_POST['offline'])) {
				$search .= " AND u.last_action < DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
				$total_pages_sql .= " AND u.last_action < DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
			} elseif (!empty($_POST['online'])) {
				$search .= " AND u.last_action > DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
				$total_pages_sql .= " AND u.last_action > DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
			} elseif (empty($_POST['online']) && empty($_POST['offline'])) {
				$search = '';
				$total_pages_sql = '';
			}
		} else {
			if (!empty($_POST['username']) || !empty($_POST['firstNameSearch']) || !empty($_POST['lastNameSearch']) || !empty($_POST['instagramSearch']) || !empty($_POST['facebookSearch'])) {
				$search .= " WHERE u.username COLLATE utf8_general_ci LIKE '%".$_POST['username']."%' AND u.first_name COLLATE utf8_general_ci LIKE '%".$_POST['firstNameSearch']."%' AND u.last_name COLLATE utf8_general_ci LIKE '%".$_POST['lastNameSearch']."%' AND u.instagram COLLATE utf8_general_ci LIKE '%".$_POST['instagramSearch']."%' AND u.facebook COLLATE utf8_general_ci LIKE '%".$_POST['facebookSearch']."%'";
				$total_pages_sql .= " WHERE u.username COLLATE utf8_general_ci LIKE '%".$_POST['username']."%' AND u.first_name COLLATE utf8_general_ci LIKE '%".$_POST['firstNameSearch']."%' AND u.last_name COLLATE utf8_general_ci LIKE '%".$_POST['lastNameSearch']."%' AND u.instagram COLLATE utf8_general_ci LIKE '%".$_POST['instagramSearch']."%' AND u.facebook COLLATE utf8_general_ci LIKE '%".$_POST['facebookSearch']."%'";
				if (!empty($_POST['online']) && !empty($_POST['offline'])) {
				} elseif (!empty($_POST['offline'])) {
					$search .= " AND u.last_action < DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
					$total_pages_sql .= " AND u.last_action < DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
				} elseif (!empty($_POST['online'])) {
					$search .= " AND u.last_action > DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
					$total_pages_sql .= " AND u.last_action > DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
				} elseif (empty($_POST['online']) && empty($_POST['offline'])) {
					$search = '';
					$total_pages_sql = '';
				}
			} else {
				if (!empty($_POST['online']) && !empty($_POST['offline'])) {
				} elseif (!empty($_POST['offline'])) {
					$search .= " WHERE u.last_action < DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
					$total_pages_sql .= " WHERE u.last_action < DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
				} elseif (!empty($_POST['online'])) {
					$search .= " WHERE u.last_action > DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
					$total_pages_sql .= " WHERE u.last_action > DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
				} elseif (empty($_POST['online']) && empty($_POST['offline'])) {
					$search = '';
					$total_pages_sql = '';
				}
			}
		}
	}
}
$search .= " ORDER BY u.user_id ASC";

if (isset($_POST['page'])) {
	$page = $_POST['page'];
	if (DEBUG_MODE == true && user_in_group("Main administrator", $_SESSION['user_id'])) {
		echo "<b>Page</b>: ".$page."<br>";
	}
} else {
	$page = 1;
	if (DEBUG_MODE == true && user_in_group("Main administrator", $_SESSION['user_id'])) {
		echo "<b>Page</b>: ".$page."<br>";
	}
}
//Items per page, but there is one row less, because user_id=0 (System) is hided
$items_per_page = 16; // $items_per_page = $items_per_page - 1
$offset = ($page-1)*$items_per_page;
if (DEBUG_MODE == true && user_in_group("Main administrator", $_SESSION['user_id'])) {
	echo "<b>COUNT SQL: </b>".$total_pages_sql."<br>";
}
$total_pages_sql_result = mysqli_query($connect, $total_pages_sql);
$total_rows = mysqli_fetch_array($total_pages_sql_result)[0];
$total_pages = ceil($total_rows/$items_per_page);
if (DEBUG_MODE == true && user_in_group("Main administrator", $_SESSION['user_id'])) {
	echo "<b>TOTAL_ROWS SQL: </b>".$total_rows;
}
$search .= " LIMIT $offset, $items_per_page";

if (DEBUG_MODE == true && user_in_group("Main administrator", $_SESSION['user_id'])) {
	echo "<br>".$search;
}
/*if ((isset($_POST['column_name'])) && (isset($_POST['order']))) {
	$search .= " ORDER BY ".$_POST['column_name']." ".$order;
		echo $search;
} else {
	$search .= " ORDER BY s.confirmed_date DESC";
}*/

$search_query = mysqli_query($connect, $search);
$search_query2 = mysqli_query($connect, $search);

$online_check = 'SELECT last_action FROM users WHERE activated = 1';
$online_check_query = mysqli_query($connect, $online_check);

$online_counter = 0;
while ($online_row = mysqli_fetch_row($online_check_query)) {
	$date = date("Y-m-d H:i:s", strtotime('-2 minutes'));
	if ($online_row[0] > $date ) {
		$online_counter += 1;
	}
}

if (mysqli_num_rows($search_query) == 0) {
	echo "<p class='font-weight-bold text-center'>Nebyly nalezeni žádní uživatelé!</p>";
} else {
	$i = 0;
	$post2 = mysqli_fetch_array($search_query2);
		echo "Celkem uživatelů online: <span class='font-weight-bold'>".$online_counter."</span>";
		echo $output_start = '
		<div class="table-responsive">
			<table class="table table-hover table-striped table-sm">
				<thead>
					<tr>
						<th scope="col" style="font-family: \'Baloo\', cursive;">ID</th>
						<th scope="col" style="font-family: \'Baloo\', cursive;">Status</th>
						<th scope="col" style="font-family: \'Baloo\', cursive;">Uživatel</th>';
		if (isset($post2['first_name'])) {
			echo "<th scope='col' style='font-family: \"Baloo\", cursive;'>Křestní jméno</th>";
		}
		if (isset($post2['last_name'])) {
			echo "<th scope='col' style='font-family: \"Baloo\", cursive;'>Příjmení</th>";
		}
		if (isset($post2['email'])) {
			echo "<th scope='col' style='font-family: \"Baloo\", cursive;'>Email</th>";
		}
		if (isset($post2['balance'])) {
			echo "<th scope='col' style='font-family: \"Baloo\", cursive;'>Konto</th>";
		}
		if (isset($post2['bought_items'])) {
			echo "<th scope='col' style='font-family: \"Baloo\", cursive;'>Koupeno testů</th>";
		}
		if (isset($post2['confirmed_items'])) {
			echo "<th scope='col' style='font-family: \"Baloo\", cursive;'>Schváleno testů</th>";
		}
		if (isset($post2['declined_items'])) {
			echo "<th scope='col' style='font-family: \"Baloo\", cursive;'>Odmítnuto testů</th>";
		}
		if (isset($post2['confirmed_reports'])) {
			echo "<th scope='col' style='font-family: \"Baloo\", cursive;'>Schváleno reklamací</th>";
		}
		if (isset($post2['declined_reports'])) {
			echo "<th scope='col' style='font-family: \"Baloo\", cursive;'>Odmítnuto reklamací</th>";
		}
		if (isset($post2['removed_items'])) {
			echo "<th scope='col' style='font-family: \"Baloo\", cursive;'>Odstraněno testů</th>";
		}
		if (isset($post2['bank_number'])) {
			echo "<th scope='col' style='font-family: \"Baloo\", cursive;'>Bankovní účet</th>";
		}
		if (isset($post2['last_action'])) {
			echo "<th scope='col' style='font-family: \"Baloo\", cursive;'>Naposled online</th>";
		}
		if (isset($post2['register_date'])) {
			echo "<th scope='col' style='font-family: \"Baloo\", cursive;'>Datum registrace</th>";
		}
		if (isset($post2['school_id'])) {
			echo "<th scope='col' style='font-family: \"Baloo\", cursive;'>Škola</th>";
		}
		if (isset($post2['instagram'])) {
			echo "<th scope='col' style='font-family: \"Baloo\", cursive;'>Instagram</th>";
		}
		if (isset($post2['facebook'])) {
			echo "<th scope='col' style='font-family: \"Baloo\", cursive;'>Facebook</th>";
		}
		if (isset($post2['register_ip'])) {
			echo "<th scope='col' style='font-family: \"Baloo\", cursive;'>Registrační IP</th>";
		}
		if (isset($post2['last_ip'])) {
			echo "<th scope='col' style='font-family: \"Baloo\", cursive;'>Poslední IP</th>";
		}
		//data-toggle="tooltip" title="Refresh list"
		echo $output_end = '
						<th scope="col" class="text-right"><a class="btn btn-outline-dark btn-sm p-2" id="refresh-table" style="border-radius:50px;"><i class="fas fa-sync-alt"></i></a></th>
					</tr>
				</thead>
				<tbody>';
	while ($post = mysqli_fetch_array($search_query)) {
		$i++;
		/*if (isset($post['balance'])) {
			$post['balance'] = "Balance: ".$post['balance'];
		}
		echo "Id: ".$post['user_id']." ".$post['balance']."";
		echo "<br>";
		*/
		//Don't show System user
		if ($post['user_id'] == 0) {
			continue;
		}
		//---------
		$status = '';
		$date = date("Y-m-d H:i:s", strtotime('-2 minutes'));
		if ($post['last_action'] > $date ) {
			$status = '<i class="fas fa-circle text-success" data-toggle="tooltip" title="Online"></i>';
		} else {
			$status = '<i class="far fa-circle grey-text" data-toggle="tooltip" title="Offline"></i>';
		}
		echo $post_row_start = sprintf("
			<tr class=' h-100'>
				<td class='my-auto'>%d</td>
				<td class='my-auto'>%s</td>
				<td class='my-auto'><a href='".SITE_ROOT."profile_show.php?profile_id=%d' class='text-primary font-weight-bold'>%s</a></td>
				", 
				$post['user_id'],
				$status,
				$post['user_id'],
				$post['username']);
		if (isset($post['first_name'])) {
			if (empty($post['first_name'])) {
				echo "<td class='text-danger'>-</td>";
			} else {
				echo "<td>".$post['first_name']."</td>";
			}
		}
		if (isset($post['last_name'])) {
			if (empty($post['last_name'])) {
				echo "<td class='text-danger'>-</td>";
			} else {
				echo "<td>".$post['last_name']."</td>";
			}
		}
		if (isset($post['email'])) {
			echo "<td>".$post['email']."</td>";
		}
		if (isset($post['balance'])) {
			echo "<td>".$post['balance']."</td>";
		}
		if (isset($post['bought_items'])) {
			echo "<td>".$post['bought_items']."</td>";
		}
		if (isset($post['confirmed_items'])) {
			echo "<td>".$post['confirmed_items']."</td>";
		}
		if (isset($post['declined_items'])) {
			echo "<td>".$post['declined_items']."</td>";
		}
		if (isset($post['confirmed_reports'])) {
			echo "<td>".$post['confirmed_reports']."</td>";
		}
		if (isset($post['declined_reports'])) {
			echo "<td>".$post['declined_reports']."</td>";
		}
		if (isset($post['removed_items'])) {
			echo "<td>".$post['removed_items']."</td>";
		}
		if (isset($post['bank_number'])) {
			if ($post['bank_number'] == 0) {
				echo "<td class='text-danger'>-</td>";
			} else {
				echo "<td>".$post['bank_number']."</td>";
			}
		}
		if (isset($post['last_action'])) {
			if ($post['last_action'] == '0000-00-00 00:00:00') {
				echo "<td>-</td>";
			} else {
				echo "<td>".date('d.m.Y H:i', strtotime($post['last_action']))."</td>";
			}
		}
		if (isset($post['register_date'])) {
			if ($post['register_date'] == '0000-00-00 00:00:00') {
				echo "<td>-</td>";
			} else {
				echo "<td>".date('d.m.Y H:i', strtotime($post['register_date']))."</td>";
			}
		}
		if (isset($post['school_id'])) {
			if ($post['school_id'] == 0) {
				echo "<td class='text-danger'>-</td>";
			} else {
				//echo "<td>".$post['school_id']."</td>";
				$school_name = sprintf("SELECT s.school_name FROM school s, users u WHERE u.user_id = '%d' AND u.school_id = s.school_id;",
				mysqli_real_escape_string($connect, $post['user_id']));
				$school_name_query = mysqli_query($connect, $school_name);
				$school_name_row = mysqli_fetch_array($school_name_query);
				echo "<td><a href='".SITE_ROOT."school_info.php?school_id=".$post['school_id']."' class='text-primary font-weight-bold'>".$school_name_row['school_name']."</a></td>";
			}
		}
		if (isset($post['instagram'])) {
			if (empty($post['instagram'])) {
				echo "<td class='text-danger'>-</td>";
			} else {
				echo "<td><a class='text-primary font-weight-bold' href='https://www.instagram.com/".$post['instagram']."/'><u>".$post['instagram']."</u></a></td>";
			}
		}
		if (isset($post['facebook'])) {
			if (empty($post['facebook'])) {
				echo "<td class='text-danger'>-</td>";
			} else {
				echo "<td><a class='text-primary font-weight-bold' href='https://www.facebook.com/".$post['facebook']."/'><u>".$post['facebook']."</u></a></td>";
			}
		}
		if (isset($post['register_ip'])) {
			echo "<td>".$post['register_ip']."</td>";
		}
		if (isset($post['last_ip'])) {
			echo "<td>".$post['last_ip']."</td>";
		}
		//href='admin_action_user.php?block_id=%d'
		//href='admin_action_user.php?unblock_id=%d'
		//<a class='mx-2' href='' data-toggle='tooltip' title='Delete user'><i class='fas fa-trash-alt text-primary'></i></a>
		$post_row_end = sprintf("
				<td><a class='mx-2' data-toggle='modal' data-target='#banModal%d'><i class='fas fa-lock text-danger' data-toggle='tooltip' title='Zablokovat'></i></a>
				<a class='mx-2' data-toggle='modal' data-target='#unbanModal%d'><i class='fas fa-lock-open text-success' data-toggle='tooltip' title='Odblokovat'></i></a>
			",
			$i,
			$i);
			//Ban confirm modal
			$post_row_end .= sprintf("<div class='modal fade' id='banModal%d' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
		            <div class='modal-dialog modal-center' role='document'>
		              <div class='modal-content'>
		                <div class='modal-header'>
		                  <h4 class='modal-title w-100' id='banModalLabel%d'>Zablokovat uživatele?</h4>
		                  <button type='button' class='close' data-dismiss='modal' aria-label='Zavřít'>
		                    <span aria-hidden='true'>&times;</span>
		                  </button>
		                </div>
		                <div class='modal-body d-flex'>
		                  <a class='btn btn-success btn-sm mx-auto' href='".SITE_ROOT."admin_action_user.php?block_id=%d'>Potvrdit</a>
		                </div>
		              </div>
		            </div>
		          </div>",
			$i,
			$i,
			$post['user_id']);
			//Unban confirm modal
			$post_row_end .= sprintf("<div class='modal fade' id='unbanModal%d' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
		            <div class='modal-dialog modal-center' role='document'>
		              <div class='modal-content'>
		                <div class='modal-header'>
		                  <h4 class='modal-title w-100' id='unbanModalLabel%d'>Odblokovat uživatele?</h4>
		                  <button type='button' class='close' data-dismiss='modal' aria-label='Zavřít'>
		                    <span aria-hidden='true'>&times;</span>
		                  </button>
		                </div>
		                <div class='modal-body d-flex'>
		                  <a class='btn btn-success btn-sm mx-auto' href='".SITE_ROOT."admin_action_user.php?unblock_id=%d'>Potvrdit</a>
		                </div>
		              </div>
		            </div>
		          </div>",
			$i,
			$i,
	      	$post['user_id']);
		if (user_in_group("Main administrator", $user_id)) {
			//Set it only for Main administrators
			$post_row_end .= sprintf("<a class='mx-2' data-toggle='modal' data-target='#balanceModal%d'><i class='fas fa-hand-holding-usd text-primary' data-toggle='tooltip' title='Nastavit konto'></i></a>
				<div class='modal fade' id='balanceModal%d' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
		            <div class='modal-dialog modal-center' role='document'>
		              <div class='modal-content'>
		                <div class='modal-header'>
		                  <h4 class='modal-title w-100' id='balanceModalLabel%d'>Nastavení konta uživatele</h4>
		                  <button type='button' class='close' data-dismiss='modal' aria-label='Zavřít'>
		                    <span aria-hidden='true'>&times;</span>
		                  </button>
		                </div>
		                <div class='modal-body'>
		                  <form action='".SITE_ROOT."admin_action_user.php?action=balance&user_id=%d' class='text-center' method='post'>
		                    <input type='number' min='0' max='9999' class='form-control mb-3' autocomplete='off' required placeholder='Konto uživatele (0-9999 Kč)' name='new_balance'>
		                    <textarea name='reason' rows='6' class='form-control rounded mb-4' maxlength='40' autocomplete='off' placeholder='Důvod:'></textarea>
		                    <button type='submit' class='btn btn-outline-success btn-sm d-flex mx-auto mt-3'>Potvrdit</button>
		                  </form>
		                </div>
		              </div>
		            </div>
		          </div>",
			$i,
			$i,
	      	$i,
			$post['user_id']);
			}
			$post_row_end .= sprintf("<a class='mx-2' data-toggle='modal' data-target='#groupManage%d'><i class='fas fa-user-tag text-primary' data-toggle='tooltip' title='Spravovat skupiny'></i></a>
				<div class='modal fade' id='groupManage%d' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
			            <div class='modal-dialog modal-center' role='document'>
			              <div class='modal-content'>
			                <div class='modal-header'>
			                  <h4 class='modal-title w-100' id='groupManageLabel%d'>Vyberte akci</h4>
			                  <button type='button' class='close' data-dismiss='modal' aria-label='Zavřít'>
			                    <span aria-hidden='true'>&times;</span>
			                  </button>
			                </div>
			                <div class='modal-body'>
			                  <form action='".SITE_ROOT."admin_action_user.php?user_id=%d' class='text-center formAdmin' method='post'>
			                  <div class='row'>
								<div class='col-md-4'>
									<div class='custom-control custom-radio adminOptions'>
										<input type='radio' class='custom-control-input' id='firstRadio%d' name='action' value='setAdmin' required>
										<label for='firstRadio%d' class='custom-control-label'>+ Administrátor</label>
									</div>
									<div class='custom-control custom-radio mb-3'>
										<input type='radio' class='custom-control-input' id='secondRadio%d' name='action' value='removeAdmin'>
										<label for='secondRadio%d' class='custom-control-label'>- Administrátor</label>
									</div>
								</div>
								<div class='col-md-4'>
									<div class='custom-control custom-radio'>
										<input type='radio' class='custom-control-input' id='thirdRadio%d' name='action' value='setSupport' required>
										<label for='thirdRadio%d' class='custom-control-label'>+ Support</label>
									</div>
									<div class='custom-control custom-radio mb-3'>
										<input type='radio' class='custom-control-input' id='fourthRadio%d' name='action' value='removeSupport'>
										<label for='fourthRadio%d' class='custom-control-label'>- Support</label>
									</div>
								</div>
								<div class='col-md-4'>
									<div class='custom-control custom-radio'>
										<input type='radio' class='custom-control-input' id='fifthRadio%d' name='action' value='setValidator' required>
										<label for='fifthRadio%d' class='custom-control-label'>+ Validátor</label>
									</div>
									<div class='custom-control custom-radio mb-3'>
										<input type='radio' class='custom-control-input' id='sixthRadio%d' name='action' value='removeValidator'>
										<label for='sixthRadio%d' class='custom-control-label'>- Validátor</label>
									</div>
								</div>
			                  </div>
								<input type='password' pattern='[0-9]{1-6}' class='form-control' autocomplete='off' required placeholder='Admin heslo' maxlength='6' name='admin_set_pwd'>
								<!--<input type='text' class='form-control removeReason' autocomplete='off' required placeholder='Důvod' maxlength='400' name='remove_reason'>-->
			                    <button type='submit' class='btn btn-outline-success btn-sm d-flex mx-auto mt-3'>Potvrdit</button>
			                  </form>
			                </div>
			              </div>
			            </div>
			          </div>",
					$i,
					$i,
			      	$i,
			      	$post['user_id'],
			      	$i,
			      	$i,
			      	$i,
			      	$i,
			      	$i,
			      	$i,
			      	$i,
			      	$i,
			     	$i,
			      	$i,
			      	$i,
			      	$i);
		$post_row_end .= "</td></tr>";
		echo $post_row_end;
	}
	echo $table_end .= '</tbody></table></div>';
	
	$current_number = $page;
	if ($total_pages == 1) {
		$prev_class = 'd-none';
		$next_class = 'd-none';
		$current_page = $current_number;
		$prev_number = '';
		$next_number = '';
	} else {
		$prev_number = $current_number - 1;
		$next_number = $current_number + 1;
	}
	if ($page <= 1) {
		$prev_class = 'd-none';
		$prev_number = '';
	} else {
		$prev_class = '';
	}
	if ($page >= $total_pages) {
		$next_class = 'd-none';
		$next_number = '';
	} else {
		$next_class = '';
	}
	echo $pagination = "
	<div class='container d-flex mx-auto'>
		<nav aria-label='Page nav' class='mx-auto'>
			<ul class='pagination pg-blue'>
				<li class='page-item'><a id='1' class='page-link page'><i class='fas fa-fast-backward'></i></a></li>
				<li class='page-item $prev_class'><a id='".$prev_number."' class='page-link page'>".$prev_number."</a></li>
				<li class='page-item $current_class'><a id='".$current_number."' class='page-link page'><u>".$current_number."</u></a></li>
				<li class='page-item $next_class'><a id='".$next_number."' class='page-link page'>".$next_number."</a></li>
				<li class='page-item'><a id='$total_pages' class='page-link page'><i class='fas fa-fast-forward'></i></a></li>
			</ul>
		</nav>
	</div>";
}