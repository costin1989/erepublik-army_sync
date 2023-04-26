--
-- Structură tabel pentru tabel `accounts`
--

DROP TABLE accounts;

CREATE TABLE `accounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `api_key` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `expires_on` datetime NOT NULL,
  `max_orders` int(11) NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Eliminarea datelor din tabel `accounts`
--

INSERT INTO `accounts` (`id`, `api_key`, `username`, `expires_on`, `max_orders`, `status`) VALUES (NULL, '92e2616b747afbc057221a2dee3fa32e', 'admin', '2024-04-20 00:59:12', '100', 'active');
-- --------------------------------------------------------
--
-- Structură tabel pentru tabel `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(35) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Eliminarea datelor din tabel `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'admin', 'users with this role are able to manage accounts');

--
-- Structură tabel pentru tabel `accounts_roles`
--

CREATE TABLE `accounts_roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


INSERT INTO `accounts_roles` (`id`, `account_id`, `role_id`) VALUES
(1, 1, 1);

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `battle_id` int(11) NOT NULL,
  `fight_for` varchar(255) NOT NULL,
  `wall` int(11) NOT NULL,
  `candies` tinyint(1) NOT NULL,
  `added_by` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


--
-- Structură tabel pentru tabel `soldiers`
--

CREATE TABLE `soldiers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `ff` int(11) NOT NULL,
  `candies` int(11) NOT NULL,
  `boosters_100` int(11) NOT NULL,
  `boosters_50` int(11) NOT NULL,
  `energy_bar` int(11) NOT NULL,
  `ground_rank` int(11) NOT NULL,
  `air_rank` int(11) NOT NULL,
  `strength` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



--
-- Indexuri pentru tabele `soldiers`
--
ALTER TABLE `soldiers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- AUTO_INCREMENT pentru tabele `soldiers`
--
ALTER TABLE `soldiers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

-- --------------------------------------------------------

--
-- Indexuri pentru tabele eliminate
--

--
-- Indexuri pentru tabele `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `api_key` (`api_key`);

--
-- Indexuri pentru tabele `accounts_roles`
--
ALTER TABLE `accounts_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `fk_account_id` (`account_id`),
  ADD KEY `fk_role_id` (`role_id`);

--
-- Indexuri pentru tabele `orders`
--
ALTER TABLE `orders`
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `added_by` (`added_by`);

--
-- Indexuri pentru tabele `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- AUTO_INCREMENT pentru tabele eliminate
--

--
-- AUTO_INCREMENT pentru tabele `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pentru tabele `accounts_roles`
--
ALTER TABLE `accounts_roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pentru tabele `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT pentru tabele `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constrângeri pentru tabele eliminate
--

--
-- Constrângeri pentru tabele `accounts_roles`
--
ALTER TABLE `accounts_roles`
  ADD CONSTRAINT `fk_account_id` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `fk_role_id` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Constrângeri pentru tabele `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`added_by`) REFERENCES `accounts` (`id`);

