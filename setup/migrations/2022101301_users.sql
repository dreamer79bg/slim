INSERT IGNORE INTO `users` (`id`, `username`, `fullname`, `password`, `created`, `deleted`) VALUES
(1, 'super', 'Super User', '7c222fb2927d828af22f592134e8932480637c0d', '2022-10-10 08:56:31', 0),

(2, 'super123', '123', '7c222fb2927d828af22f592134e8932480637c0d', '2022-10-12 23:19:03', 0),
(3, 'test1665606146', 'test testov 6655', 'e86cae534db987e822c591a5d6c424fdf485659d', '2022-10-12 23:22:26', 0),
(4, 'test166560614615637224', 'test testov 554443232', '894b54b6fb953a6003d4112aa2c9a8d0096c64b6', '2022-10-12 23:22:26', 0),
(5, 'ivan', 'ivan draganov', '894b54b6fb953a6003d4112aa2c9a8d0096c64b6', '2022-10-12 23:22:26', 0),
(5, 'pesho', 'petyr stoianov', '894b54b6fb953a6003d4112aa2c9a8d0096c64b6', '2022-10-12 23:22:26', 0),
(6, 'sa1', 'ssasaas', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', '2022-10-13 08:00:29', 0)
--!ENDQUERY--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=250;


