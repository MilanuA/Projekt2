# IP Projekt 2

## Problém
Byla snaha použít načítání configu z JSONu, ale na serveru to ze záhadného důvodu nešlo. Tento repozitář obsahuje i classu, která se stará o načítání, bohužel ale nefunguje. Nevím proč.

## Krátký popis
Řešeno pomocí OOP.

## Postup nasazení
V classe `Database` vyplnit v metodě `initializePDO`  připojení k databázi, které vypadá takto:
 
 ``` 
$host = "127.0.0.1";
$db = "fill db connection";
$user = "fill db user";
$pass = "fill db password";
$charset = "utf8mb4";
 ```
 
 Poté zapnout terminál, do kterého se napíše příkaz `composer install`, který nainstaluje potřebné balíčky. 
 V případě je možné sputit i příkaz `composer update`, který updatuje balíčky.
 
