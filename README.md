# erepublik-army_sync

- All requests must use an api_key query param in order to be allowed to execute the request
- api_key is generated by us and you can see your api_key by executing a GET request on {{host}}/accounts.php

Accounts:
- there is already an admin account created
- you can create your own accout POST on {{host}}/accounts.php. by default the account has {{to_be_verified}} status
- an admin can validate your account and change the staus.'
- an admin can fully update your account
- an admin can delete your account
- an account can expire

Orders:
- you can create orders. orders are limited by the account. orders can be added only if the account is active and not expired
- you can update your orders. orders can be updated only if the account is active and not expired
- you can delete your order
- you can see your orders.
- an admin can see all the orders.
- an admin can update all the orders.
- an admin can delete any order.

Soldiers:
- you can register yourself as a soldier
- you as a soldier can decide to assign yourself to a commander (account) or multiple commanders
- you (as a commander) can see soldiers registered under your command
- you (as a soldier) can see orders set by your commander (commanders)
