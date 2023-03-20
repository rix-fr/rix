---
type:               "post"
title:              "Utiliser le fichier de configuration SSH pour ses connexions distantes."
date:               "2023-01-02"
lastModified:       ~
tableOfContent:     true
description:        "Apprendre à utiliser le fichier de configuration SSH pour organiser ses options de connexion."

thumbnail:          "content/images/blog/thumbnails/zsh.jpg"
credits:            { name: "Mohammad Rahmani", url: "https://unsplash.com/@afgprogrammer" }
tags:               ["cours", "ssh", "how-to"]
categories:         ["cours"]
authors:            ["rix"]
---

Si vous vous connectez à plusieurs hôtes distants il y a fort à parier que vous ne mémorisez pas les subtilités de connexion propre à chacun d'entre eux (utilisateur spécifique, port non standard, options spécifiques…).

On a eu, il est vrai, l'occasion de croiser des façons originales de le faire, notamment à base d'alias **Bash** mais ça reste assez artisanal alors même que tout est prévu et qu'OpenSSH permet de définir un fichier de configuration propre à chaque utilisateur du système ;)

Cet article, principalement à destination des **étudiant·e·s** et des **nouveaux arrivants** dans le domaine saura sans doute également servir de **pense bête aux confirmé·e·s** !

## Pré-requis

Ils sont plus que pauvre puisqu'un client OpenSSH et une machine distante à laquelle se connecter suffiront.

## TL;DR

Contenu d'un fichier config d'un client SSH **pour référence**:

```
Host *
    User rix
    IdentityFile ~/.ssh/ed_25519.key
    IdentityFile ~/.ssh/id_rsa.key
	IdentitiesOnly yes
	ForwardAgent yes
```

## Pourquoi utiliser un fichier de configuration ?

Comme abordé dans l'introduction, il existe autant de manières de se connecter à un serveur avec SSH qu'il n'en existe (de serveurs), les principaux bénéfices que l'on peut retirer de cette utilsation:
- Ajouter de la **cohérence** dans votre façon de vous connecter à vos différentes machines ( et on aime ça la cohérence ! ) il est en effet possible de créer une configuration spécifique à un serveur ou alors propre à différentes machines partageant les mêmes spécifités de connexion.
- Faciliter les connexions multiples, en créant des configurations aux contextes différents. Il est par exemple possible d'avoir une configuration utilisant une clé différente pour un groupe de machines données.

## Le fichier de configuration

