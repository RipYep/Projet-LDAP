# Projet-LDAP
Mise en place d’un annuaire LDAP : création d’un script d’importation des utilisateurs et groupes depuis un fichier CSV, génération de mots de passe sécurisés en SSHA, et développement d’une application web permettant la gestion des entrées (ajout, suppression, modification).  
Intégration de l’authentification LDAP à Wordpress.

---

## Présentation du projet

Ce projet consiste à mettre en place un annuaire `LDAP` *_(Lightweight Directory Access Protocol)_* permettant la gestion centralisée des utilisateurs et groupes d’un établissement. Une interface web a été développée pour interagir avec l’annuaire et une intégration avec `WordPress` a été réalisée pour l’authentification des utilisateurs.

## Installation et configuration du serveur LDAP

### Installation de LDAP sur le serveur

1. Mise à jour des paquets :
   ```bash
   sudo apt-get update && sudo apt-get upgrade -y
   ```
2. Modification du fichier `/etc/hosts` pour ajouter le nom de domaine :
   ```bash
   sudo echo "127.0.1.1 iut5-kourou.home" | sudo tee -a /etc/hosts
   ```
3. Installation du serveur LDAP (`slapd`) :
   ```bash
   sudo apt install slapd -y
   ```
4. Configuration du domaine LDAP : `dc=iut5-kourou,dc=fr`

### Création de la structure LDAP

Exemple d’utilisateur :
```ldif
dn: uid=jean.dupont,ou=People,dc=iut5-kourou,dc=fr
objectClass: top
objectClass: person
objectClass: inetOrgPerson
uid: jean.dupont
sn: DUPONT
givenName: Jean
cn: Jean DUPONT
mobile: 0694231170
mail: jean.dupont@etu.iut5-kourou.fr
```

Exemple de groupe :
```ldif
dn: cn=Profs,ou=Groups,dc=iut5-kourou,dc=fr
objectClass: top
objectClass: posixGroup
cn: Profs
description: Groupe des professeurs
gidNumber: 2003
memberUid: alice.martin
```

## Création des fichiers LDIF à partir d’un fichier CSV

Un script Python a été développé pour convertir un fichier `CSV` en fichiers `LDIF`.

### Structure du fichier `user.csv`
```
formation;Nom;Prénom;Téléphone
Administratif;LEBRUN;Françoise;0694541215
BUT1GEII;LAMBERT;Paul;0694231143
```

### Fichiers générés
- `user.ldif`
- `groupe.ldif`
- `unitOrg.ldif`
- `password.csv`

## Gestion des utilisateurs et des groupes LDAP

### Importation des fichiers dans LDAP

1. Ajout des unités organisationnelles :
   ```bash
   ldapadd -x -H ldap://10.99.34.5 -D "cn=admin,dc=iut5-kourou,dc=fr" -W -f unitOrg.ldif
   ```
2. Ajout des utilisateurs :
   ```bash
   ldapadd -x -H ldap://10.99.34.5 -D "cn=admin,dc=iut5-kourou,dc=fr" -W -f user.ldif
   ```
3. Ajout des groupes :
   ```bash
   ldapadd -x -H ldap://10.99.34.5 -D "cn=admin,dc=iut5-kourou,dc=fr" -W -f groupe.ldif
   ```
4. Vérification des ajouts :
   ```bash
   ldapsearch -x -H ldap://10.99.34.5 -b "dc=iut5-kourou,dc=fr" -s sub
   ```

## Mise en place de la partie Web

### Installation d’Apache et des modules nécessaires

```bash
sudo apt install apache2 php8.2 libapache2-mod-php php-ldap -y
sudo a2enmod php8.2
sudo a2enmod ldap
sudo systemctl restart apache2
```

### Création du répertoire de l'application

```bash
sudo mkdir /var/www/html/projet-ldap
sudo chown -R www-data:www-data /var/www/html/projet-ldap
sudo chmod -R 755 /var/www/html/projet-ldap
```

## Intégration de WordPress avec LDAP

### Installation de WordPress

```bash
wget https://wordpress.org/latest.tar.gz
tar -xvzf latest.tar.gz
mv wordpress /var/www/html/projet-ldap/
sudo chown -R www-data:www-data /var/www/html/projet-ldap/
```

### Configuration de la base de données

```sql
CREATE DATABASE wordpressDB;
CREATE USER 'wp-admin'@'localhost' IDENTIFIED BY 'wp-adminPassword';
GRANT ALL PRIVILEGES ON wordpressDB.* TO 'wp-admin'@'localhost';
FLUSH PRIVILEGES;
```

Configuration du fichier `wp-config.php` :
```php
define( 'DB_NAME', 'wordpressDB' );
define( 'DB_USER', 'wp-admin' );
define( 'DB_PASSWORD', 'wp-adminPassword' );
define( 'DB_HOST', 'localhost' );
```

### Installation du plugin LDAP pour WordPress

1. Installer **Active Directory Integration / LDAP Integration**.
2. Configuration :
   - **Directory Server** → OpenLDAP
   - **LDAP Server Domain IP** → `10.99.34.5`
   - **Service Account Username** → `cn=admin,dc=iut5-kourou,dc=fr`
   - **Service Account Password** → `admin`

### Activation de l’authentification LDAP dans WordPress

1. **Enable Login Using LDAP**
2. **Authenticate Administrators from both LDAP and WordPress**
3. **Enable Auto Registering users if they do not exist in WordPress**

---
