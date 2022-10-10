truncate table users
--!ENDQUERY--
insert ignore into users (id,username,fullname,password,created,deleted) values (1,'super','Super User','7c222fb2927d828af22f592134e8932480637c0d',now(),0)
/* password is 12345678 */