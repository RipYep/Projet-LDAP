# Projet-LDAP
Mise en place d’un annuaire LDAP : création d’un script d’importation des utilisateurs et groupes depuis un fichier CSV, génération de mots de passe sécurisés en SSHA, et développement d’une application web permettant la gestion des entrées (ajout, suppression, modification).  
Intégration de l’authentification LDAP à Wordpress.

---

## Table des matières
1. [Présentation du projet](#présentation-du-projet)
   - [](#)

---

## Présentation du projet

Ce projet consiste à mettre en place un annuaire `LDAP` *_(Lightweight Directory Access Protocol)_* permettant la gestion centralisée des utilisateurs et groupes d’un établissement. Une interface web a été développée pour interagir avec l’annuaire et une intégration avec `WordPress` a été réalisée pour l’authentification des utilisateurs.

---

## Interface de connexion
![1](https://github.com/user-attachments/assets/c9706917-c27e-4c00-a1fd-c34a01ea9234)
![6(testlogin)](https://github.com/user-attachments/assets/83852494-28d2-4e91-9a9c-b1f8c8456492)

---

## Administration LDAP depuis le compte admin
![4(admin)](https://github.com/user-attachments/assets/baaaea1b-5f3a-4044-910c-dd0476cf67d0)
![5(admin)](https://github.com/user-attachments/assets/3c66097c-2666-49b2-beb6-ae528d632b0b)
![10(adminadd)](https://github.com/user-attachments/assets/e103e87f-6d8d-4e0a-ba5a-66eddc62dd53)
![13(searchtestandmodify)](https://github.com/user-attachments/assets/1623c1db-a38f-4c66-b74d-86df9eb44905)
![14(modifyphone)](https://github.com/user-attachments/assets/e6557de9-2740-4cea-b37f-b3c08aea83c3)
![15(deleteuser)](https://github.com/user-attachments/assets/b0536418-33b4-43ca-91de-7370c6db1a4f)
![15(showuserisdeleted)](https://github.com/user-attachments/assets/b4078d54-1d20-4704-bcee-ac70e3d115f1)

---

## Connexion sur compte utilisateur
![7(test)](https://github.com/user-attachments/assets/6cae1cc6-e995-4623-81c2-c0957d163b70)
![11(usermodify)](https://github.com/user-attachments/assets/b1c4eb1e-379b-49a6-a5c8-30e71cc9182d)
![12(afterusermodified)](https://github.com/user-attachments/assets/0efb27b7-90ab-43c5-99ec-90a0364ae29e)

---

## Connexion en tant qu'admin
![8(wordpresslogin)](https://github.com/user-attachments/assets/52db2332-7bdb-41b9-80e9-01f557875f65)
![9(wordpresswpadmin)](https://github.com/user-attachments/assets/af6802cd-5fdc-4a13-b007-19a5a19bd836)

---

## Connexion en tant qu'utilisateur normale
![16(connectasuserwordpress)](https://github.com/user-attachments/assets/88e0c04c-40a1-41b5-b6dd-342ea15f8e50)

Pour se connecter sur un compte LDAP (sauf admin), il suffit de mettre le `UID` de l'utilisateur. Donc si le `UID` est `test.test` et mot de passe est `testt`, alors il faudra juste mettre :
>    - Login : test.test
>    - Password : testt