En « standard » vous trouverez un dossier appelé `.ssh` dans le répertoire utilisateur de votre système (`/home/username/` sur un système de type UNIX ou `C:\Users\username\`sous Windows). C'est ici que nous allons créé un fichier appelé tout simplement `config` (sans extension).

!!! info "Configuration globale"
    Il est possible d'appliquer un comportement global au client SSH (C'est à dire pour tous les utilisateurs du système) en utilisant le fichier `/etc/ssh/ssh_config`.

Si le répertoire `.ssh` n'existe pas (il est créé automatiquement lorsque vous créez une nouvelle clé par exemple) vous pouvez le créer comme suit:

```bash
mkdir -p ~/.ssh && chmod 0700 ~/.ssh
```

Pour ensuite créer le fichier `config`
```bash
touch ~/.ssh/config && chmod 0600 ~/.ssh/config
```

!!! info "Les droits"
    Attention SSH est très sensible (à juste titre) aux droits appliqués aux fichiers qu'il doit utiliser.
    Le répertoire `.ssh` tout comme le fichier `config` ne doit être accessible, lisible et modifiable qu'à l'utilisateur propriétaire.

## Les instructions de configuration

Le fichier `config` est basé sur un système de paires **clé/valeur** organisées par section, une structure minimal d'un fichier de configuration pourrait être la suivante:

```javascript
Host server-hostname-1
    KEY value
    KEY value
```

Allons plus loin avec une configuration:

```javascript
Host server-hostname-1
    HostName server.tld
    User rix
    IdentityFile ~/.ssh/ed_25519.key
```

La directive `Host` permet d'indiquer à la fois une nouvelle section mais également le **« pattern »** qui permettra au client de savoir quand appliquer la configuration.
Dans ce premier exemple c'est assez simple et spécifique puisque notre bloc s'appliquera à la chaine `server-hostname-1`

Ainsi lorsque nous taperons `ssh server-hostname-1` notre client saura à quel hote se connecter, avec quel utilisateur et quelle clé.
L'équivalent sans fichier de configuration serait `ssh -i ~/.ssh/ed_25519.key rix@server.tld`

L'utilisation d'un fichier de configuration prend tout son sens lorsque l'on souhaite appliquer des comportements spécifiques à un ensemble de machines.

Il est ainsi possible d'utiliser des « pattern » (et de les enchaîner) en utilisant des opérateurs:

- `Host *` par exemple s'appliquera à tous les hôtes puisque le caractère `*` correspond à aucun ou plusieurs caractères.
- `192.168.140.*` il est possible de composer, dans ce cas  vous appliquerez votre configuration à l'ensemble des hôtes dont l'adresse fait partie du sous réseau `192.168.140.0/24`
- `?` permet de restreindre une expression à un seul caractère. Ainsi `Host 172.16.1.?` correspondra à tous les hôtes ayant en dernier octet un chiffre compris entre 0 et 9.
- `!` En début de chaîne permet d'exclure une correspondance. Ainsi `172.16.1.* !172.16.1.20` s'appliquera à tous les hôtes du sous réseau `172.16.1.0/24` à l'exception de `172.16.1.20`.

!!! info "Organisation et structure du fichier"
    Le fichier de configuration SSH n'impose pas d'indentation, il est toutefois fortement recommandé de l'organiser afin de faciliter sa lecture et sa maintenance.
    Il faut également noter que les instructions sont appliquées dans leur ordre d'apparition il est donc préférable de commencer par les sections très spécifiques et de terminer par les plus génériques.

## Priorité des instructions

Il est possible en fonction d'où elles sont indiquées de surcharger certaines options de connexion, ainsi votre client SSH considérera par ordre de priorité:

- Les options spécifiées dans la ligne de commande
- Les options définies dans le fichier `config` du compte utilisateur (`~/.ssh/config`)
- Les options définies dans le fichier générique `/etc/ssh/ssh_config`

Ainsi en reprendant notre section précédente:

```
Host server-hostname-1
    HostName server.tld
    User rix
    IdentityFile ~/.ssh/ed_25519.key
```

Il est possible de vous connecter avec un autre utilisateur que celui défini dans votre fichier en surchargeant la clé `User`, soit avec `ssh root@server-hostname-1` ou encore `ssh -o "User=root" server-hostname-1`.

## Séparer et inclure ses fichiers de configuration

Il est également possible, notamment lorsque le contenu des fichiers devient conséquent ou tout simplement pour organiser ses configurations entre différents contextes (clients, perso, pro...), de séparer la configuration dans plusieurs fichiers.

Il est ainsi possible d'avoir autant de fichiers de configuration que de contextes pour ensuite les inclure dans notre fichier principal.

**Exemple:**

```ini
# Contenu de ~/.ssh/config
Host server-hostname-1
    HostName server.tld
    User rix
    IdentityFile ~/.ssh/ed_25519.key

Include ~/.ssh/config_alternative
```

```ini
# Contenu de ~/.ssh/config_alternative
Host 192.168.140.*
    User debian
    IdentityFile ~/.ssh/id_rsa.key
    AddKeysToAgent yes
    UseKeychain yes
```

## Les instructions les plus courantes / utiles

Le fichier `config` supporte nombre d'options de configuration, celles-ci sont consultables [ici](https://linux.die.net/man/5/ssh_config).
Il va de soi que dans l'activité quotidienne, les mêmes instructions sont souvent utilisées, ci-dessous une liste des plus courantes:

- `IdentityFile`: Nous l'avons vu précédemment, elle permet d'indiquer la clé à utiliser pour la section définie;
- `ForwardAgent`: Un grand classique, permet de « faire suivre » comme son nom l'indique au serveur distant, votre clé privée de manière à ce que celui-ci la stocke dans son propre agent SSH. Cette option permet ensuite de se connecter à d'autres serveurs **« par rebond »**, c'est ce principe qui est notamment mis en oeuvre par les **« bastions » SSH**;
- `IdentitiesOnly`: Important si vous utilisez l'option précédente, permet de ne transmettre que la ou les clé(s) spécifiée(s) avec l'option `IdentityFile` précédente, par défaut SSH envoie toutes les clés privées qu'il trouvera dans votre trousseau;
- `StrictHostKeyChecking`: Permet de vérifier la signature du serveur auquel on se connecte, toujours à `Yes` sauf dans le cas de figure que l'on présente plus bas.

!!! danger "Désactiver `StrictHostKeyChecking`"
    Précaution d'usage, à ne faire que si vous savez réellement ce que vous faites ;)
    Le seul exemple qui me vient à l'esprit pouvant nécessiter de désactiver cette option est celui de **séances de cours / TP** durant lesquelles nous avons souvent besoin de créer / détruire des instances qui peuvent potentillement récupérer les mêmes adresses IPs.

Pour éviter d'avoir régulièrement l'erreur, `WARNING: REMOTE HOST IDENTIFICATION HAS CHANGED!` il est possible de spécifier:
```
StrictHostKeyChecking no
UserKnownHostsFile /dev/null
```

## Multiplexer ses connexions SSH

Le principe du multiplexage réside dans le fait de **« partager » la même connexion** entre les différentes sessions ouvertes sur une même machine.
Cette stratégie permet, dans le cas d'SSH de réutiliser une connexion TCP déjà ouverte vers un serveur distant.

Le but ? S'épargner le délai d'ouverture d'une connexion TCP ainsi que celui de la réauthentification.
Cette fonctionnalité peut-être particulièrement utile lors du transfert de nombreux fichiers d'une machine à une autre, elle réside principalement en l'utilisation de **3 options de configuration**:

- [`ControlMaster`](https://man.openbsd.org/ssh_config#ControlMaster): Permet d'indiquer à SSH la stratégie à adopter lorsqu'il détecte une possibilité de réutilisation d'une connexion ouverte (Fixée à `no` par défaut);
- `ControlPersist`: Permet d'indiquer comment SSH doit gérer la fermeture de la connexion initiale à la machine distante (celle qui a entrainée l'ouverture de la socket partagée);
- [`ControlPath`](https://man.openbsd.org/ssh_config#ControlPath): Le chemin vers la socket utilisée pour le partage de connexion, cette option supporte les tokens `%h` `%p` et `%r` dont la combinaison  est très fortement recommandée. Plus d'informations sur les tokens supportés par SSH ([https://man.openbsd.org/ssh_config#TOKENS](https://man.openbsd.org/ssh_config#TOKENS)).

### Exemple de fichiers de configuration avec multiplexage

```
Host server-hostname-1
    HostName server.tld
    User rix
    IdentityFile ~/.ssh/ed_25519.key
    ControlPath ~/.ssh/controlmasters/%C
    ControlMaster auto
    ControlPersist 10m
```

!!! info "Le token %C"
    On remarquera son utilisation dans l'exemple ci-dessus. Il s'agit du **hash [SHA1](https://en.wikipedia.org/wiki/SHA-1)** des tokens %l%h%p%r (Respectivement le nom d'hôte local (`%l`), le nom d'hôte distant (`%h`), le port de connexion distant (`%p`) et pour finir le nom d'utilisateur distant utilisé (`%r`)).
    L'utilisation du token `%C` assurant à la fois, l'unicité de la connexion et l'obfuscation de ses détails sur le système de fichiers.

## Aller plus loin avec les sources

- Recommandations pour un usage sécurisé d'OpenSSH par l'ANSSI: [https://www.ssi.gouv.fr/uploads/2014/01/NT_OpenSSH.pdf](https://www.ssi.gouv.fr/uploads/2014/01/NT_OpenSSH.pdf)
- https://man.openbsd.org/ssh_config
- https://unix.stackexchange.com/a/89149
- https://www.cyberciti.biz/faq/create-ssh-config-file-on-linux-unix/
- https://www.quora.com/What-is-the-controlpersist-feature-Unix
