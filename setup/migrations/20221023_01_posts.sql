update `posts` set userid=1 where userid=0;
--!ENDQUERY--
update `posts` set featuredpos=0
--!ENDQUERY--

update `posts` set featuredpos=3 where deleted=0
order by rand()
limit 1;
--!ENDQUERY--
update `posts` set featuredpos=2 where deleted=0 and featuredpos=0
order by rand()
limit 1;
--!ENDQUERY--
update `posts` set featuredpos=1 where deleted=0 and featuredpos=0
order by rand()
limit 1;