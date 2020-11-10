INSERT IGNORE INTO `smtp_users` (`userid`, `client_idnr`, `username`, `passwd`, `email`, `forward_only`) VALUES ('1', '', '1@extern.test', '{PLAIN}test1', 'test1@extern.test', '0');
INSERT IGNORE INTO `smtp_destinations` (`userid`, `source`, `destination`, `dispatch_address`) VALUES ('1', 'test1@extern.test', '1@extern.test', '1');
INSERT IGNORE INTO `smtp_destinations` (`userid`, `source`, `destination`, `dispatch_address`) VALUES ('1', '1@extern.test', '1@extern.test', '1');

INSERT IGNORE INTO `smtp_users` (`userid`, `client_idnr`, `username`, `passwd`, `email`, `forward_only`) VALUES ('2', '', '2@extern.test', '{PLAIN}test2', 'test2@extern.test', '0');
INSERT IGNORE INTO `smtp_destinations` (`userid`, `source`, `destination`, `dispatch_address`) VALUES ('2', 'test2@extern.test', '2@extern.test', '2');
INSERT IGNORE INTO `smtp_destinations` (`userid`, `source`, `destination`, `dispatch_address`) VALUES ('2', '2@extern.test', '2@extern.test', '2');

INSERT IGNORE INTO `smtp_users` (`userid`, `client_idnr`, `username`, `passwd`, `email`, `forward_only`) VALUES ('3', '', '3@extern.test', '{PLAIN}test3', 'test3@extern.test', '0');
INSERT IGNORE INTO `smtp_destinations` (`userid`, `source`, `destination`, `dispatch_address`) VALUES ('3', 'test3@extern.test', '3@extern.test', '3');
INSERT IGNORE INTO `smtp_destinations` (`userid`, `source`, `destination`, `dispatch_address`) VALUES ('3', '3@extern.test', '3@extern.test', '3');

INSERT IGNORE INTO `smtp_users` (`userid`, `client_idnr`, `username`, `passwd`, `email`, `forward_only`) VALUES ('4', '', '4@extern.test', '{PLAIN}test4', 'test4@extern.test', '0');
INSERT IGNORE INTO `smtp_destinations` (`userid`, `source`, `destination`, `dispatch_address`) VALUES ('4', 'test4@extern.test', '4@extern.test', '4');
INSERT IGNORE INTO `smtp_destinations` (`userid`, `source`, `destination`, `dispatch_address`) VALUES ('4', '4@extern.test', '4@extern.test', '4');