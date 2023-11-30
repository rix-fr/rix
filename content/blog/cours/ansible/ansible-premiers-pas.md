---
type:               "post"
title:              "Ansible - Découverte et premiers pas."
date:               "2023-11-23"
lastModified:       ~
tableOfContent:     true
description:        "Dans ce premier cours à destination des étudiants et/ou néophytes, nous verrons ce qu'est Ansible ainsi qu'un exemple très simple de son utilisation."

thumbnail:          "content/images/blog/thumbnails/ansible-premier-pas.jpg"
tags:               ["cours", "ansible", "automation"]
categories:         ["cours"]
authors:            ["gfaivre"]
---

## Préambule

Ce cours est utilisé dans le cadre de TP au sein de l'IUT Lyon 1. Il est notamment dispensé à des étudiants peu ou pas familiers avec les stratégies d'automatisation et de déploiement des infrastructures.
Bien que très axé débutants il peut également représenter une possibilité de monter « rapidement » pour certaines équipes sur les principes fondamentaux d'Ansible afin de disposer du bagage minimal nécessaire à son utilisation.

Il s'agit bien évidemment de supports à vocation pédagogique qui **ne sont pas toujours transposables** à une activité professionnelle.

## Prérequis

Afin d'aborder les différents concepts du cours il est recommandé de disposer:
- D'au moins deux machines virtuelles accessibles via **SSH** (idéalement 4);
- **[Docker et Docker compose](https://docs.docker.com/engine/install/)** installés sur la machine de travail (Docker Desktop pour [Windows](https://docs.docker.com/desktop/install/windows-install/) et [OSX](https://docs.docker.com/desktop/install/mac-install/));
- D'une installation d'Ansible récente (2.15.5), s'il est possible de l'installer localement je recommanderais plutôt d'utiliser le [**Lazy Ansible**](/blog/cours/ansible/ansible-environnement-cle-en-main) du projet [**Manala**](https://github.com/manala/)..
- D'une paire de clés SSH que vous aurez pris soin de générer (voir [ici](/blog/cours/cle-ssh-principes-de-base)) si vous n'en disposez pas déjà.
- D'un répertoire de travail, de mon côté ça sera `workspace/ansible` (très original oui), son nom importe peu, l'idée est que vous sachiez vous y retrouver;

Pour ceux qui utilisent Windows, il est possible d'[utiliser WSL pour faire fonctionner les conteneurs Docker](/blog/cours/docker-avec-windows-et-wsl), une machine virtuelle Linux fonctionne encore mieux, libre à vous d'utiliser l'un ou l'autre.

## Mise en route

Première étape avant de pouvoir rentrer dans le vif du sujet, nous aurons besoin de mettre en place un environnement de travail dédié à nos travaux.

## Infrastructure

Pour pouvoir configurer nos serveurs, il nous faudra... des serveurs, ou plutôt des machines virtuelles pour leur facilité à être arrêtées, détruites et reconstruites.
N'importe quel fournisseur de cloud public peut faire l'affaire, utilisez celui avec lequel vous avez le plus d'affinités.

Dans le cadre de l'IUT nous utiliserons OpenStack, solution OpenSource qui a fait ses preuves et qui plus est disponible dans l'enceinte de l'université, c'est également la solution technique utilisée par le [Public Cloud d'OVHCloud](https://www.ovhcloud.com/fr/public-cloud/).
C'est donc sur cette base que je présenterai les étapes suivantes, au demeurant, parfaitement transposables chez d'autres fournisseurs.

Nous travaillerons avec deux environnements distincts, « *Staging* » et « *Production* » qui embarqueront chacune une instance applicative (qui portera donc le code d'une application) et une instance destinée aux données (et donc chargée de faire fonctionner notre serveur de base de données).
Si vous êtes limité en terme de création d'instances, il est envisageable de n'avoir qu'une instance par environnement, celle-ci embarquant **l'applicatif et les données**.

## Environnement local

Les étapes suivantes seront donc à exécuter à partir de votre machine.

### Se connecter avec le client SSH

Considérant que vous remplissez les prérequis et que vous avez créé vos instances distantes nous allons pour commencer initier une « simple » connexion SSH vers notre instance.

```
ssh debian@XXX.XXX.XXX.XXX
```

Si vous rencontrez des soucis .. forbidden (exemple) ré-essayez en ajoutant explicitement le chemin vers la clé.

```
ssh -i ~/.ssh/ed25519 debian@XXX.XXX.XXX.XXX
```

!!! info "Utilisateur sous Windows"
    Pour rappel aux utilisateurs de Windows vous trouverez ce répertoire `.ssh` dans `C:\Users\MonNomUtilisateur\`

### Configuration du client SSH

Afin d'éviter d'avoir à spécifier le chemin vers la clé à chaque connexion et afin d'affiner la configuration de notre client nous pouvons également définir un fichier `~/.ssh/config` contenant les directives suivantes:

```
Host 192.168.140.*
  Port 22
  User debian
  IdentityFile ~/.ssh/keyfile
  IdentitiesOnly yes
  ForwardAgent yes
```

Celles-ci sont relativement compréhensibles, précisons tout de même pour les deux dernières:

- `IdentitiesOnly` indique à SSH de n'envoyer au serveur **QUE** la clé définie à la directive `IdentityFile` quand bien même vous disposez d'autres clés dans votre répertoire `~/.ssh`
- `ForwardAgent` permet d'activer le transfert d'identité vers l'agent SSH du serveur

Cette configuration vous permet d'indiquer certaines directives de manière automatique pour un ou plusieurs hôtes distants, pour en savoir plus concernant les fichiers de configuration SSH vous pouvez aller jeter un oeil [ici](/blog/cours/utiliser-la-configuration-ssh-client)

### Utilisation de l'agent SSH

La prochaine étape est l'utilisation d'un service spécifique à SSH, **l'agent**.

L'agent SSH sur la plupart des systèmes UNIX est lancé au démarrage de votre machine, toutefois si ça n'est pas le cas, il est possible de le démarrer avec la commande `eval 'ssh-agent'`.
Son rôle est de permettre de stocker de manière sécurisée votre/vos clés privées SSH (rappelez-vous c'est la partie que l'on ne partage pas !) mais également d'assurer le transfert de cette clé privée en toute sécurité vers les serveurs distants auxquels vous tenterez de vous connecter.

#### Ajouter une clé dans l'agent

L'ajout d'une clé dans un agent est trivial et se fait à l'aide de la commande `ssh-add ~/.ssh/my_private_key`. 

Si vous avez protégé votre clé avec une phrase de passe elle vous sera demandée par l'agent au moment de son ajout.
Afin de vérifier que votre clé a bien été ajoutée à votre agent vous pouvez lister les clés contenues à l'intérieur avec la commande `ssh-add -l` qui devrait vous donner une sortie équivalente à la suivante:

```
rix@debian:~$ ssh-add -l 
4096 SHA256:knyjFlzIWukj77PBs0V+mO4eKD9mnSITOkYfYvgvZcQ /home/rix/.ssh/gfaivre-iut (RSA)
```
Cette étape, complétée par la directive `ForwardAgent` contenue dans notre fichier de configuration SSH (pour rappel `~/.ssh/config`) va nous permettre lorsque nous nous connectons à un serveur distant de transférer notre clé privée vers l'agent de ce même serveur.

De cette manière notre clé privée sera même disponible sur le serveur auquel nous nous connectons, nous aborderons l'utilité de cette configuration plus tard.

### Communication Ansible <> serveurs distants

Notre environnement étant « prêt » testons à présent la bonne communication avec nos serveurs distants en utilisant le module `ping` d'Ansible.

À partir de ce moment et sauf instruction contraire nous partirons du principe que nous évoluons à l'intérieur de notre répertoire de travail (`workspace/ansible` donc ;)) pour saisir nos commandes et créer notre arborescence de projet.

!!! info "Les modules Ansible"
    Dans la terminologie Ansible, les « modules » sont des morceaux de code pouvant être utilisés soit directement dans la ligne de commande (avec l'option `-m`, soit dans une section `task` d'un « playbook »). Ils peuvent prendre en charge des arguments avec une syntaxe classique `key=value`.

Pour pouvoir effectuer notre premier test nous allons donc créer un fichier que nous appellerons `hosts.yml` contenant (à adapter en fonction du réseau sur lequel sont déployées vos machines virtuelles): 

```
all:
  hosts:
    ansible-vm-01:
      ansible_host: 192.168.140.30
      ansible_user: debian
```

Attention à l'indentation et faites attention de bien utiliser des espaces pour celle-ci.

Pour terminer nous lançons notre conteneur docker « lazy » avec un `make sh` et y exécutons la commande `ansible -i hosts.yml all -m ping`, qui utilise le module **ping** d'ansible pour vérifier que l'on arrive bien à se connecter à l'instance distante.

Ce qui nous donne: 

<figure>
    <img src="content/images/blog/2023/ansible/ansible-part-1/ansible-ping.gif">
    <figcaption>
      <span class="figure__legend">Utilisation du module ping avec Ansible.</span>
    </figcaption>
</figure>

!!! danger "Le module ping"
    Bien que son nom puisse porter à confusion, il s'agit là d'un module propre à Ansible et qui n'a rien à voir avec la commande système du même nom. Pour rappel, la commande système envoie un paquet ICMP (ECHO_REQUEST) à une machine distante et attend en retour un paquet du même type (ECHO_RESPONSE) indiquant le bon état de la liaison réseau.
    Le module Ansible quant à lui se connecte via SSH à la machine distante et y vérifie la bonne configuration de Python.


Cette dernière étape me permet d'introduire un concept que nous verrons dans la section suivante, celui des *inventaires* ! 

## Aller plus loin avec les sources:

- https://docs.ansible.com/ansible/latest/module_plugin_guide/modules_intro.html
- https://www.ssh.com/academy/ssh/agent

