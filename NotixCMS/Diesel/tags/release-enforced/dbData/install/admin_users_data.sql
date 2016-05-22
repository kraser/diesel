INSERT INTO `prefix_admin_users`
(`login`,`password`,`type`,`access`,`name`,`post`,`email`,`lastenter`,`created`,`modified`) VALUES
('admin', MD5('admin'),'a','','Имярек','Администратор','dummy@notix.su','', NOW(),'');
