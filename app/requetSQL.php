<?php

// Sql get information
$sqlGetUserInfo = "SELECT `*` FROM `users` WHERE `id`=?";
$sqlGetUserProfile = "SELECT `*` FROM `profil` WHERE `uid`=?";
$sqlGetLikes = "SELECT `nb_like` FROM `profil` WHERE `uid`=?";
$sqlGetView = "SELECT `nb_view` FROM `profil` WHERE `uid`=?";
$sqlCheckLikes = "SELECT `id` FROM `likes` WHERE `src_id`=? AND `dst_id`=?";
$sqlCheckView = "SELECT `id` FROM `view` WHERE `src_id`=? AND `dst_id`=?";
$sqlGetWhoLikes = "SELECT `src_id` FROM `likes` WHERE `dst_id`=?";
$sqlGetWhoView = "SELECT `src_id` FROM `view` WHERE `dst_id`=?";
$sqlGetId = "SELECT `id` FROM `users` WHERE `login`=?";
$sqlGetCle = "SELECT `cle` FROM `users` WHERE `id`=?";
$sqlCheckLogin = "SELECT `login` FROM `users` WHERE `login`=?";
$sqlCheckEmail = "SELECT `email` FROM `users` WHERE `email`=?";
$sqlGetPassword = "SELECT `password` FROM `users` WHERE `login`=?";
$sqlGetNotifNumber = "SELECT COUNT(*) AS `numNotif` FROM `notif`
                        WHERE `is_read`='N' AND `uid`=?";
$sqlGetNoReafNotif = "SELECT * FROM `notif` WHERE `is_read`='N' AND `uid`=? ORDER BY `id` DESC";
$sqlGetAllNotif = "SELECT * FROM `notif` WHERE `uid`=?";
$sqlGetNoReadChat = "SELECT * FROM `chat` WHERE `src_id`=? AND `dst_id`=? AND `is_read`='N'";
$sqlGetAllChat = "SELECT * FROM `chat` WHERE (`src_id`=? AND `dst_id`=?) OR (`src_id`=? AND `dst_id`=?)";
$sqlGetReadChat = "SELECT * FROM `chat` WHERE `is_read`='Y' AND ((`src_id`=? AND `dst_id`=?) OR (`src_id`=? AND `dst_id`=?))";
$sqlGetProfilPhoto = "SELECT `img_path` FROM `img` WHERE `uid`=? AND `is_profil`='Y'";
$sqlGetPhoto = "SELECT `img_path` FROM `img` WHERE `uid`=? AND `is_profil`='N'";
$sqlGetAdresse = "SELECT * FROM `adresse` WHERE `uid`=?";
$sqlGetUserTag = "SELECT `tag` FROM `tags` WHERE `uid`=?";
$sqlCheckTag = "SELECT `tag` FROM `tags` WHERE `uid`=? AND `tag`=?";
$sqlGetMatchFriends = "SELECT * FROM `match` WHERE `user1`=? OR `user2`=?";
$sqlGetAllTag = "SELECT `tag` FROM `tags` GROUP BY `tag`";
$sqlGetUserLatLong = "SELECT `latitude`, `longitude` FROM `adresse` WHERE `uid`=?";
$sqlGetAllUserSameSex = "SELECT `uid` FROM `profil` WHERE `genre`=? AND `uid`!=?";
$sqlGetAllUserBtwAge = "SELECT `uid` FROM `profil` WHERE (`age` BETWEEN ? AND ?) AND `uid`!=?";
$sqlGetAllUserLikeUpper = "SELECT `uid` FROM `profil` WHERE `nb_like`>=? AND `uid`=?";
$sqlGetAllUserViewUpper = "SELECT `uid` FROM `profil` WHERE `nb_view`>=? AND `uid`=?";
$sqlGetBlakList = "SELECT * FROM `blacklist` WHERE `uid`=? AND `dst_id`=?";
$sqlCheckFriend = "SELECT `id` FROM `match` WHERE (`user1`=? AND `user2`=?) OR (`user1`=? AND `user2`=?)";

// Sql add information
$sqlAddBlackList = "INSERT INTO `blacklist` (`uid`, `dst_id`) VALUES (?, ?)";
$sqlAddMatch = "INSERT INTO `match` (`user1`, `user2`) VALUES (?, ?)";
$sqlAddReport = "INSERT INTO `report` (`uid`, `dst_id`) VALUES (?, ?)";
$sqlAddView = "INSERT INTO `view` (`src_id`, `dst_id`) VALUES (?, ?)";
$sqlAddLikes = "INSERT INTO `likes` (`src_id`, `dst_id`) VALUES (?, ?)";
$sqlAddChatMsg = "INSERT INTO `chat` (`src_id`, `dst_id`, `message`) VALUES (?, ?, ?)";
$sqlAddAdresse = "INSERT INTO `adresse` (`uid`, `pays`, `region`, `ville`, `arrond`,
                                        `rue`, `num`, `latitude`, `longitude`)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$sqlAddNewUserToDB = "INSERT INTO `users` (`nom`, `prenom`, `login`, `password`, `email`, `cle`)
                    VALUES (?, ?, ?, ?, ?, ?)";
