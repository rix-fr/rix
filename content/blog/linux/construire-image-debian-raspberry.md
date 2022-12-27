---
type:               "post"
title:              "Construire une image Debian pour Raspberry Pi"
date:               "2022-12-22"
lastModified:       ~
tableOfContent:     true
description:        "Construire une image source Debian à déployer sur Raspberry Pi en remplacement de Raspberry Pi OS (anciennement Raspbian)."

thumbnail:          "content/images/blog/thumbnails/raspberrypi.jpg"
credits:            { name: "Jainath Ponnala", url: "https://unsplash.com/@jainath" }
tags:               ["devops", "linux", "build", "raspberry"]
categories:         ["linux"]
authors:            ["gfaivre"]
---

Je vous vois venir ! Quelle idée de vouloir construire sa propre image ?
Alors même que Raspberry fourni [un OS ET un utilitaire](https://www.raspberrypi.com/software/) permettant de créer des cartes SD « bootables » rapidement et quasi sans douleur !

## TL;DR

_Pour les opérations à suivre utilisez une Debian Bulleyes (de préférence VM)_

1. Récupérer le dépôt avec les outils pour construire l'image;
```
git clone --recursive https://salsa.debian.org/raspi-team/image-specs.git
cd image-specs
```
2. Installer les différents paquets nécessaires à l'opération;
```
apt install -y vmdb2 dosfstools qemu-utils qemu-user-static debootstrap binfmt-support time kpartx bmap-tools python3
apt install -y fakemachine
```
3. Construire l'image (Pour l'exemple à destination d'un Raspberry Pi 4): `make raspi_4_bullseye.img`;
4. Écrire cette image sur une carte SD: `dd if=raspi_4_bullseye.img of=/dev/XXX bs=64k oflag=dsync status=progress`.

## Mise en contexte

Oui mais…

Avant d'aller plus loin il faut savoir que nous utilisons en interne plusieurs Raspberry Pi afin de faire tourner des services qui nous permettent de piloter la gestion du **réseau local** de nos bureaux et/ou d'y fournir des services **basiques et non critiques** (Serveurs DNS locaux, VPN, métriques, monitoring…).

Nous avons également une petite flotte de ces machines qui nous permettent **d'aller enseigner** « quelques trucs » à des étudiants (du réseau, du provisioning, linux…) en se reposant dessus.

Alors bien évidemment le besoin ne tombe pas du ciel, et même si l'exercice reste formateur et amusant (si, si) le but n'est bien évidemment pas « juste ludique ».
Raspberry Pi OS a en fait ses limites, particulièrement lorsqu'on va l'utiliser sur d'anciens Raspberry Pi (notamment des 2 ou précédents).

Pour terminer il faut savoir que Raspbian (dérivé de Debian) a été créé en premier lieu parce que jusqu'à 2018 il n'était **pas possible de démarrer** un kernel linux sur Raspberry Pi mais également parce que Raspbian arrivait avec des composants **non libres** contraires à la philosophie Debian.

## Pourquoi reconstruire et pourquoi Debian ?

- En tout premier lieu parce que nous avons besoin d'avoir accès **facilement** à certains paquets non disponibles sur Raspberry Pi OS dans les versions dont nous avons besoin;
- En second lieu parce que l'intégration de dépôts externes sur une base Raspberry Pi OS est parfois **assez chaotique**, notamment dû aux **différentes architectures** utilisées par les processeurs ARM des Raspberry Pi et de leur disponibilité (ou pas) dans ces mêmes dépôts.

On se retrouve notamment par défaut (il est possible d'aller chercher d'autres versions d'OS dans l'utilitaire de création d'images) avec une version de Raspberry Pi OS commune à toutes les versions et donc **32 bits**…

Pour rappel les premières architectures 64 bits des Raspberry Pi sont arrivées avec la version 3.
Debian fait le choix de coller exactement aux différentes archi, mais au prix d'une image pour chacune, celles-ci sont d'ailleurs disponibles [ici](https://raspi.debian.net/tested-images/).

|Version Raspberry Pi |Debian   	    |Raspberry Pi OS   	|
|---	              |---	            |---	            |
|        0 / 1        |   armel, 32 bit	|  armhf, 32 bit	|
|          2          |   armhf, 32 bit	|  armhf, 32 bit 	|
|          3          |   arm64, 64 bit	|  armhf, 32 bit 	|
|          4          |   arm64, 64 bit	|  armhf, 32 bit 	|

Il existe d'autres versions d'OS disponibles en **version serveur ou desktop** (Ubuntu, Manjaro…) mais comme nous utilisons les roles Ansible du projet [Manala](https://github.com/manala/ansible-roles) pour provisionner l'ensemble de nos machines et afin d'**assurer au maximum la compatibilité** (et nous éviter de la maintenance), nous préferons rester cohérents et utiliser Debian.

Pour terminer, les images pré-construites Debian **peuvent carrément faire l'affaire** mais certains choix de configuration ne nous vont pas et nous avons besoin que nos images soient prêtes à être « provisionnées » et donc disposer:
- de certains paquets;
- de comptes utilisateurs spécifiques;
- et bien évidemment de nos clés SSH pour l'authentification.

## Pré-requis

Ils sont peu nombreux, tant la construction d'images est rendue **très simple** par les contributeurs à la version Raspberry Pi de Debian.

Vous aurez besoin:
- D'une Debian Bullseye (de préference une machine virtuelle, histoire de garder propre notre machine de travail);
- De récupérer le dépôt contenant les outils https://salsa.debian.org/raspi-team/image-specs;
- D'un peu de temps.

## Let's go !

En tout premier lieu, une fois **sur votre VM Debian Bullseye**, on clone le répertoire d'outils:

```
git clone --recursive https://salsa.debian.org/raspi-team/image-specs.git
cd image-specs
```

Nous aurons également besoin de quelques paquets pour la construction:

```
apt install -y vmdb2 dosfstools qemu-utils qemu-user-static debootstrap binfmt-support time kpartx bmap-tools python3
apt install -y fakemachine
```

**Le fichier makefile fournit vous permet ensuite, au choix:**

- De construire une image **spécifique** à votre Raspberry Pi en utilisant une **configuration par défaut**:

    `make raspi_<model>_<release>.img`

    - `<model>` vaut pour la version de votre Raspberry (1,2,3 ou 4 avec 1 utilisé pour les versions Pi 0, 0w and 1);
    - `<release>` pour la version de Debian que vous souhaitez construire.

    Dans notre cas une Bullseye pour un Raspberry Pi 2: `make raspi_2_bullseye.img`

- De construire une image **spécifique** à partir de **VOTRE configuration** (et c'est ce choix qui nous intéresse)

### Configuration de l'image

Alors bien évidemment nous ne partirons pas d'une page blanche mais du fichier de configuration généré par l'outil grâce à la commande:

`make raspi_2_bullseye.yaml`

Nous obtenons un fichier de configuration par défaut (celui utilisé pour la construction de l'image avec la commande précédente).
Il est possible de l'éditer directement mais préférable de le nommer différemment histoire de ne pas malencontreusement écraser nos prochaines modifications en rejouant la commande précédente.

`cp raspi_2_bullseye.yaml rix_raspi_2_bullseye.yaml`

En ce qui nous concerne nous y opérerons plusieurs modifications:

- L'ajout d'un compte utilisateur non privilégié:

```yaml
- chroot: tag-root
    shell: |
      useradd -g100 -Gsudo -m -s /bin/bash rix
```

- L'ajout des clés publiques SSH

```yaml
  - create-dir: /home/rix/.ssh
    uid: 1000
    gid: 100
```
```yaml
  - create-file: /home/rix/.ssh/authorized_keys
    contents: |+
      ssh-ed25519 AAAAC3XXXXXXXXXXXXXXXXXXXX
```

- L'ajout des droits sudo à notre utilisateur

```yaml
  - create-file: /etc/sudoers.d/rix-default
    contents: |+
      rix ALL=(ALL) NOPASSWD: ALL
```

- La suppression de la connexion `root` locale sans mot de passe.

```yaml
- # Allow root logins locally with no password
- sed -i 's,root:[^:]*:,root::,' "${ROOT?}/etc/shadow"
```

!!! info "La directive `chroot`"
    Elle est essentielle dès lors que vous souhaitez exécuter une commande **dans le contexte du système en cours de construction**.

### Construction de l'image

Une fois l'ensemble de nos instructions ajoutées à notre fichier nous pouvons démarrer la construction de notre image:

```
vmdb2 --rootfs-tarball=rix_raspi_2_bullseye.tar.gz --output \
rix_raspi_2_bullseye.img rix_raspi_2_bullseye.yaml --log rix_raspi_bullseye.log
```

Cette commande devrait nous donner en sortie 3 fichiers:

- Un fichier de logs `rix_raspi_bullseye.log`
- Un fichier image `rix_raspi_2_bullseye.img`
- Un fichier image compressé `rix_raspi_2_bullseye.tar.gz`

### Installation de l'image

L'installation de l'image est ensuite assez standard, une fois votre carte SD montée:

- Soit via `bmaptool` à partir de l'image compressée;

`bmaptool copy rix_raspi_2_bullseye.tar.gz /dev/mmcblk0`

- Soit via un bon vieux `dd`:

`dd if=rix_raspi_2_bullseye.img of=/dev/XXX bs=64k oflag=dsync status=progress`

!!! danger ""
    Attention à la « cible » de l'écriture, en cas de mauvais volume sélectionné vous écraserez son contenu.

Vous voilà paré·e·s pour déployer du Raspberry Pi en série sur une base Debian !

## Pour aller plus loin

**Sources:**

- https://wiki.debian.org/RaspberryPi
- https://wiki.debian.org/RaspberryPiImages
- https://raspi.debian.net/
- https://vmdb2.liw.fi/