$sqlAddNewUserToProfil = "INSERT INTO `profil` (`uid`, `age`, `birthday`, `bio`)
                        VALUES (?, ?, ?, ?)";
$sqlAddProfilPhoto = "INSERT INTO `img` (`uid`, `img_path`, `is_profil`) VALUES (?, ?, 'Y')";
$sqlAddPhoto = "INSERT INTO `img` (`uid`, `img_path`) VALUES (?, ?)";
$sqlAddTag = "INSERT INTO `tags` (`uid`, `tag`) VALUES (?, ?)";

// Sql add notif
$sqlAddLikesNotif = "INSERT INTO `notif` (`uid`, `src_id`, `likes`) VALUES (?, ?, 'Y')";
$sqlAddRetourLikesNotif = "INSERT INTO `notif` (`uid`, `src_id`, `retour_likes`) VALUES (?, ?, 'Y')";
$sqlAddMatchUnlikesNotif = "INSERT INTO `notif` (`uid`, `src_id`, `match_unlikes`) VALUES (?, ?, 'Y')";
$sqlAddViewNotif = "INSERT INTO `notif` (`uid`, `src_id`, `view`) VALUES (?, ?, 'Y')";
$sqlAddMessageNotif = "INSERT INTO `notif` (`uid`, `src_id`, `message`) VALUES (?, ?, 'Y')";

// Sql delete information
$sqlDeleteLikes = "DELETE FROM `likes` WHERE `src_id`=? AND `dst_id`=?";
$sqlDeleteView = "DELETE FROM `view` WHERE `src_id`=? AND `dst_id`=?";
$sqlDeleteMatch = "DELETE FROM `match` WHERE `id`=?";
$sqlDeleteBlackList = "DELETE FROM `blacklist` WHERE `uid`=? AND `dst_id`=?";
$sqlDeleteProfilPhoto = "DELETE FROM `img` WHERE `uid`=? AND `is_profil`='Y'";
$sqlDeletePhoto = "DELETE FROM `img` WHERE `img_path`=? AND `uid`=? AND `is_profil`='N'";
$sqlDeleteAdresse = "DELETE FROM `adresse` WHERE `uid`=?";
$sqlDeleteTag = "DELETE FROM `tags` WHERE `uid`=? AND `tag`=?"

// Sql update information
$sqlSetOnline = "UPDATE `users` SET `online`='Y' WHERE `login`=?";
$sqlSetOffline = "UPDATE `users` SET `online`='N' WHERE `login`=?";
$sqlNewCle = "UPDATE `users` SET `cle`=? WHERE `id`=?";
$sqlNewLastCo = "UPDATE `users` SET `last_log`=CURRENT_TIMESTAMP WHERE `login`=?";
$sqlChatRead = "UPDATE `chat` SET `is_read`='Y' WHERE `src_id`=? AND `dst_id`=?";
$sqlNotifRead = "UPDATE `notif` SET `is_read`='Y' WHERE `uid`=?";
$sqlHiddenYes = "UPDATE `adresse` SET `hidden`='Y' WHERE `uid`=?";
$sqlHiddenNo = "UPDATE `adresse` SET `hidden`='N' WHERE `uid`=?";
$sqlEmailChecked = "UPDATE `users` SET `checked`='Y' WHERE `id`=?";

// Sql update profil
$sqlNewGenre = "UPDATE `profil` SET `genre`=? WHERE `uid`=?";
$sqlNewOrientSex = "UPDATE `profil` SET `orient_sex`=? WHERE `uid`=?";
$sqlNewBio = "UPDATE `profil` SET `bio`=? WHERE `uid`=?";
$sqlNewLike = "UPDATE `profil` SET `nb_like`=? WHERE `uid`=?";
$sqlNewView = "UPDATE `profil` SET `nb_view`=? WHERE `uid`=?";
$sqlUpdateProfil = "UPDATE `profil` SET `genre`=?, `orient_sex`=?, `bio`=? WHERE `uid`=?";
$sqlUpdateLatLong = "UPDATE `adresse` SET `latitude`=?, `longitude`=? WHERE `uid`=?";

// Sql update users information
$sqlNewNom = "UPDATE `users` SET `nom`=? WHERE `id`=?";
$sqlNewPrenom = "UPDATE `users` SET `prenom`=? WHERE `id`=?";
$sqlNewEmail = "UPDATE `users` SET `email`=? WHERE `id`=?";
$sqlNewPassword = "UPDATE `users` SET `password`=? WHERE `id`=?";
